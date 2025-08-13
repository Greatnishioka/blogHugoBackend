<?php

namespace App\Domain\Auth;

use Illuminate\Support\ServiceProvider;

class AuthProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Auth\Repository\AuthRepository::class,
            \App\Domain\Auth\Infrastructure\DbAuthInfrastructure::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
