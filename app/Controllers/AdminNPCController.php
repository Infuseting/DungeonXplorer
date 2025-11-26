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
                if (empty($roleFilter)) {
                    $matchRole = true;
                } else {
                    $roles = array_map('trim', explode(',', $npc['role'] ?? ''));
                    $matchRole = in_array($roleFilter, $roles);
                }
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
        
        // Support multiple roles (array -> CSV)
        $rolesArr = $_POST['roles'] ?? (isset($_POST['role']) ? [$_POST['role']] : []);
        $rolesStr = implode(',', array_filter(array_map('trim', $rolesArr)));

        $data = [
            'name' => $_POST['name'],
            'role' => $rolesStr,
            'texture' => $texturePath,
            'merchant_seed' => $_POST['merchant_seed'] ?? null,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];
        
        $npcId = $this->npcModel->create($data);

        // If merchant role included, generate inventory
        if ($npcId && in_array('merchant', explode(',', $data['role'])) && !empty($_POST['merchant_seed'])) {
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
        
        // Support multiple roles
        $rolesArr = $_POST['roles'] ?? (isset($_POST['role']) ? [$_POST['role']] : []);
        $rolesStr = implode(',', array_filter(array_map('trim', $rolesArr)));

        $data = [
            'name' => $_POST['name'],
            'role' => $rolesStr,
            'texture' => $texturePath,
            'merchant_seed' => $_POST['merchant_seed'] ?? null,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];

        $this->npcModel->update($id, $data);

        // Update dialogue assignments
        $existing = $this->npcModel->getDialogueTrees($id);
        $existingIds = array_column($existing, 'id');
        $newIds = $_POST['dialogue_trees'] ?? [];

        // Remove deselected
        foreach ($existingIds as $eid) {
            if (!in_array($eid, $newIds)) {
                $this->npcModel->removeDialogueTree($id, $eid);
            }
        }

        // Add newly selected
        foreach ($newIds as $nid) {
            if (!in_array($nid, $existingIds)) {
                $this->npcModel->assignDialogueTree($id, $nid);
            }
        }

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
