<?php
$pageTitle = 'Gestion des Items';
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Gestion des Items</h3>
        <a href="/admin/items/create" class="btn btn-primary">
            ➕ Créer un Item
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
        
        <select id="type-filter" class="form-select w-auto">
            <option value="">Tous les types</option>
            <option value="equipment" <?= ($typeFilter ?? '') === 'equipment' ? 'selected' : '' ?>>Équipement</option>
            <option value="consumable" <?= ($typeFilter ?? '') === 'consumable' ? 'selected' : '' ?>>Consommable</option>
            <option value="material" <?= ($typeFilter ?? '') === 'material' ? 'selected' : '' ?>>Matériau</option>
        </select>
        
        <select id="slot-filter" class="form-select w-auto">
            <option value="">Tous les slots</option>
            <option value="head" <?= ($slotFilter ?? '') === 'head' ? 'selected' : '' ?>>Tête</option>
            <option value="chest" <?= ($slotFilter ?? '') === 'chest' ? 'selected' : '' ?>>Torse</option>
            <option value="legs" <?= ($slotFilter ?? '') === 'legs' ? 'selected' : '' ?>>Jambes</option>
            <option value="main_hand" <?= ($slotFilter ?? '') === 'main_hand' ? 'selected' : '' ?>>Main principale</option>
            <option value="off_hand" <?= ($slotFilter ?? '') === 'off_hand' ? 'selected' : '' ?>>Main secondaire</option>
        </select>
        
        <button class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
    </div>
    
    <!-- Items Grid -->
    <?php if (!empty($items)): ?>
        <div class="grid grid-cols-[repeat(auto-fill,minmax(300px,1fr))] gap-6 mt-6">
            <?php foreach ($items as $item): ?>
                <?php 
                    $statRanges = json_decode($item['stat_ranges'] ?? '{}', true);
                ?>
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-200 hover:border-indigo-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/30">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="text-lg font-semibold truncate text-gray-200 mb-1"><?= htmlspecialchars($item['name']) ?></div>
                            <?php
                                $typeClass = match($item['type']) {
                                    'equipment' => 'bg-indigo-500/20 text-indigo-300',
                                    'consumable' => 'bg-green-500/20 text-green-300',
                                    'material' => 'bg-yellow-500/20 text-yellow-300',
                                    default => 'bg-gray-700 text-gray-300'
                                };
                            ?>
                            <span class="px-3 py-1 rounded text-xs font-medium uppercase inline-block <?= $typeClass ?>">
                                <?= $item['type'] ?>
                            </span>
                            <?php if ($item['slot_type'] !== 'none'): ?>
                                <span class="ml-2 text-gray-400 text-xs">
                                    📍 <?= $item['slot_type'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="w-12 h-12 min-w-[48px] bg-gray-900 border-2 border-gray-700 rounded-lg flex items-center justify-center text-2xl">
                            <?php if (!empty($item['icon'])): ?>
                                <img loading="lazy" src="/<?= htmlspecialchars($item['icon']) ?>" class="w-full h-full object-cover rounded">
                            <?php else: ?>
                                📦
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($item['description']): ?>
                        <p class="text-gray-400 text-sm mb-4">
                            <?= htmlspecialchars(substr($item['description'], 0, 80)) ?>
                            <?= strlen($item['description']) > 80 ? '...' : '' ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Stats -->
                    <?php if (!empty($statRanges)): ?>
                        <div class="grid grid-cols-2 gap-2 my-4">
                            <?php foreach (['strength' => '💪', 'vitality' => '❤️', 'intelligence' => '🧠', 'dexterity' => '🎯'] as $stat => $icon): ?>
                                <?php if (isset($statRanges[$stat]) && ($statRanges[$stat]['min'] > 0 || $statRanges[$stat]['max'] > 0)): ?>
                                    <div class="bg-gray-900 p-2 rounded-md text-sm">
                                        <div class="text-gray-400 text-xs uppercase"><?= $icon ?> <?= ucfirst($stat) ?></div>
                                        <div class="text-gray-200 font-semibold">
                                            <?= $statRanges[$stat]['min'] ?> - <?= $statRanges[$stat]['max'] ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Properties -->
                    <div class="mt-4 pt-4 border-t border-gray-700 flex gap-4 text-xs text-gray-400 flex-wrap">
                        <span>📏 <?= $item['width'] ?>x<?= $item['height'] ?></span>
                        <span>⚖️ <?= $item['weight'] ?>kg</span>
                        <?php if ($item['two_handed']): ?>
                            <span>🤲 2 mains</span>
                        <?php endif; ?>
                        <?php if ($item['price']): ?>
                            <span class="text-yellow-400 font-semibold">💰 <?= number_format($item['price']) ?> pièces</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-4 flex gap-2">
                        <a href="/admin/items/edit/<?= $item['id'] ?>" class="btn btn-sm btn-primary flex-1">
                            ✏️ Modifier
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteItem(<?= $item['id'] ?>)">
                            🗑️
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16 text-gray-400">
            <p class="text-5xl mb-4">📦</p>
            <p>Aucun item trouvé</p>
            <a href="/admin/items/create" class="btn btn-primary mt-4">
                Créer le premier item
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

document.getElementById('type-filter').addEventListener('change', applyFilters);
document.getElementById('slot-filter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const type = document.getElementById('type-filter').value;
    const slot = document.getElementById('slot-filter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (type) params.append('type', type);
    if (slot) params.append('slot', slot);
    
    window.location.href = '/admin/items?' + params.toString();
}

function resetFilters() {
    window.location.href = '/admin/items';
}

function deleteItem(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet item ?')) return;
    
    fetch(`/admin/items/delete/${id}`, {
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
