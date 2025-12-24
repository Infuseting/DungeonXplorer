/**
 * Quest Journal Module
 * Handles display and interaction with the player's quest log
 */

import { playSound } from './soundManager.js';

let questLog = [];
let dailyQuests = [];
let currentFilter = 'all'; // 'all', 'active', 'completed'
let currentTab = 'story'; // 'story', 'daily'

/**
 * Open Quest Journal Modal
 */
export function openQuestJournal() {
    const modal = document.getElementById('quest-journal-modal');
    if (!modal) return;

    modal.style.display = 'flex';
    modal.classList.add('active');
    playSound('open');

    // Fetch quest log
    fetch('/game/quest/log')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                questLog = data.log;
                dailyQuests = data.daily_quests || [];
                renderQuestList();
                renderDailyQuests();
            }
        })
        .catch(error => {
            console.error('Error loading quest log:', error);
            if (window.showToast) window.showToast('Erreur lors du chargement du journal', 'error');
        });
}

/**
 * Close Quest Journal Modal
 */
export function closeQuestJournal() {
    const modal = document.getElementById('quest-journal-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('active');
        playSound('close');
    }
}

/**
 * Render the list of quests based on filters
 */
function renderQuestList() {
    const container = document.getElementById('quest-list');
    const searchInput = document.getElementById('quest-search');
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

    if (!container) return;

    container.innerHTML = '';

    // Filter quests
    const filteredQuests = questLog.filter(quest => {
        // Status filter
        if (currentFilter === 'active' && quest.status !== 'ACTIVE') return false;
        if (currentFilter === 'completed' && quest.status !== 'COMPLETED') return false;

        // Search filter
        if (searchTerm && !quest.name.toLowerCase().includes(searchTerm)) return false;

        return true;
    });

    if (filteredQuests.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-300 py-8 text-lg">Aucune quête trouvée</div>';
        return;
    }

    // Render quests
    filteredQuests.forEach(quest => {
        const questEl = document.createElement('div');
        questEl.className = `quest-item ${quest.status.toLowerCase()}`;

        // Header (always visible)
        const header = document.createElement('div');
        header.className = 'quest-header';
        header.onclick = () => toggleQuestDetails(quest.id);

        const statusIcon = quest.status === 'COMPLETED' ? '✅' : '📜';

        header.innerHTML = `
            <div class="quest-title-row">
                <span class="quest-icon">${statusIcon}</span>
                <span class="quest-name">${quest.name}</span>
                <span class="quest-status-badge ${quest.status.toLowerCase()}">
                    ${quest.status === 'COMPLETED' ? 'Terminée' : 'En cours'}
                </span>
            </div>
            <div class="quest-description">${quest.description}</div>
        `;

        // Details (collapsible)
        const details = document.createElement('div');
        details.className = 'quest-details';
        details.id = `quest-details-${quest.id}`;
        details.style.display = 'none';

        // Render stages
        if (quest.stages && quest.stages.length > 0) {
            const stagesList = document.createElement('div');
            stagesList.className = 'quest-stages';

            quest.stages.forEach(stage => {
                const stageEl = document.createElement('div');
                stageEl.className = `quest-stage ${stage.status.toLowerCase()}`;

                let stageContent = `
                    <div class="stage-header">
                        <span class="stage-name">${stage.name}</span>
                        ${stage.status === 'COMPLETED' ? '<span class="stage-check">✓</span>' : ''}
                    </div>
                `;

                // Render objectives for active/completed stages
                if (stage.objectives && stage.objectives.length > 0) {
                    stageContent += '<ul class="quest-objectives">';
                    stage.objectives.forEach(obj => {
                        const isObjComplete = obj.is_completed == 1;
                        const progressText = obj.count_required > 1
                            ? `(${obj.count_current}/${obj.count_required})`
                            : '';

                        stageContent += `
                            <li class="objective ${isObjComplete ? 'completed' : ''}">
                                <span class="objective-icon">${isObjComplete ? '✓' : '○'}</span>
                                <span class="objective-text">
                                    ${obj.description} ${progressText}
                                </span>
                            </li>
                        `;
                    });
                    stageContent += '</ul>';
                }

                stageEl.innerHTML = stageContent;
                stagesList.appendChild(stageEl);
            });

            details.appendChild(stagesList);
        }

        questEl.appendChild(header);
        questEl.appendChild(details);
        container.appendChild(questEl);
    });
}

/**
 * Toggle quest details visibility
 */
function toggleQuestDetails(questId) {
    const details = document.getElementById(`quest-details-${questId}`);
    if (details) {
        const isHidden = details.style.display === 'none';
        details.style.display = isHidden ? 'block' : 'none';

        // Toggle active class on parent for styling
        details.parentElement.classList.toggle('expanded', isHidden);
    }
}

/**
 * Set current filter
 */
export function setQuestFilter(filter) {
    currentFilter = filter;

    // Update tabs UI
    document.querySelectorAll('.quest-filter-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.filter === filter);
    });

    renderQuestList();
}

/**
 * Handle search input
 */
export function handleQuestSearch() {
    renderQuestList();
}

/**
 * Switch between Story and Daily quest tabs
 */
export function switchQuestTab(tab) {
    currentTab = tab;
    
    // Update tab UI
    document.querySelectorAll('.quest-type-tab').forEach(t => {
        t.classList.toggle('active', t.dataset.tab === tab);
    });
    
    // Show/hide sections
    const storySection = document.getElementById('story-quests-section');
    const dailySection = document.getElementById('daily-quests-section');
    
    if (tab === 'story') {
        storySection.classList.remove('hidden');
        storySection.classList.add('flex');
        dailySection.classList.add('hidden');
        dailySection.classList.remove('flex');
    } else {
        storySection.classList.add('hidden');
        storySection.classList.remove('flex');
        dailySection.classList.remove('hidden');
        dailySection.classList.add('flex');
    }
}

/**
 * Render daily quests list
 */
function renderDailyQuests() {
    const container = document.getElementById('daily-quest-list');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (!dailyQuests || dailyQuests.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 py-8">Aucune quête quotidienne disponible</div>';
        return;
    }
    
    dailyQuests.forEach(quest => {
        const questEl = document.createElement('div');
        const statusClass = quest.status.toLowerCase();
        questEl.className = `daily-quest-item ${statusClass}`;
        
        const progressPercent = Math.min(100, (quest.current_progress / quest.objective_count) * 100);
        const isComplete = quest.status === 'COMPLETED';
        const isClaimed = quest.status === 'CLAIMED';
        
        // Get objective type icon
        const typeIcons = {
            'KILL_MONSTERS': '⚔️',
            'COLLECT_GOLD': '🪙',
            'COMPLETE_DUNGEON': '🏰',
            'VISIT_LOCATIONS': '🗺️',
            'USE_ITEMS': '🧪'
        };
        const typeIcon = typeIcons[quest.objective_type] || '📋';
        
        questEl.innerHTML = `
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">${typeIcon}</span>
                    <div>
                        <h4 class="font-bold text-white ${isClaimed ? 'line-through opacity-50' : ''}">${quest.name}</h4>
                        <p class="text-sm text-gray-400">${quest.description}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-yellow-400 font-bold">🪙 ${quest.gold_reward}</div>
                    ${isClaimed ? '<span class="text-xs text-green-400">✓ Réclamé</span>' : ''}
                </div>
            </div>
            
            <div class="flex items-center gap-4 mt-3">
                <div class="flex-1">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Progression</span>
                        <span>${quest.current_progress}/${quest.objective_count}</span>
                    </div>
                    <div class="daily-quest-progress">
                        <div class="daily-quest-progress-bar ${isComplete || isClaimed ? 'complete' : ''}" style="width: ${progressPercent}%"></div>
                    </div>
                </div>
                
                ${isComplete && !isClaimed ? `
                    <button class="claim-reward-btn" onclick="window.claimDailyQuestReward(${quest.id})">
                        Réclamer
                    </button>
                ` : ''}
            </div>
        `;
        
        container.appendChild(questEl);
    });
}

/**
 * Claim daily quest reward
 */
window.claimDailyQuestReward = async function(playerDailyQuestId) {
    try {
        const response = await fetch('/game/quest/daily/claim', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ player_daily_quest_id: playerDailyQuestId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            playSound('reward');
            if (window.showToast) window.showToast(data.message, 'success');
            
            // Update the quest in the local array
            const quest = dailyQuests.find(q => q.id === playerDailyQuestId);
            if (quest) {
                quest.status = 'CLAIMED';
            }
            
            // Re-render
            renderDailyQuests();
            
            // Dispatch event for gold update
            window.dispatchEvent(new CustomEvent('goldUpdated'));
        } else {
            if (window.showToast) window.showToast(data.message || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Error claiming reward:', error);
        if (window.showToast) window.showToast('Erreur lors de la réclamation', 'error');
    }
};

// Initialize event listeners when module loads
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('quest-search');
    if (searchInput) {
        searchInput.addEventListener('input', handleQuestSearch);
    }

    document.querySelectorAll('.quest-filter-tab').forEach(tab => {
        tab.addEventListener('click', () => setQuestFilter(tab.dataset.filter));
    });
    
    // Tab switching
    document.querySelectorAll('.quest-type-tab').forEach(tab => {
        tab.addEventListener('click', () => switchQuestTab(tab.dataset.tab));
    });

    const closeBtn = document.getElementById('close-quest-journal');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeQuestJournal);
    }

    // Close on backdrop click
    const backdrop = document.getElementById('quest-backdrop');
    if (backdrop) {
        backdrop.addEventListener('click', closeQuestJournal);
    }
});
