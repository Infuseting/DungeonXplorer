<?php

namespace App\Models;

use App\Config\Database;

class Monster
{
    private $db;
    
    // Properties for Combat Logic
    private $name;
    private $strength;
    private $vitality;
    private $intelligence;
    private $dexterity;
    private $defense;
    private $attaque;
    private $imagePath;
    private $sallePath;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all monsters (Admin)
     * 
     * @return array
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM monsters ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Alias for getAll to satisfy potential Combat calls (if any)
     */
    public function findAll()
    {
        return $this->getAll();
    }

    /**
     * Find monster by ID
     * Compatible with Admin (returns array) and Combat (populates object)
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            // Populate Object Properties (Combat Mode)
            $this->name = $result['name'];
            $this->imagePath = $result['image_path'];
            $this->sallePath = $result['salle_path'];

            $json = $result['base_stats_json'];
            $stats = json_decode($json, true);
            $this->strength = $stats['strength'] ?? 0;
            $this->vitality = $stats['vitality'] ?? 0;
            $this->intelligence = $stats['intelligence'] ?? 0;
            $this->dexterity = $stats['dexterity'] ?? 0;
            $this->defense = $stats['defense'] ?? 0;
            $this->attaque = $stats['attaque'] ?? 0;
        }

        return $result;
    }

    /**
     * Create a new monster (Admin)
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO monsters (name, image_path, salle_path, level_min, level_max, base_stats_json) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $statsJson = json_encode($data['stats'] ?? []);
        
        $stmt->bind_param(
            "sssiis", 
            $data['name'], 
            $data['image_path'],
            $data['salle_path'],
            $data['level_min'],
            $data['level_max'], 
            $statsJson
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update a monster (Admin)
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE monsters 
             SET name = ?, image_path = ?, salle_path = ?, level_min = ?, level_max = ?, base_stats_json = ? 
             WHERE id = ?"
        );
        
        $statsJson = json_encode($data['stats'] ?? []);
        
        $stmt->bind_param(
            "sssiisi", 
            $data['name'], 
            $data['image_path'], 
            $data['salle_path'],
            $data['level_min'],
            $data['level_max'],
            $statsJson,
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a monster (Admin)
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ==========================================
    // COMBAT METHODS (Getters & Logic)
    // ==========================================

    public function getName()
    {
        return $this->name;
    }  

    public function getSallePath()
    {
        return $this->sallePath;
    }

    public function getImagePath()
    {
        return $this->imagePath;
    }
    
    public function unsetDb()
    {
        $this->db = null;
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

    public function getDefense()
    {
        return $this->defense;
    }

    public function getAttaque()
    {
        return $this->attaque;
    }

    public function toString()
    {
        return "Monster: " . $this->name . " (STR: " . $this->strength . ", VIT: " . $this->vitality . ", INT: " . $this->intelligence . ", DEX: " . $this->dexterity . ")";
    }

    public function setVitality($vitality)
    {
        $this->vitality = $vitality;
    }

    public function getArmorClass()
    {
        return $this->getStrength()/2 + $this->getDefense()/1.5;
    }

    public function getAttaqueClass()
    {
        return $this->getStrength() + $this->getAttaque();
    }

    public function isAlive()
    {
        return $this->vitality > 0;
    }
    
    public function reduceVitality($number)
    {
        $this->vitality -= $number;
    }
}
