<?php
$pageTitle = "Éditer Quête - " . htmlspecialchars($quest['name']);
ob_start();
?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold">✏️ Éditer: <?= htmlspecialchars($quest['name']) ?></h1>
    <a href="/admin/quests"
        class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">←
        Retour</a>
</div>

<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('general')">📋 Général</button>
    <button class="tab-btn" onclick="switchTab('stages')">🎯 Étapes & Objectifs</button>
    <button class="tab-btn" onclick="switchTab('prerequisites')">🔒 Prérequis</button>
    <button class="tab-btn" onclick="switchTab('rewards')">🎁 Récompenses</button>
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
            <textarea id="description" name="description"
                rows="5"><?= htmlspecialchars($quest['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="intro_text">Texte d'introduction (Dialogue PNJ)</label>
            <textarea id="intro_text" name="intro_text" rows="3"
                placeholder="Texte dit par le PNJ avant d'accepter la quête..."><?= htmlspecialchars($quest['intro_text'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="min_level">Niveau minimum requis</label>
            <input type="number" id="min_level" name="min_level" value="<?= $quest['min_level'] ?>" min="1">
        </div>

        <div class="form-group">
            <label for="xp_reward">Gain d'XP</label>
            <input type="number" id="xp_reward" name="xp_reward" value="<?= $quest['xp_reward'] ?? 0 ?>" min="0">
        </div>

        <div class="form-group">
            <label for="gold_reward">Gain d'Or</label>
            <input type="number" id="gold_reward" name="gold_reward" value="<?= $quest['gold_reward'] ?? 0 ?>" min="0">
        </div>

        <div class="form-actions">
            <button type="submit"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">💾
                Sauvegarder</button>
        </div>
    </form>
</div>

<!-- Stages Tab -->
<div id="tab-stages" class="tab-content">
    <div class="stages-container">
        <button onclick="addStage()"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">➕
            Ajouter une Étape</button>

        <div id="stages-list">
            <?php foreach ($quest['stages'] ?? [] as $index => $stage): ?>
                <div class="stage-card" data-stage-id="<?= $stage['id'] ?>">
                    <div class="stage-header">
                        <h3>Étape <?= $index + 1 ?>: <?= htmlspecialchars($stage['name']) ?></h3>
                        <div class="stage-actions">
                            <button onclick="editStage(<?= $stage['id'] ?>)" class="btn-sm">✏️</button>
                            <button onclick="deleteStage(<?= $stage['id'] ?>)" class="btn-sm btn-danger">🗑️</button>
                        </div>
                    </div>

                    <p class="text-slate-400"><?= htmlspecialchars($stage['description'] ?? '') ?></p>

                    <div class="objectives-section">
                        <h4>Objectifs:</h4>
                        <button onclick="addObjective(<?= $stage['id'] ?>)" class="btn-sm bg-slate-700 hover:bg-slate-600">➕
                            Ajouter Objectif</button>

                        <ul class="objectives-list">
                            <?php foreach ($stage['objectives'] ?? [] as $obj): ?>
                                <li class="objective-item" data-objective-id="<?= $obj['id'] ?>">
                                    <span class="objective-icon"><?= getObjectiveIcon($obj['type']) ?></span>
                                    <span><?= htmlspecialchars($obj['description']) ?></span>
                                    <span class="objective-count">(<?= $obj['count_required'] ?>x)</span>
                                    <div class="objective-actions">
                                        <button onclick="editObjective(<?= $obj['id'] ?>)" class="btn-xs">✏️</button>
                                        <button onclick="deleteObjective(<?= $obj['id'] ?>)"
                                            class="btn-xs btn-danger">🗑️</button>
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
                                    <option value="<?= $mp['id'] ?>"><?= htmlspecialchars($mp['name']) ?> (Map
                                        <?= $mp['map_id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button onclick="addMapUnlock(<?= $stage['id'] ?>)"
                                class="btn-sm bg-slate-700 hover:bg-slate-600">➕ Ajouter</button>
                        </div>

                        <ul class="unlocks-list">
                            <?php foreach ($stage['unlocks'] ?? [] as $unlock): ?>
                                <li class="unlock-item">
                                    <span class="unlock-icon">🔓</span>
                                    <span><?= htmlspecialchars($unlock['name']) ?></span>
                                    <button onclick="removeMapUnlock(<?= $stage['id'] ?>, <?= $unlock['id'] ?>)"
                                        class="btn-xs btn-danger">🗑️</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Prerequisites Tab -->
<div id="tab-prerequisites" class="tab-content">
    <div class="prerequisites-container">
        <h3 class="text-lg font-semibold text-slate-200 mb-2">Prérequis de la Quête</h3>
        <p class="text-slate-400 mb-4">Le joueur doit avoir terminé ces quêtes pour débloquer celle-ci.</p>

        <div class="form-group">
            <label class="text-slate-300">Ajouter un prérequis</label>
            <div style="display: flex; gap: 0.5rem;">
                <select id="prerequisite-select"
                    class="flex-1 p-2 bg-slate-950 border border-slate-700 rounded-lg text-slate-200">
                    <option value="">-- Choisir une quête --</option>
                    <?php foreach ($allQuests as $q): ?>
                        <?php if ($q['id'] != $quest['id']): ?>
                            <option value="<?= $q['id'] ?>"><?= htmlspecialchars($q['name']) ?> (Niveau <?= $q['min_level'] ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button onclick="addPrerequisite()"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">➕
                    Ajouter</button>
            </div>
        </div>

        <ul class="prerequisites-list">
            <?php foreach ($prerequisites as $prereq): ?>
                <li class="prerequisite-item">
                    <span>✅ <?= htmlspecialchars($prereq['name']) ?></span>
                    <button onclick="removePrerequisite(<?= $prereq['id'] ?>)" class="btn-xs btn-danger">🗑️</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Rewards Tab -->
<div id="tab-rewards" class="tab-content">
    <div class="prerequisites-container">
        <h3 class="text-lg font-semibold text-slate-200 mb-2">Récompenses (Objets)</h3>
        <p class="text-slate-400 mb-4">Objets donnés au joueur en plus de l'XP et de l'Or.</p>

        <div class="form-group">
            <label class="text-slate-300">Ajouter un objet</label>
            <div class="flex gap-2">
                <select id="reward-item-select"
                    class="flex-grow p-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-200">
                    <option value="">-- Choisir un objet --</option>
                    <?php foreach ($allItems as $item): ?>
                        <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?> (<?= $item['type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="reward-item-qty" value="1" min="1"
                    class="w-24 p-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-200 text-center"
                    placeholder="Qté">
                <button onclick="addRewardItem()"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all whitespace-nowrap">➕
                    Ajouter</button>
            </div>
        </div>

        <ul class="prerequisites-list">
            <?php foreach ($quest['reward_items'] ?? [] as $reward): ?>
                <li class="prerequisite-item reward-item">
                    <span>🎁 <?= htmlspecialchars($reward['name']) ?> (x<?= $reward['quantity'] ?>)</span>
                    <button onclick="removeRewardItem(<?= $reward['id'] ?>)" class="btn-xs btn-danger">🗑️</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Stage Modal -->
<div id="stage-modal" class="modal">
    <div class="modal-content">
        <h3 id="stage-modal-title" class="text-xl font-semibold text-slate-200 mb-4">Ajouter une Étape</h3>
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
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">💾
                    Sauvegarder</button>
                <button type="button" onclick="closeStageModal()"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Objective Modal -->
<div id="objective-modal" class="modal">
    <div class="modal-content">
        <h3 id="objective-modal-title" class="text-xl font-semibold text-slate-200 mb-4">Ajouter un Objectif</h3>
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

            <!-- NPC Selection (for TALK_NPC) -->
            <div class="form-group" id="objective-npc-group" style="display:none;">
                <label for="objective-npc">PNJ à qui parler *</label>
                <select id="objective-npc" class="form-select">
                    <option value="">-- Sélectionner un PNJ --</option>
                    <?php foreach ($allNPCs as $npc): ?>
                        <option value="<?= $npc['id'] ?>">
                            <?= htmlspecialchars($npc['name']) ?> (ID: <?= $npc['id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Dialogue Tree Selection (for TALK_NPC) -->
            <div class="form-group" id="objective-dialogue-group" style="display:none;">
                <label for="objective-dialogue">Arbre de dialogue *</label>
                <select id="objective-dialogue" class="form-select">
                    <option value="">-- Sélectionner un dialogue --</option>
                    <?php foreach ($dialogueTrees as $tree): ?>
                        <option value="<?= $tree['id'] ?>">
                            <?= htmlspecialchars($tree['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-slate-500 block mt-1">
                    Le dialogue sera visible uniquement pendant cette étape
                </small>
            </div>

            <!-- Generic Target ID (for other types) -->
            <div class="form-group" id="objective-target-group">
                <label for="objective-target">ID Cible (optionnel)</label>
                <input type="number" id="objective-target" placeholder="ID de l'item, monstre, etc.">
            </div>
            <div class="form-actions">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">💾
                    Sauvegarder</button>
                <button type="button" onclick="closeObjectiveModal()"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php
function getObjectiveIcon($type)
{
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
    /* Dark theme styles */
    .tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid #334155;
    }

    .tab-btn {
        padding: 1rem 2rem;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-size: 1rem;
        color: #94a3b8;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        background: #1e293b;
        color: #e2e8f0;
    }

    .tab-btn.active {
        border-bottom-color: #6366f1;
        color: #6366f1;
        font-weight: 600;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .form-container {
        background: #0f172a;
        padding: 2rem;
        border-radius: 0.75rem;
        border: 1px solid #1e293b;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #cbd5e1;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        background: #020617;
        border: 1px solid #334155;
        border-radius: 0.5rem;
        color: #e2e8f0;
        font-size: 1rem;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .stages-container,
    .prerequisites-container {
        background: #0f172a;
        padding: 2rem;
        border-radius: 0.75rem;
        border: 1px solid #1e293b;
    }

    .stage-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 0.5rem;
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
        color: #e2e8f0;
        font-size: 1.1rem;
    }

    .stage-actions {
        display: flex;
        gap: 0.5rem;
    }

    .objectives-section,
    .unlocks-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #334155;
    }

    .objectives-section h4,
    .unlocks-section h4 {
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    .objectives-list,
    .unlocks-list,
    .prerequisites-list {
        list-style: none;
        padding: 0;
        margin-top: 1rem;
    }

    .objective-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        color: #e2e8f0;
    }

    .objective-icon,
    .unlock-icon {
        font-size: 1.2rem;
    }

    .objective-count {
        color: #64748b;
        font-size: 0.9rem;
    }

    .objective-actions {
        margin-left: auto;
        display: flex;
        gap: 0.25rem;
    }

    .unlock-controls {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .unlock-select {
        flex: 1;
        padding: 0.5rem;
        background: #020617;
        border: 1px solid #334155;
        border-radius: 0.375rem;
        color: #e2e8f0;
    }

    .unlock-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        color: #4ade80;
    }

    .prerequisite-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: rgba(234, 179, 8, 0.1);
        border: 1px solid rgba(234, 179, 8, 0.3);
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        color: #facc15;
    }

    .prerequisite-item.reward-item {
        background: rgba(168, 85, 247, 0.1);
        border: 1px solid rgba(168, 85, 247, 0.3);
        color: #c084fc;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: #1e293b;
        padding: 2rem;
        border-radius: 0.75rem;
        border: 1px solid #334155;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Button styles */
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        background: #334155;
        color: #e2e8f0;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-sm:hover {
        background: #475569;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        background: #334155;
        color: #e2e8f0;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-xs:hover {
        background: #475569;
    }

    .btn-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
    }

    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.3);
    }
</style>

<script>
    const questId = <?= $quest['id'] ?>;

    async function apiCall(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                return result;
            } else {
                alert(result.message || 'Erreur');
                return null;
            }
        } catch (e) {
            console.error(e);
            alert('Erreur de communication (500)');
            return null;
        }
    }

    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        event.target.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
    }

    function addStage() {
        document.getElementById('stage-modal-title').textContent = 'Ajouter une Étape';
        document.getElementById('stage-form').reset();
        document.getElementById('stage-id').value = '';
        document.getElementById('stage-modal').classList.add('active');
    }

    function editStage(stageId) {
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

        const result = await apiCall(url, data);
        if (result) {
            closeStageModal();
            location.reload();
        }
    });

    async function deleteStage(stageId) {
        if (!confirm('Supprimer cette étape et tous ses objectifs ?')) return;

        const result = await apiCall('/admin/quests/stage/delete', { id: stageId });
        if (result) {
            location.reload();
        }
    }

    function addObjective(stageId) {
        document.getElementById('objective-modal-title').textContent = 'Ajouter un Objectif';
        document.getElementById('objective-form').reset();
        document.getElementById('objective-id').value = '';
        document.getElementById('objective-stage-id').value = stageId;
        document.getElementById('objective-modal').classList.add('active');
    }

    function editObjective(objectiveId) {
        const objectives = <?= json_encode($objectives ?? []) ?>;
        const objective = objectives.find(o => o.id == objectiveId);

        if (!objective) {
            alert('Objectif non trouvé');
            return;
        }

        document.getElementById('objective-modal-title').textContent = 'Éditer l\'Objectif';
        document.getElementById('objective-id').value = objectiveId;
        document.getElementById('objective-stage-id').value = objective.stage_id;
        document.getElementById('objective-type').value = objective.type;
        document.getElementById('objective-description').value = objective.description;
        document.getElementById('objective-count').value = objective.count_required;

        const npcGroup = document.getElementById('objective-npc-group');
        const dialogueGroup = document.getElementById('objective-dialogue-group');
        const targetGroup = document.getElementById('objective-target-group');

        if (objective.type === 'TALK_NPC') {
            npcGroup.style.display = 'block';
            dialogueGroup.style.display = 'block';
            targetGroup.style.display = 'none';

            document.getElementById('objective-npc').value = objective.target_id || '';
            document.getElementById('objective-dialogue').value = objective.dialogue_tree_id || '';
        } else {
            npcGroup.style.display = 'none';
            dialogueGroup.style.display = 'none';
            targetGroup.style.display = 'block';

            document.getElementById('objective-target').value = objective.target_id || '';
        }

        document.getElementById('objective-modal').classList.add('active');
    }

    function closeObjectiveModal() {
        document.getElementById('objective-modal').classList.remove('active');

        document.getElementById('objective-id').value = '';
        document.getElementById('objective-type').value = 'KILL';
        document.getElementById('objective-description').value = '';
        document.getElementById('objective-count').value = '1';
        document.getElementById('objective-target').value = '';
        document.getElementById('objective-npc').value = '';
        document.getElementById('objective-dialogue').value = '';

        document.getElementById('objective-npc-group').style.display = 'none';
        document.getElementById('objective-dialogue-group').style.display = 'none';
        document.getElementById('objective-target-group').style.display = 'block';
    }

    document.getElementById('objective-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const objectiveId = document.getElementById('objective-id').value;
        const objectiveType = document.getElementById('objective-type').value;

        let targetId = null;
        if (objectiveType === 'TALK_NPC') {
            targetId = document.getElementById('objective-npc').value || null;
        } else {
            targetId = document.getElementById('objective-target').value || null;
        }

        const data = {
            stage_id: parseInt(document.getElementById('objective-stage-id').value),
            type: objectiveType,
            description: document.getElementById('objective-description').value,
            count_required: parseInt(document.getElementById('objective-count').value),
            target_id: targetId,
            dialogue_tree_id: document.getElementById('objective-dialogue').value || null
        };

        const url = objectiveId ? '/admin/quests/objective/update' : '/admin/quests/objective/add';
        if (objectiveId) data.id = parseInt(objectiveId);

        const result = await apiCall(url, data);
        if (result) {
            closeObjectiveModal();
            location.reload();
        }
    });

    document.getElementById('objective-type').addEventListener('change', function () {
        const type = this.value;
        const npcGroup = document.getElementById('objective-npc-group');
        const dialogueGroup = document.getElementById('objective-dialogue-group');
        const targetGroup = document.getElementById('objective-target-group');

        if (type === 'TALK_NPC') {
            npcGroup.style.display = 'block';
            dialogueGroup.style.display = 'block';
            targetGroup.style.display = 'none';
        } else {
            npcGroup.style.display = 'none';
            dialogueGroup.style.display = 'none';
            targetGroup.style.display = 'block';
        }
    });

    async function deleteObjective(objectiveId) {
        if (!confirm('Supprimer cet objectif ?')) return;

        const result = await apiCall('/admin/quests/objective/delete', { id: objectiveId });
        if (result) {
            location.reload();
        }
    }

    async function addPrerequisite() {
        const select = document.getElementById('prerequisite-select');
        const requiredQuestId = select.value;

        if (!requiredQuestId) {
            alert('Sélectionnez une quête');
            return;
        }

        const result = await apiCall('/admin/quests/prerequisite/add', {
            quest_id: questId,
            required_quest_id: parseInt(requiredQuestId)
        });

        if (result) {
            location.reload();
        }
    }

    async function removePrerequisite(requiredQuestId) {
        if (!confirm('Retirer ce prérequis ?')) return;

        const result = await apiCall('/admin/quests/prerequisite/remove', {
            quest_id: questId,
            required_quest_id: requiredQuestId
        });

        if (result) {
            location.reload();
        }
    }

    async function addMapUnlock(stageId) {
        const select = document.getElementById('unlock-select-' + stageId);
        const mapPointId = select.value;

        if (!mapPointId) {
            alert('Sélectionnez un lieu');
            return;
        }

        const result = await apiCall('/admin/quests/stage/add-unlock', {
            stage_id: stageId,
            map_point_id: parseInt(mapPointId)
        });

        if (result) {
            location.reload();
        }
    }

    async function removeMapUnlock(stageId, mapPointId) {
        if (!confirm('Retirer ce déblocage ?')) return;

        const result = await apiCall('/admin/quests/stage/remove-unlock', {
            stage_id: stageId,
            map_point_id: mapPointId
        });

        if (result) {
            location.reload();
        }
    }
    async function addRewardItem() {
        const select = document.getElementById('reward-item-select');
        const itemId = select.value;
        const quantity = parseInt(document.getElementById('reward-item-qty').value) || 1;

        if (!itemId) {
            alert('Sélectionnez un objet');
            return;
        }

        const result = await apiCall('/admin/quests/reward/item/add', {
            quest_id: questId,
            item_id: parseInt(itemId),
            quantity: quantity
        });

        if (result) {
            location.reload();
        }
    }

    async function removeRewardItem(id) {
        if (!confirm('Retirer cet objet ?')) return;

        const result = await apiCall('/admin/quests/reward/item/remove', { id: id });

        if (result) {
            location.reload();
        }
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>