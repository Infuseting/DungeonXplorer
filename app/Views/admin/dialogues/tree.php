<?php
$pageTitle = 'Éditeur d\'Arbre - ' . htmlspecialchars($tree['name']);
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">
            🌳 <?= htmlspecialchars($tree['name']) ?>
        </h3>
        <div class="flex gap-4">
            <a href="/admin/dialogues/edit/<?= $tree['id'] ?>" class="btn btn-secondary">
                ✏️ Modifier l'arbre
            </a>
            <a href="/admin/dialogues" class="btn btn-secondary">
                ← Retour
            </a>
        </div>
    </div>
    
    <?php if ($tree['description']): ?>
        <p class="text-gray-400 mb-6">
            <?= htmlspecialchars($tree['description']) ?>
        </p>
    <?php endif; ?>
    
    <div class="mb-6">
        <button class="btn btn-primary" onclick="addRootNode()">
            ➕ Ajouter nœud racine (PNJ)
        </button>
    </div>
    
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-8 min-h-[500px]" id="tree-container">
        <!-- Tree will be rendered here by JavaScript -->
    </div>
</div>

<!-- Add/Edit Node Modal -->
<div id="node-modal" class="hidden fixed inset-0 w-full h-full bg-black/70 z-50 items-center justify-center">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-8 max-w-xl w-[90%]">
        <h3 class="mb-6 text-gray-100">
            <span id="modal-title">Ajouter un nœud</span>
        </h3>
        
        <form id="node-form">
            <input type="hidden" id="node-id">
            <input type="hidden" id="parent-id">
            <input type="hidden" id="is-player-choice">
            
            <div class="mb-4">
                <label class="block mb-2 font-medium text-gray-100">Texte *</label>
                <textarea 
                    id="node-text" 
                    rows="4" 
                    required
                    class="w-full p-3 bg-gray-900 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none resize-y"
                    placeholder="Texte du dialogue ou du choix..."
                ></textarea>
            </div>
            
            <div id="choice-text-group" class="mb-4 hidden">
                <label class="block mb-2 font-medium text-gray-100">Texte du bouton de choix</label>
                <input 
                    type="text" 
                    id="choice-text"
                    class="w-full p-3 bg-gray-900 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
                    placeholder="Texte affiché sur le bouton..."
                >
            </div>
            
            <!-- Actions & Conditions (Collapsible or always visible?) -->
            <div class="border-t border-gray-700 pt-4 mt-4">
                <h4 class="text-sm uppercase text-gray-400 font-bold mb-3">Conséquences & Conditions</h4>
                
                <div class="grid grid-cols-2 gap-4">
                     <!-- Action -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-300">Action Type</label>
                        <select id="action-type" class="w-full p-2 bg-gray-900 border border-gray-700 rounded text-gray-100 text-sm">
                            <option value="NONE">Aucune</option>
                            <option value="TRIGGER_QUEST">Démarrer Quête (ID)</option>
                            <option value="GIVE_ITEM">Donner Objet (ID)</option>
                            <option value="REMOVE_ITEM">Retirer Objet (ID)</option>
                            <option value="HEAL">Soigner (PV)</option>
                            <option value="DAMAGE">Dégâts (PV)</option>
                            <option value="GIVE_GOLD">Donner Or</option>
                            <option value="REMOVE_GOLD">Retirer Or</option>
                            <option value="FORCE_FIGHT">Forcer Combat (ID Monstre)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm text-gray-300">Valeur Action</label>
                        <input type="text" id="action-value" class="w-full p-2 bg-gray-900 border border-gray-700 rounded text-gray-100 text-sm" placeholder="Ex: 10, 50...">
                    </div>
                    
                    <!-- Condition -->
                    <div>
                        <label class="block mb-1 text-sm text-gray-300">Condition Type</label>
                        <select id="condition-type" class="w-full p-2 bg-gray-900 border border-gray-700 rounded text-gray-100 text-sm">
                            <option value="NONE">Aucune</option>
                            <option value="MIN_LEVEL">Niveau Min</option>
                            <option value="HAS_ITEM">Possède Objet (ID)</option>
                            <option value="QUEST_ACTIVE">Quête Active (ID)</option>
                            <option value="QUEST_COMPLETED">Quête Terminée (ID)</option>
                            <option value="QUEST_NOT_STARTED">Quête Non Commencée (ID)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm text-gray-300">Valeur Condition</label>
                        <input type="text" id="condition-value" class="w-full p-2 bg-gray-900 border border-gray-700 rounded text-gray-100 text-sm" placeholder="Ex: 5, 101...">
                    </div>
                </div>
            </div>

            
            <div class="flex gap-4 justify-end">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    💾 Sauvegarder
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const treeId = <?= $tree['id'] ?>;
let treeData = <?= json_encode($dialogueTree ?? []) ?>;

function renderTree() {
    const container = document.getElementById('tree-container');
    container.innerHTML = '';
    
    if (treeData.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-400 p-12">Aucun nœud. Ajoutez un nœud racine pour commencer.</p>';
        return;
    }
    
    treeData.forEach(node => {
        container.appendChild(renderNode(node, 0));
    });
}

function renderNode(node, level) {
    const div = document.createElement('div');
    div.className = `my-2 p-4 bg-gray-800 border-2 border-gray-700 rounded-lg transition-all duration-200 hover:border-indigo-500 ${node.is_player_choice ? 'border-l-4 border-l-amber-400' : 'border-l-4 border-l-indigo-500'}`;
    div.style.marginLeft = `${level * 2}rem`;
    
    const icon = node.is_player_choice ? '💬' : '📝';
    const type = node.is_player_choice ? 'Joueur' : 'PNJ';
    const displayText = node.is_player_choice && node.choice_text ? node.choice_text : node.text;
    
    div.innerHTML = `
        <div class="flex items-center gap-4">
            <span class="text-2xl">${icon}</span>
            <div class="flex-1 text-gray-100">
                <strong>${type}:</strong> ${escapeHtml(displayText)}
            </div>
            <div class="flex gap-2">
                <button class="btn px-3 py-1.5 text-sm btn-primary" onclick="editNode(${node.id})">✏️</button>
                <button class="btn px-3 py-1.5 text-sm btn-secondary" onclick="addChildNode(${node.id}, 0)">+ PNJ</button>
                <button class="btn px-3 py-1.5 text-sm btn-secondary" onclick="addChildNode(${node.id}, 1)">+ Joueur</button>
                <button class="btn px-3 py-1.5 text-sm bg-red-500/20 text-red-300 border border-red-500/50 hover:bg-red-500/30" onclick="deleteNode(${node.id})">🗑️</button>
            </div>
        </div>
    `;
    
    if (node.children && node.children.length > 0) {
        const childrenDiv = document.createElement('div');
        childrenDiv.className = 'ml-8 border-l-2 border-dashed border-gray-700 pl-4';
        node.children.forEach(child => {
            childrenDiv.appendChild(renderNode(child, level + 1));
        });
        div.appendChild(childrenDiv);
    }
    
    return div;
}

function addRootNode() {
    openModal('Ajouter nœud racine (PNJ)', null, null, 0);
}

function addChildNode(parentId, isPlayerChoice) {
    const type = isPlayerChoice ? 'Joueur' : 'PNJ';
    openModal(`Ajouter réponse ${type}`, null, parentId, isPlayerChoice);
}

function editNode(nodeId) {
    const node = findNode(treeData, nodeId);
    if (!node) return;
    
    openModal('Modifier le nœud', node);
}

function openModal(title, node = null, parentId = null, isPlayerChoice = 0) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('node-id').value = node ? node.id : '';
    document.getElementById('parent-id').value = parentId || '';
    document.getElementById('is-player-choice').value = node ? node.is_player_choice : isPlayerChoice;
    document.getElementById('node-text').value = node ? node.text : '';
    document.getElementById('choice-text').value = node ? (node.choice_text || '') : '';
    
        document.getElementById('action-type').value = node ? (node.action_type || 'NONE') : 'NONE';
    document.getElementById('action-value').value = node ? (node.action_value || '') : '';
    document.getElementById('condition-type').value = node ? (node.condition_type || 'NONE') : 'NONE';
    document.getElementById('condition-value').value = node ? (node.condition_value || '') : '';
    
    const choiceGroup = document.getElementById('choice-text-group');
    if ((node ? node.is_player_choice : isPlayerChoice)) {
        choiceGroup.classList.remove('hidden');
        choiceGroup.classList.add('block');
    } else {
        choiceGroup.classList.remove('block');
        choiceGroup.classList.add('hidden');
    }
    
    document.getElementById('node-modal').classList.remove('hidden');
    document.getElementById('node-modal').classList.add('flex');
}

function closeModal() {
    document.getElementById('node-modal').classList.remove('flex');
    document.getElementById('node-modal').classList.add('hidden');
    document.getElementById('node-form').reset();
}

document.getElementById('node-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nodeId = document.getElementById('node-id').value;
    const data = {
        tree_id: treeId,
        parent_id: document.getElementById('parent-id').value || null,
        text: document.getElementById('node-text').value,
        is_player_choice: parseInt(document.getElementById('is-player-choice').value),
        choice_text: document.getElementById('choice-text').value || null,
        order_index: 0,
                action_type: document.getElementById('action-type').value,
        action_value: document.getElementById('action-value').value,
        condition_type: document.getElementById('condition-type').value,
        condition_value: document.getElementById('condition-value').value
    };
    
    const url = nodeId ? '/admin/dialogues/node/update' : '/admin/dialogues/node/add';
    if (nodeId) data.id = parseInt(nodeId);
    
    const response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    if (result.success) {
        closeModal();
        location.reload();
    }
});

async function deleteNode(nodeId) {
    if (!confirm('Supprimer ce nœud et tous ses enfants ?')) return;
    
    const response = await fetch('/admin/dialogues/node/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: nodeId})
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}

function findNode(nodes, id) {
    for (const node of nodes) {
        if (node.id === id) return node;
        if (node.children) {
            const found = findNode(node.children, id);
            if (found) return found;
        }
    }
    return null;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

renderTree();
</script>



<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
