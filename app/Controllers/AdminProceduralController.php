<?php

namespace App\Controllers;

use App\Models\ProceduralTemplate;

class AdminProceduralController
{
    private $templateModel;

    public function __construct()
    {
        $this->templateModel = new ProceduralTemplate();
    }

    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    public function index()
    {
        $templates = $this->templateModel->getAll();
        $this->render('admin/procedural/index', ['templates' => $templates]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'min_rooms' => $_POST['min_rooms'],
                'max_rooms' => $_POST['max_rooms'],
                'connection_density' => $_POST['connection_density'],
                'allow_loops' => isset($_POST['allow_loops']) ? 1 : 0,
                'allow_backtrack' => isset($_POST['allow_backtrack']) ? 1 : 0,
                'difficulty_scaling' => $_POST['difficulty_scaling'],
                'direction_types' => $_POST['direction_types'] ?? ['north', 'south', 'east', 'west'],
                'room_themes' => $_POST['room_themes'] ?? ['dungeon' => 100]
            ];

            if ($this->templateModel->create($data)) {
                header('Location: /admin/procedural');
                exit;
            }
        }
        
        $this->render('admin/procedural/create');
    }

    public function edit($id)
    {
        $template = $this->templateModel->findById($id);
        if (!$template) {
            header('Location: /admin/procedural');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'min_rooms' => $_POST['min_rooms'],
                'max_rooms' => $_POST['max_rooms'],
                'connection_density' => $_POST['connection_density'],
                'allow_loops' => isset($_POST['allow_loops']) ? 1 : 0,
                'allow_backtrack' => isset($_POST['allow_backtrack']) ? 1 : 0,
                'difficulty_scaling' => $_POST['difficulty_scaling'],
                'direction_types' => $_POST['direction_types'] ?? [],
                'room_themes' => $_POST['room_themes'] ?? []
            ];

            if ($this->templateModel->update($id, $data)) {
                header('Location: /admin/procedural');
                exit;
            }
        }

        $this->render('admin/procedural/edit', ['template' => $template]);
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->templateModel->delete($id);
            header('Location: /admin/procedural');
            exit;
        }
    }

        public function monsterPools($id)
    {
        $template = $this->templateModel->findById($id);
        if (!$template) {
            header('Location: /admin/procedural');
            exit;
        }
        $pools = $this->templateModel->getMonsterPools($id);
        $this->render('admin/procedural/monster-pools', ['template' => $template, 'pools' => $pools]);
    }

    public function addMonsterPool($templateId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                $this->templateModel->addMonsterPool($templateId, $_POST);
            header('Location: /admin/procedural/' . $templateId . '/monsters');
            exit;
        }
    }

    public function deleteMonsterPool($poolId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->templateModel->deleteMonsterPool($poolId);
                                    header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

        public function lootPools($id)
    {
        $template = $this->templateModel->findById($id);
        if (!$template) {
            header('Location: /admin/procedural');
            exit;
        }
        $pools = $this->templateModel->getLootPools($id);
                $itemModel = new Item();         $items = $itemModel->getAll();         
        $this->render('admin/procedural/loot-pools', ['template' => $template, 'pools' => $pools, 'items' => $items]);
    }

    public function addLootPool($templateId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->templateModel->addLootPool($templateId, $_POST);
            header('Location: /admin/procedural/' . $templateId . '/loot');
            exit;
        }
    }

    public function deleteLootPool($poolId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->templateModel->deleteLootPool($poolId);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}
