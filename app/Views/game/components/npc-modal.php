<!-- NPC Interaction Modal -->
<div id="npc-modal" class="fixed inset-0 z-[10000] flex items-end justify-center hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-[5px]"></div>
    <div class="relative w-full max-w-[1200px] mx-auto p-8 flex gap-8 items-end z-10">
        <!-- NPC Portrait -->
        <div class="w-[400px] h-[600px] bg-gradient-to-br from-indigo-500/10 to-purple-500/10 border-[3px] border-indigo-500/30 rounded-2xl overflow-hidden flex items-center justify-center shadow-[0_10px_40px_rgba(0,0,0,0.5)]">
            <img id="npc-portrait-img" src="" alt="NPC" class="w-full h-full object-cover">
        </div>
        
        <!-- Dialogue Box -->
        <div class="flex-1 bg-gradient-to-br from-[#1a1a2e] to-[#16213e] border-[3px] border-indigo-500/50 rounded-2xl p-8 shadow-[0_10px_40px_rgba(0,0,0,0.5)] relative min-h-[250px] flex flex-col">
            <div class="text-2xl font-bold text-amber-400 mb-4 drop-shadow-[0_2px_10px_rgba(251,191,36,0.5)]" id="npc-name">NPC Name</div>
            <div class="text-lg leading-relaxed text-gray-200 mb-6 flex-1" id="npc-text">Dialogue text goes here...</div>
            
            <!-- Dialogue Choices -->
            <div class="flex flex-col gap-3 mt-4 hidden" id="npc-choices">
                <!-- Choices will be inserted here -->
            </div>
            
            <!-- Action Buttons (for merchants, etc.) -->
            <div class="flex flex-wrap gap-4 mt-4" id="npc-actions">
                <!-- Actions will be inserted here -->
            </div>
            
            <!-- Close Button -->
            <button class="absolute top-4 right-4 bg-red-500/20 border-2 border-red-500/50 text-red-300 w-10 h-10 rounded-full text-2xl cursor-pointer transition-all duration-300 flex items-center justify-center hover:bg-red-500/40 hover:border-red-500" onclick="closeNPCModal()">✕</button>
        </div>
    </div>
</div>
<script type="module" src="/js/modules/npcModal.js"></script>