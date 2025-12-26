<?php
$pageTitle = 'Gestion des Utilisateurs';
ob_start();
?>

<div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
    <h3 class="text-xl font-semibold mb-6">Gestion des Utilisateurs</h3>

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
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Pseudo</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Email</th>
                    <th class="px-6 py-4 text-center text-slate-400 text-sm uppercase font-semibold">Personnages</th>
                    <th class="px-6 py-4 text-left text-slate-400 text-sm uppercase font-semibold">Inscrit le</th>
                    <th class="px-6 py-4 text-right text-slate-400 text-sm uppercase font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-t border-slate-800 hover:bg-indigo-500/5 transition-colors">
                        <td class="px-6 py-4 text-slate-400">#<?= $user['id'] ?></td>
                        <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="px-6 py-4 text-slate-400"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-6 py-4 text-center">
                            <a href="/admin/characters?user_id=<?= $user['id'] ?>"
                                class="inline-block bg-indigo-500/20 text-indigo-300 text-sm px-3 py-1 rounded-lg hover:bg-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                                <?= $user['character_count'] ?> perso<?= $user['character_count'] > 1 ? 's' : '' ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-sm">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button
                                onclick="resetPassword(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                class="inline-block px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-sm font-medium rounded-lg transition-all transform hover:-translate-y-0.5">
                                🔑 Reset MDP
                            </button>
                            <form action="/admin/users/delete/<?= $user['id'] ?>" method="POST" class="inline"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur et TOUTES ses données ?');">
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

<!-- Reset Password Modal -->
<div id="resetModal" class="hidden fixed inset-0 bg-black/75 z-[1000] flex items-center justify-center">
    <div class="bg-slate-900 p-8 rounded-xl max-w-md w-11/12 border border-slate-800">
        <h3 class="text-xl font-semibold mb-4">Réinitialisation MDP</h3>
        <p class="text-slate-400 mb-6">
            Génération d'un code de réinitialisation pour <span id="resetUsername"
                class="font-semibold text-indigo-400"></span>.
        </p>

        <div id="resetLoading" class="hidden text-center py-8">
            <div class="inline-block w-10 h-10 border-3 border-slate-700 border-t-indigo-500 rounded-full animate-spin">
            </div>
        </div>

        <div id="resetResult" class="hidden">
            <div class="bg-slate-950 p-6 rounded-lg mb-6 text-center border-2 border-indigo-500">
                <p class="text-sm text-slate-400 mb-2">Code (valide 24h) :</p>
                <p id="resetCode" class="text-3xl font-mono font-bold tracking-[0.5rem] text-indigo-400">------</p>
            </div>
            <p class="text-sm text-slate-400 mb-6">
                Communiquez ce code à l'utilisateur. Il devra l'utiliser sur la page de connexion via "Mot de passe
                oublié".
            </p>
        </div>

        <div class="text-right">
            <button onclick="closeModal()"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-all transform hover:-translate-y-0.5">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
    function resetPassword(userId, username) {
        const modal = document.getElementById('resetModal');
        const usernameSpan = document.getElementById('resetUsername');
        const loading = document.getElementById('resetLoading');
        const result = document.getElementById('resetResult');
        const codeDisplay = document.getElementById('resetCode');

        usernameSpan.textContent = username;
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        result.classList.add('hidden');

        fetch(`/admin/users/reset-password/${userId}`, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                loading.classList.add('hidden');
                if (data.success) {
                    codeDisplay.textContent = data.code;
                    result.classList.remove('hidden');
                } else {
                    alert('Erreur: ' + data.message);
                    closeModal();
                }
            })
            .catch(err => {
                loading.classList.add('hidden');
                alert('Erreur réseau');
                closeModal();
            });
    }

    function closeModal() {
        document.getElementById('resetModal').classList.add('hidden');
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>