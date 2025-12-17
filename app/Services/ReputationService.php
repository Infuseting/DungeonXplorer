<?php

namespace App\Services;

use App\Config\Database;

class ReputationService
{
    private $db;

        const HATED = -3000;
    const HOSTILE = -1000;
    const UNFRIENDLY = -500;     const NEUTRAL = 0;
    const FRIENDLY = 500;
    const HONORED = 1000;
    const REVERED = 5000;
    const EXALTED = 10000;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get player reputation with a faction
     */
    public function getReputation(int $characterId, int $factionId): int
    {
        $stmt = $this->db->prepare("SELECT reputation_value FROM character_reputations WHERE character_id = ? AND faction_id = ?");
        $stmt->bind_param("ii", $characterId, $factionId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        
        return $res ? (int)$res['reputation_value'] : 0;
    }

    /**
     * Modify reputation
     */
    public function modifyReputation(int $characterId, int $factionId, int $amount): bool
    {
                $current = $this->getReputation($characterId, $factionId);
        $newValue = $current + $amount;

        $stmt = $this->db->prepare("
            INSERT INTO character_reputations (character_id, faction_id, reputation_value, unlocked_at)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE reputation_value = ?
        ");
        $stmt->bind_param("iiii", $characterId, $factionId, $newValue, $newValue);
        return $stmt->execute();
    }

    /**
     * Get Readable Level
     */
    public function getReputationLevel(int $value): string
    {
        if ($value >= self::EXALTED) return 'Exalté';
        if ($value >= self::REVERED) return 'Révéré';
        if ($value >= self::HONORED) return 'Honoré';
        if ($value >= self::FRIENDLY) return 'Amical';
        if ($value >= self::NEUTRAL) return 'Neutre';
        if ($value >= self::UNFRIENDLY) return 'Inamical';
        if ($value >= self::HOSTILE) return 'Hostile';
        return 'Haï';
    }

    /**
     * Get Price Multiplier for Buying items
     * Returns multiplier (e.g., 0.9 for 10% discount)
     */
    public function getBuyPriceModifier(int $value): float
    {
        if ($value >= self::EXALTED) return 0.75;         if ($value >= self::REVERED) return 0.85;         if ($value >= self::HONORED) return 0.90;         if ($value >= self::FRIENDLY) return 0.95;         
        if ($value <= self::HATED) return 2.0;            if ($value <= self::HOSTILE) return 1.5;          if ($value <= self::UNFRIENDLY) return 1.1; 
        return 1.0;     }

    /**
     * Get Price Multiplier for Selling items (Player selling to Merchant)
     * High rep = Merchant pays more
     */
    public function getSellPriceModifier(int $value): float
    {
                        
        if ($value >= self::EXALTED) return 1.50;         if ($value >= self::REVERED) return 1.25; 
        if ($value >= self::HONORED) return 1.10; 
        
        if ($value <= self::UNFRIENDLY) return 0.8; 
        return 1.0;
    }
}
