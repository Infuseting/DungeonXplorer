<?php

namespace App\Models;

use App\Config\Database;

class Character
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $classId, $name)
    {
        $stmt = $this->db->prepare("INSERT INTO characters (user_id, class_id, name) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $classId, $name);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function updateAppearance($id, $appearanceData)
    {
        $jsonAppearance = json_encode($appearanceData);
        $stmt = $this->db->prepare("UPDATE characters SET appearance = ? WHERE id = ?");
        $stmt->bind_param("si", $jsonAppearance, $id);
        return $stmt->execute();
    }

    public function findAllByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cl.name as class_name, cs.level 
            FROM characters c
            JOIN classes cl ON c.class_id = cl.id
            LEFT JOIN character_stats cs ON c.id = cs.character_id
            WHERE c.user_id = ?
            ORDER BY c.last_played_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($results as &$character) {
            if (!empty($character['appearance'])) {
                $character['appearance'] = json_decode($character['appearance'], true);
            }
        }
        
        return $results;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM characters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result && !empty($result['appearance'])) {
            $result['appearance'] = json_decode($result['appearance'], true);
        }
        
        return $result;
    }

    public function updateLastPlayed($id)
    {
        $stmt = $this->db->prepare("UPDATE characters SET last_played_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM characters WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        return $stmt->execute();
    }
}
