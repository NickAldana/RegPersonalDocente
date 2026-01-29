<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\FormacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatBotController; // <--- IMPORTANTE: Agregamos esto

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema SIA (Acreditación UPDS)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. PORTADA Y REDIRECCIÓN INICIAL
// =========================================================================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');


// =========================================================================
// 2. RUTAS DE AUTENTICACIÓN (Invitados)
// =========================================================================
Route::middleware('guest')->group(function () {
    
    Route::controller(AuthController::class)->group(function() {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');

        // RUTAS OPCIONALES DE MICROSOFT AZURE (SSO)
        Route::get('/auth/azure', 'redirectToAzure')->name('login.azure');
        Route::get('/auth/azure/callback', 'handleAzureCallback');
    });
});


// =========================================================================
// 3. RUTAS PROTEGIDAS (Usuarios Autenticados)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- SESIÓN Y SALIDA ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- DASHBOARD PRINCIPAL ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- CHATBOT IA (RUTA AGREGADA) ---
    // Esta es la ruta que te faltaba y causaba el error
    Route::post('/chat-ai', [ChatBotController::class, 'chat'])->name('chat.ai');

    // --- PERFIL DEL USUARIO (Edición Propia) ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/perfil/edit', 'edit')->name('profile.edit');
        Route::put('/perfil/update', 'update')->name('profile.update');
    });

    // --- GESTIÓN DE PERSONAL (Solo Personal Autorizado) ---
    Route::controller(PersonalController::class)
        ->middleware('can:gestionar_personal') 
        ->group(function () {
            // Listado y Creación
            Route::get('/personal', 'index')->name('personal.index');
            Route::get('/personal/crear', 'create')->name('personal.create'); 
            Route::post('/personal', 'store')->name('personal.store');
            
            // Acciones de Estado y Cuentas
            Route::post('/personal/{id}/toggle', 'toggleStatus')->name('personal.toggle');
            Route::post('/personal/{id}/crear-usuario', 'createUser')->name('personal.create_user');
            Route::post('/personal/{id}/revocar-usuario', 'revokeUser')->name('personal.revoke');
    });

    // --- VISTAS PÚBLICAS DE PERSONAL (Solo Lectura / Kardex) ---
    Route::get('/personal/{id}', [PersonalController::class, 'show'])->name('personal.show');
    Route::get('/personal/{id}/imprimir', [PersonalController::class, 'printInformacion'])->name('personal.print');


    // --- ACADÉMICO: CARGA HORARIA ---
    Route::controller(CargaAcademicaController::class)
        ->middleware('can:asignar_carga')
        ->group(function () {
            Route::get('/carga/asignar', 'create')->name('carga.create');
            Route::post('/carga', 'store')->name('carga.store');
        });


    // --- FORMACIÓN DOCENTE (Registro de Títulos) ---
    Route::post('/formacion', [FormacionController::class, 'store'])->name('formacion.store');
    
    // Actualización de PDF
    Route::post('/formacion/actualizar-pdf', [FormacionController::class, 'updatePDF'])->name('formacion.updatePDF');

    // =========================================================================
    // 4. ANALÍTICA Y REPORTES (Sincronizado con Carpeta analitica/)
    // =========================================================================
    Route::middleware('can:ver_dashboard')->group(function () {
        
        // 1. Analítico de Docentes (Power BI Embedded)
        Route::view('/analitica/acreditacion', 'analitica.acreditacion')->name('analitica.acreditacion');

        // 2. Visor de PDF Dinámico
        Route::get('/analitica/reporte/{archivo}', function ($archivo) {
            $titulo = str_replace(['_', '.pdf'], [' ', ''], $archivo);
            
            return view('analitica.visor-pdf', [
                'archivo' => $archivo,
                'titulo' => ucwords($titulo)
            ]);
        })->name('reporte.pdf')->where('archivo', '.*');

        // 3. Ruta directa para Inversión Profesional
        Route::get('/analitica/inversion-profesional', function () {
            return view('analitica.visor-pdf', [
                'archivo' => 'Infografía de datos Oportunidades de Inversión Profesional Corporativo Azul (1).pdf', 
                'titulo' => 'Inversión Profesional'
            ]);
        })->name('reporte.inversion');
    });

    // Herramienta de Diagnóstico (Opcional)
    Route::get('/test-config', function() {
        return response()->json([
            'auth_user' => Auth::user()->Email,
            'cargo' => Auth::user()->personal->cargo->NombreCargo ?? 'Sin Cargo'
        ]);
    });

});