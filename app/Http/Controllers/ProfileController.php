<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Personal;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        $docente = $user->personal()->select(['IdPersonal', 'NombreCompleto', 'ApellidoPaterno', 'ApellidoMaterno', 'Telefono', 'CorreoElectronico', 'FotoPerfil'])->first();

        if (!$docente) {
            return back()->with('error', 'No se encontraron datos personales.');
        }
        
        return view('profile.edit', compact('docente'));
    }

    public function update(Request $request)
{
    /** @var User $user */
    $user = Auth::user();
    $docente = $user->personal;

    $request->validate([
        'Telefono' => 'nullable|string|max:20',
        'CorreoElectronico' => 'required|email|max:150|unique:Personal,CorreoElectronico,'.$docente->IdPersonal.',IdPersonal',
        'FotoPerfil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024', 
        'password' => 'nullable|min:6|confirmed', 
    ]);

    try {
        $docente->Telefono = $request->Telefono;
        $docente->CorreoElectronico = $request->CorreoElectronico;

        if ($request->hasFile('FotoPerfil')) {
            if ($docente->FotoPerfil && Storage::disk('public')->exists($docente->FotoPerfil)) {
                Storage::disk('public')->delete($docente->FotoPerfil);
            }
            
            // Usamos time() para que la URL de la imagen cambie y el navegador no use la vieja
            $extension = $request->file('FotoPerfil')->getClientOriginalExtension();
            $nombreArchivo = 'perfil_' . Auth::id() . '_' . time() . '.' . $extension;
            $path = $request->file('FotoPerfil')->storeAs('fotos', $nombreArchivo, 'public'); 
            
            $docente->FotoPerfil = $path;
        }

        $docente->save();

        $user->Email = $request->CorreoElectronico;
        if ($request->filled('password')) {
            $user->Password = Hash::make($request->password);
        }
        $user->save();

        // --- EL TRUCO DE LA VELOCIDAD ---
        // Usamos Auth::id() explícitamente para asegurar que la llave coincida con el Provider
        $userId = Auth::id();
        Cache::forget('user_sidebar_data_' . $userId);
        Cache::forget('dashboard_stats_user_' . $userId);
        Cache::forget('dashboard_stats_global');

        return back()->with('success', 'Perfil actualizado. Los cambios ya son visibles en su menú.');

    } catch (\Exception $e) {
        return back()->with('error', 'Error al guardar: ' . $e->getMessage());
    }
}
}