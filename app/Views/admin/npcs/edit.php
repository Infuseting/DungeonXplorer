<?php
$pageTitle = 'Modifier le PNJ';
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
    
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    
    .inventory-item {
        background: var(--bg-dark);
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        font-size: 0.875rem;
    }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Modifier: <?= htmlspecialchars($npc['name']) ?></h3>
        <a href="/admin/npcs" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/npcs/edit/<?= $npc['id'] ?>" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Basic Info -->
            <div class="form-group">
                <label class="form-label">Nom du PNJ *</label>
                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($npc['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Rôle *</label>
                <select name="role" id="role-select" class="form-select" required>
                    <option value="merchant" <?= $npc['role'] === 'merchant' ? 'selected' : '' ?>>Marchand</option>
                    <option value="quest_giver" <?= $npc['role'] === 'quest_giver' ? 'selected' : '' ?>>Donneur de quêtes</option>
                    <option value="lore" <?= $npc['role'] === 'lore' ? 'selected' : '' ?>>Lore</option>
                    <option value="guard" <?= $npc['role'] === 'guard' ? 'selected' : '' ?>>Garde</option>
                </select>
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
                        <?php if ($npc['texture']): ?>
                            <small style="color: var(--text-light); display: block; margin-top: 0.25rem;">
                                Texture actuelle: <?= htmlspecialchars($npc['texture']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div id="texture-preview" style="width: 64px; height: 64px; border: 2px dashed var(--border); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: var(--bg-dark); overflow: hidden;">
                        <?php if ($npc['texture']): ?>
                            <img src="/<?= htmlspecialchars($npc['texture']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="color: var(--text-muted); font-size: 0.75rem;">Aperçu</span>
                        <?php endif; ?>
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
                        <input type="number" name="merchant_seed" id="merchant-seed" class="form-input" value="<?= $npc['merchant_seed'] ?? '' ?>">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Même SEED = même inventaire
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary" onclick="regenerateInventory()" style="margin-top: 1.75rem;">
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
                    <div style="margin-top: 1.5rem;">
                        <h5 style="color: var(--text-light); margin-bottom: 0.75rem;">
                            Inventaire Actuel (<?= count($merchantInventory) ?> items)
                        </h5>
                        <div class="inventory-grid">
                            <?php foreach ($merchantInventory as $item): ?>
                                <div class="inventory-item">
                                    <div style="font-weight: 600; color: var(--text-light);">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </div>
                                    <div style="color: #fbbf24; margin-top: 0.25rem;">
                                        💰 <?= number_format($item['price']) ?> pièces
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="/admin/npcs" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">💾 Sauvegarder</button>
        </div>
    </form>
</div>

<script>
// Show/hide merchant fields
const roleSelect = document.getElementById('role-select');
const merchantFields = document.getElementById('merchant-fields');
const merchantSeed = document.getElementById('merchant-seed');

function updateMerchantFields() {
    const isMerchant = roleSelect.value === 'merchant';
    merchantFields.style.display = isMerchant ? 'block' : 'none';
    if (isMerchant) {
        merchantSeed.required = true;
    } else {
        merchantSeed.required = false;
    }
}

roleSelect.addEventListener('change', updateMerchantFields);
updateMerchantFields();

// Regenerate inventory
function regenerateInventory() {
    if (!confirm('Régénérer l\'inventaire ? Cela remplacera l\'inventaire actuel.')) return;
    
    fetch('/admin/npcs/<?= $npc['id'] ?>/regenerate-inventory', {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
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
