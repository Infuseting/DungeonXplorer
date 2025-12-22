<?php
$pageTitle = 'Gestion des PNJ';
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Gestion des PNJ</h3>
        <a href="/admin/npcs/create" class="btn btn-primary">
            ➕ Créer un PNJ
        </a>
    </div>
    
    <!-- Search and Filters -->
    <div class="flex items-center gap-4 mb-6">
        <input 
            type="text" 
            id="search-input" 
            class="form-input flex-1"
            placeholder="🔍 Rechercher par nom..."
            value="<?= htmlspecialchars($search ?? '') ?>"
        >
        
        <select id="role-filter" class="form-select w-auto">
            <option value="">Tous les rôles</option>
            <option value="merchant" <?= ($roleFilter ?? '') === 'merchant' ? 'selected' : '' ?>>Marchand</option>
            <option value="quest_giver" <?= ($roleFilter ?? '') === 'quest_giver' ? 'selected' : '' ?>>Donneur de quêtes</option>
            <option value="lore" <?= ($roleFilter ?? '') === 'lore' ? 'selected' : '' ?>>Lore</option>
            <option value="guard" <?= ($roleFilter ?? '') === 'guard' ? 'selected' : '' ?>>Garde</option>
        </select>
        
        <button class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
    </div>
    
    <!-- NPCs Grid -->
    <?php if (!empty($npcs)): ?>
        <div class="grid grid-cols-[repeat(auto-fill,minmax(320px,1fr))] gap-6 mt-6">
            <?php foreach ($npcs as $npc): ?>
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-200 hover:border-indigo-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/30">
                    <div class="flex gap-4 mb-4">
                        <div class="w-16 h-16 bg-gray-900 border-2 border-gray-700 rounded-lg flex items-center justify-center overflow-hidden">
                            <?php if ($npc['texture']): ?>
                                <img src="/<?= htmlspecialchars($npc['texture']) ?>" alt="<?= htmlspecialchars($npc['name']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-[32px]">👤</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <div class="text-lg font-semibold text-gray-200 mb-1">
                                <?= htmlspecialchars($npc['name']) ?>
                            </div>
                            <?php
                                $roles = array_map('trim', explode(',', $npc['role'] ?? ''));
                                foreach ($roles as $r):
                                    $roleClass = match($r) {
                                        'merchant' => 'bg-yellow-500/20 text-yellow-300',
                                        'quest_giver' => 'bg-purple-500/20 text-purple-300',
                                        'lore' => 'bg-indigo-500/20 text-indigo-300',
                                        'guard' => 'bg-red-500/20 text-red-300',
                                        default => 'bg-gray-700 text-gray-300'
                                    };
                            ?>
                                <span class="px-3 py-1 rounded text-xs font-medium uppercase <?= $roleClass ?>">
                                    <?= str_replace('_', ' ', $r) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Merchant Info -->
                    <?php $rolesArr = array_map('trim', explode(',', $npc['role'] ?? '')); ?>
                    <?php if (in_array('merchant', $rolesArr) && $npc['merchant_seed']): ?>
                        <div class="bg-gray-900 p-3 rounded-lg mb-4">
                            <div class="text-xs text-gray-400 mb-1">SEED Marchand</div>
                            <div class="text-gray-200 font-semibold font-mono">
                                #<?= $npc['merchant_seed'] ?>
                            </div>
                            <div class="text-xs text-gray-400 mt-2">
                                Rachat: <?= ($npc['buy_rate_own'] * 100) ?>% / <?= ($npc['buy_rate_other'] * 100) ?>%
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="/admin/npcs/edit/<?= $npc['id'] ?>" class="btn btn-sm btn-primary flex-1">
                            ✏️ Modifier
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteNPC(<?= $npc['id'] ?>)">
                            🗑️
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16 text-gray-400">
            <p class="text-5xl mb-4">👥</p>
            <p>Aucun PNJ trouvé</p>
            <a href="/admin/npcs/create" class="btn btn-primary mt-4">
                Créer le premier PNJ
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
let searchTimeout;

document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
});

document.getElementById('role-filter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const role = document.getElementById('role-filter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (role) params.append('role', role);
    
    window.location.href = '/admin/npcs?' + params.toString();
}

function resetFilters() {
    window.location.href = '/admin/npcs';
}

function deleteNPC(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce PNJ ?')) return;
    
    fetch(`/admin/npcs/delete/${id}`, {
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
