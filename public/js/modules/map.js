/**
 * Map Module
 * Handles Leaflet map initialization and configuration
 */

let gameMap = null;
let gameMapMaxZoom = null;
let currentMarkers = [];

/**
 * Initialize the Leaflet map
 * @param {string} configUrl - URL to the map configuration JSON
 * @param {string} tilesPath - Path to the tiles directory
 * @param {string} containerId - ID of the container element
 * @returns {Promise<L.Map>} The initialized map instance
 */
export async function initMap(configUrl = '/assets/map/main/map_config.json', tilesPath = '/assets/map/main', containerId = 'map') {
    try {
        const response = await fetch(configUrl);
        const cfg = await response.json();

        const tileSize = cfg.tile_size || 256;
        const maxZoom = cfg.max_zoom || 0;
        const width = cfg.width;
        const height = cfg.height;

        // Remove existing map if any
        if (gameMap) {
            gameMap.remove();
            gameMap = null;
        }

        const map = L.map(containerId, {
            crs: L.CRS.Simple,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            center: [0, 0],
            zoom: maxZoom,
            zoomControl: containerId !== 'map', // Show zoom control for submaps
            attributionControl: false,
            maxBoundsViscosity: 1.0
        });

        const tileLayer = L.tileLayer(tilesPath + '/{z}/{x}/{y}.png', {
            tileSize: tileSize,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            noWrap: true,
            continuousWorld: false,
            attribution: ''
        }).addTo(map);

        const southWest = map.unproject([0, height], maxZoom);
        const northEast = map.unproject([width, 0], maxZoom);
        const bounds = new L.LatLngBounds(southWest, northEast);

        map.setMaxBounds(bounds);
        map.fitBounds(bounds);

        // Store globally for access
        gameMap = map;
        gameMapMaxZoom = maxZoom;

        // Trigger event that map is ready
        window.dispatchEvent(new CustomEvent('mapReady', { detail: { containerId } }));

        return map;
    } catch (err) {
        console.error('Impossible de charger map_config.json', err);
        const mapDiv = document.getElementById(containerId);
        if (mapDiv) {
            mapDiv.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erreur de chargement de la carte</div>';
        }
        throw err;
    }
}

/**
 * Load and display map points
 * @param {number} mapId - ID of the map
 * @param {number} characterId - ID of the character
 * @param {L.Map} map - Optional map instance (uses gameMap if not provided)
 */
export async function loadMapPoints(mapId, characterId, map = null) {
    const targetMap = map || gameMap;

    if (!targetMap) {
        console.error('No map instance available');
        return;
    }

    try {
        // Clear existing markers
        clearMapPoints();

        // Fetch points from API
        const response = await fetch(`/game/map/points/${mapId}?character_id=${characterId}`);
        const data = await response.json();

        if (!data.success) {
            console.error('Failed to load map points:', data.message);
            return;
        }

        // Import mapPoints module dynamically to avoid circular dependency
        const { initMapPoints } = await import('./mapPoints.js');

        // Store markers reference
        const markers = initMapPoints(targetMap, data.points);
        currentMarkers = markers || [];

        console.log(`Loaded ${data.points.length} points for map ${mapId}`);
    } catch (error) {
        console.error('Error loading map points:', error);
    }
}

/**
 * Clear all map markers
 */
export function clearMapPoints() {
    if (currentMarkers && currentMarkers.length > 0) {
        currentMarkers.forEach(marker => {
            if (marker && marker.remove) {
                marker.remove();
            }
        });
        currentMarkers = [];
    }
}

/**
 * Reload map with new data
 * @param {number} mapId - ID of the map to load
 * @param {number} characterId - ID of the character
 * @param {string} configUrl - URL to the map configuration
 * @param {string} tilesPath - Path to the tiles
 */
export async function reloadMap(mapId, characterId, configUrl = '/assets/map/main/map_config.json', tilesPath = '/assets/map/main') {
    try {
        // Reinitialize map
        await initMap(configUrl, tilesPath, 'map');

        // Load points for this map
        await loadMapPoints(mapId, characterId);

        console.log(`Map ${mapId} reloaded successfully`);
    } catch (error) {
        console.error('Error reloading map:', error);
    }
}

/**
 * Get the current map instance
 * @returns {L.Map|null} The map instance or null if not initialized
 */
export function getMap() {
    return gameMap;
}

/**
 * Get the maximum zoom level
 * @returns {number|null} The max zoom level or null if not initialized
 */
export function getMaxZoom() {
    return gameMapMaxZoom;
}
