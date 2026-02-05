<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     * Verifica que el usuario tenga permiso de acceso en cada clic.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si hay alguien logueado
        if (Auth::check()) {
            
            // 2. Accedemos al usuario actual
            $user = Auth::user();

            // 3. Verificación Estricta
            // En el modelo Usuario.php definimos: protected $casts = ['Activo' => 'boolean'];
            // Por lo tanto, $user->Activo será true o false, no 1 o 0.
            if ($user->Activo === false) { 
                
                // A. Cerrar sesión inmediatamente
                Auth::guard('web')->logout();

                // B. Limpiar la sesión para evitar secuestros
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // C. Redirigir con mensaje de error específico para el login
                // Usamos 'withErrors' para que aparezca rojo en el input de usuario
                return redirect()->route('login')
                    ->withErrors(['NombreUsuario' => 'Su cuenta ha sido desactivada. Contacte a Talento Humano.']);
            }
        }

        return $next($request);
    }
}