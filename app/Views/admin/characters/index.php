<?php
$pageTitle = 'Gestion des Personnages';
ob_start();
?>

<div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
    <h3 class="text-xl font-semibold mb-6">Gestion des Personnages</h3>

    <!-- Filters -->
    <form action="/admin/characters" method="GET"
        class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-slate-400 text-sm mb-2 font-medium">Nom</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>"
                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-indigo-500 transition-colors"
                placeholder="Rechercher...">
        </div>
        <div>
            <label class="block text-slate-400 text-sm mb-2 font-medium">Classe</label>
            <select name="class_id"
                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-indigo-500 transition-colors">
                <option value="">Toutes</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= ($_GET['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-slate-400 text-sm mb-2 font-medium">Niveau</label>
            <input type="number" name="level" value="<?= htmlspecialchars($_GET['level'] ?? '') ?>"
                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-indigo-500 transition-colors"
                placeholder="Niveau...">
        </div>
        <div>
            <label class="block text-slate-400 text-sm mb-2 font-medium">Propriétaire</label>
            <select name="user_id"
                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-indigo-500 transition-colors">
                <option value="">Tous</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= ($_GET['user_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit"
                class="w-full px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all transform hover:-translate-y-0.5">
                🔍 Filtrer
            </button>
        </div>
    </form>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-900/20 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
            Opération réussie.
        </div>
    <?php endif; ?>

    <div class="bg-slate-900 rounded-lg overflow-hidden border border-slate-800">
        <table class="w-full">
            <thead class="bg-slate-950">
                <tr>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">ID</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Nom</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Classe</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Niveau</th>
                    <th class="px-6 py-4 text-center text-slate-400 text-sm uppercase font-semibold">Gold</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Propriétaire</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Créé le</th>
                    <th class="px-6 py-4 text-right text-slate-400 text-sm uppercase font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($characters as $char): ?>
                    <tr class="border-t border-slate-800 hover:bg-indigo-500/5 transition-colors">
                        <td class="px-6 py-4 text-slate-400">#<?= $char['id'] ?></td>
                        <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($char['name']) ?></td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-block bg-purple-500/20 text-purple-300 px-3 py-1 rounded-lg text-sm font-medium">
                                <?= htmlspecialchars($char['class_name']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-block bg-green-500/20 text-green-300 px-3 py-1 rounded-lg text-sm font-medium">
                                Niv. <?= $char['level'] ?? 1 ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center gap-1 bg-yellow-500/20 text-yellow-300 text-sm px-3 py-1 rounded-lg font-medium">
                                🪙 <?= number_format($char['gold'] ?? 0, 0, ',', ' ') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="/admin/users?id=<?= $char['user_id'] ?>" class="text-blue-400 hover:underline">
                                <?= htmlspecialchars($char['username']) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-sm">
                            <?= date('d/m/Y', strtotime($char['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="/admin/characters/delete/<?= $char['id'] ?>" method="POST" class="inline"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce personnage ?');">
                                <button type="submit"
                                    class="inline-block px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-lg transition-all transform hover:-translate-y-0.5">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>