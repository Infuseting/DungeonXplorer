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

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, description, base_stats_json FROM classes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        // Décoder le JSON pour faciliter l'accès
        if ($result && !empty($result['base_stats_json'])) {
            $result['base_stats'] = json_decode($result['base_stats_json'], true);
        }
        
        return $result;
    }
}
