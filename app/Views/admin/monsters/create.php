<?php
$pageTitle = 'Créer un Monstre';
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="/admin/monsters" class="text-gray-400 hover:text-white transition-colors">
            ← Retour
        </a>
        <h1 class="text-2xl font-bold">Nouveau Monstre</h1>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
        <form action="/admin/monsters/create" method="POST" class="space-y-6">
            
            <!-- Informations de base -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-indigo-400 border-b border-gray-700 pb-2">Informations Générales</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Nom du Monstre</label>
                        <input type="text" name="name" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Niveau Min</label>
                            <input type="number" name="level_min" value="1" min="1" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Niveau Max</label>
                            <input type="number" name="level_max" value="10" min="1" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Chemin Image (Monstre)</label>
                        <input type="text" name="image_path" placeholder="/assets/images/monsters/..."
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Chemin Image (Salle Default)</label>
                        <input type="text" name="salle_path" placeholder="/assets/images/salles/..."
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-rose-400 border-b border-gray-700 pb-2">Statistiques de Combat</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Attaque</label>
                            <input type="number" name="attaque" value="10" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Défense</label>
                            <input type="number" name="defense" value="5" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Force</label>
                            <input type="number" name="strength" value="10" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Vitalité</label>
                            <input type="number" name="vitality" value="10" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Dextérité</label>
                            <input type="number" name="dexterity" value="10" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Intelligence</label>
                            <input type="number" name="intelligence" value="10" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-yellow-400 border-b border-gray-700 pb-2 pt-4">Récompenses</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">XP Donnée</label>
                            <input type="number" name="xp" value="50" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Or Donné</label>
                            <input type="number" name="gold" value="10" 
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-800">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-lg transition-colors">
                    Créer le Monstre
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/admin.php';
?>
