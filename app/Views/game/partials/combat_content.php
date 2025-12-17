<?php
use App\Models\CharacterBuff;
use App\Models\Character;
use App\Models\Stats;
use App\Models\Skill;

// Expected variables:
// $monsterModel (Monster)
// $characterModel (Character)
// $initialData (array|null)
?>
<div id="combat-scene" class="absolute inset-0 w-full h-full bg-gray-900 overflow-y-auto z-50 p-4 md:p-8"
     data-max-hp="<?= (int)$characterModel->getVitality() ?>"
     data-return-story-id="<?= htmlspecialchars($returnStoryId ?? '') ?>"
     data-initial-data="<?= htmlspecialchars(json_encode($initialData ?? null), ENT_QUOTES, 'UTF-8') ?>">

    <!-- Interface Graphique Combat -->
    <div class="flex flex-col md:flex-col justify-center items-center gap-8 max-w-7xl mx-auto py-8">

        <div class="w-full md:w-[70%] h-[500px] bg-gray-700 rounded-lg flex items-center justify-center drop-shadow-[0_0_5px_#a78bfa] relative overflow-hidden">
            <img src="<?= $monsterModel->getSallePath() ?>" class="w-full h-full object-cover absolute inset-0 text-white" id="bg" alt="Décor de salle">

            <div class="absolute top-8 flex flex-col text-justify z-10 w-full px-4">
                <h2 class="text-2xl md:text-3xl font-bold text-white text-center drop-shadow-lg mb-4 bg-black/50 p-2 rounded">VS <?= $monsterModel->getName()?></h2>
                <div id="combat-log" class="text-center bg-black/60 p-2 rounded min-h-[60px] flex flex-col justify-end"></div>
            </div>

            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 text-center w-full">
                <div id="win?"></div>
                <div id='redirectBtn' class="flex justify-center"></div>
            </div>

            <div class="absolute bottom-0 w-full flex justify-center h-[300px] pointer-events-none">
                <img id='monster' src="<?= $monsterModel->getImagePath()?>" alt="Monstre" class="object-contain h-full transition-opacity duration-1000 opacity-100 drop-shadow-2xl" >
            </div>
            
            <div id="dice" class="absolute bottom-3 right-3 text-4xl font-bold text-violet-400 drop-shadow-[0_0_6px_#a78bfa] z-10 bg-black/50 rounded-full w-16 h-16 flex items-center justify-center">
                🎲 
            </div>
        </div>

        <div class="w-full md:w-[70%] bg-gray-800 rounded-lg flex flex-col md:flex-row relative overflow-visible p-4 gap-4 shadow-xl border border-gray-700">

            <!-- Stats à gauche -->
            <div class="w-full md:w-1/3 flex flex-col mb-4 md:mb-0 bg-gray-900/50 rounded-lg p-4">
                <h3 class='text-violet-400 font-bold mb-4 text-center md:text-xl border-b border-gray-700 pb-2'>
                    Stats de <?= htmlspecialchars($characterModel->getName()) ?>
                </h3>

                <ul class="text-sm grid grid-cols-2 gap-4 mx-auto w-full">
                    <li class="flex items-center gap-2 w-full bg-gray-800/50 p-2 rounded">
                        <span class="material-symbols-outlined text-red-500" title="Santé du joueur">favorite</span>
                        <span class="text-xl font-bold text-white" id="player-hp"><?= htmlspecialchars($characterModel->getCurrentHp()); ?></span>
                    </li>

                    <li class="flex items-center gap-2 w-full bg-gray-800/50 p-2 rounded">
                        <span class="material-symbols-outlined text-orange-400" title="Puissance d'attaque">swords</span>
                        <span class="text-xl font-bold text-white"><?= htmlspecialchars($characterModel->getAttaqueClass()); ?></span>
                    </li>

                    <li class="flex items-center gap-2 w-full bg-gray-800/50 p-2 rounded">
                        <span class="material-symbols-outlined text-blue-400" title="Défense du joueur">shield</span>
                        <span class="text-xl font-bold text-white"><?= htmlspecialchars($characterModel->getArmorClass()); ?></span>
                    </li>

                    <li class="flex items-center gap-2 w-full bg-gray-800/50 p-2 rounded">
                        <span class="material-symbols-outlined text-amber-600" title="Force du joueur">fitness_center</span>
                        <span class="text-xl font-bold text-white"><?= htmlspecialchars($characterModel->getStrength()); ?></span>
                    </li>

                    <li class="flex items-center gap-2 w-full bg-gray-800/50 p-2 rounded">
                        <span class="material-symbols-outlined text-purple-400" title="Intelligence du joueur">psychology</span>
                        <span class="text-xl font-bold text-white"><?= htmlspecialchars($characterModel->getIntelligence()); ?></span>
                    </li>

                    <li class="flex items-center gap-2 w-full bg-gray-800/50 p-2 rounded">
                        <span class="material-symbols-outlined text-green-400" title="Dextérié du joueur">directions_run</span>
                        <span class="text-xl font-bold text-white"><?= htmlspecialchars($characterModel->getDexterity()); ?></span>
                    </li>
                </ul>
            </div>

            <!-- Cercle au centre -->
            <div class="w-full md:w-1/3 flex flex-col items-center justify-center relative mb-4 md:mb-0">
                <!-- Avatar -->
                <?php $character =  $characterModel->toArray(); ?>
                <div id='character-container' class="w-40 h-40 md:w-48 md:h-48 rounded-full border-4 border-violet-500 bg-gray-700 overflow-hidden flex items-center justify-center relative shadow-[0_0_15px_rgba(167,139,250,0.5)]">
                     <?= renderCharacter($character, [
                        'size' => 'full',
                        'showFilter' => true,
                        'id' => 'character',
                        'class' => 'object-cover w-full h-full hover:scale-110 transition duration-500'
                    ]); ?>
                </div>

                <!-- Status Effects Container -->
                <div id="status-effects-container" class="mt-4 flex flex-wrap gap-2 justify-center min-h-[40px] w-full px-2">
                    <?php
                        $buffModel = new CharacterBuff();
                        $activeBuffs = $buffModel->getActiveBuffs($characterModel->getId());
                        
                        if(empty($activeBuffs)):
                    ?>
                        <span id="no-status" class="text-xs text-gray-500 italic">Aucun effet actif</span>
                    <?php else: ?>
                        <?php foreach($activeBuffs as $buff): ?>
                            <div class="px-2 py-1 bg-gray-800 border border-gray-600 rounded text-xs text-violet-300 flex items-center gap-1 shadow-sm" title="<?= htmlspecialchars($buff['name']) ?>">
                                <span><?= htmlspecialchars($buff['name']) ?></span>
                                <span class="text-gray-500 text-[10px]">(<?= $buff['duration_remaining'] ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions Panel -->
            <div class="w-full md:w-1/3 p-3 bg-gray-900/50 rounded-lg flex flex-col relative">
                <!-- Main Action Menu (Default Visible) -->
                <div id="action-menu-main" class="flex flex-col h-full">
                    <h3 class='text-violet-400 font-semibold mb-3 text-center text-lg uppercase tracking-wider border-b border-gray-700 pb-2'>
                        Commandes
                    </h3>

                    <ul class="grid grid-cols-2 gap-3 mb-4 flex-1 content-start transition-all duration-300">
                        
                        <li>
                        <button id="btn-attack" disabled
                            class="
                            w-full px-2 py-3 text-xs md:text-sm uppercase font-bold
                            bg-red-900/30 text-red-400 border border-red-800 rounded
                            hover:not-disabled:bg-red-900/60 hover:not-disabled:text-white hover:not-disabled:border-red-500 transition duration-200
                            shadow-md flex flex-col items-center gap-1 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed
                            ">
                            <span class="material-symbols-outlined text-lg">swords</span>
                            Attaque
                        </button>
                        </li>

                        <li>
                        <button id="btn-defend" disabled
                            class="
                            w-full px-2 py-3 text-xs md:text-sm uppercase font-bold
                            bg-blue-900/30 text-blue-400 border border-blue-800 rounded
                            hover:not-disabled:bg-blue-900/60 hover:not-disabled:text-white hover:not-disabled:border-blue-500 transition duration-200
                            shadow-md flex flex-col items-center gap-1 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed
                            ">
                            <span class="material-symbols-outlined text-lg">shield</span>
                            Défense
                        </button>
                        </li>

                        <!-- Potions Button -->
                        <li>
                        <button id="btn-potions-menu" onclick="window.toggleActionMenu('potions')" disabled
                            class="
                            w-full px-2 py-3 text-xs md:text-sm uppercase font-bold
                            bg-yellow-900/30 text-yellow-400 border border-yellow-800 rounded
                            hover:not-disabled:bg-yellow-900/60 hover:not-disabled:text-white hover:not-disabled:border-yellow-500 transition duration-200
                            shadow-md flex flex-col items-center gap-1 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed
                            "
                            title="Lancer les dés d'abord">
                            <span class="material-symbols-outlined text-lg">healing</span>
                            Potions <?= !empty($potions) ? '('.count($potions).')' : '' ?>
                        </button>
                        </li>

                        <!-- Skills Button -->
                        <li>
                            <?php 
                                // Filter Active Skills
                                $actives = array_filter($skills ?? [], function($s) { return $s['type'] === 'active'; });
                            ?>
                            <button id="btn-skills-menu" onclick="window.toggleActionMenu('skills')" disabled
                                class="
                                w-full px-2 py-3 text-xs md:text-sm uppercase font-bold
                                bg-violet-900/30 text-violet-300 border border-violet-500 rounded
                                hover:not-disabled:bg-violet-900/60 hover:not-disabled:text-white hover:not-disabled:border-violet-300 transition duration-200
                                shadow-md flex flex-col items-center gap-1 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed
                                "
                                title="Lancer les dés d'abord">
                                <span class="material-symbols-outlined text-lg">bolt</span>
                                Spécial <?= !empty($actives) ? '('.count($actives).')' : '' ?>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Potions Sub-Menu (Hidden) -->
                <div id="action-menu-potions" class="hidden flex flex-col h-full absolute inset-0 bg-gray-900/95 z-20 p-3 rounded-lg backdrop-blur-sm">
                     <h3 class='text-yellow-400 font-semibold mb-3 text-center text-lg uppercase tracking-wider border-b border-gray-700 pb-2 flex justify-between items-center'>
                        <button onclick="window.toggleActionMenu('main')" class="text-xs text-gray-400 hover:text-white flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">arrow_back</span> Retour
                        </button>
                        Potions
                        <span class="w-12"></span> <!-- Spacer -->
                    </h3>
                    
                    <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                        <?php if(!empty($potions)): ?>
                            <?php foreach($potions as $potion): ?>
                                <button onclick="usePotion(<?= $potion['id'] ?>)" 
                                        class="w-full flex items-center justify-between p-2 rounded bg-gray-800 border border-gray-600 hover:border-yellow-500 hover:bg-gray-700 group transition-all text-left"
                                        title="<?= htmlspecialchars($potion['description'] ?? 'Restaure de la santé') ?>">
                                    <div class="flex items-center gap-2">
                                        <div class="text-2xl"><?= $potion['icon'] ?? '🧪' ?></div>
                                        <div>
                                            <div class="font-bold text-yellow-100 group-hover:text-yellow-400 text-sm"><?= htmlspecialchars($potion['name']) ?></div>
                                            <!-- Simple stat preview if available -->
                                            <div class="text-[10px] text-gray-400">Restaure HP</div> 
                                        </div>
                                    </div>
                                    <div class="text-xs font-mono font-bold bg-black/50 px-2 py-1 rounded text-gray-300">x<?= $potion['count'] ?></div>
                                </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-center italic mt-4">Inventaire vide.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Skills Sub-Menu (Hidden) -->
                <div id="action-menu-skills" class="hidden flex flex-col h-full absolute inset-0 bg-gray-900/95 z-20 p-3 rounded-lg backdrop-blur-sm">
                     <h3 class='text-violet-400 font-semibold mb-3 text-center text-lg uppercase tracking-wider border-b border-gray-700 pb-2 flex justify-between items-center'>
                        <button onclick="window.toggleActionMenu('main')" class="text-xs text-gray-400 hover:text-white flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">arrow_back</span> Retour
                        </button>
                        Compétences
                        <span class="w-12"></span> <!-- Spacer -->
                    </h3>
                    
                    <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                        <?php if(!empty($actives)): ?>
                            <?php foreach($actives as $skill): ?>
                                <button onclick="sendAction('use_skill', <?= $skill['id'] ?>)" 
                                        class="w-full flex items-center justify-between p-2 rounded bg-gray-800 border border-gray-600 hover:border-violet-500 hover:bg-gray-700 group transition-all text-left"
                                        title="<?= htmlspecialchars($skill['description'] ?? 'Attaque spéciale') ?>">
                                    <div class="flex items-center gap-2">
                                        <div class="text-2xl bg-violet-900/50 p-1 rounded"><?= $skill['icon'] ?? '⚡' ?></div>
                                        <div>
                                            <div class="font-bold text-violet-100 group-hover:text-violet-400 text-sm"><?= htmlspecialchars($skill['name']) ?></div>
                                            <div class="text-[10px] text-gray-400">Coût: <?= $skill['cost'] ?? 0 ?> MP</div> 
                                        </div>
                                    </div>
                                </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <p class="text-gray-500 text-center italic mt-4">Aucune compétence.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-auto">
                    <button id="rollBtn"
                    class=" 
                        w-full py-3 px-4 text-base font-bold uppercase tracking-widest
                        bg-gradient-to-r from-purple-800 to-indigo-900 hover:from-purple-700 hover:to-indigo-800 
                        text-white rounded-lg border border-purple-500/50
                        shadow-[0_0_15px_rgba(124,58,237,0.3)]
                        transition duration-200 transform hover:scale-[1.02] active:scale-95
                        flex items-center justify-center space-x-2 animate-pulse ring-2 ring-violet-500
                    ">
                    
                    <span>LANCER LES DÉS</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</div>
