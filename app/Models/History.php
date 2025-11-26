<?php

namespace App\Models;

use App\Config\Database;
use App\Models\Chapter;

class History
{
    private $db;
    private int $historyId;
    private int $historyTypeId;
    private string $name;
    private string $description;
    private array $chapterList;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->chapterList = array();
    }

    public function create(int $historyId)
    {
        $stmt = $this->db->prepare("SELECT * FROM history WHERE history_id = ?");
        $stmt->bind_param("i", $historyId);
        $stmt->execute();
        $donnees = $stmt->get_result()->fetch_assoc();
        

        $stmt = $this->db->prepare("SELECT * FROM chapters WHERE history_id = ?");
        $stmt->bind_param("i", $historyId);
        $stmt->execute();
        $donnees = $stmt->get_result()->fetch_assoc();

        foreach($donnees as $item) {
            $chapter = new Chapter();
            $chapter->create($item);
            $this->addChapter($chapter);
        }

        var_dump($donnees);
        

        var_dump($this->chapterList);
    }
    
    public function addChapter(array $chapter): void {
        array_push($this->chapterList, $chapter);
    }

    public function getChapterList(): array {
        return $this->chapterList;
    }

}
