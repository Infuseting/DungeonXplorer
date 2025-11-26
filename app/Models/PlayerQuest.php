<?php

namespace App\Models;

class PlayerQuest
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }
    
    /**
     * Get player's active quests
     */
    public function getActiveQuests($userId)
    {
        $stmt = $this->db->prepare("
            SELECT pq.*, q.name, q.description 
            FROM player_quests pq
            JOIN quests q ON pq.quest_id = q.id
            WHERE pq.user_id = ? AND pq.status = 'ACTIVE'
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Start a quest for a player
     */
    public function startQuest($userId, $questId)
    {
        // Get first stage
        $stmt = $this->db->prepare("SELECT id FROM quest_stages WHERE quest_id = ? ORDER BY order_index ASC LIMIT 1");
        $stmt->bind_param("i", $questId);
        $stmt->execute();
        $result = $stmt->get_result();
        $firstStage = $result->fetch_assoc();
        
        if (!$firstStage) return false;
        
        // Create player quest
        $stmt = $this->db->prepare("INSERT INTO player_quests (user_id, quest_id, current_stage_id, status) VALUES (?, ?, ?, 'ACTIVE')");
        $stmt->bind_param("iii", $userId, $questId, $firstStage['id']);
        
        if ($stmt->execute()) {
            $playerQuestId = $this->db->insert_id;
            
            // Initialize progress for all objectives in first stage
            $this->initializeStageProgress($playerQuestId, $firstStage['id']);
            
            return $playerQuestId;
        }
        return false;
    }
    
    /**
     * Initialize progress tracking for a stage
     */
    private function initializeStageProgress($playerQuestId, $stageId)
    {
        $stmt = $this->db->prepare("SELECT id FROM quest_objectives WHERE stage_id = ?");
        $stmt->bind_param("i", $stageId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $insertStmt = $this->db->prepare("INSERT INTO player_quest_progress (player_quest_id, objective_id, count_current, is_completed) VALUES (?, ?, 0, 0)");
        
        while ($objective = $result->fetch_assoc()) {
            $insertStmt->bind_param("ii", $playerQuestId, $objective['id']);
            $insertStmt->execute();
        }
    }
    
    /**
     * Update objective progress
     */
    public function updateProgress($playerQuestId, $objectiveId, $increment = 1)
    {
        $stmt = $this->db->prepare("
            UPDATE player_quest_progress 
            SET count_current = count_current + ? 
            WHERE player_quest_id = ? AND objective_id = ?
        ");
        $stmt->bind_param("iii", $increment, $playerQuestId, $objectiveId);
        $stmt->execute();
        
        // Check if objective is completed
        $this->checkObjectiveCompletion($playerQuestId, $objectiveId);
        
        // Check if stage is completed
        $this->checkStageCompletion($playerQuestId);
    }
    
    /**
     * Check if an objective is completed
     */
    private function checkObjectiveCompletion($playerQuestId, $objectiveId)
    {
        $stmt = $this->db->prepare("
            SELECT pqp.count_current, qo.count_required
            FROM player_quest_progress pqp
            JOIN quest_objectives qo ON pqp.objective_id = qo.id
            WHERE pqp.player_quest_id = ? AND pqp.objective_id = ?
        ");
        $stmt->bind_param("ii", $playerQuestId, $objectiveId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data && $data['count_current'] >= $data['count_required']) {
            $updateStmt = $this->db->prepare("UPDATE player_quest_progress SET is_completed = 1 WHERE player_quest_id = ? AND objective_id = ?");
            $updateStmt->bind_param("ii", $playerQuestId, $objectiveId);
            $updateStmt->execute();
        }
    }
    
    /**
     * Check if all objectives in current stage are completed
     */
    private function checkStageCompletion($playerQuestId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total, SUM(is_completed) as completed
            FROM player_quest_progress
            WHERE player_quest_id = ?
        ");
        $stmt->bind_param("i", $playerQuestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data['total'] == $data['completed']) {
            // Unlock map points for this stage
            $this->unlockMapPointsForStage($playerQuestId);
            
            // Move to next stage or complete quest
            $this->advanceToNextStage($playerQuestId);
        }
    }

    /**
     * Unlock map points associated with the current stage
     */
    private function unlockMapPointsForStage($playerQuestId)
    {
        // Get user_id and current_stage_id
        $stmt = $this->db->prepare("SELECT user_id, current_stage_id FROM player_quests WHERE id = ?");
        $stmt->bind_param("i", $playerQuestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $pq = $result->fetch_assoc();
        
        if (!$pq || !$pq['current_stage_id']) return;
        
        // Get unlocks for this stage
        $questStageModel = new QuestStage();
        $unlocks = $questStageModel->getMapUnlocks($pq['current_stage_id']);
        
        if (empty($unlocks)) return;
        
        $mapPointModel = new MapPoint();
        foreach ($unlocks as $unlock) {
            $mapPointModel->unlockForUser($pq['user_id'], $unlock['id']);
        }
    }
    
    /**
     * Advance to next stage or complete quest
     */
    private function advanceToNextStage($playerQuestId)
    {
        // Get current stage
        $stmt = $this->db->prepare("SELECT quest_id, current_stage_id FROM player_quests WHERE id = ?");
        $stmt->bind_param("i", $playerQuestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $playerQuest = $result->fetch_assoc();
        
        // Get next stage
        $stmt = $this->db->prepare("
            SELECT qs.id, qs.order_index
            FROM quest_stages qs
            WHERE qs.quest_id = ? AND qs.order_index > (
                SELECT order_index FROM quest_stages WHERE id = ?
            )
            ORDER BY qs.order_index ASC
            LIMIT 1
        ");
        $stmt->bind_param("ii", $playerQuest['quest_id'], $playerQuest['current_stage_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $nextStage = $result->fetch_assoc();
        
        if ($nextStage) {
            // Move to next stage
            $updateStmt = $this->db->prepare("UPDATE player_quests SET current_stage_id = ? WHERE id = ?");
            $updateStmt->bind_param("ii", $nextStage['id'], $playerQuestId);
            $updateStmt->execute();
            
            // Initialize progress for new stage
            $this->initializeStageProgress($playerQuestId, $nextStage['id']);
        } else {
            // Complete quest
            $updateStmt = $this->db->prepare("UPDATE player_quests SET status = 'COMPLETED', completed_at = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $playerQuestId);
            $updateStmt->execute();
        }
    }
}
