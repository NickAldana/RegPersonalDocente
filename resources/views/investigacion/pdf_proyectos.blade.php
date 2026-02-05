<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte Consolidado de Proyectos - SIA</title>
    <style>
        /* Tipografía compatible con DomPDF */
        @page { margin: 1cm; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8pt;
            color: #1a202c;
            margin: 0;
            padding: 0;
        }

        /* IDENTIDAD INSTITUCIONAL */
        .header {
            width: 100%;
            border-bottom: 3px solid #003566;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .brand-box { width: 20%; vertical-align: top; }
        .title-box { width: 60%; text-align: center; vertical-align: middle; }
        .meta-box { width: 20%; text-align: right; font-size: 7pt; color: #718096; }

        .logo-upds { font-size: 22pt; font-weight: bold; color: #003566; margin: 0; }
        .sub-logo { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #1a202c; }
        
        .main-title { 
            font-size: 14pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #1a202c;
            margin: 0;
        }
        .subtitle { font-size: 9pt; color: #4a5568; margin-top: 5px; }

        /* TABLA MAESTRA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #003566;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7pt;
            border: 1px solid #003566;
            padding: 8px 4px;
            text-align: center;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 4px;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) { background-color: #f8fafc; }

        /* ESTILOS DE CONTENIDO */
        .project-name { font-weight: bold; font-size: 8.5pt; color: #003566; display: block; margin-bottom: 2px; }
        .line-label { font-size: 7pt; color: #4a5568; font-style: italic; }
        .code-badge { 
            font-family: 'Courier', monospace; 
            background-color: #edf2f7; 
            padding: 1px 3px; 
            font-size: 7pt; 
            border-radius: 2px;
        }

        /* EQUIPO */
        .team-box { font-size: 7pt; line-height: 1.2; }
        .member-item { margin-bottom: 3px; border-bottom: 0.5px solid #edf2f7; padding-bottom: 1px; }
        .member-name { font-weight: bold; }
        .member-role { color: #718096; font-size: 6.5pt; }
        .is-lead { color: #b45309; font-weight: bold; }

        /* STATUS BADGES */
        .status {
            font-weight: bold;
            font-size: 6.5pt;
            text-transform: uppercase;
            text-align: center;
            padding: 3px 0;
            border-radius: 3px;
            color: #000;
            border: 1px solid #cbd5e1;
        }
        .st-ejecucion { background-color: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .st-planificado { background-color: #fef3c7; color: #92400e; border-color: #fde68a; }
        .st-otros { background-color: #f1f5f9; color: #475569; }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="brand-box">
                <div class="logo-upds">UPDS</div>
                <div class="sub-logo">Santa Cruz</div>
            </td>
            <td class="title-box">
                <h1 class="main-title">Cartera Consolidada de Proyectos</h1>
                <div class="subtitle">
                    Dirección de Investigación | SIA v4.0
                    @if(request('estado')) - Filtrado: {{ strtoupper(request('estado')) }} @endif
                </div>
            </td>
            <td class="meta-box">
                <strong>Emisión:</strong> {{ date('d/m/Y') }}<br>
                <strong>Hora:</strong> {{ date('H:i') }}<br>
                <strong>Pág:</strong> 1 de 1
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="28%">Proyecto / Línea de Investigación</th>
                <th width="15%">Unidad Académica</th>
                <th width="32%">Cuerpo Científico Asignado</th>
                <th width="10%">Cronograma</th>
                <th width="10%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyectos as $proyecto)
            <tr>
                <td style="text-align: center; font-weight: bold; color: #64748b;">
                    {{ str_pad($proyecto->ProyectoinvestigacionID, 4, '0', STR_PAD_LEFT) }}
                </td>
                <td>
                    <span class="project-name">{{ $proyecto->Nombreproyecto }}</span>
                    <span class="line-label">Línea: {{ $proyecto->linea->Nombrelineainvestigacion ?? 'General' }}</span><br>
                    <span class="code-badge">{{ $proyecto->CodigoProyecto }}</span>
                </td>
                <td>
                    <span style="font-weight: bold;">{{ $proyecto->carrera->Nombrecarrera ?? 'Institucional' }}</span>
                </td>
                <td class="team-box">
                    @foreach($proyecto->equipo as $miembro)
                        <div class="member-item">
                            <span class="member-name">{{ $miembro->Apellidopaterno }} {{ $miembro->Nombrecompleto }}</span>
                            @if($miembro->pivot->EsResponsable) <span class="is-lead">(Líder)</span> @endif
                            <br>
                            <span class="member-role">{{ $miembro->pivot->Rol }} | 
                                {{ \Carbon\Carbon::parse($miembro->pivot->FechaInicio)->format('d/m/y') }} - 
                                {{ $miembro->pivot->FechaFin ? \Carbon\Carbon::parse($miembro->pivot->FechaFin)->format('d/m/y') : 'Activo' }}
                            </span>
                        </div>
                    @endforeach
                </td>
                <td style="text-align: center;">
                    <strong>Inicio:</strong><br>
                    {{ \Carbon\Carbon::parse($proyecto->Fechainicio)->format('d/m/Y') }}
                </td>
                <td>
                    @php
                        $clase = match($proyecto->Estado) {
                            'En Ejecución' => 'st-ejecucion',
                            'Planificado' => 'st-planificado',
                            default => 'st-otros'
                        };
                    @endphp
                    <div class="status {{ $clase }}">
                        {{ $proyecto->Estado }}
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 30px;">No se encontraron registros activos.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte oficial del Vicerrectorado de Investigación UPDS. 
        Generado digitalmente por SIA Platform el {{ date('d/m/Y H:i') }}.
    </div>

</body>
</html>