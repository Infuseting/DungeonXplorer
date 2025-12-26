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
        if (!$stmt)
            return false;
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
        if (!$stmt)
            return false;
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
        if (!$stmt)
            return 5000;
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
        if (!$stmt)
            return 10;
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
        if (!$stmt)
            return [];
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
        if (!$stmt)
            return null;
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
        if (!$enchantment)
            return false;

        $compatibleSlots = json_decode($enchantment['compatible_slot_types'], true);
        if (!$compatibleSlots)
            return true; // Si pas de restriction, compatible avec tout

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
        if (!$stmt) {
            return ['success' => false, 'message' => 'Erreur base de données'];
        }
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
        if (!$stmt) {
            return ['success' => false, 'message' => 'Erreur base de données'];
        }
        $stmt->bind_param("ii", $characterInventoryId, $enchantmentId);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            return ['success' => false, 'message' => 'Cet item possède déjà cet enchantement'];
        }

        // Vérifier l'or du joueur
        $stmt = $this->db->prepare("SELECT gold FROM characters WHERE id = ?");
        if (!$stmt) {
            return ['success' => false, 'message' => 'Erreur base de données'];
        }
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $character = $stmt->get_result()->fetch_assoc();

        if (!$character || $character['gold'] < $enchantment['cost']) {
            return ['success' => false, 'message' => 'Or insuffisant'];
        }

        // Déduire l'or
        $newGold = $character['gold'] - $enchantment['cost'];
        $stmt = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
        if (!$stmt) {
            return ['success' => false, 'message' => 'Erreur lors de la déduction d\'or'];
        }
        $stmt->bind_param("di", $newGold, $characterId);
        $stmt->execute();

        // Appliquer l'enchantement
        $stmt = $this->db->prepare("
            INSERT INTO item_enchantments (character_inventory_id, enchantment_id)
            VALUES (?, ?)
        ");
        if (!$stmt) {
            return ['success' => false, 'message' => 'Erreur lors de l\'application de l\'enchantement'];
        }
        $stmt->bind_param("ii", $characterInventoryId, $enchantmentId);
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Erreur: ' . $stmt->error];
        }

        // Mettre à jour les stats de l'item
        $this->updateItemStats($characterInventoryId);

        // Mettre à jour le compteur d'enchantements
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
            SELECT ci.instance_stats, i.stat_ranges
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = ?
        ");
        $stmt->bind_param("i", $characterInventoryId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        // Attempts to get base stats from current instance_stats (removing old bonuses)
        // Or generate new base stats from stat_ranges if this is a fresh item
        $currentStats = json_decode($item['instance_stats'] ?? '{}', true) ?: [];

        // If we have enchantment_bonuses tracked, subtract them to get base stats
        // Otherwise, if stats exist, assume they are base stats for now (risky but better than empty)
        $baseStats = [];
        if (isset($currentStats['enchantment_bonuses'])) {
            foreach ($currentStats as $key => $val) {
                if ($key !== 'enchantment_bonuses' && $key !== 'enchantment_ids' && $key !== 'rarity') {
                    $bonus = $currentStats['enchantment_bonuses'][$key] ?? 0;
                    $baseStats[$key] = $val - $bonus;
                }
            }
            // Preserve rarity
            if (isset($currentStats['rarity']))
                $baseStats['rarity'] = $currentStats['rarity'];

        } elseif (!empty($currentStats)) {
            // No tracked bonuses, assume current stats are base stats
            $baseStats = $currentStats;
        } else {
            // No instance stats at all, generate base stats from ranges (mid-range?)
            // Or just leave empty and let them be generated?
            // Ideally should be generated when item is created.
            // For now, let's pick average of ranges
            $ranges = json_decode($item['stat_ranges'] ?? '{}', true);
            foreach ($ranges as $stat => $range) {
                if (is_array($range) && isset($range['min'], $range['max'])) {
                    $baseStats[$stat] = floor(($range['min'] + $range['max']) / 2);
                } elseif (is_numeric($range)) {
                    $baseStats[$stat] = $range;
                }
            }
            $baseStats['rarity'] = 'common'; // Default
        }

        // Start with base stats
        $instanceStats = $baseStats;

        // Récupérer tous les enchantements
        $enchantments = $this->getItemEnchantments($characterInventoryId);

        // Calculer les bonus d'enchantement
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

        // Appliquer les bonus aux stats de l'instance
        // Les stats de base + les bonus d'enchantement
        foreach ($enchantmentBonuses as $stat => $bonus) {
            $baseValue = $baseStats[$stat] ?? 0;
            $instanceStats[$stat] = $baseValue + $bonus;
        }

        // Store the enchantment bonuses for reference (e.g., to show in tooltip)
        if (!empty($enchantmentBonuses)) {
            $instanceStats['enchantment_bonuses'] = $enchantmentBonuses;
        }

        // Preserve rarity if it exists
        if (isset($baseStats['rarity'])) {
            $instanceStats['rarity'] = $baseStats['rarity'];
        }

        // Mettre à jour
        $statsJson = json_encode($instanceStats);
        $stmt = $this->db->prepare("UPDATE character_inventory SET instance_stats = ? WHERE id = ?");
        $stmt->bind_param("si", $statsJson, $characterInventoryId);
        $stmt->execute();
    }

    /**
     * Incrémente le compteur d'enchantements pour la maison du personnage
     */
    private function incrementEnchantmentCount($characterId)
    {
        // Trouver l'établi de la maison principale du joueur
        $stmt = $this->db->prepare("
            SELECT chw.id 
            FROM character_house_workbenches chw
            JOIN character_houses ch ON chw.character_house_id = ch.id
            WHERE ch.character_id = ? AND ch.is_primary = 1
            LIMIT 1
        ");
        if (!$stmt)
            return;
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $workbench = $stmt->get_result()->fetch_assoc();

        if ($workbench) {
            $stmt = $this->db->prepare("
                UPDATE character_house_workbenches 
                SET total_enchantments = total_enchantments + 1 
                WHERE id = ?
            ");
            $stmt->bind_param("i", $workbench['id']);
            $stmt->execute();
        }
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
                   i.name, i.description, i.type, i.slot_type, i.icon, i.stat_ranges
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = ? 
              AND i.type = 'equipment'
              AND i.slot_type != 'none'
            ORDER BY ci.location, i.slot_type
        ");
        if (!$stmt)
            return [];
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Ajouter les enchantements existants et la rareté à chaque item
        foreach ($items as &$item) {
            $item['enchantments'] = $this->getItemEnchantments($item['inventory_id']);

            // Use instance_stats if available (includes enchantment bonuses), otherwise use base_stats
            if (!empty($item['instance_stats'])) {
                $item['stats'] = $item['instance_stats'];
            } elseif (!empty($item['base_stats'])) {
                $item['stats'] = $item['base_stats'];
            } else {
                $item['stats'] = '{}';
            }

            // Extraire la rareté de instance_stats si disponible
            $instanceStats = json_decode($item['instance_stats'] ?? '{}', true);
            $item['rarity'] = $instanceStats['rarity'] ?? 'common';
        }

        return $items;
    }
}
