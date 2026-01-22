@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 px-2">
        <div class="mb-3 mb-md-0">
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                DIRECTORIO DE PERSONAL
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-people-fill me-1 text-upds-gold"></i> Gestión Académica y Administrativa
            </p>
        </div>
        @can('gestionar_personal')
            <a href="{{ route('personal.create') }}" class="btn btn-sia-primary shadow-lg hover-scale">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Expediente
            </a>
        @endcan
    </div>

    {{-- BARRA DE HERRAMIENTAS Y FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
        <div class="card-body p-4">
            <form action="{{ route('personal.index') }}" method="GET" class="row g-3 align-items-end">
                
                {{-- Buscador --}}
                <div class="col-lg-6">
                    <label class="form-label sia-label">Búsqueda Inteligente</label>
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted opacity-50"></i>
                        <input type="text" name="buscar" class="form-control sia-input-search ps-5" 
                               placeholder="Nombre, Apellido, CI o Cargo..." 
                               value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- Filtro Estado --}}
                <div class="col-lg-4">
                    <label class="form-label sia-label">Estado de Vinculación</label>
                    <select name="estado" class="form-select sia-input fw-bold text-secondary" onchange="this.form.submit()">
                        <option value="">-- Todos los Registros --</option>
                        <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>🟢 Personal Activo</option>
                        <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>🔴 Inactivos / Baja</option>
                    </select>
                </div>

                {{-- Botón Refresh --}}
                <div class="col-lg-2">
                    <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary w-100 py-2 rounded-pill fw-bold border-2">
                        <i class="bi bi-arrow-clockwise me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-gray-50 border-bottom">
                        <tr>
                            <th class="ps-4 py-4 text-secondary text-xs fw-black text-uppercase tracking-widest">Profesional</th>
                            <th class="py-4 text-secondary text-xs fw-black text-uppercase tracking-widest">Identificación</th>
                            <th class="py-4 text-secondary text-xs fw-black text-uppercase tracking-widest">Vinculación</th>
                            <th class="text-center py-4 text-secondary text-xs fw-black text-uppercase tracking-widest">Sistema</th>
                            <th class="text-center py-4 text-secondary text-xs fw-black text-uppercase tracking-widest">Estado</th>
                            <th class="text-end pe-4 py-4 text-secondary text-xs fw-black text-uppercase tracking-widest">Gestión</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0 bg-white">
                        @forelse($personal as $docente)
                        <tr class="group transition-colors hover:bg-gray-50">
                            {{-- Columna: Docente --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        @if($docente->FotoPerfil)
                                            <img src="{{ asset('storage/' . $docente->FotoPerfil) }}" class="rounded-circle border border-2 border-white shadow-sm object-cover" width="48" height="48">
                                        @else
                                            <div class="rounded-circle bg-gray-100 text-upds-blue border border-2 border-gray-200 d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px;">
                                                {{ substr($docente->NombreCompleto, 0, 1) }}{{ substr($docente->ApellidoPaterno, 0, 1) }}
                                            </div>
                                        @endif
                                        @if($docente->usuario)
                                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-upds-blue mb-0 text-truncate" style="max-width: 280px;">
                                            {{ $docente->ApellidoPaterno }} {{ $docente->ApellidoMaterno }}
                                        </div>
                                        <div class="small text-muted fw-medium">{{ $docente->NombreCompleto }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna: Documento --}}
                            <td>
                                <div class="d-flex align-items-center text-dark fw-bold">
                                    <i class="bi bi-card-heading text-secondary me-2 opacity-50"></i>
                                    {{ $docente->CI ?? 'N/A' }}
                                </div>
                            </td>

                            {{-- Columna: Cargo --}}
                            <td>
                                <div class="badge bg-blue-50 text-upds-blue border border-blue-100 mb-1">
                                    {{ $docente->cargo->NombreCargo ?? 'Sin Asignar' }}
                                </div>
                                <div class="text-xs text-muted fw-semibold">
                                    <i class="bi bi-file-text me-1"></i> {{ $docente->contrato->NombreContrato ?? 'Sin Contrato' }}
                                </div>
                            </td>
                            
                            {{-- Columna: Acceso Sistema --}}
                            <td class="text-center">
                                @if($docente->usuario)
                                    <span class="d-inline-flex align-items-center justify-content-center bg-gray-100 text-dark rounded-circle" style="width: 32px; height: 32px;" title="Acceso Habilitado">
                                        <i class="bi bi-key-fill text-upds-gold"></i>
                                    </span>
                                @else
                                    <span class="d-inline-flex align-items-center justify-content-center bg-gray-50 text-muted rounded-circle opacity-50" style="width: 32px; height: 32px;" title="Sin Usuario">
                                        <i class="bi bi-dash-lg"></i>
                                    </span>
                                @endif
                            </td>

                            {{-- Columna: Estado --}}
                            <td class="text-center">
                                @if($docente->Activo)
                                    <span class="badge bg-green-100 text-green-700 rounded-pill px-3 py-1 fw-bold border border-green-200">
                                        ACTIVO
                                    </span>
                                @else
                                    <span class="badge bg-red-50 text-red-600 rounded-pill px-3 py-1 fw-bold border border-red-100">
                                        INACTIVO
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Columna: Acciones --}}
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm border" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots text-secondary"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-xl rounded-3 py-2" style="min-width: 220px;">
                                        
                                        {{-- Ver Perfil --}}
                                        <li>
                                            <a class="dropdown-item py-2 px-3 d-flex align-items-center fw-medium text-secondary hover-bg-gray" href="{{ route('personal.show', $docente->IdPersonal) }}">
                                                <i class="bi bi-eye me-3 text-upds-blue"></i> Ver Expediente
                                            </a>
                                        </li>

                                        @can('gestionar_personal')
                                            <li><hr class="dropdown-divider my-1 border-gray-100"></li>
                                            
                                            {{-- Asignar Carga --}}
                                            <li>
                                                <a class="dropdown-item py-2 px-3 d-flex align-items-center fw-medium text-secondary hover-bg-gray" href="{{ route('carga.create', ['docente_id' => $docente->IdPersonal]) }}">
                                                    <i class="bi bi-calendar-check me-3 text-upds-gold"></i> Asignar Carga
                                                </a>
                                            </li>

                                            {{-- Gestión Usuario --}}
                                            @if(!$docente->usuario)
                                                <li>
                                                    <form action="{{ route('personal.create_user', $docente->IdPersonal) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center w-100 fw-medium text-secondary hover-bg-gray">
                                                            <i class="bi bi-person-fill-lock me-3 text-dark"></i> Crear Credenciales
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('personal.revoke', $docente->IdPersonal) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center w-100 fw-medium text-danger hover-bg-red-50" onclick="return confirm('¿Revocar acceso al sistema?')">
                                                            <i class="bi bi-slash-circle me-3"></i> Revocar Acceso
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            <li><hr class="dropdown-divider my-1 border-gray-100"></li>
                                            
                                            {{-- Estado --}}
                                            <li>
                                                <form action="{{ route('personal.status', $docente->IdPersonal) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center w-100 fw-bold small text-uppercase {{ $docente->Activo ? 'text-danger' : 'text-success' }}" onclick="return confirm('¿Cambiar estado del personal?')">
                                                        @if($docente->Activo)
                                                            <span class="d-flex align-items-center"><i class="bi bi-archive-fill me-3"></i> Dar de Baja</span>
                                                        @else
                                                            <span class="d-flex align-items-center"><i class="bi bi-arrow-counterclockwise me-3"></i> Reactivar</span>
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                    <div class="bg-gray-50 rounded-circle p-4 mb-3">
                                        <i class="bi bi-search fs-1 text-muted opacity-25"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">No se encontraron registros</h6>
                                    <p class="small text-muted mb-3">Intente ajustar los filtros de búsqueda.</p>
                                    <a href="{{ route('personal.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                                        Restablecer Filtros
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($personal->hasPages())
            <div class="card-footer bg-white border-top py-4">
                <div class="d-flex justify-content-center">
                    {{ $personal->links() }} {{-- Asegúrate de que tu Paginator use estilos Bootstrap 5 --}}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* VARIABLES LOCALES V4.0 */
    :root {
        --upds-blue: #003566;
        --upds-blue-dark: #001d3d;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        
        /* Paleta de Estados */
        --green-100: #dcfce7;
        --green-200: #bbf7d0;
        --green-700: #15803d;
        --red-50: #fef2f2;
        --red-100: #fee2e2;
        --red-600: #dc2626;
        --blue-50: #eff6ff;
        --blue-100: #dbeafe;
    }

    /* CLASES DE UTILIDAD */
    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    .bg-gray-100 { background-color: var(--gray-100) !important; }
    
    .fw-black { font-weight: 900; }
    .text-xs { font-size: 0.75rem; }
    .sia-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    /* INPUTS & BUSCADOR */
    .sia-input-search {
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        padding: 0.6rem 1rem 0.6rem 3rem;
        transition: all 0.2s;
    }
    .sia-input-search:focus {
        border-color: var(--upds-blue);
        box-shadow: 0 0 0 4px rgba(0, 53, 102, 0.1);
    }
    
    .sia-input {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.6rem 1rem;
    }

    /* BOTÓN PRINCIPAL */
    .btn-sia-primary {
        background-color: var(--upds-blue);
        color: white;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        border: none;
        transition: all 0.3s;
    }
    .btn-sia-primary:hover {
        background-color: var(--upds-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 53, 102, 0.25) !important;
        color: white;
    }

    /* ESTADOS Y BADGES */
    .bg-green-100 { background-color: var(--green-100) !important; }
    .text-green-700 { color: var(--green-700) !important; }
    .border-green-200 { border-color: var(--green-200) !important; }
    
    .bg-red-50 { background-color: var(--red-50) !important; }
    .text-red-600 { color: var(--red-600) !important; }
    .border-red-100 { border-color: var(--red-100) !important; }

    .bg-blue-50 { background-color: var(--blue-50) !important; }
    .border-blue-100 { border-color: var(--blue-100) !important; }

    /* DROPDOWN & HOVERS */
    .hover-bg-gray:hover { background-color: #f1f5f9; color: var(--upds-blue) !important; }
    .hover-bg-red-50:hover { background-color: #fef2f2; color: #dc2626 !important; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }
    .object-cover { object-fit: cover; }
</style>
@endsection