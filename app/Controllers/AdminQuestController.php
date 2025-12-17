<?php

namespace App\Controllers;
use App\Config\Database;
use App\Models\Quest;
use App\Models\QuestStage;

class AdminQuestController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * List all quests
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        
        $questModel = new Quest();
        
        $query = "SELECT q.*, 
                  (SELECT COUNT(*) FROM quest_stages WHERE quest_id = q.id) as stage_count
                  FROM quests q WHERE 1=1";
        $params = [];
        $types = '';
        
        if ($search) {
            $query .= " AND q.name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }
        
        $query .= " ORDER BY q.created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $quests = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->db->query($query);
            $quests = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        require_once __DIR__ . '/../Views/admin/quests/index.php';
    }
    
    /**
     * Create new quest
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questModel = new Quest();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'min_level' => (int)($_POST['min_level'] ?? 1),
                'intro_text' => $_POST['intro_text'] ?? '',
                'xp_reward' => (int)($_POST['xp_reward'] ?? 0),
                'gold_reward' => (int)($_POST['gold_reward'] ?? 0)
            ];
            
            $questId = $questModel->create($data);
            
            if ($questId) {
                header('Location: /admin/quests/edit/' . $questId);
                exit;
            }
        }
        
        require_once __DIR__ . '/../Views/admin/quests/create.php';
    }
    
    /**
     * Edit quest
     */
    public function edit($id)
    {
        $questModel = new Quest();
        $quest = $questModel->getFullQuest($id);
        
        if (!$quest) {
            header('Location: /admin/quests');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'min_level' => (int)($_POST['min_level'] ?? 1),
                'intro_text' => $_POST['intro_text'] ?? '',
                'xp_reward' => (int)($_POST['xp_reward'] ?? 0),
                'gold_reward' => (int)($_POST['gold_reward'] ?? 0)
            ];
            
            $questModel->update($id, $data);
            header('Location: /admin/quests');
            exit;
        }
        
        // Get NPCs for assignment
        $npcModel = new NPC();
        $npcs = $npcModel->getAll();
        
        // Get assigned NPCs
        $stmt = $this->db->prepare("
            SELECT n.*, nq.type 
            FROM npc_quests nq
            JOIN npcs n ON nq.npc_id = n.id
            WHERE nq.quest_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $assignedNPCs = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get map points for unlock selection
        $mapPointModel = new MapPoint();
        $stmt = $this->db->prepare("SELECT id, name, map_id FROM map_points ORDER BY map_id, name");
        $stmt->execute();
        $allMapPoints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Load unlocks for each stage
        $stageModel = new QuestStage();
        if (!empty($quest['stages'])) {
            foreach ($quest['stages'] as &$stage) {
                $stage['unlocks'] = $stageModel->getMapUnlocks($stage['id']);
            }
            unset($stage); // Important: destroy the reference to avoid issues
        }

        // Load prerequisites
        $prerequisites = $questModel->getPrerequisites($id);
        $allQuests = $questModel->getAll();
        
        // Load dialogue trees for objective assignment
        $dialogueModel = new DialogueTree();
        $dialogueTrees = $dialogueModel->getAll();
        
        // Load NPCs for TALK_NPC objectives
        $npcModel = new NPC();
        $allNPCs = $npcModel->getAll();

        // Load all items for rewards selection
        $itemModel = new Item();
        $allItems = $itemModel->getAll();
        
        require_once __DIR__ . '/../Views/admin/quests/edit.php';
    }
    
    /**
     * Delete quest
     */
    public function delete($id)
    {
        $questModel = new Quest();
        $questModel->delete($id);
        
        header('Location: /admin/quests');
        exit;
    }

    /**
     * Add reward item (API)
     */
    public function addRewardItem()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['quest_id']) || empty($data['item_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $questModel = new Quest();
            $success = $questModel->addRewardItem((int)$data['quest_id'], (int)$data['item_id'], (int)($data['quantity'] ?? 1));
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }

    /**
     * Remove reward item (API)
     */
    public function removeRewardItem()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                exit;
            }
            
            $questModel = new Quest();
            $success = $questModel->removeRewardItem((int)$data['id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }
    
    /**
     * Add stage (API)
     */
    public function addStage()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['quest_id']) || empty($data['name'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $stageModel = new QuestStage();
            $stageId = $stageModel->create([
                'quest_id' => (int)$data['quest_id'],
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
                'order_index' => (int)($data['order_index'] ?? 0),
                'rewards_json' => $data['rewards_json'] ?? null
            ]);
            
            if ($stageId) {
                echo json_encode(['success' => true, 'stage_id' => $stageId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
        }
        exit;
    }
    
    /**
     * Update stage (API)
     */
    public function updateStage()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $stageModel = new QuestStage();
            $success = $stageModel->update((int)$data['id'], [
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
                'order_index' => (int)($data['order_index'] ?? 0),
                'rewards_json' => $data['rewards_json'] ?? null
            ]);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }
    
    /**
     * Delete stage (API)
     */
    public function deleteStage()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                exit;
            }
            
            $stageModel = new QuestStage();
            $success = $stageModel->delete((int)$data['id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }
    
    /**
     * Add objective (API)
     */
    public function addObjective()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['stage_id']) || empty($data['type'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $objectiveModel = new QuestObjective();
            $objectiveId = $objectiveModel->create([
                'stage_id' => (int)$data['stage_id'],
                'type' => $data['type'],
                'target_id' => !empty($data['target_id']) ? (int)$data['target_id'] : null,
                'count_required' => (int)($data['count_required'] ?? 1),
                'description' => trim($data['description'] ?? ''),
                'dialogue_tree_id' => !empty($data['dialogue_tree_id']) ? (int)$data['dialogue_tree_id'] : null
            ]);
            
            if ($objectiveId) {
                echo json_encode(['success' => true, 'objective_id' => $objectiveId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
        }
        exit;
    }
    
    /**
     * Update objective (API)
     */
    public function updateObjective()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $objectiveModel = new QuestObjective();
            $success = $objectiveModel->update((int)$data['id'], [
                'type' => $data['type'],
                'target_id' => !empty($data['target_id']) ? (int)$data['target_id'] : null,
                'count_required' => (int)($data['count_required'] ?? 1),
                'description' => trim($data['description'] ?? ''),
                'dialogue_tree_id' => !empty($data['dialogue_tree_id']) ? (int)$data['dialogue_tree_id'] : null
            ]);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }
    
    /**
     * Delete objective (API)
     */
    public function deleteObjective()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                exit;
            }
            
            $objectiveModel = new QuestObjective();
            $success = $objectiveModel->delete((int)$data['id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }
    
    /**
     * Assign NPC to quest (API)
     */
    public function assignNPC()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['npc_id']) || empty($data['quest_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $type = $data['type'] ?? 'GIVER';
            
            $stmt = $this->db->prepare("INSERT IGNORE INTO npc_quests (npc_id, quest_id, type) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $data['npc_id'], $data['quest_id'], $type);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'assignation']);
            }
        }
        exit;
    }
    
    /**
     * Remove NPC from quest (API)
     */
    public function removeNPC()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['npc_id']) || empty($data['quest_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $stmt = $this->db->prepare("DELETE FROM npc_quests WHERE npc_id = ? AND quest_id = ?");
            $stmt->bind_param("ii", $data['npc_id'], $data['quest_id']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        }
        exit;
    }

    /**
     * Add map unlock to stage (API)
     */
    public function addMapUnlock()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['stage_id']) || empty($data['map_point_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $stageModel = new QuestStage();
            $success = $stageModel->addMapUnlock((int)$data['stage_id'], (int)$data['map_point_id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }

    /**
     * Remove map unlock from stage (API)
     */
    public function removeMapUnlock()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['stage_id']) || empty($data['map_point_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $stageModel = new QuestStage();
            $success = $stageModel->removeMapUnlock((int)$data['stage_id'], (int)$data['map_point_id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }

    /**
     * Add prerequisite (API)
     */
    public function addPrerequisite()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['quest_id']) || empty($data['required_quest_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $questModel = new Quest();
            $success = $questModel->addPrerequisite((int)$data['quest_id'], (int)$data['required_quest_id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }

    /**
     * Remove prerequisite (API)
     */
    public function removePrerequisite()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || empty($data['quest_id']) || empty($data['required_quest_id'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $questModel = new Quest();
            $success = $questModel->removePrerequisite((int)$data['quest_id'], (int)$data['required_quest_id']);
            
            echo json_encode(['success' => $success]);
        }
        exit;
    }
}
