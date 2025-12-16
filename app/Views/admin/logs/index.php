<?php
$title = "Diagnostic Logs";
ob_start();
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold">Diagnostic Logs</h1>
</div>

<!-- Filters -->
<div class="bg-gray-800 p-4 rounded-lg mb-6">
    <form method="GET" action="/admin/logs" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        
        <!-- Category -->
        <div>
            <label class="block text-sm font-medium mb-1">Catégorie</label>
            <select name="category" class="w-full bg-gray-700 rounded px-3 py-2">
                <option value="">Toutes</option>
                <?php foreach(['CRITICAL', 'GAMEPLAY', 'SECURITY', 'SYSTEM'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= ($_GET['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Action Type -->
        <div>
            <label class="block text-sm font-medium mb-1">Action Type</label>
            <input type="text" name="action_type" value="<?= htmlspecialchars($_GET['action_type'] ?? '') ?>" class="w-full bg-gray-700 rounded px-3 py-2" placeholder="ex: NPC_BUY">
        </div>

        <!-- User ID -->
        <div>
            <label class="block text-sm font-medium mb-1">User ID</label>
            <input type="number" name="user_id" value="<?= htmlspecialchars($_GET['user_id'] ?? '') ?>" class="w-full bg-gray-700 rounded px-3 py-2">
        </div>

        <!-- Character ID -->
        <div>
            <label class="block text-sm font-medium mb-1">Char ID</label>
            <input type="number" name="character_id" value="<?= htmlspecialchars($_GET['character_id'] ?? '') ?>" class="w-full bg-gray-700 rounded px-3 py-2">
        </div>

        <!-- Dates -->
        <div>
            <label class="block text-sm font-medium mb-1">De</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>" class="w-full bg-gray-700 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">À</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>" class="w-full bg-gray-700 rounded px-3 py-2">
        </div>

        <!-- Submit -->
        <div class="md:col-span-6 flex justify-end">
            <a href="/admin/logs" class="bg-gray-600 px-4 py-2 rounded mr-2 hover:bg-gray-500">Reset</a>
            <button type="submit" class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-500">Filtrer</button>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-gray-800 rounded-lg overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-700 text-gray-300">
            <tr>
                <th class="p-3">Date</th>
                <th class="p-3">Catégorie</th>
                <th class="p-3">Action</th>
                <th class="p-3">User</th>
                <th class="p-3">Perso</th>
                <th class="p-3">Détails</th>
                <th class="p-3">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">Aucun log trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-750">
                        <td class="p-3 text-sm text-gray-400"><?= $log['created_at'] ?></td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs font-bold 
                                <?= $log['category'] === 'CRITICAL' ? 'bg-red-900 text-red-200' : 
                                   ($log['category'] === 'SECURITY' ? 'bg-orange-900 text-orange-200' : 
                                   ($log['category'] === 'GAMEPLAY' ? 'bg-blue-900 text-blue-200' : 'bg-gray-600')) ?>">
                                <?= $log['category'] ?>
                            </span>
                        </td>
                        <td class="p-3 font-mono text-sm"><?= htmlspecialchars($log['action_type']) ?></td>
                        <td class="p-3 text-sm">
                            <?php if($log['user_id']): ?>
                                <span title="ID: <?= $log['user_id'] ?>"><?= htmlspecialchars($log['username'] ?? 'Unknown') ?></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-sm">
                             <?php if($log['character_id']): ?>
                                <span title="ID: <?= $log['character_id'] ?>"><?= htmlspecialchars($log['character_name'] ?? 'Unknown') ?></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-xs font-mono w-1/3">
                            <?php 
                                $details = json_decode($log['details'], true);
                                if ($details) {
                                    echo "<pre class='whitespace-pre-wrap'>" . json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                                } else {
                                    echo "-";
                                }
                            ?>
                        </td>
                        <td class="p-3 text-xs text-gray-500"><?= htmlspecialchars($log['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center gap-2">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&<?= http_build_query(array_merge($_GET, ['page' => null])) ?>" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">Précédent</a>
    <?php endif; ?>
    
    <span class="px-3 py-1 text-gray-400">Page <?= $page ?> / <?= max(1, $totalPages) ?></span>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?>&<?= http_build_query(array_merge($_GET, ['page' => null])) ?>" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">Suivant</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../templates/admin_layout.php';
?>
