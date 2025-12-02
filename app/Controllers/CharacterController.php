<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\CharacterStats;

class CharacterController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $characterModel = new Character();
        $characters = $characterModel->findAllByUserId($_SESSION['user_id']);
        
        // If user has no characters, redirect to create
        if (empty($characters)) {
            header('Location: /personnage/create');
            exit;
        }

        // Get the last played character (first in the list due to ORDER BY)
        $selectedCharacter = $characters[0];
        
        require_once __DIR__ . '/../Views/character/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $classModel = new CharacterClass();
        $classes = $classModel->findAll();

        require_once __DIR__ . '/../Views/character/create.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $classId = $_POST['class_id'] ?? '';
        
        // Basic validation
        if (empty($name) || empty($classId)) {
            header('Location: /personnage/create?error=missing_fields');
            exit;
        }

        // Get class base stats
        $classModel = new CharacterClass();
        $class = $classModel->findById($classId);
        
        if (!$class) {
            header('Location: /personnage/create?error=invalid_class');
            exit;
        }

        $baseStats = json_decode($class['base_stats_json'], true);

        // Create Character
        $characterModel = new Character();
        $characterId = $characterModel->create($_SESSION['user_id'], $classId, $name);

        if ($characterId) {
            // Create Stats
            $statsModel = new CharacterStats();
            $statsModel->create($characterId, $baseStats);

            header('Location: /personnage');
            exit;
        } else {
            header('Location: /personnage/create?error=creation_failed');
            exit;
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $characterId = $_POST['character_id'] ?? null;

        if ($characterId) {
            $characterModel = new Character();
            $characterModel->delete($characterId, $_SESSION['user_id']);
        }

        header('Location: /personnage');
        exit;
    }
}
