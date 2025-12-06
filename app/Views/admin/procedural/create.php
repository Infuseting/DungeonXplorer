<?php
$pageTitle = 'Nouveau Template';
ob_start();
?>

<div class="flex items-center gap-4 mb-6">
    <a href="/admin/procedural" class="text-gray-400 hover:text-white transition-colors">
        ⬅️ Retour
    </a>
    <h1 class="text-2xl font-bold text-gray-100">Créer un template procédural</h1>
</div>

<div class="bg-gray-800 rounded-lg border border-gray-700 p-6 max-w-2xl">
    <form action="/admin/procedural/create" method="POST" class="space-y-6">
        
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nom du template</label>
            <input type="text" id="name" name="name" required
                   class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Rooms -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nombre de pièces (Min - Max)</label>
                <div class="flex gap-2">
                    <input type="number" name="min_rooms" min="3" value="5" required placeholder="Min"
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <input type="number" name="max_rooms" min="3" value="15" required placeholder="Max"
                           class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <!-- Density -->
            <div>
                <label for="connection_density" class="block text-sm font-medium text-gray-300 mb-2">Densité de connexions (0-1)</label>
                <input type="number" id="connection_density" name="connection_density" min="0" max="1" step="0.1" value="0.3"
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">0 = Linéaire, 1 = Très interconnecté</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Options -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">Options de génération</label>
                <label class="flex items-center gap-2 text-gray-300">
                    <input type="checkbox" name="allow_loops" value="1" checked class="rounded bg-gray-900 border-gray-700 text-indigo-600 focus:ring-indigo-500">
                    Autoriser les boucles
                </label>
                <label class="flex items-center gap-2 text-gray-300">
                    <input type="checkbox" name="allow_backtrack" value="1" checked class="rounded bg-gray-900 border-gray-700 text-indigo-600 focus:ring-indigo-500">
                    Autoriser le retour en arrière
                </label>
            </div>

            <!-- Difficulty Scaling -->
            <div>
                <label for="difficulty_scaling" class="block text-sm font-medium text-gray-300 mb-2">Scaling de difficulté</label>
                <select id="difficulty_scaling" name="difficulty_scaling"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="fixed">Fixe (Selon niveau du template)</option>
                    <option value="linear" selected>Linéaire (Suit le joueur)</option>
                    <option value="exponential">Exponentiel (Difficile)</option>
                </select>
            </div>
        </div>

        <!-- Directions -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Directions autorisées</label>
            <div class="flex flex-wrap gap-4">
                <?php foreach(['north', 'south', 'east', 'west', 'up', 'down'] as $dir): ?>
                    <label class="flex items-center gap-2 text-gray-300">
                        <input type="checkbox" name="direction_types[]" value="<?= $dir ?>" checked class="rounded bg-gray-900 border-gray-700 text-indigo-600 focus:ring-indigo-500">
                        <?= ucfirst($dir) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Créer le template
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
