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

    // Clear current view if fleeting to simulate movement
    if (storyState.isFleeting) {
        document.getElementById('choices-container').innerHTML = '<div class="col-span-full text-center text-gray-400 italic">Fuite en cours...</div>';
    }

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
        
        // Reset fleeting state
        storyState.isFleeting = false;
        
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
        // Create a temp image to check if it loads
        const img = new Image();
        img.onload = () => {
             bg.style.backgroundImage = `url('${node.image_path}')`;
        };
        img.onerror = () => {
             console.warn('Image failed to load, utilizing placeholder:', node.image_path);
             bg.style.backgroundImage = `url('/assets/images/placeholder_dungeon.jpg')`;
        };
        img.src = node.image_path;
    } else {
        bg.style.backgroundImage = `url('/assets/images/placeholder_dungeon.jpg')`;
    }

    // Update Exit Button - DÉSACTIVÉ car "Dire au revoir" à la princesse quitte le donjon
    const exitBtn = document.getElementById('exit-dungeon-btn');
    if (exitBtn) {
        // Toujours masquer le bouton, la sortie se fait via "Dire au revoir"
        exitBtn.classList.add('hidden');
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
        btnTalk.onclick = () => interactWithNPC(entity.npc_id);
        container.appendChild(btnTalk);

        // Goodbye / Ignore Button
        const btnBye = document.createElement('button');
        btnBye.className = 'w-full bg-gradient-to-b from-gray-700 to-gray-800 border border-gray-600 p-4 rounded-lg text-gray-200 text-left shadow-lg hover:from-gray-600 hover:to-gray-700 flex items-center gap-3 group mt-2';
        btnBye.innerHTML = `
            <span class="text-3xl group-hover:scale-110 transition-transform">👋</span>
            <div class="flex flex-col">
                <span class="font-bold text-lg">Dire au revoir</span>
                <span class="text-xs text-gray-400">Quitter le donjon</span>
            </div>
        `;
        // Si c'est la princesse (NPC 3), toujours quitter le donjon
        btnBye.onclick = async () => {
            if (entity.npc_id === 3) {
                console.log('[Story] Princess goodbye - marking as interacted and exiting dungeon');
                
                // D'abord marquer la princesse comme interagée pour valider la quête
                try {
                    const formData = new FormData();
                    formData.append('story_id', storyState.storyId);
                    formData.append('node_id', storyState.currentNode.id);
                    formData.append('npc_id', entity.npc_id);

                    const response = await fetch('/story/npc/interact', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    console.log('[Story] NPC interact response:', data);
                    
                    // Attendre un peu pour que PHP sauvegarde la session
                    await new Promise(resolve => setTimeout(resolve, 300));
                    
                    // Puis quitter le donjon
                    showToast('Au revoir Princesse !', 'success');
                    setTimeout(() => {
                        window.exitDungeon();
                    }, 1000);
                } catch (e) {
                    console.error('[Story] Error marking NPC interaction:', e);
                    showToast('Erreur lors de la sortie', 'error');
                }
            } else {
                // Sinon, comportement normal (marquer comme interagi)
                interactWithNPC(entity.npc_id);
            }
        };
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
            // Si le joueur a reculé, recharger le nœud (qui est maintenant le précédent)
            if (data.retreated) {
                storyState.isFleeting = true;
                setTimeout(() => {
                    loadCurrentNode();
                }, 800);
            } else {
                // Si pas de recul possible (début du donjon), juste cacher le monstre
                loadCurrentNode();
            }
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
        
        console.log('[NPC Interaction] Response:', data);

        if (data.success) {
            if (data.dialogue) {
                 console.log('[NPC Interaction] Rendering dialogue:', data.dialogue);
                 // Passer l'information d'auto-exit au dialogue
                 renderDialogue(data.dialogue, data.auto_exit || false);
            } else {
                 console.log('[NPC Interaction] No dialogue data, NPC marked as interacted');
                 showToast('Vous avez parlé avec le personnage.', 'success');
                 
                 // Si auto_exit, quitter directement
                 if (data.auto_exit) {
                     setTimeout(() => {
                         window.exitDungeon();
                     }, 1000);
                 } else {
                     // Reload to potentially unlock exit
                     setTimeout(() => {
                         loadCurrentNode();
                     }, 500);
                 }
            }
        } else {
            showToast(data.message || 'Interaction impossible', 'error');
        }
    } catch (e) {
        console.error('[NPC Interaction] Error:', e);
    }
};

function renderDialogue(dialogueData, autoExit = false) {
    // Create Dialogue Overlay
    const overlay = document.createElement('div');
    overlay.id = 'dialogue-overlay';
    overlay.className = 'fixed inset-0 z-50 flex items-end justify-center pointer-events-auto bg-black/50 backdrop-blur-[2px] animate-fade-in';
    
    // Stocker les métadonnées du dialogue pour savoir s'il faut quitter après
    overlay.dataset.npcId = dialogueData.npc_id || '';
    overlay.dataset.autoExit = autoExit ? 'true' : 'false';
    
    // Bubble Container
    const bubble = document.createElement('div');
    bubble.className = 'bg-gray-900 border-2 border-white/20 p-6 md:p-8 rounded-t-2xl md:rounded-2xl max-w-4xl w-full mx-auto md:mb-12 shadow-[0_-10px_40px_rgba(0,0,0,0.8)] animate-slide-up transform transition-all';
    
    // Header (Title)
    const header = document.createElement('div');
    header.innerHTML = `<h3 class="text-yellow-500 font-bold text-xl mb-2">${dialogueData.title || 'Inconnu'}</h3>`;
    bubble.appendChild(header);

    // Text Content
    const content = document.createElement('div');
    content.id = 'dialogue-text';
    content.className = 'text-white text-lg leading-relaxed mb-6 font-serif';
    // Typewriter effect could go here
    content.innerText = dialogueData.root.text;
    bubble.appendChild(content);

    // Choices Container
    const choicesDiv = document.createElement('div');
    choicesDiv.className = 'flex flex-col gap-2 mt-4 border-t border-gray-700 pt-4';
    
    // Render Choices
    if(dialogueData.root.choices && dialogueData.root.choices.length > 0) {
        dialogueData.root.choices.forEach(choice => {
            const btn = document.createElement('button');
            btn.className = 'text-left px-4 py-3 bg-gray-800 hover:bg-gray-700 text-gray-200 rounded border border-gray-700 hover:border-gray-500 transition-colors flex items-center gap-3 group';
            btn.innerHTML = `<span class="text-blue-400 group-hover:text-blue-300">➤</span> ${choice.text}`; // Assuming child node text is the choice text
            
            // On click -> Traverse to next node (would need logic to fetch children of this child)
            // For now, let's assume simple 1-depth or close dialogue
            btn.onclick = () => {
                 // Close logic for now as deep traversal isn't fully implemented in this MVP block
                 // TODO: Implement deep traversal via API
                 closeDialogue();
                 // Trigger actions if any (not implemented yet in this block)
            };
            choicesDiv.appendChild(btn);
        });
    } else {
        // Continue/Close button
        const btn = document.createElement('button');
        btn.className = 'text-center px-4 py-3 bg-gray-800 hover:bg-gray-700 text-gray-200 rounded border border-gray-700 hover:border-gray-500 transition-colors font-bold uppercase tracking-widest';
        btn.innerText = "Fermer";
        btn.onclick = closeDialogue;
        choicesDiv.appendChild(btn);
    }
    
    bubble.appendChild(choicesDiv);
    overlay.appendChild(bubble);
    document.body.appendChild(overlay);
}

function closeDialogue() {
    const overlay = document.getElementById('dialogue-overlay');
    if (overlay) {
        const shouldAutoExit = overlay.dataset.autoExit === 'true';
        
        overlay.classList.add('opacity-0');
        setTimeout(() => {
            overlay.remove();
            
            // Si c'est la princesse et qu'on peut sortir automatiquement, quitter le donjon
            if (shouldAutoExit) {
                console.log('[Dialogue] Auto-exit triggered, leaving dungeon...');
                showToast('Vous quittez le donjon avec la princesse ! 👑', 'success');
                setTimeout(() => {
                    window.exitDungeon();
                }, 500);
            } else {
                // Reload node to update state (e.g. exit unlocked)
                // Petit délai pour s'assurer que la session backend est à jour
                setTimeout(() => {
                    loadCurrentNode();
                }, 200);
            }
        }, 300);
    }
}

// Reset Story Global Function
window.resetStory = async (storyId) => {
    if(!confirm("Voulez-vous vraiment recommencer l'histoire à zéro ?")) return;
    
    try {
        const formData = new FormData();
        formData.append('story_id', storyId);

        const response = await fetch('/story/reset', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showToast('Histoire réinitialisée', 'success');
            // Reload page to restart
            setTimeout(() => window.location.reload(), 500);
        } else {
             showToast(data.message, 'error');
        }
    } catch (e) {
        console.error('[Story] Reset error:', e);
        showToast('Erreur lors de la réinitialisation', 'error');
    }
};

// Exit Dungeon Global Function - Sortir sans recharger la page
window.exitDungeon = async () => {
    try {
        const formData = new FormData();
        formData.append('story_id', storyState.storyId);

        // Afficher le toast immédiatement
        showToast('Vous quittez le donjon... 🚪', 'success');

        // Attendre que le backend supprime complètement la progression
        const response = await fetch('/story/exit', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        console.log('[Story] Exit response:', data);

        // Attendre un peu pour que la DB soit à jour
        await new Promise(resolve => setTimeout(resolve, 200));

        // Afficher la map sans recharger la page
        if (window.GameRouter) {
            window.GameRouter.showMap();
        } else {
            window.location.href = '/game';
        }
    } catch (e) {
        console.error('[Story] Exit error:', e);
        // En cas d'erreur, afficher la map quand même
        if (window.GameRouter) {
            window.GameRouter.showMap();
        } else {
            window.location.href = '/game';
        }
    }
};
