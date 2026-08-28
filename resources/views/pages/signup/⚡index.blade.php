<?php

use App\Models\Shift;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.public', ['title' => 'Inschrijven | PinoCrew'])] class extends Component
{
    #[Computed]
    public function shifts()
    {
        return Shift::query()
            ->withCount('signups')
            ->where('is_published', true)
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('starts_at')
            ->get();
    }
};
?>

<div class="min-h-screen bg-slate-50">
    {{-- Header --}}
    
    <header class="border-b border-slate-200 bg-white">
        <title>{{ $title ?? 'PinoCrew' }}</title>
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('img/logoblauw.png') }}" alt="Pinoke logo" class="h-14 w-auto" />

                <span class="text-xl font-semibold tracking-tight text-slate-900 ">
                    PinoCrew
                </span>
            </a>

            <span class="hidden text-sm text-slate-500 sm:block">
            </span>
        </div>
        <title>Inschrijven | PinoCrew</title>
    </header>

    {{-- Content --}}
    <main class="mx-auto max-w-5xl px-6 py-12">
        <div class="max-w-2xl">
            <span class="text-sm font-semibold text-blue-600">
                Vrijwilligersdiensten
            </span>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Waar wil je bij helpen?
            </h1>

            <p class="mt-3 text-lg leading-8 text-slate-600">
                Kies een dienst en schrijf je gemakkelijk in.</p>
        </div>

        <div class="mt-10 space-y-4">
            @forelse ($this->shifts as $shift)
                @php
                    $isFull = $shift->capacity !== null
                        && $shift->signups_count >= $shift->capacity;

                    $available = $shift->capacity !== null
                        ? max(0, $shift->capacity - $shift->signups_count)
                        : null;
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ $shift->date->translatedFormat('l j F') }}
                                </span>

                                @if ($isFull)
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        Vol
                                    </span>
                                @elseif ($available !== null)
                                    <span class="text-xs text-slate-500">
                                        {{ $available }} {{ $available === 1 ? 'plek' : 'plekken' }} beschikbaar
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-xl font-semibold text-slate-900">
                                {{ $shift->title }}
                            </h2>

                            <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500">
                                <span>
                                    {{ substr($shift->starts_at, 0, 5) }}

                                    @if ($shift->ends_at)
                                        – {{ substr($shift->ends_at, 0, 5) }}
                                    @endif
                                </span>

                                @if ($shift->location)
                                    <span>{{ $shift->location }}</span>
                                @endif
                            </div>

                            @if ($shift->description)
                                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                                    {{ $shift->description }}
                                </p>
                            @endif
                        </div>

                        <div class="shrink-0">
                            @if ($isFull)
                                <span class="inline-flex w-full justify-center rounded-xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-400 sm:w-auto">
                                    Dienst vol
                                </span>
                            @else
                                <a
                                    href="{{ route('signup.show', ['shift' => $shift]) }}"
                                    wire:navigate
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500 sm:w-auto"
                                >
                                    Inschrijven
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Geen openstaande diensten
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Er zijn op dit moment geen diensten waarvoor je je kunt inschrijven.
                    </p>
                </div>
            @endforelse
        </div>
    </main>
</div>