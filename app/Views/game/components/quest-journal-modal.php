<!-- Quest Journal Modal -->
<div id="quest-journal-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="quest-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative z-10 w-full h-full flex items-center justify-center p-4 lg:p-8 pointer-events-none">
        <div class="bg-gray-800/90 border border-gray-600 rounded-2xl shadow-2xl flex flex-col w-full max-w-4xl h-[85vh] pointer-events-auto transform transition-all scale-100 overflow-hidden">
        
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-600">
                <h2 class="text-xl lg:text-2xl font-bold text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Journal de Quêtes
                </h2>
                <button id="close-quest-journal" class="text-gray-400 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-600 bg-gray-900/30">
                <button class="quest-type-tab active flex-1 py-3 text-center font-medium transition-colors" data-tab="story">
                    📖 Quêtes d'Histoire
                </button>
                <button class="quest-type-tab flex-1 py-3 text-center font-medium transition-colors" data-tab="daily">
                    ⏰ Quêtes Quotidiennes
                </button>
            </div>
        
            <!-- Story Quests Section -->
            <div id="story-quests-section" class="flex-1 flex flex-col overflow-hidden">
                <!-- Controls -->
                <div class="p-4 bg-gray-900/30 border-b border-gray-700 flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                        <input type="text" id="quest-search" placeholder="Rechercher une quête..." 
                               class="w-full bg-gray-900/50 border border-gray-700 rounded-lg py-2 pl-10 pr-4 text-gray-200 focus:outline-none focus:border-violet-500 transition-colors">
                    </div>
                
                    <!-- Filters -->
                    <div class="flex bg-gray-900/50 rounded-lg p-1 border border-gray-700">
                        <button class="quest-filter-tab active px-4 py-1.5 rounded-md text-sm font-medium transition-colors hover:text-white text-gray-400" data-filter="all">
                            Toutes
                        </button>
                        <button class="quest-filter-tab px-4 py-1.5 rounded-md text-sm font-medium transition-colors hover:text-white text-gray-400" data-filter="active">
                            En cours
                        </button>
                        <button class="quest-filter-tab px-4 py-1.5 rounded-md text-sm font-medium transition-colors hover:text-white text-gray-400" data-filter="completed">
                            Terminées
                        </button>
                    </div>
                </div>
            
                <!-- Quest List -->
                <div id="quest-list" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                    <!-- Quests will be injected here -->
                </div>
            </div>
        
            <!-- Daily Quests Section (Hidden by default) -->
            <div id="daily-quests-section" class="flex-1 flex-col overflow-hidden hidden">
                <!-- Daily Quest Header Info -->
                <div class="p-4 bg-gray-900/30 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Quêtes du Jour
                            </h3>
                            <p class="text-sm text-gray-400">3 quêtes aléatoires disponibles chaque jour</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-yellow-400" id="daily-quest-reward-total">🪙 15 pièces max</div>
                            <div class="text-sm text-gray-400" id="daily-quest-timer">Renouvellement à minuit</div>
                        </div>
                    </div>
                </div>
            
                <!-- Daily Quest List -->
                <div id="daily-quest-list" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                    <!-- Daily quests will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Quest Item Styles */
.quest-item {
    background: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    border-radius: 0.75rem;
    overflow: hidden;
    transition: all 0.2s;
}

.quest-item:hover {
    border-color: #8b5cf6;
    background: rgba(17, 24, 39, 0.9);
}

.quest-item.completed {
    border-color: #059669;
    background: rgba(6, 78, 59, 0.2);
}

.quest-header {
    padding: 1rem;
    cursor: pointer;
}

.quest-title-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.quest-icon {
    font-size: 1.25rem;
}

.quest-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #e5e7eb;
    flex: 1;
}

.quest-status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-weight: 500;
    text-transform: uppercase;
}

.quest-status-badge.active {
    background: rgba(139, 92, 246, 0.2);
    color: #a78bfa;
    border: 1px solid rgba(139, 92, 246, 0.4);
}

.quest-status-badge.completed {
    background: rgba(5, 150, 105, 0.2);
    color: #34d399;
    border: 1px solid rgba(5, 150, 105, 0.4);
}

.quest-description {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-left: 2rem;
}

/* Quest Details */
.quest-details {
    background: rgba(17, 24, 39, 0.5);
    border-top: 1px solid #374151;
    padding: 1rem;
    padding-left: 3rem;
}

.quest-stage {
    margin-bottom: 1rem;
    padding-left: 1rem;
    border-left: 2px solid #4b5563;
}

.quest-stage.active {
    border-left-color: #8b5cf6;
}

.quest-stage.completed {
    border-left-color: #10b981;
    opacity: 0.7;
}

.stage-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.stage-name {
    font-weight: 600;
    color: #d1d5db;
}

.stage-check {
    color: #10b981;
    font-weight: bold;
}

.quest-objectives {
    list-style: none;
    padding: 0;
    margin: 0;
}

.objective {
    display: flex;
    align-items: start;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
    color: #9ca3af;
}

.objective.completed {
    color: #10b981;
    text-decoration: line-through;
}

.objective-icon {
    font-weight: bold;
    min-width: 1.25rem;
}

/* Active tab style */
.quest-filter-tab.active {
    background: #7c3aed;
    color: white;
}

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(17, 24, 39, 0.5);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #8b5cf6;
}

/* Tab Navigation Styles */
.quest-type-tab {
    color: #9ca3af;
    border-bottom: 2px solid transparent;
}

.quest-type-tab:hover {
    color: #a78bfa;
    background: rgba(139, 92, 246, 0.1);
}

.quest-type-tab.active {
    color: #a78bfa;
    border-bottom-color: #8b5cf6;
    background: rgba(139, 92, 246, 0.1);
}

/* Daily Quest Styles */
.daily-quest-item {
    background: rgba(17, 24, 39, 0.8);
    border: 2px solid #374151;
    border-radius: 0.75rem;
    padding: 1rem;
    transition: all 0.3s;
}

.daily-quest-item:hover {
    border-color: #8b5cf6;
    background: rgba(17, 24, 39, 0.9);
}

.daily-quest-item.completed {
    border-color: #059669;
    background: rgba(6, 78, 59, 0.2);
}

.daily-quest-item.claimed {
    border-color: #374151;
    background: rgba(17, 24, 39, 0.4);
    opacity: 0.7;
}

.daily-quest-progress {
    height: 8px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.daily-quest-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #7c3aed, #8b5cf6);
    border-radius: 4px;
    transition: width 0.5s ease;
}

.daily-quest-progress-bar.complete {
    background: linear-gradient(90deg, #059669, #10b981);
}

.claim-reward-btn {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s;
    animation: pulse-violet 2s infinite;
}

.claim-reward-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
}

@keyframes pulse-violet {
    0%, 100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(139, 92, 246, 0); }
}
</style>
