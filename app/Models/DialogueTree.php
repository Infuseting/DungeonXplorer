<?php

namespace App\Models;
use App\Config\Database;

class DialogueTree
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all dialogue trees
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM dialogue_trees ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find dialogue tree by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM dialogue_trees WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Create new dialogue tree
     */
    public function create($name, $description = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO dialogue_trees (name, description)
            VALUES (?, ?)
        ");
        
        $stmt->bind_param("ss", $name, $description);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Update dialogue tree
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE dialogue_trees 
            SET name = ?, description = ?
            WHERE id = ?
        ");
        
        $stmt->bind_param("ssi", $data['name'], $data['description'], $id);
        return $stmt->execute();
    }
    
    /**
     * Delete dialogue tree
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM dialogue_trees WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Get all dialogues for a tree
     */
    public function getDialogues($treeId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM dialogues 
            WHERE tree_id = ? 
            ORDER BY parent_id, order_index
        ");
        $stmt->bind_param("i", $treeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Build hierarchical dialogue tree
     */
    public function getDialogueTree($treeId)
    {
        $dialogues = $this->getDialogues($treeId);
        return $this->buildTree($dialogues);
    }
    
    /**
     * Build tree structure from flat array
     */
    private function buildTree($dialogues, $parentId = null)
    {
        $branch = [];
        
        foreach ($dialogues as $dialogue) {
            if ($dialogue['parent_id'] == $parentId) {
                $children = $this->buildTree($dialogues, $dialogue['id']);
                if ($children) {
                    $dialogue['children'] = $children;
                }
                $branch[] = $dialogue;
            }
        }
        
        return $branch;
    }
    
    /**
     * Add dialogue node
     */
    public function addDialogue($treeId, $text, $parentId = null, $isPlayerChoice = false, $choiceText = null, $orderIndex = 0, $actionType='NONE', $actionValue=null, $conditionType='NONE', $conditionValue=null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO dialogues (tree_id, parent_id, text, is_player_choice, choice_text, order_index, action_type, action_value, condition_type, condition_value)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("iisisissss", $treeId, $parentId, $text, $isPlayerChoice, $choiceText, $orderIndex, $actionType, $actionValue, $conditionType, $conditionValue);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Delete dialogue node (and all children)
     */
    public function deleteDialogue($dialogueId)
    {
        // CASCADE delete will handle children
        $stmt = $this->db->prepare("DELETE FROM dialogues WHERE id = ?");
        $stmt->bind_param("i", $dialogueId);
        return $stmt->execute();
    }
    
    /**
     * Get available choices for a dialogue node
     */
    public function getDialogueChoices($dialogueId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM dialogues 
            WHERE parent_id = ? AND is_player_choice = 1
            ORDER BY order_index
        ");
        $stmt->bind_param("i", $dialogueId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get NPCs using this dialogue tree
     */
    public function getNPCsUsingTree($treeId)
    {
        $stmt = $this->db->prepare("
            SELECT n.* 
            FROM npcs n
            JOIN npc_dialogue_trees ndt ON n.id = ndt.npc_id
            WHERE ndt.tree_id = ?
        ");
        $stmt->bind_param("i", $treeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get root dialogues (entry points) for a tree
     */
    public function getRootDialogues($treeId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM dialogues 
            WHERE tree_id = ? AND parent_id IS NULL
            ORDER BY order_index
        ");
        $stmt->bind_param("i", $treeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get children of a dialogue node
     */
    public function getChildren($dialogueId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM dialogues 
            WHERE parent_id = ?
            ORDER BY order_index
        ");
        $stmt->bind_param("i", $dialogueId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get quest objective linked to this dialogue tree
     */
    public function getQuestObjective($treeId)
    {
        $stmt = $this->db->prepare("
            SELECT qo.*, qs.quest_id, qs.id as stage_id
            FROM quest_objectives qo
            JOIN quest_stages qs ON qo.stage_id = qs.id
            WHERE qo.dialogue_tree_id = ?
        ");
        $stmt->bind_param("i", $treeId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
