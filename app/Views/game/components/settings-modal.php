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

            <!-- Linked Accounts -->
            <div id="linked-accounts-section" class="border-t border-gray-700 pt-4">
                <h3 class="text-lg font-semibold text-gray-300 mb-3">Comptes connectés</h3>
                <div class="space-y-2">
                    <!-- Google -->
                    <div class="flex items-center justify-between bg-gray-800 p-3 rounded">
                        <span class="text-gray-300 flex items-center gap-2">
                             <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24">
                                <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .533 5.333.533 12S5.867 24 12.48 24c3.44 0 6.053-1.147 8.16-3.293 2.133-2.133 2.907-5.133 2.907-7.653 0-.693-.067-1.453-.173-2.133H12.48z" fill="currentColor" />
                            </svg>
                            Google
                        </span>
                        <div id="btn-link-google">
                            <a href="/oauth/login/google" class="text-sm text-violet-400 hover:text-violet-300">Connecter</a>
                        </div>
                    </div>
                     <!-- Discord -->
                    <div class="flex items-center justify-between bg-gray-800 p-3 rounded">
                        <span class="text-gray-300 flex items-center gap-2">
                            <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.772-.6083 1.1588a18.2915 18.2915 0 00-7.3986 0 13.9856 13.9856 0 00-.616-1.1588.0776.0776 0 00-.0785-.0371 19.7825 19.7825 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.419-2.1568 2.419zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.419-2.1568 2.419z"/>
                            </svg>
                            Discord
                        </span>
                        <div id="btn-link-discord">
                           <a href="/oauth/login/discord" class="text-sm text-violet-400 hover:text-violet-300">Connecter</a>
                        </div>
                    </div>
                     <!-- GitHub -->
                    <div class="flex items-center justify-between bg-gray-800 p-3 rounded">
                        <span class="text-gray-300 flex items-center gap-2">
                             <svg class="h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                            GitHub
                        </span>
                         <div id="btn-link-github">
                            <a href="/oauth/login/github" class="text-sm text-violet-400 hover:text-violet-300">Connecter</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // ... (existing code)

        // Fetch Connected Accounts
        async function loadConnectedAccounts() {
             try {
                const response = await fetch('/user/connected-accounts');
                const data = await response.json();
                
                if (data.accounts) {
                    ['google', 'discord', 'github'].forEach(provider => {
                        const container = document.getElementById(`btn-link-${provider}`);
                        if (data.accounts.includes(provider)) {
                            container.innerHTML = `
                                <span class="text-green-400 text-sm mr-2">Connecté</span>
                                <form action="/oauth/unlink/${provider}" method="POST" class="inline">
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-400 opacity-50 hover:opacity-100">Délier</button>
                                </form>
                            `;
                        }
                    });
                }
            } catch (e) {
                console.error("Failed to load connected accounts", e);
            }
        }
        
        // Load on open profile tab
        // Note: profileTabBtn is defined below in the unified block to avoid duplicates
        
        const audioTabBtn = document.getElementById('tab-audio-btn');
        const profileTabBtn = document.getElementById('tab-profile-btn');
        const audioContent = document.getElementById('tab-audio-content');
        const profileContent = document.getElementById('tab-profile-content');
        const settingsModal = document.getElementById('settings-modal');

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
                
                // Load data when switching to profile
                loadConnectedAccounts();
            }
        }

        if(audioTabBtn) audioTabBtn.addEventListener('click', () => switchTab('audio'));
        if(profileTabBtn) profileTabBtn.addEventListener('click', () => switchTab('profile'));
        
        // Auto-open from URL
        const urlParams = new URLSearchParams(window.location.search);
        const settingsParam = urlParams.get('settings');
        if (settingsParam === 'profile') {
            settingsModal.classList.remove('hidden');
            switchTab('profile');
        } else if (settingsParam) {
             settingsModal.classList.remove('hidden');
        }

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
