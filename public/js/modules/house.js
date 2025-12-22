/**
 * House Module - Gestion de la maison du joueur
 * DungeonXplorer
 */

import { showToast } from './toast.js';

class HouseManager {
    constructor() {
        this.modal = document.getElementById('house-modal');
        this.currentHouse = null;
        this.playerGold = 0;
        this.playerLevel = 1;
        this.houses = [];
        this.furniture = [];
        this.categories = [];
        
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

        // Rename house
        document.getElementById('rename-house-btn')?.addEventListener('click', () => this.renameHouse());

        // Deposit all
        document.getElementById('deposit-all-btn')?.addEventListener('click', () => this.depositAll());

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
        
        // Render stored items
        storage.forEach(item => {
            html += `
                <div class="storage-item rarity-${item.rarity || 'common'}" 
                     data-storage-id="${item.id}"
                     data-item-name="${item.name}"
                     draggable="true"
                     title="${item.name}">
                    <img src="/${item.icon}" alt="${item.name}" draggable="false">
                    ${item.quantity > 1 ? `<span class="quantity-badge">${item.quantity}</span>` : ''}
                </div>
            `;
        });

        // Render empty slots
        for (let i = storage.length; i < capacity; i++) {
            html += '<div class="empty-slot" data-slot-index="' + i + '"></div>';
        }

        container.innerHTML = html;

        // Bind click events for withdrawal
        container.querySelectorAll('.storage-item').forEach(item => {
            item.addEventListener('click', () => this.withdrawItem(item.dataset.storageId));
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
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', (e) => {
                item.classList.remove('dragging');
                document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            });
        });

        // Drop zone for inventory grid (withdraw from storage)
        inventoryGrid.addEventListener('dragover', (e) => {
            e.preventDefault();
            inventoryGrid.classList.add('drag-over');
        });

        inventoryGrid.addEventListener('dragleave', (e) => {
            inventoryGrid.classList.remove('drag-over');
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

        container.innerHTML = inventory.map(item => `
            <div class="inventory-transfer-item rarity-${item.rarity || 'common'}"
                 data-inventory-id="${item.id}"
                 data-item-name="${item.name}"
                 draggable="true"
                 title="${item.name}">
                <img src="/${item.icon}" alt="${item.name}" draggable="false">
                ${item.quantity > 1 ? `<span class="quantity-badge">${item.quantity}</span>` : ''}
            </div>
        `).join('');

        // Bind click events for deposit
        container.querySelectorAll('.inventory-transfer-item').forEach(item => {
            item.addEventListener('click', () => this.depositItem(item.dataset.inventoryId));
        });

        // Setup drag events for inventory items
        this.setupInventoryDragEvents();
    }

    setupInventoryDragEvents() {
        const storageGrid = document.getElementById('house-storage-grid');
        const inventoryGrid = document.getElementById('house-inventory-grid');

        // Drag from inventory items
        inventoryGrid.querySelectorAll('.inventory-transfer-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('type', 'inventory');
                e.dataTransfer.setData('inventoryId', item.dataset.inventoryId);
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', (e) => {
                item.classList.remove('dragging');
                document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            });
        });

        // Drop zone for storage grid (deposit to storage)
        storageGrid.addEventListener('dragover', (e) => {
            e.preventDefault();
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

    showNotification(message, type = 'info') {
        showToast(message, type);
    }
}

// Initialize and export
const houseManager = new HouseManager();

export { houseManager };
export default HouseManager;
