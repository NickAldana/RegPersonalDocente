@extends('layouts.app')

@section('content')
<div class="px-4 py-5 my-5 text-center bg-white rounded-3 shadow-sm border border-0">
    <div class="mb-4 text-indigo">
        <i class="bi bi-building-check" style="font-size: 4rem;"></i>
    </div>
    <h1 class="display-5 fw-bold text-dark">Bienvenido al Sistema</h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4 text-muted">
            Hola, <strong>{{ Auth::user()->personal->NombreCompleto }}</strong>. <br>
            Tienes acceso con perfil de <span class="badge bg-indigo">{{ Auth::user()->cargo()->NombreCargo }}</span>.
        </p>
        
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            @if(Auth::user()->canDo('gestionar_personal'))
                <a href="{{ route('personal.index') }}" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="bi bi-search"></i> Consultar Directorio
                </a>
            @endif
            
            <a href="{{ route('personal.show', Auth::user()->IdPersonal) }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-person-badge"></i> Ver Mi Kardex
            </a>
        </div>
    </div>
</div>

{{-- Solo visible para ADMINS o JEFES --}}
@if(Auth::user()->canDo('ver_dashboard'))
<div class="row mt-5">
    <div class="col-md-4">
        <div class="card p-3 text-center h-100 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-indigo"><i class="bi bi-people fs-1 d-block mb-2"></i>Personal</h5>
                <p class="card-text small text-muted">Gestión de docentes, administrativos y contratos.</p>
                <a href="{{ route('personal.index') }}" class="btn btn-sm btn-light stretched-link">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center h-100 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-indigo"><i class="bi bi-calendar3 fs-1 d-block mb-2"></i>Carga Académica</h5>
                <p class="card-text small text-muted">Asignación de materias y control de horarios.</p>
                <a href="{{ route('carga.create') }}" class="btn btn-sm btn-light stretched-link">Ir al módulo</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center h-100 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-indigo"><i class="bi bi-bar-chart-fill fs-1 d-block mb-2"></i>Indicadores</h5>
                <p class="card-text small text-muted">Reportes de acreditación y estadísticas en tiempo real.</p>
                <button class="btn btn-sm btn-light disabled">Próximamente</button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection