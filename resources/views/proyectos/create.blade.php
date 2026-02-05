@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Alertas de Sistema --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center animate-fade-in">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl border border-slate-200 overflow-hidden">
            {{-- Encabezado Principal --}}
            <div class="bg-[#003566] p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight uppercase">Gestión de Proyectos</h2>
                    <p class="text-blue-100 text-xs mt-1 font-medium uppercase tracking-widest">Formulario de registro oficial de investigación</p>
                </div>
                <a href="{{ route('investigacion.index') }}" class="flex items-center text-sm font-bold bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition-all border border-white/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    LISTADO PROYECTOS
                </a>
            </div>

            <form action="{{ route('investigacion.storeProyecto') }}" method="POST" id="formProyecto" class="p-8 space-y-10">
                @csrf

                {{-- Sección 1: Información General --}}
                <section>
                    <div class="flex items-center space-x-2 mb-6 border-b border-slate-100 pb-2">
                        <span class="bg-slate-800 text-white text-[10px] font-bold px-2 py-0.5 rounded">01</span>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Información Primaria</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="md:col-span-2">
                            <div class="flex justify-between items-center mb-2">
                                <label for="Nombreproyecto" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Denominación del Proyecto</label>
                                <span id="char-count" class="text-[10px] font-bold text-slate-400 uppercase italic transition-colors">Mínimo 10 caracteres</span>
                            </div>
                            <input type="text" name="Nombreproyecto" id="Nombreproyecto" value="{{ old('Nombreproyecto') }}"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#003566] focus:border-transparent transition-all uppercase placeholder:font-normal"
                                placeholder="Ej: Análisis de impacto socio-educativo en entornos rurales..." required minlength="10">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Unidad Académica / Carrera</label>
                            <select name="CarreraID" id="CarreraID" class="w-full rounded-xl border-slate-200 bg-slate-50 py-3 px-4 font-bold text-sm text-slate-700 focus:ring-2 focus:ring-[#003566]" required>
                                <option value="">Seleccione Carrera...</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->CarreraID }}">{{ $carrera->Nombrecarrera }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Línea de Investigación Institucional</label>
                            <select name="LineainvestigacionID" id="LineainvestigacionID" class="tom-select" required>
                                <option value="">Buscar línea...</option>
                                @php $facultadActual = ''; @endphp
                                @foreach($lineas as $linea)
                                    @php $nombreFacu = $linea->facultad->Nombrefacultad ?? 'OTRAS LÍNEAS'; @endphp
                                    @if($facultadActual != $nombreFacu)
                                        @if($facultadActual != '') </optgroup> @endif
                                        <optgroup label="{{ $nombreFacu }}">
                                        @php $facultadActual = $nombreFacu; @endphp
                                    @endif
                                    <option value="{{ $linea->LineainvestigacionID }}">{{ $linea->Nombrelineainvestigacion }}</option>
                                @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2" id="label-fecha">Cronograma: Inicio</label>
                            <input type="date" name="Fechainicio" id="Fechainicio" value="{{ old('Fechainicio', date('Y-m-d')) }}"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 font-bold text-sm text-slate-700 focus:ring-2 focus:ring-[#003566]" required>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Estado de Gestión</label>
                            <select name="Estado" id="Estado" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 font-bold text-sm text-slate-700 focus:ring-2 focus:ring-[#003566]" required>
                                <option value="En Ejecución" selected>VIGENTE (ACTIVO)</option>
                                <option value="Planificado">PLANIFICADO (SOLICITUD)</option>
                                <option value="Cancelado">NO PROCEDE (CANCELADO)</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- Sección 2: Equipo de Investigación --}}
                <section>
                    <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="bg-slate-800 text-white text-[10px] font-bold px-2 py-0.5 rounded">02</span>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Cuerpo de Investigadores</h3>
                        </div>
                        <button type="button" id="btn-add-member" 
                            class="text-[10px] font-black uppercase tracking-tighter bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-700 shadow-sm transition-all flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Incorporar Miembro
                        </button>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                        <table class="min-w-full divide-y divide-slate-200" id="tabla-equipo">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Personal Científico</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Función</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Alta</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">Líder</th>
                                    <th class="px-6 py-4 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100" id="tbody-equipo">
                                {{-- JS inyectará filas aquí --}}
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Footer de Formulario --}}
                <div class="pt-6 flex justify-end items-center space-x-4 border-t border-slate-100">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Verifique que toda la información sea veraz</span>
                    <button type="submit" class="px-10 py-4 bg-[#003566] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] shadow-lg hover:bg-slate-900 transition-all transform hover:-translate-y-1 active:scale-95">
                        Confirmar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Recursos Externos --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    /* Personalización de TomSelect para diseño formal */
    .ts-control { border-radius: 0.75rem !important; padding: 0.75rem !important; border: 1px solid #e2e8f0 !important; background-color: #f8fafc !important; font-weight: 700 !important; font-size: 0.875rem !important; }
    .ts-wrapper.focus .ts-control { border-color: #003566 !important; box-shadow: 0 0 0 3px rgba(0, 53, 102, 0.05) !important; }
    .ts-dropdown { z-index: 9999 !important; border-radius: 0.75rem !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    .ts-dropdown .optgroup-header { font-weight: 900 !important; color: #003566 !important; background-color: #f1f5f9 !important; text-transform: uppercase; font-size: 10px !important; padding: 10px !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    new TomSelect('#LineainvestigacionID', { create: false, maxOptions: null });

    const tbody = document.getElementById('tbody-equipo');
    const btnAdd = document.getElementById('btn-add-member');
    const inputNombre = document.getElementById('Nombreproyecto');
    const inputFechaInicio = document.getElementById('Fechainicio');
    const selectEstado = document.getElementById('Estado');
    const labelFecha = document.getElementById('label-fecha');
    const personales = @json($personales);

    function toggleFechaLimit() {
        const todayStr = "{{ date('Y-m-d') }}";
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];

        if (selectEstado.value === 'Planificado') {
            labelFecha.innerText = "Cronograma: Estimado Inicio (Futuro)";
            inputFechaInicio.setAttribute('min', tomorrowStr);
            inputFechaInicio.removeAttribute('max');
            if (inputFechaInicio.value <= todayStr) inputFechaInicio.value = tomorrowStr;
        } else {
            labelFecha.innerText = "Cronograma: Fecha de Inicio (Vigente)";
            inputFechaInicio.removeAttribute('min');
            inputFechaInicio.setAttribute('max', todayStr);
            if (inputFechaInicio.value > todayStr) inputFechaInicio.value = todayStr;
        }
    }

    selectEstado.addEventListener('change', toggleFechaLimit);
    toggleFechaLimit();

    inputNombre.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        const count = this.value.length;
        const counter = document.getElementById('char-count');
        
        if (count === 0) {
            counter.innerText = "Requerido (Mín. 10 caracteres)";
            counter.className = "text-[10px] font-black text-slate-400 uppercase italic";
        } else if (count < 10) {
            counter.innerText = `Nombre incompleto (Faltan ${10 - count})`;
            counter.className = "text-[10px] font-black text-rose-500 uppercase italic animate-pulse";
        } else {
            counter.innerText = "Longitud de nombre correcta";
            counter.className = "text-[10px] font-black text-emerald-600 uppercase italic";
        }
    });

    function addRow() {
        const row = document.createElement('tr');
        row.className = "hover:bg-slate-50 transition-colors group";
        
        row.innerHTML = `
            <td class="px-6 py-4">
                <select name="participantes[]" class="select-personal-dinamico" required>
                    <option value="">Buscar científico por apellido...</option>
                    ${personales.map(p => `<option value="${p.PersonalID}">${p.Apellidopaterno} ${p.Apellidomaterno ?? ''} ${p.Nombrecompleto}</option>`).join('')}
                </select>
            </td>
            <td class="px-6 py-4">
                <select name="roles_proy[]" class="w-full rounded-xl border-slate-200 text-[11px] font-black uppercase py-2 bg-slate-50 role-selector focus:ring-1 focus:ring-slate-400">
                    <option value="1">ENCARGADO DE PROYECTO</option>
                    <option value="2">DOCENTE INVESTIGADOR</option>
                    <option value="3" selected>ESTUDIANTE INVESTIGADOR</option>
                    <option value="4">PASANTE</option>
                    <option value="5">REVISOR TÉCNICO</option>
                    <option value="6">TUTOR / ASESOR</option>
                </select>
            </td>
            <td class="px-6 py-4">
                <input type="date" name="fechas_inc[]" class="w-full rounded-xl border-slate-200 text-[11px] font-black py-2 bg-slate-50 input-fecha-miembro" value="${inputFechaInicio.value}" min="${inputFechaInicio.value}">
            </td>
            <td class="px-6 py-4 text-center">
                <input type="checkbox" value="1" class="rounded text-[#003566] h-5 w-5 border-slate-300 focus:ring-0 cursor-pointer jefe-checkbox">
            </td>
            <td class="px-6 py-4 text-center">
                <button type="button" class="btn-remove text-slate-300 hover:text-rose-600 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;

        tbody.appendChild(row);

        const ts = new TomSelect(row.querySelector('.select-personal-dinamico'), {
            create: false,
            searchField: ['text'], 
            placeholder: "Científico...",
            maxOptions: 50,
            dropdownParent: 'body'
        });

        ts.on('change', function(value) {
            let selects = document.querySelectorAll('.select-personal-dinamico');
            let count = 0;
            selects.forEach(s => { if(s.value === value) count++; });
            if(count > 1) {
                alert("Restricción: Este investigador ya ha sido incorporado al cuerpo de este proyecto.");
                ts.clear();
            }
        });

        row.querySelector('.role-selector').addEventListener('change', function() {
            if(this.value === "1") {
                document.querySelectorAll('.jefe-checkbox').forEach(cb => cb.checked = false);
                row.querySelector('.jefe-checkbox').checked = true;
            }
        });

        row.querySelector('.btn-remove').addEventListener('click', () => {
            row.remove();
            reindexCheckboxes();
        });

        reindexCheckboxes();
    }

    function reindexCheckboxes() {
        document.querySelectorAll('.jefe-checkbox').forEach((cb, i) => {
            cb.setAttribute('name', `es_responsable[${i}]`);
        });
    }

    inputFechaInicio.addEventListener('change', function() {
        const nuevaFecha = this.value;
        document.querySelectorAll('.input-fecha-miembro').forEach(el => {
            el.min = nuevaFecha;
            if (el.value < nuevaFecha) el.value = nuevaFecha;
        });
    });

    btnAdd.addEventListener('click', addRow);
    addRow(); 
});
</script>
@endsection