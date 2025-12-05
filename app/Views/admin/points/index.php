<?php
$pageTitle = 'Gestion des Points';
ob_start();
?>

<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<div class="card">
    <h3 class="card-header">Gestion des Points de Carte</h3>
    
    <!-- Search and Filters -->
    <div class="flex gap-4 mb-6 flex-wrap items-center">
        <!-- Search Input with Icon -->
        <div class="relative flex-1 min-w-[250px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text" 
                id="search-input" 
                class="w-full pl-10 pr-4 py-2.5 bg-gray-800 border-2 border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all" 
                placeholder="Rechercher par nom..."
                value="<?= htmlspecialchars($search ?? '') ?>"
            >
        </div>
        
        <!-- Type Filter with Icon -->
        <div class="relative min-w-[180px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <select id="type-filter" class="w-full pl-10 pr-4 py-2.5 bg-gray-800 border-2 border-gray-600 rounded-lg text-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all appearance-none cursor-pointer">
                <option value="">Tous les types</option>
                <option value="story" <?= ($typeFilter ?? '') === 'story' ? 'selected' : '' ?>>📖 Histoire</option>
                <option value="place" <?= ($typeFilter ?? '') === 'place' ? 'selected' : '' ?>>🏛️ Lieu</option>
                <option value="dungeon" <?= ($typeFilter ?? '') === 'dungeon' ? 'selected' : '' ?>>⚔️ Donjon</option>
                <option value="npc" <?= ($typeFilter ?? '') === 'npc' ? 'selected' : '' ?>>👤 PNJ</option>
                <option value="quest" <?= ($typeFilter ?? '') === 'quest' ? 'selected' : '' ?>>📜 Quête</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
        
        <!-- Map Filter with Icon -->
        <div class="relative min-w-[180px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
            </div>
            <select id="map-filter" class="w-full pl-10 pr-4 py-2.5 bg-gray-800 border-2 border-gray-600 rounded-lg text-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all appearance-none cursor-pointer">
                <option value="">Toutes les cartes</option>
                <?php foreach ($maps as $map): ?>
                    <option value="<?= $map['id'] ?>" <?= ($mapFilter ?? '') == $map['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($map['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
        
        <button class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors flex items-center gap-2" onclick="resetFilters()">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Réinitialiser
        </button>
    </div>
    
    <!-- Points Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse bg-gray-900 rounded-xl overflow-hidden">
            <thead class="bg-indigo-500/10">
                <tr>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">Nom</th>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">Type</th>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">Carte</th>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">Visibilité</th>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">Sous-Carte</th>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">PNJ Associé</th>
                    <th class="p-4 text-left text-gray-100 font-semibold border-b border-gray-700">Actions</th>
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
                                <?php 
                                $typeClasses = [
                                    'npc' => 'bg-amber-500/30 text-amber-200 border border-amber-400/30',
                                    'quest' => 'bg-purple-500/30 text-purple-200 border border-purple-400/30',
                                    'place' => 'bg-blue-500/30 text-blue-200 border border-blue-400/30',
                                    'story' => 'bg-red-500/30 text-red-200 border border-red-400/30'
                                ];
                                $typeClass = $typeClasses[$point['type']] ?? 'bg-gray-500/20 text-gray-300';
                                ?>
                                <span class="px-3 py-1 rounded text-xs font-medium uppercase inline-block <?= $typeClass ?>">
                                    <?= $point['type'] ?>
                                </span>
                            </td>
                            <td class="p-4 border-b border-gray-700 text-gray-200"><?= htmlspecialchars($point['map_name'] ?? 'N/A') ?></td>
                            <td class="p-4 border-b border-gray-700">
                                <label class="relative inline-block w-10 h-5 align-middle" title="Visible par défaut ?">
                                    <input type="checkbox" 
                                           class="opacity-0 w-0 h-0 peer"
                                           onchange="toggleVisibility(<?= $point['id'] ?>, !this.checked)"
                                           <?= empty($point['is_hidden']) ? 'checked' : '' ?>>
                                    <span class="absolute cursor-pointer inset-0 bg-gray-600 transition-[.4s] rounded-[20px] before:absolute before:content-[''] before:h-4 before:w-4 before:left-0.5 before:bottom-0.5 before:bg-white before:transition-[.4s] before:rounded-full peer-checked:bg-indigo-500 peer-checked:before:translate-x-5"></span>
                                </label>
                                <small class="block text-gray-300 mt-1 font-medium">
                                    <?= empty($point['is_hidden']) ? '✓ Visible' : '✗ Caché' ?>
                                </small>
                            </td>
                            <td class="p-4 border-b border-gray-700">
                                <small class="text-gray-300">
                                    <span class="font-mono">X: <?= number_format($point['x'], 2) ?></span><br>
                                    <span class="font-mono">Y: <?= number_format($point['y'], 2) ?></span>
                                </small>
                            </td>
                            <td class="p-4 border-b border-gray-700">
                                <?php if ($point['type'] === 'place'): ?>
                                    <select 
                                        class="p-2 bg-gray-800 border border-gray-700 rounded-md text-gray-100 cursor-pointer transition-all duration-200 hover:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" 
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
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 border-b border-gray-700 text-gray-400">
                                <?php if ($point['type'] === 'npc'): ?>
                                    <select 
                                        class="p-2 bg-gray-800 border border-gray-700 rounded-md text-gray-100 cursor-pointer transition-all duration-200 hover:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" 
                                        onchange="updateNPC(<?= $point['id'] ?>, this.value)"
                                    >
                                        <option value="">Aucun PNJ</option>
                                        <?php foreach ($npcs as $npc): ?>
                                            <option 
                                                value="<?= $npc['id'] ?>" 
                                                <?= $point['target_id'] == $npc['id'] ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($npc['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($point['target_id']): ?>
                                        <a href="/admin/npcs/edit/<?= $point['target_id'] ?>" 
                                           class="btn btn-sm btn-primary mt-2 inline-block">
                                            👤 Gérer PNJ
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 border-b border-gray-700 text-gray-400">
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
                        <td colspan="7" class="text-center p-12 text-gray-400">
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

// Update visibility
function toggleVisibility(pointId, isVisible) {
    // isVisible is true if checked (Visible), so is_hidden should be 0
    // isVisible is false if unchecked (Hidden), so is_hidden should be 1
    const isHidden = isVisible ? 0 : 1;
    
    fetch('/admin/points/update-visibility', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            point_id: pointId,
            is_hidden: isHidden
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            // Update text label
            const row = document.querySelector(`input[onchange*="${pointId}"]`).closest('td');
            const label = row.querySelector('small');
            if (label) {
                label.textContent = isHidden ? 'Caché' : 'Visible';
            }
        } else {
            showToast(data.message, 'error');
            // Revert checkbox state
            const checkbox = document.querySelector(`input[onchange*="${pointId}"]`);
            checkbox.checked = !isVisible;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Erreur de communication avec le serveur', 'error');
    });
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

// Update NPC assignment
function updateNPC(pointId, npcId) {
    fetch('/admin/points/update-npc', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            point_id: pointId,
            npc_id: npcId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
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
    toast.className = `fixed bottom-8 right-8 px-6 py-4 bg-gray-900 border rounded-lg text-gray-100 shadow-[0_10px_25px_rgba(0,0,0,0.3)] animate-[slideIn_0.3s_ease-out] z-[1000] ${type === 'success' ? 'border-green-500' : 'border-red-500'}`;
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
