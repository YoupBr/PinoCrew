<div class="flex flex-col gap-8">

    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            Dashboard
        </flux:heading>

        <flux:subheading>
            Overzicht van de vrijwilligersinschrijvingen.
        </flux:subheading>
    </div>

    {{-- Statistieken --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500">
                Open diensten
            </div>

            <div class="mt-2 text-3xl font-bold">
                {{ $this->openShifts }}
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500">
                Inschrijvingen
            </div>

            <div class="mt-2 text-3xl font-bold">
                {{ $this->signupCount }}
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500">
                Teams op norm
            </div>

            <div class="mt-2 text-3xl font-bold text-green-600">
                {{ $this->teamsOnTarget }}
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500">
                Teams onder norm
            </div>

            <div class="mt-2 text-3xl font-bold text-orange-600">
                {{ $this->teamsBelowTarget }}
            </div>
        </div>

    </div>

    <div class="grid gap-6 xl:grid-cols-2">

        {{-- Komende diensten --}}
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

            <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="lg">
                    Komende diensten
                </flux:heading>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">

                @forelse ($this->upcomingShifts as $shift)

                    <div class="flex items-center justify-between gap-4 p-5">

                        <div>
                            <div class="font-semibold">
                                {{ $shift->title }}
                            </div>

                            <div class="mt-1 text-sm text-zinc-500">
                                {{ $shift->date->translatedFormat('d F Y') }}

                                @if ($shift->starts_at)
                                    · {{ substr($shift->starts_at, 0, 5) }}
                                @endif

                                @if ($shift->ends_at)
                                    – {{ substr($shift->ends_at, 0, 5) }}
                                @endif
                            </div>

                            @if ($shift->location)
                                <div class="mt-1 text-xs text-zinc-400">
                                    {{ $shift->location }}
                                </div>
                            @endif
                        </div>

                        <div class="text-right">
                            <div class="font-semibold">
                                {{ $shift->signups_count }}

                                @if ($shift->capacity)
                                    / {{ $shift->capacity }}
                                @endif
                            </div>

                            <div class="text-xs text-zinc-500">
                                inschrijvingen
                            </div>
                        </div>

                    </div>

                @empty

                    <div class="p-6 text-sm text-zinc-500">
                        Geen komende diensten.
                    </div>

                @endforelse

            </div>
        </div>

        {{-- Vrijwilligers per hockeyteam --}}
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

            <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="lg">
                    Vrijwilligers per team
                </flux:heading>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">

                @forelse ($this->hockeyTeams as $team)

                    @php
                        $remaining = max(
                            0,
                            $team->required_volunteers - $team->signups_count
                        );

                        $completed = $remaining === 0;
                    @endphp

                    <div class="flex items-center justify-between gap-4 p-5">

                        <div>
                            <div class="font-semibold">
                                {{ $team->name }}
                            </div>

                            <div class="mt-1 text-sm text-zinc-500">
                                {{ $team->signups_count }}
                                van
                                {{ $team->required_volunteers }}
                                vrijwilligers
                            </div>
                        </div>

                        @if ($completed)

                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-950 dark:text-green-400">
                                Op norm
                            </span>

                        @else

                            <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 dark:bg-orange-950 dark:text-orange-400">
                                Nog {{ $remaining }} nodig
                            </span>

                        @endif

                    </div>

                @empty

                    <div class="p-6 text-sm text-zinc-500">
                        Er zijn nog geen hockeyteams.
                    </div>

                @endforelse

            </div>
        </div>

    </div>

</div>