<?php

namespace App\Controllers;

use App\Models\Character;
use App\Models\Inventory;
use App\Models\Map;
use App\Models\MapPoint;
use App\Models\StoryProgress;
use App\Models\Quest;
use App\Models\PlayerQuest;
use App\Models\DialogueTree;
use App\Models\NPC;
use App\Models\CharacterStats;
use App\Models\Skill;
use App\Services\TokenService;
use App\Models\DailyQuest;
use App\Models\House;
use App\Config\Database ;

class GameController
{
    /**
     * Point d'entrée principal du jeu (/game).
     * Affiche l'interface de jeu, la carte, et initialise les données du personnage.
     */
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Gestion de la sélection du personnage (POST ou Session)
        $characterId = $_POST['character_id'] ?? $_SESSION['character_id'] ?? null;

        if (!$characterId) {
            header('Location: /personnage');
            exit;
        }

        $_SESSION['character_id'] = $characterId;

        $characterModel = new Character();
        $character = $characterModel->findById($characterId);

        // Sécurité : Vérifie que le personnage appartient bien à l'utilisateur connecté
        if ($character->getUserId() !== $_SESSION['user_id']) {
            header('Location: /personnage');
            exit;
        }

        // Chargement de l'inventaire
        $inventoryModel = new Inventory();
        $inventory = $inventoryModel->getCharacterInventory($characterId);
        
        // Vérification de la progression active (Histoire/Donjon)
        $storyProgressModel = new StoryProgress();
        $activeStory = $storyProgressModel->getActiveStory($characterId);
        
        if ($activeStory) {
            // Si le joueur est en cours d'histoire, on pourrait rediriger ou adapter l'interface (Logique à compléter)
        }

        // Initialisation de la carte
        $mapModel = new Map();
        $mapPointModel = new MapPoint();
        
        // Chargement des compétences et arbre de compétences
        $skillModel = new Skill();
        $classSkills = $skillModel->getSkillsByClass($character->getClassId());
        $unlocked = $skillModel->getUnlockedSkills($characterId);
        $unlockedIds = array_map(function($s) { return $s['id']; }, $unlocked);
        
        // Préparation des données de compétences pour le frontend (JSON)
        // Détermine le statut : locked, unlocked, available (si prérequis et coût ok)
        $playerSkillsJson = json_encode(array_map(function($s) use ($character, $unlockedIds) {
            $isUnlocked = in_array($s['id'], $unlockedIds);
            $canAfford = $character->getSkillPoints() >= $s['cost_sp'];
            $levelMet = $character->getLevel() >= $s['min_level'];
            $prereqMet = true;
            if ($s['parent_skill_id']) $prereqMet = in_array($s['parent_skill_id'], $unlockedIds);
            
            $s['status'] = 'locked';
            if ($isUnlocked) $s['status'] = 'unlocked';
            else if ($canAfford && $levelMet && $prereqMet) $s['status'] = 'available';
            
            return $s;
        }, $classSkills));

        // Recupereation des données sur le niveau et l'expérience
        $currentLevel = $character->getLevel();
        $currentXp = $character->getExperience();
        $xpForNext = $character->getXpForNextLevel();
        $xpPercent = $xpForNext > 0 ? min(100, ($currentXp / $xpForNext) * 100) : 0;

        
        // Chargement de la carte par défaut (ID 1 - Carte du Monde)
        $mapId = 1;
        $mapConfig = $mapModel->getMapConfig($mapId);
        $mapPoints = $mapPointModel->getVisiblePointsForCharacter($mapId, $characterId);

        require_once __DIR__ . '/../Views/game/index.php';
    }

    /**
     * Charge les données d'une sous-carte (API).
     */
    public function loadSubMap()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $mapId = $data['mapId'] ?? null;

        if (!$mapId) {
            echo json_encode(['success' => false, 'message' => 'ID de carte manquant']);
            exit;
        }

        $mapModel = new Map();
        $mapPointModel = new MapPoint();

        $map = $mapModel->findById($mapId);
        
        if (!$map) {
            echo json_encode(['success' => false, 'message' => 'Carte non trouvée']);
            exit;
        }

        // Récupère les points et enrichit avec le statut des quêtes
        $points = $mapPointModel->getVisiblePointsForCharacter($mapId, $_SESSION['character_id']);
        $points = $this->enrichPointsWithQuestStatus($points, $_SESSION['character_id']);

        echo json_encode([
            'success' => true,
            'map' => [
                'id' => $map['id'],
                'name' => $map['name'],
                'description' => $map['description'],
                'image_path' => $map['image_path']
            ],
            'points' => $points
        ]);
        exit;
    }

    /**
     * Récupère les points d'une carte spécifique (API).
     */
    public function getMapPoints($mapId)
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Aucun personnage sélectionné']);
            exit;
        }
        $mapPointModel = new MapPoint();
        $points = $mapPointModel->getVisiblePointsForCharacter($mapId, $_SESSION['character_id']);
        $points = $this->enrichPointsWithQuestStatus($points, $_SESSION['character_id']);
        
        // Ajouter le point de maison uniquement sur la carte principale (map_id = 1)
        if ($mapId == 1) {
            // Récupérer les coordonnées de la maison principale du joueur
            $houseModel = new House();
            $primaryHouse = $houseModel->getPrimaryHouse($_SESSION['character_id']);
            
            // Coordonnées par défaut (Petite Cabane) si pas de maison
            $houseX = 54;
            $houseY = -108;
            $houseName = 'Ma Maison';
            
            if ($primaryHouse) {
                $houseX = $primaryHouse['map_x'] ?? 54;
                $houseY = $primaryHouse['map_y'] ?? -108;
                $houseName = $primaryHouse['custom_name'] ?? $primaryHouse['name'] ?? 'Ma Maison';
            }
            
            $points[] = [
                'id' => 'house',
                'map_id' => 1,
                'name' => $houseName,
                'description' => 'Votre maison personnelle où vous pouvez stocker vos objets et vous reposer.',
                'x' => $houseX,
                'y' => $houseY,
                'type' => 'house',
                'target_id' => null,
                'sub_map_id' => null,
                'story_id' => null,
                'icon' => null,
                'is_locked' => 0,
                'is_hidden' => 0,
                'radius' => 20,
                'has_quest' => false
            ];
        }
        
        echo json_encode([
            'success' => true,
            'points' => $points
        ]);
        exit;
    }
        
    /**
     * Récupère les détails d'un PNJ pour l'interaction (Dialogues, Quêtes, Magasin).
     */
    public function getNPC($id)
    {
        header('Content-Type: application/json');
        
        $npcModel = new NPC();
        $dialogueModel = new DialogueTree();
        
        $npc = $npcModel->findById($id);
        
        if (!$npc) {
            echo json_encode(['success' => false, 'message' => 'PNJ non trouvé']);
            exit;
        }
        
        // Récupération de tous les arbres de dialogue du PNJ
        $allDialogueTrees = $npcModel->getDialogueTrees($id);
        
        $availableDialogues = [];
        
        // Filtrage des dialogues selon les quêtes actives
        if (isset($_SESSION['character_id'])) {
            $playerQuestModel = new PlayerQuest();
            
            // Mettre à jour les quêtes qui nécessitent de parler à ce NPC (en ville)
            $questUpdates = $playerQuestModel->onNPCInteraction($_SESSION['character_id'], $id);
            if (!empty($questUpdates)) {
                error_log("[Game NPC] Quest updates for NPC $id (character {$_SESSION['character_id']}): " . json_encode($questUpdates));
            }
            
            $db = Database::getInstance()->getConnection();
            
            foreach ($allDialogueTrees as $tree) {
                // Vérifie si le dialogue est lié à un objectif de quête
                $objective = $dialogueModel->getQuestObjective($tree['id']);
                
                if ($objective) {
                    // Si lié, on vérifie si le joueur est à ce stade de la quête active
                    $stmt = $db->prepare("
                        SELECT pq.id 
                        FROM player_quests pq
                        WHERE pq.character_id = ? 
                        AND pq.quest_id = ? 
                        AND pq.current_stage_id = ?
                        AND pq.status = 'ACTIVE'
                    ");
                    $stmt->bind_param("iii", 
                        $_SESSION['character_id'], 
                        $objective['quest_id'], 
                        $objective['stage_id']
                    );
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $availableDialogues[] = $tree;
                    }
                } else {
                    // Dialogue standard (non lié à une quête)
                    $availableDialogues[] = $tree;
                }
            }
        } else {
            // Pas de session personnage : on montre tout (debug/fallback)
            $availableDialogues = $allDialogueTrees;
        }
        
        // Gestion de l'inventaire marchand (si le PNJ est marchand)
        $merchantInventory = [];
        $npcRoles = array_map('trim', explode(',', $npc['role'] ?? ''));
        if (in_array('merchant', $npcRoles) && $npc['merchant_seed']) {
            $merchantInventory = $npcModel->getMerchantInventory($id);
        }

        // Gestion des quêtes disponibles (si le PNJ est donneur de quêtes)
        $availableQuests = [];
        if (in_array('quest_giver', $npcRoles) && isset($_SESSION['character_id'])) {
            $allNpcQuests = $npcModel->getQuests($id);
            $playerQuestModel = new PlayerQuest();
            $questModel = new Quest();
            $characterModel = new Character();
            
            $character = $characterModel->findById($_SESSION['character_id']);
            $playerLevel = $character->getLevel() ?? 1;

            foreach ($allNpcQuests as $quest) {
                // On ne propose que les quêtes où le PNJ est le donneur (GIVER)
                if (($quest['relation_type'] ?? 'GIVER') !== 'GIVER') continue;

                // Vérification du niveau minimum
                if ($playerLevel < $quest['min_level']) continue;

                // Vérification que la quête n'est pas déjà commencée/terminée
                $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $quest['id']);
                if ($status !== 'NOT_STARTED') continue;

                // Vérification des prérequis (autres quêtes terminées)
                $prerequisites = $questModel->getPrerequisites($quest['id']);
                $prereqsMet = true;
                foreach ($prerequisites as $prereq) {
                    $prereqStatus = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $prereq['required_quest_id']);
                    if ($prereqStatus !== 'COMPLETED') {
                        $prereqsMet = false;
                        break;
                    }
                }
                if (!$prereqsMet) continue;

                $availableQuests[] = $quest;
            }

            // Personnalisation de la salutation si une quête est active pour ce PNJ
            foreach ($allNpcQuests as $quest) {
                if (($quest['relation_type'] ?? 'GIVER') !== 'GIVER') continue;
                $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $quest['id']);
                if ($status === 'ACTIVE') {
                    $npc['active_quest_greeting'] = "Alors, comment avance la quête \"" . $quest['name'] . "\" ? Je compte sur vous !";
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'npc' => $npc,
            'dialogue_trees' => $availableDialogues,
            'merchant_inventory' => $merchantInventory,
            'quests' => $availableQuests
        ]);
        exit;
    }
    
    /**
     * Récupère la structure d'un arbre de dialogue.
     */
    public function getDialogueTree($treeId)
    {
        header('Content-Type: application/json');
        
        $dialogueModel = new DialogueTree();
        $tree = $dialogueModel->getDialogueTree($treeId);
        
        if (empty($tree)) {
            echo json_encode(['success' => false, 'message' => 'Arbre de dialogue non trouvé']);
            exit;
        }
        
        // On récupère la racine de l'arbre
        $dialogue = $tree[0] ?? null;
        
        echo json_encode([
            'success' => true,
            'dialogue' => $dialogue
        ]);
        exit;
    }
    
    /**
     * Accepte une quête proposée par un PNJ.
     * Vérifie les prérequis et l'état actuel avant de l'ajouter.
     */
    public function acceptQuest()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $questId = $data['quest_id'] ?? null;
        
        if (!$questId) {
            echo json_encode(['success' => false, 'message' => 'ID de quête manquant']);
            exit;
        }
        
        $playerQuestModel = new PlayerQuest();
        
        // Vérification : La quête ne doit pas être déjà commencée
        $status = $playerQuestModel->getQuestStatus($_SESSION['character_id'], $questId);
        if ($status !== 'NOT_STARTED') {
            echo json_encode(['success' => false, 'message' => 'Quête déjà acceptée ou terminée']);
            exit;
        }
        
        // Démarrage de la quête
        $playerQuestId = $playerQuestModel->startQuest($_SESSION['character_id'], $questId);
        
        if ($playerQuestId) {
            $questModel = new Quest();
            $quest = $questModel->findById($questId);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Quête acceptée !',
                'quest_name' => $quest['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Impossible d\'accepter la quête']);
        }
        exit;
    }

    /**
     * Récupère le journal des quêtes (encours, terminées) du personnage.
     */
    public function getQuestLog()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        $playerQuestModel = new PlayerQuest();
        $log = $playerQuestModel->getQuestLog($_SESSION['character_id']);
        
        // Get daily quests
        $dailyQuestModel = new DailyQuest();
        $dailyQuests = $dailyQuestModel->getDailyQuestsForCharacter($_SESSION['character_id']);
        
        echo json_encode([
            'success' => true,
            'log' => $log,
            'daily_quests' => $dailyQuests
        ]);
        exit;
    }
    
    /**
     * Récupère les quêtes quotidiennes du personnage.
     */
    public function getDailyQuests()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        $dailyQuestModel = new DailyQuest();
        $dailyQuests = $dailyQuestModel->getDailyQuestsForCharacter($_SESSION['character_id']);
        $stats = $dailyQuestModel->getDailyQuestStats($_SESSION['character_id']);
        
        echo json_encode([
            'success' => true,
            'daily_quests' => $dailyQuests,
            'stats' => $stats
        ]);
        exit;
    }
    
    /**
     * Réclame la récompense d'une quête quotidienne complétée.
     */
    public function claimDailyQuestReward()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $playerDailyQuestId = $data['player_daily_quest_id'] ?? null;
        
        if (!$playerDailyQuestId) {
            echo json_encode(['success' => false, 'message' => 'ID de quête quotidienne manquant']);
            exit;
        }
        
        $dailyQuestModel = new DailyQuest();
        $result = $dailyQuestModel->claimReward($_SESSION['character_id'], $playerDailyQuestId);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Complète un dialogue et met à jour la progression de quête associée si nécessaire.
     */
    public function completeDialogue()
    {
        header('Content-Type: application/json');
        
        // Debug logs (à nettoyer potentiellement ou passer en niveau DEBUG uniquement)
        error_log("=== completeDialogue called ===");
        
        if (!isset($_SESSION['character_id'])) {
            error_log("ERROR: No character_id in session");
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }
        
        error_log("Character ID: " . $_SESSION['character_id']);
        
        $data = json_decode(file_get_contents('php://input'), true);
        $treeId = $data['tree_id'] ?? null;
        
        error_log("Tree ID received: " . ($treeId ?? 'NULL'));
        
        if (!$treeId) {
            error_log("ERROR: No tree_id provided");
            echo json_encode(['success' => false, 'message' => 'ID de dialogue manquant']);
            exit;
        }
        
        // Vérification si ce dialogue est un objectif de quête
        $dialogueModel = new DialogueTree();
        $objective = $dialogueModel->getQuestObjective($treeId);
        
        error_log("Objective found: " . ($objective ? "YES (ID: {$objective['id']})" : "NO"));
        
        if ($objective) {
            // Dialogue lié à une quête : On met à jour la progression
            $playerQuestModel = new PlayerQuest();
            $db = Database::getInstance()->getConnection();
            
            error_log("Quest ID: {$objective['quest_id']}, Stage ID: {$objective['stage_id']}");
            
            // On récupère la quête active correspondante pour ce personnage
            $stmt = $db->prepare("
                SELECT id 
                FROM player_quests 
                WHERE character_id = ? 
                AND quest_id = ? 
                AND current_stage_id = ?
                AND status = 'ACTIVE'
            ");
            $stmt->bind_param("iii", 
                $_SESSION['character_id'], 
                $objective['quest_id'],
                $objective['stage_id']
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $playerQuest = $result->fetch_assoc();
            
            error_log("Player quest found: " . ($playerQuest ? "YES (ID: {$playerQuest['id']})" : "NO"));
            
            if ($playerQuest) {
                // Mise à jour de l'objectif
                error_log("Calling updateProgress({$playerQuest['id']}, {$objective['id']}, 1)");
                $events = $playerQuestModel->updateProgress($playerQuest['id'], $objective['id'], 1);
                
                error_log("SUCCESS: Quest updated");
                echo json_encode([
                    'success' => true,
                    'quest_updated' => true,
                    'message' => 'Objectif de quête complété !',
                    'quest_update' => [
                        'quest_name' => $events['quest_name'],
                        'objective_description' => $events['objective_description'],
                        'objective_completed' => $events['objective_completed'],
                        'quest_completed' => $events['quest_completed'],
                        'unlocked_points' => $events['unlocked_points']
                    ]
                ]);
            } else {
                error_log("WARNING: Player doesn't have this quest active");
                // Cas : Dialogue lié à une quête mais le joueur n'a pas cette quête active
                echo json_encode([
                    'success' => true,
                    'quest_updated' => false
                ]);
            }
        } else {
            error_log("INFO: Normal dialogue, not linked to quest");
            // Cas : Dialogue normal, simple fin de conversation
            echo json_encode([
                'success' => true,
                'quest_updated' => false
            ]);
        }
        
        exit;
    }

    /**
     * Utilise un objet consommable depuis l'inventaire.
     * Applique les effets (Soin, Buff) et consomme l'objet.
     */
    public function useItem()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $inventoryItemId = $data['item_id'] ?? null;

        if (!$inventoryItemId) {
            echo json_encode(['success' => false, 'message' => 'ID d\'objet manquant']);
            exit;
        }

        $characterId = $_SESSION['character_id'];
        $inventoryModel = new Inventory();
        $characterModel = new Character();
        $buffService = new BuffService();

        // Récupération sécurisée de l'objet dans l'inventaire du personnage
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT i.* 
            FROM character_inventory ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = ? AND ci.character_id = ?
        ");
        $stmt->bind_param("ii", $inventoryItemId, $characterId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Objet non trouvé']);
            exit;
        }

        if ($item['type'] !== 'consumable') {
            echo json_encode(['success' => false, 'message' => 'Cet objet n\'est pas consommable']);
            exit;
        }

        $effectApplied = false;
        $message = "Objet utilisé";

        if ($item['effect_type'] === 'heal') {
            // Logique de Soin
            $healAmount = $item['effect_value'];
            $character = $characterModel->findById($characterId);
            
            // Note: Update de la DB via le modèle
            $characterModel->heal($characterId, $healAmount);
            $message = "Points de vie restaurés: +$healAmount";
            $effectApplied = true;

        } elseif ($item['effect_type'] === 'buff') {
            // Logique de Buff
            $stats = json_decode($item['stats'], true); 
            $durationValue = $item['duration_value'];
            $durationType = $item['duration_type']; 
            
            $buffService->applyBuff(
                $characterId, 
                $item['name'], 
                $stats, 
                $durationType, 
                $durationValue
            );
            $message = "Effet appliqué: " . $item['name'];
            $effectApplied = true;
        }

        if ($effectApplied) {
            // Consommation de l'objet (réduction quantité ou suppression)
            $inventoryModel->consumeItem($characterId, $inventoryItemId);
            
            // Mise à jour des quêtes quotidiennes (USE_ITEMS)
            $dailyQuestModel = new DailyQuest();
            $dailyQuestModel->onItemUsed($characterId, $item['id']);
            
            // Actualisation indispensable des stats pour le client
             $character = $characterModel->findById($characterId);
             $characterModel->resetCache(); 
             
             // Rechargement frais des données
             $charObj = new Character();
             $charObj->findById($characterId);
             
             $newStats = [
                 'strength' => $charObj->getStrength(),
                 'vitality' => $charObj->getVitality(),
                 'dexterity' => $charObj->getDexterity(),
                 'intelligence' => $charObj->getIntelligence(),
             ];

             echo json_encode([
                 'success' => true, 
                 'message' => $message,
                 'new_stats' => $newStats
             ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucun effet disponible']);
        }
        exit;
    }

    /**
     * Traite la sélection d'une option de dialogue.
     * Vérifie les conditions et déclenche les actions associées.
     */
    public function selectDialogueOption()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['character_id'])) {
            echo json_encode(['success' => false, 'message' => 'Personnage non sélectionné']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $dialogueId = $input['dialogue_id'] ?? null;
        
        if (!$dialogueId) {
            echo json_encode(['success' => false, 'message' => 'ID de dialogue manquant']);
            exit;
        }

        $db = Database::getInstance()->getConnection();
        
        // Récupération du dialogue cible
        $stmt = $db->prepare("SELECT * FROM dialogues WHERE id = ?");
        $stmt->bind_param("i", $dialogueId);
        $stmt->execute();
        $dialogue = $stmt->get_result()->fetch_assoc();

        if (!$dialogue) {
            echo json_encode(['success' => false, 'message' => 'Dialogue introuvable']);
            exit;
        }

        $characterId = $_SESSION['character_id'];

        // Vérification des conditions optionnelles (ex: niveau, objet requis)
        if (!$this->validateCondition($characterId, $dialogue['condition_type'], $dialogue['condition_value'])) {
             echo json_encode(['success' => false, 'message' => 'Condition non remplie pour ce choix']);
             exit;
        }

        // Exécution de l'action associée (ex: donner quête, donner objet, soigner)
        $actionResult = $this->executeAction($characterId, $dialogue['action_type'], $dialogue['action_value']);

        echo json_encode([
            'success' => true,
            'action_result' => $actionResult,
            'next_dialogue_id' => $dialogueId         ]);
        exit;
    }

    /**
     * Vérifie si une condition est remplie pour le personnage.
     * Types supportés : MIN_LEVEL, HAS_ITEM, QUEST_ACTIVE, QUEST_COMPLETED, QUEST_NOT_STARTED.
     */
    private function validateCondition($characterId, $type, $value)
    {
        if ($type === 'NONE') return true;
        
        $characterModel = new Character();
        $invModel = new Inventory();
        $pqModel = new PlayerQuest();

        switch ($type) {
            case 'MIN_LEVEL':
                $char = $characterModel->findById($characterId);
                return ($char['level'] ?? 1) >= (int)$value;
                
            case 'HAS_ITEM':
                $inv = $invModel->getCharacterInventory($characterId);
                // Vérifie dans l'inventaire principal
                foreach ($inv['inventory'] as $item) {
                     if ($item['item_id'] == $value) return true;
                }
                // Vérifie dans l'équipement
                foreach ($inv['equipped'] as $item) {
                     if ($item['item_id'] == $value) return true;
                }
                return false;

            case 'QUEST_ACTIVE':
                return $pqModel->getQuestStatus($characterId, $value) === 'ACTIVE';

            case 'QUEST_COMPLETED':
                return $pqModel->getQuestStatus($characterId, $value) === 'COMPLETED';
            
            case 'QUEST_NOT_STARTED':
                return $pqModel->getQuestStatus($characterId, $value) === 'NOT_STARTED';
        }
        
        return true;
    }

    /**
     * Exécute une action scriptée sur le personnage.
     * Types supportés : TRIGGER_QUEST, GIVE/REMOVE_ITEM, HEAL, DAMAGE, GIVE/REMOVE_GOLD, FORCE_FIGHT, MODIFY_REPUTATION.
     */
    private function executeAction($characterId, $type, $value)
    {
        if ($type === 'NONE') return null;

        $characterModel = new Character();
        $invModel = new Inventory();
        $pqModel = new PlayerQuest();
        $char = $characterModel->findById($characterId); 
        
        switch ($type) {
            case 'TRIGGER_QUEST':
                return $pqModel->startQuest($characterId, $value);

            case 'GIVE_ITEM':
                return $invModel->addItem($characterId, $value);

            case 'REMOVE_ITEM':
                // On vérifie d'abord que le joueur possède l'objet
                $valid = $this->validateCondition($characterId, 'HAS_ITEM', $value); 
                if($valid) {
                     $inv = $invModel->getCharacterInventory($characterId);
                     foreach ($inv['inventory'] as $item) {
                         if ($item['item_id'] == $value) {
                             return $invModel->deleteItem($characterId, $item['id']);
                         }
                     }
                }
                return false;

            case 'HEAL':
                $characterModel->heal($characterId, (int)$value);
                return ['healed' => $value];

            case 'DAMAGE':
                 $characterModel->reduceVitality((int)$value);
                 return ['damage' => $value];

            case 'GIVE_GOLD':
                $characterModel->addGold((int)$value);
                return ['gold_added' => $value];

            case 'REMOVE_GOLD':
                $characterModel->addGold(-(int)$value);
                return ['gold_removed' => $value];

            case 'FORCE_FIGHT':
                // Redirection ou logique de combat forcé (à implémenter côté client/serveur)
                return ['force_fight' => $value];

            case 'MODIFY_REPUTATION':
                // Format attendu: faction_id:amount
                $parts = explode(':', $value);
                if (count($parts) === 2) {
                    $factionId = (int)$parts[0];
                    $amount = (int)$parts[1];
                    $repService = new ReputationService();
                    if ($repService->modifyReputation($characterId, $factionId, $amount)) {
                         return ['reputation_modified' => $amount, 'faction_id' => $factionId];
                    }
                }
                return false;
        }

        return null;
    }

    /**
     * Parcourt les points de la carte pour indiquer si une quête est disponible.
     * Ajoute le flag 'has_quest' = true si le joueur remplit les conditions pour prendre une nouvelle quête.
     */
    private function enrichPointsWithQuestStatus($points, $characterId)
    {
        $npcModel = new NPC();
        $playerQuestModel = new PlayerQuest();
        $questModel = new Quest();
        $characterModel = new Character();
        
        $character = $characterModel->findById($characterId);
        $playerLevel = $character->toArray()['level'] ?? 1;

        foreach ($points as &$point) {
            $point['has_quest'] = false;

            if ($point['type'] === 'npc' && !empty($point['target_id'])) {
                $npcId = $point['target_id'];
                $allNpcQuests = $npcModel->getQuests($npcId);

                foreach ($allNpcQuests as $quest) {
                    // On ne vérifie que les quêtes données par ce PNJ
                    if (($quest['relation_type'] ?? 'GIVER') !== 'GIVER') continue;

                    // Vérif niveau
                    if ($playerLevel < $quest['min_level']) continue;

                    // Vérif statut (Non commencée uniquement)
                    $status = $playerQuestModel->getQuestStatus($characterId, $quest['id']);
                    if ($status !== 'NOT_STARTED') continue;

                    // Vérif prérequis
                    $prerequisites = $questModel->getPrerequisites($quest['id']);
                    $prereqsMet = true;
                    foreach ($prerequisites as $prereq) {
                        $prereqStatus = $playerQuestModel->getQuestStatus($characterId, $prereq['required_quest_id']);
                        if ($prereqStatus !== 'COMPLETED') {
                            $prereqsMet = false;
                            break;
                        }
                    }
                    
                    if ($prereqsMet) {
                        $point['has_quest'] = true;
                        break; // Une seule quête suffit pour afficher l'icône
                    }
                }
            }
        }
        
        return $points;
    }
}
