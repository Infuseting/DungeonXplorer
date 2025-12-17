<!-- Save/Load Modal -->
<div id="save-load-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="bg-gray-900 border-2 border-slate-600 rounded-lg shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-400">save</span>
                Gestion de la Partie
            </h2>
            <button id="saveload-close-btn" class="text-gray-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-700 bg-gray-800/50">
            <button class="flex-1 py-3 text-center transition-colors font-bold text-gray-400 hover:text-white border-b-2 border-transparent" data-tab="save">
                SAUVEGARDER
            </button>
            <button class="flex-1 py-3 text-center transition-colors font-bold text-gray-400 hover:text-white border-b-2 border-transparent" data-tab="load">
                CHARGER
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-gray-900">
            
            <!-- Save Tab -->
            <div id="tab-content-save" class="hidden space-y-6">
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <label class="block text-sm font-bold text-gray-400 mb-2">Nom de la sauvegarde</label>
                    <div class="flex gap-2">
                        <input type="text" id="save-name-input" 
                               class="flex-1 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-purple-500"
                               placeholder="Ex: Avant le boss...">
                        <button id="btn-create-save" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-sm">save</span>
                            Sauvegarder
                        </button>
                    </div>
                </div>

                <div class="text-center text-gray-500 text-sm italic">
                    <p>La sauvegarde enregistre votre progression actuelle :</p>
                    <p>Niveau, Inventaire, Quêtes, Position...</p>
                </div>
            </div>

            <!-- Load Tab -->
            <div id="tab-content-load" class="hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-white font-bold">Sauvegardes disponibles</h3>
                    <button id="btn-refresh-saves" class="text-sm text-purple-400 hover:text-purple-300 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">refresh</span> Actualiser
                    </button>
                </div>
                
                <div id="saves-list-container" class="space-y-2">
                    <!-- Saves will be injected here -->
                    <div class="text-center py-8 text-gray-500">Chargement...</div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('save-load-modal');
    const closeBtn = document.getElementById('saveload-close-btn');
    const tabs = modal.querySelectorAll('[data-tab]');
    const tabContents = {
        save: document.getElementById('tab-content-save'),
        load: document.getElementById('tab-content-load')
    };
    const saveInput = document.getElementById('save-name-input');
    const createSaveBtn = document.getElementById('btn-create-save');
    const refreshBtn = document.getElementById('btn-refresh-saves');
    const savesContainer = document.getElementById('saves-list-container');
    
        window.SaveSystem = {
        open(mode = 'save') {
            modal.classList.remove('hidden');
            this.switchTab(mode);
            if (mode === 'save') {
                saveInput.value = "Save " + new Date().toLocaleString('fr-FR');
                saveInput.focus();
            } else {
                this.loadSaves();
            }
        },

        close() {
            modal.classList.add('hidden');
        },

        switchTab(tabName) {
                        tabs.forEach(t => {
                if(t.dataset.tab === tabName) {
                    t.classList.add('text-purple-400', 'border-purple-500');
                    t.classList.remove('text-gray-400', 'border-transparent');
                } else {
                    t.classList.remove('text-purple-400', 'border-purple-500');
                    t.classList.add('text-gray-400', 'border-transparent');
                }
            });

                        Object.values(tabContents).forEach(el => el.classList.add('hidden'));
            tabContents[tabName].classList.remove('hidden');

            if(tabName === 'load') this.loadSaves();
        },

        async loadSaves() {
            savesContainer.innerHTML = '<div class="text-center py-8 text-gray-500 flex justify-center"><span class="material-symbols-outlined animate-spin">refresh</span></div>';
            
            try {
                const response = await fetch('/game/saves', { method: 'POST' });
                const data = await response.json();

                if (data.success && data.saves.length > 0) {
                    savesContainer.innerHTML = data.saves.map(save => `
                        <div class="bg-gray-800 p-3 rounded border border-gray-700 hover:border-gray-500 transition-colors flex justify-between items-center group">
                            <div>
                                <div class="font-bold text-white">${save.save_name}</div>
                                <div class="text-xs text-gray-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                                    ${new Date(save.created_at).toLocaleString('fr-FR')}
                                </div>
                            </div>
                            <button onclick="SaveSystem.loadGame(${save.id})" class="px-3 py-1 bg-gray-700 hover:bg-green-600 text-gray-200 hover:text-white rounded text-sm transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">restore</span>
                                Charger
                            </button>
                        </div>
                    `).join('');
                } else {
                    savesContainer.innerHTML = '<div class="text-center py-8 text-gray-500 italic">Aucune sauvegarde trouvée.</div>';
                }
            } catch (e) {
                console.error(e);
                savesContainer.innerHTML = '<div class="text-center py-4 text-red-400">Erreur de chargement.</div>';
            }
        },

        async createSave() {
            const name = saveInput.value.trim() || ("Save " + new Date().toLocaleString());
            createSaveBtn.disabled = true;
            createSaveBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">refresh</span> En cours...';

            try {
                const formData = new FormData();
                formData.append('name', name);
                
                const response = await fetch('/game/save', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                const { showToast } = await import('/js/modules/toast.js');
                
                if (data.success) {
                    showToast(data.message, 'success');
                    this.close();
                } else {
                    showToast(data.message, 'error');
                }
            } catch (e) {
                console.error(e);
            } finally {
                createSaveBtn.disabled = false;
                createSaveBtn.innerHTML = '<span class="material-symbols-outlined text-sm">save</span> Sauvegarder';
            }
        },

        async loadGame(id) {
            if(!confirm("Charger cette sauvegarde ? Toute progression non sauvegardée sera perdue.")) return;

            const { showToast } = await import('/js/modules/toast.js');
            const { playSound } = await import('/js/modules/soundManager.js');             
                                                            
            showToast('Chargement en cours...', 'info');

            try {
                const formData = new FormData();
                formData.append('save_id', id);

                const response = await fetch('/game/load', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('Erreur: ' + data.message, 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur technique', 'error');
            }
        }
    };

        closeBtn.addEventListener('click', () => SaveSystem.close());
    tabs.forEach(t => t.addEventListener('click', () => SaveSystem.switchTab(t.dataset.tab)));
    createSaveBtn.addEventListener('click', () => SaveSystem.createSave());
    refreshBtn.addEventListener('click', () => SaveSystem.loadSaves());

        document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            SaveSystem.close();
        }
    });
});
</script>
