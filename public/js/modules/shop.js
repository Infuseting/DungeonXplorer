/**
 * Shop Module
 * Handles NPC trading interface
 */

import { showToast } from './toast.js';

let currentShopNPC = null;
let playerGold = 0;

/**
 * Open Shop Modal
 */
export function openShop(npcId) {
    // Close NPC modal first if open
    if (window.closeNPCModal) {
        window.closeNPCModal();
    }

    fetch(`/game/shop/${npcId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentShopNPC = data.npc;
                playerGold = data.player_gold;
                renderShop(data);
                document.getElementById('shop-modal').classList.remove('hidden');
            } else {
                showToast(data.message || 'Impossible d\'ouvrir la boutique', 'error');
            }
        })
        .catch(error => {
            console.error('Error opening shop:', error);
            showToast('Erreur de communication', 'error');
        });
}

/**
 * Render Shop Interface
 */
function renderShop(data) {
    const merchantInventory = data.merchant_inventory;
    const playerInventory = data.player_inventory;

    // Update Gold Display
    updateGoldDisplay();

    // 1. Render Merchant Inventory (Left Column)
    const merchantList = document.getElementById('shop-merchant-list');
    merchantList.innerHTML = '';

    if (merchantInventory.length === 0) {
        merchantList.innerHTML = '<div class="text-gray-500 text-center italic p-4">Ce marchand n\'a rien à vendre.</div>';
    } else {
        merchantInventory.forEach(item => {
            const itemEl = createShopItemElement(item, 'buy');
            merchantList.appendChild(itemEl);
        });
    }

    // 2. Render Player Equipment (Middle Column)
    const equipmentList = document.getElementById('shop-equipment-list');
    equipmentList.innerHTML = '';
    // Flatten equipped items
    if (playerInventory.equipped) {
        Object.values(playerInventory.equipped).forEach(item => {
            // Can't sell equipped items usually, but let's show them read-only or allow unequip?
            // For now, just show them as "Equipped"
            const itemEl = createShopItemElement(item, 'equipped');
            equipmentList.appendChild(itemEl);
        });
    }

    // 3. Render Player Inventory (Right Column)
    const inventoryList = document.getElementById('shop-inventory-list');
    inventoryList.innerHTML = '';

    if (playerInventory.backpack) {
        playerInventory.backpack.forEach(item => {
            const itemEl = createShopItemElement(item, 'sell');
            inventoryList.appendChild(itemEl);
        });
    }
}

/**
 * Create Shop Item Element
 * @param {Object} item 
 * @param {string} action 'buy' | 'sell' | 'equipped'
 */
function createShopItemElement(item, action) {
    const el = document.createElement('div');
    el.className = 'flex items-center p-2 bg-gray-800 border border-gray-700 rounded mb-2 hover:bg-gray-750 transition-colors';

    // Icon
    const icon = document.createElement('img');
    icon.src = '/' + (item.icon || 'assets/images/items/default.png');
    icon.className = 'w-10 h-10 min-w-10 min-h-10 object-contain bg-gray-900 rounded border border-gray-600 mr-3';
    el.appendChild(icon);

    // Info
    const info = document.createElement('div');
    info.className = 'flex-1';

    const name = document.createElement('div');
    name.className = 'font-bold text-sm text-gray-200 truncate';
    name.textContent = item.name;
    info.appendChild(name);

    const price = document.createElement('div');
    price.className = 'text-xs text-yellow-400';

    if (action === 'buy') {
        price.textContent = `${item.buy_price || item.price} 🪙`;
    } else if (action === 'sell') {
        price.textContent = `${item.sell_price || Math.floor(item.price * 0.15)} 🪙`; // Fallback calculation if not provided
    } else {
        price.textContent = 'Équipé';
        price.className = 'text-xs text-gray-500';
    }
    info.appendChild(price);

    el.appendChild(info);

    // Action Button
    if (action === 'buy') {
        const btn = document.createElement('button');
        btn.className = 'px-3 py-1 bg-green-700 hover:bg-green-600 text-white text-xs rounded shadow';
        btn.textContent = 'Acheter';
        btn.onclick = () => buyItem(item.id);
        el.appendChild(btn);
    } else if (action === 'sell') {
        const btn = document.createElement('button');
        btn.className = 'px-3 py-1 bg-red-700 hover:bg-red-600 text-white text-xs rounded shadow';
        btn.textContent = 'Vendre';
        btn.onclick = () => sellItem(item.id); // This is the inventory ID
        el.appendChild(btn);
    }

    return el;
}

/**
 * Buy Item
 */
function buyItem(itemId) {
    fetch('/game/shop/buy', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            npc_id: currentShopNPC.id,
            item_id: itemId
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Objet acheté !', 'success');
                playerGold = data.new_gold;
                updateGoldDisplay();
                // Refresh shop to show updated inventory
                openShop(currentShopNPC.id);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => console.error(err));
}

/**
 * Sell Item
 */
function sellItem(inventoryItemId) {
    fetch('/game/shop/sell', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            npc_id: currentShopNPC.id,
            item_id: inventoryItemId
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Objet vendu !', 'success');
                playerGold += data.gold_earned; // Or use returned total if available
                updateGoldDisplay();
                // Refresh shop
                openShop(currentShopNPC.id);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => console.error(err));
}

function updateGoldDisplay() {
    const el = document.getElementById('shop-player-gold');
    if (el) {
        el.textContent = `${playerGold} 🪙`;
    }
}

/**
 * Initialize Shop Modal
 */
export function initShop() {
    const modal = document.getElementById('shop-modal');
    const closeBtn = document.getElementById('shop-close-btn');

    if (closeBtn) {
        closeBtn.onclick = () => {
            modal.classList.add('hidden');
        };
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    });
}

if (typeof window !== 'undefined') {
    window.openShop = openShop;
}
