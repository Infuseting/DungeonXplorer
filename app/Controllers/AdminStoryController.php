<?php

namespace App\Controllers;

use App\Models\Story;
use App\Models\StoryNode;
use App\Models\ProceduralTemplate;

class AdminStoryController
{
    private $storyModel;
    private $nodeModel;
    private $templateModel;

    public function __construct()
    {
        $this->storyModel = new Story();
        $this->nodeModel = new StoryNode();
        $this->templateModel = new ProceduralTemplate();
    }

    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    public function index()
    {
        $stories = $this->storyModel->getAll();
        $this->render('admin/stories/index', ['stories' => $stories]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'type' => $_POST['type'],
                'difficulty_level' => $_POST['difficulty_level'],
                'min_level' => $_POST['min_level'],
                'procedural_template_id' => !empty($_POST['procedural_template_id']) ? $_POST['procedural_template_id'] : null
            ];

            if ($this->storyModel->create($data)) {
                header('Location: /admin/stories');
                exit;
            }
        }
        
        $templates = $this->templateModel->getAll();
        $this->render('admin/stories/create', ['templates' => $templates]);
    }

    public function edit($id)
    {
        $story = $this->storyModel->findById($id);
        if (!$story) {
            header('Location: /admin/stories');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'type' => $_POST['type'],
                'difficulty_level' => $_POST['difficulty_level'],
                'min_level' => $_POST['min_level'],
                'procedural_template_id' => !empty($_POST['procedural_template_id']) ? $_POST['procedural_template_id'] : null
            ];

            if ($this->storyModel->update($id, $data)) {
                header('Location: /admin/stories');
                exit;
            }
        }

        $templates = $this->templateModel->getAll();
        $this->render('admin/stories/edit', ['story' => $story, 'templates' => $templates]);
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->storyModel->delete($id);
            header('Location: /admin/stories');
            exit;
        }
    }

    public function manageNodes($id)
    {
        $story = $this->storyModel->findById($id);
        if (!$story || $story['type'] === 'procedural') {
            header('Location: /admin/stories');
            exit;
        }

        $nodes = $this->nodeModel->getByStoryId($id);
        
        // Get full data for each node (connections, etc)
        foreach ($nodes as &$node) {
            $node['connections'] = $this->nodeModel->getConnections($node['id']);
        }

        // Fetch available entities for the editor
        $monsterModel = new \App\Models\Monster();
        $npcModel = new \App\Models\NPC();
        $itemModel = new \App\Models\Item();

        $monsters = $monsterModel->getAll();
        $npcs = $npcModel->getAll();
        $items = $itemModel->getAll();

        $this->render('admin/stories/nodes', [
            'story' => $story, 
            'nodes' => $nodes,
            'availableMonsters' => $monsters,
            'availableNPCs' => $npcs,
            'availableItems' => $items
        ]);
    }

    public function createNode($storyId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'story_id' => $storyId,
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'image_path' => $_POST['image_path'] ?? '',
                'is_start_node' => isset($_POST['is_start_node']) ? 1 : 0,
                'is_end_node' => isset($_POST['is_end_node']) ? 1 : 0,
                'node_x' => $_POST['node_x'] ?? 0,
                'node_y' => $_POST['node_y'] ?? 0
            ];

            $nodeId = $this->nodeModel->create($data);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => !!$nodeId, 'id' => $nodeId]);
            exit;
        }
    }

    public function updateNode($nodeId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'image_path' => $_POST['image_path'] ?? '',
                'is_start_node' => isset($_POST['is_start_node']) ? 1 : 0,
                'is_end_node' => isset($_POST['is_end_node']) ? 1 : 0,
                'can_exit' => isset($_POST['can_exit']) ? 1 : 0,
                'node_x' => $_POST['node_x'] ?? 0,
                'node_y' => $_POST['node_y'] ?? 0
            ];

            $success = $this->nodeModel->update($nodeId, $data);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }
    }

    public function deleteNode($nodeId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->nodeModel->delete($nodeId);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }
    }

    public function uploadNodeImage()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
            echo json_encode(['success' => false, 'message' => 'Aucune image fournie']);
            exit;
        }

        $file = $_FILES['image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
            exit;
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 5MB)']);
            exit;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('story_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/assets/story-images/' . $filename;
        
        // Create directory if it doesn't exist
        $directory = dirname($uploadPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $imagePath = '/assets/story-images/' . $filename;
            echo json_encode([
                'success' => true,
                'path' => $imagePath
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload']);
        }
        exit;
    }

    public function createConnection()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $fromNodeId = $_POST['from_node_id'] ?? null;
        $toNodeId = $_POST['to_node_id'] ?? null;
        
        if (!$fromNodeId || !$toNodeId) {
            echo json_encode(['success' => false, 'message' => 'Nœuds manquants']);
            exit;
        }

        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO story_node_connections (from_node_id, to_node_id, direction_text, condition_type, condition_value) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $directionText = $_POST['direction_text'] ?? '';
        $conditionType = $_POST['condition_type'] ?? 'none';
        $conditionValue = $_POST['condition_value'] ?? '';
        
        $stmt->bind_param("iisss", $fromNodeId, $toNodeId, $directionText, $conditionType, $conditionValue);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'id' => $stmt->insert_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
        }
        exit;
    }

    public function updateConnection($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "UPDATE story_node_connections 
             SET direction_text = ?, condition_type = ?, condition_value = ?,
                 allow_return = ?, return_text = ?, return_condition_type = ?, return_condition_value = ?
             WHERE id = ?"
        );
        
        $directionText = $_POST['direction_text'] ?? '';
        $conditionType = $_POST['condition_type'] ?? 'none';
        $conditionValue = $_POST['condition_value'] ?? '';
        $allowReturn = isset($_POST['allow_return']) ? 1 : 0;
        $returnText = $_POST['return_text'] ?? '';
        $returnConditionType = $_POST['return_condition_type'] ?? 'none';
        $returnConditionValue = $_POST['return_condition_value'] ?? '';
        
        $stmt->bind_param("sssisssi", 
            $directionText, $conditionType, $conditionValue,
            $allowReturn, $returnText, $returnConditionType, $returnConditionValue,
            $id
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
        exit;
    }

    public function deleteConnection($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM story_node_connections WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
        exit;
    }

    // --- Entity Management API ---

    public function getNodeEntities($nodeId)
    {
        header('Content-Type: application/json');

        $monsters = $this->nodeModel->getMonsters($nodeId);
        $npcs = $this->nodeModel->getNPCs($nodeId);
        $loot = $this->nodeModel->getLoots($nodeId);
        $traps = $this->nodeModel->getTraps($nodeId);

        echo json_encode([
            'success' => true,
            'monsters' => $monsters,
            'npcs' => $npcs,
            'loot' => $loot,
            'traps' => $traps
        ]);
        exit;
    }

    public function addNodeEntity()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $nodeId = $_POST['node_id'] ?? null;
        $type = $_POST['type'] ?? null;
        
        if (!$nodeId || !$type) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            exit;
        }

        $id = false;

        switch ($type) {
            case 'monster':
                // For monsters, we verify we have the base monster ID to copy stats, OR directly passed stats
                // Here we assume we pass a monster_id from the selection list, and we fetch that monster to allow manual override?
                // Or simplified: We just pass base params.
                // Let's implement getting data from a "monster_id" to auto-fill if not provided, but simplified for now:
                
                $monsterData = [
                    'name' => $_POST['monster_name'] ?? 'Monstre',
                    'level' => $_POST['monster_level'] ?? 1,
                    'quantity' => $_POST['quantity'] ?? 1,
                    'is_boss' => isset($_POST['is_boss']) ? 1 : 0,
                    'can_flee' => isset($_POST['can_flee']) ? 1 : 0,
                    'stats' => [] // Could be expanded to allow custom stats override
                ];
                
                // If a template ID is provided, we could fetch it here, but let's trust the frontend or Controller to handle "Add from Template" vs "Custom" logic.
                // For now, let's assume the frontend sends the name/level.
                
                // Wait, the Requirement is to CRUD "Monsters". So we should select a Monster ID and add it.
                // But the `story_node_monsters` table has `monster_name`, `monster_level`, `monster_stats`. It does NOT have `monster_id` foreign key.
                // So we MUST copy data.
                
                if (!empty($_POST['template_id'])) {
                    $monsterModel = new \App\Models\Monster();
                    $template = $monsterModel->findById($_POST['template_id']);
                    if ($template) {
                        $monsterData['name'] = $template['name'];
                        $monsterData['level'] = $template['level_min']; // Default to min level
                        $monsterData['stats'] = json_decode($template['base_stats_json'], true);
                    }
                }
                
                $id = $this->nodeModel->addMonster($nodeId, $monsterData);
                break;

            case 'npc':
                $npcId = $_POST['npc_id'] ?? null;
                if ($npcId) {
                    $id = $this->nodeModel->addNPC($nodeId, $npcId);
                }
                break;

            case 'loot':
                $itemId = $_POST['item_id'] ?? null;
                if ($itemId) {
                    $chance = $_POST['drop_chance'] ?? 1.0;
                    $quantity = $_POST['quantity'] ?? 1;
                    $isGuaranteed = isset($_POST['is_guaranteed']) ? 1 : 0;
                    
                    $trapData = null;
                    if (!empty($_POST['is_trapped'])) {
                        $trapData = [
                            'damage' => $_POST['trap_damage'] ?? '1d4',
                            'dc' => $_POST['trap_dc'] ?? 10,
                            'description' => $_POST['trap_description'] ?? ''
                        ];
                    }
                    
                    $id = $this->nodeModel->addLoot($nodeId, $itemId, $quantity, $chance, $isGuaranteed, $trapData);
                }
                break;
                
            case 'trap':
                $data = [
                    'description' => $_POST['description'] ?? 'Un piège',
                    'damage_dice' => $_POST['damage_dice'] ?? '1d6',
                    'effect_text' => $_POST['effect_text'] ?? 'Vous subissez des dégâts !',
                    'avoid_stat' => $_POST['avoid_stat'] ?? 'DEX',
                    'difficulty_class' => $_POST['difficulty_class'] ?? 12
                ];
                $id = $this->nodeModel->addTrap($nodeId, $data);
                break;
        }

        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
        exit;
    }

    public function removeNodeEntity()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $id = $_POST['entity_id'] ?? null;
        $type = $_POST['type'] ?? null;

        if (!$id || !$type) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            exit;
        }

        $success = false;
        switch ($type) {
            case 'monster':
                $success = $this->nodeModel->removeMonster($id);
                break;
            case 'npc':
                $success = $this->nodeModel->removeNPC($id);
                break;
            case 'loot':
                $success = $this->nodeModel->removeLoot($id);
                break;
            case 'trap':
                $success = $this->nodeModel->removeTrap($id);
                break;
        }

        echo json_encode(['success' => $success]);
        exit;
    }
}
