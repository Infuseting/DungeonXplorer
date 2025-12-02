<?php
$pageTitle = "Créer une Quête";
ob_start();
?>

<div class="admin-header">
    <h1>➕ Créer une Quête</h1>
    <a href="/admin/quests" class="btn">← Retour</a>
</div>

<div class="form-container">
    <form method="POST">
        <div class="form-group">
            <label for="name">Nom de la quête *</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"></textarea>
        </div>
        
        <div class="form-group">
            <label for="min_level">Niveau minimum requis</label>
            <input type="number" id="min_level" name="min_level" value="1" min="1">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Créer et Éditer</button>
            <a href="/admin/quests" class="btn">Annuler</a>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 800px;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
</style>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/layouts/admin.php';
?>
