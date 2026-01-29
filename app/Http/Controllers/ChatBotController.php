<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Para guardar el modelo y no preguntar siempre
use Illuminate\Http\Client\Response;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        $pregunta = $request->input('message', '');
        
        if (empty($pregunta)) {
            return response()->json(['reply' => "No escuché bien. 👂"]);
        }

        try {
            $usuario = Auth::user();
            $emailUsuario = $usuario ? $usuario->Email : 'Invitado';
            
            // PASO 0: AUTO-DETECTAR MODELO (La magia que soluciona el 404)
            $modelo = $this->obtenerModeloDisponible();

            // 1. GENERAR SQL
            $sqlQuery = $this->conectarConGemini($pregunta, $emailUsuario, 'SQL', null, $modelo);

            if (!$sqlQuery || str_starts_with(strtoupper($sqlQuery), 'ERROR')) {
                return response()->json(['reply' => $this->conectarConGemini($pregunta, $emailUsuario, 'CASUAL', null, $modelo)]);
            }

            // 2. EJECUTAR SQL
            $resultados = DB::select($sqlQuery);

            // 3. REDACTAR RESPUESTA
            $respuestaFinal = $this->conectarConGemini($pregunta, $emailUsuario, 'REDACTAR', $resultados, $modelo);

            return response()->json(['reply' => $respuestaFinal]);

        } catch (\Exception $e) {
            return response()->json(['reply' => "🔴 <strong>ERROR CRÍTICO:</strong><br>" . $e->getMessage()]);
        }
    }

    /**
     * Busca automáticamente qué modelo funciona con tu API Key.
     */
    private function obtenerModeloDisponible()
    {
        // Guardamos el nombre del modelo en caché por 1 día para no preguntar a cada rato
        return Cache::remember('gemini_model_name', 86400, function () {
            $apiKey = env('GEMINI_API_KEY');
            $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";

            /** @var Response $response */
            $response = Http::withoutVerifying()->get($url);

            if ($response->failed()) {
                throw new \Exception("Tu API Key no funciona o no tiene acceso a internet. Error: " . $response->body());
            }

            $data = $response->json();
            
            // Buscamos el primer modelo que sirva para 'generateContent'
            foreach ($data['models'] as $model) {
                if (isset($model['supportedGenerationMethods']) && in_array('generateContent', $model['supportedGenerationMethods'])) {
                    // Preferencia: Si encontramos uno "flash", lo usamos primero (es más rápido)
                    if (str_contains($model['name'], 'flash')) {
                        return str_replace('models/', '', $model['name']);
                    }
                }
            }

            // Si no hay flash, devolvemos el primero que encontremos (ej: gemini-pro)
            foreach ($data['models'] as $model) {
                if (in_array('generateContent', $model['supportedGenerationMethods'])) {
                    return str_replace('models/', '', $model['name']);
                }
            }

            throw new \Exception("No se encontraron modelos de chat disponibles para tu API Key.");
        });
    }

    private function conectarConGemini($pregunta, $emailUsuario, $tipo, $datos = null, $modelo)
    {
        $apiKey = env('GEMINI_API_KEY');
        // Usamos el modelo que encontramos automáticamente
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key={$apiKey}";

        $prompt = "";

        if ($tipo === 'SQL') {
            $prompt = "
                Eres un experto SQL Server. 
                USUARIO: {$emailUsuario}.
                
                TABLAS:
                - Personal(IdPersonal, NombreCompleto, ApellidoPaterno, Activo, CorreoElectronico, IdCargo, IdTipoContrato)
                - Cargo(IdCargo, NombreCargo) 
                - TipoContrato(IdTipoContrato, NombreContrato)
                - Carrera(IdCarrera, NombreCarrera)
                - Materia(IdMateria, NombreMateria)
                - PersonalMateria(IdPersonal, IdMateria, Gestion)
                - Formacion(IdPersonal, IdGradoAcademico, TituloObtenido)
                - GradoAcademico(IdGradoAcademico, NombreGrado) (5=PhD, 4=Maestría)
                - Indicadores(NombreIndicador, ValorMinimo, ValorOptimo)

                REGLAS:
                1. SOLO código SQL (SELECT). Sin markdown.
                2. Si preguntan 'sobre mí', busca Personal WHERE CorreoElectronico = '{$emailUsuario}'.
                3. 'Docentes sin carga': SELECT * FROM Personal WHERE IdPersonal NOT IN (SELECT IdPersonal FROM PersonalMateria).
                4. Si no hay datos que consultar, responde: ERROR
                
                PREGUNTA: {$pregunta}
            ";
        } elseif ($tipo === 'REDACTAR') {
            $jsonDatos = json_encode(array_slice($datos, 0, 15));
            $prompt = "Actúa como AgileBot. Pregunta: '{$pregunta}'. Datos: {$jsonDatos}. Responde amable con HTML.";
        } else {
            $prompt = "Usuario dice: '{$pregunta}'. Responde amable.";
        }

        /** @var Response $response */
        $response = Http::withoutVerifying()->post($url, [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        if ($response->failed()) {
            // Si falla, borramos la caché para buscar el modelo de nuevo la próxima vez
            Cache::forget('gemini_model_name');
            throw new \Exception("Google API Error con modelo {$modelo}: " . $response->body());
        }

        $texto = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
        return trim(str_replace(['```sql', '```', 'sql'], '', $texto));
    }
}