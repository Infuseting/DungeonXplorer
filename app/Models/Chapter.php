<?php

namespace App\Models;

use App\Config\Database;

class Chapter
{
    private $db;
    private $chapterId;
    private $name;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($chapterId, $name, $description)
    {
        $stmt = $this->db->prepare("INSERT INTO chapters (chapter_id, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $chapterId, $name, $description);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
}
