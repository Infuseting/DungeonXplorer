<?php
namespace App\Models;

use App\Models\Skill;
use App\Models\Character;
use App\Services\DifficultyService;
use App\Models\Stats;
use App\Models\Database;
use App\Models\Inventory;
use App\Models\CharacterBuff;

if (isset($_POST['diceRoll'])) {
    $_SESSION['diceRoll'] = intval($_POST['diceRoll']);
}




class Combat
    { 
        private  $joueur;
        private $boss;
        private $end = false;

        public function __construct(  $joueur, $boss)
        {
            $this->joueur = $joueur;
            $this->boss = $boss;
        }

  

        public function dice()
        {
            return rand(1, 20);
        }

        public function getPlayerHp() {
            return $this->joueur->getCurrentHp();
        }

        public function getMonster() {
            return $this->boss;
        }

        public function getJoueur() {
            return $this->joueur;
        }

        public function isMonsterAlive() {
            return $this->boss->isAlive();
        }
        
        public function isEnd() { 
            return $this->end; 
        }

        public function isAlive($entity)
        {
            if ($entity->getVitality() <= 0) {
                $this->end = true;
                return false;
            }
            return true;
        }

       

        public function playerTurn($action, $skillId = null){
            $bool = false;

            switch ($action) {
                case 'use_skill':
                    if (!$skillId) { $message = "No skill selected."; break; }
                    $skillModel = new Skill();
                    $skill = $skillModel->findById($skillId);
                    if (!$skill) { $message = "Skill not found."; break; }
                    
                    $message = $this->joueur->getName() . " uses " . $skill['name'] . "!\n";
                    
                    // Logic based on effect_type
                    // Supported: damage_phys_percent, damage_mag_percent, buff_str_flat, heal, passive_*
                    
                    if (strpos($skill['effect_type'], 'damage_') !== false) {
                         // Damage Logic
                         $isPhys = strpos($skill['effect_type'], '_phys') !== false;
                         $multiplier = ($skill['effect_value'] / 100);
                         
                         if ($isPhys) {
                             // Physical: Check Hit
                             $hitRoll = $this->dice() + ($this->joueur->getDexterity() / 2);
                             $defenseScore = 10 + ($this->boss->getDexterity() / 2);
                             if ($hitRoll >= $defenseScore) {
                                 $bool = true;
                                 $baseDmg = $this->joueur->getAttaqueClass();
                                 $dmg = max(1, floor($baseDmg * $multiplier));
                                 $reduction = $this->boss->getDefense() / 2;
                                 $actual = max(1, $dmg - $reduction);
                                 $this->boss->reduceVitality($actual);
                                  $message .= "It hits for $actual damage! (mult: {$multiplier}x)\n";
                             } else {
                                 $message .= "It missed!\n";
                             }
                         } else {
                             // Magic: Auto Hit (usually, or vs Int)
                             $bool = true; // Visual effect
                             // Base Magic Dmg? Using Int?
                             // Let's us Int + Level as base?
                             // Or just Weapon Dmg * Multiplier (Magic weapons)?
                             // Using Int for now: Int * 0.5 + Level
                             $baseDmg = ($this->joueur->getIntelligence()) + 2; 
                             $dmg = max(1, floor($baseDmg * $multiplier));
                             // Magic might ignore Armor? Or use Magic Def?
                             // Assuming ignores Armor for now.
                             $this->boss->reduceVitality($dmg);
                             $message .= "Magic blast hits for $dmg damage!\n";
                         }
                    } elseif (strpos($skill['effect_type'], 'buff_') !== false) {
                        // Buff Logic
                        // e.g. buff_str_flat
                        $parts = explode('_', $skill['effect_type']); // buff, str, flat
                        $stat = $parts[1];
                        $val = (int)$skill['effect_value'];
                        
                        if ($stat === 'str') {
                            $this->joueur->setStrength($this->joueur->getStrength() + $val);
                            $message .= "Strength increased by $val!\n";
                        } elseif ($stat === 'dex') {
                            $this->joueur->setDexterity($this->joueur->getDexterity() + $val);
                            $message .= "Dexterity increased by $val!\n";
                        }
                    } elseif ($skill['effect_type'] === 'heal') {
                        // Heal Logic
                        $healAmount = (int)$skill['effect_value'];
                        // Use Character model heal method (updates DB)
                        $this->joueur->heal($this->joueur->getId(), $healAmount);
                        // Visual update in message. 
                        // Note: local object 'currentHp' might not update automatically if 'heal' only touches DB.
                        // Ideally we update local property too, but it's private.
                        // BUT interfaceCombat.php fetches HP from DB/Session or response?
                        // response uses $combat->getPlayerHp().
                        // We need $combat->joueur to reflect new HP.
                        // I'll add setHp to Character? Or just rely on DB fetch in UI?
                        // CombatController sends 'playerHp' => $combat->getPlayerHp().
                        // Combat::getPlayerHp() calls $this->joueur->getCurrentHp? Or getVitality?
                        // I need to check getPlayerHp logic.
                        $message .= "Healed for $healAmount HP!\n";
                    }
                    break;

                case 'attack':
                    // Hit Chance: Player Dex vs Monster Dex
                    $hitRoll = $this->dice() + ($this->joueur->getDexterity() / 2);
                    $defenseScore = 10 + ($this->boss->getDexterity() / 2); // Base AC 10 + Dex Mod
                    
                    if ($hitRoll >= $defenseScore) {
                        $bool = true;
                        
                        // Damage: Player Str + Weapon Damage (included in getAttaqueClass or separate?)
                        // getAttaqueClass currently seems to be Str + DamageStat? Let's check Character.php
                        // Assuming getAttaqueClass returns total Attack Power (Str + Weapon)
                        $rawDamage = $this->joueur->getAttaqueClass(); 
                        
                        // Defense: Monster Defense Stat
                        $reduction = $this->boss->getDefense() / 2;
                        $actualDamage = max(1, $rawDamage - $reduction); // Min 1 damage
                        
                        // --- Affinity / Weakness Check ---
                        // Default damage type is 'physical' for now
                        $damageType = 'physical'; 
                        
                        // Check modifiers
                        $affinity = $this->boss->getAffinityModifier($damageType);
                        if ($affinity) {
                            $type = $affinity['type'] ?? 'percent'; // percent or flat
                            $value = floatval($affinity['value'] ?? 0);
                            
                            if ($type === 'percent') {
                                // Value is % (e.g. -50 for 50% resistance, +50 for 50% weakness)
                                $modifier = 1 + ($value / 100);
                                $actualDamage *= $modifier;
                                $message .= " (Weakness/Resist: {$value}%)";
                            } else {
                                // Flat value
                                $actualDamage += $value;
                                $message .= " (Weakness/Resist: {$value} flat)";
                            }
                        }
                        
                        // Check Creature Type Bonus? (Not implemented on player items yet)
                        // ...
                        
                        $actualDamage = max(1, floor($actualDamage)); // Ensure integer >= 1
                        
                        $this->boss->reduceVitality($actualDamage);
                        $message = $this->joueur->getName() . " hits " . $this->boss->getName() . " for " . $actualDamage . " damage! (Roll: $hitRoll vs AC: $defenseScore - Reduct: $reduction)\n";
                    } else {
                        $message = $this->joueur->getName() . " misses " . $this->boss->getName() . "! (Roll: $hitRoll vs AC: $defenseScore)\n";
                    }
                    break;

                case 'defend':
                    // Temp bonus to Defense (AC)
                    // We can boost Dex or Defense stat temporarily?
                    // Let's boost Armor Class used for Hit detection or Damage reduction?
                    // User asked for "Defense (Reduction des dégâts)"
                    // "Agilité (Chance de toucher, Esquive)"
                    // So Defend should boost Reduction (Defense) or Evasion (Agility)?
                    // "Defend" usually implies blocking/parrying -> Mitigation or Evasion.
                    // Let's boost Evasion (Dex) + Defense for this turn.
                    
                    $_SESSION['temp_defense_bonus'] = $_SESSION['diceRoll']; // Use dice roll as bonus
                    
                    $message = $this->joueur->getName() . " takes a defensive stance!\n";
                    break;
                    
                case 'usePotion':
                    // Potion logic (restore HP)
                    // Use max HP session var
                    $restore = 20; // Default potion?
                    $maxHp = $_SESSION['maxHpPlayer'];
                    $current = $this->joueur->getVitality(); // Actually currentHp via getter
                    $newHp = min($maxHp, $current + $restore);
                    
                    // We need a setCurrentHp on Character? 
                    // Character::heal() adds to current_hp.
                    $this->joueur->heal($_SESSION['character_id'], $restore); // Use DB method
                    
                    // Sync local cache if possible or trust DB for next fetch?
                    // Combat loop might keep object in memory.
                    // We should probably update the local object too if methods allow.
                    // Character::heal updates DB.
                    // Character model currently uses 'vitality' property for max HP in getVitality()? 
                    // No, getVitality returns the Stat.
                    // Combat logic relies on getVitality() returning HP?
                    // In Character.php check: getVitality() returns stat. 
                    // This is a disconnect. 'isAlive' checks 'currentHp' or 'vitality'.
                    // Fixing this: Combat should use 'getCurrentHp' and 'getMaxHp'.
                    
                    $message = $this->joueur->getName() . " drinks a potion (+20 HP)!\n";
                    break;

                default:
                    $message = $this->joueur->getName() . " does nothing...\n";
                    break;
            }

            if(!$this->boss->isAlive()) {
                $message .= $this->boss->getName() . " is defeated!\n";
                $this->endCombat();
            }

            return [$message, $bool];
        }

        public function monsterTurn()
        {
            $bool = false;
            $message = "";

            // Check if player is already dead before acting
            if (!$this->joueur->isAlive()) {
                 $message = $this->joueur->getName() . " is already down! " . $this->boss->getName() . " looms over...";
                 $this->endCombat();
                 return [$message, false];
            }

            if($this->boss->isAlive()) {
                // Calculation
                // Hit Chance: Monster Dex vs Player Dex
                $hitRoll = $this->dice() + ($this->boss->getDexterity() / 2);
                
                // Player evasion
                $playerDex = $this->joueur->getDexterity();
                // Check if Defend action active? (Bonus to AC?)
                 $bonusEvasion = 0;
                 // If we had a dodge action...
                
                $defenseScore = 10 + ($playerDex / 2) + $bonusEvasion;
                
                if($hitRoll >= $defenseScore) {
                    $bool = true;
                    
                    // Damage: Monster Str/Attack
                    $rawDamage = $this->boss->getAttaqueClass(); // Assuming this is Attack Power
                    
                    // Mitigation: Player Defense
                    $playerDef = $this->joueur->getArmorClass(); // getArmorClass in Character uses Str/2 + Equipped Defense?
                    // Let's stick to using stats directly if possible or the helper methods.
                    // Character::getArmorClass() = Str/2 + EquippedDefense. 
                    // This is effectively "Defense".
                    
                    $reduction = $playerDef / 2;
                    
                    // Defend Action Bonus?
                    if (isset($_SESSION['temp_defense_bonus'])) {
                        $reduction += $_SESSION['temp_defense_bonus'];
                        unset($_SESSION['temp_defense_bonus']);
                    }

                    $actualDamage = max(0, $rawDamage - $reduction);

                    // Difficulty Modifier (Incoming Damage)
                    if (isset($_SESSION['current_difficulty'])) {
                        $diffService = new DifficultyService(); 
                        // Note: Unserializing service from session might be better if strictly needed, 
                        // but instantiating new one is cheap here.
                        $dmgMod = $diffService->getDamageModifier($_SESSION['current_difficulty']);
                        $actualDamage = floor($actualDamage * $dmgMod);
                        // Ensure at least 1 dmg if raw was high enough to penetrate armor
                        if (($rawDamage - $reduction) > 0) $actualDamage = max(1, $actualDamage);
                    }
                    
                    $this->joueur->reduceVitality($actualDamage);
                    $message = $this->boss->getName() . " hits " . $this->joueur->getName() . " for " . $actualDamage . " damage! (Roll: $hitRoll vs AC: $defenseScore)\n";
                } else {
                    $message =  $this->boss->getName() . " misses " . $this->joueur->getName() . "! (Roll: $hitRoll vs AC: $defenseScore)\n";
                    // If defend was active, clear it
                    if (isset($_SESSION['temp_defense_bonus'])) unset($_SESSION['temp_defense_bonus']);
                }

                if(!$this->joueur->isAlive()) {
                    $message .= $this->boss->getName() . " wins the combat!\n";
                    $this->endCombat();
                }
                
            } else {
                $message = $this->boss->getName()." is already dead.";
            }
            
            return [$message,$bool];
        }

        public function endCombat()
        {
            $this->end = true;
        }

       

        public function getActions() {
        return ['attack', 'usePotion', 'defend', 'run'];
    }


    }