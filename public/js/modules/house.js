/**
 * House Module - Gestion de la maison du joueur
 * DungeonXplorer
 */

import { showToast } from './toast.js';
import { playSound } from './soundManager.js';

// State for context menu
let contextMenuTarget = null;

// Helper function for Safari compatibility (optional chaining fallback)
function safeGetElement(id) {
    return document.getElementById(id);
}

function safeAddEventListener(element, event, handler) {
    if (element) {
        element.addEventListener(event, handler);
    }
}

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
        safeAddEventListener(safeGetElement('close-house-modal'), 'click', () => this.close());

        // Tab navigation
        document.querySelectorAll('.house-tab').forEach(tab => {
            tab.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Shop sub-tabs
        document.querySelectorAll('.shop-subtab').forEach(tab => {
            tab.addEventListener('click', (e) => this.switchShopSubtab(e.target.dataset.subtab));
        });

        // Go to shop button
        safeAddEventListener(safeGetElement('go-to-shop-btn'), 'click', () => {
            this.switchTab('shop');
        });

        // Workbench go to shop button
        safeAddEventListener(safeGetElement('workbench-go-to-shop-btn'), 'click', () => {
            this.switchTab('shop');
        });

        // Purchase workbench button
        safeAddEventListener(safeGetElement('purchase-workbench-btn'), 'click', () => this.purchaseWorkbench());

        // Rename house
        safeAddEventListener(safeGetElement('rename-house-btn'), 'click', () => this.renameHouse());

        // Deposit all
        safeAddEventListener(safeGetElement('deposit-all-btn'), 'click', () => this.depositAll());

        // Workbench apply button
        safeAddEventListener(safeGetElement('workbench-apply-btn'), 'click', () => this.applyEnchantment());

        // Click outside to close
        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) this.close();
            });
        }

        // ESC to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal && !this.modal.classList.contains('hidden')) {
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
            // Add error handler before setting src
            img.onerror = () => {
                console.warn('Failed to load house image:', house.image);
                img.classList.add('hidden');
                placeholder.classList.remove('hidden');
            };
            img.onload = () => {
                img.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            // Handle case where image path might already start with /
            const imagePath = house.image.startsWith('/') ? house.image : '/' + house.image;
            img.src = imagePath;
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
            storage: {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>`,
                label: 'Stockage',
                suffix: ' slots',
                color: 'text-amber-400'
            },
            comfort: {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>`,
                label: 'Confort',
                suffix: '',
                color: 'text-pink-400'
            },
            luck: {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" />
                </svg>`,
                label: 'Chance',
                suffix: '%',
                color: 'text-green-400'
            },
            xp: {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>`,
                label: 'XP',
                suffix: '%',
                color: 'text-blue-400'
            },
            gold: {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`,
                label: 'Or',
                suffix: '%',
                color: 'text-yellow-400'
            },
            defense: {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>`,
                label: 'Défense',
                suffix: '',
                color: 'text-slate-400'
            }
        };

        const defaultIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
        </svg>`;

        container.innerHTML = Object.entries(bonuses)
            .filter(([key, value]) => value > 0)
            .map(([key, value]) => {
                const type = bonusTypes[key] || { icon: defaultIcon, label: key, suffix: '', color: 'text-violet-400' };
                return `
                    <div class="bonus-item">
                        <span class="bonus-icon ${type.color}">${type.icon}</span>
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
        var tabEl = document.getElementById('house-tab-' + tabName);
        if (tabEl) tabEl.classList.remove('hidden');

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
            const enchantments = item.enchantments || [];
            const enchantCount = enchantments.length;
            const isEnchanted = enchantCount > 0;

            html += `
                <div class="storage-item item-icon ${isEnchanted ? 'enchanted-item' : ''} rarity-${item.rarity || 'common'}" 
                     data-storage-id="${item.id}"
                     data-item-id="${item.item_id}"
                     data-id="${item.id}"
                     data-name="${this.escapeHtml(item.name)}"
                     data-type="${item.type || 'Objet'}"
                     data-description="${this.escapeHtml(item.description || '')}"
                     data-stats='${this.escapeJsonAttr(statsJson)}'
                     data-stat-ranges='${this.escapeJsonAttr(typeof item.stat_ranges === 'string' ? item.stat_ranges : JSON.stringify(item.stat_ranges || {}))}'
                     data-enchantments='${this.escapeJsonAttr(JSON.stringify(enchantments))}'
                     data-slot-type="${item.slot_type || 'none'}"
                     data-rarity="${item.rarity || 'common'}"
                     data-location="storage"
                     draggable="true">
                    <img src="/${item.icon}" alt="${this.escapeHtml(item.name)}" draggable="false" class="relative z-[2]">
                    ${item.quantity > 1 ? `<span class="quantity-badge">${item.quantity}</span>` : ''}
                    ${isEnchanted ? `<span class="enchant-badge">${enchantCount}</span>` : ''}
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
            const enchantments = item.enchantments || [];
            const enchantCount = enchantments.length;
            const isEnchanted = enchantCount > 0;

            return `
                <div class="inventory-transfer-item item-icon ${isEnchanted ? 'enchanted-item' : ''} rarity-${item.rarity || 'common'}"
                     data-inventory-id="${item.id}"
                     data-item-id="${item.item_id}"
                     data-id="${item.id}"
                     data-name="${this.escapeHtml(item.name)}"
                     data-type="${item.type || 'Objet'}"
                     data-description="${this.escapeHtml(item.description || '')}"
                     data-stats='${this.escapeJsonAttr(statsJson)}'
                     data-enchantments='${this.escapeJsonAttr(JSON.stringify(enchantments))}'
                     data-slot-type="${item.item_slot_type || item.slot_type || 'none'}"
                     data-rarity="${item.rarity || 'common'}"
                     data-location="inventory"
                     draggable="true">
                    <img src="/${item.icon}" alt="${this.escapeHtml(item.name)}" draggable="false" class="relative z-[2]">
                    ${item.quantity > 1 ? `<span class="quantity-badge">${item.quantity}</span>` : ''}
                    ${isEnchanted ? `<span class="enchant-badge">${enchantCount}</span>` : ''}
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

        container.innerHTML = houses.map(house => {
            const imagePath = house.image ? (house.image.startsWith('/') ? house.image : '/' + house.image) : null;
            return `
            <div class="house-card ${house.owned ? 'owned' : ''}">
                <div class="aspect-video bg-gray-900 flex items-center justify-center overflow-hidden">
                    ${imagePath
                    ? `<img src="${imagePath}" alt="${house.name}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                           <div class="hidden flex-col items-center justify-center text-gray-500">
                               <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                               </svg>
                           </div>`
                    : `<div class="flex flex-col items-center justify-center text-gray-500">
                               <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                               </svg>
                           </div>`
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
        `;
        }).join('');

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
                ${cat.name}
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
        const n = Number(num);
        if (!isFinite(n)) return '0';
        return new Intl.NumberFormat('fr-FR').format(n);
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /**
     * Escape JSON string for use in single-quoted HTML attributes
     * Only escapes single quotes, preserves double quotes for valid JSON
     */
    escapeJsonAttr(jsonStr) {
        if (!jsonStr) return '{}';
        return jsonStr.replace(/'/g, '&#39;');
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
        const enchantsEl = document.getElementById('tooltip-enchants');

        item.addEventListener('mouseenter', (e) => {
            // Populate Data
            nameEl.textContent = item.dataset.name || 'Objet inconnu';
            typeEl.textContent = item.dataset.type || '';
            descEl.innerHTML = item.dataset.description || '';

            // Parse and display stats
            statsEl.innerHTML = '';
            try {
                const statsRaw = item.dataset.stats || '{}';
                const stats = typeof statsRaw === 'string' ? JSON.parse(statsRaw) : statsRaw;
                const enchantBonuses = stats.enchantment_bonuses || {};

                for (const [key, value] of Object.entries(stats)) {
                    // Skip internal fields and non-numeric values
                    if (key === 'enchantment_bonuses' || key === 'rarity') continue;
                    if (typeof value !== 'number') continue;

                    const statRow = document.createElement('div');
                    statRow.className = 'tooltip-stat flex justify-between';
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                    // Check if this stat has an enchantment bonus - show total in green if enchanted
                    const bonus = enchantBonuses[key] || 0;
                    if (bonus > 0) {
                        statRow.innerHTML = `<span>${label}</span><span class="text-green-400">+${value}</span>`;
                    } else {
                        statRow.innerHTML = `<span>${label}</span><span>+${value}</span>`;
                    }
                    statsEl.appendChild(statRow);
                }
            } catch (err) {
                console.error('Error parsing stats', err, item.dataset.stats);
            }

            // Display enchantments if available
            if (enchantsEl) {
                enchantsEl.innerHTML = '';
                enchantsEl.classList.add('hidden');
                try {
                    const enchantments = JSON.parse(item.dataset.enchantments || '[]');
                    if (enchantments && enchantments.length > 0) {
                        enchantsEl.classList.remove('hidden');
                        enchantsEl.innerHTML = `
                            <p class="text-xs text-violet-400 font-bold mb-1">✨ Enchantements:</p>
                            ${enchantments.map(e => `
                                <div class="text-xs text-purple-300 flex items-center gap-1">
                                    <span class="text-violet-400">✦</span> ${e.name}
                                </div>
                            `).join('')}
                        `;
                    }
                } catch (err) {
                    // No enchantments or invalid data
                }
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
        var ctxEquip = document.getElementById('house-ctx-equip');
        if (ctxEquip) {
            ctxEquip.addEventListener('click', async () => {
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
        }

        // Deposit action
        var ctxDeposit = document.getElementById('house-ctx-deposit');
        if (ctxDeposit) {
            ctxDeposit.addEventListener('click', async () => {
                if (contextMenuTarget) {
                    const itemId = contextMenuTarget.dataset.inventoryId;
                    await this.depositItem(itemId);
                }
                menu.classList.add('hidden');
            });
        }

        // Withdraw action
        var ctxWithdraw = document.getElementById('house-ctx-withdraw');
        if (ctxWithdraw) {
            ctxWithdraw.addEventListener('click', async () => {
                if (contextMenuTarget) {
                    const itemId = contextMenuTarget.dataset.storageId;
                    await this.withdrawItem(itemId);
                }
                menu.classList.add('hidden');
            });
        }

        // Drop action
        var ctxDrop = document.getElementById('house-ctx-drop');
        if (ctxDrop) {
            ctxDrop.addEventListener('click', async () => {
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
                var noHouseEl = document.getElementById('workbench-no-house');
                var lockedEl = document.getElementById('workbench-locked');
                var interfaceEl = document.getElementById('workbench-interface');

                if (noHouseEl) noHouseEl.classList.add('hidden');
                if (lockedEl) lockedEl.classList.add('hidden');
                if (interfaceEl) interfaceEl.classList.add('hidden');

                if (!data.has_house) {
                    // No house - show message to buy a house
                    if (noHouseEl) noHouseEl.classList.remove('hidden');
                } else if (data.has_workbench) {
                    // Has workbench - show interface
                    if (interfaceEl) interfaceEl.classList.remove('hidden');
                    this.renderWorkbenchItems(data.items);
                    this.renderWorkbenchEnchantments(data.enchantments);
                } else {
                    // Has house but no workbench - show paywall
                    if (lockedEl) lockedEl.classList.remove('hidden');
                    document.getElementById('workbench-price').textContent = this.formatNumber(data.workbench_price);
                    document.getElementById('workbench-required-level').textContent = data.workbench_required_level;

                    // Update button state
                    const purchaseBtn = document.getElementById('purchase-workbench-btn');
                    const errorEl = document.getElementById('workbench-purchase-error');

                    if (purchaseBtn) {
                        const canAfford = this.playerGold >= data.workbench_price;
                        const hasLevel = data.player_level >= data.workbench_required_level;
                        purchaseBtn.disabled = !canAfford || !hasLevel;

                        if (errorEl) errorEl.classList.add('hidden');
                        if (!canAfford && errorEl) {
                            errorEl.textContent = 'Or insuffisant (' + this.formatNumber(this.playerGold) + ' / ' + this.formatNumber(data.workbench_price) + ')';
                            errorEl.classList.remove('hidden');
                        } else if (!hasLevel && errorEl) {
                            errorEl.textContent = 'Niveau insuffisant (' + data.player_level + ' / ' + data.workbench_required_level + ')';
                            errorEl.classList.remove('hidden');
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
            const isEnchanted = enchantCount > 0;

            // Merge base stats with instance stats for display
            let displayStats = {};
            try {
                const baseStats = typeof item.stats === 'string' ? JSON.parse(item.stats || '{}') : (item.stats || {});
                const instanceStats = typeof item.instance_stats === 'string' ? JSON.parse(item.instance_stats || '{}') : (item.instance_stats || {});
                displayStats = { ...baseStats, ...instanceStats };
                // Keep enchantment_bonuses for display, only remove rarity
                delete displayStats.rarity;
            } catch (e) {
                displayStats = {};
            }
            const statsJson = JSON.stringify(displayStats);

            return `
                <div class="workbench-item ${isEnchanted ? 'enchanted-item' : ''} rarity-${item.rarity || 'common'} cursor-pointer hover:ring-2 hover:ring-violet-500 transition-all relative"
                     data-inventory-id="${item.inventory_id}"
                     data-item-id="${item.item_id}"
                     data-name="${this.escapeHtml(item.name)}"
                     data-type="${item.slot_type || item.type}"
                     data-slot-type="${item.slot_type}"
                     data-stats='${this.escapeJsonAttr(statsJson)}'
                     data-stat-ranges='${this.escapeJsonAttr(typeof item.stat_ranges === 'string' ? item.stat_ranges : JSON.stringify(item.stat_ranges || {}))}'
                     data-description="${this.escapeHtml(item.description || '')}"
                     data-enchantments='${this.escapeJsonAttr(JSON.stringify(item.enchantments || []))}'>
                    <img src="/${item.icon}" alt="${this.escapeHtml(item.name)}" class="w-full h-full object-contain p-1 relative z-[2]" draggable="false">
                    ${isEnchanted ? `<span class="enchant-badge">${enchantCount}</span>` : ''}
                </div>
            `;
        }).join('');

        // Add click events and tooltips
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

        var imgEl = itemElement.querySelector('img');
        this.selectedWorkbenchItem = {
            inventoryId: itemElement.dataset.inventoryId,
            name: itemElement.dataset.name,
            type: itemElement.dataset.type,
            slotType: itemElement.dataset.slotType,
            stats: JSON.parse(itemElement.dataset.stats || '{}'),
            enchantments: JSON.parse(itemElement.dataset.enchantments || '[]'),
            icon: imgEl ? imgEl.src : ''
        };

        // Reset selected enchantment when changing item
        this.selectedEnchantment = null;
        document.querySelectorAll('.enchantment-item').forEach(i => i.classList.remove('ring-2', 'ring-violet-500'));
        const enchantSlot = document.getElementById('workbench-enchant-slot');
        enchantSlot.innerHTML = '<span class="text-gray-600 text-3xl">✨</span>';
        enchantSlot.classList.remove('border-violet-500');

        // Update item slot visual
        const itemSlot = document.getElementById('workbench-item-slot');
        itemSlot.innerHTML = '<img src="' + this.selectedWorkbenchItem.icon + '" alt="' + this.selectedWorkbenchItem.name + '" class="w-full h-full object-contain p-1">';

        // Show item info (only enchantments, no name)
        const itemInfo = document.getElementById('workbench-item-info');
        const enchantsContainer = document.getElementById('workbench-item-enchants');

        var self = this;
        if (this.selectedWorkbenchItem.enchantments.length > 0) {
            itemInfo.classList.remove('hidden');
            enchantsContainer.innerHTML = this.selectedWorkbenchItem.enchantments.map(function (e) {
                return '<div class="flex items-center justify-between text-xs bg-gray-900/50 rounded px-2 py-1">' +
                    '<span class="text-purple-300">✨ ' + e.name + '</span>' +
                    '<button class="text-red-400 hover:text-red-300 text-xs px-1" data-enchant-id="' + e.id + '" onclick="if(window.houseManager)window.houseManager.removeEnchantment(' + e.id + ')" title="Retirer (50🪙)">✕</button>' +
                    '</div>';
            }).join('');
        } else {
            itemInfo.classList.add('hidden');
        }

        // Filter compatible enchantments and disable already applied ones
        this.filterCompatibleEnchantments(this.selectedWorkbenchItem.slotType);

        // Hide result preview since no enchantment is selected
        document.getElementById('workbench-result-preview').classList.add('hidden');

        playSound('click');
    }

    /**
     * Filter and highlight compatible enchantments
     */
    filterCompatibleEnchantments(slotType) {
        const enchantmentsList = document.getElementById('workbench-enchantments-list');
        const items = enchantmentsList.querySelectorAll('.enchantment-item');

        // Get list of already applied enchantment IDs
        var appliedEnchantmentIds = [];
        if (this.selectedWorkbenchItem && this.selectedWorkbenchItem.enchantments) {
            appliedEnchantmentIds = this.selectedWorkbenchItem.enchantments.map(function (e) { return String(e.enchantment_id); });
        }

        items.forEach(function (item) {
            const enchantId = item.dataset.enchantmentId;
            const compatibleSlots = JSON.parse(item.dataset.compatibleSlots || '[]');
            const isCompatible = compatibleSlots.length === 0 || compatibleSlots.includes(slotType);
            const isAlreadyApplied = appliedEnchantmentIds.includes(enchantId);

            // Remove all state classes first
            item.classList.remove('opacity-50', 'pointer-events-none', 'cursor-pointer', 'already-applied');

            if (isAlreadyApplied) {
                // Already applied - show as disabled with special styling
                item.classList.add('opacity-40', 'pointer-events-none', 'already-applied');
                // Add visual indicator
                if (!item.querySelector('.applied-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'applied-badge text-xs text-green-400 ml-2';
                    badge.textContent = '✓ Appliqué';
                    var flexEl = item.querySelector('.flex');
                    if (flexEl) flexEl.appendChild(badge);
                }
            } else if (isCompatible) {
                item.classList.add('cursor-pointer');
                // Remove applied badge if exists
                var badge = item.querySelector('.applied-badge');
                if (badge) badge.remove();
            } else {
                item.classList.add('opacity-50', 'pointer-events-none');
                var badge = item.querySelector('.applied-badge');
                if (badge) badge.remove();
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
                        <span class="text-amber-400 text-sm">${this.formatNumber(ench.cost)} 🪙</span>
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
        document.getElementById('workbench-enchant-label').classList.add('invisible');

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
                document.getElementById('workbench-enchant-label').classList.remove('invisible');
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

    /**
     * Setup tooltip for an item
     * @param {HTMLElement} item - Item element
     */
    setupItemTooltip(item) {
        let tooltip = document.getElementById('house-item-tooltip');
        if (!tooltip) {
            // Create tooltip if not exists
            tooltip = document.createElement('div');
            tooltip.id = 'house-item-tooltip';
            tooltip.className = 'fixed hidden bg-gray-900 border border-gray-600 p-3 rounded shadow-xl max-w-xs text-sm text-gray-300 z-[9999] pointer-events-none';
            tooltip.innerHTML = `
                <div class="tooltip-header">
                    <div id="house-tooltip-name" class="font-bold text-violet-400 text-base"></div>
                    <div id="house-tooltip-type" class="text-xs text-gray-500 uppercase"></div>
                </div>
                <div id="house-tooltip-stats" class="space-y-1 mb-2"></div>
                <div id="house-tooltip-enchants" class="hidden border-t border-violet-700/50 pt-2 mt-2"></div>
                <div id="house-tooltip-desc" class="italic text-gray-400 text-xs border-t border-gray-700 pt-2"></div>
            `;
            document.body.appendChild(tooltip);
        }

        const nameEl = document.getElementById('house-tooltip-name');
        const typeEl = document.getElementById('house-tooltip-type');
        const statsEl = document.getElementById('house-tooltip-stats');
        const descEl = document.getElementById('house-tooltip-desc');
        const enchantsEl = document.getElementById('house-tooltip-enchants');

        const showTooltip = (e) => {
            // Don't show if dragging
            if (item.classList.contains('dragging')) return;

            // Populate Data
            nameEl.textContent = item.dataset.name;
            typeEl.textContent = item.dataset.type;
            descEl.innerHTML = item.dataset.description;

            // Parse and display stats
            statsEl.innerHTML = '';
            try {
                // Try-catch for JSON parsing errors
                let stats = {};
                try {
                    stats = JSON.parse(item.dataset.stats || '{}');
                } catch (e) {
                    // If double encoded or similar issues
                    console.warn('Failed to parse stats once', item.dataset.stats);
                }

                const enchantBonuses = stats.enchantment_bonuses || {};

                // Filter out non-displayable keys
                const hasStats = Object.keys(stats).length > 0 && JSON.stringify(stats) !== '{}';

                if (hasStats) {
                    for (const [key, value] of Object.entries(stats)) {
                        if (key === 'enchantment_bonuses' || key === 'rarity' || key === 'enchantment_ids') continue;
                        if (typeof value !== 'number') continue; // only show numeric stats

                        const statRow = document.createElement('div');
                        statRow.className = 'flex justify-between items-center text-gray-300';
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                        const bonus = enchantBonuses[key] || 0;
                        if (bonus > 0) {
                            // Show base + bonus logic if we had base. But here 'value' is likely instance_stats (total).
                            // If instance_stats is total, we just show value. 
                            // To show breakdown: base = value - bonus.
                            // For simplicity: just show Value (green if bonus exists?)
                            statRow.innerHTML = `<span>${label}</span><span class="text-green-400">+${value}</span>`;
                        } else {
                            statRow.innerHTML = `<span>${label}</span><span>+${value}</span>`;
                        }
                        statsEl.appendChild(statRow);
                    }
                }
            } catch (err) {
                console.error('Error parsing stats for tooltip', err);
            }

            // Display enchantments
            enchantsEl.innerHTML = '';
            enchantsEl.classList.add('hidden');
            try {
                const enchantments = JSON.parse(item.dataset.enchantments || '[]');
                if (enchantments && enchantments.length > 0) {
                    enchantsEl.classList.remove('hidden');
                    enchantsEl.innerHTML = `
                        <p class="text-xs text-violet-400 font-bold mb-1">✨ Enchantements:</p>
                        ${enchantments.map(enc => `
                            <div class="text-xs text-purple-300 flex items-center gap-1">
                                <span class="text-violet-400">✦</span> ${enc.name}
                            </div>
                        `).join('')}
                    `;
                }
            } catch (err) { }

            tooltip.classList.remove('hidden');
            updatePosition(e);
        };

        const updatePosition = (e) => {
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
        };

        const hideTooltip = () => {
            tooltip.classList.add('hidden');
        };

        item.addEventListener('mouseenter', showTooltip);
        item.addEventListener('mousemove', updatePosition);
        item.addEventListener('mouseleave', hideTooltip);
    }

    /**
     * Setup context menu for an item (Stub for now)
     */
    setupItemContextMenu(item) {
        // TODO: Implement context menu for house items (Withdraw/Deposit/Sell)
        item.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            // Placeholder: maybe simple double click does something?
            // For now, prevent default browser menu is good enough.
        });

        // Double click to transfer
        item.addEventListener('dblclick', async () => {
            const location = item.dataset.location;
            if (location === 'storage') {
                await this.withdrawItem(item.dataset.id);
            } else if (location === 'inventory') {
                await this.depositItem(item.dataset.id);
            }
        });
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
