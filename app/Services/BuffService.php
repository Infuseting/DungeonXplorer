<?php

namespace App\Services;

use App\Config\Database;
use App\Models\Character;

class BuffService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Apply a buff to a character
     * 
     * @param int $characterId
     * @param string $name
     * @param array $modifiers Key-value pair of stats (e.g. ['strength' => 5])
     * @param string $durationType 'seconds' or 'turns'
     * @param int $durationValue
     * @return bool
     */
    public function applyBuff(int $characterId, string $name, array $modifiers, string $durationType, int $durationValue): bool
    {
        $expiresAt = null;
        if ($durationType === 'seconds') {
            $expiresAt = date('Y-m-d H:i:s', time() + $durationValue);
        }

        $jsonModifiers = json_encode($modifiers);

        $stmt = $this->db->prepare("
            INSERT INTO character_buffs (character_id, name, stat_modifiers, duration_type, duration_remaining, expires_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("isssis", $characterId, $name, $jsonModifiers, $durationType, $durationValue, $expiresAt);
        
        return $stmt->execute();
    }

    /**
     * Process turn updates for a character's buffs
     * Decrements 'turns' based buffs
     */
    public function processTurn(int $characterId)
    {
                $stmt = $this->db->prepare("
            UPDATE character_buffs 
            SET duration_remaining = duration_remaining - 1 
            WHERE character_id = ? AND duration_type = 'turns'
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();

                $this->removeExpiredBuffs($characterId);
    }

    /**
     * Remove all expired buffs (both time and turn based)
     */
    public function cleanExpired(int $characterId)
    {
                        $this->removeExpiredBuffs($characterId);
    }

    private function removeExpiredBuffs(int $characterId)
    {
                $stmt = $this->db->prepare("
            DELETE FROM character_buffs 
            WHERE character_id = ? 
            AND (
                duration_remaining <= 0 
                OR (duration_type = 'seconds' AND expires_at < NOW())
            )
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
    }

    /**
     * Get active buffs data + aggregated modifiers
     */
    public function getActiveBuffs(int $characterId)
    {
                $this->cleanExpired($characterId);

        $stmt = $this->db->prepare("SELECT * FROM character_buffs WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $buffs = [];
        $aggregatedStats = [
            'strength' => 0,
            'vitality' => 0,
            'dexterity' => 0,
            'intelligence' => 0,
            'hp_max' => 0         ];

        while ($row = $result->fetch_assoc()) {
            $modifiers = json_decode($row['stat_modifiers'], true);
            $row['modifiers'] = $modifiers;
            $buffs[] = $row;

            if ($modifiers) {
                foreach ($modifiers as $stat => $val) {
                    if (isset($aggregatedStats[$stat])) {
                        $aggregatedStats[$stat] += $val;
                    }
                }
            }
        }

        return [
            'buffs' => $buffs,
            'stats' => $aggregatedStats
        ];
    }
}
