/**
 * Shop Module
 * Handles NPC trading interface
 */

import { showToast } from './toast.js';
import { setupTooltip } from './inventory.js';
import { playSound } from './soundManager.js';

let currentShopNPC = null;
let currentShopName = null; // preferred shop name to display (may differ from NPC name)
let playerGold = 0;

/**
 * Open Shop Modal
 */
export function openShop(npcId, shopName = null) {
    // Close NPC modal first if open
    if (window.closeNPCModal) {
        window.closeNPCModal();
    }

    fetch(`/game/shop/${npcId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentShopNPC = data.npc;
                // Determine shop display name priority:
                // 1. explicit shopName passed from caller
                // 2. shop_name returned by backend in npc object
                // 3. previously stored currentShopName (preserve across refreshes)
                // 4. fallback to NPC name (handled in renderShop)
                if (shopName) {
                    currentShopName = shopName;
                } else if (data.npc && data.npc.shop_name) {
                    currentShopName = data.npc.shop_name;
                }

                playerGold = data.player_gold;
                renderShop(data);
                document.getElementById('shop-modal').classList.remove('hidden');
                playSound('open');
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
    console.log(data);

    const merchantInventory = data.merchant_inventory;
    const playerInventory = data.player_inventory;

    // Update Shop Title with preferred shop name (shop_name) or fallback to NPC name
    const shopTitle = document.getElementById('shop-title');
    if (shopTitle && currentShopNPC) {
        const displayName = currentShopName || currentShopNPC.shop_name || currentShopNPC.name || '';
        shopTitle.textContent = displayName ? `🪙 Boutique - ${displayName}` : '🪙 Boutique';
    }

    // Update Gold Display
    updateGoldDisplay();

    // 1. Render Merchant Inventory (Left Column)
    const merchantList = document.getElementById('shop-merchant-list');
    merchantList.innerHTML = '';

    if (merchantInventory.length === 0) {
        merchantList.innerHTML = '<div class="text-gray-500 text-center italic p-4 w-full">Ce marchand n\'a rien à vendre.</div>';
    } else {
        merchantInventory.forEach(item => {
            const itemEl = createShopSlot(item, 'buy');
            merchantList.appendChild(itemEl);
        });
    }

    // 2. Render Player Inventory (Right Column)
    const inventoryList = document.getElementById('shop-inventory-list');
    inventoryList.innerHTML = '';

    if (playerInventory.inventory) {
        playerInventory.inventory.forEach(item => {
            const itemEl = createShopSlot(item, 'sell');
            inventoryList.appendChild(itemEl);
        });
    }
}

/**
 * Create Shop Slot Element (Grid Style)
 * @param {Object} item 
 * @param {string} action 'buy' | 'sell'
 */
function createShopSlot(item, action) {
    const enchantments = item.enchantments || [];
    const enchantCount = enchantments.length;
    const isEnchanted = enchantCount > 0;

    const slot = document.createElement('div');
    slot.className = `w-16 h-16 slot rounded-lg flex items-center justify-center relative bg-gray-800 hover:bg-gray-700 transition-colors border border-gray-600 cursor-pointer ${isEnchanted ? 'enchanted-item' : ''}`;

    // Add visual indicator for action
    if (action === 'buy') {
        slot.classList.add('hover:border-green-500');
    } else {
        slot.classList.add('hover:border-red-500');
    }

    const img = document.createElement('img');
    img.src = '/' + (item.icon || 'assets/images/items/default.png');
    img.className = 'w-12 h-12 object-contain item-icon relative z-[2]';

    // Add data attributes for tooltip
    img.dataset.name = item.name;
    img.dataset.type = item.type;
    img.dataset.description = item.description;

    // Handle stats - ensure it's a proper JSON string
    let statsString = '{}';
    if (item.stats) {
        if (typeof item.stats === 'string') {
            statsString = item.stats;
        } else if (typeof item.stats === 'object') {
            statsString = JSON.stringify(item.stats);
        }
    }
    img.dataset.stats = statsString;

    // Handle stat ranges
    let statRangesString = '{}';
    if (item.stat_ranges) {
        if (typeof item.stat_ranges === 'string') {
            statRangesString = item.stat_ranges;
        } else if (typeof item.stat_ranges === 'object') {
            statRangesString = JSON.stringify(item.stat_ranges);
        }
    }
    img.dataset.statRanges = statRangesString;

    img.dataset.enchantments = JSON.stringify(enchantments);

    // Add price info to tooltip data (custom handling or append to description)
    // We'll use a custom tooltip handler or modify the description temporarily for the tooltip
    // But better: let's use the existing setupTooltip but inject the price info into the description or a new attribute

    let priceText = '';
    if (action === 'buy') {
        const price = Math.floor(item.buy_price || item.price);
        priceText = `Prix d'achat: <span class="text-yellow-400 font-bold">${price} 🪙</span>`;
        slot.onclick = () => buyItem(item.id);
    } else {
        const price = Math.floor(item.sell_price || (item.price * 0.15));
        priceText = `Prix de vente: <span class="text-yellow-400 font-bold">${price} 🪙</span>`;
        slot.onclick = () => sellItem(item.id);
    }

    // Append price to description for the tooltip to pick it up
    // The existing setupTooltip reads data-description. 
    // We can append the price HTML to it.
    const originalDesc = item.description || '';
    img.dataset.description = originalDesc + (originalDesc ? '<br><br>' : '') + priceText;

    slot.appendChild(img);

    // Add enchantment badge if enchanted
    if (isEnchanted) {
        const badge = document.createElement('span');
        badge.className = 'enchant-badge';
        badge.textContent = enchantCount;
        slot.appendChild(badge);
    }

    // Setup tooltip
    setupTooltip(img);

    return slot;
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
                playerGold = Math.floor(data.new_gold);
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
                playerGold += Math.floor(data.gold_earned); // Or use returned total if available
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
        el.textContent = `${Math.floor(playerGold)} 🪙`;
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
            playSound('close');
        };
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            playSound('close');
        }
    });
}

if (typeof window !== 'undefined') {
    window.openShop = openShop;
}
