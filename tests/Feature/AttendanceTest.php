<?php

use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

beforeEach(function () {
    $this->withoutVite();
});

function attendanceSignup(array $shiftAttributes = [], array $signupAttributes = []): Signup
{
    $shift = Shift::create(array_merge([
        'title' => 'Blaashal opbouw', 'date' => '2026-10-16',
        'starts_at' => '10:00:00', 'ends_at' => '12:00:00', 'is_published' => true,
    ], $shiftAttributes));
    $team = HockeyTeam::create(['name' => 'Heren '.(HockeyTeam::count() + 3), 'active' => true]);

    return Signup::create(array_merge([
        'shift_id' => $shift->id, 'hockey_team_id' => $team->id,
        'name' => 'Youp Bronkhorst', 'email' => 'private@example.com', 'phone' => '0612345678',
    ], $signupAttributes));
}

test('public attendance page is available without an account', function () {
    $this->get(route('attendance.index'))->assertSee('Je naam')->assertDontSee('attendance-team', false);
});

test('autocomplete finds names across teams without exposing contact details', function () {
    $this->travelTo(Carbon::parse('2026-10-16 10:30', 'Europe/Amsterdam'));
    $signup = attendanceSignup();
    attendanceSignup([], ['name' => 'Someone Else']);
    attendanceSignup(['is_published' => false]);
    attendanceSignup(['date' => '2026-10-15']);

    $response = $this->getJson(route('attendance.search', ['name' => 'You']));

    $response->assertJsonCount(1, 'matches')
        ->assertJsonPath('matches.0.label', 'Youp Bronkhorst · Heren 3 · Blaashal opbouw · 10:00')
        ->assertDontSee('private@example.com')->assertDontSee('0612345678')->assertDontSee('Someone Else')
        ->assertHeader('Cache-Control', 'no-store, private');
});

test('a matching signup is hidden before the check-in window', function () {
    $this->travelTo(Carbon::parse('2026-10-16 07:59', 'Europe/Amsterdam'));
    $signup = attendanceSignup();

    $this->getJson(route('attendance.search', ['name' => 'You']))
        ->assertJsonCount(0, 'matches');
});

test('search rejects short names and wildcard enumeration', function (string $name) {
    $this->getJson(route('attendance.search', ['name' => $name]))
        ->assertUnprocessable()->assertJsonValidationErrors('name');
})->with(['short' => 'Yo', 'percent' => 'You%', 'underscore' => 'You_', 'slash' => 'You\\']);

test('public check-in records the original arrival time only once', function () {
    $this->travelTo(Carbon::parse('2026-10-16 10:30', 'Europe/Amsterdam'));
    $signup = attendanceSignup();
    $token = $this->getJson(route('attendance.search', ['name' => 'You']))
        ->json('matches.0.token');

    expect($token)->toBeString()->not->toBeEmpty();
    $this->withCookie(config('session.cookie'), session()->getId());

    $this->post(route('attendance.store'), ['token' => $token])->assertRedirect(route('attendance.index'));
    $arrival = $signup->fresh()->checked_in_at;
    expect($arrival)->not->toBeNull();
    $this->travel(1)->minutes();
    $this->post(route('attendance.store'), ['token' => $token])->assertRedirect(route('attendance.index'));

    expect($signup->fresh()->checked_in_at->timestamp)->toBe($arrival->timestamp);
});

test('public check-in rejects forged selections', function () {
    $signup = attendanceSignup();

    $this->postJson(route('attendance.store'), ['token' => (string) $signup->id])
        ->assertUnprocessable()->assertJsonValidationErrors('token');

    expect($signup->fresh()->checked_in_at)->toBeNull();
});

test('public check-in rejects expired tokens and closed shifts', function (int $minutes) {
    $this->travelTo(Carbon::parse('2026-10-16 11:55', 'Europe/Amsterdam'));
    $signup = attendanceSignup();
    $token = $this->getJson(route('attendance.search', ['name' => 'You']))
        ->json('matches.0.token');

    expect($token)->toBeString()->not->toBeEmpty();
    $this->withCookie(config('session.cookie'), session()->getId());
    $this->travel($minutes)->minutes();

    $this->postJson(route('attendance.store'), ['token' => $token])
        ->assertUnprocessable()->assertJsonValidationErrors('token');

    expect($signup->fresh()->checked_in_at)->toBeNull();
})->with(['shift closed with valid token' => 6, 'expired token' => 11]);

test('tokens cannot be used from a different browser session', function () {
    $this->travelTo(Carbon::parse('2026-10-16 10:30', 'Europe/Amsterdam'));
    $signup = attendanceSignup();
    $token = Crypt::encryptString(json_encode([
        'signup' => $signup->id, 'expires' => now()->addMinutes(10)->timestamp, 'session' => 'another-session',
    ]));

    $this->postJson(route('attendance.store'), ['token' => $token])
        ->assertUnprocessable()->assertJsonValidationErrors('token');

    expect($signup->fresh()->checked_in_at)->toBeNull();
});

test('overnight shifts can still check in after midnight', function () {
    $this->travelTo(Carbon::parse('2026-10-17 00:30', 'Europe/Amsterdam'));
    $signup = attendanceSignup(['starts_at' => '23:00:00', 'ends_at' => '01:00:00']);

    $this->getJson(route('attendance.search', ['name' => 'You']))
        ->assertJsonCount(1, 'matches');
});

test('attendance reports and corrections require authentication', function () {
    $signup = attendanceSignup();
    $user = User::factory()->create();

    $this->get(route('attendance-report.index', ['current_team' => $user->currentTeam->slug]))
        ->assertRedirect(route('login'));
    $this->patch(route('attendance-report.update', ['current_team' => $user->currentTeam->slug, 'signup' => $signup->id]), ['action' => 'present'])
        ->assertRedirect(route('login'));

    expect($signup->fresh()->checked_in_at)->toBeNull();
});

test('users cannot modify attendance through a team they do not belong to', function () {
    $signup = attendanceSignup();
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->patch(route('attendance-report.update', [
        'current_team' => $other->currentTeam->slug, 'signup' => $signup->id,
    ]), ['action' => 'present'])->assertForbidden();

    expect($signup->fresh()->checked_in_at)->toBeNull();
});

test('administrators can correct attendance and view shift and team totals', function () {
    $this->travelTo(Carbon::parse('2026-10-16 13:00', 'Europe/Amsterdam'));
    $signup = attendanceSignup();
    $user = User::factory()->create();
    $parameters = ['current_team' => $user->currentTeam->slug, 'signup' => $signup->id];

    $this->actingAs($user)->patch(route('attendance-report.update', $parameters), ['action' => 'present'])
        ->assertRedirect();
    expect($signup->fresh()->checked_in_by)->toBe($user->id);
    $this->get(route('attendance-report.index', ['current_team' => $user->currentTeam->slug, 'shift' => $signup->shift_id]))
        ->assertViewHas('totals', ['registered' => 1, 'present' => 1, 'absent' => 0])
        ->assertSee('Youp Bronkhorst')->assertSee('Heren 3');
    $this->patch(route('attendance-report.update', $parameters), ['action' => 'absent'])->assertRedirect();
    expect($signup->fresh()->absence_confirmed_at)->not->toBeNull();
    expect($signup->fresh()->checked_in_at)->toBeNull();
    $this->patch(route('attendance-report.update', $parameters), ['action' => 'reset'])->assertRedirect();
    expect($signup->fresh()->absence_confirmed_at)->toBeNull();
});

test('absence cannot be confirmed before a shift ends', function () {
    $this->travelTo(Carbon::parse('2026-10-16 10:30', 'Europe/Amsterdam'));
    $signup = attendanceSignup();
    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('attendance-report.update', [
        'current_team' => $user->currentTeam->slug, 'signup' => $signup->id,
    ]), ['action' => 'absent'])->assertSessionHasErrors('action');

    expect($signup->fresh()->absence_confirmed_at)->toBeNull();
});

test('surname search works without a team selection and distinguishes matching teams', function () {
    $this->travelTo(Carbon::parse('2026-10-16 10:30', 'Europe/Amsterdam'));
    $first = attendanceSignup();
    $second = attendanceSignup();
    $second->hockeyTeam->update(['name' => 'Dames 2']);

    $this->getJson(route('attendance.search', ['name' => 'Bronkhorst']))
        ->assertJsonCount(2, 'matches')
        ->assertSee('Heren 3')->assertSee('Dames 2')
        ->assertDontSee('private@example.com');
});

test('search explains why a found registration is outside the check-in window', function () {
    $this->travelTo(Carbon::parse('2026-10-16 07:00', 'Europe/Amsterdam'));
    attendanceSignup();

    $this->getJson(route('attendance.search', ['name' => 'Bronkhorst']))
        ->assertJsonCount(0, 'matches')
        ->assertJsonPath('message', 'Je inschrijving is gevonden, maar aanmelden kan alleen vanaf twee uur vóór aanvang tot het einde van je dienst. Vraag de coördinator om hulp.');
});

test('name search never returns more than eight suggestions', function () {
    $this->travelTo(Carbon::parse('2026-10-16 10:30', 'Europe/Amsterdam'));
    for ($i = 0; $i < 9; $i++) {
        attendanceSignup();
    }

    $this->getJson(route('attendance.search', ['name' => 'Bronkhorst']))
        ->assertJsonCount(8, 'matches');
});
