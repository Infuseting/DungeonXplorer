<?php
namespace App\Controllers;

use App\Models\Skill;
use App\Models\CharacterClass;

class AdminSkillController
{
    private $skillModel;
    private $classModel;

    public function __construct()
    {
        $this->skillModel = new Skill();
        $this->classModel = new CharacterClass();
    }

    public function editor()
    {
        $classId = $_GET['class_id'] ?? 1;         
        $classes = $this->classModel->getAll();         $skills = $this->skillModel->getSkillsByClass($classId);
        
                foreach ($skills as &$skill) {
            $skill['node_x'] = $skill['node_x'] ?? 100;
            $skill['node_y'] = $skill['node_y'] ?? 100;
        }

        require_once __DIR__ . '/../Views/admin/skills/graph.php';
    }

    public function updatePosition()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        $x = $_POST['node_x'] ?? null;
        $y = $_POST['node_y'] ?? null;

        if (!$id || $x === null || $y === null) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        if ($this->skillModel->updatePosition($id, $x, $y)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}
