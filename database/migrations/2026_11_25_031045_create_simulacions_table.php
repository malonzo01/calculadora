<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulaciones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Parámetros de entrada (Moneda base USD)
            $table->integer('conv_dia')->default(30);
            $table->integer('msj_por_conv')->default(3);
            $table->integer('personas')->default(1);
            $table->decimal('horas_dia', 4, 1)->default(4.0);
            $table->decimal('ticket_usd', 12, 2)->default(35.00);
            $table->decimal('costo_hora_usd', 8, 2)->default(4.50); // Costo hora humano en USD
            $table->integer('sin_responder')->default(30);
            $table->integer('msj_marketing')->default(0);
            $table->decimal('conversion', 5, 2)->default(8.0);
            $table->decimal('tasa_bcv', 12, 4)->default(920.0000); // Tasa oficial automatizada

            // Resultados calculados en USD (Base)
            $table->integer('conv_mes')->default(0);
            $table->integer('msj_servicio')->default(0);
            $table->integer('gratis')->default(0);
            $table->integer('pagos')->default(0);
            $table->decimal('usd_servicio', 12, 2)->default(0);
            $table->decimal('usd_marketing', 12, 2)->default(0);
            $table->decimal('meta_usd', 12, 2)->default(0);
            $table->decimal('horas_mes', 8, 1)->default(0);
            $table->decimal('a_mano_usd', 12, 2)->default(0);
            $table->integer('sin_resp')->default(0);
            $table->decimal('ventas_perdidas', 8, 2)->default(0);
            $table->decimal('escape_usd', 12, 2)->default(0);
            $table->decimal('pct_vs_mano', 8, 2)->default(0);
            $table->decimal('pct_vs_escape', 8, 2)->default(0);
            $table->decimal('ventas_para_pagarla', 8, 2)->default(0);

            // Equivalentes en Bolívares (Bs. según tasa BCV)
            $table->decimal('meta_bs', 16, 2)->default(0);
            $table->decimal('a_mano_bs', 16, 2)->default(0);
            $table->decimal('escape_bs', 16, 2)->default(0);

            // Datos de contacto del Lead
            $table->string('nombre')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('empresa')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('informe_descargado_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulaciones');
    }
};
