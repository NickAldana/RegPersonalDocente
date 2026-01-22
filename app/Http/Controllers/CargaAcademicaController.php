<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
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

        $queryDocentes = Personal::where('Activo', 1)->orderBy('ApellidoPaterno');
        $queryMaterias = Materia::orderBy('NombreMateria');

        if (!$esSuperAdmin) {
            $miPersonal = $user->personal;
            if ($miPersonal) {
                $misCarrerasIds = $miPersonal->carreras->pluck('IdCarrera')->toArray();

                if (empty($misCarrerasIds)) {
                    return redirect()->route('dashboard')->with('error', 'No tienes una carrera asignada.');
                }

                $miNivel = $user->cargo()->nivel_jerarquico ?? 0;

                if ($miNivel >= 80) { // DECANO
                    $misFacultadesIds = Carrera::whereIn('IdCarrera', $misCarrerasIds)->pluck('IdFacultad')->unique();
                    $queryDocentes->whereHas('carreras', fn($q) => $q->whereIn('IdFacultad', $misFacultadesIds));
                    $queryMaterias->whereHas('carrera', fn($q) => $q->whereIn('IdFacultad', $misFacultadesIds));
                } else { // JEFE DE CARRERA
                    $queryDocentes->whereHas('carreras', fn($q) => $q->whereIn('Carrera.IdCarrera', $misCarrerasIds));
                    $queryMaterias->whereIn('IdCarrera', $misCarrerasIds);
                }
            } else {
                abort(403, 'No tienes perfil de personal asociado.');
            }
        }

        $docentes = $queryDocentes->get();
        $materias = $queryMaterias->get();
        $docente_id = $request->get('docente_id');
        $materia_id = $request->get('IdMateria'); 

        return view('carga.create', compact('docentes', 'materias', 'docente_id', 'materia_id'));
    }

    public function store(Request $request)
    {
        $anioActual = date('Y');
        $request->validate([
            'IdPersonal' => 'required|exists:Personal,IdPersonal',
            'IdMateria'  => 'required|exists:Materia,IdMateria',
            'Gestion'    => 'required|integer|min:' . $anioActual . '|max:' . ($anioActual + 1),
            'Periodo'    => 'required|string', 
        ]);

        try {
            $existe = DB::table('PersonalMateria')
                ->where('IdPersonal', $request->IdPersonal)
                ->where('IdMateria', $request->IdMateria)
                ->where('Gestion', $request->Gestion)
                ->where('Periodo', $request->Periodo)
                ->exists();

            if ($existe) {
                return back()->with('error', 'El docente ya tiene asignada esta materia en ese periodo.')->withInput();
            }

            DB::table('PersonalMateria')->insert([
                'IdPersonal' => $request->IdPersonal,
                'IdMateria'  => $request->IdMateria,
                'Gestion'    => $request->Gestion,
                'Periodo'    => $request->Periodo,
            ]);

            return redirect()->route('personal.show', $request->IdPersonal)->with('success', 'Carga académica asignada.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        }
    }
}