
import { updateCombatState, changeMusicCategory } from '/js/modules/soundManager.js';

let end = false;
let MAX_HP = 100;
let characterId = null;

// DOM Elements
let winOrLoss, redirection, dice, bg, btn, btnAttack, btnDefend, btnHeal, btnRun, monster, character, log;

export async function init() {
    console.log('[Combat] Initializing...');

    // Get Elements
    const sceneCtx = document.getElementById('combat-scene');
    if (!sceneCtx) {
        console.error('[Combat] Scene container not found');
        return;
    }

    // Read Config
    MAX_HP = parseInt(sceneCtx.dataset.maxHp) || 100;
    characterId = window.characterId;

    // Initialize References
    setupReferences();

    console.log('[Combat] Scene Dataset:', sceneCtx.dataset);

    // Sound
    changeMusicCategory('combat');
    setTimeout(() => {
        const currentHp = parseInt(document.getElementById('player-hp').textContent) || MAX_HP;
        updateCombatState(currentHp, MAX_HP);
    }, 1000);

    // Initial Data Handling
    if (sceneCtx.dataset.initialData) {
        try {
            const initialData = JSON.parse(sceneCtx.dataset.initialData);
            handleInitialData(initialData);
        } catch (e) {
            console.error('[Combat] Failed to parse initial data', e);
        }
    }

    // Attach Global Functions for Button OnClicks
    // Since the HTML uses "onclick=", we need to attach these to window or rewrite the listeners.
    // Rewriting listeners is cleaner for modules.
    // Attach Global Functions for Button OnClicks
    // Since the HTML uses "onclick=", we need to attach these to window or rewrite the listeners.
    // Rewriting listeners is cleaner for modules but we have specific button IDs here.
    attachEventListeners();

    // Expose menu toggling to window for inline onclicks in partials
    window.toggleActionMenu = toggleActionMenu;
    window.usePotion = usePotion;
    window.sendAction = sendAction; // Expose for skill buttons
}

function setupReferences() {
    winOrLoss = document.getElementById('win?');
    redirection = document.getElementById('redirectBtn');
    dice = document.getElementById("dice");
    bg = document.getElementById('bg');
    btn = document.getElementById("rollBtn");
    btnAttack = document.getElementById("btn-attack");
    btnDefend = document.getElementById("btn-defend");
    // btnHeal & btnRun are now menu toggles handled inline, but we keep refs if needed for legacy
    monster = document.getElementById("monster");
    character = document.getElementById('character');
    log = document.getElementById("combat-log");
}

function attachEventListeners() {
    if (btnAttack) btnAttack.onclick = () => sendAction('attack');
    if (btnDefend) btnDefend.onclick = () => sendAction('defend');

    // Dice
    if (btn) btn.onclick = rollDice;
}

function toggleActionMenu(menuName) {
    const main = document.getElementById('action-menu-main');
    const potions = document.getElementById('action-menu-potions');
    const skills = document.getElementById('action-menu-skills');

    // Hide all first
    if (main) main.classList.add('hidden');
    if (potions) potions.classList.add('hidden');
    if (skills) skills.classList.add('hidden');

    // Show requested
    const target = document.getElementById(`action-menu-${menuName}`);
    if (target) {
        target.classList.remove('hidden');
        // Simple animation
        target.classList.add('animate-fade-in');
        setTimeout(() => target.classList.remove('animate-fade-in'), 300);
    }
}

function usePotion(itemId) {
    // Determine action. Assuming 'usePotion' expects 'item_id'.
    // If backend controller expects 'action=usePotion' and 'item_id', we pass that.
    sendAction('usePotion', null, itemId);
}

function disableActions() {
    if (btnAttack) btnAttack.disabled = true;
    if (btnDefend) btnDefend.disabled = true;
    // Disable menu toggles
    const toggles = document.querySelectorAll('[onclick*="toggleActionMenu"]');
    toggles.forEach(b => b.disabled = true);

    // Disable sub-menu items
    const subButtons = document.querySelectorAll('#action-menu-potions button, #action-menu-skills button');
    subButtons.forEach(b => b.disabled = true);

    const skillBtns = document.querySelectorAll('[data-action="use_skill"]');
    skillBtns.forEach(b => b.disabled = true);
}

function enableActions() {
    if (btnAttack) btnAttack.disabled = false;
    if (btnDefend) btnDefend.disabled = false;

    // Enable menu toggles
    const toggles = document.querySelectorAll('[onclick*="toggleActionMenu"]');
    toggles.forEach(b => b.disabled = false);

    // Enable sub-menu items
    const subButtons = document.querySelectorAll('#action-menu-potions button, #action-menu-skills button');
    subButtons.forEach(b => b.disabled = false);

    const skillBtns = document.querySelectorAll('[data-action="use_skill"]');
    skillBtns.forEach(b => b.disabled = false);
}

function handleInitialData(initialData) {
    setTimeout(() => {
        const msgClass = initialData.monster_starts ? "text-red-400" : "text-green-400";
        const title = initialData.monster_starts ? "⚠️ ATTENTION !" : "🚀 INITIATIVE !";

        log.innerHTML += `<p class="${msgClass} font-bold mb-1">${title}</p>`;
        log.innerHTML += `<p class="${msgClass} font-semibold mb-1">${initialData.message}</p>`;

        if (initialData.hit) {
            playerHit();
        }
        
        // Mise à jour des HP après l'attaque initiale
        if (typeof initialData.playerHp !== "undefined") {
            const hpEl = document.getElementById('player-hp');
            const hpBar = document.getElementById('player-hp-bar');
            
            updateCombatState(initialData.playerHp, MAX_HP);
            hpEl.textContent = initialData.playerHp;
            
            if (hpBar) {
                const hpPercent = (initialData.playerHp / MAX_HP) * 100;
                hpBar.style.width = hpPercent + "%";
                
                // Changement de couleur selon le pourcentage
                if (hpPercent <= 25) {
                    hpBar.className = "bg-red-600 h-full transition-all duration-500 animate-pulse";
                } else if (hpPercent <= 50) {
                    hpBar.className = "bg-gradient-to-r from-orange-600 to-orange-400 h-full transition-all duration-500";
                } else {
                    hpBar.className = "bg-gradient-to-r from-red-600 to-red-400 h-full transition-all duration-500";
                }
            }
        }
        
        // Vérification de mort instantanée
        if (initialData.playerDead || initialData.gameOver) {
            playerLoss();
        }
    }, 500);
}

function sendAction(action, skillId = null, itemId = null) {
    disableActions();
    dice.textContent = "🎲 ";

    let body = "action=" + encodeURIComponent(action);
    if (skillId) body += "&skill_id=" + encodeURIComponent(skillId);
    if (itemId) body += "&item_id=" + encodeURIComponent(itemId);

    fetch("/game/combat/action", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body
    })
        .then(res => res.text())
        .then(text => {
            try {
                const cleanText = text.trim();
                const data = JSON.parse(cleanText);
                console.log(data);
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

                        if (data.newTurn && !data.win && !data.gameOver) {
                            log.innerHTML += `<p class="text-yellow-400 font-bold animate-pulse mt-2">À vous de jouer !</p>`;
                            if (btn && !end) {
                                btn.disabled = false;
                                btn.classList.add("animate-pulse", "ring-2", "ring-violet-500");
                                btn.classList.remove("opacity-50", "cursor-not-allowed");
                            }
                        }
                    }, 1000);
                    if (data.win) {
                        end = true;
                        playerWin(data.rewards);
                    }
                    
                    // Vérification du Game Over
                    if (data.gameOver && !data.win) {
                        end = true;
                        playerLoss();
                    }
                    
                    // Mise à jour des HP du joueur
                    if (typeof data.playerHp !== "undefined") {
                        const hpEl = document.getElementById('player-hp');
                        const hpBar = document.getElementById('player-hp-bar');
                        
                        updateCombatState(data.playerHp, MAX_HP);

                        if (data.playerHp <= 0) {
                            playerLoss();
                            hpEl.textContent = "0";
                            if (hpBar) hpBar.style.width = "0%";
                        } else {
                            hpEl.textContent = data.playerHp;
                            
                            // Mise à jour de la barre de PV
                            if (hpBar) {
                                const hpPercent = (data.playerHp / MAX_HP) * 100;
                                hpBar.style.width = hpPercent + "%";
                                
                                // Changement de couleur selon le pourcentage
                                if (hpPercent <= 25) {
                                    hpBar.className = "bg-red-600 h-full transition-all duration-500 animate-pulse";
                                } else if (hpPercent <= 50) {
                                    hpBar.className = "bg-gradient-to-r from-orange-600 to-orange-400 h-full transition-all duration-500";
                                } else {
                                    hpBar.className = "bg-gradient-to-r from-red-600 to-red-400 h-full transition-all duration-500";
                                }
                            }
                            
                            // Animation de dégâts
                            if (data.damageJ) {
                                hpEl.classList.add('animate-bounce');
                                setTimeout(() => hpEl.classList.remove('animate-bounce'), 500);
                            }
                        }
                    }
                    
                    // Mise à jour de la barre de PV du monstre (uniquement visuelle)
                    if (typeof data.monsterHp !== "undefined") {
                        const monsterHpBar = document.getElementById('monster-hp-bar');
                        
                        if (monsterHpBar) {
                            const maxHp = parseInt(monsterHpBar.dataset.maxHp) || 100;
                            const hpPercent = (data.monsterHp / maxHp) * 100;
                            
                            // Mise à jour de la largeur de la barre
                            monsterHpBar.style.width = hpPercent + "%";
                            
                            // Changement de couleur selon le pourcentage
                            if (hpPercent <= 0) {
                                monsterHpBar.className = "bg-gray-600 h-full transition-all duration-500 shadow-lg";
                            } else if (hpPercent <= 25) {
                                monsterHpBar.className = "bg-red-700 h-full transition-all duration-500 animate-pulse shadow-lg";
                            } else if (hpPercent <= 50) {
                                monsterHpBar.className = "bg-orange-500 h-full transition-all duration-500 shadow-lg";
                            } else if (hpPercent <= 75) {
                                monsterHpBar.className = "bg-yellow-500 h-full transition-all duration-500 shadow-lg";
                            } else {
                                monsterHpBar.className = "bg-red-500 h-full transition-all duration-500 shadow-lg";
                            }
                            
                            // Animation si le monstre prend des dégâts
                            if (data.damageM) {
                                monsterHpBar.classList.add('scale-105');
                                setTimeout(() => monsterHpBar.classList.remove('scale-105'), 200);
                            }
                        }
                    }
                    // if (btn) btn.disabled = false; // Logic moved inside newTurn check with highlight
                }, 1500);
            } catch (e) {
                console.error("Réponse non JSON:", e);
            }
        });
}

function rollDice() {
    if (end) return;
    enableActions();
    if (btn) {
        btn.disabled = true;
        btn.classList.remove("animate-pulse", "ring-2", "ring-violet-500");
        btn.classList.add("opacity-50", "cursor-not-allowed");
    }

    let result = Math.floor(Math.random() * 20) + 1;

    dice.textContent = "🎲 ";
    dice.classList.add("animate-spin");

    setTimeout(() => {
        dice.classList.remove("animate-spin");
        dice.textContent = "🎲 " + result;
    }, 500);

    fetch("/game/combat/roll-dice", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "diceRoll=" + result
    });
}

function playerWin(rewards) {
    disableActions();
    setTimeout(() => {
        bossDie();
        setTimeout(() => {
            log.textContent = "";
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
                        <p class="text-green-400 font-bold uppercase">Niveau Supérieur ! (+${rewards.levels_gained})</p>
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

                if (rewards.quests && rewards.quests.length > 0) {
                    rewardHtml += `<div class="mt-3 pt-2 border-t border-gray-600">`;
                    rewards.quests.forEach(q => {
                        if (q.quest_completed) {
                            rewardHtml += `<div class="bg-yellow-900/50 p-2 rounded mb-1 border border-yellow-600 animate-pulse">
                                 <p class="text-yellow-300 font-bold">Quête Terminée : ${q.quest_name}</p>
                             </div>`;
                        } else if (q.objective_completed) {
                            rewardHtml += `<div class="bg-blue-900/50 p-2 rounded mb-1 border border-blue-600">
                                 <p class="text-blue-300">Objectif accompli : ${q.objective_description}</p>
                             </div>`;
                        } else {
                            rewardHtml += `<div class="text-xs text-gray-400">
                                 Progression quête : ${q.quest_name}
                             </div>`;
                        }
                    });
                    rewardHtml += `</div>`;
                }

                rewardHtml += `</div>`;
            }

            let redirect = document.createElement("button");
            redirect.textContent = "Retour au Donjon"
            redirect.classList.add('px-8', 'py-3', 'bg-purple-600', 'text-white', 'font-bold', 'uppercase', 'tracking-widest', 'hover:bg-purple-500', 'mt-6', 'rounded-full', 'shadow-lg', 'transform', 'hover:scale-105', 'transition', 'duration-200');
            redirect.onclick = () => {
                const sceneCtx = document.getElementById('combat-scene');
                let returnStoryId = sceneCtx ? sceneCtx.dataset.returnStoryId : null;

                console.log('[Combat] Return Story ID:', returnStoryId);

                if (returnStoryId === 'undefined' || returnStoryId === 'null') returnStoryId = null;

                if (window.GameRouter && returnStoryId) {
                    // Navigate back to the story node
                    window.GameRouter.navigate(`/story/enter/${returnStoryId}`);
                } else if (window.GameRouter) {
                    window.GameRouter.showMap();
                } else {
                    window.location.href = "/game";
                }
            }
            winOrLoss.innerHTML = '<p class ="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-red-600 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] mb-4"> VICTOIRE </p>' + rewardHtml;
            redirection.appendChild(redirect);
        }, 1000);
    }, 1000);
}

function playerLoss() {
    end = true;
    disableActions();
    if (btn) btn.disabled = true;
    playerDie();

    setTimeout(() => {
        log.textContent = "";
        
        // Récupérer le story_id si disponible
        const sceneCtx = document.getElementById('combat-scene');
        const returnStoryId = sceneCtx ? sceneCtx.dataset.returnStoryId : null;

        // Créer l'overlay de Game Over
        const gameOverOverlay = document.createElement('div');
        gameOverOverlay.id = 'game-over-overlay';
        gameOverOverlay.className = 'fixed inset-0 z-[100] flex items-center justify-center';
        gameOverOverlay.innerHTML = `
            <div class="absolute inset-0 bg-black/90 backdrop-blur-sm"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-red-950/50 via-transparent to-black/80"></div>
            
            <!-- Particules de sang/fumée animées -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="blood-particle" style="left: 10%; animation-delay: 0s;"></div>
                <div class="blood-particle" style="left: 25%; animation-delay: 0.5s;"></div>
                <div class="blood-particle" style="left: 40%; animation-delay: 1s;"></div>
                <div class="blood-particle" style="left: 55%; animation-delay: 1.5s;"></div>
                <div class="blood-particle" style="left: 70%; animation-delay: 0.3s;"></div>
                <div class="blood-particle" style="left: 85%; animation-delay: 0.8s;"></div>
            </div>
            
            <!-- Vignette Effect -->
            <div class="absolute inset-0 shadow-[inset_0_0_150px_60px_rgba(0,0,0,0.9)] pointer-events-none"></div>
            
            <!-- Contenu principal -->
            <div class="relative z-10 flex flex-col items-center text-center px-4 max-w-lg w-full animate-fade-in-up">
                                
                <!-- Titre GAME OVER -->
                <h1 class="game-over-title text-6xl md:text-8xl font-black tracking-wider mb-2">
                    GAME OVER
                </h1>
                
                <!-- Sous-titre -->
                <p class="text-red-400/80 text-lg md:text-xl font-medium mb-2 tracking-wide">
                    Votre aventure s'achève ici...
                </p>
                <p class="text-gray-500 text-sm mb-8">
                    Les ténèbres vous ont englouti
                </p>
                
                <!-- Séparateur décoratif -->
                <div class="flex items-center gap-4 mb-8 w-full max-w-xs">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-red-800/50 to-transparent"></div>
                    <span class="text-red-700">☠</span>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-red-800/50 to-transparent"></div>
                </div>
                
                <!-- Options de sauvegarde -->
                <div id="game-over-saves" class="w-full bg-gray-900/80 border border-gray-700/50 rounded-xl p-5 backdrop-blur-sm shadow-2xl">
                    <p class="text-gray-300 text-sm uppercase tracking-wider mb-4 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Charger une sauvegarde
                    </p>
                    <div id="saves-list" class="flex flex-col gap-2 max-h-48 overflow-y-auto custom-scrollbar">
                        <p class="text-gray-500 text-sm py-4">Chargement...</p>
                    </div>
                </div>
                
                <!-- Bouton retour map -->
                <button id="return-to-map-btn" onclick="window.resetStoryAndReturnToMap(${returnStoryId})" 
                        class="mt-6 px-8 py-3 bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-700 hover:to-gray-800 
                               text-gray-300 hover:text-white rounded-lg border border-gray-600 hover:border-gray-500
                               transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl
                               transform hover:scale-105 active:scale-95">
                    <span class="material-symbols-outlined">map</span>
                    Retour à la Carte
                </button>
            </div>
        `;

        // Ajouter les styles CSS pour les animations
        const styleEl = document.createElement('style');
        styleEl.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes bloodFall {
                0% {
                    transform: translateY(-100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 0.6;
                }
                90% {
                    opacity: 0.3;
                }
                100% {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
            
            @keyframes glitchText {
                0%, 100% {
                    text-shadow: 
                        0 0 10px rgba(220, 38, 38, 0.8),
                        0 0 20px rgba(220, 38, 38, 0.6),
                        0 0 40px rgba(220, 38, 38, 0.4),
                        0 0 80px rgba(220, 38, 38, 0.2);
                }
                25% {
                    text-shadow: 
                        -2px 0 10px rgba(220, 38, 38, 0.8),
                        2px 0 20px rgba(185, 28, 28, 0.6),
                        0 0 40px rgba(220, 38, 38, 0.4);
                }
                50% {
                    text-shadow: 
                        2px 0 10px rgba(185, 28, 28, 0.8),
                        -2px 0 20px rgba(220, 38, 38, 0.6),
                        0 0 60px rgba(220, 38, 38, 0.5);
                }
                75% {
                    text-shadow: 
                        0 2px 10px rgba(220, 38, 38, 0.8),
                        0 -2px 20px rgba(185, 28, 28, 0.6),
                        0 0 40px rgba(220, 38, 38, 0.4);
                }
            }
            
            @keyframes pulseSlow {
                0%, 100% {
                    opacity: 0.8;
                    transform: scale(1);
                }
                50% {
                    opacity: 1;
                    transform: scale(1.05);
                }
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 1s ease-out forwards;
            }
            
            .animate-pulse-slow {
                animation: pulseSlow 3s ease-in-out infinite;
            }
            
            .game-over-title {
                color: transparent;
                background: linear-gradient(180deg, #dc2626 0%, #7f1d1d 50%, #450a0a 100%);
                -webkit-background-clip: text;
                background-clip: text;
                animation: glitchText 4s ease-in-out infinite;
                letter-spacing: 0.1em;
            }
            
            .blood-particle {
                position: absolute;
                width: 4px;
                height: 20px;
                background: linear-gradient(180deg, rgba(220, 38, 38, 0.6), transparent);
                border-radius: 50%;
                animation: bloodFall 4s linear infinite;
            }
            
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(0,0,0,0.3);
                border-radius: 3px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(220, 38, 38, 0.5);
                border-radius: 3px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(220, 38, 38, 0.7);
            }
        `;
        document.head.appendChild(styleEl);

        // Ajouter l'overlay au DOM
        document.body.appendChild(gameOverOverlay);

        // Charger les sauvegardes
        fetch('/game/saves')
            .then(r => r.json())
            .then(data => {
                const savesList = document.getElementById('saves-list');
                if (data.success && data.saves.length > 0) {
                    savesList.innerHTML = data.saves.map(s => `
                        <button onclick="window.loadSave(${s.id})" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-blue-900/50 to-blue-800/30 
                                       hover:from-blue-800/60 hover:to-blue-700/40 
                                       text-blue-100 hover:text-white rounded-lg border border-blue-700/30 
                                       hover:border-blue-500/50 transition-all duration-200
                                       flex items-center justify-between group">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-400 group-hover:text-blue-300">bookmark</span>
                                <span class="font-medium">${s.save_name}</span>
                            </span>
                            <span class="text-xs text-blue-400/60">${s.created_at}</span>
                        </button>
                    `).join('');
                } else {
                    savesList.innerHTML = `
                        <div class="py-6 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-600 mb-2">folder_off</span>
                            <p class="text-gray-500 text-sm">Aucune sauvegarde trouvée</p>
                        </div>
                    `;
                }
            })
            .catch(() => {
                document.getElementById('saves-list').innerHTML = `
                    <p class="text-red-400 text-sm py-4">Erreur lors du chargement des sauvegardes</p>
                `;
            });

    }, 2000);
}

// Global scope for HTML button access (Load Save in GameOver)
window.loadSave = function (id) {
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
};

// Réinitialiser la progression de l'histoire et retourner à la map
window.resetStoryAndReturnToMap = function (storyId) {
    const formData = new FormData();
    if (storyId && storyId !== 'null' && storyId !== 'undefined') {
        formData.append('story_id', storyId);
    }
    
    fetch('/story/reset', {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(d => {
            // Rediriger vers la map que la réinitialisation ait réussi ou non
            if (window.GameRouter) {
                window.GameRouter.showMap();
            } else {
                window.location.href = '/game';
            }
        })
        .catch(err => {
            console.error('Error resetting story:', err);
            // Rediriger quand même vers la map
            if (window.GameRouter) {
                window.GameRouter.showMap();
            } else {
                window.location.href = '/game';
            }
        });
};

function ennemyHit() {
    gsap.from(monster, {
        x: "-8",
        duration: 0.1,
        yoyo: true,
        repeat: 2,
        ease: "power2.out"
    });
}
function playerHit() {
    gsap.from(character, {
        x: "-8",
        duration: 0.1,
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
