<?php

namespace App\Controllers;

use App\Models\Inventory;

class InventoryController
{
    public function move()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['itemId'] ?? null;
        $targetLocation = $input['location'] ?? null;
        $targetSlot = $input['slot'] ?? null;
        $targetX = $input['x'] ?? null;
        $targetY = $input['y'] ?? null;

        if (!$itemId || !$targetLocation) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $inventoryModel = new Inventory();
        $result = $inventoryModel->moveItem($_SESSION['character_id'], $itemId, $targetLocation, $targetSlot, $targetX, $targetY);

        echo json_encode($result);
    }

    public function equip()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['itemId'] ?? null;

        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing itemId']);
            exit;
        }

        $inventoryModel = new Inventory();
        $result = $inventoryModel->equipItemById($_SESSION['character_id'], $itemId);

        echo json_encode($result);
    }

    public function unequip()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $slotName = $input['slot'] ?? null;

        if (!$slotName) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing slot']);
            exit;
        }

        $inventoryModel = new Inventory();
        $result = $inventoryModel->unequipItem($_SESSION['character_id'], $slotName);

        echo json_encode($result);
    }
    public function drop()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['itemId'] ?? null;

        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing itemId']);
            exit;
        }

        $inventoryModel = new Inventory();
        $result = $inventoryModel->deleteItem($_SESSION['character_id'], $itemId);

        echo json_encode($result);
    }
}
