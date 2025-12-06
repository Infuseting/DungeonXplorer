/**
 * Inventory Module
 * Handles all inventory-related functionality including drag-and-drop, tooltips, and equipment
 */

import { showToast } from './toast.js';
import { playSound } from './soundManager.js';

// State
let draggedItem = null;
let draggedItemData = null;
let selectedItem = null; // For mobile click-based interaction
let longPressTimer = null;

const CELL_SIZE = 40;
const LONG_PRESS_DURATION = 500; // ms

/**
 * Check if device is mobile
 */
function isMobile() {
    return window.innerWidth < 1024;
}

/**
 * Initialize the inventory system
 */
export function initInventory() {
    // Initialize all item icons
    document.querySelectorAll('.item-icon').forEach(item => {
        if (isMobile()) {
            setupMobileInteraction(item);
        } else {
            setupDraggable(item);
            setupTooltip(item);
        }
    });

    // Setup drop zones (desktop only)
    if (!isMobile()) {
        setupEquipmentSlots();
        setupInventoryGrid();
    } else {
        setupMobileSlotInteraction();
    }

    // Setup modal controls
    setupModalControls();
}

/**
 * Setup tooltip for an item
 * @param {HTMLElement} item - Item element
 */
export function setupTooltip(item) {
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
        descEl.innerHTML = item.dataset.description;

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
 * Setup mobile interaction for an item (click-based)
 * @param {HTMLElement} item - Item element
 */
function setupMobileInteraction(item) {
    // Long press for tooltip
    item.addEventListener('touchstart', (e) => {
        longPressTimer = setTimeout(() => {
            showMobileTooltip(item, e.touches[0]);
        }, LONG_PRESS_DURATION);
    });

    item.addEventListener('touchend', (e) => {
        clearTimeout(longPressTimer);

        // If it was a quick tap (not long press), handle selection
        if (!document.getElementById('item-tooltip').classList.contains('hidden')) {
            // Tooltip is showing, hide it
            document.getElementById('item-tooltip').classList.add('hidden');
        } else {
            // Handle item selection/deselection
            handleMobileItemClick(item);
        }
    });

    item.addEventListener('touchmove', () => {
        clearTimeout(longPressTimer);
    });

    // Also support regular click for testing on desktop
    item.addEventListener('click', (e) => {
        if (isMobile()) {
            e.preventDefault();
            handleMobileItemClick(item);
        }
    });
}

/**
 * Show tooltip on mobile (long press)
 * @param {HTMLElement} item - Item element
 * @param {Touch} touch - Touch object
 */
function showMobileTooltip(item, touch) {
    const tooltip = document.getElementById('item-tooltip');
    const nameEl = document.getElementById('tooltip-name');
    const typeEl = document.getElementById('tooltip-type');
    const statsEl = document.getElementById('tooltip-stats');
    const descEl = document.getElementById('tooltip-desc');

    // Populate Data
    nameEl.textContent = item.dataset.name;
    typeEl.textContent = item.dataset.type;
    descEl.innerHTML = item.dataset.description;

    // Parse and display stats
    statsEl.innerHTML = '';
    try {
        const stats = JSON.parse(item.dataset.stats);
        for (const [key, value] of Object.entries(stats)) {
            const statRow = document.createElement('div');
            statRow.className = 'tooltip-stat';
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            statRow.innerHTML = `<span>${label}</span><span>${value}</span>`;
            statsEl.appendChild(statRow);
        }
    } catch (e) {
        console.error('Error parsing stats', e);
    }

    // Position tooltip
    const offset = 15;
    let left = touch.clientX + offset;
    let top = touch.clientY + offset;

    // Boundary checks
    if (left + tooltip.offsetWidth > window.innerWidth) {
        left = touch.clientX - tooltip.offsetWidth - offset;
    }
    if (top + tooltip.offsetHeight > window.innerHeight) {
        top = touch.clientY - tooltip.offsetHeight - offset;
    }

    tooltip.style.left = left + 'px';
    tooltip.style.top = top + 'px';
    tooltip.classList.remove('hidden');

    playSound('click');
}

/**
 * Handle mobile item click (selection)
 * @param {HTMLElement} item - Item element
 */
function handleMobileItemClick(item) {
    if (selectedItem === item) {
        // Deselect
        selectedItem.classList.remove('selected');
        selectedItem = null;
        playSound('click');
    } else {
        // Select
        if (selectedItem) {
            selectedItem.classList.remove('selected');
        }
        selectedItem = item;
        item.classList.add('selected');
        playSound('click');
    }
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

        // Play pickup sound
        playSound('itemPickup');

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
        grayedImg.src = '/' + iconPath;
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
 * Setup mobile slot interaction (click to equip/unequip)
 */
function setupMobileSlotInteraction() {
    // Equipment slots
    document.querySelectorAll('.slot[data-slot]').forEach(slot => {
        slot.addEventListener('click', (e) => {
            e.preventDefault();
            handleMobileSlotClick(slot);
        });
    });

    // Inventory grid - click to unequip
    const inventoryGrid = document.getElementById('inventory-grid');
    if (inventoryGrid) {
        inventoryGrid.addEventListener('click', (e) => {
            // If an item is selected and user clicks on empty inventory space, unequip it
            if (selectedItem && !e.target.closest('.item-icon')) {
                const itemId = selectedItem.dataset.id;
                moveItem(itemId, 'inventory');
                selectedItem.classList.remove('selected');
                selectedItem = null;
            }
        });
    }
}

/**
 * Handle mobile slot click (equip or unequip)
 * @param {HTMLElement} slot - Slot element
 */
function handleMobileSlotClick(slot) {
    const slotName = slot.dataset.slot;
    const equippedItem = slot.querySelector('.item-icon');

    if (selectedItem) {
        // Equip the selected item
        const itemId = selectedItem.dataset.id;
        moveItem(itemId, 'equipped', slotName);
        selectedItem.classList.remove('selected');
        selectedItem = null;
    } else if (equippedItem) {
        // Unequip the item
        const itemId = equippedItem.dataset.id;
        moveItem(itemId, 'inventory');
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
 * Setup inventory grid drop zone
 */
function setupInventoryGrid() {
    const container = document.getElementById('inventory-grid'); // User requested drop zone on the wrapper
    if (!container) return;

    container.addEventListener('dragover', e => {
        e.preventDefault();
        if (!draggedItemData) return;
        container.classList.add('border-violet-500', 'bg-gray-800'); // Visual feedback
    });

    container.addEventListener('dragleave', e => {
        container.classList.remove('border-violet-500', 'bg-gray-800');
    });

    container.addEventListener('drop', e => {
        e.preventDefault();
        container.classList.remove('border-violet-500', 'bg-gray-800');
        if (!draggedItemData) return;

        // Move item to inventory
        moveItem(draggedItemData.id, 'inventory');
    });
}

/**
 * Move an item to a new location
 * @param {string} itemId - Item ID
 * @param {string} location - Target location ('equipped', 'inventory')
 * @param {string|null} slot - Target slot name (for equipped items)
 */
function moveItem(itemId, location, slot = null) {
    const targetContainer = getTargetContainer(location, slot);

    // Attempt to find the item element in the DOM to get its data
    // We prefer the draggedItem if it matches, otherwise find any instance
    let sourceElement = draggedItem;
    if (!sourceElement || sourceElement.dataset.id !== itemId) {
        sourceElement = document.querySelector(`.item-icon[data-id="${itemId}"]`);
    }

    if (!sourceElement) {
        console.error('Could not find source element for item', itemId);
        return;
    }

    // Capture data BEFORE any async operations or removals
    const itemData = {
        src: sourceElement.src,
        id: sourceElement.dataset.id,
        slotType: sourceElement.dataset.slotType,
        twoHanded: sourceElement.dataset.twoHanded,
        name: sourceElement.dataset.name,
        type: sourceElement.dataset.type,
        description: sourceElement.dataset.description,
        stats: sourceElement.dataset.stats,
        weight: sourceElement.dataset.weight
    };

    fetch('/game/inventory/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            itemId: itemId,
            location: location,
            slot: slot
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success: Update DOM dynamically
                if (targetContainer) {
                    // 1. ROBUST WIPE: Remove ALL instances of this item from the DOM
                    const allInstances = document.querySelectorAll(`.item-icon[data-id="${itemId}"]`);
                    allInstances.forEach(item => {
                        const parent = item.closest('.slot');
                        if (parent) {
                            if (parent.dataset.location === 'inventory') {
                                // Remove inventory slot entirely
                                parent.remove();
                            } else if (parent.dataset.slot) {
                                // Clear equipment slot
                                const label = parent.querySelector('.slot-label')?.textContent || '';
                                parent.innerHTML = '<span class="slot-label">' + label + '</span>';
                            }
                        } else {
                            // Just remove the item if it's orphaned or in a weird container
                            item.remove();
                        }
                    });

                    // 2. Add to Target Location
                    if (location === 'equipped') {
                        // Clear target container first (just to be safe)
                        const label = targetContainer.querySelector('.slot-label')?.textContent || '';
                        targetContainer.innerHTML = '<span class="slot-label">' + label + '</span>';

                        const img = document.createElement('img');
                        img.src = itemData.src;
                        img.className = 'w-full h-full object-contain item-icon p-1';
                        img.draggable = true;
                        img.dataset.id = itemData.id;
                        img.dataset.slotType = itemData.slotType;
                        img.dataset.twoHanded = itemData.twoHanded;
                        img.dataset.name = itemData.name;
                        img.dataset.type = itemData.type;
                        img.dataset.description = itemData.description;
                        img.dataset.stats = itemData.stats;
                        img.dataset.weight = itemData.weight;

                        targetContainer.appendChild(img);
                        setupDraggable(img);
                        setupTooltip(img);

                        // Handle Two-Handed Weapons Visuals
                        const isTwoHanded = itemData.twoHanded === '1';
                        if (isTwoHanded) {
                            let otherSlotName = null;
                            if (slot === 'main_hand') otherSlotName = 'off_hand';
                            else if (slot === 'off_hand') otherSlotName = 'main_hand';

                            if (otherSlotName) {
                                const otherSlot = document.querySelector(`.slot[data-slot="${otherSlotName}"]`);
                                if (otherSlot) {
                                    // Clear other slot
                                    otherSlot.innerHTML = '<span class="slot-label">' + otherSlot.querySelector('.slot-label')?.textContent + '</span>';

                                    // Add ghost image
                                    const ghostImg = img.cloneNode(true);
                                    ghostImg.classList.add('opacity-30', 'pointer-events-none');
                                    ghostImg.style.filter = 'grayscale(100%)';
                                    ghostImg.removeAttribute('draggable');
                                    // Ensure ghost has ID so it can be wiped later
                                    ghostImg.dataset.id = itemData.id;

                                    const borderDiv = document.createElement('div');
                                    borderDiv.className = 'absolute inset-0 border-2 border-red-500 rounded-lg pointer-events-none';

                                    otherSlot.appendChild(ghostImg);
                                    otherSlot.appendChild(borderDiv);
                                }
                            }
                        }
                    } else if (location === 'inventory') {
                        // Add to inventory grid
                        const inventoryContainer = document.getElementById('inventory-container');
                        const newSlot = document.createElement('div');
                        newSlot.className = 'w-16 h-16 slot rounded-lg flex items-center justify-center relative bg-gray-800 hover:bg-gray-700 transition-colors';
                        newSlot.dataset.location = 'inventory';
                        newSlot.dataset.inventoryId = itemId;

                        const img = document.createElement('img');
                        img.src = itemData.src;
                        img.className = 'w-12 h-12 object-contain item-icon';
                        img.draggable = true;
                        img.dataset.id = itemData.id;
                        img.dataset.slotType = itemData.slotType;
                        img.dataset.twoHanded = itemData.twoHanded;
                        img.dataset.name = itemData.name;
                        img.dataset.type = itemData.type;
                        img.dataset.description = itemData.description;
                        img.dataset.stats = itemData.stats;
                        img.dataset.weight = itemData.weight;

                        newSlot.appendChild(img);
                        inventoryContainer.appendChild(newSlot);

                        setupDraggable(img);
                        setupTooltip(img);
                    }
                }
                playSound('itemPickup');
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
 * @returns {HTMLElement|null} Container element
 */
function getTargetContainer(location, slot) {
    if (location === 'equipped') {
        return document.querySelector(`.slot[data-slot="${slot}"]`);
    } else if (location === 'inventory') {
        return document.getElementById('inventory-container');
    }
    return null;
}



/**
 * Setup inventory modal controls
 */
function setupModalControls() {
    const inventoryModal = document.getElementById('inventory-modal');
    const inventoryToggleBtn = document.getElementById('inventory-toggle');
    const inventoryBackdrop = document.getElementById('inventory-backdrop');
    const inventoryCloseBtn = document.getElementById('inventory-close-btn');

    function toggleInventory() {
        const isHidden = inventoryModal.classList.contains('hidden');
        if (isHidden) {
            inventoryModal.classList.remove('hidden');
            playSound('open');
        } else {
            inventoryModal.classList.add('hidden');
            playSound('close');
            // Clear selection on close
            if (selectedItem) {
                selectedItem.classList.remove('selected');
                selectedItem = null;
            }
        }
    }

    inventoryToggleBtn.addEventListener('click', toggleInventory);
    inventoryBackdrop.addEventListener('click', toggleInventory);

    // Mobile close button
    if (inventoryCloseBtn) {
        inventoryCloseBtn.addEventListener('click', toggleInventory);
    }

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
        if (isMobile()) {
            setupMobileInteraction(item);
        } else {
            setupDraggable(item);
            setupTooltip(item);
        }
    });
}
