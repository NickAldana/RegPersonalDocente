@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                PROGRAMACIÓN ACADÉMICA
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-layers-fill me-1 text-upds-gold"></i> Gestión 2026
            </p>
        </div>
        <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary border-2 rounded-pill px-4 fw-bold text-xs text-uppercase tracking-wide hover-scale">
            <i class="bi bi-arrow-left me-2"></i> Directorio
        </a>
    </div>

    <div class="row g-4">
        
        {{-- COLUMNA 1: SELECTOR DE MATERIAS (PANEL LATERAL OPTIMIZADO) --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card sia-panel border-0 shadow-lg h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="fw-bold text-upds-blue text-uppercase tracking-wide mb-3" style="font-size: 0.75rem;">
                        1. Catálogo de Asignaturas
                    </h6>
                    
                    {{-- BUSCADOR MATERIAS --}}
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-upds-blue opacity-50"></i>
                        <input type="text" id="buscadorMateria" class="form-control sia-input-search ps-5" placeholder="Filtrar materia..." autocomplete="off">
                    </div>
                    <div class="mt-2 text-end">
                        <small id="contadorMaterias" class="text-muted text-xs fw-bold">Cargando catálogo...</small>
                    </div>
                </div>

                <div class="card-body p-0 bg-light">
                    {{-- FORMULARIO OCULTO PARA EL SUBMIT --}}
                    <form action="{{ route('carga.create') }}" method="GET" id="formMateriaSelector" class="h-100">
                        @if(request('docente_id'))
                            <input type="hidden" name="docente_id" value="{{ request('docente_id') }}">
                        @endif
                        
                        {{-- CONTENEDOR DE LA LISTA (Se llena con JS) --}}
                        <div class="custom-scrollbar h-100 p-2" style="max-height: 600px; overflow-y: auto;" id="contenedorMaterias">
                            {{-- Aquí JavaScript inyectará las tarjetas --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA 2: CONFIGURACIÓN Y DOCENTE (ÁREA DE TRABAJO) --}}
        <div class="col-lg-8 col-xl-9">
            <form action="{{ route('carga.store') }}" method="POST">
                @csrf
                {{-- Input oculto que recibe el ID seleccionado desde el JS --}}
                <input type="hidden" name="IdMateria" id="inputMateriaId" value="{{ $materia_id }}">

                {{-- PANEL SUPERIOR: CONFIGURACIÓN --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-blue-50 text-upds-blue rounded-circle p-2 me-3">
                                <i class="bi bi-sliders2 fs-5"></i>
                            </div>
                            <h6 class="fw-bold text-upds-blue text-uppercase tracking-wide mb-0" style="font-size: 0.8rem;">
                                2. Configuración del Periodo
                            </h6>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Gestión</label>
                                <input type="number" name="Gestion" class="form-control sia-input fw-black text-upds-blue fs-5" value="2026" readonly>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted text-uppercase">Periodo Académico</label>
                                <select name="Periodo" class="form-select sia-input fw-bold text-dark" required>
                                    <option value="I">Semestre I (Regular)</option>
                                    <option value="II">Semestre II (Regular)</option>
                                    <option value="Invierno">Curso de Invierno</option>
                                    <option value="Verano">Curso de Verano</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PANEL PRINCIPAL: SELECCIÓN DOCENTE --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative">
                    
                    {{-- Overlay de Bloqueo si no hay materia --}}
                    @if(!$materia_id)
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 z-2 d-flex flex-column align-items-center justify-content-center backdrop-blur-sm">
                        <div class="text-center animate__animated animate__fadeInUp">
                            <i class="bi bi-arrow-left-circle-fill text-upds-blue display-3 opacity-50 mb-3"></i>
                            <h4 class="fw-bold text-upds-blue">Seleccione una Asignatura</h4>
                            <p class="text-muted">Debe elegir una materia del panel izquierdo para asignar un docente.</p>
                        </div>
                    </div>
                    @endif

                    <div class="card-header bg-upds-blue text-white py-4 px-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                             <div class="bg-white/10 rounded-circle p-2 me-3 text-upds-gold">
                                <i class="bi bi-person-video3 fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-uppercase tracking-wide">3. Asignación Docente</h6>
                                <small class="text-white-50">Seleccione el profesional encargado</small>
                            </div>
                        </div>
                        
                        {{-- Buscador Docente --}}
                        <div class="w-50">
                            <div class="position-relative">
                                <input type="text" id="buscadorDocente" class="form-control border-0 rounded-pill bg-white bg-opacity-10 text-white placeholder-white-50 ps-4 pe-4" placeholder="Buscar por nombre o apellido...">
                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-white-50"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0 bg-gray-50">
                        {{-- Alertas --}}
                        @if(session('info') || session('error'))
                            <div class="p-4 pb-0">
                                @if(session('info'))
                                    <div class="alert alert-primary border-0 border-start border-4 border-primary shadow-sm bg-white">
                                        <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger border-0 border-start border-4 border-danger shadow-sm bg-white">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- LISTA DE DOCENTES (GRID) --}}
                        <div class="custom-scrollbar p-4" style="max-height: 450px; overflow-y: auto;" id="listaDocentes">
                            <div class="row g-3">
                                @foreach($docentes as $docente)
                                    <div class="col-md-6 item-docente">
                                        <label class="sia-docente-card h-100 d-flex align-items-center p-3 rounded-4 bg-white border cursor-pointer position-relative overflow-hidden transition-all">
                                            
                                            <input type="radio" name="IdPersonal" value="{{ $docente->IdPersonal }}" 
                                                   class="sia-radio-hidden"
                                                   {{ (request('docente_id') == $docente->IdPersonal) ? 'checked' : '' }}
                                                   required>
                                            
                                            {{-- Estado de Selección --}}
                                            <div class="selection-border"></div>

                                            {{-- Avatar --}}
                                            <div class="me-3 position-relative z-1">
                                                @if($docente->FotoPerfil)
                                                    <img src="{{ asset('storage/'.$docente->FotoPerfil) }}" class="rounded-circle border border-2 border-gray-100 shadow-sm" width="56" height="56" style="object-fit: cover;" loading="lazy">
                                                @else
                                                    <div class="rounded-circle bg-gray-100 text-upds-blue d-flex align-items-center justify-content-center fw-bold border border-2 border-gray-200" style="width: 56px; height: 56px; font-size: 1.2rem;">
                                                        {{ substr($docente->NombreCompleto, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1 z-1 position-relative">
                                                <div class="fw-bold text-dark docente-nombre lh-1 mb-1">
                                                    {{ $docente->ApellidoPaterno }} {{ $docente->ApellidoMaterno }}
                                                </div>
                                                <div class="text-secondary small fw-semibold mb-1">
                                                    {{ $docente->NombreCompleto }}
                                                </div>
                                                
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <span class="badge bg-blue-50 text-upds-blue border border-blue-100 fw-bold px-2 py-1" style="font-size: 0.65rem;">
                                                        {{ $docente->cargo->NombreCargo ?? 'EXTERNO' }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Check Icon --}}
                                            <div class="check-icon position-absolute top-50 end-0 translate-middle-y me-3 opacity-0 transition-all">
                                                <i class="bi bi-check-circle-fill text-upds-gold fs-3"></i>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-flex justify-content-end align-items-center">
                            @if($materia_id)
                                {{-- Buscamos el nombre de la materia seleccionada en JS o PHP para mostrarlo --}}
                                @php $matSel = $materias->firstWhere('IdMateria', $materia_id); @endphp
                                <div class="text-muted small me-4 d-none d-md-block">
                                    <i class="bi bi-info-circle me-1"></i> Asignando a: <strong>{{ $matSel ? $matSel->NombreMateria : '...' }}</strong>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-sia-assign shadow-lg" {{ !$materia_id ? 'disabled' : '' }}>
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-save-fill me-2 fs-5"></i>
                                    <span class="text-uppercase tracking-wider">Confirmar Carga</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ESTILOS V4.0 ESPECÍFICOS --}}
{{-- ESTILOS V4.0 ESPECÍFICOS --}}
<style>
    /* ... (Mantén tus estilos CSS igual que antes, no hace falta cambiarlos) ... */
    :root { --upds-blue: #003566; --upds-gold: #ffc300; --upds-gray-light: #f8fafc; }
    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .bg-blue-50 { background-color: #eff6ff !important; }
    .bg-gray-50 { background-color: #f9fafb !important; }
    .border-blue-100 { border-color: #dbeafe !important; }
    .sia-input { background-color: #f1f5f9; border: 2px solid transparent; border-radius: 0.75rem; transition: all 0.2s; }
    .sia-input:focus { background-color: white; border-color: var(--upds-blue); box-shadow: 0 0 0 4px rgba(0, 53, 102, 0.1); }
    .sia-input-search { border-radius: 50px; background-color: #f8fafc; border: 1px solid #e2e8f0; }
    .sia-list-item { transition: all 0.2s ease; border: 1px solid transparent; }
    .sia-list-item:hover { background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-color: #e2e8f0; }
    .sia-list-item.active { background-color: var(--upds-blue); color: white !important; box-shadow: 0 4px 12px rgba(0, 53, 102, 0.25); }
    .sia-list-item.active .text-dark { color: white !important; }
    .sia-list-item.active .text-muted { color: rgba(255,255,255,0.7) !important; }
    .sia-indicator { width: 8px; height: 8px; border-radius: 50%; background-color: #cbd5e1; }
    .sia-list-item.active .sia-indicator { background-color: var(--upds-gold); box-shadow: 0 0 0 2px rgba(255, 195, 0, 0.5); }
    .sia-docente-card { border-color: #f1f5f9; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    .sia-docente-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border-color: #e2e8f0; }
    .sia-radio-hidden { display: none; }
    .sia-radio-hidden:checked + .selection-border { position: absolute; inset: 0; border: 2px solid var(--upds-blue); border-radius: 1rem; background-color: rgba(0, 53, 102, 0.02); }
    .sia-radio-hidden:checked ~ .check-icon { opacity: 1 !important; transform: translate(-50%, -50%) scale(1.1); }
    .sia-radio-hidden:checked ~ div .docente-nombre { color: var(--upds-blue) !important; }
    .btn-sia-assign { background-color: var(--upds-gold); color: var(--upds-blue); font-weight: 900; padding: 0.75rem 2.5rem; border-radius: 50px; border: none; transition: all 0.3s; }
    .btn-sia-assign:hover:not(:disabled) { background-color: #ffb700; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 195, 0, 0.3) !important; }
    .btn-sia-assign:disabled { background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
    .cursor-pointer { cursor: pointer; }
    .fw-black { font-weight: 900; }
    .text-xs { font-size: 0.75rem; }
    .backdrop-blur-sm { backdrop-filter: blur(4px); }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

{{-- 
    TRUCO TÉCNICO: CARGA DE DATOS SIN ERRORES EN VS CODE
    Colocamos los datos PHP en scripts tipo JSON. El navegador los ignora visualmente,
    VS Code no los valida como JS, y nosotros los leemos limpios después.
--}}
<script type="application/json" id="data-materias">
    {!! json_encode($materias) !!}
</script>

<script type="application/json" id="data-materia-activa">
    {!! json_encode($materia_id) !!}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. LEER DATOS DEL DOM (Adiós errores rojos)
        const rawMaterias = document.getElementById('data-materias').textContent;
        const rawActiva = document.getElementById('data-materia-activa').textContent;
        
        const catalogoMaterias = JSON.parse(rawMaterias);
        const materiaActivaId = JSON.parse(rawActiva);

        // 2. RENDERIZADO DINÁMICO
        const inputMateria = document.getElementById('buscadorMateria');
        const contenedor = document.getElementById('contenedorMaterias');
        // const contadorLabel = document.getElementById('contadorMaterias'); // Descomentar si usas el contador

        function renderizarMaterias(filtro = '') {
            // Optimización: Usamos un array temporal antes de insertar en el DOM
            let htmlBuffer = '';
            const limiteVisual = 50; 

            const materiasFiltradas = catalogoMaterias.filter(m => 
                m.NombreMateria.toLowerCase().includes(filtro.toLowerCase())
            );

            materiasFiltradas.slice(0, limiteVisual).forEach(materia => {
                const isActive = (materiaActivaId == materia.IdMateria) ? 'active' : '';
                const isChecked = (materiaActivaId == materia.IdMateria) ? 'checked' : '';
                const siglaHtml = materia.Sigla ? `<div class="text-muted item-subtitle mt-1"><i class="bi bi-hash me-1"></i>${materia.Sigla}</div>` : '';
                const checkIcon = (materiaActivaId == materia.IdMateria) ? `<i class="bi bi-check-circle-fill text-upds-gold fs-5 ms-2 animate__animated animate__zoomIn"></i>` : '';
                
                htmlBuffer += `
                <label class="sia-list-item d-flex align-items-center p-3 mb-2 rounded-3 cursor-pointer item-materia ${isActive}" onclick="seleccionarMateria(${materia.IdMateria})">
                    <input type="radio" name="IdMateriaRadio" class="d-none" ${isChecked}>
                    <div class="sia-indicator me-3"></div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark item-title lh-sm">${materia.NombreMateria}</div>
                        ${siglaHtml}
                    </div>
                    ${checkIcon}
                </label>`;
            });

            if(materiasFiltradas.length === 0) {
                htmlBuffer = `<div class="text-center p-4 text-muted"><small>No se encontraron materias.</small></div>`;
            }

            contenedor.innerHTML = htmlBuffer;
        }

        renderizarMaterias();

        inputMateria.addEventListener('keyup', function() {
            renderizarMaterias(this.value);
        });

        window.seleccionarMateria = function(id) {
            const form = document.getElementById('formMateriaSelector');
            const hiddenInput = document.getElementById('inputMateriaId'); // Asegúrate que este ID exista en tu HTML (Columna 1)
            
            // Si el input hidden no existe en el form izquierdo, lo creamos
            let input = form.querySelector('input[name="IdMateria"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'IdMateria';
                form.appendChild(input);
            }
            
            input.value = id;
            form.submit();
        };

        // 3. FILTRO DOCENTES
        const inputDocente = document.getElementById('buscadorDocente');
        if(inputDocente) {
            const itemsDocente = document.getElementsByClassName('item-docente');
            inputDocente.addEventListener('keyup', function() {
                const texto = this.value.toLowerCase();
                for (let i = 0; i < itemsDocente.length; i++) {
                    const item = itemsDocente[i];
                    if (item.innerText.toLowerCase().includes(texto)) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                }
            });
        }
    });
</script>
@endsection