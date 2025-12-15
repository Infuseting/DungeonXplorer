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
            SELECT ci.*, i.name, i.description, i.type, i.slot_type as item_slot_type, i.two_handed, i.width, i.height, i.weight, i.icon, i.stats, i.max_stack, i.price
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

            if ($item['location'] === 'equipped') {
                $inventory['equipped'][$item['slot_name']] = $item;
            } else {
                // All non-equipped items (backpack/pockets) go to 'inventory'
                $inventory['inventory'][] = $item;
            }
        }

        // Calculate max weight: 60 + Strength + Backpack Capacity
        $maxWeight = $this->calculateMaxWeight($characterId, $inventory['equipped']);

        $inventory['current_weight'] = $currentWeight;
        $inventory['max_weight'] = $maxWeight;

        return $inventory;
    }

    private function calculateMaxWeight($characterId, $equippedItems)
    {
        // Base weight
        $baseWeight = 60;

        // Get character strength
        $stmt = $this->db->prepare("SELECT strength FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $strength = $stats['strength'] ?? 0;

        // Get backpack capacity if equipped
        $backpackCapacity = 0;
        if (isset($equippedItems['backpack'])) {
            $backpackStats = json_decode($equippedItems['backpack']['stats'] ?? '{}', true);
            $backpackCapacity = $backpackStats['capacity'] ?? 0;
        }

        return $baseWeight + $strength + $backpackCapacity;
    }

    public function moveItem($characterId, $inventoryItemId, $targetLocation, $targetSlot = null, $targetX = null, $targetY = null)
    {
        // 1. Get the item
        $item = $this->getItemInInventory($characterId, $inventoryItemId);
        if (!$item) return ['success' => false, 'message' => 'Item not found'];

        // 2. Validate Target Location
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
        // Simply move to inventory location (stored as 'backpack' in DB)
        $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'backpack', slot_name = NULL, grid_x = NULL, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("i", $item['id']);
        
        return ['success' => $stmt->execute()];
    }

    private function equipItem($characterId, $item, $slotName)
    {
        // Validate slot type - allow flexible matching
        $validSlot = false;
        
        // Direct match
        if ($item['item_slot_type'] === $slotName) {
            $validSlot = true;
        }
        // Ring can go in ring_1 or ring_2
        elseif ($item['item_slot_type'] === 'ring' && in_array($slotName, ['ring_1', 'ring_2'])) {
            $validSlot = true;
        }
        // Main hand weapons can go in main_hand or off_hand
        elseif ($item['item_slot_type'] === 'main_hand' && in_array($slotName, ['main_hand', 'off_hand'])) {
            $validSlot = true;
        }
        
        if (!$validSlot) {
             return ['success' => false, 'message' => 'Invalid slot for this item'];
        }

        // Check if slot is occupied
        $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND location = 'equipped' AND slot_name = ?");
        $stmt->bind_param("is", $characterId, $slotName);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
             return ['success' => false, 'message' => 'Slot occupied'];
        }

        // If two-handed weapon, check if the opposite slot is free
        if ($item['two_handed']) {
            $oppositeSlot = ($slotName === 'main_hand') ? 'off_hand' : 'main_hand';
            $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND location = 'equipped' AND slot_name = ?");
            $stmt->bind_param("is", $characterId, $oppositeSlot);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                return ['success' => false, 'message' => 'Both hands must be empty for two-handed weapons'];
            }
        }

        // If equipping to main_hand, check if off_hand has a two-handed weapon
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

        // If equipping to off_hand, check if main_hand has a two-handed weapon
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

    // Public method for equipping by item ID (for Ctrl+Click)
    public function equipItemById($characterId, $inventoryItemId)
    {
        $item = $this->getItemInInventory($characterId, $inventoryItemId);
        if (!$item) return ['success' => false, 'message' => 'Item not found'];

        // Determine the correct slot based on item type
        $slotName = $this->determineSlotForItem($item['item_slot_type']);
        if (!$slotName) {
            return ['success' => false, 'message' => 'This item cannot be equipped'];
        }

        return $this->equipItem($characterId, $item, $slotName);
    }

    // Public method for unequipping by slot name
    public function unequipItem($characterId, $slotName)
    {
        // Find the item in the slot
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

        // Move to inventory (stored as 'backpack')
        $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'backpack', slot_name = NULL, grid_x = NULL, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("i", $result['id']);
        return ['success' => $stmt->execute()];
    }

    public function addItem($characterId, $itemId)
    {
        // 1. Get Item Details
        $stmt = $this->db->prepare("SELECT width, height, max_stack, type, weight FROM items WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $itemData = $stmt->get_result()->fetch_assoc();

        if (!$itemData) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        // 2. Check weight limit
        $inventory = $this->getCharacterInventory($characterId);
        $itemWeight = floatval($itemData['weight'] ?? 0);
        
        if (($inventory['current_weight'] + $itemWeight) > $inventory['max_weight']) {
            return ['success' => false, 'message' => 'Inventory too heavy'];
        }

        // 3. Add to inventory (stored as 'backpack')
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
        // 1. Verify ownership
        $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND id = ?");
        $stmt->bind_param("ii", $characterId, $inventoryItemId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'message' => 'Item not found or not owned'];
        }

        // 2. Delete
        $stmt = $this->db->prepare("DELETE FROM character_inventory WHERE id = ?");
        $stmt->bind_param("i", $inventoryItemId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Item deleted'];
        } else {
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    private function determineSlotForItem($itemSlotType)
    {
        // Map item slot types to actual slot names
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