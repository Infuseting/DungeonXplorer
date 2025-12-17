<?php

namespace App\Controllers;
use App\Config\Database;
use App\Models\Faction;
use App\Models\NPC;
use App\Models\Story;

class AdminMapController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index()
    {
                $selectedMapId = isset($_GET['map_id']) ? (int)$_GET['map_id'] : null;
        
                $defaultMapId = $this->ensureMainMapExists();
        
                if (!$selectedMapId) {
            $selectedMapId = $defaultMapId;
        }
        
                $maps = $this->getAllMaps();
        
                $selectedMap = $this->getMapById($selectedMapId);
        
                $mapPoints = $this->getMapPoints($selectedMapId);
        
                $factionModel = new Faction();
        $factions = $factionModel->getAll();

        require_once __DIR__ . '/../Views/admin/map/index.php';
    }

    public function updateMap()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mapId = $_POST['map_id'] ?? null;
            if (!$mapId) {
                header('Location: /admin/map');
                exit;
            }

            $name = $_POST['name'] ?? '';
            $factionId = !empty($_POST['faction_id']) ? $_POST['faction_id'] : null;

            $stmt = $this->db->prepare("UPDATE maps SET name = ?, faction_id = ? WHERE id = ?");
            $stmt->bind_param("sii", $name, $factionId, $mapId);
            $stmt->execute();

            header('Location: /admin/map?map_id=' . $mapId . '&success=updated');
            exit;
        }
    }
    
    public function managePoints()
    {
                $search = $_GET['search'] ?? '';
        $typeFilter = $_GET['type'] ?? '';
        $mapFilter = $_GET['map_id'] ?? '';
        
                $maps = $this->getAllMaps();
        
                $npcModel = new NPC();
        $npcs = $npcModel->getAll();
        
                $storyModel = new Story();
        $stories = $storyModel->getAll();
        
                $query = "SELECT mp.*, m.name as map_name FROM map_points mp 
                  LEFT JOIN maps m ON mp.map_id = m.id WHERE 1=1";
        $params = [];
        $types = '';
        
        if ($search) {
            $query .= " AND mp.name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }
        
        if ($typeFilter) {
            $query .= " AND mp.type = ?";
            $params[] = $typeFilter;
            $types .= 's';
        }
        
        if ($mapFilter) {
            $query .= " AND mp.map_id = ?";
            $params[] = $mapFilter;
            $types .= 'i';
        }
        
        $query .= " ORDER BY mp.created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $points = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->db->query($query);
            $points = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        require_once __DIR__ . '/../Views/admin/points/index.php';
    }
    
    public function updatePointSubMap()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $pointId = $data['point_id'] ?? null;
            $subMapId = $data['sub_map_id'] ?? null;
            
            if (!$pointId) {
                echo json_encode(['success' => false, 'message' => 'Point ID manquant']);
                exit;
            }
            
                        $stmt = $this->db->prepare("UPDATE map_points SET sub_map_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $subMapId, $pointId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Sous-carte mise à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        }
        exit;
    }
    
    public function updatePointNPC()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $pointId = $data['point_id'] ?? null;
            $npcId = $data['npc_id'] ?? null;
            
            if (!$pointId) {
                echo json_encode(['success' => false, 'message' => 'Point ID manquant']);
                exit;
            }
            
                        $stmt = $this->db->prepare("UPDATE map_points SET target_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $npcId, $pointId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'PNJ mis à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        }
        exit;
    }

    public function updatePointVisibility()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $pointId = $data['point_id'] ?? null;
            $isHidden = isset($data['is_hidden']) ? (int)$data['is_hidden'] : 0;
            
            if (!$pointId) {
                echo json_encode(['success' => false, 'message' => 'Point ID manquant']);
                exit;
            }
            
            $stmt = $this->db->prepare("UPDATE map_points SET is_hidden = ? WHERE id = ?");
            $stmt->bind_param("ii", $isHidden, $pointId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Visibilité mise à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        }
        exit;
    }
    
    public function updatePointStory()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $pointId = $data['point_id'] ?? null;
            $storyId = $data['story_id'] ?? null;
            
            if (!$pointId) {
                echo json_encode(['success' => false, 'message' => 'Point ID manquant']);
                exit;
            }
            
                        $stmt = $this->db->prepare("UPDATE map_points SET story_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $storyId, $pointId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Histoire/Donjon mis à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        }
        exit;
    }
    
    public function createPoint()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                'is_hidden' => isset($_POST['is_hidden']) ? 1 : 0
            ];
            
            $stmt = $this->db->prepare("
                INSERT INTO map_points (map_id, name, description, x, y, radius, type, target_id, icon, is_hidden)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "issddisisi",
                $data['map_id'],
                $data['name'],
                $data['description'],
                $data['x'],
                $data['y'],
                $data['radius'],
                $data['type'],
                $data['target_id'],
                $data['icon'],
                $data['is_hidden']
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
                'is_hidden' => isset($_POST['is_hidden']) ? 1 : 0
            ];
            
            $stmt = $this->db->prepare("
                UPDATE map_points 
                SET name = ?, description = ?, x = ?, y = ?, radius = ?, type = ?, target_id = ?, is_hidden = ?
                WHERE id = ?
            ");
            $stmt->bind_param(
                "ssddisiii",
                $data['name'],
                $data['description'],
                $data['x'],
                $data['y'],
                $data['radius'],
                $data['type'],
                $data['target_id'],
                $data['is_hidden'],
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
    
    private function getMapById($mapId)
    {
        $stmt = $this->db->prepare("SELECT * FROM maps WHERE id = ?");
        $stmt->bind_param("i", $mapId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    private function ensureMainMapExists()
    {
                $result = $this->db->query("SELECT id FROM maps ORDER BY id ASC LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        
                $stmt = $this->db->prepare("INSERT INTO maps (name, description, image_path) VALUES (?, ?, ?)");
        $name = 'Main World';
        $desc = 'The main game world';
        $path = '/assets/map/main/map_config.json';
        $stmt->bind_param("sss", $name, $desc, $path);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
                return 1;
    }
}
