<!-- Quest Journal Modal -->
<div id="quest-journal-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="relative w-full max-w-4xl h-[80vh] bg-gray-900 border-2 border-amber-600 rounded-lg shadow-2xl flex flex-col overflow-hidden">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 bg-gray-800 border-b border-gray-700">
            <h2 class="text-2xl font-bold text-amber-500 flex items-center gap-2">
                <span>📜</span> Journal de Quêtes
            </h2>
            <button id="close-quest-journal" class="text-gray-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Controls -->
        <div class="p-4 bg-gray-800/50 border-b border-gray-700 flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                <input type="text" id="quest-search" placeholder="Rechercher une quête..." 
                       class="w-full bg-gray-900 border border-gray-700 rounded-md py-2 pl-10 pr-4 text-gray-200 focus:outline-none focus:border-amber-500">
            </div>
            
            <!-- Filters -->
            <div class="flex bg-gray-900 rounded-md p-1 border border-gray-700">
                <button class="quest-filter-tab active px-4 py-1.5 rounded text-sm font-medium transition-colors hover:text-white text-gray-400" data-filter="all">
                    Toutes
                </button>
                <button class="quest-filter-tab px-4 py-1.5 rounded text-sm font-medium transition-colors hover:text-white text-gray-400" data-filter="active">
                    En cours
                </button>
                <button class="quest-filter-tab px-4 py-1.5 rounded text-sm font-medium transition-colors hover:text-white text-gray-400" data-filter="completed">
                    Terminées
                </button>
            </div>
        </div>
        
        <!-- Quest List -->
        <div id="quest-list" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
            <!-- Quests will be injected here -->
        </div>
    </div>
</div>

<style>
/* Quest Item Styles */
.quest-item {
    background: rgba(31, 41, 55, 0.6);
    border: 1px solid #374151;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: all 0.2s;
}

.quest-item:hover {
    border-color: #4b5563;
    background: rgba(31, 41, 55, 0.8);
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
    background: rgba(217, 119, 6, 0.2);
    color: #fbbf24;
    border: 1px solid rgba(217, 119, 6, 0.4);
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
    background: rgba(17, 24, 39, 0.8);
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
    border-left-color: #f59e0b;
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
    background: #4b5563;
    color: white;
}

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(31, 41, 55, 0.5);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>
