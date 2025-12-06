<?php
$pageTitle = 'Pools de Monstres : ' . htmlspecialchars($template['name']);
ob_start();
?>

<div class="flex items-center gap-4 mb-6">
    <a href="/admin/procedural" class="text-gray-400 hover:text-white transition-colors">
        ⬅️ Retour
    </a>
    <h1 class="text-2xl font-bold text-gray-100">Monstres : <?= htmlspecialchars($template['name']) ?></h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- List -->
    <div class="lg:col-span-2 bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-700 bg-gray-900">
            <h2 class="font-bold text-gray-100">Pool Actuel</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-900 text-gray-400 border-b border-gray-700">
                        <th class="p-3 font-medium">Monstre</th>
                        <th class="p-3 font-medium">Niveau</th>
                        <th class="p-3 font-medium">Poids</th>
                        <th class="p-3 font-medium">Qté</th>
                        <th class="p-3 font-medium">Type</th>
                        <th class="p-3 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php if (empty($pools)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                Aucun monstre dans le pool.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pools as $pool): ?>
                            <tr class="hover:bg-gray-750">
                                <td class="p-3 text-gray-200"><?= htmlspecialchars($pool['monster_name']) ?></td>
                                <td class="p-3 text-gray-400"><?= $pool['min_level'] ?> - <?= $pool['max_level'] ?></td>
                                <td class="p-3 text-gray-400"><?= $pool['spawn_weight'] ?></td>
                                <td class="p-3 text-gray-400"><?= $pool['min_quantity'] ?> - <?= $pool['max_quantity'] ?></td>
                                <td class="p-3">
                                    <?php if ($pool['is_boss']): ?>
                                        <span class="text-red-400 text-xs border border-red-400 px-1 rounded">BOSS</span>
                                    <?php elseif ($pool['boss_room_only']): ?>
                                        <span class="text-orange-400 text-xs border border-orange-400 px-1 rounded">Boss Room</span>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-xs">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-right">
                                    <form action="/admin/procedural/monsters/delete/<?= $pool['id'] ?>" method="POST" onsubmit="return confirm('Supprimer ?');">
                                        <button type="submit" class="text-red-400 hover:text-red-300">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Form -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 h-fit">
        <div class="p-4 border-b border-gray-700 bg-gray-900">
            <h2 class="font-bold text-gray-100">Ajouter un Monstre</h2>
        </div>
        <form action="/admin/procedural/<?= $template['id'] ?>/monsters/add" method="POST" class="p-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nom du Monstre</label>
                <input type="text" name="monster_name" required class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white">
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Niveau Min</label>
                    <input type="number" name="min_level" value="1" min="1" required class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Niveau Max</label>
                    <input type="number" name="max_level" value="3" min="1" required class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Qté Min</label>
                    <input type="number" name="min_quantity" value="1" min="1" required class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Qté Max</label>
                    <input type="number" name="max_quantity" value="1" min="1" required class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Poids de Spawn (Fréquence)</label>
                <input type="number" name="spawn_weight" value="100" min="1" required class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white">
            </div>

            <div class="space-y-2 pt-2">
                <label class="flex items-center gap-2 text-gray-300 text-sm">
                    <input type="checkbox" name="is_boss" value="1" class="rounded bg-gray-900 border-gray-700 text-indigo-600">
                    Est un Boss
                </label>
                <label class="flex items-center gap-2 text-gray-300 text-sm">
                    <input type="checkbox" name="boss_room_only" value="1" class="rounded bg-gray-900 border-gray-700 text-indigo-600">
                    Salle du Boss uniquement
                </label>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-medium transition-colors">
                Ajouter
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
