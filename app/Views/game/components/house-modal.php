<!-- House Modal -->
<div id="house-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="house-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-4 lg:p-8 pointer-events-none">
        <div class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex flex-col w-full max-w-6xl h-[90vh] pointer-events-auto transform transition-all scale-100 overflow-hidden">
            
            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b border-gray-600">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <div>
                        <h2 id="house-title" class="text-xl lg:text-2xl font-bold text-white">Ma Maison</h2>
                        <p id="house-location" class="text-sm text-gray-400">Aucune maison</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 bg-gray-900/50 px-3 py-1 rounded-lg border border-gray-700">
                        <span class="text-yellow-400">🪙</span>
                        <span id="house-player-gold" class="text-yellow-300 font-bold">0</span>
                    </div>
                    <button id="close-house-modal" class="text-gray-400 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

        <!-- Tab Navigation -->
            <div class="flex border-b border-gray-600 bg-gray-900/30">
                <button class="house-tab active px-6 py-3 text-sm font-medium transition-colors" data-tab="overview">
                    Vue d'ensemble
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors" data-tab="storage">
                    Coffre
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors" data-tab="furniture">
                    Meubles
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors" data-tab="shop">
                    Acheter
                </button>
            </div>

        <!-- Tab Content -->
            <div class="flex-1 overflow-y-auto p-4">
            
                <!-- Overview Tab -->
                <div id="house-tab-overview" class="house-tab-content">
                    <div id="no-house-message" class="hidden text-center py-12">
                        <span class="text-6xl mb-4 block">🏚️</span>
                        <h3 class="text-xl text-gray-300 mb-2">Vous n'avez pas encore de maison</h3>
                        <p class="text-gray-500 mb-6">Achetez votre première maison pour stocker vos objets et profiter de bonus !</p>
                        <button id="go-to-shop-btn" class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            🛒 Voir les maisons disponibles
                        </button>
                    </div>
                
                    <div id="house-overview" class="hidden">
                        <!-- House Image & Info -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                                <div class="aspect-video bg-gray-800 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                                    <img id="house-image" src="" alt="Maison" class="w-full h-full object-cover hidden">
                                    <span id="house-image-placeholder" class="text-8xl">🏠</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <input type="text" id="house-custom-name" placeholder="Nom personnalisé..." 
                                               class="bg-transparent text-lg font-bold text-white border-b border-transparent hover:border-gray-600 focus:border-violet-500 focus:outline-none transition-colors">
                                    </div>
                                    <button id="rename-house-btn" class="text-violet-400 hover:text-violet-300 text-sm">
                                        Renommer
                                    </button>
                                </div>
                            </div>
                        
                            <!-- Bonuses -->
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2 border-b border-gray-600 pb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Bonus Actifs
                                </h3>
                                <div id="house-bonuses" class="grid grid-cols-2 gap-3">
                                    <!-- Bonus items will be inserted here -->
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700 text-center">
                                <div class="text-2xl font-bold text-white">
                                    <span id="storage-used">0</span>/<span id="storage-capacity">0</span>
                                </div>
                                <div class="text-sm text-gray-400">Stockage</div>
                            </div>
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700 text-center">
                                <div class="text-2xl font-bold text-white">
                                    <span id="furniture-count">0</span>/<span id="furniture-slots">0</span>
                                </div>
                            <div class="text-sm text-gray-400">Meubles</div>
                            </div>
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700 text-center">
                                <div id="luck-bonus" class="text-2xl font-bold text-green-400">+0%</div>
                                <div class="text-sm text-gray-400">Chance</div>
                            </div>
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700 text-center">
                                <div id="xp-bonus" class="text-2xl font-bold text-blue-400">+0%</div>
                                <div class="text-sm text-gray-400">XP Bonus</div>
                            </div>
                        </div>

                        <!-- Owned Houses -->
                        <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                            <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-600 pb-2">Vos Propriétés</h3>
                            <div id="owned-houses-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Houses will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Storage Tab -->
                <div id="house-tab-storage" class="house-tab-content hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- House Storage -->
                        <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-600 pb-2">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Coffre de la Maison
                                </h3>
                                <span class="text-sm text-gray-400">
                                    <span id="storage-count-display">0</span>/<span id="storage-max-display">0</span> slots
                                </span>
                            </div>
                            <div id="house-storage-grid" class="grid grid-cols-5 gap-2 min-h-[300px]">
                                <!-- Storage items will be inserted here -->
                            </div>
                            <p class="drag-hint mt-2">⬇️ Glissez un objet ici pour le déposer</p>
                        </div>

                        <!-- Player Inventory -->
                        <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-600 pb-2">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Votre Inventaire
                                </h3>
                                <div class="flex gap-2">
                                    <button id="deposit-all-btn" class="text-xs bg-violet-600 hover:bg-violet-700 text-white px-3 py-1 rounded transition-colors">
                                        Tout déposer
                                    </button>
                                </div>
                            </div>
                            <div id="house-inventory-grid" class="grid grid-cols-5 gap-2 min-h-[300px]">
                                <!-- Inventory items will be inserted here -->
                            </div>
                            <p class="drag-hint mt-2">⬆️ Glissez un objet ici pour le récupérer</p>
                        </div>
                    </div>
                
                    <!-- Transfer Instructions -->
                    <div class="mt-4 bg-gray-900/30 rounded-xl p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 text-center">
                            💡 <strong>Astuce :</strong> Glissez-déposez ou cliquez sur un objet pour le transférer
                        </p>
                    </div>
                </div>

            <!-- Furniture Tab -->
                <div id="house-tab-furniture" class="house-tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="placed-furniture-grid">
                        <!-- Placed furniture will be inserted here -->
                    </div>
                    <div id="no-furniture-message" class="hidden text-center py-12">
                        <span class="text-6xl mb-4 block">🪑</span>
                        <h3 class="text-xl text-gray-300 mb-2">Aucun meuble installé</h3>
                        <p class="text-gray-500">Achetez des meubles pour décorer votre maison et obtenir des bonus !</p>
                    </div>
                </div>

                <!-- Shop Tab -->
                <div id="house-tab-shop" class="house-tab-content hidden">
                    <!-- Sub-tabs for Houses and Furniture -->
                    <div class="flex gap-4 mb-6">
                        <button class="shop-subtab active px-4 py-2 rounded-lg font-medium transition-colors" data-subtab="houses">
                            Maisons
                        </button>
                        <button class="shop-subtab px-4 py-2 rounded-lg font-medium transition-colors" data-subtab="furniture-shop">
                            Meubles
                        </button>
                    </div>

                    <!-- Houses Shop -->
                    <div id="shop-houses" class="shop-subtab-content">
                        <div id="houses-shop-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Houses will be inserted here -->
                        </div>
                    </div>

                    <!-- Furniture Shop -->
                    <div id="shop-furniture" class="shop-subtab-content hidden">
                        <!-- Category Filter -->
                        <div class="flex gap-2 mb-4 flex-wrap" id="furniture-category-filter">
                            <button class="furniture-cat-btn active px-3 py-1 rounded-lg text-sm transition-colors" data-category="all">
                                Tous
                            </button>
                            <!-- Categories will be inserted dynamically -->
                        </div>
                        <div id="furniture-shop-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Furniture will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* House Modal Styles */
#house-modal .house-tab {
    color: #9ca3af;
    border-bottom: 2px solid transparent;
}

#house-modal .house-tab:hover {
    color: #a78bfa;
    background-color: rgba(139, 92, 246, 0.1);
}

#house-modal .house-tab.active {
    color: #a78bfa;
    border-bottom-color: #8b5cf6;
    background-color: rgba(139, 92, 246, 0.1);
}

#house-modal .shop-subtab {
    background-color: #374151;
    color: #9ca3af;
}

#house-modal .shop-subtab:hover {
    background-color: #4b5563;
    color: white;
}

#house-modal .shop-subtab.active {
    background-color: #7c3aed;
    color: white;
}

#house-modal .furniture-cat-btn {
    background-color: #374151;
    color: #9ca3af;
}

#house-modal .furniture-cat-btn:hover {
    background-color: #4b5563;
    color: white;
}

#house-modal .furniture-cat-btn.active {
    background-color: #7c3aed;
    color: white;
}

/* Storage Item Styles */
.storage-item, .inventory-transfer-item {
    width: 60px;
    height: 60px;
    background-color: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.storage-item:hover, .inventory-transfer-item:hover {
    border-color: #8b5cf6;
    transform: scale(1.05);
}

.storage-item img, .inventory-transfer-item img {
    width: 48px;
    height: 48px;
    object-fit: contain;
}

.storage-item .quantity-badge, .inventory-transfer-item .quantity-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 10px;
    padding: 1px 4px;
    border-radius: 4px;
}

/* Rarity Colors */
.rarity-common { border-color: #9ca3af !important; }
.rarity-uncommon { border-color: #22c55e !important; }
.rarity-rare { border-color: #3b82f6 !important; }
.rarity-epic { border-color: #a855f7 !important; }
.rarity-legendary { border-color: #f59e0b !important; box-shadow: 0 0 10px rgba(245, 158, 11, 0.3); }

/* Furniture Card */
.furniture-card {
    background-color: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    border-radius: 12px;
    padding: 16px;
    transition: all 0.2s;
}

.furniture-card:hover {
    border-color: #8b5cf6;
}

/* House Card */
.house-card {
    background-color: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
}

.house-card:hover {
    border-color: #8b5cf6;
    transform: translateY(-2px);
}

.house-card.owned {
    border-color: #22c55e;
    background-color: rgba(34, 197, 94, 0.1);
}

.house-card.primary {
    border-color: #8b5cf6;
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.3);
}

/* Bonus Item */
.bonus-item {
    background-color: rgba(17, 24, 39, 0.5);
    border: 1px solid #374151;
    border-radius: 8px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.bonus-item .bonus-icon {
    font-size: 24px;
}

.bonus-item .bonus-value {
    font-weight: bold;
}

/* Empty Slot */
.empty-slot {
    width: 60px;
    height: 60px;
    background-color: rgba(17, 24, 39, 0.5);
    border: 2px dashed #374151;
    border-radius: 8px;
}

/* Drag & Drop Styles */
.storage-item,
.inventory-transfer-item {
    cursor: grab;
    transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
}

.storage-item:active,
.inventory-transfer-item:active {
    cursor: grabbing;
}

.storage-item.dragging,
.inventory-transfer-item.dragging {
    opacity: 0.5;
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
}

#house-storage-grid.drag-over,
#house-inventory-grid.drag-over {
    background-color: rgba(139, 92, 246, 0.1);
    border: 2px dashed #8b5cf6;
    border-radius: 8px;
}

#house-storage-grid {
    transition: background-color 0.2s, border 0.2s;
    border: 2px solid transparent;
    padding: 8px;
    min-height: 200px;
}

#house-inventory-grid {
    transition: background-color 0.2s, border 0.2s;
    border: 2px solid transparent;
    padding: 8px;
    min-height: 200px;
}

/* Hover effects */
.storage-item:hover,
.inventory-transfer-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

/* Instructions text */
.drag-hint {
    text-align: center;
    color: #6b7280;
    font-size: 12px;
    margin-top: 8px;
}

/* Valid drop zone highlight */
.valid-drop-zone {
    border-color: #10b981 !important;
    background-color: rgba(16, 185, 129, 0.1);
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
}
</style>
