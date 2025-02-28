<?php

namespace App\Providers;

use App\Contracts\SpotifyService as SpotifyServiceContract;
use App\Services\SpotifyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SpotifyServiceContract::class, function () {
            return new SpotifyService(
                config('services.spotify.client_id'),
                config('services.spotify.client_secret')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
