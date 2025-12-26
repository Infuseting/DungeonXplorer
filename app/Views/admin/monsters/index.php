<?php
$pageTitle = 'Gestion des Monstres';
ob_start();
?>

<div class="mb-6 flex justify-between items-center">
    <div class="flex gap-4">
        <div class="relative">
            <input type="text" placeholder="Rechercher..." class="bg-gray-800 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
        </div>
    </div>
    <a href="/admin/monsters/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
        <span>➕</span> Nouveau Monstre
    </a>
</div>

<div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
    <table class="w-full text-left text-gray-400">
        <thead class="bg-gray-800/50 text-gray-200 uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Nom</th>
                <th class="px-6 py-4">Image</th>
                <th class="px-6 py-4">Niveaux</th>
                <th class="px-6 py-4">Salle Default</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            <?php foreach ($monsters as $monster): ?>
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-white">
                        <?= htmlspecialchars($monster['name']) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($monster['image_path']): ?>
                            <img src="<?= htmlspecialchars($monster['image_path']) ?>" alt="Monster" class="w-10 h-10 object-cover rounded bg-gray-800">
                        <?php else: ?>
                            <span class="text-gray-600 text-sm">Aucune</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        Lvl <?= $monster['level_min'] ?> - <?= $monster['level_max'] ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                         <?= htmlspecialchars($monster['salle_path'] ?? '-') ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/admin/monsters/edit/<?= $monster['id'] ?>" class="p-2 hover:bg-blue-500/10 text-blue-400 rounded-lg">
                                ✏️
                            </a>
                            <form action="/admin/monsters/delete/<?= $monster['id'] ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce monstre ?');" class="inline">
                                <button type="submit" class="p-2 hover:bg-red-500/10 text-red-400 rounded-lg">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($monsters)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Aucun monstre trouvé.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
