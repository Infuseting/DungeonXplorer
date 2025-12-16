<?php
$title = "Gestion des Factions";
ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-100">Factions</h1>
    <a href="/admin/factions/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
        <i class="fas fa-plus mr-2"></i>Nouvelle Faction
    </a>
</div>

<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-700">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-gray-300">
            <thead class="bg-gray-900 text-gray-100 uppercase text-sm font-semibold">
                <tr>
                    <th class="px-6 py-4">Nom</th>
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php if (empty($factions)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">
                            Aucune faction créée
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($factions as $faction): ?>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">
                                <?= htmlspecialchars($faction['name']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm max-w-xs truncate">
                                <?= htmlspecialchars($faction['description'] ?? '') ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="/admin/factions/edit/<?= $faction['id'] ?>" class="text-blue-400 hover:text-blue-300 transition-colors" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/admin/factions/delete/<?= $faction['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette faction ?');">
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors" title="Supprimer">
                                        <i class="fas fa-trash"></i>
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
require __DIR__ . '/../../layouts/admin.php';
