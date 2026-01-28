<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formacion;
use Illuminate\Support\Facades\Storage;

class FormacionController extends Controller
{
    // Función para crear un registro desde cero (Ya la tienes)
    public function store(Request $request)
    {
        $request->validate([
            'IdPersonal'        => 'required|exists:Personal,IdPersonal',
            'IdCentroFormacion' => 'required|exists:CentroFormacion,IdCentroFormacion',
            'IdGradoAcademico'  => 'required|exists:GradoAcademico,IdGradoAcademico',
            'TituloObtenido'    => 'required|string|min:3|max:300',
            'AñoEstudios'       => 'required|integer|min:1950|max:' . date('Y'),
            'ArchivoTitulo'     => 'nullable|file|mimes:pdf|max:5120' // Solo PDF para títulos
        ]);

        $duplicado = Formacion::where([
            ['IdPersonal', $request->IdPersonal],
            ['IdGradoAcademico', $request->IdGradoAcademico],
            ['TituloObtenido', $request->TituloObtenido]
        ])->exists();

        if ($duplicado) {
            return back()->with('error', 'Este grado académico ya está registrado.');
        }

        try {
            $datos = $request->all();

            if ($request->hasFile('ArchivoTitulo')) {
                $nombreArchivo = $request->IdPersonal . '_titulo_' . time() . '.' . $request->file('ArchivoTitulo')->getClientOriginalExtension();
                $ruta = $request->file('ArchivoTitulo')->storeAs('titulos', $nombreArchivo, 'public');
                $datos['RutaArchivo'] = $ruta;
            }

            Formacion::create($datos);

            return redirect()->route('personal.show', $request->IdPersonal)
                ->with('success', 'Formación académica registrada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar formación: ' . $e->getMessage());
        }
    }

    /**
     * FUNCIÓN NUEVA: ACTUALIZACIÓN RÁPIDA DE PDF
     * Se usa desde el Kardex para títulos que no tenían respaldo.
     */
    public function updatePDF(Request $request)
    {
        // 1. Validar que el archivo sea PDF y el ID de formación exista
        $request->validate([
            'IdFormacion'   => 'required|exists:Formacion,IdFormacion',
            'ArchivoTitulo' => 'required|file|mimes:pdf|max:5120'
        ]);

        try {
            $formacion = Formacion::findOrFail($request->IdFormacion);

            // 2. Mantenimiento: Si ya existe un archivo físico, lo borramos antes de reemplazarlo
            if ($formacion->RutaArchivo) {
                Storage::disk('public')->delete($formacion->RutaArchivo);
            }

            // 3. Guardar el nuevo respaldo con nombre único
            $nombreDoc = 'respaldo_' . $request->IdFormacion . '_' . time() . '.pdf';
            $ruta = $request->file('ArchivoTitulo')->storeAs('titulos', $nombreDoc, 'public');

            // 4. Actualizar solo el campo de la ruta en la base de datos
            $formacion->update([
                'RutaArchivo' => $ruta
            ]);

            return back()->with('success', 'Archivo de respaldo adjuntado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el documento: ' . $e->getMessage());
        }
    }
}