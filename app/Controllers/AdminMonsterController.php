<?php

namespace App\Controllers;

use App\Models\Monster;

class AdminMonsterController
{
    private $monsterModel;

    public function __construct()
    {
        $this->monsterModel = new Monster();
    }

    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    public function index()
    {
        $monsters = $this->monsterModel->getAll();
        $this->render('admin/monsters/index', ['monsters' => $monsters]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stats = [
                'strength' => $_POST['strength'] ?? 10,
                'dexterity' => $_POST['dexterity'] ?? 10,
                'intelligence' => $_POST['intelligence'] ?? 10,
                'vitality' => $_POST['vitality'] ?? 10,
                'attaque' => $_POST['attaque'] ?? 10,
                'defense' => $_POST['defense'] ?? 10,
                'xp' => $_POST['xp'] ?? 0,
                'gold' => $_POST['gold'] ?? 0
            ];

            $data = [
                'name' => $_POST['name'],
                'image_path' => $_POST['image_path'],
                'salle_path' => $_POST['salle_path'],
                'level_min' => $_POST['level_min'],
                'level_max' => $_POST['level_max'],
                'stats' => $stats
            ];

            if ($this->monsterModel->create($data)) {
                header('Location: /admin/monsters');
                exit;
            }
        }
        
        $this->render('admin/monsters/create');
    }

    public function edit($id)
    {
        $monster = $this->monsterModel->findById($id);
        if (!$monster) {
            header('Location: /admin/monsters');
            exit;
        }
        
        // Decode stats for the view
        if (!empty($monster['base_stats_json'])) {
            $monster['stats'] = json_decode($monster['base_stats_json'], true);
        } else {
            $monster['stats'] = [];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stats = [
                'strength' => $_POST['strength'] ?? 10,
                'dexterity' => $_POST['dexterity'] ?? 10,
                'intelligence' => $_POST['intelligence'] ?? 10,
                'vitality' => $_POST['vitality'] ?? 10,
                'attaque' => $_POST['attaque'] ?? 10,
                'defense' => $_POST['defense'] ?? 10,
                'xp' => $_POST['xp'] ?? 0,
                'gold' => $_POST['gold'] ?? 0
            ];

            $data = [
                'name' => $_POST['name'],
                'image_path' => $_POST['image_path'],
                'salle_path' => $_POST['salle_path'],
                'level_min' => $_POST['level_min'],
                'level_max' => $_POST['level_max'],
                'stats' => $stats
            ];

            if ($this->monsterModel->update($id, $data)) {
                header('Location: /admin/monsters');
                exit;
            }
        }
        
        $this->render('admin/monsters/edit', ['monster' => $monster]);
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->monsterModel->delete($id);
            header('Location: /admin/monsters');
            exit;
        }
    }
}
