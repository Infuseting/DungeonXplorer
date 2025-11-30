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

    public function create($characterId, $skinColor, $hairColor, $hairStyle, $eyeColor)
    {
        $stmt = $this->db->prepare("
            INSERT INTO character_appearances (character_id, skin_color, hair_color, hair_style, eye_color) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issss", $characterId, $skinColor, $hairColor, $hairStyle, $eyeColor);
        return $stmt->execute();
    }

    public function findByCharacterId($characterId)
    {
        $stmt = $this->db->prepare("SELECT * FROM character_appearances WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($characterId, $skinColor, $hairColor, $hairStyle, $eyeColor)
    {
        $stmt = $this->db->prepare("
            UPDATE character_appearances 
            SET skin_color = ?, hair_color = ?, hair_style = ?, eye_color = ?
            WHERE character_id = ?
        ");
        $stmt->bind_param("ssssi", $skinColor, $hairColor, $hairStyle, $eyeColor, $characterId);
        return $stmt->execute();
    }
}