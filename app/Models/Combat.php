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
                    
                                                            
                    if (strpos($skill['effect_type'], 'damage_') !== false) {
                                                  $isPhys = strpos($skill['effect_type'], '_phys') !== false;
                         $multiplier = ($skill['effect_value'] / 100);
                         
                         if ($isPhys) {
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
                                                          $bool = true;                                                                                                                                                  $baseDmg = ($this->joueur->getIntelligence()) + 2; 
                             $dmg = max(1, floor($baseDmg * $multiplier));
                                                                                       $this->boss->reduceVitality($dmg);
                             $message .= "Magic blast hits for $dmg damage!\n";
                         }
                    } elseif (strpos($skill['effect_type'], 'buff_') !== false) {
                                                                        $parts = explode('_', $skill['effect_type']);                         $stat = $parts[1];
                        $val = (int)$skill['effect_value'];
                        
                        if ($stat === 'str') {
                            $this->joueur->setStrength($this->joueur->getStrength() + $val);
                            $message .= "Strength increased by $val!\n";
                        } elseif ($stat === 'dex') {
                            $this->joueur->setDexterity($this->joueur->getDexterity() + $val);
                            $message .= "Dexterity increased by $val!\n";
                        }
                    } elseif ($skill['effect_type'] === 'heal') {
                                                $healAmount = (int)$skill['effect_value'];
                                                $this->joueur->heal($this->joueur->getId(), $healAmount);
                                                                                                                                                                                                                                                                        $message .= "Healed for $healAmount HP!\n";
                    }
                    break;

                case 'attack':
                                        $hitRoll = $this->dice() + ($this->joueur->getDexterity() / 2);
                    $defenseScore = 10 + ($this->boss->getDexterity() / 2);                     
                    if ($hitRoll >= $defenseScore) {
                        $bool = true;
                        
                                                                                                $rawDamage = $this->joueur->getAttaqueClass(); 
                        
                                                $reduction = $this->boss->getDefense() / 2;
                        $actualDamage = max(1, $rawDamage - $reduction);                         
                                                                        $damageType = 'physical'; 
                        
                                                $affinity = $this->boss->getAffinityModifier($damageType);
                        if ($affinity) {
                            $type = $affinity['type'] ?? 'percent';                             $value = floatval($affinity['value'] ?? 0);
                            
                            if ($type === 'percent') {
                                                                $modifier = 1 + ($value / 100);
                                $actualDamage *= $modifier;
                                $message .= " (Weakness/Resist: {$value}%)";
                            } else {
                                                                $actualDamage += $value;
                                $message .= " (Weakness/Resist: {$value} flat)";
                            }
                        }
                        
                                                                        
                        $actualDamage = max(1, floor($actualDamage));                         
                        $this->boss->reduceVitality($actualDamage);
                        $message = $this->joueur->getName() . " hits " . $this->boss->getName() . " for " . $actualDamage . " damage! (Roll: $hitRoll vs AC: $defenseScore - Reduct: $reduction)\n";
                    } else {
                        $message = $this->joueur->getName() . " misses " . $this->boss->getName() . "! (Roll: $hitRoll vs AC: $defenseScore)\n";
                    }
                    break;

                case 'defend':
                                                                                                                                                                                    
                    $_SESSION['temp_defense_bonus'] = $_SESSION['diceRoll'];                     
                    $message = $this->joueur->getName() . " takes a defensive stance!\n";
                    break;
                    
                case 'usePotion':
                                                            $restore = 20;                     $maxHp = $_SESSION['maxHpPlayer'];
                    $current = $this->joueur->getVitality();                     $newHp = min($maxHp, $current + $restore);
                    
                                                            $this->joueur->heal($_SESSION['character_id'], $restore);                     
                                                                                                                                                                                                                            
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

                        if (!$this->joueur->isAlive()) {
                 $message = $this->joueur->getName() . " is already down! " . $this->boss->getName() . " looms over...";
                 $this->endCombat();
                 return [$message, false];
            }

            if($this->boss->isAlive()) {
                                                $hitRoll = $this->dice() + ($this->boss->getDexterity() / 2);
                
                                $playerDex = $this->joueur->getDexterity();
                                 $bonusEvasion = 0;
                                 
                $defenseScore = 10 + ($playerDex / 2) + $bonusEvasion;
                
                if($hitRoll >= $defenseScore) {
                    $bool = true;
                    
                                        $rawDamage = $this->boss->getAttaqueClass();                     
                                        $playerDef = $this->joueur->getArmorClass();                                                                                 
                    $reduction = $playerDef / 2;
                    
                                        if (isset($_SESSION['temp_defense_bonus'])) {
                        $reduction += $_SESSION['temp_defense_bonus'];
                        unset($_SESSION['temp_defense_bonus']);
                    }

                    $actualDamage = max(0, $rawDamage - $reduction);

                                        if (isset($_SESSION['current_difficulty'])) {
                        $diffService = new DifficultyService(); 
                                                                        $dmgMod = $diffService->getDamageModifier($_SESSION['current_difficulty']);
                        $actualDamage = floor($actualDamage * $dmgMod);
                                                if (($rawDamage - $reduction) > 0) $actualDamage = max(1, $actualDamage);
                    }
                    
                    $this->joueur->reduceVitality($actualDamage);
                    $message = $this->boss->getName() . " hits " . $this->joueur->getName() . " for " . $actualDamage . " damage! (Roll: $hitRoll vs AC: $defenseScore)\n";
                } else {
                    $message =  $this->boss->getName() . " misses " . $this->joueur->getName() . "! (Roll: $hitRoll vs AC: $defenseScore)\n";
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