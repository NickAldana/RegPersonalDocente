<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Título dinámico usando los campos REALES de tu base de datos --}}
    <title>Kardex_{{ $docente->CI }}_{{ Str::slug($docente->ApellidoPaterno) }}</title>
    
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
            -webkit-print-color-adjust: exact; /* For WebKit browsers */
            print-color-adjust: exact; /* Standard property */
        }
        .table-custom { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table-custom th { 
            background-color: #f3f4f6; 
            border: 1px solid #d1d5db; 
            padding: 6px; 
            font-weight: 700; 
            text-transform: uppercase; 
            color: var(--upds-blue); 
            -webkit-print-color-adjust: exact; /* For WebKit browsers */
            print-color-adjust: exact; /* Standard property */
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
                <div class="small fw-bold mt-1 text-primary">ID: PERS-{{ str_pad($docente->IdPersonal, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="text-end mb-3" style="font-size: 10px; color: #666;">
            Generado el: {{ date('d/m/Y H:i') }}
        </div>

        {{-- 2. DATOS PERSONALES (Corregido con nombres de columnas SQL) --}}
        <div class="section-header">I. Datos de Identificación y Vinculación</div>
        
        <div class="row g-3 mb-3 position-relative" style="z-index: 1;">
            <div class="col-2 text-center">
                @if($docente->FotoPerfil)
                    <img src="{{ asset('storage/'.$docente->FotoPerfil) }}" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; padding: 2px;">
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
                        {{-- CORRECCIÓN: Concatenación manual, ya que tu tabla tiene columnas separadas --}}
                        <div class="data-value text-uppercase">
                            {{ $docente->ApellidoPaterno }} {{ $docente->ApellidoMaterno }} {{ $docente->NombreCompleto }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="data-label">Cédula de Identidad</div>
                        <div class="data-value fw-bold">{{ $docente->CI }}</div>
                    </div>

                    <div class="col-6">
                        <div class="data-label">Cargo Institucional</div>
                        {{-- CORRECCIÓN: Usar NombreCargo --}}
                        <div class="data-value">{{ $docente->cargo->NombreCargo ?? 'Sin Asignar' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="data-label">Modalidad de Contrato</div>
                        {{-- CORRECCIÓN: Usar NombreContrato --}}
                        <div class="data-value">{{ $docente->contrato->NombreContrato ?? 'Sin Asignar' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="data-label">Correo Electrónico</div>
                        <div class="data-value text-lowercase">{{ $docente->CorreoElectronico }}</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Teléfono</div>
                        <div class="data-value">{{ $docente->Telefono ?? '-' }}</div>
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
                    <th style="width: 10%" class="text-center">Año</th>
                    <th style="width: 20%">Grado Académico</th>
                    <th style="width: 40%">Título Obtenido</th>
                    <th style="width: 30%">Institución</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docente->formaciones as $f)
                <tr>
                    {{-- CORRECCIÓN: Campos según tu tabla Formacion --}}
                    <td class="text-center fw-bold">{{ $f->AñoEstudios }}</td>
                    <td>{{ $f->gradoAcademico->NombreGrado ?? '-' }}</td> {{-- NombreGrado --}}
                    <td class="text-uppercase">{{ $f->TituloObtenido }}</td>
                    <td>{{ $f->centroFormacion->NombreCentro ?? '-' }}</td> {{-- NombreCentro --}}
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-3 text-muted">- Sin registros -</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- 4. HISTORIAL DE MATERIAS --}}
        <div class="section-header">III. Historial de Carga Horaria</div>
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
                    {{-- CORRECCIÓN: Datos de la tabla pivote PersonalMateria --}}
                    <td class="text-center fw-bold">{{ $m->pivot->Gestion }}</td>
                    <td class="text-center text-uppercase">{{ $m->pivot->Periodo }}</td>
                    <td class="text-center">{{ $m->Sigla ?? '-' }}</td>
                    <td>{{ $m->NombreMateria }}</td> {{-- NombreMateria --}}
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-3 text-muted">- Sin carga asignada -</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- 5. UNIDADES ACADÉMICAS --}}
        <div class="section-header">IV. Áreas de Vinculación</div>
        <div class="border p-3" style="background-color: #fcfcfc;">
            @if($docente->carreras->isNotEmpty())
                <div class="row">
                @foreach($docente->carreras as $carrera)
                    <div class="col-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check2-square me-2 text-primary"></i>
                            <div>
                                {{-- CORRECCIÓN: NombreCarrera y NombreFacultad --}}
                                <div class="fw-bold" style="font-size: 10px;">{{ $carrera->NombreCarrera }}</div>
                                <div class="text-muted" style="font-size: 9px;">{{ $carrera->facultad->NombreFacultad ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="text-muted text-center fst-italic">Sin vinculación específica.</div>
            @endif
        </div>

        {{-- PIE DE PÁGINA --}}
        <div class="row" style="margin-top: 80px;">
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <div class="fw-bold text-uppercase" style="font-size: 10px;">{{ $docente->ApellidoPaterno }} {{ $docente->ApellidoMaterno }}</div>
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