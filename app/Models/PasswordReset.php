<?php

namespace App\Models;

use App\Config\Database;

class PasswordReset
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $code, $expiresAt)
    {
                $this->deleteUserCodes($userId);

        $stmt = $this->db->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $code, $expiresAt);
        return $stmt->execute();
    }

    public function verify($userId, $code)
    {
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE user_id = ? AND code = ? AND expires_at > NOW()");
        $stmt->bind_param("is", $userId, $code);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteUserCodes($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
}
