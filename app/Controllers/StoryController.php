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

        switch ($connection['condition_type']) {
            case 'item':
                // Check if player has item
                return $this->inventoryModel->hasItem($characterId, $connection['condition_value']);
            case 'level':
                // Check player level
                $character = $this->characterModel->findById($characterId);
                return $character['level'] >= (int)$connection['condition_value'];
            case 'quest_active':
                return true; // Placeholder
            case 'quest_completed':
                return true; // Placeholder
            case 'monster_killed':
                // Check if monster killed in current node
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
     * Collect loot
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

            // Add item to inventory
            $this->inventoryModel->addItem($characterId, $validLoot['item_id'], $validLoot['quantity']);
            
            // Mark as collected
            $this->progressModel->collectLoot($characterId, $nodeId, $lootId);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid loot']);
        }
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
