<?php
$title = 'Jeu - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen overflow-hidden select-none';
$customStyles = '<link rel="stylesheet" href="/css/game.css">';

ob_start();
?>

<div class="relative w-full h-screen bg-gray-900 overflow-hidden">
    <!-- Map Component -->
    <?php require __DIR__ . '/components/map.php'; ?>

    <!-- Inventory Toggle Button -->
    <button id="inventory-toggle" class="absolute bottom-8 right-8 z-40 w-16 h-16 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </button>

    <!-- Inventory Component -->
    <?php require __DIR__ . '/components/inventory.php'; ?>

    <!-- Sub-Map Modal Component -->
    <?php require __DIR__ . '/components/submap-modal.php'; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Game JavaScript (ES6 Modules) -->
<script type="module">
    import { initMap, getMap } from '/js/modules/map.js';
    import { initMapPoints, initPanelControls } from '/js/modules/mapPoints.js';
    import { initInventory } from '/js/modules/inventory.js';
    import { initSubMapControls } from '/js/modules/subMap.js';

    // Map points data from PHP
    const mapPoints = <?= json_encode($mapPoints ?? []) ?>;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', async () => {
        console.log('Initializing game...');
        
        // Initialize inventory system
        initInventory();
        console.log('Inventory initialized');
        
        // Initialize map panel controls
        initPanelControls();
        console.log('Panel controls initialized');
        
        // Initialize sub-map controls
        initSubMapControls();
        console.log('Sub-map controls initialized');
        
        // Initialize map
        try {
            const map = await initMap();
            console.log('Map initialized:', map);
            
            // Add map points
            initMapPoints(map, mapPoints);
            console.log('Map points initialized');
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
