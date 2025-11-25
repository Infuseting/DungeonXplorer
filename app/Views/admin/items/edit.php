<?php
$pageTitle = 'Modifier l\'Item';
$statRanges = json_decode($item['stat_ranges'] ?? '{}', true);
ob_start();
?>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .form-group-full {
        grid-column: 1 / -1;
    }
    
    .stats-section {
        background: var(--bg-darker);
        padding: 1.5rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
    }
    
    .stat-range {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 0.5rem;
        align-items: center;
    }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Modifier: <?= htmlspecialchars($item['name']) ?></h3>
        <a href="/admin/items" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/items/edit/<?= $item['id'] ?>" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom de l'Item *</label>
                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($item['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Icône</label>
                <input type="text" name="icon" class="form-input" value="<?= htmlspecialchars($item['icon'] ?? '') ?>" placeholder="icon.png">
            </div>
            
            <div class="form-group-full">
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
                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                    💰 Prix de vente/achat (laisser vide si l'item n'est pas vendable)
                </small>
            </div>
            
            <div class="form-group" id="two-handed-group" style="display: none;">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="two_handed" value="1" <?= $item['two_handed'] ? 'checked' : '' ?>>
                    Arme à deux mains
                </label>
            </div>
            
            <!-- Image Upload -->
            <div class="form-group-full">
                <label class="form-label">Image de l'Item</label>
                <div style="display: flex; gap: 1rem; align-items: start;">
                    <div style="flex: 1;">
                        <input type="file" name="item_image" id="item-image" class="form-input" accept="image/*">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">
                            Formats acceptés: PNG, JPG, GIF. Taille recommandée: 64x64px
                        </small>
                        <?php if ($item['icon']): ?>
                            <small style="color: var(--text-light); display: block; margin-top: 0.25rem;">
                                Image actuelle: <?= htmlspecialchars($item['icon']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div id="image-preview" style="width: 64px; height: 64px; border: 2px dashed var(--border); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: var(--bg-dark); overflow: hidden;">
                        <?php if ($item['icon']): ?>
                            <img src="/<?= htmlspecialchars($item['icon']) ?>" style="width: 100%; height: 100%; object-fit: contain;">
                        <?php else: ?>
                            <span style="color: var(--text-muted); font-size: 0.75rem;">Aperçu</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="form-group-full">
                <div class="stats-section">
                    <h4 style="color: var(--text-light); margin-bottom: 1.5rem; font-size: 1.125rem;">
                        📊 Statistiques (Min - Max)
                    </h4>
                    
                    <div style="display: grid; gap: 1rem;">
                        <!-- Strength -->
                        <div>
                            <label class="form-label">💪 Force</label>
                            <div class="stat-range">
                                <input type="number" name="strength_min" class="form-input" placeholder="Min" value="<?= $statRanges['strength']['min'] ?? 0 ?>" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="strength_max" class="form-input" placeholder="Max" value="<?= $statRanges['strength']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        
                        <!-- Vitality -->
                        <div>
                            <label class="form-label">❤️ Vitalité</label>
                            <div class="stat-range">
                                <input type="number" name="vitality_min" class="form-input" placeholder="Min" value="<?= $statRanges['vitality']['min'] ?? 0 ?>" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="vitality_max" class="form-input" placeholder="Max" value="<?= $statRanges['vitality']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        
                        <!-- Intelligence -->
                        <div>
                            <label class="form-label">🧠 Intelligence</label>
                            <div class="stat-range">
                                <input type="number" name="intelligence_min" class="form-input" placeholder="Min" value="<?= $statRanges['intelligence']['min'] ?? 0 ?>" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="intelligence_max" class="form-input" placeholder="Max" value="<?= $statRanges['intelligence']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        
                        <!-- Dexterity -->
                        <div>
                            <label class="form-label">🎯 Dextérité</label>
                            <div class="stat-range">
                                <input type="number" name="dexterity_min" class="form-input" placeholder="Min" value="<?= $statRanges['dexterity']['min'] ?? 0 ?>" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="dexterity_max" class="form-input" placeholder="Max" value="<?= $statRanges['dexterity']['max'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
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
            imagePreview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: contain;">`;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
