<div class="flex flex-col gap-8">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-0">
        <div>
            <flux:heading size="xl">
                Crew
            </flux:heading>

            <flux:subheading>
                Beheer alle vrijwilligersinschrijvingen binnen PinoCrew.
            </flux:subheading>
        </div>

        <div class="ms-8">
            <a href="{{ route('crew.print', [
                'current_team' => request()->route('current_team'),
                'search' => $search,
                'shift' => $shiftFilter,
                'team' => $teamFilter, ]) }}"
                    target="_blank">
                 <flux:button icon="printer" class="px-1">
                      Print lijst
                </flux:button>
            </a>

            <flux:button wire:click="exportCsv" icon="arrow-down-tray">Export CSV</flux:button>
        </div>
    </div>


    {{-- Statistieken --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-zinc-500">
                        Totaal inschrijvingen
                    </div>

                    <div class="mt-2 text-3xl font-bold">
                        {{ $this->totalSignups }}
                    </div>
                </div>

                <div class="rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <flux:icon.users class="size-6 text-zinc-500" />
                </div>
            </div>
        </div>


        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-zinc-500">
                        Vandaag ingeschreven
                    </div>

                    <div class="mt-2 text-3xl font-bold">
                        {{ $this->signupsToday }}
                    </div>
                </div>

                <div class="rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <flux:icon.user-plus class="size-6 text-zinc-500" />
                </div>
            </div>
        </div>


        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-zinc-500">
                        Teams vertegenwoordigd
                    </div>

                    <div class="mt-2 text-3xl font-bold">
                        {{ $this->representedTeams }}
                    </div>
                </div>

                <div class="rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <flux:icon.shield-check class="size-6 text-zinc-500" />
                </div>
            </div>
        </div>


        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-zinc-500">
                        Komende diensten
                    </div>

                    <div class="mt-2 text-3xl font-bold">
                        {{ $this->upcomingSignups }}
                    </div>
                </div>

                <div class="rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <flux:icon.calendar-days class="size-6 text-zinc-500" />
                </div>
            </div>
        </div>

    </div>


    {{-- Filters + tabel --}}
    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">

            <div class="flex flex-col gap-4 xl:flex-row xl:items-end">

                <div class="flex-1">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        label="Zoeken"
                        icon="magnifying-glass"
                        placeholder="Naam, e-mail, telefoon, team of dienst..."
                    />
                </div>


                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                    <flux:select
                        wire:model.live="team"
                        label="Team"
                        >
                        <option value="">
                            Alle teams
                        </option>

                        @foreach ($this->hockeyTeams as $hockeyTeam)
                            <option value="{{ $hockeyTeam->id }}">
                                {{ $hockeyTeam->name }}
                            </option>
                        @endforeach
                    </flux:select>


                    <flux:select
                        wire:model.live="shift"
                        label="Dienst">
                        <option value="">
                            Alle diensten
                        </option>

                        @foreach ($this->shifts as $shiftOption)
                            <option value="{{ $shiftOption->id }}">
                                {{ $shiftOption->title }}
                            </option>
                        @endforeach
                    </flux:select>


                    <flux:select
                        wire:model.live="sort"
                        label="Sortering">
                        <option value="newest">
                            Nieuwste eerst
                        </option>

                        <option value="oldest">
                            Oudste eerst
                        </option>

                        <option value="name">
                            Naam A-Z
                        </option>
                    </flux:select>

                </div>
            </div>

            @if (
                $search !== '' ||
                $team !== '' ||
                $shift !== '' ||
                $sort !== 'newest'
            )
                <div class="mt-4">
                    <flux:button
                        size="sm"
                        variant="ghost"
                        icon="x-mark"
                        wire:click="clearFilters"
                    >
                        Filters wissen
                    </flux:button>
                </div>
            @endif

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">

                <thead class="bg-zinc-50 dark:bg-zinc-800/50">

                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Vrijwilliger
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Team
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Dienst
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Contact
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Ingeschreven
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Acties
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                    @forelse ($this->signups as $signup)

                        <tr
                            wire:key="signup-{{ $signup->id }}"
                            class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                        >

                            {{-- Naam --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-zinc-100 font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ strtoupper(substr($signup->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-white">
                                            {{ $signup->name }}
                                        </div>

                                        <div class="text-xs text-zinc-500">
                                            #{{ $signup->id }}
                                        </div>
                                    </div>

                                </div>

                            </td>


                            {{-- Hockeyteam --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                @if ($signup->hockeyTeam)
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                        {{ $signup->hockeyTeam->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-zinc-400">
                                        —
                                    </span>
                                @endif

                            </td>


                            {{-- Dienst --}}
                            <td class="px-5 py-4">

                                @if ($signup->shift)

                                    <div class="font-medium">
                                        {{ $signup->shift->title }}
                                    </div>

                                    <div class="mt-1 text-xs text-zinc-500">
                                        {{ $signup->shift->date->translatedFormat('d F Y') }}

                                        @if ($signup->shift->starts_at)
                                            · {{ substr($signup->shift->starts_at, 0, 5) }}
                                        @endif
                                    </div>

                                @else
                                    <span class="text-sm text-zinc-400">
                                        Dienst verwijderd
                                    </span>
                                @endif

                            </td>


                            {{-- Contact --}}
                            <td class="px-5 py-4">

                                <div class="text-sm">
                                    <a
                                        href="mailto:{{ $signup->email }}"
                                        class="hover:underline"
                                    >
                                        {{ $signup->email }}
                                    </a>
                                </div>

                                @if ($signup->phone)
                                    <div class="mt-1 text-xs text-zinc-500">
                                        <a
                                            href="tel:{{ $signup->phone }}"
                                            class="hover:underline"
                                        >
                                            {{ $signup->phone }}
                                        </a>
                                    </div>
                                @endif

                            </td>


                            {{-- Datum inschrijving --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <div class="text-sm">
                                    {{ $signup->created_at->translatedFormat('d M Y') }}
                                </div>

                                <div class="mt-1 text-xs text-zinc-500">
                                    {{ $signup->created_at->format('H:i') }}
                                </div>

                            </td>


                            {{-- Actions --}}
                            <td class="whitespace-nowrap px-5 py-4 text-right">

                                <flux:dropdown position="bottom" align="end">

                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="ellipsis-horizontal"
                                    />

                                    <flux:menu>

                                        @if ($signup->email)
                                            <flux:menu.item
                                                icon="envelope"
                                                href="mailto:{{ $signup->email }}"
                                            >
                                                E-mail sturen
                                            </flux:menu.item>
                                        @endif


                                        @if ($signup->phone)
                                            <flux:menu.item
                                                icon="phone"
                                                href="tel:{{ $signup->phone }}"
                                            >
                                                Bellen
                                            </flux:menu.item>
                                        @endif


                                        <flux:menu.separator />


                                        <flux:menu.item
                                            variant="danger"
                                            icon="trash"
                                            wire:click="deleteSignup({{ $signup->id }})"
                                            wire:confirm="Weet je zeker dat je deze inschrijving wilt verwijderen?"
                                        >
                                            Verwijderen
                                        </flux:menu.item>

                                    </flux:menu>

                                </flux:dropdown>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="px-6 py-16 text-center"
                            >
                                <div class="mx-auto flex max-w-sm flex-col items-center">

                                    <div class="mb-4 rounded-full bg-zinc-100 p-4 dark:bg-zinc-800">
                                        <flux:icon.users class="size-7 text-zinc-400" />
                                    </div>

                                    <div class="font-semibold">
                                        Geen inschrijvingen gevonden
                                    </div>

                                    <div class="mt-1 text-sm text-zinc-500">
                                        Pas je zoekopdracht of filters aan.
                                    </div>

                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($this->signups->hasPages())
            <div class="border-t border-zinc-200 px-5 py-4 dark:border-zinc-700">
                {{ $this->signups->links() }}
            </div>
        @endif

    </div>

</div>