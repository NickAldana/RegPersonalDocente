<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rules\Password;
use App\Models\Usuario;
use App\Models\Bitacora;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth, DB};

class UsuarioController extends Controller
{
    /**
     * SEG-02: Listado maestro de cuentas de acceso.
     */
  public function index(Request $request)
{
    /** @var \App\Models\Usuario $currentUser */
    $currentUser = Auth::user();

    // 1. CONSULTA BASE (Se añade UsuarioID a la carga de personal)
    $query = Usuario::select(['UsuarioID', 'NombreUsuario', 'Correo', 'Activo', 'Idpersonal'])
        ->with([
            // REGLA ORO: Se agregó UsuarioID al final para que la relación no sea NULL
            'personal:PersonalID,Nombrecompleto,CargoID,Fotoperfil,Añosexperiencia,UsuarioID', 
            'personal.cargo:CargoID,Nombrecargo,nivel_jerarquico'
        ]);

    // 2. SEGURIDAD: VISIÓN DE TÚNEL (SEG-04)
    if (!$currentUser->canDo('acceso_total')) {
        $miNivel = $currentUser->personal->cargo->nivel_jerarquico ?? 1000;
        $query->whereHas('personal.cargo', function($q) use ($miNivel) {
            $q->where('nivel_jerarquico', '>=', $miNivel);
        });
    }

    // 3. FILTROS (Búsqueda y Estado)
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function($q) use ($search) {
            $q->where('NombreUsuario', 'like', "%{$search}%")
              ->orWhere('Correo', 'like', "%{$search}%")
              ->orWhereHas('personal', function($qp) use ($search) {
                  $qp->where('Nombrecompleto', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('estado')) {
        $query->where('Activo', $request->estado);
    }

    // 4. RESULTADOS PAGINADOS
    $usuarios = $query->orderBy('Activo', 'desc')
                      ->orderBy('UsuarioID', 'desc')
                      ->paginate(15)
                      ->withQueryString();

    return view('seguridad.usuarios.index', compact('usuarios'));
}

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        $usuario = Usuario::with('personal.cargo')->findOrFail($id);

        if (!$this->validarJerarquiaUsuario($usuario)) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Acceso denegado: No puede gestionar una cuenta de rango superior.');
        }

        return view('seguridad.usuarios.edit', compact('usuario'));
    }

    /**
     * Actualización de credenciales.
     * Sincronizado con la columna 'Password'.
     */
public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        // 1. Verificación de jerarquía
        if (!$this->validarJerarquiaUsuario($usuario)) {
            return redirect()->route('usuarios.index')->with('error', 'Acción denegada por jerarquía.');
        }

        // 2. Validación Robusta (Cerrada correctamente)
        $request->validate([
            'Correo' => "required|email|unique:usuario,Correo,{$id},UsuarioID",
            'password' => [
                'nullable', 
                'confirmed', 
                Password::min(8)->letters()->numbers() // Alfanumérica: Letras + Números
            ],
        ]); 

        // 3. Actualización de datos
        $usuario->Correo = $request->Correo;

        // Si el admin escribió una clave nueva en el formulario
        if ($request->filled('password')) {
            // MAPEO: Guardamos en la columna física 'Password' (P mayúscula)
            $usuario->Password = Hash::make($request->password); 
            
            Bitacora::registrar(
                'CAMBIO_PASSWORD_ADMIN', 
                "Se cambió manualmente la contraseña del usuario: {$usuario->NombreUsuario}.",
                $usuario->UsuarioID
            );
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Cuenta actualizada correctamente.');
    }

    /**
     * SEG-02: Reset de contraseña al CI del docente.
     */
    public function resetPassword($id)
    {
        $usuario = Usuario::with('personal')->findOrFail($id);

        if (!$this->validarJerarquiaUsuario($usuario)) {
            return back()->with('error', 'No tiene permisos para restablecer la clave de un superior.');
        }

        if (!$usuario->personal || !$usuario->personal->Ci) {
            return back()->with('error', 'Este usuario no tiene un registro de personal asociado o CI válido.');
        }

        // MAPEO: Restablecemos a la columna física 'Password' usando el CI
        $usuario->Password = Hash::make($usuario->personal->Ci); 
        $usuario->save();

        Bitacora::registrar(
            'RESET_PASSWORD', 
            "Se restableció la contraseña al CI por defecto para: {$usuario->NombreUsuario}.",
            $usuario->UsuarioID
        );

        return back()->with('success', "La contraseña de {$usuario->NombreUsuario} ha sido restablecida al CI.");
    }

    /**
     * Activar / Bloquear acceso.
     */
    public function toggleStatus($id)
    {
        $usuario = Usuario::findOrFail($id);

        if (!$this->validarJerarquiaUsuario($usuario)) {
            return back()->with('error', 'No puede modificar el estado de un superior.');
        }

        $nuevoEstado = !$usuario->Activo;
        $usuario->Activo = $nuevoEstado;
        $usuario->save();

        if ($usuario->personal) {
            $usuario->personal->Activo = $nuevoEstado;
            $usuario->personal->save();
        }

        $accion = $nuevoEstado ? 'ACTIVAR_USUARIO' : 'BLOQUEAR_USUARIO';
        Bitacora::registrar(
            $accion, 
            "Estado de cuenta actualizado para: {$usuario->NombreUsuario}.",
            $usuario->UsuarioID
        );

        return back()->with('success', $nuevoEstado ? 'Cuenta activada correctamente.' : 'Cuenta bloqueada.');
    }

    /**
     * SEG-04: Lógica de protección jerárquica.
     */
    private function validarJerarquiaUsuario($targetUser)
    {
        /** @var \App\Models\Usuario $currentUser */
        $currentUser = Auth::user();

        if ($currentUser->canDo('acceso_total')) {
            return true;
        }

        if (!$currentUser->personal?->cargo || !$targetUser->personal?->cargo) {
            return false;
        }

        // El número menor indica mayor jerarquía (1 Rector < 4 Docente)
        return $currentUser->personal->cargo->nivel_jerarquico < $targetUser->personal->cargo->nivel_jerarquico;
    }
}