<?php

namespace App\Models;

use App\Config\Database;

class CharacterAppearance
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($characterId, $appearance)
    {
        $stmt = $this->db->prepare("
            INSERT INTO character_appearance (character_id, skin_color, hair_style, hair_color, eye_color, face_style) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssss", 
            $characterId, 
            $appearance['skin_color'], 
            $appearance['hair_style'], 
            $appearance['hair_color'], 
            $appearance['eye_color'], 
            $appearance['face_style']
        );
        return $stmt->execute();
    }

    public function findByCharacterId($characterId)
    {
        $stmt = $this->db->prepare("SELECT * FROM character_appearance WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
