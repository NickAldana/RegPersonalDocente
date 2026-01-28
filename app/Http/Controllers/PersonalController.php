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
use Illuminate\Support\Facades\Cache;

class PersonalController extends Controller
{
    // =========================================================================
    // 1. LISTADO Y BÚSQUEDA
    // =========================================================================
   // En PersonalController.php

// =========================================================================
// 1. LISTADO Y BÚSQUEDA (OPTIMIZADO)
// =========================================================================
public function index(Request $request)
{
    /** @var \App\Models\User $currentUser */
    $currentUser = Auth::user();
    
    // 1. CACHÉ DE CARRERAS (Catálogo para el filtro)
    $carreras = Cache::remember('catalogo_carreras_filtro_' . $currentUser->id, 86400, function() use ($currentUser) {
        $query = Carrera::orderBy('NombreCarrera');
        if (!$currentUser->canDo('acceso_total')) {
            $misCarrerasIds = $currentUser->personal->carreras->pluck('IdCarrera')->toArray();
            $query->whereIn('IdCarrera', $misCarrerasIds);
        }
        return $query->get(['IdCarrera', 'NombreCarrera']);
    });

    // 2. LÓGICA DE CARGA BAJO DEMANDA (CORREGIDA)
    // Detectamos si la URL tiene parámetros. Si está vacía (entrada inicial), no cargamos nada.
    // Si tiene parámetros (clic en Filtrar), procedemos con la consulta aunque los campos estén vacíos.
    if (empty($request->query())) {
        $personal = collect(); 
        return view('personal.index', compact('personal', 'carreras'));
    }

    // 3. CONSULTA OPTIMIZADA
    $query = Personal::select('IdPersonal', 'NombreCompleto', 'ApellidoPaterno', 'ApellidoMaterno', 'CI', 'IdCargo', 'Activo', 'FotoPerfil')
        ->with([
            'cargo:IdCargo,NombreCargo', 
            'contrato:IdTipoContrato,NombreContrato',
            'formaciones' => fn($q) => $q->select('IdFormacion', 'IdPersonal', 'IdGradoAcademico', 'TituloObtenido')
        ])
        ->withCount('materias');

    // SEGURIDAD HORIZONTAL (Visión de Túnel para Dirección de Acreditación)
    if (!$currentUser->canDo('acceso_total')) {
        $query->whereHas('carreras', function($q) use ($currentUser) {
            $misCarrerasIds = $currentUser->personal->carreras->pluck('IdCarrera')->toArray();
            $q->whereIn('Carrera.IdCarrera', $misCarrerasIds);
        });
    }

    // APLICACIÓN DE FILTROS (Solo si tienen valor)
    if ($request->filled('buscar')) {
        $busqueda = trim($request->buscar);
        $query->where(function($q) use ($busqueda) {
            $q->where('NombreCompleto', 'like', $busqueda . '%')
              ->orWhere('CI', 'like', $busqueda . '%');
        });
    }

    if ($request->filled('carrera')) {
        $query->whereHas('carreras', fn($q) => $q->where('Carrera.IdCarrera', $request->carrera));
    }

    if ($request->filled('estado')) {
        $query->where('Activo', $request->estado);
    }

    // PAGINACIÓN LIVIANA
    $personal = $query->orderBy('ApellidoPaterno')
                      ->paginate(15)
                      ->withQueryString();
    
    return view('personal.index', compact('personal', 'carreras'));
}

// =========================================================================
// 2. ACCIONES (LIMPIEZA DE CACHÉ AUTOMÁTICA)
// =========================================================================
public function toggleStatus($id)
{
    if (!$this->validarJerarquia($id)) {
        return back()->with('error', 'Acción denegada.');
    }

    $docente = Personal::findOrFail($id);
    $docente->Activo = !$docente->Activo;
    $docente->save();

    // Sincronizamos Login
    if ($docente->usuario) {
        $docente->usuario->Activo = $docente->Activo;
        $docente->usuario->save();
    }

    // IMPORTANTE: Limpiamos los indicadores del Dashboard para que se actualicen
    Cache::forget('dashboard_stats_global');
    Cache::forget('dashboard_stats_user_' . Auth::id());

    return back()->with('success', "Estado actualizado.");
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
    
    // 1. Catálogos rápidos (Caché de 24h) para evitar consultas a Azure
    $carreras = Cache::remember('cat_carreras_all', 86400, fn() => 
        Carrera::orderBy('NombreCarrera')->get(['IdCarrera', 'NombreCarrera'])
    );

    $grados = Cache::remember('cat_grados_academicos', 86400, fn() => 
        GradoAcademico::all(['IdGradoAcademico', 'NombreGrado'])
    );

    $tiposContrato = Cache::remember('cat_tipos_contrato', 86400, fn() => 
        TipoContrato::all(['IdTipoContrato', 'NombreContrato'])
    );

    // 2. Cargos sugeridos (Filtrado por jerarquía)
    $miNivel = $currentUser->canDo('acceso_total') ? 1000 : ($currentUser->personal->cargo->nivel_jerarquico ?? 0);
    $cargos = Cargo::where('nivel_jerarquico', '<', $miNivel)
                   ->orderBy('nivel_jerarquico', 'desc')
                   ->get(['IdCargo', 'NombreCargo']);

    // 3. Listado de Carreras Típicas para el Autocompletado (Datalist)
    $carrerasTipicas = [
        'Ingeniería de Sistemas', 'Ingeniería Industrial', 'Ingeniería Comercial',
        'Administración de Empresas', 'Derecho', 'Psicología', 'Contaduría Pública',
        'Comunicación Social', 'Medicina', 'Arquitectura', 'Diseño Gráfico'
    ];

    return view('personal.create', compact('tiposContrato', 'cargos', 'carreras', 'grados', 'carrerasTipicas'));
}

   public function store(Request $request)
{
    // 1. VALIDACIONES (ArchivoTitulo ahora es opcional)
    $request->validate([
        'IdCargo'          => 'required|exists:Cargo,IdCargo',
        'IdCarrera'        => 'required|exists:Carrera,IdCarrera',
        'NombreCompleto'   => 'required|string|min:3|max:200',
        'ApellidoPaterno'  => 'required|string|min:2|max:100',
        'CI'               => 'required|string|max:20|unique:Personal,CI',
        'IdTipoContrato'   => 'required|exists:TipoContrato,IdTipoContrato',
        'IdGradoAcademico' => 'required|exists:GradoAcademico,IdGradoAcademico',
        'TituloObtenido'   => 'required|string|min:3|max:300',
        'AñoEstudios'      => 'required|integer|min:1950|max:' . date('Y'),
        'ArchivoTitulo'    => 'nullable|file|mimes:pdf|max:5120', // Opcional, máx 5MB
    ]);

    DB::beginTransaction();
    try {
        // 2. SANITIZACIÓN (Nick brandon -> Nick Brandon)
        $nombreLimpio   = Str::title(trim($request->NombreCompleto)); 
        $paternoLimpio  = Str::title(trim($request->ApellidoPaterno));
        $maternoLimpio  = $request->ApellidoMaterno ? Str::title(trim($request->ApellidoMaterno)) : null;

        // 3. GENERACIÓN DE CORREO (sc.nick.perez.m@upds.net.bo)
        $primerNombreSlug = Str::slug(explode(' ', $nombreLimpio)[0]); 
        $paternoSlug      = Str::slug($paternoLimpio);
        $inicialMaterno   = $maternoLimpio ? substr(Str::slug($maternoLimpio), 0, 1) : '';
        $emailGenerado    = "sc.{$primerNombreSlug}.{$paternoSlug}" . ($inicialMaterno ? ".{$inicialMaterno}" : "") . "@upds.net.bo";

        // Evitar duplicados de correo
        $contador = 1;
        while (Personal::where('CorreoElectronico', $emailGenerado)->exists()) {
            $emailGenerado = "sc.{$primerNombreSlug}.{$paternoSlug}" . ($inicialMaterno ? ".{$inicialMaterno}" : "") . str_pad($contador, 2, '0', STR_PAD_LEFT) . "@upds.net.bo";
            $contador++;
        }

        // 4. GUARDADO DE PERSONAL
        $nuevoDocente = Personal::create([
            'NombreCompleto'    => $nombreLimpio,
            'ApellidoPaterno'   => $paternoLimpio,
            'ApellidoMaterno'   => $maternoLimpio,
            'CI'                => $request->CI,
            'CorreoElectronico' => $emailGenerado,
            'IdCargo'           => $request->IdCargo,
            'IdTipoContrato'    => $request->IdTipoContrato,
            'Activo'            => true
        ]);

        // 5. VINCULAR CARRERA (Para Power BI / Acreditación)
        DB::table('CarreraPersonal')->insert([
            'IdCarrera'  => $request->IdCarrera,
            'IdPersonal' => $nuevoDocente->IdPersonal,
            'Gestion'    => 2026 
        ]);

        // 6. REGISTRAR FORMACIÓN (Manejo de PDF)
        $rutaPDF = null;
        if ($request->hasFile('ArchivoTitulo')) {
            $rutaPDF = $request->file('ArchivoTitulo')->store('titulos', 'public');
        }

        Formacion::create([
            'IdPersonal'        => $nuevoDocente->IdPersonal,
            'IdCentroFormacion' => 1, // Por defecto UPDS (puedes cambiarlo)
            'IdGradoAcademico'  => $request->IdGradoAcademico,
            'TituloObtenido'    => Str::upper($request->TituloObtenido),
            'AñoEstudios'       => $request->AñoEstudios,
            'RutaArchivo'       => $rutaPDF
        ]);

        // 7. CREAR USUARIO LOGIN (Contraseña = CI)
        User::create([
            'IdPersonal' => $nuevoDocente->IdPersonal,
            'Email'      => $emailGenerado,
            'Password'   => Hash::make($request->CI),
            'Activo'     => true
        ]);

        DB::commit();

        // LIMPIEZA DE CACHÉ: Actualiza el Dashboard de inmediato
        Cache::forget('dashboard_stats_global');
        Cache::forget('dashboard_stats_user_' . Auth::id());

        return redirect()->route('personal.index')
            ->with('success', "Personal registrado: $nombreLimpio. Correo: $emailGenerado. " . ($rutaPDF ? "" : "Póngase en contacto para el PDF."));

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

    // 1. SEGURIDAD: Reutilizamos el Gate cacheado para rapidez
    if (!$user->canDo('ver_kardex_global') && $user->IdPersonal != $id) {
        abort(403, 'No tienes autorización para ver este perfil.');
    }

    // 2. CONSULTA SELECTIVA (Eager Loading con columnas específicas)
    // Esto evita traer datos binarios o de auditoría innecesarios de Azure.
    $docente = Personal::with([
        'contrato:IdTipoContrato,NombreContrato', 
        'cargo:IdCargo,NombreCargo', 
        'materias:IdMateria,NombreMateria', 
        'formaciones.gradoAcademico:IdGradoAcademico,NombreGrado', 
        'formaciones.centroFormacion:IdCentroFormacion,NombreCentro',
        'publicaciones'
    ])->findOrFail($id);

    // 3. CACHÉ DE CATÁLOGOS PARA MODALES
    // Estos datos se usan en los modales de "Agregar Título" o "Editar Formación".
    // Al cachearlos, los formularios de edición cargan instantáneamente.
    $grados = Cache::remember('cat_grados_academicos', 86400, function() {
        return GradoAcademico::all(['IdGradoAcademico', 'NombreGrado']);
    });

    $centros = Cache::remember('cat_centros_formacion', 86400, function() {
        return CentroFormacion::all(['IdCentroFormacion', 'NombreCentro']);
    });

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