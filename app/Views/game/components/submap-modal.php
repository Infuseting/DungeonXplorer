<!-- Sub-Map Modal Component -->
<div id="submap-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div id="submap-backdrop" class="absolute inset-0 bg-black bg-opacity-80 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full h-full flex flex-col">
        <!-- Header -->
        <div class="relative z-10 bg-gray-900 border-b border-gray-700 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 id="submap-title" class="text-2xl font-bold text-white">Sous-carte</h2>
                <p id="submap-description" class="text-sm text-gray-400 mt-1"></p>
            </div>
            <button id="close-submap" class="text-gray-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Map Container -->
        <div class="relative flex-1 bg-gray-900">
            <div id="submap-container" class="w-full h-full"></div>
        </div>
        
        <!-- Point Details Panel (reuse existing structure) -->
        <div id="submap-point-panel" class="absolute top-0 right-0 h-full w-96 bg-gray-900 border-l border-gray-700 shadow-2xl transform translate-x-full transition-transform duration-300 overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Détails du point</h3>
                    <button id="close-submap-point-panel" class="text-gray-400 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 id="submap-point-title" class="text-lg font-semibold text-white mb-2"></h4>
                        <span id="submap-point-type" class="inline-block px-3 py-1 rounded-full text-xs font-semibold"></span>
                    </div>
                    
                    <div>
                        <p id="submap-point-description" class="text-gray-300 text-sm"></p>
                    </div>
                    
                    <div class="text-xs text-gray-500">
                        <p id="submap-point-coords"></p>
                    </div>
                    
                    <div id="submap-point-actions" class="mt-6"></div>
                </div>
            </div>
        </div>
    </div>
</div>
