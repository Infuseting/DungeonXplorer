<?php
$pageTitle = "Créer une Quête";
ob_start();
?>

<div class="flex justify-between items-center mb-8">
    <h1>➕ Créer une Quête</h1>
    <a href="/admin/quests" class="btn">← Retour</a>
</div>

<div class="max-w-3xl bg-white p-8 rounded-lg shadow-sm">
    <form method="POST">
        <div class="mb-6">
            <label for="name" class="block mb-2 font-medium text-gray-800">Nom de la quête *</label>
            <input type="text" id="name" name="name" class="w-full p-3 border border-gray-300 rounded text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
        </div>
        
        <div class="mb-6">
            <label for="description" class="block mb-2 font-medium text-gray-800">Description</label>
            <textarea id="description" name="description" rows="5" class="w-full p-3 border border-gray-300 rounded text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none resize-y"></textarea>
        </div>
        
        <div class="mb-6">
            <label for="min_level" class="block mb-2 font-medium text-gray-800">Niveau minimum requis</label>
            <input type="number" id="min_level" name="min_level" value="1" min="1" class="w-full p-3 border border-gray-300 rounded text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
        </div>
        
        <div class="flex gap-4 mt-8">
            <button type="submit" class="btn btn-primary">💾 Créer et Éditer</button>
            <a href="/admin/quests" class="btn">Annuler</a>
        </div>
    </form>
</div>



<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/layouts/admin.php';
?>
