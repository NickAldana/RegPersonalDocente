@extends('layouts.app')

@section('content')
{{-- 
    CONTENEDOR MAESTRO MEJORADO 
    Usamos 'h-screen' en lugar de cálculos fijos para asegurar que cubra toda la pantalla.
    El 'pb-20' da espacio abajo para que no se corte en laptops.
--}}
<div class="d-flex flex-column h-screen bg-slate-50 overflow-hidden">

    {{-- 1. HEADER INSTITUCIONAL --}}
    <div class="px-4 py-3 d-flex justify-content-between align-items-center flex-shrink-0 bg-white border-bottom shadow-sm no-print z-10 relative">
        <div>
            <h6 class="text-[10px] fw-black text-slate-400 uppercase tracking-[2px] mb-1">Dirección de Acreditación • Santa Cruz</h6>
            <h3 class="fw-black text-upds-blue mb-0 tracking-tighter d-flex align-items-center">
                <i class="bi bi-mortarboard-fill text-upds-gold me-3 fs-4"></i>
                ACREDITACIÓN EDUCATIVA
            </h3>
        </div>
        
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 btn-sm fw-bold shadow-sm hover-scale d-flex align-items-center">
                <i class="bi bi-printer-fill me-2"></i> IMPRIMIR
            </button>

            <button onclick="reloadFrame()" class="btn btn-light border rounded-circle text-upds-blue btn-sm shadow-sm hover-scale" title="Actualizar Reporte">
                <i class="bi bi-arrow-clockwise fs-5"></i>
            </button>
            
            <a href="{{ route('dashboard') }}" class="btn btn-sia-primary-outline rounded-pill px-4 btn-sm fw-bold hover-scale d-flex align-items-center">
                <i class="bi bi-house-door me-2"></i> INICIO
            </a>
        </div>
    </div>

    {{-- 2. CUERPO DEL VISOR (ÁREA DEL REPORTE) --}}
    {{-- 'flex-grow-1' hace que ocupe todo el espacio restante disponible --}}
    <div class="flex-grow-1 p-3 p-md-4 overflow-hidden position-relative">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white h-100 d-flex flex-column">
            
            {{-- BARRA DE ESTADO --}}
            <div class="bg-slate-50 border-bottom py-2 px-4 d-flex justify-content-between align-items-center flex-shrink-0 no-print">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-upds-blue text-white px-3 py-2 rounded-pill fw-bold text-[10px] shadow-sm">
                        <i class="bi bi-shield-check me-2 text-upds-gold"></i> ACCESO AUTORIZADO
                    </span>
                    <div class="vr h-50 my-auto text-slate-300 opacity-50"></div>
                    <span class="text-[11px] fw-bold text-slate-500 uppercase tracking-widest">
                        <i class="bi bi-lightning-charge-fill me-1 text-warning"></i> Sincronización AgileQ++
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <div class="sia-ui-pulse me-2 bg-success"></div>
                    <span class="text-[10px] fw-black text-success uppercase">Conexión Live</span>
                </div>
            </div>

            {{-- CONTENEDOR DEL IFRAME (AJUSTE CLAVE) --}}
            {{-- Usamos 'h-100' para que el iframe llene la tarjeta --}}
            <div class="position-relative flex-grow-1 bg-white h-100 w-100">
                {{-- Loader Visual --}}
                <div class="position-absolute top-50 start-50 translate-middle text-center z-0 no-print">
                    <div class="spinner-border text-upds-blue mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <p class="text-slate-400 small fw-bold tracking-widest uppercase">Cargando Inteligencia Educativa...</p>
                </div>

                {{-- Iframe del Nuevo Reporte --}}
                <iframe 
                    id="powerbi-frame"
                    title="InformeDocentes" 
                    width="100%" 
                    height="100%" 
                    src="https://app.powerbi.com/reportEmbed?reportId=731124ea-fada-4b61-855e-98d4c1e92389&autoAuth=true&ctid=cf263038-f11c-49d2-9183-556a34b747e2" 
                    frameborder="0" 
                    allowFullScreen="true"
                    class="position-relative z-1 w-100 h-100 d-block">
                </iframe>
            </div>

            {{-- FOOTER TÉCNICO --}}
            <div class="bg-upds-blue text-white py-1 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="font-size: 10px;">
                <div class="font-monospace opacity-50">
                    SIA-PBI-ID: 731124EA • ENGINE: <strong>V4.0</strong>
                </div>
                <div class="fw-black text-upds-gold tracking-widest uppercase">
                    Vicerrectorado Académico • UPDS 2026
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos de Identidad UPDS SCZ */
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --upds-blue-dark: #001d3d;
    }

    /* Aseguramos que el contenedor ocupe la pantalla correcta */
    .h-screen { height: 100vh; }
    
    .text-upds-blue { color: var(--upds-blue) !important; }
    .bg-upds-blue { background-color: var(--upds-blue) !important; }
    .text-upds-gold { color: var(--upds-gold) !important; }
    
    .fw-black { font-weight: 900; }
    .text-\[10px\] { font-size: 10px; }
    .text-\[11px\] { font-size: 11px; }

    .btn-sia-primary-outline {
        border: 2px solid var(--upds-blue);
        color: var(--upds-blue);
        background: transparent;
        transition: all 0.3s;
    }
    .btn-sia-primary-outline:hover {
        background-color: var(--upds-blue);
        color: white;
    }

    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }

    .sia-ui-pulse {
        width: 8px; height: 8px;
        border-radius: 50%;
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(21, 128, 61, 0); }
        100% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0); }
    }

    /* Reglas para Impresión */
    @media print {
        aside, nav, .no-print { display: none !important; }
        body, html { 
            height: 100% !important; 
            margin: 0 !important; 
            padding: 0 !important; 
            overflow: visible !important;
        }
        .card { border: none !important; shadow: none !important; }
        iframe { height: 100vh !important; width: 100vw !important; }
    }
</style>

<script>
    function reloadFrame() {
        const iframe = document.getElementById('powerbi-frame');
        // Pequeño truco para forzar recarga limpia sin caché
        iframe.src = iframe.src;
    }
</script>
@endsection