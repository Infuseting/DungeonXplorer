<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\CharacterStats;
use App\Services\CharacterStatsService;

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
        
        $characters = $characterModel->findAllByUserId($_SESSION['user_id']);
        
        if (empty($characters)) {
            header('Location: /personnage/create');
            exit;
        }

        // Enrichir chaque personnage avec ses stats effectives
        foreach ($characters as &$character) {
            // Classe
            $character['class'] = $classModel->findById($character['class_id']);
            
            // Stats effectives (base + items + compétences)
            $effectiveStats = CharacterStatsService::getEffectiveStats($character['id']);
            
            // Assigner les stats pour l'affichage
            $character['strength'] = $effectiveStats['strength'];
            $character['dexterity'] = $effectiveStats['dexterity'];
            $character['intelligence'] = $effectiveStats['intelligence'];
            $character['vitality'] = $effectiveStats['vitality'];
            $character['attack'] = $effectiveStats['attack'];
            $character['defense'] = $effectiveStats['defense'];
            
            // Niveau et XP depuis character_stats
            $statsModel = new CharacterStats();
            $baseStats = $statsModel->findByCharacterId($character['id']);
            $character['level'] = $baseStats['level'] ?? 1;
            $character['xp'] = $baseStats['xp'] ?? 0;
            
            // Décoder les données d'apparence si elles sont en JSON
            if (isset($character['appearance']) && is_string($character['appearance'])) {
                $character['appearance'] = json_decode($character['appearance'], true) ?? [];
            } elseif (!isset($character['appearance'])) {
                $character['appearance'] = [];
            }
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
        
                if (empty($name) || empty($classId)) {
            header('Location: /personnage/create?error=missing_fields');
            exit;
        }

                $classModel = new CharacterClass();
        $class = $classModel->findById($classId);
        
        if (!$class) {
            header('Location: /personnage/create?error=invalid_class');
            exit;
        }

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
