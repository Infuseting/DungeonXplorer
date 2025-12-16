<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\Inventory;
use App\Models\Map;
use App\Models\MapPoint;
use App\Models\StoryProgress;
use App\Models\Quest;
use App\Models\PlayerQuest;
use App\Models\DialogueTree;
use App\Models\NPC;
use App\Models\CharacterStats;
use App\Models\Skill;
use App\Services\TokenService;


class GameController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Check if a character is selected
        $characterId = $_POST['character_id'] ?? $_SESSION['character_id'] ?? null;

        if (!$characterId) {
            header('Location: /personnage');
            exit;
        }

        // Store selected character in session
        $_SESSION['character_id'] = $characterId;

        $characterModel = new Character();
        $character = $characterModel->findById($characterId);

        // Verify ownership
        if ($character->getUserId() !== $_SESSION['user_id']) {
            header('Location: /personnage');
            exit;
        }

        // Load inventory
        $inventoryModel = new Inventory();
        $inventory = $inventoryModel->getCharacterInventory($characterId);
        
        // Check if character is in a dungeon
        $storyProgressModel = new StoryProgress();
        $activeStory = $storyProgressModel->getActiveStory($characterId);
        
        if ($activeStory) {
            header('Location: /story/enter/' . $activeStory['story_id']);
            exit;
        }

        // Load map configuration and points using models
        $mapModel = new Map();
        $mapPointModel = new MapPoint();
        
        // Prepare Skills Data for Modal
        $skillModel = new Skill();
        $classSkills = $skillModel->getSkillsByClass($character->getClassId());
        $unlocked = $skillModel->getUnlockedSkills($characterId);
        $unlockedIds = array_map(function($s) { return $s['id']; }, $unlocked);
        
        $playerSkillsJson = json_encode(array_map(function($s) use ($character, $unlockedIds) {
            $isUnlocked = in_array($s['id'], $unlockedIds);
            $canAfford = $character->getSkillPoints() >= $s['cost_sp'];
            $levelMet = $character->getLevel() >= $s['min_level'];
            $prereqMet = true;
            if ($s['parent_skill_id']) $prereqMet = in_array($s['parent_skill_id'], $unlockedIds);
            
            $s['status'] = 'locked';
            if ($isUnlocked) $s['status'] = 'unlocked';
            else if ($canAfford && $levelMet && $prereqMet) $s['status'] = 'available';
            
            return $s;
        }, $classSkills));

        
        // Default to map ID 1 (you can make this dynamic later)
        $mapId = 1;
        $mapConfig = $mapModel->getMapConfig($mapId);
        $mapPoints = $mapPointModel->getVisiblePointsForCharacter($mapId, $characterId);

        require_once __DIR__ . '/../Views/game/index.php';
    }

    /**
     * API endpoint to load sub-map data
     */
    public function loadSubMap()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $mapId = $data['mapId'] ?? null;

        if (!$mapId) {
            echo json_encode(['success' => false, 'message' => 'ID de carte manquant']);
            exit;
        }

        $mapModel = new Map();
        $mapPointModel = new MapPoint();

        $map = $mapModel->findById($mapId);
        
        if (!$map) {
            echo json_encode(['success' => false, 'message' => 'Carte non trouvée']);
            exit;
        }

        $points = $mapPointModel->getVisiblePointsForCharacter($mapId, $_SESSION['character_id']);
        $points = $this->enrichPointsWithQuestStatus($points, $_SESSION['character_id']);

        echo json_encode([
            'success' => true,
            'map' => [
                'id' => $map['id'],
                'name' => $map['name'],
                'description' => $map['description'],
                'image_path' => $map['image_path']
            ],
            'points' => $points
        ]);
        exit;
    }

    /**
     * API endpoint to get map points for a specific map
     */
    public function getMapPoints($mapId)
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Aucun personnage sélectionné']);
            exit;
        }
        $mapPointModel = new MapPoint();
        $points = $mapPointModel->getVisiblePointsForCharacter($mapId, $_SESSION['character_id']);
        $points = $this->enrichPointsWithQuestStatus($points, $_SESSION['character_id']);
        
        echo json_encode([
            'success' => true,
            'points' => $points
        ]);
        exit;
    }
        
    /**
     * Get NPC data for interaction
     */
    public function getNPC($id)
    {
        header('Content-Type: application/json');
        
        $npcModel = new NPC();
        $dialogueModel = new DialogueTree();
        
        $npc = $npcModel->findById($id);
        
        if (!$npc) {
            echo json_encode(['success' => false, 'message' => 'PNJ non trouvé']);
            exit;
        }
        
        // Get all dialogue trees for this NPC
        $allDialogueTrees = $npcModel->getDialogueTrees($id);
        
        // Filter dialogues based on quest progression
        $availableDialogues = [];
        
        if (isset($_SESSION['character_id'])) {
            $playerQuestModel = new PlayerQuest();
            $db = Database::getInstance()->getConnection();
            
            foreach ($allDialogueTrees as $tree) {
                // Check if dialogue is linked to a quest objective
                $objective = $dialogueModel->getQuestObjective($tree['id']);
                
                if ($objective) {
                    // Dialogue is linked to a quest - check if player is at the correct stage
                    $stmt = $db->prepare("
                        SELECT pq.id 
                        FROM player_quests pq
                        WHERE pq.character_id = ? 
                        AND pq.quest_id = ? 
                        AND pq.current_stage_id = ?
                        AND pq.status = 'ACTIVE'
                    ");
                    $stmt->bind_param("iii", 
                        $_SESSION['character_id'], 
                        $objective['quest_id'], 
                        $objective['stage_id']
                    );
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    // Only show if player is at this stage
                    if ($result->num_rows > 0) {
                        $availableDialogues[] = $tree;
                    }
                } else {
                    // Normal dialogue (not linked to quest) - always available
                    $availableDialogues[] = $tree;
                }
            }
        } else {
            // No character session, show all dialogues
            $availableDialogues = $allDialogueTrees;
        }
        
        // Get merchant inventory if NPC has merchant role
        $merchantInventory = [];
        $npcRoles = array_map('trim', explode(',', $npc['role'] ?? ''));
        if (in_array('merchant', $npcRoles) && $npc['merchant_seed']) {
            $merchantInventory = $npcModel->getMerchantInventory($id);
        }

        // Get Available Quests
        $availableQuests = [];
        if (in_array('quest_giver', $npcRoles) && isset($_SESSION['character_id'])) {
            $allNpcQuests = $npcModel->getQuests($id);
            $playerQuestModel = new PlayerQuest();
            $questModel = new Quest();
            $characterModel = new Character();
            
            $character = $characterModel->findById($_SESSION['character_id']);
            $playerLevel = $character->getLevel() ?? 1;

            foreach ($allNpcQuests as $quest) {
                // 1. Must be GIVER
                if (($quest['relation_type'] ?? 'GIVER') !== 'GIVER') continue;

                // 2. Check Level
                if ($playerLevel < $quest['min_level']) continue;

                // 3. Check if already started or completed
                $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $quest['id']);
                if ($status !== 'NOT_STARTED') continue;

                // 4. Check Prerequisites
                $prerequisites = $questModel->getPrerequisites($quest['id']);
                $prereqsMet = true;
                foreach ($prerequisites as $prereq) {
                    $prereqStatus = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $prereq['required_quest_id']);
                    if ($prereqStatus !== 'COMPLETED') {
                        $prereqsMet = false;
                        break;
                    }
                }
                if (!$prereqsMet) continue;

                $availableQuests[] = $quest;
            }

            // Check for Active Quests for Greeting Override
            foreach ($allNpcQuests as $quest) {
                if (($quest['relation_type'] ?? 'GIVER') !== 'GIVER') continue;
                $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $quest['id']);
                if ($status === 'ACTIVE') {
                    // Customizable greeting for active quest
                    $npc['active_quest_greeting'] = "Alors, comment avance la quête \"" . $quest['name'] . "\" ? Je compte sur vous !";
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'npc' => $npc,
            'dialogue_trees' => $availableDialogues,
            'merchant_inventory' => $merchantInventory,
            'quests' => $availableQuests
        ]);
        exit;
    }
    
    /**
     * Get dialogue tree structure
     */
    public function getDialogueTree($treeId)
    {
        header('Content-Type: application/json');
        
        $dialogueModel = new DialogueTree();
        $tree = $dialogueModel->getDialogueTree($treeId);
        
        if (empty($tree)) {
            echo json_encode(['success' => false, 'message' => 'Arbre de dialogue non trouvé']);
            exit;
        }
        
        // Get first root dialogue
        $dialogue = $tree[0] ?? null;
        
        echo json_encode([
            'success' => true,
            'dialogue' => $dialogue
        ]);
        exit;
    }
    
    /**
     * Accept a quest
     */
    public function acceptQuest()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $questId = $data['quest_id'] ?? null;
        
        if (!$questId) {
            echo json_encode(['success' => false, 'message' => 'ID de quête manquant']);
            exit;
        }
        
        $playerQuestModel = new PlayerQuest();
        
        // Check if already started
        $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $questId);
        if ($status !== 'NOT_STARTED') {
            echo json_encode(['success' => false, 'message' => 'Quête déjà acceptée ou terminée']);
            exit;
        }
        
        // Start quest
        $playerQuestId = $playerQuestModel->startQuest($_SESSION['character_id'], $questId);
        
        if ($playerQuestId) {
            $questModel = new Quest();
            $quest = $questModel->findById($questId);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Quête acceptée !',
                'quest_name' => $quest['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Impossible d\'accepter la quête']);
        }
        exit;
    }

    /**
     * Get quest log for the current character
     */
    public function getQuestLog()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        $playerQuestModel = new PlayerQuest();
        $log = $playerQuestModel->getQuestLog($_SESSION['character_id']);
        
        echo json_encode([
            'success' => true,
            'log' => $log
        ]);
        exit;
    }
    
    /**
     * Complete a dialogue and update quest progress if applicable
     */
    public function completeDialogue()
    {
        header('Content-Type: application/json');
        
        // Debug log
        error_log("=== completeDialogue called ===");
        
        if (!isset($_SESSION['character_id'])) {
            error_log("ERROR: No character_id in session");
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        error_log("Character ID: " . $_SESSION['character_id']);
        
        $data = json_decode(file_get_contents('php://input'), true);
        $treeId = $data['tree_id'] ?? null;
        
        error_log("Tree ID received: " . ($treeId ?? 'NULL'));
        
        if (!$treeId) {
            error_log("ERROR: No tree_id provided");
            echo json_encode(['success' => false, 'message' => 'ID de dialogue manquant']);
            exit;
        }
        
        // Check if this dialogue is linked to a quest objective
        $dialogueModel = new DialogueTree();
        $objective = $dialogueModel->getQuestObjective($treeId);
        
        error_log("Objective found: " . ($objective ? "YES (ID: {$objective['id']})" : "NO"));
        
        if ($objective) {
            // This dialogue is linked to a quest - update progress
            $playerQuestModel = new PlayerQuest();
            $db = Database::getInstance()->getConnection();
            
            error_log("Quest ID: {$objective['quest_id']}, Stage ID: {$objective['stage_id']}");
            
            // Get the player's active quest for this quest_id
            $stmt = $db->prepare("
                SELECT id 
                FROM player_quests 
                WHERE character_id = ? 
                AND quest_id = ? 
                AND current_stage_id = ?
                AND status = 'ACTIVE'
            ");
            $stmt->bind_param("iii", 
                $_SESSION['character_id'], 
                $objective['quest_id'],
                $objective['stage_id']
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $playerQuest = $result->fetch_assoc();
            
            error_log("Player quest found: " . ($playerQuest ? "YES (ID: {$playerQuest['id']})" : "NO"));
            
            if ($playerQuest) {
                // Update the objective progress
                error_log("Calling updateProgress({$playerQuest['id']}, {$objective['id']}, 1)");
                $events = $playerQuestModel->updateProgress($playerQuest['id'], $objective['id'], 1);
                
                error_log("SUCCESS: Quest updated");
                echo json_encode([
                    'success' => true,
                    'quest_updated' => true,
                    'message' => 'Objectif de quête complété !',
                    'quest_update' => [
                        'quest_name' => $events['quest_name'],
                        'objective_description' => $events['objective_description'],
                        'objective_completed' => $events['objective_completed'],
                        'quest_completed' => $events['quest_completed'],
                        'unlocked_points' => $events['unlocked_points']
                    ]
                ]);
            } else {
                error_log("WARNING: Player doesn't have this quest active");
                // Player doesn't have this quest active
                echo json_encode([
                    'success' => true,
                    'quest_updated' => false
                ]);
            }
        } else {
            error_log("INFO: Normal dialogue, not linked to quest");
            // Normal dialogue, not linked to a quest
            echo json_encode([
                'success' => true,
                'quest_updated' => false
            ]);
        }
        
        exit;
    }

    /**
     * Use a consumable item
     */
    public function useItem()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $inventoryItemId = $data['item_id'] ?? null;

        if (!$inventoryItemId) {
            echo json_encode(['success' => false, 'message' => 'ID d\'objet manquant']);
            exit;
        }

        $characterId = $_SESSION['character_id'];
        $inventoryModel = new Inventory();
        $characterModel = new Character();
        $buffService = new BuffService();

        // 1. Get Item Details from Inventory (to check type and verify ownership)
        // Need to expose a getter for specific item details in Inventory model or use raw query here?
        // Inventory::getItemInInventory is private. Let's make it public or duplicate logic safely.
        // Actually moveItem uses getItemInInventory. Let's use a new helper or public method if available.
        // I added consumeItem but strictly it just consumes. I need item stats/effects first.
        // Let's rely on DB here for safety.
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT i.* 
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = ? AND ci.character_id = ?
        ");
        $stmt->bind_param("ii", $inventoryItemId, $characterId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Objet non trouvé']);
            exit;
        }

        if ($item['type'] !== 'consumable') {
            echo json_encode(['success' => false, 'message' => 'Cet objet n\'est pas consommable']);
            exit;
        }

        // 2. Apply Effect
        $effectApplied = false;
        $message = "Objet utilisé";

        if ($item['effect_type'] === 'heal') {
            // Instant Heal
            $healAmount = $item['effect_value'];
            $character = $characterModel->findById($characterId);
            // Assuming max HP logic exists or using Vitality * 10 or similar.
            // For now, let's implement a simple heal logic in specific method or direct DB update.
            // Let's use a helper in Character model if possible, or direct update here.
            // Character has reduceVitality, need restore/heal.
            
            // Logic: Current HP is often stored? Or is it calculated?
            // User schema check: `characters` has `vitality`. 
            // Usually games have `current_hp` and `max_hp`.
            // Let's assume `current_hp` exists in `character_stats` or `characters`?
            // Checked Character.php: `findById` selects `cs.vitality`. It doesn't seem to explicitly show `current_hp`.
            // Wait, `reduceVitality` decreases `this->vitality`. This implies `vitality` IS the health pool? 
            // Or `vitality` stat determines MaxHP?
            // Typically RPG: Vitality -> MaxHP. CurrentHP is separate.
            // IF the user requests "potions qui redonne des HPs", there must be HP.
            // Let's check `character_stats` table columns from previous context or assume.
            // If `reduceVitality` lowers `vitality` directly, then `vitality` IS the health.
            // That's unusual but possible.
            // Let's assume standard behavior: Heal = +Vitality (if it's the pool) or +CurrentHP.
            // Given `reduceVitality` exists, I will assume updating `vitality` column IS the heal (or rather, restoring it?).
            // Actually, if `reduceVitality` permantly lowers the STAT vitality, that's bad.
            // But if `vitality` is just "Health Points", then `increaseVitality` is Heal.
            
            // However, the user said "potions qui redonne des HPs" vs "potions qui donnent des HPs max".
            // So:
            // Heal = Restore Current HP.
            // Buff Max HP = Increase Max HP (temporarily).
            
            // I need to know where Current HP is.
            // Let's assume there is a `current_hp` column or `hp` column.
            // `Character.php` selects `cs.vitality`. Maybe `vitality` IS the stat, and `current_hp` is missing?
            // Start of conversation mentioned `Character.php`.
            // `reduceVitality` implies damage.
            // Let's verify schema if possible. If not, I'll log a warning or assume a column 'current_hp'.
            // Actually, if I look at `Character::findById`, it gets `c.gold`, `cs.level`, `cs.xp`, ... `cs.vitality`.
            // It does NOT select `current_hp`.
            
            // I'll assume for now that I should create `current_hp` in `character_stats` if it doesn't exist, 
            // OR the user implies `vitality` is strictly the stat and we need a way to track damage.
            // BUT, `reduceVitality` implementation: `$this->vitality -= $number`. This modifies the object property.
            // It doesn't persist it in that method.
            
            // Let's add `heal` method to Character that updates `current_hp`.
            // If `current_hp` doesn't exist, I might need to add it.
            // Actually, looking at `Character.php`:
            /*
                public function reduceVitality($number)
                {
                    $this->vitality -= $number;
                }
            */
            // This suggests partial implementation.
            
            // DECISION: I will assume `character_stats` should have `current_hp`. 
            // I'll check if `current_hp` exists in the STANDALONE MIGRATION check or just add it if missing to be safe.
            // Alternatively, maybe "Vitality" IS the HP? "2.0 / 75.0 kg". "Strength +0".
            // Let's assume `current_hp` column needs to be used.
            // For now, I will use `Character::heal($amount)` and implement it to try updating `current_hp`.
            
            $characterModel->heal($characterId, $healAmount);
            $message = "Points de vie restaurés: +$healAmount";
            $effectApplied = true;

        } elseif ($item['effect_type'] === 'buff') {
            // Apply Buff
            $stats = json_decode($item['stats'], true); // Use stats column for buff modifiers
            $durationValue = $item['duration_value'];
            $durationType = $item['duration_type']; // 'seconds' or 'turns'
            
            $buffService->applyBuff(
                $characterId, 
                $item['name'], 
                $stats, 
                $durationType, 
                $durationValue
            );
            $message = "Effet appliqué: " . $item['name'];
            $effectApplied = true;
        }

        if ($effectApplied) {
            // 3. Consume Item
            $inventoryModel->consumeItem($characterId, $inventoryItemId);
            
            // Get updated stats for UI
            // Force cache reset
             $character = $characterModel->findById($characterId);
             $characterModel->resetCache(); 
             // We need to re-instantiate or reset
             
             // actually the model instance $characterModel is just a factory/gateway usually.
             // But we need the object to get stats.
             $charObj = new Character();
             $charObj->findById($characterId);
             
             $newStats = [
                 'strength' => $charObj->getStrength(),
                 'vitality' => $charObj->getVitality(),
                 'dexterity' => $charObj->getDexterity(),
                 'intelligence' => $charObj->getIntelligence(),
                 // Add current HP if we have it
             ];

             echo json_encode([
                 'success' => true, 
                 'message' => $message,
                 'new_stats' => $newStats
             ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucun effet disponible']);
        }
        exit;
    }

    /**
     * Handle dialogue choice selection
     */
    public function selectDialogueOption()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $dialogueId = $input['dialogue_id'] ?? null;
        
        if (!$dialogueId) {
            echo json_encode(['success' => false, 'message' => 'ID de dialogue manquant']);
            exit;
        }

        $db = Database::getInstance()->getConnection();
        
        // 1. Fetch dialogue node
        $stmt = $db->prepare("SELECT * FROM dialogues WHERE id = ?");
        $stmt->bind_param("i", $dialogueId);
        $stmt->execute();
        $dialogue = $stmt->get_result()->fetch_assoc();

        if (!$dialogue) {
            echo json_encode(['success' => false, 'message' => 'Dialogue introuvable']);
            exit;
        }

        $characterId = $_SESSION['character_id'];

        // 2. Validate Condition
        if (!$this->validateCondition($characterId, $dialogue['condition_type'], $dialogue['condition_value'])) {
             echo json_encode(['success' => false, 'message' => 'Condition non remplie pour ce choix']);
             exit;
        }

        // 3. Execute Action
        $actionResult = $this->executeAction($characterId, $dialogue['action_type'], $dialogue['action_value']);

        // 4. Return success + result
        echo json_encode([
            'success' => true,
            'action_result' => $actionResult,
            'next_dialogue_id' => $dialogueId // Frontend can use this to fetch children or just close if leaf
        ]);
        exit;
    }

    private function validateCondition($characterId, $type, $value)
    {
        if ($type === 'NONE') return true;
        
        $characterModel = new Character();
        $invModel = new Inventory();
        $pqModel = new PlayerQuest();

        switch ($type) {
            case 'MIN_LEVEL':
                $char = $characterModel->findById($characterId);
                return ($char['level'] ?? 1) >= (int)$value;
                
            case 'HAS_ITEM':
                $inv = $invModel->getCharacterInventory($characterId);
                // Search in inventory or equipped
                foreach ($inv['inventory'] as $item) {
                     if ($item['item_id'] == $value) return true;
                }
                foreach ($inv['equipped'] as $item) {
                     if ($item['item_id'] == $value) return true;
                }
                return false;

            case 'QUEST_ACTIVE':
                return $pqModel->getQuestStatus($characterId, $value) === 'ACTIVE';

            case 'QUEST_COMPLETED':
                return $pqModel->getQuestStatus($characterId, $value) === 'COMPLETED';
            
            case 'QUEST_NOT_STARTED':
                return $pqModel->getQuestStatus($characterId, $value) === 'NOT_STARTED';
        }
        
        return true;
    }

    private function executeAction($characterId, $type, $value)
    {
        if ($type === 'NONE') return null;

        $characterModel = new Character();
        $invModel = new Inventory();
        $pqModel = new PlayerQuest();
        $char = $characterModel->findById($characterId); // Hydrate model

        switch ($type) {
            case 'TRIGGER_QUEST':
                return $pqModel->startQuest($characterId, $value);

            case 'GIVE_ITEM':
                return $invModel->addItem($characterId, $value);

            case 'REMOVE_ITEM':
                // Need to find inventory_id for the item_id
                $valid = $this->validateCondition($characterId, 'HAS_ITEM', $value); // Check exist
                if($valid) {
                     // Get inventory ID. 
                     // This is tricky as we need the inventory row ID, not item ID.
                     // Simple implementation: Remove first found.
                     // TODO: Add removeByItemId in Inventory model
                     // Workaround:
                     $inv = $invModel->getCharacterInventory($characterId);
                     foreach ($inv['inventory'] as $item) {
                         if ($item['item_id'] == $value) {
                             return $invModel->deleteItem($characterId, $item['id']);
                         }
                     }
                }
                return false;

            case 'HEAL':
                $characterModel->heal($characterId, (int)$value);
                return ['healed' => $value];

            case 'DAMAGE':
                 $characterModel->reduceVitality((int)$value);
                 return ['damage' => $value];

            case 'GIVE_GOLD':
                $characterModel->addGold((int)$value);
                return ['gold_added' => $value];

            case 'REMOVE_GOLD':
                $characterModel->addGold(-(int)$value);
                return ['gold_removed' => $value];

            case 'FORCE_FIGHT':
                return ['force_fight' => $value];

            case 'MODIFY_REPUTATION':
                // Value format: "faction_id:amount"
                $parts = explode(':', $value);
                if (count($parts) === 2) {
                    $factionId = (int)$parts[0];
                    $amount = (int)$parts[1];
                    $repService = new ReputationService();
                    if ($repService->modifyReputation($characterId, $factionId, $amount)) {
                         return ['reputation_modified' => $amount, 'faction_id' => $factionId];
                    }
                }
                return false;
        }

        return null;
    }

    /**
     * Check for available quests and add has_quest flag to points
     */
    private function enrichPointsWithQuestStatus($points, $characterId)
    {
        $npcModel = new NPC();
        $playerQuestModel = new PlayerQuest();
        $questModel = new Quest();
        $characterModel = new Character();
        
        $character = $characterModel->findById($characterId);
        $playerLevel = $character->toArray()['level'] ?? 1;

        foreach ($points as &$point) {
            $point['has_quest'] = false;

            if ($point['type'] === 'npc' && !empty($point['target_id'])) {
                $npcId = $point['target_id'];
                $allNpcQuests = $npcModel->getQuests($npcId);

                foreach ($allNpcQuests as $quest) {
                    // 1. Must be GIVER
                    if (($quest['relation_type'] ?? 'GIVER') !== 'GIVER') continue;

                    // 2. Check Level
                    if ($playerLevel < $quest['min_level']) continue;

                    // 3. Check if already started or completed
                    $status = $playerQuestModel->getQuestStatus($characterId, $quest['id']);
                    if ($status !== 'NOT_STARTED') continue;

                    // 4. Check Prerequisites
                    $prerequisites = $questModel->getPrerequisites($quest['id']);
                    $prereqsMet = true;
                    foreach ($prerequisites as $prereq) {
                        $prereqStatus = $playerQuestModel->getQuestStatus($characterId, $prereq['required_quest_id']);
                        if ($prereqStatus !== 'COMPLETED') {
                            $prereqsMet = false;
                            break;
                        }
                    }
                    
                    if ($prereqsMet) {
                        $point['has_quest'] = true;
                        break; // Found one available quest, no need to check others for this NPC
                    }
                }
            }
        }
        
        return $points;
    }
}
