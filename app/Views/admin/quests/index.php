<?php
$pageTitle = "Gestion des Quêtes";
ob_start();
?>

<div class="admin-header">
    <h1>📜 Gestion des Quêtes</h1>
    <div class="admin-actions">
        <form method="GET" style="display: inline-block;">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">🔍</button>
        </form>
        <a href="/admin/quests/create" class="btn btn-primary">➕ Nouvelle Quête</a>
    </div>
</div>

<?php if (empty($quests)): ?>
    <div class="empty-state">
        <p>Aucune quête trouvée.</p>
        <a href="/admin/quests/create" class="btn btn-primary">Créer la première quête</a>
    </div>
<?php else: ?>
    <div class="quest-grid">
        <?php foreach ($quests as $quest): ?>
            <div class="quest-card">
                <div class="quest-header">
                    <h3><?= htmlspecialchars($quest['name']) ?></h3>
                    <span class="badge">Niveau <?= $quest['min_level'] ?></span>
                </div>
                
                <p class="quest-description"><?= htmlspecialchars(substr($quest['description'] ?? '', 0, 150)) ?><?= strlen($quest['description'] ?? '') > 150 ? '...' : '' ?></p>
                
                <div class="quest-stats">
                    <span>📋 <?= $quest['stage_count'] ?> étape(s)</span>
                </div>
                
                <div class="quest-actions">
                    <a href="/admin/quests/edit/<?= $quest['id'] ?>" class="btn btn-sm">✏️ Éditer</a>
                    <form method="POST" action="/admin/quests/delete/<?= $quest['id'] ?>" style="display: inline;" onsubmit="return confirm('Supprimer cette quête ?');">
                        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.admin-actions {
    display: flex;
    gap: 1rem;
}

.quest-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.quest-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    transition: box-shadow 0.2s;
}

.quest-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.quest-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.quest-header h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
}

.badge {
    background: #4CAF50;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.85rem;
}

.quest-description {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.quest-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    font-size: 0.9rem;
    color: #666;
}

.quest-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.btn-danger {
    background: #f44336;
}

.btn-danger:hover {
    background: #d32f2f;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 8px;
    border: 2px dashed #ddd;
}
</style>
<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/layouts/admin.php';
?>
