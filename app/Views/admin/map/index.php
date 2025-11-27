<?php
$pageTitle = 'Gestion de la Carte';
ob_start();
?>

<style>
    #admin-map {
        height: 600px;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid var(--border);
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        color: var(--text-light);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.625rem;
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        color: var(--text-light);
        font-size: 0.875rem;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .points-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .point-item {
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }
    
    .point-item:hover {
        border-color: var(--primary);
        transform: translateX(4px);
    }
    
    .point-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .point-name {
        font-weight: 600;
        color: var(--text-light);
    }
    
    .point-type {
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .type-story { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; }
    .type-place { background: rgba(34, 197, 94, 0.2); color: #86efac; }
    .type-dungeon { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
    .type-npc { background: rgba(251, 191, 36, 0.2); color: #fde047; }
    .type-quest { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; }
    
    .point-coords {
        color: var(--text-muted);
        font-size: 0.75rem;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .btn-danger {
        background: #dc2626;
        color: white;
    }
    
    .btn-danger:hover {
        background: #b91c1c;
    }
    
    .leaflet-container {
        background: transparent;
    }
    
    .temp-marker {
        background: rgba(99, 102, 241, 0.3);
        border: 2px solid var(--primary);
        border-radius: 50%;
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.1); opacity: 1; }
    }
</style>

<div class="grid grid-cols-2 gap-6">
    <!-- Left: Map -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-header" style="margin-bottom: 0;">Carte Interactive</h3>
            
            <!-- Map Selector Dropdown -->
            <div style="min-width: 250px;">
                <select id="map-selector" class="form-select" onchange="changeMap(this.value)">
                    <?php foreach ($maps as $map): ?>
                        <option value="<?= $map['id'] ?>" <?= $map['id'] == $selectedMap['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($map['name']) ?> (ID: <?= $map['id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.875rem;">
            Cliquez sur la carte pour placer un nouveau point
        </p>
        <div id="admin-map"></div>
    </div>
    
    <!-- Right: Form & Points List -->
    <div class="flex flex-col gap-6">
        <!-- Add Point Form -->
        <div class="card">
            <h3 class="card-header">Ajouter un Point</h3>
            <form id="point-form" method="POST" action="/admin/map/create">
                <input type="hidden" name="map_id" id="form-map-id" value="<?= $selectedMap['id'] ?>">
                
                <div class="form-group">
                    <label class="form-label">Nom du Point</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Longitude (X)</label>
                        <input type="number" step="0.00000001" name="x" id="input-x" class="form-input" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Latitude (Y)</label>
                        <input type="number" step="0.00000001" name="y" id="input-y" class="form-input" required readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rayon de Cliquabilité (pixels)</label>
                    <input type="number" name="radius" id="input-radius" class="form-input" value="50" min="10" max="200">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="story">Histoire</option>
                        <option value="place">Lieu</option>
                        <option value="dungeon">Donjon</option>
                        <option value="npc">PNJ</option>
                        <option value="quest">Quête</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Icône (optionnel)</label>
                    <input type="text" name="icon" class="form-input" placeholder="icon.png">
                </div>

                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="is_hidden" value="1">
                        Caché par défaut (nécessite déblocage)
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-full">
                    Créer le Point
                </button>
            </form>
        </div>
        
        <!-- Points List -->
        <div class="card">
            <h3 class="card-header">Points Existants</h3>
            <div class="points-list">
                <?php if (!empty($mapPoints)): ?>
                    <?php foreach ($mapPoints as $point): ?>
                        <div class="point-item">
                            <div class="point-header">
                                <span class="point-name"><?= htmlspecialchars($point['name']) ?></span>
                                <span class="point-type type-<?= $point['type'] ?>">
                                    <?= $point['type'] ?>
                                </span>
                            </div>
                            <div class="point-coords">
                                📍 (<?= number_format($point['x'], 6) ?>, <?= number_format($point['y'], 6) ?>) 
                                • Rayon: <?= $point['radius'] ?>px
                            </div>
                            <?php if ($point['description']): ?>
                                <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.5rem;">
                                    <?= htmlspecialchars($point['description']) ?>
                                </p>
                            <?php endif; ?>
                            <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                                <button class="btn btn-sm btn-danger" onclick="deletePoint(<?= $point['id'] ?>)">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 2rem;">
                        Aucun point sur la carte
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
let adminMap;
let tempMarker = null;
let radiusCircle = null;
const selectedMapId = <?= $selectedMap['id'] ?>;
const selectedMapConfig = '<?= $selectedMap['image_path'] ?? '/assets/map/main/map_config.json' ?>';

function changeMap(mapId) {
    window.location.href = '/admin/map?map_id=' + mapId;
}

document.addEventListener('DOMContentLoaded', () => {
    fetch(selectedMapConfig).then(r => r.json()).then(cfg => {
        const tileSize = cfg.tile_size || 256;
        const maxZoom = cfg.max_zoom || 0;
        const width = cfg.width;
        const height = cfg.height;

        adminMap = L.map('admin-map', {
            crs: L.CRS.Simple,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            center: [0, 0],
            zoom: maxZoom - 1,
            zoomControl: true,
            attributionControl: false,
            maxBoundsViscosity: 1.0
        });

        // Determine tile path from config path
        const basePath = selectedMapConfig.substring(0, selectedMapConfig.lastIndexOf('/'));
        L.tileLayer(basePath + '/{z}/{x}/{y}.png', {
            tileSize: tileSize,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            noWrap: true,
            continuousWorld: false,
            attribution: ''
        }).addTo(adminMap);

        const southWest = adminMap.unproject([0, height], maxZoom);
        const northEast = adminMap.unproject([width, 0], maxZoom);
        const bounds = new L.LatLngBounds(southWest, northEast);

        adminMap.setMaxBounds(bounds);
        adminMap.fitBounds(bounds);

        // Click to place marker
        adminMap.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            // Update form inputs
            document.getElementById('input-x').value = lng.toFixed(8);
            document.getElementById('input-y').value = lat.toFixed(8);
            
            // Remove previous temp marker
            if (tempMarker) {
                adminMap.removeLayer(tempMarker);
            }
            if (radiusCircle) {
                adminMap.removeLayer(radiusCircle);
            }
            
            // Add new temp marker
            tempMarker = L.circleMarker(e.latlng, {
                radius: 8,
                fillColor: '#6366f1',
                color: '#4f46e5',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.6
            }).addTo(adminMap);
            
            // Add radius circle
            updateRadiusCircle(e.latlng);
        });

        // Load existing points
        <?php if (!empty($mapPoints)): ?>
            <?php foreach ($mapPoints as $point): ?>
                const marker<?= $point['id'] ?> = L.circleMarker([<?= $point['y'] ?>, <?= $point['x'] ?>], {
                    radius: 6,
                    fillColor: getTypeColor('<?= $point['type'] ?>'),
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(adminMap);
                
                marker<?= $point['id'] ?>.bindPopup(`
                    <strong><?= htmlspecialchars($point['name']) ?></strong><br>
                    Type: <?= $point['type'] ?><br>
                    Rayon: <?= $point['radius'] ?>px
                `);
            <?php endforeach; ?>
        <?php endif; ?>
    }).catch(err => {
        console.error('Impossible de charger map_config.json', err);
        document.getElementById('admin-map').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ef4444;">Erreur de chargement de la carte</div>';
    });
});

// Update radius circle when radius input changes
document.getElementById('input-radius').addEventListener('input', function() {
    if (tempMarker) {
        const lat = parseFloat(document.getElementById('input-y').value);
        const lng = parseFloat(document.getElementById('input-x').value);
        updateRadiusCircle(L.latLng(lat, lng));
    }
});

function updateRadiusCircle(latlng) {
    const radius = parseInt(document.getElementById('input-radius').value) || 50;
    
    if (radiusCircle) {
        adminMap.removeLayer(radiusCircle);
    }
    
    // Convert pixel radius to map units (approximate)
    const mapRadius = radius / 10; // Adjust this factor based on your map scale
    
    radiusCircle = L.circle(latlng, {
        radius: mapRadius,
        color: '#6366f1',
        fillColor: '#6366f1',
        fillOpacity: 0.1,
        weight: 1,
        dashArray: '5, 5'
    }).addTo(adminMap);
}

function getTypeColor(type) {
    const colors = {
        'story': '#6366f1',
        'place': '#22c55e',
        'dungeon': '#ef4444',
        'npc': '#fbbf24',
        'quest': '#a855f7'
    };
    return colors[type] || '#6366f1';
}

function deletePoint(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce point ?')) return;
    
    fetch(`/admin/map/delete/${id}`, {
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
