<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($story['name']) ?> - DungeonXplorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes slideIn { from { transform: translateX(-100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; transform: translateX(-100%); } }
        .animate-slideIn { animation: slideIn 0.3s ease-out; }
        .animate-fadeOut { animation: fadeOut 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-black text-white h-screen flex flex-col overflow-hidden font-sans select-none">
    
    <!-- User Menu (Top Left) -->
    <div class="fixed top-4 left-4 z-50">
        <div class="relative">
            <button id="user-menu-button" class="w-12 h-12 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95 text-xl font-bold">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="user-dropdown" class="hidden absolute top-14 left-0 w-72 bg-gray-800 border-2 border-gray-600 rounded-lg shadow-xl overflow-hidden">
                <button id="settings-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Paramètres
                </button>
                
                <button id="return-menu-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Sauvegarde et Menu
                </button>

                <button id="logout-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sauvegarde et Déconnexion
                </button>
            </div>
        </div>
    </div>

    <!-- Room Description Banner (Top) -->
    <div id="room-description" class="fixed top-0 left-0 right-0 z-40 bg-gray-900/95 border-b border-yellow-600/30 backdrop-blur-md shadow-lg hidden">
        <div class="max-w-7xl mx-auto px-8 py-6">
            <p id="room-desc" class="text-gray-300 font-bold text-lg mx-auto leading-relaxed text-center"></p>
        </div>
    </div>

    <!-- Bottom Right: Inventory, Quests & Exit -->
    <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-4">
        <!-- Exit Dungeon Button (Conditional) -->
        <button id="exit-dungeon-btn" class="hidden w-16 h-16 bg-yellow-600 hover:bg-yellow-500 text-white rounded-full shadow-lg border-2 border-yellow-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
        
        <!-- Quest Journal Button -->
        <button id="quest-journal-toggle" class="w-16 h-16 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg border-2 border-purple-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </button>
        
        <!-- Inventory Button -->
        <button id="inventory-toggle" class="w-16 h-16 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </button>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden relative" style="padding-top: 0;">
        
        <!-- Background Image Layer -->
        <div id="story-background" class="absolute inset-0 bg-cover bg-center transition-all duration-500 z-0 bg-[url('/assets/images/placeholder_dungeon.jpg')]">
            <div class="absolute inset-0 bg-black/30"></div>
        </div>

        <!-- Center Content (Node Info) -->
        <div class="relative z-10 flex-1 flex flex-col items-center justify-center p-8">
            
            <!-- Node Content Container -->
            <div id="main-content-container" class="bg-gray-900/90 border border-gray-600 rounded-xl p-8 max-w-4xl w-full shadow-2xl backdrop-blur-sm hidden">
                
                <!-- Interaction Area (Monsters, Loot, NPCs) -->
                <div id="interaction-area" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 hidden">
                    <!-- Monsters -->
                    <div id="monsters-container" class="hidden">
                        <h3 class="text-red-400 font-bold mb-2 flex items-center gap-2">
                            <span>⚔️</span> Monstres
                        </h3>
                        <div id="monsters-list" class="space-y-2"></div>
                    </div>

                    <!-- Loot -->
                    <div id="loot-container" class="hidden">
                        <h3 class="text-yellow-400 font-bold mb-2 flex items-center gap-2">
                            <span>💎</span> Butin
                        </h3>
                        <div id="loot-list" class="space-y-2"></div>
                    </div>

                    <!-- NPCs -->
                    <div id="npcs-container" class="hidden">
                        <h3 class="text-blue-400 font-bold mb-2 flex items-center gap-2">
                            <span>👤</span> PNJ
                        </h3>
                        <div id="npcs-list" class="space-y-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Drawer for Choices -->
    <div id="choices-drawer" class="fixed bottom-0 left-0 right-0 z-20 transition-transform duration-300 transform translate-y-full">
        <!-- Toggle Handle -->
        <div class="absolute -top-10 left-0 right-0 flex justify-center pointer-events-none">
            <button id="choices-toggle" class="pointer-events-auto bg-gray-900/90 text-yellow-500 px-8 py-2 rounded-t-xl border-t border-x border-yellow-600/50 hover:bg-gray-800 transition-all shadow-[0_-5px_15px_rgba(0,0,0,0.5)] flex items-center gap-2 group">
                <span class="font-bold tracking-wider uppercase text-sm">Actions & Déplacements</span>
                <span id="toggle-icon" class="group-hover:-translate-y-1 transition-transform">🔼</span>
            </button>
        </div>
        
        <!-- Drawer Content -->
        <div class="bg-gray-900/95 border-t border-yellow-600/30 p-6 pb-8 shadow-[0_-10px_40px_rgba(0,0,0,0.8)] backdrop-blur-md">
            <div id="choices-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 max-w-7xl mx-auto">
                <!-- Choices injected by JS -->
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php require_once __DIR__ . '/components/inventory.php'; ?>
    <?php require_once __DIR__ . '/components/quest-journal-modal.php'; ?>
    <?php require_once __DIR__ . '/components/settings-modal.php'; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 left-4 z-50 flex flex-col gap-2"></div>

<!-- Data passed to JS -->
<script>
    const STORY_ID = <?= $story['id'] ?>;

    // Drawer Toggle Logic
    document.addEventListener('DOMContentLoaded', () => {
        const drawer = document.getElementById('choices-drawer');
        const toggleBtn = document.getElementById('choices-toggle');
        const toggleIcon = document.getElementById('toggle-icon');
        let isOpen = false;

        toggleBtn.addEventListener('click', () => {
            isOpen = !isOpen;
            if (isOpen) {
                drawer.classList.remove('translate-y-full');
                toggleIcon.textContent = '🔽';
            } else {
                drawer.classList.add('translate-y-full');
                toggleIcon.textContent = '🔼';
            }
        });
        
        // User Menu Dropdown
        const userMenuBtn = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        
        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target) && e.target !== userMenuBtn) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
        
        // Settings button
        const settingsBtn = document.getElementById('settings-button');
        const settingsModal = document.getElementById('settings-modal');
        
        if (settingsBtn && settingsModal) {
            settingsBtn.addEventListener('click', async () => {
                userDropdown.classList.add('hidden');
                settingsModal.classList.remove('hidden');
                
                // Load current volumes
                const { getVolumes } = await import('/js/modules/soundManager.js');
                const volumes = getVolumes();
                const masterSlider = document.getElementById('master-volume');
                const musicSlider = document.getElementById('music-volume');
                const sfxSlider = document.getElementById('sfx-volume');
                
                if (masterSlider) masterSlider.value = Math.round(volumes.master * 100);
                if (musicSlider) musicSlider.value = Math.round(volumes.music * 100);
                if (sfxSlider) sfxSlider.value = Math.round(volumes.sfx * 100);
                
                // Update displays
                updateVolumeDisplays();
            });
        }
        
        // Settings modal close button
        const settingsCloseBtn = document.getElementById('settings-close-btn');
        if (settingsCloseBtn && settingsModal) {
            settingsCloseBtn.addEventListener('click', () => {
                settingsModal.classList.add('hidden');
            });
        }
        
        // Volume sliders
        const masterVolumeSlider = document.getElementById('master-volume');
        const musicVolumeSlider = document.getElementById('music-volume');
        const sfxVolumeSlider = document.getElementById('sfx-volume');
        
        function updateVolumeDisplays() {
            const masterValue = document.getElementById('master-volume-value');
            const musicValue = document.getElementById('music-volume-value');
            const sfxValue = document.getElementById('sfx-volume-value');
            
            if (masterVolumeSlider && masterValue) masterValue.textContent = masterVolumeSlider.value + '%';
            if (musicVolumeSlider && musicValue) musicValue.textContent = musicVolumeSlider.value + '%';
            if (sfxVolumeSlider && sfxValue) sfxValue.textContent = sfxVolumeSlider.value + '%';
        }
        
        if (masterVolumeSlider) {
            masterVolumeSlider.addEventListener('input', updateVolumeDisplays);
        }
        if (musicVolumeSlider) {
            musicVolumeSlider.addEventListener('input', updateVolumeDisplays);
        }
        if (sfxVolumeSlider) {
            sfxVolumeSlider.addEventListener('input', updateVolumeDisplays);
        }
        
        // Save settings button
        const settingsSaveBtn = document.getElementById('settings-save-btn');
        if (settingsSaveBtn && settingsModal) {
            settingsSaveBtn.addEventListener('click', async () => {
                const { setMasterVolume, setMusicVolume, setSFXVolume } = await import('/js/modules/soundManager.js');
                
                if (masterVolumeSlider) setMasterVolume(parseInt(masterVolumeSlider.value) / 100);
                if (musicVolumeSlider) setMusicVolume(parseInt(musicVolumeSlider.value) / 100);
                if (sfxVolumeSlider) setSFXVolume(parseInt(sfxVolumeSlider.value) / 100);
                
                settingsModal.classList.add('hidden');
                
                // Show toast if available
                try {
                    const { showToast } = await import('/js/modules/toast.js');
                    showToast('Paramètres sauvegardés !', 'success');
                } catch (e) {
                    console.log('Paramètres sauvegardés !');
                }
            });
        }
        
        // Return to menu button
        const returnMenuBtn = document.getElementById('return-menu-button');
        if (returnMenuBtn) {
            returnMenuBtn.addEventListener('click', async () => {
                // Save game first
                await fetch('/game/save', { method: 'POST' });
                window.location.href = '/personnage';
            });
        }
        
        // Logout button
        const logoutBtn = document.getElementById('logout-button');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                // Save game first
                await fetch('/game/save', { method: 'POST' });
                window.location.href = '/logout';
            });
        }
        
        // Exit Dungeon Button
        const exitDungeonBtn = document.getElementById('exit-dungeon-btn');
        if (exitDungeonBtn) {
            exitDungeonBtn.addEventListener('click', exitDungeon);
        }
    });

    async function exitDungeon() {
        if (!confirm('Voulez-vous vraiment sortir du donjon ?')) return;
        
        try {
            const formData = new FormData();
            formData.append('story_id', STORY_ID);
            
            const response = await fetch('/story/exit', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                window.location.href = '/game';
            } else {
                alert(data.message || 'Impossible de sortir');
            }
        } catch (e) {
            console.error(e);
            alert('Erreur lors de la sortie');
        }
    }
</script>

<script type="module">
    import { initInventory } from '/js/modules/inventory.js';
    import { openQuestJournal } from '/js/modules/questJournal.js';

    // Make character ID available globally
    window.characterId = <?= $_SESSION['character_id'] ?>;

    // Initialize modules after DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Initializing story modules...');
        
        // Initialize inventory system
        initInventory();
        console.log('Inventory initialized');
        
        // Quest Journal - override the toggle to use the module
        const questJournalBtn = document.getElementById('quest-journal-toggle');
        if (questJournalBtn) {
            // Remove existing listeners by cloning
            const newQuestBtn = questJournalBtn.cloneNode(true);
            questJournalBtn.parentNode.replaceChild(newQuestBtn, questJournalBtn);
            
            newQuestBtn.addEventListener('click', () => {
                openQuestJournal();
            });
        }
        console.log('Quest Journal initialized');
    });
</script>

<script type="module" src="/assets/js/story.js"></script>



</body>
</html>
