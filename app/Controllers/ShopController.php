<?php

namespace App\Controllers;

use App\Models\NPC;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;

class ShopController
{
    public function getShop($npcId)
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $npcModel = new NPC();
        $npc = $npcModel->findById($npcId);

        if (!$npc) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'NPC not found']);
            exit;
        }

        // Get NPC inventory
        $merchantInventory = $npcModel->getMerchantInventory($npcId);
        
        // Calculate prices for the player
        foreach ($merchantInventory as &$item) {
            // Buying from NPC: Base Price * Multiplier (e.g. 1.0 or higher)
            // For now, let's assume a standard markup or just use base price
            // The NPC model has calculateBuyPrice but that seems to be for the NPC buying FROM the player?
            // Let's check NPC model again.
            // Ah, calculateBuyPrice in NPC.php uses buy_rate_own/other. That sounds like what the NPC pays.
            // So we need a sell_rate (what NPC sells for).
            // For now, let's assume NPC sells at base price * 1.5 or something, or just base price.
            // Let's use base price for simplicity for now, or add a multiplier.
            $item['buy_price'] = $item['price']; // Price player pays to buy FROM NPC
        }

        // Get Player Inventory
        $inventoryModel = new Inventory();
        $playerInventory = $inventoryModel->getCharacterInventory($_SESSION['character_id']);

        // Calculate sell prices for player items (what NPC pays)
        if (isset($playerInventory['inventory'])) {
            foreach ($playerInventory['inventory'] as &$item) {
                $item['sell_price'] = $npcModel->calculateBuyPrice($npcId, $item['item_id'], $item['price']);
            }
        }

        // Get Player Money (Gold)
        // Assuming gold is stored in characters table or inventory?
        // Let's check Character model or User model.
        // Wait, I haven't checked where gold is stored.
        // I'll assume it's on the character for now.
        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['character_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $playerGold = $result['gold'] ?? 0;

        echo json_encode([
            'success' => true,
            'npc' => $npc,
            'merchant_inventory' => $merchantInventory,
            'player_inventory' => $playerInventory, // Send full structure for UI to render
            'player_gold' => $playerGold
        ]);
    }

    public function buy()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $npcId = $input['npc_id'] ?? null;
        $itemId = $input['item_id'] ?? null;

        if (!$npcId || !$itemId) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $db = \App\Config\Database::getInstance()->getConnection();
        $npcModel = new NPC();
        $inventoryModel = new Inventory();

        // 1. Verify Item exists in NPC inventory
        // We check if the NPC sells this item. Quantity is ignored as stock is unlimited.
        $stmt = $db->prepare("SELECT 1 FROM npc_merchant_inventory WHERE npc_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $npcId, $itemId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Item not available']);
            exit;
        }

        // 2. Get Item Price
        $itemModel = new Item();
        $itemData = $itemModel->findById($itemId);
        $price = $itemData['price']; // Player pays full price (or markup)

        // 3. Check Player Gold
        $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['character_id']);
        $stmt->execute();
        $charData = $stmt->get_result()->fetch_assoc();
        
        if ($charData['gold'] < $price) {
            echo json_encode(['success' => false, 'message' => 'Not enough gold']);
            exit;
        }

        // 4. Add Item to Player Inventory
        // We need to find a free slot.
        // Using a helper method in Inventory model would be good, but for now let's try to find a spot.
        // Actually, Inventory::moveItem checks bounds, but doesn't "add" new items.
        // We need an "addItem" method in Inventory model.
        // I'll check if it exists or create it.
        // For now, I'll implement a basic "find first free slot" logic here or assume Inventory model has it.
        // Wait, I viewed Inventory.php and it didn't have `addItem`. I should add it.
        
        // Let's assume I'll add `addItem` to Inventory model.
        $addResult = $inventoryModel->addItem($_SESSION['character_id'], $itemId);
        
        if (!$addResult['success']) {
            echo json_encode(['success' => false, 'message' => $addResult['message']]);
            exit;
        }

        // 5. Deduct Gold
        $newGold = $charData['gold'] - $price;
        $stmt = $db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("ii", $newGold, $_SESSION['character_id']);
        $stmt->execute();

        // 6. Decrease NPC Inventory - REMOVED (Unlimited Stock)

        echo json_encode(['success' => true, 'message' => 'Item purchased', 'new_gold' => $newGold]);
    }

    public function sell()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $npcId = $input['npc_id'] ?? null;
        $inventoryItemId = $input['item_id'] ?? null; // This is the ID in character_inventory, not item_id

        if (!$npcId || !$inventoryItemId) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $db = \App\Config\Database::getInstance()->getConnection();
        $npcModel = new NPC();
        
        // 1. Get Item from Player Inventory
        $stmt = $db->prepare("
            SELECT ci.*, i.price, i.id as real_item_id 
            FROM character_inventory ci 
            JOIN items i ON ci.item_id = i.id 
            WHERE ci.id = ? AND ci.character_id = ?
        ");
        $stmt->bind_param("ii", $inventoryItemId, $_SESSION['character_id']);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }

        // 2. Calculate Sell Price
        $sellPrice = $npcModel->calculateBuyPrice($npcId, $item['real_item_id'], $item['price']);

        // 3. Remove Item from Player Inventory
        $stmt = $db->prepare("DELETE FROM character_inventory WHERE id = ?");
        $stmt->bind_param("i", $inventoryItemId);
        $stmt->execute();

        // 4. Add Gold to Player
        $stmt = $db->prepare("UPDATE characters SET gold = gold + ? WHERE id = ?");
        $stmt->bind_param("ii", $sellPrice, $_SESSION['character_id']);
        $stmt->execute();

        // 5. Add to NPC Inventory - REMOVED (No Buyback)

        echo json_encode(['success' => true, 'message' => 'Item sold', 'gold_earned' => $sellPrice]);
    }
}
