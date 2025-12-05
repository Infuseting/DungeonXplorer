<?php
$pageTitle = 'Gestion de la Carte';
ob_start();
?>

<style>
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.1); opacity: 1; }
    }
</style>

<div class="grid grid-cols-2 gap-6">
    <!-- Left: Map -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-header mb-0">Carte Interactive</h3>
            
            <!-- Map Selector Dropdown -->
            <div class="min-w-[250px]">
                <select id="map-selector" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" onchange="changeMap(this.value)">
                    <?php foreach ($maps as $map): ?>
                        <option value="<?= $map['id'] ?>" <?= $map['id'] == $selectedMap['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($map['name']) ?> (ID: <?= $map['id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <p class="text-gray-400 mb-4 text-sm">
            Cliquez sur la carte pour placer un nouveau point
        </p>
        <div id="admin-map" class="h-[600px] rounded-xl overflow-hidden border border-gray-700"></div>
    </div>
    
    <!-- Right: Form & Points List -->
    <div class="flex flex-col gap-6">
        <!-- Add Point Form -->
        <div class="card">
            <h3 class="card-header">Ajouter un Point</h3>
            <form id="point-form" method="POST" action="/admin/map/create">
                <input type="hidden" name="map_id" id="form-map-id" value="<?= $selectedMap['id'] ?>">
                
                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Nom du Point</label>
                    <input type="text" name="name" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-200 font-medium mb-2">Longitude (X)</label>
                        <input type="number" step="0.00000001" name="x" id="input-x" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" required readonly>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-200 font-medium mb-2">Latitude (Y)</label>
                        <input type="number" step="0.00000001" name="y" id="input-y" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" required readonly>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Rayon de Cliquabilité (pixels)</label>
                    <input type="number" name="radius" id="input-radius" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" value="50" min="10" max="200">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Type</label>
                    <select name="type" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" required>
                        <option value="story">Histoire</option>
                        <option value="place">Lieu</option>
                        <option value="dungeon">Donjon</option>
                        <option value="npc">PNJ</option>
                        <option value="quest">Quête</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Description</label>
                    <textarea name="description" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 resize-y min-h-[80px]"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Icône (optionnel)</label>
                    <input type="text" name="icon" class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10" placeholder="icon.png">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2 flex items-center gap-2 cursor-pointer">
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
            <div class="max-h-[400px] overflow-y-auto">
                <?php if (!empty($mapPoints)): ?>
                    <?php foreach ($mapPoints as $point): ?>
                        <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 mb-3 transition-all duration-200 hover:border-indigo-500 hover:translate-x-1">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-gray-200"><?= htmlspecialchars($point['name']) ?></span>
                                <?php
                                    $typeClass = match($point['type']) {
                                        'story' => 'bg-indigo-500/20 text-indigo-300',
                                        'place' => 'bg-green-500/20 text-green-300',
                                        'dungeon' => 'bg-red-500/20 text-red-300',
                                        'npc' => 'bg-yellow-500/20 text-yellow-300',
                                        'quest' => 'bg-purple-500/20 text-purple-300',
                                        default => 'bg-gray-700 text-gray-300'
                                    };
                                ?>
                                <span class="px-3 py-1 rounded text-xs font-medium uppercase <?= $typeClass ?>">
                                    <?= $point['type'] ?>
                                </span>
                            </div>
                            <div class="text-gray-400 text-xs">
                                📍 (<?= number_format($point['x'], 6) ?>, <?= number_format($point['y'], 6) ?>) 
                                • Rayon: <?= $point['radius'] ?>px
                            </div>
                            <?php if ($point['description']): ?>
                                <p class="text-gray-400 text-sm mt-2">
                                    <?= htmlspecialchars($point['description']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="mt-3 flex gap-2">
                                <button class="px-3 py-1.5 text-sm bg-red-600 text-white hover:bg-red-700 rounded" onclick="deletePoint(<?= $point['id'] ?>)">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400 text-center py-8">
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
        document.getElementById('admin-map').innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erreur de chargement de la carte</div>';
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
