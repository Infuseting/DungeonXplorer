<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Character;

class AdminController
{
    public function dashboard()
    {
        $userModel = new User();
        $characterModel = new Character();
        
        // Get stats for dashboard
        $stats = [
            'total_users' => $this->getTotalUsers(),
            'total_characters' => $this->getTotalCharacters(),
            'total_maps' => $this->getTotalMaps(),
            'total_quests' => $this->getTotalQuests(),
        ];
        
        // Get recent activity
        $recentCharacters = $this->getRecentCharacters(5);
        
        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }
    
    private function getTotalUsers()
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        $result = $db->query("SELECT COUNT(*) as count FROM users");
        return $result->fetch_assoc()['count'];
    }
    
    private function getTotalCharacters()
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        $result = $db->query("SELECT COUNT(*) as count FROM characters");
        return $result->fetch_assoc()['count'];
    }
    
    private function getTotalMaps()
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        $result = $db->query("SELECT COUNT(*) as count FROM maps");
        return $result->fetch_assoc()['count'];
    }
    
    private function getTotalQuests()
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        $result = $db->query("SELECT COUNT(*) as count FROM quests");
        return $result->fetch_assoc()['count'];
    }
    
    private function getRecentCharacters($limit = 5)
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT c.*, u.username, cl.name as class_name 
            FROM characters c 
            JOIN users u ON c.user_id = u.id 
            JOIN classes cl ON c.class_id = cl.id 
            ORDER BY c.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
