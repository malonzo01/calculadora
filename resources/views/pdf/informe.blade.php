<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de WhatsApp - Capybaras Digitales</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #161E11;
            font-size: 11px;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .header {
            border-bottom: 2px solid #33521c;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #33521c;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #835425;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 3px;
        }
        .report-meta {
            text-align: right;
            font-size: 10px;
            color: #525b4d;
        }
        .intro-box {
            background-color: #f7f9f5;
            border-left: 4px solid #33521c;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .intro-title {
            font-size: 13px;
            font-weight: bold;
            color: #33521c;
            margin-bottom: 4px;
        }
        .cards-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 15px;
        }
        .card {
            background-color: #FDFBF7;
            border: 1px solid #d8ddd3;
            border-radius: 8px;
            padding: 14px;
            vertical-align: top;
            width: 33.33%;
        }
        .card-meta {
            border-top: 3px solid #33521c;
        }
        .card-mano {
            border-top: 3px solid #835425;
        }
        .card-escape {
            border-top: 3px solid #DAA520;
        }
        .card-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            color: #525b4d;
            margin-bottom: 6px;
        }
        .card-amount-usd {
            font-size: 18px;
            font-weight: bold;
            color: #161E11;
            margin-bottom: 2px;
        }
        .card-amount-bs {
            font-size: 11px;
            font-weight: bold;
            color: #835425;
            margin-bottom: 6px;
        }
        .card-desc {
            font-size: 9.5px;
            color: #525b4d;
            line-height: 1.3;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #33521c;
            border-bottom: 1px solid #d8ddd3;
            padding-bottom: 5px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th {
            background-color: #e8f1dc;
            color: #33521c;
            text-align: left;
            padding: 7px 10px;
            font-size: 10px;
            border: 1px solid #d8ddd3;
        }
        table.data-table td {
            padding: 6px 10px;
            font-size: 10px;
            border: 1px solid #d8ddd3;
        }
        .highlight-box {
            background-color: #fdf7e7;
            border: 1px solid #DAA520;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .footer {
            border-top: 1px solid #d8ddd3;
            padding-top: 10px;
            margin-top: 25px;
            font-size: 9.5px;
            color: #525b4d;
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/logo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <table style="width: 100%;" class="header">
        <tr>
            @if($logoBase64)
            <td style="width: 58px; vertical-align: middle;">
                <img src="{{ $logoBase64 }}" style="width: 50px; height: 50px;" alt="Capybaras Digitales">
            </td>
            @endif
            <td style="vertical-align: middle;">
                <div class="brand-title">Capybaras Digitales C.A.</div>
                <div class="brand-subtitle">Auditoría Financiera de WhatsApp · Moneda Base $ USD & Bolívares</div>
            </td>
            <td class="report-meta" style="vertical-align: middle;">
                <strong>ID Simulación:</strong> {{ substr($simulacion->uuid ?? 'sim-'.time(), 0, 8) }}<br>
                <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}<br>
                <strong>Tasa Oficial BCV:</strong> Bs. {{ number_format($metricas['tasa_bcv'], 4, ',', '.') }} / USD<br>
                @if($simulacion->empresa)
                    <strong>Cliente:</strong> {{ $simulacion->empresa }}<br>
                @endif
            </td>
        </tr>
    </table>

    <div class="intro-box">
        <div class="intro-title">Diagnóstico Financiero de WhatsApp</div>
        Comparativa directa entre el costo oficial de la API de Meta, el costo laboral real de contestar a mano y el dinero que se pierde por consultas que quedan sin respuesta inmediata. Expresado en <strong>dólares ($ USD)</strong> y en <strong>bolívares (Bs.)</strong> a la tasa oficial del Banco Central de Venezuela.
    </div>

    <!-- Las 3 Cuentas Clave en USD y Bs -->
    <table class="cards-table">
        <tr>
            <td class="card card-meta">
                <div class="card-label">1. Te cobra Meta (API)</div>
                <div class="card-amount-usd">${{ number_format($metricas['meta_usd'], 2, '.', ',') }} USD</div>
                <div class="card-amount-bs">Bs. {{ number_format($metricas['meta_bs'], 2, ',', '.') }}</div>
                <div class="card-desc">
                    @if($metricas['pagos'] == 0)
                        Tus {{ number_format($metricas['msj_servicio'], 0, ',', '.') }} mensajes entran en los 1.000 gratis mensuales de Meta.
                    @else
                        1.000 gratis + {{ number_format($metricas['pagos'], 0, ',', '.') }} mensajes pagos a USD $0,026 c/u.
                    @endif
                </div>
            </td>
            <td class="card card-mano">
                <div class="card-label">2. Te cuesta a mano</div>
                <div class="card-amount-usd">${{ number_format($metricas['a_mano_usd'], 2, '.', ',') }} USD</div>
                <div class="card-amount-bs">Bs. {{ number_format($metricas['a_mano_bs'], 2, ',', '.') }}</div>
                <div class="card-desc">
                    {{ number_format($metricas['horas_mes'], 1, ',', '.') }} horas / mes calculadas a ${{ number_format($metricas['costo_hora_usd'], 2) }} USD/h.
                </div>
            </td>
            <td class="card card-escape">
                <div class="card-label">3. Se te escapa (Ventas)</div>
                <div class="card-amount-usd">${{ number_format($metricas['escape_usd'], 2, '.', ',') }} USD</div>
                <div class="card-amount-bs">Bs. {{ number_format($metricas['escape_bs'], 2, ',', '.') }}</div>
                <div class="card-desc">
                    ~{{ number_format($metricas['ventas_perdidas'], 1, ',', '.') }} ventas perdidas/mes por {{ number_format($metricas['sin_resp'], 0, ',', '.') }} consultas sin contestar a tiempo.
                </div>
            </td>
        </tr>
    </table>

    <!-- Veredicto Financiero -->
    <div class="highlight-box">
        <strong style="color: #835425; font-size: 12px;">Conclusión Ejecutiva:</strong><br>
        Lo que te cobra Meta representa apenas el <strong>{{ $metricas['pct_vs_mano'] }}%</strong> de lo que actualmente gastas en contestar manualmente, y el <strong>{{ $metricas['pct_vs_escape'] }}%</strong> de lo que estás perdiendo por no atender a tiempo.
        <br><br>
        @if($metricas['ventas_para_pagarla'] > 0 && $metricas['ventas_para_pagarla'] <= 1)
            ⚡ <strong>Retorno Inmediato:</strong> Con recuperar <strong>una sola venta al mes</strong> (${{ number_format($metricas['ticket_usd'], 2) }} USD / Bs. {{ number_format($metricas['ticket_bs'], 2, ',', '.') }}) ya pagás la totalidad de la factura de Meta.
        @elseif($metricas['ventas_para_pagarla'] > 1)
            ⚡ <strong>Retorno Inmediato:</strong> Te alcanza con recuperar <strong>{{ ceil($metricas['ventas_para_pagarla']) }} ventas al mes</strong> (${{ number_format($metricas['ticket_usd'], 2) }} USD c/u) para cubrir el 100% de la factura de Meta.
        @endif
    </div>

    <!-- Parámetros utilizados -->
    <div class="section-title">Parámetros cargados en la simulación</div>
    <table class="data-table">
        <tr>
            <th width="35%">Métrica de Entrada</th>
            <th width="30%">Valor Ingresado</th>
            <th width="35%">Impacto en la cuenta</th>
        </tr>
        <tr>
            <td>Conversaciones por día</td>
            <td><strong>{{ number_format($simulacion->conv_dia) }}</strong> clientes/día</td>
            <td>{{ number_format($metricas['conv_mes']) }} conversaciones al mes</td>
        </tr>
        <tr>
            <td>Mensajes emitidos por conversación</td>
            <td><strong>{{ $simulacion->msj_por_conv }}</strong> msj</td>
            <td>{{ number_format($metricas['msj_servicio']) }} mensajes salientes de servicio</td>
        </tr>
        <tr>
            <td>Personas dedicadas a contestar</td>
            <td><strong>{{ $simulacion->personas }}</strong> {{ $simulacion->personas == 1 ? 'persona' : 'personas' }}</td>
            <td>Capacidad humana actual</td>
        </tr>
        <tr>
            <td>Horas por día dedicadas a WhatsApp</td>
            <td><strong>{{ $simulacion->horas_dia }}</strong> hs/día</td>
            <td>{{ number_format($metricas['horas_mes'], 1) }} horas mensuales de atención</td>
        </tr>
        <tr>
            <td>Ticket promedio por venta</td>
            <td><strong>${{ number_format($simulacion->ticket_usd, 2) }} USD</strong> (Bs. {{ number_format($metricas['ticket_bs'], 2, ',', '.') }})</td>
            <td>Base de facturación por conversión</td>
        </tr>
        <tr>
            <td>Costo hora humano</td>
            <td><strong>${{ number_format($simulacion->costo_hora_usd, 2) }} USD/h</strong></td>
            <td>Bs. {{ number_format($simulacion->costo_hora_usd * $metricas['tasa_bcv'], 2, ',', '.') }}/h</td>
        </tr>
        <tr>
            <td>Consultas sin responder a tiempo</td>
            <td><strong>{{ $simulacion->sin_responder }}%</strong></td>
            <td>{{ number_format($metricas['sin_resp']) }} clientes desatendidos al mes</td>
        </tr>
        <tr>
            <td>Tasa oficial del BCV aplicada</td>
            <td><strong>Bs. {{ number_format($metricas['tasa_bcv'], 4, ',', '.') }} / USD</strong></td>
            <td>Banco Central de Venezuela (automatizada)</td>
        </tr>
    </table>

    <div class="footer">
        Documento generado automáticamente por la Calculadora de Retorno de <strong>Capybaras Digitales</strong>.<br>
        ¿Querés automatizar tu WhatsApp? Contactanos haciendo clic en nuestro enlace oficial de <strong>WhatsApp de Capybaras Digitales</strong>.
    </div>

</body>
</html>
