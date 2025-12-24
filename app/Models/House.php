<?php

namespace App\Models;

use App\Config\Database;

class House
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère toutes les maisons disponibles à l'achat
     */
    public function getAvailableHouses($characterLevel = 1)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM houses 
            WHERE is_available = 1 AND required_level <= ?
            ORDER BY price ASC
        ");
        $stmt->bind_param("i", $characterLevel);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère toutes les maisons (admin)
     */
    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM houses ORDER BY price ASC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère une maison par son ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM houses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Crée une nouvelle maison (admin)
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO houses (name, description, price, storage_slots, furniture_slots, image, location_name, is_available, required_level, map_x, map_y)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssiiiisiidd",
            $data['name'],
            $data['description'],
            $data['price'],
            $data['storage_slots'],
            $data['furniture_slots'],
            $data['image'],
            $data['location_name'],
            $data['is_available'],
            $data['required_level'],
            $data['map_x'],
            $data['map_y']
        );
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    /**
     * Met à jour une maison (admin)
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE houses SET 
                name = ?, description = ?, price = ?, storage_slots = ?, 
                furniture_slots = ?, image = ?, location_name = ?, 
                is_available = ?, required_level = ?, map_x = ?, map_y = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "ssiiiisiiddi",
            $data['name'],
            $data['description'],
            $data['price'],
            $data['storage_slots'],
            $data['furniture_slots'],
            $data['image'],
            $data['location_name'],
            $data['is_available'],
            $data['required_level'],
            $data['map_x'],
            $data['map_y'],
            $id
        );
        return $stmt->execute();
    }

    /**
     * Supprime une maison (admin)
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM houses WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Récupère les maisons d'un personnage
     */
    public function getCharacterHouses($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT ch.*, h.name, h.description, h.storage_slots, h.furniture_slots, h.image, h.location_name
            FROM character_houses ch
            JOIN houses h ON ch.house_id = h.id
            WHERE ch.character_id = ?
            ORDER BY ch.is_primary DESC, ch.purchased_at DESC
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère la maison principale d'un personnage
     */
    public function getPrimaryHouse($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT ch.*, h.name, h.description, h.storage_slots, h.furniture_slots, h.image, h.location_name, h.map_x, h.map_y
            FROM character_houses ch
            JOIN houses h ON ch.house_id = h.id
            WHERE ch.character_id = ? AND ch.is_primary = 1
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Achète une maison pour un personnage
     */
    public function purchaseHouse($characterId, $houseId)
    {
        // Vérifier si le personnage possède déjà cette maison
        $stmt = $this->db->prepare("SELECT id FROM character_houses WHERE character_id = ? AND house_id = ?");
        $stmt->bind_param("ii", $characterId, $houseId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Vous possédez déjà cette maison'];
        }

        // Récupérer le prix de la maison
        $house = $this->findById($houseId);
        if (!$house) {
            return ['success' => false, 'message' => 'Maison introuvable'];
        }

        // Vérifier l'or du personnage
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();
        
        if ($character['gold'] < $house['price']) {
            return ['success' => false, 'message' => 'Vous n\'avez pas assez d\'or'];
        }

        // Vérifier si c'est la première maison
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM character_houses WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'];
        $isPrimary = ($count == 0) ? 1 : 0;

        // Déduire l'or
        $newGold = $character['gold'] - $house['price'];
        $stmt = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("di", $newGold, $characterId);
        $stmt->execute();

        // Créer la possession
        $stmt = $this->db->prepare("INSERT INTO character_houses (character_id, house_id, is_primary) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $characterId, $houseId, $isPrimary);
        
        if ($stmt->execute()) {
            return [
                'success' => true, 
                'message' => 'Félicitations ! Vous êtes maintenant propriétaire de ' . $house['name'],
                'new_gold' => $newGold,
                'character_house_id' => $this->db->insert_id
            ];
        }

        return ['success' => false, 'message' => 'Erreur lors de l\'achat'];
    }

    /**
     * Définit une maison comme principale
     */
    public function setPrimaryHouse($characterId, $characterHouseId)
    {
        // Retirer le statut principal des autres maisons
        $stmt = $this->db->prepare("UPDATE character_houses SET is_primary = 0 WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();

        // Définir la nouvelle maison principale
        $stmt = $this->db->prepare("UPDATE character_houses SET is_primary = 1 WHERE id = ? AND character_id = ?");
        $stmt->bind_param("ii", $characterHouseId, $characterId);
        return $stmt->execute();
    }

    /**
     * Renommer une maison
     */
    public function renameHouse($characterHouseId, $characterId, $newName)
    {
        $stmt = $this->db->prepare("UPDATE character_houses SET custom_name = ? WHERE id = ? AND character_id = ?");
        $stmt->bind_param("sii", $newName, $characterHouseId, $characterId);
        return $stmt->execute();
    }

    /**
     * Récupère les détails complets d'une maison possédée
     */
    public function getCharacterHouseDetails($characterHouseId, $characterId)
    {
        $stmt = $this->db->prepare("
            SELECT ch.*, h.name, h.description, h.storage_slots, h.furniture_slots, h.image, h.location_name
            FROM character_houses ch
            JOIN houses h ON ch.house_id = h.id
            WHERE ch.id = ? AND ch.character_id = ?
        ");
        $stmt->bind_param("ii", $characterHouseId, $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Calcule les bonus totaux d'une maison
     */
    public function getHouseBonuses($characterHouseId)
    {
        $stmt = $this->db->prepare("
            SELECT f.bonus_type, SUM(f.bonus_value) as total_bonus
            FROM character_house_furniture chf
            JOIN furniture f ON chf.furniture_id = f.id
            WHERE chf.character_house_id = ?
            GROUP BY f.bonus_type
        ");
        $stmt->bind_param("i", $characterHouseId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $bonuses = [
            'storage' => 0,
            'comfort' => 0,
            'luck' => 0,
            'xp' => 0,
            'gold' => 0,
            'defense' => 0
        ];
        
        foreach ($results as $row) {
            if (isset($bonuses[$row['bonus_type']])) {
                $bonuses[$row['bonus_type']] = (int)$row['total_bonus'];
            }
        }
        
        return $bonuses;
    }
}
