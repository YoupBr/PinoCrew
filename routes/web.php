<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrewPrintController;

Route::view('/', 'welcome')->name('home');

Route::livewire('inschrijven', 'pages::signup.index')
    ->name('signup');

Route::livewire('inschrijven/{shift}', 'pages::signup.show')
    ->name('signup.show');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {

    // Dashboard in beheer
        Route::livewire('dashboard', 'pages::dashboard')
    ->name('dashboard');

    // Crew in beheer
        Route::livewire('crew', 'pages::crew.index')
    ->name('crew.index');

    // Shifts in beheer
        Route::livewire('shifts', 'pages::shifts.index')
    ->name('shifts.index');

    // Mail in beheer
        Route::livewire('mail', 'pages::mail.index')
    ->name('mail.index');

    // Mag later waarschijnlijk weg.
        Route::view('events', 'events')->name('events.index');
    });

    // Printfunctie in crewscherm
        Route::get('crew/print', CrewPrintController::class)
    ->name('crew.print');


    Route::get('/dashboard', function () {
    $user = request()->user();

    if (! $user?->currentTeam) {
        abort(403, 'Je bent nog niet aan een PinoCrew-team gekoppeld.');
    }

    return redirect()->route('dashboard', [
        'current_team' => $user->currentTeam->slug,
    ]);
})->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';