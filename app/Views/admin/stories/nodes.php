<?php
$pageTitle = 'Gestion des Nœuds : ' . htmlspecialchars($story['name']);
ob_start();
?>

<link rel="stylesheet" href="/assets/css/admin/story-editor.css">

<div class="flex flex-col h-[calc(100vh-100px)]">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <a href="/admin/stories" class="text-gray-400 hover:text-white transition-colors">
                ⬅️ Retour
            </a>
            <h1 class="text-xl font-bold text-gray-100">Éditeur de Donjon : <?= htmlspecialchars($story['name']) ?></h1>
        </div>
        <div class="flex gap-2">
            <button onclick="storyEditor.addNode()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm font-medium flex items-center gap-1">
                ➕ Ajouter Nœud
            </button>
            <button onclick="storyEditor.saveLayout()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium flex items-center gap-1">
                💾 Sauvegarder Position
            </button>
        </div>
    </div>

    <!-- Editor Area -->
    <div class="flex flex-1 gap-4 overflow-hidden">
        <!-- Canvas Area -->
        <div class="flex-1 bg-gray-900 rounded-lg border border-gray-700 relative overflow-hidden" id="editor-container">
            <div id="canvas-grid" class="absolute inset-0 pointer-events-none opacity-20"></div>
            <div id="nodes-layer" class="absolute inset-0">
                <!-- Nodes will be rendered here by JS -->
            </div>
            <svg id="connections-layer" class="absolute inset-0 w-full h-full pointer-events-none">
                <!-- Connections will be rendered here -->
            </svg>
        </div>

        <!-- Sidebar / Properties Panel -->
        <div class="w-80 bg-gray-800 rounded-lg border border-gray-700 flex flex-col overflow-hidden hidden" id="properties-panel">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-750">
                <h2 class="font-bold text-gray-100">Propriétés</h2>
                <button onclick="storyEditor.closePanel()" class="text-gray-400 hover:text-white">✕</button>
            </div>
            
            <div class="p-4 overflow-y-auto flex-1 space-y-4" id="properties-content">
                <!-- Dynamic content form -->
            </div>
        </div>
    </div>
</div>

<!-- Data passed to JS -->
<script>
    const STORY_ID = <?= $story['id'] ?>;
    const INITIAL_NODES = <?= json_encode($nodes) ?>;
</script>
<script src="/assets/js/admin/story-editor.js"></script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
