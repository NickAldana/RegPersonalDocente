@extends('layouts.app')

@section('content')
{{-- Contenedor Maestro: Ocupa toda la pantalla y aplica el fondo gris corporativo --}}
<div class="container-fluid p-0 bg-gray-50 d-flex flex-column" style="height: 100vh; overflow: hidden;">

    {{-- 1. HEADER INSTITUCIONAL - ALTA DENSIDAD --}}
    <div class="px-4 py-3 d-flex justify-content-between align-items-center flex-shrink-0 bg-white border-bottom shadow-sm">
        <div>
            <h6 class="text-[10px] fw-black text-muted uppercase tracking-[2px] mb-1">Vicerrectorado Académico</h6>
            <h3 class="fw-black text-upds-blue mb-0 tracking-tighter d-flex align-items-center">
                <i class="bi bi-mortarboard-fill text-cyan-500 me-3 fs-4"></i>
                GRADOS ACADÉMICOS
            </h3>
        </div>
        
        <div class="d-flex gap-3">
            <button onclick="reloadFrame()" class="btn btn-light border-0 rounded-circle text-upds-blue shadow-sm hover-scale" title="Actualizar Reporte">
                <i class="bi bi-arrow-clockwise fs-5"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-sia-primary-outline rounded-pill px-4 fw-bold hover-scale d-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i> VOLVER AL PANEL
            </a>
        </div>
    </div>

    {{-- 2. ÁREA DEL REPORTE (Ajustada al formato de consola) --}}
    <div class="px-4 py-4 flex-grow-1 overflow-hidden">
        <div class="card border-0 shadow-2xl rounded-4 overflow-hidden bg-white h-100 d-flex flex-column border-upds-blue-soft">
            
            {{-- BARRA DE ESTADO EJECUTIVA --}}
            <div class="bg-light border-bottom py-2 px-4 d-flex justify-content-between align-items-center flex-shrink-0">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-upds-blue text-white px-3 py-2 rounded-pill fw-bold small shadow-sm">
                        <i class="bi bi-bar-chart-fill me-2 text-cyan-400"></i> ANÁLISIS DE FORMACIÓN
                    </span>
                    <div class="vr h-50 my-auto text-muted opacity-25"></div>
                    <span class="text-[11px] fw-bold text-muted uppercase tracking-widest">
                        <i class="bi bi-database-fill-check me-1 text-success"></i> Sincronización Live
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="sia-ui-pulse me-2 bg-success"></div>
                    <span class="text-[10px] fw-black text-success uppercase">Servicio Conectado</span>
                </div>
            </div>

            {{-- CONTENEDOR DEL INFORME (PBI EMBED) --}}
            <div class="position-relative flex-grow-1 bg-white">
                {{-- Loader Visual Institucional --}}
                <div class="position-absolute top-50 start-50 translate-middle text-center z-0">
                    <div class="spinner-border text-upds-blue mb-3" style="width: 3.5rem; height: 3.5rem; border-width: 0.25em;" role="status"></div>
                    <h6 class="fw-black text-upds-blue opacity-50 tracking-tighter">PREPARANDO INTELIGENCIA ACADÉMICA...</h6>
                </div>

                {{-- Iframe del Reporte 1 --}}
                <iframe 
                    id="powerbi-frame"
                    title="Dashboard Grados UPDS" 
                    width="100%" 
                    height="100%" 
                    src="https://app.powerbi.com/reportEmbed?reportId=6f3c50ce-b969-4cc1-8cd3-bb4a3507202d&autoAuth=true&ctid=cf263038-f11c-49d2-9183-556a34b747e2" 
                    frameborder="0" 
                    allowFullScreen="true"
                    class="position-relative z-1 w-100 h-100">
                </iframe>
            </div>

            {{-- FOOTER TÉCNICO UPDS --}}
            <div class="bg-upds-blue text-white py-2 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="font-size: 10px;">
                <div class="font-monospace opacity-50">
                    SIA-PBI-ENGINE-V2 • SOURCE: <strong>SQL_ACADEMICO_PROD</strong>
                </div>
                <div class="fw-black text-cyan-400 tracking-widest uppercase">
                    Vicerrectorado Académico • Sede Santa Cruz
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* VARIABLES DE IDENTIDAD UPDS */
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --upds-blue-dark: #001d3d;
    }

    /* Clases de Color Institucional */
    .text-upds-blue { color: var(--upds-blue) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .text-cyan-400 { color: #22d3ee; }
    .text-cyan-500 { color: #06b6d4; }
    .border-upds-blue-soft { border: 1px solid rgba(0, 53, 102, 0.1) !important; }

    /* Tipografía Ejecutiva */
    .fw-black { font-weight: 900; }
    .text-\[10px\] { font-size: 10px; }
    .text-\[11px\] { font-size: 11px; }

    /* Botones Personalizados */
    .btn-sia-primary-outline {
        border: 2px solid var(--upds-blue);
        color: var(--upds-blue);
        background: transparent;
        transition: all 0.3s ease;
    }
    .btn-sia-primary-outline:hover {
        background-color: var(--upds-blue);
        color: white;
    }

    /* Efectos y Sombras */
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }

    /* Pulso de Estado */
    .sia-ui-pulse {
        width: 8px; height: 8px;
        border-radius: 50%;
        animation: sia-pulse-glow 2s infinite;
    }

    @keyframes sia-pulse-glow {
        0% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(21, 128, 61, 0); }
        100% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0); }
    }
</style>

<script>
    function reloadFrame() {
        const iframe = document.getElementById('powerbi-frame');
        iframe.src = iframe.src;
    }
</script>
@endsection@extends('layouts.app')

@section('content')
{{-- Contenedor Maestro sin rellenos para ganar espacio --}}
<div class="container-fluid p-0 bg-gray-900 d-flex flex-column" style="height: 100vh; overflow: hidden;">

    {{-- 1. HEADER COMPACTO - Gana espacio vertical --}}
    <div class="px-4 py-2 d-flex justify-content-between align-items-center flex-shrink-0 bg-white border-bottom shadow-sm">
        <div class="d-flex align-items-center">
            <i class="bi bi-mortarboard-fill text-cyan-500 me-3 fs-4"></i>
            <div>
                <h4 class="fw-black text-upds-blue mb-0 tracking-tighter" style="font-size: 1.1rem;">GRADOS ACADÉMICOS</h4>
                <p class="text-[9px] fw-bold text-muted uppercase m-0 tracking-widest">Vicerrectorado • Análisis Live</p>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button onclick="reloadFrame()" class="btn btn-light btn-sm rounded-pill border shadow-sm text-upds-blue fw-bold" title="Refrescar Datos">
                <i class="bi bi-arrow-clockwise me-1"></i> ACTUALIZAR
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-sia-primary-outline btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> VOLVER AL PANEL
            </a>
        </div>
    </div>

    {{-- 2. ÁREA DEL REPORTE - MÁXIMA EXPANSIÓN --}}
    {{-- Eliminamos el card y el padding para que el reporte "respire" --}}
    <div class="flex-grow-1 position-relative bg-white">
        
        {{-- Loader Visual --}}
        <div class="position-absolute top-50 start-50 translate-middle text-center z-0">
            <div class="spinner-border text-upds-blue mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h6 class="fw-black text-upds-blue opacity-50 small tracking-widest">EXPANDIENDO INTELIGENCIA ACADÉMICA...</h6>
        </div>

        {{-- Iframe con clase de expansión total --}}
        <iframe 
            id="powerbi-frame"
            title="Dashboard Grados UPDS" 
            src="https://app.powerbi.com/reportEmbed?reportId=6f3c50ce-b969-4cc1-8cd3-bb4a3507202d&autoAuth=true&ctid=cf263038-f11c-49d2-9183-556a34b747e2" 
            frameborder="0" 
            allowFullScreen="true"
            class="position-relative z-1 w-100 h-100"
            style="display: block; border: none;">
        </iframe>
    </div>

    {{-- 3. FOOTER TÉCNICO ULTRA COMPACTO --}}
    <div class="bg-upds-blue text-white py-1 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="font-size: 9px;">
        <div class="font-monospace opacity-50">
            SOURCE: SQL_ACADEMICO_PROD • ENGINE: V2.1
        </div>
        <div class="fw-black text-upds-gold tracking-widest uppercase">
            Sede Santa Cruz • <i class="bi bi-shield-fill-check text-success"></i> Acceso Seguro
        </div>
    </div>
</div>

<style>
    /* VARIABLES UPDS */
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
    }

    .text-upds-blue { color: var(--upds-blue) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    .text-cyan-500 { color: #06b6d4; }

    .fw-black { font-weight: 900; }
    .text-\[9px\] { font-size: 9px; }

    .btn-sia-primary-outline {
        border: 1.5px solid var(--upds-blue);
        color: var(--upds-blue);
        background: transparent;
        transition: all 0.2s ease;
    }
    .btn-sia-primary-outline:hover {
        background-color: var(--upds-blue);
        color: white;
    }

    /* Forzar que el layout ocupe todo el alto disponible sin barras laterales del navegador */
    body, html { 
        height: 100%; 
        margin: 0; 
        overflow: hidden !important; 
    }
</style>

<script>
    function reloadFrame() {
        const iframe = document.getElementById('powerbi-frame');
        iframe.src = iframe.src;
    }
</script>
@endsection