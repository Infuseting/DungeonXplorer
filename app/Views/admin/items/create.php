<?php
$pageTitle = 'Créer un Item';
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
        <h3 class="card-header" style="margin-bottom: 0;">Créer un Nouvel Item</h3>
        <a href="/admin/items" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/items/create" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom de l'Item *</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Icône</label>
                <input type="text" name="icon" class="form-input" placeholder="icon.png">
            </div>
            
            <div class="form-group-full">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="3"></textarea>
            </div>
            
            <!-- Type and Slot -->
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    <option value="equipment">Équipement</option>
                    <option value="consumable">Consommable</option>
                    <option value="material">Matériau</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Emplacement</label>
                <select name="slot_type" id="slot-type" class="form-select">
                    <option value="none">Aucun</option>
                    <option value="head">Tête</option>
                    <option value="shoulders">Épaules</option>
                    <option value="amulet">Amulette</option>
                    <option value="chest">Torse</option>
                    <option value="belt">Ceinture</option>
                    <option value="legs">Jambes</option>
                    <option value="boots">Bottes</option>
                    <option value="ring">Anneau</option>
                    <option value="main_hand">Main principale</option>
                    <option value="off_hand">Main secondaire</option>
                    <option value="gloves">Gants</option>
                    <option value="bracers">Bracelets</option>
                    <option value="backpack">Sac à dos</option>
                </select>
            </div>
            
            <!-- Physical Properties -->
            <div class="form-group">
                <label class="form-label">Largeur (cases)</label>
                <input type="number" name="width" class="form-input" value="1" min="1" max="10">
            </div>
            
            <div class="form-group">
                <label class="form-label">Hauteur (cases)</label>
                <input type="number" name="height" class="form-input" value="1" min="1" max="10">
            </div>
            
            <div class="form-group">
                <label class="form-label">Poids (kg)</label>
                <input type="number" step="0.01" name="weight" class="form-input" value="0.00" min="0">
            </div>
            
            <div class="form-group">
                <label class="form-label">Stack Maximum</label>
                <input type="number" name="max_stack" class="form-input" value="1" min="1" max="999">
            </div>
            
            <div class="form-group">
                <label class="form-label">Prix (optionnel)</label>
                <input type="number" name="price" class="form-input" placeholder="Laisser vide si non vendable" min="0">
                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                    💰 Prix de vente/achat (laisser vide si l'item n'est pas vendable)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; height: 100%; padding-top: 1.5rem;">
                    <input type="checkbox" name="is_purchasable" value="1">
                    Achetable auprès des PNJs
                </label>
            </div>
            
            <div class="form-group" id="two-handed-group" style="display: none;">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="two_handed" value="1">
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
                    </div>
                    <div id="image-preview" style="width: 64px; height: 64px; border: 2px dashed var(--border); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: var(--bg-dark);">
                        <span style="color: var(--text-muted); font-size: 0.75rem;">Aperçu</span>
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
                                <input type="number" name="strength_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="strength_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                        
                        <!-- Vitality -->
                        <div>
                            <label class="form-label">❤️ Vitalité</label>
                            <div class="stat-range">
                                <input type="number" name="vitality_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="vitality_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                        
                        <!-- Intelligence -->
                        <div>
                            <label class="form-label">🧠 Intelligence</label>
                            <div class="stat-range">
                                <input type="number" name="intelligence_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="intelligence_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                        
                        <!-- Dexterity -->
                        <div>
                            <label class="form-label">🎯 Dextérité</label>
                            <div class="stat-range">
                                <input type="number" name="dexterity_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span style="color: var(--text-muted);">→</span>
                                <input type="number" name="dexterity_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="/admin/items" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">✅ Créer l'Item</button>
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
