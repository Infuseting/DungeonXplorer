<?php
$title = "Nouvelle Faction";
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="/admin/factions" class="text-gray-400 hover:text-white mr-4 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-100">Nouvelle Faction</h1>
    </div>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-700">
        <form action="/admin/factions/create" method="POST" class="space-y-6">
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nom de la Faction</label>
                <input type="text" id="name" name="name" required
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors"
                       placeholder="ex: Royaume d'Azeroth">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors"
                          placeholder="Description de la faction..."></textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-700">
                <a href="/admin/factions" class="px-4 py-2 text-gray-400 hover:text-white mr-4 transition-colors">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-lg hover:shadow-indigo-500/30">
                    <i class="fas fa-save mr-2"></i>Créer la Faction
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
