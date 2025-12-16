<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\CharacterStats;
use App\Services\DifficultyService;

class CharacterDifficultyController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Must have character in progress
        if (!isset($_SESSION['temp_character'])) {
            header('Location: /personnage/create');
            exit;
        }
        
        require __DIR__ . '/../Views/character/difficulty.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /personnage/difficulty');
            exit;
        }

        if (!isset($_SESSION['temp_character'])) {
            header('Location: /personnage/create');
            exit;
        }

        $difficulty = $_POST['difficulty'] ?? DifficultyService::NORMAL;
        // If IronMan difficulty selected, force is_ironman flag. 
        // Or if checkbox is checked.
        $isIronman = isset($_POST['is_ironman']) || $difficulty === DifficultyService::IRONMAN;
        
        // Finalize Character Creation
        $tempChar = $_SESSION['temp_character'];
        $appearance = $tempChar['appearance'] ?? []; // Retrieve appearance from session
        
        // Create Character in DB
        $characterModel = new Character();
        $newCharacterId = $characterModel->create(
            $_SESSION['user_id'],
            $tempChar['class_id'],
            $tempChar['name'],
            $difficulty,
            $isIronman ? 1 : 0
        );

        if (!$newCharacterId) {
            header('Location: /personnage/create?error=creation_failed');
            exit;
        }

        // Create Stats
        $statsModel = new CharacterStats();
        $statsModel->create($newCharacterId, $tempChar['class_id']);

        // Save Appearance
        // Make sure we have the appearance data correctly
        if (!empty($appearance)) {
             $characterModel->updateAppearance($newCharacterId, $appearance);
        }

        // Clean session
        unset($_SESSION['temp_character']);

        // Redirect to Hub
        header('Location: /personnage');
        exit;
    }
}
