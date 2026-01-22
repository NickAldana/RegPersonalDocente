@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- 1. ALERTA DE SEGURIDAD --}}
    @if(Auth::user()->personal && empty(Auth::user()->personal->FotoPerfil))
        <div class="alert bg-white border-l-4 border-yellow-400 shadow-sm rounded-3 mb-4 d-flex align-items-center p-4" role="alert" style="border-left: 5px solid var(--upds-gold);">
            <div class="rounded-circle bg-yellow-50 text-yellow-600 p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                <i class="bi bi-shield-exclamation fs-4 text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-1">Acción Requerida: Seguridad de la Cuenta</h6>
                <p class="small text-muted mb-0">Por razones de seguridad, <strong>actualice su contraseña por defecto</strong> y su <strong>foto de perfil</strong> ingresando a <a href="{{ route('profile.edit') }}" class="fw-bold text-decoration-none" style="color: var(--upds-blue);">Configuración de Usuario</a>.</p>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SECCIÓN A: BIENVENIDA EJECUTIVA --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="sia-card border-0 p-4 d-flex align-items-center justify-content-between position-relative overflow-hidden">
                <div class="position-absolute end-0 top-0 h-100 w-25 bg-gradient-to-l from-gray-50 to-transparent d-none d-md-block" style="background: linear-gradient(90deg, transparent, #f8fafc);"></div>
                
                <div class="position-relative z-10">
                    <h2 class="fw-black text-upds-blue mb-1 ls-tight">
                        Hola, {{ Auth::user()->personal ? explode(' ', Auth::user()->personal->NombreCompleto)[0] : 'Colega' }}
                    </h2>
                    <p class="text-secondary mb-0 fw-medium">
                        Bienvenido al SIA. Usted ha ingresado como: 
                        <span class="badge bg-upds-gold text-upds-blue fw-bold px-3 py-2 rounded-pill ms-2 shadow-sm">
                            {{ Auth::user()->cargo() ? Auth::user()->cargo()->NombreCargo : 'Administrador' }}
                        </span>
                    </p>
                </div>
                
                <div class="d-none d-md-block opacity-10 position-relative z-0">
                    <i class="bi bi-mortarboard-fill text-upds-blue" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN PARA ADMINISTRADORES --}}
    @can('gestionar_personal')
        
        <h6 class="text-xs fw-bold text-muted uppercase tracking-wider mb-3 ps-1">Métricas en Tiempo Real &bull; Gestión 2026</h6>

        {{-- SECCIÓN KPI --}}
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="sia-stat-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="sia-icon-box bg-blue-50 text-upds-blue">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-dark mb-0 mt-2">{{ $totalDocentes ?? 0 }}</h3>
                    <p class="text-xs text-muted font-bold uppercase mt-1">Total Personal</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sia-stat-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="sia-icon-box bg-green-50 text-success">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-dark mb-0 mt-2">{{ $activos ?? 0 }}</h3>
                    <p class="text-xs text-muted font-bold uppercase mt-1">Docentes Activos</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sia-stat-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="sia-icon-box bg-yellow-50 text-upds-gold-dark">
                            <i class="bi bi-book-half"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-dark mb-0 mt-2">{{ $materiasAsignadas ?? 0 }}</h3>
                    <p class="text-xs text-muted font-bold uppercase mt-1">Materias Asignadas</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="sia-stat-card h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="sia-icon-box bg-red-50 text-danger">
                            <i class="bi bi-person-x-fill"></i>
                        </div>
                    </div>
                    <h3 class="fw-black text-dark mb-0 mt-2">{{ $inactivos ?? 0 }}</h3>
                    <p class="text-xs text-muted font-bold uppercase mt-1">Inactivos / Bajas</p>
                </div>
            </div>
        </div>

        {{-- SECCIÓN B: ACCESOS RÁPIDOS --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="sia-card h-100 p-4 hover-lift">
                    <div class="d-flex align-items-start">
                        <div class="sia-icon-lg bg-upds-blue text-white me-4">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-upds-blue">Gestión de Expedientes</h5>
                            <p class="text-muted small mb-4">Administración centralizada de docentes, contratos y asignación de cargos jerárquicos.</p>
                            <a href="{{ route('personal.index') }}" class="btn btn-sia-primary btn-sm rounded-pill px-4">
                                Ir al Directorio <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sia-card h-100 p-4 hover-lift">
                    <div class="d-flex align-items-start">
                        <div class="sia-icon-lg bg-white border border-2 border-dashed border-gray-300 text-gray-500 me-4">
                            <i class="bi bi-calendar-plus-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-gray-800">Asignación de Carga</h5>
                            <p class="text-muted small mb-4">Distribución de materias y horarios para la gestión académica actual.</p>
                            <a href="{{ route('carga.create') }}" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">
                                Gestionar Materias
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN POWER BI: CENTRO DE INTELIGENCIA UNIFICADO --}}
        <h6 class="text-xs fw-bold text-muted uppercase tracking-wider mb-3 ps-1 mt-5">Inteligencia de Negocios &bull; Power BI Live</h6>
        <div class="row g-4 mb-5">
            
            {{-- Tarjeta 1: Grados Académicos (Birrete Cyan) --}}
            <div class="col-md-6">
                <div class="card border-0 rounded-4 overflow-hidden text-white h-100 shadow-lg" 
                     style="background: linear-gradient(135deg, var(--upds-blue) 0%, #00509d 100%);">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                                    <i class="bi bi-mortarboard-fill fs-4" style="color: #22d3ee;"></i>
                                </div>
                                <div class="d-flex align-items-center bg-black/20 px-3 py-1 rounded-pill backdrop-blur-sm border border-white/10">
                                    <span class="pulse-red me-2"></span>
                                    <span class="small fw-bold tracking-wide">GRADOS LIVE</span>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-2">Monitor de Grados Académicos</h4>
                            <p class="small text-white/70 mb-4">Análisis de formación docente: Maestrías, Diplomados y Doctorados en tiempo real.</p>
                        </div>
                        <a href="{{ route('analitica.acreditacion') }}" class="btn btn-white text-upds-blue fw-bold rounded-pill px-4 shadow w-fit align-self-start">
                            <i class="bi bi-mortarboard-fill me-2"></i> Ver Grados
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tarjeta 2: Profesión y Contratos (Maletín Dorado) --}}
            <div class="col-md-6">
                <div class="card border-0 rounded-4 overflow-hidden h-100 shadow-lg bg-white position-relative">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: var(--upds-gold);"></div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="p-2 rounded-lg bg-warning bg-opacity-10">
                                    <i class="bi bi-briefcase-fill fs-4 text-warning"></i>
                                </div>
                                <div class="d-flex align-items-center bg-light px-3 py-1 rounded-pill border">
                                    <span class="sia-ui-pulse me-2" style="background-color: var(--upds-gold);"></span>
                                    <span class="small fw-bold tracking-wide text-dark" style="font-size: 0.7rem;">CONTRATOS LIVE</span>
                                </div>
                            </div>

                            <h4 class="fw-black text-dark mb-2">Perfil Profesional y Contratos</h4>
                            <p class="small text-muted mb-4">
                                Distribución por tipos de contrato y profesiones normalizadas del plantel docente.
                            </p>
                        </div>

                        <a href="{{ route('analitica.powerbi_show') }}" class="btn btn-dark text-white fw-bold rounded-pill px-4 shadow w-fit align-self-start transition-all hover-scale">
                            <i class="bi bi-briefcase-fill me-2 text-warning"></i> Ver Profesiones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- SECCIÓN C: MENÚ DOCENTE --}}
    @cannot('gestionar_personal')
        <div class="row g-4 mt-2">
            <div class="col-12"><h6 class="text-xs fw-bold text-muted uppercase tracking-wider mb-2">Servicios al Docente</h6></div>
            
            <div class="col-md-6">
                <div class="sia-card h-100 p-4 hover-lift d-flex align-items-center">
                    <div class="me-4 position-relative">
                        @if(Auth::user()->personal && Auth::user()->personal->FotoPerfil)
                            <img src="{{ Storage::url(Auth::user()->personal->FotoPerfil) }}" class="rounded-circle shadow-sm border border-2 border-white" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-gray-100 text-gray-400 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-badge-fill fs-1"></i>
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2"></span>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark">Mi Hoja de Vida</h5>
                        <p class="text-muted small mb-3">Gestione su formación y producción intelectual.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('personal.show', Auth::user()->IdPersonal) }}" class="btn btn-sia-primary btn-sm rounded-pill px-3">Ver Perfil</a>
                            <a href="{{ route('personal.print', Auth::user()->IdPersonal) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="sia-card h-100 p-4 border-dashed bg-gray-50 d-flex align-items-center justify-content-center text-center opacity-75">
                    <div>
                        <i class="bi bi-cone-striped fs-2 text-muted mb-2 d-block"></i>
                        <h6 class="fw-bold text-muted">Módulo de Certificados</h6>
                        <small class="text-muted">Próximamente disponible</small>
                    </div>
                </div>
            </div>
        </div>
    @endcannot

</div>

{{-- ESTILOS ESPECÍFICOS --}}
<style>
    :root {
        --upds-blue: #003566;
        --upds-blue-dark: #001d3d;
        --upds-gold: #ffc300;
        --upds-gold-dark: #e6b000;
    }

    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .text-upds-gold-dark { color: var(--upds-gold-dark) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .bg-upds-gold { background-color: var(--upds-gold) !important; }
    
    .fw-black { font-weight: 900; }
    .ls-tight { letter-spacing: -0.025em; }

    .sia-card {
        background: white;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .sia-stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .sia-stat-card:hover, .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #e2e8f0;
    }

    .sia-icon-box {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .sia-icon-lg {
        width: 60px; height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .btn-sia-primary {
        background-color: var(--upds-blue);
        color: white;
        font-weight: 700;
        border: none;
        transition: all 0.2s;
    }
    .btn-sia-primary:hover {
        background-color: var(--upds-blue-dark);
        color: white;
        transform: translateY(-2px);
    }
    .btn-white {
        background-color: white;
        color: var(--upds-blue);
        transition: all 0.2s;
    }
    .btn-white:hover {
        background-color: #f8fafc;
        transform: translateY(-2px);
    }

    .pulse-red {
        display: inline-block;
        width: 8px; height: 8px;
        background-color: #ff4d4d;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7);
        animation: pulse 2s infinite;
    }

    .sia-ui-pulse {
        display: inline-block;
        width: 8px; height: 8px;
        border-radius: 50%;
        position: relative;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(255, 77, 77, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); }
    }
    
    .bg-blue-50 { background-color: #eff6ff; }
    .bg-green-50 { background-color: #f0fdf4; }
    .bg-yellow-50 { background-color: #fefce8; }
    .bg-red-50 { background-color: #fef2f2; }
</style>
@endsection