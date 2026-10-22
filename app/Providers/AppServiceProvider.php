<?php

namespace App\Providers;

use App\Enums\TeamRole;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Gate::define('export-crew', fn (User $user): bool => $user->teams()
            ->where('teams.slug', 'pinoke')
            ->wherePivotIn('role', [TeamRole::Admin->value, TeamRole::Owner->value])
            ->exists());

        RateLimiter::for('attendance-search', fn (Request $request) => Limit::perMinute(60)
            ->by('attendance-search:'.$request->session()->getId()));
        RateLimiter::for('attendance-store', fn (Request $request) => Limit::perMinute(20)
            ->by('attendance-store:'.$request->session()->getId()));
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
