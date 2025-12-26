<?php

namespace App\Models;
use App\Config\Database;
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
                mt_srand($seed);
        
                $result = $this->db->query("SELECT * FROM items WHERE price IS NOT NULL AND price > 0");
        $availableItems = $result->fetch_all(MYSQLI_ASSOC);
        
        if (empty($availableItems)) {
            return [];
        }
        
                $count = mt_rand(10, min(20, count($availableItems)));
        
                shuffle($availableItems);
        $selectedItems = array_slice($availableItems, 0, $count);
        
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
        $items = $result->fetch_all(MYSQLI_ASSOC);
        
        // Ensure stats is always a valid JSON string
        foreach ($items as &$item) {
            if (empty($item['stats'])) {
                $item['stats'] = '{}';
            }
        }
        
        return $items;
    }
    
    /**
     * Save merchant inventory
     */
    public function saveMerchantInventory($npcId, $items)
    {
                $stmt = $this->db->prepare("DELETE FROM npc_merchant_inventory WHERE npc_id = ?");
        $stmt->bind_param("i", $npcId);
        $stmt->execute();
        
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
                        $npc = $this->findById($npcId);
            $factionId = $npc['faction_id'] ?? null;
            
            if ($factionId) {
                $repValue = $repService->getReputation($characterId, $factionId);
                $modifier = $repService->getBuyPriceModifier($repValue);
            }
        }

                if (isset($_SESSION['current_difficulty'])) {
             $diffService = new DifficultyService();
             $diffMod = $diffService->getPriceModifier($_SESSION['current_difficulty']);
             $modifier *= $diffMod;
        }

        $npc = $this->findById($npcId);         if (!$npc) return 0;

                                
                        
        $finalPrice = ceil($basePrice * $modifier);
        
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

                                $baseRate = $npc['buy_rate_other'];
        
        $modifier = 1.0;
        if ($characterId) {
            $repService = new ReputationService();
            $factionId = $npc['faction_id'] ?? null;
            
            if ($factionId) {
                $repValue = $repService->getReputation($characterId, $factionId);
                                $modifier = $repService->getSellPriceModifier($repValue);
            }
        }

        $price = floor($basePrice * $baseRate * $modifier);
        return max(1, $price);     }
    
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
