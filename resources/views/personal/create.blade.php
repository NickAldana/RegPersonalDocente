@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                NUEVO REGISTRO DE PERSONAL
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-folder-plus me-1 text-upds-gold"></i> Gestión de Talento Humano
            </p>
        </div>
        <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary border-2 rounded-pill px-4 fw-bold text-xs text-uppercase tracking-wide hover-scale">
            <i class="bi bi-x-lg me-2"></i> Cancelar
        </a>
    </div>

    {{-- ALERTAS DE SISTEMA --}}
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
            <form action="{{ route('personal.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    
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
                                {{-- CargoID (Coincide con el Controller) --}}
                                <div class="col-md-6">
                                    <label class="form-label sia-label">Cargo Designado <span class="text-danger">*</span></label>
                                    <select name="CargoID" class="form-select sia-input fw-bold bg-white" required>
                                        <option value="" selected disabled>Seleccionar Cargo...</option>
                                        @foreach($cargos as $cargo)
                                            <option value="{{ $cargo->CargoID }}" {{ old('CargoID') == $cargo->CargoID ? 'selected' : '' }}>
                                                {{ $cargo->Nombrecargo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- TipocontratoID (Coincide con el Controller) --}}
                                <div class="col-md-6">
                                    <label class="form-label sia-label">Modalidad Contrato <span class="text-danger">*</span></label>
                                    <select name="TipocontratoID" class="form-select sia-input" required>
                                        <option value="" selected disabled>Tipo de Vinculación...</option>
                                        @foreach($tiposContrato as $contrato)
                                            <option value="{{ $contrato->TipocontratoID }}" {{ old('TipocontratoID') == $contrato->TipocontratoID ? 'selected' : '' }}>
                                                {{ $contrato->Nombrecontrato }}
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
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Nombres <span class="text-danger">*</span></label>
                                    <input type="text" name="Nombrecompleto" class="form-control sia-input" value="{{ old('Nombrecompleto') }}" required placeholder="Ej: Juan Carlos">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Apellido Paterno <span class="text-danger">*</span></label>
                                    <input type="text" name="Apellidopaterno" class="form-control sia-input" value="{{ old('Apellidopaterno') }}" required placeholder="Ej: Pérez">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Apellido Materno</label>
                                    <input type="text" name="Apellidomaterno" class="form-control sia-input" value="{{ old('Apellidomaterno') }}" placeholder="Ej: Gómez">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Cédula de Identidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-50 border-end-0 rounded-start-4 ps-3"><i class="bi bi-card-heading text-muted"></i></span>
                                        {{-- Name ajustado a 'Ci' --}}
                                        <input type="text" name="Ci" class="form-control sia-input border-start-0 ps-2 fw-bold text-dark" value="{{ old('Ci') }}" required placeholder="Ej: 8877665">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Correo Institucional (Preview)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-100 border-end-0 rounded-start-4 ps-3"><i class="bi bi-lock-fill text-muted opacity-50"></i></span>
                                        <input type="text" class="form-control sia-input border-start-0 ps-2 fst-italic text-muted bg-gray-100" value="Se generará automáticamente" disabled readonly>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Teléfono Móvil</label>
                                    {{-- Name ajustado a 'Telelefono' --}}
                                    <input type="text" name="Telelefono" class="form-control sia-input" value="{{ old('Telelefono') }}" placeholder="Ej: 70012345">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label mb-3">Género <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4 p-3 bg-gray-50 rounded-4 border border-dashed">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Genero" id="genM" value="Masculino" {{ old('Genero') == 'Masculino' ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-medium" for="genM">Masculino</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Genero" id="genF" value="Femenino" {{ old('Genero') == 'Femenino' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="genF">Femenino</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Experiencia (Años)</label>
                                    <input type="number" name="AniosExperiencia" class="form-control sia-input" value="{{ old('AniosExperiencia') }}" placeholder="Ej: 5">
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
                                    <select name="GradoacademicoID" class="form-select sia-input" required>
                                        <option value="" selected disabled>Nivel...</option>
                                        @foreach($grados as $grado)
                                            <option value="{{ $grado->GradoacademicoID }}" {{ old('GradoacademicoID') == $grado->GradoacademicoID ? 'selected' : '' }}>
                                                {{ $grado->Nombregrado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-8">
                                    <label class="form-label sia-label">Título Profesional Obtenido <span class="text-danger">*</span></label>
                                    {{-- Name ajustado a 'Tituloobtenido' --}}
                                    <input type="text" name="Tituloobtenido" class="form-control sia-input" placeholder="EJ: LICENCIATURA EN INGENIERÍA DE SISTEMAS" value="{{ old('Tituloobtenido') }}" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label sia-label">Respaldo Digital (Opcional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-file-earmark-pdf-fill text-danger"></i></span>
                                        <input type="file" name="ArchivoTitulo" class="form-control sia-input border-start-0" accept=".pdf">
                                    </div>
                                    <p class="text-[10px] text-muted mt-2 fw-bold uppercase"><i class="bi bi-info-circle me-1"></i> Puede dejar este campo vacío y regularizar el PDF más tarde.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- FOOTER / ACCIONES --}}
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('personal.index') }}" class="btn text-secondary fw-bold text-uppercase text-xs hover-text-dark">Cancelar Registro</a>
                            <button type="submit" class="btn btn-sia-primary shadow-lg">
                                <i class="bi bi-save2-fill me-2"></i> Registrar Docente
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* VARIABLES DE DISEÑO V4.0 */
    :root {
        --upds-blue: #003566;
        --upds-blue-dark: #001d3d;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
    }

    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    .fw-black { font-weight: 900; }
    .text-xs { font-size: 0.75rem; }
    .text-\[10px\] { font-size: 10px; }
    
    .sia-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.025em;
        margin-bottom: 0.35rem;
    }

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

    .hover-scale:hover { transform: scale(1.05); }
    .hover-text-dark:hover { color: #0f172a !important; text-decoration: underline; }
    .rounded-start-4 { border-top-left-radius: 0.75rem !important; border-bottom-left-radius: 0.75rem !important; }
</style>
@endsection