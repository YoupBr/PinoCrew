<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        Route::livewire('crew', 'pages::crew.index')->name('crew.index');
        Route::livewire('events', 'pages::events.index')->name('events.index');
        Route::livewire('planning', 'pages::planning.index')->name('planning.index');
    });

require __DIR__.'/settings.php';