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
        
        // Load map configuration and points using models
        $mapModel = new Map();
        $mapPointModel = new MapPoint();
        
        // Default to map ID 1 (you can make this dynamic later)
        $mapId = 1;
        $mapConfig = $mapModel->getMapConfig($mapId);
        $mapPoints = $mapPointModel->getPointsByMapId($mapId);

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

        $points = $mapPointModel->getPointsByMapId($mapId);

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
        
        // Get dialogue trees
        $dialogueTrees = $npcModel->getDialogueTrees($id);
        
        // Get merchant inventory if merchant
        $merchantInventory = [];
        if ($npc['role'] === 'merchant' && $npc['merchant_seed']) {
            $merchantInventory = $npcModel->getMerchantInventory($id);
        }
        
        echo json_encode([
            'success' => true,
            'npc' => $npc,
            'dialogue_trees' => $dialogueTrees,
            'merchant_inventory' => $merchantInventory
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
}
