@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ENCABEZADO INSTITUCIONAL --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 px-2">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                    <li class="breadcrumb-item small text-muted text-uppercase fw-bold">Gestión Académica</li>
                    <li class="breadcrumb-item small text-upds-blue text-uppercase fw-bold active">Expediente del Docente</li>
                </ol>
            </nav>
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                EXPEDIENTE DIGITAL
            </h1>
        </div>
        
        <div class="d-flex gap-2">
            @can('gestionar_personal')
                <a href="{{ route('personal.index') }}" class="btn btn-white border rounded-pill px-4 fw-bold text-xs shadow-sm transition-all">
                    <i class="bi bi-arrow-left me-2"></i> VOLVER AL LISTADO
                </a>
            @endcan
            
            <a href="{{ route('personal.print', $docente->PersonalID) }}" target="_blank" class="btn btn-sia-primary shadow-sm rounded-pill px-4 fw-bold text-xs">
                <i class="bi bi-printer-fill me-2"></i> GENERAR KARDEX
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- COLUMNA IZQUIERDA: TARJETA DE IDENTIDAD --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-body p-0">
                    {{-- Avatar Section --}}
                    <div class="bg-gray-50 p-5 text-center border-bottom border-light">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $docente->Fotoperfil ? asset('storage/' . $docente->Fotoperfil) : 'https://ui-avatars.com/api/?name='.urlencode($docente->Nombrecompleto).'&background=f1f5f9&color=003566' }}" 
                                 class="rounded-circle border border-4 border-white shadow-md object-cover" width="140" height="140">
                            
                            <div class="position-absolute bottom-0 end-0 translate-middle-x mb-n1">
                                @if($docente->Activo)
                                    <span class="badge bg-success rounded-pill border border-2 border-white px-3 shadow-sm" style="font-size: 0.65rem;">ACTIVO</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill border border-2 border-white px-3 shadow-sm" style="font-size: 0.65rem;">INACTIVO</span>
                                @endif
                            </div>
                        </div>
                        
                        <h5 class="fw-black text-upds-blue mb-1" style="font-size: 1.1rem; line-height: 1.2;">
                            {{ $docente->nombre_institucional }}
                        </h5>
                        <p class="text-muted small fw-bold text-uppercase mt-2 mb-0" style="letter-spacing: 1px;">
                            {{ $docente->cargo->Nombrecargo ?? 'DOCENTE' }}
                        </p>
                    </div>

                    {{-- Contact Info --}}
                    <div class="p-4">
                        <label class="text-xxs fw-bold text-muted text-uppercase mb-3 d-block tracking-wider">Información de Enlace</label>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-blue-soft text-upds-blue rounded-3 p-2 me-3"><i class="bi bi-envelope-at"></i></div>
                            <div class="text-truncate">
                                <small class="d-block text-muted text-xxs fw-bold">CORREO INSTITUCIONAL</small>
                                <span class="small fw-bold text-dark">{{ $docente->Correoelectronico }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-blue-soft text-upds-blue rounded-3 p-2 me-3"><i class="bi bi-whatsapp"></i></div>
                            <div>
                                <small class="d-block text-muted text-xxs fw-bold">TELÉFONO / MÓVIL</small>
                                <span class="small fw-bold text-dark">{{ $docente->Telelefono ?? 'SIN REGISTRO' }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-blue-soft text-upds-blue rounded-3 p-2 me-3"><i class="bi bi-card-text"></i></div>
                            <div>
                                <small class="d-block text-muted text-xxs fw-bold">IDENTIFICACIÓN</small>
                                <span class="small fw-bold text-dark">CI: {{ $docente->Ci }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen de Seguridad --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h6 class="text-xxs fw-bold text-muted text-uppercase mb-3">Acceso al Sistema</h6>
                @if($docente->usuario && $docente->usuario->Activo)
                    <div class="d-flex align-items-center text-success small fw-bold">
                        <i class="bi bi-shield-check fs-5 me-2"></i> Cuenta Habilitada
                    </div>
                @else
                    <div class="d-flex align-items-center text-muted small fw-bold">
                        <i class="bi bi-shield-lock fs-5 me-2"></i> Sin Acceso
                    </div>
                @endif
            </div>
        </div>

        {{-- COLUMNA DERECHA: INFORMACIÓN DETALLADA --}}
        <div class="col-lg-8 col-xl-9">
            
            {{-- Trayectoria Laboral --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4 border-end-md">
                            <div class="ps-md-2">
                                <label class="text-xxs fw-bold text-muted text-uppercase d-block mb-1">Tipo de Contrato</label>
                                <span class="small fw-bold text-dark text-uppercase">{{ $docente->contrato->Nombrecontrato ?? 'NO DEFINIDO' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 border-end-md">
                            <div class="ps-md-2">
                                <label class="text-xxs fw-bold text-muted text-uppercase d-block mb-1">Trayectoria Docente</label>
                                <span class="small fw-bold text-dark">{{ $docente->Añosexperiencia ?? '0' }} AÑOS REGISTRADOS</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ps-md-2">
                                <label class="text-xxs fw-bold text-muted text-uppercase d-block mb-1">Máximo Grado Alcanzado</label>
                                <span class="small fw-bold text-upds-blue text-uppercase">{{ $docente->grado->Nombregrado ?? 'LICENCIATURA' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SISTEMA DE PESTAÑAS --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs sia-tabs border-0" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-formacion" type="button">
                                <i class="bi bi-mortarboard me-2"></i> FORMACIÓN
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-carga" type="button">
                                <i class="bi bi-calendar3 me-2"></i> CARGA HORARIA 2026
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-produccion" type="button">
                                <i class="bi bi-journal-check me-2"></i> PRODUCCIÓN CIENTÍFICA
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 bg-gray-50">
                    <div class="tab-content">
                        
                        {{-- TAB 1: FORMACIÓN ACADÉMICA --}}
                        <div class="tab-pane fade show active" id="tab-formacion">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold text-upds-blue mb-0">Títulos y Postgrados Certificados</h6>
                                @if(Auth::user()->canDo('gestionar_personal') || Auth::id() == $docente->PersonalID)
                                    <button class="btn btn-outline-upds btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalFormacion">
                                        <i class="bi bi-plus-lg me-1"></i> ADJUNTAR TÍTULO
                                    </button>
                                @endif
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-sm align-middle bg-white rounded-3 shadow-sm">
                                    <thead class="bg-gray-100">
                                        <tr class="text-xxs text-muted fw-bold text-uppercase">
                                            <th class="ps-3 py-2">Grado</th>
                                            <th>Título Obtenido</th>
                                            <th>Institución</th>
                                            <th class="text-end pe-3">Evidencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($docente->formaciones as $f)
                                            <tr>
                                                <td class="ps-3"><span class="badge bg-blue-soft text-upds-blue">{{ $f->grado->Nombregrado }}</span></td>
                                                <td class="small fw-bold text-dark text-uppercase">{{ $f->Tituloobtenido }}</td>
                                                <td class="small text-muted">{{ $f->centro->Nombrecentro }}</td>
                                                <td class="text-end pe-3">
                                                    @if($f->RutaArchivo)
                                                        <a href="{{ asset('storage/' . $f->RutaArchivo) }}" target="_blank" class="text-primary fs-5"><i class="bi bi-file-earmark-pdf-fill"></i></a>
                                                    @else
                                                        <button class="btn btn-link text-warning p-0 text-xxs fw-bold shadow-none" 
                                                                data-id="{{ $f->FormacionID }}" 
                                                                data-titulo="{{ $f->Tituloobtenido }}"
                                                                onclick="abrirModalPDF(this.getAttribute('data-id'), this.getAttribute('data-titulo'))">
                                                            SUBIR PDF
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5 text-muted small">Sin registros de formación académica.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: CARGA HORARIA --}}
                        <div class="tab-pane fade" id="tab-carga">
                            <h6 class="fw-bold text-upds-blue mb-4">Docencia - Planificación Gestión 2026</h6>
                            <div class="row g-3">
                                @forelse($docente->materias as $m)
                                    <div class="col-md-6">
                                        <div class="p-3 border-0 shadow-sm rounded-3 bg-white d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="text-xxs fw-bold text-upds-blue mb-1">{{ $m->Sigla }}</div>
                                                <div class="small fw-bold text-dark text-uppercase">{{ $m->Nombremateria }}</div>
                                                <div class="text-xxs text-muted fw-bold text-uppercase">{{ $m->carrera->Nombrecarrera }}</div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-gray-100 text-dark border shadow-none px-3">P-{{ $m->carga->Periodo }}</span>
                                                @if($m->carga->RutaAutoevaluacion)
                                                    <a href="{{ asset('storage/'.$m->carga->RutaAutoevaluacion) }}" target="_blank" class="d-block mt-1 text-success small" title="Ver Autoevaluación">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 text-muted small">No tiene carga horaria asignada actualmente.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- TAB 3: PRODUCCIÓN CIENTÍFICA (MEJORADA) --}}
                        <div class="tab-pane fade" id="tab-produccion">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold text-upds-blue mb-0">Investigación y Publicaciones Académicas</h6>
                                
                                {{-- BOTÓN PARA REGISTRO RÁPIDO (Se muestra si se tiene permiso) --}}
                                @can('gestionar_personal')
                                    <button class="btn btn-sia-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFastPub">
                                        <i class="bi bi-plus-lg me-1"></i> REGISTRAR OBRA
                                    </button>
                                @endcan
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle bg-white rounded-3 shadow-sm">
                                    <thead class="bg-gray-100">
                                        <tr class="text-xxs text-muted fw-bold text-uppercase">
                                            <th class="ps-3 py-2">Tipo</th>
                                            <th>Título de la Publicación</th>
                                            <th class="text-center">Rol</th>
                                            <th class="text-end pe-3">Año</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($docente->publicaciones as $pub)
                                            <tr>
                                                <td class="ps-3"><span class="small fw-bold text-muted">{{ $pub->tipo->Nombretipo }}</span></td>
                                                <td class="small fw-bold text-dark text-uppercase">{{ $pub->Nombrepublicacion }}</td>
                                                <td class="text-center small">
                                                    <span class="badge bg-light text-dark border px-3">
                                                        {{ $pub->pivot->rol->Nombrerol ?? 'AUTOR' }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-3 small text-muted">
                                                    {{ $pub->Fechapublicacion ? \Carbon\Carbon::parse($pub->Fechapublicacion)->year : 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5 text-muted small">No se registra producción intelectual.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: ADJUNTAR RESPALDO PDF (Formación) --}}
<div class="modal fade" id="modalSubirPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h6 class="modal-title fw-bold text-upds-blue small uppercase">Adjuntar Respaldo PDF</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('formacion.updatePDF') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="FormacionID" id="form_pdf_id">
                <div class="modal-body p-4 text-center">
                    <p class="text-xxs text-muted fw-black mb-3 text-uppercase" id="form_pdf_titulo"></p>
                    <div class="p-3 bg-gray-50 border border-dashed rounded-3">
                        <input type="file" name="RutaArchivo" class="form-control form-control-sm border-0 bg-transparent shadow-none" accept=".pdf" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="submit" class="btn btn-sia-primary w-100 fw-bold">SUBIR Y ACTUALIZAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: REGISTRO RÁPIDO DE PUBLICACIÓN (Fast Track) --}}
<div class="modal fade" id="modalFastPub" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-upds-blue text-white px-4">
                <h6 class="modal-title fw-bold small text-uppercase">
                    <i class="bi bi-journal-plus me-2"></i> Nueva Publicación para: {{ $docente->getNombreCortoAttribute() }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('publicaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-gray-50">
                    
                    {{-- 1. DETALLES DE LA OBRA --}}
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="text-xxs fw-bold text-muted text-uppercase">Título de la Obra</label>
                            <input type="text" name="Nombrepublicacion" class="form-control fw-bold border-0 bg-white shadow-sm" placeholder="Ej: INTELIGENCIA ARTIFICIAL EN LA EDUCACIÓN" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-xxs fw-bold text-muted text-uppercase">Tipo</label>
                            <select name="TipopublicacionID" class="form-select border-0 bg-white shadow-sm small" required>
                                @foreach($tiposPub ?? [] as $t)
                                    <option value="{{ $t->TipopublicacionID }}">{{ $t->Nombretipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-xxs fw-bold text-muted text-uppercase">Medio / Editorial</label>
                            <select name="MediopublicacionID" class="form-select border-0 bg-white shadow-sm small" required>
                                @foreach($mediosPub ?? [] as $m)
                                    <option value="{{ $m->MediopublicacionID }}">{{ $m->Nombremedio }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="text-xxs fw-bold text-muted text-uppercase">Fecha</label>
                            <input type="date" name="Fechapublicacion" class="form-control border-0 bg-white shadow-sm" required>
                        </div>
                    </div>

                    {{-- 2. VINCULACIÓN AUTOMÁTICA --}}
                    <div class="bg-blue-soft rounded-3 p-3 border border-blue-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-check-fill text-upds-blue fs-4 me-3"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $docente->nombre_institucional }}</h6>
                                    <small class="text-muted">Se asignará automáticamente como autor.</small>
                                </div>
                            </div>
                            
                            <div style="width: 200px;">
                                <label class="text-xxs fw-bold text-upds-blue text-uppercase mb-1">Rol en la obra</label>
                                <select name="roles[]" class="form-select form-select-sm border-upds-blue fw-bold text-upds-blue">
                                    @foreach($rolesPub ?? [] as $rol)
                                        <option value="{{ $rol->RolID }}">{{ $rol->Nombrerol }}</option>
                                    @endforeach
                                </select>
                                {{-- INPUT OCULTO CON EL ID DEL DOCENTE ACTUAL --}}
                                <input type="hidden" name="autores[]" value="{{ $docente->PersonalID }}">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 bg-white p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sia-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save-fill me-2"></i> Guardar y Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    function abrirModalPDF(id, titulo) {
        document.getElementById('form_pdf_id').value = id;
        document.getElementById('form_pdf_titulo').innerText = titulo;
        new bootstrap.Modal(document.getElementById('modalSubirPDF')).show();
    }
</script>

<style>
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --blue-soft: #eef2f7;
    }

    .fw-black { font-weight: 900; }
    .text-xxs { font-size: 0.62rem; letter-spacing: 0.5px; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    .bg-blue-soft { background-color: var(--blue-soft); }
    .object-cover { object-fit: cover; }
    
    /* Pestañas Sobrias */
    .sia-tabs .nav-link { color: #94a3b8; font-weight: 800; font-size: 0.72rem; padding: 1.2rem 1.5rem; border: none; border-bottom: 3px solid transparent; transition: 0.3s; }
    .sia-tabs .nav-link.active { color: var(--upds-blue); border-bottom-color: var(--upds-gold); background: white; }
    .sia-tabs .nav-link:hover:not(.active) { color: var(--upds-blue); border-bottom-color: #e2e8f0; }
    
    .btn-sia-primary { background-color: var(--upds-blue); color: white; border: none; transition: 0.3s; }
    .btn-sia-primary:hover { background-color: #00284d; transform: translateY(-1px); color: white; }
    
    .btn-outline-upds { border: 2px solid var(--upds-blue); color: var(--upds-blue); font-weight: 800; font-size: 0.65rem; transition: 0.2s; }
    .btn-outline-upds:hover { background: var(--upds-blue); color: white; }

    .border-end-md { border-right: 1px solid #e2e8f0; }

    @media (max-width: 768px) {
        .border-end-md { border-right: none; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; }
    }

    @media print {
        .btn, nav, .card-header button, .modal, .breadcrumb { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        .bg-gray-50 { background-color: white !important; }
        body { background: white !important; }
    }
</style>
@endsection