<?php
$title = 'Choisir la Difficulté - DungeonXplorer';
$bodyClass = 'bg-gray-900 min-h-screen';

ob_start();
?>

<!-- Background -->
<div class="absolute inset-0 z-0">
    <img src="/assets/images/character_create_bg.png" alt="Background" class="w-full h-full object-cover opacity-40">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-gray-900/80 to-gray-900"></div>
</div>

<div class="relative z-10 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <h1 class="text-3xl md:text-5xl font-bold text-center text-white mb-2">Choisir votre Destinée</h1>
        <p class="text-center text-gray-400 mb-10 text-lg">Le chemin que vous empruntez déterminera vos épreuves.</p>

        <form action="/personnage/difficulty" method="POST" id="difficulty-form">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                
                <!-- Adventure -->
                <label class="cursor-pointer group">
                    <input type="radio" name="difficulty" value="STORY" class="peer sr-only">
                    <div class="h-full bg-gray-800/80 backdrop-blur border-2 border-gray-700 rounded-xl p-6 transition-all duration-300 peer-checked:border-green-500 peer-checked:bg-green-900/20 group-hover:border-green-400 group-hover:-translate-y-1">
                        <div class="w-12 h-12 bg-green-500/20 text-green-400 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">📖</div>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-green-300">Aventure Narrative</h3>
                        <p class="text-sm text-gray-400 mb-4 h-10">Pour ceux qui veulent se concentrer sur l'histoire et l'exploration.</p>
                        <ul class="text-xs space-y-2 text-gray-300">
                            <li class="flex items-center gap-2"><span class="text-green-400">▼</span> Dégâts ennemis -30%</li>
                            <li class="flex items-center gap-2"><span class="text-green-400">▲</span> XP +25%</li>
                            <li class="flex items-center gap-2"><span class="text-green-400">▲</span> Taux Critique Augmenté</li>
                        </ul>
                    </div>
                </label>

                <!-- Normal -->
                <label class="cursor-pointer group">
                    <input type="radio" name="difficulty" value="NORMAL" class="peer sr-only" checked>
                    <div class="h-full bg-gray-800/80 backdrop-blur border-2 border-gray-700 rounded-xl p-6 transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-900/20 group-hover:border-blue-400 group-hover:-translate-y-1">
                        <div class="w-12 h-12 bg-blue-500/20 text-blue-400 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">⚔️</div>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-300">Épreuve Classique</h3>
                        <p class="text-sm text-gray-400 mb-4 h-10">L'expérience équilibrée et standard du jeu.</p>
                        <ul class="text-xs space-y-2 text-gray-300">
                            <li class="flex items-center gap-2">Stats Standards (100%)</li>
                            <li class="flex items-center gap-2">Sauvegarde Standard</li>
                            <li class="flex items-center gap-2">Recommandé pour débuter</li>
                        </ul>
                    </div>
                </label>

                <!-- Heroic -->
                <label class="cursor-pointer group">
                    <input type="radio" name="difficulty" value="HEROIC" class="peer sr-only">
                    <div class="h-full bg-gray-800/80 backdrop-blur border-2 border-gray-700 rounded-xl p-6 transition-all duration-300 peer-checked:border-orange-500 peer-checked:bg-orange-900/20 group-hover:border-orange-400 group-hover:-translate-y-1">
                        <div class="w-12 h-12 bg-orange-500/20 text-orange-400 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">🔥</div>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-orange-300">Défi Héroïque</h3>
                        <p class="text-sm text-gray-400 mb-4 h-10">Pour les vétérans cherchant un challenge exigeant.</p>
                        <ul class="text-xs space-y-2 text-gray-300">
                            <li class="flex items-center gap-2"><span class="text-red-400">▲</span> Dégâts ennemis +20%</li>
                            <li class="flex items-center gap-2"><span class="text-red-400">▼</span> Fuites plus difficiles</li>
                            <li class="flex items-center gap-2"><span class="text-red-400">▲</span> Prix marchands +15%</li>
                        </ul>
                    </div>
                </label>

                <!-- IronMan (Destin de Fer) -->
                <label class="cursor-pointer group">
                    <input type="radio" name="difficulty" value="IRONMAN" class="peer sr-only" id="diff-ironman">
                    <div class="h-full bg-gray-800/80 backdrop-blur border-2 border-gray-700 rounded-xl p-6 transition-all duration-300 peer-checked:border-red-600 peer-checked:bg-red-900/20 group-hover:border-red-500 group-hover:-translate-y-1 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-600/20 rounded-full blur-xl group-hover:bg-red-600/40 transition"></div>
                        <div class="w-12 h-12 bg-red-600/20 text-red-500 rounded-lg flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">💀</div>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-red-400">Le Destin de Fer</h3>
                        <p class="text-sm text-gray-400 mb-4 h-10">La gloire ou la mort. Aucune seconde chance.</p>
                        <ul class="text-xs space-y-2 text-gray-300">
                            <li class="flex items-center gap-2"><span class="text-red-500 font-bold">⚠️ MORT PERMANENTE</span></li>
                            <li class="flex items-center gap-2"><span class="text-red-400">▲</span> Dégâts ennemis +35%</li>
                            <li class="flex items-center gap-2"><span class="text-red-400">▼</span> Butin réduit</li>
                        </ul>
                    </div>
                </label>
            </div>

            <!-- Options Supplementaires -->
            <div class="max-w-xl mx-auto mb-10">
                <label class="flex items-center justify-between bg-gray-800/50 border border-gray-700 rounded-lg p-4 cursor-pointer hover:bg-gray-800 transition" id="ironman-option-container">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded bg-red-900/30 text-red-500 flex items-center justify-center border border-red-500/30">
                            <i class="fas fa-skull"></i>
                        </div>
                        <div>
                            <span class="block text-white font-bold">Mode IronMan</span>
                            <span class="text-sm text-gray-400">Ajouter la mort permanente à n'importe quelle difficulté.</span>
                        </div>
                    </div>
                    <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="is_ironman" id="checkbox-ironman" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"/>
                        <label for="checkbox-ironman" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                    </div>
                </label>
            </div>

            <style>
                /* Custom Toggle Switch */
                .toggle-checkbox:checked {
                    right: 0;
                    border-color: #ef4444;
                }
                .toggle-checkbox {
                    right: calc(100% - 1.5rem);
                    transition: all 0.3s;
                    border-color: #4b5563;
                }
                .toggle-label {
                    width: 3rem;
                }
                .toggle-checkbox:checked + .toggle-label {
                    background-color: #ef4444;
                }
            </style>

            <div class="flex justify-center gap-4">
                 <a href="/personnage/apparence/preview" class="px-8 py-3 rounded-lg border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 transition font-medium">
                    Retour
                </a>
                <button type="submit" class="px-10 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg hover:shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                    Commencer l'Aventure
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const diffRadios = document.querySelectorAll('input[name="difficulty"]');
    const ironmanCheckbox = document.getElementById('checkbox-ironman');
    const ironmanContainer = document.getElementById('ironman-option-container');

    diffRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'IRONMAN') {
                // Force check IronMan and disable toggle
                ironmanCheckbox.checked = true;
                ironmanCheckbox.disabled = true;
                ironmanContainer.style.opacity = '0.5';
                ironmanContainer.title = "Inclus dans 'Le Destin de Fer'";
            } else {
                // Re-enable toggle
                ironmanCheckbox.disabled = false;
                ironmanContainer.style.opacity = '1';
                ironmanContainer.title = "";
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
