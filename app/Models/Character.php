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
    private $classId;
    private $armor;
    private $id;
    private array $appearance = [];
    private $className;
    private $currentHp; // Added currentHp

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
    // ... existing getEquippedStats ...
    
    // ... skipping to findById ...

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT c.id, c.user_id, cl.name as class_name,c.name,c.appearance, c.class_id, c.gold, cs.level, cs.xp, cs.strength,cs.dexterity,cs.intelligence,cs.vitality, cs.current_hp FROM characters c  Join character_stats cs on c.id=cs.character_id join classes cl on cl.id = c.class_id WHERE c.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $data= $stmt->get_result()->fetch_assoc();
        
        if ($data) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->classId = $data['class_id'];
            $this->className = $data['class_name'];
            $this->gold = $data['gold'];
            $this->level = $data['level'];
            $this->xp = $data['xp'];
            $this->strength = $data['strength'];
            $this->vitality = $data['vitality'];
            $this->intelligence = $data['intelligence'];
            $this->dexterity = $data['dexterity'];
            $this->appearance = json_decode($data['appearance'] ?? '[]', true);
            $this->currentHp = $data['current_hp'] ?? $data['vitality']; // Default to vitality if null
            return $this;
        }
        return null; // Return null if not found
    }




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

            $this->classId = $data['class_id'];

            $this->className = $data['class_name'];
        }
        
                if (!empty($data['appearance'])) {
                $this->appearance = json_decode($data['appearance'], true);
            } else {
                $this->appearance = []; // valeur par défaut
            }

        
        return $data;
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
        // For now, assume healing just adds to a 'current_hp' column if it exists,
        // or just logs it if we don't have that column yet.
        // Assuming 'vitality' is the stat, and maybe there's 'hp' column in characters or character_stats.
        // Let's safe-check and try to update 'hp' in character_stats.
        // If the column doesn't exist, this query will fail silently or throw, but we should try.
        // Actually, let's look at migration output to see if we can add 'current_hp'.
        // For now, let's implement the query assuming 'current_hp' exists or will exist.
        // If not, we might need a migration for it too.
        
        // Check if current_hp exists in schema (can't easily checking runtime).
        // I will add 'current_hp' to the migration script just in case!
        
        $stmt = $this->db->prepare("UPDATE character_stats SET current_hp = current_hp + ? WHERE character_id = ?");
        $stmt->bind_param("ii", $amount, $characterId);
        return $stmt->execute();
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
        // Actually reduce current HP, not max vitality
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

    public function getId(){
        return $this->id;
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
