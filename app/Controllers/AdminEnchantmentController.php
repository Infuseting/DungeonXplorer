<?php
namespace App\Controllers;

use App\Models\Enchantment;
use App\Services\LoggerService;

class AdminEnchantmentController
{
    private $enchantmentModel;

    public function __construct()
    {
        $this->enchantmentModel = new Enchantment();
    }

    public function index()
    {
        $enchantments = $this->enchantmentModel->getAll();
        require_once __DIR__ . '/../Views/admin/enchantments/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'icon' => $_POST['icon'],
                'stat_modifiers' => $_POST['stat_modifiers'],
                'compatible_slot_types' => $_POST['compatible_slot_types'],
                'cost' => (int) $_POST['cost'],
                'required_level' => (int) $_POST['required_level'],
                'rarity' => $_POST['rarity'] ?: 'common',
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];

            $id = $this->enchantmentModel->create($data);
            if ($id) {
                $logger = new LoggerService();
                $logger->logCritical($_SESSION['user_id'], 'ADMIN_ENCHANTMENT_CREATE', [
                    'enchantment_id' => $id,
                    'name' => $data['name']
                ]);
                header('Location: /admin/enchantments?success=created');
                exit;
            }
        }
        // For simplicity, we use a modal in index, so create might just redirect
        header('Location: /admin/enchantments');
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'icon' => $_POST['icon'],
                'stat_modifiers' => $_POST['stat_modifiers'],
                'compatible_slot_types' => $_POST['compatible_slot_types'],
                'cost' => (int) $_POST['cost'],
                'required_level' => (int) $_POST['required_level'],
                'rarity' => $_POST['rarity'],
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];

            if ($this->enchantmentModel->update($id, $data)) {
                $logger = new LoggerService();
                $logger->logCritical($_SESSION['user_id'], 'ADMIN_ENCHANTMENT_UPDATE', [
                    'enchantment_id' => $id,
                    'name' => $data['name']
                ]);
                header('Location: /admin/enchantments?success=updated');
                exit;
            }
        }
    }

    public function delete($id)
    {
        if ($this->enchantmentModel->delete($id)) {
            $logger = new LoggerService();
            $logger->logCritical($_SESSION['user_id'], 'ADMIN_ENCHANTMENT_DELETE', ['enchantment_id' => $id]);
            header('Location: /admin/enchantments?success=deleted');
            exit;
        }
        header('Location: /admin/enchantments?error=delete_failed');
    }
}
