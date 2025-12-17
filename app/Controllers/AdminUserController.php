<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\PasswordReset;

class AdminUserController
{
    public function index()
    {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        
        require_once __DIR__ . '/../Views/admin/users/index.php';
    }

    public function resetPassword($id)
    {
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', time() + 86400); 
        $resetModel = new PasswordReset();
        if ($resetModel->create($id, $code, $expiresAt)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'code' => $code]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to generate code']);
        }
        exit;
    }

    public function delete($id)
    {
        $userModel = new User();
        if ($userModel->delete($id)) {
            header('Location: /admin/users?success=user_deleted');
        } else {
            header('Location: /admin/users?error=delete_failed');
        }
        exit;
    }
}
