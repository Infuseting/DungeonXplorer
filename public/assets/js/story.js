import { showToast } from '/js/modules/toast.js';
import { changeMusicCategory, initSoundManager } from '/js/modules/soundManager.js';

const storyState = {
    currentNode: null,
    nodeStatus: null,
    isLoading: false
};

document.addEventListener('DOMContentLoaded', async () => {
    await initSoundManager();
    changeMusicCategory('dungeon');
    loadCurrentNode();
});

async function loadCurrentNode() {
    if (storyState.isLoading) return;
    storyState.isLoading = true;

    try {
        const response = await fetch(`/story/current?story_id=${STORY_ID}`);
        const data = await response.json();

        if (data.error) {
            showToast(data.error, 'error');
            return;
        }

        storyState.currentNode = data.node;
        storyState.nodeStatus = data.status;
        storyState.fledMonsters = data.fled_monsters || []; // Store fled IDs
        renderNode();
    } catch (error) {
        console.error('Error loading node:', error);
        showToast('Erreur de chargement', 'error');
    } finally {
        storyState.isLoading = false;
    }
}

function renderNode() {
    const node = storyState.currentNode;
    if (!node) return;

    // Update Room Description
    const roomNameEl = document.getElementById('room-name');
    if (roomNameEl) roomNameEl.textContent = node.name;

    const roomDescEl = document.getElementById('room-desc');
    if (roomDescEl) roomDescEl.textContent = node.description;

    // Always show room description banner
    const roomDescriptionBanner = document.getElementById('room-description');
    if (roomDescriptionBanner) roomDescriptionBanner.classList.remove('hidden');


    // Update Background
    const bg = document.getElementById('story-background');
    if (node.image_path) {
        bg.style.backgroundImage = `url('${node.image_path}')`;
    } else {
        bg.style.backgroundImage = `url('/assets/images/placeholder_dungeon.jpg')`;
    }

    // Update Exit Button
    const exitBtn = document.getElementById('exit-dungeon-btn');
    if (exitBtn) {
        exitBtn.classList.toggle('hidden', !node.can_exit);
    }

    // Render Interactions
    const hasActiveMonsters = renderInteractions(node);

    // Render Choices - Hide if active monsters exist
    const choicesDrawer = document.getElementById('choices-drawer');
    if (hasActiveMonsters) {
        choicesDrawer.classList.add('hidden'); // Or translate-y-full with pointer-events-none, but hidden is safer to prevent clicks
        choicesDrawer.style.display = 'none'; // Force hide
    } else {
        choicesDrawer.style.display = 'block'; // Restore
        choicesDrawer.classList.remove('hidden');
        renderChoices(node);
    }
}

const area = document.getElementById('interaction-area');
const monstersContainer = document.getElementById('monsters-container');
const monstersList = document.getElementById('monsters-list');
const lootContainer = document.getElementById('loot-container');
const lootList = document.getElementById('loot-list');
const npcsContainer = document.getElementById('npcs-container');
const npcsList = document.getElementById('npcs-list');
const trapsContainer = document.getElementById('traps-container'); // Correct ID usage
const trapsList = document.getElementById('traps-list');

let hasInteractions = false;
let activeMonstersCount = 0;

// Check Search State
// We use sessionStorage to remember if we searched THIS node
const searchKey = `searched_${STORY_ID}_${node.id}`;
const hasSearched = sessionStorage.getItem(searchKey) === 'true';

// --- Monsters (Always visible) ---
monstersList.innerHTML = '';
if (node.monsters && node.monsters.length > 0) {
    // ... (existing monster logic) ...
    const totalMonsters = node.monsters.length;
    const fledCount = node.monsters.filter(m => storyState.fledMonsters.includes(m.id)).length;
    const allFled = totalMonsters > 0 && totalMonsters === fledCount;

    if (allFled) {
        monstersContainer.classList.add('hidden');
        activeMonstersCount = 0;
    } else {
        hasInteractions = true;
        monstersContainer.classList.remove('hidden');

        node.monsters.forEach(monster => {
            const isFled = storyState.fledMonsters.includes(monster.id);
            if (!isFled) activeMonstersCount++;

            const el = document.createElement('div');
            const bgClass = isFled ? 'bg-gray-800/60 border-gray-700 opacity-70' : 'bg-red-900/40 border-red-800';
            el.className = `${bgClass} p-2 rounded border flex justify-between items-center transition-all`;

            let actionsHtml = '';
            if (isFled) {
                actionsHtml = '<span class="text-xs text-gray-400 font-bold border border-gray-500 px-2 py-1 rounded flex items-center">💨 A fui</span>';
            } else {
                actionsHtml = `
                        <div class="flex gap-2">
                             ${(monster.can_flee === undefined || monster.can_flee == 1) ?
                        `<button class="bg-gray-700 hover:bg-gray-600 text-white px-2 py-1 rounded text-sm" onclick="attemptFlee(${monster.id})">🏃 Fuir</button>` :
                        '<span class="text-xs text-red-500 font-bold border border-red-500 px-1 py-1 rounded flex items-center">🔒 Combat Forcé</span>'
                    }
                            <button class="bg-red-700 hover:bg-red-600 text-white px-2 py-1 rounded text-sm" onclick="startCombat(${monster.id})">⚔️ Attaquer</button>
                        </div>
                    `;
            }

            el.innerHTML = `
                    <span class="font-bold ${isFled ? 'text-gray-400' : 'text-red-200'}">${monster.monster_name} (Niv. ${monster.monster_level})</span>
                    ${actionsHtml}
                `;
            monstersList.appendChild(el);
        });
    }
} else {
    monstersContainer.classList.add('hidden');
}

// --- Loot & Traps (Requires Search) ---
// Note: NPCs are usually visible? Let's say yes. Loot/Traps hidden.

// Loot
lootList.innerHTML = '';
const hasLoot = node.loots && node.loots.length > 0;

if (hasSearched && hasLoot) {
    hasInteractions = true;
    lootContainer.classList.remove('hidden');
    node.loots.forEach(loot => {
        const el = document.createElement('div');
        el.className = 'bg-yellow-900/40 p-2 rounded border border-yellow-800 flex justify-between items-center';
        el.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-xl">${loot.icon || '📦'}</span>
                    <span class="text-yellow-200">${loot.name} x${loot.quantity}</span>
                </div>
                <button class="bg-yellow-700 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm" id="loot-btn-${loot.id}">Prendre</button>
            `;
        lootList.appendChild(el);
        document.getElementById(`loot-btn-${loot.id}`).addEventListener('click', () => collectLoot(loot.id));
    });
} else {
    lootContainer.classList.add('hidden');
}

// Traps
trapsList.innerHTML = '';
const hasTraps = node.traps && node.traps.length > 0;

if (hasSearched && hasTraps) {
    hasInteractions = true;
    trapsContainer.classList.remove('hidden');
    node.traps.forEach(trap => {
        const el = document.createElement('div');
        el.className = 'bg-purple-900/40 p-2 rounded border border-purple-800 flex justify-between items-center';
        el.innerHTML = `
                <div class="flex flex-col">
                    <span class="font-bold text-purple-200">⚠️ ${trap.description}</span>
                    <span class="text-xs text-gray-400">Difficulté: ${trap.difficulty_class} (${trap.avoid_stat})</span>
                </div>
                <button class="bg-purple-700 hover:bg-purple-600 text-white px-2 py-1 rounded text-sm" onclick="attemptTrapAvoidance(${trap.id})">Esquiver/Désamorcer</button>
            `;
        trapsList.appendChild(el);
    });
} else {
    trapsContainer.classList.add('hidden');
}

// NPCs
npcsList.innerHTML = '';
if (node.npcs && node.npcs.length > 0) {
    hasInteractions = true;
    npcsContainer.classList.remove('hidden');
    node.npcs.forEach(npc => {
        const el = document.createElement('div');
        el.className = 'bg-blue-900/40 p-2 rounded border border-blue-800 flex justify-between items-center';
        el.innerHTML = `
                <span class="font-bold text-blue-200">${npc.name}</span>
                <button class="bg-blue-700 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm">Parler</button>
            `;
        npcsList.appendChild(el);
    });
} else {
    npcsContainer.classList.add('hidden');
}

// --- Search Button Injection in renderChoices or here? ---
// If monsters are present, choices are hidden anyway.
// We want "Fouiller" to be a primary action.
// Let's inject a "Fouiller" button into the choices drawer if NOT searched.
// We pass this info to renderChoices via storyState or arguments.
storyState.showSearchButton = !hasSearched && !activeMonstersCount > 0; // Can't search during combat

// Visibility of Main Container
if (hasInteractions || activeMonstersCount > 0) {
    area.classList.remove('hidden');
    const mainContainer = document.getElementById('main-content-container');
    if (mainContainer) mainContainer.classList.remove('hidden');
} else {
    area.classList.add('hidden');
    // Don't hide main container if we want to show description, but here it wraps interaction area.
    // It's fine.
}

return activeMonstersCount > 0;
}

function renderChoices(node) {
    const container = document.getElementById('choices-container');
    container.innerHTML = '';

    // Add Search Button if applicable
    if (storyState.showSearchButton) {
        const btn = document.createElement('button');
        btn.className = 'w-full bg-gradient-to-b from-blue-700 to-blue-800 border border-blue-600 p-4 rounded-lg text-blue-100 text-left transition-all duration-200 flex items-center gap-3 hover:from-blue-600 hover:to-blue-700 hover:shadow-md group';
        btn.innerHTML = `
            <span class="text-2xl group-hover:scale-110 transition-transform">🔍</span>
            <span class="font-medium">Fouiller la salle</span>
        `;
        btn.onclick = searchRoom;
        container.appendChild(btn);
    }

    if (!node.connections || node.connections.length === 0) {
        // ... (end node logic)
        if (node.is_end_node) {
            // ... existing
            container.innerHTML += `
                <div class="col-span-2 text-center">
                    <h3 class="text-green-400 font-bold text-xl mb-4">🎉 Donjon Terminé !</h3>
                    <a href="/story/exit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold inline-block">
                        Quitter et récupérer les récompenses
                    </a>
                </div>
            `;
        } else {
            container.innerHTML += `<div class="text-gray-500 italic text-center col-span-2">Aucune issue...</div>`;
        }
    }

    node.connections.forEach(conn => {
        // ... (existing connection buttons)
        const btn = document.createElement('button');
        btn.className = 'w-full bg-gradient-to-b from-gray-700 to-gray-800 border border-gray-600 p-4 rounded-lg text-gray-200 text-left transition-all duration-200 flex items-center gap-3 hover:not-disabled:from-gray-600 hover:not-disabled:to-gray-700 hover:not-disabled:border-gray-500 hover:not-disabled:-translate-y-0.5 hover:not-disabled:shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:grayscale group';

        // Determine icon based on direction text (simple heuristic)
        let icon = '➡️';
        const text = conn.direction_text.toLowerCase();

        const icons = {
            'nord': '⬆️', 'haut': '⬆️',
            'sud': '⬇️', 'bas': '⬇️',
            'ouest': '⬅️', 'gauche': '⬅️',
            'est': '➡️', 'droite': '➡️',
            'escalier': '🪜', 'porte': '🚪',
            'retour': '↩️', 'rebrousser': '↩️'
        };

        for (const [key, val] of Object.entries(icons)) {
            if (text.includes(key)) {
                icon = val;
                break;
            }
        }
        if (conn.is_return) icon = '↩️';

        const isLocked = conn.is_accessible === false;
        btn.disabled = isLocked;

        let labelHtml = `<span class="font-medium">${conn.direction_text}</span>`;
        if (isLocked) {
            icon = '🔒';
            if (conn.lock_reason) {
                labelHtml += `<div class="text-xs text-red-400 mt-1">${conn.lock_reason}</div>`;
            }
        }

        btn.innerHTML = `
            <span class="text-2xl group-hover:scale-110 transition-transform">${icon}</span>
            <div class="flex flex-col text-left">
                ${labelHtml}
            </div>
        `;

        btn.addEventListener('click', () => {
            console.log('Attempting move to node:', conn.to_node_id);
            moveToNode(conn.to_node_id);
        });
        container.appendChild(btn);
    });
}

// Search Action
window.searchRoom = async () => {
    try {
        const formData = new FormData();
        formData.append('story_id', STORY_ID);

        const response = await fetch('/story/search', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            // Mark as searched in session
            sessionStorage.setItem(`searched_${STORY_ID}_${storyState.currentNode.id}`, 'true');

            showToast(data.message, data.action === 'triggered' ? 'warning' : 'info');

            if (data.action === 'triggered') {
                // Update HP if we triggered a trap
                // Reload node ? Or just re-render interactions?
                // Re-render interactions will show the loot/traps now
            }

            // Re-render UI to show revealed items
            renderNode();

        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error searching:', error);
    }
};

// Expose to window for inline calls if needed
window.moveToNode = moveToNode;

async function moveToNode(nodeId) {
    console.log('moveToNode called with:', nodeId);
    try {
        const formData = new FormData();
        formData.append('story_id', STORY_ID);
        formData.append('node_id', nodeId);

        const response = await fetch('/story/move', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        console.log('Move response:', data);

        if (data.success) {
            loadCurrentNode();
        } else {
            showToast(data.message || 'Déplacement impossible', 'warning');
        }
    } catch (error) {
        console.error('Error moving:', error);
        showToast('Erreur de communication', 'error');
    }
}

async function collectLoot(lootId) {
    try {
        const formData = new FormData();
        formData.append('story_id', STORY_ID);
        formData.append('node_id', storyState.currentNode.id);
        formData.append('loot_id', lootId);

        const response = await fetch('/story/loot', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            if (data.trap_triggered) {
                showToast(`⚠️ ${data.trap_message} (-${data.damage} HP)`, 'warning');
            } else {
                showToast('Objet récupéré !', 'success');
            }

            // Remove from UI locally
            const btn = document.getElementById(`loot-btn-${lootId}`);
            if (btn) btn.closest('div').remove();

            // If list empty, hide container
            const list = document.getElementById('loot-list');
            if (list.children.length === 0) {
                document.getElementById('loot-container').classList.add('hidden');
            }
        } else {
            showToast(data.message || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Error collecting loot:', error);
    }
}

window.attemptTrapAvoidance = async (trapId) => {
    try {
        const formData = new FormData();
        formData.append('story_id', STORY_ID);
        formData.append('trap_id', trapId);

        const response = await fetch('/story/trap/avoid', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showToast(`${data.message}`, 'success');
            // Visual feedback? 
            // Maybe remove the trap from UI? Or mark as disarmed?
            // Backend doesn't persist disarmed state for room traps currently in `story_node_traps` (unless we add a progress tracking for it).
            // For now, the user can "avoid" it repeatedly? Maybe we should hide it.
            // But valid point: if they successfully avoided, they can proceed?
            // Actually room traps are just hazards. They don't block movement unless configured (not implemented yet).
        } else {
            showToast(`${data.message} (-${data.damage} HP)`, 'error');
            if (data.damage > 0) {
                // Update HP UI if we had one visible live.
            }
        }
    } catch (error) {
        console.error('Error avoiding trap:', error);
    }
};

// Expose for inline onclicks
// Expose for inline onclicks
window.startCombat = (monsterId, options = {}) => {
    // Redirect to the real combat system
    // Identify via URL
    window.location.href = `/game/combat/start/${monsterId}`;
};

window.attemptFlee = async (monsterId) => {
    try {
        const formData = new FormData();
        formData.append('story_id', STORY_ID);
        formData.append('monster_id', monsterId);

        const response = await fetch('/story/flee', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            // Refresh to update UI states (fled status)
            loadCurrentNode();
        } else {
            showToast(data.message, 'warning');
            if (data.force_combat) {
                setTimeout(() => {
                    startCombat(monsterId, { initiative: 'enemy' });
                }, 500);
            }
        }
    } catch (error) {
        console.error('Error fleeing:', error);
    }
};

function removeMonsterFromUI(monsterId) {
    const btn = document.querySelector(`button[onclick*="startCombat(${monsterId})"]`);
    if (btn) {
        const item = btn.closest('div');
        item.remove();
    }
    checkRoomCleared();
}

async function checkRoomCleared() {
    const list = document.getElementById('monsters-list');
    if (list.children.length === 0) {
        // All monsters gone
        const container = document.getElementById('monsters-container');
        container.classList.add('hidden');

        // Tell backend we cleared the room
        try {
            const formData = new FormData();
            formData.append('story_id', STORY_ID);

            await fetch('/story/clear-monsters', {
                method: 'POST',
                body: formData
            });

            showToast('Zone sécurisée !', 'success');
            // Reload to show NPCs/Loot
            loadCurrentNode();

        } catch (e) {
            console.error(e);
        }
    }
}
