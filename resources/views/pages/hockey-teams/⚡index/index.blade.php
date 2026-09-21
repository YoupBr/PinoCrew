<div class="space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">
                Teams
            </flux:heading>

            <flux:text class="mt-1">
                Beheer de hockeyteams die vrijwilligers kunnen selecteren.
            </flux:text>
        </div>

        <flux:button
            variant="primary"
            icon="plus"
            wire:click="createTeam"
        >
            Team toevoegen
        </flux:button>
    </div>

    @error('team')
        <flux:callout variant="danger" icon="exclamation-triangle">
            {{ $message }}
        </flux:callout>
    @enderror

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Team</th>
                    <th class="px-4 py-3 text-left font-medium">Benodigd</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-right font-medium"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($this->teams as $team)
                    <tr wire:key="team-{{ $team->id }}">
                        <td class="px-4 py-4">
                            <div class="font-medium">
                                {{ $team->name }}
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            {{ $team->required_volunteers }}
                        </td>

                        <td class="px-4 py-4">
                            @if ($team->active)
                                <flux:badge color="green">
                                    Actief
                                </flux:badge>
                            @else
                                <flux:badge color="zinc">
                                    Inactief
                                </flux:badge>
                            @endif
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    wire:click="editTeam({{ $team->id }})"
                                >
                                    Bewerken
                                </flux:button>

                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    wire:click="toggleActive({{ $team->id }})"
                                >
                                    {{ $team->active ? 'Deactiveren' : 'Activeren' }}
                                </flux:button>

                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    wire:click="deleteTeam({{ $team->id }})"
                                    wire:confirm="Weet je zeker dat je dit team wilt verwijderen?"
                                >
                                    Verwijderen
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="4"
                            class="px-4 py-10 text-center text-zinc-500"
                        >
                            Er zijn nog geen teams aangemaakt.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal
        wire:model="showTeamModal"
        class="md:w-[32rem]"
    >
        <form wire:submit="saveTeam" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingTeamId ? 'Team bewerken' : 'Team toevoegen' }}
                </flux:heading>

                <flux:text class="mt-1">
                    Stel de gegevens van het hockeyteam in.
                </flux:text>
            </div>

            <flux:input
                wire:model="name"
                label="Teamnaam"
                placeholder="Bijvoorbeeld JO18-1"
                required
            />

            <flux:input
                wire:model="required_volunteers"
                type="number"
                min="0"
                label="Benodigde vrijwilligers"
                description="Het gewenste aantal vrijwilligers vanuit dit team."
                required
            />

            <flux:switch
                wire:model="active"
                label="Team actief"
                description="Alleen actieve teams zijn beschikbaar bij inschrijven."
            />

            <div class="flex justify-end gap-2">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="$set('showTeamModal', false)"
                >
                    Annuleren
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                >
                    {{ $editingTeamId ? 'Wijzigingen opslaan' : 'Team toevoegen' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

</div>