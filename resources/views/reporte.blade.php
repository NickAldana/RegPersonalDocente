@extends('layouts.app')

@section('content')
<div class="container-fluid p-0 d-flex flex-column" style="height: 100vh; overflow: hidden;">
    
    {{-- Header Ejecutivo Dinámico --}}
    <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-shrink-0">
        <div>
            <h4 class="fw-black text-upds-blue mb-0 tracking-tighter text-uppercase">
                {{ str_replace(['_', '-'], ' ', $titulo ?? 'REPORTE DE ACREDITACIÓN') }}
            </h4>
            <span class="text-[10px] fw-bold text-muted uppercase tracking-widest">Documento de Presentación Oficial</span>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sia-primary-outline rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> VOLVER AL PANEL
        </a>
    </div>

    {{-- Visor de PDF Dinámico --}}
    <div class="flex-grow-1 bg-gray-200">
        <iframe 
            src="{{ asset('documentos/' . $archivo) }}#toolbar=0" 
            width="100%" 
            height="100%" 
            style="border: none;">
        </iframe>
    </div>

    {{-- Footer Técnico --}}
    <div class="bg-upds-blue text-white py-1 px-4 text-center flex-shrink-0" style="font-size: 10px;">
        <span class="opacity-50">SIA v4.0 • Archivo: {{ $archivo }}</span>
    </div>
</div>
@endsection