@extends('layouts.app')

@section('content')
{{-- Eliminamos el 100vh fijo para que no choque con el header del layout --}}
<div class="d-flex flex-column" style="height: calc(100vh - 130px); margin: -1.5rem;">
    
    {{-- Header Ejecutivo con Alto Contraste --}}
    <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-shrink-0 shadow-sm">
        <div>
            <h4 class="fw-black text-slate-800 mb-0 tracking-tighter uppercase" style="color: #003566;">
                {{ str_replace(['_', '-'], ' ', $titulo ?? 'REPORTE DE ACREDITACIÓN') }}
            </h4>
            <div class="d-flex align-items-center gap-2">
                <span class="sia-ui-pulse"></span>
                <span class="text-[10px] fw-bold text-slate-500 uppercase tracking-widest">Documento Oficial de Presentación</span>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            {{-- Botón para abrir en pestaña nueva (útil si quieren descargar/imprimir) --}}
            <a href="{{ asset('documentos/' . $archivo) }}" target="_blank" class="btn btn-light border rounded-pill px-3 btn-sm fw-bold text-slate-600">
                <i class="bi bi-box-arrow-up-right me-1"></i> VER PANTALLA COMPLETA
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-sia-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
                <i class="bi bi-house-door me-2"></i> INICIO
            </a>
        </div>
    </div>

    {{-- Visor de PDF con carga optimizada --}}
    <div class="flex-grow-1 bg-slate-200 position-relative">
        {{-- Spinner de carga mientras el PDF aparece --}}
        <div class="position-absolute top-50 start-50 translate-middle text-center opacity-50" style="z-index: 0;">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <p class="text-[10px] fw-bold tracking-widest uppercase">Procesando Documento...</p>
        </div>

        {{-- Iframe con el PDF --}}
        <iframe 
            src="{{ asset('documentos/' . $archivo) }}#toolbar=1&navpanes=0" 
            width="100%" 
            height="100%" 
            class="position-relative"
            style="border: none; z-index: 10;">
        </iframe>
    </div>

    {{-- Footer Técnico Informativo --}}
    <div class="bg-slate-800 text-white py-2 px-4 d-flex justify-content-between align-items-center flex-shrink-0" style="font-size: 10px; background-color: #001d3d;">
        <span class="opacity-70 fw-bold">SIA v4.0 • SISTEMA INTEGRADO DE ACREDITACIÓN</span>
        <span class="bg-white/10 px-2 py-0.5 rounded">ORIGEN: {{ $archivo }}</span>
    </div>
</div>

<style>
    /* Estilos específicos para evitar scroll global */
    body { overflow: hidden !important; }
    
    .btn-sia-primary {
        background-color: #003566;
        color: white;
        border: none;
        transition: all 0.2s;
    }
    .btn-sia-primary:hover {
        background-color: #001d3d;
        color: #ffc300;
        transform: translateY(-1px);
    }
</style>
@endsection