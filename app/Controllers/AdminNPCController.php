<?php

namespace App\Controllers;
use App\Config\Database;
use App\Models\NPC;
use App\Models\DialogueTree;
use App\Models\Faction;
use App\Models\Quest;
use App\Models\Item;
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

            $itemModel = new Item();
            $items = $itemModel->getAll();

            require_once __DIR__ . '/../Views/admin/npcs/create.php';
            return;
        }

        $texturePath = null;

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

        $rolesArr = $_POST['roles'] ?? (isset($_POST['role']) ? [$_POST['role']] : []);
        $rolesStr = implode(',', array_filter(array_map('trim', $rolesArr)));

        if (empty($rolesStr)) {
            $rolesStr = 'npc';
        }

        $merchantSeed = null;
        if (in_array('merchant', $rolesArr)) {
            $inventoryType = $_POST['inventory_type'] ?? 'seed';
            if ($inventoryType === 'seed' && !empty($_POST['merchant_seed'])) {
                $merchantSeed = $_POST['merchant_seed'];
            }
        }

        $data = [
            'name' => $_POST['name'],
            'role' => $rolesStr,
            'faction_id' => !empty($_POST['faction_id']) ? $_POST['faction_id'] : null,
            'texture' => $texturePath,
            'merchant_seed' => $merchantSeed,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];

        $npcId = $this->npcModel->create($data);

        if ($npcId && in_array('merchant', $rolesArr)) {
            $inventoryType = $_POST['inventory_type'] ?? 'seed';

            if ($inventoryType === 'seed' && !empty($merchantSeed)) {
                $inventory = $this->npcModel->generateMerchantInventory($merchantSeed);
                $this->npcModel->saveMerchantInventory($npcId, $inventory);
            } elseif ($inventoryType === 'manual' && !empty($_POST['manual_items'])) {
                // Prepare items array for saveMerchantInventory
                // It expects array of items, each having 'id'
                $itemsToSave = [];
                foreach ($_POST['manual_items'] as $itemId) {
                    $itemsToSave[] = ['id' => $itemId];
                }
                $this->npcModel->saveMerchantInventory($npcId, $itemsToSave);
            }
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

            $itemModel = new Item();
            $allItems = $itemModel->getAll();

            $allQuests = $this->questModel->getAll();
            $assignedQuests = $this->npcModel->getQuests($id);

            require_once __DIR__ . '/../Views/admin/npcs/edit.php';
            return;
        }

        $texturePath = $npc['texture'];

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
                    if ($npc['texture'] && file_exists(__DIR__ . '/../../public/' . $npc['texture'])) {
                        unlink(__DIR__ . '/../../public/' . $npc['texture']);
                    }
                    $texturePath = 'assets/npcs/' . $fileName;
                }
            }
        }

        $rolesArr = $_POST['roles'] ?? (isset($_POST['role']) ? [$_POST['role']] : []);
        $rolesStr = implode(',', array_filter(array_map('trim', $rolesArr)));

        if (empty($rolesStr)) {
            $rolesStr = 'npc';
        }

        $merchantSeed = $npc['merchant_seed']; // Default to existing
        if (in_array('merchant', $rolesArr)) {
            $inventoryType = $_POST['inventory_type'] ?? 'seed';
            if ($inventoryType === 'seed') {
                $merchantSeed = $_POST['merchant_seed'] ?? null;
            } else {
                $merchantSeed = null; // Clear seed if manual
            }
        }

        $data = [
            'name' => $_POST['name'],
            'role' => $rolesStr,
            'faction_id' => !empty($_POST['faction_id']) ? $_POST['faction_id'] : null,
            'texture' => $texturePath,
            'merchant_seed' => $merchantSeed,
            'buy_rate_own' => $_POST['buy_rate_own'] ?? 0.05,
            'buy_rate_other' => $_POST['buy_rate_other'] ?? 0.15
        ];

        $this->npcModel->update($id, $data);

        // Handle Inventory Updates if Merchant
        if (in_array('merchant', $rolesArr)) {
            $inventoryType = $_POST['inventory_type'] ?? 'seed';

            if ($inventoryType === 'manual') {
                // Save keys irrespective of seed change, because user might just be updating the list
                $itemsToSave = [];
                if (!empty($_POST['manual_items'])) {
                    foreach ($_POST['manual_items'] as $itemId) {
                        $itemsToSave[] = ['id' => $itemId];
                    }
                }
                $this->npcModel->saveMerchantInventory($id, $itemsToSave);
            } elseif ($inventoryType === 'seed' && $merchantSeed != $npc['merchant_seed']) {
                // Only regenerate if seed changed or switched back to seed mode? 
                // Actually, if switched to seed mode, we should regen if the seed is different OR if the inventory was manual before.
                // But checking if it was manual is harder without checking null seed state.
                // Let's just regen if seed is provided.
                if ($merchantSeed) {
                    $inventory = $this->npcModel->generateMerchantInventory($merchantSeed);
                    $this->npcModel->saveMerchantInventory($id, $inventory);
                }
            }
        }

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
