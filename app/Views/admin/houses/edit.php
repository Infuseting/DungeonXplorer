<?php
$pageTitle = 'Modifier la Maison';
ob_start();
?>

<div class="card max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="/admin/houses" class="text-gray-400 hover:text-white">
            ← Retour
        </a>
        <h3 class="card-header mb-0">🏠 Modifier: <?= htmlspecialchars($house['name']) ?></h3>
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
                <input type="text" name="name" required class="form-input w-full" value="<?= htmlspecialchars($house['name']) ?>">
            </div>

            <!-- Prix -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Prix (or) *</label>
                <input type="number" name="price" required min="0" class="form-input w-full" value="<?= $house['price'] ?>">
            </div>

            <!-- Slots de stockage -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Slots de stockage</label>
                <input type="number" name="storage_slots" min="1" class="form-input w-full" value="<?= $house['storage_slots'] ?>">
            </div>

            <!-- Slots de meubles -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Slots de meubles</label>
                <input type="number" name="furniture_slots" min="1" class="form-input w-full" value="<?= $house['furniture_slots'] ?>">
            </div>

            <!-- Niveau requis -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Niveau requis</label>
                <input type="number" name="required_level" min="1" class="form-input w-full" value="<?= $house['required_level'] ?>">
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Lieu</label>
                <input type="text" name="location_name" class="form-input w-full" value="<?= htmlspecialchars($house['location_name'] ?? '') ?>">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="3" class="form-input w-full"><?= htmlspecialchars($house['description'] ?? '') ?></textarea>
        </div>

        <!-- Image -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Image (chemin)</label>
            <input type="text" name="image" class="form-input w-full" value="<?= htmlspecialchars($house['image'] ?? '') ?>">
            <?php if ($house['image']): ?>
                <div class="mt-2 p-2 bg-gray-800 rounded inline-block">
                    <img src="/<?= htmlspecialchars($house['image']) ?>" alt="Preview" class="max-h-20">
                </div>
            <?php endif; ?>
        </div>

        <!-- Disponible -->
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_available" id="is_available" <?= $house['is_available'] ? 'checked' : '' ?> class="form-checkbox">
            <label for="is_available" class="text-sm text-gray-300">Disponible à l'achat</label>
        </div>

        <!-- Submit -->
        <div class="flex gap-4 pt-4">
            <button type="submit" class="btn btn-primary flex-1">
                💾 Enregistrer
            </button>
            <a href="/admin/houses" class="btn btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
