<?php

namespace App\Services;

use App\Models\CharacterBuff;
use App\Models\Character;

class StatusEffectService
{
    private $buffModel;
    private $characterModel;

    public function __construct()
    {
        $this->buffModel = new CharacterBuff();
        $this->characterModel = new Character();
    }

    /**
     * Applique un effet de statut
     */
    public function applyEffect($characterId, $name, $duration, $modifiers = [], $durationType = 'turns')
    {
        // Prevent duplicate effects if needed, or stack?
        // Simple logic: Add new buff.
        return $this->buffModel->create($characterId, $name, $modifiers, $durationType, $duration);
    }

    /**
     * Traite les effets au début/fin de tour
     * Retourne un tableau de messages (ex: dégâts de poison)
     */
    public function processTurn($characterId)
    {
        $buffs = $this->buffModel->getActiveBuffs($characterId);
        $messages = [];
        $preventAction = false;

        foreach ($buffs as $buff) {
            $name = $buff['name'];
            $modifiers = $buff['stat_modifiers'];

            // Handle DOTs (Damage Over Time)
            if (isset($modifiers['dot_damage'])) {
                $dmg = $modifiers['dot_damage'];
                // Apply damage directly to character
                // We need to instantiate Character to use methods
                $this->characterModel->findById($characterId);
                $this->characterModel->reduceVitality($dmg);
                $messages[] = "Vous subissez $dmg dégâts de $name !";
            }

            // Handle Stun
            if (isset($modifiers['stun']) && $modifiers['stun'] === true) {
                $preventAction = true;
                $messages[] = "Vous êtes étourdi par $name et ne pouvez pas agir !";
            }

            // Decrease duration
            $this->buffModel->decreaseDuration($buff['id']);
            
            // Check expiry
            if ($buff['duration_remaining'] - 1 <= 0) {
                $messages[] = "L'effet $name s'est dissipé.";
                $this->buffModel->remove($buff['id']);
            }
        }

        return [
            'messages' => $messages,
            'prevent_action' => $preventAction
        ];
    }

    /**
     * Get aggregated stat modifiers (e.g. for calculating Haste bonus)
     */
    public function getEffectiveStatModifiers($characterId)
    {
        $buffs = $this->buffModel->getActiveBuffs($characterId);
        $mods = [
            'strength' => 0,
            'dexterity' => 0,
            'intelligence' => 0,
            'vitality' => 0,
            'armor' => 0
        ];

        foreach ($buffs as $buff) {
            $m = $buff['stat_modifiers'];
            foreach ($mods as $stat => $val) {
                if (isset($m[$stat])) {
                    $mods[$stat] += $m[$stat];
                }
            }
        }
        return $mods;
    }
}
