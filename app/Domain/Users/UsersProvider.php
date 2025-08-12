<?php

namespace App\Domain\Users;

use Illuminate\Support\ServiceProvider;

class UsersProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Users\Repository\UsersRepository::class,
            \App\Domain\Users\Infrastructure\DbUsersInfrastructure::class,
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
