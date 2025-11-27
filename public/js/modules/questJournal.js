/**
 * Quest Journal Module
 * Handles display and interaction with the player's quest log
 */

let questLog = [];
let currentFilter = 'all'; // 'all', 'active', 'completed'

/**
 * Open Quest Journal Modal
 */
export function openQuestJournal() {
    const modal = document.getElementById('quest-journal-modal');
    if (!modal) return;

    modal.style.display = 'flex';
    modal.classList.add('active');

    // Fetch quest log
    fetch('/game/quest/log')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                questLog = data.log;
                renderQuestList();
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
        container.innerHTML = '<div class="empty-state">Aucune quête trouvée</div>';
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

// Initialize event listeners when module loads
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('quest-search');
    if (searchInput) {
        searchInput.addEventListener('input', handleQuestSearch);
    }

    document.querySelectorAll('.quest-filter-tab').forEach(tab => {
        tab.addEventListener('click', () => setQuestFilter(tab.dataset.filter));
    });

    const closeBtn = document.getElementById('close-quest-journal');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeQuestJournal);
    }
});
