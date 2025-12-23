<?php
use App\Models\CharacterBuff;
use App\Models\Character;
use App\Models\Stats;
use App\Models\Skill
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <title>Combat</title>
    <style>
        @layer utilities {
            @keyframes fadeInOut {
                0% {
                    opacity: 0;
                }

                50% {
                    opacity: 1;
                }

                100% {
                    opacity: 0;
                }
            }

            .animate-fadeInOut {
                animation: fadeInOut 4s ease-in-out forwards;
            }
        }
    </style>

</head>

<body class="bg-gray-900 text-white min-h-screen">
    <div class="relative z-10 min-h-screen flex flex-col">

        <!-- Header -->
        <header
            class="flex justify-between items-center p-4 md:p-6 md:pb-2 bg-gray-900/50 backdrop-blur-sm lg:bg-transparent">
            <div class="text-xl md:text-2xl font-bold text-violet-500 tracking-wider uppercase">DungeonXplorer</div>
            <div class="flex items-center gap-4">
                <span class="hidden md:inline text-gray-300">Bienvenue,
                    <?php echo '<span class="text-white font semi-bold">' . $_SESSION['username'] . '</span>' ?> </span>
                <a href="/logout"
                    class="px-3 py-1 md:px-4 md:py-2 border border-red-500/50 text-red-400 rounded hover:bg-red-500/10 transition text-sm md:text:base">Déconnexion</a>
            </div>
        </header>

        <!-- Combat Interface -->
        <main class="flex-grow p-4 pt-0 md:p-4 md:pt-0 backdrop-blur-sm rounded-lg m-2">

            <!-- Interface Graphique Combat -->
            <div class="flex flex-col justify-center items-center gap-4 md:gap-8 w-full max-w-7xl mx-auto">

                <div
                    class="w-full lg:w-3/4 h-[400px] md:h-[500px] bg-gray-700 rounded-lg flex items-center justify-center drop-shadow-[0_0_5px_#a78bfa] relative overflow-hidden">
                    <img src=<?php echo $monsterModel->getSallePath() ?> class="w-full h-full object-cover" id="bg"
                        alt="">

                    <div class="absolute top-8 flex flex-col text-justify w-full px-4">
                        <h2 class="text-2xl md:text-3xl font-bold text-white text-center drop-shadow-lg mb-4">VS
                            <?php echo $monsterModel->getName() ?></h2>
                        <div id="combat-log" class="text-center px-2"></div>
                    </div>

                    <div class="absolute top-25 z-50 w-full text-center">
                        <div id="win?"></div>
                        <div id='redirectBtn'></div>
                    </div>

                    <div class="absolute bottom-7 w-48 h-48 md:w-64 md:h-64">
                        <img id='monster' src=<?php echo $monsterModel->getImagePath() ?> alt="Monstre"
                            class="object-contain w-full h-full transition-opacity duration-1000 opacity-100">
                    </div>


                    <div id="dice"
                        class="absolute bottom-3 right-3 text-3xl md:text-4xl font-bold text-violet-400 drop-shadow-[0_0_6px_#a78bfa]">
                        🎲
                    </div>
                </div>

                <div
                    class="w-full lg:w-3/4 bg-gray-800 rounded-lg flex flex-col md:flex-row relative overflow-visible p-4 gap-4">

                    <!-- Stats à gauche -->
                    <div class="w-full md:w-1/3 flex flex-col mb-4 md:mb-0 bg-gray-900/50 rounded-lg">
                        <?php
                        echo "<h3 class='text-violet-400 font-bold mb-2 text-center mt-2 md:text-xl'>Stats de " . htmlspecialchars($characterModel->getName()) . "</h3>";
                        ?>

                        <ul class="text-sm grid grid-cols-2 gap-6 mx-auto">
                            <li class="flex items-center gap-2 w-full">
                                <span class="material-symbols-outlined text-violet-400"
                                    title="Santé du joueur">favorite</span>
                                <span class="text-xl font-bold"
                                    id="player-hp"><?php echo htmlspecialchars($characterModel->getVitality()); ?></span>
                            </li>

                            <li class="flex items-center gap-2 w-full">
                                <span class="material-symbols-outlined text-violet-400"
                                    title="Puissance d'attaque">swords</span>
                                <span
                                    class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getAttaqueClass()); ?></span>
                            </li>

                            <li class="flex items-center gap-2 w-full">
                                <span class="material-symbols-outlined text-violet-400"
                                    title="Défense du joueur">shield</span>
                                <span
                                    class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getArmorClass()); ?></span>
                            </li>

                            <li class="flex items-center gap-2 w-full">
                                <span class="material-symbols-outlined text-violet-400"
                                    title="Force du joueur">fitness_center</span>
                                <span
                                    class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getStrength()); ?></span>
                            </li>

                            <li class="flex items-center gap-2 w-full">
                                <span class="material-symbols-outlined text-violet-400"
                                    title="Intelligence du joueur">psychology</span>
                                <span
                                    class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getIntelligence()); ?></span>
                            </li>

                            <li class="flex items-center gap-2 w-full">
                                <span class="material-symbols-outlined text-violet-400"
                                    title="Dextérié du joueur">directions_run</span>
                                <span
                                    class="text-xl font-bold"><?php echo htmlspecialchars($characterModel->getDexterity()); ?></span>
                            </li>
                        </ul>
                    </div>

                    <?php
                    $id = $characterModel->getClassName();
                    switch ($id) {
                        case 1:
                            $classImage = '/assets/images/warrior.png';
                            break;
                        case 2:
                            $classImage = '/assets/images/wizard.png';
                            break;
                        case 3:
                            $classImage = '/assets/images/thief.png';
                            break;
                        default:
                            $classImage = '/assets/images/default_class.png';
                            break;
                    }

                    $character = $characterModel->toArray();
                    ?>
                    <!--object-cover w-full h-full -->
                    <!-- Cercle au centre -->
                    <div class="w-full md:w-1/3 flex flex-col items-center justify-center relative mb-4 md:mb-0">
                        <!-- Avatar -->
                        <div id='character'
                            class="w-48 h-48 rounded-full border-4 border-violet-500 bg-gray-700 overflow-hidden flex items-center justify-center relative">
                            <?= renderCharacter($character, [
                                'size' => 'full',
                                'showFilter' => true,
                                'id' => 'character',
                                'class' => 'object-cover w-full h-full drop-shadow-2xl hover:brightness-110 transition duration-500'
                            ]); ?>
                        </div>

                        <!-- Status Effects Container -->
                        <div id="status-effects-container"
                            class="mt-4 flex flex-wrap gap-2 justify-center min-h-[40px] w-full px-2">
                            <?php
                            $buffModel = new CharacterBuff();
                            $activeBuffs = $buffModel->getActiveBuffs($characterModel->getId());

                            if (empty($activeBuffs)):
                                ?>
                                <span id="no-status" class="text-xs text-gray-500 italic">Aucun effet actif</span>
                            <?php else: ?>
                                <?php foreach ($activeBuffs as $buff): ?>
                                    <div class="px-2 py-1 bg-gray-800 border border-gray-600 rounded text-xs text-violet-300 flex items-center gap-1"
                                        title="<?= htmlspecialchars($buff['name']) ?>">
                                        <span><?= htmlspecialchars($buff['name']) ?></span>
                                        <span class="text-gray-500 text-[10px]">(<?= $buff['duration_remaining'] ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="w-full md:w-1/3 p-3 bg-gray-900/50 rounded-lg ">

                        <h3
                            class='text-violet-400 font-semibold mb-3 text-center text-lg uppercase tracking-wider border-b border-gray-700 pb-2'>
                            Actions
                        </h3>

                        <ul class="grid grid-cols-2 gap-3 mb-4">

                            <li>
                                <button id="btn-attack" onclick="sendAction('attack')" class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-red-400 border border-red-800 rounded
                            hover:bg-red-900/40 hover:text-white transition duration-150
                            shadow-md shadow-red-900/30
                            ">
                                    Attaque
                                </button>
                            </li>

                            <li>
                                <button id="btn-defend" onclick="sendAction('defend')" class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-blue-400 border border-blue-800 rounded
                            hover:bg-blue-900/40 hover:text-white transition duration-150
                            shadow-md shadow-blue-900/30
                            ">
                                    Défense
                                </button>
                            </li>

                            <li>
                                <button id="btn-heal" onclick="sendAction('usePotion')" class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-yellow-400 border border-yellow-800 rounded
                            hover:bg-yellow-900/40 hover:text-white transition duration-150
                            shadow-md shadow-yellow-900/30
                            ">
                                    Potion
                                </button>
                            </li>

                            <li>
                                <?php
                                $skillModel = new Skill();
                                $unlockedSkills = $skillModel->getUnlockedSkills($characterModel->getId());
                                $actives = array_filter($unlockedSkills, function ($s) {
                                    return $s['type'] === 'active'; });

                                if (empty($actives)):
                                    ?>
                                    <button id="btn-special" onclick="sendAction('specialCapacity')" class="
                            w-full px-3 py-2 text-sm uppercase font-medium
                            bg-gray-700/50 text-violet-300 border border-violet-500 rounded
                            hover:bg-violet-900/40 hover:text-white transition duration-150
                            shadow-md shadow-violet-500/30
                            ">
                                        Spécial
                                    </button>
                                <?php else: ?>
                                    <?php foreach ($actives as $skill): ?>
                                        <button onclick="sendAction('use_skill', <?= $skill['id'] ?>)" class="
                                w-full px-3 py-2 text-sm uppercase font-medium mb-2
                                bg-gray-700/50 text-violet-300 border border-violet-500 rounded
                                hover:bg-violet-900/40 hover:text-white transition duration-150
                                shadow-md shadow-violet-500/30
                                ">
                                            <?= htmlspecialchars($skill['name']) ?>
                                        </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </li>

                        </ul>

                        <div class="text-center">
                            <button onclick="rollDice()" id="rollBtn" class=" 
                            w-full py-2 px-4 text-sm font-bold uppercase
                            bg-purple-900 hover:bg-purple-700 text-white 
                            rounded-md border-b-2 border-purple-950 active:border-b-0 active:translate-y-px
                            shadow-lg shadow-purple-950/70 transition duration-150
                            flex items-center justify-center space-x-2 
                        ">

                                <span>LANCER LES DÉS</span>
                            </button>
                        </div>

                    </div>
                </div>
        </main>
    </div>

    <script type="module">
        import { updateCombatState, changeMusicCategory } from '/js/modules/soundManager.js';
        window.updateCombatSound = updateCombatState;
        window.changeMusicCategory = changeMusicCategory;

        changeMusicCategory('combat');
    </script>

    <script>
        let end = false;
        const MAX_HP = <?php echo (int) $characterModel->getVitality(); ?>;
        const winOrLoss = document.getElementById('win?');
        const redirection = document.getElementById('redirectBtn');
        const dice = document.getElementById("dice");
        const bg = document.getElementById('bg');
        const btn = document.getElementById("rollBtn");
        const btnAttack = document.getElementById("btn-attack");
        const btnDefend = document.getElementById("btn-defend");
        const btnHeal = document.getElementById("btn-heal");
        const btnRun = document.getElementById("btn-special");
        const monster = document.getElementById("monster");
        const character = document.getElementById('character');

        setTimeout(() => {
            if (window.updateCombatSound) {
                const currentHp = parseInt(document.getElementById('player-hp').textContent) || MAX_HP;
                window.updateCombatSound(currentHp, MAX_HP);
            }
        }, 1000);

        function disableActions() {
            btnAttack.disabled = true;
            btnDefend.disabled = true;
            btnHeal.disabled = true;
            btnRun.disabled = true;
        }

        function enableActions() {
            btnAttack.disabled = false;
            btnDefend.disabled = false;
            btnHeal.disabled = false;
            btnRun.disabled = false;
        }

        function sendAction(action, skillId = null) {
            disableActions();
            dice.textContent = "🎲 ";

            let body = "action=" + encodeURIComponent(action);
            if (skillId) body += "&skill_id=" + encodeURIComponent(skillId);

            fetch("/game/combat/action", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: body
            })
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        console.log(data);
                        const log = document.getElementById("combat-log");

                        log.innerHTML = "";

                        if (data.player) {
                            log.innerHTML += `<p class="text-blue-400 font-semibold mb-1">${data.player}</p>`;
                            if (data.damageM) {
                                ennemyHit();
                            }
                        }

                        setTimeout(() => {
                            if (data.monster) {
                                log.innerHTML += `<p class="text-red-400 font-semibold mb-1">${data.monster}</p>`;
                                if (data.damageJ) {
                                    playerHit();
                                }
                            }
                            setTimeout(() => {
                                if (!data.success) {
                                    log.innerHTML += `<p class="text-red-600 font-bold mb-2">${data.message}</p>`;
                                }

                                if (data.newTurn && !data.win) {
                                    log.innerHTML += `<p class="text-yellow-400 font-bold animate-pulse mt-2">À vous de jouer !</p>`;
                                }
                            }, 1000);
                            if (data.win) {
                                end = true;
                                playerWin(data.rewards);
                            }
                            if (typeof data.playerHp !== "undefined") {
                                document.getElementById('player-hp').innerHTML = "";

                                if (window.updateCombatSound) {
                                    window.updateCombatSound(data.playerHp, MAX_HP);
                                }

                                if (data.playerHp <= 0) {
                                    playerLoss();
                                    document.getElementById('player-hp').innerHTML += "<p style='color:red'>" + data.playerHp + "</p>";
                                } else {
                                    document.getElementById('player-hp').textContent = data.playerHp;
                                }
                            }
                            btn.disabled = false;
                            console.log("HP joueur:", data.playerHp);
                        }, 1500);
                    } catch (e) {
                        console.error("Réponse non JSON:", text);
                        console.log(e.message);
                    }
                });
        }

        function rollDice() {
            if (end) return;
            enableActions();
            btn.disabled = true;

            let result = Math.floor(Math.random() * 20) + 1;

            dice.textContent = "🎲 "; dice.classList.add("animate-spin");
            setTimeout(() => {
                dice.classList.remove("animate-spin");
                dice.textContent = "🎲 " + result;
            }, 500);

            fetch("/game/combat/roll-dice", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "diceRoll=" + result
            }).then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.text();
            }).then(data => {
                console.log("Dice roll sent to server:", data);
            }).catch(error => {
                console.error("There was a problem with the fetch operation:", error);
            });
        }

        function playerWin(rewards) {
            disableActions();
            setTimeout(() => {
                bossDie();
                setTimeout(() => {
                    document.getElementById('combat-log').textContent = "";
                    winOrLoss.classList.add("text-black-600", "animate-pulse");

                    let rewardHtml = '';
                    if (rewards) {
                        rewardHtml += `<div class="mt-4 text-center bg-gray-800/90 p-4 rounded-lg border border-yellow-500/50 shadow-lg">
                                <h3 class="text-yellow-400 font-bold mb-2 uppercase tracking-wide">Butin de Guerre</h3>
                                <div class="flex flex-col gap-1">
                                    <p class="text-violet-300 font-bold text-lg">XP: <span class="text-white">+${rewards.xp}</span></p>
                                    <p class="text-yellow-300 font-bold text-lg">Or: <span class="text-white">+${rewards.gold}</span></p>
                                </div>`;

                        if (rewards.levels_gained > 0) {
                            rewardHtml += `<div class="mt-2 p-2 bg-green-900/50 rounded border border-green-500/50 animate-bounce">
                                    <p class="text-green-400 font-bold uppercase">🎉 Niveau Supérieur ! (+${rewards.levels_gained})</p>
                                </div>`;
                        }

                        if (rewards.loot && rewards.loot.length > 0) {
                            rewardHtml += `<div class="mt-3 pt-2 border-t border-gray-600">
                                    <p class="text-blue-300 font-semibold mb-1">Objets trouvés :</p>
                                    <ul class="text-sm text-gray-300 space-y-1">`;
                            rewards.loot.forEach(item => {
                                rewardHtml += `<li class="flex items-center justify-center gap-2"><span class="text-yellow-500">★</span> ${item}</li>`;
                            });
                            rewardHtml += `</ul></div>`;
                        }
                        rewardHtml += `</div>`;
                    }

                    let redirect = document.createElement("button");
                    redirect.textContent = "Retour au Donjon"
                    redirect.classList.add('px-8', 'py-3', 'bg-purple-600', 'text-white', 'font-bold', 'uppercase', 'tracking-widest', 'hover:bg-purple-500', 'mt-6', 'rounded-full', 'shadow-lg', 'transform', 'hover:scale-105', 'transition', 'duration-200');
                    redirect.onclick = () => {
                        window.location.href = "/game";
                    }
                    winOrLoss.innerHTML = '<p class ="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-red-600 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] mb-4"> VICTOIRE </p>' + rewardHtml;
                    redirection.appendChild(redirect);
                }, 1000);
            }, 1000);
        }

        function playerLoss() {
            end = true;
            disableActions();
            btn.disabled = true;
            playerDie();

            setTimeout(() => {
                document.getElementById('combat-log').textContent = "";
                bg.classList.add("sepia", "saturate-200", "hue-rotate-[-50deg]", "brightness-75");
                winOrLoss.classList.add("text-red-600", "animate-pulse");

                fetch('/game/saves')
                    .then(r => r.json())
                    .then(data => {
                        let savesHtml = '<div class="mt-4 p-4 bg-gray-900/90 rounded text-center"><p class="text-white mb-2">Charger une sauvegarde :</p>';
                        if (data.success && data.saves.length > 0) {
                            savesHtml += '<div class="flex flex-col gap-2">';
                            data.saves.forEach(s => {
                                savesHtml += `<button onclick="loadSave(${s.id})" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded">${s.save_name} (${s.created_at})</button>`;
                            });
                            savesHtml += '</div>';
                        } else {
                            savesHtml += '<p class="text-gray-400">Aucune sauvegarde trouvée.</p>';
                        }
                        savesHtml += '<button onclick="window.location.href=\'/game\'" class="mt-4 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded">Retour Menu (Risqué)</button></div>';

                        winOrLoss.innerHTML = '<p class ="text-5xl font-black text-red-600 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] mb-4">GAME OVER</p>' + savesHtml;
                    });

            }, 2000);
        }

        function loadSave(id) {
            if (!confirm("Charger cette sauvegarde ?")) return;
            fetch('/game/load', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'save_id=' + id
            })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        alert("Partie chargée !");
                        window.location.href = '/game';
                    } else {
                        alert("Erreur: " + d.message);
                    }
                });
        }
        function ennemyHit() {
            gsap.from(monster, {
                x: "-8", duration: 0.1,
                yoyo: true,
                repeat: 2,
                ease: "power2.out"
            });
        }
        function playerHit() {
            gsap.from(character, {
                x: "-8", duration: 0.1,
                yoyo: true,
                repeat: 2,
                ease: "power2.out"
            });


        }
        function playerDie() {
            gsap.fromTo("body",
                { x: -10 },
                { x: 10, duration: 0.05, repeat: 10, yoyo: true }
            );
        }
        function bossDie() {
            gsap.fromTo("body",
                { x: -10 },
                { x: 10, duration: 0.05, repeat: 10, yoyo: true }
            );
            const tl = gsap.timeline();

            tl.to(monster, {
                filter: "brightness(3)",
                duration: 0.1,
            });

            tl.to(monster, {
                x: "+=5",
                repeat: 5,
                yoyo: true,
                duration: 0.05
            });

            tl.to(monster, {
                scale: 0,
                opacity: 0,
                duration: 0.4,
                ease: "power3.in"
            });
        }

        <?php if (isset($initialData) && $initialData): ?>
            const initialData = <?php echo json_encode($initialData); ?>;
            setTimeout(() => {
                const log = document.getElementById("combat-log");

                const msgClass = initialData.monster_starts ? "text-red-400" : "text-green-400";
                const title = initialData.monster_starts ? "⚠️ ATTENTION !" : "🚀 INITIATIVE !";

                log.innerHTML += `<p class="${msgClass} font-bold mb-1">${title}</p>`;
                log.innerHTML += `<p class="${msgClass} font-semibold mb-1">${initialData.message}</p>`;

                if (initialData.hit) {
                    playerHit();
                    document.getElementById('player-hp').textContent = "<?php echo htmlspecialchars($characterModel->getVitality()); ?>";
                }
            }, 500);
        <?php endif; ?>
    </script>
</body>

</html>