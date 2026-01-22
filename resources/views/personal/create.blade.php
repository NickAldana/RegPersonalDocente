@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                NUEVO EXPEDIENTE
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-folder-plus me-1 text-upds-gold"></i> Gestión de Talento Humano
            </p>
        </div>
        <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary border-2 rounded-pill px-4 fw-bold text-xs text-uppercase tracking-wide hover-scale">
            <i class="bi bi-x-lg me-2"></i> Cancelar
        </a>
    </div>

    {{-- ALERTAS DE SISTEMA (Estilo V4.0) --}}
    @if (session('success'))
        <div class="alert bg-white border-start border-4 border-success shadow-sm rounded-3 mb-4 d-flex align-items-center p-3 animate__animated animate__fadeIn">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 me-3">
                <i class="bi bi-check-lg fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Operación Exitosa</h6>
                <small class="text-muted">{{ session('success') }}</small>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert bg-white border-start border-4 border-danger shadow-sm rounded-3 mb-4 p-3 animate__animated animate__shakeX">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 me-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <h6 class="fw-bold text-danger mb-0">Se requieren correcciones</h6>
            </div>
            <ul class="mb-0 small text-muted ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO PRINCIPAL --}}
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form action="{{ route('personal.store') }}" method="POST">
                @csrf
                
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    
                    {{-- Card Header Institucional --}}
                    <div class="card-header bg-upds-blue text-white py-4 px-5 border-bottom border-white-10">
                        <div class="d-flex align-items-center">
                            <div class="bg-white/10 rounded-circle p-3 me-3 text-upds-gold">
                                <i class="bi bi-person-lines-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-uppercase tracking-wide">Ficha de Alta de Personal</h5>
                                <small class="text-white-50">Complete los datos obligatorios marcados con asterisco (*)</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        
                        {{-- BLOQUE 1: VINCULACIÓN INSTITUCIONAL --}}
                        <div class="p-5 border-bottom bg-gray-50">
                            <h6 class="text-xs fw-black text-upds-blue text-uppercase tracking-widest mb-4 d-flex align-items-center">
                                <span class="bg-upds-gold rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span>
                                1. Asignación Institucional
                            </h6>
                            
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Carrera / Área <span class="text-danger">*</span></label>
                                    <select name="IdCarrera" class="form-select sia-input fw-bold" required>
                                        <option value="" selected disabled>Seleccionar Unidad...</option>
                                        @foreach($carreras as $carrera)
                                            <option value="{{ $carrera->IdCarrera }}" {{ old('IdCarrera') == $carrera->IdCarrera ? 'selected' : '' }}>
                                                {{ $carrera->NombreCarrera }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Cargo Designado <span class="text-danger">*</span></label>
                                    <select name="IdCargo" class="form-select sia-input fw-bold" required>
                                        <option value="" selected disabled>Definir Cargo...</option>
                                        @foreach($cargos as $cargo)
                                            <option value="{{ $cargo->IdCargo }}" {{ old('IdCargo') == $cargo->IdCargo ? 'selected' : '' }}>
                                                {{ $cargo->NombreCargo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Modalidad Contrato <span class="text-danger">*</span></label>
                                    <select name="IdTipoContrato" class="form-select sia-input" required>
                                        <option value="" selected disabled>Tipo de Vinculación...</option>
                                        @foreach($tiposContrato as $contrato)
                                            <option value="{{ $contrato->IdTipoContrato }}" {{ old('IdTipoContrato') == $contrato->IdTipoContrato ? 'selected' : '' }}>
                                                {{ $contrato->NombreContrato }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE 2: DATOS PERSONALES --}}
                        <div class="p-5 border-bottom bg-white">
                            <h6 class="text-xs fw-black text-upds-blue text-uppercase tracking-widest mb-4 d-flex align-items-center">
                                <span class="bg-upds-gold rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span>
                                2. Información Personal
                            </h6>

                            <div class="row g-4">
                                {{-- Fila 1: Nombres --}}
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" name="NombreCompleto" class="form-control sia-input" value="{{ old('NombreCompleto') }}" required placeholder="Ej: Juan Carlos">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Apellido Paterno <span class="text-danger">*</span></label>
                                    <input type="text" name="ApellidoPaterno" class="form-control sia-input" value="{{ old('ApellidoPaterno') }}" required placeholder="Ej: Pérez">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Apellido Materno</label>
                                    <input type="text" name="ApellidoMaterno" class="form-control sia-input" value="{{ old('ApellidoMaterno') }}" placeholder="Ej: Gómez">
                                </div>

                                {{-- Fila 2: Identificación y Contacto --}}
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Documento de Identidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-50 border-end-0 rounded-start-4 ps-3"><i class="bi bi-card-heading text-muted"></i></span>
                                        <input type="text" name="CI" class="form-control sia-input border-start-0 ps-2 fw-bold text-dark" value="{{ old('CI') }}" required placeholder="Ej: 8877665">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Correo Institucional</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-100 border-end-0 rounded-start-4 ps-3"><i class="bi bi-lock-fill text-muted opacity-50"></i></span>
                                        <input type="text" class="form-control sia-input border-start-0 ps-2 fst-italic text-muted bg-gray-100" value="Autogenerado por sistema" disabled readonly>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Teléfono Móvil</label>
                                    <input type="text" name="Telefono" class="form-control sia-input" value="{{ old('Telefono') }}" placeholder="Ej: 70012345">
                                </div>

                                {{-- Fila 3: Detalles --}}
                                <div class="col-md-6">
                                    <label class="form-label sia-label mb-3">Género</label>
                                    <div class="d-flex gap-4 p-3 bg-gray-50 rounded-4 border border-dashed">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Genero" id="genM" value="Masculino" {{ old('Genero') == 'Masculino' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="genM">Masculino</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Genero" id="genF" value="Femenino" {{ old('Genero') == 'Femenino' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="genF">Femenino</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label sia-label">Experiencia Profesional</label>
                                    <select name="AniosExperiencia" class="form-select sia-input">
                                        <option value="" selected disabled>Seleccionar Rango...</option>
                                        <option value="1 - 4" {{ old('AniosExperiencia') == '1 - 4' ? 'selected' : '' }}>Junior (1 - 4 años)</option>
                                        <option value="5 - 9" {{ old('AniosExperiencia') == '5 - 9' ? 'selected' : '' }}>Semi Senior (5 - 9 años)</option>
                                        <option value="10 - 14" {{ old('AniosExperiencia') == '10 - 14' ? 'selected' : '' }}>Senior (10 - 14 años)</option>
                                        <option value="15 - 19" {{ old('AniosExperiencia') == '15 - 19' ? 'selected' : '' }}>Experto (15 - 19 años)</option>
                                        <option value="+20" {{ old('AniosExperiencia') == '+20' ? 'selected' : '' }}>Master (+20 años)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE 3: FORMACIÓN ACADÉMICA --}}
                        <div class="p-5 bg-gray-50">
                            <h6 class="text-xs fw-black text-upds-blue text-uppercase tracking-widest mb-4 d-flex align-items-center">
                                <span class="bg-upds-gold rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span>
                                3. Formación Académica Base
                            </h6>
                            
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Grado Académico <span class="text-danger">*</span></label>
                                    <select name="IdGradoAcademico" class="form-select sia-input" required>
                                        <option value="" selected disabled>Nivel alcanzado...</option>
                                        <option value="1" {{ old('IdGradoAcademico') == 1 ? 'selected' : '' }}>Licenciatura</option>
                                        <option value="2" {{ old('IdGradoAcademico') == 2 ? 'selected' : '' }}>Diplomado</option>
                                        <option value="3" {{ old('IdGradoAcademico') == 3 ? 'selected' : '' }}>Especialidad</option>
                                        <option value="4" {{ old('IdGradoAcademico') == 4 ? 'selected' : '' }}>Maestría</option>
                                        <option value="5" {{ old('IdGradoAcademico') == 5 ? 'selected' : '' }}>Doctorado</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-5">
                                    <label class="form-label sia-label">Título Profesional Obtenido <span class="text-danger">*</span></label>
                                    <input type="text" name="TituloObtenido" class="form-control sia-input" placeholder="Ej: Lic. en Ingeniería de Sistemas" value="{{ old('TituloObtenido') }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label sia-label">Año de Graduación</label>
                                    <input type="number" name="AñoEstudios" class="form-control sia-input text-center fw-bold text-upds-blue" 
                                           min="1950" max="{{ date('Y') }}" value="{{ old('AñoEstudios', date('Y')) }}" required>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- FOOTER / ACCIONES --}}
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('personal.index') }}" class="btn text-secondary fw-bold text-uppercase text-xs hover-text-dark">Cancelar</a>
                            <button type="submit" class="btn btn-sia-primary shadow-lg">
                                <i class="bi bi-save2-fill me-2"></i> Registrar Expediente
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* SISTEMA DE DISEÑO V4.0 */
    :root {
        --upds-blue: #003566;
        --upds-blue-dark: #001d3d;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
    }

    /* Colores */
    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .bg-upds-gold { background-color: var(--upds-gold) !important; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    .bg-gray-100 { background-color: var(--gray-100) !important; }

    /* Tipografía */
    .fw-black { font-weight: 900; }
    .text-xs { font-size: 0.75rem; }
    .sia-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.025em;
        margin-bottom: 0.35rem;
    }

    /* Inputs Técnicos */
    .sia-input {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.65rem 1rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #334155;
        font-weight: 500;
    }
    
    .sia-input:focus {
        border-color: var(--upds-blue);
        box-shadow: 0 0 0 4px rgba(0, 53, 102, 0.1);
        background-color: white;
    }

    /* Botón Principal */
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
        box-shadow: 0 10px 20px rgba(0, 53, 102, 0.3) !important;
        color: white;
    }

    /* Hover Utilities */
    .hover-scale:hover { transform: scale(1.05); }
    .hover-text-dark:hover { color: #0f172a !important; text-decoration: underline; }

    /* Bordes Especiales */
    .rounded-start-4 { border-top-left-radius: 0.75rem !important; border-bottom-left-radius: 0.75rem !important; }
</style>
@endsection