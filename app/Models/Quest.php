<?php

namespace App\Models;

class Quest
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
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
        $stmt = $this->db->prepare("INSERT INTO quests (name, description, min_level, intro_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $data['name'], $data['description'], $data['min_level'], $data['intro_text']);
        
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
        $stmt = $this->db->prepare("UPDATE quests SET name = ?, description = ?, min_level = ?, intro_text = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $data['name'], $data['description'], $data['min_level'], $data['intro_text'], $id);
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
    
    /**
     * Get full quest details (stages + objectives)
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
