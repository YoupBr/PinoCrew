<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        Route::view('crew', 'crew')->name('crew.index');
        Route::view('shifts', 'shifts')->name('shifts.index');
        Route::view('events', 'events')->name('events.index');
    });

require __DIR__.'/settings.php';