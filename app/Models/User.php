<?php

namespace App\Models;

use App\Config\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($username, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        return $stmt->execute();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

        public function createRememberToken($userId, $selector, $validator, $expiresAt)
    {
        $hashedValidator = hash('sha256', $validator);
        $stmt = $this->db->prepare("INSERT INTO user_tokens (user_id, selector, hashed_validator, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $selector, $hashedValidator, $expiresAt);
        return $stmt->execute();
    }

    public function findUserByToken($selector, $validator)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_tokens WHERE selector = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $result = $stmt->get_result();
        $tokenData = $result->fetch_assoc();

        if ($tokenData && hash_equals($tokenData['hashed_validator'], hash('sha256', $validator))) {
            return $this->findById($tokenData['user_id']);
        }

        return null;
    }

    public function deleteToken($selector)
    {
        $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE selector = ?");
        $stmt->bind_param("s", $selector);
        return $stmt->execute();
    }
    
    public function deleteUserTokens($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

        public function getAllUsers()
    {
        $result = $this->db->query("
            SELECT u.*, 
            (SELECT COUNT(*) FROM characters c WHERE c.user_id = u.id) as character_count 
            FROM users u 
            ORDER BY u.created_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        return $stmt->execute();
    }

    public function delete($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    public function updateProfile($userId, $username)
    {
        $stmt = $this->db->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $username, $userId);
        return $stmt->execute();
    }

    public function updateEmail($userId, $email)
    {
        $stmt = $this->db->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $email, $userId);
        return $stmt->execute();
    }
}
