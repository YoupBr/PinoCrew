<x-layouts::app title="Aanwezigheid">
    <div class="flex flex-col gap-8">
        <div>
            <flux:heading size="xl">Aanwezigheid per dienst</flux:heading>
            <flux:text class="mt-2">Bekijk de opkomst en corrigeer een vergeten of onjuiste aanmelding.</flux:text>
            <a href="{{ route('attendance.index') }}" target="_blank" rel="noopener" class="mt-3 inline-block text-sm underline">Open openbare aanmeldpagina</a>
        </div>
        @if (session('status'))<p role="status" class="rounded-xl bg-green-50 p-4 text-green-800">{{ session('status') }}</p>@endif
        @if ($errors->any())<p role="alert">Controleer de invoer en probeer opnieuw.</p>@endif
        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-left text-sm">
                <thead><tr class="border-b border-zinc-200 dark:border-zinc-700"><th class="p-4">Dienst</th><th class="p-4">Datum</th><th class="p-4">Aanwezig / ingeschreven</th><th class="p-4">Opkomst</th><th class="p-4">Details</th></tr></thead>
                <tbody>
                    @forelse ($shifts as $shift)
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <td class="p-4">{{ $shift->title }}</td><td class="p-4">{{ $shift->date->format('d-m-Y') }} · {{ substr($shift->starts_at, 0, 5) }}</td>
                            <td class="p-4">{{ $shift->present_count }} / {{ $shift->signups_count }}</td>
                            <td class="p-4">{{ $shift->signups_count ? round(100 * $shift->present_count / $shift->signups_count) : 0 }}%</td>
                            <td class="p-4"><a class="underline" href="{{ route('attendance-report.index', ['current_team' => request()->route('current_team'), 'shift' => $shift->id]) }}">Bekijk</a></td>
                        </tr>
                    @empty<tr><td colspan="5" class="p-4">Er zijn nog geen diensten.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        {{ $shifts->links() }}
        @if ($selectedShift)
            <section class="space-y-5">
                <flux:heading size="lg">{{ $selectedShift->title }} · {{ $selectedShift->date->format('d-m-Y') }}</flux:heading>
                <flux:text>{{ $totals['present'] }} aanwezig · {{ $totals['absent'] }} afwezig bevestigd · {{ $totals['registered'] - $totals['present'] - $totals['absent'] }} niet aangemeld · {{ $totals['registered'] }} ingeschreven</flux:text>
                <p class="text-sm text-zinc-500">‘Niet aangemeld’ is geen bewijs van afwezigheid. Controleer na afloop of iemand vergeten is te scannen. Voor diensten van vóór invoering van deze functie zijn geen aanwezigheidsgegevens beschikbaar.</p>
                <form method="GET" action="{{ route('attendance-report.index', ['current_team' => request()->route('current_team')]) }}" class="flex flex-wrap items-end gap-3">
                    <input type="hidden" name="shift" value="{{ $selectedShift->id }}">
                    <flux:select name="status" label="Filter aanwezigheid">
                        <option value="" @selected(! request('status'))>Iedereen</option>
                        <option value="present" @selected(request('status') === 'present')>Aanwezig</option>
                        <option value="absent" @selected(request('status') === 'absent')>Afwezig bevestigd</option>
                        <option value="missing" @selected(request('status') === 'missing')>Niet aangemeld</option>
                    </flux:select>
                    <flux:button type="submit">Filteren</flux:button>
                </form>
                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-left text-sm">
                        <thead><tr><th class="p-4">Naam</th><th class="p-4">Team</th><th class="p-4">Status</th><th class="p-4">Correctie</th></tr></thead>
                        <tbody>
                            @forelse ($signups as $signup)
                                <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                    <td class="p-4">{{ $signup->name }}</td><td class="p-4">{{ $signup->hockeyTeam->name }}</td>
                                    <td class="p-4">
                                        @if ($signup->checked_in_at)
                                            Aanwezig · {{ $signup->checked_in_at->timezone('Europe/Amsterdam')->format('d-m H:i') }}
                                            @if ($signup->checked_in_by) <span class="text-zinc-500">(beheerder)</span> @endif
                                        @elseif ($signup->absence_confirmed_at)
                                            Afwezig bevestigd
                                        @elseif ($selectedShift->attendanceEndsAt()->isPast())
                                            Niet aangemeld · controleren
                                        @else
                                            Nog niet aangemeld
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('attendance-report.update', ['current_team' => request()->route('current_team'), 'signup' => $signup->id]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex flex-wrap gap-2">
                                                @if (! $signup->checked_in_at)
                                                    <flux:button type="submit" name="action" value="present" size="sm">Aanwezig melden</flux:button>
                                                @endif
                                                @if ($selectedShift->attendanceEndsAt()->isPast() && ! $signup->absence_confirmed_at)
                                                    <flux:button type="submit" name="action" value="absent" size="sm">Afwezig bevestigen</flux:button>
                                                @endif
                                                @if ($signup->checked_in_at || $signup->absence_confirmed_at)
                                                    <flux:button type="submit" name="action" value="reset" size="sm">Registratie wissen</flux:button>
                                                @endif
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty<tr><td colspan="4" class="p-4">Geen inschrijvingen voor dit filter.</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
                {{ $signups->links() }}
                <flux:heading size="lg">Opkomst per hockeyteam</flux:heading>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($teams as $team)
                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700"><strong>{{ $team['name'] }}</strong><p class="mt-2">{{ $team['present'] }} / {{ $team['registered'] }} aanwezig · {{ $team['absent'] }} afwezig bevestigd · {{ $team['registered'] - $team['present'] - $team['absent'] }} niet aangemeld</p></div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts::app>
