<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Calculadora de Costos WhatsApp API & Retorno | Capybaras Digitales Venezuela' }}</title>
    <meta name="description"
        content="Calculá en $ USD y Bolívares con tasa oficial del BCV cuánto te cuesta atender WhatsApp a mano, las tarifas oficiales de Meta WhatsApp Cloud API y cuánto dinero recuperás al automatizar.">
    <meta name="keywords"
        content="calculadora whatsapp, precios whatsapp api venezuela, tarifas whatsapp cloud api, tasa bcv whatsapp, automatizacion whatsapp venezuela, crm whatsapp caracas, capybaras digitales">
    <meta name="author" content="Capybaras Digitales C.A.">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- GEO Optimization (Geotargeting Venezuela & LatAm) -->
    <meta name="geo.region" content="VE">
    <meta name="geo.placename" content="Caracas, Venezuela">
    <meta name="geo.position" content="10.4806;-66.9036">
    <meta name="ICBM" content="10.4806, -66.9036">
    <meta name="language" content="es-VE">

    <!-- Open Graph / Redes Sociales -->
    <meta property="og:locale" content="es_VE">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Calculadora de Costos WhatsApp API & Retorno · Capybaras Digitales">
    <meta property="og:description"
        content="Calculá en $ USD y Bolívares (tasa oficial BCV) el costo oficial de Meta, cuánto te cuesta atender a mano y la plata que se te escapa.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Capybaras Digitales C.A.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="500">
    <meta property="og:image:alt" content="Logotipo Oficial Capybaras Digitales C.A.">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Calculadora de Costos WhatsApp API & Retorno · Capybaras Digitales">
    <meta name="twitter:description"
        content="Calculá en $ USD y Bolívares a tasa oficial BCV el costo real de WhatsApp para tu negocio.">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64x64.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon-64x64.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Schema.org JSON-LD (GEO & Generative Engine Optimization) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@graph": [
        {
          "@type": "WebApplication",
          "@id": "http://calculadora.capybarasdigitales.com/#webapp",
          "name": "Calculadora de Costos WhatsApp API & Retorno Financiero",
          "url": "http://calculadora.capybarasdigitales.com/",
          "applicationCategory": "BusinessApplication",
          "operatingSystem": "All",
          "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
          },
          "description": "Herramienta online para estimar costos de WhatsApp Cloud API oficial de Meta en USD y Bolívares con tasa oficial del Banco Central de Venezuela (BCV), costo laboral y retorno de inversión."
        },
        {
          "@type": "LocalBusiness",
          "@id": "http://calculadora.capybarasdigitales.com/#organization",
          "name": "Capybaras Digitales C.A.",
          "url": "http://calculadora.capybarasdigitales.com/",
          "logo": "{{ asset('images/logo.png') }}",
          "image": "{{ asset('images/logo.png') }}",
          "description": "Agencia especializada en Marketing Digital, Desarrollo Web y Automatización con Inteligencia Artificial y WhatsApp en Venezuela.",
          "telephone": "+584244582245",
          "address": {
            "@type": "PostalAddress",
            "addressCountry": "VE",
            "addressLocality": "Caracas"
          },
          "areaServed": [
            { "@type": "Country", "name": "Venezuela" },
            { "@type": "Continent", "name": "Latin America" }
          ]
        },
        {
          "@type": "FAQPage",
          "@id": "http://calculadora.capybarasdigitales.com/#faq",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "¿Cuánto cuesta realmente enviar mensajes por WhatsApp Cloud API en Venezuela?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Los primeros 1.000 mensajes de servicio salientes de cada mes son 100% gratuitos. A partir de allí, Meta cobra USD $0,026 por mensaje de servicio y USD $0,0618 por mensaje de marketing, calculados en Bolívares a la tasa oficial del Banco Central de Venezuela (BCV)."
              }
            },
            {
              "@type": "Question",
              "name": "¿Cómo se actualiza la tasa del Banco Central de Venezuela (BCV)?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "La calculadora de Capybaras Digitales sincroniza automáticamente la cotización oficial del BCV en tiempo real, permitiendo ver las cifras en $ USD y en Bolívares (Bs.)."
              }
            },
            {
              "@type": "Question",
              "name": "¿Cómo ayuda Capybaras Digitales a automatizar la atención en WhatsApp?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Capybaras Digitales implementa agentes inteligentes con IA y CRM multicanal para atender consultas las 24 horas del día, responder de inmediato fuera de horario y evitar la fuga de ventas en tu negocio."
              }
            }
          ]
        }
      ]
    }
    </script>
</head>

<body
    class="min-h-full flex flex-col bg-[#FDFBF7] text-[#161E11] font-sans selection:bg-[#33521c] selection:text-white">
    <!-- Navbar Superior -->
    <header
        class="w-full border-b border-[#d8ddd3]/70 bg-[#FDFBF7]/90 backdrop-blur-md sticky top-0 z-40 transition-all">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="Capybaras Digitales C.A."
                    class="w-11 h-11 rounded-full object-cover border-2 border-[#DAA520] shadow-md shadow-[#33521c]/20 group-hover:scale-105 transition-transform">
                <div>
                    <span
                        class="block font-extrabold text-[#33521c] text-lg tracking-tight leading-tight group-hover:text-[#4a6b32] transition-colors">
                        Capybaras Digitales
                    </span>
                    <span class="block text-xs font-medium text-[#525b4d] tracking-wider uppercase">
                        Automatización con IA & WhatsApp
                    </span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <span
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#e8f1dc] text-[#33521c] border border-[#dce6d1]">
                    <span class="w-2 h-2 rounded-full bg-[#33521c] animate-pulse"></span>
                    Tarifas Meta Actualizadas
                </span>
                <a href="https://wa.me/584244582245?text=Hola%20Capybaras%20Digitales,%20estuve%20usando%20la%20calculadora%20de%20WhatsApp%20y%20quiero%20más%20información"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#835425] hover:bg-[#b07a45] text-white transition-all shadow-sm shadow-[#835425]/30">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.586-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.302c-.087.087-.179.182-.077.357.101.174.452.744.97 1.206.666.594 1.228.779 1.402.866.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z" />
                    </svg>
                    <span>Asesoría Directa</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#d8ddd3] bg-[#F4EFE6] text-[#525b4d] mt-16 pt-12 pb-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Capybaras Digitales"
                        class="w-12 h-12 rounded-full object-cover border-2 border-[#DAA520] shadow-xs">
                    <div>
                        <p class="font-extrabold text-[#33521c] text-base">Capybaras Digitales C.A.</p>
                        <p class="text-xs text-[#525b4d]">Soluciones de IA, automatización y agentes de WhatsApp para
                            negocios de alto impacto.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('politicas.privacidad') }}"
                        class="text-[#33521c] hover:text-[#4a6b32] hover:underline transition-colors">
                        Políticas de Privacidad
                    </a>
                    <span class="text-[#d8ddd3]">·</span>
                    <a href="{{ url('/') }}"
                        class="text-[#33521c] hover:text-[#4a6b32] hover:underline transition-colors">
                        Calculadora de WhatsApp
                    </a>
                    <span class="text-[#d8ddd3]">·</span>
                    <a href="https://wa.me/584244582245?text=Hola%20Capybaras%20Digitales,%20quiero%20contactar%20con%20su%20equipo"
                        target="_blank" class="text-[#835425] hover:text-[#b07a45] hover:underline transition-colors">
                        Contacto por WhatsApp
                    </a>
                </div>
            </div>

            <div
                class="border-t border-[#d8ddd3]/70 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-[#525b4d]/85">
                <p>
                    &copy; {{ date('Y') }} <strong>Capybaras Digitales</strong>. Todos los derechos reservados.
                </p>
                <p class="text-center md:text-right max-w-xl text-[11px] text-[#525b4d]/75">
                    Tarifas oficiales de WhatsApp Cloud API de Meta Platforms Inc. y cotización oficial del Banco
                    Central de Venezuela (BCV). Los resultados son simulaciones analíticas y no constituyen un valor
                    garantizado.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>
