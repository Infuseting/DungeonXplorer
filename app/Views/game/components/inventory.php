<style>
/* Inventory Slot Styles */
.slot {
    background-color: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    transition: all 0.2s;
}

<?php
/**
 * Helper function to render an equipped item with enchantment support
 */
function renderEquippedItem($item, $isTwoHanded = null) {
    if (!$item) return '';
    
    $enchantments = $item['enchantments'] ?? [];
    $enchantCount = count($enchantments);
    $isEnchanted = $enchantCount > 0;
    $enchantedClass = $isEnchanted ? 'enchanted-item' : '';
    
    // Ensure stats is properly formatted JSON
    $statsJson = $item['stats'];
    if (is_array($statsJson)) {
        $statsJson = json_encode($statsJson);
    } elseif (empty($statsJson)) {
        $statsJson = '{}';
    }
    // Only escape single quotes for use in single-quoted attribute
    $statsJson = str_replace("'", "&#39;", $statsJson);
    
    $enchantmentsJson = json_encode($enchantments);
    $enchantmentsJson = str_replace("'", "&#39;", $enchantmentsJson);
    
    // Handle two-handed attribute
    $twoHandedAttr = '';
    if ($isTwoHanded !== null) {
        $twoHandedAttr = 'data-two-handed="' . ($item['two_handed'] ? '1' : '0') . '"';
    }
    
    $html = '<div class="relative w-full h-full ' . $enchantedClass . '">';
    $html .= '<img src="/' . $item['icon'] . '" 
                 class="w-full h-full object-contain item-icon p-1 relative z-[2]" 
                 draggable="true" 
                 data-id="' . $item['id'] . '"
                 data-slot-type="' . htmlspecialchars($item['item_slot_type']) . '"
                 ' . $twoHandedAttr . '
                 data-name="' . htmlspecialchars($item['name']) . '"
                 data-type="' . htmlspecialchars($item['type']) . '"
                 data-description="' . htmlspecialchars($item['description']) . '"
                 data-stats=\'' . $statsJson . '\'
                 data-enchantments=\'' . $enchantmentsJson . '\'>';
    
    if ($isEnchanted) {
        $html .= '<span class="enchant-badge">' . $enchantCount . '</span>';
    }
    
    $html .= '</div>';
    return $html;
}
?>

.slot:hover {
    border-color: #8b5cf6;
}

.slot.drag-over {
    background-color: rgba(139, 92, 246, 0.2);
    border-color: #8b5cf6;
}

/* Item Icon Styles - Desktop */
@media (min-width: 1024px) {
    .item-icon {
        cursor: grab;
    }
    
    .item-icon:active {
        cursor: grabbing;
    }
}

/* Item Icon Styles - Mobile */
@media (max-width: 1023px) {
    .item-icon {
        cursor: pointer;
    }
    
    .item-icon.selected {
        border: 3px solid #8b5cf6 !important;
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.6);
        transform: scale(1.05);
    }
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

/* Valid drop zone highlight */
.valid-drop-zone {
    border-color: #10b981 !important;
    background-color: rgba(16, 185, 129, 0.1);
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
}

/* ==========================================
   MINECRAFT-STYLE ENCHANTED ITEM EFFECT
   ========================================== */

/* Enchanted item shimmer effect */
.enchanted-item {
    position: relative;
    overflow: hidden;
}

.enchanted-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent 0%,
        transparent 40%,
        rgba(138, 43, 226, 0.3) 45%,
        rgba(75, 0, 130, 0.5) 50%,
        rgba(138, 43, 226, 0.3) 55%,
        transparent 60%,
        transparent 100%
    );
    animation: enchantShimmer 3s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
}

.enchanted-item::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(138, 43, 226, 0.1) 0%,
        transparent 50%,
        rgba(75, 0, 130, 0.15) 100%
    );
    pointer-events: none;
    z-index: 0;
}

@keyframes enchantShimmer {
    0% {
        transform: translateX(-50%) skewX(-15deg);
    }
    100% {
        transform: translateX(100%) skewX(-15deg);
    }
}

/* Enchantment count badge */
.enchant-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    font-size: 10px;
    font-weight: bold;
    min-width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 6px rgba(139, 92, 246, 0.6);
    z-index: 10;
}
</style>

<!-- Inventory Modal -->
<div id="inventory-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="inventory-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-4 lg:p-8 pointer-events-none">
        <div class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex flex-col lg:flex-row gap-4 lg:gap-8 p-4 lg:p-8 max-w-6xl w-full h-[90vh] lg:h-auto pointer-events-auto transform transition-all scale-100 overflow-hidden">
            <div class="w-full lg:w-1/2 flex flex-col min-h-0">
                <h2 class="text-xl lg:text-2xl font-bold text-white mb-4 lg:mb-6 border-b border-gray-600 pb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 lg:h-6 lg:w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Équipement
                </h2>
                
                <div class="flex-1 relative flex items-center justify-center bg-gray-900/50 rounded-xl border border-gray-700 p-2 lg:p-4 overflow-y-auto min-h-0">
                    <!-- Silhouette / Paper Doll -->
                    <div class="relative w-[240px] h-[350px] lg:w-[400px] lg:h-[600px] flex-shrink-0">
                        <!-- Head -->
                        <div class="absolute top-2 lg:top-4 left-1/2 transform -translate-x-1/2 w-12 h-12 lg:w-16 lg:h-16 slot rounded-lg" data-slot="head">
                            <span class="slot-label">Tête</span>
                            <?php if(isset($inventory['equipped']['head'])): echo renderEquippedItem($inventory['equipped']['head']); endif; ?>
                        </div>
                        
                        <!-- Shoulders -->
                        <div class="absolute top-10 lg:top-16 left-4 lg:left-8 w-12 h-12 lg:w-16 lg:h-16 slot rounded-lg" data-slot="shoulders">
                            <span class="slot-label">Épaules</span>
                            <?php if(isset($inventory['equipped']['shoulders'])): echo renderEquippedItem($inventory['equipped']['shoulders']); endif; ?>
                        </div>
                        
                        <!-- Amulet -->
                        <div class="absolute top-10 lg:top-16 right-4 lg:right-8 transform w-12 h-12 lg:w-16 lg:h-16 slot rounded-lg" data-slot="amulet">
                            <span class="slot-label">Amulette</span>
                            <?php if(isset($inventory['equipped']['amulet'])): echo renderEquippedItem($inventory['equipped']['amulet']); endif; ?>
                        </div>
                        
                        <!-- Chest -->
                        <div class="absolute top-20 lg:top-36 left-1/2 transform -translate-x-1/2 w-16 h-20 lg:w-20 lg:h-24 slot rounded-lg" data-slot="chest">
                            <span class="slot-label">Torse</span>
                            <?php if(isset($inventory['equipped']['chest'])): echo renderEquippedItem($inventory['equipped']['chest']); endif; ?>
                        </div>
                        
                        <!-- Gloves -->
                        <div class="absolute top-24 lg:top-40 left-4 lg:left-8 w-12 h-24 lg:w-16 lg:h-32 slot rounded-lg" data-slot="gloves">
                            <span class="slot-label">Gants</span>
                            <?php if(isset($inventory['equipped']['gloves'])): echo renderEquippedItem($inventory['equipped']['gloves']); endif; ?>
                        </div>
                        
                        <!-- Bracers -->
                        <div class="absolute top-24 lg:top-40 right-4 lg:right-8 w-12 h-24 lg:w-16 lg:h-32 slot rounded-lg" data-slot="bracers">
                            <span class="slot-label">Bracelets</span>
                            <?php if(isset($inventory['equipped']['bracers'])): echo renderEquippedItem($inventory['equipped']['bracers']); endif; ?>
                        </div>
                        
                        <!-- Belt -->
                        <div class="absolute top-[165px] lg:top-[260px] left-1/2 transform -translate-x-1/2 w-16 h-8 lg:w-20 lg:h-10 slot rounded-lg" data-slot="belt">
                            <span class="slot-label">Ceinture</span>
                            <?php if(isset($inventory['equipped']['belt'])): echo renderEquippedItem($inventory['equipped']['belt']); endif; ?>
                        </div>
                        
                        <!-- Legs -->
                        <div class="absolute top-[200px] lg:top-[310px] left-1/2 w-16 h-20 lg:w-20 transform -translate-x-1/2 lg:h-24 slot rounded-lg" data-slot="legs">
                            <span class="slot-label">Jambes</span>
                            <?php if(isset($inventory['equipped']['legs'])): echo renderEquippedItem($inventory['equipped']['legs']); endif; ?>
                        </div>
                        
                        <!-- Boots -->
                        <div class="absolute top-[290px] lg:top-[420px] left-1/2 w-16 h-12 lg:w-20 transform -translate-x-1/2 lg:h-16 slot rounded-lg" data-slot="boots">
                            <span class="slot-label">Bottes</span>
                            <?php if(isset($inventory['equipped']['boots'])): echo renderEquippedItem($inventory['equipped']['boots']); endif; ?>
                        </div>
                        
                        <!-- Rings -->
                        <div class="absolute top-[200px] lg:top-[310px] left-6 lg:left-8 w-8 h-8 lg:w-10 lg:h-10 slot rounded-full" data-slot="ring_1">
                            <span class="slot-label">Ring1</span>
                            <?php if(isset($inventory['equipped']['ring_1'])): echo renderEquippedItem($inventory['equipped']['ring_1']); endif; ?>
                        </div>
                        <div class="absolute top-[200px] lg:top-[310px] right-6 lg:right-8 w-8 h-8 lg:w-10 lg:h-10 slot rounded-full" data-slot="ring_2">
                            <span class="slot-label">Ring2</span>
                            <?php if(isset($inventory['equipped']['ring_2'])): echo renderEquippedItem($inventory['equipped']['ring_2']); endif; ?>
                        </div>
                        
                        <!-- Main Hand -->
                        <div class="absolute top-[240px] lg:top-[360px] left-4 lg:left-8 w-12 h-24 lg:w-16 lg:h-32 slot rounded-lg" data-slot="main_hand">
                            <span class="slot-label">Main</span>
                            <?php if(isset($inventory['equipped']['main_hand'])): echo renderEquippedItem($inventory['equipped']['main_hand'], true); 
                            elseif(isset($inventory['equipped']['off_hand']) && $inventory['equipped']['off_hand']['two_handed']): ?>
                                <!-- Show grayed-out image if off_hand has two-handed weapon -->
                                <img src="/<?= $inventory['equipped']['off_hand']['icon'] ?>" 
                                     class="w-full h-full object-contain p-1 opacity-30 pointer-events-none"
                                     style="filter: grayscale(100%);">
                                <div class="absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none"></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Off Hand -->
                        <div class="absolute top-[240px] lg:top-[360px] right-4 lg:right-8 w-12 h-24 lg:w-16 lg:h-32 slot rounded-lg" data-slot="off_hand">
                            <span class="slot-label">Off</span>
                            <?php if(isset($inventory['equipped']['off_hand'])): echo renderEquippedItem($inventory['equipped']['off_hand'], true);
                            elseif(isset($inventory['equipped']['main_hand']) && $inventory['equipped']['main_hand']['two_handed']): ?>
                                <!-- Show grayed-out image if main_hand has two-handed weapon -->
                                <img src="/<?= $inventory['equipped']['main_hand']['icon'] ?>" 
                                     class="w-full h-full object-contain p-1 opacity-30 pointer-events-none"
                                     style="filter: grayscale(100%);">
                                <div class="absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none"></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Backpack Slot (Special) -->
                        <div class="absolute bottom-4 right-[-30px] w-10 h-10 lg:w-12 lg:h-12 slot rounded-lg border-yellow-500/50" data-slot="backpack">
                            <span class="slot-label">Sac</span>
                            <?php if(isset($inventory['equipped']['backpack'])): echo renderEquippedItem($inventory['equipped']['backpack']); endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Inventory -->
            <div class="w-full lg:w-1/2 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-3 lg:mb-4">
                    <h2 class="text-xl lg:text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 lg:h-6 lg:w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Inventaire
                    </h2>
                    <button id="inventory-close-btn" class="lg:hidden text-gray-400 hover:text-white text-2xl">&times;</button>
                </div>
                
                <!-- Weight Indicator -->
                <div class="mb-3 lg:mb-4 bg-gray-900/50 p-2 lg:p-3 rounded-xl border border-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs lg:text-sm font-medium text-gray-400">Poids</span>
                        <span class="text-xs lg:text-sm font-bold text-white">
                            <span id="current-weight"><?= number_format($inventory['current_weight'] ?? 0, 1) ?></span> / 
                            <span id="max-weight"><?= number_format($inventory['max_weight'] ?? 60, 1) ?></span> kg
                        </span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2 lg:h-3 overflow-hidden">
                        <?php 
                            $currentWeight = $inventory['current_weight'] ?? 0;
                            $maxWeight = $inventory['max_weight'] ?? 60;
                            $percentage = $maxWeight > 0 ? min(($currentWeight / $maxWeight) * 100, 100) : 0;
                            $barColor = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500');
                        ?>
                        <div class="<?= $barColor ?> h-full transition-all duration-300 w-[<?= $percentage ?>%]"></div>
                    </div>
                </div>

                <!-- Inventory Grid -->
                <div id="inventory-grid" class="flex-1 flex flex-col bg-gray-900/50 p-2 lg:p-4 rounded-xl border border-gray-700 overflow-hidden min-h-0">
                    <div class="flex flex-col gap-2 mb-2 lg:mb-3">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xs lg:text-sm font-medium text-gray-400 uppercase tracking-wider">Contenu</h3>
                            
                            <!-- Sort Controls -->
                            <select id="sort-select" class="bg-gray-800 text-xs text-white border border-gray-600 rounded px-2 py-1 focus:outline-none focus:border-violet-500">
                                <option value="default">Tri: Défaut</option>
                                <option value="weight-desc">Poids (Lourd > Léger)</option>
                                <option value="weight-asc">Poids (Léger > Lourd)</option>
                                <option value="price-desc">Valeur (Chère > Moins)</option>
                                <option value="name-asc">Nom (A-Z)</option>
                            </select>
                        </div>

                        <!-- Filter Controls -->
                        <div class="flex gap-2 text-xs overflow-x-auto pb-1" id="filter-buttons">
                            <button class="filter-btn px-3 py-1 bg-violet-600 text-white rounded-full transition-colors whitespace-nowrap active" data-filter="all">Tout</button>
                            <button class="filter-btn px-3 py-1 bg-gray-700 text-gray-300 hover:text-white rounded-full transition-colors whitespace-nowrap" data-filter="weapon">Armes</button>
                            <button class="filter-btn px-3 py-1 bg-gray-700 text-gray-300 hover:text-white rounded-full transition-colors whitespace-nowrap" data-filter="armor">Armures</button>
                            <button class="filter-btn px-3 py-1 bg-gray-700 text-gray-300 hover:text-white rounded-full transition-colors whitespace-nowrap" data-filter="consumable">Conso</button>
                             <button class="filter-btn px-3 py-1 bg-gray-700 text-gray-300 hover:text-white rounded-full transition-colors whitespace-nowrap" data-filter="resource">Ressources</button>
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-auto">
                        <div id="inventory-container" class="grid grid-cols-4 lg:grid-cols-6 gap-2">
                            <?php if(isset($inventory['inventory']) && !empty($inventory['inventory'])): ?>
                                <?php foreach($inventory['inventory'] as $item): 
                                    $enchantments = $item['enchantments'] ?? [];
                                    $enchantCount = count($enchantments);
                                    $isEnchanted = $enchantCount > 0;
                                    $enchantedClass = $isEnchanted ? 'enchanted-item' : '';
                                    
                                    // Properly format JSON for HTML attributes
                                    $statsJson = $item['stats'];
                                    if (is_array($statsJson)) {
                                        $statsJson = json_encode($statsJson);
                                    } elseif (empty($statsJson)) {
                                        $statsJson = '{}';
                                    }
                                    $statsJson = str_replace("'", "&#39;", $statsJson);
                                    
                                    $enchantmentsJson = json_encode($enchantments);
                                    $enchantmentsJson = str_replace("'", "&#39;", $enchantmentsJson);
                                ?>
                                    <div class="inventory-item-slot w-16 h-16 bg-gray-800/80 border-2 border-gray-600 relative flex items-center justify-center transition-all duration-200 hover:border-gray-500 hover:bg-gray-700/90 [&.drag-over]:border-violet-500 [&.drag-over]:bg-violet-500/20 rounded-lg <?= $enchantedClass ?>" 
                                         data-location="inventory" 
                                         data-inventory-id="<?= $item['id'] ?>"
                                         data-price="<?= $item['price'] ?? 0 ?>"
                                         data-weight="<?= $item['weight'] ?? 0 ?>"
                                         data-name="<?= htmlspecialchars($item['name']) ?>"
                                         data-type="<?= htmlspecialchars($item['type']) ?>"
                                         data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                                         data-description="<?= htmlspecialchars($item['description']) ?>"
                                         data-stats='<?= $statsJson ?>'
                                         data-enchantments='<?= $enchantmentsJson ?>'
                                         data-id="<?= $item['id'] ?>">
                                        <img src="/<?= $item['icon'] ?>" 
                                             class="w-12 h-12 object-contain cursor-grab active:cursor-grabbing item-icon relative z-[2]" 
                                             draggable="true" 
                                             data-id="<?= $item['id'] ?>"
                                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                                             data-name="<?= htmlspecialchars($item['name']) ?>"
                                             data-type="<?= htmlspecialchars($item['type']) ?>"
                                             data-description="<?= htmlspecialchars($item['description']) ?>"
                                             data-stats='<?= $statsJson ?>'
                                             data-enchantments='<?= $enchantmentsJson ?>'
                                             data-weight="<?= $item['weight'] ?>">
                                        <?php if($isEnchanted): ?>
                                            <span class="enchant-badge"><?= $enchantCount ?></span>
                                        <?php endif; ?>
                                        <?php if(isset($item['quantity']) && $item['quantity'] > 1): ?>
                                            <span class="absolute bottom-0 right-1 text-xs font-bold text-white drop-shadow-md z-[3]"><?= $item['quantity'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-4 lg:col-span-6 text-center text-gray-500 italic py-8">
                                    Inventaire vide
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const container = document.getElementById('inventory-container');
                    const sortSelect = document.getElementById('sort-select');
                    const filterBtns = document.querySelectorAll('.filter-btn');
                    const tooltip = document.getElementById('item-tooltip');
                    let items = Array.from(document.querySelectorAll('.inventory-item-slot'));

                                        const paperDollSlots = document.querySelectorAll('.slot'); 
                    function showTooltip(e, itemElement) {
                        const stats = JSON.parse(itemElement.dataset.stats || '{}');
                        const name = itemElement.dataset.name;
                        const type = itemElement.dataset.type;
                        const desc = itemElement.dataset.description;
                        const slotType = itemElement.dataset.slotType;
                        const itemId = itemElement.dataset.id;
                        
                        console.log('Hover Item:', name, slotType, itemId);

                                                let equippedItem = null;
                        
                        let targetSlotName = null;
                        if (['head', 'shoulders', 'amulet', 'chest', 'legs', 'boots', 'gloves', 'bracers', 'belt'].includes(slotType)) {
                            targetSlotName = slotType;
                        } else if (slotType === 'ring') {
                                                        if (document.querySelector(`.slot[data-slot="ring_1"] .item-icon`)) {
                                targetSlotName = 'ring_1';
                                                                                                                                                                                                                                                                const r1 = document.querySelector(`.slot[data-slot="ring_1"] .item-icon`);
                                const r2 = document.querySelector(`.slot[data-slot="ring_2"] .item-icon`);
                                if (!r1 && r2) targetSlotName = 'ring_2';
                                else targetSlotName = 'ring_1';
                            } else {
                                targetSlotName = 'ring_1';
                            }
                        } else if (slotType === 'main_hand') {
                            targetSlotName = 'main_hand';
                        } else if (slotType === 'off_hand') {
                            targetSlotName = 'off_hand';
                        }
                        
                        console.log('Target Slot:', targetSlotName);

                        if (targetSlotName) {
                            const equippedSlot = document.querySelector(`.slot[data-slot="${targetSlotName}"] .item-icon`);
                            if (equippedSlot) {
                                                                                                                                if (equippedSlot.dataset.id !== itemId) {
                                     equippedItem = {
                                        name: equippedSlot.dataset.name,
                                        type: equippedSlot.dataset.type,
                                        stats: JSON.parse(equippedSlot.dataset.stats || '{}'),
                                        icon: equippedSlot.src
                                    };
                                }
                            }
                        }

                                                if (equippedItem) {
                            renderComparisonTooltip(name, type, stats, desc, equippedItem);
                        } else {
                            renderSingleTooltip(name, type, stats, desc);
                        }

                                                const rect = itemElement.getBoundingClientRect();
                        tooltip.style.left = `${rect.right + 10}px`;
                        tooltip.style.top = `${rect.top}px`;
                        
                                                tooltip.classList.remove('hidden');
                        const tooltipRect = tooltip.getBoundingClientRect();
                        if (tooltipRect.right > window.innerWidth) {
                            tooltip.style.left = `${rect.left - tooltipRect.width - 10}px`;
                        }
                        if (tooltipRect.bottom > window.innerHeight) {
                            tooltip.style.top = `${window.innerHeight - tooltipRect.height - 10}px`;
                        }
                    }

                    function renderSingleTooltip(name, type, stats, desc) {
                        tooltip.innerHTML = `
                            <div class="tooltip-header mb-2 border-b border-gray-700 pb-1">
                                <div class="font-bold text-violet-400 text-base">${name}</div>
                                <div class="text-xs text-gray-500 uppercase">${type}</div>
                            </div>
                            <div class="space-y-1 mb-2">
                                ${formatStats(stats)}
                            </div>
                            <div class="italic text-gray-400 text-xs border-t border-gray-700 pt-2">${desc || ''}</div>
                        `;
                        tooltip.className = "fixed bg-gray-900 border border-gray-600 p-3 rounded shadow-xl max-w-xs text-sm text-gray-300 z-50 pointer-events-none";
                    }

                    function renderComparisonTooltip(newName, newType, newStats, newDesc, equipped) {
                         tooltip.innerHTML = `
                            <div class="flex gap-4">
                                <!-- Equipped -->
                                <div class="w-1/2 border-r border-gray-700 pr-4 opacity-75">
                                    <div class="text-xs text-gray-500 uppercase mb-1">Actuellement équipé</div>
                                    <div class="font-bold text-gray-300 text-sm mb-1">${equipped.name}</div>
                                    <div class="space-y-1 text-xs">
                                        ${formatStats(equipped.stats)}
                                    </div>
                                </div>
                                <!-- New -->
                                <div class="w-1/2 pl-2">
                                    <div class="text-xs text-green-500 uppercase mb-1">Sélectionné</div>
                                    <div class="font-bold text-violet-400 text-sm mb-1">${newName}</div>
                                    <div class="space-y-1 text-xs">
                                        ${formatStatsWithDiff(newStats, equipped.stats)}
                                    </div>
                                    <div class="italic text-gray-500 text-[10px] mt-2 border-t border-gray-700 pt-1">${newDesc || ''}</div>
                                </div>
                            </div>
                        `;
                         tooltip.className = "fixed bg-gray-900 border border-gray-600 p-3 rounded shadow-xl w-[500px] text-sm text-gray-300 z-50 pointer-events-none";
                    }

                    function formatStats(stats) {
                        const enchantBonuses = stats.enchantment_bonuses || {};
                        return Object.entries(stats).map(([idx, val]) => {
                             if(idx === 'capacity' || idx === 'enchantment_bonuses' || idx === 'rarity') return ''; 
                             const label = idx.charAt(0).toUpperCase() + idx.slice(1).replace(/_/g, ' ');
                             const bonus = enchantBonuses[idx] || 0;
                             // Show total value, in green if enchanted
                             if (bonus > 0) {
                                 return `<div class="flex justify-between"><span>${label}</span><span class="text-green-400">+${val}</span></div>`;
                             }
                             return `<div class="flex justify-between"><span>${label}</span><span class="text-white">+${val}</span></div>`;
                        }).join('');
                    }

                    function formatStatsWithDiff(newStats, oldStats) {
                         const allKeys = new Set([...Object.keys(newStats), ...Object.keys(oldStats)]);
                         const newBonuses = newStats.enchantment_bonuses || {};
                         
                         return Array.from(allKeys).map(key => {
                             if(key === 'capacity' || key === 'enchantment_bonuses' || key === 'rarity') return '';
                             const newVal = newStats[key] || 0;
                             const oldVal = oldStats[key] || 0;
                             const diff = newVal - oldVal;
                             const label = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                             
                             // Show total value, in green if enchanted
                             const newBonus = newBonuses[key] || 0;
                             let valueDisplay = newBonus > 0 
                                 ? `<span class="text-green-400">+${newVal}</span>`
                                 : `<span class="text-white">+${newVal}</span>`;
                             
                             let diffHtml = '';
                             if (diff > 0) diffHtml = `<span class="text-green-400 text-[10px] ml-1">(+${diff})</span>`;
                             if (diff < 0) diffHtml = `<span class="text-red-400 text-[10px] ml-1">(${diff})</span>`;
                             
                             return `<div class="flex justify-between items-center">
                                        <span>${label}</span>
                                        <span>
                                            ${valueDisplay}
                                            ${diffHtml}
                                        </span>
                                     </div>`;
                         }).join('');
                    }

                                        const ctxMenu = document.getElementById('item-context-menu');
                    const btnEquip = document.getElementById('ctx-equip');
                    const btnUnequip = document.getElementById('ctx-unequip');
                    const btnDrop = document.getElementById('ctx-drop');
                    
                                        let btnConsume = document.getElementById('ctx-consume');
                    if (!btnConsume) {
                        btnConsume = document.createElement('button');
                        btnConsume.id = 'ctx-consume';
                        btnConsume.className = 'w-full text-left px-4 py-2 text-sm text-green-400 hover:bg-gray-700 hover:text-green-300 transition-colors flex items-center gap-2 hidden';
                        btnConsume.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Consommer
                        `;
                                                btnDrop.parentNode.insertBefore(btnConsume, btnDrop.parentNode.lastElementChild);                                                                         btnUnequip.after(btnConsume);
                    }

                    let currentCtxItem = null;

                    document.addEventListener('contextmenu', (e) => {
                                                const slot = e.target.closest('.inventory-item-slot') || e.target.closest('.slot');
                        const img = e.target.closest('.item-icon');
                        
                                                                        let target = null;

                        if (e.target.closest('.inventory-item-slot')) {
                            target = e.target.closest('.inventory-item-slot');
                        } else if (img) {
                             target = img;
                        }

                        if (target && target.dataset.id) {
                            e.preventDefault();
                            currentCtxItem = target;
                            showContextMenu(e.pageX, e.pageY, target);
                        } else {
                            hideContextMenu();
                        }
                    });

                                        document.addEventListener('click', (e) => {
                        if (!ctxMenu.contains(e.target)) hideContextMenu();
                    });

                    function showContextMenu(x, y, item) {
                        const isEquipped = item.closest('.slot') !== null;                         const type = item.dataset.type;
                        
                                                btnEquip.classList.add('hidden');
                        btnUnequip.classList.add('hidden');
                        btnConsume.classList.add('hidden');

                        if (type === 'consumable') {
                            btnConsume.classList.remove('hidden');
                        } else {
                            if (isEquipped) {
                                btnUnequip.classList.remove('hidden');
                            } else {
                                btnEquip.classList.remove('hidden');
                            }
                        }

                        ctxMenu.style.left = `${x}px`;
                        ctxMenu.style.top = `${y}px`;
                        ctxMenu.classList.remove('hidden');
                    }

                    function hideContextMenu() {
                        ctxMenu.classList.add('hidden');
                        currentCtxItem = null;
                    }

                                        btnEquip.addEventListener('click', () => {
                        if (!currentCtxItem) return;
                                                                                                                                                                                                equipItem(currentCtxItem.dataset.id);
                        hideContextMenu();
                    });
                    
                                        btnConsume.addEventListener('click', () => {
                        if (!currentCtxItem) return;
                        consumeItem(currentCtxItem.dataset.id);
                        hideContextMenu();
                    });

                    function consumeItem(itemId) {
                         fetch('/game/use-item', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ item_id: itemId })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                                                                                                                if (currentCtxItem) {
                                                                                                            currentCtxItem.remove(); 
                                }
                                                                alert(data.message);                                                                 if (data.new_stats) {
                                                                          location.reload(); 
                                }
                            } else {
                                alert(data.message);
                            }
                        });
                    }

                                        function equipItem(id) {
                                                                                                                                                                                                        fetch('/game/equip-item', { 
                             method: 'POST', 
                             headers: { 'Content-Type': 'application/json' },
                             body: JSON.stringify({ item_id: id })
                         }).then(() => location.reload());
                    }



                                        function filterItems(type) {
                        items.forEach(item => {
                            const itemType = item.dataset.type;
                            if (type === 'all' || itemType === type || (type === 'armor' && ['head', 'chest', 'legs', 'boots', 'gloves', 'shoulders'].includes(itemType))) {
                                item.classList.remove('hidden');
                            } else {
                                item.classList.add('hidden');
                            }
                        });
                    }

                    function sortItems(criteria) {
                        const sorted = items.sort((a, b) => {
                            switch(criteria) {
                                case 'weight-desc':
                                    return (parseFloat(b.dataset.weight) || 0) - (parseFloat(a.dataset.weight) || 0);
                                case 'weight-asc':
                                    return (parseFloat(a.dataset.weight) || 0) - (parseFloat(b.dataset.weight) || 0);
                                case 'price-desc':
                                    return (parseFloat(b.dataset.price) || 0) - (parseFloat(a.dataset.price) || 0);
                                case 'name-asc':
                                    return a.dataset.name.localeCompare(b.dataset.name);
                                default: 
                                    return 0; 
                            }
                        });
                        sorted.forEach(item => container.appendChild(item));
                                            }

                    sortSelect.addEventListener('change', (e) => sortItems(e.target.value));

                    filterBtns.forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            filterBtns.forEach(b => {
                                b.classList.remove('bg-violet-600', 'text-white', 'active');
                                b.classList.add('bg-gray-700', 'text-gray-300');
                            });
                            e.target.classList.remove('bg-gray-700', 'text-gray-300');
                            e.target.classList.add('bg-violet-600', 'text-white', 'active');
                            filterItems(e.target.dataset.filter);
                        });
                    });
                });
                </script>
            </div>
        </div>
    </div>
</div>

<!-- Tooltip Container -->
<div id="item-tooltip" class="fixed hidden bg-gray-900 border border-gray-600 p-3 rounded shadow-xl max-w-xs text-sm text-gray-300 z-[9999]">
    <div class="tooltip-header">
        <div id="tooltip-name" class="font-bold text-violet-400 text-base"></div>
        <div id="tooltip-type" class="text-xs text-gray-500 uppercase"></div>
    </div>
    <div id="tooltip-stats" class="space-y-1 mb-2"></div>
    <div id="tooltip-enchants" class="hidden border-t border-violet-700/50 pt-2 mt-2"></div>
    <div id="tooltip-desc" class="italic text-gray-400 text-xs border-t border-gray-700 pt-2"></div>
</div>

<!-- Context Menu -->
<div id="item-context-menu" class="fixed hidden z-50 bg-gray-800 border border-gray-600 rounded-lg shadow-xl w-48 py-1 overflow-hidden">
    <button id="ctx-equip" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        Équiper
    </button>
    <button id="ctx-unequip" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors flex items-center gap-2 hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        Déséquiper
    </button>
    <div class="h-px bg-gray-700 my-1"></div>
    <button id="ctx-drop" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-colors flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Jeter cet objet
    </button>
</div>
