<?php

use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public', ['title' => 'Inschrijven | PinoCrew'])] class extends Component
{
    public Shift $shift;

    public string $name = '';
    public string $email = '';
    public string $hockey_team_id = '';
    public string $phone = '';

    public bool $submitted = false;

    public function mount(Shift $shift): void
    {
        abort_unless(
            $shift->is_published && $shift->date->gte(today()),
            404
        );

        $this->shift = $shift;
    }

    #[Computed]
    public function hockeyTeams()
    {
        return HockeyTeam::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function isFull(): bool
    {
        if ($this->shift->capacity === null) {
            return false;
        }

        return $this->shift->signups()->count() >= $this->shift->capacity;
    }

    public function signup(): void
    {
        if ($this->isFull) {
            $this->addError('capacity', 'Deze dienst is inmiddels vol.');

            return;
        }

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'hockey_team_id' => [
                'required',
                Rule::exists('hockey_teams', 'id')
                    ->where(fn ($query) => $query->where('active', true)),
            ],
        ]);

        Signup::create([
            'shift_id' => $this->shift->id,
            'hockey_team_id' => $validated['hockey_team_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $this->submitted = true;
    }
};
?>

<div class="min-h-screen bg-slate-50">
    {{-- Header --}}
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-3xl items-center justify-center px-0 py-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('img/logoblauw.png') }}" alt="Pinoké logo" class="h-16 w-auto object-contain"/></a>
        </div>
    </header>

       <main class="mx-auto max-w-3xl px-6 py-10">
        <a href="{{ route('signup') }}"
            wire:navigate
            class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-500">
            ← Terug naar alle diensten
        </a>

        <div class="mt-6 overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-sm">
            <div class="bg-gradient-to-br from-blue-600 to-blue-500 px-6 py-7 text-white sm:px-8">
                <span class="text-sm font-semibold text-blue-100">
                    {{ $shift->date->translatedFormat('l j F') }}
                </span>

                <h1 class="mt-2 text-3xl font-bold tracking-tight">
                    {{ $shift->title }}
                </h1>

                <div class="mt-5 flex flex-wrap gap-3 text-sm">
                    <span class="rounded-full bg-white/10 px-3 py-1.5">
                        {{ substr($shift->starts_at, 0, 5) }}
                        @if ($shift->ends_at)
                            – {{ substr($shift->ends_at, 0, 5) }}
                        @endif
                    </span>

                    @if ($shift->location)
                        <span class="rounded-full bg-white/10 px-3 py-1.5">
                            {{ $shift->location }}
                        </span>
                    @endif
                </div>
            </div>

            @if ($shift->description)
                <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                    <p class="leading-7 text-slate-600">
                        {{ $shift->description }}
                    </p>
                </div>
            @endif
        <div class="mt-10 rounded-3xl border border-slate-300 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">
            <div class="border-b border-slate-200 pb-5">
                <h2 class="text-2xl font-bold text-slate-900">
                    Aanmelden voor deze dienst
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-700">
                    Vul hieronder je gegevens in en kies namens welk team je helpt.
                </p>
            </div>

            @error('capacity')
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                    {{ $message }}
                </div>
            @enderror

            @if ($this->isFull)
                <div class="mt-6 rounded-xl border border-slate-300 bg-slate-100 p-5">
                    <p class="font-semibold text-slate-900">
                        Deze dienst is vol
                    </p>

                    <p class="mt-1 text-sm text-slate-700">
                        Er zijn momenteel geen plekken meer beschikbaar.
                    </p>
                </div>
            @else
                <form wire:submit="signup" class="mt-7 space-y-6">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-900">
                            Naam
                        </label>

                        <input
                            id="name"
                            type="text"
                            wire:model="name"
                            autocomplete="name"
                            placeholder="Voor- en achternaam"
                            class="block w-full rounded-xl border border-slate-300 bg-white
                                px-4 py-3 text-slate-950 placeholder:text-slate-400
                                outline-none transition
                                focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('name')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-900">
                            E-mailadres
                        </label>

                        <input
                            id="email"
                            type="email"
                            wire:model="email"
                            autocomplete="email"
                            placeholder="naam@voorbeeld.nl"
                            class="block w-full rounded-xl border border-slate-300 bg-white
                                px-4 py-3 text-slate-950 placeholder:text-slate-400
                                outline-none transition
                                focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('email')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-slate-900">
                            Telefoonnummer
                        </label>

                        <input
                            id="phone"
                            type="tel"
                            wire:model="phone"
                            autocomplete="tel"
                            placeholder="06 12345678"
                            class="block w-full rounded-xl border border-slate-300 bg-white
                                px-4 py-3 text-slate-950 placeholder:text-slate-400
                                outline-none transition
                                focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

                        @error('phone')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="hockey_team_id" class="mb-2 block text-sm font-semibold text-slate-900">
                            Namens welk team help je?
                        </label>

                        <select
                            id="hockey_team_id"
                            wire:model="hockey_team_id"
                            class="block w-full rounded-xl border border-slate-300 bg-white
                                px-4 py-3 text-slate-950 outline-none transition
                                focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">Kies je team</option>

                            @foreach ($this->hockeyTeams as $team)
                                <option value="{{ $team->id }}">
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('hockey_team_id')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-slate-200 pt-6">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="signup"
                            class="flex w-full items-center justify-center rounded-xl
                                bg-blue-600 px-6 py-4 text-base font-bold text-white
                                shadow-lg shadow-blue-600/20 transition
                                hover:bg-blue-500
                                focus:outline-none focus:ring-4 focus:ring-blue-200
                                disabled:opacity-60">
                            <span wire:loading.remove wire:target="signup">
                                Inschrijving bevestigen
                            </span>

                            <span wire:loading wire:target="signup">
                                Bezig met inschrijven...
                            </span>
                        </button>
                    </div>
                    </form>
            @endif
        </div>
        </div>
    </main>
</div>