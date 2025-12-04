<?php
$pageTitle = 'Templates Procéduraux';
ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-100">Templates de Génération</h1>
    <a href="/admin/procedural/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
        <span>➕</span> Nouveau Template
    </a>
</div>

<div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-900 text-gray-400 border-b border-gray-700">
                    <th class="p-4 font-medium">ID</th>
                    <th class="p-4 font-medium">Nom</th>
                    <th class="p-4 font-medium">Pièces (Min-Max)</th>
                    <th class="p-4 font-medium">Densité</th>
                    <th class="p-4 font-medium">Difficulté</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php if (empty($templates)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            Aucun template créé pour le moment.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($templates as $template): ?>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <td class="p-4 text-gray-300">#<?= $template['id'] ?></td>
                            <td class="p-4">
                                <div class="font-medium text-gray-100"><?= htmlspecialchars($template['name']) ?></div>
                                <div class="text-sm text-gray-500 truncate max-w-xs"><?= htmlspecialchars($template['description']) ?></div>
                            </td>
                            <td class="p-4 text-gray-300">
                                <?= $template['min_rooms'] ?> - <?= $template['max_rooms'] ?>
                            </td>
                            <td class="p-4 text-gray-300">
                                <?= $template['connection_density'] * 100 ?>%
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs font-medium bg-gray-700 text-gray-300 rounded-full border border-gray-600">
                                    <?= ucfirst($template['difficulty_scaling']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="/admin/procedural/edit/<?= $template['id'] ?>" class="text-blue-400 hover:text-blue-300 font-medium">
                                    ✏️ Éditer
                                </a>
                                
                                <form action="/admin/procedural/delete/<?= $template['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce template ?');">
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
