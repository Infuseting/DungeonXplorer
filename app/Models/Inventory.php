<?php

namespace App\Models;

use App\Config\Database;

class Inventory
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getCharacterInventory($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT ci.*, i.name, i.description, i.type, i.slot_type as item_slot_type, i.two_handed, i.width, i.height, i.weight, i.icon, i.stats as base_stats, i.max_stack, i.price
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ?
            ORDER BY ci.location, ci.grid_x
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


        $inventory = [
            'equipped' => [],
            'inventory' => []
        ];

        $currentWeight = 0;

        foreach ($result as $item) {
            $itemWeight = floatval($item['weight'] ?? 0);
            $currentWeight += $itemWeight;
            
            // Get enchantments for this item
            $item['enchantments'] = $this->getItemEnchantments($item['id']);
            
            // Use instance_stats if available (includes enchantment bonuses), otherwise use base_stats
            // Ensure stats is always a valid JSON string
            if (!empty($item['instance_stats'])) {
                $item['stats'] = $item['instance_stats'];
            } elseif (!empty($item['base_stats'])) {
                $item['stats'] = $item['base_stats'];
            } else {
                $item['stats'] = '{}';
            }

            if ($item['location'] === 'equipped') {
                $inventory['equipped'][$item['slot_name']] = $item;
            } else {
                                $inventory['inventory'][] = $item;
            }
        }

                $maxWeight = $this->calculateMaxWeight($characterId, $inventory['equipped']);

        $inventory['current_weight'] = $currentWeight;
        $inventory['max_weight'] = $maxWeight;

        return $inventory;
    }
    
    /**
     * Get enchantments for an inventory item
     */
    private function getItemEnchantments($inventoryItemId)
    {
        $stmt = $this->db->prepare("
            SELECT ie.id, ie.enchantment_id, e.name, e.description, e.icon, e.stat_modifiers, e.rarity
            FROM item_enchantments ie
            JOIN enchantments e ON ie.enchantment_id = e.id
            WHERE ie.character_inventory_id = ?
        ");
        if (!$stmt) return [];
        $stmt->bind_param("i", $inventoryItemId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function calculateMaxWeight($characterId, $equippedItems)
    {
                $baseWeight = 60;

                $stmt = $this->db->prepare("SELECT strength FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $strength = $stats['strength'] ?? 0;

                $backpackCapacity = 0;
        if (isset($equippedItems['backpack'])) {
            $backpackStats = json_decode($equippedItems['backpack']['stats'] ?? '{}', true);
            $backpackCapacity = $backpackStats['capacity'] ?? 0;
        }

        return $baseWeight + $strength + $backpackCapacity;
    }

    public function moveItem($characterId, $inventoryItemId, $targetLocation, $targetSlot = null, $targetX = null, $targetY = null)
    {
                $item = $this->getItemInInventory($characterId, $inventoryItemId);
        if (!$item) return ['success' => false, 'message' => 'Item not found'];

                if ($targetLocation === 'inventory' || $targetLocation === 'backpack' || $targetLocation === 'pockets') {
            return $this->moveToInventory($characterId, $item);
        } elseif ($targetLocation === 'equipped') {
            return $this->equipItem($characterId, $item, $targetSlot);
        }

        return ['success' => false, 'message' => 'Invalid location'];
    }

    private function getItemInInventory($characterId, $inventoryItemId)
    {
        $stmt = $this->db->prepare("
            SELECT ci.*, i.width, i.height, i.slot_type as item_slot_type, i.two_handed, i.icon, i.weight
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ? AND ci.id = ?
        ");
        $stmt->bind_param("ii", $characterId, $inventoryItemId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function moveToInventory($characterId, $item)
    {
                $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'backpack', slot_name = NULL, grid_x = NULL, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("i", $item['id']);
        
        return ['success' => $stmt->execute()];
    }

    private function equipItem($characterId, $item, $slotName)
    {
                $validSlot = false;
        
                if ($item['item_slot_type'] === $slotName) {
            $validSlot = true;
        }
                elseif ($item['item_slot_type'] === 'ring' && in_array($slotName, ['ring_1', 'ring_2'])) {
            $validSlot = true;
        }
                elseif ($item['item_slot_type'] === 'main_hand' && in_array($slotName, ['main_hand', 'off_hand'])) {
            $validSlot = true;
        }
        
        if (!$validSlot) {
             return ['success' => false, 'message' => 'Invalid slot for this item'];
        }

                $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND location = 'equipped' AND slot_name = ?");
        $stmt->bind_param("is", $characterId, $slotName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
             return ['success' => false, 'message' => 'Slot occupied'];
        }

                if ($item['two_handed']) {
            $oppositeSlot = ($slotName === 'main_hand') ? 'off_hand' : 'main_hand';
            $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND location = 'equipped' AND slot_name = ?");
            $stmt->bind_param("is", $characterId, $oppositeSlot);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                return ['success' => false, 'message' => 'Both hands must be empty for two-handed weapons'];
            }
        }

                if ($slotName === 'main_hand') {
            $stmt = $this->db->prepare("
                SELECT i.two_handed 
                FROM character_inventory ci
                JOIN items i ON ci.item_id = i.id
                WHERE ci.character_id = ? AND ci.location = 'equipped' AND ci.slot_name = 'off_hand'
            ");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result && $result['two_handed']) {
                return ['success' => false, 'message' => 'Cannot equip main-hand with two-handed weapon in off-hand'];
            }
        }

                if ($slotName === 'off_hand') {
            $stmt = $this->db->prepare("
                SELECT i.two_handed 
                FROM character_inventory ci
                JOIN items i ON ci.item_id = i.id
                WHERE ci.character_id = ? AND ci.location = 'equipped' AND ci.slot_name = 'main_hand'
            ");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result && $result['two_handed']) {
                return ['success' => false, 'message' => 'Cannot equip off-hand with two-handed weapon'];
            }
        }

        $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'equipped', slot_name = ?, grid_x = NULL, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("si", $slotName, $item['id']);
        
        $success = $stmt->execute();
        return [
            'success' => $success, 
            'two_handed' => $item['two_handed'],
            'slot_name' => $slotName,
            'icon' => $item['icon']
        ];
    }

        public function equipItemById($characterId, $inventoryItemId)
    {
        $item = $this->getItemInInventory($characterId, $inventoryItemId);
        if (!$item) return ['success' => false, 'message' => 'Item not found'];

                $slotName = $this->determineSlotForItem($item['item_slot_type']);
        if (!$slotName) {
            return ['success' => false, 'message' => 'This item cannot be equipped'];
        }

        return $this->equipItem($characterId, $item, $slotName);
    }

        public function unequipItem($characterId, $slotName)
    {
                $stmt = $this->db->prepare("
            SELECT ci.id 
            FROM character_inventory ci
            WHERE ci.character_id = ? AND ci.location = 'equipped' AND ci.slot_name = ?
        ");
        $stmt->bind_param("is", $characterId, $slotName);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            return ['success' => false, 'message' => 'No item in this slot'];
        }

                $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'backpack', slot_name = NULL, grid_x = NULL, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("i", $result['id']);
        return ['success' => $stmt->execute()];
    }

    public function addItem($characterId, $itemId)
    {
                $stmt = $this->db->prepare("SELECT width, height, max_stack, type, weight FROM items WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $itemData = $stmt->get_result()->fetch_assoc();

        if (!$itemData) {
            return ['success' => false, 'message' => 'Item not found'];
        }

                $inventory = $this->getCharacterInventory($characterId);
        $itemWeight = floatval($itemData['weight'] ?? 0);
        
        if (($inventory['current_weight'] + $itemWeight) > $inventory['max_weight']) {
            return ['success' => false, 'message' => 'Inventory too heavy'];
        }

                $stmt = $this->db->prepare("INSERT INTO character_inventory (character_id, item_id, location) VALUES (?, ?, 'backpack')");
        $stmt->bind_param("ii", $characterId, $itemId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Item added', 'new_item_id' => $this->db->insert_id];
        } else {
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function deleteItem($characterId, $inventoryItemId)
    {
                $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND id = ?");
        $stmt->bind_param("ii", $characterId, $inventoryItemId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'message' => 'Item not found or not owned'];
        }

                $stmt = $this->db->prepare("DELETE FROM character_inventory WHERE id = ?");
        $stmt->bind_param("i", $inventoryItemId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Item deleted'];
        } else {
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function consumeItem($characterId, $inventoryItemId)
    {
                $stmt = $this->db->prepare("SELECT id, quantity, item_id FROM character_inventory WHERE character_id = ? AND id = ?");
        $stmt->bind_param("ii", $characterId, $inventoryItemId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return ['success' => false, 'message' => 'Item not found'];
        }

                if (isset($result['quantity']) && $result['quantity'] > 1) {
            $stmt = $this->db->prepare("UPDATE character_inventory SET quantity = quantity - 1 WHERE id = ?");
            $stmt->bind_param("i", $inventoryItemId);
        } else {
            $stmt = $this->db->prepare("DELETE FROM character_inventory WHERE id = ?");
            $stmt->bind_param("i", $inventoryItemId);
        }

        if ($stmt->execute()) {
             return ['success' => true, 'message' => 'Item consumed', 'itemId' => $result['item_id']];
        } else {
             return ['success' => false, 'message' => 'Database error'];
        }
    }

    private function determineSlotForItem($itemSlotType)
    {
                $slotMap = [
            'head' => 'head',
            'shoulders' => 'shoulders',
            'amulet' => 'amulet',
            'chest' => 'chest',
            'belt' => 'belt',
            'legs' => 'legs',
            'boots' => 'boots',
            'ring' => 'ring_1',
            'main_hand' => 'main_hand',
            'off_hand' => 'off_hand',
            'gloves' => 'gloves',
            'bracers' => 'bracers',
            'backpack' => 'backpack'
        ];

        return $slotMap[$itemSlotType] ?? null;
    }
}