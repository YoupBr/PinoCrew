<?php

use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

->withMiddleware(function (Middleware $middleware) {
    $middleware->redirectUsersTo(function ($request) {
        $user = $request->user();

        if ($user?->currentTeam) {
            return route('dashboard', [
                'current_team' => $user->currentTeam->slug,
            ]);
        }

        return route('home');
    });
})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
