<?php
$pageTitle = 'Gestion des Dialogues';
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Arbres de Dialogue</h3>
        <a href="/admin/dialogues/create" class="btn btn-primary">
            ➕ Créer un arbre
        </a>
    </div>
    
    <!-- Search Bar -->
    <div class="mb-6">
        <input 
            type="text" 
            id="search-input" 
            placeholder="🔍 Rechercher un arbre..."
            class="w-full p-3 bg-gray-900 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"
            value="<?= htmlspecialchars($search ?? '') ?>"
        >
    </div>
    
    <!-- Dialogue Trees Grid -->
    <div class="grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-6 mt-6">
        <?php if (!empty($trees)): ?>
            <?php foreach ($trees as $tree): ?>
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-300 hover:border-indigo-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/20">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-xl font-semibold text-gray-100">
                                🌳 <?= htmlspecialchars($tree['name']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($tree['description']): ?>
                        <p class="text-gray-400 mb-4 text-sm">
                            <?= htmlspecialchars($tree['description']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="flex gap-4 mb-4 text-sm text-gray-400">
                        <span>📝 <?= $tree['node_count'] ?> nœuds</span>
                        <span>👥 <?= $tree['npc_count'] ?> PNJ</span>
                    </div>
                    
                    <div class="flex gap-2 mt-4">
                        <a href="/admin/dialogues/tree/<?= $tree['id'] ?>" class="btn btn-primary flex-1">
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
            <div class="col-span-full text-center p-12 text-gray-400">
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
let searchTimeout;
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const search = this.value;
        window.location.href = '/admin/dialogues' + (search ? '?search=' + encodeURIComponent(search) : '');
    }, 500);
});

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
