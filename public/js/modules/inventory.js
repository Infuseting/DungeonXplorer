/**
 * Inventory Module
 * Handles all inventory-related functionality including drag-and-drop, tooltips, and equipment
 */

import { showToast } from './toast.js';

// State
let draggedItem = null;
let draggedItemData = null;

const CELL_SIZE = 40;

/**
 * Initialize the inventory system
 */
export function initInventory() {
    // Initialize all item icons
    document.querySelectorAll('.item-icon').forEach(item => {
        setupDraggable(item);
        setupTooltip(item);
    });

    // Setup drop zones
    setupEquipmentSlots();
    setupPocketSlots();
    setupBackpackGrid();

    // Setup modal controls
    setupModalControls();
}

/**
 * Setup tooltip for an item
 * @param {HTMLElement} item - Item element
 */
function setupTooltip(item) {
    const tooltip = document.getElementById('item-tooltip');
    const nameEl = document.getElementById('tooltip-name');
    const typeEl = document.getElementById('tooltip-type');
    const statsEl = document.getElementById('tooltip-stats');
    const descEl = document.getElementById('tooltip-desc');

    item.addEventListener('mouseenter', e => {
        if (draggedItem) return; // Don't show tooltip while dragging

        // Populate Data
        nameEl.textContent = item.dataset.name;
        typeEl.textContent = item.dataset.type;
        descEl.textContent = item.dataset.description;

        // Parse and display stats
        statsEl.innerHTML = '';
        try {
            const stats = JSON.parse(item.dataset.stats);
            for (const [key, value] of Object.entries(stats)) {
                const statRow = document.createElement('div');
                statRow.className = 'tooltip-stat';
                // Format key (e.g., capacity_width -> Capacity Width)
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                statRow.innerHTML = `<span>${label}</span><span>${value}</span>`;
                statsEl.appendChild(statRow);
            }
        } catch (e) {
            console.error('Error parsing stats', e);
        }

        tooltip.classList.remove('hidden');
    });

    item.addEventListener('mousemove', e => {
        if (draggedItem) return;

        // Position tooltip near cursor but not under it
        const offset = 15;
        let left = e.clientX + offset;
        let top = e.clientY + offset;

        // Boundary checks (keep on screen)
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
 * Setup draggable functionality for an item
 * @param {HTMLElement} item - Item element
 */
function setupDraggable(item) {
    // Ctrl+Click to quick equip
    item.addEventListener('click', e => {
        if (e.ctrlKey && item.dataset.slotType && item.dataset.slotType !== 'none') {
            e.preventDefault();
            quickEquipItem(item.dataset.id);
        }
    });

    item.addEventListener('dragstart', e => {
        draggedItem = e.target;
        // Store item data
        draggedItemData = {
            id: e.target.dataset.id,
            width: parseInt(e.target.dataset.width || 1),
            height: parseInt(e.target.dataset.height || 1),
            slotType: e.target.dataset.slotType
        };
        e.dataTransfer.setData('text/plain', e.target.dataset.id);
        e.dataTransfer.effectAllowed = 'move';
        setTimeout(() => e.target.classList.add('opacity-50'), 0);

        // Highlight valid drop zones
        highlightValidSlots(draggedItemData.slotType);
    });

    item.addEventListener('dragend', e => {
        e.target.classList.remove('opacity-50');
        draggedItem = null;
        draggedItemData = null;
        // Remove all highlights
        document.querySelectorAll('.drag-over, .valid-drop-zone').forEach(el => {
            el.classList.remove('drag-over', 'valid-drop-zone');
        });
    });
}

/**
 * Highlight valid equipment slots for an item type
 * @param {string} itemSlotType - Type of item slot
 */
function highlightValidSlots(itemSlotType) {
    if (!itemSlotType || itemSlotType === 'none') return;

    // Map item types to valid slots
    const slotMapping = {
        'head': ['head'],
        'shoulders': ['shoulders'],
        'amulet': ['amulet'],
        'chest': ['chest'],
        'belt': ['belt'],
        'legs': ['legs'],
        'boots': ['boots'],
        'ring': ['ring_1', 'ring_2'],
        'main_hand': ['main_hand', 'off_hand'], // Weapons can go in either hand
        'off_hand': ['off_hand'],
        'gloves': ['gloves'],
        'bracers': ['bracers'],
        'backpack': ['backpack']
    };

    const validSlots = slotMapping[itemSlotType] || [];
    validSlots.forEach(slotName => {
        const slot = document.querySelector(`.slot[data-slot="${slotName}"]`);
        if (slot) {
            slot.classList.add('valid-drop-zone');
        }
    });
}

/**
 * Quick equip an item (Ctrl+Click)
 * @param {string} itemId - Item ID
 */
function quickEquipItem(itemId) {
    // Find the item element
    const itemElement = document.querySelector(`.item-icon[data-id="${itemId}"]`);
    if (!itemElement) return;

    // Get item slot type to determine target slot
    const slotType = itemElement.dataset.slotType;

    fetch('/game/inventory/equip', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ itemId: itemId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Item equipped successfully');
                const slotMapping = {
                    'head': 'head',
                    'shoulders': 'shoulders',
                    'amulet': 'amulet',
                    'chest': 'chest',
                    'belt': 'belt',
                    'legs': 'legs',
                    'boots': 'boots',
                    'ring': 'ring_1', // Default to first ring slot
                    'main_hand': 'main_hand',
                    'off_hand': 'off_hand',
                    'gloves': 'gloves',
                    'bracers': 'bracers',
                    'backpack': 'backpack'
                };

                const targetSlot = slotMapping[slotType];
                if (targetSlot) {
                    // Move item to equipment slot
                    const targetContainer = document.querySelector(`.slot[data-slot="${targetSlot}"]`);
                    if (targetContainer) {
                        // Remove from old parent
                        if (itemElement.parentElement) {
                            itemElement.parentElement.removeChild(itemElement);
                        }

                        // Add to equipment slot
                        targetContainer.appendChild(itemElement);

                        // Update styling for equipped item
                        itemElement.className = 'w-full h-full object-contain item-icon p-1';

                        // Re-setup draggable and tooltip
                        setupDraggable(itemElement);
                        setupTooltip(itemElement);

                        // Handle two-handed weapons
                        if (data.two_handed) {
                            handleTwoHandedWeapon(data.slot_name, data.icon);
                        }
                    }
                }
            } else {
                showToast(data.message || 'Impossible d\'équiper l\'objet', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur de communication avec le serveur', 'error');
        });
}

/**
 * Handle two-handed weapon visual effects
 * @param {string} equippedSlot - Slot where weapon was equipped
 * @param {string} iconPath - Path to weapon icon
 */
function handleTwoHandedWeapon(equippedSlot, iconPath) {
    // Determine the opposite slot
    const oppositeSlot = (equippedSlot === 'main_hand') ? 'off_hand' : 'main_hand';
    const oppositeContainer = document.querySelector(`.slot[data-slot="${oppositeSlot}"]`);

    if (oppositeContainer) {
        // Clear the opposite slot
        const existingItems = oppositeContainer.querySelectorAll('.item-icon, img');
        existingItems.forEach(item => item.remove());

        const existingBorders = oppositeContainer.querySelectorAll('.border-red-500');
        existingBorders.forEach(border => border.remove());

        // Add grayed-out image
        const grayedImg = document.createElement('img');
        grayedImg.src = '/'+ iconPath;
        grayedImg.className = 'w-full h-full object-contain p-1 opacity-30 pointer-events-none';
        grayedImg.style.filter = 'grayscale(100%)';

        // Add red border overlay
        const redBorder = document.createElement('div');
        redBorder.className = 'absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none';

        oppositeContainer.appendChild(grayedImg);
        oppositeContainer.appendChild(redBorder);
    }
}

/**
 * Clear two-handed weapon visual effects
 * @param {string} fromSlot - Slot to clear from
 */
function clearTwoHandedWeaponVisual(fromSlot) {
    // Determine the opposite slot
    const oppositeSlot = (fromSlot === 'main_hand') ? 'off_hand' : 'main_hand';
    const oppositeContainer = document.querySelector(`.slot[data-slot="${oppositeSlot}"]`);

    if (oppositeContainer) {
        // Remove grayed-out images (those without data-id, which are the visual indicators)
        const grayedImages = oppositeContainer.querySelectorAll('img.opacity-30');
        grayedImages.forEach(img => img.remove());

        // Remove red borders
        const redBorders = oppositeContainer.querySelectorAll('.border-red-500');
        redBorders.forEach(border => border.remove());
    }
}

/**
 * Setup equipment slot drop zones
 */
function setupEquipmentSlots() {
    document.querySelectorAll('.slot[data-slot]').forEach(slot => {
        slot.addEventListener('dragover', e => {
            e.preventDefault();
            if (!draggedItemData) return;

            // Check slot type compatibility
            const targetSlot = slot.dataset.slot;
            slot.classList.add('drag-over');
        });

        slot.addEventListener('dragleave', e => {
            slot.classList.remove('drag-over');
        });

        slot.addEventListener('drop', e => {
            e.preventDefault();
            slot.classList.remove('drag-over');
            if (!draggedItemData) return;

            moveItem(draggedItemData.id, 'equipped', slot.dataset.slot);
        });
    });
}

/**
 * Setup pocket slot drop zones
 */
function setupPocketSlots() {
    document.querySelectorAll('.slot[data-location="pockets"]').forEach(slot => {
        slot.addEventListener('dragover', e => {
            e.preventDefault();
            slot.classList.add('drag-over');
        });

        slot.addEventListener('dragleave', e => {
            slot.classList.remove('drag-over');
        });

        slot.addEventListener('drop', e => {
            e.preventDefault();
            slot.classList.remove('drag-over');
            if (!draggedItemData) return;

            moveItem(draggedItemData.id, 'pockets', null, null, null, slot.dataset.index);
        });
    });
}

/**
 * Setup backpack grid drop zone
 */
function setupBackpackGrid() {
    const grid = document.getElementById('backpack-grid');

    grid.addEventListener('dragover', e => {
        e.preventDefault();
        if (!draggedItemData) return;

        const rect = grid.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
    });

    grid.addEventListener('drop', e => {
        e.preventDefault();
        if (!draggedItemData) return;

        const rect = grid.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const gridX = Math.floor(x / CELL_SIZE);
        const gridY = Math.floor(y / CELL_SIZE);

        moveItem(draggedItemData.id, 'backpack', null, gridX, gridY);
    });
}

/**
 * Move an item to a new location
 * @param {string} itemId - Item ID
 * @param {string} location - Target location ('equipped', 'pockets', 'backpack')
 * @param {string|null} slot - Target slot name
 * @param {number|null} x - Grid X position
 * @param {number|null} y - Grid Y position
 * @param {number|null} pocketIndex - Pocket index
 */
function moveItem(itemId, location, slot = null, x = null, y = null, pocketIndex = null) {
    const targetContainer = getTargetContainer(location, slot, pocketIndex);
    const itemElement = draggedItem; // Capture reference immediately

    fetch('/game/inventory/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            itemId: itemId,
            location: location,
            slot: slot || pocketIndex,
            x: x,
            y: y
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success: Update DOM
                if (itemElement) {
                    // Check if we're unequipping a two-handed weapon
                    const wasTwoHanded = itemElement.dataset.twoHanded === '1';
                    const fromSlot = itemElement.closest('.slot[data-slot]')?.dataset.slot;
                    const toSlot = location === 'equipped' ? slot : null;
                    updateItemPosition(itemElement, location, slot, x, y, pocketIndex);

                    // Clean up two-handed weapon visual if unequipping
                    if (wasTwoHanded && fromSlot && (fromSlot === 'main_hand' || fromSlot === 'off_hand')) {
                        clearTwoHandedWeaponVisual(fromSlot);
                    }
                    if (data.two_handed && toSlot && (toSlot === 'main_hand' || toSlot === 'off_hand')) {
                        handleTwoHandedWeapon(toSlot, data.icon);
                    }
                }
                showToast('Objet déplacé avec succès', 'success');
            } else {
                showToast(data.message || 'Impossible de déplacer l\'objet', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur de communication avec le serveur', 'error');
        });
}

/**
 * Get target container element
 * @param {string} location - Location type
 * @param {string|null} slot - Slot name
 * @param {number|null} pocketIndex - Pocket index
 * @returns {HTMLElement|null} Container element
 */
function getTargetContainer(location, slot, pocketIndex) {
    if (location === 'equipped') {
        return document.querySelector(`.slot[data-slot="${slot}"]`);
    } else if (location === 'pockets') {
        return document.querySelector(`.slot[data-location="pockets"][data-index="${pocketIndex}"]`);
    } else if (location === 'backpack') {
        return document.getElementById('backpack-grid');
    }
    return null;
}

/**
 * Update item position in DOM
 * @param {HTMLElement} itemElement - Item element
 * @param {string} location - Location type
 * @param {string|null} slot - Slot name
 * @param {number|null} x - Grid X position
 * @param {number|null} y - Grid Y position
 * @param {number|null} pocketIndex - Pocket index
 */
function updateItemPosition(itemElement, location, slot, x, y, pocketIndex) {
    const targetContainer = getTargetContainer(location, slot, pocketIndex);
    if (!targetContainer) return;

    // Remove from old parent
    if (itemElement.parentElement) {
        itemElement.parentElement.removeChild(itemElement);
    }

    targetContainer.appendChild(itemElement);

    // Reset styles
    itemElement.style.position = '';
    itemElement.style.left = '';
    itemElement.style.top = '';
    itemElement.style.width = '';
    itemElement.style.height = '';

    // Apply specific styles for Backpack Grid
    if (location === 'backpack') {
        itemElement.style.position = 'absolute';
        itemElement.style.left = (x * 40) + 'px';
        itemElement.style.top = (y * 40) + 'px';
        itemElement.style.width = (draggedItemData.width * 40) + 'px';
        itemElement.style.height = (draggedItemData.height * 40) + 'px';
    }
    // Apply styles for Pockets
    else if (location === 'pockets') {
        itemElement.className = 'w-12 h-12 object-contain item-icon';
    }
    // Apply styles for Equipped slots - fill the entire slot
    else if (location === 'equipped') {
        itemElement.className = 'w-full h-full object-contain item-icon p-1';
    }
}

/**
 * Setup inventory modal controls
 */
function setupModalControls() {
    const inventoryModal = document.getElementById('inventory-modal');
    const inventoryToggleBtn = document.getElementById('inventory-toggle');
    const inventoryBackdrop = document.getElementById('inventory-backdrop');

    function toggleInventory() {
        const isHidden = inventoryModal.classList.contains('hidden');
        if (isHidden) {
            inventoryModal.classList.remove('hidden');
        } else {
            inventoryModal.classList.add('hidden');
        }
    }

    inventoryToggleBtn.addEventListener('click', toggleInventory);
    inventoryBackdrop.addEventListener('click', toggleInventory);

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !inventoryModal.classList.contains('hidden')) {
            toggleInventory();
        }
    });
}

/**
 * Refresh inventory items (re-initialize draggables and tooltips)
 */
export function refreshInventory() {
    document.querySelectorAll('.item-icon').forEach(item => {
        setupDraggable(item);
        setupTooltip(item);
    });
}
