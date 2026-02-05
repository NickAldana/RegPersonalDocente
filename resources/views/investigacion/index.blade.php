@extends('layouts.app')

@section('title', 'Gestión de Proyectos de Investigación')

@section('content')
<div class="py-10 bg-slate-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
        
        {{-- Alertas de Sistema --}}
        @if(session('success'))
            <div class="bg-emerald-600 text-white p-4 rounded shadow-lg flex items-center justify-between mb-4 animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-emerald-200"></i>
                    <p class="font-bold text-sm uppercase tracking-wide">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="hover:text-emerald-200 font-bold">&times;</button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-rose-600 text-white p-4 rounded shadow-lg mb-4 animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-ban mr-3 text-rose-200"></i>
                    <p class="font-bold text-sm uppercase tracking-wide">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="hover:text-rose-200 font-bold">&times;</button>
            </div>
        @endif

        {{-- Banner Principal SIA v4.0 --}}
        <div class="bg-[#003566] rounded-t-lg p-6 shadow-lg border-b-4 border-[#FFC300]">
            <div class="flex items-center gap-4">
                <div class="bg-white/10 p-3 rounded-lg backdrop-blur-md">
                    <i class="fas fa-project-diagram text-[#FFC300] text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white uppercase tracking-tighter leading-none">
                        Cartera de Proyectos
                    </h1>
                    <p class="text-[11px] font-bold text-blue-200/80 uppercase tracking-[0.2em] mt-1">
                        Dirección de Investigación | SIA v4.0
                    </p>
                </div>
            </div>
        </div>

        {{-- Barra de Herramientas --}}
        <div class="bg-white border-x border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-md mb-4">
            <div class="flex gap-4">
                <a href="{{ route('publicaciones.index') }}" class="flex items-center px-4 py-2 text-[#003566] border border-[#003566] rounded hover:bg-blue-50 transition text-[10px] font-black uppercase tracking-widest no-underline">
                    <i class="fas fa-book mr-2"></i> Repositorio de Publicaciones
                </a>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('investigacion.pdf_proyectos', request()->all()) }}" class="flex items-center px-5 py-2.5 bg-[#d31c38] text-white rounded shadow-lg hover:bg-[#b0162c] transition text-[10px] font-black uppercase tracking-widest no-underline">
                    <i class="fas fa-file-pdf mr-2"></i> Exportar PDF
                </a>
                
                <a href="{{ route('investigacion.createProyecto') }}" class="flex items-center px-6 py-2.5 bg-[#FFC300] text-[#003566] rounded shadow-lg hover:bg-[#e6b000] transition text-[10px] font-black uppercase tracking-widest no-underline">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo Proyecto
                </a>
            </div>
        </div>

        {{-- Panel de Filtros Avanzados --}}
        <div class="bg-white p-8 border border-slate-200 shadow-sm rounded-lg mb-6">
            <form action="{{ route('investigacion.index') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest ml-1 italic">Búsqueda Título / Código</label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Escriba aquí..." class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 text-xs font-bold focus:border-[#003566] focus:ring-0 rounded-lg">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest ml-1 italic">Unidad Académica</label>
                        <select name="carrera" class="w-full py-3 bg-slate-50 border border-slate-300 text-xs font-bold focus:border-[#003566] focus:ring-0 rounded-lg">
                            <option value="">-- Todas las Carreras --</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->CarreraID }}" {{ request('carrera') == $c->CarreraID ? 'selected' : '' }}>
                                    {{ $c->Nombrecarrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest ml-1 italic">Rango de Fecha</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full py-2.5 bg-slate-50 border border-slate-300 text-[10px] font-bold rounded-lg">
                            <span class="text-slate-400 font-bold text-xs">AL</span>
                            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full py-2.5 bg-slate-50 border border-slate-300 text-[10px] font-bold rounded-lg">
                        </div>
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit" class="w-full bg-[#003566] text-white py-3 rounded-lg hover:bg-slate-800 transition shadow-lg flex justify-center">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado SIA:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['En Ejecución' => 'Vigente', 'Planificado' => 'Planificado', 'Finalizado' => 'Finalizado', 'Cancelado' => 'Anulado'] as $val => $label)
                            <label class="cursor-pointer group">
                                <input type="radio" name="estado" value="{{ $val }}" {{ request('estado') == $val ? 'checked' : '' }} onchange="this.form.submit()" class="hidden peer">
                                <span class="px-4 py-1.5 border border-slate-200 text-[9px] font-black text-slate-400 peer-checked:bg-[#003566] peer-checked:text-white peer-checked:border-[#003566] rounded-lg transition uppercase tracking-tighter shadow-sm">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('investigacion.index') }}" class="text-[10px] font-black text-rose-600 hover:text-rose-700 uppercase tracking-widest border-b-2 border-rose-100 transition-all no-underline">
                            <i class="fas fa-undo mr-1"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabla de Datos SIA --}}
        <div class="bg-white shadow-2xl border border-slate-200 overflow-hidden rounded-lg">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-800 text-white">
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] border-r border-slate-700">Expediente y Título</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] border-r border-slate-700 w-64">Clasificación y Equipo</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] border-r border-slate-700 w-40">Estado</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] w-48">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($proyectos as $proyecto)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-6 py-6 border-r border-slate-100">
                                <div class="text-[13px] font-black text-[#003566] mb-2 uppercase leading-snug tracking-tight group-hover:text-indigo-700 transition">
                                    {{ $proyecto->Nombreproyecto }}
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-[9px] font-black bg-slate-100 text-slate-500 px-2 py-0.5 rounded uppercase tracking-widest">{{ $proyecto->CodigoProyecto }}</span>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter italic">
                                        <i class="far fa-calendar-alt mr-1 opacity-50"></i> Inicio: {{ \Carbon\Carbon::parse($proyecto->Fechainicio)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-6 border-r border-slate-100">
                                <div class="flex flex-col gap-2">
                                    <div class="inline-block self-start px-2 py-0.5 border border-indigo-200 bg-indigo-50 text-indigo-700 text-[8px] font-black uppercase rounded shadow-sm">
                                        {{ $proyecto->linea->Nombrelineainvestigacion ?? 'S/L' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter leading-tight">
                                        U.A.: {{ $proyecto->carrera->Nombrecarrera ?? 'No Definida' }}
                                    </div>
                                    <div class="text-[9px] font-black text-[#003566] uppercase flex items-center gap-2">
                                        <i class="fas fa-users-cog opacity-50"></i>
                                        @php $activos = $proyecto->equipo->whereNull('pivot.FechaFin')->count(); @endphp
                                        <span>{{ $activos }} Miembros Activos</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-6 border-r border-slate-100 text-center">
                                @php
                                    $estilo = match($proyecto->Estado) {
                                        'En Ejecución' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                                        'Planificado'  => 'bg-amber-50 text-amber-700 border-amber-300',
                                        'Finalizado'   => 'bg-slate-100 text-slate-500 border-slate-300',
                                        'Cancelado'    => 'bg-rose-50 text-rose-700 border-rose-300',
                                        default        => 'bg-slate-100 text-slate-600 border-slate-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 border-2 rounded-full text-[9px] font-black uppercase tracking-widest {{ $estilo }}">
                                    @if($proyecto->Estado == 'En Ejecución') 
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-ping"></span> 
                                    @endif
                                    {{ $proyecto->Estado }}
                                </span>
                            </td>

                            <td class="px-6 py-6 text-center">
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('investigacion.showProyecto', $proyecto->ProyectoinvestigacionID) }}" 
                                       class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-slate-800 text-white rounded text-[9px] font-black uppercase tracking-widest hover:bg-black transition no-underline">
                                        <i class="fas fa-eye"></i> Ver Ficha
                                    </a>

                                    @if(in_array($proyecto->Estado, ['Finalizado', 'Cancelado']))
                                        <span class="inline-flex items-center justify-center px-3 py-2 bg-slate-100 text-slate-400 rounded text-[9px] font-black uppercase border border-slate-200 cursor-not-allowed tracking-widest italic">
                                            <i class="fas fa-lock mr-1.5"></i> Historial
                                        </span>
                                    @else
                                        <a href="{{ route('investigacion.editProyecto', $proyecto->ProyectoinvestigacionID) }}" 
                                           class="w-full flex justify-center items-center gap-2 px-3 py-2 bg-amber-400 text-amber-900 rounded text-[9px] font-black uppercase tracking-widest hover:bg-[#FFC300] transition no-underline shadow-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-32 text-center bg-slate-50">
                                <div class="flex flex-col items-center opacity-40">
                                    <i class="fas fa-folder-open text-5xl mb-4 text-slate-300"></i>
                                    <p class="text-slate-500 font-black uppercase text-xs tracking-[0.4em]">Sin coincidencias encontradas</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-200">
                {{ $proyectos->appends(request()->all())->links() }}
            </div>
        </div>

        <div class="text-center py-8">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] mb-1">
                SISTEMA INTEGRADO DE ACREDITACIÓN UPDS
            </p>
            <div class="flex justify-center items-center gap-2 text-[8px] font-bold text-slate-300 uppercase">
                <span>Versión estable 4.0.2</span>
                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                <span>Audit Log: Enabled</span>
            </div>
        </div>
    </div>
</div>

<style>
    .font-black { font-weight: 900; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    
    tbody tr { transition: all 0.2s ease; }
    tbody tr:hover { transform: scale(1.002); box-shadow: 0 4px 20px -10px rgba(0,0,0,0.1); }
</style>
@endsection