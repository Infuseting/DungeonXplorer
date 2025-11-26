<?php
$pageTitle = 'Gestion des PNJ';
ob_start();
?>

<style>
    .npcs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .npc-card {
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: all 0.2s;
    }
    
    .npc-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    .npc-header {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .npc-texture {
        width: 64px;
        height: 64px;
        background: var(--bg-dark);
        border: 2px solid var(--border);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .npc-texture img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .role-merchant { background: rgba(251, 191, 36, 0.2); color: #fde047; }
    .role-quest_giver { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; }
    .role-lore { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; }
    .role-guard { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Gestion des PNJ</h3>
        <a href="/admin/npcs/create" class="btn btn-primary">
            ➕ Créer un PNJ
        </a>
    </div>
    
    <!-- Search and Filters -->
    <div class="search-bar" style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
        <input 
            type="text" 
            id="search-input" 
            class="form-input" 
            style="flex: 1; min-width: 250px;"
            placeholder="🔍 Rechercher par nom..."
            value="<?= htmlspecialchars($search ?? '') ?>"
        >
        
        <select id="role-filter" class="form-select" style="min-width: 150px;">
            <option value="">Tous les rôles</option>
            <option value="merchant" <?= ($roleFilter ?? '') === 'merchant' ? 'selected' : '' ?>>Marchand</option>
            <option value="quest_giver" <?= ($roleFilter ?? '') === 'quest_giver' ? 'selected' : '' ?>>Donneur de quêtes</option>
            <option value="lore" <?= ($roleFilter ?? '') === 'lore' ? 'selected' : '' ?>>Lore</option>
            <option value="guard" <?= ($roleFilter ?? '') === 'guard' ? 'selected' : '' ?>>Garde</option>
        </select>
        
        <button class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
    </div>
    
    <!-- NPCs Grid -->
    <?php if (!empty($npcs)): ?>
        <div class="npcs-grid">
            <?php foreach ($npcs as $npc): ?>
                <div class="npc-card">
                    <div class="npc-header">
                        <div class="npc-texture">
                            <?php if ($npc['texture']): ?>
                                <img src="/<?= htmlspecialchars($npc['texture']) ?>" alt="<?= htmlspecialchars($npc['name']) ?>">
                            <?php else: ?>
                                <span style="font-size: 32px;">👤</span>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 1.125rem; font-weight: 600; color: var(--text-light); margin-bottom: 0.25rem;">
                                <?= htmlspecialchars($npc['name']) ?>
                            </div>
                            <?php
                                $roles = array_map('trim', explode(',', $npc['role'] ?? ''));
                                foreach ($roles as $r):
                            ?>
                                <span class="role-badge role-<?= $r ?>">
                                    <?= str_replace('_', ' ', $r) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Merchant Info -->
                    <?php $rolesArr = array_map('trim', explode(',', $npc['role'] ?? '')); ?>
                    <?php if (in_array('merchant', $rolesArr) && $npc['merchant_seed']): ?>
                        <div style="background: var(--bg-dark); padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">SEED Marchand</div>
                            <div style="color: var(--text-light); font-weight: 600; font-family: monospace;">
                                #<?= $npc['merchant_seed'] ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                                Rachat: <?= ($npc['buy_rate_own'] * 100) ?>% / <?= ($npc['buy_rate_other'] * 100) ?>%
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Actions -->
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/admin/npcs/edit/<?= $npc['id'] ?>" class="btn btn-sm btn-primary" style="flex: 1;">
                            ✏️ Modifier
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteNPC(<?= $npc['id'] ?>)">
                            🗑️
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 4rem; color: var(--text-muted);">
            <p style="font-size: 3rem; margin-bottom: 1rem;">👥</p>
            <p>Aucun PNJ trouvé</p>
            <a href="/admin/npcs/create" class="btn btn-primary" style="margin-top: 1rem;">
                Créer le premier PNJ
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Search and filter
let searchTimeout;

document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
});

document.getElementById('role-filter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const role = document.getElementById('role-filter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (role) params.append('role', role);
    
    window.location.href = '/admin/npcs?' + params.toString();
}

function resetFilters() {
    window.location.href = '/admin/npcs';
}

function deleteNPC(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce PNJ ?')) return;
    
    fetch(`/admin/npcs/delete/${id}`, {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
