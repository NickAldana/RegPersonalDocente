<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event; // Importante para Socialite
use App\Models\User;
use SocialiteProviders\Manager\SocialiteWasCalled; // Requiere el paquete de Azure
use SocialiteProviders\Azure\AzureExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // --------------------------------------------------------------------
        // 1. REGISTRO DEL DRIVER DE AZURE (SOCIALITE)
        // --------------------------------------------------------------------
        // Este listener "enseña" a Laravel a hablar con Microsoft Azure
        Event::listen(SocialiteWasCalled::class, [AzureExtendSocialite::class, 'handle']);

        // --------------------------------------------------------------------
        // 2. GATE GLOBAL (Super Admin)
        // --------------------------------------------------------------------
        // Si el usuario tiene 'acceso_total' (Rector/Vice), se abren todas las puertas[cite: 44, 45].
        Gate::before(function (User $user, $ability) {
            if ($user->canDo('acceso_total')) {
                return true;
            }
        });

        // --------------------------------------------------------------------
        // 3. GATES ESPECÍFICOS (Permisos de Acreditación)
        // --------------------------------------------------------------------
        
        Gate::define('gestionar_personal', function (User $user) {
            return $user->canDo('gestionar_personal'); // [cite: 44]
        });

        Gate::define('asignar_carga', function (User $user) {
            return $user->canDo('asignar_carga'); // [cite: 44]
        });

        Gate::define('ver_dashboard', function (User $user) {
            return $user->canDo('ver_dashboard'); // [cite: 44]
        });
        
        Gate::define('ver_kardex_global', function (User $user) {
            return $user->canDo('ver_kardex_global'); // [cite: 44, 47]
        });

        Gate::define('ver_kardex_propio', function (User $user) {
            return $user->canDo('ver_kardex_propio'); // [cite: 44, 49]
        });

        Gate::define('ver_indicadores', function (User $user) {
            return $user->canDo('ver_indicadores') || $user->canDo('ver_dashboard');
        });
    }
}