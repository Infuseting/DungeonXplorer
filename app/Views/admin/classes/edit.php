<?php
$pageTitle = isset($class) ? 'Modifier la Classe' : 'Nouvelle Classe';
$isEdit = isset($class);
ob_start();
?>

<div class="mb-6">
    <a href="/admin/classes" class="text-gray-400 hover:text-white flex items-center gap-2 transition-colors mb-4">
        <span>←</span> Retour aux classes
    </a>
    <h1 class="text-2xl font-bold text-white"><?= $pageTitle ?></h1>
</div>

<?php if ($isEdit): ?>
<div class="mb-6 border-b border-gray-800">
    <nav class="-mb-px flex gap-6" aria-label="Tabs">
        <button onclick="switchTab('details')" id="tab-details" class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-400" aria-current="page">
            Détails & Compétences
        </button>
        <button onclick="switchTab('tree')" id="tab-tree" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-400 hover:text-gray-300 hover:border-gray-300">
            Arbre Visuel
        </button>
    </nav>
</div>
<?php endif; ?>

<div id="content-details" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Formulaire Classe -->
    <div class="lg:col-span-1 space-y-6">
        <form action="<?= $isEdit ? '/admin/classes/update/' . $class['id'] : '/admin/classes/store' ?>" method="POST" class="bg-gray-900 p-6 rounded-xl border border-gray-800">
            <h2 class="text-lg font-semibold text-white mb-4">Détails de la Classe</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Nom</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($class['name'] ?? '') ?>" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($class['description'] ?? '') ?></textarea>
                </div>

                <div class="border-t border-gray-800 pt-4">
                    <h3 class="text-sm font-medium text-gray-300 mb-3">Stats de Base</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php 
                        $stats = $isEdit ? ($class['base_stats'] ?? []) : [];
                        $defaultStats = ['strength' => 10, 'dexterity' => 10, 'intelligence' => 10, 'vitality' => 10];
                        ?>
                        <?php foreach ($defaultStats as $stat => $defaultVal): ?>
                            <div>
                                <label class="block text-xs uppercase text-gray-500 mb-1"><?= $stat ?></label>
                                <input type="number" name="<?= $stat ?>" value="<?= $stats[$stat] ?? $defaultVal ?>" 
                                       class="w-full bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white text-sm">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <?= $isEdit ? 'Mettre à jour' : 'Créer la classe' ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Gestion des Compétences (Seulement en mode édition) -->
    <?php if ($isEdit): ?>
    <div class="lg:col-span-2">
        <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-white">Compétences de Classe</h2>
                <a href="/admin/classes/skills/add/<?= $class['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-3 py-1.5 rounded-lg transition-colors flex items-center gap-2">
                    <span>➕</span> Ajouter
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-gray-400">
                    <thead class="bg-gray-800/50 text-gray-200 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Lvl</th>
                            <th class="px-6 py-3">Nom</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Coût</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <?php if (isset($skills) && count($skills) > 0): ?>
                            <?php foreach ($skills as $skill): ?>
                                <tr class="hover:bg-gray-800/30">
                                    <td class="px-6 py-3 text-sm"><?= $skill['min_level'] ?></td>
                                    <td class="px-6 py-3 font-medium text-white"><?= htmlspecialchars($skill['name']) ?></td>
                                    <td class="px-6 py-3 text-xs uppercase">
                                        <span class="px-2 py-0.5 rounded <?= $skill['type'] === 'active' ? 'bg-blue-900 text-blue-200' : 'bg-green-900 text-green-200' ?>">
                                            <?= $skill['type'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-xs">
                                        <?php if($skill['cost_mp'] > 0) echo "<span class='text-blue-400'>{$skill['cost_mp']} MP</span> "; ?>
                                        <?php if($skill['cost_sp'] > 0) echo "<span class='text-yellow-400'>{$skill['cost_sp']} SP</span>"; ?>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="/admin/classes/skills/edit/<?= $skill['id'] ?>" class="text-blue-400 hover:text-blue-300">✏️</a>
                                            <form action="/admin/classes/skills/delete/<?= $skill['id'] ?>" method="POST" onsubmit="return confirm('Supprimer cette compétence ?');" class="inline">
                                                <button type="submit" class="text-red-400 hover:text-red-300">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                    Aucune compétence configurée pour cette classe.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Visual Editor Tab -->
<?php if ($isEdit): ?>
<div id="content-tree" class="hidden">
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-white">Éditeur Visuel</h2>
            <div class="space-x-2">
                <p class="inline text-sm text-gray-500 mr-4">Glissez les nœuds pour organiser l'arbre.</p>
                <button id="savePositionsBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                    💾 Sauvegarder les positions
                </button>
            </div>
        </div>
        <div class="relative w-full h-[600px] bg-slate-950 rounded-lg overflow-hidden border border-gray-700" id="canvasContainer">
            <canvas id="skillTreeCanvas" class="w-full h-full block cursor-grab active:cursor-grabbing"></canvas>
        </div>
    </div>
</div>

<script>
    window.skillNodes = <?= json_encode($skills ?? []) ?>;

    function switchTab(tab) {
        // Toggle Buttons
        const btnDetails = document.getElementById('tab-details');
        const btnTree = document.getElementById('tab-tree');
        
        if (tab === 'details') {
            btnDetails.className = 'border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-400';
            btnTree.className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-400 hover:text-gray-300 hover:border-gray-300';
            document.getElementById('content-details').classList.remove('hidden');
            document.getElementById('content-tree').classList.add('hidden');
        } else {
            btnDetails.className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-400 hover:text-gray-300 hover:border-gray-300';
            btnTree.className = 'border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-400';
            document.getElementById('content-details').classList.add('hidden');
            document.getElementById('content-tree').classList.remove('hidden');
            // Trigger Resize for Canvas
            window.dispatchEvent(new Event('resize'));
        }
    }
</script>
<script src="/js/modules/admin/skill-tree.js"></script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
