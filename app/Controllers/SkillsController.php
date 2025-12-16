<?php
namespace App\Controllers;

use App\Models\Character;
use App\Models\Skill;

class SkillsController
{
    public function index()
    {
        if (!isset($_SESSION['character_id'])) {
            header('Location: /game');
            exit;
        }

        $characterModel = new Character();
        $character = $characterModel->findById($_SESSION['character_id']);
        
        $skillModel = new Skill();
        $classSkills = $skillModel->getSkillsByClass($character->getClassId());
        $unlocked = $skillModel->getUnlockedSkills($character->getId());
        
        // Map unlocked IDs for easy lookup
        $unlockedIds = array_map(function($s) { return $s['id']; }, $unlocked);
        
        // Prepare data for view
        require_once __DIR__ . '/../Views/game/skills.php';
    }

    public function unlock()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'No character loaded']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $skillId = $data['skill_id'] ?? null;
        
        if (!$skillId) {
            echo json_encode(['success' => false, 'message' => 'Missing skill ID']);
            return;
        }

        $characterModel = new Character();
        $character = $characterModel->findById($_SESSION['character_id']);
        
        $result = $character->unlockSkill($skillId);
        
        echo json_encode($result);
    }
}
