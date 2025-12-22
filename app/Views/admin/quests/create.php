<?php
$pageTitle = "Créer une Quête";
ob_start();
?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold">➕ Créer une Quête</h1>
    <a href="/admin/quests" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">← Retour</a>
</div>

<div class="max-w-3xl bg-slate-900 border border-slate-800 p-8 rounded-xl">
    <form method="POST">
        <div class="mb-6">
            <label for="name" class="block mb-2 font-medium text-slate-300">Nom de la quête *</label>
            <input type="text" id="name" name="name" class="w-full p-3 bg-slate-950 border border-slate-700 rounded-lg text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
        </div>
        
        <div class="mb-6">
            <label for="description" class="block mb-2 font-medium text-slate-300">Description</label>
            <textarea id="description" name="description" rows="5" class="w-full p-3 bg-slate-950 border border-slate-700 rounded-lg text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none resize-y"></textarea>
        </div>
        
        <div class="mb-6">
            <label for="min_level" class="block mb-2 font-medium text-slate-300">Niveau minimum requis</label>
            <input type="number" id="min_level" name="min_level" value="1" min="1" class="w-full p-3 bg-slate-950 border border-slate-700 rounded-lg text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
        </div>
        
        <div class="flex gap-4 mt-8">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">💾 Créer et Éditer</button>
            <a href="/admin/quests" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">Annuler</a>
        </div>
    </form>
</div>



<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/layouts/admin.php';
?>
