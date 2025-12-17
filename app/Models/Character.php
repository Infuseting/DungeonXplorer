<?php

namespace App\Models;

use App\Config\Database;
use App\Models\Inventory;
use App\Models\Stats as StatsEnum; 

#[\AllowDynamicProperties]
class Character
{
    public function getId() {
        return $this->id;
    }
    public function getEquippedStats(StatsEnum $stat): int
    {
        $inventoryModel = new Inventory();
        $inv = $inventoryModel->getCharacterInventory($this->id);
        
        $total = 0;
        $statKey = $stat->value; // e.g., 'damage', 'defense'

        if (!empty($inv['equipped'])) {
            foreach ($inv['equipped'] as $item) {
                $stats = json_decode($item['stats'] ?? '[]', true);
                if (isset($stats[$statKey])) {
                    $total += (int)$stats[$statKey];
                }
            }
        }
        
        return $total;
    }

    // Removed duplicate isAlive() here - use the one checking currentHp

    public function getAttaqueClass()
    {
        return  $this->getStrength() + $this->getEquippedStats(StatsEnum::Damage);
    }
    public function resetCache(): void
    {
        $this->inventoryCache = [];
        $this->statsCache = [];
    }

    private array $inventoryCache = [];
    private array $statsCache = [];


    public function __construct()
    {   
        $this->db = Database::getInstance()->getConnection();
        $this->inventory = new Inventory();
    }

    public function __wakeup()
    {
        $this->db = Database::getInstance()->getConnection();
        if (!$this->inventory) {
             $this->inventory = new Inventory();
        }
    }

    public function create($userId, $classId, $name, $difficulty = 'NORMAL', $isIronman = 0)
    {
        $stmt = $this->db->prepare("INSERT INTO characters (user_id, class_id, name, difficulty, is_ironman) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $userId, $classId, $name, $difficulty, $isIronman);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function findAllByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT c.*, cl.name as class_name, cs.level 
                                    FROM characters c 
                                    JOIN classes cl ON c.class_id = cl.id 
                                    JOIN character_stats cs ON c.id = cs.character_id 
                                    WHERE c.user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $characters = [];
        while ($row = $result->fetch_assoc()) {
            $characters[] = $row;
        }
        return $characters;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT c.id, c.user_id, cl.name as class_name,c.name,c.appearance, c.class_id, c.gold, c.difficulty, c.is_ironman, cs.level, cs.xp, cs.strength,cs.dexterity,cs.intelligence,cs.vitality, cs.current_hp, cs.skill_points FROM characters c  Join character_stats cs on c.id=cs.character_id join classes cl on cl.id = c.class_id WHERE c.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $data= $stmt->get_result()->fetch_assoc();
        
        if ($data) {
            $this->id = $data['id'];
            $this->userId = $data['user_id'];
            $this->name = $data['name'];
            $this->classId = $data['class_id'];
            $this->className = $data['class_name'];
            $this->gold = $data['gold'];
            $this->level = $data['level'];
            $this->xp = $data['xp'];
            $this->skillPoints = $data['skill_points'] ?? 0;
            $this->strength = $data['strength'];
            $this->vitality = $data['vitality'];
            $this->intelligence = $data['intelligence'];
            $this->dexterity = $data['dexterity'];
            $this->appearance = json_decode($data['appearance'] ?? '[]', true);
            $this->difficulty = $data['difficulty'] ?? 'NORMAL';
            $this->isIronman = $data['is_ironman'] ?? 0;
            $this->currentHp = $data['current_hp'] ?? $data['vitality']; // Default to vitality if null

            // Load Effective Stats (Base + Equipment + Skills)
            // This ensures combat uses the boosting values
            $statsModel = new CharacterStats();
            $effective = $statsModel->getEffectiveStats($this->id);
            if ($effective && isset($effective['stats'])) {
                $this->strength = $effective['stats']['strength'];
                $this->dexterity = $effective['stats']['dexterity'];
                $this->intelligence = $effective['stats']['intelligence'];
                $this->vitality = $effective['stats']['vitality'];
            }

            return $this;
        }
        return null; // Return null if not found
    }
    public function getSkillPoints() {
        return $this->skillPoints;
    }
    public function getLevel() {
        return $this->level;
    }
    public function unlockSkill($skillId) {
        $stmt = $this->db->prepare("INSERT INTO character_skills (character_id, skill_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $this->id, $skillId);
        return $stmt->execute();
    }



    public function unsetDb() {
        $this->db = null;
    }

    public function toArray(): array {
        return [
            'id'        => $this->id ?? 0,
            'name'      => $this->name ?? '',
            'class_name'=> $this->className ?? '',
            'appearance'=> $this->appearance ?? [],
            'class'     => ['name' => $this->className ?? ''],
        ];
    }   



    public function updateLastPlayed($id)
    {
        $stmt = $this->db->prepare("UPDATE characters SET last_played_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        $_SESSION['character_id'] = $id;
        return $stmt->execute();
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM characters WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        return $stmt->execute();
    }

     public function getInventory(): array
    {
        // Si déjà chargé, on renvoie directement
        if (!empty($this->inventoryCache)) {
            echo 'empty';
            return $this->inventoryCache;
        }

            

            $result = $this->inventory->getCharacterInventory($this->id);

            return is_array($result) ? $result : [];

        

        
    }


    public function toString()
    {
        return "\nName: " . $this->name . "\n" .
               "Level: " . $this->level . "\n" .
               "XP: " . $this->xp . "\n" .
               "Gold: " . $this->gold . "\n" .
               "Strength: " . $this->strength . "\n" .
               "Vitality: " . $this->vitality . "\n" .
               "Intelligence: " . $this->intelligence . "\n" .
               "Dexterity: " . $this->dexterity . "\n";
    }

    

    public function heal($characterId, $amount)
    {
        $stmt = $this->db->prepare("UPDATE character_stats SET current_hp = LEAST(vitality, current_hp + ?) WHERE character_id = ?");
        $stmt->bind_param("ii", $amount, $characterId);
        return $stmt->execute();
    }

    public function addGold($amount)
    {
        $stmt = $this->db->prepare("UPDATE characters SET gold = gold + ? WHERE id = ?");
        $stmt->bind_param("ii", $amount, $this->id);
        if ($stmt->execute()) {
            $this->gold += $amount;
            return true;
        }
        return false;
    }

    public function addXp($amount)
    {
        // 1. Fetch current stats fresh to be safe
        $stmt = $this->db->prepare("SELECT xp, level, skill_points, vitality FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        
        $xp = $stats['xp'] + $amount;
        $level = $stats['level'];
        $sp = $stats['skill_points'];
        $levelsGained = 0;
        
        // Threshold Formula: Level * 100
        $threshold = $level * 100;
        
        while ($xp >= $threshold) {
            $xp -= $threshold;
            $level++;
            $levelsGained++;
            $sp++; // 1 SP per level
            $threshold = $level * 100;
        }
        
        // Update DB
        $stmt = $this->db->prepare("UPDATE character_stats SET xp = ?, level = ?, skill_points = ? WHERE character_id = ?");
        $stmt->bind_param("iiii", $xp, $level, $sp, $this->id);
        $success = $stmt->execute();
        
        if ($success && $levelsGained > 0) {
            // Restore HP on level up? Optional, but nice.
            // Let's refill HP.
             $this->db->query("UPDATE character_stats SET current_hp = vitality WHERE character_id = " . $this->id);
        }
        
        // Update local
        $this->xp = $xp;
        $this->level = $level;
        $this->skillPoints = $sp;
        
        return [
            'success' => $success,
            'levels_gained' => $levelsGained,
            'current_level' => $level,
            'current_xp' => $xp,
            'next_threshold' => $threshold
        ];
    }

    public function getName()
    {
        return $this->name;
    } 
    public function getCurrentHp() 
    {
        return $this->currentHp ?? $this->vitality;
    }  

    public function getDifficulty()
    {
        return $this->difficulty ?? 'NORMAL';
    }

    public function isIronman()
    {
        return (bool)$this->isIronman;
    }
    public function getStrength()
    {
        return $this->strength;
    }
    public function getVitality()
    {
        return $this->vitality;
    }
    public function getIntelligence()
    {
        return $this->intelligence;
    }

    public function setArmorClass($nArmor){
        $this->armor = $nArmor;

    }
    public function getDexterity()
    {
        return $this->dexterity;
    }
    public function setVitality($vitality)
    {
        $this->vitality = $vitality;
    }

    public function reduceVitality($number)
    {
        $stmt = $this->db->prepare("UPDATE character_stats SET current_hp = GREATEST(0, current_hp - ?) WHERE character_id = ?");
        $stmt->bind_param("ii", $number, $this->id);
        $stmt->execute();
        
        // Update local cache if current object is waiting
        if (isset($this->currentHp)) {
            $this->currentHp = max(0, $this->currentHp - $number);
        }
    }
    
    public function isAlive()
    {
        if (isset($this->currentHp)) {
            return $this->currentHp > 0;
        }
        // Fallback if currentHp not set (should not happen if loaded via findById)
        $stmt = $this->db->prepare("SELECT current_hp FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res['current_hp'] > 0);
    }


    public function getClassId(){
        return $this->classId;
    }
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Get Total Defense (Reduction)
     * Used for Damage Reduction, not Hit Avoidance (Evasion).
     */
    public function getArmorClass()
    {
        // Defense Stat from items + potentially Vitality/2 or specific Defense stat?
        // User requested: "Utilisation de la Défense (Réduction des dégâts)"
        // Strength was used in original formula. Vitality creates "meat shield"?
        // Let's stick to Equipment Defense + potentially a small constant/stat if defined.
        // For now: Just Equipped Defense.
        if($this->armor == null) $this->armor = $this->getEquippedStats(StatsEnum::Defense);
        return $this->armor;
    }

    // Removed duplicate isAlive() here - use the one checking currentHp

    
   
    public function getUserId(){
        return $this->userId;
    }


    // Admin Methods
    public function deleteById($id)
    {
        $stmt = $this->db->prepare("DELETE FROM characters WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getAllCharacters($filters = [])
    {
        $sql = "
            SELECT c.*, u.username, cl.name as class_name, s.level 
            FROM characters c 
            JOIN users u ON c.user_id = u.id 
            JOIN classes cl ON c.class_id = cl.id 
            LEFT JOIN character_stats s ON c.id = s.character_id
            WHERE 1=1
        ";
        
        $params = [];
        $types = "";

        if (!empty($filters['class_id'])) {
            $sql .= " AND c.class_id = ?";
            $params[] = $filters['class_id'];
            $types .= "i";
        }

        if (!empty($filters['level'])) {
            $sql .= " AND s.level = ?";
            $params[] = $filters['level'];
            $types .= "i";
        }

        if (!empty($filters['name'])) {
            $sql .= " AND c.name LIKE ?";
            $params[] = "%" . $filters['name'] . "%";
            $types .= "s";
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND c.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }
        
        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
