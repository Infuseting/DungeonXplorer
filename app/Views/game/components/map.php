<!-- Map Component -->
<div class="absolute inset-0 bg-gray-900">
    <div id="map"></div>
</div>

<!-- Map Point Details Panel -->
<div id="point-panel"
    class="absolute top-0 right-0 h-full w-full md:w-96 bg-gray-800/95 backdrop-blur-md border-l border-gray-700 shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
    <div class="h-full flex flex-col">
        <!-- Panel Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
            <h2 id="point-title" class="text-2xl font-bold text-white"></h2>
            <button id="close-point-panel" class="text-gray-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Panel Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div id="point-type" class="inline-block px-3 py-1 rounded-full text-sm font-semibold mb-4"></div>
            <p id="point-description" class="text-gray-300 mb-6"></p>

            <div class="space-y-4">
                <div id="point-extra-details" class="hidden space-y-4">
                    <!-- Dynamic details like level, difficulty -->
                </div>

                <div class="bg-gray-900/50 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase mb-2">Coordonnées</h3>
                    <p id="point-coords" class="text-white"></p>
                </div>

                <div id="point-actions" class="space-y-2">
                    <!-- Actions will be added dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>