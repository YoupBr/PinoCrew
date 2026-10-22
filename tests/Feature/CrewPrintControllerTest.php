<?php

use App\Enums\TeamRole;
use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('guests cannot access the crew print page', function (string $method) {
    $this->call($method, route('crew.print'))
        ->assertRedirect(route('login'));
})->with(['GET', 'HEAD']);

test('crew export requires an admin or owner membership of the Pinoke team', function (
    string $slug,
    TeamRole $role,
    bool $allowed,
) {
    $user = User::factory()->create();
    $team = Team::factory()->create(['slug' => $slug]);
    $team->members()->attach($user, ['role' => $role->value]);

    expect(Gate::forUser($user)->allows('export-crew'))->toBe($allowed);
})->with([
    'Pinoke admin' => ['pinoke', TeamRole::Admin, true],
    'Pinoke owner' => ['pinoke', TeamRole::Owner, true],
    'Pinoke member' => ['pinoke', TeamRole::Member, false],
    'other team admin' => ['other-team', TeamRole::Admin, false],
    'other team owner' => ['other-team', TeamRole::Owner, false],
]);

test('ordinary Pinoke members cannot access the crew print page', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['slug' => 'pinoke']);
    $team->members()->attach($user, ['role' => TeamRole::Member->value]);
    $user->switchTeam($team);

    $this->actingAs($user)->get(route('crew.print'))->assertForbidden();
});

test('owning a personal team does not grant access to the crew print page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('crew.print'))->assertForbidden();
});

test('removed Pinoke admins cannot print using a stale current team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['slug' => 'pinoke']);
    $team->members()->attach($user, ['role' => TeamRole::Admin->value]);
    $user->switchTeam($team);
    $team->members()->detach($user);

    $this->actingAs($user)->get(route('crew.print'))->assertForbidden();
});

test('crew print page filters signups by shift, team and search text', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['slug' => 'pinoke']);
    $team->members()->attach($user, ['role' => TeamRole::Admin->value]);

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
        'phone' => '0612345678',
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

    $response = $this->actingAs($user)->get(route('crew.print', [
        'shift' => $morningShift->id,
        'team' => $leftTeam->id,
        'search' => 'alice',
    ]));

    $response->assertOk();
    $response->assertHeader('Cache-Control', 'no-store, private');
    $response->assertSee('Alice Example')->assertSee('0612345678');
    $response->assertDontSee('Bob Example')->assertDontSee('Charlie Example');
    $response->assertViewHas('signups', fn ($signups) => $signups->count() === 1 && $signups->first()->id === $match->id);
    $response->assertViewHas('shift', fn ($shift) => $shift->is($morningShift));
    $response->assertViewHas('hockeyTeam', fn ($team) => $team->is($leftTeam));
});
