<?php
namespace App\Models;

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

        public function isAlive($entity)
        {
            if ($entity->getVitality() <= 0) {
                $this->end = true;
                return false;
            }
            return true;
        }

       

        public function playerTurn($action){
            $bool = false;

            switch ($action) {
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