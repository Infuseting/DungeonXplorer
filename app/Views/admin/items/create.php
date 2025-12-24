<?php
$pageTitle = 'Créer un Item';
ob_start();
?>



<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Créer un Nouvel Item</h3>
        <a href="/admin/items" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/items/create" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom de l'Item *</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Icône</label>
                <input type="text" name="icon" class="form-input" placeholder="icon.png">
            </div>
            
            <div class="col-span-2">
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
                <small class="text-gray-400 block mt-1">
                    🪙 Prix de vente/achat (laisser vide si l'item n'est pas vendable)
                </small>
            </div>

            <div class="form-group">
                <label class="form-label flex items-center gap-2 h-full pt-6">
                    <input type="checkbox" name="is_purchasable" value="1">
                    Achetable auprès des PNJs
                </label>
            </div>
            
            <div class="form-group hidden" id="two-handed-group">
                <label class="form-label flex items-center gap-2">
                    <input type="checkbox" name="two_handed" value="1">
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
                                <option value="none">Aucun</option>
                                <option value="heal">Soin (Heal)</option>
                                <option value="buff">Buff de Stat</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Valeur de l'Effet</label>
                            <input type="number" name="effect_value" class="form-input" value="0">
                            <small class="text-gray-400">PV rendus ou valeur fixe</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Type de Durée</label>
                            <select name="duration_type" class="form-select">
                                <option value="instant">Instantané</option>
                                <option value="seconds">Temps réel (secondes)</option>
                                <option value="turns">Tours de combat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Valeur Durée</label>
                            <input type="number" name="duration_value" class="form-input" value="0">
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
                    </div>
                    <div id="image-preview" class="w-16 h-16 border-2 border-dashed border-gray-700 rounded-lg flex items-center justify-center bg-gray-900">
                        <span class="text-gray-400 text-xs">Aperçu</span>
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
                                <input type="number" name="strength_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="strength_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                        
                        <!-- Vitality -->
                        <div>
                            <label class="form-label">❤️ Vitalité</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="vitality_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="vitality_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                        
                        <!-- Intelligence -->
                        <div>
                            <label class="form-label">🧠 Intelligence</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="intelligence_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="intelligence_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                        
                        <!-- Dexterity -->
                        <div>
                            <label class="form-label">🎯 Dextérité</label>
                            <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                <input type="number" name="dexterity_min" class="form-input" placeholder="Min" value="0" min="0">
                                <span class="text-gray-400">→</span>
                                <input type="number" name="dexterity_max" class="form-input" placeholder="Max" value="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex gap-4 justify-end">
            <a href="/admin/items" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">✅ Créer l'Item</button>
        </div>
    </form>
</div>

<script>
const slotTypeSelect = document.getElementById('slot-type');
const twoHandedGroup = document.getElementById('two-handed-group');

function updateTwoHandedVisibility() {
    const slot = slotTypeSelect.value;
    const isHandSlot = slot === 'main_hand' || slot === 'off_hand';
    twoHandedGroup.style.display = isHandSlot ? 'block' : 'none';
}

slotTypeSelect.addEventListener('change', updateTwoHandedVisibility);
updateTwoHandedVisibility();

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
