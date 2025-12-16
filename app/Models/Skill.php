<?php
namespace App\Models;

use App\Config\Database;

class Skill
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM skills WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getSkillsByClass($classId)
    {
        $stmt = $this->db->prepare("SELECT * FROM skills WHERE class_id = ? ORDER BY min_level ASC, id ASC");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUnlockedSkills($characterId)
    {
        $sql = "SELECT s.*, cs.unlocked_at 
                FROM skills s 
                JOIN character_skills cs ON s.id = cs.skill_id 
                WHERE cs.character_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function unlock($characterId, $skillId)
    {
        // Check if already unlocked
        $stmtCheck = $this->db->prepare("SELECT id FROM character_skills WHERE character_id = ? AND skill_id = ?");
        $stmtCheck->bind_param("ii", $characterId, $skillId);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Already unlocked'];
        }

        // Check prerequisites (Cost, Parent, Level) - Logic usually in Controller, but can be here.
        // For now, just the Insert.
        $stmt = $this->db->prepare("INSERT INTO character_skills (character_id, skill_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $characterId, $skillId);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => $stmt->error];
    }
    
    // Helper to get Passive Bonuses
    public function getPassiveBonuses($characterId)
    {
        $sql = "SELECT s.effect_type, s.effect_value 
                FROM skills s 
                JOIN character_skills cs ON s.id = cs.skill_id 
                WHERE cs.character_id = ? AND s.type = 'passive'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
