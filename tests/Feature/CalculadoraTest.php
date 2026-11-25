<?php

namespace Tests\Feature;

use App\Models\Simulacion;
use App\Services\BcvExchangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CalculadoraTest extends TestCase
{
    use RefreshDatabase;

    public function test_pantalla_calculadora_carga_exitosamente_con_bcv_y_whatsapp(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Capybaras Digitales');
        $response->assertSee('Tasa Oficial BCV');
        $response->assertSee('584244582245');
        $response->assertSee('Todos los derechos reservados');
        $response->assertSee('Políticas de Privacidad');
    }

    public function test_pantalla_politicas_de_privacidad_carga_exitosamente(): void
    {
        $response = $this->get('/politicas-de-privacidad');
        $response->assertStatus(200);
        $response->assertSee('Políticas de Privacidad');
        $response->assertSee('Protección de Datos');
        $response->assertSee('Capybaras Digitales');
    }

    public function test_servicio_bcv_obtiene_tasa_valida(): void
    {
        $info = BcvExchangeService::obtenerTasa();
        $this->assertIsArray($info);
        $this->assertArrayHasKey('tasa', $info);
        $this->assertGreaterThan(0, $info['tasa']);
    }

    public function test_calculo_de_metricas_en_usd_y_bolivares(): void
    {
        $tasaPrueba = 920.00;
        $metricas = Simulacion::calcularMetricas([
            'conv_dia' => 30,
            'msj_por_conv' => 3,
            'personas' => 1,
            'horas_dia' => 4.0,
            'ticket_usd' => 35.00,
            'costo_hora_usd' => 4.50,
            'sin_responder' => 30,
            'msj_marketing' => 0,
            'conversion' => 8.0,
            'tasa_bcv' => $tasaPrueba,
        ]);

        // 30 conv/dia * 30 dias = 900 conversaciones/mes
        $this->assertEquals(900, $metricas['conv_mes']);
        // 900 * 3 msj/conv = 2700 mensajes de servicio
        $this->assertEquals(2700, $metricas['msj_servicio']);
        // 1000 gratis, 1700 pagos
        $this->assertEquals(1000, $metricas['gratis']);
        $this->assertEquals(1700, $metricas['pagos']);
        // 1700 * 0.026 USD = 44.20 USD
        $this->assertEquals(44.20, $metricas['meta_usd']);
        // En bolívares: 44.20 * 920 = 40664.00 Bs
        $this->assertEquals(40664.00, $metricas['meta_bs']);
    }

    public function test_componente_volt_calcula_reactivamente_y_guarda_en_mysql(): void
    {
        Volt::test('calculadora')
            ->set('convDia', 50)
            ->set('ticketUSD', 40.00)
            ->call('guardarSimulacion')
            ->assertSet('guardadoExitoso', true);

        $this->assertDatabaseHas('simulaciones', [
            'conv_dia' => 50,
            'ticket_usd' => 40.00,
        ]);
    }

    public function test_cargar_simulacion_por_uuid_en_url(): void
    {
        $sim = Simulacion::create([
            'conv_dia' => 80,
            'msj_por_conv' => 4,
            'personas' => 2,
            'horas_dia' => 5.0,
            'ticket_usd' => 120.00,
            'costo_hora_usd' => 5.00,
            'sin_responder' => 20,
            'msj_marketing' => 500,
            'conversion' => 10.0,
            'tasa_bcv' => 950.00,
        ]);

        Volt::test('calculadora', ['s' => $sim->uuid])
            ->assertSet('convDia', 80)
            ->assertSet('ticketUSD', 120.00);
    }

    public function test_descarga_de_informe_pdf(): void
    {
        $component = Volt::test('calculadora')
            ->set('convDia', 40)
            ->set('nombre', 'Carlos Test')
            ->set('email', 'carlos@test.com')
            ->call('descargarPdf');

        $this->assertDatabaseHas('simulaciones', [
            'email' => 'carlos@test.com',
            'nombre' => 'Carlos Test',
        ]);
    }
}
