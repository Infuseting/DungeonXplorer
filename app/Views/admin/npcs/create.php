<?php
$pageTitle = 'Créer un PNJ';
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
    
    #merchant-fields {
        display: none;
        background: var(--bg-darker);
        padding: 1.5rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
    }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Créer un Nouveau PNJ</h3>
        <a href="/admin/npcs" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/npcs/create" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom du PNJ *</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Rôles</label>
                <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                    <label><input type="checkbox" name="roles[]" value="merchant"> Marchand</label>
                    <label><input type="checkbox" name="roles[]" value="quest_giver"> Donneur de quêtes</label>
                    <label><input type="checkbox" name="roles[]" value="lore"> Lore</label>
                    <label><input type="checkbox" name="roles[]" value="guard"> Garde</label>
                </div>
            </div>
            
            <!-- Texture Upload -->
            <div class="form-group-full">
                <label class="form-label">Texture du PNJ</label>
                <div style="display: flex; gap: 1rem; align-items: start;">
                    <div style="flex: 1;">
                        <input type="file" name="texture" id="texture-input" class="form-input" accept="image/*">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">
                            Formats acceptés: PNG, JPG, GIF. Taille recommandée: 64x64px
                        </small>
                    </div>
                    <div id="texture-preview" style="width: 64px; height: 64px; border: 2px dashed var(--border); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: var(--bg-dark);">
                        <span style="color: var(--text-muted); font-size: 0.75rem;">Aperçu</span>
                    </div>
                </div>
            </div>
            
            <!-- Merchant Fields -->
            <div class="form-group-full" id="merchant-fields">
                <h4 style="color: var(--text-light); margin-bottom: 1rem; font-size: 1.125rem;">
                    💰 Configuration Marchand
                </h4>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">SEED Inventaire *</label>
                        <input type="number" name="merchant_seed" id="merchant-seed" class="form-input" placeholder="Ex: 12345">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Même SEED = même inventaire. Générera 10-20 items aléatoires.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary" onclick="generateRandomSeed()" style="margin-top: 1.75rem;">
                            🎲 Générer SEED Aléatoire
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Taux rachat items vendus (%)</label>
                        <input type="number" step="0.01" name="buy_rate_own" class="form-input" value="5" min="0" max="100">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Pourcentage du prix pour items que le marchand vend
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Taux rachat autres items (%)</label>
                        <input type="number" step="0.01" name="buy_rate_other" class="form-input" value="15" min="0" max="100">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Pourcentage du prix pour autres items
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Dialogue Trees -->
            <div class="form-group-full">
                <label class="form-label">Arbres de Dialogue</label>
                <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top:0.5rem;">
                    <?php if (!empty($dialogueTrees)): ?>
                        <?php foreach ($dialogueTrees as $tree): ?>
                            <label><input type="checkbox" name="dialogue_trees[]" value="<?= $tree['id'] ?>"> <?= htmlspecialchars($tree['name']) ?></label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--text-muted);">Aucun arbre de dialogue créé.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="/admin/npcs" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">✅ Créer le PNJ</button>
        </div>
    </form>
</div>

<script>
// Show/hide merchant fields
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

// Generate random SEED
function generateRandomSeed() {
    const seed = Math.floor(Math.random() * 1000000);
    document.getElementById('merchant-seed').value = seed;
}

// Texture preview
const textureInput = document.getElementById('texture-input');
const texturePreview = document.getElementById('texture-preview');

textureInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            texturePreview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
        };
        reader.readAsDataURL(file);
    }
});

// Convert percentages to decimals before submit
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
