<?php

namespace App\Models;

use App\Config\Database;
use App\Models\Inventory;
use App\Models\Stats;
class Character
{
    private $db;
    private $name;
    private $level;
    private $xp;
    private $gold;  
    private $strength;
    private $vitality;
    private $intelligence;
    private $dexterity;
    private $inventory;
    private $className;
    private $armor;
    private $id;

     // caches
    private array $inventoryCache = [];
    private array $statsCache = [];


    public function __construct()
    {   
        $this->db = Database::getInstance()->getConnection();
        $this->inventory = new Inventory();
    }

    public function create($userId, $classId, $name)
    {
        $stmt = $this->db->prepare("INSERT INTO characters (user_id, class_id, name) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $classId, $name);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
public function getEquippedStats(Stats $statsEnum): int
{
    // Si déjà calculé, on renvoie directement
    if (isset($this->statsCache[$statsEnum->value])) {
        return $this->statsCache[$statsEnum->value];
    }

    $charInventory = $this->getInventory();
    if(empty($charInventory) ) return 0;
    $stats = 0;
    foreach ($charInventory as $item) {
        if (is_array($item)) {
        foreach ($item as $subItem) {if ($subItem['location'] != 'equipped') continue;
            $data = json_decode($subItem['stats'], true);
            $stats += $data[$statsEnum->value] ?? 0;
        }
    }


    }
    // Mise en cache
    $this->statsCache[$statsEnum->value] = $stats;
    return $stats;
}


    public function updateAppearance($id, $appearanceData)
    {
        $jsonAppearance = json_encode($appearanceData);
        $stmt = $this->db->prepare("UPDATE characters SET appearance = ? WHERE id = ?");
        $stmt->bind_param("si", $jsonAppearance, $id);
        return $stmt->execute();
    }

    public function findAllByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cl.name as class_name, cs.level 
            FROM characters c
            JOIN classes cl ON c.class_id = cl.id
            LEFT JOIN character_stats cs ON c.id = cs.character_id
            WHERE c.user_id = ?
            ORDER BY c.last_played_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
         foreach ($results as &$character) {
            if (!empty($character['appearance'])) {
                $character['appearance'] = json_decode($character['appearance'], true);
            }
        }
        
        return $results;
    }

    public function unsetDb(){
        $this->db = null;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT c.id,c.name, c.class_id, c.gold, cs.level, cs.xp, cs.strength,cs.dexterity,cs.intelligence,cs.vitality  FROM characters c  Join character_stats cs on c.id=cs.character_id WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
            $data= $stmt->get_result()->fetch_assoc();



        if ($data) {

            $this->id = $data['id'];

            $this->name = $data['name'];

            $this->strength = $data['strength'];

            $this->vitality = $data['vitality'];

            $this->intelligence = $data['intelligence'];

            $this->dexterity = $data['dexterity'];

            $this->level = $data['level'];

            $this->xp = $data['xp'];

            $this->gold = $data['gold'];

            $this->className = $data['class_id'];
        }
        
        if ($data && !empty($data['appearance'])) {
            $data['appearance'] = json_decode($result['data'], true);
        }
        
        return $data;
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

    public function getName()
    {
        return $this->name;
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
        $this->vitality -= $number;
    }
    public function getClassName()
    {
        return $this->className;
    }
    public function getArmorClass()
    {
        if($this->armor == null) $this->armor = $this->getStrength()/2 + $this->getEquippedStats(Stats::Defense);
        return $this->armor;
    }

    public function isAlive()
    {
        return $this->vitality > 0;
    }

    public function getAttaqueClass()
    {
        return  $this->getStrength() + $this->getEquippedStats(Stats::Damage);
    }
     public function resetCache(): void
    {
        $this->inventoryCache = [];
        $this->statsCache = [];
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
