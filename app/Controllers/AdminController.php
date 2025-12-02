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
            'total_npcs' => $this->getTotalNPCs(),
        ];
        
        // Get recent activity
        $recentCharacters = $this->getRecentCharacters(5);
        
        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    // API endpoint for dashboard charts
    public function stats()
    {
        $db = \App\Config\Database::getInstance()->getConnection();

        // Class distribution
        $classes = [];
        $res = $db->query("SELECT cl.name, COUNT(*) as count FROM characters c JOIN classes cl ON c.class_id = cl.id GROUP BY cl.id");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $classes[] = [
                    'name' => $row['name'],
                    'count' => (int)$row['count']
                ];
            }
        }

        // Activity last 7 days (inclusive)
        $activity = [];
        $stmt = $db->prepare("SELECT DATE(created_at) as date, COUNT(*) as count FROM characters WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY DATE(created_at)");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $map = [];
            while ($r = $result->fetch_assoc()) {
                $map[$r['date']] = (int)$r['count'];
            }

            // Fill missing days
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $activity[] = [
                    'date' => $date,
                    'count' => isset($map[$date]) ? $map[$date] : 0
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'class_distribution' => $classes,
            'activity' => $activity
        ]);
        exit;
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
    
    
    private function getTotalNPCs()
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        $result = $db->query("SELECT COUNT(*) as count FROM npcs");
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
