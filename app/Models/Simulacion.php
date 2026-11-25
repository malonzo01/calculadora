<?php

namespace App\Models;

use App\Services\BcvExchangeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Simulacion extends Model
{
    use HasFactory;

    protected $table = 'simulaciones';

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Calcular métricas financieras con moneda base USD y conversión a Bolívares (BCV)
     */
    public static function calcularMetricas(array $datos): array
    {
        $convDia = (int) ($datos['conv_dia'] ?? 30);
        $msjPorConv = (int) ($datos['msj_por_conv'] ?? 3);
        $personas = (int) ($datos['personas'] ?? 1);
        $horasDia = (float) ($datos['horas_dia'] ?? 4.0);
        $ticketUSD = (float) ($datos['ticket_usd'] ?? $datos['ticket'] ?? 35.00);
        $costoHoraUSD = (float) ($datos['costo_hora_usd'] ?? 4.50);
        $sinResponder = (float) ($datos['sin_responder'] ?? 30);
        $msjMarketing = (int) ($datos['msj_marketing'] ?? 0);
        $conversion = (float) ($datos['conversion'] ?? 8.0);
        
        // Tasa BCV (si no se especifica, se toma la automatizada del servicio)
        $tasaBCV = (float) ($datos['tasa_bcv'] ?? 0);
        if ($tasaBCV <= 0) {
            $infoBCV = BcvExchangeService::obtenerTasa();
            $tasaBCV = (float) ($infoBCV['tasa'] ?? 920.00);
        }

        // Constantes oficiales de Meta y horas laborales
        $diasLaborablesMes = 236 / 12; // ~19.67 días hábiles
        $tarifaMetaServicioUSD = 0.026;
        $tarifaMetaMarketingUSD = 0.0618;
        $mensajesServicioGratis = 1000;

        // 1. Cuentas de Meta WhatsApp Cloud API
        $convMes = 30 * $convDia;
        $msjServicio = $convMes * $msjPorConv;
        $gratis = min($msjServicio, $mensajesServicioGratis);
        $pagos = max(0, $msjServicio - $mensajesServicioGratis);

        $usdServicio = $pagos * $tarifaMetaServicioUSD;
        $usdMarketing = $msjMarketing * $tarifaMetaMarketingUSD;
        $metaUSD = $usdServicio + $usdMarketing;

        // 2. Costo de atención humana
        $horasMes = $horasDia * $diasLaborablesMes * $personas;
        $aManoUSD = $costoHoraUSD * $horasMes;

        // 3. Fuga de ventas por no contestar a tiempo
        $sinResp = $convMes * ($sinResponder / 100);
        $ventasPerdidas = $sinResp * ($conversion / 100);
        $escapeUSD = $ventasPerdidas * $ticketUSD;

        // 4. Índices comparativos y retorno (ROI)
        $pctVsMano = $aManoUSD > 0 ? ($metaUSD / $aManoUSD) * 100 : 0;
        $pctVsEscape = $escapeUSD > 0 ? ($metaUSD / $escapeUSD) * 100 : 0;
        $ventasParaPagarla = $ticketUSD > 0 ? $metaUSD / $ticketUSD : 0;

        // 5. Conversión a Bolívares según tasa oficial BCV
        $metaBs = $metaUSD * $tasaBCV;
        $aManoBs = $aManoUSD * $tasaBCV;
        $escapeBs = $escapeUSD * $tasaBCV;
        $ticketBs = $ticketUSD * $tasaBCV;

        return [
            'conv_mes' => $convMes,
            'msj_servicio' => $msjServicio,
            'gratis' => $gratis,
            'pagos' => $pagos,
            'usd_servicio' => round($usdServicio, 2),
            'usd_marketing' => round($usdMarketing, 2),
            'meta_usd' => round($metaUSD, 2),
            'meta_bs' => round($metaBs, 2),
            'horas_mes' => round($horasMes, 1),
            'a_mano_usd' => round($aManoUSD, 2),
            'a_mano_bs' => round($aManoBs, 2),
            'sin_resp' => (int) round($sinResp),
            'ventas_perdidas' => round($ventasPerdidas, 2),
            'escape_usd' => round($escapeUSD, 2),
            'escape_bs' => round($escapeBs, 2),
            'ticket_usd' => round($ticketUSD, 2),
            'ticket_bs' => round($ticketBs, 2),
            'tasa_bcv' => round($tasaBCV, 4),
            'costo_hora_usd' => round($costoHoraUSD, 2),
            'pct_vs_mano' => round($pctVsMano, 1),
            'pct_vs_escape' => round($pctVsEscape, 1),
            'ventas_para_pagarla' => round($ventasParaPagarla, 2),
        ];
    }
}
