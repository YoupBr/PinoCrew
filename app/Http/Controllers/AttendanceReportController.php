<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Signup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttendanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'shift' => ['nullable', 'integer', 'exists:shifts,id'],
            'status' => ['nullable', 'in:present,missing,absent'],
        ]);
        $selectedShift = isset($data['shift']) ? Shift::findOrFail($data['shift']) : null;
        $shifts = Shift::query()->withCount([
            'signups',
            'signups as present_count' => fn ($query) => $query->whereNotNull('checked_in_at'),
        ])->orderByDesc('date')->orderBy('starts_at')->paginate(20, ['*'], 'shifts_page')->withQueryString();

        $signups = $selectedShift?->signups()->with('hockeyTeam')
            ->when(($data['status'] ?? '') === 'present', fn ($query) => $query->whereNotNull('checked_in_at'))
            ->when(($data['status'] ?? '') === 'missing', fn ($query) => $query->whereNull('checked_in_at')->whereNull('absence_confirmed_at'))
            ->when(($data['status'] ?? '') === 'absent', fn ($query) => $query->whereNotNull('absence_confirmed_at'))
            ->orderBy('name')->paginate(50, ['*'], 'signups_page')->withQueryString();

        $totals = $selectedShift ? [
            'registered' => $selectedShift->signups()->count(),
            'present' => $selectedShift->signups()->whereNotNull('checked_in_at')->count(),
            'absent' => $selectedShift->signups()->whereNotNull('absence_confirmed_at')->count(),
        ] : null;
        $teams = $selectedShift ? $selectedShift->signups()->with('hockeyTeam')->get()
            ->groupBy('hockey_team_id')->map(fn ($rows) => [
                'name' => $rows->first()->hockeyTeam->name,
                'registered' => $rows->count(),
                'present' => $rows->whereNotNull('checked_in_at')->count(),
                'absent' => $rows->whereNotNull('absence_confirmed_at')->count(),
            ])->sortBy('name') : collect();

        return view('attendance-report', compact('shifts', 'selectedShift', 'signups', 'totals', 'teams'));
    }

    public function update(Request $request, string $current_team, Signup $signup): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', 'in:present,absent,reset']]);

        if ($data['action'] === 'absent' && ! $signup->shift->attendanceEndsAt()->isPast()) {
            throw ValidationException::withMessages(['action' => 'Bevestig afwezigheid pas na afloop van de dienst.']);
        }

        if ($data['action'] === 'present') {
            Signup::query()->whereKey($signup->id)->whereNull('checked_in_at')->update([
                'checked_in_at' => now(),
                'checked_in_by' => $request->user()->id,
                'absence_confirmed_at' => null,
                'absence_confirmed_by' => null,
                'updated_at' => now(),
            ]);
        } else {
            $signup->checked_in_at = null;
            $signup->checked_in_by = null;
            $signup->absence_confirmed_at = $data['action'] === 'absent' ? now() : null;
            $signup->absence_confirmed_by = $data['action'] === 'absent' ? $request->user()->id : null;
            $signup->save();
        }

        return to_route('attendance-report.index', [
            'current_team' => $current_team,
            'shift' => $signup->shift_id,
        ])->with('status', 'Aanwezigheid bijgewerkt.');
    }
}
