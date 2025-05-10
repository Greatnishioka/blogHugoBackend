<?php

namespace App\Domain\Articles;

use Illuminate\Support\ServiceProvider;

class ArticlesErrorProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Articles\Repository\ArticlesRepository::class,
            \App\Domain\Articles\Infrastructure\DbArticlesInfrastructure::class,
            //\App\Domain\AppInfo\Infrastructure\DemoDbGetVersionInfrastructure::class,

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
