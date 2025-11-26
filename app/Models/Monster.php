<?php

namespace App\Models;

use App\Config\Database;

class Monster 
{
    private $db;
    private $name;
    private $strength;
    private $vitality;
    private $intelligence;
    private $dexterity;
    private $defense;
    private $attaque;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['name'];

            $json = $result['base_stats_json'];
            $stats = json_decode($json, true);
            $this->strength = $stats['strength'];
            $this->vitality = $stats['vitality'];
            $this->intelligence = $stats['intelligence'];
            $this->dexterity = $stats['dexterity'];
            $this->defense = $stats['defense'];
            $this->attaque = $stats['attaque'];

           
        }


    }

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM monsters");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
        return  $this->getStrength()/2 + $this->getDefense();
    }

    public function getAttaqueClass()
    {
        return  $this->getStrength() + $this->getAttaque();
    }

    public function isAlive()
    {
        return $this->vitality > 0;
    }


}
