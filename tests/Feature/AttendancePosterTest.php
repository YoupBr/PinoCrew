<?php

use App\Models\User;

beforeEach(function () {
    $this->withoutVite();
});

test('guests cannot open the poster generator', function () {
    $user = User::factory()->create();

    $this->get(route('attendance-poster.index', ['current_team' => $user->currentTeam->slug]))
        ->assertRedirect(route('login'));
});

test('team members can open the poster generator', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('attendance-poster.index', ['current_team' => $user->currentTeam->slug]))
        ->assertSee('Maak A4-poster');
});

test('users cannot generate posters through another team', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)
        ->get(route('attendance-poster.print', [
            'current_team' => $other->currentTeam->slug,
            'title' => 'Welkom',
            'instruction' => 'Scan de code.',
        ]))
        ->assertForbidden();
});

test('posters display escaped custom text and the fixed destination', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('attendance-poster.print', [
            'current_team' => $user->currentTeam->slug,
            'title' => '<script>alert(1)</script>',
            'location' => 'Veld 14',
            'instruction' => 'Scan de code.',
            'url' => 'https://example.com',
        ]))
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('Veld 14')
        ->assertSee('Scan de code.')
        ->assertSee('https://pinocrew.nl/aanwezigheid')
        ->assertDontSee('https://example.com');
});

test('posters require a title and instruction', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('attendance-poster.print', ['current_team' => $user->currentTeam->slug]))
        ->assertSessionHasErrors([
            'title' => 'Vul een titel in.',
            'instruction' => 'Vul een instructie in.',
        ]);
});

test('poster text is bounded to fit the page', function (string $field, int $limit, string $message) {
    $user = User::factory()->create();
    $parameters = [
        'current_team' => $user->currentTeam->slug,
        'title' => 'Welkom',
        'instruction' => 'Scan de code.',
        $field => str_repeat('a', $limit + 1),
    ];

    $this->actingAs($user)
        ->get(route('attendance-poster.print', $parameters))
        ->assertSessionHasErrors([$field => $message]);
})->with([
    'title' => ['title', 70, 'Gebruik maximaal 70 tekens voor de titel.'],
    'location' => ['location', 90, 'Gebruik maximaal 90 tekens voor de locatie.'],
    'instruction' => ['instruction', 220, 'Gebruik maximaal 220 tekens voor de instructie.'],
]);
