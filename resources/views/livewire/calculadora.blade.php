<?php

use Livewire\Volt\Component;
use App\Models\Simulacion;
use App\Services\BcvExchangeService;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    // Parámetros principales (Moneda base USD)
    public int $convDia = 30;
    public int $msjPorConv = 3;
    public int $personas = 1;
    public float $horasDia = 4.0;
    public float $ticketUSD = 35.0;
    public int $sinResponder = 30;

    // Supuestos avanzados y Tasa BCV
    public float $costoHoraUSD = 4.5; // Costo por hora en USD
    public int $msjMarketing = 0;
    public float $conversion = 8.0;
    public float $tasaBCV = 920.0;
    public string $fuenteBCV = '';
    public string $actualizadoBCV = '';

    // Modales y estados
    public ?string $s = null;
    public bool $showLeadModal = false;
    public bool $isDownloading = false;
    public bool $guardadoExitoso = false;
    public bool $tasaActualizadaFeedback = false;
    public ?string $savedUuid = null;
    public ?string $activePreset = null;

    // Datos del Lead
    public string $nombre = '';
    public string $email = '';
    public string $whatsapp = '';
    public string $empresa = '';

    public function mount(?string $s = null): void
    {
        // Obtener tasa automática del BCV
        $this->cargarTasaBCV();

        // Cargar simulación existente si viene en URL (?s=uuid) o prop
        $uuid = $s ?: $this->s ?: request()->query('s');
        if ($uuid) {
            $sim = Simulacion::where('uuid', $uuid)->first();
            if ($sim) {
                $this->convDia = $sim->conv_dia;
                $this->msjPorConv = $sim->msj_por_conv;
                $this->personas = $sim->personas;
                $this->horasDia = (float) $sim->horas_dia;
                $this->ticketUSD = (float) $sim->ticket_usd;
                $this->costoHoraUSD = (float) $sim->costo_hora_usd;
                $this->sinResponder = $sim->sin_responder;
                $this->msjMarketing = $sim->msj_marketing;
                $this->conversion = (float) $sim->conversion;
                $this->tasaBCV = (float) $sim->tasa_bcv;
                $this->savedUuid = $sim->uuid;
            }
        } else {
            // Permitir precargar preset desde URL (ej: ?preset=dental o ?sector=dental)
            $presetParam = request()->query('preset') ?: request()->query('sector');
            if ($presetParam) {
                $this->aplicarPreset($presetParam);
            }
        }
    }

    public function cargarTasaBCV(bool $forzar = false): void
    {
        $info = BcvExchangeService::obtenerTasa($forzar);
        $this->tasaBCV = (float) ($info['tasa'] ?? 920.0);
        $this->fuenteBCV = (string) ($info['fuente'] ?? 'BCV Oficial');
        $this->actualizadoBCV = (string) ($info['actualizado_at'] ?? now()->format('d/m/Y H:i'));

        if ($forzar) {
            $this->tasaActualizadaFeedback = true;
        }
    }

    public function refrescarTasaBCV(): void
    {
        $this->cargarTasaBCV(true);
    }

    public function aplicarPreset(string $tipo): void
    {
        $this->activePreset = $tipo;

        match ($tipo) {
            'comercio' => [($this->convDia = 25), ($this->msjPorConv = 3), ($this->personas = 1), ($this->horasDia = 3.5), ($this->ticketUSD = 25.0), ($this->costoHoraUSD = 3.5), ($this->sinResponder = 30)],
            'dental' => [($this->convDia = 35), ($this->msjPorConv = 3), ($this->personas = 1), ($this->horasDia = 4.0), ($this->ticketUSD = 45.0), ($this->costoHoraUSD = 4.0), ($this->sinResponder = 30)],
            'servicios' => [($this->convDia = 15), ($this->msjPorConv = 4), ($this->personas = 1), ($this->horasDia = 4.0), ($this->ticketUSD = 150.0), ($this->costoHoraUSD = 6.0), ($this->sinResponder = 35)],
            'ecommerce' => [($this->convDia = 120), ($this->msjPorConv = 3), ($this->personas = 2), ($this->horasDia = 6.0), ($this->ticketUSD = 45.0), ($this->costoHoraUSD = 4.0), ($this->sinResponder = 40)],
            'salud' => [($this->convDia = 60), ($this->msjPorConv = 2), ($this->personas = 2), ($this->horasDia = 5.0), ($this->ticketUSD = 20.0), ($this->costoHoraUSD = 4.5), ($this->sinResponder = 25)],
            default => null,
        };
    }

    public function calcular(): array
    {
        return Simulacion::calcularMetricas([
            'conv_dia' => $this->convDia,
            'msj_por_conv' => $this->msjPorConv,
            'personas' => $this->personas,
            'horas_dia' => $this->horasDia,
            'ticket_usd' => $this->ticketUSD,
            'costo_hora_usd' => $this->costoHoraUSD,
            'sin_responder' => $this->sinResponder,
            'msj_marketing' => $this->msjMarketing,
            'conversion' => $this->conversion,
            'tasa_bcv' => $this->tasaBCV,
        ]);
    }

    public function guardarSimulacion(): void
    {
        $metricas = $this->calcular();

        $sim = Simulacion::create([
            'uuid' => (string) Str::uuid(),
            'conv_dia' => $this->convDia,
            'msj_por_conv' => $this->msjPorConv,
            'personas' => $this->personas,
            'horas_dia' => $this->horasDia,
            'ticket_usd' => $this->ticketUSD,
            'costo_hora_usd' => $this->costoHoraUSD,
            'sin_responder' => $this->sinResponder,
            'msj_marketing' => $this->msjMarketing,
            'conversion' => $this->conversion,
            'tasa_bcv' => $this->tasaBCV,
            'nombre' => $this->nombre ?: null,
            'email' => $this->email ?: null,
            'whatsapp' => $this->whatsapp ?: null,
            'empresa' => $this->empresa ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            ...$metricas,
        ]);

        $this->savedUuid = $sim->uuid;
        $this->guardadoExitoso = true;
    }

    public function abrirModalDescarga(): void
    {
        $this->showLeadModal = true;
    }

    public function cerrarModal(): void
    {
        $this->showLeadModal = false;
    }

    public function descargarPdf()
    {
        $ip = request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('pdf-dl:' . $ip, 8)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('pdf-dl:' . $ip);
            session()->flash('error', "Demasiadas descargas solicitadas. Por favor espera {$seconds} segundos.");
            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit('pdf-dl:' . $ip, 60);

        $this->validate([
            'email' => 'nullable|email',
        ]);

        $metricas = $this->calcular();

        $sim = Simulacion::create([
            'uuid' => (string) Str::uuid(),
            'conv_dia' => $this->convDia,
            'msj_por_conv' => $this->msjPorConv,
            'personas' => $this->personas,
            'horas_dia' => $this->horasDia,
            'ticket_usd' => $this->ticketUSD,
            'costo_hora_usd' => $this->costoHoraUSD,
            'sin_responder' => $this->sinResponder,
            'msj_marketing' => $this->msjMarketing,
            'conversion' => $this->conversion,
            'tasa_bcv' => $this->tasaBCV,
            'nombre' => $this->nombre ?: null,
            'email' => $this->email ?: null,
            'whatsapp' => $this->whatsapp ?: null,
            'empresa' => $this->empresa ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'informe_descargado_at' => now(),
            ...$metricas,
        ]);

        $this->savedUuid = $sim->uuid;
        $this->showLeadModal = false;

        $pdf = Pdf::loadView('pdf.informe', [
            'simulacion' => $sim,
            'metricas' => $metricas,
        ]);

        return response()->streamDownload(function () use ($pdf, $sim) {
            echo $pdf->output();
        }, "Informe-WhatsApp-Capybaras-Digitales-{$sim->uuid}.pdf");
    }

    public function with(): array
    {
        return [
            'metricas' => $this->calcular(),
        ];
    }
};
?>

<div class="space-y-10">

    <!-- Barra de Tasa Oficial BCV Automatizada -->
    <section class="max-w-4xl mx-auto">
        <div
            class="bg-white rounded-2xl p-4 sm:p-5 border border-[#d8ddd3] shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-[#e8f1dc] text-[#33521c] flex items-center justify-center font-bold text-lg shadow-xs">
                    🇻🇪
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Tasa Oficial BCV
                            Automatizada:</span>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#e8f1dc] text-[#33521c]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#33521c] animate-pulse"></span>
                            En vivo
                        </span>
                    </div>
                    <div class="text-lg sm:text-xl font-extrabold text-[#33521c] font-mono">
                        1 USD = Bs. {{ number_format($tasaBCV, 4, ',', '.') }}
                        <span class="text-xs font-normal text-[#525b4d] ml-1">({{ $fuenteBCV }})</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button type="button" wire:click="refrescarTasaBCV"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-[#F4EFE6] hover:bg-[#dce6d1] text-[#33521c] border border-[#d8ddd3] transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <svg wire:loading.class="animate-spin" wire:target="refrescarTasaBCV"
                        class="w-3.5 h-3.5 text-[#33521c]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span wire:loading.remove wire:target="refrescarTasaBCV">Actualizar Tasa BCV</span>
                    <span wire:loading wire:target="refrescarTasaBCV">Consultando BCV...</span>
                </button>
            </div>
        </div>

        @if ($tasaActualizadaFeedback)
            <div
                class="mt-2 text-center text-xs font-bold text-[#33521c] bg-[#e8f1dc] py-1.5 px-3 rounded-lg animate-in fade-in">
                ✓ Tasa oficial del BCV sincronizada correctamente (Bs. {{ number_format($tasaBCV, 4, ',', '.') }} / USD)
            </div>
        @endif
    </section>

    <!-- Encabezado Principal con Estilo Capybaras Digitales -->
    <section class="text-center max-w-3xl mx-auto space-y-4">
        <div
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#e8f1dc] text-[#33521c] border border-[#dce6d1] shadow-xs">
            <span class="text-sm">🦫</span>
            <span>Precios de WhatsApp · Moneda Base USD ($) con Conversión Oficial BCV</span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-[#161E11] leading-tight">
            ¿Cuánto te sale automatizar tu WhatsApp?
            <span class="block text-[#835425] text-2xl sm:text-4xl mt-1 font-bold">Y cuánto te deja en $ USD y
                Bs.</span>
        </h1>

        <p class="text-base sm:text-lg text-[#525b4d] leading-relaxed">
            Calculá el costo en dólares y su equivalente en bolívares a la tasa oficial del BCV. Ponle tus números y
            mira las tres cuentas clave juntas.
        </p>

        <!-- Presets Rápidos por Rubro -->
        <div class="pt-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#835425] mb-2.5">O elige un rubro para
                empezar con números típicos en USD:</p>
            <div class="flex flex-wrap items-center justify-center gap-2">
                <button type="button" wire:click="aplicarPreset('dental')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border {{ $activePreset === 'dental' ? 'bg-[#33521c] text-white border-[#33521c] shadow-xs' : 'bg-white hover:bg-[#e8f1dc] text-[#33521c] border-[#d8ddd3]' }}">
                    🦷 Odontología / Clínica Dental (Ticket $45 USD)
                </button>
                <button type="button" wire:click="aplicarPreset('comercio')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border {{ $activePreset === 'comercio' ? 'bg-[#33521c] text-white border-[#33521c] shadow-xs' : 'bg-white hover:bg-[#e8f1dc] text-[#33521c] border-[#d8ddd3]' }}">
                    🏪 Comercio Local (Ticket $25 USD)
                </button>
                <button type="button" wire:click="aplicarPreset('servicios')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border {{ $activePreset === 'servicios' ? 'bg-[#33521c] text-white border-[#33521c] shadow-xs' : 'bg-white hover:bg-[#e8f1dc] text-[#33521c] border-[#d8ddd3]' }}">
                    🏢 Servicios / B2B (Ticket $150 USD)
                </button>
                <button type="button" wire:click="aplicarPreset('ecommerce')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border {{ $activePreset === 'ecommerce' ? 'bg-[#33521c] text-white border-[#33521c] shadow-xs' : 'bg-white hover:bg-[#e8f1dc] text-[#33521c] border-[#d8ddd3]' }}">
                    🚀 E-Commerce (Ticket $45 USD)
                </button>
                <button type="button" wire:click="aplicarPreset('salud')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all border {{ $activePreset === 'salud' ? 'bg-[#33521c] text-white border-[#33521c] shadow-xs' : 'bg-white hover:bg-[#e8f1dc] text-[#33521c] border-[#d8ddd3]' }}">
                    🏥 Salud / Consultas (Ticket $20 USD)
                </button>
            </div>
        </div>
    </section>

    <!-- Controles Principales (Inputs & Sliders en USD) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#d8ddd3] shadow-sm shadow-[#33521c]/5 space-y-8">
        <div class="border-b border-[#d8ddd3]/80 pb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-[#DAA520]"></span>
                Tus números de operación
            </h2>
            <span class="text-xs text-[#525b4d] font-medium hidden sm:inline">Moneda base: <strong>$ USD</strong>
                (calculado automáticamente en Bolívares)</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Conversaciones por día -->
            <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-[#d8ddd3]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Conversaciones por
                        día</label>
                    <span class="text-xl font-extrabold text-[#33521c] font-mono">{{ number_format($convDia) }}</span>
                </div>
                <input type="range" min="5" max="500" step="5" wire:model.live="convDia"
                    class="w-full">
                <p class="text-xs text-[#525b4d] leading-snug">Clientes distintos que te escriben en un día promedio.
                </p>
            </div>

            <!-- Mensajes que mandás vos por conversación -->
            <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-[#d8ddd3]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Mensajes por
                        conversación</label>
                    <span class="text-xl font-extrabold text-[#33521c] font-mono">{{ $msjPorConv }}</span>
                </div>
                <input type="range" min="1" max="15" step="1" wire:model.live="msjPorConv"
                    class="w-full">
                <p class="text-xs text-[#525b4d] leading-snug">Meta cobra los que <strong>salen</strong> de tu negocio,
                    no los que recibís.</p>
            </div>

            <!-- Personas atendiendo -->
            <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-[#d8ddd3]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Personas atendiendo</label>
                    <span class="text-xl font-extrabold text-[#33521c] font-mono">
                        {{ $personas == 1 ? '1 persona' : $personas . ' personas' }}
                    </span>
                </div>
                <input type="range" min="0" max="10" step="1" wire:model.live="personas"
                    class="w-full">
                <p class="text-xs text-[#525b4d] leading-snug">Cuántas personas contestan WhatsApp hoy (0 si lo hacés
                    vos mismo).</p>
            </div>

            <!-- Horas por día que le dedican -->
            <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-[#d8ddd3]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Horas diarias por
                        persona</label>
                    <span class="text-xl font-extrabold text-[#33521c] font-mono">
                        {{ $horasDia == 1 ? '1 hora' : $horasDia . ' horas' }}
                    </span>
                </div>
                <input type="range" min="0" max="12" step="0.5" wire:model.live="horasDia"
                    class="w-full">
                <p class="text-xs text-[#525b4d] leading-snug">Solo el tiempo dedicado a responder mensajes, no toda la
                    jornada.</p>
            </div>

            <!-- Ticket promedio de venta (en $ USD) -->
            <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-[#d8ddd3]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Ticket promedio ($
                        USD)</label>
                    <span
                        class="text-xl font-extrabold text-[#835425] font-mono">${{ number_format($ticketUSD, 2, '.', ',') }}</span>
                </div>
                <div class="relative">
                    <span
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#525b4d] font-bold text-xs">$
                        USD</span>
                    <input type="number" min="0" step="1" wire:model.live.debounce.300ms="ticketUSD"
                        class="w-full pl-16 pr-4 py-2 bg-white border border-[#d8ddd3] rounded-xl text-sm font-semibold text-[#161E11] focus:ring-2 focus:ring-[#33521c] focus:outline-hidden">
                </div>
                <p class="text-xs text-[#525b4d] leading-snug">
                    Equivalente: <strong class="text-[#33521c]">Bs.
                        {{ number_format($ticketUSD * $tasaBCV, 2, ',', '.') }}</strong> al cambio BCV.
                </p>
            </div>

            <!-- Consultas que no contestás a tiempo -->
            <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-[#d8ddd3]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#525b4d]">Sin contestar a
                        tiempo</label>
                    <span class="text-xl font-extrabold text-[#DAA520] font-mono">{{ $sinResponder }}%</span>
                </div>
                <input type="range" min="0" max="80" step="5" wire:model.live="sinResponder"
                    class="w-full">
                <p class="text-xs text-[#525b4d] leading-snug">Consultas de noche, fines de semana o cuando el equipo
                    está saturado.</p>
            </div>
        </div>

        <!-- Supuestos Avanzados Desplegables -->
        <details class="group border border-[#d8ddd3] rounded-2xl bg-[#F4EFE6]/50 transition-all overflow-hidden">
            <summary
                class="cursor-pointer px-6 py-4 flex items-center justify-between text-sm font-bold text-[#33521c] hover:bg-[#F4EFE6] select-none">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#835425] transition-transform group-open:rotate-90" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Los supuestos avanzados, para que los discutas
                </span>
                <span class="text-xs font-semibold text-[#835425] group-open:hidden">Ver costo hora, campañas y tasa
                    BCV</span>
            </summary>

            <div class="p-6 border-t border-[#d8ddd3] bg-white space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                    <!-- Costo por hora humana en USD -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#525b4d]">Costo hora humano ($ USD/h)</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#525b4d] font-bold text-xs">$</span>
                            <input type="number" min="0.5" step="0.5"
                                wire:model.live.debounce.300ms="costoHoraUSD"
                                class="w-full pl-7 pr-4 py-2 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm font-semibold text-[#161E11]">
                        </div>
                        <p class="text-[11px] text-[#525b4d]">Equivale a Bs.
                            {{ number_format($costoHoraUSD * $tasaBCV, 2, ',', '.') }}/h según BCV.</p>
                    </div>

                    <!-- Mensajes de promoción por mes -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#525b4d]">Mensajes de promoción / mes</label>
                        <input type="number" min="0" step="100"
                            wire:model.live.debounce.300ms="msjMarketing"
                            class="w-full px-4 py-2 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm font-semibold text-[#161E11]">
                        <p class="text-[11px] text-[#525b4d]">Campañas salientes. Tarifa Meta: USD $0,0618 c/u.</p>
                    </div>

                    <!-- Tasa de conversión de lo no contestado -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#525b4d]">Conversión estimada (%)</label>
                        <div class="relative">
                            <input type="number" min="1" max="100" step="1"
                                wire:model.live.debounce.300ms="conversion"
                                class="w-full px-4 pr-8 py-2 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm font-semibold text-[#161E11]">
                            <span
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#525b4d] font-bold text-xs">%</span>
                        </div>
                        <p class="text-[11px] text-[#525b4d]">De las consultas que se pierden, cuántas hubiesen
                            comprado.</p>
                    </div>

                    <!-- Tasa BCV personalizada si desea simular -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#525b4d]">Tasa BCV (Bs. / USD)</label>
                            <button type="button" wire:click="refrescarTasaBCV"
                                class="text-[10px] text-[#33521c] font-bold underline cursor-pointer">
                                Sincronizar
                            </button>
                        </div>
                        <input type="number" step="0.01" wire:model.live.debounce.300ms="tasaBCV"
                            class="w-full px-4 py-2 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm font-semibold text-[#161E11]">
                        <p class="text-[11px] text-[#525b4d]">Actualizada automáticamente desde el Banco Central de
                            Venezuela.</p>
                    </div>
                </div>

                <div
                    class="p-4 rounded-xl bg-[#e8f1dc]/60 border border-[#dce6d1] text-xs text-[#33521c] leading-relaxed">
                    <strong>Bases de cálculo oficiales:</strong> Las tarifas de Meta WhatsApp Cloud API son oficiales a
                    nivel global: <strong>USD $0,026</strong> por mensaje de servicio y <strong>USD $0,0618</strong> por
                    mensaje de marketing, con los primeros <strong>1.000 mensajes de servicio gratis todos los
                        meses</strong>. El valor en Bolívares se calcula con la tasa oficial del BCV vigente al momento
                    de la simulación.
                </div>
            </div>
        </details>
    </div>

    <!-- Sección de Resultados: Las 3 Cuentas Clave en USD y Bolívares (BCV) -->
    <section class="space-y-4">
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#161E11]">Tus tres números mensuales</h2>
            <p class="text-sm text-[#525b4d] mt-1">Expresados en <strong>$ USD</strong> y en <strong>Bolívares
                    (Bs.)</strong> a la tasa oficial del BCV.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Tarjeta 1: Te cobra Meta -->
            <div
                class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#33521c] shadow-lg shadow-[#33521c]/10 flex flex-col justify-between relative overflow-hidden group">
                <div
                    class="absolute -right-12 -top-12 w-28 h-28 bg-[#e8f1dc] rounded-full blur-xl group-hover:scale-150 transition-transform">
                </div>
                <div class="relative space-y-3">
                    <span
                        class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#e8f1dc] text-[#33521c]">
                        1. Te cobra Meta (API)
                    </span>
                    <div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-[#33521c] font-mono tracking-tight">
                            ${{ number_format($metricas['meta_usd'], 2, '.', ',') }} <span
                                class="text-lg font-bold">USD</span>
                        </div>
                        <div class="text-sm font-bold text-[#835425] mt-1.5 flex items-center gap-1.5">
                            <span>🇻🇪</span>
                            <span>Bs. {{ number_format($metricas['meta_bs'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="relative pt-6 border-t border-[#d8ddd3] mt-6 text-xs text-[#525b4d] leading-relaxed">
                    @if ($metricas['pagos'] == 0)
                        <span class="text-[#33521c] font-bold">¡Costo $0!</span> Tus
                        {{ number_format($metricas['msj_servicio'], 0, ',', '.') }} mensajes entran completos en los
                        1.000 gratis mensuales de Meta.
                    @else
                        {{ number_format($metricas['gratis'], 0, ',', '.') }} gratis +
                        {{ number_format($metricas['pagos'], 0, ',', '.') }} pagos a USD $0,026 cada uno.
                    @endif
                </div>
            </div>

            <!-- Tarjeta 2: Te cuesta atenderlo a mano -->
            <div
                class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#835425] shadow-lg shadow-[#835425]/10 flex flex-col justify-between relative overflow-hidden group">
                <div
                    class="absolute -right-12 -top-12 w-28 h-28 bg-[#f5ede4] rounded-full blur-xl group-hover:scale-150 transition-transform">
                </div>
                <div class="relative space-y-3">
                    <span
                        class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#f5ede4] text-[#835425]">
                        2. Te cuesta atender a mano
                    </span>
                    <div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-[#835425] font-mono tracking-tight">
                            ${{ number_format($metricas['a_mano_usd'], 2, '.', ',') }} <span
                                class="text-lg font-bold">USD</span>
                        </div>
                        <div class="text-sm font-bold text-[#525b4d] mt-1.5 flex items-center gap-1.5">
                            <span>🇻🇪</span>
                            <span>Bs. {{ number_format($metricas['a_mano_bs'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="relative pt-6 border-t border-[#d8ddd3] mt-6 text-xs text-[#525b4d] leading-relaxed">
                    {{ number_format($metricas['horas_mes'], 1, ',', '.') }} horas de equipo al mes calculadas a
                    ${{ number_format($costoHoraUSD, 2) }} USD/hora.
                </div>
            </div>

            <!-- Tarjeta 3: Se te escapa (Fuga de clientes) -->
            <div
                class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#DAA520] shadow-lg shadow-[#DAA520]/10 flex flex-col justify-between relative overflow-hidden group">
                <div
                    class="absolute -right-12 -top-12 w-28 h-28 bg-[#fdf7e7] rounded-full blur-xl group-hover:scale-150 transition-transform">
                </div>
                <div class="relative space-y-3">
                    <span
                        class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#fdf7e7] text-[#835425]">
                        3. Se te escapa en ventas
                    </span>
                    <div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-[#835425] font-mono tracking-tight">
                            ${{ number_format($metricas['escape_usd'], 2, '.', ',') }} <span
                                class="text-lg font-bold">USD</span>
                        </div>
                        <div class="text-sm font-bold text-[#DAA520] mt-1.5 flex items-center gap-1.5">
                            <span>🇻🇪</span>
                            <span>Bs. {{ number_format($metricas['escape_bs'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="relative pt-6 border-t border-[#d8ddd3] mt-6 text-xs text-[#525b4d] leading-relaxed">
                    ~{{ number_format($metricas['ventas_perdidas'], 1, ',', '.') }} ventas perdidas/mes por
                    {{ number_format($metricas['sin_resp'], 0, ',', '.') }} consultas sin responder a tiempo.
                </div>
            </div>
        </div>
    </section>

    <!-- El Veredicto / Conclusión -->
    <section
        class="rounded-3xl p-8 sm:p-10 bg-gradient-to-br from-[#33521c] to-[#1e3410] text-white shadow-xl space-y-6">
        <div class="flex items-center gap-2 text-[#f6be3b] text-xs font-bold uppercase tracking-widest">
            <span>✨</span>
            <span>El Veredicto Financiero</span>
        </div>

        @if ($metricas['a_mano_usd'] == 0 && $metricas['escape_usd'] == 0)
            <p class="text-lg text-white/90">
                Cargá cuántas horas le dedicás y tu ticket promedio para ver la comparación completa.
            </p>
        @else
            <div class="space-y-4">
                <p class="text-xl sm:text-2xl font-bold leading-snug">
                    Lo que te cobra Meta es apenas el <span
                        class="text-[#f6be3b] underline decoration-2">{{ $metricas['pct_vs_mano'] }}%</span> de lo que
                    ya estás gastando en atender a mano
                    @if ($metricas['escape_usd'] > 0)
                        , y el <span
                            class="text-[#f6be3b] underline decoration-2">{{ $metricas['pct_vs_escape'] }}%</span> de
                        lo que se te está escapando por no contestar al instante.
                    @else
                        .
                    @endif
                </p>

                @if ($metricas['ventas_para_pagarla'] > 0 && $metricas['ventas_para_pagarla'] <= 1)
                    <div
                        class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/20 text-sm sm:text-base text-white/95 flex items-center gap-3">
                        <span class="text-2xl">🎯</span>
                        <span>
                            Con recuperar <strong>una sola venta al mes</strong> de las que hoy perdés
                            (${{ number_format($ticketUSD, 2) }} USD / Bs.
                            {{ number_format($ticketUSD * $tasaBCV, 2, ',', '.') }}), ya pagaste la factura entera de
                            WhatsApp y el resto es ganancia neta.
                        </span>
                    </div>
                @elseif($metricas['ventas_para_pagarla'] > 1)
                    <div
                        class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/20 text-sm sm:text-base text-white/95 flex items-center gap-3">
                        <span class="text-2xl">🎯</span>
                        <span>
                            Te alcanza con recuperar <strong>{{ ceil($metricas['ventas_para_pagarla']) }} ventas al
                                mes</strong> (${{ number_format($ticketUSD, 2) }} USD c/u) para cubrir completamente lo
                            que te cobra Meta.
                        </span>
                    </div>
                @endif
            </div>
        @endif
    </section>

    <!-- Banner Recomendado CapyDental para Clínicas Odontológicas -->
    @if($activePreset === 'dental' || request()->query('preset') === 'dental' || request()->query('sector') === 'dental')
        <div class="rounded-3xl p-6 sm:p-7 bg-gradient-to-r from-teal-900 to-[#1b4332] text-white border border-teal-400/30 shadow-lg flex flex-col md:flex-row items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-teal-400/20 text-teal-300 border border-teal-300/30 flex items-center justify-center text-3xl font-black shrink-0">
                    🦷
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-teal-300 bg-teal-950/60 px-2.5 py-1 rounded-full border border-teal-500/30">Solución Especializada</span>
                    <h3 class="text-lg font-black text-white mt-1">¿Tienes una Clínica Dental? Conoce CapyDental</h3>
                    <p class="text-xs sm:text-sm text-teal-100/90 max-w-xl mt-0.5">
                        CRM Odontológico con Inteligencia Artificial que atiende pacientes en WhatsApp 24/7, responde dudas sobre tratamientos y agenda citas directamente en tu calendario.
                    </p>
                </div>
            </div>
            <a href="https://crm.capybarasdigitales.com/#demo" target="_blank"
                class="w-full md:w-auto px-6 py-3.5 rounded-xl bg-teal-400 hover:bg-teal-300 text-teal-950 font-black text-xs sm:text-sm text-center transition-all shadow-md shrink-0 flex items-center justify-center gap-2">
                <span>Ver Demo de CapyDental</span>
                <span>→</span>
            </a>
        </div>
    @endif

    <!-- Aviso Importante de Meta -->
    <div class="rounded-2xl p-5 bg-[#fdf7e7] border-l-4 border-[#DAA520] flex items-start gap-4">
        <span class="text-2xl">⚠️</span>
        <div class="text-xs sm:text-sm text-[#525b4d] leading-relaxed">
            <strong class="text-[#835425]">Aviso importante sobre las cuentas de WhatsApp:</strong> Asegurate de
            registrar un método de pago internacional o tarjeta habilitada en tu cuenta comercial de Meta Business
            Manager. A las cuentas sin método configurado, Meta les deja de <em>entregar</em> los mensajes salientes de
            servicio. No te los cobra: simplemente no los envía.
        </div>
    </div>

    <!-- Barra de Acciones Principales -->
    <section class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
        <button type="button" wire:click="abrirModalDescarga"
            class="w-full sm:w-auto px-8 py-4 rounded-2xl font-extrabold text-sm sm:text-base bg-[#33521c] hover:bg-[#4a6b32] text-white shadow-lg shadow-[#33521c]/25 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-3 cursor-pointer">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Descargar informe en PDF (USD & Bs.)</span>
        </button>

        <button type="button" wire:click="guardarSimulacion"
            class="w-full sm:w-auto px-6 py-4 rounded-2xl font-bold text-sm bg-white hover:bg-[#F4EFE6] text-[#835425] border-2 border-[#835425]/40 transition-all flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-5 h-5 text-[#835425]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            <span>{{ $guardadoExitoso ? '✓ Simulación guardada en MySQL' : 'Guardar simulación' }}</span>
        </button>

        <a href="https://wa.me/584244582245?text=Hola%20Capybaras%20Digitales!%20Hice%20mi%20cálculo%20en%20USD:%20Atiendo%20{{ $convDia }}%20conversaciones/día%20y%20se%20me%20escapan%20${{ number_format($metricas['escape_usd'], 2) }}%20USD%20(Bs.%20{{ number_format($metricas['escape_bs'], 2, ',', '.') }}).%20Quiero%20automatizar%20mi%20negocio."
            target="_blank"
            class="w-full sm:w-auto px-6 py-4 rounded-2xl font-bold text-sm bg-[#835425] hover:bg-[#b07a45] text-white shadow-md shadow-[#835425]/20 transition-all flex items-center justify-center gap-2">
            <span>🦫 Que lo miremos juntos</span>
        </a>
    </section>

    <!-- Aviso de Enlace Compartible si está guardado -->
    @if ($savedUuid)
        <div
            class="max-w-xl mx-auto p-4 rounded-2xl bg-[#e8f1dc] border border-[#dce6d1] text-xs text-[#33521c] flex items-center justify-between gap-3">
            <div>
                <strong>Simulación guardada:</strong> Podés compartir este cálculo con tu equipo mediante este link:
                <span class="font-mono block text-[11px] text-[#4a6b32] mt-0.5">{{ url('/?s=' . $savedUuid) }}</span>
            </div>
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ url('/?s=' . $savedUuid) }}'); alert('Enlace copiado al portapapeles');"
                class="px-3 py-1.5 rounded-lg bg-[#33521c] text-white font-bold text-xs hover:bg-[#4a6b32] transition-colors whitespace-nowrap cursor-pointer">
                Copiar Link
            </button>
        </div>
    @endif

    <p class="text-center text-xs text-[#525b4d]/75 max-w-xl mx-auto">
        Tarifas oficiales Meta WhatsApp Cloud API en USD con conversión automática a Bolívares según la tasa del Banco
        Central de Venezuela (BCV).
    </p>

    <!-- Sección de Preguntas Frecuentes (SEO & GEO Optimizado para Venezuela y LatAm) -->
    <section class="max-w-4xl mx-auto pt-6 space-y-6">
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-extrabold text-[#161E11]">Preguntas Frecuentes sobre WhatsApp API en Venezuela
            </h2>
            <p class="text-xs sm:text-sm text-[#525b4d]">Todo lo que necesitas saber sobre costos oficiales de Meta,
                facturación y tasa BCV.</p>
        </div>

        <div class="space-y-3">
            <details class="group bg-white rounded-2xl border border-[#d8ddd3] p-5 transition-all">
                <summary
                    class="font-bold text-sm text-[#33521c] flex items-center justify-between cursor-pointer select-none">
                    <span>¿Cuánto cuesta realmente enviar mensajes por WhatsApp Cloud API en Venezuela?</span>
                    <span class="text-lg text-[#835425] group-open:rotate-45 transition-transform">+</span>
                </summary>
                <div
                    class="pt-3 text-xs sm:text-sm text-[#525b4d] leading-relaxed border-t border-[#d8ddd3]/60 mt-3 space-y-1.5">
                    <p>Meta ofrece <strong>1.000 mensajes de servicio gratuitos cada mes</strong> para todas las
                        empresas con WhatsApp Cloud API oficial. Si superas esa cuota, los mensajes de servicio
                        adicionales tienen un costo de <strong>USD $0,026</strong> cada uno, y los mensajes de marketing
                        saliente cuestan <strong>USD $0,0618</strong>.</p>
                    <p>En Venezuela, estos montos se cancelan al cambio oficial del Banco Central de Venezuela (BCV) con
                        tarjeta internacional o método de pago habilitado en Meta Business Suite.</p>
                </div>
            </details>

            <details class="group bg-white rounded-2xl border border-[#d8ddd3] p-5 transition-all">
                <summary
                    class="font-bold text-sm text-[#33521c] flex items-center justify-between cursor-pointer select-none">
                    <span>¿Cómo se actualiza la tasa del Banco Central de Venezuela (BCV)?</span>
                    <span class="text-lg text-[#835425] group-open:rotate-45 transition-transform">+</span>
                </summary>
                <div class="pt-3 text-xs sm:text-sm text-[#525b4d] leading-relaxed border-t border-[#d8ddd3]/60 mt-3">
                    <p>La calculadora de <strong>Capybaras Digitales</strong> consulta de manera automatizada la
                        cotización oficial del BCV en tiempo real a través de APIs financieras y sincronización directa
                        con el portal del Banco Central, garantizando que siempre veas los montos equivalentes exactos
                        en Bolívares (Bs.).</p>
                </div>
            </details>

            <details class="group bg-white rounded-2xl border border-[#d8ddd3] p-5 transition-all">
                <summary
                    class="font-bold text-sm text-[#33521c] flex items-center justify-between cursor-pointer select-none">
                    <span>¿Por qué atender a mano cuesta más que automatizar con Capybaras Digitales?</span>
                    <span class="text-lg text-[#835425] group-open:rotate-45 transition-transform">+</span>
                </summary>
                <div
                    class="pt-3 text-xs sm:text-sm text-[#525b4d] leading-relaxed border-t border-[#d8ddd3]/60 mt-3 space-y-1.5">
                    <p>Cuando atiendes manualmente, pagas horas de personal que no alcanzan a responder de noche o en
                        fines de semana, perdiendo hasta un 30% o 40% de tus ventas potenciales. La automatización con
                        IA y agentes de WhatsApp de Capybaras Digitales atiende al instante 24/7, califica prospectos y
                        agenda citas sin descanso.</p>
                </div>
            </details>
        </div>
    </section>

    <!-- Modal de Descarga de Informe / Captura de Lead -->
    @if ($showLeadModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-in fade-in duration-200">
            <div
                class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 border border-[#d8ddd3] shadow-2xl space-y-6 relative">
                <button type="button" wire:click="cerrarModal"
                    class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">
                    ✕
                </button>

                <div class="space-y-2 text-center">
                    <img src="{{ asset('images/logo.png') }}"
                        class="w-16 h-16 mx-auto rounded-full object-cover border-2 border-[#DAA520] shadow-md shadow-[#33521c]/20"
                        alt="Capybaras Digitales">
                    <h3 class="text-xl font-extrabold text-[#161E11]">Tu Informe Personalizado</h3>
                    <p class="text-xs text-[#525b4d]">
                        Completá tus datos para personalizar el informe con el membrete de tu empresa y descargarlo en
                        PDF en $ USD y Bolívares (BCV).
                    </p>
                </div>

                <form wire:submit.prevent="descargarPdf" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#525b4d] mb-1">Nombre completo</label>
                        <input type="text" wire:model="nombre" placeholder="Ej: Juan Pérez"
                            class="w-full px-4 py-2.5 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm focus:ring-2 focus:ring-[#33521c] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#525b4d] mb-1">Empresa / Negocio</label>
                        <input type="text" wire:model="empresa" placeholder="Ej: Distribuidora Norte"
                            class="w-full px-4 py-2.5 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm focus:ring-2 focus:ring-[#33521c] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#525b4d] mb-1">Email</label>
                        <input type="email" wire:model="email" placeholder="juan@empresa.com"
                            class="w-full px-4 py-2.5 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm focus:ring-2 focus:ring-[#33521c] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#525b4d] mb-1">WhatsApp de contacto</label>
                        <input type="text" wire:model="whatsapp" placeholder="+58 412 1234567"
                            class="w-full px-4 py-2.5 bg-[#FDFBF7] border border-[#d8ddd3] rounded-xl text-sm focus:ring-2 focus:ring-[#33521c] focus:outline-hidden">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3.5 rounded-xl font-extrabold text-sm bg-[#33521c] hover:bg-[#4a6b32] text-white shadow-md shadow-[#33521c]/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="descargarPdf">Descargar PDF en USD y Bs.</span>
                            <span wire:loading wire:target="descargarPdf">Generando informe...</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-center text-[#525b4d]">
                        🔒 Tus datos están protegidos bajo nuestras <a href="{{ route('politicas.privacidad') }}"
                            target="_blank" class="text-[#33521c] font-bold underline hover:text-[#4a6b32]">Políticas
                            de Privacidad</a>. No hacemos spam.
                    </p>
                </form>
            </div>
        </div>
    @endif

</div>
