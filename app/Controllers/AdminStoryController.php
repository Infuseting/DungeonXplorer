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

        $this->render('admin/stories/nodes', ['story' => $story, 'nodes' => $nodes]);
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
}
