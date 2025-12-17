// story.js - Refactored for SPA
import { showToast } from '/js/modules/toast.js';
import { changeMusicCategory, initSoundManager } from '/js/modules/soundManager.js';

const storyState = {
    currentNode: null,
    nodeStatus: null,
    isLoading: false,
    storyId: null
};

// Export init function for GameRouter
export async function init() {
    console.log('[Story] Initializing...');

    // Get Story ID from DOM
    const sceneElement = document.getElementById('story-scene');
    if (sceneElement && sceneElement.dataset.storyId) {
        storyState.storyId = parseInt(sceneElement.dataset.storyId);
        window.STORY_ID = storyState.storyId; // Legacy support
    } else if (window.STORY_ID) {
        storyState.storyId = window.STORY_ID;
    } else {
        console.error('[Story] No Story ID found');
        return;
    }

    await initSoundManager();
    changeMusicCategory('dungeon');
    loadCurrentNode();
    initUI();
}

function initUI() {
    // Re-attach UI listeners if needed
    const drawer = document.getElementById('choices-drawer');
    const toggleBtn = document.getElementById('choices-toggle');
    const toggleIcon = document.getElementById('toggle-icon');
    let isOpen = false;

    if (toggleBtn && drawer) {
        // Remove old listeners to be safe (cloning)
        const newBtn = toggleBtn.cloneNode(true);
        toggleBtn.parentNode.replaceChild(newBtn, toggleBtn);

        newBtn.addEventListener('click', () => {
            isOpen = !isOpen;
            if (isOpen) {
                drawer.classList.remove('translate-y-full');
                if (toggleIcon) toggleIcon.textContent = '🔽';
            } else {
                drawer.classList.add('translate-y-full');
                if (toggleIcon) toggleIcon.textContent = '🔼';
            }
        });
    }

    // Top Drawer Logic
    const topDrawer = document.getElementById('top-drawer');
    const topToggleBtn = document.getElementById('top-drawer-toggle');
    const topToggleIcon = document.getElementById('top-toggle-icon');
    let isTopOpen = false;

    if (topToggleBtn && topDrawer) {
        const newTopBtn = topToggleBtn.cloneNode(true);
        topToggleBtn.parentNode.replaceChild(newTopBtn, topToggleBtn);

        newTopBtn.addEventListener('click', () => {
            isTopOpen = !isTopOpen;
            if (isTopOpen) {
                topDrawer.classList.remove('-translate-y-full');
                if (topToggleIcon) topToggleIcon.textContent = '🔼';
            } else {
                topDrawer.classList.add('-translate-y-full');
                if (topToggleIcon) topToggleIcon.textContent = '🔽';
            }
        });
    }
}

async function loadCurrentNode() {
    if (storyState.isLoading) return;
    storyState.isLoading = true;

    try {
        const response = await fetch(`/story/current?story_id=${storyState.storyId}`);
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
    // const roomDescriptionBanner = document.getElementById('room-description'); // Legacy
    // if (roomDescriptionBanner) roomDescriptionBanner.classList.remove('hidden');


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

    // Render Interactions (Now returns active entity info)
    const interactionResult = renderInteractions(node);
    const activeEntity = interactionResult ? interactionResult.entity : null;

    // Render Choices - Hide if active monsters exist
    const choicesDrawer = document.getElementById('choices-drawer');
    const drawerContainer = document.getElementById('choices-container');

    // Ensure drawer is visible
    choicesDrawer.classList.remove('hidden');
    choicesDrawer.classList.remove('translate-y-full'); // Auto-open for engagement
    choicesDrawer.style.display = 'block';

    if (activeEntity) {
        // Render Engagement Actions (Attack/Flee or Talk/Goodbye)
        renderEngagementActions(interactionResult.type, activeEntity);
    } else {
        // Standard Movement/Search Choices
        renderChoices(node);
    }
}

function renderEngagementActions(type, entity) {
    const container = document.getElementById('choices-container');
    container.innerHTML = '';

    if (type === 'monster') {
        // Attack Button
        const btnAttack = document.createElement('button');
        btnAttack.className = 'w-full bg-gradient-to-b from-red-700 to-red-800 border border-red-600 p-4 rounded-lg text-white text-left shadow-lg hover:from-red-600 hover:to-red-700 flex items-center gap-3 group';
        btnAttack.innerHTML = `
            <span class="text-3xl group-hover:scale-110 transition-transform">⚔️</span>
            <div class="flex flex-col">
                <span class="font-bold text-lg">Attaquer</span>
                <span class="text-xs text-red-200">Engager le combat</span>
            </div>
        `;
        btnAttack.onclick = () => startCombat(entity.id);
        container.appendChild(btnAttack);

        // Flee Button
        const canFlee = (entity.can_flee === undefined || entity.can_flee == 1);
        if (canFlee) {
            const btnFlee = document.createElement('button');
            btnFlee.className = 'w-full bg-gradient-to-b from-gray-700 to-gray-800 border border-gray-600 p-4 rounded-lg text-gray-200 text-left shadow-lg hover:from-gray-600 hover:to-gray-700 flex items-center gap-3 group mt-2';
            btnFlee.innerHTML = `
                <span class="text-3xl group-hover:scale-110 transition-transform">🏃</span>
                <div class="flex flex-col">
                    <span class="font-bold text-lg">Fuir</span>
                    <span class="text-xs text-gray-400">Tenter de s'échapper</span>
                </div>
            `;
            btnFlee.onclick = () => attemptFlee(entity.id);
            container.appendChild(btnFlee);
        } else {
            const btnLocked = document.createElement('div');
            btnLocked.className = 'w-full bg-gray-900 border border-red-900 p-4 rounded-lg text-gray-500 text-center mt-2 italic';
            btnLocked.innerText = "🔒 Fuite Impossible";
            container.appendChild(btnLocked);
        }

    } else if (type === 'npc') {
        // Talk Button
        const btnTalk = document.createElement('button');
        btnTalk.className = 'w-full bg-gradient-to-b from-blue-700 to-blue-800 border border-blue-600 p-4 rounded-lg text-white text-left shadow-lg hover:from-blue-600 hover:to-blue-700 flex items-center gap-3 group';
        btnTalk.innerHTML = `
            <span class="text-3xl group-hover:scale-110 transition-transform">💬</span>
            <div class="flex flex-col">
                <span class="font-bold text-lg">Parler</span>
                <span class="text-xs text-blue-200">Discuter avec ${entity.name}</span>
            </div>
        `;
        btnTalk.onclick = () => interactWithNPC(entity.id);
        container.appendChild(btnTalk);

        // Goodbye / Ignore Button
        // "Dire au revoir" essentially skips the interaction (marks as interacted without "talking" logic deep dive?)
        // Or we assume "Parler" is the only way to progress if it's forced?
        // User said "Parler / Dire au revoir".
        // Let's implement Goodbye as "Mark as interacted" too, but maybe with a different toast message or just skip.
        // Actually, if we want to "Move to next", we MUST mark it as interacted.

        const btnBye = document.createElement('button');
        btnBye.className = 'w-full bg-gradient-to-b from-gray-700 to-gray-800 border border-gray-600 p-4 rounded-lg text-gray-200 text-left shadow-lg hover:from-gray-600 hover:to-gray-700 flex items-center gap-3 group mt-2';
        btnBye.innerHTML = `
            <span class="text-3xl group-hover:scale-110 transition-transform">👋</span>
            <div class="flex flex-col">
                <span class="font-bold text-lg">Dire au revoir</span>
                <span class="text-xs text-gray-400">Quitter la conversation</span>
            </div>
        `;
        // We reuse interactWithNPC but maybe we should distinguish?
        // For now, reuse it to clear the "active" status.
        btnBye.onclick = () => interactWithNPC(entity.id);
        container.appendChild(btnBye);
    }
}

// Updated renderInteractions to return Active Entity for Sequential Flow
function renderInteractions(node) {
    const area = document.getElementById('interaction-area');
    const monstersContainer = document.getElementById('monsters-container'); // Legacy container, reuse or hide
    // We will assume a new "Focused Encounter" container exists in HTML, or we inject it into 'interaction-area'

    let activeEntity = null;
    let activeType = null;

    // 1. Check Monsters (First non-fled, non-killed logic matches backend filter)
    // Backend already filtered out killed monsters.
    // We need to check fled monsters locally for now (as backend might return them if we want to show them as "fled")
    // But for sequential view, if it's fled, we skip it? 
    // Backend actually filtered out killed ones.
    // Fled ones are in `storyState.fledMonsters`.

    if (node.monsters && node.monsters.length > 0) {
        for (const m of node.monsters) {
            if (!storyState.fledMonsters.includes(m.id)) {
                activeEntity = m;
                activeType = 'monster';
                break;
            }
        }
    }

    // 2. If no active monster, Check NPCs
    if (!activeEntity && node.npcs && node.npcs.length > 0) {
        // Frontend assumes Backend filtered out "interacted" NPCs
        // So the first one is the active one!
        activeEntity = node.npcs[0];
        activeType = 'npc';
    }

    // 3. Render Focused View
    if (activeEntity) {
        area.classList.remove('hidden');
        const mainContainer = document.getElementById('main-content-container');
        if (mainContainer) mainContainer.classList.remove('hidden');

        // Hide standard lists just in case
        ['monsters-container', 'loot-container', 'traps-container', 'npcs-container'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });

        // Allow 'monsters-container' to act as the "Focus Card" for now or create generic one
        // Let's reuse 'monsters-container' but clear it and style it for single entity
        monstersContainer.classList.remove('hidden');
        monstersContainer.innerHTML = '';

        const card = document.createElement('div');
        card.className = 'flex flex-col items-center justify-center p-6 bg-black/60 rounded-xl border-2 border-red-500/50 shadow-2xl backdrop-blur-sm animate-fade-in';

        // Image/Icon (Placeholder or real path)
        const imagePath = activeEntity.image_path || (activeType === 'monster' ? '/assets/images/monster_placeholder.png' : '/assets/images/npc_placeholder.png');
        // Use emoji for now if no image
        const icon = activeType === 'monster' ? '👹' : '👤';

        card.innerHTML = `
                <div class="text-6xl mb-4 filter drop-shadow-lg">${icon}</div>
                <h3 class="text-2xl font-bold text-white mb-2">${activeType === 'monster' ? activeEntity.monster_name : activeEntity.name}</h3>
                <div class="text-sm text-gray-300 italic mb-4">${activeType === 'monster' ? `Niveau ${activeEntity.monster_level}` : 'Personnage'}</div>
                <div class="w-full h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent opacity-50"></div>
            `;

        monstersContainer.appendChild(card);

        return { type: activeType, entity: activeEntity };
    }

    // 4. No Active Entity -> Show Loot/Traps/Empty
    // Hide monsters container
    if (monstersContainer) monstersContainer.classList.add('hidden');

    // Loot & Traps Logic (Standard Room View)
    const lootContainer = document.getElementById('loot-container');
    const lootList = document.getElementById('loot-list');
    const trapsContainer = document.getElementById('traps-container');
    const trapsList = document.getElementById('traps-list');

    const searchKey = `searched_${storyState.storyId}_${node.id}`;
    const hasSearched = sessionStorage.getItem(searchKey) === 'true';

    let hasItems = false;

    // Loot
    if (lootList) lootList.innerHTML = '';
    if (hasSearched && node.loots && node.loots.length > 0) {
        hasItems = true;
        if (lootContainer) lootContainer.classList.remove('hidden');
        node.loots.forEach(loot => {
            const el = document.createElement('div');
            el.className = 'bg-yellow-900/40 p-2 rounded border border-yellow-800 flex justify-between items-center mb-2';
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
    } else if (lootContainer) {
        lootContainer.classList.add('hidden');
    }

    // Traps
    if (trapsList) trapsList.innerHTML = '';
    if (hasSearched && node.traps && node.traps.length > 0) {
        hasItems = true;
        if (trapsContainer) trapsContainer.classList.remove('hidden');
        node.traps.forEach(trap => {
            const el = document.createElement('div');
            el.className = 'bg-purple-900/40 p-2 rounded border border-purple-800 flex justify-between items-center mb-2';
            el.innerHTML = `
                    <div class="flex flex-col">
                        <span class="font-bold text-purple-200">⚠️ ${trap.description}</span>
                    </div>
                    <button class="bg-purple-700 hover:bg-purple-600 text-white px-2 py-1 rounded text-sm" onclick="attemptTrapAvoidance(${trap.id})">Esquiver</button>
                `;
            trapsList.appendChild(el);
        });
    } else if (trapsContainer) {
        trapsContainer.classList.add('hidden');
    }

    if (hasItems) {
        area.classList.remove('hidden');
        const mainContainer = document.getElementById('main-content-container');
        if (mainContainer) mainContainer.classList.remove('hidden');
    } else {
        // Hide area if nothing interesting to show (except maybe text?)
        // Keep hidden unless we want to show "Empty room" text
    }

    return null;
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
        formData.append('story_id', storyState.storyId);

        const response = await fetch('/story/search', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            // Mark as searched in session
            sessionStorage.setItem(`searched_${storyState.storyId}_${storyState.currentNode.id}`, 'true');

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
        formData.append('story_id', storyState.storyId);
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
        formData.append('story_id', storyState.storyId);
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
        formData.append('story_id', storyState.storyId);
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
    // Ensure storyId is available
    if (!storyState.storyId) {
        const scene = document.getElementById('story-scene');
        if (scene && scene.dataset.storyId) {
            storyState.storyId = parseInt(scene.dataset.storyId);
        } else if (window.STORY_ID) {
            storyState.storyId = window.STORY_ID;
        }
    }

    // Redirect to the real combat system
    if (window.GameRouter) {
        window.GameRouter.navigate(`/game/combat/start/${monsterId}?story_id=${storyState.storyId}`);
    } else {
        window.location.href = `/game/combat/start/${monsterId}?story_id=${storyState.storyId}`;
    }
};

window.attemptFlee = async (monsterId) => {
    try {
        const formData = new FormData();
        formData.append('story_id', storyState.storyId);
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
            formData.append('story_id', storyState.storyId);

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

window.interactWithNPC = async (npcId) => {
    try {
        const formData = new FormData();
        formData.append('story_id', storyState.storyId);
        formData.append('node_id', storyState.currentNode.id);
        formData.append('npc_id', npcId);

        const response = await fetch('/story/npc/interact', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showToast('Vous choisissez de parler avec le personnage.', 'success');
            // Reload to potentially unlock exit
            loadCurrentNode();
        } else {
            showToast(data.message || 'Interaction impossible', 'error');
        }
    } catch (e) {
        console.error(e);
    }
};
