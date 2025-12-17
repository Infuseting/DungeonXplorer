<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\User;
use App\Config\Database;

class AdminCharacterController
{
    public function index()
    {
        $characterModel = new Character();
        $userModel = new User();
        
        $filters = [
            'class_id' => $_GET['class_id'] ?? null,
            'level' => $_GET['level'] ?? null,
            'name' => $_GET['name'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
        ];

        $characters = $characterModel->getAllCharacters($filters);
        
                $db = Database::getInstance()->getConnection();
        $classes = $db->query("SELECT * FROM classes")->fetch_all(MYSQLI_ASSOC);
        $users = $userModel->getAllUsers();

        require_once __DIR__ . '/../Views/admin/characters/index.php';
    }

    public function delete($id)
    {
        $characterModel = new Character();
        if ($characterModel->deleteById($id)) {
            header('Location: /admin/characters?success=character_deleted');
        } else {
            header('Location: /admin/characters?error=delete_failed');
        }
        exit;
    }
}
