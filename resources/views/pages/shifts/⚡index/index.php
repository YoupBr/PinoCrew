<?php

use App\Models\Shift;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component
{
    public bool $showForm = false;

    public ?int $editingShiftId = null;

    public string $title = '';
    public string $description = '';
    public string $date = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $location = '';
    public ?int $capacity = null;
    public bool $is_published = false;

    #[Computed]
    public function shifts()
    {
        return Shift::query()
            ->withCount('signups')
            ->orderByDesc('date')
            ->orderBy('starts_at')
            ->get();
    }

    public function createShift(): void
    {
        $this->resetForm();

        $this->showForm = true;
    }

    public function editShift(int $shiftId): void
    {
        $shift = Shift::findOrFail($shiftId);

        $this->editingShiftId = $shift->id;
        $this->title = $shift->title;
        $this->description = $shift->description ?? '';
        $this->date = $shift->date?->format('Y-m-d') ?? '';
        $this->starts_at = $shift->starts_at ?? '';
        $this->ends_at = $shift->ends_at ?? '';
        $this->location = $shift->location ?? '';
        $this->capacity = $shift->capacity;
        $this->is_published = (bool) $shift->is_published;

        $this->showForm = true;
    }

    public function saveShift(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'starts_at' => ['required'],
            'ends_at' => ['nullable'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['boolean'],
        ]);

        if ($this->editingShiftId) {
            Shift::findOrFail($this->editingShiftId)->update($validated);
        } else {
            Shift::create($validated);
        }

        $this->resetForm();
        $this->showForm = false;

        unset($this->shifts);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function togglePublished(int $shiftId): void
    {
        $shift = Shift::findOrFail($shiftId);

        $shift->update([
            'is_published' => ! $shift->is_published,
        ]);

        unset($this->shifts);
    }

    public function deleteShift(int $shiftId): void
    {
        $shift = Shift::withCount('signups')->findOrFail($shiftId);

        if ($shift->signups_count > 0) {
            $this->addError(
                'delete',
                'Deze dienst heeft al inschrijvingen en kan daarom niet zomaar worden verwijderd.'
            );

            return;
        }

        $shift->delete();

        unset($this->shifts);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingShiftId',
            'title',
            'description',
            'date',
            'starts_at',
            'ends_at',
            'location',
            'capacity',
            'is_published',
        ]);

        $this->resetValidation();
    }
};