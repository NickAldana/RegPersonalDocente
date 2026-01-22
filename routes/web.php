<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\FormacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema SIA (Acreditación UPDS)
|--------------------------------------------------------------------------
*/

Route::get('/test-config', function() {
    dd(config('services.azure'));
});

// =========================================================================
// 1. RUTAS PÚBLICAS (Invitados)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/', function () { 
        return redirect()->route('login'); 
    });
    
    Route::controller(AuthController::class)->group(function() {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');

        // RUTAS DE MICROSOFT AZURE (SSO)
        Route::get('/auth/azure', 'redirectToAzure')->name('login.azure');
        Route::get('/auth/azure/callback', 'handleAzureCallback');
    });
});


// =========================================================================
// 2. RUTAS PROTEGIDAS (Usuarios Autenticados)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- SESIÓN ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- DASHBOARD PRINCIPAL ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- PERFIL DEL USUARIO ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/perfil/edit', 'edit')->name('profile.edit');
        Route::put('/perfil/update', 'update')->name('profile.update');
    });

    // --- GESTIÓN DE PERSONAL (Privilegiado) ---
    Route::controller(PersonalController::class)
        ->middleware('can:gestionar_personal') 
        ->group(function () {
            Route::get('/personal', 'index')->name('personal.index');
            Route::get('/personal/crear', 'create')->name('personal.create'); 
            Route::post('/personal', 'store')->name('personal.store');
            
            Route::post('/personal/{id}/status', 'toggleStatus')->name('personal.status');
            Route::post('/personal/{id}/crear-usuario', 'createUser')->name('personal.create_user');
            Route::post('/personal/{id}/revocar-usuario', 'revokeUser')->name('personal.revoke');
    });

    // --- VISTAS PÚBLICAS DE PERSONAL (Solo Lectura) ---
    Route::get('/personal/{id}', [PersonalController::class, 'show'])->name('personal.show');
    Route::get('/personal/{id}/imprimir', [PersonalController::class, 'printInformacion'])->name('personal.print');

    // --- CARGA ACADÉMICA ---
    Route::controller(CargaAcademicaController::class)
        ->middleware('can:asignar_carga')
        ->group(function () {
            Route::get('/carga/asignar', 'create')->name('carga.create');
            Route::post('/carga', 'store')->name('carga.store');
        });

    // --- FORMACIÓN DOCENTE ---
    Route::post('/formacion', [FormacionController::class, 'store'])->name('formacion.store');

    // =========================================================================
    // 3. ANALÍTICA Y REPORTES (POWER BI & DOCUMENTACIÓN)
    // =========================================================================
    Route::middleware('can:ver_dashboard')->group(function () {
        
        // Reporte 1: Grados Académicos
        Route::view('/analitica/acreditacion', 'reporte-bi')->name('analitica.acreditacion');

        // Reporte 2: Profesión y Contratos
        Route::view('/analitica/corporativo', 'powerbi')->name('analitica.powerbi_show');

        // [NUEVO] REPORTE DE PRESENTACIÓN (PDF ESTÁTICO)
        // Esta ruta carga la vista 'reporte.blade.php' que visualiza el PDF
        // Reporte 1: Presentación Final (El que ya tenías)
Route::get('/analitica/presentacion-final', function () {
    return view('reporte', [
        'archivo' => 'reporte_presentacion.pdf', 
        'titulo' => 'Presentación de Acreditación'
    ]);
})->name('reporte.pdf');

// Reporte 2: Infografía de Inversión (El nuevo PDF azul)
Route::get('/analitica/inversion-profesional', function () {
    return view('reporte', [
        'archivo' => 'Infografía de datos Oportunidades de Inversión Profesional Corporativo Azul (1).pdf', 
        'titulo' => 'Inversión Profesional'
    ]);
})->name('reporte.inversion');
    });

});