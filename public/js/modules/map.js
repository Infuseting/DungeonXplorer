/**
 * Map Module
 * Handles Leaflet map initialization and configuration
 */

let gameMap = null;
let gameMapMaxZoom = null;

/**
 * Initialize the Leaflet map
 * @param {string} configUrl - URL to the map configuration JSON
 * @returns {Promise<L.Map>} The initialized map instance
 */
export async function initMap(configUrl = '/assets/map/main/map_config.json') {
    try {
        const response = await fetch(configUrl);
        const cfg = await response.json();

        const tileSize = cfg.tile_size || 256;
        const maxZoom = cfg.max_zoom || 0;
        const width = cfg.width;
        const height = cfg.height;

        const map = L.map('map', {
            crs: L.CRS.Simple,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            center: [0, 0],
            zoom: maxZoom,
            zoomControl: false,
            attributionControl: false,
            maxBoundsViscosity: 1.0
        });

        const tileLayer = L.tileLayer('/assets/map/main/{z}/{x}/{y}.png', {
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
        window.dispatchEvent(new Event('mapReady'));

        return map;
    } catch (err) {
        console.error('Impossible de charger map_config.json', err);
        const mapDiv = document.getElementById('map');
        if (mapDiv) {
            mapDiv.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erreur de chargement de la carte</div>';
        }
        throw err;
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
