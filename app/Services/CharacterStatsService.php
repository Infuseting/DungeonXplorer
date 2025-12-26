<?php

namespace App\Services;

use App\Models\CharacterStats;
use App\Models\Inventory;
use App\Models\Skill;

class CharacterStatsService
{
    private static $cache = [];

    /**
     * Calcule les stats effectives totales d'un personnage
     * (Base + Items équipés + Compétences passives - Pénalités)
     * 
     * @param int $characterId
     * @param bool $useCache
     * @return array ['strength', 'dexterity', 'intelligence', 'vitality', 'attack', 'defense']
     */
    public static function getEffectiveStats($characterId, $useCache = true)
    {
        if ($useCache && isset(self::$cache[$characterId])) {
            return self::$cache[$characterId];
        }

        $statsModel = new CharacterStats();
        $baseStats = $statsModel->findByCharacterId($characterId);
        
        if (!$baseStats) {
            return self::getDefaultStats();
        }

        // 1. Stats de base
        $effectiveStats = [
            'strength' => (int)$baseStats['strength'],
            'dexterity' => (int)$baseStats['dexterity'],
            'intelligence' => (int)$baseStats['intelligence'],
            'vitality' => (int)$baseStats['vitality']
        ];

        // 2. Bonus des items équipés
        $inventoryModel = new Inventory();
        $inventoryData = $inventoryModel->getCharacterInventory($characterId);
        $equippedItems = $inventoryData['equipped'];

        foreach ($equippedItems as $item) {
            if (!empty($item['instance_stats'])) {
                // instance_stats est déjà un JSON string ou un array
                $itemStats = is_string($item['instance_stats']) 
                    ? json_decode($item['instance_stats'], true) 
                    : $item['instance_stats'];
                    
                if ($itemStats) {
                    foreach ($itemStats as $stat => $value) {
                        if (isset($effectiveStats[$stat])) {
                            $effectiveStats[$stat] += (int)$value;
                        }
                    }
                }
            } elseif (!empty($item['stats'])) {
                // Fallback sur stats si instance_stats n'existe pas
                $itemStats = is_string($item['stats']) 
                    ? json_decode($item['stats'], true) 
                    : $item['stats'];
                    
                if ($itemStats) {
                    foreach ($itemStats as $stat => $value) {
                        if (isset($effectiveStats[$stat])) {
                            $effectiveStats[$stat] += (int)$value;
                        }
                    }
                }
            }
        }

        // 3. Bonus des compétences passives
        $skillModel = new Skill();
        $passives = $skillModel->getPassiveBonuses($characterId);
        
        foreach ($passives as $passive) {
            $type = $passive['effect_type'];
            $value = (int)$passive['effect_value'];
            
            // Mapping des types de passives vers les stats
            if (strpos($type, 'passive_') === 0 && strpos($type, '_flat') !== false) {
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

        // 4. Pénalité de surpoids
        $backpackCapacity = 0;
        if (isset($equippedItems['backpack']) && !empty($equippedItems['backpack']['stats'])) {
            $bpStats = is_string($equippedItems['backpack']['stats'])
                ? json_decode($equippedItems['backpack']['stats'], true)
                : $equippedItems['backpack']['stats'];
            $backpackCapacity = (int)($bpStats['capacity'] ?? 0);
        }

        $maxWeight = 60 + $effectiveStats['strength'] + $backpackCapacity;
        $currentWeight = $inventoryData['current_weight'];
        $isOverweight = $currentWeight > $maxWeight;

        if ($isOverweight) {
            foreach ($effectiveStats as $key => $val) {
                $effectiveStats[$key] = floor($val * 0.5);
            }
        }

        // 5. Calcul des stats dérivées (Attaque et Défense)
        // Attaque = Force + Modificateur de Dextérité
        $dexMod = floor(($effectiveStats['dexterity'] - 10) / 2);
        $effectiveStats['attack'] = $effectiveStats['strength'] + max(0, $dexMod);
        
        // Défense = 10 + Modificateur de Dextérité
        $effectiveStats['defense'] = 10 + $dexMod;

        // Informations additionnelles
        $effectiveStats['is_overweight'] = $isOverweight;
        $effectiveStats['current_weight'] = $currentWeight;
        $effectiveStats['max_weight'] = $maxWeight;

        // Mise en cache
        self::$cache[$characterId] = $effectiveStats;

        return $effectiveStats;
    }

    /**
     * Invalide le cache pour un personnage
     */
    public static function clearCache($characterId = null)
    {
        if ($characterId === null) {
            self::$cache = [];
        } else {
            unset(self::$cache[$characterId]);
        }
    }

    /**
     * Stats par défaut en cas d'erreur
     */
    private static function getDefaultStats()
    {
        return [
            'strength' => 10,
            'dexterity' => 10,
            'intelligence' => 10,
            'vitality' => 10,
            'attack' => 10,
            'defense' => 10,
            'is_overweight' => false,
            'current_weight' => 0,
            'max_weight' => 70
        ];
    }

    /**
     * Récupère une stat spécifique
     */
    public static function getStat($characterId, $statName, $useCache = true)
    {
        $stats = self::getEffectiveStats($characterId, $useCache);
        return $stats[$statName] ?? 0;
    }
}
