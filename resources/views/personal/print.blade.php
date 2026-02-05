<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Título dinámico ajustado a tus variables de BD --}}
    <title>Kardex_{{ $docente->Ci }}_{{ Str::slug($docente->Apellidopaterno) }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* ESTILOS DE IMPRESIÓN OFICIALES UPDS V4.0 */
        :root {
            --upds-blue: #003566;
            --upds-gold: #ffc300;
            --text-dark: #111827;
        }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: var(--text-dark); background-color: #525659; }
        .page-sheet { background-color: white; width: 210mm; min-height: 297mm; margin: 30px auto; padding: 15mm; box-shadow: 0 0 15px rgba(0,0,0,0.3); position: relative; }
        .doc-header { border-bottom: 2px solid var(--upds-blue); padding-bottom: 15px; margin-bottom: 25px; }
        .logo-text { color: var(--upds-blue); font-weight: 900; font-size: 28px; line-height: 1; }
        .section-header { 
            background-color: var(--upds-blue); 
            color: white; 
            padding: 6px 10px; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 12px; 
            margin-top: 20px; 
            margin-bottom: 10px; 
            border-left: 5px solid var(--upds-gold); 
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-custom { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table-custom th { 
            background-color: #f3f4f6; 
            border: 1px solid #d1d5db; 
            padding: 6px; 
            font-weight: 700; 
            text-transform: uppercase; 
            color: var(--upds-blue); 
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-custom td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: middle; }
        .data-label { font-weight: 700; color: #4b5563; text-transform: uppercase; font-size: 10px; }
        .data-value { font-weight: 500; color: #000; border-bottom: 1px dotted #ccc; padding-bottom: 2px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.04; font-size: 150px; font-weight: 900; color: var(--upds-blue); z-index: 0; pointer-events: none; }
        .floating-actions { position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px; }
        
        @media print {
            body { background-color: white; margin: 0; }
            .page-sheet { box-shadow: none; margin: 0; width: 100%; padding: 0; }
            .no-print, .floating-actions { display: none !important; }
            @page { margin: 1.5cm; size: auto; }
        }
    </style>
</head>
<body>

    <div class="floating-actions no-print">
        <button onclick="window.print()" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4"><i class="bi bi-printer-fill me-2"></i> Imprimir</button>
        <button onclick="window.close()" class="btn btn-light fw-bold shadow-sm rounded-pill px-4"><i class="bi bi-x-lg me-2"></i> Cerrar</button>
    </div>

    <div class="page-sheet">
        <div class="watermark">UPDS</div>

        {{-- 1. ENCABEZADO --}}
        <div class="doc-header d-flex justify-content-between align-items-end position-relative" style="z-index: 1;">
            <div>
                <div class="logo-text">UPDS</div>
                <div class="text-uppercase fw-bold text-muted small" style="letter-spacing: 2px;">Universidad Privada Domingo Savio</div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold m-0 text-uppercase">Kardex Académico</h4>
                {{-- Ajuste ID: PersonalID --}}
                <div class="small fw-bold mt-1 text-primary">ID: PERS-{{ str_pad($docente->PersonalID, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="text-end mb-3" style="font-size: 10px; color: #666;">
            Generado el: {{ date('d/m/Y H:i') }}
        </div>

        {{-- 2. DATOS PERSONALES --}}
        <div class="section-header">I. Datos de Identificación y Vinculación</div>
        
        <div class="row g-3 mb-3 position-relative" style="z-index: 1;">
            <div class="col-2 text-center">
                {{-- Ajuste: Fotoperfil (lowercase 'p') --}}
                @if($docente->Fotoperfil)
                    <img src="{{ asset('storage/'.$docente->Fotoperfil) }}" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; padding: 2px;">
                @else
                    <div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #f9f9f9; margin: 0 auto;">
                        <span style="font-size: 10px; color: #999;">FOTO</span>
                    </div>
                @endif
            </div>

            <div class="col-10">
                <div class="row g-2">
                    <div class="col-8">
                        <div class="data-label">Apellidos y Nombres</div>
                        {{-- Ajuste: Apellidopaterno, Apellidomaterno, Nombrecompleto --}}
                        <div class="data-value text-uppercase">
                            {{ $docente->Apellidopaterno }} {{ $docente->Apellidomaterno }} {{ $docente->Nombrecompleto }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="data-label">Cédula de Identidad</div>
                        {{-- Ajuste: Ci --}}
                        <div class="data-value fw-bold">{{ $docente->Ci }}</div>
                    </div>

                    <div class="col-6">
                        <div class="data-label">Cargo Institucional</div>
                        {{-- Ajuste: cargo->Nombrecargo --}}
                        <div class="data-value">{{ $docente->cargo->Nombrecargo ?? 'Sin Asignar' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="data-label">Modalidad de Contrato</div>
                        {{-- Ajuste: contrato->Nombrecontrato --}}
                        <div class="data-value">{{ $docente->contrato->Nombrecontrato ?? 'Sin Asignar' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="data-label">Correo Electrónico</div>
                        {{-- Ajuste: Correoelectronico --}}
                        <div class="data-value text-lowercase">{{ $docente->Correoelectronico }}</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Teléfono</div>
                        {{-- Ajuste: Telelefono (según controller) --}}
                        <div class="data-value">{{ $docente->Telelefono ?? '-' }}</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Género</div>
                        <div class="data-value">{{ $docente->Genero }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. FORMACIÓN ACADÉMICA --}}
        <div class="section-header">II. Formación Académica Registrada</div>
        <table class="table-custom mb-3">
            <thead>
                <tr>
                    <th style="width: 25%">Grado Académico</th>
                    <th style="width: 45%">Título Obtenido</th>
                    <th style="width: 30%">Institución</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docente->formaciones as $f)
                <tr>
                    {{-- Ajuste: Relación 'grado' y atributo 'Nombregrado' --}}
                    <td>{{ $f->grado->Nombregrado ?? '-' }}</td>
                    {{-- Ajuste: Atributo 'Tituloobtenido' --}}
                    <td class="text-uppercase">{{ $f->Tituloobtenido }}</td>
                    {{-- Ajuste: Relación 'centro' y atributo 'Nombrecentro' --}}
                    <td>{{ $f->centro->Nombrecentro ?? 'Externa' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-3 text-muted">- Sin registros -</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- 4. HISTORIAL DE MATERIAS --}}
        <div class="section-header">III. Carga Académica (Gestión Actual)</div>
        <table class="table-custom mb-3">
            <thead>
                <tr>
                    <th style="width: 10%" class="text-center">Gestión</th>
                    <th style="width: 15%" class="text-center">Periodo</th>
                    <th style="width: 15%" class="text-center">Sigla</th>
                    <th style="width: 60%">Asignatura</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docente->materias as $m)
                <tr>
                    {{-- Ajuste: Datos de pivot --}}
                    <td class="text-center fw-bold">{{ $m->pivot->Gestion }}</td>
                    <td class="text-center text-uppercase">{{ $m->pivot->Periodo }}</td>
                    {{-- Ajuste: Atributos de Materia --}}
                    <td class="text-center">{{ $m->Sigla ?? '-' }}</td>
                    <td>{{ $m->Nombremateria }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-3 text-muted">- Sin carga asignada para la gestión activa -</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- 5. ÁREAS DE VINCULACIÓN (Derivadas de las materias) --}}
        <div class="section-header">IV. Áreas de Vinculación</div>
        <div class="border p-3" style="background-color: #fcfcfc;">
            @php
                // Extraemos carreras únicas de las materias asignadas
                $carrerasUnicas = $docente->materias->pluck('carrera')->unique('CarreraID');
            @endphp

            @if($carrerasUnicas->isNotEmpty())
                <div class="row">
                @foreach($carrerasUnicas as $carrera)
                    @if($carrera)
                    <div class="col-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check2-square me-2 text-primary"></i>
                            <div>
                                {{-- Ajuste: Nombrecarrera --}}
                                <div class="fw-bold" style="font-size: 10px;">{{ $carrera->Nombrecarrera }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
                </div>
            @else
                <div class="text-muted text-center fst-italic">Sin vinculación específica a carreras actualmente.</div>
            @endif
        </div>

        {{-- 6. PRODUCCIÓN INTELECTUAL --}}
        <div class="section-header">V. Producción Intelectual e Investigación</div>
        <table class="table-custom mb-3">
            <thead>
                <tr>
                    <th style="width: 15%" class="text-center">Fecha</th>
                    <th style="width: 20%">Tipo</th>
                    <th style="width: 45%">Título de la Obra</th>
                    <th style="width: 20%" class="text-center">Medio/Revista</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docente->publicaciones as $pub)
                <tr>
                    {{-- Ajuste: Fechapublicacion --}}
                    <td class="text-center fw-bold">{{ \Carbon\Carbon::parse($pub->Fechapublicacion)->format('d/m/Y') }}</td>
                    {{-- Ajuste: Relación 'tipo', atributo 'Nombretipo' --}}
                    <td class="text-uppercase" style="font-size: 9px;">{{ $pub->tipo->Nombretipo ?? 'Genérico' }}</td>
                    {{-- Ajuste: Nombrepublicacion --}}
                    <td class="fst-italic">"{{ $pub->Nombrepublicacion }}"</td>
                    {{-- Ajuste: Relación 'medio', atributo 'Nombremedio' --}}
                    <td class="text-center text-muted">{{ $pub->medio->Nombremedio ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-3 text-muted">- Sin producción registrada -</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- PIE DE PÁGINA --}}
        <div class="row" style="margin-top: 80px;">
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <div class="fw-bold text-uppercase" style="font-size: 10px;">{{ $docente->Apellidopaterno }} {{ $docente->Apellidomaterno }}</div>
                    <div style="font-size: 9px;">Firma del Docente</div>
                </div>
            </div>
            <div class="col-4 text-center"></div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <div class="fw-bold text-uppercase" style="font-size: 10px;">Talento Humano</div>
                    <div style="font-size: 9px;">Sello y Firma Autorizada</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>