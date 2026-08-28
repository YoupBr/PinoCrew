<div class="flex flex-col gap-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">
                Diensten
            </flux:heading>

            <flux:subheading>
                Maak en beheer de vrijwilligersdiensten van PinoCrew.
            </flux:subheading>
        </div>

        <flux:button
            variant="primary"
            icon="plus"
            wire:click="createShift"
        >
            Nieuwe dienst
        </flux:button>
    </div>

    @error('delete')
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
            {{ $message }}
        </div>
    @enderror

    @if ($showForm)
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">

            <div class="mb-6">
                <flux:heading size="lg">
                    {{ $editingShiftId ? 'Dienst bewerken' : 'Nieuwe dienst' }}
                </flux:heading>

                <flux:subheading>
                    Vul de gegevens van de dienst in.
                </flux:subheading>
            </div>

            <form wire:submit="saveShift" class="flex flex-col gap-6">

                <div class="grid gap-6 lg:grid-cols-2">

                    <div class="lg:col-span-2">
                        <flux:input
                            wire:model="title"
                            label="Titel"
                            placeholder="Bijvoorbeeld: Opbouw hal"
                            required
                        />
                    </div>

                    <flux:input
                        wire:model="date"
                        type="date"
                        label="Datum"
                        required
                    />

                    <flux:input
                        wire:model="location"
                        label="Locatie"
                        placeholder="Bijvoorbeeld: Pinoké Dome"
                    />

                    <flux:input
                        wire:model="starts_at"
                        type="time"
                        label="Starttijd"
                        required
                    />

                    <flux:input
                        wire:model="ends_at"
                        type="time"
                        label="Eindtijd"
                    />

                    <flux:input
                        wire:model="capacity"
                        type="number"
                        min="1"
                        label="Maximaal aantal vrijwilligers"
                        placeholder="Geen limiet"
                    />

                    <div class="flex items-end">
                        <flux:checkbox
                            wire:model="is_published"
                            label="Direct publiceren"
                        />
                    </div>

                    <div class="lg:col-span-2">
                        <flux:textarea
                            wire:model="description"
                            label="Beschrijving"
                            rows="4"
                            placeholder="Omschrijf kort wat vrijwilligers tijdens deze dienst gaan doen..."
                        />
                    </div>

                </div>

                <div class="flex justify-end gap-3">
                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="cancelForm"
                    >
                        Annuleren
                    </flux:button>

                    <flux:button
                        type="submit"
                        variant="primary"
                    >
                        {{ $editingShiftId ? 'Wijzigingen opslaan' : 'Dienst aanmaken' }}
                    </flux:button>
                </div>

            </form>

        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

        <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">
                Alle diensten
            </flux:heading>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">

                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Dienst
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Datum
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Locatie
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Inschrijvingen
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Status
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            Acties
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                    @forelse ($this->shifts as $shift)

                        <tr
                            wire:key="shift-{{ $shift->id }}"
                            class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                        >
                            <td class="px-5 py-4">
                                <div class="font-semibold">
                                    {{ $shift->title }}
                                </div>

                                @if ($shift->description)
                                    <div class="mt-1 max-w-md truncate text-sm text-zinc-500">
                                        {{ $shift->description }}
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="font-medium">
                                    {{ $shift->date->translatedFormat('d F Y') }}
                                </div>

                                <div class="mt-1 text-sm text-zinc-500">
                                    {{ substr($shift->starts_at, 0, 5) }}

                                    @if ($shift->ends_at)
                                        – {{ substr($shift->ends_at, 0, 5) }}
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4 text-sm">
                                {{ $shift->location ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="font-semibold">
                                    {{ $shift->signups_count }}

                                    @if ($shift->capacity)
                                        / {{ $shift->capacity }}
                                    @endif
                                </div>

                                <div class="text-xs text-zinc-500">
                                    vrijwilligers
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($shift->is_published)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-950 dark:text-green-300">
                                        Gepubliceerd
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                        Concept
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <flux:dropdown position="bottom" align="end">

                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="ellipsis-horizontal"
                                    />

                                    <flux:menu>

                                        <flux:menu.item
                                            icon="pencil-square"
                                            wire:click="editShift({{ $shift->id }})"
                                        >
                                            Bewerken
                                        </flux:menu.item>

                                        <flux:menu.item
                                            icon="{{ $shift->is_published ? 'eye-slash' : 'eye' }}"
                                            wire:click="togglePublished({{ $shift->id }})"
                                        >
                                            {{ $shift->is_published ? 'Depubliceren' : 'Publiceren' }}
                                        </flux:menu.item>

                                        <flux:menu.separator />

                                        <flux:menu.item
                                            variant="danger"
                                            icon="trash"
                                            wire:click="deleteShift({{ $shift->id }})"
                                            wire:confirm="Weet je zeker dat je deze dienst wilt verwijderen?"
                                        >
                                            Verwijderen
                                        </flux:menu.item>

                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <div class="mb-4 rounded-full bg-zinc-100 p-4 dark:bg-zinc-800">
                                        <flux:icon.calendar-days class="size-7 text-zinc-400" />
                                    </div>

                                    <div class="font-semibold">
                                        Nog geen diensten
                                    </div>

                                    <div class="mt-1 text-sm text-zinc-500">
                                        Maak de eerste vrijwilligersdienst aan.
                                    </div>

                                    <flux:button
                                        class="mt-5"
                                        variant="primary"
                                        icon="plus"
                                        wire:click="createShift"
                                    >
                                        Nieuwe dienst
                                    </flux:button>
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

</div>