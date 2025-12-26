<?php

namespace App\Controllers;

use App\Models\Story;
use App\Models\StoryNode;
use App\Models\StoryProgress;
use App\Models\StoryInstance;
use App\Models\Character;
use App\Models\Inventory;
use App\Models\CharacterStats;
use App\Models\NPC;
use App\Models\DialogueTree;
use App\Config\Database;

class StoryController
{
    /** @var Story Modèle des histoires */
    private $storyModel;
    /** @var StoryNode Modèle des noeuds d'histoire */
    private $nodeModel;
    /** @var StoryProgress Modèle de progression */
    private $progressModel;
    /** @var StoryInstance Modèle des instances procédurales */
    private $instanceModel;
    /** @var Character Modèle des personnages */
    private $characterModel;
    /** @var Inventory Modèle de l'inventaire */
    private $inventoryModel;

    /**
     * Constructeur.
     * Initialise tous les modèles nécessaires le contrôleur.
     */
    public function __construct()
    {
        $this->storyModel = new Story();
        $this->nodeModel = new StoryNode();
        $this->progressModel = new StoryProgress();
        $this->instanceModel = new StoryInstance();
        $this->characterModel = new Character();
        $this->inventoryModel = new Inventory();
    }

    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    /**
     * Point d'entrée pour lancer ou reprendre une histoire (donjon).
     * 
     * @param int $storyId L'ID de l'histoire à rejoindre.
     */
    public function enterStory($storyId)
    {
        $characterId = $_SESSION['character_id'];
        $story = $this->storyModel->findById($storyId);

        if (!$story) {
            header('Location: /game');
            exit;
        }

        // Vérifie si une progression existe déjà pour ce personnage et cette histoire
        $progress = $this->progressModel->getProgress($characterId, $storyId);

        if (!$progress) {
            // Pas de progression : Initialisation
            if ($story['type'] === 'manual') {
                // Histoire manuelle : On commence au noeud de départ défini
                $startNode = $this->storyModel->getStartNode($storyId);
                if (!$startNode) {
                    header('Location: /game');
                    exit;
                }
                $this->progressModel->startStory($characterId, $storyId, $startNode['id']);
            } else {
                // Histoire procédurale : On cherche ou génère une instance
                $instance = $this->instanceModel->getByStoryAndCharacter($storyId, $characterId);

                if (!$instance) {
                    // Génération d'une nouvelle instance procédurale
                    $generator = new ProceduralGenerator();
                    $instanceId = $generator->generate($storyId, $characterId);

                    if (!$instanceId) {
                        header('Location: /game?error=generation_failed');
                        exit;
                    }
                    $instance = $this->instanceModel->findById($instanceId);
                }

                // Récupération du noeud de départ de l'instance
                $startNode = $this->nodeModel->getInstanceStartNode($instance['id']);
                if ($startNode) {
                    $this->progressModel->startStory($characterId, $storyId, $startNode['id']);
                } else {
                    header('Location: /game?error=no_start_node');
                    exit;
                }
            }
        }

        // Si la requête est AJAX, on renvoie une vue partielle pour l'interface
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Mise à jour des quêtes quotidiennes (VISIT_LOCATIONS)
            $dailyQuestModel = new \App\Models\DailyQuest();
            $dailyQuestModel->onLocationVisited($characterId, $storyId);

            extract([
                'story' => $story,
                'inventory' => $this->inventoryModel->getCharacterInventory($characterId)
            ]);
            require __DIR__ . '/../Views/game/partials/story_content.php';
            exit;
        }

        // Redirection par défaut (fallback)
        header('Location: /game');
        exit;

        // Note: Le code suivant n'est jamais atteint à cause du exit ci-dessus.
        // TODO: Vérifier si le logging doit être fait avant le exit ou s'il est obsolète.
        /*
        $logger = new LoggerService();
        $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'DUNGEON_ENTER', [
            'story_id' => $storyId,
            'story_title' => $story['title']
        ]);
        */
    }

    /**
     * Récupère les données du noeud actuel (Format JSON pour AJAX).
     * Filtre les monstres, butins et interactions selon l'état de la session.
     */
    public function getCurrentNode()
    {
        $characterId = $_SESSION['character_id'];

        // Validation de l'ID de l'histoire
        $storyId = $_GET['story_id'] ?? null;
        if (!$storyId) {
            echo json_encode(['error' => 'No story ID provided']);
            exit;
        }

        // Récupération de la progression
        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if (!$progress) {
            echo json_encode(['error' => 'No progress found']);
            exit;
        }

        // Chargement des données brutes du noeud
        $node = $this->nodeModel->getFullNodeData($progress['current_node_id']);
        $nodeStatus = $this->progressModel->getNodeStatus($characterId, $node['id']);

        // Marquer le noeud comme visité si c'est la première fois
        if (!$nodeStatus || !$nodeStatus['is_visited']) {
            $this->progressModel->markNodeVisited($characterId, $node['id']);
        }

        // --- Filtrage des Monstres (Fuis ou Tués) ---

        // Récupération des monstres fuis (en session)
        $sessionKeyFled = 'fled_monsters_' . $characterId . '_' . $node['id'];
        $fledMonsters = $_SESSION[$sessionKeyFled] ?? [];

        // Récupération des monstres tués (en session ou DB, ici session par simplification temporaire)
        // TODO: Vérifier la cohérence avec la DB story_node_monsters
        $sessionKeyKilled = 'killed_monsters_' . $characterId . '_' . $node['id'];
        $killedMonsters = $_SESSION[$sessionKeyKilled] ?? [];

        // --- Filtrage des Butins (Déjà ramassés) ---

        if (!empty($node['loots'])) {
            foreach ($node['loots'] as $key => $loot) {
                if ($this->progressModel->hasCollectedLoot($characterId, $node['id'], $loot['id'])) {
                    unset($node['loots'][$key]);
                }
            }
            $node['loots'] = array_values($node['loots']);
        }

        // --- Application des filtres sur les monstres ---

        if ($nodeStatus && $nodeStatus['monsters_cleared']) {
            // Si la salle est marquée comme nettoyée, on vide la liste des monstres
            $node['monsters'] = [];
        } else {
            if (!empty($node['monsters'])) {
                // Retirer les monstres déjà tués
                foreach ($node['monsters'] as $key => $m) {
                    if (in_array($m['id'], $killedMonsters)) {
                        unset($node['monsters'][$key]);
                    }
                }
                $node['monsters'] = array_values($node['monsters']);

                // Si tous les monstres sont morts, on marque la salle comme nettoyée
                if (empty($node['monsters'])) {
                    $this->progressModel->markNodeCleared($characterId, $node['id']);
                    $nodeStatus = $this->progressModel->getNodeStatus($characterId, $node['id']);
                }
            }

            // Vérification de la présence de monstres actifs (non fuis)
            if (!empty($node['monsters'])) {
                $allFled = true;
                foreach ($node['monsters'] as $m) {
                    if (!in_array($m['id'], $fledMonsters)) {
                        $allFled = false;
                        break;
                    }
                }

                // Si des monstres sont présents et non fuis, on masque les PNJ et les butins
                // pour obliger le joueur à gérer la menace (combat ou fuite)
                if (!$allFled) {
                    $node['npcs'] = [];
                    $node['loots'] = [];
                }
            }
        }

        // --- Filtrage des PNJ (Interactions uniques) ---

        $sessionKeyNPC = 'npc_interacted_' . $characterId . '_' . $node['id'];
        $interactedNPCs = $_SESSION[$sessionKeyNPC] ?? [];
        if ($interactedNPCs === true)
            $interactedNPCs = array_column($node['npcs'], 'id');

        if (!empty($node['npcs'])) {
            foreach ($node['npcs'] as $key => $n) {
                if (in_array($n['id'], $interactedNPCs)) {
                    unset($node['npcs'][$key]);
                }
            }
            $node['npcs'] = array_values($node['npcs']);
        }

        // --- Chargement des Pièges ---

        $node['traps'] = $this->nodeModel->getTraps($node['id']);

        // --- Vérification de l'accessibilité des chemins ---

        if (!empty($node['connections'])) {
            foreach ($node['connections'] as &$conn) {
                $conn['is_accessible'] = $this->checkCondition($conn, $characterId);

                // Ajout d'une raison textuelle si le chemin est verrouillé
                if (!$conn['is_accessible']) {
                    if (strpos($conn['condition_type'], 'stat_') === 0) {
                        $stat = ucfirst(substr($conn['condition_type'], 5));
                        $conn['lock_reason'] = "Requis : $stat " . $conn['condition_value'];
                    } elseif ($conn['condition_type'] === 'level') {
                        $conn['lock_reason'] = "Niveau " . $conn['condition_value'] . " requis";
                    } elseif ($conn['condition_type'] === 'item') {
                        $conn['lock_reason'] = "Objet requis";
                    } elseif ($conn['condition_type'] === 'class') {
                        $conn['lock_reason'] = "Classe requise";
                    } else {
                        $conn['lock_reason'] = "Condition non remplie";
                    }
                }
            }
        }

        echo json_encode([
            'node' => $node,
            'status' => $nodeStatus,
            'fled_monsters' => $fledMonsters,
            'character_id' => $characterId
        ]);
    }

    /**
     * Tente de déplacer le personnage vers un autre noeud.
     * Vérifie les conditions de sortie de la salle actuelle et d'entrée dans la salle cible.
     */
    public function moveToNode()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $nodeId = $_POST['node_id'];

        error_log("Move request: Story $storyId, Node $nodeId, Char $characterId");

        // Vérification de la progression
        $progress = $this->progressModel->getProgress($characterId, $storyId);

        if (!$progress) {
            error_log("No progress found");
            echo json_encode(['success' => false, 'message' => 'No progress found']);
            return;
        }

        $currentNodeId = $progress['current_node_id'];

        // Vérification : Les monstres doivent être gérés (tués ou fuis) avant de partir
        $nodeStatus = $this->progressModel->getNodeStatus($characterId, $currentNodeId);
        $monsters = $this->nodeModel->getMonsters($currentNodeId);

        $canMove = true;
        if (!empty($monsters)) {
            $areMonstersCleared = $nodeStatus && $nodeStatus['monsters_cleared'];
            if (!$areMonstersCleared) {
                // Si non nettoyés, on vérifie si tous les monstres restants ont été fuis
                $sessionKey = 'fled_monsters_' . $characterId . '_' . $currentNodeId;
                $fledMonsters = $_SESSION[$sessionKey] ?? [];

                $allFled = true;
                foreach ($monsters as $m) {
                    if (!in_array($m['id'], $fledMonsters)) {
                        $allFled = false;
                        break;
                    }
                }

                if (!$allFled) {
                    echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas partir ! Il reste des monstres actifs.']);
                    return;
                }
            }
        }

        // Validation de la connexion entre les noeuds
        $connections = $this->nodeModel->getConnections($currentNodeId);
        $returnConnections = $this->nodeModel->getReturnConnections($currentNodeId);
        $allConnections = array_merge($connections, $returnConnections);

        $validMove = false;
        foreach ($allConnections as $conn) {
            if ($conn['to_node_id'] == $nodeId) {
                // Vérification des conditions d'accès (stats, items, etc.)
                if ($this->checkCondition($conn, $characterId)) {
                    $validMove = true;
                } else {
                    error_log("Condition failed for connection to $nodeId");
                }
                break;
            }
        }

        if ($validMove) {
            // Nettoyage de la session de fuite pour cette salle (optionnel, ou garder si on revient?)
            // Pour l'instant on nettoie pour réinitialiser l'état si on revient
            $sessionKey = 'fled_monsters_' . $characterId . '_' . $currentNodeId;
            if (isset($_SESSION[$sessionKey])) {
                unset($_SESSION[$sessionKey]);
            }

            // Mise à jour de la position du joueur
            $this->progressModel->updateProgress($characterId, $storyId, $nodeId);
            echo json_encode(['success' => true]);
        } else {
            error_log("Invalid move or conditions not met");
            echo json_encode(['success' => false, 'message' => 'Déplacement invalide ou conditions non remplies']);
        }
    }

    /**
     * Vérifie si une condition d'accès est remplie pour une connexion.
     * 
     * @param array $connection La connexion à vérifier.
     * @param int $characterId L'ID du personnage.
     * @return bool True si accessible, False sinon.
     */
    private function checkCondition($connection, $characterId)
    {
        if ($connection['condition_type'] === 'none')
            return true;

        // Condition basée sur une statistique (ex: stat_strength)
        if (strpos($connection['condition_type'], 'stat_') === 0) {
            $statName = substr($connection['condition_type'], 5);
            $statsModel = new CharacterStats();
            $effectiveStats = $statsModel->getEffectiveStats($characterId);

            if (!$effectiveStats)
                return false;

            $currentVal = $effectiveStats[$statName] ?? 0;
            $requiredVal = (int) $connection['condition_value'];

            // On vérifie si la stat est supérieure ou égale
            return $currentVal >= $requiredVal;
        }

        // Autres types de conditions
        switch ($connection['condition_type']) {
            case 'item':
                // Requiert un objet spécifique
                return $this->inventoryModel->hasItem($characterId, $connection['condition_value']);
            case 'level':
                // Requiert un niveau minimum
                $character = $this->characterModel->findById($characterId);
                return $character['level'] >= (int) $connection['condition_value'];
            case 'class':
                // Requiert une classe spécifique
                $character = $this->characterModel->findById($characterId);
                return $character['class_id'] == $connection['condition_value'];
            case 'quest_active':
                return true; // Implémentation future
            case 'quest_completed':
                return true; // Implémentation future
            case 'monster_killed':
                // Requiert d'avoir nettoyé une salle précédente (from_node_id)
                $nodeStatus = $this->progressModel->getNodeStatus($characterId, $connection['from_node_id']);
                return $nodeStatus && $nodeStatus['monsters_cleared'];
            default:
                return true;
        }
    }

    /**
     * Tente de fuir un combat contre un monstre.
     * Utilise la Dextérité du joueur contre le niveau du monstre.
     */
    public function attemptFlee()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $monsterId = $_POST['monster_id'];

        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if (!$progress) {
            echo json_encode(['success' => false, 'message' => 'Not in story']);
            exit;
        }

        $currentNodeId = $progress['current_node_id'];
        $monsters = $this->nodeModel->getMonsters($currentNodeId);
        $targetMonster = null;
        foreach ($monsters as $m) {
            if ($m['id'] == $monsterId) {
                $targetMonster = $m;
                break;
            }
        }

        if (!$targetMonster) {
            echo json_encode(['success' => false, 'message' => 'Monster not found']);
            exit;
        }

        if (isset($targetMonster['can_flee']) && $targetMonster['can_flee'] == 0) {
            echo json_encode(['success' => false, 'message' => 'Impossible de fuir ce monstre !']);
            exit;
        }

        // Calcul des chances de fuite
        $statsModel = new CharacterStats();
        $stats = $statsModel->getEffectiveStats($characterId);

        $dexterity = $stats ? $stats['dexterity'] : 10;

        $monsterLevel = $targetMonster['monster_level'];

        // Formule : Base 50% + Différence (Dex - Niveau Monstre) * 2
        $baseChance = 50;
        $bonus = ($dexterity - $monsterLevel) * 2;
        $chance = $baseChance + $bonus;

        // Bornage entre 5 et 95%
        $chance = max(5, min(95, $chance));

        $roll = rand(1, 100);
        $success = $roll <= $chance;

        if ($success) {
            // Fuite réussie : On mémorise que ce monstre a été fui pour cette session/salle
            $sessionKey = 'fled_monsters_' . $characterId . '_' . $currentNodeId;
            if (!isset($_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey] = [];
            }
            if (!in_array($monsterId, $_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey][] = $monsterId;
            }

            // Faire reculer le joueur : trouver un nœud connecté pour y retourner
            $returnConnections = $this->nodeModel->getReturnConnections($currentNodeId);
            $previousNodeId = null;

            if (!empty($returnConnections)) {
                // Prendre la première connexion de retour disponible
                $previousNodeId = $returnConnections[0]['from_node_id'];
                $this->progressModel->updateProgress($characterId, $storyId, $previousNodeId);
            }
        } else {
            // Echec : Le monstre a l'initiative pour le combat (implémentation future)
            $_SESSION['combat_initiative'] = 'enemy';
        }

        echo json_encode([
            'success' => $success,
            'roll' => $roll,
            'chance' => $chance,
            'force_combat' => !$success,
            'retreated' => $success && isset($previousNodeId) && $previousNodeId !== null,
            'message' => $success ? "Vous avez pris la fuite ! Vous reculez dans le donjon." : "Échec de la fuite ! Le monstre vous bloque."
        ]);
    }

    /**
     * Marque la salle comme nettoyée.
     * Cette action n'est valide que si tous les monstres ont été vaincus (gestion côté client/serveur).
     */
    public function clearMonsters()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];

        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if ($progress) {
            $this->progressModel->markNodeCleared($characterId, $progress['current_node_id']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Tente d'éviter ou de désamorcer un piège détecté.
     * Utilise la statistique appropriée (DEX par défaut) contre la difficulté du piège.
     */
    public function attemptTrapAvoidance()
    {
        $characterId = $_SESSION['character_id'];
        $trapId = $_POST['trap_id'];

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM story_node_traps WHERE id = ?");
        $stmt->bind_param("i", $trapId);
        $stmt->execute();
        $trap = $stmt->get_result()->fetch_assoc();

        if (!$trap) {
            echo json_encode(['success' => false, 'message' => 'Piège introuvable']);
            exit;
        }

        $statsModel = new CharacterStats();
        $stats = $statsModel->getEffectiveStats($characterId);

        // Mappage des stats (Code court -> Nom complet)
        $statMap = [
            'DEX' => 'dexterity',
            'STR' => 'strength',
            'INT' => 'intelligence',
            'WIS' => 'wisdom',
            'CON' => 'constitution'
        ];
        // Détermine la stat à utiliser pour l'évitement/désamorçage
        $statName = $statMap[$trap['avoid_stat']] ?? 'dexterity';
        $statValue = $stats[$statName] ?? 10;

        // Calcul du jet : 1d20 + Modificateur de stat
        $mod = floor(($statValue - 10) / 2);
        $roll = rand(1, 20);
        $total = $roll + $mod;

        $success = $total >= $trap['difficulty_class'];
        $damageTaken = 0;

        if (!$success) {
            // Echec : Le piège se déclenche et inflige des dégâts
            $parts = explode('d', $trap['damage_dice']);
            $count = (int) $parts[0];
            $faces = (int) ($parts[1] ?? 6);

            for ($i = 0; $i < $count; $i++) {
                $damageTaken += rand(1, $faces);
            }

            $this->characterModel->takeDamage($characterId, $damageTaken);
        }

        echo json_encode([
            'success' => $success,
            'roll' => $roll,
            'total' => $total,
            'dc' => $trap['difficulty_class'],
            'damage' => $damageTaken,
            'message' => $success ? "Vous avez évité le piège !" : "Échec ! " . $trap['effect_text']
        ]);
        exit;
    }

    /**
     * Récupère un butin (objet) dans la salle.
     * Gère également les pièges sur les coffres ou objets piégés.
     */
    public function collectLoot()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $nodeId = $_POST['node_id'];
        $lootId = $_POST['loot_id'];

        // Vérification : Les monstres doivent être vaincus avant de piller
        $nodeStatus = $this->progressModel->getNodeStatus($characterId, $nodeId);
        $monsters = $this->nodeModel->getMonsters($nodeId);

        if (!empty($monsters)) {
            if (!$nodeStatus || !$nodeStatus['monsters_cleared']) {
                echo json_encode(['success' => false, 'message' => 'Vous devez d\'abord vaincre les monstres !']);
                return;
            }
        }

        // Vérification de l'existence du butin
        $loots = $this->nodeModel->getLoots($nodeId);
        $validLoot = null;
        foreach ($loots as $loot) {
            if ($loot['id'] == $lootId) {
                $validLoot = $loot;
                break;
            }
        }

        if ($validLoot) {
            if ($this->progressModel->hasCollectedLoot($characterId, $nodeId, $lootId)) {
                echo json_encode(['success' => false, 'message' => 'Already collected']);
                return;
            }

            $trapTriggered = false;
            $damageTaken = 0;
            $trapMessage = '';

            // Gestion du piège sur le butin
            if ($validLoot['is_trapped']) {
                $statsModel = new CharacterStats();
                $stats = $statsModel->getEffectiveStats($characterId);
                $dex = $stats['dexterity'] ?? 10;
                $mod = floor(($dex - 10) / 2);

                $roll = rand(1, 20);
                $total = $roll + $mod;

                if ($total < $validLoot['trap_dc']) {
                    $trapTriggered = true;
                    // Déclenchement du piège
                    $parts = explode('d', $validLoot['trap_damage']);
                    $count = (int) $parts[0];
                    $faces = (int) ($parts[1] ?? 4);
                    for ($i = 0; $i < $count; $i++) {
                        $damageTaken += rand(1, $faces);
                    }
                    $this->characterModel->takeDamage($characterId, $damageTaken);
                    $trapMessage = "Le coffre était piégé ! " . ($validLoot['trap_description'] ?: "Vous subissez des dégâts.");
                }
            }

            // Ajout de l'objet à l'inventaire
            $this->inventoryModel->addItem($characterId, $validLoot['item_id'], $validLoot['quantity']);

            // Marquage du butin comme collecté
            $this->progressModel->collectLoot($characterId, $nodeId, $lootId);

            echo json_encode([
                'success' => true,
                'trap_triggered' => $trapTriggered,
                'damage' => $damageTaken,
                'trap_message' => $trapMessage
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid loot']);
        }
    }
    /**
     * Fouille la pièce (Action "Rechercher").
     * Permet de découvrir des pièges ou des butins cachés.
     * Utilise le max(Intelligence, Sagesse).
     */
    public function searchRoom()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];

        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if (!$progress) {
            echo json_encode(['success' => false, 'message' => 'Not in story']);
            exit;
        }

        $nodeId = $progress['current_node_id'];
        $traps = $this->nodeModel->getTraps($nodeId);
        $loots = $this->nodeModel->getLoots($nodeId);

        $statsModel = new CharacterStats();
        $stats = $statsModel->getEffectiveStats($characterId);
        $wis = $stats['wisdom'] ?? 10;
        $int = $stats['intelligence'] ?? 10;

        // Calcul du jet de recherche
        $mod = floor((max($wis, $int) - 10) / 2);
        $roll = rand(1, 20);
        $total = $roll + $mod;

        $message = "Vous fouillez minutieusement la pièce...";
        $foundTraps = [];
        $triggeredTrap = null;
        $damageTaken = 0;

        foreach ($traps as $trap) {
            // TODO: Stocker l'état "déjà trouvé" pour ne pas le redécouvrir à chaque fois ?                                               
            if ($total >= $trap['difficulty_class']) {
                $foundTraps[] = $trap['id'];
                $message .= " Vous repérez un piège !";
            } elseif ($total < $trap['difficulty_class'] - 5) {
                // Echec critique (> 5 points en dessous) : Déclenchement accidentel
                $triggeredTrap = $trap;
                break;
            }
        }

        if ($triggeredTrap) {
            // Déclenchement du piège lors de la fouille
            $parts = explode('d', $triggeredTrap['damage_dice']);
            $count = (int) $parts[0];
            $faces = (int) ($parts[1] ?? 6);

            for ($i = 0; $i < $count; $i++) {
                $damageTaken += rand(1, $faces);
            }
            $this->characterModel->takeDamage($characterId, $damageTaken);
            $message = "En fouillant, vous déclenchez un piège ! " . $triggeredTrap['effect_text'];

            return json_encode([
                'success' => true,
                'action' => 'triggered',
                'damage' => $damageTaken,
                'message' => $message,
                'found_loot' => !empty($loots)
            ]);
        }

        if (empty($traps) && empty($loots)) {
            $message = "Vous ne trouvez rien d'intéressant.";
        } elseif (!empty($loots)) {
            $message .= " Vous trouvez des objets.";
        }

        echo json_encode([
            'success' => true,
            'action' => 'searched',
            'roll' => $roll,
            'total' => $total,
            'message' => $message,
            'found_traps' => $foundTraps
        ]);
    }

    /**
     * Gère la sortie de l'histoire (donjon).
     * Vérifie si le noeud permet la sortie et si les conditions de sortie sont satisfaites.
     */
    public function exitStory()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'] ?? $_GET['story_id'] ?? null;
        
        error_log("[Exit Story] START - Character: $characterId, Story: $storyId");

        // Si storyId manquant, on tente de le récupérer depuis la progression active
        if (!$storyId) {
            $active = $this->progressModel->getActiveStory($characterId);
            if ($active)
                $storyId = $active['story_id'];
        }

        if (!$storyId) {
            error_log("[Exit Story] ABORT - No story ID found");
            header('Location: /game');
            exit;
        }

        $progress = $this->progressModel->getProgress($characterId, $storyId);
        error_log("[Exit Story] Progress found: " . ($progress ? "YES" : "NO"));
        
        if ($progress) {
            $node = $this->nodeModel->findById($progress['current_node_id']);
            error_log("[Exit Story] Node found: " . ($node ? "YES (id={$node['id']}, can_exit={$node['can_exit']})" : "NO"));
            
            // On permet TOUJOURS la sortie, peu importe can_exit
            if ($node) {
                // Vérifier si c'est une vraie complétion (avec dialogues) ou un easter egg
                $completedProperly = true;
                if (!empty($node['exit_condition_type']) && $node['exit_condition_type'] === 'npc_talked') {
                    $sessionKey = 'npc_interacted_' . $characterId . '_' . $node['id'];
                    error_log("[Exit Story] Checking completion - Session key: $sessionKey");
                    error_log("[Exit Story] Session value: " . json_encode($_SESSION[$sessionKey] ?? 'NOT SET'));
                    
                    if (!isset($_SESSION[$sessionKey]) || empty($_SESSION[$sessionKey])) {
                        // Marquer que le joueur est parti sans parler (easter egg)
                        $exitedWithoutTalkingKey = 'exited_without_talking_' . $characterId . '_' . $node['id'];
                        $_SESSION[$exitedWithoutTalkingKey] = true;
                        $completedProperly = false;
                        error_log("[Exit Story] Completion: FALSE (no NPC interaction) - Easter egg activated");
                    } else {
                        error_log("[Exit Story] Completion: TRUE (NPC interacted: " . json_encode($_SESSION[$sessionKey]) . ")");
                    }
                }

                // Sortie validée : Suppression complète de TOUTE la progression (progress + nodes + loots)
                error_log("[Exit Story] Resetting all progress for character $characterId, story $storyId");
                $deleted = $this->progressModel->resetProgress($characterId, $storyId);
                error_log("[Exit Story] Reset result: " . ($deleted ? "SUCCESS" : "FAILED"));
                
                // Vérification et suppression manuelle si resetProgress a échoué
                if (!$deleted) {
                    error_log("[Exit Story] resetProgress failed, trying manual DELETE");
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("DELETE FROM character_story_progress WHERE character_id = ? AND story_id = ?");
                    $stmt->bind_param("ii", $characterId, $storyId);
                    $manualDelete = $stmt->execute();
                    error_log("[Exit Story] Manual DELETE result: " . ($manualDelete ? "SUCCESS" : "FAILED"));
                }

                // Ne valider la quête quotidienne QUE si complétion correcte
                if ($completedProperly) {
                    error_log("[Exit Story] Completing dungeon properly - updating daily quests");
                    
                    // Mise à jour des quêtes quotidiennes (COMPLETE_DUNGEON)
                    $dailyQuestModel = new \App\Models\DailyQuest();
                    $dailyQuestModel->onDungeonCompleted($characterId, $storyId);
                } else {
                    error_log("[Exit Story] Exiting without proper completion (easter egg)");
                }
                
                // Nettoyer TOUTES les clés de session liées à ce donjon (pour la rejouabilité)
                error_log("[Exit Story] Cleaning session keys for all nodes");
                $nodes = $this->nodeModel->getByStoryId($storyId);
                foreach ($nodes as $node) {
                    $nid = $node['id'];
                    unset($_SESSION['killed_monsters_' . $characterId . '_' . $nid]);
                    unset($_SESSION['fled_monsters_' . $characterId . '_' . $nid]);
                    unset($_SESSION['npc_interacted_' . $characterId . '_' . $nid]);
                    unset($_SESSION['exited_without_talking_' . $characterId . '_' . $nid]);
                    
                    // Legacy support (old keys)
                    unset($_SESSION['killed_monsters_' . $nid]);
                    unset($_SESSION['fled_monsters_' . $nid]);
                    unset($_SESSION['npc_interacted_' . $nid]);
                    unset($_SESSION['exited_without_talking_' . $nid]);
                }
                error_log("[Exit Story] Cleaned session keys for " . count($nodes) . " nodes");

                // Si GET, rediriger vers la carte avec message de succès
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    if ($completedProperly) {
                        $_SESSION['success_message'] = 'Félicitations ! Vous avez terminé le donjon avec succès ! 🎉';
                    } else {
                        $_SESSION['success_message'] = 'Vous avez quitté le donjon... mais peut-être auriez-vous dû parler à quelqu\'un ? 🤔';
                    }
                    header('Location: /game');
                    exit;
                }

                echo json_encode(['success' => true, 'completed_properly' => $completedProperly]);
                exit;
            }
        }

        // Si GET, rediriger
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Location: /game');
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Sortie impossible ici']);
    }
    /**
     * Enregistre l'interaction avec un PNJ.
     * Renvoie l'arbre de dialogue du PNJ s'il en a un.
     */
    public function interactWithNPC()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $nodeId = $_POST['node_id'];
        $npcId = $_POST['npc_id'];

        // Bloquer l'interaction avec le roi Minos (NPC 1) tant qu'on n'a pas parlé à la princesse (NPC 3)
        if ($npcId == 1) {
            // Vérifier si le joueur a parlé à la princesse dans cette session de donjon
            $princessSessionKey = 'npc_interacted_' . $characterId . '_' . $nodeId;
            $hasTalkedToPrincess = false;
            
            // Parcourir tous les nœuds possibles pour voir si la princesse a été interagée
            $nodeModel = new \App\Models\StoryNode();
            $nodes = $nodeModel->getByStoryId($storyId);
            foreach ($nodes as $node) {
                $sessionKey = 'npc_interacted_' . $characterId . '_' . $node['id'];
                if (isset($_SESSION[$sessionKey]) && is_array($_SESSION[$sessionKey]) && in_array(3, $_SESSION[$sessionKey])) {
                    $hasTalkedToPrincess = true;
                    break;
                }
            }
            
            if (!$hasTalkedToPrincess) {
                error_log("[NPC Interaction] Blocked interaction with King Minos - Princess not talked yet");
                echo json_encode([
                    'success' => false, 
                    'message' => 'Le roi semble occupé... Peut-être devriez-vous d\'abord accomplir votre mission ? 🤔'
                ]);
                return;
            }
        }

        $sessionKey = 'npc_interacted_' . $characterId . '_' . $nodeId;
        if (!isset($_SESSION[$sessionKey]) || !is_array($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }

        if (!in_array($npcId, $_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey][] = $npcId;
        }

        // Mettre à jour les quêtes qui nécessitent de parler à ce NPC
        $pqModel = new \App\Models\PlayerQuest();
        $questUpdates = $pqModel->onNPCInteraction($characterId, $npcId);
        error_log("[NPC Interaction] Quest updates: " . json_encode($questUpdates));

        // Vérifier si le joueur est parti sans parler (easter egg)
        $exitedWithoutTalkingKey = 'exited_without_talking_' . $characterId . '_' . $nodeId;
        $hasExitedWithoutTalking = isset($_SESSION[$exitedWithoutTalkingKey]) && $_SESSION[$exitedWithoutTalkingKey];

        // Récupération du dialogue
        $npcModel = new NPC();
        $dialogueTreeModel = new DialogueTree();

        $trees = $npcModel->getDialogueTrees($npcId);
        error_log("[NPC Interaction] NPC ID: $npcId, Trees found: " . count($trees));
        
        $dialogueData = null;

        if (!empty($trees)) {
            // Choisir l'arbre en fonction de différentes conditions
            $selectedTree = null;
            
            // 1. Si le joueur est parti sans parler à la princesse
            if ($hasExitedWithoutTalking) {
                foreach ($trees as $tree) {
                    if (stripos($tree['name'], 'Mécontente') !== false || $tree['id'] == 6) {
                        $selectedTree = $tree;
                        unset($_SESSION[$exitedWithoutTalkingKey]);
                        error_log("[NPC Interaction] Easter egg dialogue selected for NPC $npcId");
                        break;
                    }
                }
            }
            
            // 2. Si c'est le roi Minos (NPC 1), vérifier si la quête est complétée
            if (!$selectedTree && $npcId == 1) {
                // Vérifier si le joueur a parlé à la princesse (objectif complété)
                $db = \App\Config\Database::getInstance()->getConnection();
                $stmt = $db->prepare("
                    SELECT COUNT(*) as completed
                    FROM player_quest_progress pqp
                    JOIN quest_objectives qo ON pqp.objective_id = qo.id
                    WHERE pqp.character_id = ? 
                    AND qo.type = 'TALK_NPC' 
                    AND qo.target_id = 3
                    AND pqp.current_progress >= qo.count_required
                ");
                $stmt->bind_param("i", $characterId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $hasTalkedToPrincess = $result['completed'] > 0;
                
                error_log("[NPC Interaction] King Minos - Has talked to princess: " . ($hasTalkedToPrincess ? "YES" : "NO"));
                
                // Sélectionner le dialogue approprié
                foreach ($trees as $tree) {
                    if ($hasTalkedToPrincess && stripos($tree['name'], 'Complétée') !== false) {
                        $selectedTree = $tree;
                        error_log("[NPC Interaction] Selected completion dialogue for King Minos");
                        break;
                    } else if (!$hasTalkedToPrincess && stripos($tree['name'], 'Complétée') === false) {
                        $selectedTree = $tree;
                        error_log("[NPC Interaction] Selected initial dialogue for King Minos");
                        break;
                    }
                }
            }
            
            // 3. Si aucun dialogue spécial, prendre le premier (dialogue par défaut)
            if (!$selectedTree) {
                $selectedTree = $trees[0];
            }

            $treeId = $selectedTree['id'];
            error_log("[NPC Interaction] Using tree ID: $treeId");
            
            $rootDialogues = $dialogueTreeModel->getRootDialogues($treeId);
            error_log("[NPC Interaction] Root dialogues found: " . count($rootDialogues));

            // On construit une structure simple pour le frontend
            if (!empty($rootDialogues)) {
                $dialogueData = [
                    'tree_id' => $treeId,
                    'root' => $rootDialogues[0], // Premier message
                    'title' => $selectedTree['name']
                ];

                // Récupérer les enfants (réponses possibles) du noeud racine
                $children = $dialogueTreeModel->getChildren($rootDialogues[0]['id']);
                $dialogueData['root']['choices'] = $children;
                
                error_log("[NPC Interaction] Dialogue data prepared with " . count($children) . " choices");
            }
        } else {
            error_log("[NPC Interaction] No dialogue trees found for NPC $npcId");
        }

        // Vérifier si on peut sortir automatiquement après ce dialogue
        // Si c'est la princesse (NPC 3) dans un noeud avec exit_condition_type='npc_talked'
        $node = $this->nodeModel->findById($nodeId);
        $autoExit = false;
        if ($npcId == 3 && $node && $node['can_exit'] && $node['exit_condition_type'] === 'npc_talked') {
            $autoExit = true;
            error_log("[NPC Interaction] Auto-exit enabled for princess dialogue");
        }
        
        // Log pour debug de la session
        error_log("[NPC Interaction] Session key $sessionKey = " . json_encode($_SESSION[$sessionKey]));
        
        // Si c'est le roi Minos et que le joueur a complété tous les objectifs, valider la quête
        if ($npcId == 1 && !empty($questUpdates)) {
            foreach ($questUpdates as $update) {
                // Vérifier si tous les objectifs de la quête sont complétés
                if (isset($update['quest_name']) && stripos($update['quest_name'], 'princesse') !== false) {
                    error_log("[NPC Interaction] Checking if all objectives completed for quest: {$update['quest_name']}");
                    
                    // Marquer la quête comme complétée si tous les objectifs sont faits
                    $allObjectivesComplete = $pqModel->checkQuestCompletion($characterId, $update['quest_name']);
                    if ($allObjectivesComplete) {
                        error_log("[NPC Interaction] Quest '{$update['quest_name']}' COMPLETED!");
                    }
                }
            }
        }
        
        // Force l'écriture de la session immédiatement pour éviter les problèmes de timing
        session_write_close();
        // Redémarre la session pour les requêtes suivantes
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        echo json_encode([
            'success' => true,
            'dialogue' => $dialogueData,
            'auto_exit' => $autoExit
        ]);
    }

    /**
     * Réinitialise la progression de l'histoire pour permettre de rejouer.
     */
    public function resetStory()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'] ?? null;

        if (!$storyId) {
            echo json_encode(['success' => false, 'message' => 'Missing Story ID']);
            return;
        }

        // Supprimer la progression en base de données
        if ($this->progressModel->resetProgress($characterId, $storyId)) {

            // Nettoyage des clés de session liées à cette histoire
            // Note: C'est une approche brutale mais efficace. 
            // On pourrait affiner en listant tous les noeuds de l'histoire mais c'est coûteux.

            // On parcourt toutes les clés de session pour trouver celles correspondant à ce char/story ?
            // Trop lourd. On va supposer que le joueur recommence à zéro et que les clés de session deviendront orphelines (TTL session) ou on les laisse.
            // Le plus important est que la progression DB soit reset. 
            // Les clés de session (killed_monsters_XXX) sont liées au NodeID.
            // Si on recommence, on repasse par les mêmes NodeID (pour une histoire manuelle).
            // Donc il FAUT nettoyer la session si on veut que les monstres réapparaissent.

            // Récupération des noeuds de l'histoire pour nettoyer la session
            $nodes = $this->nodeModel->getByStoryId($storyId);
            foreach ($nodes as $node) {
                $nid = $node['id'];
                unset($_SESSION['killed_monsters_' . $characterId . '_' . $nid]);
                unset($_SESSION['fled_monsters_' . $characterId . '_' . $nid]);
                unset($_SESSION['npc_interacted_' . $characterId . '_' . $nid]);

                // Legacy support (old keys)
                unset($_SESSION['killed_monsters_' . $nid]);
                unset($_SESSION['fled_monsters_' . $nid]);
                unset($_SESSION['npc_interacted_' . $nid]);
            }

            echo json_encode(['success' => true, 'message' => 'Histoire réinitialisée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la réinitialisation']);
        }
    }

    /**
     * Réinitialise la progression d'une histoire (après une défaite par exemple)
     */
    public function resetProgress()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'] ?? null;

        // Si storyId manquant, on tente de le récupérer depuis la progression active
        if (!$storyId) {
            $active = $this->progressModel->getActiveStory($characterId);
            if ($active)
                $storyId = $active['story_id'];
        }

        if ($storyId) {
            // Supprimer la progression
            $this->progressModel->deleteProgress($characterId, $storyId);

            // Nettoyer les sessions
            $keys = array_keys($_SESSION);
            foreach ($keys as $key) {
                if (
                    strpos($key, 'fled_monsters_') === 0 ||
                    strpos($key, 'killed_monsters_') === 0 ||
                    strpos($key, 'npc_interacted_') === 0
                ) {
                    unset($_SESSION[$key]);
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucune histoire active']);
        }
    }
}

