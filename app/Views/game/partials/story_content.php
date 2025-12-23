<?php
?>
<div id="story-scene" data-story-id="<?= $story['id'] ?>" class="absolute inset-0 w-full h-full">

    <!-- Background Image Layer -->
    <div id="story-background"
        class="absolute inset-0 bg-cover bg-center transition-all duration-500 z-0 bg-[url('/assets/images/placeholder_dungeon.jpg')]">
        <div class="absolute inset-0 bg-black/30"></div>
    </div>

    <!-- Room Description Drawer (Top) -->
    <div id="top-drawer"
        class="fixed top-0 left-0 right-0 z-40 transition-transform duration-300 transform -translate-y-full">
        <!-- Drawer Content -->
        <div
            class="bg-gray-900/95 border-b border-yellow-600/30 p-6 pb-8 shadow-[0_10px_40px_rgba(0,0,0,0.8)] backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-8">
                <h2 id="room-name" class="text-xl text-yellow-500 font-bold mb-1 text-center"></h2>
                <p id="room-desc" class="text-gray-300 font-bold text-lg mx-auto leading-relaxed text-center"></p>
            </div>
        </div>

        <!-- Toggle Handle -->
        <div class="absolute -bottom-10 left-0 right-0 flex justify-center pointer-events-none">
            <button id="top-drawer-toggle"
                class="pointer-events-auto bg-gray-900/90 text-yellow-500 px-8 py-2 rounded-b-xl border-b border-x border-yellow-600/50 hover:bg-gray-800 transition-all shadow-[0_5px_15px_rgba(0,0,0,0.5)] flex items-center gap-2 group">
                <span class="font-bold tracking-wider uppercase text-sm">Description</span>
                <span id="top-toggle-icon" class="group-hover:translate-y-1 transition-transform">🔽</span>
            </button>
        </div>
    </div>

    <!-- Center Content (Node Info) -->
    <div class="relative z-10 flex-1 flex flex-col items-center justify-center p-8 h-full pt-24 pb-32 overflow-y-auto">

        <!-- Node Content Container -->
        <div id="main-content-container"
            class="bg-gray-900/90 border border-gray-600 rounded-xl p-8 max-w-4xl w-full shadow-2xl backdrop-blur-sm hidden">

            <!-- Interaction Area (Monsters, Loot, NPCs) -->
            <div id="interaction-area" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 hidden">
                <!-- Monsters -->
                <div id="monsters-container" class="hidden">
                    <h3 class="text-red-400 font-bold mb-2 flex items-center gap-2">
                        <span>⚔️</span> Monstres
                    </h3>
                    <div id="monsters-list" class="space-y-2"></div>
                </div>

                <!-- Loot -->
                <div id="loot-container" class="hidden">
                    <h3 class="text-yellow-400 font-bold mb-2 flex items-center gap-2">
                        <span>💎</span> Butin
                    </h3>
                    <div id="loot-list" class="space-y-2"></div>
                </div>

                <!-- Traps -->
                <div id="traps-container" class="hidden">
                    <h3 class="text-purple-400 font-bold mb-2 flex items-center gap-2">
                        <span>⚠️</span> Pièges
                    </h3>
                    <div id="traps-list" class="space-y-2"></div>
                </div>

                <!-- NPCs -->
                <div id="npcs-container" class="hidden">
                    <h3 class="text-blue-400 font-bold mb-2 flex items-center gap-2">
                        <span>👤</span> PNJ
                    </h3>
                    <div id="npcs-list" class="space-y-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Drawer for Choices -->
    <div id="choices-drawer"
        class="fixed bottom-0 left-0 right-0 z-20 transition-transform duration-300 transform translate-y-full">
        <!-- Toggle Handle -->
        <div class="absolute -top-10 left-0 right-0 flex justify-center pointer-events-none">
            <button id="choices-toggle"
                class="pointer-events-auto bg-gray-900/90 text-yellow-500 px-8 py-2 rounded-t-xl border-t border-x border-yellow-600/50 hover:bg-gray-800 transition-all shadow-[0_-5px_15px_rgba(0,0,0,0.5)] flex items-center gap-2 group">
                <span class="font-bold tracking-wider uppercase text-sm">Actions & Déplacements</span>
                <span id="toggle-icon" class="group-hover:-translate-y-1 transition-transform">🔼</span>
            </button>
        </div>

        <!-- Drawer Content -->
        <div
            class="bg-gray-900/95 border-t border-yellow-600/30 p-6 pb-8 shadow-[0_-10px_40px_rgba(0,0,0,0.8)] backdrop-blur-md">
            <div id="choices-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 max-w-7xl mx-auto">
                <!-- Choices injected by JS -->
            </div>
        </div>
    </div>

    <!-- Exit Button (Bottom Right) -->
    <div class="fixed bottom-24 right-8 z-50">
        <button id="exit-dungeon-btn" onclick="exitDungeon()"
            class="hidden w-16 h-16 bg-yellow-600 hover:bg-yellow-500 text-white rounded-full shadow-lg border-2 border-yellow-400 flex items-center justify-center transition-transform hover:scale-110 active:scale-95"
            title="Sortir du donjon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
    </div>

</div>

<!-- Inline Script for Global Actions -->
<script>
    window.exitDungeon = async function () {
        if (!confirm('Voulez-vous vraiment sortir du donjon ?')) return;

        try {
            const formData = new FormData();
            formData.append('story_id', <?= $story['id'] ?>);

            const response = await fetch('/story/exit', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                if (window.GameRouter) {
                    window.GameRouter.showMap();
                } else {
                    window.location.href = '/game';
                }
            } else {
                alert(data.message || 'Impossible de sortir');
            }
        } catch (e) {
            console.error(e);
            alert('Erreur lors de la sortie');
        }
    };

</script>