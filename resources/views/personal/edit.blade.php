@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                EDITAR FICHA DE PERSONAL
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-pencil-square me-1 text-upds-gold"></i> Actualización de Legajo: {{ $personal->PersonalID }}
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
                <h6 class="fw-bold text-danger mb-0">Error en la actualización</h6>
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
            <form action="{{ route('personal.update', $personal->PersonalID) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    
                    {{-- CABECERA CON FOTO (Estilo Perfil) --}}
                    <div class="card-header bg-upds-blue text-white py-5 px-5 border-bottom border-white-10 position-relative overflow-hidden">
                        {{-- Fondo decorativo sutil --}}
                        <div class="position-absolute top-0 end-0 opacity-10 translate-middle-y">
                            <i class="bi bi-person-badge" style="font-size: 15rem;"></i>
                        </div>

                        <div class="d-flex flex-column flex-md-row align-items-center position-relative z-1">
                            {{-- Foto de Perfil --}}
                            <div class="position-relative group-hover-avatar me-md-4 mb-3 mb-md-0">
                                <div class="avatar-container rounded-circle border-4 border-white shadow-lg bg-white overflow-hidden d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    @if($personal->Fotoperfil)
                                        <img src="{{ Storage::url($personal->Fotoperfil) }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <span class="display-4 fw-bold text-upds-blue opacity-50">
                                            {{ substr($personal->Nombrecompleto, 0, 1) }}{{ substr($personal->Apellidopaterno, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Botón flotante para cambiar foto --}}
                                <label for="fotoInput" class="position-absolute bottom-0 end-0 btn btn-sm btn-warning rounded-circle shadow-sm border-2 border-white" style="width: 35px; height: 35px; padding: 0;" title="Cambiar Foto">
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100">
                                        <i class="bi bi-camera-fill text-white"></i>
                                    </div>
                                    <input type="file" name="Fotoperfil" id="fotoInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>

                            <div class="text-center text-md-start">
                                <h4 class="mb-1 fw-black text-uppercase tracking-wide">{{ $personal->Apellidopaterno }} {{ $personal->Apellidomaterno }}</h4>
                                <h5 class="mb-2 fw-light text-uppercase">{{ $personal->Nombrecompleto }}</h5>
                                <span class="badge bg-white/20 text-white border border-white/30 rounded-pill px-3 fw-normal">
                                    {{ $personal->cargo->Nombrecargo ?? 'Sin Cargo Asignado' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        
                        {{-- BLOQUE 1: VINCULACIÓN INSTITUCIONAL --}}
                        <div class="p-5 border-bottom bg-gray-50">
                            <h6 class="text-xs fw-black text-upds-blue text-uppercase tracking-widest mb-4 d-flex align-items-center">
                                <span class="bg-upds-gold rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span>
                                1. Datos Institucionales
                            </h6>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label sia-label">Cargo Actual <span class="text-danger">*</span></label>
                                    <select name="CargoID" class="form-select sia-input fw-bold bg-white" required>
                                        @foreach($cargos as $cargo)
                                            <option value="{{ $cargo->CargoID }}" {{ old('CargoID', $personal->CargoID) == $cargo->CargoID ? 'selected' : '' }}>
                                                {{ $cargo->Nombrecargo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label sia-label">Modalidad Contrato <span class="text-danger">*</span></label>
                                    <select name="TipocontratoID" class="form-select sia-input" required>
                                        @foreach($tiposContrato as $contrato)
                                            <option value="{{ $contrato->TipocontratoID }}" {{ old('TipocontratoID', $personal->TipocontratoID) == $contrato->TipocontratoID ? 'selected' : '' }}>
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
                                    <input type="text" name="Nombrecompleto" class="form-control sia-input" value="{{ old('Nombrecompleto', $personal->Nombrecompleto) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Apellido Paterno <span class="text-danger">*</span></label>
                                    <input type="text" name="Apellidopaterno" class="form-control sia-input" value="{{ old('Apellidopaterno', $personal->Apellidopaterno) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label sia-label">Apellido Materno</label>
                                    <input type="text" name="Apellidomaterno" class="form-control sia-input" value="{{ old('Apellidomaterno', $personal->Apellidomaterno) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Cédula de Identidad <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-50 border-end-0 rounded-start-4 ps-3"><i class="bi bi-card-heading text-muted"></i></span>
                                        <input type="text" name="Ci" class="form-control sia-input border-start-0 ps-2 fw-bold text-dark" value="{{ old('Ci', $personal->Ci) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Correo Institucional</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-50 border-end-0 rounded-start-4 ps-3"><i class="bi bi-envelope-at text-muted"></i></span>
                                        <input type="email" name="Correoelectronico" class="form-control sia-input border-start-0 ps-2 fw-bold text-dark" value="{{ old('Correoelectronico', $personal->Correoelectronico) }}" required>
                                    </div>
                                    <small class="text-[10px] text-muted ms-2"><i class="bi bi-info-circle me-1"></i> Esto actualizará también el usuario de acceso.</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Teléfono Móvil</label>
                                    <input type="text" name="Telelefono" class="form-control sia-input" value="{{ old('Telelefono', $personal->Telelefono) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label mb-3">Género</label>
                                    <div class="d-flex gap-4 p-3 bg-gray-50 rounded-4 border border-dashed">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Genero" id="genM" value="Masculino" {{ old('Genero', $personal->Genero) == 'Masculino' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="genM">Masculino</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Genero" id="genF" value="Femenino" {{ old('Genero', $personal->Genero) == 'Femenino' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="genF">Femenino</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label sia-label">Experiencia (Años)</label>
                                    <input type="text" name="AniosExperiencia" class="form-control sia-input" value="{{ old('AniosExperiencia', $personal->Añosexperiencia) }}" placeholder="Ej: 5">
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE 3: PERFIL ACADÉMICO (Solo datos base de la tabla Personal) --}}
                        <div class="p-5 bg-gray-50">
                            <h6 class="text-xs fw-black text-upds-blue text-uppercase tracking-widest mb-4 d-flex align-items-center">
                                <span class="bg-upds-gold rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></span>
                                3. Nivel Académico Registrado
                            </h6>
                            
                            <div class="row g-4 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label sia-label">Grado Académico Actual <span class="text-danger">*</span></label>
                                    <select name="GradoacademicoID" class="form-select sia-input" required>
                                        @foreach($grados as $grado)
                                            <option value="{{ $grado->GradoacademicoID }}" {{ old('GradoacademicoID', $personal->GradoacademicoID) == $grado->GradoacademicoID ? 'selected' : '' }}>
                                                {{ $grado->Nombregrado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-light border border-slate-200 d-flex align-items-center mb-0 py-2">
                                        <i class="bi bi-mortarboard-fill text-upds-blue fs-4 me-3"></i>
                                        <div>
                                            <small class="d-block text-muted text-[10px] text-uppercase fw-bold">Para actualizar títulos específicos</small>
                                            <span class="text-xs fw-bold">Por favor, utilice el módulo "Formación" en el Kardex.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- FOOTER / ACCIONES --}}
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('personal.index') }}" class="btn text-secondary fw-bold text-uppercase text-xs hover-text-dark">Cancelar Cambios</a>
                            <button type="submit" class="btn btn-warning text-dark fw-black text-uppercase text-xs rounded-pill px-4 py-3 shadow-lg hover-scale">
                                <i class="bi bi-arrow-repeat me-2"></i> Actualizar Expediente
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Estilos V4.0 heredados */
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
    }
    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .bg-white\/20 { background-color: rgba(255, 255, 255, 0.2); }
    .fw-black { font-weight: 900; }
    .text-xs { font-size: 0.75rem; }
    .text-\[10px\] { font-size: 10px; }
    
    .sia-label {
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.025em;
    }
    .sia-input {
        border: 2px solid #e2e8f0; border-radius: 0.75rem; padding: 0.65rem 1rem; color: #334155; font-weight: 500;
    }
    .sia-input:focus {
        border-color: var(--upds-blue); box-shadow: 0 0 0 4px rgba(0, 53, 102, 0.1); background-color: white;
    }
    .hover-scale:hover { transform: scale(1.02); transition: transform 0.2s; }
</style>

<script>
    // Previsualización simple de imagen
    function previewImage(input) {
        if (input.files && input.files[0]) {
            // Aquí podrías agregar lógica JS para cambiar la imagen en el DOM instantáneamente
            // Por ahora es funcional solo con el submit
        }
    }
</script>
@endsection