<?php
$pageTitle = 'Gestion des Maisons';
ob_start();
?>

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="card-header mb-0">🏠 Gestion des Maisons</h3>
        <a href="/admin/houses/create" class="btn btn-primary">
            ➕ Créer une Maison
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-900/50 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            <?php
            $messages = [
                'created' => '✅ Maison créée avec succès',
                'updated' => '✅ Maison mise à jour',
                'deleted' => '✅ Maison supprimée'
            ];
            echo $messages[$_GET['success']] ?? '✅ Opération réussie';
            ?>
        </div>
    <?php endif; ?>

    <!-- Houses Grid -->
    <?php if (!empty($houses)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($houses as $house): ?>
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:border-amber-500 hover:-translate-y-0.5 hover:shadow-lg">
                    <!-- Image -->
                    <div class="h-40 bg-gray-900 flex items-center justify-center">
                        <?php if ($house['image']): ?>
                            <img src="/<?= htmlspecialchars($house['image']) ?>" alt="<?= htmlspecialchars($house['name']) ?>" class="max-h-full max-w-full object-contain">
                        <?php else: ?>
                            <span class="text-6xl">🏠</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-bold text-white"><?= htmlspecialchars($house['name']) ?></h4>
                            <span class="px-2 py-1 text-xs rounded <?= $house['is_available'] ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400' ?>">
                                <?= $house['is_available'] ? 'Disponible' : 'Indisponible' ?>
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-400 mb-3 line-clamp-2"><?= htmlspecialchars($house['description'] ?? 'Pas de description') ?></p>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                            <div class="text-gray-400">🪙 Prix:</div>
                            <div class="text-amber-400 font-bold"><?= number_format($house['price'], 0, ',', ' ') ?> or</div>
                            
                            <div class="text-gray-400">📦 Stockage:</div>
                            <div class="text-white"><?= $house['storage_slots'] ?> slots</div>
                            
                            <div class="text-gray-400">🪑 Meubles:</div>
                            <div class="text-white"><?= $house['furniture_slots'] ?> max</div>
                            
                            <div class="text-gray-400">⭐ Niveau requis:</div>
                            <div class="text-white"><?= $house['required_level'] ?></div>
                        </div>
                        
                        <?php if ($house['location_name']): ?>
                            <div class="text-xs text-gray-500 mb-3">📍 <?= htmlspecialchars($house['location_name']) ?></div>
                        <?php endif; ?>
                        
                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="/admin/houses/edit/<?= $house['id'] ?>" class="btn btn-sm btn-secondary flex-1">
                                ✏️ Modifier
                            </a>
                            <form action="/admin/houses/delete/<?= $house['id'] ?>" method="POST" class="flex-1" onsubmit="return confirm('Supprimer cette maison ?')">
                                <button type="submit" class="btn btn-sm btn-danger w-full">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <span class="text-6xl mb-4 block">🏚️</span>
            <p class="text-gray-400">Aucune maison créée</p>
            <a href="/admin/houses/create" class="btn btn-primary mt-4">Créer la première maison</a>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>
