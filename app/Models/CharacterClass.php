<?php

namespace App\Models;

use App\Config\Database;

class CharacterClass
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT id, name, description, base_stats_json FROM classes ORDER BY id ASC");
        return $stmt->fetch_all(MYSQLI_ASSOC);
    }
    public function getAll() { return $this->findAll(); }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, description, base_stats_json FROM classes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
                if ($result && !empty($result['base_stats_json'])) {
            $result['base_stats'] = json_decode($result['base_stats_json'], true);
        }
        
        return $result;
    }
    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO classes (name, description, base_stats_json) VALUES (?, ?, ?)");
        $jsonStats = json_encode($data['base_stats'] ?? []);
        $stmt->bind_param("sss", $data['name'], $data['description'], $jsonStats);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE classes SET name = ?, description = ?, base_stats_json = ? WHERE id = ?");
        $jsonStats = json_encode($data['base_stats'] ?? []);
        $stmt->bind_param("sssi", $data['name'], $data['description'], $jsonStats, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
