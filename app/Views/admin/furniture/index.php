<?php
$pageTitle = 'Gestion des Meubles';
ob_start();

$rarityColors = [
    'common' => 'bg-gray-600 text-gray-200',
    'uncommon' => 'bg-green-600 text-white',
    'rare' => 'bg-blue-600 text-white',
    'epic' => 'bg-purple-600 text-white',
    'legendary' => 'bg-amber-500 text-black'
];
?>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">🪑 Gestion des Meubles</h3>
        <a href="/admin/furniture/create" class="btn btn-primary">
            ➕ Créer un Meuble
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-900/50 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <?php
            $messages = [
                'created' => '✅ Meuble créé avec succès',
                'updated' => '✅ Meuble mis à jour',
                'deleted' => '✅ Meuble supprimé'
            ];
            echo $messages[$_GET['success']] ?? '✅ Opération réussie';
            ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="flex items-center gap-4 mb-6">
        <select id="category-filter" class="form-select" onchange="filterByCategory(this.value)">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <select id="rarity-filter" class="form-select" onchange="filterByRarity(this.value)">
            <option value="">Toutes les raretés</option>
            <option value="common">Commun</option>
            <option value="uncommon">Peu commun</option>
            <option value="rare">Rare</option>
            <option value="epic">Épique</option>
            <option value="legendary">Légendaire</option>
        </select>
    </div>

    <!-- Furniture Grid -->
    <?php if (!empty($furniture)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="furniture-grid">
            <?php foreach ($furniture as $item): ?>
                <div class="furniture-item bg-gray-800 border border-gray-700 rounded-xl p-4 transition-all duration-200 hover:border-violet-500"
                     data-category="<?= $item['category_id'] ?>"
                     data-rarity="<?= $item['rarity'] ?>">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="text-3xl"><?= $item['icon'] ?? '🪑' ?></div>
                        <span class="px-2 py-1 text-xs rounded <?= $rarityColors[$item['rarity']] ?? $rarityColors['common'] ?>">
                            <?= ucfirst($item['rarity']) ?>
                        </span>
                    </div>
                    
                    <!-- Info -->
                    <h4 class="font-bold text-white mb-1"><?= htmlspecialchars($item['name']) ?></h4>
                    <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($item['category_name'] ?? 'Sans catégorie') ?></p>
                    <p class="text-sm text-gray-400 mb-3 line-clamp-2"><?= htmlspecialchars($item['description'] ?? '') ?></p>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-1 text-xs mb-3">
                        <div class="text-gray-400">💰 Prix:</div>
                        <div class="text-amber-400"><?= number_format($item['price'], 0, ',', ' ') ?></div>
                        
                        <?php if ($item['bonus_type'] !== 'none'): ?>
                            <div class="text-gray-400">✨ Bonus:</div>
                            <div class="text-green-400">+<?= $item['bonus_value'] ?> <?= $item['bonus_type'] ?></div>
                        <?php endif; ?>
                        
                        <div class="text-gray-400">⭐ Niveau:</div>
                        <div class="text-white"><?= $item['required_level'] ?></div>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs mb-3">
                        <span class="<?= $item['is_available'] ? 'text-green-400' : 'text-red-400' ?>">
                            <?= $item['is_available'] ? '✅ Disponible' : '❌ Indisponible' ?>
                        </span>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="/admin/furniture/edit/<?= $item['id'] ?>" class="btn btn-sm btn-secondary flex-1 text-center">
                            ✏️
                        </a>
                        <form action="/admin/furniture/delete/<?= $item['id'] ?>" method="POST" onsubmit="return confirm('Supprimer ce meuble ?')">
                            <button type="submit" class="btn btn-sm btn-danger">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <span class="text-6xl mb-4 block">🪑</span>
            <p class="text-gray-400">Aucun meuble créé</p>
            <a href="/admin/furniture/create" class="btn btn-primary mt-4">Créer le premier meuble</a>
        </div>
    <?php endif; ?>
</div>

<script>
function filterByCategory(categoryId) {
    document.querySelectorAll('.furniture-item').forEach(item => {
        if (!categoryId || item.dataset.category === categoryId) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function filterByRarity(rarity) {
    document.querySelectorAll('.furniture-item').forEach(item => {
        if (!rarity || item.dataset.rarity === rarity) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
