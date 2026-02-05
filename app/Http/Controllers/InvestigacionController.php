<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{Proyectoinvestigacion, Publicacion, Personal, Carrera, Tipopublicacion, Mediopublicacion, Rol, Lineainvestigacion};

class InvestigacionController extends Controller
{
    // Helper para centralizar la traducción de roles (Switch optimizado)
    private function traducirRol($codigo) {
        return match ($codigo) {
            '1' => 'ENCARGADO DE PROYECTO',
            '2' => 'DOCENTE INVESTIGADOR',
            '3' => 'ESTUDIANTE INVESTIGADOR',
            '4' => 'PASANTE',
            '5' => 'REVISOR TÉCNICO',
            '6' => 'TUTOR / ASESOR',
            default => $codigo, 
        };
    }

    // =========================================================================
    // BLOQUE 1: INDEX CON FILTROS DE FECHA
    // =========================================================================
   public function index(Request $request)
{
    // 1. Validación de filtros para mayor seguridad
    $request->validate([
        'fecha_desde' => 'nullable|date',
        'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        'estado'      => 'nullable|in:En Ejecución,Planificado,Finalizado,Cancelado',
    ]);

    $busqueda = $request->get('q');
    $filtroEstado = $request->get('estado');
    $filtroCarrera = $request->get('carrera');
    
    // 2. Optimización de Carga (Eager Loading)
    // He añadido 'equipo' para que el conteo de investigadores en la vista sea instantáneo
    $proyectos = Proyectoinvestigacion::with(['carrera', 'linea', 'equipo']) 
        ->when($busqueda, function($query) use ($busqueda) {
            return $query->where(function($q) use ($busqueda) {
                $q->where('Nombreproyecto', 'LIKE', "%$busqueda%")
                  ->orWhere('CodigoProyecto', 'LIKE', "%$busqueda%");
            });
        })
        ->when($filtroEstado, function($q) use ($filtroEstado) { 
            return $q->where('Estado', $filtroEstado); 
        })
        ->when($filtroCarrera, function($q) use ($filtroCarrera) { 
            return $q->where('CarreraID', $filtroCarrera); 
        })
        ->when($request->fecha_desde, function($q) use ($request) { 
            return $q->whereDate('Fechainicio', '>=', $request->fecha_desde); 
        })
        ->when($request->fecha_hasta, function($q) use ($request) { 
            return $q->whereDate('Fechainicio', '<=', $request->fecha_hasta); 
        })
        // 3. Orden lógico: Los más recientes primero y luego por ID
        ->orderBy('Fechainicio', 'desc')
        ->orderBy('ProyectoinvestigacionID', 'desc')
        ->paginate(10)
        ->appends($request->all());

    // 4. Datos para los selectores del filtro
    $carreras = Carrera::orderBy('Nombrecarrera')->get();
    
    // Si no vas a usar la lista de personal en el INDEX, podrías quitar esta línea 
    // para liberar memoria, a menos que tengas un modal de creación rápida ahí.
    $personales = Personal::where('Activo', 1)->orderBy('Apellidopaterno')->get();
    
    return view('investigacion.index', compact(
        'proyectos', 
        'personales', 
        'carreras', 
        'busqueda', 
        'filtroEstado', 
        'filtroCarrera'
    ));
}
    /**
     * Vista principal del Repositorio de Publicaciones (Index)
     */
    public function indexPublicaciones(Request $request)
    {
        // 1. Validaciones de entrada para los filtros
        $request->validate([
            'q' => 'nullable|string|max:100',
            'carrera' => 'nullable|exists:Carrera,CarreraID',
            'tipo' => 'nullable|exists:Tipopublicacion,TipopublicacionID',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        ], [
            'fecha_hasta.after_or_equal' => 'La fecha final no puede ser anterior a la fecha inicial.'
        ]);

        $query = $request->get('q');
        $carreraId = $request->get('carrera');
        $tipoId = $request->get('tipo');
        $fDesde = $request->get('fecha_desde');
        $fHasta = $request->get('fecha_hasta');

        // 2. Consulta con relaciones y filtros dinámicos
        $publicaciones = Publicacion::with(['tipo', 'medio', 'autores', 'proyecto.carrera', 'linea'])
            ->when($query, function($q) use ($query) { 
                return $q->where('Nombrepublicacion', 'LIKE', "%$query%"); 
            })
            ->when($carreraId, function($q) use ($carreraId) {
                return $q->whereHas('proyecto', function($p) use ($carreraId) { 
                    $p->where('CarreraID', $carreraId); 
                });
            })
            ->when($tipoId, function($q) use ($tipoId) { 
                return $q->where('TipopublicacionID', $tipoId); 
            })
            ->when($fDesde, function($q) use ($fDesde) { 
                return $q->whereDate('Fechapublicacion', '>=', $fDesde); 
            })
            ->when($fHasta, function($q) use ($fHasta) { 
                return $q->whereDate('Fechapublicacion', '<=', $fHasta); 
            })
            ->orderBy('Fechapublicacion', 'desc')
            ->paginate(15)
            ->appends($request->all());

        // 3. Datos para los select de los filtros
        $carreras = Carrera::orderBy('Nombrecarrera')->get();
        $tipos = Tipopublicacion::orderBy('Nombretipo')->get();

        return view('publicaciones.index', compact('publicaciones', 'query', 'carreras', 'tipos'));
    }

    // =========================================================================
    // BLOQUE 2: GESTIÓN DE PROYECTOS CON LÓGICA DE FECHAS
    // =========================================================================
    // =========================================================================
    // NUEVO MÉTODO: VISTA DE CREACIÓN DE PROYECTO
    // =========================================================================
    public function createProyecto()
{
    $carreras = Carrera::orderBy('Nombrecarrera')->get();
    $personales = Personal::where('Activo', 1)->orderBy('Apellidopaterno')->get();
    // Cargamos con relación facultad para mostrar sigla en el buscador
    $lineas = Lineainvestigacion::with('facultad')->orderBy('Nombrelineainvestigacion')->get();

    return view('proyectos.create', compact('carreras', 'personales', 'lineas'));
}

public function storeProyecto(Request $request)
{
    // 1. VALIDACIÓN DINÁMICA Y RIGUROSA
    $request->validate([
        'Nombreproyecto' => 'required|min:10',
        'Estado'         => 'required|in:En Ejecución,Planificado,Finalizado,Cancelado',
        // Si es Planificado, debe ser estrictamente fecha futura (después de hoy).
        // Si es En Ejecución, debe ser hoy o una fecha pasada.
        'Fechainicio'    => $request->Estado === 'Planificado' 
                            ? 'required|date|after:today' 
                            : 'required|date|before_or_equal:today',
        'participantes'  => 'required|array|min:1',
    ], [
        'Fechainicio.after' => 'Un proyecto planificado debe programarse para una fecha futura.',
        'Fechainicio.before_or_equal' => 'Un proyecto en ejecución no puede tener una fecha de inicio futura.'
    ]);

    DB::beginTransaction();
    try {
        // 2. GENERACIÓN DE CÓDIGO AUTOMÁTICO
        $anioActual = date('Y');
        $ultimoProyecto = Proyectoinvestigacion::where('CodigoProyecto', 'LIKE', "INV-$anioActual-%")
                            ->orderBy('CodigoProyecto', 'desc')
                            ->lockForUpdate() 
                            ->first();

        if ($ultimoProyecto) {
            $partes = explode('-', $ultimoProyecto->CodigoProyecto);
            $ultimoCorrelativo = (int)end($partes);
            $numero = $ultimoCorrelativo + 1;
        } else {
            $numero = 1; 
        }

        $codigoGenerado = "INV-" . $anioActual . "-" . str_pad($numero, 3, '0', STR_PAD_LEFT);

        // 3. CREACIÓN DEL PROYECTO
        $proyecto = Proyectoinvestigacion::create(array_merge($request->except('CodigoProyecto'), [
            'Nombreproyecto' => Str::upper($request->Nombreproyecto),
            'CodigoProyecto' => $codigoGenerado 
        ]));

        // 4. GUARDAR EQUIPO (Sin duplicados)
        $participantesUnicos = array_unique($request->participantes);
        foreach ($participantesUnicos as $index => $idPersonal) {
            if (empty($idPersonal)) continue;
            
            $proyecto->equipo()->attach($idPersonal, [
                'Rol'           => $this->traducirRol($request->roles_proy[$index]),
                'EsResponsable' => isset($request->es_responsable[$index]) ? 1 : 0, 
                // Se usa la fecha de ingreso específica o la del inicio del proyecto
                'FechaInicio'   => $request->fechas_inc[$index] ?? $proyecto->Fechainicio, 
            ]);
        }
        
        DB::commit();
        return redirect()->route('investigacion.index')
                         ->with('success', "Proyecto $codigoGenerado registrado correctamente.");

    } catch (\Exception $e) { 
        DB::rollBack(); 
        return back()->with('error', 'Error al registrar: ' . $e->getMessage())->withInput(); 
    }
}
/**
     * Muestra el formulario de edición con los datos cargados.
     */
public function editProyecto($id)
    {
        // Cargamos el proyecto con su equipo (relación belongsToMany)
        $proyecto = Proyectoinvestigacion::with('equipo')->findOrFail($id);

        // Bloqueo de seguridad: No se editan proyectos que ya han sido cerrados históricamente
        if (in_array($proyecto->Estado, ['Finalizado', 'Cancelado'])) {
            return redirect()->route('investigacion.index')
                ->with('error', 'ACCESO DENEGADO: El proyecto se encuentra cerrado (' . $proyecto->Estado . ').');
        }

        $carreras = Carrera::orderBy('Nombrecarrera')->get();
        $personales = Personal::where('Activo', 1)->orderBy('Apellidopaterno')->get();
        $lineas = Lineainvestigacion::with('facultad')->orderBy('Nombrelineainvestigacion')->get(); 
        
        return view('proyectos.edit', compact('proyecto', 'carreras', 'personales', 'lineas'));
    }

    /**
     * Procesa la actualización de datos y sincronización de personal.
     */
public function updateProyecto(Request $request, $id)
    {
        $proyecto = Proyectoinvestigacion::findOrFail($id);

        // 1. Verificación de seguridad: Integridad Histórica
        if (in_array($proyecto->Estado, ['Finalizado', 'Cancelado'])) {
            return redirect()->route('investigacion.index')
                ->with('error', 'ERROR DE INTEGRIDAD: No se pueden alterar registros históricos cerrados.');
        }

        // 2. Validaciones
        $request->validate([
            'Nombreproyecto' => 'required|min:10',
            'Estado'         => 'required|in:En Ejecución,Planificado,Finalizado,Cancelado',
            // Reglas de fecha según estado
            'Fechainicio'    => $request->Estado === 'Planificado' 
                                ? 'required|date|after:today' 
                                : 'required|date|before_or_equal:today',
            'participantes'  => 'required|array|min:1',
        ], [
            'Fechainicio.after' => 'La planificación debe programarse para una fecha futura.',
            'Fechainicio.before_or_equal' => 'Un proyecto vigente no puede iniciar en una fecha futura.'
        ]);

        DB::beginTransaction();
        try {
            $esCierreProyecto = in_array($request->Estado, ['Finalizado', 'Cancelado']);
            $hoy = now()->format('Y-m-d');

            // 3. Actualizar datos maestros del proyecto
            $proyecto->update(array_merge($request->except('CodigoProyecto'), [
                'Nombreproyecto'    => Str::upper($request->Nombreproyecto),
                'Fechafinalizacion' => $esCierreProyecto ? $hoy : null
            ]));

            /* * 4. REINICIO TOTAL DEL EQUIPO 
             * Usamos detach() para limpiar la tabla pivote de este proyecto.
             * Esto es necesario porque sync() usa el ID como clave única y sobrescribiría 
             * el registro histórico si intentamos meter a la misma persona de nuevo.
             */
            $proyecto->equipo()->detach();

            // 5. Insertar registros uno por uno (Loop de Attach)
            foreach ($request->participantes as $i => $idp) {
                if (empty($idp)) continue;

                // A. Sanitización de Fecha Fin (Convertir vacío a NULL)
                // Si viene vacío del formulario, es NULL (Activo). Si tiene fecha, es Histórico.
                $fechaFinInput = !empty($request->fechas_fin[$i]) ? $request->fechas_fin[$i] : null;
                
                // B. Lógica de Cierre Global
                // Si el proyecto se cierra, forzamos fecha fin 'hoy' (a menos que ya tuviera una fecha anterior).
                $fechaFinFinal = $esCierreProyecto ? ($fechaFinInput ?? $hoy) : $fechaFinInput;

                // C. Fecha Inicio
                // Usamos la del input o, si falla, la del proyecto por defecto.
                $fechaInicioFinal = $request->fechas_inc[$i] ?? $proyecto->Fechainicio;

                // D. Insertar el registro individualmente
                // attach() permite duplicados de ID siempre que sean registros (filas) diferentes
                $proyecto->equipo()->attach($idp, [
                    'Rol'           => $this->traducirRol($request->roles_proy[$i]),
                    'EsResponsable' => isset($request->es_responsable[$i]) ? 1 : 0,
                    'FechaInicio'   => $fechaInicioFinal,
                    'FechaFin'      => $fechaFinFinal
                ]);
            }
            
            DB::commit();
            return redirect()->route('investigacion.index')
                ->with('success', "Expediente del proyecto {$proyecto->CodigoProyecto} actualizado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Detección de errores específicos de SQL Server para claves duplicadas
            // Esto pasa si tu tabla pivote tiene un constraint UNIQUE(ProyectoinvestigacionID, PersonalID)
            if (str_contains($e->getMessage(), 'Duplicate entry') || 
                str_contains($e->getMessage(), 'Violation of PRIMARY KEY') || 
                str_contains($e->getMessage(), 'Violation of UNIQUE KEY')) {
                
                return back()->with('error', 'ERROR DE BASE DE DATOS: La configuración actual de tu base de datos impide registrar a la misma persona dos veces en un proyecto (Restricción Unique). Contacta a soporte técnico.')->withInput();
            }

            return back()->with('error', 'Fallo en la actualización: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================================
    // BLOQUE 3: GESTIÓN DE PUBLICACIONES (AUTOMATIZADA)
    // =========================================================================

    public function createPublicacion()
    {
        $tipos = Tipopublicacion::all();
        $medios = Mediopublicacion::all();
        $roles = Rol::all(); 
        $lineas = Lineainvestigacion::with('facultad')->orderBy('Nombrelineainvestigacion')->get();
        $personales = Personal::where('Activo', 1)->orderBy('Apellidopaterno')->get(); 

        // Traemos proyectos con su línea y su equipo para el autocompletado en JS
        $proyectos = Proyectoinvestigacion::with(['linea', 'equipo'])
            ->where('Estado', 'En Ejecución')
            ->orderBy('Nombreproyecto')
            ->get();

        return view('publicaciones.create', compact('tipos', 'medios', 'roles', 'proyectos', 'personales', 'lineas'));
    }

    public function storePublicacion(Request $request)
    {
        // 1. Validaciones de integridad y seguridad de archivos
        $request->validate([
            'Nombrepublicacion' => 'required|string|min:10|max:500',
            'Fechapublicacion' => 'required|date|before_or_equal:today',
            'LineainvestigacionID' => 'required|exists:Lineainvestigacion,LineainvestigacionID',
            'MediopublicacionID' => 'required',
            'archivo_evidencia' => 'nullable|file|mimes:pdf|max:10240', // Solo PDF, máx 10MB
            'UrlPublicacion' => 'nullable|url',
            'autores' => 'required|array|min:1',
        ], [
            'Fechapublicacion.before_or_equal' => 'No se pueden registrar publicaciones con fechas futuras.',
            'archivo_evidencia.mimes' => 'El archivo de respaldo debe ser obligatoriamente un PDF.'
        ]);

        DB::beginTransaction();
        try {
            // 2. Creación forzando Mayúsculas para el reporte formal (SIA v4.0)
            $publicacion = Publicacion::create(array_merge($request->all(), [
                'Nombrepublicacion' => Str::upper($request->Nombrepublicacion)
            ]));

            // 3. Guardado físico del PDF si existe
            if ($request->hasFile('archivo_evidencia')) {
                $ruta = $request->file('archivo_evidencia')->store('evidencias_publicaciones', 'public');
                $publicacion->update(['RutaArchivo' => $ruta]);
            }

            // 4. Vinculación de autores con sus roles
            $syncData = [];
            foreach ($request->autores as $index => $idPersonal) {
                if (empty($idPersonal)) continue;
                $syncData[$idPersonal] = [
                    'RolID' => $request->roles[$index] ?? 1 // 1 = Autor principal por defecto
                ];
            }
            $publicacion->autores()->sync($syncData);

            DB::commit();
            return redirect()->route('publicaciones.index')->with('success', 'Publicación registrada y vinculada correctamente.');

        } catch (\Exception $e) { 
            DB::rollBack(); 
            return back()->with('error', 'Error en el registro: ' . $e->getMessage())->withInput(); 
        }
    }

    public function editPublicacion($id)
    {
        // Cargamos la publicación con sus autores para que JS pueda renderizarlos
        $publicacion = Publicacion::with('autores')->findOrFail($id);
        $tipos = Tipopublicacion::all();
        $medios = Mediopublicacion::all();
        $roles = Rol::all();
        $lineas = Lineainvestigacion::all();
        $proyectos = Proyectoinvestigacion::where('Estado', 'En Ejecución')->get();
        $personales = Personal::where('Activo', 1)->orderBy('Apellidopaterno')->get();

        return view('publicaciones.edit', compact('publicacion', 'tipos', 'medios', 'roles', 'proyectos', 'personales', 'lineas'));
    }

    public function updatePublicacion(Request $request, $id)
    {
        $request->validate([
            'Nombrepublicacion' => 'required|min:10',
            'Fechapublicacion' => 'required|date|before_or_equal:today',
        ]);

        DB::beginTransaction();
        try {
            $publicacion = Publicacion::findOrFail($id);
            $publicacion->update(array_merge($request->all(), [
                'Nombrepublicacion' => Str::upper($request->Nombrepublicacion)
            ]));

            if ($request->hasFile('archivo_evidencia')) {
                $publicacion->update(['RutaArchivo' => $request->file('archivo_evidencia')->store('evidencias', 'public')]);
            }

            $syncData = [];
            foreach ($request->autores as $i => $idp) { 
                if(!empty($idp)) {
                    $syncData[$idp] = ['RolID' => $request->roles[$i] ?? 1]; 
                }
            }
            $publicacion->autores()->sync($syncData);

            DB::commit();
            return redirect()->route('publicaciones.index')->with('success', 'Registro actualizado con éxito.');
        } catch (\Exception $e) { 
            DB::rollBack(); 
            return back()->with('error', $e->getMessage()); 
        }
    }

    // =========================================================================
    // BLOQUE 4: GENERACIÓN DE REPORTES PDF (ESTILO SIA V4.0)
    // =========================================================================

   public function reportePublicacionesPDF(Request $request) 
{
    // 1. Filtramos exactamente igual que en la vista index para mantener coherencia académica
    $publicaciones = Publicacion::with(['tipo', 'autores', 'proyecto.carrera', 'linea', 'medio'])
        ->when($request->q, function($q) use ($request) { 
            return $q->where('Nombrepublicacion', 'LIKE', "%{$request->q}%"); 
        })
        ->when($request->carrera, function($q) use ($request) {
            return $q->whereHas('proyecto', function($p) use ($request) { 
                $p->where('CarreraID', $request->carrera); 
            });
        })
        ->when($request->tipo, function($q) use ($request) { 
            return $q->where('TipopublicacionID', $request->tipo); 
        })
        ->orderBy('Fechapublicacion', 'desc')
        ->get();

    // 2. CORRECCIÓN DE RUTA: Apuntamos al nombre real del archivo que mostraste
    // Cambiado de 'publicaciones.pdf' a 'publicaciones.pdf_publicacion'
    $pdf = Pdf::loadView('publicaciones.pdf_publicacion', compact('publicaciones'));
    
    // 3. Configuración de salida institucional
    return $pdf->setPaper('letter', 'portrait')
               ->stream('Kardex_Produccion_Cientifica_' . date('Ymd') . '.pdf');
}

 public function reporteProyectosPDF(Request $request)
{
    // CASO 1: REPORTE INDIVIDUAL (KARDEX)
    // Se activa cuando la URL tiene ?id=X
    if ($request->has('id')) {
        // Buscamos el proyecto específico
        $proyecto = Proyectoinvestigacion::with(['carrera', 'linea', 'equipo', 'publicaciones'])
            ->findOrFail($request->id);

        // Cargamos la vista del Kardex Individual
        $pdf = Pdf::loadView('investigacion.reporte_individual', compact('proyecto'));
        
        return $pdf->setPaper('letter', 'portrait')
                   ->stream('Kardex_'.$proyecto->CodigoProyecto.'.pdf');
    }

    // CASO 2: REPORTE GENERAL (LISTADO)
    // Se activa cuando NO hay ID en la URL (desde el Index)
    $proyectos = Proyectoinvestigacion::with(['carrera', 'linea', 'equipo'])
        ->when($request->estado, function($q) use ($request) { 
            return $q->where('Estado', $request->estado); 
        })
        ->when($request->carrera, function($q) use ($request) { 
            return $q->where('CarreraID', $request->carrera); 
        })
        ->orderBy('Fechainicio', 'desc')
        ->get();

    // Cargamos la vista de la Cartera General
    $pdf = Pdf::loadView('investigacion.pdf_proyectos', compact('proyectos'));
    
    return $pdf->setPaper('letter', 'landscape')
               ->download('Cartera_Proyectos_Investigacion.pdf');
}

public function showProyecto($id)
{
    // 1. CARGAMOS RELACIONES (Incluyendo 'publicaciones.tipo' para ver el nombre del tipo)
    $proyecto = Proyectoinvestigacion::with([
        'carrera', 
        'linea', 
        'equipo', 
        'publicaciones.tipo' // <--- AQUÍ ESTÁ LA CLAVE
    ])->findOrFail($id);

    // 2. ORDENAMIENTO DE EQUIPO (Mantenemos tu lógica que funciona bien)
    $equipoOrdenado = $proyecto->equipo->sort(function($a, $b) {
        if ($a->pivot->EsResponsable && !$b->pivot->EsResponsable) return -1;
        if (!$a->pivot->EsResponsable && $b->pivot->EsResponsable) return 1;
        $aActivo = is_null($a->pivot->FechaFin);
        $bActivo = is_null($b->pivot->FechaFin);
        if ($aActivo && !$bActivo) return -1;
        if (!$aActivo && $bActivo) return 1;
        return 0;
    });

    $proyecto->setRelation('equipo', $equipoOrdenado);

    return view('investigacion.show', compact('proyecto'));
}
}