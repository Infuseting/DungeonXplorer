<?php
$pageTitle = 'Éditeur d\'Arbre - ' . htmlspecialchars($tree['name']);
ob_start();
?>

<style>
    .tree-container {
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 2rem;
        min-height: 500px;
    }
    
    .tree-node {
        margin: 0.5rem 0;
        padding: 1rem;
        background: var(--bg-dark);
        border: 2px solid var(--border);
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    
    .tree-node:hover {
        border-color: var(--primary);
    }
    
    .tree-node.npc {
        border-left: 4px solid #6366f1;
    }
    
    .tree-node.player {
        border-left: 4px solid #fbbf24;
    }
    
    .node-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .node-icon {
        font-size: 1.5rem;
    }
    
    .node-text {
        flex: 1;
        color: var(--text-light);
    }
    
    .node-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .node-children {
        margin-left: 2rem;
        border-left: 2px dashed var(--border);
        padding-left: 1rem;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: var(--bg-dark);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 2rem;
        max-width: 600px;
        width: 90%;
    }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">
            🌳 <?= htmlspecialchars($tree['name']) ?>
        </h3>
        <div style="display: flex; gap: 1rem;">
            <a href="/admin/dialogues/edit/<?= $tree['id'] ?>" class="btn btn-secondary">
                ✏️ Modifier l'arbre
            </a>
            <a href="/admin/dialogues" class="btn btn-secondary">
                ← Retour
            </a>
        </div>
    </div>
    
    <?php if ($tree['description']): ?>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
            <?= htmlspecialchars($tree['description']) ?>
        </p>
    <?php endif; ?>
    
    <div style="margin-bottom: 1.5rem;">
        <button class="btn btn-primary" onclick="addRootNode()">
            ➕ Ajouter nœud racine (PNJ)
        </button>
    </div>
    
    <div class="tree-container" id="tree-container">
        <!-- Tree will be rendered here by JavaScript -->
    </div>
</div>

<!-- Add/Edit Node Modal -->
<div id="node-modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-bottom: 1.5rem; color: var(--text-light);">
            <span id="modal-title">Ajouter un nœud</span>
        </h3>
        
        <form id="node-form">
            <input type="hidden" id="node-id">
            <input type="hidden" id="parent-id">
            <input type="hidden" id="is-player-choice">
            
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Texte *</label>
                <textarea 
                    id="node-text" 
                    rows="4" 
                    required
                    style="width: 100%; padding: 0.75rem; background: var(--bg-darker); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text-light); resize: vertical;"
                    placeholder="Texte du dialogue ou du choix..."
                ></textarea>
            </div>
            
            <div id="choice-text-group" style="margin-bottom: 1rem; display: none;">
                <label class="form-label">Texte du bouton de choix</label>
                <input 
                    type="text" 
                    id="choice-text"
                    style="width: 100%; padding: 0.75rem; background: var(--bg-darker); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text-light);"
                    placeholder="Texte affiché sur le bouton..."
                >
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
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

// Render tree
function renderTree() {
    const container = document.getElementById('tree-container');
    container.innerHTML = '';
    
    if (treeData.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 3rem;">Aucun nœud. Ajoutez un nœud racine pour commencer.</p>';
        return;
    }
    
    treeData.forEach(node => {
        container.appendChild(renderNode(node, 0));
    });
}

// Render single node
function renderNode(node, level) {
    const div = document.createElement('div');
    div.className = `tree-node ${node.is_player_choice ? 'player' : 'npc'}`;
    div.style.marginLeft = `${level * 2}rem`;
    
    const icon = node.is_player_choice ? '💬' : '📝';
    const type = node.is_player_choice ? 'Joueur' : 'PNJ';
    const displayText = node.is_player_choice && node.choice_text ? node.choice_text : node.text;
    
    div.innerHTML = `
        <div class="node-content">
            <span class="node-icon">${icon}</span>
            <div class="node-text">
                <strong>${type}:</strong> ${escapeHtml(displayText)}
            </div>
            <div class="node-actions">
                <button class="btn btn-sm btn-primary" onclick="editNode(${node.id})">✏️</button>
                <button class="btn btn-sm btn-secondary" onclick="addChildNode(${node.id}, 0)">+ PNJ</button>
                <button class="btn btn-sm btn-secondary" onclick="addChildNode(${node.id}, 1)">+ Joueur</button>
                <button class="btn btn-sm btn-danger" onclick="deleteNode(${node.id})">🗑️</button>
            </div>
        </div>
    `;
    
    if (node.children && node.children.length > 0) {
        const childrenDiv = document.createElement('div');
        childrenDiv.className = 'node-children';
        node.children.forEach(child => {
            childrenDiv.appendChild(renderNode(child, level + 1));
        });
        div.appendChild(childrenDiv);
    }
    
    return div;
}

// Add root node
function addRootNode() {
    openModal('Ajouter nœud racine (PNJ)', null, null, 0);
}

// Add child node
function addChildNode(parentId, isPlayerChoice) {
    const type = isPlayerChoice ? 'Joueur' : 'PNJ';
    openModal(`Ajouter réponse ${type}`, null, parentId, isPlayerChoice);
}

// Edit node
function editNode(nodeId) {
    const node = findNode(treeData, nodeId);
    if (!node) return;
    
    openModal('Modifier le nœud', node);
}

// Open modal
function openModal(title, node = null, parentId = null, isPlayerChoice = 0) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('node-id').value = node ? node.id : '';
    document.getElementById('parent-id').value = parentId || '';
    document.getElementById('is-player-choice').value = node ? node.is_player_choice : isPlayerChoice;
    document.getElementById('node-text').value = node ? node.text : '';
    document.getElementById('choice-text').value = node ? (node.choice_text || '') : '';
    
    const choiceGroup = document.getElementById('choice-text-group');
    choiceGroup.style.display = (node ? node.is_player_choice : isPlayerChoice) ? 'block' : 'none';
    
    document.getElementById('node-modal').classList.add('active');
}

// Close modal
function closeModal() {
    document.getElementById('node-modal').classList.remove('active');
    document.getElementById('node-form').reset();
}

// Save node
document.getElementById('node-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const nodeId = document.getElementById('node-id').value;
    const data = {
        tree_id: treeId,
        parent_id: document.getElementById('parent-id').value || null,
        text: document.getElementById('node-text').value,
        is_player_choice: parseInt(document.getElementById('is-player-choice').value),
        choice_text: document.getElementById('choice-text').value || null,
        order_index: 0
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

// Delete node
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

// Helper functions
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

// Initial render
renderTree();
</script>

<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-light);
    }
    
    .btn-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.5);
    }
    
    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.3);
    }
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
