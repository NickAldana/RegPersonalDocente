@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-black text-upds-blue mb-0">EDITAR PRODUCCIÓN CIENTÍFICA</h2>
                    <p class="text-muted small fw-bold text-uppercase">Actualización de Registro y Evidencia Digital</p>
                </div>
                <a href="{{ route('publicaciones.index') }}" class="btn btn-light border fw-bold small rounded-pill px-4">
                    <i class="bi bi-x-lg me-2"></i> CANCELAR
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <form action="{{ route('publicaciones.update', $publicacion->PublicacionID) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-5">
                        
                        {{-- SECCIÓN 1: DATOS DE LA OBRA --}}
                        <h6 class="text-xxs fw-bold text-muted text-uppercase mb-4 border-bottom pb-2">1. Detalles Bibliográficos</h6>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <label class="text-xxs fw-bold text-upds-blue text-uppercase">Título de la Publicación</label>
                                <input type="text" name="Nombrepublicacion" class="form-control bg-gray-50 border-0 fw-bold py-2" 
                                       value="{{ $publicacion->Nombrepublicacion }}" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Fecha de Publicación</label>
                                <input type="date" name="Fechapublicacion" class="form-control bg-gray-50 border-0 small" 
                                       value="{{ $publicacion->Fechapublicacion ? $publicacion->Fechapublicacion->format('Y-m-d') : '' }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Tipo de Producción</label>
                                <select name="TipopublicacionID" class="form-select bg-gray-50 border-0 small fw-bold" required>
                                    @foreach($tipos as $t)
                                        <option value="{{ $t->TipopublicacionID }}" {{ $publicacion->TipopublicacionID == $t->TipopublicacionID ? 'selected' : '' }}>
                                            {{ $t->Nombretipo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Medio / Editorial</label>
                                <select name="MediopublicacionID" class="form-select bg-gray-50 border-0 small fw-bold" required>
                                    @foreach($medios as $m)
                                        <option value="{{ $m->MediopublicacionID }}" {{ $publicacion->MediopublicacionID == $m->MediopublicacionID ? 'selected' : '' }}>
                                            {{ $m->Nombremedio }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Línea de Investigación</label>
                                <select name="LineainvestigacionID" class="form-select bg-gray-50 border-0 small fw-bold" required>
                                    @foreach($lineas as $l)
                                        <option value="{{ $l->LineainvestigacionID }}" {{ $publicacion->LineainvestigacionID == $l->LineainvestigacionID ? 'selected' : '' }}>
                                            {{ $l->Nombrelineainvestigacion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: CONTEXTO --}}
                        <div class="bg-blue-soft rounded-3 p-4 mb-5 border border-blue-100">
                            <h6 class="text-xxs fw-bold text-upds-blue text-uppercase mb-3">2. Origen de la Producción</h6>
                            <div class="d-flex gap-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="origen_obra" id="origen_indep" value="indep" 
                                           {{ !$publicacion->ProyectoinvestigacionID ? 'checked' : '' }} onchange="toggleProyecto(false)">
                                    <label class="form-check-label fw-bold text-secondary text-sm" for="origen_indep">
                                        <i class="bi bi-person-badge me-1"></i> Autoría Personal / Independiente
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="origen_obra" id="origen_inst" value="inst" 
                                           {{ $publicacion->ProyectoinvestigacionID ? 'checked' : '' }} onchange="toggleProyecto(true)">
                                    <label class="form-check-label fw-bold text-upds-blue text-sm" for="origen_inst">
                                        <i class="bi bi-bank2 me-1"></i> Vinculada a Proyecto UPDS
                                    </label>
                                </div>
                            </div>

                            <div id="box-proyecto" style="display: {{ $publicacion->ProyectoinvestigacionID ? 'block' : 'none' }};">
                                <div class="bg-white p-3 rounded-3 border border-blue-200 shadow-sm animate__animated animate__fadeIn">
                                    <label class="text-xxs fw-bold text-upds-blue text-uppercase d-block mb-1">Proyecto de Investigación</label>
                                    <select name="ProyectoinvestigacionID" class="form-select border-upds-blue fw-bold small">
                                        <option value="">-- SELECCIONE EL PROYECTO --</option>
                                        @foreach($proyectos as $p)
                                            <option value="{{ $p->ProyectoinvestigacionID }}" {{ $publicacion->ProyectoinvestigacionID == $p->ProyectoinvestigacionID ? 'selected' : '' }}>
                                                [{{ $p->CodigoProyecto }}] {{ Str::limit($p->Nombreproyecto, 80) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: EVIDENCIA --}}
                        <h6 class="text-xxs fw-bold text-muted text-uppercase mb-4 border-bottom pb-2">3. Gestión de Evidencia Digital</h6>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="p-3 bg-gray-50 rounded-3 border border-dashed text-center">
                                    <label class="text-xxs fw-bold text-upds-blue text-uppercase d-block mb-2 text-start">Archivo PDF Actual</label>
                                    @if($publicacion->RutaArchivo)
                                        <div class="alert alert-info py-2 px-3 border-0 d-flex align-items-center mb-3 text-start" style="font-size: 0.75rem;">
                                            <i class="bi bi-file-earmark-pdf-fill fs-4 me-2"></i>
                                            <div>
                                                Documento listo: <a href="{{ asset('storage/' . $publicacion->RutaArchivo) }}" target="_blank" class="fw-bold text-decoration-none">Ver archivo</a>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted small italic text-start mb-2">Sin archivo adjunto</p>
                                    @endif
                                    <input type="file" name="archivo_evidencia" class="form-control form-control-sm" accept=".pdf">
                                    <small class="text-muted text-xxs mt-2 d-block text-start">* Suba uno nuevo para reemplazar el archivo previo.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-gray-50 rounded-3 border border-dashed">
                                    <label class="text-xxs fw-bold text-upds-blue text-uppercase d-block mb-2">Enlace Digital (DOI/URL)</label>
                                    <input type="url" name="UrlPublicacion" class="form-control form-control-sm" 
                                           value="{{ $publicacion->UrlPublicacion }}" placeholder="https://doi.org/...">
                                    <small class="text-muted text-xxs mt-2 d-block">* Link directo al repositorio o sitio oficial.</small>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 4: AUTORES DINÁMICOS --}}
                        <div class="d-flex justify-content-between align-items-end mb-3 border-bottom pb-2">
                            <h6 class="text-xxs fw-bold text-muted text-uppercase mb-0">4. Equipo de Autores</h6>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-xs text-decoration-none" onclick="agregarFilaAutor()">
                                <i class="bi bi-plus-circle-fill me-1"></i> AÑADIR OTRO AUTOR
                            </button>
                        </div>

                        <div id="contenedor-autores">
                            {{-- Se llena mediante JS --}}
                        </div>

                    </div>

                    <div class="card-footer bg-gray-50 p-4 text-end border-top">
                        <button type="submit" class="btn btn-sia-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> ACTUALIZAR REGISTRO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const listaPersonales = @json($personales);
    const listaRoles = @json($roles);
    const autoresExistentes = @json($publicacion->autores);
    
    // Contador alto para nuevos registros dinámicos
    let nextIdx = 1000;

    document.addEventListener('DOMContentLoaded', () => {
        if(autoresExistentes.length > 0) {
            autoresExistentes.forEach((a, index) => agregarFilaAutor(a.PersonalID, a.pivot.RolID, index));
        } else {
            agregarFilaAutor();
        }
    });

    function agregarFilaAutor(idPersonal = null, idRol = null, forcedIdx = null) {
        const contenedor = document.getElementById('contenedor-autores');
        const idx = (forcedIdx !== null) ? forcedIdx : nextIdx++;

        let optsP = '<option value="">-- SELECCIONAR --</option>';
        listaPersonales.forEach(p => {
            const sel = (idPersonal == p.PersonalID) ? 'selected' : '';
            optsP += `<option value="${p.PersonalID}" ${sel}>[${p.CodigoItem ?? 'S/I'}] ${p.Apellidopaterno} ${p.Nombrecompleto}</option>`;
        });

        let optsR = '';
        listaRoles.forEach(r => {
            const sel = (idRol == r.RolID) ? 'selected' : '';
            optsR += `<option value="${r.RolID}" ${sel}>${r.Nombrerol}</option>`;
        });

        const fila = `
            <div class="row g-2 mb-2 fila-autor animate__animated animate__fadeIn">
                <div class="col-md-7">
                    <select name="autores[${idx}]" class="form-select bg-gray-50 border-0 small fw-bold" required>${optsP}</select>
                </div>
                <div class="col-md-4">
                    <select name="roles[${idx}]" class="form-select bg-gray-50 border-0 small fw-bold" required>${optsR}</select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger border-0 w-100 btn-sm" onclick="eliminarFila(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>`;
        contenedor.insertAdjacentHTML('beforeend', fila);
    }

    function eliminarFila(btn) {
        if (document.querySelectorAll('.fila-autor').length > 1) {
            btn.closest('.fila-autor').remove();
        } else {
            alert("Debe existir al menos un autor registrado.");
        }
    }

    function toggleProyecto(mostrar) {
        const box = document.getElementById('box-proyecto');
        const select = box.querySelector('select');
        box.style.display = mostrar ? 'block' : 'none';
        select.required = mostrar;
        if (!mostrar) select.value = "";
    }
</script>

<style>
    .bg-gray-50 { background-color: #f8fafc !important; }
    .bg-blue-soft { background-color: #eef2f7; }
    .text-xxs { font-size: 0.65rem; letter-spacing: 0.5px; }
    .fw-black { font-weight: 900; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    .animate__fadeIn { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection