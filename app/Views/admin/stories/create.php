<?php
$pageTitle = 'Nouvelle Histoire';
ob_start();
?>

<div class="flex items-center gap-4 mb-6">
    <a href="/admin/stories" class="text-gray-400 hover:text-white transition-colors">
        ⬅️ Retour
    </a>
    <h1 class="text-2xl font-bold text-gray-100">Créer une nouvelle histoire</h1>
</div>

<div class="bg-gray-800 rounded-lg border border-gray-700 p-6 max-w-2xl">
    <form action="/admin/stories/create" method="POST" class="space-y-6">
        
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nom de l'histoire</label>
            <input type="text" id="name" name="name" required
                   class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-300 mb-2">Type d'histoire</label>
                <select id="type" name="type" onchange="toggleTemplateSelect()"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="manual">Manuelle (Graphe de nœuds)</option>
                    <option value="procedural">Procédurale (Génération auto)</option>
                </select>
            </div>

            <!-- Procedural Template (Hidden by default) -->
            <div id="template-container" class="hidden">
                <label for="procedural_template_id" class="block text-sm font-medium text-gray-300 mb-2">Template de génération</label>
                <select id="procedural_template_id" name="procedural_template_id"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Sélectionner un template...</option>
                    <?php foreach ($templates as $template): ?>
                        <option value="<?= $template['id'] ?>"><?= htmlspecialchars($template['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Requis pour les histoires procédurales.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Difficulty -->
            <div>
                <label for="difficulty_level" class="block text-sm font-medium text-gray-300 mb-2">Difficulté (1-5)</label>
                <input type="number" id="difficulty_level" name="difficulty_level" min="1" max="5" value="1"
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <!-- Min Level -->
            <div>
                <label for="min_level" class="block text-sm font-medium text-gray-300 mb-2">Niveau Minimum Requis</label>
                <input type="number" id="min_level" name="min_level" min="1" value="1"
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Créer l'histoire
            </button>
        </div>
    </form>
</div>

<script>
function toggleTemplateSelect() {
    const type = document.getElementById('type').value;
    const container = document.getElementById('template-container');
    const select = document.getElementById('procedural_template_id');
    
    if (type === 'procedural') {
        container.classList.remove('hidden');
        select.required = true;
    } else {
        container.classList.add('hidden');
        select.required = false;
        select.value = '';
    }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
