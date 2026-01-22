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
            
            {{-- Botón de Impresión --}}
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
                    
                    {{-- Bloque Foto y Estado --}}
                    <div class="bg-gradient-upds p-5 text-center position-relative">
                        <div class="position-relative d-inline-block mb-3">
                            @if($docente->FotoPerfil)
                                <img src="{{ asset('storage/' . $docente->FotoPerfil) }}" class="rounded-circle border border-4 border-white shadow-md object-cover" width="140" height="140">
                            @else
                                <div class="rounded-circle bg-white text-upds-blue d-flex align-items-center justify-content-center fw-black border border-4 border-upds-gold shadow-md" style="width: 140px; height: 140px; font-size: 3.5rem;">
                                    {{ substr($docente->NombreCompleto, 0, 1) }}
                                </div>
                            @endif
                            
                            {{-- Badge Estado --}}
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

                    {{-- Datos de Contacto Rápidos --}}
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="sia-label-mini">Credenciales de Acceso</label>
                            @if($docente->usuario)
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
            
            {{-- 1. Tarjeta de Vinculación --}}
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
                                                    {{ $carrera->facultad->NombreFacultad ?? '' }}
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

            {{-- 2. Pestañas de Historial --}}
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
                        <li class="nav-item">
                            <button class="nav-link" id="pubs-tab" data-bs-toggle="tab" data-bs-target="#pubs" type="button">
                                <i class="bi bi-book-half me-2"></i> Producción
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
                                @if(Auth::user()->can('gestionar_personal') || Auth::user()->IdPersonal == $docente->IdPersonal)
                                    <button class="btn btn-sm btn-sia-ghost" data-bs-toggle="modal" data-bs-target="#modalFormacion">
                                        <i class="bi bi-plus-circle-fill me-1"></i> Agregar
                                    </button>
                                @endif
                            </div>

                            <div class="list-group shadow-sm rounded-3 overflow-hidden border-0">
                                @forelse($docente->formaciones as $formacion)
                                    <div class="list-group-item p-3 border-start border-4 border-start-upds-gold">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold text-upds-blue mb-1">{{ $formacion->TituloObtenido }}</h6>
                                                <div class="small text-muted mb-1">
                                                    <i class="bi bi-bank2 me-1"></i> {{ $formacion->centroFormacion->NombreCentro ?? 'Institución externa' }}
                                                </div>
                                                <span class="badge bg-gray-200 text-dark border">{{ $formacion->gradoAcademico->NombreGrado ?? 'Grado' }}</span>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-dark fs-5">{{ $formacion->AñoEstudios }}</div>
                                                @if($formacion->RutaArchivo)
                                                    <a href="{{ Storage::url($formacion->RutaArchivo) }}" target="_blank" class="btn btn-xs text-danger hover-bg-red-50 mt-1">
                                                        <i class="bi bi-file-pdf-fill"></i> Ver
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted bg-white">
                                        <i class="bi bi-inbox fs-4 d-block mb-2 opacity-50"></i>
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
                                        <i class="bi bi-arrow-right-circle-fill me-1"></i> Gestionar
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
                                                <td colspan="3" class="text-center py-4 text-muted">No hay carga asignada.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB: PUBLICACIONES --}}
                        <div class="tab-pane fade" id="pubs">
                            <h6 class="fw-bold text-dark mb-3">Investigación y Publicaciones</h6>
                            <div class="row g-3">
                                @forelse($docente->publicaciones as $pub)
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-white border-start border-4 border-start-primary">
                                            <div class="card-body">
                                                <h6 class="fw-bold text-dark mb-2">{{ $pub->NombrePublicacion }}</h6>
                                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                                    <span><i class="bi bi-tag-fill me-1"></i> {{ $pub->tipoPublicacion->NombreTipo ?? 'Publicación' }}</span>
                                                    <span><i class="bi bi-calendar3 me-1"></i> {{ $pub->FechaPublicacion ? \Carbon\Carbon::parse($pub->FechaPublicacion)->format('Y') : '' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4 text-muted bg-white rounded-3 border border-dashed">
                                        No se encontraron publicaciones.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL AGREGAR TÍTULO (Estilo V4.0) --}}
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
                    <button type="submit" class="btn btn-sia-primary rounded-pill px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* VARIABLES Y ESTILOS V4.0 */
    :root {
        --upds-blue: #003566;
        --upds-blue-dark: #001d3d;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --blue-50: #eff6ff;
        --blue-100: #dbeafe;
    }

    /* Colores y Fondos */
    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    .bg-blue-50 { background-color: var(--blue-50) !important; }
    .border-blue-100 { border-color: var(--blue-100) !important; }
    .bg-gradient-upds { background: linear-gradient(135deg, var(--upds-blue) 0%, var(--upds-blue-dark) 100%); }
    
    .bg-green-500 { background-color: #22c55e; }
    .bg-red-500 { background-color: #ef4444; }
    .bg-green-50 { background-color: #f0fdf4; }
    .border-green-100 { border-color: #dcfce7; }

    /* Bordes */
    .border-start-upds-gold { border-left-color: var(--upds-gold) !important; }

    /* Tipografía */
    .fw-black { font-weight: 900; }
    .sia-label-mini { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 0.25rem; letter-spacing: 0.05em; }
    
    .sia-section-title {
        font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--upds-blue);
        border-bottom: 2px solid var(--gray-100); padding-bottom: 0.75rem;
    }
    .sia-section-title .icon { color: var(--upds-gold); margin-right: 0.5rem; }

    /* Componentes */
    .sia-info-box { background: var(--gray-50); padding: 1rem; border-radius: 0.75rem; border: 1px solid var(--gray-200); height: 100%; }
    .sia-info-box label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; display: block; margin-bottom: 0.25rem; }
    .sia-info-box p { font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 0; }

    .sia-input { border: 1px solid var(--gray-200); border-radius: 0.5rem; font-size: 0.9rem; padding: 0.6rem 1rem; }
    .sia-input:focus { border-color: var(--upds-blue); box-shadow: 0 0 0 3px rgba(0, 53, 102, 0.1); }

    /* Tabs Personalizados */
    .sia-tabs .nav-link {
        color: #64748b; font-weight: 600; font-size: 0.9rem; padding: 1rem 1.5rem; border: none; border-bottom: 3px solid transparent; transition: all 0.2s;
    }
    .sia-tabs .nav-link:hover { color: var(--upds-blue); background-color: var(--gray-50); }
    .sia-tabs .nav-link.active { color: var(--upds-blue); border-bottom-color: var(--upds-gold); background-color: transparent; }

    /* Botones */
    .btn-sia-primary { background-color: var(--upds-blue); color: white; font-weight: 700; border-radius: 50px; padding: 0.6rem 1.5rem; border: none; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.3s; }
    .btn-sia-primary:hover { background-color: var(--upds-blue-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); color: white; }
    
    .btn-sia-ghost { background-color: white; border: 1px solid var(--gray-200); color: var(--upds-blue); font-weight: 600; border-radius: 50px; padding: 0.4rem 1rem; font-size: 0.8rem; transition: all 0.2s; }
    .btn-sia-ghost:hover { border-color: var(--upds-blue); background-color: var(--blue-50); }

    .hover-scale:hover { transform: scale(1.05); transition: transform 0.2s; }
    .object-cover { object-fit: cover; }
</style>
@endsection