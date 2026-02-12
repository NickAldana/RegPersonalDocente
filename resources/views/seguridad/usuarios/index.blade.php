@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-md-5">
    
    {{-- 1. HEADER INSTITUCIONAL --}}
    <div class="row mb-5 align-items-end">
        <div class="col-md-7">
            <h6 class="text-upds-gold fw-bold text-uppercase mb-1 tracking-widest" style="font-size: 0.75rem;">
                Seguridad de la Información <span class="mx-2 text-slate-300">|</span> Acreditación V4.0
            </h6>
            <h2 class="display-6 fw-black text-upds-blue mb-0">Gestión de Cuentas</h2>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <form action="{{ route('usuarios.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-search-wrapper flex-grow-1 shadow-sm border">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control border-0" 
                           placeholder="Buscar por nombre, usuario o correo..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-upds-blue fw-bold px-4 rounded-3 shadow-sm">Filtrar</button>
            </form>
        </div>
    </div>

    {{-- 2. DASHBOARD DE ESTADOS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-4 border-upds-blue">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-blue-50 text-upds-blue p-3 rounded-4 me-3">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0 text-slate-800">{{ $usuarios->total() }}</h4>
                        <small class="text-slate-500 fw-bold text-uppercase" style="font-size: 0.65rem;">Usuarios en Sistema</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TABLA DE NIVEL EJECUTIVO --}}
    <div class="card border-0 shadow-xl rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead>
                    <tr class="bg-slate-50 border-bottom">
                        <th class="ps-4 py-4 text-upds-blue font-black small text-uppercase ls-1">Identidad de Acceso</th>
                        <th class="py-4 text-upds-blue font-black small text-uppercase ls-1">Personal / Nivel</th>
                        <th class="py-4 text-upds-blue font-black small text-uppercase ls-1 text-center">Estado</th>
                        <th class="py-4 text-upds-blue font-black small text-uppercase ls-1 text-center">Antigüedad</th>
                        <th class="text-end pe-4 py-4 text-upds-blue font-black small text-uppercase ls-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                    <tr class="hover-row transition-all border-bottom border-slate-50">
                        {{-- Columna 1: Usuario --}}
                        <td class="ps-4 py-4">
                            <div class="d-flex align-items-center">
                                <div class="initial-avatar shadow-sm me-3 bg-upds-blue text-white fw-bold border border-white">
                                    {{ substr($user->NombreUsuario, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-black text-slate-800 mb-0" style="font-size: 0.9rem;">{{ $user->NombreUsuario }}</div>
                                    <div class="text-slate-400 small d-flex align-items-center">
                                        <i class="bi bi-envelope-at me-1"></i>
                                        {{ $user->Correo }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Columna 2: Personal + Seniority Badge --}}
                       <td>
    @if($user->personal)
        <div class="d-flex flex-column gap-1">
            <span class="fw-bold text-slate-700 small">{{ $user->personal->Nombrecompleto }}</span>
            <div class="d-flex align-items-center gap-2">
                {{-- Etiqueta de Cargo --}}
                <span class="text-upds-blue fw-medium" style="font-size: 0.75rem;">
                    <i class="bi bi-patch-check-fill text-info me-1"></i>
                    {{ $user->personal->cargo->Nombrecargo ?? 'Docente' }}
                </span>

                {{-- MEDALLA DE ANTIGÜEDAD (Accessor del Modelo) --}}
                <span class="badge border {{ $user->personal->seniority_color }}" 
                      style="font-size: 0.65rem; padding: 2px 8px; border-radius: 4px;">
                    {{ $user->personal->seniority_label }}
                </span>
            </div>
        </div>
    @else
        <span class="badge bg-slate-50 text-slate-400 fw-normal border italic px-3 py-1 rounded-pill">Sin perfil</span>
    @endif
</td>

                        {{-- Columna 3: Estado --}}
                        <td class="text-center">
                            @if($user->Activo)
                                <div class="status-pill active shadow-sm">
                                    <span class="pulse"></span> Activo
                                </div>
                            @else
                                <div class="status-pill blocked">
                                    <span class="dot"></span> Bloqueado
                                </div>
                            @endif
                        </td>

                        {{-- Columna 4: Fecha --}}
                        <td class="text-center">
    <div class="text-slate-500 fw-bold small">
        <i class="bi bi-calendar-check me-1"></i>
        {{ $user->Creacionfecha ? $user->Creacionfecha->format('d/m/Y') : 'N/A' }}
    </div>
</td>

                        {{-- Columna 5: Botones --}}
                        <td class="text-end pe-4">
                            <div class="btn-group rounded-3 shadow-sm border p-1 bg-white">
                                <a href="{{ route('usuarios.edit', $user->UsuarioID) }}" 
                                   class="btn btn-action-icon" title="Editar">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </a>
                                <form action="{{ route('usuarios.reset', $user->UsuarioID) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-action-icon" title="Resetear Clave"
                                            onclick="return confirm('¿Restablecer contraseña al CI?')">
                                        <i class="bi bi-shield-lock text-warning"></i>
                                    </button>
                                </form>
                                <form action="{{ route('usuarios.toggle', $user->UsuarioID) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-action-icon" 
                                            title="{{ $user->Activo ? 'Bloquear' : 'Activar' }}">
                                        <i class="bi {{ $user->Activo ? 'bi-lock text-danger' : 'bi-unlock-fill text-success' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-slate-400">No hay registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($usuarios->hasPages())
        <div class="card-footer bg-white border-top py-4 px-4">
            {{ $usuarios->links('vendor.pagination.bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --slate-800: #1e293b;
    }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .text-upds-blue { color: var(--upds-blue) !important; }
    .btn-upds-blue { background-color: var(--upds-blue); color: white; }
    .fw-black { font-weight: 900; }
    .ls-1 { letter-spacing: 0.5px; }
    
    .input-search-wrapper {
        background: white; border-radius: 10px; display: flex; align-items: center; padding: 0 15px;
    }
    .initial-avatar { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    
    .status-pill { display: inline-flex; align-items: center; padding: 5px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    .status-pill.active { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-pill.blocked { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    
    .status-pill .pulse { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; margin-right: 7px; animation: pulse-green 2s infinite; }
    @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
    
    .btn-action-icon { padding: 6px 12px; border: none; background: transparent; transition: 0.2s; }
    .btn-action-icon:hover { background: #f1f5f9; }
    .hover-row:hover { background-color: #f8fafc !important; }
</style>
@endsection