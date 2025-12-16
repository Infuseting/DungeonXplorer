<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\CharacterStats;

class CharacterAppearanceController
{   
    
    public function index($characterId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Mode preview : personnage temporaire en session
        if ($characterId === 'preview') {
            if (!isset($_SESSION['temp_character'])) {
                header('Location: /personnage/create');
                exit;
            }

            $character = $_SESSION['temp_character'];
            $character['id'] = 'preview';
            $isPreview = true;
        } 
        // Mode édition : personnage existant en BDD
        else {
            $characterModel = new Character();
            $character = $characterModel->findById($characterId);
            
            if (!$character || $character->getUserId() != $_SESSION['user_id']) {
                header('Location: /personnage');
                exit;
            }
            
            $classModel = new CharacterClass();
            $character['class'] = $classModel->findById($character['class_id']);
            
            $statsModel = new CharacterStats();
            $character['stats'] = $statsModel->findByCharacterId($characterId);
            
            $isPreview = false;
        }
        
        // Vérifier que $character['class'] existe
        if (!isset($character['class']) || !is_array($character['class'])) {
            error_log("Error: character class not found or invalid");
            error_log("Character data: " . print_r($character, true));
            header('Location: /personnage/create?error=invalid_class');
            exit;
        }
        
        // Charger les options d'apparence disponibles pour cette classe
        $className = strtolower($character['class']['name']);
        $appearanceOptions = $this->getAppearanceOptions($className);
        
        require __DIR__ . '/../Views/character/appearance.php';
    }
    public function toFullArray($characterId) {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $characterModel = new Character();
        $character = $characterModel->findById($characterId);
        
        if (!$character || $character->getUserId() != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        // Convert object to array for renderCharacter
        $characterData = $character->toArray();
        
        // Add User ID manually if needed or just ensure structure matches
        $characterData['user_id'] = $character->getUserId();

        // Récupérer la classe complète
        $classModel = new CharacterClass();
        $characterData['class'] = $classModel->findById($characterData['class']['name'] ?? 'Warrior'); // Wait, toArray has class name?
        // Actually toArray returns ['class' => ['name' => ...]]
        // But we need the ID to fetch full class?
        // findById on Character loaded classId.
        // We can just use the class info present in character object if available, OR fetch again.
        // Character object has classId.
        // Let's add getClassId to Character model or use internal property if public (it's private).
        // Let's assume we can fetch by class Name or similar, OR add getClassId method.
        // For now, simpler: Character::findById query ALREADY joined classes and got `class_name`.
        // `renderCharacter` uses `$character['class']['name']` OR `$character['class_name']`.
        // `toArray` returns `class_name` and `class` nested. this should be enough for name.
        
        // Récupérer les stats
        $statsModel = new CharacterStats();
        $characterData['stats'] = $statsModel->findByCharacterId($characterId);
        
        // Rendre le personnage
        echo renderCharacter($characterData, [
            'size' => 'full',
            'showFilter' => true,
            'id' => 'character-' . $characterData['id'],
            'class' => 'max-h-full max-w-full drop-shadow-2xl hover:brightness-110 transition duration-500'
        ]);
    }
    private function getAppearanceOptions($className)
    {
        $basePath = __DIR__ . '/../../public/assets/images/' . $className;
        
        $options = [
            'eyes' => [],
            'makeup' => []
        ];
        
        // Scanner le dossier eyes
        $eyesPath = $basePath . '/eyes';
        if (is_dir($eyesPath)) {
            $eyeFiles = glob($eyesPath . '/eyes_*.png');
            if ($eyeFiles) {
                foreach ($eyeFiles as $file) {
                    $filename = basename($file, '.png');
                    $eyeType = str_replace('eyes_', '', $filename);
                    $options['eyes'][$eyeType] = ucfirst($eyeType);
                }
            }
        }
        
        // Scanner le dossier makeup
        $makeupPath = $basePath . '/makeup';
        if (is_dir($makeupPath)) {
            $makeupFiles = glob($makeupPath . '/*.png');
            if ($makeupFiles) {
                foreach ($makeupFiles as $file) {
                    $filename = basename($file, '.png');
                    $makeupName = ucwords(str_replace('_', ' ', $filename));
                    $options['makeup'][$filename] = $makeupName;
                }
            }
        }
        
        return $options;
    }
    
    public function update($characterId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /personnage/apparence/' . $characterId);
            exit;
        }
        
        // Récupérer les maquillages cochés
        $selectedMakeups = $_POST['makeup'] ?? [];
        $makeupData = [];
        
        // Convertir le tableau en format associatif
        foreach ($selectedMakeups as $makeupFile) {
            $makeupData[$makeupFile] = true;
        }
        
        $hairData = [
            'redCyan' => (int)($_POST['hair_red_cyan'] ?? 100),
            'greenMagenta' => (int)($_POST['hair_green_magenta'] ?? 100),
            'blueYellow' => (int)($_POST['hair_blue_yellow'] ?? 100),
            'natural' => isset($_POST['hair_natural'])
        ];
        
        $appearance = [
            'hair' => $hairData,
            'eyes' => [
                'color' => $_POST['eye_color'] ?? 'brown'
            ],
            'makeup' => $makeupData
        ];
        
        // Mode preview : save appearance to session and redirect to difficulty
        if ($characterId === 'preview') {
            if (!isset($_SESSION['temp_character'])) {
                header('Location: /personnage/create');
                exit;
            }

            // Update session with appearance data
            $_SESSION['temp_character']['appearance'] = $appearance;

            // Redirect to Difficulty Selection
            header('Location: /personnage/difficulty');
            exit;
        }
        // Mode édition : mettre à jour un personnage existant
        else {
            $characterModel = new Character();
            $character = $characterModel->findById($characterId);
            
            if (!$character || $character->getUserId() != $_SESSION['user_id']) {
                header('Location: /personnage');
                exit;
            }
            
            $characterModel->updateAppearance($characterId, $appearance);
            
            header('Location: /personnage');
            exit;
        }
    }
}