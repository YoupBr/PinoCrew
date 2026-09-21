<?php

use App\Models\HockeyTeam;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $editingTeamId = null;

    public string $name = '';
    public int $required_volunteers = 0;
    public bool $active = true;

    public bool $showTeamModal = false;

    #[Computed]
    public function teams()
    {
        return HockeyTeam::query()
            ->orderBy('name')
            ->get();
    }

    public function createTeam(): void
    {
        $this->resetForm();

        $this->showTeamModal = true;
    }

    public function editTeam(int $teamId): void
    {
        $team = HockeyTeam::findOrFail($teamId);

        $this->editingTeamId = $team->id;
        $this->name = $team->name;
        $this->required_volunteers = $team->required_volunteers ?? 0;
        $this->active = (bool) $team->active;

        $this->showTeamModal = true;
    }

    public function saveTeam(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'required_volunteers' => ['required', 'integer', 'min:0'],
            'active' => ['boolean'],
        ]);

        if ($this->editingTeamId) {
            HockeyTeam::findOrFail($this->editingTeamId)
                ->update($validated);
        } else {
            HockeyTeam::create($validated);
        }

        $this->showTeamModal = false;

        $this->resetForm();

        unset($this->teams);
    }

    public function toggleActive(int $teamId): void
    {
        $team = HockeyTeam::findOrFail($teamId);

        $team->update([
            'active' => ! $team->active,
        ]);

        unset($this->teams);
    }

    public function deleteTeam(int $teamId): void
    {
        $team = HockeyTeam::findOrFail($teamId);

        if ($team->signups()->exists()) {
            $this->addError(
                'team',
                'Dit team kan niet worden verwijderd omdat er aanmeldingen aan gekoppeld zijn.'
            );

            return;
        }

        $team->delete();

        unset($this->teams);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingTeamId',
            'name',
            'required_volunteers',
            'active',
        ]);

        $this->required_volunteers = 0;
        $this->active = true;

        $this->resetValidation();
    }
};