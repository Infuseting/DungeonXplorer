<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\NPC;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;

class ShopController
{
    /**
     * Récupère les données du magasin pour un PNJ donné.
     * Inclut l'inventaire du marchand (avec prix) et celui du joueur (avec prix de vente estimés).
     */
    public function getShop($npcId)
    {
        header('Content-Type: application/json');
        
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

        // Récupération de l'inventaire du marchand
        $merchantInventory = $npcModel->getMerchantInventory($npcId);
        
        // Initialisation des prix d'achat
        foreach ($merchantInventory as &$item) {
            // Pour l'instant, le prix d'achat est le prix de base.
            // TODO: Implémenter des modificateurs basés sur la réputation ou le charisme.
            $item['buy_price'] = $item['price'];         
        }

        // Récupération de l'inventaire du joueur
        $inventoryModel = new Inventory();
        $playerInventory = $inventoryModel->getCharacterInventory($_SESSION['character_id']);

        // Calcul du prix de revente pour chaque objet du joueur
        if (isset($playerInventory['inventory'])) {
            foreach ($playerInventory['inventory'] as &$item) {
                $item['sell_price'] = $npcModel->calculateBuyPrice($npcId, $item['item_id'], $item['price']);
            }
        }

        // Récupération de l'or actuel du personnage
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['character_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $playerGold = $result['gold'] ?? 0;

        echo json_encode([
            'success' => true,
            'npc' => $npc,
            'merchant_inventory' => $merchantInventory,
            'player_inventory' => $playerInventory,             
            'player_gold' => $playerGold
        ]);
    }

    /**
     * Traite l'achat d'un objet par le joueur.
     * Vérifie la disponibilité de l'objet et les fonds du joueur.
     */
    public function buy()
    {
        header('Content-Type: application/json');

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

        $db = Database::getInstance()->getConnection();
        $npcModel = new NPC();
        $inventoryModel = new Inventory();

        // Vérifie si le PNJ vend bien cet objet
        $stmt = $db->prepare("SELECT 1 FROM npc_merchant_inventory WHERE npc_id = ? AND item_id = ?");
        $stmt->bind_param("ii", $npcId, $itemId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res) {
            echo json_encode(['success' => false, 'message' => 'Item not available']);
            exit;
        }

        // Récupère le prix de l'objet
        $itemModel = new Item();
        $itemData = $itemModel->findById($itemId);
        $price = $itemData['price']; 
        
        // Vérifie l'or du joueur
        $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['character_id']);
        $stmt->execute();
        $charData = $stmt->get_result()->fetch_assoc();
        
        if ($charData['gold'] < $price) {
            echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas assez d\'or en votre possession !']);
            exit;
        }

        // Ajout de l'objet à l'inventaire du joueur
        $addResult = $inventoryModel->addItem($_SESSION['character_id'], $itemId);
        
        if (!$addResult['success']) {
            echo json_encode(['success' => false, 'message' => $addResult['message']]);
            exit;
        }

        // Déduction de l'or
        $newGold = $charData['gold'] - $price;
        $stmt = $db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("ii", $newGold, $_SESSION['character_id']);
        $stmt->execute();

        // Logging de la transaction (si LoggerService existe)
        if (class_exists('App\Services\LoggerService')) {
             $logger = new \App\Services\LoggerService();
             $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'NPC_BUY', [
                'npc_id' => $npcId,
                'item_id' => $itemId,
                'price' => $price,
                'gold_remaining' => $newGold
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Item purchased', 'new_gold' => $newGold]);
    }

    /**
     * Traite la vente d'un objet par le joueur au PNJ.
     */
    public function sell()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $npcId = $input['npc_id'] ?? null;
        $inventoryItemId = $input['item_id'] ?? null; 
        if (!$npcId || !$inventoryItemId) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $npcModel = new NPC();
        
        // Vérifie que le joueur possède bien l'objet (via l'ID unique d'inventaire)
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

        // Calcul du prix de reprise
        $sellPrice = $npcModel->calculateBuyPrice($npcId, $item['real_item_id'], $item['price']);

        // Suppression de l'objet de l'inventaire
        $stmt = $db->prepare("DELETE FROM character_inventory WHERE id = ?");
        $stmt->bind_param("i", $inventoryItemId);
        $stmt->execute();

        // Ajout de l'or au joueur
        $stmt = $db->prepare("UPDATE characters SET gold = gold + ? WHERE id = ?");
        $stmt->bind_param("ii", $sellPrice, $_SESSION['character_id']);
        $stmt->execute();

         // Logging de la transaction
        if (class_exists('App\Services\LoggerService')) {
            $logger = new \App\Services\LoggerService();
            $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'NPC_SELL', [
                'npc_id' => $npcId,
                'item_id' => $item['real_item_id'],
                'price' => $sellPrice,
                'gold_earned' => $sellPrice
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Item sold', 'gold_earned' => $sellPrice]);
    }
}
