<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $gestionActual = 2026;

        // 1. LÓGICA PARA ADMINISTRADORES / GESTORES (Dirección de Acreditación)
        if ($user->canDo('gestionar_personal')) {
            
            $cacheKey = $user->canDo('acceso_total') 
                ? 'dashboard_stats_global' 
                : 'dashboard_stats_user_' . $user->id;

            // Caché de 60 segundos para equilibrio entre velocidad y tiempo real
            $stats = Cache::remember($cacheKey, 60, function () use ($user, $gestionActual) {
                $query = Personal::query();

                // SEGURIDAD: Los contadores de personas siguen filtrados por área
                if (!$user->canDo('acceso_total')) {
                    $misCarrerasIds = $user->personal->carreras->pluck('IdCarrera')->toArray();
                    $query->whereHas('carreras', fn($q) => $q->whereIn('Carrera.IdCarrera', $misCarrerasIds));
                }

                $baseQuery = clone $query;

           return [
    'totalDocentes'     => $baseQuery->count(),
    'activos'           => (clone $baseQuery)->where('Activo', 1)->count(),
    'inactivos'         => (clone $baseQuery)->where('Activo', 0)->count(),
    
    'pendientesPDF'     => (clone $baseQuery)->whereDoesntHave('formaciones', fn($q) => 
                            $q->whereNotNull('RutaArchivo')
                           )->count(),

    // CAMBIO AQUÍ: Ahora contamos todas las materias del sistema, no solo las asignadas
    'materiasAsignadas' => \App\Models\Materia::count(), 

    'porContrato'       => (clone $baseQuery)
        ->select('IdTipoContrato', DB::raw('count(*) as total'))
        ->groupBy('IdTipoContrato')
        ->with('contrato:IdTipoContrato,NombreContrato')
        ->get()
];            });

            return view('dashboard', $stats);
        }

        // 2. LÓGICA PARA DOCENTES
        $miCarga = $user->personal ? $user->personal->materias()->where('Gestion', $gestionActual)->count() : 0;
        
        return view('dashboard', [
            'misMaterias' => $miCarga,
            'isDocente'   => true
        ]);
    }
}