<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendancePosterController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\CrewPrintController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('aanwezigheid', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('aanwezigheid/zoeken', [AttendanceController::class, 'search'])
    ->middleware('throttle:attendance-search')->name('attendance.search');
Route::post('aanwezigheid', [AttendanceController::class, 'store'])
    ->middleware('throttle:attendance-store')->name('attendance.store');

Route::livewire('inschrijven', 'pages::signup.index')
    ->name('signup');

Route::livewire('inschrijven/{shift}', 'pages::signup.show')
    ->name('signup.show');

Route::view('legal.privacy', 'legal.privacy')
    ->name('legal.privacy');

require __DIR__.'/settings.php';

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {

        Route::view('qr-poster', 'attendance-poster')
            ->name('attendance-poster.index');

        Route::get('qr-poster/print', AttendancePosterController::class)
            ->name('attendance-poster.print');

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
        Route::view('events', 'events')
            ->name('events.index');

        Route::livewire('teams', 'pages::hockey-teams.index')
            ->name('hockey-teams.index');

        Route::livewire('agenda', 'pages::calendar.index')
            ->name('calendar.index');

        Route::get('aanwezigheid', [AttendanceReportController::class, 'index'])
            ->name('attendance-report.index');
        Route::patch('aanwezigheid/{signup}', [AttendanceReportController::class, 'update'])
            ->name('attendance-report.update');
    });

// Printfunctie in crewscherm
Route::get('crew/print', CrewPrintController::class)
    ->middleware(['auth', 'verified', 'can:export-crew'])
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
