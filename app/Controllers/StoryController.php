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
        }

        echo json_encode([
            'node' => $node,
            'status' => $nodeStatus
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
        error_log("Current node: $currentNodeId");
        
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
            $this->progressModel->updateProgress($characterId, $storyId, $nodeId);
            echo json_encode(['success' => true]);
        } else {
            error_log("Invalid move or conditions not met");
            echo json_encode(['success' => false, 'message' => 'Invalid move or conditions not met']);
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
                // Check if quest is active
                // Need QuestModel method for this
                return true; // Placeholder
            case 'quest_completed':
                // Check if quest is completed
                return true; // Placeholder
            case 'monster_killed':
                // Check if monster killed in current node (or specific node?)
                // Usually refers to clearing the room
                $nodeStatus = $this->progressModel->getNodeStatus($characterId, $connection['from_node_id']);
                return $nodeStatus && $nodeStatus['monsters_cleared'];
            default:
                return true;
        }
    }

    /**
     * Collect loot
     */
    /**
     * Collect loot
     */
    public function collectLoot()
    {
        $characterId = $_SESSION['character_id'];
        $storyId = $_POST['story_id'];
        $nodeId = $_POST['node_id'];
        $lootId = $_POST['loot_id'];

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
