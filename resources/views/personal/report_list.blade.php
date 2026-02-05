<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte_Nomina_Oficial</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA (Carta Vertical) */
        @page {
            /* Márgenes: Arriba, Derecha, Abajo, Izquierda */
            margin: 3.5cm 1.5cm 3.5cm 1.5cm; 
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        body {
            font-size: 9px;
            color: #444;
            line-height: 1.3;
        }

        /* 2. ENCABEZADO FIJO (Repite en cada hoja) */
        header {
            position: fixed;
            top: -2.8cm;
            left: 0;
            right: 0;
            height: 2.5cm;
            border-bottom: 2px solid #003566; /* Azul UPDS */
        }

        /* 3. PIE DE PÁGINA FIJO (Repite en cada hoja) */
        footer {
            position: fixed;
            bottom: -3.2cm; /* Bajamos el pie para aprovechar el margen inferior */
            left: 0;
            right: 0;
            height: 3cm; /* Altura suficiente para contacto + legal */
            /* El borde superior lo manejamos en la tabla interna */
        }

        /* 4. MARCA DE AGUA */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            width: 100%;
            transform: translate(-50%, -50%) rotate(-45deg);
            text-align: center;
            font-size: 110px;
            font-weight: 900;
            color: #003566;
            opacity: 0.04;
            z-index: -1000;
            white-space: nowrap;
        }

        /* 5. TABLAS Y UTILIDADES */
        .w-100 { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-upper { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #003566; }
        .text-gold { color: #d97706; }

        /* 6. TABLA DE DATOS */
        .data-table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        
        .data-table th {
            background-color: #003566;
            color: white;
            padding: 6px 8px;
            font-size: 8px;
            text-transform: uppercase;
            text-align: left;
            border: 1px solid #00284d;
        }
        
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            vertical-align: middle;
            font-size: 9px;
        }
        
        /* Zebra Striping (Funciona si el motor PDF lo soporta) */
        .data-table tr:nth-child(even) { background-color: #f8fafc; }

        /* 7. ESTADOS (Badges) */
        .badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 7px; text-transform: uppercase; }
        .active { color: #065f46; background-color: #d1fae5; border: 1px solid #a7f3d0; }
        .inactive { color: #991b1b; background-color: #fee2e2; border: 1px solid #fecaca; }

        /* DETALLES DEL FOOTER */
        .footer-table {
            width: 100%;
            border-top: 2px solid #003566; /* Línea separadora azul fuerte */
            padding-top: 8px;
            margin-bottom: 8px;
        }

        .footer-cell {
            vertical-align: top;
            font-size: 8px;
            line-height: 1.4;
            color: #444;
        }

        .legal-block {
            font-size: 7px; 
            color: #888; 
            text-align: justify; 
            border-top: 1px dotted #ccc; /* Separador sutil para el aviso legal */
            padding-top: 4px;
            margin-top: 4px;
        }
        
    </style>
</head>
<body>

    <div class="watermark">DOCUMENTO OFICIAL UPDS</div>

    <header>
        <table class="w-100">
            <tr>
                <td style="width: 30%; vertical-align: top;">
                    <div style="font-size: 26px; font-weight: 900; color: #003566; line-height: 1;">UPDS</div>
                    <div style="font-size: 7px; font-weight: bold; letter-spacing: 3px; color: #d97706; margin-bottom: 5px;">SANTA CRUZ</div>
                    
                    <div style="font-size: 8px; color: #333;">
                        <strong>VICERRECTORADO ACADÉMICO</strong><br>
                        Dirección de Talento Humano
                    </div>
                </td>

                <td style="width: 40%; vertical-align: middle; text-align: center;">
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase; color: #111;">
                        Nómina de Personal Docente
                    </div>
                    <div style="font-size: 8px; color: #555; margin-top: 4px; padding: 2px 10px; border: 1px dashed #ccc; display: inline-block;">
                        {{ $subtitulo }}
                    </div>
                </td>

                <td style="width: 30%; vertical-align: top; text-align: right;">
                    <table style="width: 100%; font-size: 8px; color: #555;">
                        <tr>
                            <td class="text-right font-bold">CÓDIGO:</td>
                            <td class="text-right">RRHH-LIST-{{ date('Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">VERSIÓN:</td>
                            <td class="text-right">SIA v4.0</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">FECHA:</td>
                            <td class="text-right">{{ date('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">USUARIO:</td>
                            <td class="text-right text-upper">{{ auth()->user()->name ?? 'SISTEMA' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td class="footer-cell" style="width: 35%;">
                    <strong class="text-blue text-upper">Sede Central Santa Cruz</strong><br>
                    Av. Beni, 3er Anillo Externo<br>
                    Teléfono: +591 3 342-6600<br>
                    Santa Cruz de la Sierra, Bolivia
                </td>

                <td class="footer-cell" style="width: 30%; text-align: center;">
                    <strong class="text-blue">www.upds.edu.bo</strong><br>
                    info@upds.edu.bo<br>
                    <span class="text-gold" style="font-weight: bold; font-style: italic;">"Profesionales más Humanos"</span>
                </td>

                <td class="footer-cell" style="width: 35%; text-align: right;">
                    <div style="font-weight: bold; color: #003566;">SISTEMA INTEGRADO (SIA)</div>
                    <div>Reporte generado automáticamente</div>
                    </td>
            </tr>
        </table>

        <div class="legal-block">
            <strong>Aviso de Confidencialidad:</strong> Este documento contiene información de carácter personal y académico perteneciente a la Universidad Privada Domingo Savio. Su uso está restringido exclusivamente para fines administrativos internos. Cualquier reproducción total o parcial sin autorización está prohibida. Generado automáticamente por el Sistema Integrado de Acreditación (SIA).
        </div>

        <script type="text/php">
            if (isset($pdf)) {
                $font = $fontMetrics->getFont("Helvetica", "bold");
                $size = 8;
                $text = "Pág. {PAGE_NUM} / {PAGE_COUNT}";
                
                // Coordenadas calculadas para hoja Carta (Letter) con márgenes
                $width = $fontMetrics->get_text_width($text, $font, $size);
                $x = ($pdf->get_width() - $width) - 40; // Alineado a la derecha
                $y = $pdf->get_height() - 85; // Altura ajustada dentro del footer
                
                $pdf->page_text($x, $y, $text, $font, $size, array(0.4, 0.4, 0.4));
            }
        </script>
    </footer>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 12%;">ID Personal</th>
                <th style="width: 33%;">Apellidos y Nombres</th>
                <th style="width: 25%;">Cargo / Contrato</th>
                <th style="width: 15%;">Contacto</th>
                <th style="width: 10%; text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personales as $i => $p)
            <tr>
                <td class="text-center font-bold">{{ $i + 1 }}</td>
                
                <td>
                    <div class="font-bold">{{ $p->Ci }}</div>
                    <div style="font-size: 7px; color: #777;">COD: {{ str_pad($p->PersonalID, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
                
                <td class="text-upper">
                    <span class="font-bold text-blue">{{ $p->Apellidopaterno }} {{ $p->Apellidomaterno }}</span>,
                    {{ $p->Nombrecompleto }}
                </td>
                
                <td>
                    <div class="font-bold text-upper" style="font-size: 8px;">
                        {{ $p->cargo->Nombrecargo ?? 'SIN ASIGNAR' }}
                    </div>
                    <div style="font-size: 7px; text-transform: uppercase; color: #666; margin-top: 1px;">
                        {{ $p->contrato->Nombrecontrato ?? 'SIN CONTRATO' }}
                    </div>
                </td>
                
                <td>
                    <div style="font-size: 8px;">{{ $p->Telelefono }}</div>
                    <div style="font-size: 7px; color: #555;">{{ Str::limit($p->Correoelectronico, 22) }}</div>
                </td>
                
                <td class="text-center">
                    @if($p->Activo)
                        <span class="badge active">ACTIVO</span>
                    @else
                        <span class="badge inactive">BAJA</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px; border-top: 1px dotted #999; padding-top: 5px;">
        <table class="w-100">
            <tr>
                <td style="font-size: 9px;">
                    <strong>Resumen:</strong> Se listan <strong>{{ count($personales) }}</strong> registros en total.
                </td>
                <td style="text-align: right; width: 40%;">
                    <div style="margin-top: 20px; border-top: 1px solid #333; text-align: center; font-size: 8px; width: 100%;">
                        Firma y Sello Responsable
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>