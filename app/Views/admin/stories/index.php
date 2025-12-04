<?php
$pageTitle = 'Gestion des Histoires';
ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-100">Histoires & Donjons</h1>
    <a href="/admin/stories/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
        <span>➕</span> Nouvelle Histoire
    </a>
</div>

<div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-900 text-gray-400 border-b border-gray-700">
                    <th class="p-4 font-medium">ID</th>
                    <th class="p-4 font-medium">Nom</th>
                    <th class="p-4 font-medium">Type</th>
                    <th class="p-4 font-medium">Niveau Min</th>
                    <th class="p-4 font-medium">Difficulté</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php if (empty($stories)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            Aucune histoire créée pour le moment.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stories as $story): ?>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <td class="p-4 text-gray-300">#<?= $story['id'] ?></td>
                            <td class="p-4">
                                <div class="font-medium text-gray-100"><?= htmlspecialchars($story['name']) ?></div>
                                <div class="text-sm text-gray-500 truncate max-w-xs"><?= htmlspecialchars($story['description']) ?></div>
                            </td>
                            <td class="p-4">
                                <?php if ($story['type'] === 'manual'): ?>
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-900 text-blue-200 rounded-full border border-blue-700">Manuelle</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium bg-purple-900 text-purple-200 rounded-full border border-purple-700">Procédurale</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-gray-300">Niv. <?= $story['min_level'] ?></td>
                            <td class="p-4">
                                <div class="flex items-center gap-1">
                                    <?php for($i=0; $i<$story['difficulty_level']; $i++): ?>
                                        <span class="text-yellow-500">💀</span>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <?php if ($story['type'] === 'manual'): ?>
                                    <a href="/admin/stories/<?= $story['id'] ?>/nodes" class="text-indigo-400 hover:text-indigo-300 font-medium" title="Gérer les nœuds">
                                        🗺️ Nœuds
                                    </a>
                                <?php endif; ?>
                                
                                <a href="/admin/stories/edit/<?= $story['id'] ?>" class="text-blue-400 hover:text-blue-300 font-medium">
                                    ✏️ Éditer
                                </a>
                                
                                <form action="/admin/stories/delete/<?= $story['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette histoire ?');">
                                    <button type="submit" class="text-red-400 hover:text-red-300 font-medium">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
