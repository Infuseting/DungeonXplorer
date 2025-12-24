<?php
$pageTitle = "Gestion des Quêtes Quotidiennes";
ob_start();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1>⏰ Gestion des Quêtes Quotidiennes</h1>
        <p class="text-slate-400 mt-2">Pool de quêtes attribuées aléatoirement aux joueurs chaque jour</p>
    </div>
    <div class="flex gap-4">
        <a href="/admin/quests" class="btn bg-slate-700 hover:bg-slate-600">📜 Quêtes d'Histoire</a>
        <button onclick="openCreateModal()" class="btn btn-primary">➕ Nouvelle Quête Quotidienne</button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="text-3xl font-bold text-indigo-400"><?= count($dailyQuests) ?></div>
        <div class="text-slate-400 text-sm mt-1">Quêtes dans le pool</div>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="text-3xl font-bold text-green-400"><?= count(array_filter($dailyQuests, fn($q) => $q['is_active'])) ?></div>
        <div class="text-slate-400 text-sm mt-1">Quêtes actives</div>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="text-3xl font-bold text-yellow-400"><?= $stats['total_assigned'] ?? 0 ?></div>
        <div class="text-slate-400 text-sm mt-1">Assignées aujourd'hui</div>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="text-3xl font-bold text-purple-400"><?= $stats['total_completed'] ?? 0 ?></div>
        <div class="text-slate-400 text-sm mt-1">Complétées aujourd'hui</div>
    </div>
</div>

<!-- Daily Quests Table -->
<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-950">
            <tr>
                <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">ID</th>
                <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Nom</th>
                <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Type d'Objectif</th>
                <th class="px-6 py-4 text-center text-slate-400 text-sm uppercase font-semibold">Objectif</th>
                <th class="px-6 py-4 text-center text-slate-400 text-sm uppercase font-semibold">Récompense</th>
                <th class="px-6 py-4 text-center text-slate-400 text-sm uppercase font-semibold">Statut</th>
                <th class="px-6 py-4 text-right text-slate-400 text-sm uppercase font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dailyQuests)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        Aucune quête quotidienne. Créez-en une pour commencer.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($dailyQuests as $quest): ?>
                    <tr class="border-t border-slate-800 hover:bg-indigo-500/5 transition-colors">
                        <td class="px-6 py-4 text-slate-400">#<?= $quest['id'] ?></td>
                        <td class="px-6 py-4">
                            <div class="font-semibold"><?= htmlspecialchars($quest['name']) ?></div>
                            <div class="text-slate-400 text-sm"><?= htmlspecialchars(substr($quest['description'], 0, 60)) ?>...</div>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $typeLabels = [
                                'KILL_MONSTERS' => ['⚔️ Tuer des monstres', 'bg-red-500/20 text-red-300'],
                                'COLLECT_GOLD' => ['🪙 Collecter de l\'or', 'bg-yellow-500/20 text-yellow-300'],
                                'COMPLETE_DUNGEON' => ['🏰 Compléter un donjon', 'bg-purple-500/20 text-purple-300'],
                                'VISIT_LOCATIONS' => ['🗺️ Visiter des lieux', 'bg-blue-500/20 text-blue-300'],
                                'USE_ITEMS' => ['🧪 Utiliser des items', 'bg-green-500/20 text-green-300'],
                            ];
                            $type = $typeLabels[$quest['objective_type']] ?? ['❓ Inconnu', 'bg-slate-500/20 text-slate-300'];
                            ?>
                            <span class="inline-block px-3 py-1 rounded-lg text-sm <?= $type[1] ?>">
                                <?= $type[0] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-mono text-lg"><?= $quest['objective_count'] ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-lg">
                                🪙 <?= $quest['gold_reward'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($quest['is_active']): ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-green-500/20 text-green-300">Actif</span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-slate-500/20 text-slate-400">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($quest)) ?>)" class="inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-all">
                                ✏️ Éditer
                            </button>
                            <button onclick="toggleActive(<?= $quest['id'] ?>, <?= $quest['is_active'] ? 'false' : 'true' ?>)" class="inline-block px-4 py-2 <?= $quest['is_active'] ? 'bg-amber-600 hover:bg-amber-500' : 'bg-green-600 hover:bg-green-500' ?> text-white text-sm font-medium rounded-lg transition-all">
                                <?= $quest['is_active'] ? '⏸️' : '▶️' ?>
                            </button>
                            <button onclick="deleteQuest(<?= $quest['id'] ?>)" class="inline-block px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-lg transition-all">
                                🗑️
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Statistiques des joueurs -->
<div class="mt-8 bg-slate-900 border border-slate-800 rounded-xl p-6">
    <h3 class="text-xl font-semibold mb-6">📊 Activité des Joueurs (Aujourd'hui)</h3>
    
    <?php if (empty($playerStats)): ?>
        <p class="text-slate-400 text-center py-8">Aucune activité aujourd'hui.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-950">
                    <tr>
                        <th class="px-4 py-3 text-left text-slate-400 text-sm">Personnage</th>
                        <th class="px-4 py-3 text-left text-slate-400 text-sm">Utilisateur</th>
                        <th class="px-4 py-3 text-center text-slate-400 text-sm">Quêtes Assignées</th>
                        <th class="px-4 py-3 text-center text-slate-400 text-sm">Complétées</th>
                        <th class="px-4 py-3 text-center text-slate-400 text-sm">Réclamées</th>
                        <th class="px-4 py-3 text-center text-slate-400 text-sm">Or Gagné</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerStats as $stat): ?>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 font-medium"><?= htmlspecialchars($stat['character_name']) ?></td>
                            <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($stat['username']) ?></td>
                            <td class="px-4 py-3 text-center"><?= $stat['total_assigned'] ?></td>
                            <td class="px-4 py-3 text-center text-green-400"><?= $stat['total_completed'] ?></td>
                            <td class="px-4 py-3 text-center text-purple-400"><?= $stat['total_claimed'] ?></td>
                            <td class="px-4 py-3 text-center text-yellow-400">🪙 <?= $stat['gold_earned'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div id="questModal" class="hidden fixed inset-0 bg-black/75 z-[1000] flex items-center justify-center">
    <div class="bg-slate-900 p-8 rounded-xl max-w-lg w-11/12 border border-slate-800">
        <h3 id="modalTitle" class="text-xl font-semibold mb-6">Nouvelle Quête Quotidienne</h3>
        
        <form id="questForm" method="POST">
            <input type="hidden" id="questId" name="id" value="">
            
            <div class="mb-4">
                <label class="block text-slate-400 text-sm mb-2">Nom de la quête</label>
                <input type="text" id="questName" name="name" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3 focus:border-indigo-500 focus:outline-none">
            </div>
            
            <div class="mb-4">
                <label class="block text-slate-400 text-sm mb-2">Description</label>
                <textarea id="questDescription" name="description" required rows="3" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3 focus:border-indigo-500 focus:outline-none"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-slate-400 text-sm mb-2">Type d'objectif</label>
                    <select id="questType" name="objective_type" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3 focus:border-indigo-500 focus:outline-none">
                        <option value="KILL_MONSTERS">⚔️ Tuer des monstres</option>
                        <option value="COLLECT_GOLD">🪙 Collecter de l'or</option>
                        <option value="COMPLETE_DUNGEON">🏰 Compléter un donjon</option>
                        <option value="VISIT_LOCATIONS">🗺️ Visiter des lieux</option>
                        <option value="USE_ITEMS">🧪 Utiliser des items</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 text-sm mb-2">Quantité requise</label>
                    <input type="number" id="questCount" name="objective_count" required min="1" value="1" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3 focus:border-indigo-500 focus:outline-none">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-slate-400 text-sm mb-2">Récompense (pièces d'or)</label>
                <input type="number" id="questReward" name="gold_reward" required min="1" value="5" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3 focus:border-indigo-500 focus:outline-none">
            </div>
            
            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-all">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Nouvelle Quête Quotidienne';
        document.getElementById('questForm').action = '/admin/quests/daily/create';
        document.getElementById('questId').value = '';
        document.getElementById('questName').value = '';
        document.getElementById('questDescription').value = '';
        document.getElementById('questType').value = 'KILL_MONSTERS';
        document.getElementById('questCount').value = '1';
        document.getElementById('questReward').value = '5';
        document.getElementById('questModal').classList.remove('hidden');
    }
    
    function openEditModal(quest) {
        document.getElementById('modalTitle').textContent = 'Modifier la Quête';
        document.getElementById('questForm').action = '/admin/quests/daily/edit/' + quest.id;
        document.getElementById('questId').value = quest.id;
        document.getElementById('questName').value = quest.name;
        document.getElementById('questDescription').value = quest.description;
        document.getElementById('questType').value = quest.objective_type;
        document.getElementById('questCount').value = quest.objective_count;
        document.getElementById('questReward').value = quest.gold_reward;
        document.getElementById('questModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('questModal').classList.add('hidden');
    }
    
    function toggleActive(id, newState) {
        fetch('/admin/quests/daily/toggle/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_active: newState })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
    
    function deleteQuest(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette quête quotidienne ?')) return;
        
        fetch('/admin/quests/daily/delete/' + id, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once dirname(__DIR__, 2) . '/layouts/admin.php';
?>
