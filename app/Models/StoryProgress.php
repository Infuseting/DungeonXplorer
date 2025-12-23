<?php

namespace App\Models;

use App\Config\Database;

class StoryProgress
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get progress for a character in a story
     * 
     * @param int $characterId
     * @param int $storyId
     * @return array|null
     */
    public function getProgress($characterId, $storyId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM character_story_progress 
             WHERE character_id = ? AND story_id = ?"
        );
        $stmt->bind_param("ii", $characterId, $storyId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Start a story for a character
     * 
     * @param int $characterId
     * @param int $storyId
     * @param int $startNodeId
     * @return bool
     */
    public function startStory($characterId, $storyId, $startNodeId)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO character_story_progress (character_id, story_id, current_node_id, started_at, in_dungeon) 
             VALUES (?, ?, ?, NOW(), 1)
             ON DUPLICATE KEY UPDATE current_node_id = VALUES(current_node_id), last_updated = NOW(), in_dungeon = 1"
        );
        $stmt->bind_param("iii", $characterId, $storyId, $startNodeId);
        return $stmt->execute();
    }

    /**
     * Update current node
     * 
     * @param int $characterId
     * @param int $storyId
     * @param int $nodeId
     * @return bool
     */
    public function updateProgress($characterId, $storyId, $nodeId)
    {
        $stmt = $this->db->prepare(
            "UPDATE character_story_progress 
             SET current_node_id = ?, last_updated = NOW(), in_dungeon = 1 
             WHERE character_id = ? AND story_id = ?"
        );
        $stmt->bind_param("iii", $nodeId, $characterId, $storyId);
        return $stmt->execute();
    }

    /**
     * Exit dungeon (set in_dungeon = 0)
     * 
     * @param int $characterId
     * @param int $storyId
     * @return bool
     */
    public function exitDungeon($characterId, $storyId)
    {
        $stmt = $this->db->prepare(
            "UPDATE character_story_progress 
             SET in_dungeon = 0, last_updated = NOW() 
             WHERE character_id = ? AND story_id = ?"
        );
        $stmt->bind_param("ii", $characterId, $storyId);
        return $stmt->execute();
    }

    /**
     * Delete progress for a story (reset)
     * 
     * @param int $characterId
     * @param int $storyId
     * @return bool
     */
    public function deleteProgress($characterId, $storyId)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM character_story_progress 
             WHERE character_id = ? AND story_id = ?"
        );
        $stmt->bind_param("ii", $characterId, $storyId);
        return $stmt->execute();
    }

    /**
     * Get active story for character
     * 
     * @param int $characterId
     * @return array|null
     */
    public function getActiveStory($characterId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM character_story_progress 
             WHERE character_id = ? AND in_dungeon = 1 
             ORDER BY last_updated DESC LIMIT 1"
        );
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Complete story
     * 
     * @param int $characterId
     * @param int $storyId
     * @return bool
     */
    public function completeStory($characterId, $storyId)
    {
        $stmt = $this->db->prepare(
            "UPDATE character_story_progress 
             SET completed = 1, completed_at = NOW(), in_dungeon = 0 
             WHERE character_id = ? AND story_id = ?"
        );
        $stmt->bind_param("ii", $characterId, $storyId);
        return $stmt->execute();
    }

    /**
     * Check if loot has been collected
     * 
     * @param int $characterId
     * @param int $nodeId
     * @param int $lootId
     * @return bool
     */
    public function hasCollectedLoot($characterId, $nodeId, $lootId)
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM character_story_loots_collected 
             WHERE character_id = ? AND node_id = ? AND loot_id = ?"
        );
        $stmt->bind_param("iii", $characterId, $nodeId, $lootId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Collect loot
     * 
     * @param int $characterId
     * @param int $nodeId
     * @param int $lootId
     * @return bool
     */
    public function collectLoot($characterId, $nodeId, $lootId)
    {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO character_story_loots_collected (character_id, node_id, loot_id) 
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iii", $characterId, $nodeId, $lootId);
        return $stmt->execute();
    }

    /**
     * Get node status (visited, cleared)
     * 
     * @param int $characterId
     * @param int $nodeId
     * @return array|null
     */
    public function getNodeStatus($characterId, $nodeId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM character_story_node_status 
             WHERE character_id = ? AND node_id = ?"
        );
        $stmt->bind_param("ii", $characterId, $nodeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Mark node as visited
     * 
     * @param int $characterId
     * @param int $nodeId
     * @return bool
     */
    public function markNodeVisited($characterId, $nodeId)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO character_story_node_status (character_id, node_id, is_visited) 
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE is_visited = 1"
        );
        $stmt->bind_param("ii", $characterId, $nodeId);
        return $stmt->execute();
    }

    /**
     * Mark node as cleared (monsters killed)
     * 
     * @param int $characterId
     * @param int $nodeId
     * @return bool
     */
    public function markNodeCleared($characterId, $nodeId)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO character_story_node_status (character_id, node_id, monsters_cleared) 
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE monsters_cleared = 1"
        );
        $stmt->bind_param("ii", $characterId, $nodeId);
        return $stmt->execute();
    }
}
