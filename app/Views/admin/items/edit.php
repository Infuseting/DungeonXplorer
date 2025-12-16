<?php
$pageTitle = 'Modifier l\'Item';
$statRanges = json_decode($item['stat_ranges'] ?? '{}', true);
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Modifier: <?= htmlspecialchars($item['name']) ?></h3>
        <a href="/admin/items" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/items/edit/<?= $item['id'] ?>" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom de l'Item *</label>
                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($item['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Icône</label>
                <input type="text" name="icon" class="form-input" value="<?= htmlspecialchars($item['icon'] ?? '') ?>" placeholder="icon.png">
            </div>
            
            <div class="col-span-2">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="3"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>
            
            <!-- Type and Slot -->
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    <option value="equipment" <?= $item['type'] === 'equipment' ? 'selected' : '' ?>>Équipement</option>
                    <option value="consumable" <?= $item['type'] === 'consumable' ? 'selected' : '' ?>>Consommable</option>
                    <option value="material" <?= $item['type'] === 'material' ? 'selected' : '' ?>>Matériau</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Emplacement</label>
                <select name="slot_type" id="slot-type" class="form-select">
                    <option value="none" <?= $item['slot_type'] === 'none' ? 'selected' : '' ?>>Aucun</option>
                    <option value="head" <?= $item['slot_type'] === 'head' ? 'selected' : '' ?>>Tête</option>
                    <option value="shoulders" <?= $item['slot_type'] === 'shoulders' ? 'selected' : '' ?>>Épaules</option>
                    <option value="amulet" <?= $item['slot_type'] === 'amulet' ? 'selected' : '' ?>>Amulette</option>
                    <option value="chest" <?= $item['slot_type'] === 'chest' ? 'selected' : '' ?>>Torse</option>
                    <option value="belt" <?= $item['slot_type'] === 'belt' ? 'selected' : '' ?>>Ceinture</option>
                    <option value="legs" <?= $item['slot_type'] === 'legs' ? 'selected' : '' ?>>Jambes</option>
                    <option value="boots" <?= $item['slot_type'] === 'boots' ? 'selected' : '' ?>>Bottes</option>
                    <option value="ring" <?= $item['slot_type'] === 'ring' ? 'selected' : '' ?>>Anneau</option>
                    <option value="main_hand" <?= $item['slot_type'] === 'main_hand' ? 'selected' : '' ?>>Main principale</option>
                    <option value="off_hand" <?= $item['slot_type'] === 'off_hand' ? 'selected' : '' ?>>Main secondaire</option>
                    <option value="gloves" <?= $item['slot_type'] === 'gloves' ? 'selected' : '' ?>>Gants</option>
                    <option value="bracers" <?= $item['slot_type'] === 'bracers' ? 'selected' : '' ?>>Bracelets</option>
                    <option value="backpack" <?= $item['slot_type'] === 'backpack' ? 'selected' : '' ?>>Sac à dos</option>
                </select>
            </div>
            
            <!-- Physical Properties -->
            <div class="form-group">
                <label class="form-label">Largeur (cases)</label>
                <input type="number" name="width" class="form-input" value="<?= $item['width'] ?>" min="1" max="10">
            </div>
            
            <div class="form-group">
                <label class="form-label">Hauteur (cases)</label>
                <input type="number" name="height" class="form-input" value="<?= $item['height'] ?>" min="1" max="10">
            </div>
            
            <div class="form-group">
                <label class="form-label">Poids (kg)</label>
                <input type="number" step="0.01" name="weight" class="form-input" value="<?= $item['weight'] ?>" min="0">
            </div>
            
            <div class="form-group">
                <label class="form-label">Stack Maximum</label>
                <input type="number" name="max_stack" class="form-input" value="<?= $item['max_stack'] ?>" min="1" max="999">
            </div>
            
            <div class="form-group">
                <label class="form-label">Prix (optionnel)</label>
                <input type="number" name="price" class="form-input" value="<?= $item['price'] ?? '' ?>" placeholder="Laisser vide si non vendable" min="0">
                <small class="text-gray-400 block mt-1">
                    💰 Prix de vente/achat (laisser vide si l'item n'est pas vendable)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label flex items-center gap-2 h-full pt-6">
                    <input type="checkbox" name="is_purchasable" value="1" <?= ($item['is_purchasable'] ?? 0) ? 'checked' : '' ?>>
                    Achetable auprès des PNJs
                </label>
            </div>
            
            <div class="form-group hidden" id="two-handed-group">
                <label class="form-label flex items-center gap-2">
                    <input type="checkbox" name="two_handed" value="1" <?= $item['two_handed'] ? 'checked' : '' ?>>
                    Arme à deux mains
                </label>
            </div>
            
            <!-- Consumable Properties (Shown only if type=consumable) -->
            <div class="col-span-2 hidden" id="consumable-properties">
                <div class="bg-gray-900 p-6 rounded-xl border border-gray-700">
                    <h4 class="text-gray-200 mb-6 text-lg">
                        🧪 Propriétés du Consommable
                    </h4>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Type d'Effet</label>
                            <select name="effect_type" class="form-select">
                                <option value="none" <?= ($item['effect_type'] ?? 'none') === 'none' ? 'selected' : '' ?>>Aucun</option>
                                <option value="heal" <?= ($item['effect_type'] ?? 'none') === 'heal' ? 'selected' : '' ?>>Soin (Heal)</option>
                                <option value="buff" <?= ($item['effect_type'] ?? 'none') === 'buff' ? 'selected' : '' ?>>Buff de Stat</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Valeur de l'Effet</label>
                            <input type="number" name="effect_value" class="form-input" value="<?= $item['effect_value'] ?? 0 ?>">
                            <small class="text-gray-400">PV rendus ou valeur fixe</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Type de Durée</label>
                            <select name="duration_type" class="form-select">
                                <option value="instant" <?= ($item['duration_type'] ?? 'instant') === 'instant' ? 'selected' : '' ?>>Instantané</option>
                                <option value="seconds" <?= ($item['duration_type'] ?? 'instant') === 'seconds' ? 'selected' : '' ?>>Temps réel (secondes)</option>
                                <option value="turns" <?= ($item['duration_type'] ?? 'instant') === 'turns' ? 'selected' : '' ?>>Tours de combat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Valeur Durée</label>
                            <input type="number" name="duration_value" class="form-input" value="<?= $item['duration_value'] ?? 0 ?>">
                            <small class="text-gray-400">Nombre de secondes ou tours</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="col-span-2">
                <label class="form-label">Image de l'Item</label>
                <div class="flex gap-4 items-start">
                    <div class="flex-1">
                        <input type="file" name="item_image" id="item-image" class="form-input" accept="image/*">
                        <small class="text-gray-400 block mt-2">
                            Formats acceptés: PNG, JPG, GIF. Taille recommandée: 64x64px
                        </small>
                        <?php if ($item['icon']): ?>
                            <small class="text-gray-200 block mt-1">
                                Image actuelle: <?= htmlspecialchars($item['icon']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div id="image-preview" class="w-16 h-16 border-2 border-dashed border-gray-700 rounded-lg flex items-center justify-center bg-gray-900 overflow-hidden">
                        <?php if ($item['icon']): ?>
                            <img src="/<?= htmlspecialchars($item['icon']) ?>" class="w-full h-full object-contain">
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">Aperçu</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="col-span-2">
                <div class="bg-gray-900 p-6 rounded-xl border border-gray-700">
                    <h4 class="text-gray-200 mb-6 text-lg">
                        📊 Statistiques (Min - Max)
                    </h4>
                    
                    <div class="grid gap-4">
                        <!-- Strength -->
                        <div>
                            <label class="form-label">💪 Force</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="strength_min" class="form-input" placeholder="Min" value="<?= $statRanges['strength']['min'] ?? 0 ?>" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="strength_max" class="form-input" placeholder="Max" value="<?= $statRanges['strength']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        
                        <!-- Vitality -->
                        <div>
                            <label class="form-label">❤️ Vitalité</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="vitality_min" class="form-input" placeholder="Min" value="<?= $statRanges['vitality']['min'] ?? 0 ?>" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="vitality_max" class="form-input" placeholder="Max" value="<?= $statRanges['vitality']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        
                        <!-- Intelligence -->
                        <div>
                            <label class="form-label">🧠 Intelligence</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="intelligence_min" class="form-input" placeholder="Min" value="<?= $statRanges['intelligence']['min'] ?? 0 ?>" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="intelligence_max" class="form-input" placeholder="Max" value="<?= $statRanges['intelligence']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        
                        <!-- Dexterity -->
                        <div>
                            <label class="form-label">🎯 Dextérité</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="dexterity_min" class="form-input" placeholder="Min" value="<?= $statRanges['dexterity']['min'] ?? 0 ?>" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="dexterity_max" class="form-input" placeholder="Max" value="<?= $statRanges['dexterity']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex gap-4 justify-end">
            <a href="/admin/items" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">💾 Sauvegarder</button>
        </div>
    </form>
</div>

<script>
// Show/hide two-handed checkbox based on slot type
const slotTypeSelect = document.getElementById('slot-type');
const twoHandedGroup = document.getElementById('two-handed-group');

function updateTwoHandedVisibility() {
    const slot = slotTypeSelect.value;
    const isHandSlot = slot === 'main_hand' || slot === 'off_hand';
    twoHandedGroup.style.display = isHandSlot ? 'block' : 'none';
}

slotTypeSelect.addEventListener('change', updateTwoHandedVisibility);
updateTwoHandedVisibility();

// Image preview
const imageInput = document.getElementById('item-image');
const imagePreview = document.getElementById('image-preview');

imageInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-contain">`;
        };
        reader.readAsDataURL(file);
    }
});
// Consumable Type Logic
const typeSelect = document.querySelector('select[name="type"]');
const consumableProps = document.getElementById('consumable-properties');

function updateTypeVisibility() {
    if (typeSelect.value === 'consumable') {
        consumableProps.classList.remove('hidden');
    } else {
        consumableProps.classList.add('hidden');
    }
}
typeSelect.addEventListener('change', updateTypeVisibility);
updateTypeVisibility();

</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
