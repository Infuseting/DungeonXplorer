<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbre de Compétences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body class="bg-gray-900 text-gray-100 font-lato min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-cinzel text-amber-500">Compétences - <?= htmlspecialchars($character->getName()) ?></h1>
            <a href="/game" class="text-gray-400 hover:text-white flex items-center gap-2">
                <span class="material-symbols-outlined">arrow_back</span> Retour
            </a>
        </div>
    </nav>

    <main class="flex-grow container mx-auto p-6">
        
        <!-- Header Info -->
        <div class="flex justify-between items-center mb-8 bg-gray-800 p-4 rounded-lg border border-gray-700">
            <div class="flex items-center gap-4">
                <div class="bg-gray-700 p-3 rounded-full">
                    <span class="material-symbols-outlined text-amber-500 text-3xl">local_police</span>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Points Disponibles</p>
                    <p class="text-3xl font-bold text-amber-400" id="sp-display"><?= $character->getSkillPoints() ?></p>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-sm text-gray-400">Niveau actuel</p>
                <p class="text-xl font-bold"><?= $character->getLevel() ?></p>
            </div>
        </div>

        <!-- Skills Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($classSkills as $skill): ?>
                <?php 
                    $isUnlocked = in_array($skill['id'], $unlockedIds);
                    $canAfford = $character->getSkillPoints() >= $skill['cost_sp'];
                    $levelMet = $character->getLevel() >= $skill['min_level'];
                    $prereqMet = true;
                    if ($skill['parent_skill_id']) {
                        $prereqMet = in_array($skill['parent_skill_id'], $unlockedIds);
                    }
                    $isAvailable = !$isUnlocked && $canAfford && $levelMet && $prereqMet;
                    
                    // Style classes
                    $cardClass = "bg-gray-800 border-2";
                    if ($isUnlocked) {
                        $cardClass .= " border-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.3)]";
                    } elseif ($isAvailable) {
                        $cardClass .= " border-gray-600 hover:border-gray-400";
                    } else {
                        $cardClass .= " border-gray-700 opacity-60";
                    }
                ?>
                
                <div class="<?= $cardClass ?> rounded-lg p-5 relative transition-all duration-300 skill-card" data-id="<?= $skill['id'] ?>">
                    <!-- Level Badge -->
                    <div class="absolute -top-3 -right-3 bg-gray-900 border border-gray-600 rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold text-gray-400" title="Niveau requis">
                        <?= $skill['min_level'] ?>
                    </div>

                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-bold text-amber-100"><?= htmlspecialchars($skill['name']) ?></h3>
                        <?php if ($skill['type'] === 'paddle'): // Typo fix: passive ?>
                            <span class="px-2 py-0.5 rounded text-xs bg-blue-900 text-blue-300 border border-blue-700 uppercase">Passif</span>
                        <?php elseif ($skill['type'] === 'passive'): ?>
                            <span class="px-2 py-0.5 rounded text-xs bg-blue-900 text-blue-300 border border-blue-700 uppercase">Passif</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded text-xs bg-red-900 text-red-300 border border-red-700 uppercase">Actif</span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-gray-400 text-sm mb-4 min-h-[40px]"><?= htmlspecialchars($skill['description']) ?></p>
                    
                    <div class="text-sm text-gray-500 mb-4 space-y-1">
                        <?php if ($skill['type'] === 'active'): ?>
                            <div class="flex justify-between">
                                <span>Coût Mana:</span> <span class="text-blue-400"><?= $skill['cost_mp'] ?> MP</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Cooldown:</span> <span><?= $skill['cooldown'] > 0 ? $skill['cooldown'].' tours' : 'Aucun' ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($skill['parent_skill_id']): ?>
                            <div class="text-orange-400 text-xs mt-2">
                                <span class="material-symbols-outlined text-[14px] align-middle">lock</span> Requis: Compétence précédente
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto">
                        <?php if ($isUnlocked): ?>
                            <button disabled class="w-full py-2 bg-green-900/30 border border-green-600 text-green-400 rounded cursor-default flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">check_circle</span> Appris
                            </button>
                        <?php else: ?>
                            <button onclick="unlockSkill(<?= $skill['id'] ?>)" 
                                    <?= !$isAvailable ? 'disabled' : '' ?>
                                    class="w-full py-2 rounded font-bold transition-all flex items-center justify-center gap-2
                                    <?= $isAvailable 
                                        ? 'bg-amber-600 hover:bg-amber-500 text-white shadow-lg shadow-amber-900/20' 
                                        : 'bg-gray-700 text-gray-500 cursor-not-allowed' 
                                    ?>">
                                <span>Débloquer</span>
                                <span class="bg-black/20 px-2 rounded text-sm"><?= $skill['cost_sp'] ?> SP</span>
                            </button>
                            <?php if (!$isAvailable && !$prereqMet): ?>
                                <p class="text-center text-xs text-red-400 mt-2">Prérequis manquant</p>
                            <?php elseif (!$isAvailable && !$levelMet): ?>
                                <p class="text-center text-xs text-red-400 mt-2">Niveau trop bas</p>
                            <?php elseif (!$isAvailable && !$canAfford): ?>
                                <p class="text-center text-xs text-red-400 mt-2">Points insuffisants</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        function unlockSkill(skillId) {
            if(!confirm('Débloquer cette compétence pour ' + event.currentTarget.querySelector('span:last-child').innerText + ' ?')) return;
            
            fetch('/game/skills/unlock', {
                method: 'POST',
                headers: {
                     'Content-Type': 'application/json',
                },
                body: JSON.stringify({ skill_id: skillId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                  // Reload to update UI state easily
                  window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erreur de communication');
            });
        }
    </script>
</body>
</html>
