<?php
$pageTitle = 'Gestion des Points';
ob_start();
?>

<style>
    .search-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    
    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 0.625rem;
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        color: var(--text-light);
    }
    
    .filter-select {
        padding: 0.625rem;
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        color: var(--text-light);
        min-width: 150px;
    }
    
    .points-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--bg-darker);
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .points-table thead {
        background: rgba(99, 102, 241, 0.1);
    }
    
    .points-table th {
        padding: 1rem;
        text-align: left;
        color: var(--text-light);
        font-weight: 600;
        border-bottom: 1px solid var(--border);
    }
    
    .points-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        color: var(--text-muted);
    }
    
    .points-table tr:hover {
        background: rgba(99, 102, 241, 0.05);
    }
    
    .type-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        display: inline-block;
    }
    
    .type-story { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; }
    .type-place { background: rgba(34, 197, 94, 0.2); color: #86efac; }
    .type-dungeon { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
    .type-npc { background: rgba(251, 191, 36, 0.2); color: #fde047; }
    .type-quest { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; }
    
    .submap-select {
        padding: 0.5rem;
        background: var(--bg-dark);
        border: 1px solid var(--border);
        border-radius: 0.375rem;
        color: var(--text-light);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .submap-select:hover {
        border-color: var(--primary);
    }
    
    .submap-select:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        padding: 1rem 1.5rem;
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        color: var(--text-light);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease-out;
        z-index: 1000;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .toast.success { border-color: #22c55e; }
    .toast.error { border-color: #ef4444; }
</style>

<div class="card">
    <h3 class="card-header">Gestion des Points de Carte</h3>
    
    <!-- Search and Filters -->
    <div class="search-bar">
        <input 
            type="text" 
            id="search-input" 
            class="search-input" 
            placeholder="🔍 Rechercher par nom..."
            value="<?= htmlspecialchars($search ?? '') ?>"
        >
        
        <select id="type-filter" class="filter-select">
            <option value="">Tous les types</option>
            <option value="story" <?= ($typeFilter ?? '') === 'story' ? 'selected' : '' ?>>Histoire</option>
            <option value="place" <?= ($typeFilter ?? '') === 'place' ? 'selected' : '' ?>>Lieu</option>
            <option value="dungeon" <?= ($typeFilter ?? '') === 'dungeon' ? 'selected' : '' ?>>Donjon</option>
            <option value="npc" <?= ($typeFilter ?? '') === 'npc' ? 'selected' : '' ?>>PNJ</option>
            <option value="quest" <?= ($typeFilter ?? '') === 'quest' ? 'selected' : '' ?>>Quête</option>
        </select>
        
        <select id="map-filter" class="filter-select">
            <option value="">Toutes les cartes</option>
            <?php foreach ($maps as $map): ?>
                <option value="<?= $map['id'] ?>" <?= ($mapFilter ?? '') == $map['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($map['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
    </div>
    
    <!-- Points Table -->
    <div style="overflow-x: auto;">
        <table class="points-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Carte</th>
                    <th>Coordonnées</th>
                    <th>Sous-Carte</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($points)): ?>
                    <?php foreach ($points as $point): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--text-light);">
                                    <?= htmlspecialchars($point['name']) ?>
                                </strong>
                                <?php if ($point['description']): ?>
                                    <br>
                                    <small style="color: var(--text-muted);">
                                        <?= htmlspecialchars(substr($point['description'], 0, 50)) ?>
                                        <?= strlen($point['description']) > 50 ? '...' : '' ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="type-badge type-<?= $point['type'] ?>">
                                    <?= $point['type'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($point['map_name'] ?? 'N/A') ?></td>
                            <td>
                                <small>
                                    X: <?= number_format($point['x'], 2) ?><br>
                                    Y: <?= number_format($point['y'], 2) ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($point['type'] === 'place'): ?>
                                    <select 
                                        class="submap-select" 
                                        onchange="updateSubMap(<?= $point['id'] ?>, this.value)"
                                    >
                                        <option value="">Aucune sous-carte</option>
                                        <?php foreach ($maps as $map): ?>
                                            <option 
                                                value="<?= $map['id'] ?>" 
                                                <?= $point['sub_map_id'] == $map['id'] ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($map['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-sm btn-danger" 
                                    onclick="deletePoint(<?= $point['id'] ?>)"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            Aucun point trouvé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Search and filter functionality
let searchTimeout;

document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
});

document.getElementById('type-filter').addEventListener('change', applyFilters);
document.getElementById('map-filter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const type = document.getElementById('type-filter').value;
    const mapId = document.getElementById('map-filter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (type) params.append('type', type);
    if (mapId) params.append('map_id', mapId);
    
    window.location.href = '/admin/points?' + params.toString();
}

function resetFilters() {
    window.location.href = '/admin/points';
}

// Update sub-map assignment
function updateSubMap(pointId, subMapId) {
    fetch('/admin/points/update-submap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            point_id: pointId,
            sub_map_id: subMapId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Erreur de communication avec le serveur', 'error');
    });
}

// Delete point
function deletePoint(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce point ?')) return;
    
    fetch(`/admin/map/delete/${id}`, {
        method: 'POST'
    }).then(() => {
        location.reload();
    });
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
