<!-- Settings Modal -->
<div id="settings-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80">
    <div class="bg-gray-900 border-2 border-violet-600 rounded-lg p-6 w-full max-w-md shadow-2xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-violet-400">⚙️ Paramètres</h2>
            <button id="settings-close-btn" class="text-gray-400 hover:text-white text-2xl">&times;</button>
        </div>

        <!-- Audio Settings -->
        <div class="space-y-6">
            <!-- Master Volume -->
            <div>
                <label class="block text-white font-semibold mb-2 flex items-center justify-between">
                    <span>🔊 Volume Global</span>
                    <span id="master-volume-value" class="text-violet-400">70%</span>
                </label>
                <input type="range" id="master-volume" min="0" max="100" value="70" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
            </div>

            <!-- Music Volume -->
            <div>
                <label class="block text-white font-semibold mb-2 flex items-center justify-between">
                    <span>🎵 Volume Musique</span>
                    <span id="music-volume-value" class="text-violet-400">50%</span>
                </label>
                <input type="range" id="music-volume" min="0" max="100" value="50" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
            </div>

            <!-- SFX Volume -->
            <div>
                <label class="block text-white font-semibold mb-2 flex items-center justify-between">
                    <span>🔔 Volume Effets Sonores</span>
                    <span id="sfx-volume-value" class="text-violet-400">80%</span>
                </label>
                <input type="range" id="sfx-volume" min="0" max="100" value="80" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-6 pt-4 border-t border-gray-700">
            <button id="settings-save-btn" class="w-full px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-semibold transition-colors">
                Sauvegarder
            </button>
        </div>
    </div>
</div>
