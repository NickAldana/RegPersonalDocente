@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-black text-upds-blue mb-0">REGISTRO DE PRODUCCIÓN CIENTÍFICA</h2>
                    <p class="text-muted small fw-bold text-uppercase">Módulo de Acreditación - Sistema SIA v4.0</p>
                </div>
                <a href="{{ route('publicaciones.index') }}" class="btn btn-light border fw-bold small rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i> VOLVER AL REPOSITORIO
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <form action="{{ route('publicaciones.store') }}" method="POST" enctype="multipart/form-data" id="formPublicacion">
                    @csrf
                    
                    <div class="card-body p-5">
                        
                        <div class="bg-blue-soft rounded-3 p-4 mb-4 border border-blue-100">
                            <h6 class="text-xxs fw-bold text-upds-blue text-uppercase mb-3">1. Origen y Vinculación Académica</h6>
                            <div class="d-flex gap-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="origen_obra" id="origen_indep" value="indep" checked onchange="toggleProyecto(false)">
                                    <label class="form-check-label fw-bold text-secondary text-sm" for="origen_indep">Producción Independiente</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="origen_obra" id="origen_inst" value="inst" onchange="toggleProyecto(true)">
                                    <label class="form-check-label fw-bold text-upds-blue text-sm" for="origen_inst">Vinculada a Proyecto UPDS</label>
                                </div>
                            </div>

                            <div id="box-proyecto" style="display: none;">
                                <div class="bg-white p-3 rounded-3 border border-blue-200 shadow-sm animate__animated animate__fadeIn">
                                    <label class="text-xxs fw-bold text-upds-blue text-uppercase d-block mb-1">Seleccionar Proyecto Vigente</label>
                                    <select name="ProyectoinvestigacionID" class="form-select border-upds-blue fw-bold small" onchange="autocompletarDesdeProyecto(this.value)">
                                        <option value="">-- SELECCIONE EL PROYECTO --</option>
                                        @foreach($proyectos as $p)
                                            <option value="{{ $p->ProyectoinvestigacionID }}">
                                                [{{ $p->CodigoProyecto }}] {{ Str::limit($p->Nombreproyecto, 85) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 text-[10px] text-indigo-500 font-bold">
                                        <i class="bi bi-info-circle-fill"></i> Se importará automáticamente la línea y el equipo de investigación.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="text-xxs fw-bold text-muted text-uppercase mb-4 border-bottom pb-2">2. Detalles Bibliográficos de la Obra</h6>
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <label class="text-xxs fw-bold text-upds-blue text-uppercase">Título de la Publicación</label>
                                <input type="text" name="Nombrepublicacion" class="form-control bg-gray-50 border-0 fw-bold py-2" placeholder="EJ: ESTUDIO DE FACTIBILIDAD..." required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Fecha de Publicación</label>
                                <input type="date" name="Fechapublicacion" id="Fechapublicacion" class="form-control bg-gray-50 border-0 small" max="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Tipo de Producción</label>
                                <select name="TipopublicacionID" class="form-select bg-gray-50 border-0 small fw-bold" required>
                                    <option value="">-- SELECCIONAR --</option>
                                    @foreach($tipos as $t)
                                        <option value="{{ $t->TipopublicacionID }}">{{ $t->Nombretipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Medio / Editorial</label>
                                <select name="MediopublicacionID" class="form-select bg-gray-50 border-0 small fw-bold" required>
                                    <option value="">-- SELECCIONAR --</option>
                                    @foreach($medios as $m)
                                        <option value="{{ $m->MediopublicacionID }}">{{ $m->Nombremedio }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="text-xxs fw-bold text-muted text-uppercase">Línea de Investigación (Campo Obligatorio)</label>
                                <select name="LineainvestigacionID" id="LineainvestigacionID" class="form-select bg-gray-50 border-0 small fw-bold" required>
                                    <option value="">-- SELECCIONAR LÍNEA --</option>
                                    @foreach($lineas as $l)
                                        <option value="{{ $l->LineainvestigacionID }}">{{ $l->Nombrelineainvestigacion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h6 class="text-xxs fw-bold text-muted text-uppercase mb-4 border-bottom pb-2">3. Respaldo y Evidencia Digital</h6>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="p-3 bg-gray-50 rounded-3 border border-dashed">
                                    <label class="text-xxs fw-bold text-upds-blue text-uppercase d-block mb-2">Opción A: Subir PDF</label>
                                    <input type="file" name="archivo_evidencia" class="form-control form-control-sm" accept=".pdf">
                                    <small class="text-muted text-xxs mt-1">* Máximo 10MB.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-gray-50 rounded-3 border border-dashed">
                                    <label class="text-xxs fw-bold text-upds-blue text-uppercase d-block mb-2">Opción B: URL / DOI</label>
                                    <input type="url" name="UrlPublicacion" class="form-control form-control-sm" placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-3 border-bottom pb-2">
                            <h6 class="text-xxs fw-bold text-muted text-uppercase mb-0">4. Equipo de Autores</h6>
                            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-xs text-decoration-none" onclick="agregarFilaAutor()">
                                <i class="bi bi-plus-circle-fill me-1"></i> AÑADIR OTRO AUTOR
                            </button>
                        </div>

                        <div id="contenedor-autores">
                            </div>

                    </div>

                    <div class="card-footer bg-gray-50 p-4 text-end border-top">
                        <button type="submit" class="btn btn-sia-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-check-lg me-2"></i> FINALIZAR REGISTRO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Datos de proyectos inyectados desde el controlador
    const proyectosData = @json($proyectos);
    let countAutores = 0;

    // Al cargar la página, añadir al menos una fila vacía
    document.addEventListener('DOMContentLoaded', () => {
        agregarFilaAutor();
    });

    // 2. Lógica de Autocompletado
    function autocompletarDesdeProyecto(proyectoId) {
        if (!proyectoId) return;

        const proyecto = proyectosData.find(p => p.ProyectoinvestigacionID == proyectoId);
        if (!proyecto) return;

        // Auto-seleccionar Línea de Investigación
        document.getElementById('LineainvestigacionID').value = proyecto.LineainvestigacionID;

        // Limpiar autores actuales y cargar el equipo del proyecto
        const contenedor = document.getElementById('contenedor-autores');
        contenedor.innerHTML = '';
        
        if (proyecto.equipo && proyecto.equipo.length > 0) {
            proyecto.equipo.forEach(miembro => {
                agregarFilaAutor(miembro.PersonalID, 1); // 1 = Rol "Autor" por defecto
            });
        } else {
            agregarFilaAutor();
        }
    }

    // 3. Función para añadir filas de autores
    function agregarFilaAutor(idPersonal = '', idRol = '1') {
        const contenedor = document.getElementById('contenedor-autores');
        
        const fila = document.createElement('div');
        fila.className = 'row g-2 mb-2 fila-autor animate__animated animate__fadeIn';
        
        // Generar opciones de personal
        let optsP = `<option value="">-- SELECCIONAR --</option>`;
        @foreach($personales as $p)
            optsP += `<option value="{{ $p->PersonalID }}" ${idPersonal == {{ $p->PersonalID }} ? 'selected' : ''}>` +
                     `[{{ $p->CodigoItem ?? 'S/I' }}] {{ $p->Apellidopaterno }} {{ $p->Nombrecompleto }}</option>`;
        @endforeach

        // Generar opciones de roles
        let optsR = '';
        @foreach($roles as $r)
            optsR += `<option value="{{ $r->RolID }}" ${idRol == {{ $r->RolID }} ? 'selected' : ''}>{{ $r->Nombrerol }}</option>`;
        @endforeach

        fila.innerHTML = `
            <div class="col-md-7">
                <select name="autores[${countAutores}]" class="form-select bg-gray-50 border-0 small fw-bold" required>${optsP}</select>
            </div>
            <div class="col-md-4">
                <select name="roles[${countAutores}]" class="form-select bg-gray-50 border-0 small fw-bold" required>${optsR}</select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger border-0 w-100 btn-sm" onclick="eliminarFila(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        contenedor.appendChild(fila);
        countAutores++;
    }

    function eliminarFila(btn) {
        if (document.querySelectorAll('.fila-autor').length > 1) {
            btn.closest('.fila-autor').remove();
        } else {
            alert("Debe registrar al menos un autor.");
        }
    }

    function toggleProyecto(mostrar) {
        const box = document.getElementById('box-proyecto');
        const selectProy = document.querySelector('select[name="ProyectoinvestigacionID"]');
        box.style.display = mostrar ? 'block' : 'none';
        selectProy.required = mostrar;

        if (!mostrar) {
            selectProy.value = "";
            document.getElementById('LineainvestigacionID').value = "";
            document.getElementById('contenedor-autores').innerHTML = '';
            agregarFilaAutor();
        }
    }
</script>

<style>
    .bg-gray-50 { background-color: #f8fafc !important; }
    .bg-blue-soft { background-color: #eef2f7; }
    .text-xxs { font-size: 0.65rem; letter-spacing: 0.5px; }
    .fw-black { font-weight: 900; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate__fadeIn { animation: fadeIn 0.3s ease-in-out; }
</style>
@endsection