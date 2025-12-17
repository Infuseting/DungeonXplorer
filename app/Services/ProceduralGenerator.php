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
                $story = $this->storyModel->findById($storyId);
        if (!$story || !$story['procedural_template_id']) {
            return false;
        }

        $template = $this->templateModel->findById($story['procedural_template_id']);
        if (!$template) {
            return false;
        }

                $instanceData = [
            'story_id' => $storyId,
            'character_id' => $characterId,
            'current_node_id' => null,             'status' => 'active',
            'dungeon_data' => json_encode(['seed' => time()])         ];
        
                                                        $instanceId = $this->createInstance($storyId, $characterId);
        if (!$instanceId) return false;

                $layout = $this->generateLayout($template);

                $nodeMap = [];         $startNodeId = null;

        foreach ($layout['rooms'] as $coord => $roomData) {
            $isStart = ($coord === $layout['start']);
            $isEnd = ($coord === $layout['end']);
            
                                                $imagePath = $this->pickRoomImage($template, $isStart, $isEnd);

            $nodeData = [
                'story_id' => $storyId,
                'story_instance_id' => $instanceId,
                'name' => $this->generateRoomName($isStart, $isEnd),
                'description' => $this->generateRoomDescription($isStart, $isEnd),
                'image_path' => $imagePath,
                'is_start_node' => $isStart ? 1 : 0,
                'is_end_node' => $isEnd ? 1 : 0,
                'can_exit' => $isEnd ? 1 : 0,                                                               'node_x' => $roomData['x'],
                'node_y' => $roomData['y']
            ];
            
                        if ($isStart) $nodeData['can_exit'] = 1;

            $nodeId = $this->nodeModel->create($nodeData);
            if ($nodeId) {
                $nodeMap[$coord] = $nodeId;
                if ($isStart) $startNodeId = $nodeId;
                
                                if (!$isStart) {                     $this->populateRoom($nodeId, $template, $isEnd);
                }
            }
        }

                foreach ($layout['connections'] as $conn) {
            $fromCoord = $conn['from'];             $toCoord = $conn['to'];                 
            if (isset($nodeMap[$fromCoord]) && isset($nodeMap[$toCoord])) {
                $fromId = $nodeMap[$fromCoord];
                $toId = $nodeMap[$toCoord];
                
                $this->createConnection($fromId, $toId, $conn['direction']);
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
        
                $currentX = 0;
        $currentY = 0;
        $rooms["0,0"] = ['x' => 0, 'y' => 0];
        
        $directions = [
            ['x' => 0, 'y' => 1, 'name' => 'Nord'],
            ['x' => 0, 'y' => -1, 'name' => 'Sud'],
            ['x' => 1, 'y' => 0, 'name' => 'Est'],
            ['x' => -1, 'y' => 0, 'name' => 'Ouest']
        ];
        
                $sanity = 0;
        while (count($rooms) < $targetRooms && $sanity < 1000) {
            $sanity++;
            
                        $dir = $directions[array_rand($directions)];
            
            $nextX = $currentX + $dir['x'];
            $nextY = $currentY + $dir['y'];
            $key = "$nextX,$nextY";
            
            if (!isset($rooms[$key])) {
                $rooms[$key] = ['x' => $nextX, 'y' => $nextY];
                
                                $connections[] = [
                    'from' => "$currentX,$currentY",
                    'to' => "$nextX,$nextY",
                    'direction' => $dir['name']
                ];
                
                $currentX = $nextX;
                $currentY = $nextY;
            } else {
                                if ($template['allow_backtrack']) {
                    $currentX = $nextX;
                    $currentY = $nextY;
                }
            }
        }
        
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
                        $monsterPools = $this->templateModel->getMonsterPools($template['id']);
        
                $spawnChance = $isBossRoom ? 100 : 50; 
        
        if (rand(1, 100) <= $spawnChance) {
                        $validPools = array_filter($monsterPools, function($p) use ($isBossRoom) {
                if ($p['boss_room_only'] && !$isBossRoom) return false;
                if ($isBossRoom && $p['is_boss']) return true;                 if ($isBossRoom && !$p['is_boss']) return false;                                                                                return true;
            });
            
            if (!empty($validPools)) {
                                $pool = $this->pickWeighted($validPools);
                if ($pool) {
                    $qty = rand($pool['min_quantity'], $pool['max_quantity']);
                    $this->nodeModel->addMonster($nodeId, [
                        'name' => $pool['monster_name'],
                        'level' => rand($pool['min_level'], $pool['max_level']),
                        'stats' => $pool['monster_stats_base'],                         'quantity' => $qty,
                        'is_boss' => $pool['is_boss'],
                        'can_flee' => $pool['is_boss'] ? 0 : 1
                    ]);
                }
            }
        }
        
                        if (rand(1, 100) <= 20) {
             $this->nodeModel->addTrap($nodeId, [
                 'description' => "Un mécanisme suspect...",
                 'damage_dice' => '1d6',
                 'effect_text' => 'Une fléchette vous touche !',
                 'avoid_stat' => 'DEX',
                 'difficulty_class' => 12
             ]);
        }
        
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
                    $this->nodeModel->addLoot($nodeId, $pool['item_id'], $qty, 1.0, 0);                 }
            }
        }
    }

    private function createConnection($fromId, $toId, $text)
    {
        $stmt = $this->db->prepare("INSERT INTO story_node_connections (from_node_id, to_node_id, action_text, direction_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $fromId, $toId, $text, $text);
        $stmt->execute();
    }
    
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
                if ($isStart) return "/assets/images/cave_start.webp";
        if ($isEnd) return "/assets/images/cave_boss.webp";
        return "/assets/images/cave_room.webp";
    }
}
