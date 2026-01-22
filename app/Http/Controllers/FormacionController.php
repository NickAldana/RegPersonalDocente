<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formacion;
use Illuminate\Support\Facades\Storage;

class FormacionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            'IdPersonal'        => 'required|exists:Personal,IdPersonal',
            'IdCentroFormacion' => 'required|exists:CentroFormacion,IdCentroFormacion',
            'IdGradoAcademico'  => 'required|exists:GradoAcademico,IdGradoAcademico',
            'TituloObtenido'    => 'required|string|min:3|max:300',
            'AñoEstudios'       => 'required|integer|min:1950|max:' . date('Y'),
            'ArchivoTitulo'     => 'nullable|file|mimes:pdf,jpg,png|max:5120' // Máx 5MB
        ]);

        // 2. Validación de duplicados lógicos
        $duplicado = Formacion::where([
            ['IdPersonal', $request->IdPersonal],
            ['IdGradoAcademico', $request->IdGradoAcademico],
            ['TituloObtenido', $request->TituloObtenido]
        ])->exists();

        if ($duplicado) {
            return back()->with('error', 'Este grado académico ya está registrado para este docente.');
        }

        try {
            $datos = $request->all();

            // 3. Subida de Archivo (Si existe en el request)
            if ($request->hasFile('ArchivoTitulo')) {
                $nombreArchivo = $request->IdPersonal . '_titulo_' . time() . '.' . $request->file('ArchivoTitulo')->getClientOriginalExtension();
                $ruta = $request->file('ArchivoTitulo')->storeAs('titulos', $nombreArchivo, 'public');
                $datos['RutaArchivo'] = $ruta; // Guardamos la ruta en la BD
            }

            Formacion::create($datos);

            return redirect()->route('personal.show', $request->IdPersonal)
                ->with('success', 'Formación académica actualizada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar formación: ' . $e->getMessage());
        }
    }
}