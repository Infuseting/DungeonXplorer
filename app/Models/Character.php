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
    private $id;

    public function __construct($id)
    {   
        $this->id = $id;
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
    public function getEquippedStats($statsEnum) {
        $charInventory = $this->getInventory();
        $stats = 0;
        foreach ($charInventory as $item) {
            if (is_array($item)) {
                foreach ($item as $subItem) {
                    if ($subItem['location'] != 'equipped') continue;
                    
                    $data = json_decode($subItem['stats'], true);                    
                    $stats += $data[$statsEnum->value] ?? 0;
                }
            }
        }   
        return $stats;
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
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT c.name, c.gold, cs.level, cs.xp, cs.strength,cs.dexterity,cs.intelligence,cs.vitality  FROM characters c  Join character_stats cs on c.id=cs.character_id WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();


        $data= $stmt->get_result()->fetch_assoc();

        if ($data) {
            $this->name = $data['name'];
            $this->strength = $data['strength'];
            $this->vitality = $data['vitality'];
            $this->intelligence = $data['intelligence'];
            $this->dexterity = $data['dexterity'];
            $this->level = $data['level'];
            $this->xp = $data['xp'];
            $this->gold = $data['gold'];

        }


    }

    public function updateLastPlayed($id)
    {
        $stmt = $this->db->prepare("UPDATE characters SET last_played_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM characters WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        return $stmt->execute();
    }

    public function getInventory()
    {
        return $this->inventory->getCharacterInventory($this->id);
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
    public function getDexterity()
    {
        return $this->dexterity;
    }
    public function setVitality($vitality)
    {
        $this->vitality = $vitality;
    }

    public function getArmorClass()
    {
        return  $this->getStrength()/2 + $this->getEquippedStats(Stats::Defense);
    }

    public function isAlive()
    {
        return $this->vitality > 0;
    }

    public function getAttaqueClass()
    {
        return  $this->getStrength() + $this->getEquippedStats(Stats::Damage);
    }
}
