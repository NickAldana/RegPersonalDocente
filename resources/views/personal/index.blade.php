@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    {{-- ENCABEZADO INSTITUCIONAL Y ACCIONES GLOBALES --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                    <li class="breadcrumb-item small text-muted text-uppercase fw-bold">Gestión Académica</li>
                    <li class="breadcrumb-item small text-upds-blue text-uppercase fw-bold active">Personal Docente</li>
                </ol>
            </nav>
            <h2 class="fw-black text-upds-blue mb-0" style="letter-spacing: -0.02em;">
                DIRECTORIO PROFESIONAL
            </h2>
        </div>
        
        {{-- ZONA DE ACCIONES (Reporte y Creación) --}}
        <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2 mt-3 mt-md-0">
            
            {{-- BOTÓN REPORTE (Formal y separado de filtros) --}}
            {{-- request()->all() mantiene los filtros aplicados en el reporte --}}
            <a href="{{ route('personal.report', request()->all()) }}" target="_blank" class="btn btn-white border shadow-sm rounded-pill px-4 py-2 hover-scale d-flex align-items-center text-secondary fw-bold small">
                <i class="bi bi-printer-fill me-2 text-upds-gold"></i>
                IMPRIMIR NÓMINA
            </a>

            <a href="{{ route('personal.create') }}" class="btn btn-upds-blue rounded-pill px-4 py-2 shadow-sm hover-scale d-flex align-items-center">
                <i class="bi bi-person-plus-fill me-2"></i>
                <span class="fw-bold small">REGISTRAR NUEVO</span>
            </a>
        </div>
    </div>

    {{-- BARRA DE FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 bg-white">
            <form action="{{ route('personal.index') }}" method="GET" class="row g-2 align-items-end">
                
                {{-- Buscador --}}
                <div class="col-lg-4 col-md-12">
                    <label class="form-label text-xxs fw-bold text-muted text-uppercase ms-1">Búsqueda Global</label>
                    <div class="input-group bg-gray-50 rounded-3 border">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted small"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent py-2 small fw-bold text-dark" 
                               placeholder="Nombre, CI o Correo..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Carrera --}}
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xxs fw-bold text-muted text-uppercase ms-1">Carrera</label>
                    <select name="carrera_id" class="form-select border-0 bg-gray-50 small fw-bold text-muted shadow-none">
                        <option value="">-- TODAS --</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->CarreraID }}" {{ request('carrera_id') == $c->CarreraID ? 'selected' : '' }}>
                                {{ Str::upper($c->Nombrecarrera) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Cargo --}}
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xxs fw-bold text-muted text-uppercase ms-1">Cargo</label>
                    <select name="cargo_id" class="form-select border-0 bg-gray-50 small fw-bold text-muted shadow-none">
                        <option value="">-- TODOS --</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->CargoID }}" {{ request('cargo_id') == $cargo->CargoID ? 'selected' : '' }}>
                                {{ Str::upper($cargo->Nombrecargo) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Estado --}}
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-xxs fw-bold text-muted text-uppercase ms-1">Estado</label>
                    <select name="estado" class="form-select border-0 bg-gray-50 small fw-bold text-muted shadow-none">
                        <option value="">CUALQUIER ESTADO</option>
                        <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>ACTIVOS</option>
                        <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>DE BAJA</option>
                    </select>
                </div>

                {{-- Botón Filtrar --}}
                <div class="col-lg-2 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-upds w-100 fw-bold py-2 text-xs">
                            FILTRAR DATOS <i class="bi bi-funnel ms-1"></i>
                        </button>
                    </div>
                    
                    {{-- Botón Limpiar (Solo si hay filtros activos) --}}
                    @if(request()->anyFilled(['search', 'carrera_id', 'cargo_id', 'estado']))
                        <div class="text-center mt-1">
                            <a href="{{ route('personal.index') }}" class="text-danger text-xxs fw-bold text-uppercase text-decoration-none">
                                <i class="bi bi-x-circle-fill me-1"></i> Limpiar Filtros
                            </a>
                        </div>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-gray-100">
                    <tr class="text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">
                        <th class="ps-4 py-3 text-muted border-0">Identificación del Profesional</th>
                        <th class="py-3 text-muted border-0">Cargo y Contrato</th>
                        <th class="py-3 text-muted border-0 text-center">Carga</th>
                        <th class="py-3 text-muted border-0 text-center">Estatus</th>
                        <th class="pe-4 py-3 text-muted border-0 text-end">Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($personales as $p)
                        <tr class="border-bottom border-light transition-all">
                            {{-- Perfil Consistente --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center py-2">
                                    <div class="position-relative me-3">
                                        <img src="{{ $p->Fotoperfil ? asset('storage/' . $p->Fotoperfil) : 'https://ui-avatars.com/api/?name='.urlencode($p->Nombrecompleto).'&background=f1f5f9&color=003566' }}" 
                                             class="rounded-circle border shadow-sm object-cover" width="45" height="45">
                                        @if($p->usuario && $p->usuario->Activo)
                                            <span class="online-indicator"></span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm fw-bold text-upds-blue">
                                            {{ Str::upper($p->Apellidopaterno) }} {{ Str::upper($p->Apellidomaterno) }}, {{ Str::upper($p->Nombrecompleto) }}
                                        </h6>
                                        <span class="text-xxs text-muted fw-bold">CI: {{ $p->Ci }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Cargo --}}
                            <td>
                                <div class="badge bg-blue-soft text-upds-blue fw-bold mb-1" style="font-size: 0.65rem;">
                                    {{ Str::upper($p->cargo->Nombrecargo ?? 'SIN ASIGNAR') }}
                                </div>
                                <div class="text-xxs text-muted fw-bold">{{ Str::upper($p->contrato->Nombrecontrato ?? 'SIN CONTRATO') }}</div>
                            </td>

                            {{-- Carga (KPI) --}}
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold text-dark">{{ $p->materias_count }}</span>
                                    <span class="text-xxs text-muted uppercase">Mat.</span>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="text-center">
                                @if($p->Activo)
                                    <span class="badge-status status-active">ACTIVO</span>
                                @else
                                    <span class="badge-status status-inactive">INACTIVO</span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('personal.show', $p->PersonalID) }}" class="btn btn-ghost-primary" title="Ver Expediente">
                                        EXPEDIENTE
                                    </a>
                                    
                                    <a href="{{ route('personal.edit', $p->PersonalID) }}" class="btn btn-ghost-warning" title="Editar">
                                        EDITAR
                                    </a>

                                    <form action="{{ route('personal.toggle', $p->PersonalID) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($p->Activo)
                                            <button type="submit" class="btn btn-ghost-danger" onclick="return confirm('¿Confirmar baja? Se bloqueará el acceso al sistema.')">
                                                DAR BAJA
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-ghost-success">
                                                ALTA
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox text-light display-3"></i>
                                <p class="text-muted mt-2 fw-bold">No se encontraron registros de personal docente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Paginación --}}
        @if($personales->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-xxs text-muted fw-bold">RESULTADOS: {{ $personales->total() }} PROFESIONALES</span>
                    {{ $personales->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* VARIABLES SOBRIAS V4.0 */
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
    }

    .text-upds-blue { color: var(--upds-blue) !important; }
    .bg-blue-soft { background-color: #e0eaf4; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    
    .btn-upds-blue { background-color: var(--upds-blue); color: white; border: none; }
    .btn-upds-blue:hover { background-color: #00284d; color: white; }
    
    .btn-outline-upds { border: 2px solid var(--upds-blue); color: var(--upds-blue); background: transparent; }
    .btn-outline-upds:hover { background-color: var(--upds-blue); color: white; }

    /* BOTÓN BLANCO PARA REPORTE */
    .btn-white { background-color: white; border-color: #e2e8f0; }
    .btn-white:hover { background-color: #f8fafc; border-color: #cbd5e1; }

    /* BOTONES GHOST (SOBRIOS) */
    .btn-ghost-primary, .btn-ghost-warning, .btn-ghost-danger, .btn-ghost-success {
        font-size: 0.6rem; font-weight: 800; padding: 5px 12px; border-radius: 6px;
        background: transparent; border: 1px solid transparent; transition: all 0.2s;
    }
    .btn-ghost-primary { color: #2563eb; border-color: #dbeafe; }
    .btn-ghost-primary:hover { background: #eff6ff; border-color: #2563eb; }
    
    .btn-ghost-warning { color: #d97706; border-color: #fef3c7; }
    .btn-ghost-warning:hover { background: #fffbeb; border-color: #d97706; }
    
    .btn-ghost-danger { color: #dc2626; border-color: #fee2e2; }
    .btn-ghost-danger:hover { background: #fef2f2; border-color: #dc2626; }

    .btn-ghost-success { color: #16a34a; border-color: #dcfce7; }
    .btn-ghost-success:hover { background: #f0fdf4; border-color: #16a34a; }

    /* BADGES DE ESTADO */
    .badge-status {
        font-size: 0.6rem; font-weight: 900; padding: 4px 10px; border-radius: 4px; display: inline-block;
    }
    .status-active { color: #16a34a; border: 1px solid #dcfce7; }
    .status-inactive { color: #94a3b8; border: 1px solid #e2e8f0; }

    /* UTILIDADES */
    .text-xxs { font-size: 0.65rem; }
    .object-cover { object-fit: cover; }
    .hover-scale:hover { transform: scale(1.02); transition: transform 0.2s; }
    
    .online-indicator {
        position: absolute; bottom: 1px; right: 1px; width: 11px; height: 11px;
        background-color: #10b981; border: 2px solid white; border-radius: 50%;
    }

    /* PAGINACIÓN */
    .pagination { margin-bottom: 0; }
    .page-link { border: none; color: var(--upds-blue); font-weight: bold; font-size: 0.8rem; }
    .page-item.active .page-link { background-color: var(--upds-blue); border-radius: 8px; }
</style>
@endsection