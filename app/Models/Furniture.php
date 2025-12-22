<?php

namespace App\Models;

use App\Config\Database;

class Furniture
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère tous les meubles disponibles
     */
    public function getAvailable($characterLevel = 1)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT f.*, fc.name as category_name, fc.icon as category_icon
                FROM furniture f
                LEFT JOIN furniture_categories fc ON f.category_id = fc.id
                WHERE f.is_available = 1 AND f.required_level <= ?
                ORDER BY fc.sort_order ASC, f.price ASC
            ");
            if (!$stmt) return [];
            $stmt->bind_param("i", $characterLevel);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Récupère tous les meubles (admin)
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT f.*, fc.name as category_name
            FROM furniture f
            LEFT JOIN furniture_categories fc ON f.category_id = fc.id
            ORDER BY fc.sort_order ASC, f.price ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère un meuble par son ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT f.*, fc.name as category_name
            FROM furniture f
            LEFT JOIN furniture_categories fc ON f.category_id = fc.id
            WHERE f.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Récupère toutes les catégories
     */
    public function getCategories()
    {
        $stmt = $this->db->prepare("SELECT * FROM furniture_categories ORDER BY sort_order ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Crée un nouveau meuble (admin)
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO furniture (category_id, name, description, price, icon, image, bonus_type, bonus_value, is_available, required_level, rarity)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ississsiiss",
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['icon'],
            $data['image'],
            $data['bonus_type'],
            $data['bonus_value'],
            $data['is_available'],
            $data['required_level'],
            $data['rarity']
        );
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    /**
     * Met à jour un meuble (admin)
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE furniture SET 
                category_id = ?, name = ?, description = ?, price = ?, 
                icon = ?, image = ?, bonus_type = ?, bonus_value = ?,
                is_available = ?, required_level = ?, rarity = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "ississsiissi",
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['icon'],
            $data['image'],
            $data['bonus_type'],
            $data['bonus_value'],
            $data['is_available'],
            $data['required_level'],
            $data['rarity'],
            $id
        );
        return $stmt->execute();
    }

    /**
     * Supprime un meuble (admin)
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM furniture WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Récupère les meubles placés dans une maison
     */
    public function getHouseFurniture($characterHouseId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT chf.*, f.name, f.description, f.icon, f.image, f.bonus_type, f.bonus_value, f.rarity,
                       fc.name as category_name, fc.icon as category_icon
                FROM character_house_furniture chf
                JOIN furniture f ON chf.furniture_id = f.id
                LEFT JOIN furniture_categories fc ON f.category_id = fc.id
                WHERE chf.character_house_id = ?
                ORDER BY chf.placed_at ASC
            ");
            if (!$stmt) return [];
            $stmt->bind_param("i", $characterHouseId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Achète et place un meuble dans une maison
     */
    public function purchaseFurniture($characterId, $characterHouseId, $furnitureId)
    {
        // Vérifier le meuble
        $furniture = $this->findById($furnitureId);
        if (!$furniture) {
            return ['success' => false, 'message' => 'Meuble introuvable'];
        }

        // Vérifier la maison et les slots disponibles
        $stmt = $this->db->prepare("
            SELECT ch.id, h.furniture_slots,
                   (SELECT COUNT(*) FROM character_house_furniture WHERE character_house_id = ch.id) as current_furniture
            FROM character_houses ch
            JOIN houses h ON ch.house_id = h.id
            WHERE ch.id = ? AND ch.character_id = ?
        ");
        $stmt->bind_param("ii", $characterHouseId, $characterId);
        $stmt->execute();
        $house = $stmt->get_result()->fetch_assoc();

        if (!$house) {
            return ['success' => false, 'message' => 'Maison introuvable'];
        }

        if ($house['current_furniture'] >= $house['furniture_slots']) {
            return ['success' => false, 'message' => 'Plus de place pour de nouveaux meubles'];
        }

        // Vérifier l'or
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        if ($character['gold'] < $furniture['price']) {
            return ['success' => false, 'message' => 'Vous n\'avez pas assez d\'or'];
        }

        // Déduire l'or
        $newGold = $character['gold'] - $furniture['price'];
        $stmt = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("di", $newGold, $characterId);
        $stmt->execute();

        // Placer le meuble
        $stmt = $this->db->prepare("INSERT INTO character_house_furniture (character_house_id, furniture_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $characterHouseId, $furnitureId);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => $furniture['name'] . ' a été ajouté à votre maison !',
                'new_gold' => $newGold
            ];
        }

        return ['success' => false, 'message' => 'Erreur lors de l\'achat'];
    }

    /**
     * Retire un meuble d'une maison (sans remboursement)
     */
    public function removeFurniture($characterId, $characterHouseFurnitureId)
    {
        $stmt = $this->db->prepare("
            DELETE chf FROM character_house_furniture chf
            JOIN character_houses ch ON chf.character_house_id = ch.id
            WHERE chf.id = ? AND ch.character_id = ?
        ");
        $stmt->bind_param("ii", $characterHouseFurnitureId, $characterId);
        return $stmt->execute();
    }

    /**
     * Vend un meuble (remboursement partiel)
     */
    public function sellFurniture($characterId, $characterHouseFurnitureId)
    {
        // Récupérer les infos du meuble
        $stmt = $this->db->prepare("
            SELECT chf.*, f.price, f.name
            FROM character_house_furniture chf
            JOIN furniture f ON chf.furniture_id = f.id
            JOIN character_houses ch ON chf.character_house_id = ch.id
            WHERE chf.id = ? AND ch.character_id = ?
        ");
        $stmt->bind_param("ii", $characterHouseFurnitureId, $characterId);
        $stmt->execute();
        $furnitureData = $stmt->get_result()->fetch_assoc();

        if (!$furnitureData) {
            return ['success' => false, 'message' => 'Meuble introuvable'];
        }

        // Calculer le prix de revente (50%)
        $sellPrice = floor($furnitureData['price'] * 0.5);

        // Ajouter l'or
        $stmt = $this->db->prepare("UPDATE characters SET gold = gold + ? WHERE id = ?");
        $stmt->bind_param("ii", $sellPrice, $characterId);
        $stmt->execute();

        // Supprimer le meuble
        $stmt = $this->db->prepare("DELETE FROM character_house_furniture WHERE id = ?");
        $stmt->bind_param("i", $characterHouseFurnitureId);
        $stmt->execute();

        // Récupérer le nouvel or
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $newGold = $stmt->get_result()->fetch_assoc()['gold'];

        return [
            'success' => true,
            'message' => $furnitureData['name'] . ' vendu pour ' . $sellPrice . ' or',
            'new_gold' => $newGold,
            'sell_price' => $sellPrice
        ];
    }
}
