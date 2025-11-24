<?php
$title = 'Jeu - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen overflow-hidden select-none';
$customStyles = '
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
    .item-icon {
        cursor: grab;
    }
    .item-icon:active {
        cursor: grabbing;
    }
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
    #map {
        width: 100%;
        height: 100%;
        background: url(\'/assets/images/texture_table.webp\') no-repeat center center fixed;
        background-size: cover;
        z-index: 0;
    }
    .leaflet-container {
        background: transparent;
    }
';

ob_start();
?>

<div class="relative w-full h-screen bg-gray-900 overflow-hidden">
    <!-- Game Area (Full Screen) -->
    <div class="absolute inset-0 bg-gray-900">
        <div id="map"></div>
    </div>

    <!-- Inventory Toggle Button -->
    <button id="inventory-toggle" class="absolute bottom-8 right-8 z-40 w-16 h-16 bg-violet-600 hover:bg-violet-700 text-white rounded-full shadow-lg border-2 border-violet-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </button>

    <!-- Inventory Modal -->
    <div id="inventory-modal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div id="inventory-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
        
        <!-- Modal Content -->
        <div class="relative z-10 w-full h-full flex items-center justify-center p-8 pointer-events-none">
            <div class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex gap-8 p-8 max-w-6xl w-full pointer-events-auto transform transition-all scale-100">
                
                <!-- Left Column: Character & Equipment -->
                <div class="w-1/2 flex flex-col">
                    <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-600 pb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Équipement
                    </h2>
                    
                    <div class="flex-1 relative flex items-center justify-center bg-gray-900/50 rounded-xl border border-gray-700 p-4">
                        <!-- Silhouette / Paper Doll -->
                        <div class="relative w-[400px] h-[600px]">
                            <div class="absolute top-4 left-1/2 transform -translate-x-1/2 w-16 h-16 slot rounded-lg" data-slot="head">
                    <span class="slot-label">Tête</span>
                    <?php if(isset($inventory['equipped']['head'])): $item = $inventory['equipped']['head']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Shoulders -->
                <div class="absolute top-16 left-16 w-16 h-16 slot rounded-lg" data-slot="shoulders">
                    <span class="slot-label">Épaules</span>
                    <?php if(isset($inventory['equipped']['shoulders'])): $item = $inventory['equipped']['shoulders']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Amulet -->
                <div class="absolute top-16 right-16 transform w-16 h-16 slot rounded-lg" data-slot="amulet">
                    <span class="slot-label">Amulette</span>
                    <?php if(isset($inventory['equipped']['amulet'])): $item = $inventory['equipped']['amulet']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Chest -->
                <div class="absolute top-36 left-1/2 transform -translate-x-1/2 w-20 h-24 slot rounded-lg" data-slot="chest">
                    <span class="slot-label">Torse</span>
                    <?php if(isset($inventory['equipped']['chest'])): $item = $inventory['equipped']['chest']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                
                <!-- Gloves -->
                <div class="absolute top-40 left-8 w-16 h-32 slot rounded-lg" data-slot="gloves">
                    <span class="slot-label">Gants</span>
                    <?php if(isset($inventory['equipped']['gloves'])): $item = $inventory['equipped']['gloves']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Bracers -->
                <div class="absolute top-40 right-8 w-16 h-32 slot rounded-lg" data-slot="bracers">
                    <span class="slot-label">Bracelets</span>
                    <?php if(isset($inventory['equipped']['bracers'])): $item = $inventory['equipped']['bracers']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Belt -->
                <div class="absolute top-[260px] left-1/2 transform -translate-x-1/2 w-20 h-10 slot rounded-lg" data-slot="belt">
                    <span class="slot-label">Ceinture</span>
                    <?php if(isset($inventory['equipped']['belt'])): $item = $inventory['equipped']['belt']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Legs -->
                <div class="absolute top-[310px] left-1/2 w-20 transform -translate-x-1/2 h-24 slot rounded-lg" data-slot="legs">
                    <span class="slot-label">Jambes</span>
                    <?php if(isset($inventory['equipped']['legs'])): $item = $inventory['equipped']['legs']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Boots -->
                <div class="absolute top-[420px] left-1/2 w-20 transform -translate-x-1/2 h-16 slot rounded-lg" data-slot="boots">
                    <span class="slot-label">Bottes</span>
                    <?php if(isset($inventory['equipped']['boots'])): $item = $inventory['equipped']['boots']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <!-- Rings -->
                <div class="absolute top-80 left-28 w-10 h-10 slot rounded-full" data-slot="ring_1">
                    <span class="slot-label">Ring1</span>
                    <?php if(isset($inventory['equipped']['ring_1'])): $item = $inventory['equipped']['ring_1']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                <div class="absolute top-80 right-28 w-10 h-10 slot rounded-full" data-slot="ring_2">
                    <span class="slot-label">Ring2</span>
                    <?php if(isset($inventory['equipped']['ring_2'])): $item = $inventory['equipped']['ring_2']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                
                <div class="absolute top-[360px] left-8 w-16 h-32 slot rounded-lg" data-slot="main_hand">
                    <span class="slot-label">Main</span>
                    <?php if(isset($inventory['equipped']['main_hand'])): $item = $inventory['equipped']['main_hand']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-two-handed="<?= $item['two_handed'] ? '1' : '0' ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php elseif(isset($inventory['equipped']['off_hand']) && $inventory['equipped']['off_hand']['two_handed']): ?>
                        <!-- Show grayed-out image if off_hand has two-handed weapon -->
                        <img src="/assets/images/items/<?= $inventory['equipped']['off_hand']['icon'] ?>" 
                             class="w-full h-full object-contain p-1 opacity-30 pointer-events-none"
                             style="filter: grayscale(100%);">
                        <div class="absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none"></div>
                    <?php endif; ?>
                </div>
                <div class="absolute top-[360px] right-8 w-16 h-32 slot rounded-lg" data-slot="off_hand">
                    <span class="slot-label">Off</span>
                    <?php if(isset($inventory['equipped']['off_hand'])): $item = $inventory['equipped']['off_hand']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-two-handed="<?= $item['two_handed'] ? '1' : '0' ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php elseif(isset($inventory['equipped']['main_hand']) && $inventory['equipped']['main_hand']['two_handed']): ?>
                        <!-- Show grayed-out image if main_hand has two-handed weapon -->
                        <img src="/assets/images/items/<?= $inventory['equipped']['main_hand']['icon'] ?>" 
                             class="w-full h-full object-contain p-1 opacity-30 pointer-events-none"
                             style="filter: grayscale(100%);">
                        <div class="absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none"></div>
                    <?php endif; ?>
                </div>
                
                <!-- Backpack Slot (Special) -->
                <div class="absolute bottom-4 right-4 w-12 h-12 slot rounded-lg border-yellow-500/50" data-slot="backpack">
                    <span class="slot-label">Sac</span>
                    <?php if(isset($inventory['equipped']['backpack'])): $item = $inventory['equipped']['backpack']; ?>
                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                             class="w-full h-full object-contain item-icon p-1" 
                             draggable="true" 
                             data-id="<?= $item['id'] ?>"
                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                             data-name="<?= htmlspecialchars($item['name']) ?>"
                             data-type="<?= htmlspecialchars($item['type']) ?>"
                             data-description="<?= htmlspecialchars($item['description']) ?>"
                             data-stats='<?= htmlspecialchars($item['stats']) ?>'>
                    <?php endif; ?>
                </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Inventory -->
                <div class="w-1/2 flex flex-col">
                    <h2 class="text-2xl font-bold text-white mb-6 border-b border-gray-600 pb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Sac à Dos
                    </h2>
                    
                    <!-- Pockets -->
                    <div class="mb-6 bg-gray-900/50 p-4 rounded-xl border border-gray-700">
                        <h3 class="text-sm font-medium text-gray-400 mb-3 uppercase tracking-wider">Poches Rapides</h3>
                        <div class="flex gap-4 justify-center">
                            <?php for($i=0; $i<4; $i++): ?>
                                <div class="w-16 h-16 slot rounded-lg flex items-center justify-center relative bg-gray-800" data-location="pockets" data-index="<?= $i ?>">
                                    <?php if(isset($inventory['pockets'][$i])): $item = $inventory['pockets'][$i]; ?>
                                        <img src="/assets/images/items/<?= $item['icon'] ?>" 
                                             class="w-12 h-12 object-contain item-icon" 
                                             draggable="true" 
                                             data-id="<?= $item['id'] ?>"
                                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                                             data-name="<?= htmlspecialchars($item['name']) ?>"
                                             data-type="<?= htmlspecialchars($item['type']) ?>"
                                             data-description="<?= htmlspecialchars($item['description']) ?>"
                                             data-stats='<?= htmlspecialchars($item['stats']) ?>'
                                        >
                                        <span class="absolute bottom-0 right-1 text-xs font-bold text-white drop-shadow-md"><?= $item['quantity'] > 1 ? $item['quantity'] : '' ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Backpack Grid -->
                    <div class="flex-1 flex flex-col bg-gray-900/50 p-4 rounded-xl border border-gray-700">
                        <h3 class="text-sm font-medium text-gray-400 mb-3 uppercase tracking-wider flex justify-between">
                            <span>Contenu du Sac</span>
                            <span class="text-xs text-gray-500">0 / 50 kg</span>
                        </h3>
                        
                        <div class="flex-1 overflow-auto flex items-center justify-center bg-gray-900 rounded-lg border border-gray-700 p-4">
                            <!-- Grid Container -->
                            <div id="backpack-grid" class="grid gap-0 bg-gray-800 border border-gray-600 relative shadow-inner" 
                                 style="grid-template-columns: repeat(6, 40px); grid-template-rows: repeat(4, 40px); width: 242px; height: 162px;">
                                <!-- Grid Cells generated by JS or PHP loop -->
                                <?php for($y=0; $y<4; $y++): ?>
                                    <?php for($x=0; $x<6; $x++): ?>
                                        <div class="grid-cell border-gray-700/50" data-x="<?= $x ?>" data-y="<?= $y ?>"></div>
                                    <?php endfor; ?>
                                <?php endfor; ?>
                                
                                <!-- Items placed on grid -->
                                <?php foreach($inventory['backpack'] as $item): ?>
                                    <div class="absolute bg-violet-600/30 border border-violet-500/50 flex items-center justify-center item-icon hover:bg-violet-600/50 transition-colors"
                                         style="left: <?= $item['grid_x'] * 40 ?>px; top: <?= $item['grid_y'] * 40 ?>px; width: <?= $item['width'] * 40 ?>px; height: <?= $item['height'] * 40 ?>px;"
                                         draggable="true" 
                                         data-id="<?= $item['id'] ?>"
                                         data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                                         data-name="<?= htmlspecialchars($item['name']) ?>"
                                         data-type="<?= htmlspecialchars($item['type']) ?>"
                                         data-description="<?= htmlspecialchars($item['description']) ?>"
                                         data-stats='<?= htmlspecialchars($item['stats']) ?>'
                                    >
                                        <img src="/assets/images/items/<?= $item['icon'] ?>" class="max-w-full max-h-full object-contain">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Tooltip Container -->
    <div id="item-tooltip" class="fixed hidden bg-gray-900 border border-gray-600 p-3 rounded shadow-xl max-w-xs text-sm text-gray-300">
        <div class="tooltip-header">
            <div id="tooltip-name" class="font-bold text-violet-400 text-base"></div>
            <div id="tooltip-type" class="text-xs text-gray-500 uppercase"></div>
        </div>
        <div id="tooltip-stats" class="space-y-1 mb-2"></div>
        <div id="tooltip-desc" class="italic text-gray-400 text-xs border-t border-gray-700 pt-2"></div>
    </div>
</div>

<script>
    let draggedItem = null;
    let draggedItemData = null;

    // Initialize Draggables
    document.querySelectorAll('.item-icon').forEach(item => {
        setupDraggable(item);
        setupTooltip(item);
    });

    function setupTooltip(item) {
        const tooltip = document.getElementById('item-tooltip');
        const nameEl = document.getElementById('tooltip-name');
        const typeEl = document.getElementById('tooltip-type');
        const statsEl = document.getElementById('tooltip-stats');
        const descEl = document.getElementById('tooltip-desc');

        item.addEventListener('mouseenter', e => {
            if (draggedItem) return; // Don't show tooltip while dragging

            // Populate Data
            nameEl.textContent = item.dataset.name;
            typeEl.textContent = item.dataset.type;
            descEl.textContent = item.dataset.description;

            // Parse and display stats
            statsEl.innerHTML = '';
            try {
                const stats = JSON.parse(item.dataset.stats);
                for (const [key, value] of Object.entries(stats)) {
                    const statRow = document.createElement('div');
                    statRow.className = 'tooltip-stat';
                    // Format key (e.g., capacity_width -> Capacity Width)
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    statRow.innerHTML = `<span>${label}</span><span>${value}</span>`;
                    statsEl.appendChild(statRow);
                }
            } catch (e) {
                console.error('Error parsing stats', e);
            }

            tooltip.classList.remove('hidden');
        });

        item.addEventListener('mousemove', e => {
            if (draggedItem) return;
            
            // Position tooltip near cursor but not under it
            const offset = 15;
            let left = e.clientX + offset;
            let top = e.clientY + offset;

            // Boundary checks (keep on screen)
            if (left + tooltip.offsetWidth > window.innerWidth) {
                left = e.clientX - tooltip.offsetWidth - offset;
            }
            if (top + tooltip.offsetHeight > window.innerHeight) {
                top = e.clientY - tooltip.offsetHeight - offset;
            }

            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
        });

        item.addEventListener('mouseleave', () => {
            tooltip.classList.add('hidden');
        });
    }

    function setupDraggable(item) {
        // Ctrl+Click to equip
        item.addEventListener('click', e => {
            if (e.ctrlKey && item.dataset.slotType && item.dataset.slotType !== 'none') {
                e.preventDefault();
                quickEquipItem(item.dataset.id);
            }
        });

        item.addEventListener('dragstart', e => {
            draggedItem = e.target;
            // Store item data
            draggedItemData = {
                id: e.target.dataset.id,
                width: parseInt(e.target.dataset.width || 1),
                height: parseInt(e.target.dataset.height || 1),
                slotType: e.target.dataset.slotType
            };
            e.dataTransfer.setData('text/plain', e.target.dataset.id);
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => e.target.classList.add('opacity-50'), 0);

            // Highlight valid drop zones
            highlightValidSlots(draggedItemData.slotType);
        });

        item.addEventListener('dragend', e => {
            e.target.classList.remove('opacity-50');
            draggedItem = null;
            draggedItemData = null;
            // Remove all highlights
            document.querySelectorAll('.drag-over, .valid-drop-zone').forEach(el => {
                el.classList.remove('drag-over', 'valid-drop-zone');
            });
        });
    }

    function highlightValidSlots(itemSlotType) {
        if (!itemSlotType || itemSlotType === 'none') return;

        // Map item types to valid slots
        const slotMapping = {
            'head': ['head'],
            'shoulders': ['shoulders'],
            'amulet': ['amulet'],
            'chest': ['chest'],
            'belt': ['belt'],
            'legs': ['legs'],
            'boots': ['boots'],
            'ring': ['ring_1', 'ring_2'],
            'main_hand': ['main_hand', 'off_hand'], // Weapons can go in either hand
            'off_hand': ['off_hand'],
            'gloves': ['gloves'],
            'bracers': ['bracers'],
            'backpack': ['backpack']
        };

        const validSlots = slotMapping[itemSlotType] || [];
        validSlots.forEach(slotName => {
            const slot = document.querySelector(`.slot[data-slot="${slotName}"]`);
            if (slot) {
                slot.classList.add('valid-drop-zone');
            }
        });
    }

    function quickEquipItem(itemId) {
        // Find the item element
        const itemElement = document.querySelector(`.item-icon[data-id="${itemId}"]`);
        if (!itemElement) return;

        // Get item slot type to determine target slot
        const slotType = itemElement.dataset.slotType;
        
        fetch('/game/inventory/equip', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ itemId: itemId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Determine target slot
                const slotMapping = {
                    'head': 'head',
                    'shoulders': 'shoulders',
                    'amulet': 'amulet',
                    'chest': 'chest',
                    'belt': 'belt',
                    'legs': 'legs',
                    'boots': 'boots',
                    'ring': 'ring_1', // Default to first ring slot
                    'main_hand': 'main_hand',
                    'off_hand': 'off_hand',
                    'gloves': 'gloves',
                    'bracers': 'bracers',
                    'backpack': 'backpack'
                };
                
                const targetSlot = slotMapping[slotType];
                if (targetSlot) {
                    // Move item to equipment slot
                    const targetContainer = document.querySelector(`.slot[data-slot="${targetSlot}"]`);
                    if (targetContainer) {
                        // Remove from old parent
                        if (itemElement.parentElement) {
                            itemElement.parentElement.removeChild(itemElement);
                        }
                        
                        // Add to equipment slot
                        targetContainer.appendChild(itemElement);
                        
                        // Update styling for equipped item
                        itemElement.className = 'w-full h-full object-contain item-icon p-1';
                        
                        // Re-setup draggable and tooltip
                        setupDraggable(itemElement);
                        setupTooltip(itemElement);
                        
                        // Handle two-handed weapons
                        if (data.two_handed) {
                            handleTwoHandedWeapon(data.slot_name, data.icon);
                        }
                    }
                }
            } else {
                showToast(data.message || 'Impossible d\'équiper l\'objet', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur de communication avec le serveur', 'error');
        });
    }

    function handleTwoHandedWeapon(equippedSlot, iconPath) {
        // Determine the opposite slot
        const oppositeSlot = (equippedSlot === 'main_hand') ? 'off_hand' : 'main_hand';
        const oppositeContainer = document.querySelector(`.slot[data-slot="${oppositeSlot}"]`);
        
        if (oppositeContainer) {
            // Clear the opposite slot
            const existingItems = oppositeContainer.querySelectorAll('.item-icon, img');
            existingItems.forEach(item => item.remove());
            
            const existingBorders = oppositeContainer.querySelectorAll('.border-red-500');
            existingBorders.forEach(border => border.remove());
            
            // Add grayed-out image
            const grayedImg = document.createElement('img');
            grayedImg.src = '/assets/images/items/' + iconPath;
            grayedImg.className = 'w-full h-full object-contain p-1 opacity-30 pointer-events-none';
            grayedImg.style.filter = 'grayscale(100%)';
            
            // Add red border overlay
            const redBorder = document.createElement('div');
            redBorder.className = 'absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none';
            
            oppositeContainer.appendChild(grayedImg);
            oppositeContainer.appendChild(redBorder);
        }
    }

    function clearTwoHandedWeaponVisual(fromSlot) {
        // Determine the opposite slot
        const oppositeSlot = (fromSlot === 'main_hand') ? 'off_hand' : 'main_hand';
        const oppositeContainer = document.querySelector(`.slot[data-slot="${oppositeSlot}"]`);
        
        if (oppositeContainer) {
            // Remove grayed-out images (those without data-id, which are the visual indicators)
            const grayedImages = oppositeContainer.querySelectorAll('img.opacity-30');
            grayedImages.forEach(img => img.remove());
            
            // Remove red borders
            const redBorders = oppositeContainer.querySelectorAll('.border-red-500');
            redBorders.forEach(border => border.remove());
        }
    }

    // Drop Zones: Equipment Slots
    document.querySelectorAll('.slot[data-slot]').forEach(slot => {
        slot.addEventListener('dragover', e => {
            e.preventDefault();
            if (!draggedItemData) return;
            
            // Check slot type compatibility
            const targetSlot = slot.dataset.slot;
            slot.classList.add('drag-over');
        });

        slot.addEventListener('dragleave', e => {
            slot.classList.remove('drag-over');
        });

        slot.addEventListener('drop', e => {
            e.preventDefault();
            slot.classList.remove('drag-over');
            if (!draggedItemData) return;

            moveItem(draggedItemData.id, 'equipped', slot.dataset.slot);
        });
    });

    // Drop Zones: Pockets
    document.querySelectorAll('.slot[data-location="pockets"]').forEach(slot => {
        slot.addEventListener('dragover', e => {
            e.preventDefault();
            slot.classList.add('drag-over');
        });

        slot.addEventListener('dragleave', e => {
            slot.classList.remove('drag-over');
        });

        slot.addEventListener('drop', e => {
            e.preventDefault();
            slot.classList.remove('drag-over');
            if (!draggedItemData) return;

            moveItem(draggedItemData.id, 'pockets', null, null, null, slot.dataset.index);
        });
    });

    // Drop Zone: Backpack Grid
    const grid = document.getElementById('backpack-grid');
    const CELL_SIZE = 40;

    grid.addEventListener('dragover', e => {
        e.preventDefault();
        if (!draggedItemData) return;

        const rect = grid.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
    });

    grid.addEventListener('drop', e => {
        e.preventDefault();
        if (!draggedItemData) return;

        const rect = grid.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const gridX = Math.floor(x / CELL_SIZE);
        const gridY = Math.floor(y / CELL_SIZE);

        moveItem(draggedItemData.id, 'backpack', null, gridX, gridY);
    });

    function moveItem(itemId, location, slot = null, x = null, y = null, pocketIndex = null) {
        const targetContainer = getTargetContainer(location, slot, pocketIndex);
        const itemElement = draggedItem; // Capture reference immediately
        
        fetch('/game/inventory/move', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                itemId: itemId,
                location: location,
                slot: slot || pocketIndex,
                x: x,
                y: y
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success: Update DOM
                if (itemElement) {
                    // Check if we're unequipping a two-handed weapon
                    const wasTwoHanded = itemElement.dataset.twoHanded === '1';
                    const fromSlot = itemElement.closest('.slot[data-slot]')?.dataset.slot;
                    
                    updateItemPosition(itemElement, location, slot, x, y, pocketIndex);
                    
                    // Clean up two-handed weapon visual if unequipping
</script>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Map Logic -->
<script src="/js/map.js"></script>
                    if (wasTwoHanded && fromSlot && (fromSlot === 'main_hand' || fromSlot === 'off_hand') && location !== 'equipped') {
                        clearTwoHandedWeaponVisual(fromSlot);
                    }
                }
                
                // Handle two-handed weapons for equipped items
                if (location === 'equipped' && data.two_handed && data.slot_name && data.icon) {
                    handleTwoHandedWeapon(data.slot_name, data.icon);
                }
            } else {
                // Error: Show Toast
                showToast(data.message || 'Impossible de déplacer l\'objet', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur de communication avec le serveur', 'error');
        });
    }

    function getTargetContainer(location, slot, pocketIndex) {
        if (location === 'equipped') {
            return document.querySelector(`.slot[data-slot="${slot}"]`);
        } else if (location === 'pockets') {
            return document.querySelector(`.slot[data-location="pockets"][data-index="${pocketIndex}"]`);
        } else if (location === 'backpack') {
            return document.getElementById('backpack-grid');
        }
        return null;
    }

    function updateItemPosition(itemElement, location, slot, x, y, pocketIndex) {
        const targetContainer = getTargetContainer(location, slot, pocketIndex);
        if (!targetContainer) return;

        // Remove from old parent
        if (itemElement.parentElement) {
            itemElement.parentElement.removeChild(itemElement);
        }

        targetContainer.appendChild(itemElement);

        // Reset styles
        itemElement.style.position = '';
        itemElement.style.left = '';
        itemElement.style.top = '';
        itemElement.style.width = '';
        itemElement.style.height = '';

        // Apply specific styles for Backpack Grid
        if (location === 'backpack') {
            itemElement.style.position = 'absolute';
            itemElement.style.left = (x * 40) + 'px';
            itemElement.style.top = (y * 40) + 'px';
            itemElement.style.width = (draggedItemData.width * 40) + 'px';
            itemElement.style.height = (draggedItemData.height * 40) + 'px';
        } 
        // Apply styles for Pockets
        else if(location === 'pockets') {
            itemElement.className = 'w-12 h-12 object-contain item-icon';
        } 
        // Apply styles for Equipped slots - fill the entire slot
        else if(location === 'equipped') {
            itemElement.className = 'w-full h-full object-contain item-icon p-1';
        }
    }

    // --- INVENTORY MODAL LOGIC ---
    const inventoryModal = document.getElementById('inventory-modal');
    const inventoryToggleBtn = document.getElementById('inventory-toggle');
    const inventoryBackdrop = document.getElementById('inventory-backdrop');

    function toggleInventory() {
        const isHidden = inventoryModal.classList.contains('hidden');
        if (isHidden) {
            inventoryModal.classList.remove('hidden');
        } else {
            inventoryModal.classList.add('hidden');
        }
    }

    inventoryToggleBtn.addEventListener('click', toggleInventory);
    inventoryBackdrop.addEventListener('click', toggleInventory);

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !inventoryModal.classList.contains('hidden')) {
            toggleInventory();
        }
    });

    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        // Colors based on type
        let bgClass = 'bg-gray-800';
        let borderClass = 'border-gray-600';
        let icon = '';

        if (type === 'error') {
            bgClass = 'bg-red-900/90';
            borderClass = 'border-red-700';
            icon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>`;
        } else if (type === 'success') {
            bgClass = 'bg-green-900/90';
            borderClass = 'border-green-700';
            icon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>`;
        }

        toast.className = `toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg backdrop-blur text-white min-w-[300px] ${bgClass} ${borderClass}`;
        toast.innerHTML = `
            ${icon}
            <span class="text-sm font-medium">${message}</span>
        `;

        container.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            toast.classList.add('hiding');
            toast.addEventListener('animationend', () => {
                toast.remove();
            });
        }, 3000);
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
