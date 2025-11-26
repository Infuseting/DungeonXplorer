<?php

namespace App\Models;

use App\Config\Database;

class Chapter
{
    private $db;
    public $chapterId;
    public $historyId;
    public $name;
    public $description;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load (array $data): bool {
        if (count($data) === 4) {
            $this->chapterId = $data['chapter_id'];
            $this->historyId = $data['name'];
            $this->name = $data['description'];
            $this->description = $data['history_id'];
            
            return true;
        }
        return false;
    }
}
