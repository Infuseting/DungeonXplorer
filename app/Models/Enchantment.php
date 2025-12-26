<?php
namespace App\Models;

use App\Config\Database;

class Enchantment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM enchantments ORDER BY rarity, cost ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM enchantments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO enchantments 
            (name, description, icon, stat_modifiers, compatible_slot_types, cost, required_level, rarity, is_available) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssiisi",
            $data['name'],
            $data['description'],
            $data['icon'],
            $data['stat_modifiers'],
            $data['compatible_slot_types'],
            $data['cost'],
            $data['required_level'],
            $data['rarity'],
            $data['is_available']
        );

        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE enchantments 
            SET name = ?, description = ?, icon = ?, stat_modifiers = ?, 
                compatible_slot_types = ?, cost = ?, required_level = ?, 
                rarity = ?, is_available = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssiisii",
            $data['name'],
            $data['description'],
            $data['icon'],
            $data['stat_modifiers'],
            $data['compatible_slot_types'],
            $data['cost'],
            $data['required_level'],
            $data['rarity'],
            $data['is_available'],
            $id
        );

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM enchantments WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
