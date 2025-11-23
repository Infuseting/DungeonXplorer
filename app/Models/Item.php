<?php

namespace App\Models;

use App\Config\Database;

class Item
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findAll()
    {
        $result = $this->db->query("SELECT * FROM items");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
