/**
 * Map Module
 * Handles Leaflet map initialization and configuration
 */

import { changeMusicCategory } from './soundManager.js';

let gameMap = null;
let gameMapMaxZoom = null;
let currentMarkers = [];

// Navigation system - also expose on window for initialization
let mapHistory = [];
let currentMapData = null;

// Make currentMapData accessible globally for initialization
Object.defineProperty(window, 'currentMapData', {
    get: () => currentMapData,
    set: (value) => { currentMapData = value; }
});

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

        // Change music category if specified in config
        if (cfg.musicCategory) {
            console.log(`[Map] Changing music to category: ${cfg.musicCategory}`);
            changeMusicCategory(cfg.musicCategory);
        }

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
        currentMarkers.forEach(markerData => {
            // markerData is { marker: L.marker, point: point }
            if (markerData && markerData.marker && markerData.marker.remove) {
                markerData.marker.remove();
            } else if (markerData && markerData.remove) {
                // Fallback if it's a direct marker
                markerData.remove();
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

/**
 * Load a map (main or submap) with navigation history
 * @param {number} mapId - ID of the map
 * @param {string} configUrl - URL to map_config.json
 * @param {string} tilesPath - Path to tiles directory
 * @param {string} mapName - Name of the map (for display)
 */
export async function loadMap(mapId, configUrl, tilesPath, mapName = null) {
    console.log(`[Map] Loading map ${mapId}: ${mapName || 'Unnamed'}`);
    console.log(`[Map] Current map data before:`, currentMapData);
    console.log(`[Map] History before:`, mapHistory);

    // Save current map to history before loading new one
    if (currentMapData) {
        mapHistory.push({ ...currentMapData });
        console.log(`[Map] Saved to history. Stack size: ${mapHistory.length}`);
    }

    // Load the new map
    await initMap(configUrl, tilesPath, 'map');
    await loadMapPoints(mapId, window.characterId);

    // Store current map data
    currentMapData = {
        mapId,
        configUrl,
        tilesPath,
        mapName
    };

    console.log(`[Map] Current map data after:`, currentMapData);
    console.log(`[Map] History after:`, mapHistory);

    // Update back button visibility
    updateBackButton();
}

/**
 * Go back to the previous map in history
 */
export async function goBackToParentMap() {
    if (mapHistory.length === 0) {
        console.warn('[Map] No map in history to go back to');
        return;
    }

    // Get previous map from history
    const previousMap = mapHistory.pop();
    console.log(`[Map] Going back to: ${previousMap.mapName || previousMap.mapId}`);

    // Load previous map WITHOUT adding to history
    await initMap(previousMap.configUrl, previousMap.tilesPath, 'map');
    await loadMapPoints(previousMap.mapId, window.characterId);

    // Update current map data
    currentMapData = previousMap;

    // Update back button visibility
    updateBackButton();
}

/**
 * Update back button visibility based on history
 */
function updateBackButton() {
    const backButton = document.getElementById('back-to-parent-map');
    if (!backButton) {
        console.warn('[Map] Back button not found in DOM');
        return;
    }

    console.log(`[Map] Updating back button. History length: ${mapHistory.length}`);

    if (mapHistory.length > 0) {
        backButton.classList.remove('hidden');
        console.log('[Map] Back button shown');
        // Update button text with parent map name if available
        const parentMap = mapHistory[mapHistory.length - 1];
        if (parentMap.mapName) {
            backButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            `;
        }
    } else {
        backButton.classList.add('hidden');
        console.log('[Map] Back button hidden');
    }
}

/**
 * Get current map ID
 * @returns {number|null} Current map ID or null
 */
export function getCurrentMapId() {
    return currentMapData ? currentMapData.mapId : null;
}

/**
 * Clear navigation history (useful for logout/reset)
 */
export function clearMapHistory() {
    mapHistory = [];
    currentMapData = null;
    updateBackButton();
}

// Make loadMapPoints available globally
window.loadMapPoints = loadMapPoints;
window.getCurrentMapId = getCurrentMapId;
