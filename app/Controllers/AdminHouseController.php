<?php

namespace App\Controllers;

use App\Models\House;
use App\Models\Furniture;

class AdminHouseController
{
    /**
     * Liste des maisons (admin)
     */
    public function index()
    {
        $houseModel = new House();
        $houses = $houseModel->getAll();

        require __DIR__ . '/../Views/admin/houses/index.php';
    }

    /**
     * Formulaire de création de maison
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $houseModel = new House();
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => (int)($_POST['price'] ?? 0),
                'storage_slots' => (int)($_POST['storage_slots'] ?? 20),
                'furniture_slots' => (int)($_POST['furniture_slots'] ?? 5),
                'image' => $_POST['image'] ?? null,
                'location_name' => $_POST['location_name'] ?? '',
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'required_level' => (int)($_POST['required_level'] ?? 1)
            ];

            if ($houseModel->create($data)) {
                header('Location: /admin/houses?success=created');
                exit;
            }
            $error = 'Erreur lors de la création';
        }

        require __DIR__ . '/../Views/admin/houses/create.php';
    }

    /**
     * Formulaire d'édition de maison
     */
    public function edit($id)
    {
        $houseModel = new House();
        $house = $houseModel->findById($id);

        if (!$house) {
            header('Location: /admin/houses?error=notfound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => (int)($_POST['price'] ?? 0),
                'storage_slots' => (int)($_POST['storage_slots'] ?? 20),
                'furniture_slots' => (int)($_POST['furniture_slots'] ?? 5),
                'image' => $_POST['image'] ?? null,
                'location_name' => $_POST['location_name'] ?? '',
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'required_level' => (int)($_POST['required_level'] ?? 1)
            ];

            if ($houseModel->update($id, $data)) {
                header('Location: /admin/houses?success=updated');
                exit;
            }
            $error = 'Erreur lors de la mise à jour';
        }

        require __DIR__ . '/../Views/admin/houses/edit.php';
    }

    /**
     * Suppression de maison
     */
    public function delete($id)
    {
        $houseModel = new House();
        if ($houseModel->delete($id)) {
            header('Location: /admin/houses?success=deleted');
        } else {
            header('Location: /admin/houses?error=deletefailed');
        }
        exit;
    }

    /**
     * Liste des meubles (admin)
     */
    public function furnitureIndex()
    {
        $furnitureModel = new Furniture();
        $furniture = $furnitureModel->getAll();
        $categories = $furnitureModel->getCategories();

        require __DIR__ . '/../Views/admin/furniture/index.php';
    }

    /**
     * Formulaire de création de meuble
     */
    public function furnitureCreate()
    {
        $furnitureModel = new Furniture();
        $categories = $furnitureModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => (int)($_POST['price'] ?? 0),
                'icon' => $_POST['icon'] ?? '🪑',
                'image' => $_POST['image'] ?? null,
                'bonus_type' => $_POST['bonus_type'] ?? 'none',
                'bonus_value' => (int)($_POST['bonus_value'] ?? 0),
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'required_level' => (int)($_POST['required_level'] ?? 1),
                'rarity' => $_POST['rarity'] ?? 'common'
            ];

            if ($furnitureModel->create($data)) {
                header('Location: /admin/furniture?success=created');
                exit;
            }
            $error = 'Erreur lors de la création';
        }

        require __DIR__ . '/../Views/admin/furniture/create.php';
    }

    /**
     * Formulaire d'édition de meuble
     */
    public function furnitureEdit($id)
    {
        $furnitureModel = new Furniture();
        $furniture = $furnitureModel->findById($id);
        $categories = $furnitureModel->getCategories();

        if (!$furniture) {
            header('Location: /admin/furniture?error=notfound');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => (int)($_POST['price'] ?? 0),
                'icon' => $_POST['icon'] ?? '🪑',
                'image' => $_POST['image'] ?? null,
                'bonus_type' => $_POST['bonus_type'] ?? 'none',
                'bonus_value' => (int)($_POST['bonus_value'] ?? 0),
                'is_available' => isset($_POST['is_available']) ? 1 : 0,
                'required_level' => (int)($_POST['required_level'] ?? 1),
                'rarity' => $_POST['rarity'] ?? 'common'
            ];

            if ($furnitureModel->update($id, $data)) {
                header('Location: /admin/furniture?success=updated');
                exit;
            }
            $error = 'Erreur lors de la mise à jour';
        }

        require __DIR__ . '/../Views/admin/furniture/edit.php';
    }

    /**
     * Suppression de meuble
     */
    public function furnitureDelete($id)
    {
        $furnitureModel = new Furniture();
        if ($furnitureModel->delete($id)) {
            header('Location: /admin/furniture?success=deleted');
        } else {
            header('Location: /admin/furniture?error=deletefailed');
        }
        exit;
    }
}
