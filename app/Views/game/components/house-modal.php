<!-- House Modal -->
<div id="house-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="house-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>

    <!-- Modal Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-4 lg:p-8 pointer-events-none">
        <div
            class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex flex-col w-full max-w-6xl h-[90vh] pointer-events-auto transform transition-all scale-100 overflow-hidden">

            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b border-gray-600">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-violet-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

        <!-- Tab Navigation -->
            <div class="flex border-b border-gray-600 bg-gray-900/30 overflow-x-auto">
                <button class="house-tab active px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap" data-tab="overview">
                    Vue d'ensemble
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap" data-tab="storage">
                    Coffre
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap" data-tab="workbench">
                    Établi
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap" data-tab="furniture">
                    Meubles
                </button>
                <button class="house-tab px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap" data-tab="shop">
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
                        <p class="text-gray-500 mb-6">Achetez votre première maison pour stocker vos objets et profiter
                            de bonus !</p>
                        <button id="go-to-shop-btn"
                            class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            🛒 Voir les maisons disponibles
                        </button>
                    </div>

                    <div id="house-overview" class="hidden">
                        <!-- House Image & Info -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                                <div class="aspect-[4/3] bg-gray-800 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
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
                                <h3
                                    class="text-lg font-bold text-white mb-4 flex items-center gap-2 border-b border-gray-600 pb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
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
                    </div>
                </div>

                <!-- Storage Tab -->
                <div id="house-tab-storage" class="house-tab-content hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- House Storage -->
                        <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-600 pb-2">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Coffre de la Maison
                                </h3>
                                <span class="text-sm text-gray-400">
                                    <span id="storage-count-display">0</span>/<span id="storage-max-display">0</span>
                                    slots
                                </span>
                            </div>
                            <div id="house-storage-grid" class="grid grid-cols-4 sm:grid-cols-5 gap-2 min-h-[300px]">
                                <!-- Storage items will be inserted here -->
                            </div>
                        </div>

                        <!-- Player Inventory -->
                        <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-600 pb-2">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Votre Inventaire
                                </h3>
                                <div class="flex gap-2">
                                    <button id="deposit-all-btn"
                                        class="text-xs bg-violet-600 hover:bg-violet-700 text-white px-3 py-1 rounded transition-colors">
                                        Tout déposer
                                    </button>
                                </div>
                            </div>
                            <div id="house-inventory-grid" class="grid grid-cols-4 sm:grid-cols-5 gap-2 min-h-[300px]">
                                <!-- Inventory items will be inserted here -->
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Instructions -->
                    <div class="mt-4 bg-gray-900/30 rounded-xl p-4 border border-gray-700">
                        <p class="text-sm text-gray-400 text-center">
                            💡 <strong>Astuce :</strong> Glissez-déposez ou cliquez sur un objet pour le transférer
                        </p>
                    </div>
                </div>

            <!-- Workbench Tab -->
                <div id="house-tab-workbench" class="house-tab-content hidden">
                    <!-- No House Message -->
                    <div id="workbench-no-house" class="hidden text-center py-12">
                        <span class="text-8xl mb-4 block">🏠</span>
                        <h3 class="text-2xl text-gray-300 mb-2">Pas de maison</h3>
                        <p class="text-gray-500 mb-4">Vous devez d'abord acheter une maison pour accéder à l'établi d'enchantement.</p>
                        <button id="workbench-go-to-shop-btn" class="px-6 py-3 bg-gradient-to-r from-amber-600 to-amber-700 text-white rounded-lg hover:from-amber-700 hover:to-amber-800 transition-all shadow-lg">
                            🛒 Acheter une maison
                        </button>
                    </div>
                    
                    <!-- Workbench Locked Message (Paywall) -->
                    <div id="workbench-locked" class="hidden text-center py-12">
                        <span class="text-8xl mb-4 block">🔮</span>
                        <h3 class="text-2xl text-gray-300 mb-2">Établi d'Enchantement</h3>
                        <p class="text-gray-500 mb-4">Déverrouillez l'établi pour enchanter vos équipements et les rendre plus puissants !</p>
                        <div class="bg-gray-900/50 rounded-xl p-6 border border-violet-700/50 inline-block max-w-md">
                            <p class="text-violet-400 font-bold text-xl mb-2">
                                <span class="text-yellow-400" id="workbench-price">5000</span> 🪙
                            </p>
                            <p class="text-gray-400 text-sm mb-4">Niveau requis: <span class="text-white font-bold" id="workbench-required-level">10</span></p>
                            <button id="purchase-workbench-btn" class="w-full px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-700 text-white rounded-lg hover:from-violet-700 hover:to-purple-800 transition-all shadow-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                                ✨ Acheter l'Établi
                            </button>
                            <p id="workbench-purchase-error" class="text-red-400 text-sm mt-2 hidden"></p>
                        </div>
                    </div>

                    <!-- Workbench Interface (Furnace Style) -->
                    <div id="workbench-interface" class="hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            <!-- Left: Item Selection -->
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    Inventaire
                                </h3>
                                <div id="workbench-items-grid" class="grid grid-cols-4 gap-2 max-h-[400px] overflow-y-auto">
                                    <!-- Enchantable items will be inserted here -->
                                </div>
                            </div>

                            <!-- Center: Enchantment Furnace -->
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-violet-700/50 relative">
                                <h3 class="text-lg font-bold text-white mb-4 text-center flex items-center justify-center gap-2">
                                    Établi d'Enchantement
                                </h3>
                                
                                <!-- Furnace Style Interface -->
                                <div class="flex flex-col items-center gap-6">
                                    <!-- Top Slot: Item to Enchant -->
                                    <div class="relative">
                                        <div id="workbench-item-slot" 
                                             class="w-20 h-20 bg-gray-800 border-2 border-dashed border-gray-600 rounded-lg flex items-center justify-center cursor-pointer hover:border-violet-500 transition-colors"
                                             title="Placez un item à enchanter">
                                            <span class="text-gray-600 text-3xl">⚔️</span>
                                        </div>
                                        <p class="text-xs text-gray-500 text-center mt-1">Item</p>
                                    </div>

                                    <!-- Arrow Down -->
                                    <div class="text-violet-400 text-2xl animate-pulse">⬇️</div>

                                    <!-- Bottom Slot: Enchantment -->
                                    <div class="relative">
                                        <div id="workbench-enchant-slot" 
                                             class="w-20 h-20 bg-gray-800 border-2 border-dashed border-gray-600 rounded-lg flex items-center justify-center cursor-pointer hover:border-violet-500 transition-colors"
                                             title="Placez un enchantement">
                                            <span class="text-gray-600 text-3xl">✨</span>
                                        </div>
                                        <p class="text-xs text-gray-500 text-center mt-1">Enchantement</p>
                                    </div>

                                    <!-- Result Preview -->
                                    <div id="workbench-result-preview" class="hidden w-full bg-gray-800/50 rounded-lg p-4 mt-4 border border-violet-500/30">
                                        <h4 class="text-sm text-violet-400 font-bold mb-2">Aperçu du résultat:</h4>
                                        <div id="workbench-result-stats" class="text-sm text-gray-300">
                                            <!-- Stats preview will be inserted here -->
                                        </div>
                                        <div class="mt-3 flex items-center justify-between">
                                            <span class="text-amber-400 font-bold">Coût: <span id="workbench-cost">0</span> 🪙</span>
                                            <button id="workbench-apply-btn" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                                Enchanter
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Item Info - Only show enchantments -->
                                <div id="workbench-item-info" class="hidden mt-4 p-3 bg-gray-800/50 rounded-lg border border-violet-700/30">
                                    <h4 class="text-sm text-violet-400 font-bold mb-2">Enchantements appliqués:</h4>
                                    <div id="workbench-item-enchants" class="space-y-1">
                                        <!-- Current enchantments list -->
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Enchantments List -->
                            <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    Enchantements
                                </h3>
                                <div id="workbench-enchantments-list" class="space-y-2 max-h-[400px] overflow-y-auto">
                                    <!-- Enchantments will be inserted here -->
                                </div>
                            </div>

                        </div>

                        <!-- Instructions -->
                        <div class="mt-4 bg-gray-900/30 rounded-xl p-4 border border-gray-700">
                            <p class="text-sm text-gray-400 text-center">
                                💡 <strong>Comment ça marche :</strong> Sélectionnez un item équipable, choisissez un enchantement compatible, puis cliquez sur "Enchanter" pour améliorer votre équipement de façon permanente.
                            </p>
                        </div>
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
                        <p class="text-gray-500">Achetez des meubles pour décorer votre maison et obtenir des bonus !
                        </p>
                    </div>
                </div>

                <!-- Shop Tab -->
                <div id="house-tab-shop" class="house-tab-content hidden">
                    <!-- Sub-tabs for Houses and Furniture -->
                    <div class="flex gap-4 mb-6">
                        <button class="shop-subtab active px-4 py-2 rounded-lg font-medium transition-colors"
                            data-subtab="houses">
                            Maisons
                        </button>
                        <button class="shop-subtab px-4 py-2 rounded-lg font-medium transition-colors"
                            data-subtab="furniture-shop">
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
                            <button class="furniture-cat-btn active px-3 py-1 rounded-lg text-sm transition-colors"
                                data-category="all">
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
    .storage-item,
    .inventory-transfer-item {
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

    .storage-item:hover,
    .inventory-transfer-item:hover {
        border-color: #8b5cf6;
        transform: scale(1.05);
    }

    .storage-item img,
    .inventory-transfer-item img {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .storage-item .quantity-badge,
    .inventory-transfer-item .quantity-badge {
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
    .rarity-common {
        border-color: #9ca3af !important;
    }

    .rarity-uncommon {
        border-color: #22c55e !important;
    }

    .rarity-rare {
        border-color: #3b82f6 !important;
    }

    .rarity-epic {
        border-color: #a855f7 !important;
    }

    .rarity-legendary {
        border-color: #f59e0b !important;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
    }

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

/* Valid drop zone highlight */
.valid-drop-zone {
    border-color: #10b981 !important;
    background-color: rgba(16, 185, 129, 0.1);
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
}

/* ==========================================
   WORKBENCH STYLES
   ========================================== */

/* Workbench Item */
.workbench-item {
    width: 50px;
    height: 50px;
    background-color: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    position: relative;
}

.workbench-item:hover {
    border-color: #8b5cf6;
    transform: scale(1.05);
}

.workbench-item.ring-2 {
    border-color: #7c3aed;
    box-shadow: 0 0 10px rgba(124, 58, 237, 0.5);
}

/* Enchantment Item */
.enchantment-item {
    transition: all 0.2s;
}

.enchantment-item.ring-2 {
    box-shadow: 0 0 15px rgba(124, 58, 237, 0.5);
}

/* Workbench Slots */
#workbench-item-slot,
#workbench-enchant-slot {
    transition: all 0.3s;
}

#workbench-item-slot:hover,
#workbench-enchant-slot:hover {
    border-color: #7c3aed !important;
    background-color: rgba(124, 58, 237, 0.1);
}

/* Result Preview Animation */
#workbench-result-preview {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enchantment apply button animation */
#workbench-apply-btn:not(:disabled):hover {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(124, 58, 237, 0.5);
}

#workbench-apply-btn:disabled {
    cursor: not-allowed;
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
