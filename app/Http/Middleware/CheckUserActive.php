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
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado Y su campo Activo es 0 (Falso)
        if (Auth::check() && Auth::user()->Activo == 0) {
            
            // 1. Cerrar sesión forzosamente
            Auth::logout();

            // 2. Invalidar la sesión de seguridad
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 3. Expulsarlo al login con mensaje
            return redirect()->route('login')->with('error', 'Su cuenta ha sido desactivada. Contacte a Recursos Humanos.');
        }

        return $next($request);
    }
}