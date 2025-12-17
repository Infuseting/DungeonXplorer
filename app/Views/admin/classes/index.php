<?php
$pageTitle = 'Gestion des Classes';
ob_start();
?>

<div class="mb-6 flex justify-between items-center">
    <div class="flex gap-4">
        <!-- Search could be implemented later -->
    </div>
    <a href="/admin/classes/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
        <span>➕</span> Nouvelle Classe
    </a>
</div>

<div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
    <table class="w-full text-left text-gray-400">
        <thead class="bg-gray-800/50 text-gray-200 uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Nom</th>
                <th class="px-6 py-4">Description</th>
                <th class="px-6 py-4">Stats de Base</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            <?php foreach ($classes as $class): ?>
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="px-6 py-4 font-medium text-white">
                        <?= htmlspecialchars($class['name']) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400 max-w-md truncate">
                        <?= htmlspecialchars($class['description'] ?? '') ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?php 
                        $stats = json_decode($class['base_stats_json'] ?? '[]', true);
                        if ($stats): 
                            foreach ($stats as $k => $v): ?>
                                <span class="mr-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-800 text-gray-300">
                                    <?= substr(ucfirst($k), 0, 3) ?>: <?= $v ?>
                                </span>
                            <?php endforeach;
                        endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/admin/classes/edit/<?= $class['id'] ?>" class="p-2 hover:bg-blue-500/10 text-blue-400 rounded-lg">
                                ✏️
                            </a>
                            <form action="/admin/classes/delete/<?= $class['id'] ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?');" class="inline">
                                <button type="submit" class="p-2 hover:bg-red-500/10 text-red-400 rounded-lg">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($classes)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        Aucune classe trouvée.
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
