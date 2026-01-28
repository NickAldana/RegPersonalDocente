@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 px-2">
        <div class="mb-3 mb-md-0">
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                PERFIL PROFESIONAL
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-person-badge-fill me-1 text-upds-gold"></i> Legajo Digital del Personal
            </p>
        </div>
        
        <div class="d-flex gap-2">
            @can('gestionar_personal')
                <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary border-2 rounded-pill px-4 fw-bold text-xs text-uppercase tracking-wide hover-scale">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </a>
            @endcan
            
            <a href="{{ route('personal.print', $docente->IdPersonal) }}" target="_blank" class="btn btn-sia-primary shadow-lg hover-scale">
                <i class="bi bi-printer-fill me-2"></i> Imprimir Kardex
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- COLUMNA IZQUIERDA: TARJETA DE IDENTIDAD --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white h-100">
                <div class="card-body p-0">
                    
                    <div class="bg-gradient-upds p-5 text-center position-relative">
                        <div class="position-relative d-inline-block mb-3">
                            @if($docente->FotoPerfil)
                                <img src="{{ asset('storage/' . $docente->FotoPerfil) }}" class="rounded-circle border border-4 border-white shadow-md object-cover" width="140" height="140">
                            @else
                                <div class="rounded-circle bg-white text-upds-blue d-flex align-items-center justify-content-center fw-black border border-4 border-upds-gold shadow-md" style="width: 140px; height: 140px; font-size: 3.5rem;">
                                    {{ substr($docente->NombreCompleto, 0, 1) }}
                                </div>
                            @endif
                            
                            <div class="position-absolute bottom-0 end-0 translate-middle-x">
                                @if($docente->Activo)
                                    <span class="badge bg-green-500 border border-2 border-white text-white rounded-pill px-3 py-1 shadow-sm">ACTIVO</span>
                                @else
                                    <span class="badge bg-red-500 border border-2 border-white text-white rounded-pill px-3 py-1 shadow-sm">BAJA</span>
                                @endif
                            </div>
                        </div>
                        
                        <h5 class="fw-black text-white mb-1">{{ $docente->NombreCompleto }}</h5>
                        <p class="text-white-50 small fw-bold text-uppercase tracking-wider mb-0">
                            {{ $docente->ApellidoPaterno }} {{ $docente->ApellidoMaterno }}
                        </p>
                    </div>

                    <div class="p-4">
                        <div class="mb-4">
                            <label class="sia-label-mini">Credenciales de Acceso</label>
                            @if($docente->usuario && $docente->usuario->Activo)
                                <div class="d-flex align-items-center text-success fw-bold bg-green-50 p-2 rounded-3 border border-green-100">
                                    <i class="bi bi-shield-lock-fill me-2 fs-5"></i> Habilitado
                                </div>
                            @else
                                <div class="d-flex align-items-center text-muted fw-bold bg-gray-50 p-2 rounded-3 border">
                                    <i class="bi bi-shield-x me-2 fs-5"></i> Sin Acceso
                                </div>
                            @endif
                        </div>

                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-center text-dark">
                                <div class="bg-blue-50 text-upds-blue rounded-circle p-2 me-3"><i class="bi bi-envelope-fill"></i></div>
                                <div class="text-truncate small fw-medium">{{ $docente->CorreoElectronico }}</div>
                            </li>
                            <li class="mb-3 d-flex align-items-center text-dark">
                                <div class="bg-blue-50 text-upds-blue rounded-circle p-2 me-3"><i class="bi bi-telephone-fill"></i></div>
                                <div class="small fw-medium">{{ $docente->Telefono ?? 'No registrado' }}</div>
                            </li>
                            <li class="d-flex align-items-center text-dark">
                                <div class="bg-blue-50 text-upds-blue rounded-circle p-2 me-3"><i class="bi bi-person-vcard-fill"></i></div>
                                <div class="small fw-medium">CI: {{ $docente->CI }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: DETALLES Y PESTAÑAS --}}
        <div class="col-lg-8 col-xl-9">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="sia-section-title mb-4">
                        <span class="icon"><i class="bi bi-briefcase-fill"></i></span> Vinculación Institucional
                    </h6>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="sia-info-box">
                                <label>Cargo Actual</label>
                                <p>{{ $docente->cargo->NombreCargo ?? 'Sin Asignar' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sia-info-box">
                                <label>Modalidad de Contrato</label>
                                <p>{{ $docente->contrato->NombreContrato ?? 'Sin Contrato' }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="sia-info-box bg-blue-50 border-blue-100">
                                <label class="text-upds-blue">Unidad Académica (Carreras)</label>
                                @if($docente->carreras->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach($docente->carreras as $carrera)
                                            <span class="badge bg-white text-upds-blue border shadow-sm px-3 py-2 fw-bold">
                                                {{ $carrera->NombreCarrera }}
                                                <small class="d-block text-muted fw-normal mt-1" style="font-size: 0.65rem;">
                                                    {{ $carrera->facultad->NombreFacultad ?? 'UPDS' }}
                                                </small>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted fst-italic mb-0">No tiene asignación específica.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PESTAÑAS DE HISTORIAL --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs sia-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="formacion-tab" data-bs-toggle="tab" data-bs-target="#formacion" type="button">
                                <i class="bi bi-mortarboard-fill me-2"></i> Formación
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="carga-tab" data-bs-toggle="tab" data-bs-target="#carga" type="button">
                                <i class="bi bi-calendar-week-fill me-2"></i> Carga Horaria
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 bg-gray-50">
                    <div class="tab-content" id="profileTabsContent">
                        
                        {{-- TAB: FORMACIÓN --}}
                        <div class="tab-pane fade show active" id="formacion">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">Grados Académicos Registrados</h6>
                                @if(Auth::user()->can('gestionar_personal') || Auth::id() == $docente->IdPersonal)
                                    <button class="btn btn-sm btn-sia-ghost" data-bs-toggle="modal" data-bs-target="#modalFormacion">
                                        <i class="bi bi-plus-circle-fill me-1"></i> Agregar Título
                                    </button>
                                @endif
                            </div>

                            <div class="list-group shadow-sm rounded-3 overflow-hidden border-0">
                                @forelse($docente->formaciones as $formacion)
                                    <div class="list-group-item p-3 border-start border-4 border-start-upds-gold bg-white mb-2 rounded-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold text-upds-blue mb-1">{{ $formacion->TituloObtenido }}</h6>
                                                <div class="small text-muted mb-1">
                                                    <i class="bi bi-bank2 me-1"></i> {{ $formacion->centroFormacion->NombreCentro ?? 'Institución' }}
                                                </div>
                                                <span class="badge bg-gray-100 text-dark border">{{ $formacion->gradoAcademico->NombreGrado ?? 'Grado' }}</span>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-dark fs-5">{{ $formacion->AñoEstudios }}</div>
                                                
                                                {{-- Lógica de Respaldo PDF con Data Attributes --}}
                                                @if($formacion->RutaArchivo)
                                                    <a href="{{ asset('storage/' . $formacion->RutaArchivo) }}" target="_blank" class="btn btn-xs text-danger hover-bg-red-50 mt-1 fw-bold">
                                                        <i class="bi bi-file-pdf-fill me-1"></i> Ver PDF
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-xs text-warning hover-bg-orange-50 mt-1 fw-bold shadow-none" 
                                                            data-id="{{ $formacion->IdFormacion }}" 
                                                            data-titulo="{{ $formacion->TituloObtenido }}"
                                                            onclick="abrirModalPDF(this)">
                                                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Subir Respaldo
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted bg-white rounded-3">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        Sin registros académicos.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- TAB: CARGA HORARIA --}}
                        <div class="tab-pane fade" id="carga">
                             <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">Historial de Materias</h6>
                                @can('asignar_carga')
                                    <a href="{{ route('carga.create', ['docente_id' => $docente->IdPersonal]) }}" class="btn btn-sm btn-sia-ghost">
                                        <i class="bi bi-arrow-right-circle-fill me-1"></i> Gestionar Carga
                                    </a>
                                @endcan
                            </div>
                            
                            <div class="table-responsive bg-white rounded-3 shadow-sm border">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light text-secondary small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3">Gestión</th>
                                            <th class="py-3">Periodo</th>
                                            <th class="py-3">Materia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($docente->materias as $carga)
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">{{ $carga->pivot->Gestion }}</td>
                                                <td><span class="badge bg-blue-100 text-upds-blue rounded-pill px-3">{{ $carga->pivot->Periodo }}</span></td>
                                                <td class="text-secondary fw-medium">{{ $carga->NombreMateria }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Sin carga asignada.</td>
                                            </tr>
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

{{-- MODAL 1: AGREGAR NUEVO TÍTULO --}}
<div class="modal fade" id="modalFormacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl rounded-4">
            <div class="modal-header bg-upds-blue text-white px-4 py-3">
                <h6 class="modal-title fw-bold text-uppercase tracking-wide"><i class="bi bi-plus-circle me-2"></i> Nuevo Grado Académico</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('formacion.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="IdPersonal" value="{{ $docente->IdPersonal }}">
                <div class="modal-body p-4 bg-gray-50">
                    <div class="mb-3">
                        <label class="sia-label-mini">Nivel Académico</label>
                        <select name="IdGradoAcademico" class="form-select sia-input" required>
                            @foreach($grados as $grado)
                                <option value="{{ $grado->IdGradoAcademico }}">{{ $grado->NombreGrado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="sia-label-mini">Institución</label>
                        <select name="IdCentroFormacion" class="form-select sia-input" required>
                            @foreach($centros as $centro)
                                <option value="{{ $centro->IdCentroFormacion }}">{{ $centro->NombreCentro }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="sia-label-mini">Título Obtenido</label>
                        <input type="text" name="TituloObtenido" class="form-control sia-input" placeholder="Ej: Maestría en..." required>
                    </div>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="sia-label-mini">Gestión</label>
                            <input type="number" name="AñoEstudios" class="form-control sia-input text-center" placeholder="202X" required>
                        </div>
                        <div class="col-8">
                            <label class="sia-label-mini">Respaldo Digital (PDF)</label>
                            <input type="file" name="ArchivoTitulo" class="form-control sia-input" accept=".pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sia-primary rounded-pill px-4 shadow-sm">Guardar Título</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: SUBIDA RÁPIDA DE PDF (AJUSTADO) --}}
<div class="modal fade" id="modalSubirPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-2xl rounded-4">
            <div class="modal-header bg-warning text-dark px-4 py-3 border-0">
                <h6 class="modal-title fw-bold text-uppercase small"><i class="bi bi-file-earmark-pdf-fill me-2"></i> Adjuntar Respaldo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('formacion.updatePDF') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="IdFormacion" id="form_pdf_id">
                <div class="modal-body p-4 bg-gray-50 text-center">
                    <p class="small text-muted mb-3 fw-medium" id="form_pdf_titulo"></p>
                    <div class="p-3 bg-white border border-dashed rounded-4">
                        <input type="file" name="ArchivoTitulo" class="form-control form-control-sm sia-input shadow-none" accept=".pdf" required>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 px-4 pb-4">
                    <button type="submit" class="btn btn-sia-primary w-100 shadow-sm">Actualizar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    function abrirModalPDF(btn) {
        // Extraemos los datos del botón usando Data Attributes
        const id = btn.getAttribute('data-id');
        const titulo = btn.getAttribute('data-titulo');

        // Llenamos el formulario del modal
        document.getElementById('form_pdf_id').value = id;
        document.getElementById('form_pdf_titulo').innerText = "Cargando respaldo para: " + titulo;

        // Disparamos el modal de Bootstrap
        var myModal = new bootstrap.Modal(document.getElementById('modalSubirPDF'));
        myModal.show();
    }
</script>

<style>
    /* VARIABLES Y ESTILOS V4.0 */
    :root {
        --upds-blue: #003566;
        --upds-blue-dark: #001d3d;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
    }

    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-gradient-upds { background: linear-gradient(135deg, var(--upds-blue) 0%, var(--upds-blue-dark) 100%); }
    
    .sia-label-mini { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 0.25rem; }
    .sia-section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: var(--upds-blue); border-bottom: 2px solid #f1f5f9; padding-bottom: 0.75rem; }
    .sia-section-title .icon { color: var(--upds-gold); margin-right: 0.5rem; }

    .sia-info-box { background: var(--gray-50); padding: 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; height: 100%; }
    .sia-info-box label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; display: block; }
    .sia-info-box p { font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 0; }

    .sia-tabs .nav-link { color: #64748b; font-weight: 600; padding: 1rem 1.5rem; border: none; border-bottom: 3px solid transparent; }
    .sia-tabs .nav-link.active { color: var(--upds-blue); border-bottom-color: var(--upds-gold); background: transparent; }

    .btn-sia-primary { background-color: var(--upds-blue); color: white; font-weight: 700; border-radius: 50px; border: none; padding: 0.6rem 1.5rem; transition: all 0.3s; }
    .btn-sia-primary:hover { background-color: var(--upds-blue-dark); transform: translateY(-2px); color: white; }
    .btn-sia-ghost { background: white; border: 1px solid #e2e8f0; color: var(--upds-blue); font-weight: 600; border-radius: 50px; padding: 0.4rem 1rem; font-size: 0.8rem; }
    
    .btn-xs { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; border: none; background: transparent; }
    .hover-bg-red-50:hover { background-color: #fef2f2; }
    .hover-bg-orange-50:hover { background-color: #fff7ed; }
    .object-cover { object-fit: cover; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
</style>
@endsection