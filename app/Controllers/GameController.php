<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\Inventory;
use App\Models\Map;
use App\Models\MapPoint;

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
        if ($character['user_id'] !== $_SESSION['user_id']) {
            header('Location: /personnage');
            exit;
        }

        // Load inventory
        $inventoryModel = new Inventory();
        $inventory = $inventoryModel->getCharacterInventory($characterId);
        
        // Check if character is in a dungeon
        $storyProgressModel = new \App\Models\StoryProgress();
        $activeStory = $storyProgressModel->getActiveStory($characterId);
        
        if ($activeStory) {
            header('Location: /story/enter/' . $activeStory['story_id']);
            exit;
        }

        // Load map configuration and points using models
        $mapModel = new Map();
        $mapPointModel = new MapPoint();
        
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

        $mapModel = new \App\Models\Map();
        $mapPointModel = new \App\Models\MapPoint();

        $map = $mapModel->findById($mapId);
        
        if (!$map) {
            echo json_encode(['success' => false, 'message' => 'Carte non trouvée']);
            exit;
        }

        $points = $mapPointModel->getVisiblePointsForCharacter($mapId, $_SESSION['character_id']);

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
        $mapPointModel = new \App\Models\MapPoint();
        $points = $mapPointModel->getVisiblePointsForCharacter($mapId, $_SESSION['character_id']);
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
        
        $npcModel = new \App\Models\NPC();
        $dialogueModel = new \App\Models\DialogueTree();
        
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
            $playerQuestModel = new \App\Models\PlayerQuest();
            $db = \App\Config\Database::getInstance()->getConnection();
            
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
            $playerQuestModel = new \App\Models\PlayerQuest();
            $questModel = new \App\Models\Quest();
            $characterModel = new \App\Models\Character();
            
            $character = $characterModel->findById($_SESSION['character_id']);
            $playerLevel = $character['level'] ?? 1;

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
        
        $dialogueModel = new \App\Models\DialogueTree();
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
        
        $playerQuestModel = new \App\Models\PlayerQuest();
        
        // Check if already started
        $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $questId);
        if ($status !== 'NOT_STARTED') {
            echo json_encode(['success' => false, 'message' => 'Quête déjà acceptée ou terminée']);
            exit;
        }
        
        // Start quest
        $playerQuestId = $playerQuestModel->startQuest($_SESSION['character_id'], $questId);
        
        if ($playerQuestId) {
            $questModel = new \App\Models\Quest();
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
        
        $playerQuestModel = new \App\Models\PlayerQuest();
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
        $dialogueModel = new \App\Models\DialogueTree();
        $objective = $dialogueModel->getQuestObjective($treeId);
        
        error_log("Objective found: " . ($objective ? "YES (ID: {$objective['id']})" : "NO"));
        
        if ($objective) {
            // This dialogue is linked to a quest - update progress
            $playerQuestModel = new \App\Models\PlayerQuest();
            $db = \App\Config\Database::getInstance()->getConnection();
            
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
}
