<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Storage, Cache};
use App\Models\{Usuario, Personal}; // Usamos los modelos V3.1

class ProfileController extends Controller
{
    /**
     * Muestra el formulario de edición del perfil del docente logueado.
     */
    public function edit()
    {
        /** @var Usuario $user */
        $user = Auth::user();
        
        // Obtenemos la ficha personal vinculada al usuario
        // Usamos los nombres de columna exactos del script V3.1 [cite: 197, 198]
        $docente = $user->personal()
            ->select(['PersonalID', 'Nombrecompleto', 'Apellidopaterno', 'Apellidomaterno', 'Telelefono', 'Correoelectronico', 'Fotoperfil'])
            ->first();

        if (!$docente) {
            return back()->with('error', 'No se encontró una ficha de personal vinculada a su cuenta de usuario.');
        }
        
        return view('profile.edit', compact('docente'));
    }

    /**
     * Procesa la actualización de datos del perfil.
     */
    public function update(Request $request)
    {
        /** @var Usuario $user */
        $user = Auth::user();
        $docente = $user->personal;

        // Validación adaptada a V3.1 [cite: 197, 182]
        $request->validate([
            'Telelefono' => 'nullable|string|max:20',
            'Correoelectronico' => 'required|email|max:150|unique:Personal,Correoelectronico,'.$docente->PersonalID.',PersonalID',
            'Fotoperfil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024', 
            'password' => 'nullable|min:6|confirmed', 
        ]);

        try {
            // 1. Actualizar Datos en la tabla Personal
            $docente->Telelefono = $request->Telelefono;
            $docente->Correoelectronico = $request->Correoelectronico;

            // Gestión de Foto de Perfil 
            if ($request->hasFile('Fotoperfil')) {
                // Borrar foto anterior si existe
                if ($docente->Fotoperfil && Storage::disk('public')->exists($docente->Fotoperfil)) {
                    Storage::disk('public')->delete($docente->Fotoperfil);
                }
                
                $extension = $request->file('Fotoperfil')->getClientOriginalExtension();
                $nombreArchivo = 'perfil_' . $user->UsuarioID . '_' . time() . '.' . $extension;
                $path = $request->file('Fotoperfil')->storeAs('fotos/perfiles', $nombreArchivo, 'public'); 
                
                $docente->Fotoperfil = $path;
            }

            $docente->save();

            // 2. Sincronizar datos con la tabla Usuario (Login) [cite: 182]
            $user->Correo = $request->Correoelectronico; 
            if ($request->filled('password')) {
                $user->Contraseña = Hash::make($request->password); 
            }
            $user->save();

            // 3. Limpiar Caché del Sidebar
            // Vital para que el nombre y la foto nueva se reflejen de inmediato
            Cache::forget('user_sidebar_data_' . $user->UsuarioID);

            return back()->with('success', 'Perfil actualizado con éxito. Los cambios son visibles en el menú lateral.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar la actualización: ' . $e->getMessage());
        }
    }
}