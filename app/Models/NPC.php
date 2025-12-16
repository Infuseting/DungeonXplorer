<?php

namespace App\Models;

class NPC
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
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
     * Calculate buy price for item (Price player pays to merchant)
     */
    public function calculateBuyPrice($npcId, $itemId, $basePrice, $characterId = null)
    {
        $modifier = 1.0;
        
        if ($characterId) {
            $repService = new ReputationService();
            // Get NPC Faction
            $npc = $this->findById($npcId);
            $factionId = $npc['faction_id'] ?? null;
            
            if ($factionId) {
                $repValue = $repService->getReputation($characterId, $factionId);
                $modifier = $repService->getBuyPriceModifier($repValue);
            }
        }

        // Apply Difficulty Modifier
        if (isset($_SESSION['current_difficulty'])) {
             $diffService = new DifficultyService();
             $diffMod = $diffService->getPriceModifier($_SESSION['current_difficulty']);
             $modifier *= $diffMod;
        }

        $npc = $this->findById($npcId); // Fetch again or optimize. For now optimize later.
        if (!$npc) return 0;

        // Base behavior: 
        // If merchant sells his own stock -> Base Price * Modifier
        // If rebuying what player sold (buy back) -> we might handle it differently, but for now standard price.
        
        // Wait, standard buying price from merchant is Item Value (or marked up).
        // Let's say Item Value * 1.0 (Standard) * Modifier.
        
        $finalPrice = ceil($basePrice * $modifier);
        
        // Ensure constraints relative to SELL price
        // If player sells this item, how much do they get?
        // We need to ensure BuyPrice > SellPrice * 1.01
        if ($characterId) {
            $sellPrice = $this->calculateSellPrice($npcId, $itemId, $basePrice, $characterId);
            if ($finalPrice <= $sellPrice) {
                $finalPrice = ceil($sellPrice * 1.01);
            }
        }
        
        return $finalPrice;
    }

    /**
     * Calculate sell price for item (Price merchant pays to player)
     */
    public function calculateSellPrice($npcId, $itemId, $basePrice, $characterId = null)
    {
        $npc = $this->findById($npcId);
        if (!$npc) return 0;

        // Base rate based on config
        // "buy_rate_own" is when merchant buys back their own stuff? Or is it generic?
        // Let's assume 'buy_rate_other' is the standard "Merchant buying from Player" rate (e.g. 0.15).
        $baseRate = $npc['buy_rate_other'];
        
        $modifier = 1.0;
        if ($characterId) {
            $repService = new ReputationService();
            $factionId = $npc['faction_id'] ?? null;
            
            if ($factionId) {
                $repValue = $repService->getReputation($characterId, $factionId);
                // Validated: This modifier increases the sell price (e.g. 1.5x)
                $modifier = $repService->getSellPriceModifier($repValue);
            }
        }

        $price = floor($basePrice * $baseRate * $modifier);
        return max(1, $price); // Minimum 1 gold
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
