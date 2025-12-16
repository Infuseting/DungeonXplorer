<?php
namespace App\Services;

class DifficultyService
{
    const STORY = 'STORY';
    const NORMAL = 'NORMAL';
    const HEROIC = 'HEROIC';
    const IRONMAN = 'IRONMAN';
    
    /**
     * Get Damage Modifier for Incoming Damage (from Enemies)
     * 
     * Story: -30% (0.7)
     * Normal: 0% (1.0)
     * Heroic: +20% (1.2)
     * IronMan: +35% (1.35)
     */
    public function getDamageModifier(string $difficulty): float
    {
        switch ($difficulty) {
            case self::STORY:
                return 0.70;
            case self::HEROIC:
                return 1.20;
            case self::IRONMAN:
                return 1.35;
            case self::NORMAL:
            default:
                return 1.0;
        }
    }
    
    /**
     * Get XP Modifier
     * 
     * Story: +25% (1.25)
     * Normal: 0% (1.0)
     * Heroic: 0% (1.0)
     * IronMan: 0% (1.0) - Although IronMan implies harder challenge, XP is usually standard or slightly boosted in some games, 
     * but requirement says "Combat: Stats ennemis et gains d'XP/Butin Standard (100%)" for Classic.
     * Story: "Récompenses d'XP augmentées de 25%"
     */
    public function getXpModifier(string $difficulty): float
    {
        switch ($difficulty) {
            case self::STORY:
                return 1.25;
            case self::NORMAL:
            case self::HEROIC:
            case self::IRONMAN:
            default:
                return 1.0;
        }
    }
    
    /**
     * Get Merchant Price Modifier (Markup)
     * 
     * Story: 1.0
     * Normal: 1.0
     * Heroic: +15% (1.15)
     * IronMan: +15% (1.15) - Often IronMan shares Heroic stats + Permadeath
     */
    public function getPriceModifier(string $difficulty): float
    {
        switch ($difficulty) {
            case self::HEROIC:
            case self::IRONMAN:
                return 1.15;
            case self::STORY:
            case self::NORMAL:
            default:
                return 1.0;
        }
    }
    
    /**
     * Get Loot Chance Modifier
     * 
     * Story: 1.0
     * Normal: 1.0
     * Heroic: 1.0
     * IronMan: "Butin réduit" -> Let's say -20% chance (0.8)
     */
    public function getLootChanceModifier(string $difficulty): float
    {
        switch ($difficulty) {
            case self::IRONMAN:
                return 0.8;
            case self::STORY:
            case self::NORMAL:
            case self::HEROIC:
            default:
                return 1.0;
        }
    }
    
    /**
     * Get Crit Chance Modifier (Player)
     * 
     * Story: "Taux de chance de critique augmenté" -> Let's add flat +10% or multiplier
     * We'll use a multiplier for the base chance.
     */
    public function getCritChanceModifier(string $difficulty): float
    {
        switch ($difficulty) {
            case self::STORY:
                return 1.5; // 50% more chance to crit
            default:
                return 1.0;
        }
    }
    
    public function isPermadeath(string $difficulty, bool $isIronman): bool
    {
        if ($difficulty === self::IRONMAN) {
            return true;
        }
        return $isIronman;
    }
}
