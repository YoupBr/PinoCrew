<div class="flex flex-col gap-8">

    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            Mail
        </flux:heading>

        <flux:subheading>
            Stuur berichten naar vrijwilligers binnen PinoCrew.
        </flux:subheading>
    </div>

    @if ($sent)
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
            De e-mail is verzonden.
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">

        {{-- Ontvangers --}}
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

            <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                <div class="flex items-center justify-between">

                    <div>
                        <flux:heading size="lg">
                            Ontvangers
                        </flux:heading>

                        <flux:subheading>
                            {{ count($selected) }} geselecteerd
                        </flux:subheading>
                    </div>

                    <div class="flex gap-2">
                        <flux:button
                            size="sm"
                            wire:click="selectAll"
                        >
                            Alles selecteren
                        </flux:button>

                        @if (count($selected))
                            <flux:button
                                size="sm"
                                variant="ghost"
                                wire:click="deselectAll"
                            >
                                Wissen
                            </flux:button>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Filters --}}
            <div class="grid gap-4 border-b border-zinc-200 p-5 md:grid-cols-3 dark:border-zinc-700">

                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="Zoeken..."
                />

                <flux:select wire:model.live="shiftFilter">
                    <option value="">Alle diensten</option>

                    @foreach ($this->shifts as $shift)
                        <option value="{{ $shift->id }}">
                            {{ $shift->title }}
                        </option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="teamFilter">
                    <option value="">Alle teams</option>

                    @foreach ($this->hockeyTeams as $team)
                        <option value="{{ $team->id }}">
                            {{ $team->name }}
                        </option>
                    @endforeach
                </flux:select>

            </div>

            {{-- Crew --}}
            <div class="max-h-[600px] divide-y divide-zinc-100 overflow-y-auto dark:divide-zinc-800">

                @forelse ($this->recipients as $recipient)

                    <label
                        wire:key="recipient-{{ $recipient->id }}"
                        class="flex cursor-pointer items-center gap-4 p-4 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                    >

                        <input
                            type="checkbox"
                            value="{{ $recipient->id }}"
                            wire:model.live="selected"
                            class="size-4 rounded border-zinc-300"
                        >

                        <div class="min-w-0 flex-1">

                            <div class="font-medium">
                                {{ $recipient->name }}
                            </div>

                            <div class="truncate text-sm text-zinc-500">
                                {{ $recipient->email }}
                            </div>

                            <div class="mt-1 flex flex-wrap gap-2 text-xs text-zinc-400">

                                @if ($recipient->hockeyTeam)
                                    <span>
                                        {{ $recipient->hockeyTeam->name }}
                                    </span>
                                @endif

                                @if ($recipient->shift)
                                    <span>·</span>

                                    <span>
                                        {{ $recipient->shift->title }}
                                    </span>
                                @endif

                            </div>

                        </div>

                    </label>

                @empty

                    <div class="p-10 text-center text-sm text-zinc-500">
                        Geen ontvangers gevonden.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- Bericht --}}
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">

            <div class="mb-6">
                <flux:heading size="lg">
                    Nieuw bericht
                </flux:heading>

                <flux:subheading>
                    Van PinoCrew
                    &lt;zaalhockey@pinocrew.nl&gt;
                </flux:subheading>
            </div>

            <form wire:submit="send" class="space-y-6">

                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800">

                    <div class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                        Ontvangers
                    </div>

                    <div class="mt-1 text-sm font-semibold">
                        {{ count($selected) }} vrijwilliger(s)
                    </div>

                </div>

                <flux:input
                    wire:model="subject"
                    label="Onderwerp"
                    placeholder="Onderwerp van de e-mail"
                />

                <flux:textarea
                    wire:model="body"
                    label="Bericht"
                    rows="14"
                    placeholder="Typ hier je bericht..."
                />

                @error('selected')
                    <div class="text-sm font-medium text-red-600">
                        {{ $message }}
                    </div>
                @enderror

                <flux:button
                    type="submit"
                    variant="primary"
                    icon="paper-airplane"
                    class="w-full"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="send">
                        Mail versturen
                    </span>

                    <span wire:loading wire:target="send">
                        Bezig met versturen...
                    </span>
                </flux:button>

            </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

    <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
        <div class="flex items-center justify-between gap-4">

            <div>
                <flux:heading size="lg">
                    Verzendgeschiedenis
                </flux:heading>

                <flux:subheading>
                    De laatste 25 verzonden berichten.
                </flux:subheading>
            </div>

        </div>
    </div>

    @if ($this->mailLogs->isEmpty())

        <div class="p-10 text-center text-sm text-zinc-500">
            Er zijn nog geen e-mails verzonden.
        </div>

    @else

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">

                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-5 py-3 font-medium">
                            Onderwerp
                        </th>

                        <th class="px-5 py-3 font-medium">
                            Verzonden door
                        </th>

                        <th class="px-5 py-3 font-medium">
                            Ontvangers
                        </th>

                        <th class="px-5 py-3 font-medium">
                            Datum
                        </th>

                        <th class="px-5 py-3 text-right font-medium">
                            Actie
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                    @foreach ($this->mailLogs as $log)

                        <tr
                            wire:key="mail-log-{{ $log->id }}"
                            class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40">

                            <td class="px-5 py-4">

                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $log->subject }}
                                </div>

                                <div class="mt-1 max-w-md truncate text-xs text-zinc-500">
                                    {{ $log->body }}
                                </div>

                            </td>

                            <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">
                                {{ $log->user?->name ?? 'Onbekend' }}
                            </td>

                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                    {{ $log->recipient_count }}
                                </span>

                            </td>

                            <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">

                                @if ($log->sent_at)
                                    <div>
                                        {{ $log->sent_at->translatedFormat('d M Y') }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-zinc-400">
                                        {{ $log->sent_at->format('H:i') }}
                                    </div>
                                @else
                                    —
                                @endif

                            </td>

                            <td class="px-5 py-4 text-right">

                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    wire:click="openMailLog({{ $log->id }})">
                                    Bekijken
                                </flux:button>

                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    @endif
    </div>
    </div>
    </div>
</div>