<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\CharacterStats;
use App\Models\CharacterAppearance;

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
        
        // Load appearance for the selected character
        $appearanceModel = new CharacterAppearance();
        $selectedCharacter['appearance'] = $appearanceModel->findByCharacterId($selectedCharacter['id']);

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

            // Create Default Appearance (Since customization is removed)
            $appearance = [
                'skin_color' => '#ffdbac',
                'hair_style' => 'default',
                'hair_color' => '#000000',
                'eye_color' => '#000000',
                'face_style' => 'default'
            ];
            
            $appearanceModel = new CharacterAppearance();
            $appearanceModel->create($characterId, $appearance);

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
