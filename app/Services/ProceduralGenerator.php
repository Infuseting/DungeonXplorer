<?php

namespace App\Services;

use App\Models\ProceduralTemplate;
use App\Models\StoryInstance;
use App\Models\StoryNode;
use App\Models\Story;
use App\Config\Database;

class ProceduralGenerator
{
    private $db;
    private $templateModel;
    private $instanceModel;
    private $nodeModel;
    private $storyModel;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->templateModel = new ProceduralTemplate();
        $this->instanceModel = new StoryInstance();
        $this->nodeModel = new StoryNode();
        $this->storyModel = new Story();
    }

    /**
     * Generate a new dungeon instance
     * 
     * @param int $storyId
     * @param int $characterId
     * @return int|false Instance ID
     */
    public function generate($storyId, $characterId)
    {
        // 1. Get Story and Template
        $story = $this->storyModel->findById($storyId);
        if (!$story || !$story['procedural_template_id']) {
            return false;
        }

        $template = $this->templateModel->findById($story['procedural_template_id']);
        if (!$template) {
            return false;
        }

        // 2. Create Story Instance
        $instanceData = [
            'story_id' => $storyId,
            'character_id' => $characterId,
            'current_node_id' => null, // Will update later
            'status' => 'active',
            'dungeon_data' => json_encode(['seed' => time()]) // Store seed if we want reproducibility later
        ];
        
        // Manual insert for instance to get ID (Model create might not support all fields yet? Let's check or use DB directly)
        // StoryInstance::create ($characterId, $storyId) exists? 
        // Let's use direct DB for safety to ensure we get ID.
        // Or check StoryInstance model. 
        // Assuming StoryInstance::create($data) or similar exists.
        // Let's implement robustly.
        $instanceId = $this->createInstance($storyId, $characterId);
        if (!$instanceId) return false;

        // 3. Generate Layout (Grid / Random Walk)
        $layout = $this->generateLayout($template);

        // 4. Persist Nodes and Connections
        $nodeMap = []; // Key: "x,y" => DB ID
        $startNodeId = null;

        foreach ($layout['rooms'] as $coord => $roomData) {
            $isStart = ($coord === $layout['start']);
            $isEnd = ($coord === $layout['end']);
            
            // Determine Room Theme/Image
            // Simple logic: Pick random image from template images (if any) or default
            // For now, use placeholder or pick from template->room_themes
            $imagePath = $this->pickRoomImage($template, $isStart, $isEnd);

            $nodeData = [
                'story_id' => $storyId,
                'story_instance_id' => $instanceId,
                'name' => $this->generateRoomName($isStart, $isEnd),
                'description' => $this->generateRoomDescription($isStart, $isEnd),
                'image_path' => $imagePath,
                'is_start_node' => $isStart ? 1 : 0,
                'is_end_node' => $isEnd ? 1 : 0,
                'can_exit' => $isEnd ? 1 : 0, // Only exit at end? Or Start? Usually Start allows exit, End allows "Finish".
                                              // Let's allow Exit at Start node.
                'node_x' => $roomData['x'],
                'node_y' => $roomData['y']
            ];
            
            // FORCE Start Node to be exitable back to menu
            if ($isStart) $nodeData['can_exit'] = 1;

            $nodeId = $this->nodeModel->create($nodeData);
            if ($nodeId) {
                $nodeMap[$coord] = $nodeId;
                if ($isStart) $startNodeId = $nodeId;
                
                // Populate Content (Monsters, Loot, Traps)
                if (!$isStart) { // Safe zone at start
                    $this->populateRoom($nodeId, $template, $isEnd);
                }
            }
        }

        // 5. Create Connections
        foreach ($layout['connections'] as $conn) {
            $fromCoord = $conn['from']; // "0,0"
            $toCoord = $conn['to'];     // "0,1"
            
            if (isset($nodeMap[$fromCoord]) && isset($nodeMap[$toCoord])) {
                $fromId = $nodeMap[$fromCoord];
                $toId = $nodeMap[$toCoord];
                
                $this->createConnection($fromId, $toId, $conn['direction']);
                // Bidirectional? Usually yes for grid.
                // Flip direction
                $flip = [
                    'Nord' => 'Sud', 'Sud' => 'Nord', 
                    'Est' => 'Ouest', 'Ouest' => 'Est'
                ];
                $backDir = $flip[$conn['direction']] ?? 'Retour';
                $this->createConnection($toId, $fromId, $backDir);
            }
        }

        return $instanceId;
    }

    private function createInstance($storyId, $characterId)
    {
        $stmt = $this->db->prepare("INSERT INTO story_instances (story_id, character_id, status) VALUES (?, ?, 'active')");
        $stmt->bind_param("ii", $storyId, $characterId);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    private function generateLayout($template)
    {
        $minRooms = $template['min_rooms'] ?? 5;
        $maxRooms = $template['max_rooms'] ?? 10;
        $targetRooms = rand($minRooms, $maxRooms);
        
        $rooms = [];
        $connections = [];
        
        // Start at 0,0
        $currentX = 0;
        $currentY = 0;
        $rooms["0,0"] = ['x' => 0, 'y' => 0];
        
        $directions = [
            ['x' => 0, 'y' => 1, 'name' => 'Nord'],
            ['x' => 0, 'y' => -1, 'name' => 'Sud'],
            ['x' => 1, 'y' => 0, 'name' => 'Est'],
            ['x' => -1, 'y' => 0, 'name' => 'Ouest']
        ];
        
        // Random Walk
        $sanity = 0;
        while (count($rooms) < $targetRooms && $sanity < 1000) {
            $sanity++;
            
            // Pick random direction
            $dir = $directions[array_rand($directions)];
            
            $nextX = $currentX + $dir['x'];
            $nextY = $currentY + $dir['y'];
            $key = "$nextX,$nextY";
            
            if (!isset($rooms[$key])) {
                $rooms[$key] = ['x' => $nextX, 'y' => $nextY];
                
                // Add connection
                $connections[] = [
                    'from' => "$currentX,$currentY",
                    'to' => "$nextX,$nextY",
                    'direction' => $dir['name']
                ];
                
                $currentX = $nextX;
                $currentY = $nextY;
            } else {
                // If backtracking allowed, maybe just move there?
                if ($template['allow_backtrack']) {
                    $currentX = $nextX;
                    $currentY = $nextY;
                }
            }
        }
        
        // Determine End Node (Furthest from start ideally, or just last placed)
        // Simple: Last placed is End.
        end($rooms);
        $endKey = key($rooms);
        
        return [
            'rooms' => $rooms,
            'connections' => $connections,
            'start' => "0,0",
            'end' => $endKey
        ];
    }

    private function populateRoom($nodeId, $template, $isBossRoom)
    {
        // 1. Monsters
        // Get Monster Pools
        $monsterPools = $this->templateModel->getMonsterPools($template['id']);
        
        // Determine if monster spawns (e.g. 50% chance regular room, 100% boss room)
        $spawnChance = $isBossRoom ? 100 : 50; 
        
        if (rand(1, 100) <= $spawnChance) {
            // Filter pools based on Boss logic
            $validPools = array_filter($monsterPools, function($p) use ($isBossRoom) {
                if ($p['boss_room_only'] && !$isBossRoom) return false;
                if ($isBossRoom && $p['is_boss']) return true; // Boss pool for boss room
                if ($isBossRoom && !$p['is_boss']) return false; // Only bosses in boss room? Or minions too?
                                                               // Let's simplify: Boss Room gets Boss Pools.
                return true;
            });
            
            if (!empty($validPools)) {
                // Weighted Random Selection
                $pool = $this->pickWeighted($validPools);
                if ($pool) {
                    $qty = rand($pool['min_quantity'], $pool['max_quantity']);
                    $this->nodeModel->addMonster($nodeId, [
                        'name' => $pool['monster_name'],
                        'level' => rand($pool['min_level'], $pool['max_level']),
                        'stats' => $pool['monster_stats_base'], // Should scale?
                        'quantity' => $qty,
                        'is_boss' => $pool['is_boss'],
                        'can_flee' => $pool['is_boss'] ? 0 : 1
                    ]);
                }
            }
        }
        
        // 2. Traps
        // 20% Chance
        if (rand(1, 100) <= 20) {
             $this->nodeModel->addTrap($nodeId, [
                 'description' => "Un mécanisme suspect...",
                 'damage_dice' => '1d6',
                 'effect_text' => 'Une fléchette vous touche !',
                 'avoid_stat' => 'DEX',
                 'difficulty_class' => 12
             ]);
        }
        
        // 3. Loot
        // 30% Chance (100% if Boss)
        $lootChance = $isBossRoom ? 100 : 30;
        if (rand(1, 100) <= $lootChance) {
            $lootPools = $this->templateModel->getLootPools($template['id']);
            $validLoot = array_filter($lootPools, function($p) use ($isBossRoom) {
                if ($p['boss_loot_only'] && !$isBossRoom) return false;
                return true;
            });
            
            if (!empty($validLoot)) {
                $pool = $this->pickWeighted($validLoot, 'drop_weight');
                if ($pool) {
                    $qty = rand($pool['min_quantity'], $pool['max_quantity']);
                    $this->nodeModel->addLoot($nodeId, $pool['item_id'], $qty, 1.0, 0); // 100% chance once selected
                }
            }
        }
    }

    private function createConnection($fromId, $toId, $text)
    {
        $stmt = $this->db->prepare("INSERT INTO story_node_connections (from_node_id, to_node_id, action_text, direction_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $fromId, $toId, $text, $text);
        $stmt->execute();
    }
    
    // Helpers
    private function pickWeighted($items, $weightKey = 'spawn_weight') {
        $total = 0;
        foreach ($items as $item) $total += $item[$weightKey];
        $rand = rand(1, $total);
        foreach ($items as $item) {
            $rand -= $item[$weightKey];
            if ($rand <= 0) return $item;
        }
        return reset($items);
    }
    
    private function generateRoomName($isStart, $isEnd) {
        if ($isStart) return "Entrée du Donjon";
        if ($isEnd) return "Antre du Boss";
        
        $names = ["Couloir Sombre", "Salle Humide", "Ancienne Crypte", "Passage Étroit", "Salle des Gardes", "Armurerie Abandonnée"];
        return $names[array_rand($names)];
    }
    
    private function generateRoomDescription($isStart, $isEnd) {
        if ($isStart) return "Vous êtes au début de votre exploration. L'air est vicié.";
        if ($isEnd) return "Une aura menaçante règne ici. Vous sentez une présence puissante.";
        return "Une pièce sombre aux murs de pierre suintants.";
    }
    
    private function pickRoomImage($template, $isStart, $isEnd) {
        // Placeholder or real logic
        if ($isStart) return "/assets/images/cave_start.webp";
        if ($isEnd) return "/assets/images/cave_boss.webp";
        return "/assets/images/cave_room.webp";
    }
}
