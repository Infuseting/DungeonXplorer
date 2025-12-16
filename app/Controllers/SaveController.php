<?php

namespace App\Controllers;

use App\Services\SaveService;

class SaveController
{
    private $saveService;

    public function __construct()
    {
        $this->saveService = new SaveService();
    }

    /**
     * Create a manual save
     */
    public function saveGame()
    {
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $charId = $_SESSION['character_id'];
        $name = $_POST['name'] ?? 'Manuel ' . date('Y-m-d H:i');

        if ($this->saveService->createSave($charId, $name)) {
            echo json_encode(['success' => true, 'message' => 'Partie sauvegardée !']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde']);
        }
    }

    /**
     * Load a save
     */
    public function loadGame()
    {
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $charId = $_SESSION['character_id'];
        $saveId = $_POST['save_id'];

        if ($this->saveService->loadSave($saveId, $charId)) {
            echo json_encode(['success' => true, 'message' => 'Partie chargée !']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement']);
        }
    }

    /**
     * List saves (for UI)
     */
    public function listSaves()
    {
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false]);
            return;
        }

        $saves = $this->saveService->listSaves($_SESSION['character_id']);
        echo json_encode(['success' => true, 'saves' => $saves]);
    }
}
