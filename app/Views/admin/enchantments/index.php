<?php
$pageTitle = 'Gestion des Enchantements';
ob_start();
?>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">✨ Enchantements</h3>
        <button onclick="openCreateModal()" class="btn btn-primary">
            ➕ Ajouter un enchantement
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-500/20 text-green-400 p-4 rounded-lg mb-6 border border-green-500/50">
            <?php
            if ($_GET['success'] === 'created')
                echo "L'enchantement a été créé avec succès.";
            if ($_GET['success'] === 'updated')
                echo "L'enchantement a été mis à jour avec succès.";
            if ($_GET['success'] === 'deleted')
                echo "L'enchantement a été supprimé.";
            ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Nom</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Rareté</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Coût</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Niveau Requis</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Modificateurs</th>
                    <th class="px-6 py-4 text-center text-slate-400 text-sm uppercase font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                <?php foreach ($enchantments as $enchant): ?>
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (strpos($enchant['icon'], 'assets/') === 0): ?>
                                    <div
                                        class="w-10 h-10 bg-slate-900 rounded-lg border border-slate-700 p-1 flex items-center justify-center">
                                        <img src="/<?= htmlspecialchars($enchant['icon']) ?>" alt="Icon"
                                            class="max-w-full max-h-full object-contain">
                                    </div>
                                <?php else: ?>
                                    <span
                                        class="text-2xl w-10 h-10 flex items-center justify-center bg-slate-900 rounded-lg border border-slate-700"><?= htmlspecialchars($enchant['icon']) ?></span>
                                <?php endif; ?>
                                <div>
                                    <div class="text-white font-medium"><?= htmlspecialchars($enchant['name']) ?></div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">
                                        <?= htmlspecialchars($enchant['description']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $rarityClasses = [
                                'common' => 'bg-slate-100/10 text-slate-300',
                                'uncommon' => 'bg-green-500/10 text-green-400',
                                'rare' => 'bg-blue-500/10 text-blue-400',
                                'epic' => 'bg-purple-500/10 text-purple-400',
                                'legendary' => 'bg-yellow-500/10 text-yellow-400'
                            ];
                            $class = $rarityClasses[$enchant['rarity']] ?? $rarityClasses['common'];
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $class ?>">
                                <?= strtoupper($enchant['rarity']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-yellow-400 font-medium">🪙 <?= number_format($enchant['cost']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-300">Niv. <?= $enchant['required_level'] ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-slate-400 font-mono bg-slate-900/50 p-2 rounded">
                                <?= htmlspecialchars($enchant['stat_modifiers']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='openEditModal(<?= json_encode($enchant) ?>)'
                                    class="p-2 text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-colors"
                                    title="Modifier">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <a href="/admin/enchantments/delete/<?= $enchant['id'] ?>"
                                    onclick="return confirm('Supprimer cet enchantement ?')"
                                    class="p-2 text-red-400 hover:bg-red-500/10 rounded-lg transition-colors"
                                    title="Supprimer">
                                    <span class="material-symbols-outlined">delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Creation/Edition -->
<div id="enchant-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div
            class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-slate-800 flex items-center justify-between sticky top-0 bg-slate-900 z-10">
                <h3 id="modal-title" class="text-xl font-bold text-white">Ajouter un enchantement</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="enchant-form" method="POST" action="/admin/enchantments/create"
                class="p-6 space-y-6 text-white text-sm">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-slate-400 font-medium">Nom *</label>
                        <input type="text" name="name" id="field-name" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-400 font-medium">Icône (Emoji) *</label>
                        <input type="text" name="icon" id="field-icon" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-400 font-medium">Description</label>
                    <textarea name="description" id="field-description" rows="2"
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none focus:border-indigo-500 transition-colors"></textarea>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-slate-400 font-medium">Coût (Gold)</label>
                        <input type="number" name="cost" id="field-cost" value="0"
                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-400 font-medium">Niveau Minimum</label>
                        <input type="number" name="required_level" id="field-required_level" value="1"
                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-slate-400 font-medium">Rareté</label>
                        <select name="rarity" id="field-rarity"
                            class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none focus:border-indigo-500 transition-colors">
                            <option value="common">Common</option>
                            <option value="uncommon">Uncommon</option>
                            <option value="rare">Rare</option>
                            <option value="epic">Epic</option>
                            <option value="legendary">Legendary</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-400 font-medium">Modificateurs (JSON)</label>
                    <textarea name="stat_modifiers" id="field-stat_modifiers" rows="3"
                        placeholder='{"attack": 5, "defense": 2}'
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none font-mono text-sm focus:border-indigo-500 transition-colors">{}</textarea>
                    <small class="text-slate-500">Format: {"stat": valeur}. Ex: {"attack": 10, "intelligence":
                        5}</small>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-400 font-medium">Slots Compatibles (JSON)</label>
                    <input type="text" name="compatible_slot_types" id="field-compatible_slot_types"
                        placeholder='["weapon", "shield"]' value='[]'
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 outline-none font-mono text-sm focus:border-indigo-500 transition-colors">
                    <small class="text-slate-500">Ex: ["weapon", "shield", "helmet", "chest", "boots"] or [] for
                        all</small>
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_available" id="field-is_available" class="sr-only peer" checked>
                        <div
                            class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                        </div>
                        <span class="ml-3 text-slate-300 font-medium">Disponible en jeu</span>
                    </label>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()"
                        class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                        Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('enchant-modal');
    const form = document.getElementById('enchant-form');
    const title = document.getElementById('modal-title');

    function openCreateModal() {
        title.innerText = 'Ajouter un enchantement';
        form.action = '/admin/enchantments/create';
        form.reset();
        modal.classList.remove('hidden');
    }

    function openEditModal(enchant) {
        title.innerText = 'Modifier l\'enchantement: ' + enchant.name;
        form.action = '/admin/enchantments/edit/' + enchant.id;

        document.getElementById('field-name').value = enchant.name;
        document.getElementById('field-icon').value = enchant.icon;
        document.getElementById('field-description').value = enchant.description;
        document.getElementById('field-cost').value = enchant.cost;
        document.getElementById('field-required_level').value = enchant.required_level;
        document.getElementById('field-rarity').value = enchant.rarity;
        document.getElementById('field-stat_modifiers').value = enchant.stat_modifiers;
        document.getElementById('field-compatible_slot_types').value = enchant.compatible_slot_types;
        document.getElementById('field-is_available').checked = enchant.is_available == 1;

        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>