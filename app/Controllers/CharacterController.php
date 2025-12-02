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

        // Get class info
        $classModel = new CharacterClass();
        $class = $classModel->findById($classId);
        
        if (!$class) {
            header('Location: /personnage/create?error=invalid_class');
            exit;
        }

        // Store character data in session temporarily (pas encore en BDD)
        $_SESSION['temp_character'] = [
            'name' => $name,
            'class_id' => $classId,
            'class' => $class,
            'appearance' => [
                'hair' => [
                    'redCyan' => 100,
                    'greenMagenta' => 100,
                    'blueYellow' => 100
                ],
                'eyes' => [
                    'color' => 'brown'
                ],
                'makeup' => [
                    'type' => 'none'
                ]
            ]
        ];

        // Redirect to appearance customization
        header('Location: /personnage/apparence/preview');
        exit;
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
