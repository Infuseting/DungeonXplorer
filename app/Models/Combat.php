<?php
namespace App\Models;

use App\Models\Skill;
use App\Models\Character;
use App\Services\DifficultyService;
use App\Models\Stats;
use App\Config\Database;
use App\Models\Inventory;
use App\Models\CharacterBuff;

if (isset($_POST['diceRoll'])) {
    $_SESSION['diceRoll'] = intval($_POST['diceRoll']);
}

class Combat
{
    private $joueur;
    private $boss;
    private $end = false;

    public function __construct($joueur, $boss)
    {
        $this->joueur = $joueur;
        $this->boss = $boss;
    }

    public function dice()
    {
        return rand(1, 20);
    }

    public function getPlayerHp()
    {
        return $this->joueur->getCurrentHp();
    }

    public function getMonster()
    {
        return $this->boss;
    }

    public function getJoueur()
    {
        return $this->joueur;
    }

    public function isMonsterAlive()
    {
        return $this->boss->isAlive();
    }

    public function isEnd()
    {
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

    public function playerTurn($action, $skillId = null)
    {
        $bool = false;

        switch ($action) {
            case 'use_skill':
                if (!$skillId) {
                    $message = "Aucune compétence sélectionnée.";
                    break;
                }
                $skillModel = new Skill();
                $skill = $skillModel->findById($skillId);
                if (!$skill) {
                    $message = "Compétence introuvable.";
                    break;
                }

                $message = $this->joueur->getName() . " utilise <span class='text-purple-400 font-bold'>" . $skill['name'] . "</span> !\n";

                // Compétences de dégâts
                if (strpos($skill['effect_type'], 'damage_') !== false) {
                    $isPhys = strpos($skill['effect_type'], '_phys') !== false;
                    $multiplier = ($skill['effect_value'] / 100);

                    if ($isPhys) {
                        // Attaque physique avec jet d'attaque
                        $diceRoll = $this->dice();
                        $dexMod = floor(($this->joueur->getDexterity() - 10) / 2);
                        $hitRoll = $diceRoll + $dexMod;
                        $defenseScore = 10 + floor(($this->boss->getDexterity() - 10) / 2);

                        if ($hitRoll >= $defenseScore) {
                            $bool = true;
                            $baseDmg = $this->joueur->getAttaqueClass();
                            $dmg = floor($baseDmg * $multiplier);

                            // Réduction par défense (max 80%)
                            $defenseValue = $this->boss->getDefense();
                            $reduction = min($dmg * 0.8, $defenseValue);
                            $actual = max(1, $dmg - $reduction);

                            $this->boss->reduceVitality($actual);
                            $message .= "Inflige <span class='text-red-500 font-bold'>$actual dégâts</span> ! <span class='text-gray-400 text-sm'>(mult: {$multiplier}x)</span>\n";
                        } else {
                            $message .= "<span class='text-gray-400'>Raté !</span>\n";
                        }
                    } else {
                        // Attaque magique : touche toujours, défense réduite
                        $bool = true;
                        $baseDmg = $this->joueur->getIntelligence() * 2;
                        $dmg = floor($baseDmg * $multiplier);

                        // Les sorts magiques ignorent 50% de la défense
                        $defenseValue = $this->boss->getDefense();
                        $reduction = min($dmg * 0.8, $defenseValue * 0.5);
                        $actual = max(1, $dmg - $reduction);

                        $this->boss->reduceVitality($actual);
                        $message .= "Explosion magique : <span class='text-blue-500 font-bold'>$actual dégâts</span> !\n";
                    }
                } elseif (strpos($skill['effect_type'], 'buff_') !== false) {
                    // Buffs de statistiques
                    $parts = explode('_', $skill['effect_type']);
                    $stat = $parts[1];
                    $val = (int) $skill['effect_value'];

                    if (!isset($_SESSION['combat_buffs'])) {
                        $_SESSION['combat_buffs'] = ['str' => 0, 'dex' => 0];
                    }

                    if ($stat === 'str') {
                        $_SESSION['combat_buffs']['str'] += $val;
                        $message .= "<span class='text-orange-400'>Force augmentée de $val !</span>\n";
                    } elseif ($stat === 'dex') {
                        $_SESSION['combat_buffs']['dex'] += $val;
                        $message .= "<span class='text-green-400'>Dextérité augmentée de $val !</span>\n";
                    }
                } elseif ($skill['effect_type'] === 'heal') {
                    $healAmount = (int) $skill['effect_value'];
                    $this->joueur->heal($this->joueur->getId(), $healAmount);
                    $message .= "<span class='text-green-400'>Soigné de $healAmount PV !</span>\n";
                }
                break;

            case 'attack':
                // Jet d'attaque : 1d20 + Mod DEX
                $diceRoll = $this->dice();
                $dexMod = floor(($this->joueur->getDexterity() - 10) / 2);
                $hitRoll = $diceRoll + $dexMod;
                $defenseScore = 10 + floor(($this->boss->getDexterity() - 10) / 2);

                if ($hitRoll >= $defenseScore) {
                    $bool = true;

                    // Vérification du coup critique (20 naturel ou selon difficulté)
                    $isCritical = false;
                    $critMessage = '';

                    if (isset($_SESSION['current_difficulty'])) {
                        $critThreshold = 20; // Base: seulement 20 naturel

                        if ($_SESSION['current_difficulty'] === 'STORY') {
                            $critThreshold = 18; // 18-20 en mode Story
                        }

                        if ($diceRoll >= $critThreshold) {
                            $isCritical = true;
                            $critMessage = " <span class='text-yellow-400 font-bold'>💥 COUP CRITIQUE !</span>";
                        }
                    } else {
                        $isCritical = ($diceRoll === 20);
                        if ($isCritical)
                            $critMessage = " <span class='text-yellow-400 font-bold'>💥 COUP CRITIQUE !</span>";
                    }

                    // Calcul des dégâts de base : Force + Dégâts d'équipement
                    $baseDamage = $this->joueur->getAttaqueClass();

                    // Coups critiques : dégâts x2
                    if ($isCritical) {
                        $baseDamage *= 2;
                    }

                    // Réduction par défense : 1 point de défense = 1 point de réduction (max 80%)
                    $defenseValue = $this->boss->getDefense();
                    $reduction = min($baseDamage * 0.8, $defenseValue);
                    $actualDamage = max(1, $baseDamage - $reduction);

                    // Type de dégâts (pour affinités)
                    $damageType = 'physical';

                    // Application des affinités (résistances/faiblesses)
                    $affinity = $this->boss->getAffinityModifier($damageType);
                    $affinityMessage = '';
                    if ($affinity) {
                        $type = $affinity['type'] ?? 'percent';
                        $value = floatval($affinity['value'] ?? 0);

                        if ($type === 'percent') {
                            $modifier = 1 + ($value / 100);
                            $actualDamage *= $modifier;
                            $affinityMessage = $value > 0 ? " <span class='text-red-400'>(Faiblesse: +{$value}%)</span>" : " <span class='text-blue-400'>(Résistance: {$value}%)</span>";
                        } else {
                            $actualDamage += $value;
                            $affinityMessage = $value > 0 ? " (Faiblesse: +{$value})" : " (Résistance: {$value})";
                        }
                    }

                    $actualDamage = max(1, floor($actualDamage));
                    $this->boss->reduceVitality($actualDamage);
                    $message = $this->joueur->getName() . " frappe " . $this->boss->getName() . " pour <span class='text-red-500 font-bold'>" . $actualDamage . " dégâts</span>!" . $critMessage . $affinityMessage . " <span class='text-gray-400 text-sm'>(Jet: $hitRoll vs CA: $defenseScore)</span>\n";
                } else {
                    $message = $this->joueur->getName() . " <span class='text-gray-400'>rate</span> " . $this->boss->getName() . "! <span class='text-gray-400 text-sm'>(Jet: $hitRoll vs CA: $defenseScore)</span>\n";
                }
                break;

            case 'defend':
                // Posture défensive : bonus de +5 CA et réduction de 50% des dégâts ce tour
                $defenseBonus = 5 + floor($this->joueur->getDexterity() / 10);
                $_SESSION['temp_defense_bonus'] = $defenseBonus;
                $_SESSION['damage_reduction'] = 0.5; // Réduction de 50% des dégâts
                $message = $this->joueur->getName() . " <span class='text-blue-400 font-bold'>adopte une posture défensive</span> ! <span class='text-gray-400'>(+{$defenseBonus} CA, -50% dégâts reçus)</span>\n";
                break;

            case 'usePotion':
                $restore = 20;
                $maxHp = $_SESSION['maxHpPlayer'];
                $current = $this->joueur->getCurrentHp();
                $newHp = min($maxHp, $current + $restore);

                // Mettre à jour les HP en base de données
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE character_stats SET current_hp = LEAST(vitality, current_hp + ?) WHERE character_id = ?");
                $stmt->bind_param("ii", $restore, $_SESSION['character_id']);
                $stmt->execute();

                // Mettre à jour l'objet en mémoire (utiliser current_hp avec underscore)
                $this->joueur->current_hp = $newHp;

                // Consommer la potion de l'inventaire
                $inventoryModel = new Inventory();
                $inventory = $inventoryModel->getCharacterInventory($_SESSION['character_id']);

                // Trouver la première potion disponible
                foreach ($inventory['inventory'] as $item) {
                    if ($item['type'] === 'consumable' && stripos($item['name'], 'potion') !== false) {
                        $inventoryModel->consumeItem($_SESSION['character_id'], $item['id']);
                        break;
                    }
                }

                $message = $this->joueur->getName() . " <span class='text-yellow-400'>boit une potion</span> (+20 PV) !\n";
                break;

            default:
                $message = $this->joueur->getName() . " ne fait rien...\n";
                break;
        }

        if (!$this->boss->isAlive()) {
            $message .= "<span class='text-green-400 font-bold'>✓ " . $this->boss->getName() . " est vaincu !</span>\n";
        }

        return [$message, $bool];
    }

    public function monsterTurn()
    {
        $bool = false;
        $message = "";

        // Vérification si le joueur est toujours vivant
        if (!$this->joueur->isAlive()) {
            $message = $this->joueur->getName() . " <span class='text-red-400'>est déjà au sol !</span> " . $this->boss->getName() . " domine le combat...";
            $this->endCombat();
            return [$message, false];
        }

        if ($this->boss->isAlive()) {
            // Jet d'attaque du monstre : 1d20 + Mod DEX
            $diceRoll = $this->dice();
            $dexMod = floor(($this->boss->getDexterity() - 10) / 2);
            $hitRoll = $diceRoll + $dexMod;

            // Classe d'armure du joueur
            $playerDexMod = floor(($this->joueur->getDexterity() - 10) / 2);
            $defenseScore = 10 + $playerDexMod;

            // Bonus de défense temporaire (action Défendre)
            if (isset($_SESSION['temp_defense_bonus'])) {
                $defenseScore += $_SESSION['temp_defense_bonus'];
            }

            if ($hitRoll >= $defenseScore) {
                $bool = true;

                // Dégâts de base du monstre
                $baseDamage = $this->boss->getAttaqueClass();

                // Réduction par armure : 1 point d'armure = 1 point de réduction (max 80%)
                $armorValue = $this->joueur->getArmorClass();
                $reduction = min($baseDamage * 0.8, $armorValue);
                $actualDamage = max(1, $baseDamage - $reduction);

                // Réduction de dégâts supplémentaire (action Défendre)
                if (isset($_SESSION['damage_reduction'])) {
                    $actualDamage = floor($actualDamage * (1 - $_SESSION['damage_reduction']));
                    $actualDamage = max(1, $actualDamage);
                }

                // Modificateur de difficulté (dégâts ennemis)
                if (isset($_SESSION['current_difficulty'])) {
                    $diffService = new DifficultyService();
                    $dmgMod = $diffService->getDamageModifier($_SESSION['current_difficulty']);
                    $actualDamage = floor($actualDamage * $dmgMod);
                    $actualDamage = max(1, $actualDamage);
                }

                $defenseMessage = '';
                if (isset($_SESSION['temp_defense_bonus']) || isset($_SESSION['damage_reduction'])) {
                    $defenseMessage = " <span class='text-blue-400'>(Défense active !)</span>";
                }

                $this->joueur->reduceVitality($actualDamage);
                $message = $this->boss->getName() . " frappe " . $this->joueur->getName() . " pour <span class='text-orange-500 font-bold'>" . $actualDamage . " dégâts</span>!" . $defenseMessage . " <span class='text-gray-400 text-sm'>(Jet: $hitRoll vs CA: $defenseScore)</span>\n";
            } else {
                $message = $this->boss->getName() . " <span class='text-gray-400'>rate</span> " . $this->joueur->getName() . "! <span class='text-gray-400 text-sm'>(Jet: $hitRoll vs CA: $defenseScore)</span>\n";
            }

            // Nettoyer les bonus de défense après l'attaque
            if (isset($_SESSION['temp_defense_bonus']))
                unset($_SESSION['temp_defense_bonus']);
            if (isset($_SESSION['damage_reduction']))
                unset($_SESSION['damage_reduction']);

            if (!$this->joueur->isAlive()) {
                $message .= "<span class='text-red-400 font-bold'>☠ " . $this->boss->getName() . " remporte le combat !</span>\n";
                $this->endCombat();
            }

        } else {
            $message = $this->boss->getName() . " <span class='text-gray-400'>est déjà mort.</span>";
        }

        return [$message, $bool];
    }

    public function endCombat()
    {
        $this->end = true;
    }

    public function getActions()
    {
        return ['attack', 'usePotion', 'defend'];
    }
}
