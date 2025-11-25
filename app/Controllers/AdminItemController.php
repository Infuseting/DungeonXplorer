<?php

namespace App\Controllers;

class AdminItemController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    public function index()
    {
        // Get search/filter parameters
        $search = $_GET['search'] ?? '';
        $typeFilter = $_GET['type'] ?? '';
        $slotFilter = $_GET['slot'] ?? '';
        
        // Build query with filters
        $query = "SELECT * FROM items WHERE 1=1";
        $params = [];
        $types = '';
        
        if ($search) {
            $query .= " AND name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }
        
        if ($typeFilter) {
            $query .= " AND type = ?";
            $params[] = $typeFilter;
            $types .= 's';
        }
        
        if ($slotFilter) {
            $query .= " AND slot_type = ?";
            $params[] = $slotFilter;
            $types .= 's';
        }
        
        $query .= " ORDER BY created_at DESC";
        
        if (!empty($params)) {
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $result = $this->db->query($query);
            $items = $result->fetch_all(MYSQLI_ASSOC);
        }
        
        require_once __DIR__ . '/../Views/admin/items/index.php';
    }
    
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once __DIR__ . '/../Views/admin/items/create.php';
            return;
        }
        
        // POST - Create item
        $iconPath = null;
        
        // Handle image upload
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/items/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('item_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['item_image']['tmp_name'], $targetPath)) {
                    $iconPath = 'assets/items/' . $fileName;
                }
            }
        }
        
        $statRanges = [
            'strength' => [
                'min' => (int)($_POST['strength_min'] ?? 0),
                'max' => (int)($_POST['strength_max'] ?? 0)
            ],
            'vitality' => [
                'min' => (int)($_POST['vitality_min'] ?? 0),
                'max' => (int)($_POST['vitality_max'] ?? 0)
            ],
            'intelligence' => [
                'min' => (int)($_POST['intelligence_min'] ?? 0),
                'max' => (int)($_POST['intelligence_max'] ?? 0)
            ],
            'dexterity' => [
                'min' => (int)($_POST['dexterity_min'] ?? 0),
                'max' => (int)($_POST['dexterity_max'] ?? 0)
            ]
        ];
        
        $stmt = $this->db->prepare("
            INSERT INTO items (name, description, type, slot_type, two_handed, width, height, weight, icon, stat_ranges, max_stack, price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $statRangesJson = json_encode($statRanges);
        $twoHanded = isset($_POST['two_handed']) ? 1 : 0;
        $finalIcon = $iconPath ?? ($_POST['icon'] ?? null);
        
        $stmt->bind_param(
            "ssssiiiiisii",
            $_POST['name'],
            $_POST['description'],
            $_POST['type'],
            $_POST['slot_type'],
            $twoHanded,
            $_POST['width'],
            $_POST['height'],
            $_POST['weight'],
            $finalIcon,
            $statRangesJson,
            $_POST['max_stack'],
            $_POST['price'] ?? null
        );
        
        if ($stmt->execute()) {
            header('Location: /admin/items?success=created');
            exit;
        } else {
            header('Location: /admin/items/create?error=failed');
            exit;
        }
    }
    
    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        
        if (!$item) {
            header('Location: /admin/items?error=notfound');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once __DIR__ . '/../Views/admin/items/edit.php';
            return;
        }
        
        // POST - Update item
        $iconPath = $item['icon']; // Keep existing icon by default
        
        // Handle image upload
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/items/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('item_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['item_image']['tmp_name'], $targetPath)) {
                    // Delete old image if exists
                    if ($item['icon'] && file_exists(__DIR__ . '/../../public/' . $item['icon'])) {
                        unlink(__DIR__ . '/../../public/' . $item['icon']);
                    }
                    $iconPath = 'assets/items/' . $fileName;
                }
            }
        }
        
        $statRanges = [
            'strength' => [
                'min' => (int)($_POST['strength_min'] ?? 0),
                'max' => (int)($_POST['strength_max'] ?? 0)
            ],
            'vitality' => [
                'min' => (int)($_POST['vitality_min'] ?? 0),
                'max' => (int)($_POST['vitality_max'] ?? 0)
            ],
            'intelligence' => [
                'min' => (int)($_POST['intelligence_min'] ?? 0),
                'max' => (int)($_POST['intelligence_max'] ?? 0)
            ],
            'dexterity' => [
                'min' => (int)($_POST['dexterity_min'] ?? 0),
                'max' => (int)($_POST['dexterity_max'] ?? 0)
            ]
        ];
        
        $stmt = $this->db->prepare("
            UPDATE items 
            SET name = ?, description = ?, type = ?, slot_type = ?, two_handed = ?, 
                width = ?, height = ?, weight = ?, icon = ?, stat_ranges = ?, max_stack = ?, price = ?
            WHERE id = ?
        ");
        
        $statRangesJson = json_encode($statRanges);
        $twoHanded = isset($_POST['two_handed']) ? 1 : 0;
        
        $stmt->bind_param(
            "ssssiiiiissii",
            $_POST['name'],
            $_POST['description'],
            $_POST['type'],
            $_POST['slot_type'],
            $twoHanded,
            $_POST['width'],
            $_POST['height'],
            $_POST['weight'],
            $iconPath,
            $statRangesJson,
            $_POST['max_stack'],
            $_POST['price'] ?? null,
            $id
        );
        
        if ($stmt->execute()) {
            header('Location: /admin/items?success=updated');
            exit;
        } else {
            header('Location: /admin/items/edit/' . $id . '?error=failed');
            exit;
        }
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header('Location: /admin/items?success=deleted');
        } else {
            header('Location: /admin/items?error=deletefailed');
        }
        exit;
    }
}
