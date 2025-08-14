<?php

namespace App\Providers;

// Laravel Framework
use Illuminate\Support\ServiceProvider;

// Others
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\App\Domain\Articles\ArticlesProvider::class);
        $this->app->register(\App\Domain\Users\UsersProvider::class);
        $this->app->register(\App\Domain\Auth\AuthProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // レートリミットの設定
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );
    }
}
