<?php

namespace App\Models;

use App\Config\Database;

class HouseStorage
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère le contenu du stockage d'une maison
     */
    public function getStorage($characterHouseId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT hs.*, i.name, i.description, i.type, i.icon, i.stats, i.price, i.max_stack
                FROM house_storage hs
                JOIN items i ON hs.item_id = i.id
                WHERE hs.character_house_id = ?
                ORDER BY hs.slot_index ASC
            ");
            if (!$stmt) return [];
            $stmt->bind_param("i", $characterHouseId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupère la capacité de stockage d'une maison (base + bonus meubles)
     */
    public function getStorageCapacity($characterHouseId)
    {
        try {
            // Capacité de base de la maison
            $stmt = $this->db->prepare("
                SELECT h.storage_slots
                FROM character_houses ch
                JOIN houses h ON ch.house_id = h.id
                WHERE ch.id = ?
            ");
            if (!$stmt) return 0;
            $stmt->bind_param("i", $characterHouseId);
            $stmt->execute();
            $house = $stmt->get_result()->fetch_assoc();
            $baseCapacity = $house ? $house['storage_slots'] : 0;

            // Bonus des meubles - vérifier si la table existe
            $storageBonus = 0;
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(f.bonus_value), 0) as storage_bonus
                FROM character_house_furniture chf
                JOIN furniture f ON chf.furniture_id = f.id
                WHERE chf.character_house_id = ? AND f.bonus_type = 'storage'
            ");
            if ($stmt) {
                $stmt->bind_param("i", $characterHouseId);
                $stmt->execute();
                $bonus = $stmt->get_result()->fetch_assoc();
                $storageBonus = $bonus ? (int)$bonus['storage_bonus'] : 0;
            }

            return $baseCapacity + $storageBonus;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Récupère le nombre d'items actuellement stockés
     */
    public function getCurrentStorageCount($characterHouseId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM house_storage WHERE character_house_id = ?
        ");
        $stmt->bind_param("i", $characterHouseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Dépose un item de l'inventaire dans le stockage de la maison
     */
    public function depositItem($characterId, $characterHouseId, $inventoryItemId, $quantity = 1)
    {
        try {
            // Vérifier que la maison appartient au personnage
            $stmt = $this->db->prepare("SELECT id FROM character_houses WHERE id = ? AND character_id = ?");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Erreur de base de données (character_houses)'];
            }
            $stmt->bind_param("ii", $characterHouseId, $characterId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                return ['success' => false, 'message' => 'Maison introuvable ou non possédée'];
            }

            // Vérifier la capacité de stockage
            $capacity = $this->getStorageCapacity($characterHouseId);
            $currentCount = $this->getCurrentStorageCount($characterHouseId);
            
            if ($capacity <= 0) {
                return ['success' => false, 'message' => 'Erreur: capacité de stockage non trouvée'];
            }
            
            if ($currentCount >= $capacity) {
                return ['success' => false, 'message' => "Le coffre est plein ({$currentCount}/{$capacity})"];
            }

            // Récupérer l'item de l'inventaire
            $stmt = $this->db->prepare("
                SELECT ci.*, i.max_stack
                FROM character_inventory ci
                JOIN items i ON ci.item_id = i.id
                WHERE ci.id = ? AND ci.character_id = ?
            ");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Erreur de base de données (inventory)'];
            }
            $stmt->bind_param("ii", $inventoryItemId, $characterId);
            $stmt->execute();
            $inventoryItem = $stmt->get_result()->fetch_assoc();

            if (!$inventoryItem) {
                return ['success' => false, 'message' => 'Item introuvable dans l\'inventaire'];
            }

            // Si l'item est équipé, ne pas permettre le transfert
            if (isset($inventoryItem['location']) && $inventoryItem['location'] === 'equipped') {
                return ['success' => false, 'message' => 'Vous devez d\'abord déséquiper cet item'];
            }

            $itemId = $inventoryItem['item_id'];
            $availableQuantity = $inventoryItem['quantity'];
            $transferQuantity = min($quantity, $availableQuantity);

            // Vérifier si l'item existe déjà dans le stockage (pour stacking)
            $stmt = $this->db->prepare("
                SELECT hs.id, hs.quantity, i.max_stack
                FROM house_storage hs
                JOIN items i ON hs.item_id = i.id
                WHERE hs.character_house_id = ? AND hs.item_id = ?
            ");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Erreur: table house_storage non trouvée. Exécutez database_house.sql'];
            }
            $stmt->bind_param("ii", $characterHouseId, $itemId);
            $stmt->execute();
            $existingStorage = $stmt->get_result()->fetch_assoc();

            if ($existingStorage && $existingStorage['max_stack'] > 1) {
                // Ajouter au stack existant
                $newQuantity = $existingStorage['quantity'] + $transferQuantity;
                $stmt = $this->db->prepare("UPDATE house_storage SET quantity = ? WHERE id = ?");
                $stmt->bind_param("ii", $newQuantity, $existingStorage['id']);
                if (!$stmt->execute()) {
                    return ['success' => false, 'message' => 'Erreur lors de la mise à jour du stockage'];
                }
            } else {
                // Créer un nouveau slot de stockage
                $nextSlot = $currentCount;
                $stmt = $this->db->prepare("
                    INSERT INTO house_storage (character_house_id, item_id, quantity, slot_index)
                    VALUES (?, ?, ?, ?)
                ");
                if (!$stmt) {
                    return ['success' => false, 'message' => 'Erreur: impossible d\'insérer dans house_storage'];
                }
                $stmt->bind_param("iiii", $characterHouseId, $itemId, $transferQuantity, $nextSlot);
                if (!$stmt->execute()) {
                    return ['success' => false, 'message' => 'Erreur lors du dépôt: ' . $stmt->error];
                }
            }

            // Retirer de l'inventaire
            if ($transferQuantity >= $availableQuantity) {
                $stmt = $this->db->prepare("DELETE FROM character_inventory WHERE id = ?");
                $stmt->bind_param("i", $inventoryItemId);
            } else {
                $newInventoryQuantity = $availableQuantity - $transferQuantity;
                $stmt = $this->db->prepare("UPDATE character_inventory SET quantity = ? WHERE id = ?");
                $stmt->bind_param("ii", $newInventoryQuantity, $inventoryItemId);
            }
            
            if (!$stmt->execute()) {
                return ['success' => false, 'message' => 'Erreur lors du retrait de l\'inventaire'];
            }

            return ['success' => true, 'message' => 'Item déposé dans le coffre'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Retire un item du stockage vers l'inventaire
     */
    public function withdrawItem($characterId, $characterHouseId, $storageItemId, $quantity = 1)
    {
        // Vérifier que la maison appartient au personnage
        $stmt = $this->db->prepare("SELECT id FROM character_houses WHERE id = ? AND character_id = ?");
        $stmt->bind_param("ii", $characterHouseId, $characterId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'message' => 'Maison introuvable'];
        }

        // Récupérer l'item du stockage
        $stmt = $this->db->prepare("
            SELECT hs.*, i.max_stack
            FROM house_storage hs
            JOIN items i ON hs.item_id = i.id
            WHERE hs.id = ? AND hs.character_house_id = ?
        ");
        $stmt->bind_param("ii", $storageItemId, $characterHouseId);
        $stmt->execute();
        $storageItem = $stmt->get_result()->fetch_assoc();

        if (!$storageItem) {
            return ['success' => false, 'message' => 'Item introuvable dans le coffre'];
        }

        $itemId = $storageItem['item_id'];
        $availableQuantity = $storageItem['quantity'];
        $transferQuantity = min($quantity, $availableQuantity);

        // Vérifier si l'item existe déjà dans l'inventaire (pour stacking)
        $stmt = $this->db->prepare("
            SELECT ci.id, ci.quantity, i.max_stack
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ? AND ci.item_id = ? AND ci.location = 'backpack'
        ");
        $stmt->bind_param("ii", $characterId, $itemId);
        $stmt->execute();
        $existingInventory = $stmt->get_result()->fetch_assoc();

        if ($existingInventory && $existingInventory['max_stack'] > 1) {
            // Ajouter au stack existant
            $newQuantity = $existingInventory['quantity'] + $transferQuantity;
            $stmt = $this->db->prepare("UPDATE character_inventory SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQuantity, $existingInventory['id']);
            $stmt->execute();
        } else {
            // Créer un nouveau slot d'inventaire
            $stmt = $this->db->prepare("
                INSERT INTO character_inventory (character_id, item_id, location, quantity)
                VALUES (?, ?, 'backpack', ?)
            ");
            $stmt->bind_param("iii", $characterId, $itemId, $transferQuantity);
            $stmt->execute();
        }

        // Retirer du stockage
        if ($transferQuantity >= $availableQuantity) {
            $stmt = $this->db->prepare("DELETE FROM house_storage WHERE id = ?");
            $stmt->bind_param("i", $storageItemId);
        } else {
            $newStorageQuantity = $availableQuantity - $transferQuantity;
            $stmt = $this->db->prepare("UPDATE house_storage SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newStorageQuantity, $storageItemId);
        }
        $stmt->execute();

        return ['success' => true, 'message' => 'Item récupéré du coffre'];
    }

    /**
     * Transfère tous les items d'un type vers le stockage
     */
    public function depositAllOfType($characterId, $characterHouseId, $itemType)
    {
        $stmt = $this->db->prepare("
            SELECT ci.id
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ? AND ci.location != 'equipped' AND i.type = ?
        ");
        $stmt->bind_param("is", $characterId, $itemType);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $deposited = 0;
        foreach ($items as $item) {
            $result = $this->depositItem($characterId, $characterHouseId, $item['id'], 999);
            if ($result['success']) {
                $deposited++;
            }
        }

        return ['success' => true, 'message' => $deposited . ' items déposés'];
    }
}
