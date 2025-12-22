<?php
$pageTitle = 'Modifier le Meuble';
ob_start();
?>

<div class="card max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="/admin/furniture" class="text-gray-400 hover:text-white">
            ← Retour
        </a>
        <h3 class="card-header mb-0">🪑 Modifier: <?= htmlspecialchars($furniture['name']) ?></h3>
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
                <input type="text" name="name" required class="form-input w-full" value="<?= htmlspecialchars($furniture['name']) ?>">
            </div>

            <!-- Catégorie -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Catégorie</label>
                <select name="category_id" class="form-select w-full">
                    <option value="">Sans catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $furniture['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Prix -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Prix (or) *</label>
                <input type="number" name="price" required min="0" class="form-input w-full" value="<?= $furniture['price'] ?>">
            </div>

            <!-- Icône -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Icône (emoji)</label>
                <input type="text" name="icon" class="form-input w-full" value="<?= htmlspecialchars($furniture['icon'] ?? '🪑') ?>">
            </div>

            <!-- Rareté -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Rareté</label>
                <select name="rarity" class="form-select w-full">
                    <option value="common" <?= $furniture['rarity'] === 'common' ? 'selected' : '' ?>>Commun</option>
                    <option value="uncommon" <?= $furniture['rarity'] === 'uncommon' ? 'selected' : '' ?>>Peu commun</option>
                    <option value="rare" <?= $furniture['rarity'] === 'rare' ? 'selected' : '' ?>>Rare</option>
                    <option value="epic" <?= $furniture['rarity'] === 'epic' ? 'selected' : '' ?>>Épique</option>
                    <option value="legendary" <?= $furniture['rarity'] === 'legendary' ? 'selected' : '' ?>>Légendaire</option>
                </select>
            </div>

            <!-- Niveau requis -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Niveau requis</label>
                <input type="number" name="required_level" min="1" class="form-input w-full" value="<?= $furniture['required_level'] ?>">
            </div>

            <!-- Type de bonus -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Type de bonus</label>
                <select name="bonus_type" class="form-select w-full">
                    <option value="none" <?= $furniture['bonus_type'] === 'none' ? 'selected' : '' ?>>Aucun</option>
                    <option value="storage" <?= $furniture['bonus_type'] === 'storage' ? 'selected' : '' ?>>Stockage (+slots)</option>
                    <option value="comfort" <?= $furniture['bonus_type'] === 'comfort' ? 'selected' : '' ?>>Confort</option>
                    <option value="luck" <?= $furniture['bonus_type'] === 'luck' ? 'selected' : '' ?>>Chance (%)</option>
                    <option value="xp" <?= $furniture['bonus_type'] === 'xp' ? 'selected' : '' ?>>XP (%)</option>
                    <option value="gold" <?= $furniture['bonus_type'] === 'gold' ? 'selected' : '' ?>>Or (%)</option>
                    <option value="defense" <?= $furniture['bonus_type'] === 'defense' ? 'selected' : '' ?>>Défense</option>
                </select>
            </div>

            <!-- Valeur du bonus -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Valeur du bonus</label>
                <input type="number" name="bonus_value" min="0" class="form-input w-full" value="<?= $furniture['bonus_value'] ?>">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="3" class="form-input w-full"><?= htmlspecialchars($furniture['description'] ?? '') ?></textarea>
        </div>

        <!-- Image -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Image (chemin)</label>
            <input type="text" name="image" class="form-input w-full" value="<?= htmlspecialchars($furniture['image'] ?? '') ?>">
        </div>

        <!-- Disponible -->
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_available" id="is_available" <?= $furniture['is_available'] ? 'checked' : '' ?> class="form-checkbox">
            <label for="is_available" class="text-sm text-gray-300">Disponible à l'achat</label>
        </div>

        <!-- Submit -->
        <div class="flex gap-4 pt-4">
            <button type="submit" class="btn btn-primary flex-1">
                💾 Enregistrer
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
