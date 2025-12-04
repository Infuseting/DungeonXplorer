<!DOCTYPE html>
<html lang="fr" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        <?= $customStyles ?? '' ?>
    </style>
</head>
<body class="h-full">
    <div class="min-h-full flex">
        
        <!-- Left Side: Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="/assets/images/auth_bg.png" alt="Portail magique">
            <div class="absolute inset-0 bg-violet-900/40 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-90"></div>
            
            <div class="absolute bottom-0 left-0 p-12 text-white">
                <blockquote class="mt-8">
                    <p class="text-xl font-medium text-violet-100 italic">
                        <?= $quote ?? '"Le portail s\'ouvre devant vous. Oserez-vous franchir le seuil et affronter votre destin ?"' ?>
                    </p>
                    <footer class="mt-4">
                        <p class="text-base font-semibold text-violet-200"><?= $quoteAuthor ?? 'Le Gardien du Donjon' ?></p>
                    </footer>
                </blockquote>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white">
            <?= $content ?>
        </div>
    </div>
</body>
</html>
