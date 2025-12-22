<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\House;
use App\Models\Furniture;
use App\Models\HouseStorage;
use App\Models\Inventory;
use App\Models\Character;

class HouseController
{
    /**
     * Récupère les données de la maison du joueur
     */
    public function index()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $characterId = $_SESSION['character_id'];
            $houseModel = new House();
            $furnitureModel = new Furniture();
            $storageModel = new HouseStorage();

            // Récupérer les maisons du personnage
            $houses = $houseModel->getCharacterHouses($characterId);
            $primaryHouse = $houseModel->getPrimaryHouse($characterId);

            // Si le personnage a une maison principale, récupérer les détails
            $houseData = null;
            if ($primaryHouse) {
                $furniture = $furnitureModel->getHouseFurniture($primaryHouse['id']);
                $storage = $storageModel->getStorage($primaryHouse['id']);
                $storageCapacity = $storageModel->getStorageCapacity($primaryHouse['id']);
                $bonuses = $houseModel->getHouseBonuses($primaryHouse['id']);

                $houseData = [
                    'house' => $primaryHouse,
                    'furniture' => $furniture ?? [],
                    'storage' => $storage ?? [],
                    'storage_capacity' => $storageCapacity ?? $primaryHouse['storage_slots'],
                    'storage_used' => is_array($storage) ? count($storage) : 0,
                    'bonuses' => $bonuses ?? []
                ];
            }

            // Récupérer l'or du personnage
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $character = $stmt->get_result()->fetch_assoc();

            // Récupérer le niveau du personnage
            $stmt = $db->prepare("SELECT level FROM character_stats WHERE character_id = ?");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $stats = $stmt->get_result()->fetch_assoc();
            $level = $stats['level'] ?? 1;

            echo json_encode([
                'success' => true,
                'has_house' => !empty($primaryHouse),
                'houses' => $houses ?? [],
                'current_house' => $houseData,
                'player_gold' => $character['gold'] ?? 0,
                'player_level' => $level
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Liste des maisons disponibles à l'achat
     */
    public function available()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $characterId = $_SESSION['character_id'];
        $houseModel = new House();

        // Récupérer le niveau du personnage
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT level FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $level = $stats['level'] ?? 1;

        // Maisons disponibles
        $houses = $houseModel->getAvailableHouses($level);

        // Maisons déjà possédées
        $owned = $houseModel->getCharacterHouses($characterId);
        
        // Créer un mapping des maisons possédées avec leurs infos
        $ownedMap = [];
        foreach ($owned as $o) {
            $ownedMap[$o['house_id']] = [
                'character_house_id' => $o['id'],
                'is_primary' => $o['is_primary'] ?? 0,
                'custom_name' => $o['custom_name'] ?? null
            ];
        }

        // Marquer les maisons possédées avec leurs infos
        foreach ($houses as &$house) {
            if (isset($ownedMap[$house['id']])) {
                $house['owned'] = true;
                $house['character_house_id'] = $ownedMap[$house['id']]['character_house_id'];
                $house['is_primary'] = $ownedMap[$house['id']]['is_primary'];
                $house['custom_name'] = $ownedMap[$house['id']]['custom_name'];
            } else {
                $house['owned'] = false;
            }
        }

        // Or du personnage
        $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        echo json_encode([
            'success' => true,
            'houses' => $houses,
            'player_gold' => $character['gold'] ?? 0,
            'player_level' => $level
        ]);
    }

    /**
     * Acheter une maison
     */
    public function purchase()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $houseId = $input['house_id'] ?? null;

        if (!$houseId) {
            echo json_encode(['success' => false, 'message' => 'ID de maison requis']);
            exit;
        }

        $houseModel = new House();
        $result = $houseModel->purchaseHouse($_SESSION['character_id'], $houseId);

        echo json_encode($result);
    }

    /**
     * Définir une maison comme principale
     */
    public function setPrimary()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $characterHouseId = $input['character_house_id'] ?? null;

        if (!$characterHouseId) {
            echo json_encode(['success' => false, 'message' => 'ID requis']);
            exit;
        }

        $houseModel = new House();
        $success = $houseModel->setPrimaryHouse($_SESSION['character_id'], $characterHouseId);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Maison principale mise à jour' : 'Erreur'
        ]);
    }

    /**
     * Renommer une maison
     */
    public function rename()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $characterHouseId = $input['character_house_id'] ?? null;
        $newName = $input['name'] ?? null;

        if (!$characterHouseId || !$newName) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            exit;
        }

        $houseModel = new House();
        $success = $houseModel->renameHouse($characterHouseId, $_SESSION['character_id'], $newName);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Maison renommée' : 'Erreur'
        ]);
    }

    /**
     * Liste des meubles disponibles à l'achat
     */
    public function furnitureShop()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $characterId = $_SESSION['character_id'];
        $furnitureModel = new Furniture();

        // Récupérer le niveau du personnage
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT level FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $level = $stats['level'] ?? 1;

        // Meubles disponibles
        $furniture = $furnitureModel->getAvailable($level);
        $categories = $furnitureModel->getCategories();

        // Or du personnage
        $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        echo json_encode([
            'success' => true,
            'furniture' => $furniture,
            'categories' => $categories,
            'player_gold' => $character['gold'] ?? 0
        ]);
    }

    /**
     * Acheter un meuble
     */
    public function purchaseFurniture()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $furnitureId = $input['furniture_id'] ?? null;
        $characterHouseId = $input['character_house_id'] ?? null;

        if (!$furnitureId || !$characterHouseId) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            exit;
        }

        $furnitureModel = new Furniture();
        $result = $furnitureModel->purchaseFurniture($_SESSION['character_id'], $characterHouseId, $furnitureId);

        echo json_encode($result);
    }

    /**
     * Vendre un meuble
     */
    public function sellFurniture()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $characterHouseFurnitureId = $input['furniture_placement_id'] ?? null;

        if (!$characterHouseFurnitureId) {
            echo json_encode(['success' => false, 'message' => 'ID requis']);
            exit;
        }

        $furnitureModel = new Furniture();
        $result = $furnitureModel->sellFurniture($_SESSION['character_id'], $characterHouseFurnitureId);

        echo json_encode($result);
    }

    /**
     * Déposer un item dans le coffre
     */
    public function deposit()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $characterHouseId = $input['character_house_id'] ?? null;
        $inventoryItemId = $input['inventory_item_id'] ?? null;
        $quantity = $input['quantity'] ?? 1;

        if (!$characterHouseId || !$inventoryItemId) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            exit;
        }

        $storageModel = new HouseStorage();
        $result = $storageModel->depositItem($_SESSION['character_id'], $characterHouseId, $inventoryItemId, $quantity);

        echo json_encode($result);
    }

    /**
     * Retirer un item du coffre
     */
    public function withdraw()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $characterHouseId = $input['character_house_id'] ?? null;
        $storageItemId = $input['storage_item_id'] ?? null;
        $quantity = $input['quantity'] ?? 1;

        if (!$characterHouseId || !$storageItemId) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            exit;
        }

        $storageModel = new HouseStorage();
        $result = $storageModel->withdrawItem($_SESSION['character_id'], $characterHouseId, $storageItemId, $quantity);

        echo json_encode($result);
    }

    /**
     * Récupérer l'inventaire du joueur (pour le transfert)
     */
    public function getInventory()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $inventoryModel = new Inventory();
        $inventory = $inventoryModel->getCharacterInventory($_SESSION['character_id']);

        echo json_encode([
            'success' => true,
            'inventory' => $inventory['inventory'] ?? []
        ]);
    }

    /**
     * Récupérer les bonus actifs de la maison
     */
    public function getBonuses()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $houseModel = new House();
        $primaryHouse = $houseModel->getPrimaryHouse($_SESSION['character_id']);

        if (!$primaryHouse) {
            echo json_encode([
                'success' => true,
                'bonuses' => [
                    'storage' => 0,
                    'comfort' => 0,
                    'luck' => 0,
                    'xp' => 0,
                    'gold' => 0,
                    'defense' => 0
                ]
            ]);
            exit;
        }

        $bonuses = $houseModel->getHouseBonuses($primaryHouse['id']);

        echo json_encode([
            'success' => true,
            'bonuses' => $bonuses
        ]);
    }
}
