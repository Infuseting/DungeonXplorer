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

    public function __construct($historyId)
    {
        $this->db = Database::getInstance()->getConnection();
        $this->chapterList = array();

        if (!is_null($historyId)) {
            $this->create($historyId);
        }
    }

    private function create(int $historyId)
    {
        // Chargement des donnes de l'histoire dans les parametres de la classe
        $stmt = $this->db->prepare("SELECT * FROM history WHERE history_id = ?");
        $stmt->bind_param("i", $historyId);
        $stmt->execute();
        $historyData = $stmt->get_result()->fetch_assoc();

        // initialisation des attributs
        $this->historyId = $historyData['history_id'];
        $this->historyTypeId = $historyData['history_type_id'];
        $this->name = $historyData['name'];
        $this->description = $historyData['description'];

        // creation de la liste des chapitres
        $stmt = $this->db->prepare("SELECT * FROM chapters WHERE history_id = ?");
        $stmt->bind_param("i", $historyId);
        $stmt->execute();
        $historyChaptersData = $stmt->get_result()->fetch_assoc();

        $nb_chapters = count($historyChaptersData);

        // peut etre un bug dans le futur si jamais il y a plus qu'un chapitre renvoyer
        if ($nb_chapters) {
            $chapter = new Chapter();

            if ($chapter->load($historyChaptersData)) {
                $this->addChapter($chapter);
            }
        }
    }
    
    public function addChapter(Chapter $chapter): void {
        array_push($this->chapterList, $chapter);
    }

    public function getChapterList(): array {
        return $this->chapterList;
    }

}
