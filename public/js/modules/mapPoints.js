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
export function showPointDetails(point, map = null) {
    console.log('Showing details for point:', point);
    
    // Check if this is an NPC point - open directly without panel
    if (point.type === 'npc' && point.target_id) {
        console.log('Opening NPC modal:', point.target_id);
        openNPCModal(point.target_id);
        return;
    }
    
    // Zoom to the point on the map
    if (map) {
        const pointLatLng = L.latLng(parseFloat(point.y), parseFloat(point.x));
        map.flyTo(pointLatLng, map.getMaxZoom() - 1, {
            duration: 1.5,
            easeLinearity: 0.25
        });
    }

    const pointPanel = document.getElementById('point-panel');
    const extraDetails = document.getElementById('point-extra-details');

    // Reset extra details
    if (extraDetails) {
        extraDetails.innerHTML = '';
        extraDetails.classList.add('hidden');
    }

    // Update panel content based on type
    if (point.story_id) {
        document.getElementById('point-title').textContent = point.story_name || point.name;
        document.getElementById('point-description').textContent = point.story_description || point.description || 'Aucune description disponible.';

        if (extraDetails) {
            extraDetails.classList.remove('hidden');
            extraDetails.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-900/50 rounded-lg p-3 text-center border border-gray-700">
                        <div class="text-xs text-gray-400 uppercase mb-1">Niveau Requis</div>
                        <div class="text-xl font-bold text-violet-400">${point.story_min_level || 1}</div>
                    </div>
                    <div class="bg-gray-900/50 rounded-lg p-3 text-center border border-gray-700">
                        <div class="text-xs text-gray-400 uppercase mb-1">Difficulté</div>
                        <div class="text-xl font-bold text-red-400">${point.story_difficulty || 1}/5</div>
                    </div>
                </div>
            `;
        }
    } else {
        document.getElementById('point-title').textContent = point.name;
        document.getElementById('point-description').textContent = point.description || 'Aucune description disponible.';
    }

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
        case 'dungeon':
            actionBtn.textContent = '⚔️ Entrer dans le donjon';
            if (point.story_id) {
                actionBtn.addEventListener('click', () => {
                    window.location.href = `/story/enter/${point.story_id}`;
                });
            } else {
                actionBtn.disabled = true;
                actionBtn.textContent = '⛔ Donjon non configuré';
                actionBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            break;
        case 'place':
            if (point.sub_map_id) {
                actionBtn.textContent = '🚪 Entrer dans la ville';
                actionBtn.addEventListener('click', () => {
                    console.log('Opening sub-map:', point.sub_map_id);
                    openSubMap(point.sub_map_id, point.name);
                });
            } else {
                actionBtn.textContent = '🚪 Entrer';
                actionBtn.addEventListener('click', () => {
                    showToast(`Fonctionnalité "${getTypeLabel(point.type)}" à venir !`, 'info');
                });
            }
            break;
        case 'npc':
            actionBtn.textContent = '💬 Parler';
            actionBtn.addEventListener('click', () => {
                showToast(`Ce PNJ n'est pas configuré`, 'error');
            });
            break;
        case 'quest':
            actionBtn.textContent = '📜 Accepter la quête';
            actionBtn.addEventListener('click', () => {
                showToast(`Fonctionnalité "${getTypeLabel(point.type)}" à venir !`, 'info');
            });
            break;
    }

    actionsDiv.appendChild(actionBtn);

    // Show panel
    pointPanel.classList.remove('translate-x-full');
}

/**
 * Initialize map points on the map
 */
export function initMapPoints(map, points) {
    console.log('Adding markers for', points.length, 'points');

    const markers = [];

    // Add markers for each point
    points.forEach((point, index) => {
        console.log(`Adding marker ${index + 1}:`, point);

        let marker;

        // If point has available quest, show exclamation mark
        if (point.has_quest) {
            const icon = L.divIcon({
                className: 'quest-giver-icon',
                html: `<div style="
                    background-color: #fbbf24; 
                    width: 24px; 
                    height: 24px; 
                    border-radius: 50%; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    border: 2px solid white;
                    box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
                    color: #fff;
                    font-weight: bold;
                    font-size: 16px;">!</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            marker = L.marker([parseFloat(point.y), parseFloat(point.x)], {
                icon: icon
            }).addTo(map);
        } else if (point.icon) {
            // Use custom SVG icon
            const typeColor = getTypeColor(point.type);
            const icon = L.divIcon({
                className: 'custom-map-icon',
                html: `<div class="map-icon-container" style="
                    width: 32px; 
                    height: 32px; 
                    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
                    color: ${typeColor};
                ">
                    <img src="/assets/map/icons/${point.icon}" 
                         alt="${point.name}" 
                         class="map-icon-svg"
                         style="
                            width: 100%; 
                            height: 100%; 
                            object-fit: contain;
                            filter: drop-shadow(0 0 2px rgba(255,255,255,0.5));
                         ">
                </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            marker = L.marker([parseFloat(point.y), parseFloat(point.x)], {
                icon: icon
            }).addTo(map);
        } else {
            marker = L.circleMarker([parseFloat(point.y), parseFloat(point.x)], {
                radius: 8,
                fillColor: getTypeColor(point.type),
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);
        }

        console.log(`Marker ${index + 1} added at`, [parseFloat(point.y), parseFloat(point.x)]);

        // Click handler
        marker.on('click', () => {
            console.log('Marker clicked:', point.name);
            showPointDetails(point, map);
        });

        // Hover effect (only for circle markers)
        if (!point.has_quest && !point.icon) {
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
        }

        markers.push(marker);
    });

    console.log('All markers added successfully');
    return markers;
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
