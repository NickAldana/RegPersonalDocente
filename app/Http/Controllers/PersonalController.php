<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str; // <--- NECESARIO PARA LIMPIAR TEXTO (Ñ -> n, acentos, etc)
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- IMPORTANTE: Agregado para transacciones
use App\Models\Personal;
use App\Models\Carrera;
use App\Models\TipoContrato;
use App\Models\GradoAcademico; // <--- Agregado
use App\Models\CentroFormacion; // <--- Agregado
use App\Models\Formacion;       // <--- Agregado
use App\Models\Cargo;
use App\Models\User;

class PersonalController extends Controller
{
    // =========================================================================
    // 1. LISTADO Y BÚSQUEDA
    // =========================================================================
   // En PersonalController.php

    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        // Iniciamos la consulta base
        $query = Personal::with(['contrato', 'cargo', 'usuario', 'carreras']) // Eager loading 'carreras'
                         ->withCount('materias');

        // =====================================================================
        // 1. FILTRO DE SEGURIDAD HORIZONTAL (ÁREAS/FACULTADES)
        // =====================================================================
        
        // Si NO es Super Admin, aplicamos visión de túnel
        if (!$currentUser->canDo('acceso_total')) {
            
            // Verificamos si el usuario tiene ficha de personal
            $miPersonal = $currentUser->personal;

            if ($miPersonal) {
                // Obtenemos las carreras asignadas al Jefe/Decano actual
                // (pluck genera un array simple de IDs: [1, 5, 8])
                $misCarrerasIds = $miPersonal->carreras->pluck('IdCarrera')->toArray();

                if (empty($misCarrerasIds)) {
                    // CASO BORDE: Es Jefe pero no le asignaron carrera en la BD.
                    // No debería ver nada por seguridad.
                    $query->whereRaw('1 = 0'); 
                } else {
                    
                    // LÓGICA DECANO VS JEFE
                    // El Decano (Nivel 80) ve toda la Facultad. El Jefe (Nivel 50) solo su Carrera.
                    $miNivel = $currentUser->cargo()->nivel_jerarquico ?? 0;

                    if ($miNivel >= 80) { 
                        // === VISIÓN DE DECANO (Por Facultad) ===
                        // 1. Buscamos las Facultades de mis carreras
                        $misFacultadesIds = Carrera::whereIn('IdCarrera', $misCarrerasIds)
                                                   ->pluck('IdFacultad')
                                                   ->unique();
                        
                        // 2. Filtramos docentes que pertenezcan a carreras de esas facultades
                        $query->whereHas('carreras', function($q) use ($misFacultadesIds) {
                            $q->whereIn('IdFacultad', $misFacultadesIds);
                        });

                    } else {
                        // === VISIÓN DE JEFE (Por Carrera Específica) ===
                        // Solo veo docentes que dicten clases en mis carreras asignadas
                        $query->whereHas('carreras', function($q) use ($misCarrerasIds) {
                            $q->whereIn('Carrera.IdCarrera', $misCarrerasIds);
                        });
                    }
                }
            } else {
                // Si es usuario sin personal asociado y no es admin, no ve nada.
                $query->whereRaw('1 = 0');
            }
        }

        // =====================================================================
        // 2. FILTROS DE BÚSQUEDA NORMALES (Front-end)
        // =====================================================================

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('NombreCompleto', 'like', '%' . $request->buscar . '%')
                  ->orWhere('ApellidoPaterno', 'like', '%' . $request->buscar . '%')
                  ->orWhere('CI', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('Activo', $request->estado);
        }

        // Orden y Paginación
        $personal = $query->orderBy('ApellidoPaterno')->paginate(15)->withQueryString();
        
        return view('personal.index', compact('personal'));
    }

    // =========================================================================
    // 2. CREACIÓN (VISTA Y LÓGICA)
    // =========================================================================
   // En PersonalController.php

// En app/Http/Controllers/PersonalController.php

// =========================================================================
    // 2. CREACIÓN (VISTA Y LÓGICA ACTUALIZADA)
    // =========================================================================
    public function create()
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        // 1. Obtener mi nivel jerárquico
        $miCargo = optional($currentUser->personal)->cargo;
        
        // Nivel 1000 para Super Admin sin ficha
        if (!$miCargo && $currentUser->canDo('acceso_total')) {
            $miNivel = 1000; 
        } else {
            $miNivel = $miCargo ? $miCargo->nivel_jerarquico : 0;
        }

        // 2. Filtrar Cargos (Lógica RBAC)
        if ($miNivel >= 100) {
            $cargos = Cargo::all(); 
        } else {
            $cargos = Cargo::all()->filter(function ($cargo) use ($miNivel) {
                return $cargo->nivel_jerarquico < $miNivel;
            });
        }

        // 3. Obtener Catálogos para el Formulario
        $tiposContrato = TipoContrato::all();
        $carreras = Carrera::all(); // <--- NUEVO: Necesario para el selector de Carrera

        return view('personal.create', compact('tiposContrato', 'cargos', 'carreras'));
    }

   public function store(Request $request)
    {
        // 1. VALIDACIONES
        // (Ya no pedimos CorreoElectronico porque lo generamos nosotros)
        $request->validate([
            'IdCargo'           => 'required|exists:Cargo,IdCargo',
            'IdCarrera'         => 'required|exists:Carrera,IdCarrera',
            'NombreCompleto'    => 'required|string|min:3|max:200',
            'ApellidoPaterno'   => 'required|string|min:2|max:100',
            'CI'                => 'required|string|max:20|unique:Personal,CI',
            'IdTipoContrato'    => 'required|exists:TipoContrato,IdTipoContrato',
            'IdGradoAcademico'  => 'required|exists:GradoAcademico,IdGradoAcademico',
            'TituloObtenido'    => 'required|string|min:3|max:300',
            'AniosExperiencia'  => 'nullable|string',
            'AñoEstudios'       => 'required|integer|min:1950|max:' . date('Y'),
            // Si dejaste los campos en el HTML, valídalos aquí también:
            'Telefono'          => 'nullable|string|max:20',
            'Genero'            => 'nullable|string|in:Masculino,Femenino'
        ]);

        // 2. SEGURIDAD JERÁRQUICA
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->canDo('acceso_total')) {
            $cargoDestino = Cargo::find($request->IdCargo);
            $miNivel = optional(optional($currentUser->personal)->cargo)->nivel_jerarquico ?? 0;
            if ($cargoDestino->nivel_jerarquico >= $miNivel) {
                return back()->with('error', 'Seguridad: No puedes asignar un cargo igual o superior al tuyo.')->withInput();
            }
        }

        // =========================================================
        // 3. SANITIZACIÓN Y ESTANDARIZACIÓN DE TEXTO
        // =========================================================
        // Convertimos: "nick brandon" -> "Nick Brandon"
        // Convertimos: "peñaranda" -> "Peñaranda"
        $nombreLimpio   = Str::title(trim($request->NombreCompleto)); 
        $paternoLimpio  = Str::title(trim($request->ApellidoPaterno));
        $maternoLimpio  = $request->ApellidoMaterno ? Str::title(trim($request->ApellidoMaterno)) : null;

        // =========================================================
        // 4. GENERACIÓN DE CORREO AUTOMÁTICO
        // =========================================================
        // Lógica: sc.primer_nombre.paterno.inicial_materno@upds.net.bo
        
        // Obtenemos el primer nombre para el correo (Nick Brandon -> nick)
        $primerNombreSlug = Str::slug(explode(' ', $nombreLimpio)[0]); 
        $paternoSlug      = Str::slug($paternoLimpio);
        
        // Inicial materno (si existe)
        $inicialMaterno = $maternoLimpio ? substr(Str::slug($maternoLimpio), 0, 1) : '';

        // Armamos el prefijo base
        $prefijo = "sc.{$primerNombreSlug}.{$paternoSlug}";
        if ($inicialMaterno) {
            $prefijo .= ".{$inicialMaterno}";
        }
        
        $dominio = "@upds.net.bo";
        $emailGenerado = $prefijo . $dominio;

        // Verificamos duplicados (ej: si ya existe sc.juan.perez.g@...)
        $contador = 1;
        while (Personal::where('CorreoElectronico', $emailGenerado)->exists()) {
            // Agregamos un número: sc.juan.perez.g01@...
            $numero = str_pad($contador, 2, '0', STR_PAD_LEFT); 
            $emailGenerado = "{$prefijo}{$numero}{$dominio}";
            $contador++;
        }

        // =========================================================
        // 5. GUARDADO EN BASE DE DATOS (TRANSACCIÓN)
        // =========================================================
        DB::beginTransaction();

        try {
            // A. PREPARAR DATOS
            $data = $request->except(['IdCarrera', 'TituloObtenido', 'AñoEstudios']);
            
            // Sobreescribimos con los datos limpios y generados
            $data['NombreCompleto']    = $nombreLimpio;
            $data['ApellidoPaterno']   = $paternoLimpio;
            $data['ApellidoMaterno']   = $maternoLimpio;
            $data['CorreoElectronico'] = $emailGenerado;
            $data['Activo']            = true;

            // B. CREAR PERSONAL
            $nuevoDocente = Personal::create($data);

            // C. VINCULAR CARRERA (Para Power BI)
            DB::table('CarreraPersonal')->insert([
                'IdCarrera'  => $request->IdCarrera,
                'IdPersonal' => $nuevoDocente->IdPersonal,
                'Gestion'    => 2026 
            ]);

            // D. REGISTRAR TÍTULO
            Formacion::create([
                'IdPersonal'        => $nuevoDocente->IdPersonal,
                'IdCentroFormacion' => 1,
                'IdGradoAcademico'  => $request->IdGradoAcademico,
                'TituloObtenido'    => $request->TituloObtenido, // Podrías usar Str::upper() si prefieres títulos en MAYÚSCULAS
                'AñoEstudios'       => $request->AñoEstudios,
                'RutaArchivo'       => null
            ]);

            // E. CREAR USUARIO LOGIN
            // Si no pusieron CI, usamos contraseña genérica
            $passwordRaw = !empty($request->CI) ? $request->CI : 'Docente2026*';

            User::create([
                'IdPersonal' => $nuevoDocente->IdPersonal,
                'Email'      => $emailGenerado,
                'Password'   => Hash::make($passwordRaw),
                'Activo'     => true
            ]);

            DB::commit();

            return redirect()->route('personal.index')
                ->with('success', "Personal registrado: $nombreLimpio $paternoLimpio. Correo asignado: $emailGenerado");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error crítico: ' . $e->getMessage())->withInput();
        }
    }
    // =========================================================================
    // 3. DETALLE (PERFIL / KARDEX)
    // =========================================================================
    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) return redirect()->route('login');

        // SEGURIDAD: Evitar que un Docente vea el perfil de otro.
        // Si NO tiene permiso global Y NO es su propio perfil => 403
        if (!$user->canDo('ver_kardex_global') && $user->IdPersonal != $id) {
            abort(403, 'No tienes autorización para ver este perfil.');
        }

        $docente = Personal::with([
            'contrato', 'cargo', 'materias', 
            'formaciones.gradoAcademico', 'formaciones.centroFormacion', 'publicaciones'
        ])->findOrFail($id);

        $grados = GradoAcademico::all(); 
        $centros = CentroFormacion::all();

        return view('personal.show', compact('docente', 'grados', 'centros'));
    }

    // =========================================================================
    // 4. ACCIONES ADMINISTRATIVAS (SEGURIDAD JERÁRQUICA)
    // =========================================================================

    /**
     * Validador de Seguridad: Impide modificar a superiores.
     */
   /**
 * Validador de Seguridad Jerárquica (RBAC)
 * Regla: No puedes modificar a alguien con rango igual o superior al tuyo.
 */
private function validarJerarquia($targetIdPersonal)
{
    /** @var \App\Models\User $currentUser */
    $currentUser = Auth::user();

    // 1. Super Admin (Rector/Vicerrector con permiso total) siempre pasa
    if ($currentUser->canDo('acceso_total')) {
        return true;
    }

    // 2. Si el usuario actual no tiene perfil de personal asociado, bloqueamos por seguridad
    // (A menos que sea el admin 'acceso_total' que ya pasó arriba)
    if (!$currentUser->personal || !$currentUser->personal->cargo) {
        return false; 
    }

    // 3. Obtenemos al objetivo (Target)
    $targetPersonal = Personal::with('cargo')->find($targetIdPersonal);
    
    // Si el objetivo no existe o no tiene cargo, permitimos editarlo (es un error de datos que debemos poder corregir)
    if (!$targetPersonal || !$targetPersonal->cargo) {
        return true;
    }

    // 4. COMPARACIÓN DE NIVELES
    // Asumimos que MAYOR número = MAYOR jerarquía (Rector=100, Docente=10)
    // OJO: Verifica si en tu BD es al revés. Aquí uso la lógica estándar: Nivel Alto > Nivel Bajo.
    
    $miNivel = $currentUser->personal->cargo->nivel_jerarquico; 
    $targetNivel = $targetPersonal->cargo->nivel_jerarquico;

    // BLOQUEO: Si el objetivo tiene igual o más poder que yo.
    // Ejemplo: Jefe (50) intenta borrar a otro Jefe (50) -> Falso.
    // Ejemplo: Jefe (50) intenta borrar a Decano (80) -> Falso.
    if ($targetNivel >= $miNivel) {
        return false;
    }

    return true;
}

    // A. Dar de Baja / Alta
    public function toggleStatus($id)
    {
        if (!$this->validarJerarquia($id)) {
            return back()->with('error', 'Acción denegada: No tienes rango suficiente.');
        }

        $docente = Personal::findOrFail($id);
        
        // Invertir estado
        $docente->Activo = !$docente->Activo;
        $docente->save();
        
        // Sincronizar usuario de login (si existe)
        if ($docente->usuario) {
            $docente->usuario->Activo = $docente->Activo;
            $docente->usuario->save();
        }

        $estado = $docente->Activo ? 'activado' : 'desactivado';
        return back()->with('success', "Personal $estado correctamente.");
    }

    // B. Crear Usuario Manualmente (Botón)
    public function createUser($id)
    {
        if (!$this->validarJerarquia($id)) {
            return back()->with('error', 'Acción denegada: Rango insuficiente.');
        }

        $docente = Personal::findOrFail($id);

        if (!$this->validarJerarquia($id)) {
        return back()->with('error', 'Acción denegada: Rango insuficiente.');
    }

    $docente = Personal::findOrFail($id);

    if (User::where('IdPersonal', $id)->exists()) {
        return back()->with('error', 'Este personal ya tiene usuario.');
    }

    // MEJORA: Fallback a 'Docente2026*' y mensaje informativo claro
    $usarGenerica = empty($docente->CI);
    $passwordInicial = $usarGenerica ? 'Docente2026*' : $docente->CI;

    User::create([
        'IdPersonal' => $docente->IdPersonal,
        'Email'      => $docente->CorreoElectronico,
        'Password'   => Hash::make($passwordInicial),
        'Activo'     => true
    ]);

    $msg = $usarGenerica 
        ? 'Usuario creado. Contraseña temporal: Docente2026* (Falta CI).' 
        : 'Usuario creado. Contraseña: Su número de Carnet.';

    return back()->with('success', $msg);

    }

    // C. Revocar Acceso (Eliminar Login)
    public function revokeUser($id)
    {
        if (!$this->validarJerarquia($id)) {
            return back()->with('error', 'Acción denegada: Rango insuficiente.');
        }

        $user = User::where('IdPersonal', $id)->first();
        
        if ($user) {
            // Protección Anti-Suicidio (No borrar tu propia cuenta)
            if ($user->IdUser == Auth::id()) {
                return back()->with('error', 'No puedes eliminar tu propia cuenta desde aquí.');
            }

            $user->delete();
            return back()->with('success', 'Acceso al sistema revocado correctamente.');
        }
        
        return back()->with('error', 'El usuario no existe.');
    }

    // =========================================================================
    // 5. API AUXILIARES (AJAX)
    // =========================================================================
    public function porCarrera($idCarrera)
    {
        $carrera = Carrera::findOrFail($idCarrera);
        $docentes = Personal::whereHas('carrerasPersonal', function($query) use ($idCarrera) {
            $query->where('IdCarrera', $idCarrera);
        })->with(['contrato', 'cargo'])->get();

        return response()->json([
            'carrera' => $carrera->NombreCarrera,
            'cantidad' => $docentes->count(),
            'docentes' => $docentes
        ]);
    }


    // Al final del controlador
// En app/Http/Controllers/PersonalController.php

   // =========================================================================
    // IMPRESIÓN PDF (Método renombrado para coincidir con tu ruta)
    // =========================================================================
    public function printInformacion($id)
    {
        $user = Auth::user();

        // 1. Verificación de seguridad
        // Usamos 'gestionar_personal' que es el permiso estándar que no cambia de nombre
        $permisoGlobal = Gate::allows('gestionar_personal'); 

        // Si NO es gestor Y NO es su propio perfil, bloqueamos
        if (!$permisoGlobal && $user->IdPersonal != $id) {
            abort(403, 'No tienes permiso para imprimir este documento.');
        }

        // 2. Cargar datos (Relaciones necesarias para el PDF)
        $docente = Personal::with([
            'contrato', 
            'cargo', 
            'materias', 
            'formaciones.gradoAcademico', 
            'formaciones.centroFormacion',
            'carreras.facultad'
        ])->findOrFail($id);
        
        // 3. Retornar la vista
        // Nota: El archivo físico se mantiene como 'print.blade.php'
        return view('personal.print', compact('docente'));
    }

}