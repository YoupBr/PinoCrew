@php
    $calendarStartHour = 8;
    $calendarEndHour = 23;
    $hourHeight = 72;
    $totalHours = $calendarEndHour - $calendarStartHour;
    $calendarHeight = $totalHours * $hourHeight;

    $weekStartDate = \Carbon\Carbon::parse($weekStart);
    $weekEndDate = $weekStartDate->copy()->addDays(6);

    $monthNames = [
        1 => 'januari',
        2 => 'februari',
        3 => 'maart',
        4 => 'april',
        5 => 'mei',
        6 => 'juni',
        7 => 'juli',
        8 => 'augustus',
        9 => 'september',
        10 => 'oktober',
        11 => 'november',
        12 => 'december',
    ];

    $dayNames = [
        1 => 'MA',
        2 => 'DI',
        3 => 'WO',
        4 => 'DO',
        5 => 'VR',
        6 => 'ZA',
        7 => 'ZO',
    ];

    if ($weekStartDate->month === $weekEndDate->month) {
        $weekLabel = $weekStartDate->day
            . ' – '
            . $weekEndDate->day
            . ' '
            . $monthNames[$weekEndDate->month]
            . ' '
            . $weekEndDate->year;
    } else {
        $weekLabel = $weekStartDate->day
            . ' '
            . $monthNames[$weekStartDate->month]
            . ' – '
            . $weekEndDate->day
            . ' '
            . $monthNames[$weekEndDate->month]
            . ' '
            . $weekEndDate->year;
    }
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>
            <flux:heading size="xl">
                Agenda
            </flux:heading>

            <flux:text class="mt-1">
                Bekijk alle PinoCrew-diensten en inschrijvingen per week.
            </flux:text>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            <flux:button
                wire:click="today"
                variant="ghost"
            >
                Vandaag
            </flux:button>

            <div class="flex items-center rounded-lg border border-zinc-200 dark:border-zinc-700">

                <button
                    type="button"
                    wire:click="previousWeek"
                    class="flex h-9 w-9 items-center justify-center rounded-l-lg transition hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    aria-label="Vorige week"
                >
                    <flux:icon.chevron-left class="size-4" />
                </button>

                <div class="min-w-48 border-x border-zinc-200 px-4 text-center text-sm font-medium dark:border-zinc-700">
                    {{ $weekLabel }}
                </div>

                <button
                    type="button"
                    wire:click="nextWeek"
                    class="flex h-9 w-9 items-center justify-center rounded-r-lg transition hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    aria-label="Volgende week"
                >
                    <flux:icon.chevron-right class="size-4" />
                </button>

            </div>

        </div>
    </div>


    {{-- Desktop week calendar --}}
    <div class="hidden overflow-hidden rounded-xl border border-zinc-200 bg-white lg:block dark:border-zinc-700 dark:bg-zinc-900">

        {{-- Days header --}}
        <div class="grid grid-cols-[72px_repeat(7,minmax(0,1fr))] border-b border-zinc-200 dark:border-zinc-700">

            <div></div>

            @foreach ($this->weekDays as $day)
                @php
                    $isToday = $day->isToday();
                @endphp

                <div
                    @class([
                        'border-l border-zinc-200 px-3 py-4 text-center dark:border-zinc-700',
                        'bg-blue-50/60 dark:bg-blue-950/20' => $isToday,
                    ])
                >
                    <div class="text-xs font-semibold tracking-wide text-zinc-500">
                        {{ $dayNames[$day->dayOfWeekIso] }}
                    </div>

                    <div
                        @class([
                            'mx-auto mt-1 flex size-9 items-center justify-center rounded-full text-lg font-semibold',
                            'bg-blue-600 text-white' => $isToday,
                            'text-zinc-900 dark:text-white' => ! $isToday,
                        ])
                    >
                        {{ $day->day }}
                    </div>
                </div>
            @endforeach

        </div>


        {{-- Calendar body --}}
        <div class="grid grid-cols-[72px_repeat(7,minmax(0,1fr))]">

            {{-- Time column --}}
            <div
                class="relative bg-zinc-50/50 dark:bg-zinc-900"
                style="height: {{ $calendarHeight }}px;"
            >
                @for ($hour = $calendarStartHour; $hour <= $calendarEndHour; $hour++)
                    <div
                        class="absolute right-3 -translate-y-1/2 text-xs text-zinc-400"
                        style="top: {{ ($hour - $calendarStartHour) * $hourHeight }}px;"
                    >
                        {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00
                    </div>
                @endfor
            </div>


            {{-- Day columns --}}
            @foreach ($this->weekDays as $day)
                <div
                    @class([
                        'relative border-l border-zinc-200 dark:border-zinc-700',
                        'bg-blue-50/20 dark:bg-blue-950/10' => $day->isToday(),
                    ])
                    style="height: {{ $calendarHeight }}px;"
                >

                    {{-- Hour grid lines --}}
                    @for ($hour = 0; $hour <= $totalHours; $hour++)
                        <div
                            class="absolute inset-x-0 border-t border-zinc-100 dark:border-zinc-800"
                            style="top: {{ $hour * $hourHeight }}px;"
                        ></div>
                    @endfor


                    {{-- Shifts --}}
                    @foreach ($this->shiftsForDay($day) as $shift)

                        @php
                            $start = \Carbon\Carbon::parse($shift->starts_at);
                            $end = \Carbon\Carbon::parse($shift->ends_at);

                            $startMinutes =
                                (($start->hour - $calendarStartHour) * 60)
                                + $start->minute;

                            $durationMinutes = $start->diffInMinutes($end);

                            $top = ($startMinutes / 60) * $hourHeight;
                            $height = max(
                                ($durationMinutes / 60) * $hourHeight,
                                42
                            );

                            $capacity = max((int) $shift->capacity, 1);
                            $signupCount = (int) $shift->signups_count;

                            $percentage = min(
                                ($signupCount / $capacity) * 100,
                                100
                            );

                            $isFull = $signupCount >= $capacity;
                            $isAlmostFull = ! $isFull && $percentage >= 75;
                        @endphp

                        <button
                            type="button"
                            wire:click="selectShift({{ $shift->id }})"
                            @class([
                                'absolute left-1 right-1 z-10 overflow-hidden rounded-lg border p-2 text-left transition hover:brightness-95',
                                'border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950/50' => $isFull,
                                'border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950/50' => $isAlmostFull,
                                'border-blue-200 bg-blue-50 dark:border-blue-900 dark:bg-blue-950/50' => ! $isFull && ! $isAlmostFull,
                            ])
                            style="
                                top: {{ $top }}px;
                                height: {{ $height }}px;
                            "
                        >
                            <div class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $shift->title }}
                            </div>

                            <div class="mt-0.5 text-xs text-zinc-600 dark:text-zinc-300">
                                {{ $start->format('H:i') }}
                                –
                                {{ $end->format('H:i') }}
                            </div>

                            @if ($height >= 65)
                                <div class="mt-2 flex items-center gap-1 text-xs font-medium text-zinc-600 dark:text-zinc-300">
                                    <flux:icon.users class="size-3.5" />

                                    {{ $signupCount }} / {{ $shift->capacity }}
                                </div>
                            @endif

                            @if ($height >= 90)
                                <div class="mt-2 h-1 overflow-hidden rounded-full bg-black/10 dark:bg-white/10">
                                    <div
                                        class="h-full rounded-full bg-current opacity-60"
                                        style="width: {{ $percentage }}%;"
                                    ></div>
                                </div>
                            @endif
                        </button>

                    @endforeach


                    {{-- Current time indicator --}}
                    @if ($day->isToday())
                        @php
                            $now = now();

                            $nowMinutes =
                                (($now->hour - $calendarStartHour) * 60)
                                + $now->minute;

                            $nowTop = ($nowMinutes / 60) * $hourHeight;
                        @endphp

                        @if ($now->hour >= $calendarStartHour && $now->hour <= $calendarEndHour)
                            <div
                                class="pointer-events-none absolute inset-x-0 z-20 flex items-center"
                                style="top: {{ $nowTop }}px;"
                            >
                                <div class="-ml-1 size-2 rounded-full bg-red-500"></div>
                                <div class="h-px flex-1 bg-red-500"></div>
                            </div>
                        @endif
                    @endif

                </div>
            @endforeach

        </div>
    </div>


    {{-- Mobile / tablet agenda --}}
    <div class="space-y-6 lg:hidden">

        @foreach ($this->weekDays as $day)

            @php
                $dayShifts = $this->shiftsForDay($day);
            @endphp

            <section>

                <div class="mb-3 flex items-center gap-3">

                    <div
                        @class([
                            'flex size-10 shrink-0 items-center justify-center rounded-full font-semibold',
                            'bg-blue-600 text-white' => $day->isToday(),
                            'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' => ! $day->isToday(),
                        ])
                    >
                        {{ $day->day }}
                    </div>

                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-white">
                            {{ ucfirst($day->locale('nl')->translatedFormat('l')) }}
                        </div>

                        <div class="text-sm text-zinc-500">
                            {{ $day->locale('nl')->translatedFormat('j F') }}
                        </div>
                    </div>

                </div>


                @if ($dayShifts->isEmpty())

                    <div class="ml-[52px] rounded-lg border border-dashed border-zinc-200 px-4 py-4 text-sm text-zinc-400 dark:border-zinc-700">
                        Geen diensten
                    </div>

                @else

                    <div class="ml-[52px] space-y-2">

                        @foreach ($dayShifts as $shift)

                            @php
                                $start = \Carbon\Carbon::parse($shift->starts_at);
                                $end = \Carbon\Carbon::parse($shift->ends_at);

                                $capacity = max((int) $shift->capacity, 1);
                                $signupCount = (int) $shift->signups_count;

                                $percentage = min(
                                    ($signupCount / $capacity) * 100,
                                    100
                                );
                            @endphp

                            <button
                                type="button"
                                wire:click="selectShift({{ $shift->id }})"
                                class="w-full rounded-xl border border-zinc-200 bg-white p-4 text-left transition active:scale-[0.99] dark:border-zinc-700 dark:bg-zinc-900"
                            >
                                <div class="flex items-start justify-between gap-3">

                                    <div>
                                        <div class="font-semibold text-zinc-900 dark:text-white">
                                            {{ $shift->title }}
                                        </div>

                                        <div class="mt-1 text-sm text-zinc-500">
                                            {{ $start->format('H:i') }}
                                            –
                                            {{ $end->format('H:i') }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                        <flux:icon.users class="size-4" />
                                        {{ $signupCount }}/{{ $shift->capacity }}
                                    </div>

                                </div>

                                @if ($shift->location)
                                    <div class="mt-3 flex items-center gap-1.5 text-sm text-zinc-500">
                                        <flux:icon.map-pin class="size-4" />
                                        {{ $shift->location }}
                                    </div>
                                @endif

                                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <div
                                        class="h-full rounded-full bg-blue-600"
                                        style="width: {{ $percentage }}%;"
                                    ></div>
                                </div>

                            </button>

                        @endforeach

                    </div>

                @endif

            </section>

        @endforeach

    </div>


    {{-- Shift detail modal --}}
    <flux:modal
        wire:model="showShiftModal"
        class="md:w-[32rem]"
    >

        @if ($this->selectedShift)

            @php
                $shift = $this->selectedShift;

                $start = \Carbon\Carbon::parse($shift->starts_at);
                $end = \Carbon\Carbon::parse($shift->ends_at);

                $capacity = max((int) $shift->capacity, 1);

                $percentage = min(
                    ($shift->signups_count / $capacity) * 100,
                    100
                );
            @endphp

            <div class="space-y-6">

                <div>
                    <flux:heading size="lg">
                        {{ $shift->title }}
                    </flux:heading>

                    @if ($shift->description)
                        <flux:text class="mt-2">
                            {{ $shift->description }}
                        </flux:text>
                    @endif
                </div>


                {{-- Details --}}
                <div class="space-y-3">

                    <div class="flex items-center gap-3 text-sm">
                        <flux:icon.calendar-days class="size-5 text-zinc-400" />

                        <span>
                            {{ \Carbon\Carbon::parse($shift->date)
                                ->locale('nl')
                                ->translatedFormat('l j F Y') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <flux:icon.clock class="size-5 text-zinc-400" />

                        <span>
                            {{ $start->format('H:i') }}
                            –
                            {{ $end->format('H:i') }}
                        </span>
                    </div>

                    @if ($shift->location)
                        <div class="flex items-center gap-3 text-sm">
                            <flux:icon.map-pin class="size-5 text-zinc-400" />

                            <span>
                                {{ $shift->location }}
                            </span>
                        </div>
                    @endif

                </div>


                {{-- Capacity --}}
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800/50">

                    <div class="flex items-center justify-between">

                        <div>
                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                Bezetting
                            </div>

                            <div class="mt-1 text-sm text-zinc-500">
                                {{ $shift->signups_count }}
                                van
                                {{ $shift->capacity }}
                                plekken gevuld
                            </div>
                        </div>

                        <div class="text-lg font-semibold">
                            {{ round($percentage) }}%
                        </div>

                    </div>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div
                            class="h-full rounded-full bg-blue-600"
                            style="width: {{ $percentage }}%;"
                        ></div>
                    </div>

                </div>


                {{-- Signups --}}
                <div>

                    <div class="mb-3 flex items-center justify-between">

                        <flux:heading size="sm">
                            Ingeschreven crew
                        </flux:heading>

                        <flux:badge>
                            {{ $shift->signups_count }}
                        </flux:badge>

                    </div>


                    @if ($shift->signups->isEmpty())

                        <div class="rounded-lg border border-dashed border-zinc-200 px-4 py-6 text-center text-sm text-zinc-500 dark:border-zinc-700">
                            Nog niemand ingeschreven.
                        </div>

                    @else

                        <div class="max-h-64 divide-y divide-zinc-100 overflow-y-auto dark:divide-zinc-800">

                            @foreach ($shift->signups as $signup)

                                <div class="flex items-center justify-between gap-3 py-3">

                                    <div class="min-w-0">

                                        <div class="truncate text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $signup->name }}
                                        </div>

                                        @if ($signup->hockeyTeam)
                                            <div class="truncate text-xs text-zinc-500">
                                                {{ $signup->hockeyTeam->name }}
                                            </div>
                                        @endif

                                    </div>

                                    <flux:icon.check-circle class="size-5 shrink-0 text-emerald-500" />

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>


                {{-- Footer --}}
                <div class="flex justify-end">

                    <flux:button
                        wire:click="closeShift"
                        variant="ghost"
                    >
                        Sluiten
                    </flux:button>

                </div>

            </div>

        @endif

    </flux:modal>

</div>