@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-upds-blue mb-0">Directorio de Personal</h2>
            <p class="text-muted small uppercase tracking-wider">Gestión Académica y Administrativa</p>
        </div>
        @can('gestionar_personal')
        <a href="{{ route('personal.create') }}" class="btn btn-sia-primary rounded-pill shadow-sm px-4 fw-bold">
            <i class="bi bi-person-plus-fill me-2"></i> Nuevo Expediente
        </a>
        @endcan
    </div>

    {{-- BARRA DE FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="border-top: 3px solid var(--upds-gold) !important;">
        <div class="card-body p-4">
            <form action="{{ route('personal.index') }}" method="GET" class="row g-3 align-items-end">
                
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-upds-blue opacity-75">Búsqueda Inteligente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-upds-blue"></i></span>
                        <input type="text" name="buscar" class="form-control bg-light border-0" 
                               placeholder="Nombre, CI o Cargo..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-upds-blue opacity-75">Filtrar por Carrera</label>
                    <select name="carrera" class="form-select bg-light border-0">
                        <option value="">-- Todas --</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->IdCarrera }}" {{ request('carrera') == $c->IdCarrera ? 'selected' : '' }}>
                                {{ $c->NombreCarrera }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-upds-blue opacity-75">Estado</label>
                    <select name="estado" class="form-select bg-light border-0">
                        <option value="" {{ request('estado') === null ? 'selected' : '' }}>Mostrar Todos</option>
                        <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}> Activos</option>
                        <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}> Bajas</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-sia-primary w-100 fw-bold">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('personal.index') }}" class="btn btn-light border" title="Limpiar">
                        <i class="bi bi-arrow-counterclockwise text-upds-blue"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f1f5f9;">
                    <tr class="extra-small fw-bold text-upds-blue uppercase tracking-widest">
                        <th class="ps-4 py-3">Profesional</th>
                        <th class="py-3">Identificación</th>
                        <th class="py-3">Vinculación</th>
                        <th class="py-3 text-center">Estado</th>
                        <th class="pe-4 py-3 text-end">Gestión</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($personal as $docente)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        @if($docente->FotoPerfil)
                                            <img src="{{ asset('storage/' . $docente->FotoPerfil) }}" class="rounded-circle border border-2 border-white shadow-sm object-cover" width="40" height="40">
                                        @else
                                            <div class="rounded-circle bg-sia-light text-upds-blue border border-blue-100 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                {{ substr($docente->NombreCompleto, 0, 1) }}
                                            </div>
                                        @endif
                                        {{-- Punto verde online si tiene usuario activo --}}
                                        @if($docente->usuario && $docente->usuario->Activo)
                                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-upds-blue" style="font-size: 0.9rem;">
                                            {{ $docente->ApellidoPaterno }} {{ $docente->ApellidoMaterno }}
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">
                                            {{ $docente->NombreCompleto }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center text-dark fw-medium" style="font-size: 0.85rem;">
                                    <i class="bi bi-card-heading text-upds-gold me-2"></i>
                                    {{ $docente->CI ?? 'N/A' }}
                                </div>
                            </td>

                            <td>
                                <div class="badge bg-blue-50 text-upds-blue border border-blue-100 mb-1 fw-normal">
                                    {{ $docente->cargo->NombreCargo ?? 'Sin Cargo' }}
                                </div>
                                <div class="text-xs text-muted">
                                    <i class="bi bi-file-text me-1"></i> {{ $docente->contrato->NombreContrato ?? 'Sin Contrato' }}
                                </div>
                            </td>

                            <td class="text-center">
                                @if($docente->Activo)
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2">ACTIVO</span>
                                @else
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2">BAJA</span>
                                @endif
                            </td>

                            <td class="pe-4 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-white btn-sm border rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical text-upds-blue"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="min-width: 200px;">
                                        <li>
                                            <a class="dropdown-item rounded-2 small fw-medium py-2" href="{{ route('personal.show', $docente->IdPersonal) }}">
                                                <i class="bi bi-eye text-primary me-2"></i> Ver Expediente
                                            </a>
                                        </li>

                                        @can('gestionar_personal')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item rounded-2 small fw-medium py-2" href="{{ route('carga.create', ['docente_id' => $docente->IdPersonal]) }}">
                                                    <i class="bi bi-calendar-plus text-upds-gold me-2"></i> Asignar Carga
                                                </a>
                                            </li>

                                            {{-- Botón Crear Usuario: Solo si no existe cuenta --}}
                                            @if(!$docente->usuario)
                                                <li>
                                                    <form action="{{ route('personal.create_user', $docente->IdPersonal) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item rounded-2 small fw-medium py-2">
                                                            <i class="bi bi-person-fill-lock text-dark me-2"></i> Crear Usuario
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif

                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('personal.toggle', $docente->IdPersonal) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item rounded-2 small fw-bold py-2 {{ $docente->Activo ? 'text-danger' : 'text-success' }}" 
                                                            onclick="return confirm('¿Confirmar cambio de estado?')">
                                                        @if($docente->Activo)
                                                            <i class="bi bi-archive me-2"></i> Dar de Baja
                                                        @else
                                                            <i class="bi bi-check-circle me-2"></i> Reactivar
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
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <i class="bi bi-search text-upds-blue opacity-10" style="font-size: 4rem;"></i>
                                    @if(!empty(request()->query()))
                                        <h6 class="text-upds-blue fw-bold">Sin coincidencias</h6>
                                        <p class="text-muted small">No se encontró personal con esos filtros.</p>
                                    @else
                                        <h6 class="text-upds-blue fw-bold">Esperando Búsqueda</h6>
                                        <p class="text-muted small">Use los filtros superiores para visualizar al personal.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($personal instanceof \Illuminate\Pagination\LengthAwarePaginator && $personal->total() > 0)
            <div class="card-footer bg-white border-0 py-3">
                {{ $personal->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .btn-sia-primary { background-color: var(--upds-blue); color: white; border: none; transition: all 0.3s; }
    .btn-sia-primary:hover { background-color: #001d3d; color: var(--upds-gold); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 53, 102, 0.2); }
    .bg-sia-light { background-color: rgba(0, 53, 102, 0.05); }
    .text-upds-blue { color: var(--upds-blue); }
    .text-upds-gold { color: var(--upds-gold); }
    .extra-small { font-size: 0.7rem; }
    .btn-white:hover { background-color: #f8fafc; }
    .pagination .page-item.active .page-link { background-color: var(--upds-blue); border-color: var(--upds-blue); }
    .pagination .page-link { color: var(--upds-blue); }
    .dropdown-item:hover { background-color: #f1f5f9; }
    .object-cover { object-fit: cover; }
</style>
@endsection