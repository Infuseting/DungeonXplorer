<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{
    public function updateProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');

        if (empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Le nom d\'utilisateur ne peut pas être vide']);
            return;
        }

        $userModel = new User();
        if ($userModel->updateProfile($userId, $username)) {
            $_SESSION['username'] = $username; // Update session
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    public function updateEmail()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email invalide']);
            return;
        }

        $userModel = new User();
        
        // Check uniqueness
        $existing = $userModel->findByEmail($email);
        if ($existing && $existing['id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            return;
        }

        if ($userModel->updateEmail($userId, $email)) {
            echo json_encode(['success' => true, 'message' => 'Email mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    public function updatePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newPassword) || strlen($newPassword) < 6) {
            echo json_encode(['success' => false, 'message' => 'Le nouveau mot de passe doit faire au moins 6 caractères']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
            return;
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user || !$userModel->verifyPassword($currentPassword, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect']);
            return;
        }

        if ($userModel->updatePassword($userId, $newPassword)) {
            echo json_encode(['success' => true, 'message' => 'Mot de passe mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }
}
