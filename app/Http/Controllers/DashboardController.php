<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\Materia;
use App\Models\Usuario;
use App\Models\Proyectoinvestigacion; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var Usuario $user */
        $user = Auth::user();
        $gestionActual = 2026;

        // 1. LÓGICA PARA ADMINISTRADORES / GESTORES
        if ($user->canDo('gestionar_personal')) {
            
            $cacheKey = $user->canDo('acceso_total') 
                ? 'dashboard_stats_global' 
                : 'dashboard_stats_user_' . $user->UsuarioID;

            $stats = Cache::remember($cacheKey, 60, function () use ($user, $gestionActual) {
                $query = Personal::query();

                // SEGURIDAD HORIZONTAL INTEGRADA:
                if (!$user->canDo('acceso_total')) {
                    // Obtenemos las carreras vinculadas al personal del usuario actual
                    $misCarrerasIds = $user->personal ? $user->personal->materias->pluck('CarreraID')->unique()->toArray() : [];
                    
                    if (!empty($misCarrerasIds)) {
                        $query->whereHas('materias', fn($q) => $q->whereIn('CarreraID', $misCarrerasIds));
                    } else {
                        // Si es gestor pero no tiene carreras asignadas, bloqueamos resultados
                        $query->whereRaw('1 = 0');
                    }
                }

                $baseQuery = clone $query;

                return [
                    'totalDocentes'     => $baseQuery->count(),
                    'activos'           => (clone $baseQuery)->where('Activo', 1)->count(),
                    'inactivos'         => (clone $baseQuery)->where('Activo', 0)->count(),
                    'pendientesPDF'     => (clone $baseQuery)->whereDoesntHave('formaciones', fn($q) => 
                                            $q->whereNotNull('RutaArchivo')
                                           )->count(),
                    'totalMaterias'     => Materia::count(),
                    'proyectosActivos'  => Proyectoinvestigacion::where('Estado', 'En Ejecución')->count(),
                    'porContrato'       => (clone $baseQuery)
                        ->select('TipocontratoID', DB::raw('count(*) as total'))
                        ->groupBy('TipocontratoID')
                        ->with('contrato:TipocontratoID,Nombrecontrato')
                        ->get(),
                ];
            });

            return view('dashboard', $stats);
        }

        // 2. LÓGICA PARA DOCENTES (Mi Carga Académica)
        $miCarga = $user->personal 
            ? $user->personal->materias()->wherePivot('Gestion', $gestionActual)->count() 
            : 0;
        
        return view('dashboard', [
            'misMaterias' => $miCarga,
            'isDocente'   => true,
        ]);
    }
}