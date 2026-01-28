<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // <--- VITAL PARA VELOCIDAD
use App\Models\Personal;
use App\Models\Materia;
use App\Models\Carrera;

class CargaAcademicaController extends Controller
{
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $esSuperAdmin = Gate::allows('acceso_total') || ($user->canDo('acceso_total') ?? false);

        // ---------------------------------------------------------------------
        // 1. PREPARACIÓN DE FILTROS (Lógica de Seguridad)
        // ---------------------------------------------------------------------
        // Calculamos los IDs permitidos ANTES de entrar al caché para no guardar basura
        $permisos = ['nivel' => 0, 'carreras' => [], 'facultades' => []];
        
        if (!$esSuperAdmin) {
            $miPersonal = $user->personal;
            
            if (!$miPersonal) {
                abort(403, 'No tienes un perfil de personal asociado para gestionar carga.');
            }

            $misCarrerasIds = $miPersonal->carreras->pluck('IdCarrera')->toArray();
            
            if (empty($misCarrerasIds)) {
                return redirect()->route('dashboard')->with('error', 'No tienes ninguna carrera asignada a tu cargo.');
            }

            $permisos['nivel'] = $user->cargo()->nivel_jerarquico ?? 0;
            $permisos['carreras'] = $misCarrerasIds;
            
            // Si es Decano (Nivel >= 80), calculamos sus facultades
            if ($permisos['nivel'] >= 80) {
                $permisos['facultades'] = Carrera::whereIn('IdCarrera', $misCarrerasIds)
                                                 ->pluck('IdFacultad')
                                                 ->unique()
                                                 ->toArray();
            }
        }

        // ---------------------------------------------------------------------
        // 2. RECUPERACIÓN DE DOCENTES (OPTIMIZADA Y CACHEADA)
        // ---------------------------------------------------------------------
        // Guardamos la lista en memoria por 10 minutos (600s). Clave única por usuario.
        $cacheKeyDocentes = $esSuperAdmin ? 'docentes_carga_all' : 'docentes_carga_user_' . $user->id;

        $docentes = Cache::remember($cacheKeyDocentes, 600, function() use ($esSuperAdmin, $permisos) {
            // SELECT: Traemos SOLO lo que se ve en la tarjeta (Foto, Nombre, Cargo)
            $query = Personal::where('Activo', 1)
                ->select('IdPersonal', 'NombreCompleto', 'ApellidoPaterno', 'ApellidoMaterno', 'FotoPerfil', 'IdCargo', 'IdTipoContrato')
                ->with([
                    'cargo:IdCargo,NombreCargo',          // Para mostrar "DOCENTE" o cargo real
                    'contrato:IdTipoContrato,NombreContrato' // Para mostrar "TIEMPO COMPLETO"
                ])
                ->orderBy('ApellidoPaterno');

            if (!$esSuperAdmin) {
                // Filtro Jerárquico dentro del caché
                if ($permisos['nivel'] >= 80) { // Decano
                    $query->whereHas('carreras', fn($q) => $q->whereIn('IdFacultad', $permisos['facultades']));
                } else { // Jefe de Carrera
                    $query->whereHas('carreras', fn($q) => $q->whereIn('Carrera.IdCarrera', $permisos['carreras']));
                }
            }
            return $query->get();
        });

        // ---------------------------------------------------------------------
        // 3. RECUPERACIÓN DE MATERIAS (EL CUELLO DE BOTELLA SOLUCIONADO)
        // ---------------------------------------------------------------------
        // Esta lista es pesada (+1000 items). La cacheamos por 1 HORA (3600s).
        $cacheKeyMaterias = $esSuperAdmin ? 'materias_carga_all' : 'materias_carga_user_' . $user->id;

        $materias = Cache::remember($cacheKeyMaterias, 3600, function() use ($esSuperAdmin, $permisos) {
            // SELECT: Solo ID, Nombre y Sigla. Nada de descripciones largas.
            $query = Materia::select('IdMateria', 'NombreMateria', 'IdCarrera', 'Sigla')
                ->with('carrera:IdCarrera,NombreCarrera') // Para mostrar la etiqueta de la carrera
                ->orderBy('NombreMateria');

            if (!$esSuperAdmin) {
                if ($permisos['nivel'] >= 80) { // Decano
                    $query->whereHas('carrera', fn($q) => $q->whereIn('IdFacultad', $permisos['facultades']));
                } else { // Jefe de Carrera
                    $query->whereIn('IdCarrera', $permisos['carreras']);
                }
            }
            return $query->get();
        });

        // Parámetros para pre-selección (si vienes redirigido)
        $docente_id = $request->get('docente_id');
        $materia_id = $request->get('IdMateria'); 

        return view('carga.create', compact('docentes', 'materias', 'docente_id', 'materia_id'));
    }

    public function store(Request $request)
    {
        // Validación estricta
        $request->validate([
            'IdPersonal' => 'required|exists:Personal,IdPersonal',
            'IdMateria'  => 'required|exists:Materia,IdMateria',
            'Gestion'    => 'required|integer|min:2020|max:2030',
            'Periodo'    => 'required|string', 
        ]);

        try {
            // 1. Verificación de Duplicados (Usando Query Builder para velocidad)
            $existe = DB::table('PersonalMateria')
                ->where('IdPersonal', $request->IdPersonal)
                ->where('IdMateria', $request->IdMateria)
                ->where('Gestion', $request->Gestion)
                ->where('Periodo', $request->Periodo)
                ->exists();

            if ($existe) {
                return back()->with('error', 'El docente ya tiene asignada esta materia en este periodo.')->withInput();
            }

            // 2. Inserción Rápida
            DB::table('PersonalMateria')->insert([
                'IdPersonal' => $request->IdPersonal,
                'IdMateria'  => $request->IdMateria,
                'Gestion'    => $request->Gestion,
                'Periodo'    => $request->Periodo,
            ]);

            // 3. INVALIDACIÓN DE CACHÉ DEL DASHBOARD (Vital)
            // Al agregar una materia, los contadores del dashboard cambian. Forzamos su actualización.
            Cache::forget('dashboard_stats_global');
            Cache::forget('dashboard_stats_user_' . Auth::id());

            // Opcional: Si quieres que la lista de docentes se refresque (ej. contador de materias asignadas)
            // Cache::forget('docentes_carga_user_' . Auth::id());

            return redirect()->route('personal.show', $request->IdPersonal)
                ->with('success', 'Carga académica asignada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error técnico al guardar: ' . $e->getMessage())->withInput();
        }
    }
}