<?php
$pageTitle = "Gestion des Quêtes";
ob_start();
?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold">📜 Gestion des Quêtes</h1>
    <div class="flex gap-4 items-center">
        <a href="/admin/quests/daily" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">⏰ Quêtes Quotidiennes</a>
            <form method="GET" class="inline-flex items-center gap-2">
                <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:border-indigo-500 focus:outline-none">
                <button type="submit" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-all">🔍</button>
            </form>
        <a href="/admin/quests/create" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">➕ Nouvelle Quête</a>
    </div>
</div>

<?php if (empty($quests)): ?>
    <div class="text-center p-12 bg-slate-900 rounded-xl border-2 border-dashed border-slate-700">
        <p class="text-slate-400 mb-4">Aucune quête trouvée.</p>
        <a href="/admin/quests/create" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all inline-block">Créer la première quête</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-6">
        <?php foreach ($quests as $quest): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 transition-all duration-200 hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/10">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="m-0 text-xl font-semibold text-slate-200"><?= htmlspecialchars($quest['name']) ?></h3>
                    <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">Niveau <?= $quest['min_level'] ?></span>
                </div>
                
                <p class="text-slate-400 mb-4 leading-relaxed"><?= htmlspecialchars(substr($quest['description'] ?? '', 0, 150)) ?><?= strlen($quest['description'] ?? '') > 150 ? '...' : '' ?></p>
                
                <div class="flex gap-4 mb-4 pt-4 border-t border-slate-800 text-sm text-slate-500">
                    <span>📋 <?= $quest['stage_count'] ?> étape(s)</span>
                </div>
                
                <div class="flex gap-2">
                    <a href="/admin/quests/edit/<?= $quest['id'] ?>" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">✏️ Éditer</a>
                    <form method="POST" action="/admin/quests/delete/<?= $quest['id'] ?>" class="inline" onsubmit="return confirm('Supprimer cette quête ?');">
                        <button type="submit" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-500 text-white font-medium rounded-lg transition-all">🗑️</button>
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
