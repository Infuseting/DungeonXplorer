<!-- Settings Modal -->
<div id="settings-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80">
    <div class="bg-gray-900 border-2 border-violet-600 rounded-lg p-6 w-full max-w-md shadow-2xl relative">
        <!-- Close Button -->
        <button id="settings-close-btn" class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl">&times;</button>
        
        <!-- Header -->
        <h2 class="text-2xl font-bold text-violet-400 mb-6">⚙️ Paramètres</h2>

        <!-- Tabs -->
        <div class="flex border-b border-gray-700 mb-6">
            <button id="tab-audio-btn" class="flex-1 py-2 text-center text-white border-b-2 border-violet-500 font-semibold focus:outline-none transition-colors">Audio</button>
            <button id="tab-profile-btn" class="flex-1 py-2 text-center text-gray-400 hover:text-white border-b-2 border-transparent font-semibold focus:outline-none transition-colors">Profil</button>
        </div>

        <!-- Audio Tab Content -->
        <div id="tab-audio-content" class="space-y-6">
            <div>
                <label class="block text-white font-semibold mb-2 flex items-center justify-between">
                    <span>🔊 Volume Global</span>
                    <span id="master-volume-value" class="text-violet-400">70%</span>
                </label>
                <input type="range" id="master-volume" min="0" max="100" value="70" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
            </div>
            <div>
                <label class="block text-white font-semibold mb-2 flex items-center justify-between">
                    <span>🎵 Volume Musique</span>
                    <span id="music-volume-value" class="text-violet-400">50%</span>
                </label>
                <input type="range" id="music-volume" min="0" max="100" value="50" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
            </div>
            <div>
                <label class="block text-white font-semibold mb-2 flex items-center justify-between">
                    <span>🔔 Volume Effets Sonores</span>
                    <span id="sfx-volume-value" class="text-violet-400">80%</span>
                </label>
                <input type="range" id="sfx-volume" min="0" max="100" value="80" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-700">
                <button id="settings-save-btn" class="w-full px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-semibold transition-colors">
                    Sauvegarder Audio
                </button>
            </div>
        </div>

        <!-- Profile Tab Content -->
        <div id="tab-profile-content" class="hidden space-y-6 max-h-[60vh] overflow-y-auto pr-2">
            
            <!-- Update Info -->
            <form id="form-update-profile" class="space-y-3 border-b border-gray-700 pb-4">
                <h3 class="text-lg font-semibold text-gray-300">Informations</h3>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nom d'utilisateur</label>
                    <input type="text" name="username" value="<?= $_SESSION['username'] ?? '' ?>" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white focus:border-violet-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded transition-colors text-sm">Mettre à jour le profil</button>
            </form>

            <!-- Update Email -->
            <form id="form-update-email" class="space-y-3 border-b border-gray-700 pb-4">
                <h3 class="text-lg font-semibold text-gray-300">Email</h3>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nouvel Email</label>
                    <input type="email" name="email" placeholder="votre@email.com" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white focus:border-violet-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded transition-colors text-sm">Mettre à jour l'email</button>
            </form>

            <!-- Update Password -->
            <form id="form-update-password" class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-300">Mot de passe</h3>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white focus:border-violet-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white focus:border-violet-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Confirmer</label>
                    <input type="password" name="confirm_password" class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-white focus:border-violet-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-red-900/50 hover:bg-red-800/50 border border-red-800 text-white py-2 rounded transition-colors text-sm">Changer le mot de passe</button>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const audioTabBtn = document.getElementById('tab-audio-btn');
        const profileTabBtn = document.getElementById('tab-profile-btn');
        const audioContent = document.getElementById('tab-audio-content');
        const profileContent = document.getElementById('tab-profile-content');

        // Tab Switching
        function switchTab(tab) {
            if (tab === 'audio') {
                audioContent.classList.remove('hidden');
                profileContent.classList.add('hidden');
                audioTabBtn.classList.add('text-white', 'border-violet-500');
                audioTabBtn.classList.remove('text-gray-400', 'border-transparent');
                profileTabBtn.classList.add('text-gray-400', 'border-transparent');
                profileTabBtn.classList.remove('text-white', 'border-violet-500');
            } else {
                audioContent.classList.add('hidden');
                profileContent.classList.remove('hidden');
                profileTabBtn.classList.add('text-white', 'border-violet-500');
                profileTabBtn.classList.remove('text-gray-400', 'border-transparent');
                audioTabBtn.classList.add('text-gray-400', 'border-transparent');
                audioTabBtn.classList.remove('text-white', 'border-violet-500');
            }
        }

        if(audioTabBtn) audioTabBtn.addEventListener('click', () => switchTab('audio'));
        if(profileTabBtn) profileTabBtn.addEventListener('click', () => switchTab('profile'));

        // Profile Forms Handling
        async function handleFormSubmit(formId, url) {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                
                // Import toast if available, or fallback
                let showToast = (msg, type) => alert(msg);
                try {
                    const module = await import('/js/modules/toast.js');
                    showToast = module.showToast;
                } catch(e) {}

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast(data.message, 'success');
                        if (formId === 'form-update-password') form.reset();
                        
                        // If username changed, update UI
                        if (formId === 'form-update-profile' && formData.get('username')) {
                            const menuBtn = document.getElementById('user-menu-button');
                            if(menuBtn) menuBtn.innerText = formData.get('username').charAt(0).toUpperCase();
                        }
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Erreur de communication', 'error');
                }
            });
        }

        handleFormSubmit('form-update-profile', '/user/update-profile');
        handleFormSubmit('form-update-email', '/user/update-email');
        handleFormSubmit('form-update-password', '/user/update-password');
    });
</script>
