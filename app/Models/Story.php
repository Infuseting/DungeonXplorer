<?php

namespace App\Models;

use App\Config\Database;

class Story
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all stories
     * 
     * @return array
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM stories ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find story by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM stories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Create a new story
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO stories (name, description, type, difficulty_level, min_level, procedural_template_id) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $templateId = !empty($data['procedural_template_id']) ? $data['procedural_template_id'] : null;
        
        $stmt->bind_param(
            "sssiis", 
            $data['name'], 
            $data['description'], 
            $data['type'], 
            $data['difficulty_level'], 
            $data['min_level'],
            $templateId
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update a story
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE stories 
             SET name = ?, description = ?, type = ?, difficulty_level = ?, min_level = ?, procedural_template_id = ? 
             WHERE id = ?"
        );
        
        $templateId = !empty($data['procedural_template_id']) ? $data['procedural_template_id'] : null;
        
        $stmt->bind_param(
            "sssiisi", 
            $data['name'], 
            $data['description'], 
            $data['type'], 
            $data['difficulty_level'], 
            $data['min_level'],
            $templateId,
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a story
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM stories WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get start node for a story (manual only)
     * 
     * @param int $storyId
     * @return array|null
     */
    public function getStartNode($storyId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM story_nodes 
             WHERE story_id = ? AND is_start_node = 1 AND story_instance_id IS NULL 
             LIMIT 1"
        );
        $stmt->bind_param("i", $storyId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
