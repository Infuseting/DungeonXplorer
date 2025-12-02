/**
 * Sub-Map Module
 * Handles loading submaps using the main map navigation system
 */

import { loadMap } from './map.js';
import { showToast } from './toast.js';

/**
 * Open sub-map by loading it in the main map container
 * @param {number} mapId - ID of the sub-map to load
 */
export async function openSubMap(mapId) {
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
            return;
        }

        // Extract base path from image_path (remove /map_config.json)
        const basePath = data.map.image_path.substring(0, data.map.image_path.lastIndexOf('/'));

        // Use the new loadMap function to load in main container
        await loadMap(
            data.map.id,
            data.map.image_path,
            basePath,
            data.map.name
        );

        console.log(`Sub-map loaded: ${data.map.name}`);

    } catch (error) {
        console.error('Error loading sub-map:', error);
        showToast('Erreur de chargement de la carte', 'error');
    }
}
