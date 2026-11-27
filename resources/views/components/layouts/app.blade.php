<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Calculadora de Costos & Retorno WhatsApp · Capybaras Digitales' }}</title>
    <meta name="description" content="Calculá cuánto te cuesta atender por WhatsApp a mano, cuánto te cobra Meta con las tarifas oficiales y cuánta plata recuperás al automatizar con Capybaras Digitales.">
    <meta name="keywords" content="calculadora whatsapp venezuela, costo mensajes whatsapp meta, automatizacion whatsapp ia, fuga de clientes whatsapp, roi whatsapp bcv, capybaras digitales">
    <meta name="author" content="Capybaras Digitales">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Redes Sociales -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Calculadora WhatsApp · Capybaras Digitales">
    <meta property="og:locale" content="es_VE">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Calculadora: ¿Cuánto te sale automatizar tu WhatsApp? · Capybaras Digitales' }}">
    <meta property="og:description" content="Descubrí en 60 segundos tu fuga de clientes por responder tarde, el costo oficial de Meta y tu retorno exacto en USD y Bolívares Tasa BCV.">
    
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title ?? 'Calculadora de Costos & Retorno WhatsApp · Capybaras Digitales' }}">
    <meta name="twitter:description" content="Calculá tu costo de atención, facturación de Meta y recupero con IA en WhatsApp.">

    <!-- Schema.org JSON-LD (SEO y GEO) -->
    <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'WebApplication',
      'name' => 'Calculadora de Automatización WhatsApp',
      'url' => 'http://calculadora.capybarasdigitales.com',
      'applicationCategory' => ['BusinessApplication', 'FinancialApplication'],
      'operatingSystem' => 'All',
      'description' => 'Calculadora interactiva en USD y Bolívares (Tasa Oficial BCV) para estimar costos de Meta Cloud API, mano de obra y retorno de inversión al automatizar WhatsApp con Inteligencia Artificial.',
      'offers' => [
        '@type' => 'Offer',
        'price' => '0.00',
        'priceCurrency' => 'USD'
      ],
      'provider' => [
        '@type' => 'Organization',
        'name' => 'Capybaras Digitales',
        'url' => 'https://capybarasdigitales.com'
      ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🦫</text></svg>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col bg-[#FDFBF7] text-[#161E11] font-sans selection:bg-[#33521c] selection:text-white">
    <!-- Navbar Superior -->
    <header class="w-full border-b border-[#d8ddd3]/70 bg-[#FDFBF7]/90 backdrop-blur-md sticky top-0 z-40 transition-all">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-[#33521c] text-white flex items-center justify-center font-bold text-xl shadow-md shadow-[#33521c]/20 group-hover:scale-105 transition-transform">
                    🦫
                </div>
                <div>
                    <span class="block font-extrabold text-[#33521c] text-lg tracking-tight leading-tight group-hover:text-[#4a6b32] transition-colors">
                        Capybaras Digitales
                    </span>
                    <span class="block text-xs font-medium text-[#525b4d] tracking-wider uppercase">
                        Automatización con IA & WhatsApp
                    </span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#e8f1dc] text-[#33521c] border border-[#dce6d1]">
                    <span class="w-2 h-2 rounded-full bg-[#33521c] animate-pulse"></span>
                    Tarifas Meta Actualizadas
                </span>
                <a href="https://wa.me/?text=Hola%20Capybaras%20Digitales,%20estuve%20usando%20la%20calculadora%20de%20WhatsApp%20y%20quiero%20más%20información" 
                   target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#835425] hover:bg-[#b07a45] text-white transition-all shadow-sm shadow-[#835425]/30">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.586-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.302c-.087.087-.179.182-.077.357.101.174.452.744.97 1.206.666.594 1.228.779 1.402.866.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                    </svg>
                    <span>Asesoría Directa</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#d8ddd3] bg-[#F4EFE6] text-[#525b4d] mt-16 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-sm">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🦫</span>
                <div>
                    <p class="font-bold text-[#33521c]">Capybaras Digitales</p>
                    <p class="text-xs text-[#525b4d]">Soluciones de IA y automatización para negocios de alto impacto.</p>
                </div>
            </div>

            <p class="text-xs text-center md:text-right text-[#525b4d]/80 max-w-md">
                Las tarifas de Meta WhatsApp Cloud API son las oficiales del catálogo oficial. Los resultados calculados son estimaciones estratégicas basadas en tus números ingresados.
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
