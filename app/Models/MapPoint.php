<?php

namespace App\Models;

use App\Config\Database;

class MapPoint
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all points for a specific map
     * 
     * @param int $mapId
     * @return array
     */
    public function getPointsByMapId($mapId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM map_points WHERE map_id = ? ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find a point by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM map_points WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get points by type for a specific map
     * 
     * @param int $mapId
     * @param string $type
     * @return array
     */
    public function getPointsByType($mapId, $type)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM map_points WHERE map_id = ? AND type = ? ORDER BY created_at DESC"
        );
        $stmt->bind_param("is", $mapId, $type);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new map point
     * 
     * @param array $data
     * @return int|false The new point ID or false on failure
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO map_points (map_id, name, description, type, x, y, icon, metadata, is_hidden) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $metadata = isset($data['metadata']) ? json_encode($data['metadata']) : null;
        
        $stmt->bind_param(
            "isssddssi",
            $data['map_id'],
            $data['name'],
            $data['description'],
            $data['type'],
            $data['x'],
            $data['y'],
            $data['icon'],
            $metadata,
            $data['is_hidden'] ?? 0
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }

    /**
     * Update a map point
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE map_points 
             SET name = ?, description = ?, type = ?, x = ?, y = ?, icon = ?, metadata = ?, updated_at = NOW() 
             WHERE id = ?"
        );
        
        $metadata = isset($data['metadata']) ? json_encode($data['metadata']) : null;
        
        $stmt->bind_param(
            "sssddssi",
            $data['name'],
            $data['description'],
            $data['type'],
            $data['x'],
            $data['y'],
            $data['icon'],
            $metadata,
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a map point
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM map_points WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    /**
     * Get points within a bounding box
     * 
     * @param int $mapId
     * @param float $minX
     * @param float $maxX
     * @param float $minY
     * @param float $maxY
     * @return array
     */
    public function getPointsInBounds($mapId, $minX, $maxX, $minY, $maxY)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM map_points 
             WHERE map_id = ? AND x BETWEEN ? AND ? AND y BETWEEN ? AND ?
             ORDER BY created_at DESC"
        );
        
        $stmt->bind_param("idddd", $mapId, $minX, $maxX, $minY, $maxY);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if a point has a sub-map
     * 
     * @param int $pointId
     * @return bool
     */
    public function hasSubMap($pointId)
    {
        $stmt = $this->db->prepare(
            "SELECT sub_map_id FROM map_points WHERE id = ?"
        );
        $stmt->bind_param("i", $pointId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return !is_null($row['sub_map_id']);
        }
        
        return false;
    }

    /**
     * Get sub-map ID for a point
     * 
     * @param int $pointId
     * @return int|null
     */
    public function getSubMapId($pointId)
    {
        $stmt = $this->db->prepare(
            "SELECT sub_map_id FROM map_points WHERE id = ?"
        );
        $stmt->bind_param("i", $pointId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['sub_map_id'];
        }
        
        return null;
    }

    /**
     * Get all points that have sub-maps
     * 
     * @param int $mapId
     * @return array
     */
    public function getPointsWithSubMaps($mapId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM map_points 
             WHERE map_id = ? AND sub_map_id IS NOT NULL
             ORDER BY created_at DESC"
        );
        
        $stmt->bind_param("i", $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Set sub-map for a point
     * 
     * @param int $pointId
     * @param int|null $subMapId
     * @return bool
     */
    public function setSubMap($pointId, $subMapId)
    {
        $stmt = $this->db->prepare(
            "UPDATE map_points SET sub_map_id = ? WHERE id = ?"
        );
        
        $stmt->bind_param("ii", $subMapId, $pointId);
        
        return $stmt->execute();
    }
    /**
     * Check if a point is visible for a user
     * 
     * @param int $pointId
     * @param int $userId
     * @return bool
     */
    public function isVisibleForUser($pointId, $userId)
    {
        // Get point details
        $point = $this->findById($pointId);
        if (!$point) return false;
        
        // If not hidden, it's visible
        if (!$point['is_hidden']) return true;
        
        // Check if unlocked for user
        $stmt = $this->db->prepare(
            "SELECT 1 FROM user_map_unlocks WHERE user_id = ? AND map_point_id = ?"
        );
        $stmt->bind_param("ii", $userId, $pointId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    /**
     * Unlock a point for a user
     * 
     * @param int $userId
     * @param int $pointId
     * @return bool
     */
    public function unlockForUser($userId, $pointId)
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO user_map_unlocks (user_id, map_point_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $userId, $pointId);
        
        return $stmt->execute();
    }
    
    /**
     * Get unlocked points for user in a map
     * 
     * @param int $mapId
     * @param int $userId
     * @return array
     */
    public function getVisiblePointsForUser($mapId, $userId)
    {
        $stmt = $this->db->prepare(
            "SELECT mp.* 
             FROM map_points mp
             LEFT JOIN user_map_unlocks umu ON mp.id = umu.map_point_id AND umu.user_id = ?
             WHERE mp.map_id = ? 
             AND (mp.is_hidden = 0 OR umu.user_id IS NOT NULL)
             ORDER BY mp.created_at DESC"
        );
        $stmt->bind_param("ii", $userId, $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
