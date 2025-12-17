<?php

namespace App\Models;

use App\Config\Database;

class StoryNode
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find node by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM story_nodes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get all nodes for a story (manual)
     * 
     * @param int $storyId
     * @return array
     */
    public function getByStoryId($storyId)
    {
        $stmt = $this->db->prepare("SELECT * FROM story_nodes WHERE story_id = ? AND story_instance_id IS NULL");
        $stmt->bind_param("i", $storyId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new node
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO story_nodes (story_id, story_instance_id, name, description, image_path, is_start_node, is_end_node, can_exit, node_x, node_y, is_searchable, exit_condition_type, exit_condition_value) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $instanceId = !empty($data['story_instance_id']) ? $data['story_instance_id'] : null;
        $canExit = $data['can_exit'] ?? 0;
        $isSearchable = $data['is_searchable'] ?? 1;
        $exitType = $data['exit_condition_type'] ?? 'none';
        $exitValue = $data['exit_condition_value'] ?? null;
        
        $stmt->bind_param(
            "iisssiiiiisss", 
            $data['story_id'], 
            $instanceId,
            $data['name'], 
            $data['description'], 
            $data['image_path'], 
            $data['is_start_node'],
            $data['is_end_node'],
            $canExit,
            $data['node_x'],
            $data['node_y'],
            $isSearchable,
            $exitType,
            $exitValue
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update a node
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE story_nodes 
             SET name = ?, description = ?, image_path = ?, is_start_node = ?, is_end_node = ?, can_exit = ?, node_x = ?, node_y = ?, is_searchable = ?, exit_condition_type = ?, exit_condition_value = ? 
             WHERE id = ?"
        );
        
        $canExit = $data['can_exit'] ?? 0;
        $isSearchable = $data['is_searchable'] ?? 1;
        $exitType = $data['exit_condition_type'] ?? 'none';
        $exitValue = $data['exit_condition_value'] ?? null;
        
        $stmt->bind_param(
            "sssiiiiisssi", 
            $data['name'], 
            $data['description'], 
            $data['image_path'], 
            $data['is_start_node'],
            $data['is_end_node'],
            $canExit,
            $data['node_x'],
            $data['node_y'],
            $isSearchable,
            $exitType,
            $exitValue,
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a node
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM story_nodes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get connections for a node
     * 
     * @param int $nodeId
     * @return array
     */
    public function getConnections($nodeId)
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, n.name as to_node_name 
             FROM story_node_connections c
             JOIN story_nodes n ON c.to_node_id = n.id
             WHERE c.from_node_id = ?
             ORDER BY c.order_index ASC"
        );
        $stmt->bind_param("i", $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get return connections for a node (incoming connections with allow_return=1)
     * 
     * @param int $nodeId
     * @return array
     */
    public function getReturnConnections($nodeId)
    {
        // We want to find connections that point TO this node, where allow_return is true.
        // The "target" of the return is the FROM node of the original connection.
        $stmt = $this->db->prepare(
            "SELECT c.*, n.name as to_node_name, 1 as is_return
             FROM story_node_connections c
             JOIN story_nodes n ON c.from_node_id = n.id
             WHERE c.to_node_id = ? AND c.allow_return = 1"
        );
        $stmt->bind_param("i", $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $connections = $result->fetch_all(MYSQLI_ASSOC);
        
        // Fix the IDs for the frontend: 
        // For a return connection, the "destination" (to_node_id) from the player's perspective is the from_node_id of the connection
        foreach ($connections as &$conn) {
            $conn['to_node_id'] = $conn['from_node_id'];
            
            // Use return_text if available, otherwise fallback
            $displayText = !empty($conn['return_text']) ? $conn['return_text'] : ("Retour : " . ($conn['action_text'] ?: $conn['to_node_name']));
            
            $conn['action_text'] = $displayText;
            $conn['direction_text'] = $displayText;
        }
        
        error_log("Found " . count($connections) . " return connections for node $nodeId");
        
        return $connections;
    }

    /**
     * Get NPCs in a node
     * 
     * @param int $nodeId
     * @return array
     */
    public function getNPCs($nodeId)
    {
        $stmt = $this->db->prepare(
            "SELECT snn.*, n.name, n.role, n.texture 
             FROM story_node_npcs snn
             JOIN npcs n ON snn.npc_id = n.id
             WHERE snn.node_id = ?"
        );
        $stmt->bind_param("i", $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get monsters in a node
     * 
     * @param int $nodeId
     * @return array
     */
    public function getMonsters($nodeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM story_node_monsters WHERE node_id = ?");
        $stmt->bind_param("i", $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $monsters = $result->fetch_all(MYSQLI_ASSOC);
        
        // Decode JSON stats
        foreach ($monsters as &$monster) {
            if ($monster['monster_stats']) {
                $monster['monster_stats'] = json_decode($monster['monster_stats'], true);
            }
        }
        
        return $monsters;
    }

    /**
     * Get loots in a node
     * 
     * @param int $nodeId
     * @return array
     */
    public function getLoots($nodeId)
    {
        $stmt = $this->db->prepare(
            "SELECT snl.*, i.name, i.icon 
             FROM story_node_loots snl
             JOIN items i ON snl.item_id = i.id
             WHERE snl.node_id = ?"
        );
        $stmt->bind_param("i", $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get full node data
     * 
     * @param int $nodeId
     * @return array|null
     */
    public function getFullNodeData($nodeId)
    {
        $node = $this->findById($nodeId);
        if (!$node) return null;
        
        $connections = $this->getConnections($nodeId);
        $returnConnections = $this->getReturnConnections($nodeId);
        
        $node['connections'] = array_merge($connections, $returnConnections);
        $node['npcs'] = $this->getNPCs($nodeId);
        $node['monsters'] = $this->getMonsters($nodeId);
        $node['loots'] = $this->getLoots($nodeId);
        $node['traps'] = $this->getTraps($nodeId);
        
        return $node;
    }

    /**
     * Get start node for an instance
     * 
     * @param int $instanceId
     * @return array|null
     */
    public function getInstanceStartNode($instanceId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM story_nodes 
             WHERE story_instance_id = ? AND is_start_node = 1 
             LIMIT 1"
        );
        $stmt->bind_param("i", $instanceId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // --- Entity Management Methods ---

    /**
     * Add a monster to a node
     */
    public function addMonster($nodeId, $monsterData)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO story_node_monsters (node_id, monster_name, monster_level, monster_stats, quantity, is_boss, can_flee) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $statsJson = json_encode($monsterData['stats'] ?? []);
        $quantity = $monsterData['quantity'] ?? 1;
        $isBoss = $monsterData['is_boss'] ?? 0;
        $canFlee = isset($monsterData['can_flee']) ? $monsterData['can_flee'] : 1; // Default to true (1)
        
        $stmt->bind_param(
            "isssiii", 
            $nodeId, 
            $monsterData['name'], 
            $monsterData['level'], 
            $statsJson,
            $quantity,
            $isBoss,
            $canFlee
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Remove a monster from a node
     */
    public function removeMonster($id)
    {
        $stmt = $this->db->prepare("DELETE FROM story_node_monsters WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Add an NPC to a node
     */
    public function addNPC($nodeId, $npcId, $x = 0, $y = 0)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO story_node_npcs (node_id, npc_id, position_x, position_y) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iidd", $nodeId, $npcId, $x, $y);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Remove an NPC from a node
     */
    public function removeNPC($id)
    {
        $stmt = $this->db->prepare("DELETE FROM story_node_npcs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get traps in a node
     * 
     * @param int $nodeId
     * @return array
     */
    public function getTraps($nodeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM story_node_traps WHERE node_id = ?");
        $stmt->bind_param("i", $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add a trap to a node
     */
    public function addTrap($nodeId, $data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO story_node_traps (node_id, description, damage_dice, effect_text, avoid_stat, difficulty_class) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $damage = $data['damage_dice'] ?? '1d6';
        $dc = $data['difficulty_class'] ?? 12;
        $stat = $data['avoid_stat'] ?? 'DEX';
        
        $stmt->bind_param(
            "issssi", 
            $nodeId, 
            $data['description'], 
            $damage,
            $data['effect_text'],
            $stat,
            $dc
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Remove a trap
     */
    public function removeTrap($id)
    {
        $stmt = $this->db->prepare("DELETE FROM story_node_traps WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Add loot to a node (Updated with Trap support)
     */
    public function addLoot($nodeId, $itemId, $quantity = 1, $chance = 1.0, $isGuaranteed = 0, $trapData = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO story_node_loots (node_id, item_id, quantity, drop_chance, is_guaranteed, is_trapped, trap_damage, trap_dc, trap_description) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $isTrapped = $trapData ? 1 : 0;
        $trapDamage = $trapData['damage'] ?? null;
        $trapDc = $trapData['dc'] ?? null;
        $trapDesc = $trapData['description'] ?? null;
        
        $stmt->bind_param("iiidiisis", $nodeId, $itemId, $quantity, $chance, $isGuaranteed, $isTrapped, $trapDamage, $trapDc, $trapDesc);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Remove loot from a node
     */
    public function removeLoot($id)
    {
        $stmt = $this->db->prepare("DELETE FROM story_node_loots WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
