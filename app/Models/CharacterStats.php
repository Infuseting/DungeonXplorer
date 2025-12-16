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

    public function create($characterId, $classId)
    {
        // Récupérer les stats de base de la classe depuis JSON
        $stmt = $this->db->prepare("SELECT base_stats_json FROM classes WHERE id = ?");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        $classData = $result->fetch_assoc();
        
        if (!$classData || empty($classData['base_stats_json'])) {
            error_log("CharacterStats::create - Class not found or no base stats for classId: " . $classId);
            return false;
        }
        
        // Décoder le JSON
        $baseStats = json_decode($classData['base_stats_json'], true);
        
        if (!$baseStats) {
            error_log("CharacterStats::create - Invalid JSON in base_stats_json for classId: " . $classId);
            return false;
        }
        
        // Extraire les valeurs (avec valeurs par défaut si absentes)
        $strength = (int)($baseStats['strength'] ?? 10);
        $dexterity = (int)($baseStats['dexterity'] ?? 10);
        $intelligence = (int)($baseStats['intelligence'] ?? 10);
        $vitality = (int)($baseStats['vitality'] ?? 10);
        
        // Créer les stats du personnage
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
     */
    public function getEffectiveStats($characterId)
    {
        // 1. Get Base Stats
        $baseStats = $this->findByCharacterId($characterId);
        if (!$baseStats) return null;

        $effectiveStats = [
            'strength' => (int)$baseStats['strength'],
            'dexterity' => (int)$baseStats['dexterity'],
            'intelligence' => (int)$baseStats['intelligence'],
            'vitality' => (int)$baseStats['vitality'],
            
        ];

        // 2. Get Equipped Items
        $inventoryModel = new Inventory();
        $inventoryData = $inventoryModel->getCharacterInventory($characterId);
        $equippedItems = $inventoryData['equipped'];

        // 3. Calculate Bonus Stats from Equipment
        foreach ($equippedItems as $item) {
            if (!empty($item['stats'])) {
                $itemStats = json_decode($item['stats'], true);
                if ($itemStats) {
                    foreach ($itemStats as $stat => $value) {
                        // Map specific item stats to main stats if names match, or handle mapping
                        // Assuming item stats keys match 'strength', 'dexterity', etc. for simplicity
                        if (isset($effectiveStats[$stat])) {
                            $effectiveStats[$stat] += (int)$value;
                        }
                    }
                }
            }
        }


        // 3.5 Apply Passive Skills
        $skillModel = new Skill();
        $passives = $skillModel->getPassiveBonuses($characterId);
        foreach ($passives as $p) {
            $type = $p['effect_type']; // e.g., 'passive_str_flat'
            $value = (int)$p['effect_value'];
            
            // Expected format: passive_{stat}_{flat|percent}
            // Simple mapping for now:
            if ($type === 'passive_def_flat') {
                // effectiveStats doesn't have 'defense' usually, it's calculated.
                // But check getEffectiveStats return structure. It returns 'stats' array (str, dex, int, vit).
                // Defense is usually derived.
                // If we want to support defense bonus, we might need a 'bonus_defense' field in the array?
                // Or modify main stats.
                // Let's handle main stats first.
            } elseif (strpos($type, 'passive_') === 0 && strpos($type, '_flat') !== false) {
                // e.g. passive_str_flat -> maps to strength?
                // type: passive_str_flat
                // parts: [passive, str, flat]
                $parts = explode('_', $type);
                if (count($parts) >= 3) {
                    $statShort = $parts[1];
                    $statMap = [
                        'str' => 'strength',
                        'dex' => 'dexterity',
                        'int' => 'intelligence',
                        'vit' => 'vitality'
                    ];
                    if (isset($statMap[$statShort])) {
                        $fullStat = $statMap[$statShort];
                        if (isset($effectiveStats[$fullStat])) {
                            $effectiveStats[$fullStat] += $value;
                        }
                    }
                }
                }
            }

            // And I will try to update `Character` to load effective stats or use them.
            // User request is "Systeme de gain ... Interface ... Actions".
            // If I stick to `CharacterStats::getEffectiveStats`, at least weight/init are correct.
            // But Combat uses `Character` methods.
            
            // Quick fix: Update `Character.php` to fetch effective stats on load?
            // Or `getStrength()` calls `$stats->getEffectiveStats()`?
            
            // Let's finish `CharacterStats.php` first.
        if (isset($equippedItems['backpack']) && !empty($equippedItems['backpack']['stats'])) {
            $bpStats = json_decode($equippedItems['backpack']['stats'], true);
            $backpackCapacity = (int)($bpStats['capacity'] ?? 0);
        }

        $maxWeight = 60 + $effectiveStats['strength'] + $backpackCapacity;
        $currentWeight = $inventoryData['current_weight'];

        // 5. Apply Overweight Penalty
        $isOverweight = $currentWeight > $maxWeight;
        
        if ($isOverweight) {
            foreach ($effectiveStats as $key => $val) {
                $effectiveStats[$key] = floor($val * 0.5); // -50% penalty
            }
        }

        return [
            'stats' => $effectiveStats,
            'base_stats' => $baseStats,
            'weight' => [
                'current' => $currentWeight,
                'max' => $maxWeight,
                'is_overweight' => $isOverweight
            ]
        ];
    }
}
