<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Panel - DungeonXplorer</title>
    <meta name="description" content="Administration panel for DungeonXplorer">

    <!-- Social Media Meta Tags -->
    <?php
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $protocol . '://' . $host;
    $ogImage = $baseUrl . '/assets/images/auth_bg.png';
    $currentUrl = $baseUrl . ($_SERVER['REQUEST_URI'] ?? '');
    ?>
    <meta property="og:title" content="Admin Panel - DungeonXplorer">
    <meta property="og:description" content="Administration panel for DungeonXplorer">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:url" content="<?= $currentUrl ?>">
    <meta property="og:type" content="website">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Admin Panel - DungeonXplorer">
    <meta name="twitter:description" content="Administration panel for DungeonXplorer">
    <meta name="twitter:image" content="<?= $ogImage ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 fixed h-screen overflow-y-auto z-50">
            <div class="p-6 border-b border-slate-800">
                <div class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">
                    ⚔️ DungeonXplorer
                </div>
            </div>
            <nav class="py-4">
                <a href="/admin" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">📊</span>
                    Dashboard
                <a href="/admin/logs" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/logs') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">📋</span>
                    Diagnostic Logs
                </a>
                <a href="/admin/users" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">👥</span>
                    Gestion des Utilisateurs
                </a>
                <a href="/admin/characters" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/characters') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">⚔️</span>
                    Gestion des Personnages
                </a>
                <a href="/admin/classes" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/classes') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">🎓</span>
                    Classes & Compétences
                </a>
                <a href="/admin/map" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/map') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">🗺️</span>
                    Gestion de la Carte
                </a>
                <a href="/admin/points" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/points') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">⚙️</span>
                    Gestion des lieux
                </a>
                <a href="/admin/items" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/items') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">🎒</span>
                    Gestion des Items
                </a>
                <a href="/admin/npcs" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/npcs') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">👥</span>
                    Gestion des PNJ
                </a>
                <a href="/admin/dialogues" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/dialogues') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">💬</span>
                    Gestion des Dialogues
                </a>
                <a href="/admin/quests" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/quests') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">📜</span>
                    Gestion des Quêtes
                </a>
                <a href="/admin/monsters" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/monsters') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">👹</span>
                    Gestion des Monstres
                </a>
                <a href="/admin/procedural" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/procedural') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">📜</span>
                    Gestion des Histoires Procedurales
                </a>
                <a href="/admin/stories" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/stories') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">📜</span>
                    Gestion des Histoires
                </a>
                <a href="/" class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all">
                    <span class="mr-3 text-xl">🏠</span>
                    Retour au Jeu
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <header class="h-18 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-8 sticky top-0 z-40">
                <h1 class="text-2xl font-semibold"><?= $pageTitle ?? 'Admin Panel' ?></h1>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-semibold">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    </div>
                </div>
            </header>
            
            <main class="p-8">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>
</body>
</html>
