/**
 * House Module - Gestion de la maison du joueur
 * DungeonXplorer
 */

import { showToast } from './toast.js';
import { playSound } from './soundManager.js';

// State for context menu
let contextMenuTarget = null;

class HouseManager {
    constructor() {
        this.modal = document.getElementById('house-modal');
        this.currentHouse = null;
        this.playerGold = 0;
        this.playerLevel = 1;
        this.houses = [];
        this.furniture = [];
        this.categories = [];
        this.dropZonesInitialized = false;
        
        // Workbench state
        this.workbenchData = null;
        this.selectedWorkbenchItem = null;
        this.selectedEnchantment = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Close modal
        document.getElementById('close-house-modal')?.addEventListener('click', () => this.close());
        
        // Tab navigation
        document.querySelectorAll('.house-tab').forEach(tab => {
            tab.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Shop sub-tabs
        document.querySelectorAll('.shop-subtab').forEach(tab => {
            tab.addEventListener('click', (e) => this.switchShopSubtab(e.target.dataset.subtab));
        });

        // Go to shop button
        document.getElementById('go-to-shop-btn')?.addEventListener('click', () => {
            this.switchTab('shop');
        });

        // Workbench go to shop button
        document.getElementById('workbench-go-to-shop-btn')?.addEventListener('click', () => {
            this.switchTab('shop');
        });

        // Purchase workbench button
        document.getElementById('purchase-workbench-btn')?.addEventListener('click', () => this.purchaseWorkbench());

        // Rename house
        document.getElementById('rename-house-btn')?.addEventListener('click', () => this.renameHouse());

        // Deposit all
        document.getElementById('deposit-all-btn')?.addEventListener('click', () => this.depositAll());

        // Workbench apply button
        document.getElementById('workbench-apply-btn')?.addEventListener('click', () => this.applyEnchantment());

        // Click outside to close
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) this.close();
        });

        // ESC to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                this.close();
            }
        });
    }

    async open() {
        this.modal.classList.remove('hidden');
        await this.loadHouseData();
    }

    close() {
        this.modal.classList.add('hidden');
        // Reset drop zones flag so they can be re-initialized next time
        this.dropZonesInitialized = false;
        // Hide context menu if open
        const contextMenu = document.getElementById('house-item-context-menu');
        if (contextMenu) {
            contextMenu.classList.add('hidden');
        }
    }

    async loadHouseData() {
        try {
            const response = await fetch('/game/house');
            const data = await response.json();

            if (data.success) {
                this.playerGold = data.player_gold;
                this.playerLevel = data.player_level;
                this.houses = data.houses;
                this.currentHouse = data.current_house;

                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);

                if (data.has_house && this.currentHouse) {
                    this.renderOverview();
                    document.getElementById('no-house-message').classList.add('hidden');
                    document.getElementById('house-overview').classList.remove('hidden');
                } else {
                    document.getElementById('no-house-message').classList.remove('hidden');
                    document.getElementById('house-overview').classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error loading house data:', error);
            this.showNotification('Erreur lors du chargement', 'error');
        }
    }

    renderOverview() {
        const house = this.currentHouse.house;
        
        // Title and location
        document.getElementById('house-title').textContent = house.custom_name || house.name;
        document.getElementById('house-location').textContent = `${house.location_name || 'Emplacement inconnu'}`;

        // House image
        const img = document.getElementById('house-image');
        const placeholder = document.getElementById('house-image-placeholder');
        if (house.image) {
            img.src = '/' + house.image;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }

        // Custom name input
        document.getElementById('house-custom-name').value = house.custom_name || '';

        // Storage stats
        document.getElementById('storage-used').textContent = this.currentHouse.storage_used;
        document.getElementById('storage-capacity').textContent = this.currentHouse.storage_capacity;

        // Furniture stats
        document.getElementById('furniture-count').textContent = this.currentHouse.furniture.length;
        document.getElementById('furniture-slots').textContent = house.furniture_slots;

        // Bonuses
        const bonuses = this.currentHouse.bonuses;
        document.getElementById('luck-bonus').textContent = `+${bonuses.luck}%`;
        document.getElementById('xp-bonus').textContent = `+${bonuses.xp}%`;

        this.renderBonuses(bonuses);
    }

    renderBonuses(bonuses) {
        const container = document.getElementById('house-bonuses');
        const bonusTypes = {
            storage: { icon: '📦', label: 'Stockage', suffix: ' slots' },
            comfort: { icon: '🛋️', label: 'Confort', suffix: '' },
            luck: { icon: '🍀', label: 'Chance', suffix: '%' },
            xp: { icon: '⭐', label: 'XP', suffix: '%' },
            gold: { icon: '🪙', label: 'Or', suffix: '%' },
            defense: { icon: '🛡️', label: 'Défense', suffix: '' }
        };

        container.innerHTML = Object.entries(bonuses)
            .filter(([key, value]) => value > 0)
            .map(([key, value]) => {
                const type = bonusTypes[key] || { icon: '✨', label: key, suffix: '' };
                return `
                    <div class="bonus-item">
                        <span class="bonus-icon">${type.icon}</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="text-sm text-gray-400">${type.label}: </div>
                            <div class="bonus-value text-green-400">+${value}${type.suffix}</div>
                        </div>
                    </div>
                `;
            }).join('') || '<p class="text-gray-500 col-span-2 text-center">Aucun bonus actif. Achetez des meubles !</p>';
    }

    async switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.house-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.tab === tabName);
        });

        // Update tab content
        document.querySelectorAll('.house-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(`house-tab-${tabName}`)?.classList.remove('hidden');

        // Load tab-specific data
        switch (tabName) {
            case 'storage':
                await this.loadStorage();
                break;
            case 'furniture':
                this.renderPlacedFurniture();
                break;
            case 'shop':
                await this.loadShopData();
                break;
            case 'workbench':
                await this.loadWorkbench();
                break;
        }
    }

    switchShopSubtab(subtab) {
        document.querySelectorAll('.shop-subtab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.subtab === subtab);
        });

        document.querySelectorAll('.shop-subtab-content').forEach(content => {
            content.classList.add('hidden');
        });

        if (subtab === 'houses') {
            document.getElementById('shop-houses').classList.remove('hidden');
        } else {
            document.getElementById('shop-furniture').classList.remove('hidden');
        }
    }

    async loadStorage() {
        if (!this.currentHouse) {
            document.getElementById('house-storage-grid').innerHTML = '<p class="text-gray-500 col-span-5 text-center">Vous devez d\'abord acheter une maison</p>';
            return;
        }

        // Update storage count
        document.getElementById('storage-count-display').textContent = this.currentHouse.storage_used;
        document.getElementById('storage-max-display').textContent = this.currentHouse.storage_capacity;

        // Render storage items
        this.renderStorageGrid();

        // Load player inventory
        await this.loadPlayerInventory();
    }

    renderStorageGrid() {
        const container = document.getElementById('house-storage-grid');
        const storage = this.currentHouse.storage;
        const capacity = this.currentHouse.storage_capacity;

        let html = '';
        
        // Render stored items with full data attributes for tooltips
        storage.forEach(item => {
            const statsJson = typeof item.stats === 'string' ? item.stats : JSON.stringify(item.stats || {});
            html += `
                <div class="storage-item item-icon rarity-${item.rarity || 'common'}" 
                     data-storage-id="${item.id}"
                     data-item-id="${item.item_id}"
                     data-id="${item.id}"
                     data-name="${this.escapeHtml(item.name)}"
                     data-type="${item.type || 'Objet'}"
                     data-description="${this.escapeHtml(item.description || '')}"
                     data-stats='${this.escapeHtml(statsJson)}'
                     data-slot-type="${item.slot_type || 'none'}"
                     data-rarity="${item.rarity || 'common'}"
                     data-location="storage"
                     draggable="true">
                    <img src="/${item.icon}" alt="${this.escapeHtml(item.name)}" draggable="false">
                    ${item.quantity > 1 ? `<span class="quantity-badge">${item.quantity}</span>` : ''}
                </div>
            `;
        });

        // Render empty slots
        for (let i = storage.length; i < capacity; i++) {
            html += '<div class="empty-slot" data-slot-index="' + i + '"></div>';
        }

        container.innerHTML = html;

        // Setup tooltips, context menus and drag events for storage items
        container.querySelectorAll('.storage-item').forEach(item => {
            this.setupItemTooltip(item);
            this.setupItemContextMenu(item);
        });

        // Setup drag events for storage items (to withdraw)
        this.setupStorageDragEvents();
    }

    setupStorageDragEvents() {
        const storageGrid = document.getElementById('house-storage-grid');
        const inventoryGrid = document.getElementById('house-inventory-grid');

        // Drag from storage items
        storageGrid.querySelectorAll('.storage-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('type', 'storage');
                e.dataTransfer.setData('storageId', item.dataset.storageId);
                e.dataTransfer.effectAllowed = 'move';
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', (e) => {
                item.classList.remove('dragging');
                document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            });
        });

        // Initialize drop zones only once
        if (!this.dropZonesInitialized) {
            this.initializeDropZones();
        }
    }

    initializeDropZones() {
        const storageGrid = document.getElementById('house-storage-grid');
        const inventoryGrid = document.getElementById('house-inventory-grid');

        // Drop zone for inventory grid (withdraw from storage)
        inventoryGrid.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            inventoryGrid.classList.add('drag-over');
        });

        inventoryGrid.addEventListener('dragleave', (e) => {
            if (!inventoryGrid.contains(e.relatedTarget)) {
                inventoryGrid.classList.remove('drag-over');
            }
        });

        inventoryGrid.addEventListener('drop', async (e) => {
            e.preventDefault();
            inventoryGrid.classList.remove('drag-over');
            
            const type = e.dataTransfer.getData('type');
            if (type === 'storage') {
                const storageId = e.dataTransfer.getData('storageId');
                await this.withdrawItem(storageId);
            }
        });

        // Drop zone for storage grid (deposit to storage)
        storageGrid.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            storageGrid.classList.add('drag-over');
        });

        storageGrid.addEventListener('dragleave', (e) => {
            if (!storageGrid.contains(e.relatedTarget)) {
                storageGrid.classList.remove('drag-over');
            }
        });

        storageGrid.addEventListener('drop', async (e) => {
            e.preventDefault();
            storageGrid.classList.remove('drag-over');
            
            const type = e.dataTransfer.getData('type');
            if (type === 'inventory') {
                const inventoryId = e.dataTransfer.getData('inventoryId');
                await this.depositItem(inventoryId);
            }
        });

        this.dropZonesInitialized = true;
    }

    async loadPlayerInventory() {
        try {
            const response = await fetch('/game/house/inventory');
            const data = await response.json();

            if (data.success) {
                this.renderInventoryGrid(data.inventory);
            }
        } catch (error) {
            console.error('Error loading inventory:', error);
        }
    }

    renderInventoryGrid(inventory) {
        const container = document.getElementById('house-inventory-grid');

        if (!inventory || inventory.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-5 text-center py-8">Inventaire vide</p>';
            return;
        }

        container.innerHTML = inventory.map(item => {
            const statsJson = typeof item.stats === 'string' ? item.stats : JSON.stringify(item.stats || {});
            return `
                <div class="inventory-transfer-item item-icon rarity-${item.rarity || 'common'}"
                     data-inventory-id="${item.id}"
                     data-item-id="${item.item_id}"
                     data-id="${item.id}"
                     data-name="${this.escapeHtml(item.name)}"
                     data-type="${item.type || 'Objet'}"
                     data-description="${this.escapeHtml(item.description || '')}"
                     data-stats='${this.escapeHtml(statsJson)}'
                     data-slot-type="${item.item_slot_type || item.slot_type || 'none'}"
                     data-rarity="${item.rarity || 'common'}"
                     data-location="inventory"
                     draggable="true">
                    <img src="/${item.icon}" alt="${this.escapeHtml(item.name)}" draggable="false">
                    ${item.quantity > 1 ? `<span class="quantity-badge">${item.quantity}</span>` : ''}
                </div>
            `;
        }).join('');

        // Setup tooltips, context menus and click events for inventory items
        container.querySelectorAll('.inventory-transfer-item').forEach(item => {
            this.setupItemTooltip(item);
            this.setupItemContextMenu(item);
        });

        // Setup drag events for inventory items
        this.setupInventoryDragEvents();
    }

    setupInventoryDragEvents() {
        const inventoryGrid = document.getElementById('house-inventory-grid');

        // Drag from inventory items only (drop zones already initialized)
        inventoryGrid.querySelectorAll('.inventory-transfer-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('type', 'inventory');
                e.dataTransfer.setData('inventoryId', item.dataset.inventoryId);
                e.dataTransfer.effectAllowed = 'move';
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', (e) => {
                item.classList.remove('dragging');
                document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            });
        });

        // Make sure drop zones are initialized
        if (!this.dropZonesInitialized) {
            this.initializeDropZones();
        }
    }

    async depositItem(inventoryItemId) {
        if (!this.currentHouse) return;

        try {
            const response = await fetch('/game/house/deposit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    character_house_id: this.currentHouse.house.id,
                    inventory_item_id: inventoryItemId,
                    quantity: 1
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Item déposé !', 'success');
                await this.loadHouseData();
                await this.loadStorage();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error depositing item:', error);
            this.showNotification('Erreur lors du dépôt', 'error');
        }
    }

    async withdrawItem(storageItemId) {
        if (!this.currentHouse) return;

        try {
            const response = await fetch('/game/house/withdraw', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    character_house_id: this.currentHouse.house.id,
                    storage_item_id: storageItemId,
                    quantity: 1
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Item récupéré !', 'success');
                await this.loadHouseData();
                await this.loadStorage();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error withdrawing item:', error);
            this.showNotification('Erreur lors du retrait', 'error');
        }
    }

    async depositAll() {
        // TODO: Implement deposit all functionality
        this.showNotification('Fonction à venir', 'info');
    }

    renderPlacedFurniture() {
        if (!this.currentHouse) {
            document.getElementById('placed-furniture-grid').innerHTML = '';
            document.getElementById('no-furniture-message').classList.remove('hidden');
            return;
        }

        const furniture = this.currentHouse.furniture;
        const container = document.getElementById('placed-furniture-grid');

        if (!furniture || furniture.length === 0) {
            container.innerHTML = '';
            document.getElementById('no-furniture-message').classList.remove('hidden');
            return;
        }

        document.getElementById('no-furniture-message').classList.add('hidden');

        container.innerHTML = furniture.map(item => `
            <div class="furniture-card rarity-${item.rarity || 'common'}">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">${item.icon || '🪑'}</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-white">${item.name}</h4>
                        <p class="text-sm text-gray-400">${item.description || ''}</p>
                        ${item.bonus_type !== 'none' ? `
                            <div class="mt-2 text-sm text-green-400">
                                +${item.bonus_value} ${this.getBonusLabel(item.bonus_type)}
                            </div>
                        ` : ''}
                    </div>
                    <button class="sell-furniture-btn text-red-400 hover:text-red-300 text-sm"
                            data-furniture-placement-id="${item.id}"
                            title="Vendre (50% du prix)">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');

        // Bind sell buttons
        container.querySelectorAll('.sell-furniture-btn').forEach(btn => {
            btn.addEventListener('click', () => this.sellFurniture(btn.dataset.furniturePlacementId));
        });
    }

    async loadShopData() {
        await Promise.all([
            this.loadAvailableHouses(),
            this.loadAvailableFurniture()
        ]);
    }

    async loadAvailableHouses() {
        try {
            const response = await fetch('/game/house/available');
            const data = await response.json();

            if (data.success) {
                this.renderHousesShop(data.houses);
            }
        } catch (error) {
            console.error('Error loading houses:', error);
        }
    }

    renderHousesShop(houses) {
        const container = document.getElementById('houses-shop-grid');

        container.innerHTML = houses.map(house => `
            <div class="house-card ${house.owned ? 'owned' : ''}">
                <div class="aspect-video bg-gray-900 flex items-center justify-center overflow-hidden">
                    ${house.image 
                        ? `<img src="/${house.image}" alt="${house.name}" class="w-full h-full object-cover">`
                        : '<span class="text-4xl">🏠</span>'
                    }
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-white">${house.name}</h4>
                        ${house.required_level > 1 ? `<span class="text-xs text-gray-500">Niv. ${house.required_level}</span>` : ''}
                    </div>
                    <p class="text-sm text-gray-400 mb-3">${house.description || ''}</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-3">
                        <span>📦 ${house.storage_slots} slots</span>
                        <span>🪑 ${house.furniture_slots} meubles</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">${house.location_name || 'Emplacement variable'}</p>
                    ${house.owned ? `
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-green-400 font-medium">✓ Possédée</span>
                            ${house.is_primary ? `
                                <span class="text-xs bg-violet-600 text-white px-2 py-1 rounded">Principale</span>
                            ` : `
                                <button class="set-primary-shop-btn text-xs bg-violet-600 hover:bg-violet-700 text-white px-3 py-1 rounded transition-colors"
                                        data-character-house-id="${house.character_house_id}">
                                    Définir principale
                                </button>
                            `}
                        </div>
                    ` : `
                        <button class="buy-house-btn w-full bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg font-medium transition-colors ${this.playerGold < house.price ? 'opacity-50 cursor-not-allowed' : ''}"
                                data-house-id="${house.id}"
                                data-price="${house.price}"
                                ${this.playerGold < house.price ? 'disabled' : ''}>
                            🪙 ${this.formatNumber(house.price)} or
                        </button>
                    `}
                </div>
            </div>
        `).join('');

        // Bind buy buttons
        container.querySelectorAll('.buy-house-btn').forEach(btn => {
            btn.addEventListener('click', () => this.purchaseHouse(btn.dataset.houseId));
        });

        // Bind set primary buttons in shop
        container.querySelectorAll('.set-primary-shop-btn').forEach(btn => {
            btn.addEventListener('click', () => this.setPrimaryHouse(btn.dataset.characterHouseId));
        });
    }

    async loadAvailableFurniture() {
        try {
            const response = await fetch('/game/house/furniture');
            const data = await response.json();

            if (data.success) {
                this.furniture = data.furniture;
                this.categories = data.categories;
                this.renderCategoryFilter();
                this.renderFurnitureShop(this.furniture);
            }
        } catch (error) {
            console.error('Error loading furniture:', error);
        }
    }

    renderCategoryFilter() {
        const container = document.getElementById('furniture-category-filter');
        
        let html = `<button class="furniture-cat-btn active px-3 py-1 rounded-lg text-sm transition-colors" data-category="all">Tous</button>`;
        
        html += this.categories.map(cat => `
            <button class="furniture-cat-btn px-3 py-1 rounded-lg text-sm transition-colors" data-category="${cat.id}">
                ${cat.icon} ${cat.name}
            </button>
        `).join('');

        container.innerHTML = html;

        // Bind filter buttons
        container.querySelectorAll('.furniture-cat-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                container.querySelectorAll('.furniture-cat-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.filterFurniture(btn.dataset.category);
            });
        });
    }

    filterFurniture(categoryId) {
        let filtered = this.furniture;
        if (categoryId !== 'all') {
            filtered = this.furniture.filter(f => f.category_id == categoryId);
        }
        this.renderFurnitureShop(filtered);
    }

    renderFurnitureShop(furniture) {
        const container = document.getElementById('furniture-shop-grid');

        if (!furniture || furniture.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-3 text-center">Aucun meuble disponible</p>';
            return;
        }

        container.innerHTML = furniture.map(item => `
            <div class="furniture-card rarity-${item.rarity || 'common'}">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">${item.icon || '🪑'}</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-white">${item.name}</h4>
                            <span class="text-xs px-2 py-1 rounded bg-gray-700 text-gray-300">${item.category_name || 'Divers'}</span>
                        </div>
                        <p class="text-sm text-gray-400 mt-1">${item.description || ''}</p>
                        ${item.bonus_type !== 'none' ? `
                            <div class="mt-2 text-sm text-green-400">
                                +${item.bonus_value} ${this.getBonusLabel(item.bonus_type)}
                            </div>
                        ` : ''}
                        ${item.required_level > 1 ? `<p class="text-xs text-gray-500 mt-1">Niveau requis: ${item.required_level}</p>` : ''}
                    </div>
                </div>
                <button class="buy-furniture-btn w-full mt-4 bg-violet-600 hover:bg-violet-700 text-white py-2 rounded-lg font-medium transition-colors ${!this.currentHouse || this.playerGold < item.price ? 'opacity-50 cursor-not-allowed' : ''}"
                        data-furniture-id="${item.id}"
                        data-price="${item.price}"
                        ${!this.currentHouse || this.playerGold < item.price ? 'disabled' : ''}>
                    🪙 ${this.formatNumber(item.price)}
                </button>
            </div>
        `).join('');

        // Bind buy buttons
        container.querySelectorAll('.buy-furniture-btn').forEach(btn => {
            btn.addEventListener('click', () => this.purchaseFurniture(btn.dataset.furnitureId));
        });
    }

    async purchaseHouse(houseId) {
        try {
            const response = await fetch('/game/house/purchase', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ house_id: houseId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.playerGold = data.new_gold;
                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);
                await this.loadHouseData();
                await this.loadAvailableHouses();
                this.switchTab('overview');
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error purchasing house:', error);
            this.showNotification('Erreur lors de l\'achat', 'error');
        }
    }

    async purchaseFurniture(furnitureId) {
        if (!this.currentHouse) {
            this.showNotification('Vous devez d\'abord acheter une maison', 'error');
            return;
        }

        try {
            const response = await fetch('/game/house/furniture/purchase', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    furniture_id: furnitureId,
                    character_house_id: this.currentHouse.house.id
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.playerGold = data.new_gold;
                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);
                await this.loadHouseData();
                this.renderFurnitureShop(this.furniture);
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error purchasing furniture:', error);
            this.showNotification('Erreur lors de l\'achat', 'error');
        }
    }

    async sellFurniture(furniturePlacementId) {
        if (!confirm('Vendre ce meuble ? Vous récupérerez 50% du prix d\'achat.')) return;

        try {
            const response = await fetch('/game/house/furniture/sell', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ furniture_placement_id: furniturePlacementId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.playerGold = data.new_gold;
                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);
                await this.loadHouseData();
                this.renderPlacedFurniture();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error selling furniture:', error);
            this.showNotification('Erreur lors de la vente', 'error');
        }
    }

    async setPrimaryHouse(characterHouseId) {
        try {
            const response = await fetch('/game/house/set-primary', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ character_house_id: characterHouseId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Maison principale mise à jour', 'success');
                await this.loadHouseData();
                
                // Rafraîchir le shop pour mettre à jour les boutons "Principal"
                await this.loadAvailableHouses();
                
                // Rafraîchir les points de la carte pour mettre à jour la position de la maison
                if (window.loadMapPoints && window.characterId) {
                    const currentMapId = window.getCurrentMapId ? window.getCurrentMapId() : 1;
                    await window.loadMapPoints(currentMapId, window.characterId);
                }
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error setting primary house:', error);
        }
    }

    async renameHouse() {
        if (!this.currentHouse) return;

        const newName = document.getElementById('house-custom-name').value.trim();
        if (!newName) {
            this.showNotification('Entrez un nom', 'error');
            return;
        }

        try {
            const response = await fetch('/game/house/rename', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    character_house_id: this.currentHouse.house.id,
                    name: newName
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Maison renommée !', 'success');
                document.getElementById('house-title').textContent = newName;
                await this.loadHouseData();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error renaming house:', error);
        }
    }

    getBonusLabel(type) {
        const labels = {
            storage: 'stockage',
            comfort: 'confort',
            luck: '% chance',
            xp: '% XP',
            gold: '% or',
            defense: 'défense'
        };
        return labels[type] || type;
    }

    formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /**
     * Setup tooltip for an item (similar to inventory.js)
     * @param {HTMLElement} item - Item element
     */
    setupItemTooltip(item) {
        const tooltip = document.getElementById('item-tooltip');
        if (!tooltip) return;

        const nameEl = document.getElementById('tooltip-name');
        const typeEl = document.getElementById('tooltip-type');
        const statsEl = document.getElementById('tooltip-stats');
        const descEl = document.getElementById('tooltip-desc');

        item.addEventListener('mouseenter', (e) => {
            // Populate Data
            nameEl.textContent = item.dataset.name || 'Objet inconnu';
            typeEl.textContent = item.dataset.type || '';
            descEl.innerHTML = item.dataset.description || '';

            // Parse and display stats
            statsEl.innerHTML = '';
            try {
                const stats = JSON.parse(item.dataset.stats || '{}');
                for (const [key, value] of Object.entries(stats)) {
                    const statRow = document.createElement('div');
                    statRow.className = 'tooltip-stat';
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    statRow.innerHTML = `<span>${label}</span><span>${value}</span>`;
                    statsEl.appendChild(statRow);
                }
            } catch (err) {
                console.error('Error parsing stats', err);
            }

            tooltip.classList.remove('hidden');
        });

        item.addEventListener('mousemove', (e) => {
            // Position tooltip near cursor
            const offset = 15;
            let left = e.clientX + offset;
            let top = e.clientY + offset;

            // Boundary checks
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

    /**
     * Setup context menu for an item
     * @param {HTMLElement} item - Item element
     */
    setupItemContextMenu(item) {
        item.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            this.showItemContextMenu(item, e.clientX, e.clientY);
        });
    }

    /**
     * Show context menu for house items
     * @param {HTMLElement} item 
     * @param {number} x 
     * @param {number} y 
     */
    showItemContextMenu(item, x, y) {
        const menu = document.getElementById('house-item-context-menu');
        if (!menu) {
            this.createHouseContextMenu();
        }
        
        const contextMenu = document.getElementById('house-item-context-menu');
        contextMenuTarget = item;

        const location = item.dataset.location;
        const slotType = item.dataset.slotType;
        
        // Show/hide buttons based on location
        const btnEquip = document.getElementById('house-ctx-equip');
        const btnDeposit = document.getElementById('house-ctx-deposit');
        const btnWithdraw = document.getElementById('house-ctx-withdraw');
        const btnDrop = document.getElementById('house-ctx-drop');

        // Reset visibility
        btnEquip.classList.add('hidden');
        btnDeposit.classList.add('hidden');
        btnWithdraw.classList.add('hidden');

        if (location === 'storage') {
            btnWithdraw.classList.remove('hidden');
            if (slotType && slotType !== 'none') {
                btnEquip.classList.remove('hidden');
            }
        } else if (location === 'inventory') {
            btnDeposit.classList.remove('hidden');
            if (slotType && slotType !== 'none') {
                btnEquip.classList.remove('hidden');
            }
        }

        // Position
        contextMenu.style.left = `${x}px`;
        contextMenu.style.top = `${y}px`;
        contextMenu.classList.remove('hidden');

        // Boundary check
        setTimeout(() => {
            const rect = contextMenu.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                contextMenu.style.left = `${window.innerWidth - rect.width - 10}px`;
            }
            if (rect.bottom > window.innerHeight) {
                contextMenu.style.top = `${window.innerHeight - rect.height - 10}px`;
            }
        }, 0);

        playSound('click');
    }

    /**
     * Create the context menu HTML for house items
     */
    createHouseContextMenu() {
        const menu = document.createElement('div');
        menu.id = 'house-item-context-menu';
        menu.className = 'fixed bg-gray-900 border border-gray-700 rounded-lg shadow-xl z-[10000] hidden min-w-[150px]';
        menu.innerHTML = `
            <button id="house-ctx-equip" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-amber-400 hover:bg-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Équiper
            </button>
            <button id="house-ctx-deposit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-blue-400 hover:bg-gray-800 transition-colors hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Déposer dans le coffre
            </button>
            <button id="house-ctx-withdraw" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-green-400 hover:bg-gray-800 transition-colors hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Retirer du coffre
            </button>
            <div class="border-t border-gray-700 my-1"></div>
            <button id="house-ctx-drop" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Jeter
            </button>
        `;
        document.body.appendChild(menu);

        // Setup event listeners
        this.setupHouseContextMenuEvents();
    }

    /**
     * Setup event listeners for house context menu
     */
    setupHouseContextMenuEvents() {
        const menu = document.getElementById('house-item-context-menu');
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (menu && !menu.contains(e.target) && !e.target.closest('.storage-item') && !e.target.closest('.inventory-transfer-item')) {
                menu.classList.add('hidden');
            }
        });

        // Equip action - close house modal and open inventory
        document.getElementById('house-ctx-equip')?.addEventListener('click', async () => {
            if (contextMenuTarget) {
                const location = contextMenuTarget.dataset.location;
                const itemId = contextMenuTarget.dataset.storageId || contextMenuTarget.dataset.inventoryId;
                
                // If item is in storage, first withdraw it
                if (location === 'storage') {
                    await this.withdrawItem(itemId);
                }
                
                // Close house modal
                this.close();
                
                // Open inventory modal
                setTimeout(() => {
                    const inventoryModal = document.getElementById('inventory-modal');
                    if (inventoryModal) {
                        inventoryModal.classList.remove('hidden');
                        // Highlight the item if possible
                        this.showNotification('Équipez l\'objet depuis votre inventaire', 'info');
                    }
                }, 300);
            }
            menu.classList.add('hidden');
        });

        // Deposit action
        document.getElementById('house-ctx-deposit')?.addEventListener('click', async () => {
            if (contextMenuTarget) {
                const itemId = contextMenuTarget.dataset.inventoryId;
                await this.depositItem(itemId);
            }
            menu.classList.add('hidden');
        });

        // Withdraw action
        document.getElementById('house-ctx-withdraw')?.addEventListener('click', async () => {
            if (contextMenuTarget) {
                const itemId = contextMenuTarget.dataset.storageId;
                await this.withdrawItem(itemId);
            }
            menu.classList.add('hidden');
        });

        // Drop action
        document.getElementById('house-ctx-drop')?.addEventListener('click', async () => {
            if (contextMenuTarget) {
                if (confirm('Êtes-vous sûr de vouloir jeter cet objet définitivement ?')) {
                    const location = contextMenuTarget.dataset.location;
                    const itemId = contextMenuTarget.dataset.storageId || contextMenuTarget.dataset.inventoryId;
                    
                    if (location === 'storage') {
                        await this.dropStorageItem(itemId);
                    } else {
                        await this.dropInventoryItem(itemId);
                    }
                }
            }
            menu.classList.add('hidden');
        });
    }

    /**
     * Drop item from storage
     */
    async dropStorageItem(storageId) {
        try {
            const response = await fetch('/game/house/drop-storage', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ storage_id: storageId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Objet jeté', 'success');
                await this.loadStorageData();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error dropping storage item:', error);
            this.showNotification('Erreur lors de la suppression', 'error');
        }
    }

    /**
     * Drop item from inventory (while in house modal)
     */
    async dropInventoryItem(inventoryId) {
        try {
            const response = await fetch('/game/inventory/drop', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ itemId: inventoryId })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Objet jeté', 'success');
                await this.loadPlayerInventory();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error dropping item:', error);
            this.showNotification('Erreur lors de la suppression', 'error');
        }
    }

    // ==========================================
    // WORKBENCH / ENCHANTMENT SYSTEM
    // ==========================================

    /**
     * Load workbench data
     */
    async loadWorkbench() {
        try {
            const response = await fetch('/game/workbench');
            const data = await response.json();

            if (data.success) {
                this.workbenchData = data;
                this.playerGold = data.player_gold;
                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);

                // Hide all states first
                document.getElementById('workbench-no-house')?.classList.add('hidden');
                document.getElementById('workbench-locked')?.classList.add('hidden');
                document.getElementById('workbench-interface')?.classList.add('hidden');

                if (!data.has_house) {
                    // No house - show message to buy a house
                    document.getElementById('workbench-no-house')?.classList.remove('hidden');
                } else if (data.has_workbench) {
                    // Has workbench - show interface
                    document.getElementById('workbench-interface')?.classList.remove('hidden');
                    this.renderWorkbenchItems(data.items);
                    this.renderWorkbenchEnchantments(data.enchantments);
                } else {
                    // Has house but no workbench - show paywall
                    document.getElementById('workbench-locked')?.classList.remove('hidden');
                    document.getElementById('workbench-price').textContent = this.formatNumber(data.workbench_price);
                    document.getElementById('workbench-required-level').textContent = data.workbench_required_level;
                    
                    // Update button state
                    const purchaseBtn = document.getElementById('purchase-workbench-btn');
                    const errorEl = document.getElementById('workbench-purchase-error');
                    
                    if (purchaseBtn) {
                        const canAfford = this.playerGold >= data.workbench_price;
                        const hasLevel = data.player_level >= data.workbench_required_level;
                        purchaseBtn.disabled = !canAfford || !hasLevel;
                        
                        errorEl?.classList.add('hidden');
                        if (!canAfford) {
                            errorEl.textContent = `Or insuffisant (${this.formatNumber(this.playerGold)} / ${this.formatNumber(data.workbench_price)})`;
                            errorEl?.classList.remove('hidden');
                        } else if (!hasLevel) {
                            errorEl.textContent = `Niveau insuffisant (${data.player_level} / ${data.workbench_required_level})`;
                            errorEl?.classList.remove('hidden');
                        }
                    }
                }
            } else {
                this.showNotification(data.message || 'Erreur lors du chargement', 'error');
            }
        } catch (error) {
            console.error('Error loading workbench:', error);
            this.showNotification('Erreur lors du chargement de l\'établi', 'error');
        }
    }

    /**
     * Purchase the workbench for current house
     */
    async purchaseWorkbench() {
        try {
            const response = await fetch('/game/workbench/purchase', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();

            if (data.success) {
                this.showNotification('✨ Établi acheté !', 'success');
                playSound('purchase');
                
                // Update gold
                if (data.new_gold !== undefined) {
                    this.playerGold = data.new_gold;
                    document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);
                }
                
                // Reload workbench to show interface
                await this.loadWorkbench();
            } else {
                this.showNotification(data.message || 'Erreur lors de l\'achat', 'error');
            }
        } catch (error) {
            console.error('Error purchasing workbench:', error);
            this.showNotification('Erreur lors de l\'achat', 'error');
        }
    }

    /**
     * Render items available for enchanting
     */
    renderWorkbenchItems(items) {
        const container = document.getElementById('workbench-items-grid');
        
        if (!items || items.length === 0) {
            container.innerHTML = '<p class="text-gray-500 col-span-4 text-center py-4">Aucun item équipable</p>';
            return;
        }

        container.innerHTML = items.map(item => {
            const enchantCount = item.enchantments ? item.enchantments.length : 0;
            const statsJson = typeof item.stats === 'string' ? item.stats : JSON.stringify(item.stats || {});
            return `
                <div class="workbench-item rarity-${item.rarity || 'common'} cursor-pointer hover:ring-2 hover:ring-violet-500 transition-all relative"
                     data-inventory-id="${item.inventory_id}"
                     data-item-id="${item.item_id}"
                     data-name="${this.escapeHtml(item.name)}"
                     data-type="${item.type}"
                     data-slot-type="${item.slot_type}"
                     data-stats='${this.escapeHtml(statsJson)}'
                     data-enchantments='${this.escapeHtml(JSON.stringify(item.enchantments || []))}'>
                    <img src="/${item.icon}" alt="${this.escapeHtml(item.name)}" class="w-full h-full object-contain p-1" draggable="false">
                    ${enchantCount > 0 ? `<span class="absolute -top-1 -right-1 bg-violet-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">${enchantCount}</span>` : ''}
                </div>
            `;
        }).join('');

        // Add click events
        container.querySelectorAll('.workbench-item').forEach(item => {
            item.addEventListener('click', () => this.selectWorkbenchItem(item));
            this.setupItemTooltip(item);
        });
    }

    /**
     * Select an item for enchanting
     */
    selectWorkbenchItem(itemElement) {
        // Remove previous selection
        document.querySelectorAll('.workbench-item').forEach(i => i.classList.remove('ring-2', 'ring-violet-500'));
        
        // Select this item
        itemElement.classList.add('ring-2', 'ring-violet-500');
        
        this.selectedWorkbenchItem = {
            inventoryId: itemElement.dataset.inventoryId,
            name: itemElement.dataset.name,
            type: itemElement.dataset.type,
            slotType: itemElement.dataset.slotType,
            stats: JSON.parse(itemElement.dataset.stats || '{}'),
            enchantments: JSON.parse(itemElement.dataset.enchantments || '[]'),
            icon: itemElement.querySelector('img')?.src || ''
        };

        // Update item slot visual
        const itemSlot = document.getElementById('workbench-item-slot');
        itemSlot.innerHTML = `<img src="${this.selectedWorkbenchItem.icon}" alt="${this.selectedWorkbenchItem.name}" class="w-full h-full object-contain p-1">`;

        // Show item info
        const itemInfo = document.getElementById('workbench-item-info');
        itemInfo.classList.remove('hidden');
        document.getElementById('workbench-item-name').textContent = this.selectedWorkbenchItem.name;
        document.getElementById('workbench-item-type').textContent = this.selectedWorkbenchItem.slotType;

        // Show current enchantments
        const enchantsContainer = document.getElementById('workbench-item-enchants');
        if (this.selectedWorkbenchItem.enchantments.length > 0) {
            enchantsContainer.innerHTML = `
                <p class="text-xs text-violet-400 mb-1">Enchantements actuels:</p>
                ${this.selectedWorkbenchItem.enchantments.map(e => `
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-300">✨ ${e.name}</span>
                        <button class="text-red-400 hover:text-red-300 text-xs" data-enchant-id="${e.id}" onclick="window.houseManager?.removeEnchantment(${e.id})">✕</button>
                    </div>
                `).join('')}
            `;
        } else {
            enchantsContainer.innerHTML = '<p class="text-xs text-gray-500">Aucun enchantement</p>';
        }

        // Filter compatible enchantments
        this.filterCompatibleEnchantments(this.selectedWorkbenchItem.slotType);

        // Update result preview if enchantment selected
        this.updateResultPreview();

        playSound('click');
    }

    /**
     * Filter and highlight compatible enchantments
     */
    filterCompatibleEnchantments(slotType) {
        const enchantmentsList = document.getElementById('workbench-enchantments-list');
        const items = enchantmentsList.querySelectorAll('.enchantment-item');

        items.forEach(item => {
            const compatibleSlots = JSON.parse(item.dataset.compatibleSlots || '[]');
            const isCompatible = compatibleSlots.length === 0 || compatibleSlots.includes(slotType);
            
            if (isCompatible) {
                item.classList.remove('opacity-50', 'pointer-events-none');
                item.classList.add('cursor-pointer');
            } else {
                item.classList.add('opacity-50', 'pointer-events-none');
                item.classList.remove('cursor-pointer');
            }
        });
    }

    /**
     * Render available enchantments
     */
    renderWorkbenchEnchantments(enchantments) {
        const container = document.getElementById('workbench-enchantments-list');
        
        if (!enchantments || enchantments.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">Aucun enchantement disponible</p>';
            return;
        }

        const rarityColors = {
            common: 'gray',
            uncommon: 'green',
            rare: 'blue',
            epic: 'purple',
            legendary: 'amber'
        };

        container.innerHTML = enchantments.map(ench => {
            const color = rarityColors[ench.rarity] || 'gray';
            const modifiers = JSON.parse(ench.stat_modifiers || '{}');
            const compatibleSlots = ench.compatible_slot_types || '[]';
            
            return `
                <div class="enchantment-item bg-gray-800 rounded-lg p-3 border border-${color}-500/30 hover:border-${color}-500 transition-colors cursor-pointer"
                     data-enchantment-id="${ench.id}"
                     data-name="${this.escapeHtml(ench.name)}"
                     data-cost="${ench.cost}"
                     data-modifiers='${ench.stat_modifiers}'
                     data-compatible-slots='${compatibleSlots}'
                     data-rarity="${ench.rarity}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-${color}-400">${ench.name}</span>
                        <span class="text-amber-400 text-sm">${this.formatNumber(ench.cost)} 💰</span>
                    </div>
                    <p class="text-xs text-gray-400 mb-2">${ench.description || ''}</p>
                    <div class="flex flex-wrap gap-1">
                        ${Object.entries(modifiers).map(([stat, value]) => `
                            <span class="text-xs bg-gray-900 px-2 py-0.5 rounded text-${value > 0 ? 'green' : 'red'}-400">
                                ${stat.replace(/_/g, ' ')}: ${value > 0 ? '+' : ''}${value}
                            </span>
                        `).join('')}
                    </div>
                </div>
            `;
        }).join('');

        // Add click events
        container.querySelectorAll('.enchantment-item').forEach(item => {
            item.addEventListener('click', () => {
                if (!item.classList.contains('pointer-events-none')) {
                    this.selectEnchantment(item);
                }
            });
        });
    }

    /**
     * Select an enchantment
     */
    selectEnchantment(enchantElement) {
        if (!this.selectedWorkbenchItem) {
            this.showNotification('Sélectionnez d\'abord un item', 'warning');
            return;
        }

        // Remove previous selection
        document.querySelectorAll('.enchantment-item').forEach(i => i.classList.remove('ring-2', 'ring-violet-500'));
        
        // Select this enchantment
        enchantElement.classList.add('ring-2', 'ring-violet-500');

        this.selectedEnchantment = {
            id: enchantElement.dataset.enchantmentId,
            name: enchantElement.dataset.name,
            cost: parseInt(enchantElement.dataset.cost),
            modifiers: JSON.parse(enchantElement.dataset.modifiers || '{}'),
            rarity: enchantElement.dataset.rarity
        };

        // Update enchant slot visual
        const enchantSlot = document.getElementById('workbench-enchant-slot');
        enchantSlot.innerHTML = `<span class="text-2xl">✨</span><span class="text-xs text-violet-400 absolute bottom-0">${this.selectedEnchantment.name.substring(0, 8)}</span>`;
        enchantSlot.classList.add('border-violet-500');

        // Update result preview
        this.updateResultPreview();

        playSound('click');
    }

    /**
     * Update the result preview
     */
    updateResultPreview() {
        const preview = document.getElementById('workbench-result-preview');
        const statsContainer = document.getElementById('workbench-result-stats');
        const costDisplay = document.getElementById('workbench-cost');

        if (!this.selectedWorkbenchItem || !this.selectedEnchantment) {
            preview.classList.add('hidden');
            return;
        }

        // Check if item already has this enchantment
        const hasEnchantment = this.selectedWorkbenchItem.enchantments.some(
            e => e.enchantment_id == this.selectedEnchantment.id
        );

        if (hasEnchantment) {
            statsContainer.innerHTML = '<p class="text-red-400">Cet item possède déjà cet enchantement !</p>';
            preview.classList.remove('hidden');
            document.getElementById('workbench-apply-btn').disabled = true;
            document.getElementById('workbench-apply-btn').classList.add('opacity-50');
            return;
        }

        // Show preview
        preview.classList.remove('hidden');
        document.getElementById('workbench-apply-btn').disabled = false;
        document.getElementById('workbench-apply-btn').classList.remove('opacity-50');
        costDisplay.textContent = this.formatNumber(this.selectedEnchantment.cost);

        // Build stats preview
        let statsHtml = `<p class="mb-1"><strong>${this.selectedWorkbenchItem.name}</strong> + <strong class="text-violet-400">${this.selectedEnchantment.name}</strong></p>`;
        statsHtml += '<div class="space-y-1 mt-2">';
        
        for (const [stat, value] of Object.entries(this.selectedEnchantment.modifiers)) {
            const label = stat.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            statsHtml += `<div class="flex justify-between"><span>${label}</span><span class="text-green-400">+${value}</span></div>`;
        }
        
        statsHtml += '</div>';
        statsContainer.innerHTML = statsHtml;
    }

    /**
     * Apply the selected enchantment to the item
     */
    async applyEnchantment() {
        if (!this.selectedWorkbenchItem || !this.selectedEnchantment) {
            this.showNotification('Sélectionnez un item et un enchantement', 'warning');
            return;
        }

        if (this.playerGold < this.selectedEnchantment.cost) {
            this.showNotification('Or insuffisant', 'error');
            return;
        }

        try {
            const response = await fetch('/game/workbench/enchant', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    inventory_item_id: this.selectedWorkbenchItem.inventoryId,
                    enchantment_id: this.selectedEnchantment.id
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Enchantement appliqué !', 'success');
                this.playerGold = data.new_gold;
                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);
                
                // Reset selection
                this.selectedWorkbenchItem = null;
                this.selectedEnchantment = null;
                
                // Reset UI
                document.getElementById('workbench-item-slot').innerHTML = '<span class="text-gray-600 text-3xl">⚔️</span>';
                document.getElementById('workbench-enchant-slot').innerHTML = '<span class="text-gray-600 text-3xl">✨</span>';
                document.getElementById('workbench-enchant-slot').classList.remove('border-violet-500');
                document.getElementById('workbench-result-preview').classList.add('hidden');
                document.getElementById('workbench-item-info').classList.add('hidden');

                // Reload workbench data
                await this.loadWorkbench();
                
                playSound('levelUp');
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error applying enchantment:', error);
            this.showNotification('Erreur lors de l\'enchantement', 'error');
        }
    }

    /**
     * Remove an enchantment from an item
     */
    async removeEnchantment(itemEnchantmentId) {
        if (!confirm('Retirer cet enchantement coûte 50 or. Continuer ?')) {
            return;
        }

        try {
            const response = await fetch('/game/workbench/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ item_enchantment_id: itemEnchantmentId })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Enchantement retiré', 'success');
                this.playerGold = data.new_gold;
                document.getElementById('house-player-gold').textContent = this.formatNumber(this.playerGold);
                
                // Reload workbench
                await this.loadWorkbench();
            } else {
                this.showNotification(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            console.error('Error removing enchantment:', error);
            this.showNotification('Erreur lors du retrait', 'error');
        }
    }

    showNotification(message, type = 'info') {
        showToast(message, type);
    }
}

// Initialize and export
const houseManager = new HouseManager();

// Expose for inline onclick handlers
window.houseManager = houseManager;

export { houseManager };
export default HouseManager;
