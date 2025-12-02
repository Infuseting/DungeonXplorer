<?php

namespace App\Models;

class NPC
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    /**
     * Get all NPCs
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM npcs ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find NPC by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM npcs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Create new NPC
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO npcs (name, role, texture, merchant_seed, buy_rate_own, buy_rate_other) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "sssidd",
            $data['name'],
            $data['role'],
            $data['texture'],
            $data['merchant_seed'],
            $data['buy_rate_own'],
            $data['buy_rate_other']
        );
        echo $data['role'];
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Update NPC
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE npcs SET name = ?, role = ?, texture = ?, merchant_seed = ?, buy_rate_own = ?, buy_rate_other = ? WHERE id = ?");

        $stmt->bind_param(
            "sssiddi",
            $data['name'],
            $data['role'],
            $data['texture'],
            $data['merchant_seed'],
            $data['buy_rate_own'],
            $data['buy_rate_other'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete NPC
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM npcs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Generate merchant inventory based on SEED
     * Returns 10-20 random items with prices
     */
    public function generateMerchantInventory($seed)
    {
        // Initialize random generator with SEED
        mt_srand($seed);
        
        // Get all items with price
        $result = $this->db->query("SELECT * FROM items WHERE price IS NOT NULL AND price > 0");
        $availableItems = $result->fetch_all(MYSQLI_ASSOC);
        
        if (empty($availableItems)) {
            return [];
        }
        
        // Random count between 10 and 20
        $count = mt_rand(10, min(20, count($availableItems)));
        
        // Shuffle and select items
        shuffle($availableItems);
        $selectedItems = array_slice($availableItems, 0, $count);
        
        // Reset random generator
        mt_srand();
        
        return $selectedItems;
    }
    
    /**
     * Get merchant inventory for NPC
     */
    public function getMerchantInventory($npcId)
    {
        $stmt = $this->db->prepare("
            SELECT i.*, nmi.quantity 
            FROM npc_merchant_inventory nmi
            JOIN items i ON nmi.item_id = i.id
            WHERE nmi.npc_id = ?
        ");
        $stmt->bind_param("i", $npcId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Save merchant inventory
     */
    public function saveMerchantInventory($npcId, $items)
    {
        // Clear existing inventory
        $stmt = $this->db->prepare("DELETE FROM npc_merchant_inventory WHERE npc_id = ?");
        $stmt->bind_param("i", $npcId);
        $stmt->execute();
        
        // Insert new inventory
        $stmt = $this->db->prepare("
            INSERT INTO npc_merchant_inventory (npc_id, item_id, quantity)
            VALUES (?, ?, ?)
        ");
        
        foreach ($items as $item) {
            $itemId = $item['id'];
            $quantity = 1;
            $stmt->bind_param("iii", $npcId, $itemId, $quantity);
            $stmt->execute();
        }
        
        return true;
    }
    
    /**
     * Calculate buy price for item
     */
    public function calculateBuyPrice($npcId, $itemId, $basePrice)
    {
        $npc = $this->findById($npcId);
        if (!$npc) return 0;
        
        // Check if item is in merchant's inventory
        $stmt = $this->db->prepare("
            SELECT 1 FROM npc_merchant_inventory 
            WHERE npc_id = ? AND item_id = ?
        ");
        $stmt->bind_param("ii", $npcId, $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $isOwnItem = $result->num_rows > 0;
        
        if ($isOwnItem) {
            // Item sold by merchant: 5% of base price
            return $basePrice * $npc['buy_rate_own'];
        } else {
            // Other items: 15% of base price
            return $basePrice * $npc['buy_rate_other'];
        }
    }
    
    /**
     * Get dialogue trees assigned to NPC
     */
    public function getDialogueTrees($npcId)
    {
        $stmt = $this->db->prepare("
            SELECT dt.* 
            FROM dialogue_trees dt
            JOIN npc_dialogue_trees ndt ON dt.id = ndt.tree_id
            WHERE ndt.npc_id = ?
        ");
        $stmt->bind_param("i", $npcId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Assign dialogue tree to NPC
     */
    public function assignDialogueTree($npcId, $treeId)
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO npc_dialogue_trees (npc_id, tree_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $npcId, $treeId);
        return $stmt->execute();
    }
    
    /**
     * Remove dialogue tree from NPC
     */
    public function removeDialogueTree($npcId, $treeId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM npc_dialogue_trees 
            WHERE npc_id = ? AND tree_id = ?
        ");
        $stmt->bind_param("ii", $npcId, $treeId);
        return $stmt->execute();
    }

    /**
     * Get quests assigned to NPC
     */
    public function getQuests($npcId)
    {
        $stmt = $this->db->prepare("
            SELECT q.*, nq.type as relation_type
            FROM npc_quests nq
            JOIN quests q ON nq.quest_id = q.id
            WHERE nq.npc_id = ?
        ");
        $stmt->bind_param("i", $npcId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Assign quest to NPC
     */
    public function assignQuest($npcId, $questId)
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO npc_quests (npc_id, quest_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $npcId, $questId);
        return $stmt->execute();
    }

    /**
     * Remove quest from NPC
     */
    public function removeQuest($npcId, $questId)
    {
        $stmt = $this->db->prepare("DELETE FROM npc_quests WHERE npc_id = ? AND quest_id = ?");
        $stmt->bind_param("ii", $npcId, $questId);
        return $stmt->execute();
    }
}
