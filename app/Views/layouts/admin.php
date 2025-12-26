<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Panel - DungeonXplorer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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
    <link rel="stylesheet" href="/assets/css/map-icons.css">
    <style>
        body {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Card styles */
        .card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 0.75rem;
            padding: 1.5rem;
        }

        .card-header {
            font-size: 1.25rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 1rem;
        }

        /* Button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: #6366f1;
            color: white;
        }

        .btn-primary:hover {
            background: #4f46e5;
        }

        .btn-secondary {
            background: #334155;
            color: #e2e8f0;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #cbd5e1;
            font-size: 0.875rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: #020617;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            color: #e2e8f0;
            font-size: 0.9375rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: #64748b;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Search bar */
        .search-bar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-bar .form-input {
            flex: 1;
            min-width: 250px;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-200 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 fixed h-screen overflow-y-auto z-50">
            <div class="p-6 border-b border-slate-800">
                <div
                    class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-600 bg-clip-text text-transparent">
                    ⚔️ DungeonXplorer
                </div>
            </div>
            <nav class="py-4">
                <a href="/admin"
                    class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= $_SERVER['REQUEST_URI'] === '/admin' ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                    <span class="mr-3 text-xl">📊</span>
                    Dashboard
                    <a href="/admin/logs"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/logs') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">📋</span>
                        Diagnostic Logs
                    </a>
                    <a href="/admin/users"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">👥</span>
                        Gestion des Utilisateurs
                    </a>
                    <a href="/admin/characters"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/characters') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">⚔️</span>
                        Gestion des Personnages
                    </a>
                    <a href="/admin/classes"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/classes') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">🎓</span>
                        Classes & Compétences
                    </a>
                    <a href="/admin/map"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/map') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">🗺️</span>
                        Gestion de la Carte
                    </a>
                    <a href="/admin/points"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/points') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">⚙️</span>
                        Gestion des lieux
                    </a>
                    <a href="/admin/items"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/items') !== false ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">🎒</span>
                        Gestion des Items
                    </a>
                    <a href="/admin/npcs"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/npcs') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">👥</span>
                        Gestion des PNJ
                    </a>
                    <a href="/admin/dialogues"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/dialogues') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">💬</span>
                        Gestion des Dialogues
                    </a>
                    <a href="/admin/quests"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/quests') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">📜</span>
                        Gestion des Quêtes
                    </a>
                    <a href="/admin/monsters"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/monsters') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">👹</span>
                        Gestion des Monstres
                    </a>
                    <a href="/admin/procedural"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/procedural') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">📜</span>
                        Gestion des Histoires Procedurales
                    </a>
                    <a href="/admin/stories"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/stories') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">📜</span>
                        Gestion des Histoires
                    </a>
                    <a href="/admin/houses"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/houses') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">🏠</span>
                        Gestion des Maisons
                    </a>
                    <a href="/admin/furniture"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= $_SERVER['REQUEST_URI'] === '/admin/furniture' ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">🪑</span>
                        Gestion des Meubles
                    </a>
                    <a href="/admin/enchantments"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all <?= strpos($_SERVER['REQUEST_URI'], '/admin/enchantments') === 0 ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500' : '' ?>">
                        <span class="mr-3 text-xl">✨</span>
                        Gestion des Enchantements
                    </a>
                    <a href="/"
                        class="flex items-center px-6 py-3.5 text-slate-400 hover:bg-indigo-500/10 hover:text-slate-200 border-l-3 border-transparent hover:border-indigo-500 transition-all">
                        <span class="mr-3 text-xl">🎮</span>
                        Retour au Jeu
                    </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <header
                class="h-18 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-8 sticky top-0 z-40">
                <h1 class="text-2xl font-semibold"><?= $pageTitle ?? 'Admin Panel' ?></h1>
                <div class="flex items-center gap-4">
                    <!-- User Avatar Button & Menu -->
                    <div class="relative">
                        <button id="user-menu-button"
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-semibold text-white shadow-lg transition-transform hover:scale-110 active:scale-95">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="user-dropdown"
                            class="hidden absolute top-12 right-0 w-64 bg-slate-900 border border-slate-700 rounded-lg shadow-2xl overflow-hidden z-50">

                            <a href="/game"
                                class="w-full px-4 py-3 text-left text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">videogame_asset</span>
                                Retour au jeu
                            </a>

                            <button id="settings-button"
                                class="w-full px-4 py-3 text-left text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">settings</span>
                                Paramètres
                            </button>

                            <button id="save-menu-btn"
                                class="w-full px-4 py-3 text-left text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">save</span>
                                Sauvegarde et Menu
                            </button>

                            <button id="save-logout-btn"
                                class="w-full px-4 py-3 text-left text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">logout</span>
                                Sauvegarde et Déconnexion
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>
    <script type="module">
        import { showToast } from '/js/modules/toast.js';
        import { playSound, getVolumes, setMasterVolume, setMusicVolume, setSFXVolume } from '/js/modules/soundManager.js';

        // --- Menu Logic ---
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        const saveMenuBtn = document.getElementById('save-menu-btn');
        const saveLogoutBtn = document.getElementById('save-logout-btn');

        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                try { playSound('click'); } catch (e) { }
                userDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }

        // --- Save & Action Logic ---
        if (saveMenuBtn) saveMenuBtn.onclick = () => handleSaveAndAction('/personnage');
        if (saveLogoutBtn) saveLogoutBtn.onclick = () => handleSaveAndAction('/logout');

        async function handleSaveAndAction(actionUrl) {
            try { playSound('click'); } catch (e) { }
            showToast('Sauvegarde en cours...', 'info');

            try {
                const formData = new FormData();
                formData.append('name', 'Admin AutoSave ' + new Date().toLocaleString());

                const response = await fetch('/game/save', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showToast('Sauvegarde réussie !', 'success');
                } else {
                    showToast('Erreur sauvegarde: ' + data.message, 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur de connexion', 'error');
            }

            setTimeout(() => {
                window.location.href = actionUrl;
            }, 800);
        }

        // --- Settings Modal Logic ---
        const settingsButton = document.getElementById('settings-button');
        const settingsModal = document.getElementById('settings-modal');
        const settingsCloseBtn = document.getElementById('settings-close-btn');
        const settingsSaveBtn = document.getElementById('settings-save-btn');

        if (settingsButton && settingsModal) {
            settingsButton.addEventListener('click', async () => {
                try { playSound('click'); } catch (e) { }
                userDropdown.classList.add('hidden');
                settingsModal.classList.remove('hidden');

                const volumes = getVolumes();
                if (document.getElementById('master-volume')) document.getElementById('master-volume').value = Math.round(volumes.master * 100);
                if (document.getElementById('music-volume')) document.getElementById('music-volume').value = Math.round(volumes.music * 100);
                if (document.getElementById('sfx-volume')) document.getElementById('sfx-volume').value = Math.round(volumes.sfx * 100);
                updateVolumeDisplays();
            });
        }

        if (settingsCloseBtn) {
            settingsCloseBtn.addEventListener('click', () => {
                try { playSound('click'); } catch (e) { }
                settingsModal.classList.add('hidden');
            });
        }

        const masterVolumeSlider = document.getElementById('master-volume');
        const musicVolumeSlider = document.getElementById('music-volume');
        const sfxVolumeSlider = document.getElementById('sfx-volume');

        function updateVolumeDisplays() {
            if (masterVolumeSlider) document.getElementById('master-volume-value').textContent = masterVolumeSlider.value + '%';
            if (musicVolumeSlider) document.getElementById('music-volume-value').textContent = musicVolumeSlider.value + '%';
            if (sfxVolumeSlider) document.getElementById('sfx-volume-value').textContent = sfxVolumeSlider.value + '%';
        }

        if (masterVolumeSlider) masterVolumeSlider.addEventListener('input', updateVolumeDisplays);
        if (musicVolumeSlider) musicVolumeSlider.addEventListener('input', updateVolumeDisplays);
        if (sfxVolumeSlider) sfxVolumeSlider.addEventListener('input', updateVolumeDisplays);

        if (settingsSaveBtn) {
            settingsSaveBtn.addEventListener('click', async () => {
                try { playSound('click'); } catch (e) { }
                setMasterVolume(parseInt(masterVolumeSlider.value) / 100);
                setMusicVolume(parseInt(musicVolumeSlider.value) / 100);
                setSFXVolume(parseInt(sfxVolumeSlider.value) / 100);

                settingsModal.classList.add('hidden');
                showToast('Paramètres sauvegardés !', 'success');
            });
        }
    </script>
    <?php require __DIR__ . '/../game/components/settings-modal.php'; ?>
</body>

</html>