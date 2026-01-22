<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\Carrera;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Año de gestión del proyecto SIA
        $gestionActual = 2026;

        // 1. LÓGICA PARA ADMINISTRADORES (Rector, Vicerrector, Jefes)
        if ($user->canDo('gestionar_personal')) {
            
            $query = Personal::query();

            // --- FILTRO DE SEGURIDAD (Visión de Túnel) ---
            // Si no es Super Admin (acceso_total), filtramos por sus carreras asignadas
            if (!$user->canDo('acceso_total')) {
                $misCarrerasIds = $user->personal->carreras->pluck('IdCarrera')->toArray();
                
                $query->whereHas('carreras', function($q) use ($misCarrerasIds) {
                    $q->whereIn('Carrera.IdCarrera', $misCarrerasIds);
                });
            }

            // Estadísticas filtradas
            $totalDocentes = $query->count();
            $activos = (clone $query)->where('Activo', 1)->count();
            $inactivos = (clone $query)->where('Activo', 0)->count();

            // Resumen de Carga (Materias asignadas en la gestión 2026)
            $materiasAsignadas = DB::table('PersonalMateria')
                                    ->where('Gestion', $gestionActual)
                                    ->count();

            // Gráfico: Docentes por Tipo de Contrato
            $porContrato = Personal::select('IdTipoContrato', DB::raw('count(*) as total'))
                            ->groupBy('IdTipoContrato')
                            ->with('contrato')
                            ->get();

            return view('dashboard', compact(
                'totalDocentes', 
                'activos', 
                'inactivos', 
                'porContrato', 
                'materiasAsignadas'
            ));
        }

        // 2. LÓGICA PARA DOCENTES (Vista simplificada)
        // Solo enviamos los datos básicos para que no de error la vista
        return view('dashboard');
    }
}