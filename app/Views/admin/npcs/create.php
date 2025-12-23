<?php
$pageTitle = 'Créer un PNJ';
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Créer un Nouveau PNJ</h3>
        <a href="/admin/npcs" class="btn btn-secondary">
            ← Retour
        </a>
    </div>

    <form method="POST" action="/admin/npcs/create" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom du PNJ *</label>
                <input type="text" name="name" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Rôles</label>
                <div class="flex gap-4 flex-wrap">
                    <label><input type="checkbox" name="roles[]" value="merchant"> Marchand</label>
                    <label><input type="checkbox" name="roles[]" value="quest_giver"> Donneur de quêtes</label>
                    <label><input type="checkbox" name="roles[]" value="lore"> Lore</label>
                    <label><input type="checkbox" name="roles[]" value="guard"> Garde</label>
                </div>
            </div>

            <!-- Faction -->
            <div>
                <label for="faction_id" class="block text-sm font-medium text-gray-300 mb-2">Faction</label>
                <select id="faction_id" name="faction_id"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    <option value="">-- Aucune (Neutre) --</option>
                    <?php if (!empty($factions)): ?>
                        <?php foreach ($factions as $faction): ?>
                            <option value="<?= $faction['id'] ?>"><?= htmlspecialchars($faction['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="mt-1 text-xs text-gray-400">La faction influence les prix et les réactions.</p>
            </div>

            <!-- Texture Upload -->
            <div class="col-span-2">
                <label class="form-label">Texture du PNJ</label>
                <div class="flex gap-4 items-start">
                    <div class="flex-1">
                        <input type="file" name="texture" id="texture-input" class="form-input" accept="image/*">
                        <small class="text-gray-400 block mt-2">
                            Formats acceptés: PNG, JPG, GIF, WebP. Taille recommandée: 64x64px
                        </small>
                    </div>
                    <div id="texture-preview"
                        class="w-16 h-16 border-2 border-dashed border-gray-700 rounded-lg flex items-center justify-center bg-gray-900">
                        <span class="text-gray-400 text-xs">Aperçu</span>
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
                        <input type="number" name="merchant_seed" id="merchant-seed" class="form-input"
                            placeholder="Ex: 12345">
                        <small class="text-gray-400 block mt-1">
                            Même SEED = même inventaire. Générera 10-20 items aléatoires.
                        </small>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-secondary mt-7" onclick="generateRandomSeed()">
                            🎲 Générer SEED Aléatoire
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Taux rachat items vendus (%)</label>
                        <input type="number" step="0.01" name="buy_rate_own" class="form-input" value="5" min="0"
                            max="100">
                        <small class="text-gray-400 block mt-1">
                            Pourcentage du prix pour items que le marchand vend
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Taux rachat autres items (%)</label>
                        <input type="number" step="0.01" name="buy_rate_other" class="form-input" value="15" min="0"
                            max="100">
                        <small class="text-gray-400 block mt-1">
                            Pourcentage du prix pour autres items
                        </small>
                    </div>
                </div>
            </div>

            <!-- Dialogue Trees -->
            <div class="col-span-2">
                <label class="form-label">Arbres de Dialogue</label>
                <div class="flex flex-col gap-2 mt-2">
                    <?php if (!empty($dialogueTrees)): ?>
                        <?php foreach ($dialogueTrees as $tree): ?>
                            <label><input type="checkbox" name="dialogue_trees[]" value="<?= $tree['id'] ?>">
                                <?= htmlspecialchars($tree['name']) ?></label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-400">Aucun arbre de dialogue créé.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-4 justify-end">
            <a href="/admin/npcs" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">✅ Créer le PNJ</button>
        </div>
    </form>
</div>

<script>
    const merchantFields = document.getElementById('merchant-fields');
    const merchantSeed = document.getElementById('merchant-seed');
    const roleCheckboxes = document.querySelectorAll('[name="roles[]"]');

    function updateMerchantFields() {
        const isMerchant = Array.from(roleCheckboxes).some(cb => cb.value === 'merchant' && cb.checked);
        merchantFields.style.display = isMerchant ? 'block' : 'none';
        merchantSeed.required = isMerchant;
    }

    roleCheckboxes.forEach(cb => cb.addEventListener('change', updateMerchantFields));
    updateMerchantFields();

    function generateRandomSeed() {
        const seed = Math.floor(Math.random() * 1000000);
        document.getElementById('merchant-seed').value = seed;
    }

    const textureInput = document.getElementById('texture-input');
    const texturePreview = document.getElementById('texture-preview');

    textureInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                texturePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(file);
        }
    });

    document.querySelector('form').addEventListener('submit', function (e) {
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