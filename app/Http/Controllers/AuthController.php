<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Usuario; // Importamos el modelo correcto para SQL Server 

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, lo enviamos al panel interno
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Procesa la autenticación (Login Local con SQL Server).
     */
    public function login(Request $request)
    {
        // 1. Validar entradas (manteniendo tus nombres de campo del HTML)
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo institucional es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.'
        ]);

        $remember = $request->filled('remember');

        // 2. INTENTO DE LOGIN
        // Mapeo Crítico: 'email' (Request) -> 'Correo' (Tabla Usuario) 
        // Nota: Laravel usará internamente 'getAuthPassword' para comparar contra 'Contraseña' 
        if (Auth::attempt([
            'Correo' => $request->email, 
            'password' => $request->password
        ], $remember)) {
            
            // 3. Verificación de cuenta activa (Usando el Cast booleano definido en el Modelo) [cite: 92, 94]
            if (Auth::user()->Activo === false) { 
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Su cuenta ha sido desactivada. Contacte al Vicerrectorado.',
                ])->withInput();
            }

            // 4. Éxito - regenerar sesión para seguridad
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // 5. Error de credenciales
        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar sesión.
     * Ajustado para redirigir a la Portada Principal (Welcome).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirección a la ruta 'welcome' como solicitaste
        return redirect()->route('welcome');
    }
}