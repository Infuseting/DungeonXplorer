<?php

namespace App\Controllers;

use App\Models\Story;
use App\Models\StoryNode;
use App\Models\StoryProgress;
use App\Models\StoryInstance;
use App\Models\Character;
use App\Models\Inventory;

class StoryController
{
    private $storyModel;
    private $nodeModel;
    private $progressModel;
    private $instanceModel;
    private $characterModel;
    private $inventoryModel;

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
     * Enter a story from a map point
     */
    public function enterStory($storyId)
    {
        $characterId = $_SESSION['character_id'];
        $story = $this->storyModel->findById($storyId);
        
        if (!$story) {
            header('Location: /game');
            exit;
        }

        // Check if progress exists
        $progress = $this->progressModel->getProgress($characterId, $storyId);
        
        if (!$progress) {
            // Start new story
            if ($story['type'] === 'manual') {
                $startNode = $this->storyModel->getStartNode($storyId);
                if (!$startNode) {
                    // Error: Manual story has no start node
                    header('Location: /game');
                    exit;
                }
                $this->progressModel->startStory($characterId, $storyId, $startNode['id']);
            } else {
                // Procedural story logic
                $instance = $this->instanceModel->getByStoryAndCharacter($storyId, $characterId);
                
                if (!$instance) {
                    // Generate new instance
                    $generator = new \App\Services\ProceduralGenerator();
                    $instanceId = $generator->generate($storyId, $characterId);
                    
                    if (!$instanceId) {
                        // Error generating
                        header('Location: /game?error=generation_failed');
                        exit;
                    }
                    $instance = $this->instanceModel->findById($instanceId);
                }
                
                // Find start node for this instance
                $startNode = $this->nodeModel->getInstanceStartNode($instance['id']);
                if ($startNode) {
                     $this->progressModel->startStory($characterId, $storyId, $startNode['id']);
                } else {
                     header('Location: /game?error=no_start_node');
                     exit;
                }
            }
        }

        // Load inventory for the view
        $inventory = $this->inventoryModel->getCharacterInventory($characterId);

        // Redirect to story view
        $this->render('game/story', [
            'story' => $story,
            'inventory' => $inventory
        ]);

        // Log Entry
        $logger = new \App\Services\LoggerService();
        $logger->logGameplay($_SESSION['user_id'], $_SESSION['character_id'], 'DUNGEON_ENTER', [
            'story_id' => $storyId,
            'story_title' => $story['title']
        ]);
    }

    /**
     * Get current node data (AJAX)
     */
    public function getCurrentNode()
    {
        $characterId = $_SESSION['character_id'];
        // We assume the story ID is passed or stored in session, 
        // but for now let's get the active story from progress
        // This query might need optimization or a specific method in ProgressModel
        // For simplicity, let's assume the frontend passes the storyId or we find the last updated progress
        
        // Alternative: The view calls this with story_id
        $storyId = $_GET['story_id'] ?? null;
        if (!$storyId) {
            echo json_encode(['error' => 'No story ID provided']);
            exit;
        }

        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if (!$progress) {
            echo json_encode(['error' => 'No progress found']);
            exit;
        }

        $node = $this->nodeModel->getFullNodeData($progress['current_node_id']);
        $nodeStatus = $this->progressModel->getNodeStatus($characterId, $node['id']);
        
        // Mark as visited if not already
        if (!$nodeStatus || !$nodeStatus['is_visited']) {
            $this->progressModel->markNodeVisited($characterId, $node['id']);
        }

        // Get fled monsters for this session/node
        $sessionKey = 'fled_monsters_' . $node['id'];
        $fledMonsters = $_SESSION[$sessionKey] ?? [];

        // Filter loots: remove already collected ones
        if (!empty($node['loots'])) {
            foreach ($node['loots'] as $key => $loot) {
                if ($this->progressModel->hasCollectedLoot($characterId, $node['id'], $loot['id'])) {
                    unset($node['loots'][$key]);
                }
            }
            $node['loots'] = array_values($node['loots']);
        }

        // Filter monsters: remove if cleared
        if ($nodeStatus && $nodeStatus['monsters_cleared']) {
            $node['monsters'] = [];
        } else {
            // Enforce Order: Monsters > NPCs > Loot
            // If monsters are present and not cleared, hide NPCs and Loot
            if (!empty($node['monsters'])) {
                $node['npcs'] = [];
                $node['loots'] = [];
                
                // Add can_flee info if missing (it should be in * usually)
                // Assuming story_node_monsters has the column, it's already in $node['monsters']
            }
        }

        // Get traps
        $node['traps'] = $this->nodeModel->getTraps($node['id']);
        
        // Check accessibility for all connections
        if (!empty($node['connections'])) {
            foreach ($node['connections'] as &$conn) {
                $conn['is_accessible'] = $this->checkCondition($conn, $characterId);
                
                // Add human-readable reason if locked
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
            'fled_monsters' => $fledMonsters
        ]);
    }

    /**
     * Move to another node
     */
    public function moveToNode()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $nodeId = $_POST['node_id'];
        
        error_log("Move request: Story $storyId, Node $nodeId, Char $characterId");

        // Verify connection exists from current node
        $progress = $this->progressModel->getProgress($characterId, $storyId);
        
        if (!$progress) {
            error_log("No progress found");
            echo json_encode(['success' => false, 'message' => 'No progress found']);
            return;
        }

        $currentNodeId = $progress['current_node_id'];
        
        // Custom Logic: Check if player can leave the room (Monsters cleared OR ALL Monsters fled)
        $nodeStatus = $this->progressModel->getNodeStatus($characterId, $currentNodeId);
        $monsters = $this->nodeModel->getMonsters($currentNodeId);
        
        $canMove = true;
        if (!empty($monsters)) {
            $areMonstersCleared = $nodeStatus && $nodeStatus['monsters_cleared'];
            if (!$areMonstersCleared) {
                // Check if all monsters are fled
                $sessionKey = 'fled_monsters_' . $currentNodeId;
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
        
        $connections = $this->nodeModel->getConnections($currentNodeId);
        $returnConnections = $this->nodeModel->getReturnConnections($currentNodeId);
        $allConnections = array_merge($connections, $returnConnections);
        
        $validMove = false;
        foreach ($allConnections as $conn) {
            if ($conn['to_node_id'] == $nodeId) {
                // Check conditions
                if ($this->checkCondition($conn, $characterId)) {
                    $validMove = true;
                } else {
                    error_log("Condition failed for connection to $nodeId");
                }
                break;
            }
        }

        if ($validMove) {
            // Clear fled status for this node on exit
            $sessionKey = 'fled_monsters_' . $currentNodeId;
            if (isset($_SESSION[$sessionKey])) {
                unset($_SESSION[$sessionKey]);
            }

            $this->progressModel->updateProgress($characterId, $storyId, $nodeId);
            echo json_encode(['success' => true]);
        } else {
            error_log("Invalid move or conditions not met");
            echo json_encode(['success' => false, 'message' => 'Déplacement invalide ou conditions non remplies']);
        }
    }

    /**
     * Check connection condition
     */
    private function checkCondition($connection, $characterId)
    {
        if ($connection['condition_type'] === 'none') return true;

        // Stat Check (Format: stat_strength, stat_dexterity, etc.)
        if (strpos($connection['condition_type'], 'stat_') === 0) {
            $statName = substr($connection['condition_type'], 5); // e.g. "strength"
            
            $statsModel = new \App\Models\CharacterStats();
            $effectiveStats = $statsModel->getEffectiveStats($characterId);
            
            if (!$effectiveStats) return false;
            
            $currentVal = $effectiveStats[$statName] ?? 0;
            $requiredVal = (int)$connection['condition_value'];
            
            // Assume requirement is "At least X" (>=)
            return $currentVal >= $requiredVal;
        }

        switch ($connection['condition_type']) {
            case 'item':
                // Check if player has item
                return $this->inventoryModel->hasItem($characterId, $connection['condition_value']);
            case 'level':
                // Check player level
                $character = $this->characterModel->findById($characterId);
                return $character['level'] >= (int)$connection['condition_value'];
            case 'class':
                $character = $this->characterModel->findById($characterId);
                // condition_value could be class ID or Name? Let's assume ID for robustness or handle both
                return $character['class_id'] == $connection['condition_value'];
            case 'quest_active':
                return true; // Placeholder
            case 'quest_completed':
                return true; // Placeholder
            case 'monster_killed':
                // Check if monster killed in current node
                // Note: Logic suggests looking at FROM node, which is where we are.
                $nodeStatus = $this->progressModel->getNodeStatus($characterId, $connection['from_node_id']);
                return $nodeStatus && $nodeStatus['monsters_cleared'];
            default:
                return true;
        }
    }

    /**
     * Attempt to flee from a monster
     */
    public function attemptFlee()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $monsterId = $_POST['monster_id']; // ID in story_node_monsters table

        // Get monster details to check can_flee and difficulty
        // We need a method to get specific monster instance.
        // For now, we'll fetch all monsters in the node and find it.
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

        // Flee mechanics
        // Chance = 50% + (Player Dex - Monster Level) * 2%
        // Get Player Stats
        $statsModel = new \App\Models\CharacterStats();
        $stats = $statsModel->getEffectiveStats($characterId);
        
        // Fallback if stats not found (e.g. new char?)
        $dexterity = $stats ? $stats['dexterity'] : 10;
        
        $monsterLevel = $targetMonster['monster_level'];
        
        $baseChance = 50;
        $bonus = ($dexterity - $monsterLevel) * 2;
        $chance = $baseChance + $bonus;
        
        // Clamp
        $chance = max(5, min(95, $chance));
        
        $roll = rand(1, 100);
        $success = $roll <= $chance;
        
        if ($success) {
            $sessionKey = 'fled_monsters_' . $currentNodeId;
            if (!isset($_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey] = [];
            }
            if (!in_array($monsterId, $_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey][] = $monsterId;
            }
        } else {
             $_SESSION['combat_initiative'] = 'enemy';
        }

        echo json_encode([
            'success' => $success,
            'roll' => $roll,
            'chance' => $chance,
            'force_combat' => !$success,
            'message' => $success ? "Vous avez pris la fuite (Monstre unique) !" : "Échec de la fuite ! Le monstre vous bloque."
        ]);
    }

    /**
     * Mark room cleared (all monsters defeated/fled)
     */
    public function clearMonsters()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        
        // Simple security: verify we are in a node with monsters
        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if ($progress) {
            $this->progressModel->markNodeCleared($characterId, $progress['current_node_id']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    /**
     * Attempt to avoid/disarm a room trap
     */
    public function attemptTrapAvoidance()
    {
        $characterId = $_SESSION['character_id'];
        $trapId = $_POST['trap_id'];
        
        // Fetch trap details
        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM story_node_traps WHERE id = ?");
        $stmt->bind_param("i", $trapId);
        $stmt->execute();
        $trap = $stmt->get_result()->fetch_assoc();
        
        if (!$trap) {
            echo json_encode(['success' => false, 'message' => 'Piège introuvable']);
            exit;
        }

        // Calculate Roll
        $statsModel = new \App\Models\CharacterStats();
        $stats = $statsModel->getEffectiveStats($characterId);
        
        // Map stat name to valid column (e.g. 'DEX' -> 'dexterity')
        $statMap = [
            'DEX' => 'dexterity',
            'STR' => 'strength',
            'INT' => 'intelligence',
            'WIS' => 'wisdom',
            'CON' => 'constitution'
        ];
        $statName = $statMap[$trap['avoid_stat']] ?? 'dexterity';
        $statValue = $stats[$statName] ?? 10;
        
        // Simple D20 + Stat Mod mechanic
        // Mod = (Score - 10) / 2
        $mod = floor(($statValue - 10) / 2);
        $roll = rand(1, 20);
        $total = $roll + $mod;
        
        $success = $total >= $trap['difficulty_class'];
        $damageTaken = 0;
        
        if (!$success) {
            // Apply damage
            // Parse dice (e.g. "1d6")
            $parts = explode('d', $trap['damage_dice']);
            $count = (int)$parts[0]; // 1
            $faces = (int)($parts[1] ?? 6); // 6
            
            for ($i=0; $i<$count; $i++) {
                $damageTaken += rand(1, $faces);
            }
            
            // Reduce HP
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
     * Collect loot (Updated with Trap Logic)
     */
    public function collectLoot()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $nodeId = $_POST['node_id'];
        $lootId = $_POST['loot_id'];

        // Enforce: Monsters must be cleared
        $nodeStatus = $this->progressModel->getNodeStatus($characterId, $nodeId);
        $monsters = $this->nodeModel->getMonsters($nodeId);
        
        if (!empty($monsters)) {
            if (!$nodeStatus || !$nodeStatus['monsters_cleared']) {
                 echo json_encode(['success' => false, 'message' => 'Vous devez d\'abord vaincre les monstres !']);
                 return;
            }
        }

        // Verify loot exists in node
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

            // Check Trap
            if ($validLoot['is_trapped']) {
                $statsModel = new \App\Models\CharacterStats();
                $stats = $statsModel->getEffectiveStats($characterId);
                $dex = $stats['dexterity'] ?? 10;
                $mod = floor(($dex - 10) / 2);
                
                $roll = rand(1, 20);
                $total = $roll + $mod;
                
                if ($total < $validLoot['trap_dc']) {
                    $trapTriggered = true;
                    // Calculate damage
                    $parts = explode('d', $validLoot['trap_damage']);
                    $count = (int)$parts[0];
                    $faces = (int)($parts[1] ?? 4);
                    for ($i=0; $i<$count; $i++) {
                        $damageTaken += rand(1, $faces);
                    }
                    $this->characterModel->takeDamage($characterId, $damageTaken);
                    $trapMessage = "Le coffre était piégé ! " . ($validLoot['trap_description'] ?: "Vous subissez des dégâts.");
                }
            }

            // Add item to inventory
            $this->inventoryModel->addItem($characterId, $validLoot['item_id'], $validLoot['quantity']);
            
            // Mark as collected
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
}
    /**
     * Search the room (Fouiller)
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

        // Calculate Perception / Investigation Roll
        $statsModel = new \App\Models\CharacterStats();
        $stats = $statsModel->getEffectiveStats($characterId);
        $wis = $stats['wisdom'] ?? 10;
        $int = $stats['intelligence'] ?? 10;
        
        // Use higher of INT or WIS
        $mod = floor((max($wis, $int) - 10) / 2);
        $roll = rand(1, 20);
        $total = $roll + $mod;

        $message = "Vous fouillez minutieusement la pièce...";
        $foundTraps = []; // IDs of found traps
        $triggeredTrap = null;
        $damageTaken = 0;

        foreach ($traps as $trap) {
            // Trap Detection Logic
            // If total < DC: Fail to find (or trigger if very low?)
            // Let's say: If total < DC - 5 => Trigger!
            // If total >= DC => Find
            
            if ($total >= $trap['difficulty_class']) {
                $foundTraps[] = $trap['id'];
                $message .= " Vous repérez un piège !";
            } elseif ($total < $trap['difficulty_class'] - 5) {
                // Critical Fail - Trigger
                $triggeredTrap = $trap;
                break; // Stop searching if you trigger a trap
            }
        }

        if ($triggeredTrap) {
            // Apply damage
            $parts = explode('d', $triggeredTrap['damage_dice']);
            $count = (int)$parts[0]; 
            $faces = (int)($parts[1] ?? 6);
            
            for ($i=0; $i<$count; $i++) {
                $damageTaken += rand(1, $faces);
            }
            $this->characterModel->takeDamage($characterId, $damageTaken);
            $message = "En fouillant, vous déclenchez un piège ! " . $triggeredTrap['effect_text'];
            
            return echo json_encode([
                'success' => true,
                'action' => 'triggered',
                'damage' => $damageTaken,
                'message' => $message,
                'found_loot' => !empty($loots) // Still reveal loot usually? Let's say yes.
            ]);
        }

        // If no trap triggered, we find loot (always find loot if we survive the search?)
        // Or maybe hidden loot needs a check too. For now, we reveal all loot.
        
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
     * Exit story
     */
    public function exitStory()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'] ?? null; // Should be passed
        
        // We need storyId. If not passed, try to find active story
        if (!$storyId) {
            $active = $this->progressModel->getActiveStory($characterId);
            if ($active) $storyId = $active['story_id'];
        }

        if (!$storyId) {
            header('Location: /game');
            exit;
        }

        // Check if current node allows exit
        $progress = $this->progressModel->getProgress($characterId, $storyId);
        if ($progress) {
            $node = $this->nodeModel->findById($progress['current_node_id']);
            if ($node && $node['can_exit']) {
                $this->progressModel->exitDungeon($characterId, $storyId);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Sortie impossible ici']);
    }
}
