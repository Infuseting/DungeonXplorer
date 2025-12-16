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
        $classModel = new CharacterClass();
        $statsModel = new CharacterStats();
        
        $characters = $characterModel->findAllByUserId($_SESSION['user_id']);
        
        if (empty($characters)) {
            header('Location: /personnage/create');
            exit;
        }

        // Enrichir chaque personnage avec les données de classe ET les stats
        foreach ($characters as &$character) {
            // Récupérer la classe complète
            $character['class'] = $classModel->findById($character['class_id']);
            
            // Récupérer les stats du personnage
            $character['stats'] = $statsModel->findByCharacterId($character['id']);
            
            // Si pas de stats en BDD, utiliser les stats de base
            if (!$character['stats']) {
                $baseStats = json_decode($character['class']['base_stats_json'], true);
                $character['stats'] = [
                    'strength' => $baseStats['strength'] ?? 10,
                    'dexterity' => $baseStats['dexterity'] ?? 10,
                    'intelligence' => $baseStats['intelligence'] ?? 10,
                    'vitality' => $baseStats['vitality'] ?? 10,
                    'level' => 1
                ];
            }
            
            // Fusionner les stats au niveau principal
            $character['strength'] = $character['stats']['strength'];
            $character['dexterity'] = $character['stats']['dexterity'];
            $character['intelligence'] = $character['stats']['intelligence'];
            $character['vitality'] = $character['stats']['vitality'];
            $character['level'] = $character['stats']['level'] ?? 1;
        }

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

        // Store character data in session temporarily
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
