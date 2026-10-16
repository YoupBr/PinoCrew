<?php

namespace App\Http\Controllers;

use App\Models\Signup;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(): View
    {
        return view('attendance');
    }

    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100', 'not_regex:/[%_\\\\]/'],
        ]);

        $candidates = Signup::query()
            ->with(['shift', 'hockeyTeam:id,name'])
            ->where('name', 'like', '%'.trim($data['name']).'%')
            ->whereHas('shift', fn ($query) => $query
                ->where('is_published', true)
                ->whereDate('date', '>=', now('Europe/Amsterdam')->subDay()->toDateString())
                ->whereDate('date', '<=', now('Europe/Amsterdam')->toDateString()))
            ->orderBy('name')->limit(100)->get();

        $matches = $candidates->filter(fn (Signup $signup) => $signup->shift->isAttendanceOpen())->take(8)
            ->map(fn (Signup $signup) => [
                'token' => Crypt::encryptString(json_encode([
                    'signup' => $signup->id,
                    'expires' => now()->addMinutes(10)->timestamp,
                    'session' => hash('sha256', $request->session()->getId()),
                ], JSON_THROW_ON_ERROR)),
                'label' => $signup->name.' · '.$signup->hockeyTeam->name.' · '.$signup->shift->title.' · '.substr($signup->shift->starts_at, 0, 5),
            ])->values();

        $message = $matches->isNotEmpty() ? '' : ($candidates->isNotEmpty()
            ? 'Je inschrijving is gevonden, maar aanmelden kan alleen vanaf twee uur vóór aanvang tot het einde van je dienst. Vraag de coördinator om hulp.'
            : 'Geen inschrijving gevonden voor vandaag. Probeer een ander deel van je naam of meld je bij de coördinator.');

        return response()->json(['matches' => $matches, 'message' => $message])
            ->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:2048']]);

        try {
            $payload = json_decode(Crypt::decryptString($data['token']), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException $exception) {
            throw ValidationException::withMessages(['token' => 'Zoek je naam opnieuw en selecteer je dienst.']);
        }

        if (! is_array($payload) || ($payload['expires'] ?? 0) < now()->timestamp ||
            ! hash_equals(hash('sha256', $request->session()->getId()), (string) ($payload['session'] ?? ''))) {
            throw ValidationException::withMessages(['token' => 'Je selectie is verlopen. Zoek je naam opnieuw.']);
        }

        $signup = Signup::query()->with('shift')->find($payload['signup'] ?? 0);

        if (! $signup || ! $signup->shift->isAttendanceOpen()) {
            throw ValidationException::withMessages(['token' => 'Aanmelden voor deze dienst is nu niet mogelijk. Meld je bij de coördinator.']);
        }

        Signup::query()->whereKey($signup->id)->whereNull('checked_in_at')->update([
            'checked_in_at' => now(),
            'checked_in_by' => null,
            'absence_confirmed_at' => null,
            'absence_confirmed_by' => null,
            'updated_at' => now(),
        ]);

        return to_route('attendance.index')->with('attendance_success', 'Je aanwezigheid is geregistreerd. Bedankt voor je hulp!');
    }
}
