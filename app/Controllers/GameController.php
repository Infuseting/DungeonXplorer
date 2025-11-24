<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\Inventory;

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

        $inventoryModel = new Inventory();
        $inventory = $inventoryModel->getCharacterInventory($characterId);
        
        // Load map points for the game
        $db = \App\Config\Database::getInstance()->getConnection();
        $result = $db->query("SELECT * FROM map_points WHERE map_id = 1 ORDER BY created_at DESC");
        $mapPoints = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        require_once __DIR__ . '/../Views/game/index.php';
    }
}
