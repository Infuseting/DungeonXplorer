<?php

namespace App\Models;

class QuestObjective
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO quest_objectives (stage_id, type, target_id, count_required, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiis", $data['stage_id'], $data['type'], $data['target_id'], $data['count_required'], $data['description']);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE quest_objectives SET type = ?, target_id = ?, count_required = ?, description = ? WHERE id = ?");
        $stmt->bind_param("siisi", $data['type'], $data['target_id'], $data['count_required'], $data['description'], $id);
        return $stmt->execute();
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM quest_objectives WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
