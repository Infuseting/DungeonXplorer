<?php

namespace App\Services;

use App\Models\ProceduralTemplate;
use App\Models\StoryInstance;
use App\Models\StoryNode;
use App\Models\Story;

class ProceduralGenerator
{
    private $templateModel;
    private $instanceModel;
    private $nodeModel;
    private $storyModel;

    public function __construct()
    {
        $this->templateModel = new ProceduralTemplate();
        $this->instanceModel = new StoryInstance();
        $this->nodeModel = new StoryNode();
        $this->storyModel = new Story();
    }

    /**
     * Generate a new dungeon instance
     * 
     * @param int $storyId
     * @param int|null $characterId
     * @return int|false Instance ID
     */
    public function generate($storyId, $characterId = null)
    {
        // 1. Get Story and Template
        $story = $this->storyModel->findById($storyId);
        if (!$story || $story['type'] !== 'procedural' || !$story['procedural_template_id']) {
            return false;
        }

        $template = $this->templateModel->findById($story['procedural_template_id']);
        if (!$template) return false;

        // 2. Create Instance
        $seed = mt_rand(); // Or use provided seed
        srand($seed); // Initialize RNG
        
        $instanceType = $characterId ? 'character' : 'shared';
        $instanceId = $this->instanceModel->create($storyId, $characterId, $seed, $instanceType);
        
        if (!$instanceId) return false;

        // 3. Generate Graph
        $nodes = $this->generateGraph($template);

        // 4. Save Nodes to DB
        $nodeIdMap = []; // Map internal index to DB ID
        
        foreach ($nodes as $index => $nodeData) {
            $data = [
                'story_id' => $storyId,
                'story_instance_id' => $instanceId,
                'name' => $nodeData['name'],
                'description' => $nodeData['description'],
                'image_path' => $nodeData['image_path'],
                'is_start_node' => $nodeData['is_start'] ? 1 : 0,
                'is_end_node' => $nodeData['is_end'] ? 1 : 0,
                'node_x' => $nodeData['x'] * 200, // Scale for visual
                'node_y' => $nodeData['y'] * 200
            ];
            
            $dbId = $this->nodeModel->create($data);
            $nodeIdMap[$index] = $dbId;
        }

        // 5. Save Connections
        // Need to access the raw DB or add method to NodeModel for connections
        // For now, I'll assume I can use a raw query or add a method.
        // Let's add a helper method in StoryNode model later or use raw query here if possible.
        // Actually, I should use the model. I'll add `addConnection` to StoryNode model or use raw SQL here.
        // Since I don't want to modify the model right now, I'll use a direct query via the model's db connection if accessible, 
        // or just instantiate Database.
        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO story_node_connections (from_node_id, to_node_id, direction_text) VALUES (?, ?, ?)");

        foreach ($nodes as $index => $nodeData) {
            $fromId = $nodeIdMap[$index];
            foreach ($nodeData['connections'] as $targetIndex => $direction) {
                $toId = $nodeIdMap[$targetIndex];
                $stmt->bind_param("iis", $fromId, $toId, $direction);
                $stmt->execute();
            }
        }

        // 6. Populate Content (Monsters, Loot)
        $monsterPool = $this->templateModel->getMonsterPools($template['id']);
        $lootPool = $this->templateModel->getLootPools($template['id']);

        foreach ($nodes as $index => $nodeData) {
            $dbId = $nodeIdMap[$index];
            
            // Monsters
            if (!$nodeData['is_start'] && !empty($monsterPool)) {
                // Chance to spawn monster
                if (rand(0, 100) < 60 || $nodeData['is_end']) { // 60% chance or guaranteed for boss
                    $this->spawnMonsters($dbId, $monsterPool, $nodeData['is_end']);
                }
            }

            // Loot
            if (!empty($lootPool)) {
                if (rand(0, 100) < 40 || $nodeData['is_end']) { // 40% chance
                    $this->spawnLoot($dbId, $lootPool, $nodeData['is_end']);
                }
            }
        }

        return $instanceId;
    }

    private function generateGraph($template)
    {
        $minRooms = $template['min_rooms'];
        $maxRooms = $template['max_rooms'];
        $targetRooms = rand($minRooms, $maxRooms);
        
        $nodes = [];
        // Start Node
        $nodes[0] = [
            'x' => 0, 'y' => 0, 
            'is_start' => true, 'is_end' => false, 
            'connections' => [],
            'name' => 'Entrée',
            'description' => 'Le début de votre aventure.',
            'image_path' => '' // TODO: Pick from image pool
        ];

        $directions = [
            'north' => ['x' => 0, 'y' => -1, 'opp' => 'south', 'text' => 'Nord'],
            'south' => ['x' => 0, 'y' => 1, 'opp' => 'north', 'text' => 'Sud'],
            'east' => ['x' => 1, 'y' => 0, 'opp' => 'west', 'text' => 'Est'],
            'west' => ['x' => -1, 'y' => 0, 'opp' => 'east', 'text' => 'Ouest']
        ];

        $queue = [0];
        $occupied = ['0,0' => 0];
        $createdCount = 1;

        while ($createdCount < $targetRooms && !empty($queue)) {
            $currentIndex = array_shift($queue); // BFS for spread, or array_pop for DFS (linear)
            // Let's mix it up: random pick from queue for organic growth?
            // For now BFS is fine.
            
            $currentX = $nodes[$currentIndex]['x'];
            $currentY = $nodes[$currentIndex]['y'];

            // Try to add neighbors
            $possibleDirs = array_keys($directions);
            shuffle($possibleDirs);

            foreach ($possibleDirs as $dirKey) {
                if ($createdCount >= $targetRooms) break;

                $dir = $directions[$dirKey];
                $newX = $currentX + $dir['x'];
                $newY = $currentY + $dir['y'];
                $key = "$newX,$newY";

                if (!isset($occupied[$key])) {
                    // Create new node
                    $newIndex = count($nodes);
                    $nodes[$newIndex] = [
                        'x' => $newX, 'y' => $newY,
                        'is_start' => false, 'is_end' => false,
                        'connections' => [],
                        'name' => 'Salle ' . $newIndex,
                        'description' => 'Une salle sombre et humide.',
                        'image_path' => ''
                    ];
                    
                    // Connect
                    $nodes[$currentIndex]['connections'][$newIndex] = $dir['text'];
                    $nodes[$newIndex]['connections'][$currentIndex] = $directions[$dir['opp']]['text'];

                    $occupied[$key] = $newIndex;
                    $queue[] = $newIndex;
                    $createdCount++;
                } else {
                    // Node exists, maybe connect if density allows (loops)
                    if ($template['allow_loops'] && rand(0, 100) < ($template['connection_density'] * 100)) {
                        $neighborIndex = $occupied[$key];
                        // Avoid duplicate connections
                        if (!isset($nodes[$currentIndex]['connections'][$neighborIndex])) {
                            $nodes[$currentIndex]['connections'][$neighborIndex] = $dir['text'];
                            $nodes[$neighborIndex]['connections'][$currentIndex] = $directions[$dir['opp']]['text'];
                        }
                    }
                }
            }
            
            // Re-add current to queue if it has open sides? 
            // No, standard BFS/Prim's doesn't need that.
            // But to ensure we reach target, we might need to be aggressive.
            if (empty($queue) && $createdCount < $targetRooms) {
                // Pick a random existing node to branch from
                $queue[] = rand(0, count($nodes) - 1);
            }
        }

        // Mark End Node (furthest from start)
        $maxDist = 0;
        $endIndex = 0;
        foreach ($nodes as $index => $node) {
            $dist = abs($node['x']) + abs($node['y']); // Manhattan distance
            if ($dist > $maxDist) {
                $maxDist = $dist;
                $endIndex = $index;
            }
        }
        $nodes[$endIndex]['is_end'] = true;
        $nodes[$endIndex]['name'] = 'Salle du Boss';
        $nodes[$endIndex]['description'] = 'Une aura menaçante règne ici.';

        return $nodes;
    }

    private function spawnMonsters($nodeId, $pool, $isBossRoom)
    {
        // Filter pool
        $candidates = array_filter($pool, function($m) use ($isBossRoom) {
            return $isBossRoom ? ($m['is_boss'] || $m['boss_room_only']) : (!$m['is_boss'] && !$m['boss_room_only']);
        });
        
        if (empty($candidates) && $isBossRoom) {
            // Fallback: use any monster if no boss defined
             $candidates = $pool;
        }

        if (empty($candidates)) return;

        // Pick one (weighted)
        $monster = $candidates[array_rand($candidates)]; // Simple random for now
        
        // Insert
        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO story_node_monsters (node_id, monster_name, monster_level, quantity, is_boss) VALUES (?, ?, ?, ?, ?)");
        
        $qty = rand($monster['min_quantity'], $monster['max_quantity']);
        $stmt->bind_param("isiis", $nodeId, $monster['monster_name'], $monster['min_level'], $qty, $monster['is_boss']);
        $stmt->execute();
    }

    private function spawnLoot($nodeId, $pool, $isBossRoom)
    {
        // Similar logic for loot
        $candidates = array_filter($pool, function($l) use ($isBossRoom) {
            return $isBossRoom ? $l['boss_loot_only'] : !$l['boss_loot_only'];
        });
        
        if (empty($candidates)) return;

        $loot = $candidates[array_rand($candidates)];
        
        $db = \App\Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO story_node_loots (node_id, item_id, quantity, drop_chance) VALUES (?, ?, ?, ?)");
        
        $qty = rand($loot['min_quantity'], $loot['max_quantity']);
        $chance = 1.0; // Guaranteed if spawned
        $stmt->bind_param("iiid", $nodeId, $loot['item_id'], $qty, $chance);
        $stmt->execute();
    }
}
