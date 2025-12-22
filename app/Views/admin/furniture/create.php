<?php
$pageTitle = 'Créer un Meuble';
ob_start();
?>

<div class="card max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="/admin/furniture" class="text-gray-400 hover:text-white">
            ← Retour
        </a>
        <h3 class="card-header mb-0">🪑 Créer un Meuble</h3>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-900/50 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nom -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nom *</label>
                <input type="text" name="name" required class="form-input w-full" placeholder="Coffre en Bois">
            </div>

            <!-- Catégorie -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Catégorie</label>
                <select name="category_id" class="form-select w-full">
                    <option value="">Sans catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Prix -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Prix (or) *</label>
                <input type="number" name="price" required min="0" class="form-input w-full" placeholder="100">
            </div>

            <!-- Icône -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Icône (emoji)</label>
                <input type="text" name="icon" class="form-input w-full" value="🪑" placeholder="🪑">
            </div>

            <!-- Rareté -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Rareté</label>
                <select name="rarity" class="form-select w-full">
                    <option value="common">Commun</option>
                    <option value="uncommon">Peu commun</option>
                    <option value="rare">Rare</option>
                    <option value="epic">Épique</option>
                    <option value="legendary">Légendaire</option>
                </select>
            </div>

            <!-- Niveau requis -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Niveau requis</label>
                <input type="number" name="required_level" min="1" value="1" class="form-input w-full">
            </div>

            <!-- Type de bonus -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Type de bonus</label>
                <select name="bonus_type" class="form-select w-full">
                    <option value="none">Aucun</option>
                    <option value="storage">Stockage (+slots)</option>
                    <option value="comfort">Confort</option>
                    <option value="luck">Chance (%)</option>
                    <option value="xp">XP (%)</option>
                    <option value="gold">Or (%)</option>
                    <option value="defense">Défense</option>
                </select>
            </div>

            <!-- Valeur du bonus -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Valeur du bonus</label>
                <input type="number" name="bonus_value" min="0" value="0" class="form-input w-full">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="3" class="form-input w-full" placeholder="Un simple coffre pour stocker vos affaires..."></textarea>
        </div>

        <!-- Image -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Image (chemin)</label>
            <input type="text" name="image" class="form-input w-full" placeholder="assets/images/furniture/chest.png">
        </div>

        <!-- Disponible -->
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_available" id="is_available" checked class="form-checkbox">
            <label for="is_available" class="text-sm text-gray-300">Disponible à l'achat</label>
        </div>

        <!-- Submit -->
        <div class="flex gap-4 pt-4">
            <button type="submit" class="btn btn-primary flex-1">
                ✅ Créer le Meuble
            </button>
            <a href="/admin/furniture" class="btn btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
