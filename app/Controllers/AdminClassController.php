<?php

namespace App\Controllers;

use App\Models\CharacterClass;
use App\Models\Skill;
use App\Config\Database;

class AdminClassController
{
    private $classModel;
    private $skillModel;

    public function __construct()
    {
        $this->classModel = new CharacterClass();
        $this->skillModel = new Skill();
    }

    public function index()
    {
        $classes = $this->classModel->findAll();
        $totalClasses = count($classes);
        
        require __DIR__ . '/../Views/admin/classes/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/admin/classes/edit.php';
    }

    public function store()
    {
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'base_stats' => [
                'strength' => (int)$_POST['strength'],
                'dexterity' => (int)$_POST['dexterity'],
                'intelligence' => (int)$_POST['intelligence'],
                'vitality' => (int)$_POST['vitality']
            ]
        ];

        if ($this->classModel->create($data)) {
            header('Location: /admin/classes?success=created');
        } else {
            header('Location: /admin/classes?error=create_failed');
        }
        exit;
    }

    public function edit($id)
    {
        $class = $this->classModel->findById($id);
        if (!$class) {
            header('Location: /admin/classes?error=not_found');
            exit;
        }

        $skills = $this->skillModel->getSkillsByClass($id);
        
        require __DIR__ . '/../Views/admin/classes/edit.php';
    }

    public function update($id)
    {
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'base_stats' => [
                'strength' => (int)$_POST['strength'],
                'dexterity' => (int)$_POST['dexterity'],
                'intelligence' => (int)$_POST['intelligence'],
                'vitality' => (int)$_POST['vitality']
            ]
        ];

        if ($this->classModel->update($id, $data)) {
            header('Location: /admin/classes?success=updated');
        } else {
            header('Location: /admin/classes/edit/' . $id . '?error=update_failed');
        }
        exit;
    }

    public function delete($id)
    {
        if ($this->classModel->delete($id)) {
            header('Location: /admin/classes?success=deleted');
        } else {
            header('Location: /admin/classes?error=delete_failed');
        }
        exit;
    }

    /* Skills Management */

    public function addSkill($classId)
    {
        $class = $this->classModel->findById($classId);
        require __DIR__ . '/../Views/admin/classes/skills/form.php';
    }

    public function storeSkill($classId)
    {
        $data = [
            'class_id' => $classId,
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'type' => $_POST['type'],
            'min_level' => (int)$_POST['min_level'],
            'cost_mp' => (int)$_POST['cost_mp'],
            'cost_sp' => (int)$_POST['cost_sp'],
            'cooldown' => (int)$_POST['cooldown'],
            'effect_type' => $_POST['effect_type'],
            'effect_value' => (int)$_POST['effect_value'],
            'parent_skill_id' => !empty($_POST['parent_skill_id']) ? $_POST['parent_skill_id'] : null
        ];

        if ($this->skillModel->create($data)) {
            header('Location: /admin/classes/edit/' . $classId . '?success=skill_added');
        } else {
            header('Location: /admin/classes/skills/add/' . $classId . '?error=create_failed');
        }
        exit;
    }

    public function editSkill($skillId)
    {
        $skill = $this->skillModel->findById($skillId);
        if (!$skill) {
            header('Location: /admin/classes?error=skill_not_found');
            exit;
        }
        $class = $this->classModel->findById($skill['class_id']);
        $classSkills = $this->skillModel->getSkillsByClass($skill['class_id']); // For parent selection
        
        require __DIR__ . '/../Views/admin/classes/skills/form.php';
    }

    public function updateSkill($skillId)
    {
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'type' => $_POST['type'],
            'min_level' => (int)$_POST['min_level'],
            'cost_mp' => (int)$_POST['cost_mp'],
            'cost_sp' => (int)$_POST['cost_sp'],
            'cooldown' => (int)$_POST['cooldown'],
            'effect_type' => $_POST['effect_type'],
            'effect_value' => (int)$_POST['effect_value'],
            'parent_skill_id' => !empty($_POST['parent_skill_id']) ? $_POST['parent_skill_id'] : null
        ];

        $skill = $this->skillModel->findById($skillId); // To get class_id for redirect

        if ($this->skillModel->update($skillId, $data)) {
            header('Location: /admin/classes/edit/' . $skill['class_id'] . '?success=skill_updated');
        } else {
            header('Location: /admin/classes/skills/edit/' . $skillId . '?error=update_failed');
        }
        exit;
    }

    public function deleteSkill($skillId)
    {
        $skill = $this->skillModel->findById($skillId);
        if ($skill && $this->skillModel->delete($skillId)) {
            header('Location: /admin/classes/edit/' . $skill['class_id'] . '?success=skill_deleted');
        } else {
            header('Location: /admin/classes?error=delete_failed');
        }
        exit;
    }
    public function updateSkillPositions()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['positions'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }

        foreach ($input['positions'] as $pos) {
            $this->skillModel->updatePosition($pos['id'], $pos['x'], $pos['y']);
        }

        echo json_encode(['success' => true]);
        exit;
    }
}
