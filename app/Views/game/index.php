<?php
$title = 'Jeu - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen overflow-hidden select-none';
$customStyles = '<link rel="stylesheet" href="/css/game.css">';

ob_start();
?>

<div class="relative w-full h-screen bg-gray-900 overflow-hidden">
    <!-- Map & Scene Container -->
    <div id="map-container" class="absolute inset-0 z-0">
        <?php require __DIR__ . '/components/map.php'; ?>
    </div>
    
    <!-- Story/Game Scene Container (SPA) -->
    <div id="game-scene" class="absolute inset-0 z-20 hidden"></div>

    <!-- User Menu (Top Left) -->
    <div class="absolute top-4 left-4 z-50">
        <!-- Back Button (shown on submaps) -->
        <button id="back-to-parent-map" class="hidden mb-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg shadow-lg border-2 border-gray-600 flex items-center gap-2 transition-all hover:scale-105 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </button>
        
        <!-- User Avatar Button -->
        <div class="relative">
            <button id="user-menu-button" class="w-12 h-12 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95 text-xl font-bold">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="user-dropdown" class="hidden absolute top-14 left-0 w-72 bg-gray-800 border-2 border-gray-600 rounded-lg shadow-xl overflow-hidden">
                <button id="load-save-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Charger une partie
                </button>

                <button id="settings-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Paramètres
                </button>
                
                <button id="manual-save-button" class="w-full px-4 py-3 text-left text-white hover:bg-gray-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Sauvegarder
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

    <!-- Action Buttons (Bottom Right) -->
    <div class="absolute right-4 md:right-8 z-40 flex flex-col gap-2 md:gap-4" style="bottom: calc(1rem + env(safe-area-inset-bottom));">
        <!-- Quest Journal Button -->
        <button id="quest-journal-toggle" class="w-12 h-12 md:w-16 md:h-16 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg border-2 border-purple-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </button>
        
        <!-- Inventory Button -->
        <button id="inventory-toggle" class="w-12 h-12 md:w-16 md:h-16 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </button>

         <!-- Skills Button -->
         <button id="skills-toggle" class="w-12 h-12 md:w-16 md:h-16 bg-amber-600 hover:bg-amber-700 text-white rounded-full shadow-lg border-2 border-amber-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:h-8 md:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
        </button>
    </div>

    <!-- Inventory Component -->
    <?php require __DIR__ . '/components/inventory.php'; ?>

    <!-- Sub-Map Modal Component -->
    <?php require __DIR__ . '/components/submap-modal.php'; ?>
    
    <!-- NPC Interaction Modal -->
    <?php require __DIR__ . '/components/npc-modal.php'; ?>

    <!-- Quest Journal Modal -->
    <?php require __DIR__ . '/components/quest-journal-modal.php'; ?>

    <!-- Skills Modal -->
    <?php require __DIR__ . '/components/skills-modal.php'; ?>

    <!-- Save/Load Modal -->
    <?php require __DIR__ . '/components/save-load-modal.php'; ?>

    <!-- Settings Modal -->
    <?php require __DIR__ . '/components/settings-modal.php'; ?>

    <!-- Shop Modal -->
    <div id="shop-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80">
        <div class="bg-gray-900 border-2 border-yellow-600 rounded-lg p-4 w-full max-w-6xl h-5/6 flex flex-col relative shadow-2xl">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
                <h2 class="text-2xl font-bold text-yellow-500">💰 Boutique</h2>
                <div class="flex items-center gap-4">
                    <div class="text-xl font-bold text-yellow-400" id="shop-player-gold">0 🪙</div>
                    <button id="shop-close-btn" class="text-gray-400 hover:text-white text-2xl">&times;</button>
                </div>
            </div>

            <!-- Content (2 Columns) -->
            <div class="flex-1 flex gap-4 overflow-hidden">
                <!-- Left: Merchant Inventory -->
                <div class="flex-1 bg-gray-800 rounded p-2 flex flex-col border border-gray-700">
                    <h3 class="text-lg font-bold text-gray-300 mb-2 text-center border-b border-gray-700 pb-1">Marchand</h3>
                    <div id="shop-merchant-list" class="flex-1 overflow-y-auto pr-1 flex flex-wrap content-start gap-2">
                        <!-- Items populated by JS -->
                    </div>
                </div>

                <!-- Right: Player Inventory -->
                <div class="flex-1 bg-gray-800 rounded p-2 flex flex-col border border-gray-700">
                    <h3 class="text-lg font-bold text-gray-300 mb-2 text-center border-b border-gray-700 pb-1">Votre Inventaire</h3>
                    <div id="shop-inventory-list" class="flex-1 overflow-y-auto pr-1 flex flex-wrap content-start gap-2">
                        <!-- Items populated by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed right-4 z-50 flex flex-col gap-2 pointer-events-none" style="bottom: calc(1rem + env(safe-area-inset-bottom));"></div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Game JavaScript (ES6 Modules) -->
<script type="module">
    import { initMap, loadMapPoints, goBackToParentMap } from '/js/modules/map.js';
    import { initPanelControls } from '/js/modules/mapPoints.js';
    import { initInventory } from '/js/modules/inventory.js';
    import { initNPCModal } from '/js/modules/npcModal.js';
    import { openQuestJournal } from '/js/modules/questJournal.js';
    import { initShop } from '/js/modules/shop.js';
    import { initSoundManager, playSound } from '/js/modules/soundManager.js';
    import GameRouter from '/js/gameRouter.js';

        window.characterId = <?= $character->getId() ?>;

        document.addEventListener('DOMContentLoaded', async () => {
        console.log('Initializing game...');

                window.GameRouter = GameRouter;
        GameRouter.init();

        <?php if (isset($activeStory) && $activeStory): ?>
                        console.log('Resuming active story: <?= $activeStory['story_id'] ?>');
            
                         import('/js/modules/toast.js').then(m => {
                 m.showToast('Reprise de l\'histoire...', 'info');
             });

            setTimeout(() => {
                 GameRouter.navigate('/story/enter/<?= $activeStory['story_id'] ?>');
            }, 100); 
        <?php endif; ?>
        
                await initSoundManager();
        console.log('Sound manager initialized');
        
                initInventory();
        console.log('Inventory initialized');
        
                initPanelControls();
        console.log('Panel controls initialized');
        
                initNPCModal();
        console.log('NPC modal initialized');
        
                const questJournalBtn = document.getElementById('quest-journal-toggle');
        if (questJournalBtn) {
            questJournalBtn.addEventListener('click', () => {
                playSound('click');
                openQuestJournal();
            });
        }
        console.log('Quest Journal initialized');
        
                initShop();
        console.log('Shop initialized');
        
                const inventoryToggle = document.getElementById('inventory-toggle');
        if (inventoryToggle) {
            inventoryToggle.addEventListener('click', () => {
                playSound('click');
            });
        }
        
                const backButton = document.getElementById('back-to-parent-map');
        if (backButton) {
            backButton.addEventListener('click', async () => {
                playSound('click');
                await goBackToParentMap();
            });
        }
        
                const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                playSound('click');
                userDropdown.classList.toggle('hidden');
            });
            
                        document.addEventListener('click', (e) => {
                if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
        
        
        
                const loadSaveBtn = document.getElementById('load-save-button');
        if (loadSaveBtn) {
            loadSaveBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                playSound('click');
                userDropdown.classList.add('hidden');
                SaveSystem.open('load');
            });
        }
        
                const settingsButton = document.getElementById('settings-button');
        const settingsModal = document.getElementById('settings-modal');
        const settingsCloseBtn = document.getElementById('settings-close-btn');
        const settingsSaveBtn = document.getElementById('settings-save-btn');
        
        if (settingsButton && settingsModal) {
            settingsButton.addEventListener('click', async () => {
                playSound('click');
                userDropdown.classList.add('hidden');
                settingsModal.classList.remove('hidden');
                
                                const { getVolumes } = await import('/js/modules/soundManager.js');
                const volumes = getVolumes();
                document.getElementById('master-volume').value = Math.round(volumes.master * 100);
                document.getElementById('music-volume').value = Math.round(volumes.music * 100);
                document.getElementById('sfx-volume').value = Math.round(volumes.sfx * 100);
                updateVolumeDisplays();
            });
        }
        
        if (settingsCloseBtn) {
            settingsCloseBtn.addEventListener('click', () => {
                playSound('click');
                settingsModal.classList.add('hidden');
            });
        }
        
                const masterVolumeSlider = document.getElementById('master-volume');
        const musicVolumeSlider = document.getElementById('music-volume');
        const sfxVolumeSlider = document.getElementById('sfx-volume');
        
        function updateVolumeDisplays() {
            document.getElementById('master-volume-value').textContent = masterVolumeSlider.value + '%';
            document.getElementById('music-volume-value').textContent = musicVolumeSlider.value + '%';
            document.getElementById('sfx-volume-value').textContent = sfxVolumeSlider.value + '%';
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
        
                if (settingsSaveBtn) {
            settingsSaveBtn.addEventListener('click', async () => {
                playSound('click');
                
                const { setMasterVolume, setMusicVolume, setSFXVolume } = await import('/js/modules/soundManager.js');
                setMasterVolume(parseInt(masterVolumeSlider.value) / 100);
                setMusicVolume(parseInt(musicVolumeSlider.value) / 100);
                setSFXVolume(parseInt(sfxVolumeSlider.value) / 100);
                
                settingsModal.classList.add('hidden');
                
                                const { showToast } = await import('/js/modules/toast.js');
                showToast('Paramètres sauvegardés !', 'success');
            });
        }

                async function handleSaveAndAction(actionUrl) {
            const { showToast } = await import('/js/modules/toast.js');
            const { playSound } = await import('/js/modules/soundManager.js');
            
            playSound('click');
            showToast('Sauvegarde en cours...', 'info');
            
                        try {
                const formData = new FormData();
                formData.append('name', 'AutoSave ' + new Date().toLocaleString());
                
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

                const manualSaveBtn = document.getElementById('manual-save-button');
        if (manualSaveBtn) {
            manualSaveBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                playSound('click');
                userDropdown.classList.add('hidden');                 SaveSystem.open('save');
            });
        }

                const returnMenuBtn = document.getElementById('return-menu-button');
        if (returnMenuBtn) {
            returnMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                handleSaveAndAction('/personnage');
            });
        }

                const logoutBtn = document.getElementById('logout-button');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                handleSaveAndAction('/logout');
            });
        }
        
                document.addEventListener('click', (e) => {
                                                            const target = e.target.closest('button, a, .slot, .quest-item, .leaflet-interactive');
            
            if (target) {
                                                                                                                                                                
                                                                
                playSound('click');
            }
        });

                try {
            const map = await initMap();
            console.log('Map initialized:', map);
            
                        await loadMapPoints(1, window.characterId);
            console.log('Map points loaded');
            
                        const { loadMap, getCurrentMapId } = await import('/js/modules/map.js');
                        window.currentMapData = {
                mapId: 1,
                configUrl: '/assets/map/main/map_config.json',
                tilesPath: '/assets/map/main',
                mapName: 'Carte Principale'
            };
            
                        const backButton = document.getElementById('back-to-parent-map');
            if (backButton) {
                backButton.classList.add('hidden');
            }
            
        } catch (error) {
            console.error('Failed to initialize map:', error);
        }
    });
</script>
<style>
    /**
 * Game Styles
 * Styles for inventory, map, and game-specific UI elements
 */

/* Inventory Slot Styles */
.slot {
    background-color: rgba(17, 24, 39, 0.8);
    border: 1px solid #374151;
    transition: all 0.2s;
}

.slot:hover {
    border-color: #8b5cf6;
}

.slot.drag-over {
    background-color: rgba(139, 92, 246, 0.2);
    border-color: #8b5cf6;
}

/* Item Icon Styles */
.item-icon {
    cursor: grab;
}

.item-icon:active {
    cursor: grabbing;
}

/* Grid Cell Styles */
.grid-cell {
    width: 40px;
    height: 40px;
    border: 1px solid #374151;
    background-color: rgba(17, 24, 39, 0.6);
}

.grid-cell.occupied {
    background-color: rgba(55, 65, 81, 0.6);
}

/* Toast Animations */
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

.toast {
    animation: slideIn 0.3s ease-out forwards;
}

.toast.hiding {
    animation: fadeOut 0.3s ease-out forwards;
}

/* Tooltip Styles */
#item-tooltip {
    pointer-events: none;
    z-index: 100;
    transition: opacity 0.1s;
    box-shadow: 0 0 10px rgba(0,0,0,0.8);
}

.tooltip-header {
    border-bottom: 1px solid #4b5563;
    padding-bottom: 4px;
    margin-bottom: 4px;
}

.tooltip-stat {
    display: flex;
    justify-content: space-between;
    color: #9ca3af;
}

.tooltip-stat span:last-child {
    color: #fff;
}

/* Valid drop zone highlight */
.valid-drop-zone {
    border-color: #10b981 !important;
    background-color: rgba(16, 185, 129, 0.1);
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
}

/* Slot labels */
.slot-label {
    position: absolute;
    top: 2px;
    left: 2px;
    font-size: 8px;
    color: #9ca3af;
    text-transform: uppercase;
    pointer-events: none;
    z-index: 1;
}

/* Map Styles */
#map {
    width: 100%;
    height: 100%;
    background: url('/assets/images/texture_table.webp') no-repeat center center fixed;
    background-size: cover;
    z-index: 0;
}

.leaflet-container {
    background: transparent;
}
</style>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
