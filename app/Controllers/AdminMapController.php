<?php

namespace App\Controllers;

class AdminMapController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    public function index()
    {
        // Ensure main map exists
        $mainMapId = $this->ensureMainMapExists();
        
        // Get all maps
        $maps = $this->getAllMaps();
        
        // Get map points for the main map
        $mapPoints = $this->getMapPoints($mainMapId);
        
        require_once __DIR__ . '/../Views/admin/map/index.php';
    }
    
    public function createPoint()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ensure main map exists and get its ID
            $mainMapId = $this->ensureMainMapExists();
            
            $data = [
                'map_id' => $_POST['map_id'] ?? $mainMapId,
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'x' => $_POST['x'] ?? 0,
                'y' => $_POST['y'] ?? 0,
                'radius' => $_POST['radius'] ?? 50,
                'type' => $_POST['type'] ?? 'place',
                'target_id' => $_POST['target_id'] ?? null,
                'icon' => $_POST['icon'] ?? null,
            ];
            
            $stmt = $this->db->prepare("
                INSERT INTO map_points (map_id, name, description, x, y, radius, type, target_id, icon)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "issddisis",
                $data['map_id'],
                $data['name'],
                $data['description'],
                $data['x'],
                $data['y'],
                $data['radius'],
                $data['type'],
                $data['target_id'],
                $data['icon']
            );
            
            if ($stmt->execute()) {
                header('Location: /admin/map');
                exit;
            }
        }
    }
    
    public function updatePoint($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'x' => $_POST['x'] ?? 0,
                'y' => $_POST['y'] ?? 0,
                'radius' => $_POST['radius'] ?? 50,
                'type' => $_POST['type'] ?? 'place',
                'target_id' => $_POST['target_id'] ?? null,
            ];
            
            $stmt = $this->db->prepare("
                UPDATE map_points 
                SET name = ?, description = ?, x = ?, y = ?, radius = ?, type = ?, target_id = ?
                WHERE id = ?
            ");
            $stmt->bind_param(
                "ssddisii",
                $data['name'],
                $data['description'],
                $data['x'],
                $data['y'],
                $data['radius'],
                $data['type'],
                $data['target_id'],
                $id
            );
            
            if ($stmt->execute()) {
                header('Location: /admin/map');
                exit;
            }
        }
    }
    
    public function deletePoint($id)
    {
        $stmt = $this->db->prepare("DELETE FROM map_points WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header('Location: /admin/map');
            exit;
        }
    }
    
    private function getAllMaps()
    {
        $result = $this->db->query("SELECT * FROM maps ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    private function getMapPoints($mapId)
    {
        $stmt = $this->db->prepare("SELECT * FROM map_points WHERE map_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    private function ensureMainMapExists()
    {
        // Check if any map exists
        $result = $this->db->query("SELECT id FROM maps ORDER BY id ASC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        
        // No map exists, create the main map
        $stmt = $this->db->prepare("INSERT INTO maps (name, description, image_path) VALUES (?, ?, ?)");
        $name = 'Main World';
        $desc = 'The main game world';
        $path = '/assets/tiles/map_config.json';
        $stmt->bind_param("sss", $name, $desc, $path);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        // Fallback to 1 if something goes wrong
        return 1;
    }
}
