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
    <?php if (isset($customStyles)): ?>
    <style>
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
        <?= $customStyles ?>
    </style>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <?= $content ?>
    <!-- Character Renderer Script -->
    <script src="/js/character-renderer.js"></script>
</body>
</html>
