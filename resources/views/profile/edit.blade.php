@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    {{-- HEADER EJECUTIVO --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 px-2">
        <div class="mb-3 mb-md-0">
            <h1 class="fw-black text-upds-blue mb-0 tracking-tight" style="font-size: 1.75rem;">
                MI PERFIL
            </h1>
            <p class="text-secondary small fw-bold text-uppercase tracking-widest mb-0">
                <i class="bi bi-person-gear me-1 text-upds-gold"></i> Configuración de Cuenta y Datos
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary border-2 rounded-pill px-4 fw-bold text-xs text-uppercase tracking-wide hover-scale">
            <i class="bi bi-x-lg me-2"></i> Cancelar
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert bg-white border-start border-4 border-success shadow-sm rounded-3 mb-4 d-flex align-items-center p-3 animate__animated animate__fadeIn">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 me-3">
                <i class="bi bi-check-lg fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Cambios Guardados</h6>
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
                <h6 class="fw-bold text-danger mb-0">Atención</h6>
            </div>
            <ul class="mb-0 small text-muted ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            {{-- Formulario apuntando a la ruta de actualización --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    
                    <div class="card-body p-0">
                        <div class="row g-0">
                            
                            {{-- COLUMNA IZQUIERDA: IDENTIDAD VISUAL --}}
                            <div class="col-md-4 bg-gray-50 border-end border-light p-5 text-center d-flex flex-column align-items-center">
                                
                                {{-- Avatar con Botón de Edición --}}
                                <div class="position-relative d-inline-block mb-4">
                                    <div class="avatar-container shadow-lg">
                                        {{-- 
                                            NOTA: Usamos 'Fotoperfil' tal como está en la BD V3.1.
                                            Si la ruta en BD es relativa (ej: 'fotos/img.png'), se usa asset('storage/').
                                        --}}
                                        <img id="preview-img" 
                                             src="{{ $docente->Fotoperfil ? asset('storage/' . $docente->Fotoperfil) : 'https://ui-avatars.com/api/?name='.urlencode($docente->Nombrecompleto).'&background=003566&color=ffc300&size=256' }}" 
                                             class="rounded-circle object-cover w-100 h-100"
                                             alt="Avatar">
                                    </div>
                                    
                                    {{-- Botón Flotante Cámara --}}
                                    <label for="fotoInput" class="btn-camera shadow-sm hover-scale" title="Cambiar Fotografía">
                                        <i class="bi bi-camera-fill"></i>
                                        {{-- Input name coincide con la columna 'Fotoperfil' --}}
                                        <input type="file" name="Fotoperfil" id="fotoInput" class="d-none" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(this)">
                                    </label>
                                </div>

                                {{-- Nombre Completo (BD: Nombrecompleto y Apellidopaterno) --}}
                                <h5 class="fw-black text-upds-blue mb-1">
                                    {{ explode(' ', $docente->Nombrecompleto)[0] }} {{ $docente->Apellidopaterno }}
                                </h5>
                                
                                {{-- Cargo (Relación CargoID -> tabla Cargo -> columna Nombrecargo) --}}
                                <p class="text-muted small fw-bold text-uppercase tracking-wider mb-3">
                                    {{ $docente->cargo->Nombrecargo ?? 'Docente' }}
                                </p>

                                <div class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm mb-4">
                                    <i class="bi bi-circle-fill text-success me-2" style="font-size: 8px;"></i> 
                                    {{ $docente->Activo ? 'Cuenta Activa' : 'Inactivo' }}
                                </div>

                                <p class="small text-muted fst-italic px-3">
                                    "La fotografía se utilizará para su credencial digital y documentos oficiales de la Acreditación."
                                </p>
                            </div>

                            {{-- COLUMNA DERECHA: FORMULARIO --}}
                            <div class="col-md-8 p-5">
                                
                                {{-- SECCIÓN 1: DATOS INSTITUCIONALES (READ ONLY) --}}
                                <div class="mb-5">
                                    <h6 class="sia-section-title mb-4">
                                        <span class="icon"><i class="bi bi-building-lock"></i></span> Datos Institucionales
                                    </h6>
                                    
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label sia-label">Nombre Registrado</label>
                                            {{-- Concatenación de Nombrecompleto, Apellidopaterno, Apellidomaterno --}}
                                            <input type="text" class="form-control sia-input bg-gray-50 text-dark fw-bold" 
                                                   value="{{ $docente->Nombrecompleto }} {{ $docente->Apellidopaterno }} {{ $docente->Apellidomaterno }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label sia-label">Documento de Identidad (CI)</label>
                                            {{-- BD: Ci --}}
                                            <input type="text" class="form-control sia-input bg-gray-50 text-dark" value="{{ $docente->Ci }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label sia-label">Tipo de Contrato</label>
                                            {{-- Relación TipocontratoID -> tabla Tipocontrato -> columna Nombrecontrato --}}
                                            <input type="text" class="form-control sia-input bg-gray-50 text-dark" 
                                                   value="{{ $docente->tipoContrato->Nombrecontrato ?? 'No Asignado' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECCIÓN 2: CONTACTO (EDITABLE) --}}
                                <div class="mb-5">
                                    <h6 class="sia-section-title mb-4">
                                        <span class="icon"><i class="bi bi-person-lines-fill"></i></span> Información de Contacto
                                    </h6>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-7">
                                            <label class="form-label sia-label">Correo Institucional <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-envelope text-upds-gold"></i></span>
                                                {{-- BD: Correoelectronico --}}
                                                <input type="email" name="Correoelectronico" class="form-control sia-input border-start-0 ps-2" 
                                                       value="{{ old('Correoelectronico', $docente->Correoelectronico) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label sia-label">Celular / WhatsApp</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-whatsapp text-success"></i></span>
                                                {{-- BD: Telelefono (Nota: Respetamos el error tipográfico de la BD V3.1) --}}
                                                <input type="text" name="Telelefono" class="form-control sia-input border-start-0 ps-2" 
                                                       value="{{ old('Telelefono', $docente->Telelefono) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECCIÓN 3: SEGURIDAD (PASSWORD) --}}
                                <div class="bg-blue-50 p-4 rounded-4 border border-blue-100">
                                    <h6 class="fw-bold text-upds-blue mb-3 d-flex align-items-center">
                                        <i class="bi bi-shield-lock-fill me-2"></i> Seguridad de Acceso
                                    </h6>
                                    <p class="small text-muted mb-3">
                                        Complete estos campos <strong>únicamente</strong> si desea cambiar su contraseña actual.
                                    </p>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <input type="password" name="password" class="form-control sia-input bg-white" placeholder="Nueva Contraseña" autocomplete="new-password">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="password" name="password_confirmation" class="form-control sia-input bg-white" placeholder="Repetir Contraseña" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- FOOTER DE ACCIONES --}}
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <span class="text-muted small fst-italic me-auto d-none d-md-inline">
                                <i class="bi bi-info-circle me-1"></i> Los campos con fondo gris no son editables por el usuario.
                            </span>
                            
                            <button type="submit" class="btn btn-sia-primary shadow-lg px-5">
                                <i class="bi bi-check-circle-fill me-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<style>
    /* VARIABLES LOCALES V4.0 - ESTILOS UPDS */
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --gray-50: #f8fafc;
        --gray-200: #e2e8f0;
        --blue-50: #eff6ff;
        --blue-100: #dbeafe;
    }

    /* Colores */
    .text-upds-blue { color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-gray-50 { background-color: var(--gray-50) !important; }
    .bg-blue-50 { background-color: var(--blue-50) !important; }
    .border-blue-100 { border-color: var(--blue-100) !important; }

    /* Tipografía */
    .fw-black { font-weight: 900; }
    .sia-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
        margin-bottom: 0.35rem;
    }

    /* Títulos de Sección */
    .sia-section-title {
        font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--upds-blue);
        border-bottom: 2px solid var(--gray-200); padding-bottom: 0.5rem;
    }
    .sia-section-title .icon { color: var(--upds-gold); margin-right: 0.5rem; }

    /* Inputs Técnicos */
    .sia-input {
        border: 1px solid var(--gray-200);
        border-radius: 0.6rem;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
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
        padding: 0.8rem 2rem;
        border-radius: 50px;
        border: none;
        transition: all 0.3s;
    }
    .btn-sia-primary:hover {
        background-color: #00284d; /* Azul más oscuro */
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 53, 102, 0.25) !important;
        color: white;
    }

    /* Avatar Styling */
    .avatar-container {
        width: 160px; height: 160px;
        border-radius: 50%;
        border: 5px solid white;
        overflow: hidden;
        background-color: white;
    }
    .object-cover { object-fit: cover; }
    
    .btn-camera {
        position: absolute;
        bottom: 10px; right: 10px;
        background-color: var(--upds-gold);
        color: var(--upds-blue);
        width: 45px; height: 45px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        border: 3px solid white;
        font-size: 1.2rem;
        transition: all 0.2s;
    }
    .btn-camera:hover {
        background-color: #ffca2c;
        transform: scale(1.1);
    }

    .hover-scale:hover { transform: scale(1.05); transition: transform 0.2s; }
</style>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                const img = document.getElementById('preview-img');
                if(img) {
                    img.src = e.target.result;
                }
            }
            
            reader.readAsDataURL(input.files[0]); 
        }
    }
</script>
@endsection