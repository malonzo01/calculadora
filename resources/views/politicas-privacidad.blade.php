@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-6">
    <!-- Encabezado de la página -->
    <div class="space-y-3 border-b border-[#d8ddd3] pb-6">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#33521c] hover:text-[#4a6b32] transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Volver a la Calculadora</span>
        </a>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#e8f1dc] text-[#33521c] border border-[#dce6d1]">
            <span>🔒 Documento Legal</span>
        </div>

        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#161E11] tracking-tight">
            Políticas de Privacidad & Protección de Datos
        </h1>
        <p class="text-sm text-[#525b4d]">
            Última actualización: {{ now()->translatedFormat('F Y') }} · Capybaras Digitales
        </p>
    </div>

    <!-- Contenido de las Políticas -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#d8ddd3] shadow-xs space-y-8 text-[#161E11] leading-relaxed text-sm sm:text-base">
        
        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                1. Identificación del Responsable
            </h2>
            <p class="text-[#525b4d]">
                <strong>Capybaras Digitales</strong> (en adelante, "la Empresa", "nosotros" o "nuestro") es el titular y responsable del tratamiento de los datos personales recopilados a través de la herramienta interactiva <em>Calculadora de Retorno de WhatsApp</em> y sus servicios asociados de consultoría en automatización e inteligencia artificial.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                2. Datos Personales que Recopilamos
            </h2>
            <p class="text-[#525b4d]">
                Recopilamos únicamente los datos necesarios para brindar nuestras estimaciones y servicios:
            </p>
            <ul class="list-disc pl-5 space-y-1.5 text-[#525b4d]">
                <li><strong>Datos identificativos y de contacto:</strong> Nombre y apellido, correo electrónico corporativo o personal, número telefónico de WhatsApp y nombre de la empresa o rubro comercial.</li>
                <li><strong>Datos operativos de simulación:</strong> Parámetros ingresados (volumen de conversaciones diarias, mensajes salientes estimados, personas asignadas, horas dedicadas y ticket promedio en USD).</li>
                <li><strong>Datos técnicos de navegación:</strong> Dirección IP anonimizada, tipo de navegador y fecha/hora de generación del informe, con fines exclusivos de prevención de abusos y auditoría técnica.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                3. Finalidad del Tratamiento de los Datos
            </h2>
            <p class="text-[#525b4d]">
                Los datos suministrados son utilizados de forma transparente con los siguientes propósitos:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-2xl bg-[#FDFBF7] border border-[#d8ddd3]">
                    <strong class="block text-[#33521c] font-bold text-sm mb-1">📊 Emisión del Informe Personalizado</strong>
                    <p class="text-xs text-[#525b4d]">Generar, procesar y entregar el documento PDF con el análisis financiero y diagnóstico en USD y Bolívares.</p>
                </div>
                <div class="p-4 rounded-2xl bg-[#FDFBF7] border border-[#d8ddd3]">
                    <strong class="block text-[#33521c] font-bold text-sm mb-1">💬 Asesoría Técnica Directa</strong>
                    <p class="text-xs text-[#525b4d]">Contactar al usuario por WhatsApp o correo electrónico para coordinar sesiones de diagnóstico si así lo solicita.</p>
                </div>
                <div class="p-4 rounded-2xl bg-[#FDFBF7] border border-[#d8ddd3]">
                    <strong class="block text-[#33521c] font-bold text-sm mb-1">🔗 Guardado de Simulaciones</strong>
                    <p class="text-xs text-[#525b4d]">Permitir la recuperación y compartición de cálculos mediante identificadores únicos seguros (UUID).</p>
                </div>
                <div class="p-4 rounded-2xl bg-[#FDFBF7] border border-[#d8ddd3]">
                    <strong class="block text-[#33521c] font-bold text-sm mb-1">🚫 Cero Spam & Confidencialidad</strong>
                    <p class="text-xs text-[#525b4d]">No comercializamos, no alquilamos ni transferimos tus datos a terceros con fines de prospección no deseada.</p>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                4. Base Legal y Consentimiento
            </h2>
            <p class="text-[#525b4d]">
                El tratamiento de sus datos se sustenta en el <strong>consentimiento explícito</strong> otorgado por el usuario al completar voluntariamente los formularios de simulación y descarga de informe, así como en el interés legítimo de proporcionarle la estimación solicitada.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                5. Seguridad y Almacenamiento
            </h2>
            <p class="text-[#525b4d]">
                Implementamos estándares modernos de seguridad de la información, incluyendo protocolos HTTPS/TLS, cifrado de transmisiones, controles de acceso por roles a nuestras bases de datos MySQL y restricciones de aislamiento en servidores seguros.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                6. Derechos del Titular de los Datos
            </h2>
            <p class="text-[#525b4d]">
                Usted tiene pleno derecho a acceder, rectificar, limitar o solicitar la eliminación total de sus datos personales de nuestras bases de datos en cualquier momento. Para ejercer cualquiera de estos derechos, puede comunicarse directamente con nuestro equipo a través de nuestro canal de atención en WhatsApp.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-bold text-[#33521c] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#DAA520]"></span>
                7. Descargo de Responsabilidad y Tarifas de Terceros
            </h2>
            <p class="text-[#525b4d]">
                La presente herramienta realiza estimaciones analíticas basadas en las tarifas públicas y oficiales de la API de WhatsApp Cloud de Meta Platforms Inc. y la cotización oficial del Banco Central de Venezuela (BCV). Los resultados son simulaciones referenciales de apoyo a la toma de decisiones empresariales y no constituyen una garantía comercial de ingresos o ahorros contractuales.
            </p>
        </section>

        <div class="pt-4 border-t border-[#d8ddd3] flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-[#525b4d]">
                ¿Tenés dudas sobre cómo protegemos tu información? Escribinos directamente a nuestro equipo.
            </p>
            <a href="https://wa.me/584244582245?text=Hola%20Capybaras%20Digitales,%20tengo%20una%20consulta%20sobre%20sus%20políticas%20de%20privacidad" 
               target="_blank" 
               class="px-5 py-2.5 rounded-xl font-bold text-xs bg-[#33521c] hover:bg-[#4a6b32] text-white transition-all shadow-xs">
                Contactar Privacidad
            </a>
        </div>

    </div>
</div>
@endsection
