<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta_Expediente_{{ $proyecto->CodigoProyecto }}</title>
    <style>
        /** CONFIGURACIÓN DE PÁGINA (A4 Vertical) */
        @page {
            margin: 2.5cm 2.5cm 3cm 2.5cm;
            font-family: 'Times New Roman', serif;
        }

        body {
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }

        /* --- ENCABEZADO FIJO --- */
        header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            border-bottom: 2px solid #000;
        }
        
        .header-logo { font-family: sans-serif; font-size: 18px; font-weight: 900; color: #003566; text-transform: uppercase; }
        .header-sub { font-family: sans-serif; font-size: 8px; text-transform: uppercase; letter-spacing: 2px; }
        .header-meta { font-family: sans-serif; font-size: 8px; text-align: right; }

        /* --- PIE DE PÁGINA FIJO --- */
        footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1cm;
            border-top: 1px solid #000;
            font-size: 8px;
            text-align: center;
            padding-top: 5px;
            font-family: sans-serif;
        }

        /* --- ESTILOS GENERALES --- */
        .doc-title { text-align: center; font-size: 14px; font-weight: bold; text-transform: uppercase; margin-top: 20px; text-decoration: underline; }
        .doc-ref { text-align: center; font-size: 10px; font-weight: bold; margin-bottom: 20px; font-family: sans-serif; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #000; margin-top: 20px; margin-bottom: 10px; padding-bottom: 2px; font-family: sans-serif; }
        
        /* --- TABLAS --- */
        .formal-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-family: sans-serif; }
        .formal-table th, .formal-table td { border: 1px solid #000; padding: 5px 8px; vertical-align: top; font-size: 9px; }
        .formal-table th { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; width: 25%; }

        /* --- ESTADOS --- */
        .status-active { font-weight: bold; }
        .status-retired { font-style: italic; color: #444; }

        /* --- FIRMAS --- */
        .signatures-table { width: 100%; margin-top: 60px; border: none; }
        .signatures-table td { width: 33%; vertical-align: bottom; text-align: center; padding: 0 15px; height: 80px; }
        .sig-line { border-top: 1px solid #000; margin-bottom: 5px; }
        .sig-role { font-size: 8px; font-weight: bold; text-transform: uppercase; font-family: sans-serif; }
    </style>
</head>
<body>

    <header>
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <div class="header-logo">UPDS</div>
                    <div class="header-sub">Vicerrectorado de Investigación - Santa Cruz</div>
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <div class="header-meta">
                        <strong>REPORTE DE AUDITORÍA (SIA v4.0)</strong><br>
                        Fecha: {{ date('d/m/Y') }} | Hora: {{ date('H:i') }}<br>
                        Usuario: {{ auth()->user()->name ?? 'Sistema' }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Documento oficial generado digitalmente. Su validez está sujeta a la verificación en la base de datos institucional.<br>
        Av. Beni 3er Anillo Externo | Santa Cruz de la Sierra - Bolivia
    </footer>

    <div class="doc-title">ACTA DE KARDEX DE PROYECTO CIENTÍFICO</div>
    <div class="doc-ref">EXPEDIENTE N° {{ $proyecto->CodigoProyecto }}</div>

    <div class="section-title">I. IDENTIFICACIÓN Y ESTADO DEL PROYECTO</div>
    <table class="formal-table">
        <tr>
            <th>Denominación Oficial</th>
            <td colspan="3" style="font-weight: bold;">{{ $proyecto->Nombreproyecto }}</td>
        </tr>
        <tr>
            <th>Unidad Académica</th>
            <td>{{ $proyecto->carrera->Nombrecarrera ?? 'Institucional' }}</td>
            <th>Línea de Investigación</th>
            <td>{{ $proyecto->linea->Nombrelineainvestigacion ?? 'Multidisciplinaria' }}</td>
        </tr>
        <tr>
            <th>Estado Actual</th>
            <td style="font-weight: bold; text-transform: uppercase;">{{ $proyecto->Estado }}</td>
            <th>Vigencia Acumulada</th>
            <td>
                @php
                    $inicio = \Carbon\Carbon::parse($proyecto->Fechainicio);
                    $fin = $proyecto->Fechafinalizacion ? \Carbon\Carbon::parse($proyecto->Fechafinalizacion) : now();
                    $dias = floor($inicio->diffInDays($fin));
                @endphp
                {{ number_format($dias, 0, ',', '.') }} Días Calendario
            </td>
        </tr>
        <tr>
            <th>Periodo de Ejecución</th>
            <td colspan="3">
                Inicio: {{ $inicio->format('d/m/Y') }} 
                &nbsp;|&nbsp; 
                Cierre: {{ $proyecto->Fechafinalizacion ? \Carbon\Carbon::parse($proyecto->Fechafinalizacion)->format('d/m/Y') : 'VIGENTE (En curso)' }}
            </td>
        </tr>
    </table>

    <div class="section-title">II. NÓMINA DE INVESTIGADORES Y TRAZABILIDAD</div>
    <div style="font-size: 9px; margin-bottom: 5px; text-align: justify; font-family: sans-serif;">
        Se certifica que el siguiente personal científico forma o formó parte del presente proyecto, según los registros de alta y baja consignados en el sistema:
    </div>
    
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">N°</th>
                <th style="width: 35%;">Investigador</th>
                <th style="width: 25%;">Función Asignada</th>
                <th style="width: 20%; text-align: center;">Gestión (Desde - Hasta)</th>
                <th style="width: 15%; text-align: center;">Situación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyecto->equipo as $index => $miembro)
                @php $retirado = !is_null($miembro->pivot->FechaFin); @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-transform: uppercase;">
                        <strong>{{ $miembro->Apellidopaterno }}</strong>, {{ $miembro->Nombrecompleto }}
                        <br><span style="font-size: 7px; color: #555;">ID: {{ $miembro->PersonalID }}</span>
                    </td>
                    <td>
                        {{ $miembro->pivot->Rol }}
                        @if($miembro->pivot->EsResponsable)
                            <br><strong>(RESPONSABLE)</strong>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ \Carbon\Carbon::parse($miembro->pivot->FechaInicio)->format('d/m/Y') }}
                        <br>-<br>
                        {{ $retirado ? \Carbon\Carbon::parse($miembro->pivot->FechaFin)->format('d/m/Y') : 'Actualidad' }}
                    </td>
                    <td style="text-align: center;">
                        @if($retirado)
                            <span class="status-retired">BAJA</span>
                        @else
                            <span class="status-active">ACTIVO</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($proyecto->publicaciones->count() > 0)
        <div class="section-title">III. PRODUCCIÓN INTELECTUAL VINCULADA</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <th>Título de la Publicación</th>
                    <th style="width: 25%;">Categoría / Tipo</th>
                    <th style="text-align: center; width: 10%;">Año</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyecto->publicaciones as $pub)
                <tr>
                    {{-- CORRECCIÓN APLICADA: Nombrepublicacion --}}
                    <td>{{ $pub->Nombrepublicacion }}</td>
                    
                    {{-- CORRECCIÓN APLICADA: Nombretipo (desde la relación) --}}
                    <td>{{ $pub->tipo->Nombretipo ?? 'No Definido' }}</td>
                    
                    {{-- CORRECCIÓN APLICADA: Formato de Fecha para Año --}}
                    <td style="text-align: center;">
                        {{ $pub->Fechapublicacion ? $pub->Fechapublicacion->format('Y') : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="section-title">III. PRODUCCIÓN INTELECTUAL</div>
        <div style="font-size: 9px; font-style: italic; padding: 5px; border: 1px dashed #ccc; text-align: center; font-family: sans-serif;">
            No se registran publicaciones científicas a la fecha de corte.
        </div>
    @endif

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="sig-role">Responsable del Proyecto</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-role">Jefe de Investigación</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-role">VoBo Vicerrectorado</div>
            </td>
        </tr>
    </table>

</body>
</html>