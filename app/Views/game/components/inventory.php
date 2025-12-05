<style>
/* Inventory Slot Styles */
.slot {
    background-color: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
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
</style>

<!-- Inventory Modal -->
<div id="inventory-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="inventory-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-8 pointer-events-none">
        <div class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex gap-8 p-8 max-w-6xl w-full pointer-events-auto transform transition-all scale-100">
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
                        <!-- Head -->
                        <div class="absolute top-4 left-1/2 transform -translate-x-1/2 w-16 h-16 slot rounded-lg" data-slot="head">
                            <span class="slot-label">Tête</span>
                            <?php if(isset($inventory['equipped']['head'])): $item = $inventory['equipped']['head']; ?>
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $item['icon'] ?>" 
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
                        
                        <!-- Main Hand -->
                        <div class="absolute top-[360px] left-8 w-16 h-32 slot rounded-lg" data-slot="main_hand">
                            <span class="slot-label">Main</span>
                            <?php if(isset($inventory['equipped']['main_hand'])): $item = $inventory['equipped']['main_hand']; ?>
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $inventory['equipped']['off_hand']['icon'] ?>" 
                                     class="w-full h-full object-contain p-1 opacity-30 pointer-events-none"
                                     style="filter: grayscale(100%);">
                                <div class="absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none"></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Off Hand -->
                        <div class="absolute top-[360px] right-8 w-16 h-32 slot rounded-lg" data-slot="off_hand">
                            <span class="slot-label">Off</span>
                            <?php if(isset($inventory['equipped']['off_hand'])): $item = $inventory['equipped']['off_hand']; ?>
                                <img src="/<?= $item['icon'] ?>" 
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
                                <img src="/<?= $inventory['equipped']['main_hand']['icon'] ?>" 
                                     class="w-full h-full object-contain p-1 opacity-30 pointer-events-none"
                                     style="filter: grayscale(100%);">
                                <div class="absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none"></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Backpack Slot (Special) -->
                        <div class="absolute bottom-4 right-4 w-12 h-12 slot rounded-lg border-yellow-500/50" data-slot="backpack">
                            <span class="slot-label">Sac</span>
                            <?php if(isset($inventory['equipped']['backpack'])): $item = $inventory['equipped']['backpack']; ?>
                                <img src="/<?= $item['icon'] ?>" 
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
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Inventaire
                    </h2>
                </div>
                
                <!-- Weight Indicator -->
                <div class="mb-4 bg-gray-900/50 p-3 rounded-xl border border-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-400">Poids</span>
                        <span class="text-sm font-bold text-white">
                            <span id="current-weight"><?= number_format($inventory['current_weight'] ?? 0, 1) ?></span> / 
                            <span id="max-weight"><?= number_format($inventory['max_weight'] ?? 60, 1) ?></span> kg
                        </span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
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
                <div id="inventory-grid" class="flex-1 flex flex-col bg-gray-900/50 p-4 rounded-xl border border-gray-700 overflow-hidden">
                    <h3 class="text-sm font-medium text-gray-400 mb-3 uppercase tracking-wider">Contenu</h3>
                    
                    <div class="flex-1 overflow-auto">
                        <div id="inventory-container" class="grid grid-cols-6 gap-2">
                            <?php if(isset($inventory['inventory']) && !empty($inventory['inventory'])): ?>
                                <?php foreach($inventory['inventory'] as $item): ?>
                                    <div class="w-16 h-16 bg-gray-800/80 border-2 border-gray-600 relative flex items-center justify-center transition-all duration-200 hover:border-gray-500 hover:bg-gray-700/90 [&.drag-over]:border-violet-500 [&.drag-over]:bg-violet-500/20 rounded-lg flex items-center justify-center relative bg-gray-800 hover:bg-gray-700 transition-colors" 
                                         data-location="inventory" 
                                         data-inventory-id="<?= $item['id'] ?>">
                                        <img src="/<?= $item['icon'] ?>" 
                                             class="w-12 h-12 object-contain cursor-grab active:cursor-grabbing" 
                                             draggable="true" 
                                             data-id="<?= $item['id'] ?>"
                                             data-slot-type="<?= htmlspecialchars($item['item_slot_type']) ?>"
                                             data-name="<?= htmlspecialchars($item['name']) ?>"
                                             data-type="<?= htmlspecialchars($item['type']) ?>"
                                             data-description="<?= htmlspecialchars($item['description']) ?>"
                                             data-stats='<?= htmlspecialchars($item['stats']) ?>'
                                             data-weight="<?= $item['weight'] ?>">
                                        <?php if(isset($item['quantity']) && $item['quantity'] > 1): ?>
                                            <span class="absolute bottom-0 right-1 text-xs font-bold text-white drop-shadow-md"><?= $item['quantity'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-6 text-center text-gray-500 italic py-8">
                                    Inventaire vide
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tooltip Container -->
<div id="item-tooltip" class="fixed hidden bg-gray-900 border border-gray-600 p-3 rounded shadow-xl max-w-xs text-sm text-gray-300">
    <div class="tooltip-header">
        <div id="tooltip-name" class="font-bold text-violet-400 text-base"></div>
        <div id="tooltip-type" class="text-xs text-gray-500 uppercase"></div>
    </div>
    <div id="tooltip-stats" class="space-y-1 mb-2"></div>
    <div id="tooltip-desc" class="italic text-gray-400 text-xs border-t border-gray-700 pt-2"></div>
</div>
