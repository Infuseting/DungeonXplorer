<?php

namespace App\Models;

use App\Config\Database;

class CharacterBuff
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($characterId, $name, $statModifiers, $durationType, $durationRemaining)
    {
        $stmt = $this->db->prepare("
            INSERT INTO character_buffs 
            (character_id, name, stat_modifiers, duration_type, duration_remaining, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $jsonModifiers = json_encode($statModifiers);
        $stmt->bind_param("isssi", $characterId, $name, $jsonModifiers, $durationType, $durationRemaining);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getActiveBuffs($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM character_buffs 
            WHERE character_id = ? 
            AND duration_remaining > 0
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $buffs = [];
        while ($row = $result->fetch_assoc()) {
            $row['stat_modifiers'] = json_decode($row['stat_modifiers'], true);
            $buffs[] = $row;
        }
        return $buffs;
    }

    public function decreaseDuration($buffId)
    {
        $stmt = $this->db->prepare("
            UPDATE character_buffs 
            SET duration_remaining = duration_remaining - 1 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $buffId);
        return $stmt->execute();
    }

    public function remove($buffId)
    {
        $stmt = $this->db->prepare("DELETE FROM character_buffs WHERE id = ?");
        $stmt->bind_param("i", $buffId);
        return $stmt->execute();
    }
    
    public function removeExpired() {
                $this->db->query("DELETE FROM character_buffs WHERE duration_remaining <= 0");
    }
}
