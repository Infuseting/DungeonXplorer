<!DOCTYPE html>
<html lang="fr" class="<?= $htmlClass ?? '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'DungeonXplorer' ?></title>
    <meta name="description" content="<?= $description ?? 'Plongez dans un RPG textuel immersif' ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php if (isset($customStyles)): ?>
    <style><?= $customStyles ?></style>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <?= $content ?>
    <!-- Character Renderer Script -->
    <script src="/js/character-renderer.js"></script>
</body>
</html>
