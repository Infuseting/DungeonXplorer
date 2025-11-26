<?php
$pageTitle = "Éditer Quête - " . htmlspecialchars($quest['name']);
ob_start();
?>

<div class="admin-header">
    <h1>✏️ Éditer: <?= htmlspecialchars($quest['name']) ?></h1>
    <a href="/admin/quests" class="btn">← Retour</a>
</div>

<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('general')">📋 Général</button>
    <button class="tab-btn" onclick="switchTab('stages')">🎯 Étapes & Objectifs</button>
    <button class="tab-btn" onclick="switchTab('npcs')">👥 PNJ</button>
</div>

<!-- General Tab -->
<div id="tab-general" class="tab-content active">
    <form method="POST" class="form-container">
        <div class="form-group">
            <label for="name">Nom de la quête *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($quest['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($quest['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="min_level">Niveau minimum requis</label>
            <input type="number" id="min_level" name="min_level" value="<?= $quest['min_level'] ?>" min="1">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Sauvegarder</button>
        </div>
    </form>
</div>

<!-- Stages Tab -->
<div id="tab-stages" class="tab-content">
    <div class="stages-container">
        <button onclick="addStage()" class="btn btn-primary">➕ Ajouter une Étape</button>
        
        <div id="stages-list">
            <?php foreach ($quest['stages'] ?? [] as $index => $stage): ?>
                <div class="stage-card" data-stage-id="<?= $stage['id'] ?>">
                    <div class="stage-header">
                        <h3>Étape <?= $index + 1 ?>: <?= htmlspecialchars($stage['name']) ?></h3>
                        <div class="stage-actions">
                            <button onclick="editStage(<?= $stage['id'] ?>)" class="btn btn-sm">✏️</button>
                            <button onclick="deleteStage(<?= $stage['id'] ?>)" class="btn btn-sm btn-danger">🗑️</button>
                        </div>
                    </div>
                    
                    <p><?= htmlspecialchars($stage['description'] ?? '') ?></p>
                    
                    <div class="objectives-section">
                        <h4>Objectifs:</h4>
                        <button onclick="addObjective(<?= $stage['id'] ?>)" class="btn btn-sm">➕ Ajouter Objectif</button>
                        
                        <ul class="objectives-list">
                            <?php foreach ($stage['objectives'] ?? [] as $obj): ?>
                                <li class="objective-item" data-objective-id="<?= $obj['id'] ?>">
                                    <span class="objective-icon"><?= getObjectiveIcon($obj['type']) ?></span>
                                    <span><?= htmlspecialchars($obj['description']) ?></span>
                                    <span class="objective-count">(<?= $obj['count_required'] ?>x)</span>
                                    <div class="objective-actions">
                                        <button onclick="editObjective(<?= $obj['id'] ?>)" class="btn btn-xs">✏️</button>
                                        <button onclick="deleteObjective(<?= $obj['id'] ?>)" class="btn btn-xs btn-danger">🗑️</button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        </ul>
                    </div>
                    
                    <div class="unlocks-section">
                        <h4>Déblocage de Lieux:</h4>
                        <div class="unlock-controls">
                            <select id="unlock-select-<?= $stage['id'] ?>" class="unlock-select">
                                <option value="">-- Choisir un lieu à débloquer --</option>
                                <?php foreach ($allMapPoints as $mp): ?>
                                    <option value="<?= $mp['id'] ?>"><?= htmlspecialchars($mp['name']) ?> (Map <?= $mp['map_id'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <button onclick="addMapUnlock(<?= $stage['id'] ?>)" class="btn btn-sm">➕ Ajouter</button>
                        </div>
                        
                        <ul class="unlocks-list">
                            <?php foreach ($stage['unlocks'] ?? [] as $unlock): ?>
                                <li class="unlock-item">
                                    <span class="unlock-icon">🔓</span>
                                    <span><?= htmlspecialchars($unlock['name']) ?></span>
                                    <button onclick="removeMapUnlock(<?= $stage['id'] ?>, <?= $unlock['id'] ?>)" class="btn btn-xs btn-danger">🗑️</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- NPCs Tab -->
<div id="tab-npcs" class="tab-content">
    <div class="npc-assignment">
        <h3>Assigner des PNJ</h3>
        
        <div class="form-group">
            <label>PNJ Donneur de Quête</label>
            <select id="npc-select">
                <option value="">-- Sélectionner un PNJ --</option>
                <?php foreach ($npcs as $npc): ?>
                    <option value="<?= $npc['id'] ?>"><?= htmlspecialchars($npc['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button onclick="assignNPC('GIVER')" class="btn">➕ Assigner comme Donneur</button>
        </div>
        
        <h4>PNJ Assignés:</h4>
        <ul id="assigned-npcs">
            <?php foreach ($assignedNPCs as $npc): ?>
                <li data-npc-id="<?= $npc['id'] ?>">
                    <strong><?= htmlspecialchars($npc['name']) ?></strong> 
                    <span class="badge"><?= $npc['type'] === 'GIVER' ? 'Donneur' : 'Récepteur' ?></span>
                    <button onclick="removeNPC(<?= $npc['id'] ?>)" class="btn btn-xs btn-danger">🗑️</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Stage Modal -->
<div id="stage-modal" class="modal">
    <div class="modal-content">
        <h3 id="stage-modal-title">Ajouter une Étape</h3>
        <form id="stage-form">
            <input type="hidden" id="stage-id">
            <div class="form-group">
                <label for="stage-name">Nom de l'étape *</label>
                <input type="text" id="stage-name" required>
            </div>
            <div class="form-group">
                <label for="stage-description">Description</label>
                <textarea id="stage-description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="stage-order">Ordre</label>
                <input type="number" id="stage-order" value="0" min="0">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Sauvegarder</button>
                <button type="button" onclick="closeStageModal()" class="btn">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Objective Modal -->
<div id="objective-modal" class="modal">
    <div class="modal-content">
        <h3 id="objective-modal-title">Ajouter un Objectif</h3>
        <form id="objective-form">
            <input type="hidden" id="objective-id">
            <input type="hidden" id="objective-stage-id">
            <div class="form-group">
                <label for="objective-type">Type *</label>
                <select id="objective-type" required>
                    <option value="TALK_NPC">🗣️ Parler à un PNJ</option>
                    <option value="KILL_MONSTER">⚔️ Tuer un monstre</option>
                    <option value="HAVE_ITEM">🎒 Posséder un objet</option>
                    <option value="VISIT_LOCATION">📍 Visiter un lieu</option>
                    <option value="DUNGEON_CLEAR">🏰 Terminer un donjon</option>
                </select>
            </div>
            <div class="form-group">
                <label for="objective-description">Description *</label>
                <input type="text" id="objective-description" required placeholder="Ex: Parler au forgeron">
            </div>
            <div class="form-group">
                <label for="objective-count">Quantité requise</label>
                <input type="number" id="objective-count" value="1" min="1">
            </div>
            <div class="form-group">
                <label for="objective-target">ID Cible (optionnel)</label>
                <input type="number" id="objective-target" placeholder="ID du PNJ, Item, etc.">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Sauvegarder</button>
                <button type="button" onclick="closeObjectiveModal()" class="btn">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php
function getObjectiveIcon($type) {
    $icons = [
        'TALK_NPC' => '🗣️',
        'KILL_MONSTER' => '⚔️',
        'HAVE_ITEM' => '🎒',
        'VISIT_LOCATION' => '📍',
        'DUNGEON_CLEAR' => '🏰'
    ];
    return $icons[$type] ?? '❓';
}
?>

<style>
.tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #e0e0e0;
}

.tab-btn {
    padding: 1rem 2rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.2s;
}

.tab-btn:hover {
    background: #f5f5f5;
}

.tab-btn.active {
    border-bottom-color: #2196F3;
    color: #2196F3;
    font-weight: 600;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.stages-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
}

.stage-card {
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.stage-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stage-header h3 {
    margin: 0;
    color: #333;
}

.stage-actions {
    display: flex;
    gap: 0.5rem;
}

.objectives-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #ddd;
}

.objectives-section h4 {
    margin-bottom: 1rem;
}

.objectives-list {
    list-style: none;
    padding: 0;
    margin-top: 1rem;
}

.objective-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.objective-icon {
    font-size: 1.2rem;
}

.objective-count {
    color: #666;
    font-size: 0.9rem;
}

.objective-actions {
    margin-left: auto;
    display: flex;
    gap: 0.25rem;
}

.npc-assignment {
    background: white;
    padding: 2rem;
    border-radius: 8px;
}

#assigned-npcs {
    list-style: none;
    padding: 0;
}

#assigned-npcs li {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.unlocks-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #ddd;
}

.unlocks-section h4 {
    margin-bottom: 1rem;
}

.unlock-controls {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.unlock-select {
    flex: 1;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.unlocks-list {
    list-style: none;
    padding: 0;
}

.unlock-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #e8f5e9;
    border: 1px solid #c8e6c9;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.unlock-icon {
    font-size: 1.2rem;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
</style>

<script>
const questId = <?= $quest['id'] ?>;

// Tab switching
function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    event.target.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Stage Management
function addStage() {
    document.getElementById('stage-modal-title').textContent = 'Ajouter une Étape';
    document.getElementById('stage-form').reset();
    document.getElementById('stage-id').value = '';
    document.getElementById('stage-modal').classList.add('active');
}

function editStage(stageId) {
    // TODO: Load stage data and populate form
    document.getElementById('stage-modal-title').textContent = 'Éditer l\'Étape';
    document.getElementById('stage-id').value = stageId;
    document.getElementById('stage-modal').classList.add('active');
}

function closeStageModal() {
    document.getElementById('stage-modal').classList.remove('active');
}

document.getElementById('stage-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const stageId = document.getElementById('stage-id').value;
    const data = {
        quest_id: questId,
        name: document.getElementById('stage-name').value,
        description: document.getElementById('stage-description').value,
        order_index: parseInt(document.getElementById('stage-order').value)
    };
    
    const url = stageId ? '/admin/quests/stage/update' : '/admin/quests/stage/add';
    if (stageId) data.id = parseInt(stageId);
    
    const response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    if (result.success) {
        closeStageModal();
        location.reload();
    } else {
        alert(result.message || 'Erreur');
    }
});

async function deleteStage(stageId) {
    if (!confirm('Supprimer cette étape et tous ses objectifs ?')) return;
    
    const response = await fetch('/admin/quests/stage/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: stageId})
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}

// Objective Management
function addObjective(stageId) {
    document.getElementById('objective-modal-title').textContent = 'Ajouter un Objectif';
    document.getElementById('objective-form').reset();
    document.getElementById('objective-id').value = '';
    document.getElementById('objective-stage-id').value = stageId;
    document.getElementById('objective-modal').classList.add('active');
}

function editObjective(objectiveId) {
    // TODO: Load objective data
    document.getElementById('objective-modal-title').textContent = 'Éditer l\'Objectif';
    document.getElementById('objective-id').value = objectiveId;
    document.getElementById('objective-modal').classList.add('active');
}

function closeObjectiveModal() {
    document.getElementById('objective-modal').classList.remove('active');
}

document.getElementById('objective-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const objectiveId = document.getElementById('objective-id').value;
    const data = {
        stage_id: parseInt(document.getElementById('objective-stage-id').value),
        type: document.getElementById('objective-type').value,
        description: document.getElementById('objective-description').value,
        count_required: parseInt(document.getElementById('objective-count').value),
        target_id: document.getElementById('objective-target').value || null
    };
    
    const url = objectiveId ? '/admin/quests/objective/update' : '/admin/quests/objective/add';
    if (objectiveId) data.id = parseInt(objectiveId);
    
    const response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    if (result.success) {
        closeObjectiveModal();
        location.reload();
    } else {
        alert(result.message || 'Erreur');
    }
});

async function deleteObjective(objectiveId) {
    if (!confirm('Supprimer cet objectif ?')) return;
    
    const response = await fetch('/admin/quests/objective/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: objectiveId})
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}

// NPC Assignment
async function assignNPC(type) {
    const npcId = document.getElementById('npc-select').value;
    if (!npcId) {
        alert('Sélectionnez un PNJ');
        return;
    }
    
    const response = await fetch('/admin/quests/assign-npc', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            npc_id: parseInt(npcId),
            quest_id: questId,
            type: type
        })
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    } else {
        alert(result.message || 'Erreur');
    }
}

async function removeNPC(npcId) {
    if (!confirm('Retirer ce PNJ de la quête ?')) return;
    
    const response = await fetch('/admin/quests/remove-npc', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            npc_id: npcId,
            quest_id: questId
        })
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}

// Map Unlocks
async function addMapUnlock(stageId) {
    const select = document.getElementById('unlock-select-' + stageId);
    const mapPointId = select.value;
    
    if (!mapPointId) {
        alert('Sélectionnez un lieu');
        return;
    }
    
    const response = await fetch('/admin/quests/stage/add-unlock', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            stage_id: stageId,
            map_point_id: parseInt(mapPointId)
        })
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    } else {
        alert(result.message || 'Erreur');
    }
}

async function removeMapUnlock(stageId, mapPointId) {
    if (!confirm('Retirer ce déblocage ?')) return;
    
    const response = await fetch('/admin/quests/stage/remove-unlock', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            stage_id: stageId,
            map_point_id: mapPointId
        })
    });
    
    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
