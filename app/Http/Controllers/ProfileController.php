<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Personal;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        $docente = $user->personal;

        if (!$docente) {
            return back()->with('error', 'No se encontraron datos personales.');
        }
        
        return view('profile.edit', compact('docente'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var Personal $docente */
        $docente = $user->personal;

        // 1. Validaciones
        $request->validate([
            'Telefono' => 'nullable|string|max:20',
            'CorreoElectronico' => 'required|email|max:150|unique:Personal,CorreoElectronico,'.$docente->IdPersonal.',IdPersonal',
            'FotoPerfil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072', // Max 3MB
            'password' => 'nullable|min:6|confirmed', 
        ], [
            'FotoPerfil.image' => 'El archivo debe ser una imagen válida.',
            'FotoPerfil.mimes' => 'La foto debe ser JPG, JPEG, PNG o WEBP.',
            'FotoPerfil.max'   => 'La foto no debe pesar más de 3MB.',
            'password.min'     => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'CorreoElectronico.unique' => 'Este correo ya está registrado por otro usuario.',
        ]);

        // 2. Lógica de Actualización
        try {
            // A. Datos Personales
            $docente->Telefono = $request->Telefono;
            $docente->CorreoElectronico = $request->CorreoElectronico;

            // B. Manejo de Foto (CORREGIDO)
            if ($request->hasFile('FotoPerfil')) {
                // Borrar anterior si existe y no es una URL externa
                if ($docente->FotoPerfil && Storage::disk('public')->exists($docente->FotoPerfil)) {
                    Storage::disk('public')->delete($docente->FotoPerfil);
                }
                
                // Guardar nueva en 'storage/app/public/fotos'
                // Esto devuelve el string "fotos/nombre_archivo.jpg"
                $path = $request->file('FotoPerfil')->store('fotos', 'public'); 
                
                $docente->FotoPerfil = $path;
            }

            $docente->save();

            // C. Datos de Usuario (Login)
            $user->Email = $request->CorreoElectronico;
            
            // Cambio de contraseña
            if ($request->filled('password')) {
                $user->Password = Hash::make($request->password);
            }
            
            $user->save();

            return back()->with('success', 'Cambios guardados correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }
}