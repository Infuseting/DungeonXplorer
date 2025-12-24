<?php

namespace App\Models;

use App\Config\Database;

class Workbench
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Vérifie si l'établi est débloqué pour une maison spécifique
     */
    public function hasWorkbenchForHouse($characterHouseId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM character_house_workbenches 
            WHERE character_house_id = ?
        ");
        if (!$stmt) return false;
        $stmt->bind_param("i", $characterHouseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return !empty($result);
    }

    /**
     * Ancien check - compatibilité
     */
    public function hasWorkbench($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT chw.id
            FROM character_house_workbenches chw
            JOIN character_houses ch ON chw.character_house_id = ch.id
            WHERE ch.character_id = ? AND ch.is_primary = 1
            LIMIT 1
        ");
        if (!$stmt) return false;
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return !empty($result);
    }

    /**
     * Récupère le prix de l'établi selon le type de maison (depuis la BDD)
     */
    public function getWorkbenchPrice($houseId)
    {
        $stmt = $this->db->prepare("SELECT workbench_price FROM houses WHERE id = ?");
        if (!$stmt) return 5000;
        $stmt->bind_param("i", $houseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['workbench_price'] ?? 5000;
    }

    /**
     * Récupère le niveau requis selon le type de maison (depuis la BDD)
     */
    public function getWorkbenchRequiredLevel($houseId)
    {
        $stmt = $this->db->prepare("SELECT workbench_required_level FROM houses WHERE id = ?");
        if (!$stmt) return 10;
        $stmt->bind_param("i", $houseId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['workbench_required_level'] ?? 10;
    }

    /**
     * Achète l'établi pour une maison
     */
    public function purchaseWorkbench($characterId, $characterHouseId, $houseId)
    {
        // Vérifier si l'établi existe déjà
        if ($this->hasWorkbenchForHouse($characterHouseId)) {
            return ['success' => false, 'message' => 'Cette maison possède déjà un établi'];
        }

        // Récupérer le prix et niveau requis
        $price = $this->getWorkbenchPrice($houseId);
        $requiredLevel = $this->getWorkbenchRequiredLevel($houseId);

        // Vérifier le niveau du personnage
        $stmt = $this->db->prepare("SELECT level FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $level = $stats['level'] ?? 1;

        if ($level < $requiredLevel) {
            return ['success' => false, 'message' => "Niveau $requiredLevel requis pour cet établi"];
        }

        // Vérifier l'or
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        if ($character['gold'] < $price) {
            return ['success' => false, 'message' => 'Or insuffisant'];
        }

        // Déduire l'or
        $newGold = $character['gold'] - $price;
        $stmt = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("di", $newGold, $characterId);
        $stmt->execute();

        // Créer l'établi
        $stmt = $this->db->prepare("
            INSERT INTO character_house_workbenches (character_house_id, purchased_at)
            VALUES (?, NOW())
        ");
        $stmt->bind_param("i", $characterHouseId);
        $stmt->execute();

        return [
            'success' => true,
            'message' => 'Établi acheté !',
            'new_gold' => $newGold
        ];
    }

    /**
     * Récupère tous les enchantements disponibles
     */
    public function getAvailableEnchantments($characterLevel = 1)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM enchantments
            WHERE is_available = 1 AND required_level <= ?
            ORDER BY rarity, cost ASC
        ");
        if (!$stmt) return [];
        $stmt->bind_param("i", $characterLevel);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupère un enchantement par ID
     */
    public function getEnchantment($enchantmentId)
    {
        $stmt = $this->db->prepare("SELECT * FROM enchantments WHERE id = ?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $enchantmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Récupère les enchantements appliqués à un item
     */
    public function getItemEnchantments($characterInventoryId)
    {
        $stmt = $this->db->prepare("
            SELECT ie.*, e.name, e.description, e.icon, e.stat_modifiers, e.rarity
            FROM item_enchantments ie
            JOIN enchantments e ON ie.enchantment_id = e.id
            WHERE ie.character_inventory_id = ?
        ");
        $stmt->bind_param("i", $characterInventoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Vérifie si un enchantement est compatible avec un item
     */
    public function isEnchantmentCompatible($enchantmentId, $itemSlotType)
    {
        $enchantment = $this->getEnchantment($enchantmentId);
        if (!$enchantment) return false;

        $compatibleSlots = json_decode($enchantment['compatible_slot_types'], true);
        if (!$compatibleSlots) return true; // Si pas de restriction, compatible avec tout

        return in_array($itemSlotType, $compatibleSlots);
    }

    /**
     * Applique un enchantement à un item
     */
    public function applyEnchantment($characterId, $characterInventoryId, $enchantmentId)
    {
        // Vérifier que l'établi est débloqué
        if (!$this->hasWorkbench($characterId)) {
            return ['success' => false, 'message' => 'Vous n\'avez pas d\'établi'];
        }

        // Récupérer l'enchantement
        $enchantment = $this->getEnchantment($enchantmentId);
        if (!$enchantment) {
            return ['success' => false, 'message' => 'Enchantement non trouvé'];
        }

        // Vérifier que l'item appartient au joueur
        $stmt = $this->db->prepare("
            SELECT ci.*, i.slot_type, i.name as item_name
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = ? AND ci.character_id = ?
        ");
        $stmt->bind_param("ii", $characterInventoryId, $characterId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        if (!$item) {
            return ['success' => false, 'message' => 'Item non trouvé'];
        }

        // Vérifier la compatibilité
        if (!$this->isEnchantmentCompatible($enchantmentId, $item['slot_type'])) {
            return ['success' => false, 'message' => 'Cet enchantement n\'est pas compatible avec cet item'];
        }

        // Vérifier si l'item a déjà cet enchantement
        $stmt = $this->db->prepare("
            SELECT id FROM item_enchantments
            WHERE character_inventory_id = ? AND enchantment_id = ?
        ");
        $stmt->bind_param("ii", $characterInventoryId, $enchantmentId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            return ['success' => false, 'message' => 'Cet item possède déjà cet enchantement'];
        }

        // Vérifier l'or du joueur
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        if ($character['gold'] < $enchantment['cost']) {
            return ['success' => false, 'message' => 'Or insuffisant'];
        }

        // Déduire l'or
        $newGold = $character['gold'] - $enchantment['cost'];
        $stmt = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("di", $newGold, $characterId);
        $stmt->execute();

        // Appliquer l'enchantement
        $stmt = $this->db->prepare("
            INSERT INTO item_enchantments (character_inventory_id, enchantment_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $characterInventoryId, $enchantmentId);
        $stmt->execute();

        // Mettre à jour les stats de l'item
        $this->updateItemStats($characterInventoryId);

        // Mettre à jour le compteur d'enchantements (si la table existe)
        $this->incrementEnchantmentCount($characterId);

        return [
            'success' => true,
            'message' => 'Enchantement appliqué !',
            'new_gold' => $newGold,
            'enchantment' => $enchantment
        ];
    }

    /**
     * Met à jour les stats de l'item avec les enchantements
     */
    private function updateItemStats($characterInventoryId)
    {
        // Récupérer l'item et ses stats de base
        $stmt = $this->db->prepare("
            SELECT ci.instance_stats, i.stats as base_stats
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = ?
        ");
        $stmt->bind_param("i", $characterInventoryId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        $baseStats = json_decode($item['base_stats'] ?? '{}', true) ?: [];
        $instanceStats = json_decode($item['instance_stats'] ?? '{}', true) ?: $baseStats;

        // Récupérer tous les enchantements
        $enchantments = $this->getItemEnchantments($characterInventoryId);

        // Calculer les bonus
        $enchantmentBonuses = [];
        foreach ($enchantments as $ench) {
            $modifiers = json_decode($ench['stat_modifiers'], true) ?: [];
            foreach ($modifiers as $stat => $value) {
                if (!isset($enchantmentBonuses[$stat])) {
                    $enchantmentBonuses[$stat] = 0;
                }
                $enchantmentBonuses[$stat] += $value;
            }
        }

        // Stocker les bonus d'enchantement dans instance_stats
        $instanceStats['enchantment_bonuses'] = $enchantmentBonuses;
        
        // Mettre à jour
        $statsJson = json_encode($instanceStats);
        $stmt = $this->db->prepare("UPDATE character_inventory SET instance_stats = ? WHERE id = ?");
        $stmt->bind_param("si", $statsJson, $characterInventoryId);
        $stmt->execute();
    }

    /**
     * Incrémente le compteur d'enchantements du personnage
     */
    private function incrementEnchantmentCount($characterId)
    {
        // Vérifier si l'entrée existe
        $stmt = $this->db->prepare("SELECT id FROM character_workbenches WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $stmt = $this->db->prepare("
                UPDATE character_workbenches 
                SET total_enchantments = total_enchantments + 1 
                WHERE character_id = ?
            ");
            $stmt->bind_param("i", $characterId);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO character_workbenches (character_id, total_enchantments) 
                VALUES (?, 1)
            ");
            $stmt->bind_param("i", $characterId);
        }
        $stmt->execute();
    }

    /**
     * Retire un enchantement d'un item (coûte de l'or)
     */
    public function removeEnchantment($characterId, $itemEnchantmentId, $cost = 50)
    {
        // Vérifier que l'enchantement appartient au joueur
        $stmt = $this->db->prepare("
            SELECT ie.*, ci.character_id
            FROM item_enchantments ie
            JOIN character_inventory ci ON ie.character_inventory_id = ci.id
            WHERE ie.id = ? AND ci.character_id = ?
        ");
        $stmt->bind_param("ii", $itemEnchantmentId, $characterId);
        $stmt->execute();
        $enchantment = $stmt->get_result()->fetch_assoc();

        if (!$enchantment) {
            return ['success' => false, 'message' => 'Enchantement non trouvé'];
        }

        // Vérifier l'or
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        if ($character['gold'] < $cost) {
            return ['success' => false, 'message' => 'Or insuffisant (coût: ' . $cost . ')'];
        }

        // Déduire l'or
        $newGold = $character['gold'] - $cost;
        $stmt = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        $stmt->bind_param("di", $newGold, $characterId);
        $stmt->execute();

        // Supprimer l'enchantement
        $characterInventoryId = $enchantment['character_inventory_id'];
        $stmt = $this->db->prepare("DELETE FROM item_enchantments WHERE id = ?");
        $stmt->bind_param("i", $itemEnchantmentId);
        $stmt->execute();

        // Recalculer les stats de l'item
        $this->updateItemStats($characterInventoryId);

        return [
            'success' => true,
            'message' => 'Enchantement retiré',
            'new_gold' => $newGold
        ];
    }

    /**
     * Récupère les items équipables du joueur pour l'établi
     */
    public function getEnchantableItems($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT ci.id as inventory_id, ci.item_id, ci.location, ci.slot_name, ci.instance_stats,
                   i.name, i.description, i.type, i.slot_type, i.icon, i.stats
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ? 
              AND i.type = 'equipment'
              AND i.slot_type != 'none'
            ORDER BY ci.location, i.slot_type
        ");
        if (!$stmt) return [];
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Ajouter les enchantements existants et la rareté à chaque item
        foreach ($items as &$item) {
            $item['enchantments'] = $this->getItemEnchantments($item['inventory_id']);
            
            // Extraire la rareté de instance_stats si disponible
            $instanceStats = json_decode($item['instance_stats'] ?? '{}', true);
            $item['rarity'] = $instanceStats['rarity'] ?? 'common';
        }

        return $items;
    }
}
