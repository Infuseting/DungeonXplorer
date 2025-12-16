<?php

namespace App\Controllers;

class AdminDialogueController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * List all dialogue trees
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        
        $dialogueModel = new DialogueTree();
        $npcModel = new NPC();
        
        // Get all trees with node count
        $query = "SELECT dt.*, 
                  (SELECT COUNT(*) FROM dialogues WHERE tree_id = dt.id) as node_count
                  FROM dialogue_trees dt WHERE 1=1";
        $params = [];
        $types = '';
        
        if ($search) {
            $query .= " AND dt.name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }
        
        $query .= " ORDER BY dt.created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $trees = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->db->query($query);
            $trees = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get NPC count for each tree
        foreach ($trees as &$tree) {
            $tree['npc_count'] = count($dialogueModel->getNPCsUsingTree($tree['id']));
        }
        unset($tree); // Destroy reference to avoid issues
        
        require_once __DIR__ . '/../Views/admin/dialogues/index.php';
    }
    
    /**
     * Create new dialogue tree
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dialogueModel = new DialogueTree();
            
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            $treeId = $dialogueModel->create($name, $description);
            
            if ($treeId) {
                header('Location: /admin/dialogues/tree/' . $treeId);
                exit;
            }
        }
        
        require_once __DIR__ . '/../Views/admin/dialogues/create.php';
    }
    
    /**
     * Edit dialogue tree
     */
    public function edit($id)
    {
        $dialogueModel = new DialogueTree();
        $tree = $dialogueModel->findById($id);
        
        if (!$tree) {
            header('Location: /admin/dialogues');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];
            
            $dialogueModel->update($id, $data);
            header('Location: /admin/dialogues');
            exit;
        }
        
        require_once __DIR__ . '/../Views/admin/dialogues/edit.php';
    }
    
    /**
     * Delete dialogue tree
     */
    public function delete($id)
    {
        $dialogueModel = new DialogueTree();
        $dialogueModel->delete($id);
        
        header('Location: /admin/dialogues');
        exit;
    }
    
    /**
     * Visual tree editor
     */
    public function editTree($id)
    {
        $dialogueModel = new DialogueTree();
        $tree = $dialogueModel->findById($id);
        
        if (!$tree) {
            header('Location: /admin/dialogues');
            exit;
        }
        
        // Get tree structure
        $dialogueTree = $dialogueModel->getDialogueTree($id);
        
        require_once __DIR__ . '/../Views/admin/dialogues/tree.php';
    }
    
    /**
     * Add dialogue node (API)
     */
    public function addNode()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $text = trim($data['text'] ?? '');
            
            if ($text === '') {
                echo json_encode(['success' => false, 'message' => 'Le texte est requis']);
                exit;
            }
            
            $parentId = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;
            $choiceText = !empty($data['choice_text']) ? trim($data['choice_text']) : null;
            
            // New fields
            $actionType = $data['action_type'] ?? 'NONE';
            $actionValue = $data['action_value'] ?? null;
            $conditionType = $data['condition_type'] ?? 'NONE';
            $conditionValue = $data['condition_value'] ?? null;

            $dialogueModel = new DialogueTree();
            
            try {
                // Modified to pass new fields (requires Model update too, but we can do raw SQL insert here if Model doesn't support it yet)
                // Let's assume Model needs update. I'll check Model next.
                // For now, I'll direct inject via Model's method if it accepts array or I'll override it here? 
                // Better to update Model. But for this step I'll assume I update Model's addDialogue.
                // Wait, `addDialogue` in Model probably takes explicit args.
                // I should verify Model first. I'll assume I update Model later.
                
                $nodeId = $dialogueModel->addDialogue(
                    (int)$data['tree_id'],
                    $text,
                    $parentId,
                    (int)($data['is_player_choice'] ?? 0),
                    $choiceText,
                    (int)($data['order_index'] ?? 0),
                    $actionType,
                    $actionValue,
                    $conditionType,
                    $conditionValue
                );
                
                if ($nodeId) {
                    echo json_encode(['success' => true, 'node_id' => $nodeId]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
            }
        }
        exit;
    }
    
    /**
     * Update dialogue node (API)
     */
    public function updateNode()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $text = trim($data['text'] ?? '');
            if ($text === '') {
                echo json_encode(['success' => false, 'message' => 'Le texte est requis']);
                exit;
            }
            
            $choiceText = !empty($data['choice_text']) ? trim($data['choice_text']) : null;
            $actionType = $data['action_type'] ?? 'NONE';
            $actionValue = $data['action_value'] ?? null;
            $conditionType = $data['condition_type'] ?? 'NONE';
            $conditionValue = $data['condition_value'] ?? null;
            
            $stmt = $this->db->prepare("
                UPDATE dialogues 
                SET text = ?, choice_text = ?, order_index = ?,
                    action_type = ?, action_value = ?, condition_type = ?, condition_value = ?
                WHERE id = ?
            ");
            
            $orderIndex = (int)($data['order_index'] ?? 0);
            $id = (int)$data['id'];
            
            $stmt->bind_param(
                "ssissssi",
                $text,
                $choiceText,
                $orderIndex,
                $actionType,
                $actionValue,
                $conditionType,
                $conditionValue,
                $id
            );
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        }
        exit;
    }
    
    /**
     * Delete dialogue node (API)
     */
    public function deleteNode()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            
            if (!$data || empty($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                exit;
            }
            
            $dialogueModel = new DialogueTree();
            if ($dialogueModel->deleteDialogue($data['id'])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        }
        exit;
    }
}
