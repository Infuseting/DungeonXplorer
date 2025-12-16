<?php

namespace App\Models;

use App\Config\Database;

class Faction
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM factions ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM factions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO factions (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['name'], $data['description']);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE factions SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['name'], $data['description'], $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM factions WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
