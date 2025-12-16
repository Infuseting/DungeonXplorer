<?php

namespace App\Services;

class LoggerService
{
    private $db;
    
    // Categories
    const CAT_CRITICAL = 'CRITICAL';
    const CAT_GAMEPLAY = 'GAMEPLAY';
    const CAT_SECURITY = 'SECURITY';
    const CAT_SYSTEM   = 'SYSTEM';

    public function __construct() {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }

    /**
     * Generic Log Function
     */
    public function log(string $category, string $actionType, ?int $userId, ?int $charId, array $details = [])
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $detailsJson = json_encode($details);
        
        $stmt = $this->db->prepare("INSERT INTO game_logs (user_id, character_id, category, action_type, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $userId, $charId, $category, $actionType, $detailsJson, $ip);
        
        return $stmt->execute();
    }

    /**
     * Log Critical Admin Action
     */
    public function logCritical(int $adminUserId, string $actionType, array $details = [])
    {
        return $this->log(self::CAT_CRITICAL, $actionType, $adminUserId, null, $details);
    }

    /**
     * Log Gameplay Action (Character focused)
     */
    public function logGameplay(int $userId, int $charId, string $actionType, array $details = [])
    {
        return $this->log(self::CAT_GAMEPLAY, $actionType, $userId, $charId, $details);
    }

    /**
     * Log Security Event
     */
    public function logSecurity(?int $userId, string $actionType, array $details = [])
    {
        return $this->log(self::CAT_SECURITY, $actionType, $userId, null, $details);
    }

    /**
     * Fetch Logs with Filters
     */
    public function getLogs(array $filters = [], int $limit = 100, int $offset = 0)
    {
        $sql = "SELECT l.*, u.username, c.name as character_name 
                FROM game_logs l 
                LEFT JOIN users u ON l.user_id = u.id 
                LEFT JOIN characters c ON l.character_id = c.id 
                WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['category'])) {
            $sql .= " AND l.category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }

        if (!empty($filters['action_type'])) {
            $sql .= " AND l.action_type LIKE ?";
            $params[] = "%" . $filters['action_type'] . "%";
            $types .= "s";
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND l.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }
        
        if (!empty($filters['character_id'])) {
            $sql .= " AND l.character_id = ?";
            $params[] = $filters['character_id'];
            $types .= "i";
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND l.created_at >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND l.created_at <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getTotalLogs($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM game_logs l WHERE 1=1";
        $params = [];
        $types = "";

        // Same filters as above (simplified duplication for speed, ideally refactor criteria builder)
        if (!empty($filters['category'])) { $sql .= " AND l.category = ?"; $params[] = $filters['category']; $types .= "s"; }
        if (!empty($filters['action_type'])) { $sql .= " AND l.action_type LIKE ?"; $params[] = "%".$filters['action_type']."%"; $types .= "s"; }
        if (!empty($filters['user_id'])) { $sql .= " AND l.user_id = ?"; $params[] = $filters['user_id']; $types .= "i"; }
        if (!empty($filters['character_id'])) { $sql .= " AND l.character_id = ?"; $params[] = $filters['character_id']; $types .= "i"; }
        if (!empty($filters['date_from'])) { $sql .= " AND l.created_at >= ?"; $params[] = $filters['date_from']; $types .= "s"; }
        if (!empty($filters['date_to'])) { $sql .= " AND l.created_at <= ?"; $params[] = $filters['date_to']; $types .= "s"; }

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
}
