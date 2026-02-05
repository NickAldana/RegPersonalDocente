<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte_General_Profesion_Contratos_{{ date('Ymd') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --upds-blue: #003566; --upds-gold: #ffc300; }
        body { background: #e9ecef; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            position: relative;
        }

        .header-report {
            border-bottom: 3px solid var(--upds-blue);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .section-header {
            background: var(--upds-blue) !important;
            color: white !important;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            border-left: 5px solid var(--upds-gold);
            margin: 20px 0 10px 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .kpi-card {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            background: #f8f9fa;
        }
        .kpi-value { 
            font-size: 22px; 
            font-weight: 900; 
            color: var(--upds-blue); 
            display: block; 
            line-height: 1; 
        }
        .kpi-label { 
            font-size: 10px; 
            font-weight: 700; 
            text-transform: uppercase; 
            color: #6c757d; 
            margin-top: 5px; 
            display: block; 
        }

        .table-custom { 
            width: 100%; 
            font-size: 10px; 
            margin-bottom: 20px; 
            border-collapse: collapse; 
        }
        .table-custom thead { 
            display: table-header-group; /* Repite cabecera en impresión */ 
        }
        .table-custom th { 
            background: #f2f2f2 !important; 
            padding: 8px; 
            border: 1px solid #dee2e6; 
            text-transform: uppercase; 
            -webkit-print-color-adjust: exact; /* For WebKit browsers */
            print-color-adjust: exact; /* Standard property */
        }
        .table-custom td { 
            padding: 7px; 
            border: 1px solid #dee2e6; 
            vertical-align: middle; 
        }

        @media print {
            body { 
                background: white; 
                margin: 0; 
            }
            .a4-page { 
                margin: 0; 
                box-shadow: none; 
                width: 100%; 
                padding: 10mm; 
            }
            .no-print { 
                display: none; 
            }
            .page-break { 
                page-break-before: always; 
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center py-4">
    <button onclick="window.print()" class="btn btn-dark shadow-sm px-5 fw-bold">
        IMPRIMIR REPORTE EJECUTIVO
    </button>
</div>

<div class="a4-page">
    {{-- Encabezado --}}
    <div class="header-report d-flex justify-content-between align-items-end">
        <div>
            <h2 class="fw-black m-0" style="color:var(--upds-blue); font-weight: 900;">UPDS</h2>
            <small class="fw-bold text-muted">VICERRECTORADO SANTA CRUZ</small>
        </div>
        <div class="text-end">
            <h4 class="m-0 fw-bold">REPORTE EJECUTIVO GENERAL</h4>
            <p class="m-0 small text-primary fw-bold">PROFESIÓN Y CONTRATOS DOCENTES</p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-3">
            <div class="kpi-card">
                <span class="kpi-value">{{ $docentes->count() }}</span>
                <span class="kpi-label">Total Docentes</span>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-card">
                <span class="kpi-value text-success">100%</span>
                <span class="kpi-label">Cumplimiento</span>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-card">
                @php $carrerasCount = $docentes->whereNotNull('Carrera')->unique('Carrera')->count(); @endphp
                <span class="kpi-value">{{ $carrerasCount }}</span>
                <span class="kpi-label">Carreras</span>
            </div>
        </div>
        <div class="col-3">
            <div class="kpi-card">
                <span class="kpi-value">{{ date('Y') }}</span>
                <span class="kpi-label">Gestión</span>
            </div>
        </div>
    </div>

    {{-- Distribución --}}
    <div class="section-header">I. Distribución por Carrera</div>
    <table class="table-custom">
        <thead>
            <tr>
                <th width="60%">Carrera / Facultad</th>
                <th width="20%" class="text-center">Docentes</th>
                <th width="20%" class="text-center">% Participación</th>
            </tr>
        </thead>
        <tbody>
            @php
                $agrupados = $docentes->groupBy('Carrera');
                $total = $docentes->count();
            @endphp
            @foreach($agrupados as $carrera => $items)
            <tr>
                <td class="fw-bold">{{ $carrera ?: 'Área Común / Otros' }}</td>
                <td class="text-center">{{ $items->count() }}</td>
                <td class="text-center text-muted">
                    {{ $total > 0 ? number_format(($items->count() / $total) * 100, 1) : 0 }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Detalle --}}
    <div class="section-header">II. Detalle de Profesión y Vinculación</div>
    <table class="table-custom table-striped">
        <thead>
            <tr class="text-center">
                <th>Cód</th>
                <th>Nombre Completo</th>
                <th>Tipo de Contrato</th>
                <th>Profesión / Título</th>
            </tr>
        </thead>
        <tbody>
            @forelse($docentes as $doc)
            <tr>
                <td class="text-center text-muted" style="font-size: 9px;">{{ $doc->IdPersonal }}</td>
                <td class="fw-bold">{{ $doc->ApellidoPaterno }} {{ $doc->ApellidoMaterno }} {{ $doc->NombreCompleto }}</td>
                <td><span class="badge border text-dark fw-normal">{{ $doc->contrato->NombreContrato ?? 'Sin Contrato' }}</span></td>
                <td class="text-uppercase" style="font-size: 8px; color: #555;">
                    {{ optional($doc->formaciones->first())->TituloObtenido ?? 'SIN REGISTRO ACADÉMICO' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-4">No se encontraron registros en la base de datos</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Firmas --}}
    <div style="margin-top: 50px;" class="row text-center">
        <div class="col-4 offset-1">
            <div style="border-top: 1px solid #aaa; padding-top: 8px;">
                <small class="fw-bold d-block">Elaborado por:</small>
                <small class="text-muted">SIA Engine V2 - RRHH</small>
            </div>
        </div>
        <div class="col-4 offset-2">
            <div style="border-top: 1px solid #aaa; padding-top: 8px;">
                <small class="fw-bold d-block">Revisado por:</small>
                <small class="text-muted">Vicerrectorado Académico</small>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div style="margin-top: 30px; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px;" class="d-flex justify-content-between">
        <span>SIA-UPDS | Business Intelligence Unit</span>
        <span>Generado: {{ date('d/m/Y H:i:s') }}</span>
        <span>Santa Cruz - Bolivia</span>
    </div>
</div>

</body>
</html>