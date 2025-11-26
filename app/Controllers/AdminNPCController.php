<?php

namespace App\Controllers;

class AdminNPCController
{
    private $npcModel;
    private $dialogueModel;
    
    public function __construct()
    {
        $this->npcModel = new \App\Models\NPC();
        $this->dialogueModel = new \App\Models\DialogueTree();
    }
    
    /**
     * List all NPCs
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? '';
        
        $npcs = $this->npcModel->getAll();
        
        // Apply filters
        if ($search || $roleFilter) {
            $npcs = array_filter($npcs, function($npc) use ($search, $roleFilter) {
                $matchSearch = empty($search) || stripos($npc['name'], $search) !== false;
                $matchRole = empty($roleFilter) || $npc['role'] === $roleFilter;
                return $matchSearch && $matchRole;
            });
        }
        
        require_once __DIR__ . '/../Views/admin/npcs/index.php';
    }
    
    /**
     * Show create form
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $dialogueTrees = $this->dialogueModel->getAll();
            require_once __DIR__ . '/../Views/admin/npcs/create.php';
            return;
        }
        
        // POST - Create NPC
        $texturePath = null;
        
        // Handle texture upload
        if (isset($_FILES['texture']) && $_FILES['texture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/npcs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['texture']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('npc_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['texture']['tmp_name'], $targetPath)) {
                    $texturePath = 'assets/npcs/' . $fileName;
                }
            }
        }
        
        $data = [
            'name' => $_POST['name'],
            'role' => $_POST['role'],
            'image_path' => $_POST['image_path'] ?? null,
            'texture' => $texturePath,
            'merchant_seed' => $_POST['merchant_seed'] ?? null,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];
        
        $npcId = $this->npcModel->create($data);
        
        if ($npcId && $_POST['role'] === 'merchant' && !empty($_POST['merchant_seed'])) {
            // Generate and save merchant inventory
            $inventory = $this->npcModel->generateMerchantInventory($_POST['merchant_seed']);
            $this->npcModel->saveMerchantInventory($npcId, $inventory);
        }
        
        // Assign dialogue trees
        if (!empty($_POST['dialogue_trees'])) {
            foreach ($_POST['dialogue_trees'] as $treeId) {
                $this->npcModel->assignDialogueTree($npcId, $treeId);
            }
        }
        
        header('Location: /admin/npcs?success=created');
        exit;
    }
    
    /**
     * Show edit form
     */
    public function edit($id)
    {
        $npc = $this->npcModel->findById($id);
        if (!$npc) {
            header('Location: /admin/npcs?error=notfound');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $dialogueTrees = $this->dialogueModel->getAll();
            $assignedTrees = $this->npcModel->getDialogueTrees($id);
            $merchantInventory = $this->npcModel->getMerchantInventory($id);
            
            require_once __DIR__ . '/../Views/admin/npcs/edit.php';
            return;
        }
        
        // POST - Update NPC
        $texturePath = $npc['texture'];
        
        // Handle texture upload
        if (isset($_FILES['texture']) && $_FILES['texture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/npcs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['texture']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('npc_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['texture']['tmp_name'], $targetPath)) {
                    // Delete old texture
                    if ($npc['texture'] && file_exists(__DIR__ . '/../../public/' . $npc['texture'])) {
                        unlink(__DIR__ . '/../../public/' . $npc['texture']);
                    }
                    $texturePath = 'assets/npcs/' . $fileName;
                }
            }
        }
        
        $data = [
            'name' => $_POST['name'],
            'role' => $_POST['role'],
            'image_path' => $_POST['image_path'] ?? null,
            'texture' => $texturePath,
            'merchant_seed' => $_POST['merchant_seed'] ?? null,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];
        
        $this->npcModel->update($id, $data);
        
        header('Location: /admin/npcs?success=updated');
        exit;
    }
    
    /**
     * Delete NPC
     */
    public function delete($id)
    {
        $this->npcModel->delete($id);
        header('Location: /admin/npcs?success=deleted');
        exit;
    }
    
    /**
     * Regenerate merchant inventory
     */
    public function regenerateInventory($id)
    {
        $npc = $this->npcModel->findById($id);
        if ($npc && $npc['merchant_seed']) {
            $inventory = $this->npcModel->generateMerchantInventory($npc['merchant_seed']);
            $this->npcModel->saveMerchantInventory($id, $inventory);
        }
        
        header('Location: /admin/npcs/edit/' . $id . '?success=regenerated');
        exit;
    }
}
