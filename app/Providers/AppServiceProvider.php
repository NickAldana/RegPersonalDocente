<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario; // <--- CAMBIO CRÍTICO: Usamos el modelo V3.1
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

        // 2. SOCIALITE / AZURE (SSO)
        Event::listen(SocialiteWasCalled::class, [AzureExtendSocialite::class, 'handle']);

        // 3. SUPER ADMIN (Puerta Maestra)
        // Usamos el modelo Usuario
        Gate::before(function (Usuario $user, $ability) {
            // Asumimos que existe un permiso especial 'acceso_total'
            if ($user->canDo('acceso_total')) {
                return true; 
            }
        });

        // 4. GATES DINÁMICOS CON CACHÉ (Optimización RBAC)
        try {
            // Cacheamos la lista de permisos por 24 horas
            $permisos = Cache::remember('db_lista_permisos', 86400, function () {
                // V3.1: La tabla es 'Permisos' y la columna 'Nombrepermiso'
                return DB::table('Permisos')->pluck('Nombrepermiso');
            });

            foreach ($permisos as $permiso) {
                Gate::define($permiso, function (Usuario $user) use ($permiso) {
                    return $user->canDo($permiso);
                });
            }
        } catch (\Exception $e) {
            // Silencio en caso de error de conexión inicial (ej: durante migraciones)
        }

        // 5. COMPARTIR USUARIO OPTIMIZADO (SIDEBAR V3.1)
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // Cache de 5 minutos por usuario para no saturar SQL Server
                $currentUser = Cache::remember('user_sidebar_data_' . Auth::id(), 300, function () {
                    return Usuario::with([
                        // Seleccionamos solo columnas existentes en V3.1
                        'personal:PersonalID,Nombrecompleto,Fotoperfil,CargoID,UsuarioID',
                        'personal.cargo:CargoID,Nombrecargo'
                    ])
                    // Seleccionamos columnas de login
                    ->select('UsuarioID', 'NombreUsuario', 'Correo', 'Activo') 
                    ->find(Auth::id());
                });
                
                // Inyectamos la variable $currentUser a todas las vistas
                $view->with('currentUser', $currentUser);
            }
        });
    }
}