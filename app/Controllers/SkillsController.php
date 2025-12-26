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

        $unlockedIds = array_map(function ($s) {
            return $s['id']; }, $unlocked);

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

        $skillModel = new Skill();
        $skill = $skillModel->findById($skillId);

        if (!$skill) {
            echo json_encode(['success' => false, 'message' => 'Skill not found']);
            return;
        }

        $cost = $skill['cost_sp'];

        if ($character->getSkillPoints() < $cost) {
            echo json_encode(['success' => false, 'message' => 'Not enough Skill Points']);
            return;
        }

        // Start transaction manually if possible, or just checks
        if (!$character->spendSkillPoints($cost)) {
            echo json_encode(['success' => false, 'message' => 'Failed to spend SP']);
            return;
        }

        $result = $character->unlockSkill($skillId);

        if (!$result) {
            // Refund on failure (basic compensation)
            $character->spendSkillPoints(-$cost);
            echo json_encode(['success' => false, 'message' => 'Database error unlocking skill']);
            return;
        }

        echo json_encode(['success' => true]);
    }
}
