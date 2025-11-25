/**
 * Sub-Map Module
 * Handles loading and displaying sub-maps in a modal
 */

import { showToast } from './toast.js';

let subMapInstance = null;
let subMapMaxZoom = null;

/**
 * Open sub-map modal and load map data
 * @param {number} mapId - ID of the sub-map to load
 */
export async function openSubMap(mapId) {
    const modal = document.getElementById('submap-modal');
    const container = document.getElementById('submap-container');

    // Show modal
    modal.classList.remove('hidden');

    // Show loading state
    container.innerHTML = '<div class="flex items-center justify-center h-full text-white"><div class="text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div><p>Chargement de la carte...</p></div></div>';

    try {
        // Fetch sub-map data from API
        const response = await fetch('/game/submap/load', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ mapId: mapId })
        });

        const data = await response.json();

        if (!data.success) {
            showToast(data.message || 'Erreur de chargement de la carte', 'error');
            closeSubMap();
            return;
        }

        // Update modal title and description
        document.getElementById('submap-title').textContent = data.map.name;
        document.getElementById('submap-description').textContent = data.map.description || '';

        // Initialize sub-map
        await initSubMap(data.map, data.points);

    } catch (error) {
        console.error('Error loading sub-map:', error);
        showToast('Erreur de chargement de la carte', 'error');
        closeSubMap();
    }
}

/**
 * Initialize Leaflet map for sub-map
 * @param {Object} mapData - Map configuration data
 * @param {Array} points - Array of map points
 */
async function initSubMap(mapData, points) {
    const container = document.getElementById('submap-container');
    container.innerHTML = '';

    try {
        // Load map configuration
        const response = await fetch(mapData.image_path);
        const cfg = await response.json();

        const tileSize = cfg.tile_size || 256;
        const maxZoom = cfg.max_zoom || 0;
        const width = cfg.width;
        const height = cfg.height;

        // Create Leaflet map
        const map = L.map('submap-container', {
            crs: L.CRS.Simple,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            center: [0, 0],
            zoom: maxZoom,
            zoomControl: true,
            attributionControl: false,
            maxBoundsViscosity: 1.0
        });

        // Add tile layer
        const basePath = mapData.image_path.substring(0, mapData.image_path.lastIndexOf('/'));
        L.tileLayer(basePath + '/{z}/{x}/{y}.png', {
            tileSize: tileSize,
            minZoom: maxZoom - 3,
            maxZoom: maxZoom,
            noWrap: true,
            continuousWorld: false,
            attribution: ''
        }).addTo(map);

        // Set bounds
        const southWest = map.unproject([0, height], maxZoom);
        const northEast = map.unproject([width, 0], maxZoom);
        const bounds = new L.LatLngBounds(southWest, northEast);

        map.setMaxBounds(bounds);
        map.fitBounds(bounds);

        // Store map instance
        subMapInstance = map;
        subMapMaxZoom = maxZoom;

        // Add map points
        addSubMapPoints(map, points);

        console.log('Sub-map initialized successfully');

    } catch (error) {
        console.error('Error initializing sub-map:', error);
        container.innerHTML = '<div class="flex items-center justify-center h-full text-red-500"><p>Erreur de chargement de la carte</p></div>';
    }
}

/**
 * Add points to sub-map
 * @param {L.Map} map - Leaflet map instance
 * @param {Array} points - Array of point objects
 */
function addSubMapPoints(map, points) {
    const typeColors = {
        'story': '#6366f1',
        'place': '#22c55e',
        'dungeon': '#ef4444',
        'npc': '#fbbf24',
        'quest': '#a855f7'
    };

    points.forEach((point) => {
        const marker = L.circleMarker([parseFloat(point.y), parseFloat(point.x)], {
            radius: 8,
            fillColor: typeColors[point.type] || '#6366f1',
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);

        // Click handler
        marker.on('click', () => {
            showSubMapPointDetails(point);
        });

        // Hover effect
        marker.on('mouseover', function () {
            this.setStyle({
                radius: 10,
                weight: 3
            });
        });

        marker.on('mouseout', function () {
            this.setStyle({
                radius: 8,
                weight: 2
            });
        });
    });
}

/**
 * Show point details in sub-map panel
 * @param {Object} point - Point data
 */
function showSubMapPointDetails(point) {
    const panel = document.getElementById('submap-point-panel');
    const typeLabels = {
        'story': 'Histoire',
        'place': 'Lieu',
        'dungeon': 'Donjon',
        'npc': 'PNJ',
        'quest': 'Quête'
    };
    const typeColors = {
        'story': '#6366f1',
        'place': '#22c55e',
        'dungeon': '#ef4444',
        'npc': '#fbbf24',
        'quest': '#a855f7'
    };

    // Update panel content
    document.getElementById('submap-point-title').textContent = point.name;
    document.getElementById('submap-point-description').textContent = point.description || 'Aucune description disponible.';
    document.getElementById('submap-point-coords').textContent = `Lat: ${parseFloat(point.y).toFixed(6)}, Lng: ${parseFloat(point.x).toFixed(6)}`;

    // Update type badge
    const typeBadge = document.getElementById('submap-point-type');
    const typeColor = typeColors[point.type] || '#6366f1';
    typeBadge.textContent = typeLabels[point.type] || point.type;
    typeBadge.style.backgroundColor = typeColor + '33';
    typeBadge.style.color = typeColor;

    // Show panel
    panel.classList.remove('translate-x-full');
}

/**
 * Close sub-map modal
 */
export function closeSubMap() {
    const modal = document.getElementById('submap-modal');
    modal.classList.add('hidden');

    // Clean up map instance
    if (subMapInstance) {
        subMapInstance.remove();
        subMapInstance = null;
    }
}

/**
 * Initialize sub-map controls
 */
export function initSubMapControls() {
    const closeBtn = document.getElementById('close-submap');
    const backdrop = document.getElementById('submap-backdrop');
    const closePanelBtn = document.getElementById('close-submap-point-panel');
    const panel = document.getElementById('submap-point-panel');

    closeBtn.addEventListener('click', closeSubMap);
    backdrop.addEventListener('click', closeSubMap);

    closePanelBtn.addEventListener('click', () => {
        panel.classList.add('translate-x-full');
    });

    // ESC key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !document.getElementById('submap-modal').classList.contains('hidden')) {
            closeSubMap();
        }
    });
}
