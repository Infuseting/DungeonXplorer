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

    // Remember Me Functionality
    public function createRememberToken($userId, $selector, $validator, $expiresAt)
    {
        $hashedValidator = hash('sha256', $validator);
        
        $stmt = $this->db->prepare("INSERT INTO user_tokens (user_id, selector, hashed_validator, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $selector, $hashedValidator, $expiresAt);
        return $stmt->execute();
    }

    public function findUserBySelector($selector)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, ut.hashed_validator, ut.expires_at 
            FROM user_tokens ut
            JOIN users u ON ut.user_id = u.id
            WHERE ut.selector = ? AND ut.expires_at > NOW()
        ");
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function removeRememberToken($selector)
    {
        $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE selector = ?");
        $stmt->bind_param("s", $selector);
        return $stmt->execute();
    }
}
