<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\FormacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestigacionController; // Tu Controlador Maestro

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema SIA (Acreditación UPDS)
|--------------------------------------------------------------------------
|
| Versión V3.4 - Unificado en InvestigacionController
|
*/

// =========================================================================
// 1. PORTADA Y ACCESO
// =========================================================================
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('welcome');
})->name('welcome');

Route::middleware('guest')->controller(AuthController::class)->group(function() {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
});

// =========================================================================
// 2. RUTAS PROTEGIDAS
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- SISTEMA ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- PERFIL ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/perfil/edit', 'edit')->name('profile.edit');
        Route::put('/perfil/update', 'update')->name('profile.update');
    });

 // --- PERSONAL (RRHH) ---
    Route::controller(PersonalController::class)->group(function () {
        
        // 1. RUTAS ESTÁTICAS (Deben ir PRIMERO para evitar conflictos con {id})
        // Ajuste: Simplifiqué la llamada al método 'report' ya que estamos dentro del grupo del controlador
        Route::get('/personal/reporte-general', 'report')->name('personal.report'); 
        
        Route::get('/personal', 'index')->name('personal.index');
        Route::get('/personal/crear', 'create')->name('personal.create'); 
        Route::post('/personal', 'store')->name('personal.store');

        // 2. RUTAS DINÁMICAS (Requieren ID)
        Route::get('/personal/{id}/editar', 'edit')->name('personal.edit');
        Route::put('/personal/{id}', 'update')->name('personal.update');
        
        // Acciones personalizadas
        Route::post('/personal/{id}/toggle', 'toggleStatus')->name('personal.toggle');
        Route::get('/personal/{id}/imprimir', 'printInformacion')->name('personal.print');
        
        // El 'show' suele ir al final de los GET con ID para no capturar otras rutas por error
        Route::get('/personal/{id}', 'show')->name('personal.show');
    });

    // --- CARGA Y FORMACIÓN ---
    Route::controller(CargaAcademicaController::class)->group(function () {
        Route::get('/carga/asignar', 'create')->name('carga.create');
        Route::post('/carga', 'store')->name('carga.store');
    });
    Route::post('/formacion', [FormacionController::class, 'store'])->name('formacion.store');
    Route::post('/formacion/actualizar-pdf', [FormacionController::class, 'updatePDF'])->name('formacion.updatePDF');


    // =========================================================================
    // 3. MÓDULO DE INVESTIGACIÓN (Todo en InvestigacionController)
    // =========================================================================
   Route::controller(InvestigacionController::class)->group(function () {
        
        // --- VISTAS PRINCIPALES ---
        Route::get('/investigacion', 'index')->name('investigacion.index');
        Route::get('/publicaciones', 'indexPublicaciones')->name('publicaciones.index');

        // --- GESTIÓN DE PROYECTOS (Roles = Texto) ---
        
        // 1. Formulario de Alta
        Route::get('/investigacion/proyecto/crear', 'createProyecto')->name('investigacion.createProyecto'); 

        // 2. Guardar (Action del Formulario)
        Route::post('/investigacion/proyecto', 'storeProyecto')->name('investigacion.storeProyecto');

        // 3. Ficha Técnica / Detalle (NUEVA RUTA)
        Route::get('/investigacion/proyecto/{id}', 'showProyecto')->name('investigacion.showProyecto');
        
        // 4. Edición
        Route::get('/investigacion/proyecto/{id}/editar', 'editProyecto')->name('investigacion.editProyecto');
        Route::put('/investigacion/proyecto/{id}', 'updateProyecto')->name('investigacion.updateProyecto');
        
        // Reporte PDF de Proyectos
        Route::get('/investigacion/reporte-proyectos', 'reporteProyectosPDF')->name('investigacion.pdf_proyectos');

        // --- GESTIÓN DE PUBLICACIONES ---
        Route::get('/publicaciones/crear', 'createPublicacion')->name('publicaciones.create');
        Route::post('/publicaciones', 'storePublicacion')->name('publicaciones.store'); 
        Route::get('/publicaciones/{id}/editar', 'editPublicacion')->name('publicaciones.edit');
        Route::put('/publicaciones/{id}', 'updatePublicacion')->name('publicaciones.update');
        
        // Reporte PDF de Publicaciones
        Route::get('/publicaciones/reporte-general', 'reportePublicacionesPDF')->name('publicaciones.pdf');
    });
    // =========================================================================
    // 4. UTILIDADES Y ANALÍTICA
    // =========================================================================
     Route::view('/analitica/acreditacion', 'analitica.acreditacion')->name('analitica.acreditacion');

    

    Route::get('/analitica/reporte/{archivo}', function ($archivo) {

        return view('analitica.visor-pdf', ['archivo' => $archivo, 'titulo' => 'Documento']);

    })->name('reporte.pdf')->where('archivo', '.*');


    Route::get('/test-config', function() {

        return Auth::user(); // Debug rápido

    });
});