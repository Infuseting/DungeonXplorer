<?php

namespace App\Models;

use App\Config\Database;

class StoryInstance
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find instance by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM story_instances WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get instance for a story and character
     * 
     * @param int $storyId
     * @param int $characterId
     * @return array|null
     */
    public function getByStoryAndCharacter($storyId, $characterId)
    {
        // First check for character-specific instance
        $stmt = $this->db->prepare(
            "SELECT * FROM story_instances 
             WHERE story_id = ? AND instance_type = 'character' AND character_id = ? 
             AND (expires_at IS NULL OR expires_at > NOW())"
        );
        $stmt->bind_param("ii", $storyId, $characterId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($instance = $result->fetch_assoc()) {
            return $instance;
        }
        
        // Then check for shared instance
        $stmt = $this->db->prepare(
            "SELECT * FROM story_instances 
             WHERE story_id = ? AND instance_type = 'shared' 
             AND (expires_at IS NULL OR expires_at > NOW()) 
             ORDER BY generated_at DESC LIMIT 1"
        );
        $stmt->bind_param("i", $storyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Create a new instance
     * 
     * @param int $storyId
     * @param int|null $characterId
     * @param int $seed
     * @param string $instanceType
     * @return int|false
     */
    public function create($storyId, $characterId, $seed, $instanceType = 'character')
    {
        // Default expiration: 48 hours
        $expiresAt = date('Y-m-d H:i:s', strtotime('+48 hours'));
        
        $stmt = $this->db->prepare(
            "INSERT INTO story_instances (story_id, character_id, seed, instance_type, generated_at, expires_at) 
             VALUES (?, ?, ?, ?, NOW(), ?)"
        );
        
        $charId = ($instanceType === 'character') ? $characterId : null;
        
        $stmt->bind_param("iiiss", $storyId, $charId, $seed, $instanceType, $expiresAt);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Delete an instance
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM story_instances WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Clean expired instances
     * 
     * @return int Number of deleted instances
     */
    public function cleanExpiredInstances()
    {
        $this->db->query("DELETE FROM story_instances WHERE expires_at IS NOT NULL AND expires_at <= NOW()");
        return $this->db->affected_rows;
    }
}
