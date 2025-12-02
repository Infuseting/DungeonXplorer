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
            SELECT ci.*, i.name, i.description, i.type, i.slot_type as item_slot_type, i.two_handed, i.width, i.height, i.weight, i.icon, i.stats, i.max_stack
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
            'backpack' => [],
            'pockets' => []
        ];

        foreach ($result as $item) {
            if ($item['location'] === 'equipped') {
                $inventory['equipped'][$item['slot_name']] = $item;
            } elseif ($item['location'] === 'backpack') {
                $inventory['backpack'][] = $item;
            } elseif ($item['location'] === 'pockets') {
                // Use grid_x as pocket slot index (0-3)
                $pocketIndex = $item['grid_x'] ?? 0;
                $inventory['pockets'][$pocketIndex] = $item;
            }
        }
        return $inventory;
    }

    public function moveItem($characterId, $inventoryItemId, $targetLocation, $targetSlot = null, $targetX = null, $targetY = null)
    {
        // 1. Get the item and current inventory
        $item = $this->getItemInInventory($characterId, $inventoryItemId);
        if (!$item) return ['success' => false, 'message' => 'Item not found'];

        // 2. Validate Target Location
        if ($targetLocation === 'backpack') {
            return $this->moveToBackpack($characterId, $item, $targetX, $targetY);
        } elseif ($targetLocation === 'pockets') {
            return $this->moveToPockets($characterId, $item, $targetSlot);
        } elseif ($targetLocation === 'equipped') {
            return $this->equipItem($characterId, $item, $targetSlot);
        }

        return ['success' => false, 'message' => 'Invalid location'];
    }

    private function getItemInInventory($characterId, $inventoryItemId)
    {
        $stmt = $this->db->prepare("
            SELECT ci.*, i.width, i.height, i.slot_type as item_slot_type, i.two_handed, i.icon
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ? AND ci.id = ?
        ");
        $stmt->bind_param("ii", $characterId, $inventoryItemId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function moveToBackpack($characterId, $item, $x, $y)
    {
        // Basic bounds check (assuming 6x4 grid for now, should be dynamic based on bag)
        $gridWidth = 6;
        $gridHeight = 4;

        if ($x < 0 || $y < 0 || ($x + $item['width']) > $gridWidth || ($y + $item['height']) > $gridHeight) {
            return ['success' => false, 'message' => 'Out of bounds'];
        }

        // Collision check
        $inventory = $this->getCharacterInventory($characterId);
        foreach ($inventory['backpack'] as $existingItem) {
            if ($existingItem['id'] == $item['id']) continue; // Skip self

            // Check rectangle overlap
            if ($x < ($existingItem['grid_x'] + $existingItem['width']) &&
                ($x + $item['width']) > $existingItem['grid_x'] &&
                $y < ($existingItem['grid_y'] + $existingItem['height']) &&
                ($y + $item['height']) > $existingItem['grid_y']) {
                return ['success' => false, 'message' => 'Space occupied'];
            }
        }

        // Update DB
        $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'backpack', slot_name = NULL, grid_x = ?, grid_y = ? WHERE id = ?");
        $stmt->bind_param("iii", $x, $y, $item['id']);
        
        return ['success' => $stmt->execute()];
    }

    private function moveToPockets($characterId, $item, $slotIndex)
    {
        // Validate slot index (0-3)
        if ($slotIndex < 0 || $slotIndex > 3) {
            return ['success' => false, 'message' => 'Invalid pocket slot'];
        }

        // Check if pocket slot is already occupied (skip if it's the same item)
        $stmt = $this->db->prepare("SELECT id FROM character_inventory WHERE character_id = ? AND location = 'pockets' AND grid_x = ? AND id != ?");
        $stmt->bind_param("iii", $characterId, $slotIndex, $item['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Pocket slot occupied'];
        }

        // Move item to pocket slot (use grid_x as pocket index)
        $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'pockets', slot_name = NULL, grid_x = ?, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("ii", $slotIndex, $item['id']);
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

        // Find first available pocket slot
        $inventory = $this->getCharacterInventory($characterId);
        $availableSlot = null;
        for ($i = 0; $i < 4; $i++) {
            if (!isset($inventory['pockets'][$i])) {
                $availableSlot = $i;
                break;
            }
        }

        if ($availableSlot === null) {
            return ['success' => false, 'message' => 'No available pocket slots'];
        }

        // Move to first available pocket slot
        $stmt = $this->db->prepare("UPDATE character_inventory SET location = 'pockets', slot_name = NULL, grid_x = ?, grid_y = NULL WHERE id = ?");
        $stmt->bind_param("ii", $availableSlot, $result['id']);
        return ['success' => $stmt->execute()];
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