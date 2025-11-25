<?php
$pageTitle = 'Gestion des Items';
ob_start();
?>

<style>
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .item-card {
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: all 0.2s;
    }
    
    .item-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }
    
    .item-icon {
        width: 48px;
        height: 48px;
        background: var(--bg-dark);
        border: 2px solid var(--border);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .item-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 0.25rem;
    }
    
    .item-type-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        display: inline-block;
    }
    
    .type-equipment { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; }
    .type-consumable { background: rgba(34, 197, 94, 0.2); color: #86efac; }
    .type-material { background: rgba(251, 191, 36, 0.2); color: #fde047; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
        margin: 1rem 0;
    }
    
    .stat-item {
        background: var(--bg-dark);
        padding: 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .stat-label {
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .stat-value {
        color: var(--text-light);
        font-weight: 600;
    }
    
    .search-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
</style>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Gestion des Items</h3>
        <a href="/admin/items/create" class="btn btn-primary">
            ➕ Créer un Item
        </a>
    </div>
    
    <!-- Search and Filters -->
    <div class="search-bar">
        <input 
            type="text" 
            id="search-input" 
            class="form-input" 
            style="flex: 1; min-width: 250px;"
            placeholder="🔍 Rechercher par nom..."
            value="<?= htmlspecialchars($search ?? '') ?>"
        >
        
        <select id="type-filter" class="form-select" style="min-width: 150px;">
            <option value="">Tous les types</option>
            <option value="equipment" <?= ($typeFilter ?? '') === 'equipment' ? 'selected' : '' ?>>Équipement</option>
            <option value="consumable" <?= ($typeFilter ?? '') === 'consumable' ? 'selected' : '' ?>>Consommable</option>
            <option value="material" <?= ($typeFilter ?? '') === 'material' ? 'selected' : '' ?>>Matériau</option>
        </select>
        
        <select id="slot-filter" class="form-select" style="min-width: 150px;">
            <option value="">Tous les slots</option>
            <option value="head" <?= ($slotFilter ?? '') === 'head' ? 'selected' : '' ?>>Tête</option>
            <option value="chest" <?= ($slotFilter ?? '') === 'chest' ? 'selected' : '' ?>>Torse</option>
            <option value="legs" <?= ($slotFilter ?? '') === 'legs' ? 'selected' : '' ?>>Jambes</option>
            <option value="main_hand" <?= ($slotFilter ?? '') === 'main_hand' ? 'selected' : '' ?>>Main principale</option>
            <option value="off_hand" <?= ($slotFilter ?? '') === 'off_hand' ? 'selected' : '' ?>>Main secondaire</option>
        </select>
        
        <button class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
    </div>
    
    <!-- Items Grid -->
    <?php if (!empty($items)): ?>
        <div class="items-grid">
            <?php foreach ($items as $item): ?>
                <?php 
                    $statRanges = json_decode($item['stat_ranges'] ?? '{}', true);
                ?>
                <div class="item-card">
                    <div class="item-header">
                        <div style="flex: 1;">
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <span class="item-type-badge type-<?= $item['type'] ?>">
                                <?= $item['type'] ?>
                            </span>
                            <?php if ($item['slot_type'] !== 'none'): ?>
                                <span style="margin-left: 0.5rem; color: var(--text-muted); font-size: 0.75rem;">
                                    📍 <?= $item['slot_type'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="item-icon">
                            <?= $item['icon'] ? '🎨' : '📦' ?>
                        </div>
                    </div>
                    
                    <?php if ($item['description']): ?>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">
                            <?= htmlspecialchars(substr($item['description'], 0, 80)) ?>
                            <?= strlen($item['description']) > 80 ? '...' : '' ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Stats -->
                    <?php if (!empty($statRanges)): ?>
                        <div class="stats-grid">
                            <?php foreach (['strength' => '💪', 'vitality' => '❤️', 'intelligence' => '🧠', 'dexterity' => '🎯'] as $stat => $icon): ?>
                                <?php if (isset($statRanges[$stat]) && ($statRanges[$stat]['min'] > 0 || $statRanges[$stat]['max'] > 0)): ?>
                                    <div class="stat-item">
                                        <div class="stat-label"><?= $icon ?> <?= ucfirst($stat) ?></div>
                                        <div class="stat-value">
                                            <?= $statRanges[$stat]['min'] ?> - <?= $statRanges[$stat]['max'] ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Properties -->
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-muted); flex-wrap: wrap;">
                        <span>📏 <?= $item['width'] ?>x<?= $item['height'] ?></span>
                        <span>⚖️ <?= $item['weight'] ?>kg</span>
                        <?php if ($item['two_handed']): ?>
                            <span>🤲 2 mains</span>
                        <?php endif; ?>
                        <?php if ($item['price']): ?>
                            <span style="color: #fbbf24; font-weight: 600;">💰 <?= number_format($item['price']) ?> pièces</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Actions -->
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                        <a href="/admin/items/edit/<?= $item['id'] ?>" class="btn btn-sm btn-primary" style="flex: 1;">
                            ✏️ Modifier
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteItem(<?= $item['id'] ?>)">
                            🗑️
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 4rem; color: var(--text-muted);">
            <p style="font-size: 3rem; margin-bottom: 1rem;">📦</p>
            <p>Aucun item trouvé</p>
            <a href="/admin/items/create" class="btn btn-primary" style="margin-top: 1rem;">
                Créer le premier item
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Search and filter functionality
let searchTimeout;

document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
});

document.getElementById('type-filter').addEventListener('change', applyFilters);
document.getElementById('slot-filter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const type = document.getElementById('type-filter').value;
    const slot = document.getElementById('slot-filter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (type) params.append('type', type);
    if (slot) params.append('slot', slot);
    
    window.location.href = '/admin/items?' + params.toString();
}

function resetFilters() {
    window.location.href = '/admin/items';
}

function deleteItem(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet item ?')) return;
    
    fetch(`/admin/items/delete/${id}`, {
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
