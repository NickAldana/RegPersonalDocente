<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; 
use App\Models\Personal;
use App\Models\Materia;
use App\Models\Carrera;
use App\Models\Usuario;

class CargaAcademicaController extends Controller
{
    public function create(Request $request)
    {
        /** @var Usuario $user */
        $user = Auth::user();
        $esSuperAdmin = Gate::allows('acceso_total') || ($user->canDo('acceso_total') ?? false);

        // ---------------------------------------------------------------------
        // 1. PREPARACIÓN DE FILTROS (Lógica de Seguridad V3.1)
        // ---------------------------------------------------------------------
        $permisos = ['nivel' => 0, 'carreras' => [], 'facultades' => []];
        
        if (!$esSuperAdmin) {
            $miPersonal = $user->personal;
            
            if (!$miPersonal) {
                abort(403, 'No tienes un perfil de personal asociado.');
            }

            // En V3.1, obtenemos las carreras a través de las materias asignadas 
            $misCarrerasIds = $miPersonal->materias->pluck('CarreraID')->unique()->toArray();
            
            if (empty($misCarrerasIds)) {
                return redirect()->route('dashboard')->with('error', 'No tienes ninguna carrera bajo tu gestión.');
            }

            $permisos['nivel'] = $user->personal->cargo->nivel_jerarquico ?? 1000;
            $permisos['carreras'] = $misCarrerasIds;
            
            // Si tiene rango de autoridad (Decano/Nivel alto), calculamos facultades [cite: 111, 167]
            if ($permisos['nivel'] <= 2) { 
                $permisos['facultades'] = Carrera::whereIn('CarreraID', $misCarrerasIds)
                                                 ->pluck('FacultadID')
                                                 ->unique()
                                                 ->toArray();
            }
        }

        // ---------------------------------------------------------------------
        // 2. RECUPERACIÓN DE DOCENTES (V3.1 Columns)
        // ---------------------------------------------------------------------
        $cacheKeyDocentes = $esSuperAdmin ? 'docentes_carga_all' : 'docentes_carga_user_' . $user->UsuarioID;

        $docentes = Cache::remember($cacheKeyDocentes, 600, function() use ($esSuperAdmin, $permisos) {
            $query = Personal::where('Activo', 1)
                ->select('PersonalID', 'Nombrecompleto', 'Apellidopaterno', 'Apellidomaterno', 'Fotoperfil', 'CargoID', 'TipocontratoID')
                ->with([
                    'cargo:CargoID,Nombrecargo',
                    'contrato:TipocontratoID,Nombrecontrato'
                ])
                ->orderBy('Apellidopaterno');

            if (!$esSuperAdmin) {
                if ($permisos['nivel'] <= 2) { // Visión por Facultad
                    $query->whereHas('materias.carrera', fn($q) => $q->whereIn('FacultadID', $permisos['facultades']));
                } else { // Visión por Carrera
                    $query->whereHas('materias', fn($q) => $q->whereIn('CarreraID', $permisos['carreras']));
                }
            }
            return $query->get();
        });

        // ---------------------------------------------------------------------
        // 3. RECUPERACIÓN DE MATERIAS (V3.1 Columns)
        // ---------------------------------------------------------------------
        $cacheKeyMaterias = $esSuperAdmin ? 'materias_carga_all' : 'materias_carga_user_' . $user->UsuarioID;

        $materias = Cache::remember($cacheKeyMaterias, 3600, function() use ($esSuperAdmin, $permisos) {
            $query = Materia::select('MateriaID', 'Nombremateria', 'CarreraID', 'Sigla')
                ->with('carrera:CarreraID,Nombrecarrera')
                ->orderBy('Nombremateria');

            if (!$esSuperAdmin) {
                if ($permisos['nivel'] <= 2) {
                    $query->whereHas('carrera', fn($q) => $q->whereIn('FacultadID', $permisos['facultades']));
                } else {
                    $query->whereIn('CarreraID', $permisos['carreras']);
                }
            }
            return $query->get();
        });

        $docente_id = $request->get('docente_id');
        $materia_id = $request->get('MateriaID'); 

        return view('carga.create', compact('docentes', 'materias', 'docente_id', 'materia_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'PersonalID' => 'required|exists:Personal,PersonalID',
            'MateriaID'  => 'required|exists:Materia,MateriaID',
            'Gestion'    => 'required|integer|min:2020|max:2030',
            'Periodo'    => 'required|string', 
        ]);

        try {
            // 1. Verificación de Duplicados en tabla pivote V3.1 [cite: 260]
            $existe = DB::table('Personalmateria')
                ->where('PersonalID', $request->PersonalID)
                ->where('MateriaID', $request->MateriaID)
                ->where('Gestion', $request->Gestion)
                ->where('Periodo', $request->Periodo)
                ->exists();

            if ($existe) {
                return back()->with('error', 'El docente ya tiene asignada esta materia en este periodo.')->withInput();
            }

            // 2. Inserción en tabla pivote [cite: 262]
            DB::table('Personalmateria')->insert([
                'PersonalID' => $request->PersonalID,
                'MateriaID'  => $request->MateriaID,
                'Gestion'    => $request->Gestion,
                'Periodo'    => $request->Periodo,
            ]);

            // 3. Invalidación de Caché
            Cache::forget('dashboard_stats_global');
            Cache::forget('dashboard_stats_user_' . Auth::id());

            return redirect()->route('personal.show', $request->PersonalID)
                ->with('success', 'Carga académica asignada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error técnico: ' . $e->getMessage())->withInput();
        }
    }
}