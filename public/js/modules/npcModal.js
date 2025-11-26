/**
 * NPC Modal Module
 * Handles NPC interactions with visual novel style interface
 */

let currentNPC = null;
let currentDialogueTree = null;

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
        portraitEl.style.display = 'block';
    } else {
        portraitEl.style.display = 'none';
    }

    // Clear previous content
    actionsEl.innerHTML = '';
    choicesEl.innerHTML = '';
    choicesEl.style.display = 'none';

    // Display initial greeting (support multiple roles stored as CSV)
    const npcRoles = Array.isArray(currentNPC.role) ? currentNPC.role : (String(currentNPC.role || '').split(',').map(r => r.trim()).filter(Boolean));
    textEl.textContent = getGreeting(npcRoles);

    // Add action buttons based on roles
    if (npcRoles.includes('merchant')) {
        const merchantBtn = document.createElement('button');
        merchantBtn.className = 'npc-action-btn';
        merchantBtn.textContent = '💰 Vous vendez ou rachetez des trucs ?';
        merchantBtn.onclick = () => openMerchantShop();
        actionsEl.appendChild(merchantBtn);
    }

    // Add dialogue button if dialogues exist
    if (currentDialogueTree && currentDialogueTree.length > 0) {
        const dialogueBtn = document.createElement('button');
        dialogueBtn.className = 'npc-action-btn secondary';
        dialogueBtn.textContent = '💬 Parler';
        dialogueBtn.onclick = () => startDialogue();
        actionsEl.appendChild(dialogueBtn);
    }

    // Add quest button if quest_giver
    if (npcRoles.includes('quest_giver')) {
        const questBtn = document.createElement('button');
        questBtn.className = 'npc-action-btn';
        questBtn.textContent = '⚔️ Avez-vous des quêtes ?';
        questBtn.onclick = () => showQuests();
        actionsEl.appendChild(questBtn);
    }

    // Show modal
    modal.style.display = 'flex';
    modal.classList.add('active');
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
    actionsEl.style.display = 'none';

    // Display dialogue text
    textEl.textContent = dialogue.text;

    // Display choices if available
    if (dialogue.children && dialogue.children.length > 0) {
        choicesEl.innerHTML = '';
        choicesEl.style.display = 'flex';

        dialogue.children.forEach(choice => {
            if (choice.is_player_choice) {
                const btn = document.createElement('button');
                btn.className = 'npc-choice-btn';
                btn.textContent = choice.choice_text || choice.text;
                btn.onclick = () => selectChoice(choice);
                choicesEl.appendChild(btn);
            }
        });
    } else {
        // No more choices, show back button
        choicesEl.innerHTML = '';
        const backBtn = document.createElement('button');
        backBtn.className = 'npc-choice-btn';
        backBtn.textContent = '← Retour';
        backBtn.onclick = () => {
            actionsEl.style.display = 'flex';
            choicesEl.style.display = 'none';
            displayNPCModal();
        };
        choicesEl.appendChild(backBtn);
        choicesEl.style.display = 'flex';
    }
}

/**
 * Select dialogue choice
 */
function selectChoice(choice) {
    // If choice has children, display next dialogue
    if (choice.children && choice.children.length > 0) {
        const nextDialogue = choice.children.find(c => !c.is_player_choice);
        if (nextDialogue) {
            displayDialogue(nextDialogue);
        }
    } else {
        // End of dialogue, go back
        const actionsEl = document.getElementById('npc-actions');
        const choicesEl = document.getElementById('npc-choices');
        actionsEl.style.display = 'flex';
        choicesEl.style.display = 'none';
        displayNPCModal();
    }
}

/**
 * Open merchant shop
 */
function openMerchantShop() {
    // TODO: Implement merchant shop interface
    console.log('Opening merchant shop for NPC:', currentNPC.id);
    alert('Interface marchand à venir !');
}

/**
 * Show quests
 */
function showQuests() {
    // TODO: Implement quest interface
    console.log('Showing quests for NPC:', currentNPC.id);
    alert('Interface quêtes à venir !');
}

/**
 * Close NPC modal
 */
export function closeNPCModal() {
    const modal = document.getElementById('npc-modal');
    modal.style.display = 'none';
    modal.classList.remove('active');
    currentNPC = null;
    currentDialogueTree = null;
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
        if (e.key === 'Escape' && document.getElementById('npc-modal').style.display === 'flex') {
            closeNPCModal();
        }
    });
}

if (typeof window !== 'undefined') {
    window.openNPCModal = openNPCModal;
    window.closeNPCModal = closeNPCModal;
    window.initNPCModal = initNPCModal;
}
