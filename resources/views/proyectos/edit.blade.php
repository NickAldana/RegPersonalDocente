@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Alertas --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-xl shadow-sm flex items-center animate-pulse">
                <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                <span class="text-sm font-bold uppercase tracking-wide">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
            
            {{-- Encabezado --}}
            <div class="bg-[#003566] p-6 text-white flex justify-between items-center shadow-md relative z-10">
                <div>
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-folder-open text-xl text-[#FFC300]"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black tracking-widest uppercase">Expediente: {{ $proyecto->CodigoProyecto }}</h2>
                            <p class="text-blue-200 text-[10px] font-bold uppercase tracking-widest">Gestión de Acreditación e Investigación</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('investigacion.index') }}" class="group flex items-center text-[10px] font-black bg-white/10 hover:bg-white text-white hover:text-[#003566] px-4 py-2.5 rounded-lg transition-all border border-white/20 uppercase tracking-widest">
                    <i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>
                    Volver al Listado
                </a>
            </div>

            <form action="{{ route('investigacion.updateProyecto', $proyecto->ProyectoinvestigacionID) }}" method="POST" id="formProyecto" class="p-8 space-y-12">
                @csrf
                @method('PUT')

                {{-- SECCIÓN 1: DATOS GENERALES (Diseño Grid Limpio) --}}
                <section>
                    <div class="flex items-center mb-6 pb-2 border-b border-slate-100">
                        <div class="bg-[#003566] text-white text-[10px] font-black px-2 py-1 rounded mr-3">01</div>
                        <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Datos Maestros del Proyecto</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        {{-- Nombre del Proyecto (Ancho total) --}}
                        <div class="md:col-span-12">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Título Oficial de la Investigación</label>
                            <input type="text" name="Nombreproyecto" value="{{ old('Nombreproyecto', $proyecto->Nombreproyecto) }}"
                                class="w-full text-sm font-bold text-slate-700 border-0 border-b-2 border-slate-200 focus:border-[#003566] focus:ring-0 px-0 py-2 transition-colors uppercase placeholder-slate-300"
                                placeholder="INGRESE EL NOMBRE DEL PROYECTO..." required minlength="10">
                        </div>

                        {{-- Selectores (3 columnas) --}}
                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Unidad Académica</label>
                            <div class="relative">
                                <select name="CarreraID" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 rounded-lg p-3 focus:ring-2 focus:ring-[#003566] focus:border-transparent appearance-none">
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->CarreraID }}" {{ $proyecto->CarreraID == $carrera->CarreraID ? 'selected' : '' }}>{{ $carrera->Nombrecarrera }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400"><i class="fas fa-chevron-down text-xs"></i></div>
                            </div>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Línea de Investigación</label>
                            <select name="LineainvestigacionID" id="LineainvestigacionID" class="tom-select" required>
                                @foreach($lineas as $linea)
                                    <option value="{{ $linea->LineainvestigacionID }}" {{ $proyecto->LineainvestigacionID == $linea->LineainvestigacionID ? 'selected' : '' }}>{{ $linea->Nombrelineainvestigacion }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Estado Actual</label>
                            <div class="relative">
                                <select name="Estado" id="Estado" class="w-full bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 rounded-lg p-3 focus:ring-2 focus:ring-[#003566]">
                                    <option value="En Ejecución" {{ $proyecto->Estado == 'En Ejecución' ? 'selected' : '' }}>🟢 VIGENTE (EN EJECUCIÓN)</option>
                                    <option value="Planificado" {{ $proyecto->Estado == 'Planificado' ? 'selected' : '' }}>🔵 PLANIFICADO</option>
                                    <option value="Finalizado">⚫ FINALIZADO (CERRADO)</option>
                                    <option value="Cancelado">🔴 CANCELADO</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fecha Inicio --}}
                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Fecha de Inicio</label>
                            <input type="date" name="Fechainicio" id="Fechainicio" value="{{ old('Fechainicio', \Carbon\Carbon::parse($proyecto->Fechainicio)->format('Y-m-d')) }}"
                                class="w-full bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 rounded-lg p-3 focus:ring-2 focus:ring-[#003566]" required>
                        </div>
                    </div>
                </section>

                {{-- SECCIÓN 2: EQUIPO (LA JOYA DE LA CORONA) --}}
                <section>
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-slate-100">
                        <div class="flex items-center">
                            <div class="bg-[#003566] text-white text-[10px] font-black px-2 py-1 rounded mr-3">02</div>
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Cuerpo de Investigadores</h3>
                        </div>
                        <button type="button" id="btn-add-member" 
                            class="group flex items-center text-[10px] font-black uppercase tracking-widest text-[#003566] bg-blue-50 hover:bg-[#003566] hover:text-white px-4 py-2 rounded-lg transition-all border border-blue-100 shadow-sm">
                            <i class="fas fa-user-plus mr-2 text-lg"></i>
                            Agregar Investigador
                        </button>
                    </div>

                    <div class="rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/3">Investigador</th>
                                    <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rol / Jerarquía</th>
                                    <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Vigencia</th>
                                    <th class="py-4 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-equipo" class="divide-y divide-slate-100 bg-white">
                                @foreach($proyecto->equipo as $index => $miembro)
                                    @php 
                                        $estaRetirado = !is_null($miembro->pivot->FechaFin); 
                                    @endphp

                                    {{-- 
                                        TRUCO DE DISEÑO:
                                        - Activos: Borde Azul a la izquierda.
                                        - Retirados: Borde Gris Oscuro a la izquierda + Fondo sutil.
                                    --}}
                                    <tr class="transition-all group 
                                        {{ $estaRetirado ? 'bg-slate-50 border-l-4 border-l-slate-400' : 'hover:bg-blue-50/30 border-l-4 border-l-[#003566]' }}">
                                        
                                        {{-- 1. DATOS DEL INVESTIGADOR --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-start">
                                                <div class="mr-3 mt-1">
                                                    @if($estaRetirado)
                                                        <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 shadow-inner">
                                                            <i class="fas fa-user-clock text-xs"></i>
                                                        </div>
                                                    @else
                                                        <div class="h-8 w-8 rounded-full bg-[#003566] flex items-center justify-center text-white shadow-md shadow-blue-900/20">
                                                            <span class="text-xs font-bold">{{ substr($miembro->Nombrecompleto, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-slate-700 uppercase tracking-wide">
                                                        {{ $miembro->Apellidopaterno }} {{ $miembro->Nombrecompleto }}
                                                    </div>
                                                    <div class="mt-1">
                                                        @if($estaRetirado)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-slate-200 text-slate-600 uppercase tracking-wider">
                                                                Histórico / Retirado
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-wider">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                                                Activo
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ID Oculto --}}
                                            <input type="hidden" name="participantes[]" value="{{ $miembro->PersonalID }}">
                                        </td>

                                        {{-- 2. ROL --}}
                                        <td class="px-6 py-4">
                                            @if($estaRetirado)
                                                {{-- MODO LECTURA ELEGANTE (Sin input visible, parece texto) --}}
                                                <div class="text-xs font-bold text-slate-600 uppercase">
                                                    {{ $miembro->pivot->Rol }}
                                                </div>
                                                @if($miembro->pivot->EsResponsable)
                                                    <div class="text-[9px] font-black text-slate-400 mt-1 uppercase flex items-center">
                                                        <i class="fas fa-crown text-slate-400 mr-1"></i> Fue Responsable
                                                    </div>
                                                @endif

                                                {{-- Inputs Ocultos para mantener los datos --}}
                                                @php 
                                                    $rolesMap = ['ENCARGADO DE PROYECTO' => 1, 'DOCENTE INVESTIGADOR' => 2, 'ESTUDIANTE INVESTIGADOR' => 3, 'PASANTE' => 4, 'REVISOR TÉCNICO' => 5, 'TUTOR / ASESOR' => 6];
                                                    $rolVal = $rolesMap[$miembro->pivot->Rol] ?? 3;
                                                @endphp
                                                <input type="hidden" name="roles_proy[]" value="{{ $rolVal }}">
                                                @if($miembro->pivot->EsResponsable) <input type="hidden" name="es_responsable[{{ $index }}]" value="1"> @endif

                                            @else
                                                {{-- MODO EDICIÓN (Selects bonitos) --}}
                                                <select name="roles_proy[]" class="w-full bg-white border border-slate-200 text-[10px] font-bold uppercase rounded p-2 focus:ring-1 focus:ring-[#003566] mb-2">
                                                    @php $rolesMap = ['ENCARGADO DE PROYECTO' => 1, 'DOCENTE INVESTIGADOR' => 2, 'ESTUDIANTE INVESTIGADOR' => 3, 'PASANTE' => 4, 'REVISOR TÉCNICO' => 5, 'TUTOR / ASESOR' => 6]; 
                                                         $rolActual = array_search($miembro->pivot->Rol, array_keys($rolesMap)) !== false ? $rolesMap[$miembro->pivot->Rol] : 3;
                                                    @endphp
                                                    @foreach($rolesMap as $nombre => $valor)
                                                        <option value="{{ $valor }}" {{ $rolActual == $valor ? 'selected' : '' }}>{{ $nombre }}</option>
                                                    @endforeach
                                                </select>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="es_responsable[{{ $index }}]" value="1" class="rounded text-[#003566] h-4 w-4 border-slate-300 focus:ring-0 cursor-pointer" {{ $miembro->pivot->EsResponsable ? 'checked' : '' }}>
                                                    <span class="ml-2 text-[9px] font-black text-[#003566] uppercase tracking-wide">Es Responsable</span>
                                                </label>
                                            @endif
                                        </td>

                                        {{-- 3. FECHAS (El punto clave) --}}
                                        <td class="px-6 py-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Alta</label>
                                                    {{-- Fecha Alta siempre readonly, es un hecho histórico --}}
                                                    <div class="text-xs font-bold text-slate-600 font-mono bg-slate-50 px-2 py-1 rounded border border-slate-100">
                                                        {{ \Carbon\Carbon::parse($miembro->pivot->FechaInicio)->format('d/m/Y') }}
                                                    </div>
                                                    <input type="hidden" name="fechas_inc[]" value="{{ \Carbon\Carbon::parse($miembro->pivot->FechaInicio)->format('Y-m-d') }}">
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Baja / Retiro</label>
                                                    @if($estaRetirado)
                                                        {{-- SI ESTÁ RETIRADO: Mostramos la fecha fija (Estilo Etiqueta) --}}
                                                        <div class="text-xs font-bold text-slate-500 font-mono bg-slate-200 px-2 py-1 rounded border border-slate-300 flex items-center justify-between">
                                                            {{ \Carbon\Carbon::parse($miembro->pivot->FechaFin)->format('d/m/Y') }}
                                                            <i class="fas fa-lock text-[10px] text-slate-400"></i>
                                                        </div>
                                                        {{-- Enviamos la fecha oculta --}}
                                                        <input type="date" name="fechas_fin[]" value="{{ \Carbon\Carbon::parse($miembro->pivot->FechaFin)->format('Y-m-d') }}" readonly class="hidden">
                                                    @else
                                                        {{-- SI ESTÁ ACTIVO: Input limpio para dar de baja --}}
                                                        <input type="date" name="fechas_fin[]" 
                                                            class="fecha-fin-input w-full bg-white border border-slate-200 text-[10px] font-bold text-slate-600 rounded p-1 focus:ring-1 focus:ring-rose-500 focus:border-rose-500 shadow-sm"
                                                            min="{{ \Carbon\Carbon::parse($miembro->pivot->FechaInicio)->format('Y-m-d') }}"
                                                            placeholder="Vigente">
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 4. ACCIONES --}}
                                        <td class="px-6 py-4 text-center">
                                            @if($estaRetirado)
                                                <div class="text-slate-300" title="Registro Cerrado">
                                                    <i class="fas fa-shield-alt text-lg"></i>
                                                </div>
                                            @else
                                                <div class="text-emerald-500" title="Registro Consolidado">
                                                    <i class="fas fa-check-circle text-lg"></i>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Footer de Acciones --}}
                <div class="flex items-center justify-end pt-6 border-t border-slate-100">
                    <button type="submit" class="group relative px-8 py-3 bg-[#003566] text-white text-xs font-black uppercase tracking-widest rounded-lg shadow-lg hover:bg-slate-900 transition-all hover:-translate-y-0.5">
                        <span class="flex items-center">
                            <i class="fas fa-save mr-2 text-[#FFC300]"></i>
                            Guardar Cambios del Expediente
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Dependencias --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#LineainvestigacionID', { create: false, maxOptions: null });

    const tbody = document.getElementById('tbody-equipo');
    const btnAdd = document.getElementById('btn-add-member');
    const inputFechaInicio = document.getElementById('Fechainicio');
    const personales = @json($personales);

    // Inicializar TomSelect dinámicos
    function initTomSelect(el) {
        if (el.classList.contains('tomselected')) return;
        new TomSelect(el, {
            create: false,
            searchField: ['text'],
            dropdownParent: 'body',
            placeholder: 'Buscar Investigador...',
            options: personales.map(p => ({ value: p.PersonalID, text: `${p.Apellidopaterno} ${p.Nombrecompleto}`.toUpperCase() }))
        });
    }

    // Agregar Fila Nueva (DISEÑO ÁMBAR PARA DIFERENCIAR)
    // Función para AGREGAR filas nuevas (FLEXIBLE)
    function addRow() {
        const row = document.createElement('tr');
        // Usamos borde amarillo para indicar que es nuevo/pendiente
        row.className = "bg-yellow-50/30 hover:bg-yellow-50 transition-colors group animate-fade-in border-l-4 border-yellow-400"; 
        
        // 1. Fecha de HOY (Lo más probable)
        const hoy = new Date().toISOString().split('T')[0];
        
        // 2. Fecha Inicio Proyecto (Solo como referencia, NO como bloqueo)
        const inicioProyecto = inputFechaInicio.value;

        /* CAMBIO DE LÓGICA:
           - Por defecto sugerimos HOY.
           - PERO si HOY es antes del inicio del proyecto (ej: estamos planificando),
             sugerimos la fecha de inicio del proyecto para no equivocarnos.
        */
        let fechaSugerida = hoy;
        if (inicioProyecto && hoy < inicioProyecto) {
            fechaSugerida = inicioProyecto;
        }

        row.innerHTML = `
            <td class="px-6 py-5">
                <div class="flex flex-col">
                    <span class="text-[9px] text-yellow-600 font-black tracking-wider uppercase mb-1">NUEVO INGRESO</span>
                    <select name="participantes[]" class="select-nueva-fila w-full rounded-lg border-yellow-300 text-sm font-bold bg-white focus:ring-yellow-400" required>
                        <option value="">Buscar científico...</option>
                    </select>
                </div>
            </td>
            <td class="px-6 py-5">
                <div class="flex flex-col space-y-3">
                    <select name="roles_proy[]" class="w-full rounded-lg border-slate-200 text-[10px] font-black uppercase py-1.5 bg-white shadow-sm">
                        <option value="1">ENCARGADO DE PROYECTO</option>
                        <option value="2">DOCENTE INVESTIGADOR</option>
                        <option value="3" selected>ESTUDIANTE INVESTIGADOR</option>
                        <option value="4">PASANTE</option>
                        <option value="5">REVISOR TÉCNICO</option>
                        <option value="6">TUTOR / ASESOR</option>
                    </select>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="1" class="rounded text-[#003566] h-4 w-4 border-slate-300 jefe-checkbox">
                        <span class="ml-2 text-[9px] font-black text-slate-500 uppercase tracking-tighter">Liderazgo</span>
                    </label>
                </div>
            </td>
            <td class="px-6 py-5">
                {{-- AQUÍ ESTÁ EL CAMBIO: Quitamos el 'min' para dar libertad administrativa --}}
                <div class="relative">
                    <input type="date" name="fechas_inc[]" 
                        class="w-full rounded-lg border-slate-200 text-[11px] font-black py-1.5 bg-white focus:ring-1 focus:ring-yellow-400" 
                        value="${fechaSugerida}">
                    
                    {{-- Si hay fecha de proyecto, mostramos una advertencia visual sutil si se van muy atrás --}}
                    ${inicioProyecto ? `<div class="text-[8px] text-slate-400 mt-1 italic">Proyecto inicia: ${inicioProyecto.split('-').reverse().join('/')}</div>` : ''}
                </div>
            </td>
            <td class="px-6 py-5 text-center text-[10px] text-slate-400 italic">
                -- Pendiente --
                <input type="hidden" name="fechas_fin[]" value=""> 
            </td>
            <td class="px-6 py-5 text-center">
                <button type="button" class="btn-remove text-rose-400 hover:text-rose-600 transition-colors p-2 rounded-full hover:bg-rose-50" title="Cancelar">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        initTomSelect(row.querySelector('.select-nueva-fila'));
        reindexCheckboxes();
    }

    btnAdd.addEventListener('click', (e) => { e.preventDefault(); addRow(); });

    function reindexCheckboxes() {
        document.querySelectorAll('.jefe-checkbox').forEach((cb, i) => cb.setAttribute('name', `es_responsable[${i}]`));
    }

    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.btn-remove');
        if(btn) {
            // Confirmación suave solo para estar seguros
            if(confirm('¿Descartar este ingreso nuevo?')) {
                btn.closest('tr').remove();
                reindexCheckboxes();
            }
        }
    });

    // Gestión automática de colores al poner fecha de baja en registros activos
    document.querySelectorAll('.fecha-fin-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            if(this.value) {
                // Si ponen fecha -> Se pone Amarillo suave para indicar "Cambio pendiente"
                row.classList.remove('border-l-[#003566]');
                row.classList.add('bg-amber-50', 'border-l-amber-400');
            } else {
                // Si la quitan -> Vuelve a ser Azul (Activo)
                row.classList.remove('bg-amber-50', 'border-l-amber-400');
                row.classList.add('border-l-[#003566]');
            }
        });
    });

    reindexCheckboxes();
});
</script>
@endsection