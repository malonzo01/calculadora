<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BcvExchangeService
{
    protected const CACHE_KEY = 'bcv_exchange_rate_data';
    protected const CACHE_TTL_MINUTES = 60; // 1 hora
    protected const DEFAULT_FALLBACK_RATE = 920.00;

    /**
     * Obtener la tasa oficial del BCV (automatizada y con caché)
     */
    public static function obtenerTasa(bool $forzarActualizacion = false): array
    {
        if ($forzarActualizacion) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_TTL_MINUTES), function () {
            return self::consultarTasa();
        });
    }

    /**
     * Consulta con redundancia: DolarApi -> Scraping directo BCV -> Fallback
     */
    protected static function consultarTasa(): array
    {
        // 1. Intentar con DolarApi Oficial
        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->get('https://ve.dolarapi.com/v1/dolares/oficial');

            if ($response->successful()) {
                $data = $response->json();
                $promedio = (float) ($data['promedio'] ?? 0);

                if ($promedio > 0) {
                    return [
                        'tasa' => round($promedio, 4),
                        'fuente' => 'BCV (vía DolarApi Oficial)',
                        'fecha' => $data['fechaActualizacion'] ?? now()->toIso8601String(),
                        'actualizado_at' => now()->format('d/m/Y H:i'),
                        'error' => null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Fallo consulta DolarApi BCV: ' . $e->getMessage());
        }

        // 2. Fallback: Scraping directo a la web del Banco Central de Venezuela
        try {
            $response = Http::withoutVerifying()
                ->timeout(6)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get('https://www.bcv.org.ve');

            if ($response->successful()) {
                $html = $response->body();
                // Buscar el contenedor USD y el número strong en el HTML del BCV
                if (preg_match('/USD\s*<\/span>.*?<strong class="strong-tb">\s*([\d\.,]+)\s*<\/strong>/s', $html, $matches)) {
                    $tasaStr = str_replace('.', '', $matches[1]);
                    $tasaStr = str_replace(',', '.', $tasaStr);
                    $tasaFloat = (float) $tasaStr;

                    if ($tasaFloat > 0) {
                        return [
                            'tasa' => round($tasaFloat, 4),
                            'fuente' => 'Banco Central de Venezuela (bcv.org.ve directo)',
                            'fecha' => now()->toIso8601String(),
                            'actualizado_at' => now()->format('d/m/Y H:i'),
                            'error' => null,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Fallo scraping directo BCV: ' . $e->getMessage());
        }

        // 3. Fallback de seguridad si no hay conexión a internet
        return [
            'tasa' => self::DEFAULT_FALLBACK_RATE,
            'fuente' => 'Tasa referencial de contingencia',
            'fecha' => now()->toIso8601String(),
            'actualizado_at' => now()->format('d/m/Y H:i'),
            'error' => 'No se pudo conectar a los servidores del BCV en este momento. Usando tasa referencial.',
        ];
    }
}
