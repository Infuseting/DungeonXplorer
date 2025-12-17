<?php

namespace App\Models;

use App\Config\Database;

class Map
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find a map by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM maps WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get map configuration (tile settings, dimensions, etc.)
     * 
     * @param int $id
     * @return array|null
     */
    public function getMapConfig($id)
    {
        $map = $this->findById($id);
        
        if (!$map) {
            return null;
        }

                return [
            'id' => $map['id'],
            'name' => $map['name'],
            'tile_path' => $map['tile_path'] ?? '/assets/tiles',
            'config_file' => $map['config_file'] ?? '/assets/map/main/map_config.json',
            'created_at' => $map['created_at'],
            'updated_at' => $map['updated_at']
        ];
    }

    /**
     * Get all maps
     * 
     * @return array
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM maps ORDER BY created_at DESC");
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new map
     * 
     * @param array $data
     * @return int|false The new map ID or false on failure
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO maps (name, tile_path, config_file) VALUES (?, ?, ?)"
        );
        
        $stmt->bind_param(
            "sss",
            $data['name'],
            $data['tile_path'],
            $data['config_file']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }

    /**
     * Update a map
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE maps SET name = ?, tile_path = ?, config_file = ?, updated_at = NOW() WHERE id = ?"
        );
        
        $stmt->bind_param(
            "sssi",
            $data['name'],
            $data['tile_path'],
            $data['config_file'],
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a map
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM maps WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    /**
     * Get parent map of a sub-map
     * 
     * @param int $mapId
     * @return array|null
     */
    public function getParentMap($mapId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM maps WHERE id = (SELECT parent_map_id FROM maps WHERE id = ?)"
        );
        $stmt->bind_param("i", $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get all child maps (sub-maps) of a map
     * 
     * @param int $mapId
     * @return array
     */
    public function getChildMaps($mapId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM maps WHERE parent_map_id = ? ORDER BY created_at DESC"
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
     * Check if a map is a sub-map
     * 
     * @param int $mapId
     * @return bool
     */
    public function isSubMap($mapId)
    {
        $stmt = $this->db->prepare(
            "SELECT parent_map_id FROM maps WHERE id = ?"
        );
        $stmt->bind_param("i", $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return !is_null($row['parent_map_id']);
        }
        
        return false;
    }

    /**
     * Get all root maps (maps without parent)
     * 
     * @return array
     */
    public function getRootMaps()
    {
        $result = $this->db->query(
            "SELECT * FROM maps WHERE parent_map_id IS NULL ORDER BY created_at DESC"
        );
        
        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get map hierarchy (parent and all children)
     * 
     * @param int $mapId
     * @return array
     */
    public function getMapHierarchy($mapId)
    {
        $map = $this->findById($mapId);
        if (!$map) {
            return null;
        }

        return [
            'map' => $map,
            'parent' => $this->getParentMap($mapId),
            'children' => $this->getChildMaps($mapId)
        ];
    }
}
