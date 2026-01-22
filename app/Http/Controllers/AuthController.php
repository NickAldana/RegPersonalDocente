<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm()
    {
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
        // 1. Validar entradas
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo institucional es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.'
        ]);

        $remember = $request->filled('remember');

        // 2. INTENTO DE LOGIN
        // 'Email' (Mayúscula): Para que el WHERE de SQL Server busque en la columna correcta.
        // 'password' (Minúscula): Laravel usa esta llave reservada para saber qué valor hashear y comparar.
        if (Auth::attempt([
            'Email' => $request->email, 
            'password' => $request->password
        ], $remember)) {
            
            // 3. Verificación de cuenta activa (Columna 'Activo' en SQL Server)
            if (Auth::user()->Activo == 0) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Su cuenta ha sido desactivada. Contacte al Vicerrectorado.',
                ])->withInput();
            }

            // 4. Éxito
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
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}