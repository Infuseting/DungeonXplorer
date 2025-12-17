<?php
$pageTitle = 'Modifier le PNJ';
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Modifier: <?= htmlspecialchars($npc['name']) ?></h3>
        <a href="/admin/npcs" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/npcs/edit/<?= $npc['id'] ?>" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom du PNJ *</label>
                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($npc['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Rôles</label>
                <div class="flex gap-4 flex-wrap">
                    <?php
                        $npcRoles = array_map('trim', explode(',', $npc['role'] ?? ''));
                    ?>
                    <label><input type="checkbox" name="roles[]" value="merchant" <?= in_array('merchant', $npcRoles) ? 'checked' : '' ?>> Marchand</label>
                    <label><input type="checkbox" name="roles[]" value="quest_giver" <?= in_array('quest_giver', $npcRoles) ? 'checked' : '' ?>> Donneur de quêtes</label>
                    <label><input type="checkbox" name="roles[]" value="lore" <?= in_array('lore', $npcRoles) ? 'checked' : '' ?>> Lore</label>
                    <label><input type="checkbox" name="roles[]" value="guard" <?= in_array('guard', $npcRoles) ? 'checked' : '' ?>> Garde</label>
                </div>
            </div>
            
            <!-- Faction -->
            <div>
                <label for="faction_id" class="block text-sm font-medium text-gray-300 mb-2">Faction</label>
                <select id="faction_id" name="faction_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    <option value="">-- Aucune (Neutre) --</option>
                    <?php if (!empty($factions)): ?>
                        <?php foreach ($factions as $faction): ?>
                            <option value="<?= $faction['id'] ?>" <?= ($npc['faction_id'] == $faction['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($faction['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Texture Upload -->
            <div class="col-span-2">
                <label class="form-label">Texture du PNJ</label>
                <div class="flex gap-4 items-start">
                    <div class="flex-1">
                        <input type="file" name="texture" id="texture-input" class="form-input" accept="image/*">
                        <small class="text-gray-400 block mt-2">
                            Formats acceptés: PNG, JPG, GIF. Taille recommandée: 64x64px
                        </small>
                        <?php if ($npc['texture']): ?>
                            <small class="text-gray-200 block mt-1">
                                Texture actuelle: <?= htmlspecialchars($npc['texture']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div id="texture-preview" class="w-16 h-16 border-2 border-dashed border-gray-700 rounded-lg flex items-center justify-center bg-gray-900 overflow-hidden">
                        <?php if ($npc['texture']): ?>
                            <img src="/<?= htmlspecialchars($npc['texture']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">Aperçu</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Merchant Fields -->
            <div class="col-span-2 hidden bg-gray-900 p-6 rounded-xl border border-gray-700" id="merchant-fields">
                <h4 class="text-gray-200 mb-4 text-lg">
                    💰 Configuration Marchand
                </h4>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">SEED Inventaire *</label>
                        <input type="number" name="merchant_seed" id="merchant-seed" class="form-input" value="<?= $npc['merchant_seed'] ?? '' ?>">
                        <small class="text-gray-400 block mt-1">
                            Même SEED = même inventaire
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary mt-7" onclick="regenerateInventory()">
                            🔄 Régénérer Inventaire
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Taux rachat items vendus (%)</label>
                        <input type="number" step="0.01" name="buy_rate_own" class="form-input" value="<?= ($npc['buy_rate_own'] ?? 0.05) * 100 ?>" min="0" max="100">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Taux rachat autres items (%)</label>
                        <input type="number" step="0.01" name="buy_rate_other" class="form-input" value="<?= ($npc['buy_rate_other'] ?? 0.15) * 100 ?>" min="0" max="100">
                    </div>
                </div>
                
                <!-- Current Inventory -->
                <?php if (!empty($merchantInventory)): ?>
                    <div class="mt-6">
                        <h5 class="text-gray-200 mb-3">
                            Inventaire Actuel (<?= count($merchantInventory) ?> items)
                        </h5>
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-3 mt-4">
                            <?php foreach ($merchantInventory as $item): ?>
                                <div class="bg-gray-900 p-3 rounded-lg border border-gray-700 text-sm">
                                    <div class="font-semibold text-gray-200">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </div>
                                    <div class="text-yellow-400 mt-1">
                                        💰 <?= number_format($item['price']) ?> pièces
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-span-2" id="dialogue-fields">
                <h4 class="text-gray-200 mb-4 text-lg">
                    Configuration Dialogues
                </h4>
                
                <div class="flex flex-col gap-2 mt-2">
                    <?php
                        $assignedIds = array_column($assignedTrees, 'id');
                    ?>
                    <?php if (!empty($dialogueTrees)): ?>
                        <?php foreach ($dialogueTrees as $tree): ?>
                            <label>
                                <input type="checkbox" name="dialogue_trees[]" value="<?= $tree['id'] ?>" <?= in_array($tree['id'], $assignedIds) ? 'checked' : '' ?> >
                                <?= htmlspecialchars($tree['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-400">Aucun arbre de dialogue créé.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-span-2" id="quest-fields">
                <h4 class="text-gray-200 mb-4 text-lg">
                    📜 Configuration Quêtes
                </h4>
                
                <div class="flex flex-col gap-2 mt-2">
                    <?php
                        $assignedQuestIds = array_column($assignedQuests, 'id');
                    ?>
                    <?php if (!empty($allQuests)): ?>
                        <?php foreach ($allQuests as $quest): ?>
                            <label>
                                <input type="checkbox" name="quests[]" value="<?= $quest['id'] ?>" <?= in_array($quest['id'], $assignedQuestIds) ? 'checked' : '' ?> >
                                <?= htmlspecialchars($quest['name']) ?> (Niveau <?= $quest['min_level'] ?>)
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-400">Aucune quête disponible.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex gap-4 justify-end">
            <a href="/admin/npcs" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">💾 Sauvegarder</button>
        </div>
    </form>
</div>

<script>
const merchantFields = document.getElementById('merchant-fields');
const merchantSeed = document.getElementById('merchant-seed');
const questFields = document.getElementById('quest-fields');
const roleCheckboxes = document.querySelectorAll('[name="roles[]"]');

function updateRoleFields() {
    const isMerchant = Array.from(roleCheckboxes).some(cb => cb.value === 'merchant' && cb.checked);
    const isQuestGiver = Array.from(roleCheckboxes).some(cb => cb.value === 'quest_giver' && cb.checked);
    
    merchantFields.style.display = isMerchant ? 'block' : 'none';
    merchantSeed.required = isMerchant;
    
    questFields.style.display = isQuestGiver ? 'block' : 'none';
}

roleCheckboxes.forEach(cb => cb.addEventListener('change', updateRoleFields));
updateRoleFields();

function regenerateInventory() {
    if (!confirm('Régénérer l\'inventaire ? Cela remplacera l\'inventaire actuel.')) return;
    
    fetch('/admin/npcs/<?= $npc['id'] ?>/regenerate-inventory', {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
}

const textureInput = document.getElementById('texture-input');
const texturePreview = document.getElementById('texture-preview');

textureInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            texturePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    }
});

document.querySelector('form').addEventListener('submit', function(e) {
    const buyRateOwn = document.querySelector('[name="buy_rate_own"]');
    const buyRateOther = document.querySelector('[name="buy_rate_other"]');
    
    if (buyRateOwn.value) {
        buyRateOwn.value = parseFloat(buyRateOwn.value) / 100;
    }
    if (buyRateOther.value) {
        buyRateOther.value = parseFloat(buyRateOther.value) / 100;
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
