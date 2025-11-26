<?php

namespace App\Models;

use App\Config\Database;

class CharacterStats
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($characterId, $stats)
    {
        $stmt = $this->db->prepare("
            INSERT INTO character_stats (character_id, strength, dexterity, intelligence, vitality) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiiii", 
            $characterId, 
            $stats['strength'], 
            $stats['dexterity'], 
            $stats['intelligence'], 
            $stats['vitality']
        );
        return $stmt->execute();
    }

    public function findByCharacterId($characterId)
    {
        $stmt = $this->db->prepare("SELECT * FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}