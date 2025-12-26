<?php

namespace App\Models;

use App\Config\Database;
use App\Services\CharacterStatsService;

class CharacterStats
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($characterId, $classId)
    {
                $stmt = $this->db->prepare("SELECT base_stats_json FROM classes WHERE id = ?");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        $classData = $result->fetch_assoc();
        
        if (!$classData || empty($classData['base_stats_json'])) {
            error_log("CharacterStats::create - Class not found or no base stats for classId: " . $classId);
            return false;
        }
        
                $baseStats = json_decode($classData['base_stats_json'], true);
        
        if (!$baseStats) {
            error_log("CharacterStats::create - Invalid JSON in base_stats_json for classId: " . $classId);
            return false;
        }
        
                $strength = (int)($baseStats['strength'] ?? 10);
        $dexterity = (int)($baseStats['dexterity'] ?? 10);
        $intelligence = (int)($baseStats['intelligence'] ?? 10);
        $vitality = (int)($baseStats['vitality'] ?? 10);
        
                $stmt = $this->db->prepare("
            INSERT INTO character_stats 
            (character_id, level, xp, strength, dexterity, intelligence, vitality) 
            VALUES (?, 1, 0, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "iiiii", 
            $characterId, 
            $strength, 
            $dexterity, 
            $intelligence, 
            $vitality
        );
        
        $success = $stmt->execute();
        
        if (!$success) {
            error_log("CharacterStats::create - SQL Error: " . $stmt->error);
        }
        
        return $success;
    }

    public function findByCharacterId($characterId)
    {
        $stmt = $this->db->prepare("SELECT * FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function update($characterId, $data)
    {
        $fields = [];
        $values = [];
        $types = '';
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
            $types .= is_int($value) ? 'i' : 's';
        }
        
        $values[] = $characterId;
        $types .= 'i';
        
        $sql = "UPDATE character_stats SET " . implode(', ', $fields) . " WHERE character_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }

    /**
     * Get effective stats (Base + Equipment + Penalties)
     * @param int $characterId
     * @return array
     * @deprecated Utiliser CharacterStatsService::getEffectiveStats() à la place
     */
    public function getEffectiveStats($characterId)
    {
        // Déléguer au service centralisé
        $stats = CharacterStatsService::getEffectiveStats($characterId);
        
        // Retourner dans l'ancien format pour compatibilité
        return [
            'stats' => [
                'strength' => $stats['strength'],
                'dexterity' => $stats['dexterity'],
                'intelligence' => $stats['intelligence'],
                'vitality' => $stats['vitality']
            ],
            'base_stats' => $this->findByCharacterId($characterId),
            'weight' => [
                'current' => $stats['current_weight'],
                'max' => $stats['max_weight'],
                'is_overweight' => $stats['is_overweight']
            ]
        ];
    }
}
