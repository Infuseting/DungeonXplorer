<?php

namespace App\Controllers;

use App\Models\Faction;

class AdminFactionController
{
    private $factionModel;

    public function __construct()
    {
        $this->factionModel = new Faction();
    }

    public function index()
    {
        $factions = $this->factionModel->getAll();
        require __DIR__ . '/../Views/admin/factions/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            if ($this->factionModel->create($data)) {
                header('Location: /admin/factions');
                exit;
            }
        }
        require __DIR__ . '/../Views/admin/factions/create.php';
    }

    public function edit($id)
    {
        $faction = $this->factionModel->findById($id);
        if (!$faction) {
            header('Location: /admin/factions');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            if ($this->factionModel->update($id, $data)) {
                header('Location: /admin/factions');
                exit;
            }
        }
        require __DIR__ . '/../Views/admin/factions/edit.php';
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->factionModel->delete($id);
            header('Location: /admin/factions');
            exit;
        }
    }
}
