<!DOCTYPE html>
<html lang="fr" class="<?= $htmlClass ?? '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'DungeonXplorer' ?></title>
    <meta name="description" content="<?= $description ?? 'Plongez dans un RPG textuel immersif' ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        <?= $customStyles ?? '' ?>
    </style>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <?= $content ?>
    <script src="/assets/js/character-renderer.js"></script>
</body>
</html>
