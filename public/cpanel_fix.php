<?php
/**
 * Capybaras Digitales — Reparador de Caché y Vistas para la Calculadora
 * Uso: http://calculadora.capybarasdigitales.com/cpanel_fix.php?key=capydental2026
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$secretKey = 'capydental2026';
$providedKey = $_GET['key'] ?? $_POST['key'] ?? '';

if ($providedKey !== $secretKey) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:50px;"><h2>Acceso Protegido</h2><p>Clave de seguridad requerida.</p></body></html>';
    exit;
}

$baseDir = dirname(__DIR__);
$viewsDir = $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views';
$cacheDir = $baseDir . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache';

$purgedViews = 0;
if (is_dir($viewsDir)) {
    foreach (scandir($viewsDir) as $f) {
        if ($f === '.' || $f === '..' || $f === '.gitignore') continue;
        $path = $viewsDir . DIRECTORY_SEPARATOR . $f;
        if (is_file($path) && @unlink($path)) {
            $purgedViews++;
        }
    }
}

$cacheFiles = ['config.php', 'routes-v7.php', 'routes.php', 'events.php', 'compiled.php', 'packages.php', 'services.php'];
$deletedCaches = 0;
if (is_dir($cacheDir)) {
    foreach ($cacheFiles as $cf) {
        $p = $cacheDir . DIRECTORY_SEPARATOR . $cf;
        if (file_exists($p) && @unlink($p)) {
            $deletedCaches++;
        }
    }
}

// Inicializar Laravel para Artisan optimize:clear
$artisanOutput = "";
$autoload = $baseDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$bootstrap = $baseDir . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';

if (file_exists($autoload) && file_exists($bootstrap)) {
    try {
        require_once $autoload;
        $app = require_once $bootstrap;
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $artisanOutput = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $artisanOutput = "Aviso Artisan: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora — Limpieza de Caché Exitosa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#161E11] text-white min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="bg-[#223d12] border border-[#33521c] p-8 rounded-3xl max-w-lg w-full text-center space-y-5 shadow-2xl">
        <div class="text-4xl">🦫✨</div>
        <h1 class="text-2xl font-black text-white">¡Caché de Calculadora Purgada!</h1>
        <div class="space-y-2 text-left bg-black/40 p-4 rounded-2xl text-xs font-mono text-emerald-300">
            <div>✓ Vistas Blade compiladas eliminadas: <?= $purgedViews ?> archivos.</div>
            <div>✓ Cachés obsoletas de bootstrap: <?= $deletedCaches ?> archivos.</div>
            <?php if ($artisanOutput): ?>
            <pre class="text-[11px] text-slate-300 mt-2 whitespace-pre-wrap"><?= htmlspecialchars($artisanOutput) ?></pre>
            <?php endif; ?>
        </div>
        <a href="/?preset=dental" class="inline-block px-6 py-3 bg-[#835425] hover:bg-[#b07a45] text-white font-bold text-sm rounded-xl transition shadow-lg">
            Ver Calculadora (Preset Dental) ➔
        </a>
    </div>
</body>
</html>
