<?php
$pageTitle = isset($skill) ? 'Modifier Compétence' : 'Nouvelle Compétence';
$isEdit = isset($skill);
$parentId = $skill['parent_skill_id'] ?? null;
ob_start();
?>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="/admin/classes/edit/<?= $class['id'] ?>" class="text-gray-400 hover:text-white flex items-center gap-2 transition-colors mb-4">
            <span>←</span> Retour à <?= htmlspecialchars($class['name']) ?>
        </a>
        <h1 class="text-2xl font-bold text-white"><?= $pageTitle ?> <span class="text-gray-500 text-lg font-normal">pour <?= htmlspecialchars($class['name']) ?></span></h1>
    </div>

    <form action="<?= $isEdit ? '/admin/classes/skills/update/' . $skill['id'] : '/admin/classes/skills/store/' . $class['id'] ?>" method="POST" class="bg-gray-900 p-8 rounded-xl border border-gray-800 space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-400 mb-1">Nom de la compétence</label>
                <input type="text" name="name" value="<?= htmlspecialchars($skill['name'] ?? '') ?>" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-400 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($skill['description'] ?? '') ?></textarea>
            </div>

            <!-- Configuration Principale -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                <select name="type" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    <option value="active" <?= ($skill['type'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="passive" <?= ($skill['type'] ?? '') === 'passive' ? 'selected' : '' ?>>Passive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">Niveau Min. Requis</label>
                <input type="number" name="min_level" value="<?= $skill['min_level'] ?? 1 ?>" min="1"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
            </div>

            <!-- Coûts -->
            <div class="col-span-2 border-t border-gray-800 pt-4">
                <h3 class="text-sm font-medium text-gray-300 mb-3">Coûts & Cooldown</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs uppercase text-gray-500 mb-1">Mana (MP)</label>
                        <input type="number" name="cost_mp" value="<?= $skill['cost_mp'] ?? 0 ?>" 
                               class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-500 mb-1">Skill Points</label>
                        <input type="number" name="cost_sp" value="<?= $skill['cost_sp'] ?? 1 ?>" 
                               class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-500 mb-1">Cooldown (tours)</label>
                        <input type="number" name="cooldown" value="<?= $skill['cooldown'] ?? 0 ?>" 
                               class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                    </div>
                </div>
            </div>

            <!-- Effets -->
            <div class="col-span-2 border-t border-gray-800 pt-4">
                <h3 class="text-sm font-medium text-gray-300 mb-3">Effet</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Type d'effet</label>
                        <select name="effect_type" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                            <option value="damage" <?= ($skill['effect_type'] ?? '') === 'damage' ? 'selected' : '' ?>>Dégâts (Physique/Magique)</option>
                            <option value="heal" <?= ($skill['effect_type'] ?? '') === 'heal' ? 'selected' : '' ?>>Soin</option>
                            <option value="buff_str" <?= ($skill['effect_type'] ?? '') === 'buff_str' ? 'selected' : '' ?>>Buff Force</option>
                            <option value="buff_dex" <?= ($skill['effect_type'] ?? '') === 'buff_dex' ? 'selected' : '' ?>>Buff Dextérité</option>
                            <option value="buff_int" <?= ($skill['effect_type'] ?? '') === 'buff_int' ? 'selected' : '' ?>>Buff Intelligence</option>
                            <option value="buff_vit" <?= ($skill['effect_type'] ?? '') === 'buff_vit' ? 'selected' : '' ?>>Buff Vitalité</option>
                            <!-- Add more complex effects later -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Valeur / % Power</label>
                        <input type="number" name="effect_value" value="<?= $skill['effect_value'] ?? 0 ?>" 
                               class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white"
                               title="Pour les dégâts/soins, c'est souvent un % de la stat principale.">
                    </div>
                </div>
            </div>

            <!-- Dépendance (Skill Tree) -->
            <div class="col-span-2">
                 <label class="block text-sm font-medium text-gray-400 mb-1">Compétence Parente (Pré-requis)</label>
                 <select name="parent_skill_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white">
                     <option value="">-- Aucune (Racine) --</option>
                     <?php 
                     if (isset($classSkills)): 
                        foreach ($classSkills as $s): 
                            if ($isEdit && $s['id'] == $skill['id']) continue; // Cannot depend on self
                     ?>
                        <option value="<?= $s['id'] ?>" <?= $parentId == $s['id'] ? 'selected' : '' ?>>
                            Lvl <?= $s['min_level'] ?> - <?= htmlspecialchars($s['name']) ?>
                        </option>
                     <?php endforeach; endif; ?>
                 </select>
                 <p class="text-xs text-gray-500 mt-1">Le joueur doit débloquer la compétence parente avant celle-ci.</p>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
             <a href="/admin/classes/edit/<?= $class['id'] ?>" class="bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                Annuler
             </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                <?= $isEdit ? 'Enregistrer' : 'Ajouter la compétence' ?>
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../../layouts/admin.php';
?>
