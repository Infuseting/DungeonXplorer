<?php

namespace App\Controllers;

class AdminDialogueController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    /**
     * List all dialogue trees
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        
        $dialogueModel = new \App\Models\DialogueTree();
        $npcModel = new \App\Models\NPC();
        
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
            $dialogueModel = new \App\Models\DialogueTree();
            
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
        $dialogueModel = new \App\Models\DialogueTree();
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
        $dialogueModel = new \App\Models\DialogueTree();
        $dialogueModel->delete($id);
        
        header('Location: /admin/dialogues');
        exit;
    }
    
    /**
     * Visual tree editor
     */
    public function editTree($id)
    {
        $dialogueModel = new \App\Models\DialogueTree();
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
            
            $dialogueModel = new \App\Models\DialogueTree();
            
            try {
                $nodeId = $dialogueModel->addDialogue(
                    (int)$data['tree_id'],
                    $text,
                    $parentId,
                    (int)($data['is_player_choice'] ?? 0),
                    $choiceText,
                    (int)($data['order_index'] ?? 0)
                );
                
              
                if ($nodeId) {
                    echo json_encode(['success' => true, 'node_id' => $nodeId]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout (ID null)']);
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
            
            $stmt = $this->db->prepare("
                UPDATE dialogues 
                SET text = ?, choice_text = ?, order_index = ?
                WHERE id = ?
            ");
            
            $orderIndex = (int)($data['order_index'] ?? 0);
            $id = (int)$data['id'];
            
            $stmt->bind_param(
                "ssii",
                $text,
                $choiceText,
                $orderIndex,
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
            
            $dialogueModel = new \App\Models\DialogueTree();
            if ($dialogueModel->deleteDialogue($data['id'])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        }
        exit;
    }
}
