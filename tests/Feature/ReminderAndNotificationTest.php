<?php

use App\Mail\ShiftReminder;
use App\Models\HockeyTeam;
use App\Models\Shift;
use App\Models\Signup;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\Teams\TeamInvitation as TeamInvitationNotification;
use Illuminate\Support\Facades\Mail;

test('send reminders command sends reminders only for upcoming unpaid signups', function () {
    Mail::fake();

    $upcomingShiftTime = now()->copy()->addHours(24)->addMinutes(30);
    $distantShiftTime = now()->copy()->addHours(48)->addMinutes(30);

    $upcomingShift = Shift::create([
        'title' => 'Upcoming Shift',
        'date' => $upcomingShiftTime->format('Y-m-d'),
        'starts_at' => $upcomingShiftTime->format('H:i:s'),
        'ends_at' => $upcomingShiftTime->copy()->addHour()->format('H:i:s'),
        'location' => 'Arena North',
        'capacity' => 12,
        'is_published' => true,
    ]);

    $distantShift = Shift::create([
        'title' => 'Distant Shift',
        'date' => $distantShiftTime->format('Y-m-d'),
        'starts_at' => $distantShiftTime->format('H:i:s'),
        'ends_at' => $distantShiftTime->copy()->addHour()->format('H:i:s'),
        'location' => 'Arena South',
        'capacity' => 12,
        'is_published' => true,
    ]);

    $team = HockeyTeam::create(['name' => 'North Stars']);

    $eligible = Signup::create([
        'shift_id' => $upcomingShift->id,
        'hockey_team_id' => $team->id,
        'name' => 'Alice Example',
        'email' => 'alice@example.com',
    ]);

    $notEligible = Signup::create([
        'shift_id' => $distantShift->id,
        'hockey_team_id' => $team->id,
        'name' => 'Bob Example',
        'email' => 'bob@example.com',
    ]);

    $alreadySent = Signup::create([
        'shift_id' => $upcomingShift->id,
        'hockey_team_id' => $team->id,
        'name' => 'Charlie Example',
        'email' => 'charlie@example.com',
    ]);
    $alreadySent->update(['reminder_sent_at' => now()]);

    $this->artisan('pinocrew:send-reminders')->assertSuccessful();

    Mail::assertSent(ShiftReminder::class, 1);
    Mail::assertSent(ShiftReminder::class, function ($mail) use ($eligible) {
        return $mail->signup->id === $eligible->id && $mail->hasTo('alice@example.com');
    });

    expect($eligible->fresh()->reminder_sent_at)->not->toBeNull();
    expect($notEligible->fresh()->reminder_sent_at)->toBeNull();
    expect($alreadySent->fresh()->reminder_sent_at)->not->toBeNull();
});

test('team invitation notification includes the team name and login action', function () {
    $inviter = User::factory()->create(['name' => 'Jane Inviter']);
    $team = Team::factory()->create(['name' => 'Blue Sharks']);
    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'invited_by' => $inviter->id,
        'email' => 'new-member@example.com',
    ]);

    $notification = new TeamInvitationNotification($invitation);
    $mail = $notification->toMail($inviter);

    expect($mail->subject)->toBe('You\'ve been invited to join Blue Sharks');
    expect($mail->actionText)->toBe('Log in');
    expect($mail->actionUrl)->toContain('invitation='.$invitation->code);
    expect($mail->actionUrl)->toContain('/login');
});

test('user initials use the first and last characters for multi-word names', function () {
    $user = User::factory()->make(['name' => 'Ada Lovelace']);

    expect($user->initials())->toBe('AL');
});

test('user initials return a single letter for single-word names', function () {
    $user = User::factory()->make(['name' => 'Ada']);

    expect($user->initials())->toBe('A');
});
