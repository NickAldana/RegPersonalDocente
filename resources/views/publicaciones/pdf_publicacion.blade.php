<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kardex_Produccion_Cientifica_{{ date('Ymd') }}</title>
    
    <style>
        /* CONFIGURACIÓN PROFESIONAL DOMPDF */
        @page { 
            margin: 0.8cm 1.5cm 1.5cm 1.5cm; 
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 10px; 
            color: #111827; 
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* ENCABEZADO INSTITUCIONAL */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .logo-text { color: #003566; font-weight: 900; font-size: 24px; }
        .sub-logo { text-transform: uppercase; font-weight: 700; color: #6b7280; font-size: 8.5px; letter-spacing: 1px; }
        .report-title { text-align: right; text-transform: uppercase; }
        .report-title h2 { margin: 0; font-size: 14px; font-weight: 900; color: #111827; }
        .report-title p { margin: 2px 0 0 0; font-size: 9px; font-weight: 700; color: #003566; }

        /* BLOQUE DE CERTIFICACIÓN - TEXTO CORREGIDO */
        .summary-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 4px;
            font-size: 9.5px;
            color: #374151;
            margin-bottom: 18px;
            text-align: justify;
        }

        .section-header { 
            background-color: #003566; 
            color: white; 
            padding: 6px 12px; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 10px; 
            margin-bottom: 12px; 
            border-left: 5px solid #ffc300; 
        }

        /* TABLA DE DATOS - FORMATO FORMAL */
        .table-custom { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-custom th { 
            background-color: #f3f4f6; 
            border: 1px solid #d1d5db; 
            padding: 8px; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 8.5px; 
            color: #003566; 
            text-align: left;
        }
        .table-custom td { 
            border: 1px solid #e5e7eb; 
            padding: 10px 8px; 
            vertical-align: top; 
            word-wrap: break-word;
        }
        
        /* ESTILOS DE TEXTO INTERNOS CORREGIDOS */
        .pub-title { font-weight: 700; color: #000; display: block; margin-bottom: 4px; font-size: 10px; text-transform: uppercase; }
        .pub-authors { font-size: 9px; color: #4b5563; margin-bottom: 3px; }
        .pub-medio { font-size: 8.5px; color: #6b7280; }
        .badge-type { background-color: #e5e7eb; padding: 2px 6px; border-radius: 3px; font-weight: 700; font-size: 8px; text-transform: uppercase; color: #374151; }
        
        /* ESTADOS DE REFERENCIA */
        .ref-evidencia { color: #dc2626; font-weight: 700; font-size: 8px; text-align: center; }
        .ref-respaldo { color: #059669; font-weight: 700; font-size: 8px; text-align: center; }

        /* FIRMAS ALINEADAS */
        .signature-table { width: 100%; margin-top: 60px; border-collapse: collapse; }
        .signature-box { border-top: 1px solid #111827; width: 220px; margin: 0 auto; padding-top: 6px; text-align: center; font-size: 10px; font-weight: 700; }

        .footer-note { 
            position: fixed; bottom: 1.2cm; width: 100%; 
            border-top: 1px solid #e5e7eb; padding-top: 6px; 
            font-size: 8px; color: #9ca3af; text-align: center; 
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="logo-text">UPDS</div>
                <div class="sub-logo">Universidad Privada Domingo Savio</div>
            </td>
            <td class="report-title">
                <h2>Kardex de Producción Científica</h2>
                <p>Gestión Académica 2026 | Fecha: {{ date('d/m/Y') }}</p>
            </td>
        </tr>
    </table>

    <div class="summary-box">
        <strong>CERTIFICACIÓN INSTITUCIONAL:</strong> El presente documento consolida el registro oficial de la producción intelectual e investigación científica[cite: 19]. La información contenida tiene carácter de declaración jurada para fines de acreditación universitaria y escalafón docente del <strong>Sistema de Información Académica (SIA)</strong>[cite: 20].
    </div>

    <div class="section-header">Listado Consolidado de Obras e Investigaciones</div>
    
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 45%">Detalle de la Obra y Autores</th>
                <th style="width: 18%">Clasificación</th>
                <th style="width: 22%">Contexto Institucional</th>
                <th style="width: 15%">Referencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($publicaciones as $pub)
            <tr>
                <td>
                    <span class="pub-title">{{ $pub->Nombrepublicacion }}</span>
                    <div class="pub-authors">
                        <strong>Autores:</strong> 
                        @foreach($pub->autores as $autor)
                            {{ $autor->Apellidopaterno }} {{ substr($autor->Nombrecompleto, 0, 1) }}.{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>
                    <div class="pub-medio">
                        <strong>Medio:</strong> {{ $pub->medio->Nombremedio ?? 'Sin especificar' }}
                    </div>
                </td>
                <td style="text-align: center;">
                    <span class="badge-type">{{ $pub->tipo->Nombretipo ?? 'General' }}</span>
                    <div style="margin-top: 6px; font-size: 9px; color: #4b5563;">
                        <strong>Año:</strong> {{ $pub->Fechapublicacion ? $pub->Fechapublicacion->format('Y') : 'S/R' }}
                    </div>
                </td>
                <td>
                    @if($pub->proyecto)
                        <div style="font-size: 9px; margin-bottom: 4px;">
                            <strong style="color: #003566;">Proyecto:</strong> {{ $pub->proyecto->CodigoProyecto }}<br>
                            <span style="color: #6b7280;">{{ $pub->proyecto->carrera->Nombrecarrera ?? '' }}</span>
                        </div>
                    @else
                        <div style="color: #9ca3af; font-style: italic; font-size: 9px; margin-bottom: 4px;">Producción Independiente</div>
                    @endif
                    <div style="font-size: 8px; color: #4b5563; border-top: 1px solid #f3f4f6; padding-top: 3px;">
                        Línea: {{ $pub->linea->Nombrelineainvestigacion ?? 'S/L' }}
                    </div>
                </td>
                <td style="vertical-align: middle;">
                    @if($pub->RutaArchivo || $pub->UrlPublicacion)
                        <div class="ref-respaldo">RESPALDO</div>
                    @else
                        <div class="ref-evidencia">S/EVIDENCIA</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td width="50%">
                <div class="signature-box">
                    RESPONSABLE DE ÁREA
                    <div style="font-weight: 400; font-size: 8.5px; margin-top: 2px;">Firma y Sello [cite: 28]</div>
                </div>
            </td>
            <td width="50%">
                <div class="signature-box">
                    VICERRECTORADO ACADÉMICO
                    <div style="font-weight: 400; font-size: 8.5px; margin-top: 2px;">Certificación SIA [cite: 29, 31]</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Este reporte es propiedad de la Universidad Privada Domingo Savio. Toda reproducción total o parcial debe ser autorizada[cite: 25, 32].
        <br><strong>SIA - Inteligencia de Datos Académicos v4.0</strong> [cite: 26, 33]
    </div>

</body>
</html>