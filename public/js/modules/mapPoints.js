/**
 * Map Points Module
 * Handles map point markers and interactions
 */

import { showToast } from './toast.js';
import { openSubMap } from './subMap.js';
import { openNPCModal } from './npcModal.js';

/**
 * Get color for point type
 */
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

/**
 * Get label for point type
 */
function getTypeLabel(type) {
    const labels = {
        'story': 'Histoire',
        'place': 'Lieu',
        'dungeon': 'Donjon',
        'npc': 'PNJ',
        'quest': 'Quête'
    };
    return labels[type] || type;
}

/**
 * Show point details in the side panel
 */
export function showPointDetails(point) {
    console.log('Showing details for point:', point);
    if (point.type === 'place' && point.sub_map_id) {
        console.log('Opening sub-map:', point.sub_map_id);
        openSubMap(point.sub_map_id, point.name);
        return;
    }

    // Check if this is an NPC point
    if (point.type === 'npc' && point.target_id) {
        console.log('Opening NPC modal:', point.target_id);
        openNPCModal(point.target_id);
        return;
    }

    const pointPanel = document.getElementById('point-panel');

    // Update panel content
    document.getElementById('point-title').textContent = point.name;
    document.getElementById('point-description').textContent = point.description || 'Aucune description disponible.';
    document.getElementById('point-coords').textContent = `Lat: ${parseFloat(point.y).toFixed(6)}, Lng: ${parseFloat(point.x).toFixed(6)}`;

    // Update type badge
    const typeBadge = document.getElementById('point-type');
    const typeColor = getTypeColor(point.type);
    typeBadge.textContent = getTypeLabel(point.type);
    typeBadge.style.backgroundColor = typeColor + '33';
    typeBadge.style.color = typeColor;

    // Add action button based on type
    const actionsDiv = document.getElementById('point-actions');
    actionsDiv.innerHTML = '';

    const actionBtn = document.createElement('button');
    actionBtn.className = 'w-full px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-semibold transition-colors';

    switch (point.type) {
        case 'story':
            actionBtn.textContent = '📖 Lire l\'histoire';
            break;
        case 'place':
            actionBtn.textContent = '🚪 Entrer';
            break;
        case 'dungeon':
            actionBtn.textContent = '⚔️ Entrer dans le donjon';
            break;
        case 'npc':
            actionBtn.textContent = '💬 Parler';
            break;
        case 'quest':
            actionBtn.textContent = '📜 Accepter la quête';
            break;
    }

    actionBtn.addEventListener('click', () => {
        showToast(`Fonctionnalité "${getTypeLabel(point.type)}" à venir !`, 'info');
    });

    actionsDiv.appendChild(actionBtn);

    // Show panel
    pointPanel.classList.remove('translate-x-full');
}

/**
 * Initialize map points on the map
 */
export function initMapPoints(map, points) {
    console.log('Adding markers for', points.length, 'points');

    // Add markers for each point
    points.forEach((point, index) => {
        console.log(`Adding marker ${index + 1}:`, point);
        const marker = L.circleMarker([parseFloat(point.y), parseFloat(point.x)], {
            radius: 8,
            fillColor: getTypeColor(point.type),
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);

        console.log(`Marker ${index + 1} added at`, [parseFloat(point.y), parseFloat(point.x)]);

        // Click handler
        marker.on('click', () => {
            console.log('Marker clicked:', point.name);
            showPointDetails(point);
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

    console.log('All markers added successfully');
}

/**
 * Initialize panel controls
 */
export function initPanelControls() {
    const pointPanel = document.getElementById('point-panel');
    const closePanelBtn = document.getElementById('close-point-panel');

    closePanelBtn.addEventListener('click', () => {
        pointPanel.classList.add('translate-x-full');
    });
}
