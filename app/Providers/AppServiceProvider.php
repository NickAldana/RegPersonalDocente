<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;  // <--- NUEVO
use Illuminate\Support\Facades\Cache; // <--- NUEVO
use Illuminate\Support\Facades\Auth;  // <--- NUEVO
use App\Models\User;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Azure\AzureExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. FORZAR HTTPS EN PRODUCCIÓN
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // 2. SOCIALITE / AZURE
        Event::listen(SocialiteWasCalled::class, [AzureExtendSocialite::class, 'handle']);

        // 3. SUPER ADMIN (Puerta Maestra)
        Gate::before(function (User $user, $ability) {
            if ($user->canDo('acceso_total')) {
                return true; 
            }
        });

        // 4. GATES DINÁMICOS CON CACHÉ (Optimización Azure)
        // En lugar de consultar la tabla 'Permisos' en cada clic, 
        // guardamos la lista en la memoria de Railway por 24 horas.
        try {
            $permisos = Cache::remember('db_lista_permisos', 86400, function () {
                return DB::table('Permisos')->pluck('NombrePermiso');
            });

            foreach ($permisos as $permiso) {
                Gate::define($permiso, function (User $user) use ($permiso) {
                    return $user->canDo($permiso);
                });
            }
        } catch (\Exception $e) {
            // Silencio en caso de error de conexión inicial
        }

       // 5. COMPARTIR USUARIO OPTIMIZADO (SIA V4.0)
View::composer('*', function ($view) {
    if (Auth::check()) {
        // Reducimos a 300s (5 min) para balancear carga, pero la clave es la invalidación
        $currentUser = Cache::remember('user_sidebar_data_' . Auth::id(), 300, function () {
            return \App\Models\User::with([
                'personal:IdPersonal,NombreCompleto,FotoPerfil,IdCargo',
                'personal.cargo:IdCargo,NombreCargo'
            ])
            ->select('IdUser', 'IdPersonal', 'Email', 'Activo') // Solo columnas de login
            ->find(Auth::id());
        });
        
        $view->with('currentUser', $currentUser);
    }
});
    }
}