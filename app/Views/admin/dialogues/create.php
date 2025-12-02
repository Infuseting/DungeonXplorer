<?php
$pageTitle = 'Créer un Arbre de Dialogue';
ob_start();
?>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header" style="margin-bottom: 0;">Nouvel Arbre de Dialogue</h3>
        <a href="/admin/dialogues" class="btn btn-secondary">
            ← Retour
        </a>
    </div>
    
    <form method="POST" action="/admin/dialogues/create">
        <div style="display: grid; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Nom de l'arbre *</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-input" 
                    placeholder="Ex: Dialogue Marchand Épicier"
                    required
                    style="width: 100%; padding: 0.75rem; background: var(--bg-darker); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text-light);"
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea 
                    name="description" 
                    class="form-input" 
                    rows="4"
                    placeholder="Description de l'arbre de dialogue..."
                    style="width: 100%; padding: 0.75rem; background: var(--bg-darker); border: 1px solid var(--border); border-radius: 0.5rem; color: var(--text-light); resize: vertical;"
                ></textarea>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="/admin/dialogues" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">🌳 Créer et éditer l'arbre</button>
        </div>
    </form>
</div>

<style>
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-light);
    }
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
