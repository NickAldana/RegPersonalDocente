<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formacion;
use Illuminate\Support\Facades\Storage;

class FormacionController extends Controller
{
    /**
     * Registra un nuevo título profesional (Formación Académica).
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN CON COLUMNAS V3.1
        $request->validate([
            'PersonalID'        => 'required|exists:Personal,PersonalID',
            'CentroformacionID' => 'required|exists:Centroformacion,CentroformacionID',
            'GradoacademicoID'  => 'required|exists:Gradoacademico,GradoacademicoID',
            'Tituloobtenido'    => 'required|string|min:3|max:300',
            'Añosestudios'      => 'required|integer|min:1950|max:' . date('Y'),
            'ArchivoTitulo'     => 'nullable|file|mimes:pdf|max:5120' // Máx 5MB
        ]);

        // 2. VERIFICACIÓN DE DUPLICADOS (Nombres V3.1)
        $duplicado = Formacion::where([
            ['PersonalID', $request->PersonalID],
            ['GradoacademicoID', $request->GradoacademicoID],
            ['Tituloobtenido', $request->Tituloobtenido]
        ])->exists();

        if ($duplicado) {
            return back()->with('error', 'Este grado académico ya está registrado en la ficha del docente.');
        }

        try {
            $datos = $request->all();

            // 3. GESTIÓN DE ARCHIVO PDF
            if ($request->hasFile('ArchivoTitulo')) {
                $nombreArchivo = $request->PersonalID . '_titulo_' . time() . '.' . $request->file('ArchivoTitulo')->getClientOriginalExtension();
                $ruta = $request->file('ArchivoTitulo')->storeAs('titulos', $nombreArchivo, 'public');
                $datos['RutaArchivo'] = $ruta;
            }

            // 4. CREACIÓN EN SQL SERVER
            Formacion::create($datos);

            return redirect()->route('personal.show', $request->PersonalID)
                ->with('success', 'Nueva formación académica registrada.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar formación: ' . $e->getMessage());
        }
    }

    /**
     * ACTUALIZACIÓN RÁPIDA DE PDF
     * Permite adjuntar el respaldo digital a un título ya registrado.
     */
    public function updatePDF(Request $request)
    {
        $request->validate([
            'FormacionID'   => 'required|exists:Formacion,FormacionID',
            'ArchivoTitulo' => 'required|file|mimes:pdf|max:5120'
        ]);

        try {
            $formacion = Formacion::findOrFail($request->FormacionID);

            if ($formacion->RutaArchivo && Storage::disk('public')->exists($formacion->RutaArchivo)) {
                Storage::disk('public')->delete($formacion->RutaArchivo);
            }

            $nombreDoc = 'respaldo_form_' . $request->FormacionID . '_' . time() . '.pdf';
            $ruta = $request->file('ArchivoTitulo')->storeAs('titulos', $nombreDoc, 'public');

            $formacion->update([
                'RutaArchivo' => $ruta
            ]);

            return back()->with('success', 'Respaldo digital actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error técnico al procesar el PDF: ' . $e->getMessage());
        }
    }
} 