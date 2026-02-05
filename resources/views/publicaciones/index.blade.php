@extends('layouts.app')

@section('title', 'Kardex de Producción Científica')

@section('content')
<div class="py-10 bg-slate-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
        
        {{-- Alertas de Sistema --}}
        @if(session('success'))
            <div class="bg-emerald-600 text-white p-4 rounded shadow-lg flex items-center justify-between animate-fade-in mb-4" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-double mr-3 text-emerald-200"></i>
                    <p class="font-bold text-sm uppercase tracking-wide">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="hover:text-emerald-200 font-bold">&times;</button>
            </div>
        @endif

        {{-- Banner Principal Institucional --}}
        <div class="bg-[#003566] rounded-t-lg p-6 shadow-lg border-b-4 border-[#FFC300]">
            <div class="flex items-center gap-4">
                <div class="bg-white/10 p-3 rounded-lg backdrop-blur-md">
                    <i class="fas fa-microscope text-[#FFC300] text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white uppercase tracking-tighter leading-none">
                        Kardex de Producción Científica
                    </h1>
                    <p class="text-[11px] font-bold text-blue-200/80 uppercase tracking-[0.2em] mt-1">
                        Vicerrectorado de Investigación y Postgrado | SIA v4.0
                    </p>
                </div>
            </div>
        </div>

        {{-- Barra de Herramientas Superior --}}
        <div class="bg-white border-x border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-md mb-4">
            <div class="flex gap-4">
                <a href="{{ route('investigacion.index') }}" 
                   class="flex items-center px-4 py-2 text-[#003566] border border-[#003566] rounded hover:bg-blue-50 transition text-[10px] font-black uppercase tracking-widest no-underline">
                    <i class="fas fa-layer-group mr-2"></i>
                    Proyectos de Investigación
                </a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('publicaciones.pdf', request()->all()) }}" 
                   class="flex items-center px-5 py-2.5 bg-[#d31c38] text-white rounded shadow-lg hover:bg-[#b0162c] transition text-[10px] font-black uppercase tracking-widest no-underline">
                    <i class="fas fa-file-pdf mr-2 text-sm"></i>
                    Generar Reporte
                </a>

                <a href="{{ route('publicaciones.create') }}" 
                   class="flex items-center px-6 py-2.5 bg-[#FFC300] text-[#003566] rounded shadow-lg hover:bg-[#e6b000] transition text-[10px] font-black uppercase tracking-widest no-underline">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Nueva Obra
                </a>
            </div>
        </div>

        {{-- Panel de Filtros Avanzados --}}
        <div class="bg-white p-8 border border-slate-200 shadow-sm rounded-lg mb-6">
            <form action="{{ route('publicaciones.index') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    
                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Descriptor de Búsqueda</label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Título, Autor, Medio..." 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 text-xs focus:border-[#003566] focus:ring-0 transition rounded-lg">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Unidad Académica</label>
                        <select name="carrera" class="w-full py-3 bg-slate-50 border border-slate-300 text-xs focus:border-[#003566] focus:ring-0 rounded-lg">
                            <option value="">-- Todas las Unidades --</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->CarreraID }}" {{ (request('carrera') == $c->CarreraID) ? 'selected' : '' }}>
                                    {{ $c->Nombrecarrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Rango Cronológico</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full py-2.5 bg-slate-50 border border-slate-300 text-xs rounded-lg">
                            <span class="text-slate-400 font-bold">AL</span>
                            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full py-2.5 bg-slate-50 border border-slate-300 text-xs rounded-lg">
                        </div>
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit" title="Filtrar Resultados" class="w-full bg-[#003566] text-white py-3 rounded-lg hover:bg-slate-800 transition flex items-center justify-center shadow-md">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Clasificación:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tipos as $t)
                            <label class="cursor-pointer group">
                                <input type="radio" name="tipo" value="{{ $t->TipopublicacionID }}" {{ request('tipo') == $t->TipopublicacionID ? 'checked' : '' }} onchange="this.form.submit()" class="hidden peer">
                                <span class="px-4 py-1.5 border border-slate-200 text-[10px] font-bold text-slate-500 peer-checked:bg-[#003566] peer-checked:text-white peer-checked:border-[#003566] hover:border-slate-400 transition uppercase tracking-tighter rounded-lg shadow-sm">
                                    {{ $t->Nombretipo }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('publicaciones.index') }}" class="text-[10px] font-black text-rose-600 hover:text-rose-700 uppercase tracking-widest border-b-2 border-rose-100 no-underline">
                            <i class="fas fa-undo-alt mr-1"></i> Restablecer Filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabla de Publicaciones Científicas --}}
        <div class="bg-white shadow-2xl border border-slate-200 overflow-hidden rounded-lg">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-800 text-white">
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] border-r border-slate-700">Detalle de la Obra y Autores</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] border-r border-slate-700 w-44">Clasificación</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] border-r border-slate-700 w-64">Contexto Institucional</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] w-48">Acciones de Gestión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($publicaciones as $pub)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-6 py-6 border-r border-slate-100">
                                <div class="text-[13px] font-black text-[#003566] mb-2 uppercase leading-snug tracking-tight">
                                    {{ $pub->Nombrepublicacion }}
                                </div>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Autores:</span>
                                    @foreach($pub->autores as $autor)
                                        <span class="text-[11px] font-bold text-slate-700 bg-slate-50 px-2 py-0.5 border border-slate-200 rounded">
                                            {{ $autor->Apellidopaterno }} {{ substr($autor->Nombrecompleto, 0, 1) }}.
                                        </span>
                                    @endforeach
                                </div>
                                <div class="mt-3 text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    Medio de Difusión: <span class="text-[#003566]">{{ $pub->medio->Nombremedio ?? 'Sin Registro' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-6 border-r border-slate-100 text-center">
                                <div class="inline-block px-3 py-1 border-2 border-[#003566]/10 bg-blue-50 text-[#003566] text-[9px] font-black uppercase mb-1 rounded-lg shadow-sm">
                                    {{ $pub->tipo->Nombretipo ?? 'General' }}
                                </div>
                                <div class="text-[12px] font-black text-slate-800 tracking-tighter mt-1">
                                    GESTIÓN: {{ $pub->Fechapublicacion ? date('Y', strtotime($pub->Fechapublicacion)) : 'S/R' }}
                                </div>
                            </td>

                            <td class="px-6 py-6 border-r border-slate-100">
                                @if($pub->proyecto)
                                    <div class="text-[10px] font-black text-blue-800 uppercase tracking-tighter mb-1">
                                        <i class="fas fa-project-diagram mr-1"></i> PROYECTO: {{ $pub->proyecto->CodigoProyecto }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase leading-tight">{{ $pub->proyecto->carrera->Nombrecarrera }}</div>
                                @else
                                    <div class="text-[10px] text-slate-400 italic font-black uppercase tracking-tighter bg-slate-50 px-2 py-1 border border-dashed rounded inline-block">
                                        <i class="fas fa-file-signature mr-1"></i> Producción Independiente
                                    </div>
                                @endif
                                <div class="mt-4 pt-2 border-t border-slate-100 text-[9px] font-bold text-slate-400 uppercase">
                                    Línea de Investigación: <span class="text-slate-600">{{ $pub->linea->Nombrelineainvestigacion ?? 'Sin Línea' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-6 text-center">
                                <div class="flex flex-col gap-2 items-center w-full">
                                    <a href="{{ route('publicaciones.edit', $pub->PublicacionID) }}" 
                                       class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-amber-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition shadow-sm no-underline">
                                        <i class="fas fa-edit"></i> Editar Obra
                                    </a>

                                    @if($pub->RutaArchivo)
                                        <a href="{{ asset('storage/'.$pub->RutaArchivo) }}" target="_blank" 
                                           class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-[#10b981] text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-[#059669] transition shadow-sm no-underline">
                                            <i class="fas fa-file-pdf"></i> Repositorio
                                        </a>
                                    @endif

                                    @if($pub->UrlPublicacion)
                                        <a href="{{ $pub->UrlPublicacion }}" target="_blank" 
                                           class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-[#6366f1] text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-[#4f46e5] transition shadow-sm no-underline">
                                            <i class="fas fa-external-link-alt"></i> Ver Fuente
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-24 text-center">
                                <div class="opacity-20 mb-4">
                                    <i class="fas fa-folder-open text-6xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-400 font-black uppercase text-xs tracking-[0.3em]">Base de datos sin registros vigentes</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Paginación Institucional --}}
            @if($publicaciones->hasPages())
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-200">
                {{ $publicaciones->appends(request()->all())->links() }}
            </div>
            @endif
        </div>

        {{-- Pie de Página SIA --}}
        <div class="text-center py-8">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-1">
                SISTEMA INTEGRADO DE ACREDITACIÓN UPDS
            </p>
            <div class="flex justify-center items-center gap-2 text-[8px] font-bold text-slate-300 uppercase">
                <span>Versión estable 4.0.2</span>
                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                <span>Auditoría: Habilitada</span>
            </div>
        </div>
    </div>
</div>

<style>
    .font-black { font-weight: 900; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    
    input:focus, select:focus {
        outline: none !important;
        border-color: #003566 !important;
        box-shadow: 0 0 0 2px rgba(0, 53, 102, 0.1) !important;
    }

    tbody tr { transition: all 0.2s ease; }
    tbody tr:hover { transform: scale(1.001); box-shadow: 0 4px 15px -5px rgba(0,0,0,0.05); }
</style>
@endsection