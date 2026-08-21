<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        // Como el modelo Usuario usa la conexión 'mongodb', los tokens trataban de ejecutar SQL
        // contra la conexión de Mongo causando un error fatal.
        // Forzar 'sqlite' en el modelo PersonalAccessToken soluciona esto.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
