
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
            // Update HP handled by server return usually, but here relies on static PHP value 
            // which is already there.
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

                        if (data.newTurn && !data.win) {
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
                    if (typeof data.playerHp !== "undefined") {
                        const hpEl = document.getElementById('player-hp');
                        hpEl.innerHTML = "";

                        updateCombatState(data.playerHp, MAX_HP);

                        if (data.playerHp <= 0) {
                            playerLoss();
                            hpEl.innerHTML += "<p style='color:red'>" + data.playerHp + "</p>";
                        } else {
                            hpEl.textContent = data.playerHp;
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

                if (rewards.quests && rewards.quests.length > 0) {
                    rewardHtml += `<div class="mt-3 pt-2 border-t border-gray-600">`;
                    rewards.quests.forEach(q => {
                        if (q.quest_completed) {
                            rewardHtml += `<div class="bg-yellow-900/50 p-2 rounded mb-1 border border-yellow-600 animate-pulse">
                                 <p class="text-yellow-300 font-bold">🏆 Quête Terminée : ${q.quest_name}</p>
                             </div>`;
                        } else if (q.objective_completed) {
                            rewardHtml += `<div class="bg-blue-900/50 p-2 rounded mb-1 border border-blue-600">
                                 <p class="text-blue-300">✅ Objectif accompli : ${q.objective_description}</p>
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
        bg.classList.add("sepia", "saturate-200", "hue-rotate-[-50deg]", "brightness-75");
        winOrLoss.classList.add("text-red-600", "animate-pulse");

        // Fetch Saves
        fetch('/game/saves')
            .then(r => r.json())
            .then(data => {
                let savesHtml = '<div class="mt-4 p-4 bg-gray-900/90 rounded text-center"><p class="text-white mb-2">Charger une sauvegarde :</p>';
                if (data.success && data.saves.length > 0) {
                    savesHtml += '<div class="flex flex-col gap-2">';
                    data.saves.forEach(s => {
                        savesHtml += `<button onclick="window.loadSave(${s.id})" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded">${s.save_name} (${s.created_at})</button>`;
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
