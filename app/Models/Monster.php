<?php

namespace App\Models;

use App\Config\Database;

class Monster
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all monsters
     * 
     * @return array
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM monsters ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find monster by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create a new monster
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO monsters (name, image_path, salle_path, level_min, level_max, base_stats_json) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $statsJson = json_encode($data['stats'] ?? []);
        
        $stmt->bind_param(
            "sssiis", 
            $data['name'], 
            $data['image_path'],
            $data['salle_path'],
            $data['level_min'],
            $data['level_max'], 
            $statsJson
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update a monster
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE monsters 
             SET name = ?, image_path = ?, salle_path = ?, level_min = ?, level_max = ?, base_stats_json = ? 
             WHERE id = ?"
        );
        
        $statsJson = json_encode($data['stats'] ?? []);
        
        $stmt->bind_param(
            "sssiisi", 
            $data['name'], 
            $data['image_path'], 
            $data['salle_path'],
            $data['level_min'],
            $data['level_max'],
            $statsJson,
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a monster
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
