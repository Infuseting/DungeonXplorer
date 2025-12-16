/**
 * NPC Modal Module
 * Handles NPC interactions with visual novel style interface
 */

import { playSound } from './soundManager.js';

let currentNPC = null;
let currentDialogueTree = null;
let currentDialogueTreeId = null;
let currentQuests = [];

/**
 * Open NPC modal
 */
export function openNPCModal(npcId) {
    fetch(`/game/npc/${npcId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentNPC = data.npc;
                currentDialogueTree = data.dialogue_trees;
                currentQuests = data.quests || [];
                displayNPCModal();
            } else {
                console.error('Failed to load NPC data');
            }
        })
        .catch(error => {
            console.error('Error loading NPC:', error);
        });
}

/**
 * Display NPC modal
 */
function displayNPCModal() {
    const modal = document.getElementById('npc-modal');
    const nameEl = document.getElementById('npc-name');
    const textEl = document.getElementById('npc-text');
    const portraitEl = document.getElementById('npc-portrait-img');
    const actionsEl = document.getElementById('npc-actions');
    const choicesEl = document.getElementById('npc-choices');

    // Set NPC name
    nameEl.textContent = currentNPC.name;

    // Set portrait
    if (currentNPC.texture) {
        portraitEl.src = '/' + currentNPC.texture;
        portraitEl.classList.remove('hidden');
        portraitEl.classList.add('block');
    } else {
        portraitEl.classList.remove('block');
        portraitEl.classList.add('hidden');
    }

    // Clear previous content
    actionsEl.innerHTML = '';
    choicesEl.innerHTML = '';
    choicesEl.classList.remove('flex');
    choicesEl.classList.add('hidden');
    actionsEl.classList.remove('hidden');
    actionsEl.classList.add('flex'); // Ensure actions are visible

    // Display initial greeting (support text override from backend)
    const npcRoles = Array.isArray(currentNPC.role) ? currentNPC.role : (String(currentNPC.role || '').split(',').map(r => r.trim()).filter(Boolean));
    textEl.textContent = currentNPC.active_quest_greeting || getGreeting(npcRoles);

    // Add action buttons based on roles
    if (npcRoles.includes('merchant')) {
        const merchantBtn = document.createElement('button');
        merchantBtn.className = 'bg-gradient-to-br from-amber-400 to-amber-500 border-none text-[#1a1a2e] px-8 py-4 rounded-lg text-base font-semibold cursor-pointer transition-all duration-300 shadow-[0_4px_15px_rgba(251,191,36,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(251,191,36,0.5)]';
        merchantBtn.textContent = '💰 Vous vendez ou rachetez des trucs ?';
        merchantBtn.onclick = () => openMerchantShop();
        actionsEl.appendChild(merchantBtn);
    }

    // Add dialogue button if dialogues exist
    if (currentDialogueTree && currentDialogueTree.length > 0) {
        const dialogueBtn = document.createElement('button');
        dialogueBtn.className = 'bg-gradient-to-br from-indigo-500/30 to-purple-500/30 text-gray-200 border-2 border-indigo-500/50 px-8 py-4 rounded-lg text-base font-semibold cursor-pointer transition-all duration-300 shadow-[0_4px_15px_rgba(251,191,36,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(251,191,36,0.5)]';
        dialogueBtn.textContent = '💬 Parler';
        dialogueBtn.onclick = () => startDialogue();
        actionsEl.appendChild(dialogueBtn);
    }

    // Add quest button if quest_giver AND has quests
    if (npcRoles.includes('quest_giver') && currentQuests.length > 0) {
        const questBtn = document.createElement('button');
        questBtn.className = 'bg-gradient-to-br from-amber-400 to-amber-500 border-none text-[#1a1a2e] px-8 py-4 rounded-lg text-base font-semibold cursor-pointer transition-all duration-300 shadow-[0_4px_15px_rgba(251,191,36,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(251,191,36,0.5)]';
        questBtn.textContent = '⚔️ Avez-vous des quêtes ?';
        questBtn.onclick = () => showQuests();
        actionsEl.appendChild(questBtn);
    }

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.classList.add('active');
    playSound('open');
}

// ... (getGreeting, startDialogue, displayDialogue, selectChoice, openMerchantShop remain same)

/**
 * Show quests
 */
function showQuests() {
    const textEl = document.getElementById('npc-text');
    const actionsEl = document.getElementById('npc-actions');
    const choicesEl = document.getElementById('npc-choices');

    actionsEl.classList.remove('flex');
    actionsEl.classList.add('hidden');
    choicesEl.innerHTML = '';
    choicesEl.classList.remove('hidden');
    choicesEl.classList.add('flex');

    textEl.textContent = "J'ai besoin d'aide pour quelques tâches...";

    currentQuests.forEach(quest => {
        const btn = document.createElement('button');
        btn.className = 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-2 border-indigo-500/50 text-gray-200 p-4 rounded-lg text-base cursor-pointer transition-all duration-300 text-left hover:from-indigo-500/40 hover:to-purple-500/40 hover:border-indigo-500 hover:translate-x-2.5';
        btn.textContent = `📜 ${quest.name} (Niv. ${quest.min_level})`;
        btn.onclick = () => showQuestIntro(quest);
        choicesEl.appendChild(btn);
    });

    // Back button
    const backBtn = document.createElement('button');
    backBtn.className = 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-2 border-indigo-500/50 text-gray-200 p-4 rounded-lg text-base cursor-pointer transition-all duration-300 text-left hover:from-indigo-500/40 hover:to-purple-500/40 hover:border-indigo-500 hover:translate-x-2.5';
    backBtn.textContent = '← Retour';
    backBtn.onclick = () => {
        actionsEl.classList.remove('hidden');
        actionsEl.classList.add('flex');
        choicesEl.classList.remove('flex');
        choicesEl.classList.add('hidden');
        displayNPCModal();
    };
    choicesEl.appendChild(backBtn);
}

/**
 * Show quest intro text
 */
function showQuestIntro(quest) {
    const textEl = document.getElementById('npc-text');
    const choicesEl = document.getElementById('npc-choices');

    textEl.textContent = quest.intro_text || quest.description;

    choicesEl.innerHTML = '';

    // Accept Button
    const acceptBtn = document.createElement('button');
    acceptBtn.className = 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-2 border-indigo-500/50 text-gray-200 p-4 rounded-lg text-base cursor-pointer transition-all duration-300 text-left hover:from-indigo-500/40 hover:to-purple-500/40 hover:border-indigo-500 hover:translate-x-2.5 border-l-4 border-l-green-500';
    acceptBtn.textContent = '✅ Accepter la quête';
    acceptBtn.onclick = () => acceptQuest(quest.id);
    choicesEl.appendChild(acceptBtn);

    // Decline Button
    const declineBtn = document.createElement('button');
    declineBtn.className = 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-2 border-indigo-500/50 text-gray-200 p-4 rounded-lg text-base cursor-pointer transition-all duration-300 text-left hover:from-indigo-500/40 hover:to-purple-500/40 hover:border-indigo-500 hover:translate-x-2.5 border-l-4 border-l-red-500';
    declineBtn.textContent = '❌ Refuser';
    declineBtn.onclick = () => showQuests(); // Go back to quest list
    choicesEl.appendChild(declineBtn);
}

/**
 * Accept quest
 */
function acceptQuest(questId) {
    fetch('/game/quest/accept', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ quest_id: questId })
    })
        .then(res => res.json())
        .then(async data => {
            if (data.success) {
                const { showToast } = await import('./toast.js');
                const { playSound } = await import('./soundManager.js');

                playSound('notification');
                showToast(`Quête acceptée : ${data.quest_name}`, 'quest');

                // Refresh Map Points to remove '!' icon
                if (window.loadMapPoints) {
                    const mapId = window.currentMapData ? window.currentMapData.mapId : 1;
                    window.loadMapPoints(mapId, window.characterId);
                }

                closeNPCModal();
            } else {
                const { showToast } = await import('./toast.js');
                showToast(data.message || 'Erreur', 'error');
            }
        })
        .catch(err => console.error(err));
}

/**
 * Get greeting based on NPC role
 */
function getGreeting(roles) {
    const greetings = {
        merchant: "Bienvenue dans ma boutique ! Je vends et rachète toutes sortes d'objets.",
        quest_giver: "Salutations, aventurier ! J'ai peut-être quelque chose pour toi.",
        lore: "Ah, un visiteur curieux ! Que puis-je t'apprendre aujourd'hui ?",
        guard: "Halte ! Que fais-tu ici ?"
    };

    // roles may be array or CSV string; prefer merchant, then quest_giver, then lore, then guard
    const ordered = ['merchant', 'quest_giver', 'lore', 'guard'];
    const r = Array.isArray(roles) ? roles : (String(roles || '').split(',').map(x => x.trim()).filter(Boolean));
    for (const key of ordered) {
        if (r.includes(key)) return greetings[key];
    }
    return "Bonjour, voyageur.";
}

/**
 * Start dialogue tree
 */
function startDialogue() {
    if (!currentDialogueTree || currentDialogueTree.length === 0) return;

    // Get first dialogue tree
    const tree = currentDialogueTree[0];
    currentDialogueTreeId = tree.id;

    // Load root dialogues
    fetch(`/game/dialogue/tree/${tree.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDialogue(data.dialogue);
            }
        })
        .catch(error => {
            console.error('Error loading dialogue:', error);
        });
}

/**
 * Display dialogue node
 */
function displayDialogue(dialogue) {
    const textEl = document.getElementById('npc-text');
    const choicesEl = document.getElementById('npc-choices');
    const actionsEl = document.getElementById('npc-actions');

    // Hide actions
    actionsEl.classList.remove('flex');
    actionsEl.classList.add('hidden');

    // Display dialogue text
    textEl.textContent = dialogue.text;

    // Display choices if available
    if (dialogue.children && dialogue.children.length > 0) {
        choicesEl.innerHTML = '';
        choicesEl.classList.remove('hidden');
        choicesEl.classList.add('flex');

        dialogue.children.forEach(choice => {
            if (choice.is_player_choice) {
                const btn = document.createElement('button');
                btn.className = 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-2 border-indigo-500/50 text-gray-200 p-4 rounded-lg text-base cursor-pointer transition-all duration-300 text-left hover:from-indigo-500/40 hover:to-purple-500/40 hover:border-indigo-500 hover:translate-x-2.5';
                btn.textContent = choice.choice_text || choice.text;
                btn.onclick = () => {
                    playSound('click');
                    selectChoice(choice);
                };
                choicesEl.appendChild(btn);
            }
        });
    } else {
        // No more choices - dialogue has ended
        // Call completeDialogue if this is a quest dialogue
        if (currentDialogueTreeId) {
            fetch('/game/dialogue/complete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tree_id: currentDialogueTreeId })
            })
                .then(res => res.json())
                .then(async data => {
                    if (data.success && data.quest_updated) {
                        try {
                            const { showToast } = await import('/js/modules/toast.js');
                            const { playSound } = await import('/js/modules/soundManager.js');

                            const update = data.quest_update;

                            if (update) {
                                // 1. Objective Updated / Completed
                                if (update.objective_completed) {
                                    playSound('notification');
                                    showToast(
                                        `<strong>${update.quest_name}</strong><br>Objectif validé : ${update.objective_description}`,
                                        'quest'
                                    );
                                }

                                // 2. Quest Completed
                                if (update.quest_completed) {
                                    setTimeout(() => {
                                        playSound('notification'); // Or a specific quest complete sound
                                        showToast(
                                            `<strong>${update.quest_name}</strong><br>Quête terminée !`,
                                            'quest'
                                        );
                                    }, 1000); // Delay slightly
                                }

                                // 3. Map Points Unlocked
                                if (update.unlocked_points && update.unlocked_points.length > 0) {
                                    update.unlocked_points.forEach((pointName, index) => {
                                        setTimeout(() => {
                                            playSound('notification');
                                            showToast(
                                                `<strong>Nouveau lieu découvert</strong><br>${pointName}`,
                                                'success'
                                            );
                                        }, 2000 + (index * 1000));
                                    });

                                    // Refresh map points if on map
                                    // We might need to trigger a map refresh event or call a global function
                                    if (window.loadMapPoints) {
                                        window.loadMapPoints();
                                    }
                                }
                            } else {
                                // Fallback for legacy response
                                showToast('✅ ' + data.message, 'success');
                            }
                        } catch (e) {
                            console.error('Error showing quest toast:', e);
                        }
                    }
                })
        }

        // Show back button
        choicesEl.innerHTML = '';
        const backBtn = document.createElement('button');
        backBtn.className = 'bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border-2 border-indigo-500/50 text-gray-200 p-4 rounded-lg text-base cursor-pointer transition-all duration-300 text-left hover:from-indigo-500/40 hover:to-purple-500/40 hover:border-indigo-500 hover:translate-x-2.5';
        backBtn.textContent = '← Retour';
        backBtn.onclick = () => {
            actionsEl.classList.remove('hidden');
            actionsEl.classList.add('flex');
            choicesEl.classList.remove('flex');
            choicesEl.classList.add('hidden');
            displayNPCModal();
        };
        choicesEl.appendChild(backBtn);
        choicesEl.classList.remove('hidden');
        choicesEl.classList.add('flex');
    }
}

/**
 * Select dialogue choice
 */
async function selectChoice(choice) {
    // 1. Execute Action via Backend
    try {
        const response = await fetch('/game/dialogue/select', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ dialogue_id: choice.id })
        });

        const data = await response.json();

        if (!data.success) {
            // Show error (e.g. Condition unmet)
            const { showToast } = await import('./toast.js');
            showToast('⛔ ' + data.message, 'error');
            return; // Do not proceed
        }

        // 2. Handle Action Result (Visual Feedback)
        if (data.action_result) {
            const res = data.action_result;
            const { showToast } = await import('./toast.js');
            const { playSound } = await import('./soundManager.js');

            if (res.gold_added) {
                playSound('coins');
                showToast(`+${res.gold_added} Or`, 'success');
            }
            if (res.gold_removed) {
                playSound('coins');
                showToast(`-${res.gold_removed} Or`, 'warning');
            }
            if (res.healed) {
                playSound('heal');
                showToast(`+${res.healed} PV`, 'heal');
            }
            if (res.damage) {
                playSound('hit');
                showToast(`-${res.damage} PV`, 'damage');
            }
            if (res.force_fight) {
                // Trigger Fight
                showToast('⚔️ Combat !', 'warning');
                // Redirect or trigger modal?
                // For now simple refresh to trigger encounter if logic handles it, or explicit call.
                // Given the flow, we might need a specific '/game/combat/start/{monsterId}' endpoint or similar.
                // But typically combat is random or story driven.
                // Let's assume we just note it for now, user didn't specific combat flow for dialogue yet.
            }
            if (res.reputation_modified) {
                const amount = res.reputation_modified;
                if (amount > 0) {
                    showToast(`+${amount} Réputation`, 'success');
                } else {
                    showToast(`${amount} Réputation`, 'warning');
                }
            }

            // Allow Quest triggers to show notifications via verifyQuest (or handled by backend logic/toast)
            // The backend startQuest returns ID, we might want to check returned updates.
        }

    } catch (e) {
        console.error('Dialogue action error:', e);
        return;
    }

    // 3. Proceed to Next Dialogue (Children)
    if (choice.children && choice.children.length > 0) {
        const nextDialogue = choice.children.find(c => !c.is_player_choice);
        if (nextDialogue) {
            displayDialogue(nextDialogue);
        }
    } else {
        // End of dialogue - check if quest-related
        if (currentDialogueTreeId) {
            fetch('/game/dialogue/complete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tree_id: currentDialogueTreeId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.quest_updated) {
                        if (window.showToast) {
                            window.showToast('✅ ' + data.message, 'success');
                        }
                    }
                    closeDialogue();
                })
                .catch(error => {
                    console.error('Error completing dialogue:', error);
                    closeDialogue();
                });
        } else {
            closeDialogue();
        }
    }
}

function closeDialogue() {
    const actionsEl = document.getElementById('npc-actions');
    const choicesEl = document.getElementById('npc-choices');
    actionsEl.classList.remove('hidden');
    actionsEl.classList.add('flex');
    choicesEl.classList.remove('flex');
    choicesEl.classList.add('hidden');
    currentDialogueTreeId = null;
    displayNPCModal();
}

/**
 * Open merchant shop
 */
function openMerchantShop() {
    if (currentNPC) {
        import('./shop.js').then(module => {
            module.openShop(currentNPC.id);
        });
    }
}



/**
 * Close NPC modal
 */
export function closeNPCModal() {
    const modal = document.getElementById('npc-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    modal.classList.remove('active');
    currentNPC = null;
    currentDialogueTree = null;
    playSound('close');
}

/**
 * Initialize NPC modal controls
 */
export function initNPCModal() {
    // Close on overlay click
    const overlay = document.querySelector('.npc-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeNPCModal);
    }

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !document.getElementById('npc-modal').classList.contains('hidden')) {
            closeNPCModal();
        }
    });
}

if (typeof window !== 'undefined') {
    window.openNPCModal = openNPCModal;
    window.closeNPCModal = closeNPCModal;
    window.initNPCModal = initNPCModal;
}
