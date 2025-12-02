<?php
$pageTitle = 'Gestion des Dialogues';
ob_start();
?>

<style>
    .dialogue-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .dialogue-card {
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: all 0.3s;
    }
    
    .dialogue-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    
    .dialogue-header {
        display: flex;
        align-items: start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .dialogue-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-light);
    }
    
    .dialogue-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: var(--text-muted);
    }
    
    .dialogue-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Arbres de Dialogue</h3>
        <a href="/admin/dialogues/create" class="btn btn-primary">
            ➕ Créer un arbre
        </a>
    </div>
    
    <!-- Search Bar -->
    <div style="margin-bottom: 1.5rem;">
        <input 
            type="text" 
            id="search-input" 
            placeholder="🔍 Rechercher un arbre..."
            style="width: 100%; padding: 0.75rem; background: var(--bg-darker); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text-light);"
            value="<?= htmlspecialchars($search ?? '') ?>"
        >
    </div>
    
    <!-- Dialogue Trees Grid -->
    <div class="dialogue-grid">
        <?php if (!empty($trees)): ?>
            <?php foreach ($trees as $tree): ?>
                <div class="dialogue-card">
                    <div class="dialogue-header">
                        <div>
                            <div class="dialogue-title">
                                🌳 <?= htmlspecialchars($tree['name']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($tree['description']): ?>
                        <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.875rem;">
                            <?= htmlspecialchars($tree['description']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="dialogue-stats">
                        <span>📝 <?= $tree['node_count'] ?> nœuds</span>
                        <span>👥 <?= $tree['npc_count'] ?> PNJ</span>
                    </div>
                    
                    <div class="dialogue-actions">
                        <a href="/admin/dialogues/tree/<?= $tree['id'] ?>" class="btn btn-primary" style="flex: 1;">
                            🌳 Éditeur
                        </a>
                        <a href="/admin/dialogues/edit/<?= $tree['id'] ?>" class="btn btn-secondary">
                            ✏️
                        </a>
                        <button 
                            class="btn btn-danger" 
                            onclick="deleteTree(<?= $tree['id'] ?>)"
                        >
                            🗑️
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                Aucun arbre de dialogue trouvé.
                <br><br>
                <a href="/admin/dialogues/create" class="btn btn-primary">
                    ➕ Créer le premier arbre
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Search functionality
let searchTimeout;
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const search = this.value;
        window.location.href = '/admin/dialogues' + (search ? '?search=' + encodeURIComponent(search) : '');
    }, 500);
});

// Delete tree
function deleteTree(id) {
    if (!confirm('Supprimer cet arbre de dialogue ? Tous les nœuds seront également supprimés.')) return;
    
    fetch(`/admin/dialogues/delete/${id}`, {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
