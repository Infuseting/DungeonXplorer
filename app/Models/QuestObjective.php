<?php

namespace App\Models;

class QuestObjective
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO quest_objectives (stage_id, type, target_id, count_required, description, dialogue_tree_id) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stageId = $data['stage_id'];
        $type = $data['type'];
        $targetId = $data['target_id'];
        $countRequired = $data['count_required'];
        $description = $data['description'];
        $dialogueTreeId = $data['dialogue_tree_id'] ?? null;
        
        $stmt->bind_param("isiisi", $stageId, $type, $targetId, $countRequired, $description, $dialogueTreeId);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE quest_objectives SET type = ?, target_id = ?, count_required = ?, description = ?, dialogue_tree_id = ? WHERE id = ?");
        
        $type = $data['type'];
        $targetId = $data['target_id'];
        $countRequired = $data['count_required'];
        $description = $data['description'];
        $dialogueTreeId = $data['dialogue_tree_id'] ?? null;
        
        $stmt->bind_param("siisii", $type, $targetId, $countRequired, $description, $dialogueTreeId, $id);
        return $stmt->execute();
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM quest_objectives WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
