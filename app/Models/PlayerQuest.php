<?php

namespace App\Models;
use App\Config\Database;
class PlayerQuest
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get player's active quests
     */
    public function getActiveQuests($characterId)
    {
        $stmt = $this->db->prepare("
            SELECT pq.*, q.name, q.description 
            FROM player_quests pq
            JOIN quests q ON pq.quest_id = q.id
            WHERE pq.character_id = ? AND pq.status = 'ACTIVE'
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get quest status for character
     * Returns: 'NOT_STARTED', 'ACTIVE', 'COMPLETED'
     */
    public function getQuestStatus($characterId, $questId)
    {
        $stmt = $this->db->prepare("SELECT status FROM player_quests WHERE character_id = ? AND quest_id = ?");
        $stmt->bind_param("ii", $characterId, $questId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['status'];
        }
        
        return 'NOT_STARTED';
    }
    
    /**
     * Start a quest for a player character
     */
    public function startQuest($characterId, $questId)
    {
        // Get first stage
        $stmt = $this->db->prepare("SELECT id FROM quest_stages WHERE quest_id = ? ORDER BY order_index ASC LIMIT 1");
        $stmt->bind_param("i", $questId);
        $stmt->execute();
        $result = $stmt->get_result();
        $firstStage = $result->fetch_assoc();
        
        if (!$firstStage) return false;
        
        // Create player quest
        $stmt = $this->db->prepare("INSERT INTO player_quests (character_id, quest_id, current_stage_id, status) VALUES (?, ?, ?, 'ACTIVE')");
        $stmt->bind_param("iii", $characterId, $questId, $firstStage['id']);
        
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
        
        $events = [
            'objective_completed' => false,
            'quest_completed' => false,
            'unlocked_points' => [],
            'quest_name' => '',
            'objective_description' => ''
        ];

        // Get Quest Name and Objective Description
        $stmt = $this->db->prepare("
            SELECT q.name as quest_name, qo.description as objective_description
            FROM player_quests pq
            JOIN quests q ON pq.quest_id = q.id
            JOIN quest_objectives qo ON qo.id = ?
            WHERE pq.id = ?
        ");
        $stmt->bind_param("ii", $objectiveId, $playerQuestId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $events['quest_name'] = $row['quest_name'];
            $events['objective_description'] = $row['objective_description'];
        }
        
        // Check if objective is completed
        if ($this->checkObjectiveCompletion($playerQuestId, $objectiveId)) {
            $events['objective_completed'] = true;
            
            // Check if stage is completed
            $stageEvents = $this->checkStageCompletion($playerQuestId);
            $events['quest_completed'] = $stageEvents['quest_completed'];
            $events['unlocked_points'] = $stageEvents['unlocked_points'];
        }
        
        return $events;
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
            return true;
        }
        return false;
    }
    
    /**
     * Check if all objectives in current stage are completed
     */
    private function checkStageCompletion($playerQuestId)
    {
        $events = [
            'quest_completed' => false,
            'unlocked_points' => []
        ];

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
            $events['unlocked_points'] = $this->unlockMapPointsForStage($playerQuestId);
            
            // Move to next stage or complete quest
            $events['quest_completed'] = $this->advanceToNextStage($playerQuestId);
        }
        
        return $events;
    }

    /**
     * Unlock map points associated with the current stage
     */
    private function unlockMapPointsForStage($playerQuestId)
    {
        $unlockedPoints = [];

        // Get character_id and current_stage_id
        $stmt = $this->db->prepare("SELECT character_id, current_stage_id FROM player_quests WHERE id = ?");
        $stmt->bind_param("i", $playerQuestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $pq = $result->fetch_assoc();
        
        if (!$pq || !$pq['current_stage_id']) return [];
        
        // Get unlocks for this stage
        $questStageModel = new QuestStage();
        $unlocks = $questStageModel->getMapUnlocks($pq['current_stage_id']);
        
        if (empty($unlocks)) return [];
        
        $mapPointModel = new MapPoint();
        foreach ($unlocks as $unlock) {
            if ($mapPointModel->unlockForCharacter($pq['character_id'], $unlock['id'])) {
                $unlockedPoints[] = $unlock['name'];
            }
        }
        
        return $unlockedPoints;
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
            return false;
        } else {
            // Complete quest
            $updateStmt = $this->db->prepare("UPDATE player_quests SET status = 'COMPLETED', completed_at = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $playerQuestId);
            $updateStmt->execute();
            
            // Grant Rewards
            $this->grantRewards($playerQuestId);
            
            return true;
        }
    }

    /**
     * Grant rewards for completed quest
     */
    private function grantRewards($playerQuestId)
    {
        // Get Quest ID and Character ID
        $stmt = $this->db->prepare("SELECT quest_id, character_id FROM player_quests WHERE id = ?");
        $stmt->bind_param("i", $playerQuestId);
        $stmt->execute();
        $pq = $stmt->get_result()->fetch_assoc();
        
        if (!$pq) return;
        
        // Get Rewards
        $questModel = new Quest();
        $quest = $questModel->findById($pq['quest_id']); // get XP/Gold
        $rewardItems = $questModel->getRewardItems($pq['quest_id']);
        
        // Grant XP & Gold
        if (($quest['xp_reward'] ?? 0) > 0 || ($quest['gold_reward'] ?? 0) > 0) {
            $sql = "UPDATE characters SET experience = experience + ?, gold = gold + ? WHERE id = ?";
            $xp = $quest['xp_reward'] ?? 0;
            $gold = $quest['gold_reward'] ?? 0;
            $upd = $this->db->prepare($sql);
            $upd->bind_param("iii", $xp, $gold, $pq['character_id']);
            $upd->execute();
        }
        
        // Grant Items
        if (!empty($rewardItems)) {
            $invModel = new Inventory();
            foreach ($rewardItems as $reward) {
                $qty = $reward['quantity'] ?? 1;
                for ($i = 0; $i < $qty; $i++) {
                    $invModel->addItem($pq['character_id'], $reward['item_id']);
                }
            }
        }
    }

    /**
     * Get full quest log for player character
     */
    public function getQuestLog($characterId)
    {
        // 1. Get all player quests (Active & Completed)
        $stmt = $this->db->prepare("
            SELECT pq.*, q.name, q.description, q.min_level,
                   qs.order_index as current_stage_order
            FROM player_quests pq
            JOIN quests q ON pq.quest_id = q.id
            JOIN quest_stages qs ON pq.current_stage_id = qs.id
            WHERE pq.character_id = ?
            ORDER BY 
                CASE WHEN pq.status = 'ACTIVE' THEN 1 ELSE 2 END,
                pq.id DESC
        ");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $quests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $log = [];
        
        foreach ($quests as $quest) {
            $questData = [
                'id' => $quest['quest_id'],
                'name' => $quest['name'],
                'description' => $quest['description'],
                'status' => $quest['status'],
                'stages' => []
            ];
            
            // 2. Get all stages for this quest
            $stmt = $this->db->prepare("
                SELECT qs.* 
                FROM quest_stages qs 
                WHERE qs.quest_id = ? 
                ORDER BY qs.order_index ASC
            ");
            $stmt->bind_param("i", $quest['quest_id']);
            $stmt->execute();
            $stages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            foreach ($stages as $stage) {
                // Determine stage status relative to player progress
                $stageStatus = 'LOCKED'; // Default hidden/future
                
                if ($quest['status'] === 'COMPLETED') {
                    $stageStatus = 'COMPLETED';
                } elseif ($stage['order_index'] < $quest['current_stage_order']) {
                    $stageStatus = 'COMPLETED';
                } elseif ($stage['order_index'] == $quest['current_stage_order']) {
                    $stageStatus = 'ACTIVE';
                }
                
                // Skip future stages (as requested)
                if ($stageStatus === 'LOCKED') continue;
                
                $stageData = [
                    'id' => $stage['id'],
                    'name' => $stage['name'],
                    'description' => $stage['description'],
                    'status' => $stageStatus,
                    'objectives' => []
                ];
                
                // 3. Get objectives and progress if active or completed
                if ($stageStatus === 'ACTIVE') {
                    // Get real-time progress for active stage
                    $stmt = $this->db->prepare("
                        SELECT qo.*, pqp.count_current, pqp.is_completed
                        FROM quest_objectives qo
                        LEFT JOIN player_quest_progress pqp ON qo.id = pqp.objective_id AND pqp.player_quest_id = ?
                        WHERE qo.stage_id = ?
                    ");
                    $stmt->bind_param("ii", $quest['id'], $stage['id']);
                } else {
                    // For completed stages, just show objectives as done
                    $stmt = $this->db->prepare("SELECT *, count_required as count_current, 1 as is_completed FROM quest_objectives WHERE stage_id = ?");
                    $stmt->bind_param("i", $stage['id']);
                }
                
                $stmt->execute();
                $stageData['objectives'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
                $questData['stages'][] = $stageData;
            }
            
            $log[] = $questData;
        }
        
        return $log;
    }
    /**
     * Handle Monster Kill Event for Quests
     */
    public function onMonsterKilled($characterId, $monsterId)
    {
        // Find all active objectives that target this monster
        // Assuming objective types: 'KILL_MONSTER' or 'KILL' and target_id is the monster ID
        $stmt = $this->db->prepare("
            SELECT pqp.player_quest_id, pqp.objective_id, qo.description
            FROM player_quest_progress pqp
            JOIN quest_objectives qo ON pqp.objective_id = qo.id
            JOIN player_quests pq ON pqp.player_quest_id = pq.id
            WHERE pq.character_id = ? 
              AND pq.status = 'ACTIVE'
              AND (qo.type = 'KILL_MONSTER' OR qo.type = 'KILL')
              AND qo.target_id = ?
        ");
        $stmt->bind_param("ii", $characterId, $monsterId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $updates = [];
        while ($row = $result->fetch_assoc()) {
            // Update progress for each matching objective
            $event = $this->updateProgress($row['player_quest_id'], $row['objective_id'], 1);
            $event['original_description'] = $row['description'];
            $updates[] = $event;
        }
        
        return $updates;
    }
}
