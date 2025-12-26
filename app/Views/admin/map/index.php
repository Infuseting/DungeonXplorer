<?php
$pageTitle = 'Gestion de la Carte';
ob_start();
?>

<style>
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 0.7;
        }

        50% {
            transform: scale(1.1);
            opacity: 1;
        }
    }
</style>

<div class="grid grid-cols-2 gap-6">
    <!-- Left: Map -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="card-header mb-0">Carte Interactive</h3>

            <!-- Map Selector Dropdown -->
            <div class="min-w-[250px]">
                <select id="map-selector"
                    class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                    onchange="changeMap(this.value)">
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
                    <input type="text" name="name"
                        class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                        required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-200 font-medium mb-2">Longitude (X)</label>
                        <input type="number" step="0.00000001" name="x" id="input-x"
                            class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                            required readonly>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-200 font-medium mb-2">Latitude (Y)</label>
                        <input type="number" step="0.00000001" name="y" id="input-y"
                            class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                            required readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Rayon de Cliquabilité (pixels)</label>
                    <input type="number" name="radius" id="input-radius"
                        class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                        value="50" min="10" max="200">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Type</label>
                    <select name="type"
                        class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                        required>
                        <option value="story">Histoire</option>
                        <option value="place">Lieu</option>
                        <option value="dungeon">Donjon</option>
                        <option value="npc">PNJ</option>
                        <option value="quest">Quête</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Description</label>
                    <textarea name="description"
                        class="w-full p-2.5 bg-gray-900 border border-gray-700 rounded-lg text-gray-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 resize-y min-h-[80px]"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2">Icône</label>
                    <input type="hidden" name="icon" id="selected-icon" value="">

                    <!-- Icon Selector Grid -->
                    <div
                        class="grid grid-cols-5 gap-3 mb-3 p-3 bg-gray-900 border border-gray-700 rounded-lg max-h-[200px] overflow-y-auto">
                        <button type="button"
                            class="icon-option aspect-square p-3 bg-gray-800 hover:bg-gray-700 border-2 border-transparent hover:border-indigo-500 rounded-lg transition-all flex items-center justify-center group"
                            data-icon="" title="Aucune icône">
                            <span class="text-gray-400 text-xs">Aucune</span>
                        </button>
                        <?php foreach ($availableIcons as $icon): ?>
                            <?php
                            $iconName = pathinfo($icon, PATHINFO_FILENAME);
                            $displayName = ucfirst(str_replace(['_', '-'], ' ', $iconName));
                            ?>
                            <button type="button"
                                class="icon-option aspect-square p-3 bg-gray-800 hover:bg-gray-700 border-2 border-transparent hover:border-indigo-500 rounded-lg transition-all"
                                data-icon="<?= htmlspecialchars($icon) ?>" title="<?= htmlspecialchars($displayName) ?>">
                                <img src="/assets/map/icons/<?= htmlspecialchars($icon) ?>"
                                    alt="<?= htmlspecialchars($displayName) ?>"
                                    class="w-full h-full object-contain opacity-70 group-hover:opacity-100">
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Upload Custom Icon -->
                    <div class="flex gap-2">
                        <input type="file" id="custom-icon-upload" accept=".svg,.png,.jpg,.jpeg" class="hidden">
                        <button type="button" onclick="document.getElementById('custom-icon-upload').click()"
                            class="flex-1 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Upload personnalisé
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-200 font-medium mb-2 flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_hidden" value="1">
                        Caché par défaut (nécessite déblocage)
                    </label>
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                    Créer le Point
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Points List (Full Width Below) -->
<div class="card mt-6">
    <h3 class="card-header">Points Existants</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php if (!empty($mapPoints)): ?>
            <?php foreach ($mapPoints as $point): ?>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 transition-all duration-200 hover:border-indigo-500 cursor-pointer"
                    onclick="editPoint(<?= htmlspecialchars(json_encode($point)) ?>)">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-200"><?= htmlspecialchars($point['name']) ?></span>
                        <?php
                        $typeClass = match ($point['type']) {
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
                    <?php if ($point['icon']): ?>
                        <div class="flex items-center gap-2 mb-2">
                            <img src="/assets/map/icons/<?= htmlspecialchars($point['icon']) ?>" alt="Icon" class="w-6 h-6">
                            <span class="text-gray-400 text-xs"><?= htmlspecialchars($point['icon']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="text-gray-400 text-xs mb-2">
                        📍 (<?= number_format($point['x'], 6) ?>, <?= number_format($point['y'], 6) ?>)
                        • Rayon: <?= $point['radius'] ?>px
                    </div>
                    <?php if ($point['description']): ?>
                        <p class="text-gray-400 text-sm mt-2 line-clamp-2">
                            <?= htmlspecialchars($point['description']) ?>
                        </p>
                    <?php endif; ?>
                    <div class="mt-3 flex gap-2">
                        <button class="px-3 py-1.5 text-sm bg-indigo-600 text-white hover:bg-indigo-700 rounded"
                            onclick="event.stopPropagation(); editPoint(<?= htmlspecialchars(json_encode($point)) ?>)">
                            Éditer
                        </button>
                        <button class="px-3 py-1.5 text-sm bg-red-600 text-white hover:bg-red-700 rounded"
                            onclick="event.stopPropagation(); deletePoint(<?= $point['id'] ?>)">
                            Supprimer
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-400 text-center py-8 col-span-full">
                Aucun point sur la carte
            </p>
        <?php endif; ?>
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

            const basePath = selectedMapConfig.substring(0, selectedMapConfig.lastIndexOf('/'));
            const tileLayer = L.tileLayer(basePath + '/{z}/{x}/{y}.png', {
                tileSize: tileSize,
                minZoom: maxZoom - 3,
                maxZoom: maxZoom,
                noWrap: true,
                continuousWorld: false,
                attribution: '',
                errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
            });

            // Silently handle tile loading errors
            tileLayer.on('tileerror', function (error) {
                // Suppress console errors for missing tiles
            });

            tileLayer.addTo(adminMap);

            const southWest = adminMap.unproject([0, height], maxZoom);
            const northEast = adminMap.unproject([width, 0], maxZoom);
            const bounds = new L.LatLngBounds(southWest, northEast);

            adminMap.setMaxBounds(bounds);
            adminMap.fitBounds(bounds);

            adminMap.on('click', function (e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                document.getElementById('input-x').value = lng.toFixed(8);
                document.getElementById('input-y').value = lat.toFixed(8);

                if (tempMarker) {
                    adminMap.removeLayer(tempMarker);
                }
                if (radiusCircle) {
                    adminMap.removeLayer(radiusCircle);
                }

                tempMarker = L.circleMarker(e.latlng, {
                    radius: 8,
                    fillColor: '#6366f1',
                    color: '#4f46e5',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.6
                }).addTo(adminMap);

                updateRadiusCircle(e.latlng);
            });

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

    document.getElementById('input-radius').addEventListener('input', function () {
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

        const mapRadius = radius / 10;
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

    function editPoint(pointData) {
        // Scroll to form
        document.getElementById('point-form').scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Fill form fields
        const form = document.getElementById('point-form');
        form.querySelector('[name="name"]').value = pointData.name || '';
        document.getElementById('input-x').value = pointData.x || '';
        document.getElementById('input-y').value = pointData.y || '';
        document.getElementById('input-radius').value = pointData.radius || 50;
        form.querySelector('[name="type"]').value = pointData.type || 'place';
        form.querySelector('[name="description"]').value = pointData.description || '';
        form.querySelector('[name="is_hidden"]').checked = pointData.is_hidden == 1;

        // Update icon selection
        const selectedIconInput = document.getElementById('selected-icon');
        selectedIconInput.value = pointData.icon || '';

        // Update icon selector visual state
        document.querySelectorAll('.icon-option').forEach(opt => {
            opt.classList.remove('border-indigo-500', 'bg-indigo-900/30');
            if (opt.dataset.icon === (pointData.icon || '')) {
                opt.classList.add('border-indigo-500', 'bg-indigo-900/30');
            }
        });

        // Update marker on map
        if (tempMarker) {
            adminMap.removeLayer(tempMarker);
        }
        if (radiusCircle) {
            adminMap.removeLayer(radiusCircle);
        }

        const latlng = L.latLng(parseFloat(pointData.y), parseFloat(pointData.x));
        tempMarker = L.circleMarker(latlng, {
            radius: 8,
            fillColor: '#6366f1',
            color: '#4f46e5',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.6
        }).addTo(adminMap);

        updateRadiusCircle(latlng);
        adminMap.panTo(latlng);

        // Change form to update mode
        form.action = '/admin/map/update/' + pointData.id;
        form.querySelector('button[type="submit"]').textContent = 'Mettre à jour le Point';
        form.querySelector('button[type="submit"]').className = 'w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-lg';

        // Add cancel button if not exists
        if (!document.getElementById('cancel-edit-btn')) {
            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.id = 'cancel-edit-btn';
            cancelBtn.className = 'w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors shadow-lg mt-2';
            cancelBtn.textContent = 'Annuler l\'édition';
            cancelBtn.onclick = cancelEdit;
            form.querySelector('button[type="submit"]').after(cancelBtn);
        }
    }

    function cancelEdit() {
        const form = document.getElementById('point-form');
        form.reset();
        form.action = '/admin/map/create';
        form.querySelector('button[type="submit"]').textContent = 'Créer le Point';
        form.querySelector('button[type="submit"]').className = 'w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-lg';

        const cancelBtn = document.getElementById('cancel-edit-btn');
        if (cancelBtn) cancelBtn.remove();

        // Clear selected icon
        document.getElementById('selected-icon').value = '';
        document.querySelectorAll('.icon-option').forEach(opt => {
            opt.classList.remove('border-indigo-500', 'bg-indigo-900/30');
        });

        // Clear marker
        if (tempMarker) {
            adminMap.removeLayer(tempMarker);
            tempMarker = null;
        }
        if (radiusCircle) {
            adminMap.removeLayer(radiusCircle);
            radiusCircle = null;
        }
    }

    // Icon Selector Logic
    document.addEventListener('DOMContentLoaded', () => {
        const iconOptions = document.querySelectorAll('.icon-option');
        const selectedIconInput = document.getElementById('selected-icon');
        const customIconUpload = document.getElementById('custom-icon-upload');

        iconOptions.forEach(option => {
            option.addEventListener('click', function (e) {
                e.preventDefault();

                // Remove selected state from all
                iconOptions.forEach(opt => opt.classList.remove('border-indigo-500', 'bg-indigo-900/30'));

                // Add selected state
                this.classList.add('border-indigo-500', 'bg-indigo-900/30');

                // Update hidden input
                selectedIconInput.value = this.dataset.icon;
            });
        });

        // Custom icon upload
        if (customIconUpload) {
            customIconUpload.addEventListener('change', async function (e) {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('icon', file);

                try {
                    const response = await fetch('/admin/map/upload-icon', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        selectedIconInput.value = data.filename;
                        alert('Icône uploadée avec succès : ' + data.filename);
                    } else {
                        alert('Erreur lors de l\'upload : ' + data.message);
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Erreur lors de l\'upload de l\'icône');
                }
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>