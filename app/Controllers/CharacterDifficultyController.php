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
                        $isIronman = isset($_POST['is_ironman']) || $difficulty === DifficultyService::IRONMAN;
        
                $tempChar = $_SESSION['temp_character'];
        $appearance = $tempChar['appearance'] ?? [];         
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

                $statsModel = new CharacterStats();
        $statsModel->create($newCharacterId, $tempChar['class_id']);

                        if (!empty($appearance)) {
            $characterModel->updateAppearance($newCharacterId, $appearance);
        }

                unset($_SESSION['temp_character']);

                header('Location: /personnage');
        exit;
    }
}
