<?php
$pageTitle = 'Modifier Monstre';
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="/admin/monsters" class="text-gray-400 hover:text-white transition-colors">
            ← Retour
        </a>
        <h1 class="text-2xl font-bold">Modifier: <?= htmlspecialchars($monster['name']) ?></h1>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
        <form action="/admin/monsters/edit/<?= $monster['id'] ?>" method="POST" class="space-y-6">
            
            <!-- Informations de base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-indigo-400 border-b border-gray-700 pb-2">Informations Générales</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Nom du Monstre</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($monster['name']) ?>" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Niveau Min</label>
                            <input type="number" name="level_min" value="<?= $monster['level_min'] ?>" min="1" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Niveau Max</label>
                            <input type="number" name="level_max" value="<?= $monster['level_max'] ?>" min="1" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Chemin Image (Monstre)</label>
                        <input type="text" name="image_path" value="<?= htmlspecialchars($monster['image_path'] ?? '') ?>" placeholder="/assets/images/monsters/..."
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Chemin Image (Salle Default)</label>
                        <input type="text" name="salle_path" value="<?= htmlspecialchars($monster['salle_path'] ?? '') ?>" placeholder="/assets/images/salles/..."
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Type de Créature</label>
                        <select name="creature_type" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                            <?php 
                            $types = ['neutral' => 'Neutre', 'undead' => 'Mort-Vivant', 'humanoid' => 'Humanoïde', 'beast' => 'Bête', 'demon' => 'Démon', 'dragon' => 'Dragon', 'elemental' => 'Élémentaire'];
                            $current = $monster['creature_type'] ?? 'neutral';
                            foreach ($types as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $current === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Affinités -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-purple-400 border-b border-gray-700 pb-2">Forces & Faiblesses</h3>
                    
                    <div id="affinities-container" class="space-y-3">
                        <?php 
                        $affinities = $monster['affinities_data'] ?? [];
                        foreach ($affinities as $element => $data): 
                             // Simplify old numeric Structure if present, but we defined it as [type, value]
                             $type = $data['type'] ?? 'percent';
                             $value = $data['value'] ?? 0;
                        ?>
                        <div class="flex items-center gap-2 bg-gray-800/50 p-2 rounded border border-gray-700">
                            <select name="affinity_element[]" class="bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white focus:ring-1 focus:ring-purple-500">
                                <?php 
                                $elements = ['physical' => 'Physique', 'fire' => 'Feu', 'ice' => 'Glace', 'lightning' => 'Foudre', 'holy' => 'Sacré', 'shadow' => 'Ombre', 'poison' => 'Poison'];
                                foreach ($elements as $elVal => $elLabel): ?>
                                    <option value="<?= $elVal ?>" <?= $element === $elVal ? 'selected' : '' ?>><?= $elLabel ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <input type="number" name="affinity_value[]" placeholder="Val" value="<?= $value ?>" step="any"
                                   class="w-20 bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white">
                                   
                            <select name="affinity_type[]" class="bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white">
                                <option value="percent" <?= $type === 'percent' ? 'selected' : '' ?>>%</option>
                                <option value="flat" <?= $type === 'flat' ? 'selected' : '' ?>>Fixe</option>
                            </select>
                            
                            <button type="button" onclick="this.closest('div').remove()" class="text-red-400 hover:text-red-300 px-2">×</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" onclick="addAffinityRow()" 
                            class="text-sm bg-gray-800 hover:bg-gray-700 text-purple-300 border border-purple-900/50 px-3 py-1 rounded transition-colors flex items-center gap-1">
                        <span>+</span> Ajouter une affinité
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Négatif = Résistance (ex: -50%), Positif = Faiblesse (ex: +50%)</p>
                </div>

                <script>
                function addAffinityRow() {
                    const container = document.getElementById('affinities-container');
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2 bg-gray-800/50 p-2 rounded border border-gray-700';
                    div.innerHTML = `
                        <select name="affinity_element[]" class="bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white focus:ring-1 focus:ring-purple-500">
                             <option value="physical">Physique</option>
                            <option value="fire">Feu</option>
                            <option value="ice">Glace</option>
                            <option value="lightning">Foudre</option>
                            <option value="holy">Sacré</option>
                            <option value="shadow">Ombre</option>
                            <option value="poison">Poison</option>
                        </select>
                        
                        <input type="number" name="affinity_value[]" placeholder="Val" value="0" step="any"
                               class="w-20 bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white">
                               
                        <select name="affinity_type[]" class="bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white">
                            <option value="percent">%</option>
                            <option value="flat">Fixe</option>
                        </select>
                        
                        <button type="button" onclick="this.closest('div').remove()" class="text-red-400 hover:text-red-300 px-2">×</button>
                    `;
                    container.appendChild(div);
                }
                </script>

                <!-- Statistiques -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-rose-400 border-b border-gray-700 pb-2">Statistiques de Combat</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Attaque</label>
                            <input type="number" name="attaque" value="<?= $monster['stats']['attaque'] ?? 10 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Défense</label>
                            <input type="number" name="defense" value="<?= $monster['stats']['defense'] ?? 5 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Force</label>
                            <input type="number" name="strength" value="<?= $monster['stats']['strength'] ?? 10 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Vitalité</label>
                            <input type="number" name="vitality" value="<?= $monster['stats']['vitality'] ?? 10 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Dextérité</label>
                            <input type="number" name="dexterity" value="<?= $monster['stats']['dexterity'] ?? 10 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Intelligence</label>
                            <input type="number" name="intelligence" value="<?= $monster['stats']['intelligence'] ?? 10 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-yellow-400 border-b border-gray-700 pb-2 pt-4">Récompenses</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">XP Donnée</label>
                            <input type="number" name="xp" value="<?= $monster['stats']['xp'] ?? 50 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Or Donné</label>
                            <input type="number" name="gold" value="<?= $monster['stats']['gold'] ?? 10 ?>" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-800">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-lg transition-colors">
                    Sauvegarder
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
