@extends('layouts.app')

@section('title', 'Expediente: ' . $proyecto->CodigoProyecto)

@section('content')
<div class="py-10 bg-slate-100 min-h-screen font-sans antialiased">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
        
        {{-- Barra de Navegación / Acciones --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 no-print animate-fade-in">
            <a href="{{ route('investigacion.index') }}" class="flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded shadow-sm hover:bg-slate-50 transition-all text-[10px] font-black uppercase tracking-widest no-underline">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
            </a>

            <div class="flex gap-2">
                <a href="{{ route('investigacion.pdf_proyectos', ['id' => $proyecto->ProyectoinvestigacionID]) }}" 
                   class="px-6 py-2.5 bg-rose-700 text-white rounded shadow-md text-[10px] font-black uppercase tracking-widest hover:bg-rose-800 transition-all flex items-center no-underline">
                    <i class="fas fa-file-pdf mr-2"></i> Acta de Auditoría
                </a>

                @if(!in_array($proyecto->Estado, ['Finalizado', 'Cancelado']))
                <a href="{{ route('investigacion.editProyecto', $proyecto->ProyectoinvestigacionID) }}" class="px-6 py-2.5 bg-[#FFC300] text-[#003566] rounded shadow-md text-[10px] font-black uppercase tracking-widest hover:bg-yellow-400 transition-all flex items-center no-underline">
                    <i class="fas fa-pen-to-square mr-2"></i> Gestionar Expediente
                </a>
                @endif
            </div>
        </div>

        {{-- Tarjeta Principal del Expediente --}}
        <div class="bg-white border border-slate-300 shadow-xl rounded-none printable-area overflow-hidden">
            
            {{-- 1. ENCABEZADO DE ALTO NIVEL --}}
            <div class="relative bg-[#003566] p-10 overflow-hidden">
                {{-- Fondo decorativo sutil --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start gap-8">
                    <div class="flex-1 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 bg-[#FFC300] text-[#003566] text-[9px] font-black uppercase tracking-widest rounded-sm">
                                Expediente Oficial
                            </span>
                            <span class="text-blue-200 font-mono text-xs tracking-widest">
                                #{{ $proyecto->CodigoProyecto }}
                            </span>
                        </div>
                        
                        <h1 class="text-2xl md:text-3xl font-black text-white uppercase leading-snug tracking-wide">
                            {{ $proyecto->Nombreproyecto }}
                        </h1>
                        
                        <div class="flex flex-wrap gap-6 text-blue-100/80 text-[10px] font-bold uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-university text-[#FFC300]"></i>
                                {{ $proyecto->carrera->Nombrecarrera ?? 'Institucional' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-layer-group text-[#FFC300]"></i>
                                {{ $proyecto->linea->Nombrelineainvestigacion ?? 'Sin Línea Asignada' }}
                            </div>
                        </div>
                    </div>

                    {{-- SELLO DE DÍAS (Modificado según solicitud) --}}
                    <div class="text-center">
                        @php
                            // Mantenemos colores para contexto (Verde=Bien, Rojo=Cancelado, etc.)
                            $estadoColor = match($proyecto->Estado) {
                                'En Ejecución' => 'text-emerald-400 border-emerald-400',
                                'Planificado' => 'text-blue-400 border-blue-400',
                                'Finalizado' => 'text-slate-400 border-slate-400',
                                'Cancelado' => 'text-rose-400 border-rose-400',
                                default => 'text-white border-white'
                            };

                            // Calculamos días
                            $inicio = \Carbon\Carbon::parse($proyecto->Fechainicio);
                            $fin = $proyecto->Fechafinalizacion ? \Carbon\Carbon::parse($proyecto->Fechafinalizacion) : now();
                            $diasBadge = floor($inicio->diffInDays($fin));
                        @endphp
                        
                        <div class="border-4 {{ $estadoColor }} px-6 py-4 rounded-lg bg-white/5 backdrop-blur-sm transform rotate-[-2deg]">
                            {{-- ETIQUETA SUPERIOR --}}
                            <p class="text-[10px] uppercase tracking-[0.3em] text-white/60 mb-1">Días Acumulados</p>
                            
                            {{-- NÚMERO GRANDE --}}
                            <p class="text-4xl font-black uppercase tracking-tighter {{ explode(' ', $estadoColor)[0] }}">
                                {{ number_format($diasBadge, 0, ',', '.') }}
                            </p>
                            
                            {{-- ETIQUETA INFERIOR (Estado pequeño) --}}
                            <p class="text-[8px] font-bold uppercase tracking-widest text-white/40 mt-1">
                                {{ $proyecto->Estado }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. CRONOGRAMA --}}
            <div class="grid grid-cols-1 md:grid-cols-3 border-b border-slate-200 divide-y md:divide-y-0 md:divide-x divide-slate-200 bg-slate-50 text-center">
                <div class="p-6">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Fecha de Inicio</p>
                    <p class="text-sm font-bold text-slate-700">
                        {{ \Carbon\Carbon::parse($proyecto->Fechainicio)->format('d/m/Y') }}
                    </p>
                </div>
                <div class="p-6">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Fecha de Cierre</p>
                    <p class="text-sm font-bold text-slate-700">
                        @if($proyecto->Fechafinalizacion)
                            {{ \Carbon\Carbon::parse($proyecto->Fechafinalizacion)->format('d/m/Y') }}
                        @else
                            <span class="text-emerald-600 font-black tracking-widest">VIGENTE</span>
                        @endif
                    </p>
                </div>
                {{-- Columna 3: Información adicional (Ya que los días están arriba, mostramos el Estado aquí para no perder el dato) --}}
                <div class="p-6 bg-blue-50/50">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Condición Actual</p>
                    <p class="text-sm font-black text-[#003566] uppercase">
                        {{ $proyecto->Estado }}
                    </p>
                </div>
            </div>

            <div class="p-10 space-y-12">
                
                {{-- 3. CUERPO DE INVESTIGADORES --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-1 h-6 bg-[#003566]"></div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Historial de Investigadores</h3>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="px-6 py-4 text-[9px] font-black uppercase tracking-widest border-b border-slate-200">Investigador</th>
                                    <th class="px-6 py-4 text-[9px] font-black uppercase tracking-widest border-b border-slate-200">Rol & Responsabilidad</th>
                                    <th class="px-6 py-4 text-[9px] font-black uppercase tracking-widest border-b border-slate-200">Periodo</th>
                                    <th class="px-6 py-4 text-[9px] font-black uppercase tracking-widest text-center border-b border-slate-200">Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($proyecto->equipo as $miembro)
                                    @php $retirado = !is_null($miembro->pivot->FechaFin); @endphp
                                    <tr class="hover:bg-slate-50 transition-colors {{ $retirado ? 'bg-slate-50/50' : 'bg-white' }}">
                                        
                                        {{-- Investigador --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black border-2
                                                    {{ $retirado ? 'bg-slate-100 text-slate-400 border-slate-200' : 'bg-[#003566] text-white border-[#003566]' }}">
                                                    {{ substr($miembro->Nombrecompleto, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-slate-700 uppercase">{{ $miembro->Apellidopaterno }} {{ $miembro->Nombrecompleto }}</p>
                                                    <p class="text-[9px] text-slate-400 uppercase tracking-wide">ID: {{ $miembro->PersonalID }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Rol --}}
                                        <td class="px-6 py-4">
                                            <p class="text-[10px] font-bold text-slate-600 uppercase">{{ $miembro->pivot->Rol }}</p>
                                            @if($miembro->pivot->EsResponsable)
                                                <span class="inline-flex items-center text-[8px] font-black text-amber-600 uppercase tracking-tighter mt-1">
                                                    <i class="fas fa-crown mr-1"></i> Líder
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Periodo --}}
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-mono font-bold text-slate-600">
                                                    {{ \Carbon\Carbon::parse($miembro->pivot->FechaInicio)->format('d/m/Y') }} 
                                                    <span class="text-slate-300 mx-1">➜</span> 
                                                    {{ $retirado ? \Carbon\Carbon::parse($miembro->pivot->FechaFin)->format('d/m/Y') : 'Actualidad' }}
                                                </span>
                                                <span class="text-[8px] text-slate-400 font-medium italic mt-0.5">
                                                    @php
                                                        $f_ini = \Carbon\Carbon::parse($miembro->pivot->FechaInicio);
                                                        $f_fin = $retirado ? \Carbon\Carbon::parse($miembro->pivot->FechaFin) : now();
                                                        echo $f_ini->diffForHumans($f_fin, ['parts' => 2, 'join' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
                                                    @endphp
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Estatus --}}
                                        <td class="px-6 py-4 text-center">
                                            @if($retirado)
                                                <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-500 text-[8px] font-black uppercase tracking-widest border border-slate-200">
                                                    <i class="fas fa-lock mr-1"></i> Histórico
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest border border-emerald-100">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span> Activo
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

           {{-- 4. PUBLICACIONES CIENTÍFICAS (CORREGIDO) --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-1 h-6 bg-[#003566]"></div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Producción Intelectual</h3>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    @if($proyecto->publicaciones->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($proyecto->publicaciones as $pub)
                                <div class="p-4 border border-slate-200 rounded hover:border-blue-300 transition-colors bg-white flex gap-4 items-start group">
                                    {{-- Ícono --}}
                                    <div class="w-10 h-10 bg-blue-50 text-blue-600 flex items-center justify-center rounded group-hover:bg-blue-600 group-hover:text-white transition-colors shrink-0">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    
                                    {{-- Datos de la Publicación --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- 1. TÍTULO REAL --}}
                                        <h4 class="text-[11px] font-black text-slate-800 uppercase leading-tight truncate" title="{{ $pub->Nombrepublicacion }}">
                                            {{ $pub->Nombrepublicacion }}
                                        </h4>
                                        
                                        <div class="flex items-center flex-wrap gap-2 mt-2 text-[9px] font-bold text-slate-500 uppercase">
                                            {{-- 2. TIPO DE PUBLICACIÓN (Relación Correcta) --}}
                                            <span class="text-blue-600">
                                                {{ $pub->tipo->Nombretipo ?? 'No Categorizado' }}
                                            </span>
                                            
                                            <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                            
                                            {{-- 3. FECHA FORMATEADA --}}
                                            <span>
                                                {{ $pub->Fechapublicacion ? \Carbon\Carbon::parse($pub->Fechapublicacion)->format('d/m/Y') : 'Fecha Pendiente' }}
                                            </span>
                                        </div>

                                        {{-- 4. ENLACE (Si existe) --}}
                                        @if($pub->UrlPublicacion)
                                            <a href="{{ $pub->UrlPublicacion }}" target="_blank" class="text-[9px] text-[#003566] hover:underline mt-2 flex items-center gap-1 font-bold">
                                                <i class="fas fa-external-link-alt"></i> Ver Documento Digital
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- ESTADO VACÍO (Empty State) --}}
                        <div class="p-8 border-2 border-dashed border-slate-200 rounded-lg text-center bg-slate-50">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                <i class="fas fa-file-circle-xmark text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Sin producción intelectual registrada</p>
                            <p class="text-[9px] text-slate-400 mt-1 max-w-md mx-auto">
                                A la fecha de corte, este expediente no cuenta con artículos, libros o memorias científicas vinculadas en el sistema.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pie de Página --}}
            <div class="bg-slate-900 p-8 text-slate-500 flex justify-between items-center text-[9px] font-mono uppercase tracking-widest">
                <span>SIA v4.0 • Auditoría Académica</span>
                <span>Generado: {{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap');
    body { font-family: 'Roboto', sans-serif; }

    @media print {
        body { background: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .py-10 { padding: 0 !important; }
        .no-print { display: none !important; }
        .printable-area { border: none !important; box-shadow: none !important; width: 100% !important; margin: 0 !important; }
        .bg-\[\#003566\] { background-color: #003566 !important; }
        .bg-slate-900 { background-color: #0f172a !important; color: white !important; }
        .text-white { color: white !important; }
    }
</style>
@endsection