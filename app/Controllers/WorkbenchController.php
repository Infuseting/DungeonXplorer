<?php

namespace App\Controllers;

use App\Models\Workbench;
use App\Models\House;
use App\Config\Database;

class WorkbenchController
{
    /**
     * Récupère les données de l'établi
     */
    public function index()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $characterId = $_SESSION['character_id'];
            $workbenchModel = new Workbench();
            $houseModel = new House();

            // Vérifier si le joueur a une maison
            $primaryHouse = $houseModel->getPrimaryHouse($characterId);
            
            if (!$primaryHouse) {
                echo json_encode([
                    'success' => true,
                    'has_house' => false,
                    'has_workbench' => false,
                    'message' => 'Vous devez d\'abord acheter une maison'
                ]);
                exit;
            }

            // Vérifier si l'établi est débloqué pour cette maison
            $hasWorkbench = $workbenchModel->hasWorkbenchForHouse($primaryHouse['id']);

            // Récupérer le niveau du personnage
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT level FROM character_stats WHERE character_id = ?");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $stats = $stmt->get_result()->fetch_assoc();
            $level = $stats['level'] ?? 1;

            // Récupérer l'or du personnage
            $stmt = $db->prepare("SELECT gold FROM characters WHERE id = ?");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $character = $stmt->get_result()->fetch_assoc();

            // Calculer le prix de l'établi selon la maison
            $workbenchPrice = $workbenchModel->getWorkbenchPrice($primaryHouse['house_id']);
            $requiredLevel = $workbenchModel->getWorkbenchRequiredLevel($primaryHouse['house_id']);

            $response = [
                'success' => true,
                'has_house' => true,
                'has_workbench' => $hasWorkbench,
                'workbench_price' => $workbenchPrice,
                'workbench_required_level' => $requiredLevel,
                'house_name' => $primaryHouse['custom_name'] ?? $primaryHouse['name'],
                'player_gold' => $character['gold'] ?? 0,
                'player_level' => $level
            ];

            // Si l'établi est débloqué, ajouter les enchantements et items
            if ($hasWorkbench) {
                $response['enchantments'] = $workbenchModel->getAvailableEnchantments($level);
                $response['items'] = $workbenchModel->getEnchantableItems($characterId);
            } else {
                $response['enchantments'] = [];
                $response['items'] = [];
            }

            echo json_encode($response);

        } catch (\Exception $e) {
            error_log("Workbench error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Acheter l'établi pour la maison actuelle
     */
    public function purchase()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $characterId = $_SESSION['character_id'];
            $workbenchModel = new Workbench();
            $houseModel = new House();

            // Vérifier la maison principale
            $primaryHouse = $houseModel->getPrimaryHouse($characterId);
            if (!$primaryHouse) {
                echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas de maison']);
                exit;
            }

            // Acheter l'établi
            $result = $workbenchModel->purchaseWorkbench($characterId, $primaryHouse['id'], $primaryHouse['house_id']);
            echo json_encode($result);

        } catch (\Exception $e) {
            error_log("Workbench purchase error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Applique un enchantement à un item
     */
    public function enchant()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $inventoryItemId = $input['inventory_item_id'] ?? null;
            $enchantmentId = $input['enchantment_id'] ?? null;

            if (!$inventoryItemId || !$enchantmentId) {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
                exit;
            }

            $workbenchModel = new Workbench();
            $result = $workbenchModel->applyEnchantment(
                $_SESSION['character_id'],
                $inventoryItemId,
                $enchantmentId
            );

            echo json_encode($result);
        } catch (\Exception $e) {
            error_log("Workbench enchant error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Retire un enchantement d'un item
     */
    public function removeEnchantment()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemEnchantmentId = $input['item_enchantment_id'] ?? null;

        if (!$itemEnchantmentId) {
            echo json_encode(['success' => false, 'message' => 'ID requis']);
            exit;
        }

        $workbenchModel = new Workbench();
        $result = $workbenchModel->removeEnchantment(
            $_SESSION['character_id'],
            $itemEnchantmentId
        );

        echo json_encode($result);
    }

    /**
     * Récupère les enchantements compatibles avec un item
     */
    public function getCompatibleEnchantments()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['character_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $itemSlotType = $_GET['slot_type'] ?? null;

        if (!$itemSlotType) {
            echo json_encode(['success' => false, 'message' => 'Type de slot requis']);
            exit;
        }

        // Récupérer le niveau du personnage
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT level FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("i", $_SESSION['character_id']);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $level = $stats['level'] ?? 1;

        $workbenchModel = new Workbench();
        $allEnchantments = $workbenchModel->getAvailableEnchantments($level);

        // Filtrer les enchantements compatibles
        $compatible = array_filter($allEnchantments, function($ench) use ($itemSlotType, $workbenchModel) {
            return $workbenchModel->isEnchantmentCompatible($ench['id'], $itemSlotType);
        });

        echo json_encode([
            'success' => true,
            'enchantments' => array_values($compatible)
        ]);
    }
}
