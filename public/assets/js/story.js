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

    // Update Text (Safeguarded)
    const nameEl = document.getElementById('node-name');
    if (nameEl) nameEl.textContent = node.name;

    const descEl = document.getElementById('node-description');
    if (descEl) descEl.textContent = node.description;

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
        if (node.can_exit) {
            exitBtn.classList.remove('hidden');
        } else {
            exitBtn.classList.add('hidden');
        }
    }

    // Render Interactions
    renderInteractions(node);

    // Render Choices
    renderChoices(node);
}

function renderInteractions(node) {
    const area = document.getElementById('interaction-area');
    const mainContainer = document.getElementById('main-content-container');
    const monstersContainer = document.getElementById('monsters-container');
    const monstersList = document.getElementById('monsters-list');
    const lootContainer = document.getElementById('loot-container');
    const lootList = document.getElementById('loot-list');
    const npcsContainer = document.getElementById('npcs-container');
    const npcsList = document.getElementById('npcs-list');

    let hasInteractions = false;

    // Monsters
    monstersList.innerHTML = '';
    if (node.monsters && node.monsters.length > 0) {
        hasInteractions = true;
        monstersContainer.classList.remove('hidden');
        node.monsters.forEach(monster => {
            const el = document.createElement('div');
            el.className = 'bg-red-900/40 p-2 rounded border border-red-800 flex justify-between items-center';
            el.innerHTML = `
                <span class="font-bold text-red-200">${monster.monster_name} (Niv. ${monster.monster_level})</span>
                <button class="bg-red-700 hover:bg-red-600 text-white px-2 py-1 rounded text-sm" onclick="startCombat(${monster.id})">Attaquer</button>
            `;
            monstersList.appendChild(el);
        });
    } else {
        monstersContainer.classList.add('hidden');
    }

    // Loot
    lootList.innerHTML = '';
    if (node.loots && node.loots.length > 0) {
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

    if (hasInteractions) {
        area.classList.remove('hidden');
        if (mainContainer) mainContainer.classList.remove('hidden');
    } else {
        area.classList.add('hidden');
        if (mainContainer) mainContainer.classList.add('hidden');
    }
}

function renderChoices(node) {
    const container = document.getElementById('choices-container');
    container.innerHTML = '';

    if (!node.connections || node.connections.length === 0) {
        if (node.is_end_node) {
            container.innerHTML = `
                <div class="col-span-2 text-center">
                    <h3 class="text-green-400 font-bold text-xl mb-4">🎉 Donjon Terminé !</h3>
                    <a href="/story/exit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold inline-block">
                        Quitter et récupérer les récompenses
                    </a>
                </div>
            `;
        } else {
            container.innerHTML = `<div class="text-gray-500 italic text-center col-span-2">Aucune issue...</div>`;
        }
        return;
    }

    node.connections.forEach(conn => {
        const btn = document.createElement('button');
        btn.className = 'w-full bg-gradient-to-b from-gray-700 to-gray-800 border border-gray-600 p-4 rounded-lg text-gray-200 text-left transition-all duration-200 flex items-center gap-3 hover:not-disabled:from-gray-600 hover:not-disabled:to-gray-700 hover:not-disabled:border-gray-500 hover:not-disabled:-translate-y-0.5 hover:not-disabled:shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:grayscale group';

        // Determine icon based on direction text (simple heuristic)
        let icon = '➡️';
        const text = conn.direction_text.toLowerCase();
        if (text.includes('nord') || text.includes('haut')) icon = '⬆️';
        if (text.includes('sud') || text.includes('bas')) icon = '⬇️';
        if (text.includes('ouest') || text.includes('gauche')) icon = '⬅️';
        if (text.includes('est') || text.includes('droite')) icon = '➡️';
        if (text.includes('escalier')) icon = '🪜';
        if (text.includes('porte')) icon = '🚪';
        if (conn.is_return || text.includes('retour') || text.includes('rebrousser')) icon = '↩️';

        btn.innerHTML = `
            <span class="text-2xl group-hover:scale-110 transition-transform">${icon}</span>
            <span class="font-medium">${conn.direction_text}</span>
        `;

        // Check conditions (visual only, server validates)
        // We could add disabled state here if we had condition data fully processed

        btn.addEventListener('click', () => {
            console.log('Attempting move to node:', conn.to_node_id);
            moveToNode(conn.to_node_id);
        });
        container.appendChild(btn);
    });
}

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
            showToast('Objet récupéré !', 'success');
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

// Expose for inline onclicks
window.startCombat = (monsterId) => {
    showToast('Combat non implémenté pour le moment', 'info');
};
