<?php

use App\Models\Shift;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $weekStart;

    public ?int $selectedShiftId = null;
    public bool $showShiftModal = false;

    public function mount(): void
    {
        $this->weekStart = now()
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();
    }

    #[Computed]
    public function weekDays()
    {
        $start = Carbon::parse($this->weekStart);

        return collect(range(0, 6))
            ->map(fn (int $day) => $start->copy()->addDays($day));
    }

    #[Computed]
    public function shifts()
    {
        $start = Carbon::parse($this->weekStart)->startOfDay();
        $end = $start->copy()->addDays(6)->endOfDay();

        return Shift::query()
            ->withCount('signups')
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->orderBy('date')
            ->orderBy('starts_at')
            ->get();
    }

    #[Computed]
    public function selectedShift(): ?Shift
    {
        if (! $this->selectedShiftId) {
            return null;
        }

        return Shift::query()
            ->with([
                'signups' => fn ($query) => $query->orderBy('name'),
                'signups.hockeyTeam',
            ])
            ->withCount('signups')
            ->find($this->selectedShiftId);
    }

    public function shiftsForDay(Carbon $day)
    {
        return $this->shifts
            ->filter(
                fn (Shift $shift) =>
                    Carbon::parse($shift->date)->isSameDay($day)
            );
    }

    public function previousWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)
            ->subWeek()
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();

        $this->closeShift();
        unset($this->weekDays, $this->shifts);
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)
            ->addWeek()
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();

        $this->closeShift();
        unset($this->weekDays, $this->shifts);
    }

    public function today(): void
    {
        $this->weekStart = now()
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();

        $this->closeShift();
        unset($this->weekDays, $this->shifts);
    }

    public function selectShift(int $shiftId): void
    {
        $this->selectedShiftId = $shiftId;
        $this->showShiftModal = true;

        unset($this->selectedShift);
    }

    public function closeShift(): void
    {
        $this->showShiftModal = false;
        $this->selectedShiftId = null;

        unset($this->selectedShift);
    }
};