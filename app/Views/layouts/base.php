<!DOCTYPE html>
<html lang="fr" class="<?= $htmlClass ?? '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= $title ?? 'DungeonXplorer' ?></title>
    <meta name="description" content="<?= $description ?? 'Plongez dans un RPG textuel immersif' ?>">

    <!-- Social Media Meta Tags -->
    <?php
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $protocol . '://' . $host;
    $ogImage = $baseUrl . '/assets/images/auth_bg.png';
    $currentUrl = $baseUrl . ($_SERVER['REQUEST_URI'] ?? '');
    ?>
    <meta property="og:title" content="<?= $title ?? 'DungeonXplorer' ?>">
    <meta property="og:description" content="<?= $description ?? 'Plongez dans un RPG textuel immersif' ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:url" content="<?= $currentUrl ?>">
    <meta property="og:type" content="website">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $title ?? 'DungeonXplorer' ?>">
    <meta name="twitter:description" content="<?= $description ?? 'Plongez dans un RPG textuel immersif' ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">
    <!-- Google Fonts: Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Safari/Older Browser Compatibility -->
    <script>
        // Detect Safari and show warning if too old
        (function() {
            var ua = navigator.userAgent;
            var isSafari = /^((?!chrome|android).)*safari/i.test(ua);
            if (isSafari) {
                var match = ua.match(/Version\/(\d+)/);
                var version = match ? parseInt(match[1]) : 0;
                // Safari 14+ supports optional chaining and nullish coalescing
                if (version > 0 && version < 14) {
                    console.warn('[Safari] Version ' + version + ' detected - some features may not work properly');
                }
            }
        })();
    </script>
    <style>
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
    <?php if (isset($customStyles)): ?>
        <?php if (strpos($customStyles, '<link') !== false): ?>
            <?= $customStyles ?>
        <?php else: ?>
            <style><?= $customStyles ?></style>
        <?php endif; ?>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <?= $content ?>
    <!-- Character Renderer Script -->
    <script src="/js/character-renderer.js"></script>
</body>
</html>
