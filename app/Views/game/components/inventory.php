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
                                    <img src="/<?= $item['icon'] ?>" 
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
                                    <img src="/<?= $item['icon'] ?>" class="max-w-full max-h-full object-contain">
                                </div>
                            <?php endforeach; ?>
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
