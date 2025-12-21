<?php
$pageTitle = "Gestion des Quêtes";
ob_start();
?>

<div class="flex justify-between items-center mb-8">
    <h1>📜 Gestion des Quêtes</h1>
    <div class="flex gap-4">
        <a href="/admin/quests/daily" class="btn bg-purple-600 hover:bg-purple-500">⏰ Quêtes Quotidiennes</a>
        <form method="GET" class="inline-block">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">🔍</button>
        </form>
        <a href="/admin/quests/create" class="btn btn-primary">➕ Nouvelle Quête</a>
    </div>
</div>

<?php if (empty($quests)): ?>
    <div class="text-center p-12 bg-white rounded-lg border-2 border-dashed border-gray-300">
        <p>Aucune quête trouvée.</p>
        <a href="/admin/quests/create" class="btn btn-primary">Créer la première quête</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-6">
        <?php foreach ($quests as $quest): ?>
            <div class="bg-white border border-gray-200 rounded-lg p-6 transition-shadow duration-200 hover:shadow-lg">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="m-0 text-xl text-gray-800"><?= htmlspecialchars($quest['name']) ?></h3>
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm">Niveau <?= $quest['min_level'] ?></span>
                </div>
                
                <p class="text-gray-600 mb-4 leading-relaxed"><?= htmlspecialchars(substr($quest['description'] ?? '', 0, 150)) ?><?= strlen($quest['description'] ?? '') > 150 ? '...' : '' ?></p>
                
                <div class="flex gap-4 mb-4 pt-4 border-t border-gray-100 text-sm text-gray-500">
                    <span>📋 <?= $quest['stage_count'] ?> étape(s)</span>
                </div>
                
                <div class="flex gap-2">
                    <a href="/admin/quests/edit/<?= $quest['id'] ?>" class="btn px-4 py-2 text-sm">✏️ Éditer</a>
                    <form method="POST" action="/admin/quests/delete/<?= $quest['id'] ?>" class="inline" onsubmit="return confirm('Supprimer cette quête ?');">
                        <button type="submit" class="btn px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/layouts/admin.php';
?>
