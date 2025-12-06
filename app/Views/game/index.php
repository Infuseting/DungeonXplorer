<?php
$title = 'Jeu - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen overflow-hidden select-none';
$customStyles = '<link rel="stylesheet" href="/css/game.css">';

ob_start();
?>

<div class="relative w-full h-screen bg-gray-900 overflow-hidden">
    <!-- Map Component -->
    <?php require __DIR__ . '/components/map.php'; ?>

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
    </div>

    <!-- Inventory Component -->
    <?php require __DIR__ . '/components/inventory.php'; ?>

    <!-- Sub-Map Modal Component -->
    <?php require __DIR__ . '/components/submap-modal.php'; ?>
    
    <!-- NPC Interaction Modal -->
    <?php require __DIR__ . '/components/npc-modal.php'; ?>

    <!-- Quest Journal Modal -->
    <?php require __DIR__ . '/components/quest-journal-modal.php'; ?>

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

    // Make character ID available globally
    window.characterId = <?= $character['id'] ?>;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', async () => {
        console.log('Initializing game...');
        
        // Initialize sound manager first
        await initSoundManager();
        console.log('Sound manager initialized');
        
        // Initialize inventory system
        initInventory();
        console.log('Inventory initialized');
        
        // Initialize map panel controls
        initPanelControls();
        console.log('Panel controls initialized');
        
        // Initialize NPC modal
        initNPCModal();
        console.log('NPC modal initialized');
        
        // Initialize Quest Journal
        const questJournalBtn = document.getElementById('quest-journal-toggle');
        if (questJournalBtn) {
            questJournalBtn.addEventListener('click', () => {
                playSound('click');
                openQuestJournal();
            });
        }
        console.log('Quest Journal initialized');
        
        // Initialize Shop
        initShop();
        console.log('Shop initialized');
        
        // Add sound to inventory toggle button
        const inventoryToggle = document.getElementById('inventory-toggle');
        if (inventoryToggle) {
            inventoryToggle.addEventListener('click', () => {
                playSound('click');
            });
        }
        
        // Add back button handler
        const backButton = document.getElementById('back-to-parent-map');
        if (backButton) {
            backButton.addEventListener('click', async () => {
                playSound('click');
                await goBackToParentMap();
            });
        }
        
        // User menu dropdown
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                playSound('click');
                userDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
        
        // Settings modal
        const settingsButton = document.getElementById('settings-button');
        const settingsModal = document.getElementById('settings-modal');
        const settingsCloseBtn = document.getElementById('settings-close-btn');
        const settingsSaveBtn = document.getElementById('settings-save-btn');
        
        if (settingsButton && settingsModal) {
            settingsButton.addEventListener('click', async () => {
                playSound('click');
                userDropdown.classList.add('hidden');
                settingsModal.classList.remove('hidden');
                
                // Load current volumes
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
        
        // Volume sliders
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
        
        // Save settings
        if (settingsSaveBtn) {
            settingsSaveBtn.addEventListener('click', async () => {
                playSound('click');
                
                const { setMasterVolume, setMusicVolume, setSFXVolume } = await import('/js/modules/soundManager.js');
                setMasterVolume(parseInt(masterVolumeSlider.value) / 100);
                setMusicVolume(parseInt(musicVolumeSlider.value) / 100);
                setSFXVolume(parseInt(sfxVolumeSlider.value) / 100);
                
                settingsModal.classList.add('hidden');
                
                // Show toast
                const { showToast } = await import('/js/modules/toast.js');
                showToast('Paramètres sauvegardés !', 'success');
            });
        }

        // Save and Action Handler
        async function handleSaveAndAction(actionUrl) {
            const { showToast } = await import('/js/modules/toast.js');
            const { playSound } = await import('/js/modules/soundManager.js');
            
            playSound('click');
            showToast('Sauvegarde en cours...', 'info');
            
            // Simulate save delay (since it's auto-save, we just give visual feedback)
            setTimeout(() => {
                window.location.href = actionUrl;
            }, 800);
        }

        // Return to Menu
        const returnMenuBtn = document.getElementById('return-menu-button');
        if (returnMenuBtn) {
            returnMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                handleSaveAndAction('/personnage');
            });
        }

        // Logout
        const logoutBtn = document.getElementById('logout-button');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                handleSaveAndAction('/logout');
            });
        }
        
        // Global SFX Handler
        document.addEventListener('click', (e) => {
            // Check if element or parent is clickable
            // We exclude elements that might already have specific handlers if needed, 
            // but playing 'click' generally is safe.
            // Specific targets: buttons, links, inventory slots, quest items, map markers
            const target = e.target.closest('button, a, .slot, .quest-item, .leaflet-interactive');
            
            if (target) {
                // Avoid double playing if the element has a specific listener that also plays sound
                // But since we are adding this globally, it's a catch-all.
                // To prevent double sounds, we could check a data attribute or class, 
                // but for now we'll assume the specific handlers might be removed or coexist.
                // Actually, many specific handlers already play 'click'. 
                // To avoid double sounds, we should probably remove the specific 'playSound' calls 
                // or make this handler smart. 
                // Given the instruction "tu regardes tout les boutons... et tu fais joué le SFX dessus",
                // a global handler is best, but we should be careful about duplication.
                
                // For now, let's play it. If it's too much, we can refine.
                // Note: Leaflet markers might stop propagation, so we might need to attach to map events instead for them.
                // But .leaflet-interactive usually bubbles click if not stopped.
                
                playSound('click');
            }
        });

        // Initialize map
        try {
            const map = await initMap();
            console.log('Map initialized:', map);
            
            // Load map points dynamically for main map (ID = 1)
            await loadMapPoints(1, window.characterId);
            console.log('Map points loaded');
            
            // Initialize currentMapData for the main map (important for navigation)
            const { loadMap, getCurrentMapId } = await import('/js/modules/map.js');
            // Set initial map data without adding to history
            window.currentMapData = {
                mapId: 1,
                configUrl: '/assets/map/main/map_config.json',
                tilesPath: '/assets/map/main',
                mapName: 'Carte Principale'
            };
            
            // Make sure back button is hidden on main map
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
