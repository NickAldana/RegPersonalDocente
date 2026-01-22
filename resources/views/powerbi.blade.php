@extends('layouts.app')

@section('content')
{{-- Contenedor Maestro: Ocupa toda la pantalla y aplica el fondo gris corporativo --}}
<div class="container-fluid p-0 bg-gray-50 d-flex flex-column" style="height: 100vh; overflow: hidden;" id="main-report-layout">

    {{-- 1. HEADER INSTITUCIONAL - ALTA DENSIDAD --}}
    <div class="px-4 py-3 d-flex justify-content-between align-items-center flex-shrink-0 bg-white border-bottom shadow-sm no-print">
        <div>
            <h6 class="text-[10px] fw-black text-muted uppercase tracking-[2px] mb-1">Vicerrectorado Santa Cruz</h6>
            <h3 class="fw-black text-upds-blue mb-0 tracking-tighter d-flex align-items-center">
                <i class="bi bi-briefcase-fill text-upds-gold me-3 fs-4"></i>
                PROFESIÓN Y CONTRATOS
            </h3>
        </div>
        
        <div class="d-flex gap-3">
            {{-- BOTÓN DE IMPRESIÓN INTEGRADO --}}
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm hover-scale d-flex align-items-center">
                <i class="bi bi-printer-fill me-2"></i> IMPRIMIR REPORTE
            </button>

            <button onclick="reloadFrame()" class="btn btn-light border-0 rounded-circle text-upds-blue shadow-sm hover-scale" title="Actualizar Reporte">
                <i class="bi bi-arrow-clockwise fs-5"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-sia-primary-outline rounded-pill px-4 fw-bold hover-scale d-flex align-items-center">
                <i class="bi bi-arrow-left me-2"></i> VOLVER AL PANEL
            </a>
        </div>
    </div>

    {{-- 2. CUERPO DE LA CONSOLA --}}
    <div class="px-4 py-4 flex-grow-1 overflow-hidden main-print-area">
        <div class="card border-0 shadow-2xl rounded-4 overflow-hidden bg-white h-100 d-flex flex-column border-upds-blue-soft print-shadow-none">
            
            {{-- BARRA DE ESTADO EJECUTIVA --}}
            <div class="bg-light border-bottom py-2 px-4 d-flex justify-content-between align-items-center flex-shrink-0 no-print">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-upds-blue text-white px-3 py-2 rounded-pill fw-bold small shadow-sm">
                        <i class="bi bi-shield-lock-fill me-2 text-upds-gold"></i> VISTA EJECUTIVA
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
                <div class="position-absolute top-50 start-50 translate-middle text-center z-0 no-print">
                    <div class="spinner-border text-upds-blue mb-3" style="width: 3.5rem; height: 3.5rem; border-width: 0.25em;" role="status"></div>
                    <h6 class="fw-black text-upds-blue opacity-50 tracking-tighter">PREPARANDO INTELIGENCIA DE NEGOCIOS...</h6>
                </div>

                {{-- Iframe del Reporte --}}
                <iframe 
                    id="powerbi-frame"
                    title="Informe Profesión y Contratos UPDS" 
                    width="100%" 
                    height="100%" 
                    src="https://app.powerbi.com/reportEmbed?reportId=347aeed0-abd7-4069-9ae1-d9b663e683ed&autoAuth=true&ctid=cf263038-f11c-49d2-9183-556a34b747e2" 
                    frameborder="0" 
                    allowFullScreen="true"
                    class="position-relative z-1 w-100 h-100">
                </iframe>
            </div>

            {{-- FOOTER TÉCNICO UPDS --}}
            <div class="bg-upds-blue text-white py-2 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="font-size: 10px;">
                <div class="font-monospace opacity-50">
                    SIA-PBI-ENGINE-V2 • DATA_SOURCE: <strong>SQL_PROD_UPDS</strong>
                </div>
                <div class="fw-black text-upds-gold tracking-widest uppercase">
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
    .text-upds-gold { color: var(--upds-gold) !important; }
    .bg-upds-gold { background-color: var(--upds-gold) !important; }
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

    /* ============================================================
       REGLAS PARA REPORTE FORMAL IMPRESO
       ============================================================ */
    @media print {
        /* Ocultar elementos de navegación de la app (Sidebar, Navbar, etc) */
        aside, nav, .sidebar, .no-print, .navbar {
            display: none !important;
        }

        /* Ajustar el cuerpo para que use el 100% del papel */
        body, html, #main-report-layout {
            height: auto !important;
            overflow: visible !important;
            background: white !important;
        }

        .main-print-area {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: 95vh !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
        }

        /* Forzar que el Iframe sea visible y grande */
        iframe {
            height: 100% !important;
            width: 100% !important;
        }

        /* Configuración de página: Horizontal para Power BI */
        @page {
            size: landscape;
            margin: 0.5cm;
        }
    }
</style>

<script>
    function reloadFrame() {
        const iframe = document.getElementById('powerbi-frame');
        iframe.src = iframe.src;
    }
</script>
@endsection