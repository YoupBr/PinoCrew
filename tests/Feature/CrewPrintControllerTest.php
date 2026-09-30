<?php

use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;

test('crew print page filters signups by shift, team and search text', function () {
    $morningShift = Shift::create([
        'title' => 'Morning Shift',
        'date' => '2026-09-30',
        'starts_at' => '09:00:00',
        'ends_at' => '11:00:00',
        'location' => 'Arena North',
        'capacity' => 20,
        'is_published' => true,
    ]);

    $eveningShift = Shift::create([
        'title' => 'Evening Shift',
        'date' => '2026-09-30',
        'starts_at' => '18:00:00',
        'ends_at' => '20:00:00',
        'location' => 'Arena South',
        'capacity' => 20,
        'is_published' => true,
    ]);

    $leftTeam = HockeyTeam::create(['name' => 'Left Team']);
    $rightTeam = HockeyTeam::create(['name' => 'Right Team']);

    $match = Signup::create([
        'shift_id' => $morningShift->id,
        'hockey_team_id' => $leftTeam->id,
        'name' => 'Alice Example',
        'email' => 'alice@example.com',
    ]);

    Signup::create([
        'shift_id' => $morningShift->id,
        'hockey_team_id' => $rightTeam->id,
        'name' => 'Bob Example',
        'email' => 'bob@example.com',
    ]);

    Signup::create([
        'shift_id' => $eveningShift->id,
        'hockey_team_id' => $leftTeam->id,
        'name' => 'Charlie Example',
        'email' => 'charlie@example.com',
    ]);

    $response = $this->get(route('crew.print', [
        'shift' => $morningShift->id,
        'team' => $leftTeam->id,
        'search' => 'alice',
    ]));

    $response->assertOk();
    $response->assertViewHas('signups', fn ($signups) => $signups->count() === 1 && $signups->first()->id === $match->id);
    $response->assertViewHas('shift', fn ($shift) => $shift->is($morningShift));
    $response->assertViewHas('hockeyTeam', fn ($team) => $team->is($leftTeam));
});
