<?php

namespace App\Models;

class QuestStage
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO quest_stages (quest_id, name, description, order_index, rewards_json) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issis", $data['quest_id'], $data['name'], $data['description'], $data['order_index'], $data['rewards_json']);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE quest_stages SET name = ?, description = ?, order_index = ?, rewards_json = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $data['name'], $data['description'], $data['order_index'], $data['rewards_json'], $id);
        return $stmt->execute();
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM quest_stages WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getMapUnlocks($stageId)
    {
        $stmt = $this->db->prepare("
            SELECT mp.id, mp.name, mp.x, mp.y 
            FROM quest_stage_unlocks qsu
            JOIN map_points mp ON qsu.map_point_id = mp.id
            WHERE qsu.quest_stage_id = ?
        ");
        $stmt->bind_param("i", $stageId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addMapUnlock($stageId, $mapPointId)
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO quest_stage_unlocks (quest_stage_id, map_point_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $stageId, $mapPointId);
        return $stmt->execute();
    }

    public function removeMapUnlock($stageId, $mapPointId)
    {
        $stmt = $this->db->prepare("DELETE FROM quest_stage_unlocks WHERE quest_stage_id = ? AND map_point_id = ?");
        $stmt->bind_param("ii", $stageId, $mapPointId);
        return $stmt->execute();
    }
}
