<?php

namespace App\Controllers;
use App\Config\Database;
use App\Models\NPC;
use App\Models\DialogueTree;
use App\Models\Faction;
use App\Models\Quest;
use App\Services\LoggerService;

class AdminNPCController
{
    private $npcModel;
    private $dialogueModel;
    private $questModel;

    public function __construct()
    {
        $this->npcModel = new NPC();
        $this->dialogueModel = new DialogueTree();
        $this->questModel = new Quest();
    }

    /**
     * List all NPCs
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? '';

        $npcs = $this->npcModel->getAll();

        if ($search || $roleFilter) {
            $npcs = array_filter($npcs, function ($npc) use ($search, $roleFilter) {
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

            $factionModel = new Faction();
            $factions = $factionModel->getAll();

            require_once __DIR__ . '/../Views/admin/npcs/create.php';
            return;
        }

        $texturePath = null;

        // Debug file upload
        error_log("=== NPC Image Upload Debug ===");
        error_log("FILES array: " . print_r($_FILES, true));

        if (isset($_FILES['texture'])) {
            error_log("File upload error code: " . $_FILES['texture']['error']);
            error_log("File name: " . $_FILES['texture']['name']);
            error_log("File size: " . $_FILES['texture']['size']);
            error_log("Temp name: " . $_FILES['texture']['tmp_name']);
        }

        if (isset($_FILES['texture']) && $_FILES['texture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/npcs/';
            error_log("Upload directory: " . $uploadDir);
            error_log("Upload directory exists: " . (file_exists($uploadDir) ? 'YES' : 'NO'));

            if (!file_exists($uploadDir)) {
                $created = mkdir($uploadDir, 0755, true);
                error_log("Directory created: " . ($created ? 'YES' : 'NO'));
            }

            $fileExtension = strtolower(pathinfo($_FILES['texture']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
            error_log("File extension: " . $fileExtension);
            error_log("Extension allowed: " . (in_array($fileExtension, $allowedExtensions) ? 'YES' : 'NO'));

            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('npc_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                error_log("Target path: " . $targetPath);
                error_log("Temp file exists: " . (file_exists($_FILES['texture']['tmp_name']) ? 'YES' : 'NO'));

                if (move_uploaded_file($_FILES['texture']['tmp_name'], $targetPath)) {
                    $texturePath = 'assets/npcs/' . $fileName;
                    error_log("File uploaded successfully: " . $texturePath);
                } else {
                    error_log("ERROR: move_uploaded_file failed!");
                    error_log("Last error: " . print_r(error_get_last(), true));
                }
            } else {
                error_log("ERROR: Invalid file extension");
            }
        } elseif (isset($_FILES['texture'])) {
            error_log("ERROR: File upload error - " . $_FILES['texture']['error']);
        }

        $rolesArr = $_POST['roles'] ?? (isset($_POST['role']) ? [$_POST['role']] : []);
        $rolesStr = implode(',', array_filter(array_map('trim', $rolesArr)));

        if (empty($rolesStr)) {
            $rolesStr = 'npc';
        }

        $data = [
            'name' => $_POST['name'],
            'role' => $rolesStr,
            'faction_id' => !empty($_POST['faction_id']) ? $_POST['faction_id'] : null,
            'texture' => $texturePath,
            'merchant_seed' => $_POST['merchant_seed'] ?? null,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];

        $npcId = $this->npcModel->create($data);

        if ($npcId && in_array('merchant', explode(',', $data['role'])) && !empty($_POST['merchant_seed'])) {
            $inventory = $this->npcModel->generateMerchantInventory($_POST['merchant_seed']);
            $this->npcModel->saveMerchantInventory($npcId, $inventory);
        }

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


            $factionModel = new Faction();
            $factions = $factionModel->getAll();


            $allQuests = $this->questModel->getAll();
            $assignedQuests = $this->npcModel->getQuests($id);

            require_once __DIR__ . '/../Views/admin/npcs/edit.php';
            return;
        }

        $texturePath = $npc['texture'];

        // Debug file upload
        error_log("=== NPC Image Upload Debug (EDIT) ===");
        error_log("FILES array: " . print_r($_FILES, true));

        if (isset($_FILES['texture'])) {
            error_log("File upload error code: " . $_FILES['texture']['error']);
            error_log("File name: " . $_FILES['texture']['name']);
            error_log("File size: " . $_FILES['texture']['size']);
            error_log("Temp name: " . $_FILES['texture']['tmp_name']);
        }

        if (isset($_FILES['texture']) && $_FILES['texture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/npcs/';
            error_log("Upload directory: " . $uploadDir);
            error_log("Upload directory exists: " . (file_exists($uploadDir) ? 'YES' : 'NO'));

            if (!file_exists($uploadDir)) {
                $created = mkdir($uploadDir, 0755, true);
                error_log("Directory created: " . ($created ? 'YES' : 'NO'));
            }

            $fileExtension = strtolower(pathinfo($_FILES['texture']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
            error_log("File extension: " . $fileExtension);
            error_log("Extension allowed: " . (in_array($fileExtension, $allowedExtensions) ? 'YES' : 'NO'));

            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('npc_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                error_log("Target path: " . $targetPath);
                error_log("Temp file exists: " . (file_exists($_FILES['texture']['tmp_name']) ? 'YES' : 'NO'));

                if (move_uploaded_file($_FILES['texture']['tmp_name'], $targetPath)) {
                    if ($npc['texture'] && file_exists(__DIR__ . '/../../public/' . $npc['texture'])) {
                        unlink(__DIR__ . '/../../public/' . $npc['texture']);
                    }
                    $texturePath = 'assets/npcs/' . $fileName;
                    error_log("File uploaded successfully: " . $texturePath);
                } else {
                    error_log("ERROR: move_uploaded_file failed!");
                    error_log("Last error: " . print_r(error_get_last(), true));
                }
            } else {
                error_log("ERROR: Invalid file extension: " . $fileExtension);
            }
        } elseif (isset($_FILES['texture'])) {
            error_log("ERROR: File upload error - " . $_FILES['texture']['error']);
        }

        $rolesArr = $_POST['roles'] ?? (isset($_POST['role']) ? [$_POST['role']] : []);
        $rolesStr = implode(',', array_filter(array_map('trim', $rolesArr)));

        if (empty($rolesStr)) {
            $rolesStr = 'npc';
        }

        $data = [
            'name' => $_POST['name'],
            'role' => $rolesStr,
            'faction_id' => !empty($_POST['faction_id']) ? $_POST['faction_id'] : null,
            'texture' => $texturePath,
            'merchant_seed' => $_POST['merchant_seed'] ?? null,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];

        $this->npcModel->update($id, $data);

        $existing = $this->npcModel->getDialogueTrees($id);
        $existingIds = array_column($existing, 'id');
        $newIds = $_POST['dialogue_trees'] ?? [];

        foreach ($existingIds as $eid) {
            if (!in_array($eid, $newIds)) {
                $this->npcModel->removeDialogueTree($id, $eid);
            }
        }

        foreach ($newIds as $nid) {
            if (!in_array($nid, $existingIds)) {
                $this->npcModel->assignDialogueTree($id, $nid);
            }
        }

        $logger = new LoggerService();
        $logger->logCritical($_SESSION['user_id'], 'ADMIN_NPC_UPDATE', [
            'npc_id' => $id,
            'name' => $data['name'],
            'role' => $data['role']
        ]);

        $existingQuests = $this->npcModel->getQuests($id);
        $existingQuestIds = array_column($existingQuests, 'id');
        $newQuestIds = $_POST['quests'] ?? [];

        foreach ($existingQuestIds as $qid) {
            if (!in_array($qid, $newQuestIds)) {
                $this->npcModel->removeQuest($id, $qid);
            }
        }

        foreach ($newQuestIds as $qid) {
            if (!in_array($qid, $existingQuestIds)) {
                $this->npcModel->assignQuest($id, $qid);
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
