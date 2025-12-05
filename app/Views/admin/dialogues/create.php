<?php
$pageTitle = 'Créer un Arbre de Dialogue';
ob_start();
?>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">Nouvel Arbre de Dialogue</h3>
        <a href="/admin/dialogues" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/dialogues/create">
        <div class="grid gap-6">
            <div class="form-group">
                <label class="block mb-2 font-medium text-gray-100">Nom de l'arbre *</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-input w-full p-3 bg-gray-900 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" 
                    placeholder="Ex: Dialogue Marchand Épicier"
                    required
                >
            </div>
            
            <div class="form-group">
                <label class="block mb-2 font-medium text-gray-100">Description</label>
                <textarea 
                    name="description" 
                    class="form-input w-full p-3 bg-gray-900 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none resize-y" 
                    rows="4"
                    placeholder="Description de l'arbre de dialogue..."
                ></textarea>
            </div>
        </div>
        
        <div class="mt-8 flex gap-4 justify-end">
            <a href="/admin/dialogues" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">🌳 Créer et éditer l'arbre</button>
        </div>
    </form>
</div>



<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
