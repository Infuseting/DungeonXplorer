<?php

namespace App\Models;

use App\Config\Database;
use App\Models\CharacterStats;
use App\Models\Inventory;


class Monster
{
    private $db;

    private $id;
    private $name;
    private $strength;
    private $vitality;
    private $intelligence;
    private $dexterity;
    private $defense;
    private $attaque;
    private $imagePath;
    private $sallePath;
    private $creatureType = 'neutral';
    private $affinities = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();

    }

    public function __wakeup()
    {
        // Reconnexion DB après désérialisation
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $this->id = $result['id'];
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

            $this->creatureType = $result['creature_type'] ?? 'neutral';
            $this->affinities = !empty($result['affinities']) ? json_decode($result['affinities'], true) : [];
        }

        return $result;
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO monsters (name, image_path, salle_path, level_min, level_max, base_stats_json, creature_type, affinities) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $statsJson = json_encode($data['stats'] ?? []);
        $affinitiesJson = json_encode($data['affinities'] ?? []);
        $type = $data['creature_type'] ?? 'neutral';

        $stmt->bind_param(
            "sssiisss",
            $data['name'],
            $data['image_path'],
            $data['salle_path'],
            $data['level_min'],
            $data['level_max'],
            $statsJson,
            $type,
            $affinitiesJson
        );

        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE monsters 
             SET name = ?, image_path = ?, salle_path = ?, level_min = ?, level_max = ?, base_stats_json = ?, creature_type = ?, affinities = ? 
             WHERE id = ?"
        );

        $statsJson = json_encode($data['stats'] ?? []);
        $affinitiesJson = json_encode($data['affinities'] ?? []);
        $type = $data['creature_type'] ?? 'neutral';

        $stmt->bind_param(
            "sssiisssi",
            $data['name'],
            $data['image_path'],
            $data['salle_path'],
            $data['level_min'],
            $data['level_max'],
            $statsJson,
            $type,
            $affinitiesJson,
            $id
        );

        return $stmt->execute();
    }


    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }



    public function getCreatureType()
    {
        return $this->creatureType;
    }

    public function getAffinities()
    {
        return $this->affinities;
    }

    /**
     * Get Resistance/Weakness Modifier for a specific Damage Type
     * @param string $damageType e.g. 'fire', 'holy', 'physical'
     * @return array ['type' => 'percent'|'flat', 'value' => int] (Value is positive for weakness likely? Or we standardize)
     */
    public function getAffinityModifier($damageType)
    {

        if (isset($this->affinities[$damageType])) {
            return $this->affinities[$damageType];
        }
        return null;
    }
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM monsters");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $result;
    }
    public function getId()
    {
        return $this->id;
    }
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
        return "Monster: " . $this->name . " [" . $this->creatureType . "] (STR: " . $this->strength . ", VIT: " . $this->vitality . ")";
    }

    public function setVitality($vitality)
    {
        $this->vitality = $vitality;
    }

    public function getArmorClass()
    {
        return $this->getStrength() / 2 + $this->getDefense() / 1.5;
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
