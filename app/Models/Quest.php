<?php

namespace App\Models;

class Quest
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all quests
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM quests ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find quest by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM quests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Create new quest
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO quests (name, description, min_level, intro_text, xp_reward, gold_reward) VALUES (?, ?, ?, ?, ?, ?)");
        $xp = $data['xp_reward'] ?? 0;
        $gold = $data['gold_reward'] ?? 0;
        $stmt->bind_param("ssisii", $data['name'], $data['description'], $data['min_level'], $data['intro_text'], $xp, $gold);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Update quest
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE quests SET name = ?, description = ?, min_level = ?, intro_text = ?, xp_reward = ?, gold_reward = ? WHERE id = ?");
        $xp = $data['xp_reward'] ?? 0;
        $gold = $data['gold_reward'] ?? 0;
        $stmt->bind_param("ssisiisi", $data['name'], $data['description'], $data['min_level'], $data['intro_text'], $xp, $gold, $id);
        return $stmt->execute();
    }
    
    /**
     * Delete quest
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM quests WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Get quest stages
     */
    public function getStages($questId)
    {
        $stmt = $this->db->prepare("SELECT * FROM quest_stages WHERE quest_id = ? ORDER BY order_index ASC");
        $stmt->bind_param("i", $questId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRewardItems($questId)
    {
        $stmt = $this->db->prepare("
            SELECT qri.*, i.name, i.type, i.icon 
            FROM quest_reward_items qri
            JOIN items i ON qri.item_id = i.id
            WHERE qri.quest_id = ?
        ");
        $stmt->bind_param("i", $questId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addRewardItem($questId, $itemId, $quantity = 1)
    {
        $stmt = $this->db->prepare("INSERT INTO quest_reward_items (quest_id, item_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $questId, $itemId, $quantity);
        return $stmt->execute();
    }

    public function removeRewardItem($id)
    {
        $stmt = $this->db->prepare("DELETE FROM quest_reward_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Get full quest details (stages + objectives + rewards)
     */
    public function getFullQuest($questId)
    {
        $quest = $this->findById($questId);
        if (!$quest) return null;
        
        $stages = $this->getStages($questId);
        
        foreach ($stages as &$stage) {
            $stmt = $this->db->prepare("SELECT * FROM quest_objectives WHERE stage_id = ?");
            $stmt->bind_param("i", $stage['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stage['objectives'] = $result->fetch_all(MYSQLI_ASSOC);
        }
        unset($stage); // Important: destroy the reference to avoid issues
        
        $quest['stages'] = $stages;
        $quest['reward_items'] = $this->getRewardItems($questId);
        
        return $quest;
    }
    public function getPrerequisites($questId)
    {
        $stmt = $this->db->prepare("
            SELECT q.* 
            FROM quest_prerequisites qp
            JOIN quests q ON qp.required_quest_id = q.id
            WHERE qp.quest_id = ?
        ");
        $stmt->bind_param("i", $questId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addPrerequisite($questId, $requiredQuestId)
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO quest_prerequisites (quest_id, required_quest_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $questId, $requiredQuestId);
        return $stmt->execute();
    }

    public function removePrerequisite($questId, $requiredQuestId)
    {
        $stmt = $this->db->prepare("DELETE FROM quest_prerequisites WHERE quest_id = ? AND required_quest_id = ?");
        $stmt->bind_param("ii", $questId, $requiredQuestId);
        return $stmt->execute();
    }
}
