<?php

use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component
{
    #[Computed]
    public function upcomingShifts()
    {
        return Shift::query()
            ->withCount('signups')
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('starts_at')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function hockeyTeams()
    {
        return HockeyTeam::query()
            ->where('active', true)
            ->withCount('signups')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function openShifts(): int
    {
        return Shift::query()
            ->where('is_published', true)
            ->whereDate('date', '>=', today())
            ->count();
    }

    #[Computed]
    public function signupCount(): int
    {
        return Signup::count();
    }

    #[Computed]
    public function teamsOnTarget(): int
    {
        return $this->hockeyTeams
            ->filter(
                fn ($team) =>
                $team->signups_count >= $team->required_volunteers
            )
            ->count();
    }

    #[Computed]
    public function teamsBelowTarget(): int
    {
        return $this->hockeyTeams
            ->filter(
                fn ($team) =>
                $team->signups_count < $team->required_volunteers
            )
            ->count();
    }
};