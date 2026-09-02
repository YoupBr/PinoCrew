<?php

use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $team = '';

    #[Url]
    public string $shift = '';

    #[Url]
    public string $sort = 'newest';

    public string $shiftFilter = '';
    public string $teamFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTeam(): void
    {
        $this->resetPage();
    }

    public function updatedShift(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'team',
            'shift',
            'sort',
        ]);

        $this->sort = 'newest';

        $this->resetPage();
    }

    public function deleteSignup(int $signupId): void
    {
        Signup::query()->findOrFail($signupId)->delete();

        unset($this->signups);

        $this->resetPage();
    }

    #[Computed]
    public function signups()
    {
        return Signup::query()
            ->with([
                'shift',
                'hockeyTeam',
            ])

            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search)

                        ->orWhereHas('hockeyTeam', function ($query) use ($search) {
                            $query->where('name', 'like', $search);
                        })

                        ->orWhereHas('shift', function ($query) use ($search) {
                            $query->where('title', 'like', $search);
                        });
                });
            })

            ->when(
                $this->team !== '',
                fn ($query) =>
                    $query->where('hockey_team_id', $this->team)
            )

            ->when(
                $this->shift !== '',
                fn ($query) =>
                    $query->where('shift_id', $this->shift)
            )

            ->when(
                $this->sort === 'oldest',
                fn ($query) => $query->oldest()
            )

            ->when(
                $this->sort === 'newest',
                fn ($query) => $query->latest()
            )

            ->when(
                $this->sort === 'name',
                fn ($query) => $query->orderBy('name')
            )

            ->paginate(15);
    }

    #[Computed]
    public function hockeyTeams()
    {
        return HockeyTeam::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function shifts()
    {
        return Shift::query()
            ->orderByDesc('date')
            ->orderBy('starts_at')
            ->get();
    }

    #[Computed]
    public function totalSignups(): int
    {
        return Signup::query()->count();
    }

    #[Computed]
    public function signupsToday(): int
    {
        return Signup::query()
            ->whereDate('created_at', today())
            ->count();
    }

    #[Computed]
    public function representedTeams(): int
    {
        return Signup::query()
            ->whereNotNull('hockey_team_id')
            ->distinct()
            ->count('hockey_team_id');
    }

    #[Computed]
    public function upcomingSignups(): int
    {
        return Signup::query()
            ->whereHas('shift', function ($query) {
                $query->whereDate('date', '>=', today());
            })
            ->count();
    }

        public function exportCsv()
        {
        $signups = $this->signups;

        return response()->streamDownload(function () use ($signups) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Naam',
                'E-mail',
                'Telefoon',
                'Team',
                'Dienst',
                'Datum',
                'Tijd',
            ], ';');

            foreach ($signups as $signup) {
                fputcsv($handle, [
                    $signup->name,
                    $signup->email,
                    $signup->phone,
                    $signup->hockeyTeam?->name,
                    $signup->shift?->title,
                    $signup->shift?->date?->format('d-m-Y'),
                    $signup->shift
                        ? $signup->shift->starts_at . ' - ' . $signup->shift->ends_at
                        : '',
                ], ';');
            }

            fclose($handle);
        }, 'pinocrew-aanmeldingen.csv');
    }
};