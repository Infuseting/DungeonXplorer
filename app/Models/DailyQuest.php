<?php

namespace App\Models;

use App\Config\Database;

class DailyQuest
{
    private $db;
    private const MAX_DAILY_QUESTS = 3;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all available daily quests from the pool
     */
    public function getAllQuests()
    {
        $result = $this->db->query("SELECT * FROM daily_quests WHERE is_active = 1 ORDER BY id ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get daily quests for a character for today
     * If none exist, assign new random ones
     */
    public function getDailyQuestsForCharacter($characterId)
    {
        $today = date('Y-m-d');
        
        // Check if character already has daily quests for today
        $stmt = $this->db->prepare("
            SELECT pdq.*, dq.name, dq.description, dq.objective_type, dq.objective_target, dq.objective_count, dq.gold_reward
            FROM player_daily_quests pdq
            JOIN daily_quests dq ON pdq.daily_quest_id = dq.id
            WHERE pdq.character_id = ? AND pdq.assigned_date = ?
            ORDER BY pdq.id ASC
        ");
        $stmt->bind_param("is", $characterId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $quests = $result->fetch_all(MYSQLI_ASSOC);
        
        // If no quests assigned for today, assign new random ones
        if (empty($quests)) {
            $this->assignDailyQuests($characterId);
            return $this->getDailyQuestsForCharacter($characterId);
        }
        
        return $quests;
    }
    
    /**
     * Assign random daily quests to a character
     */
    private function assignDailyQuests($characterId)
    {
        $today = date('Y-m-d');
        
        // Get all available quests
        $allQuests = $this->getAllQuests();
        
        if (count($allQuests) < self::MAX_DAILY_QUESTS) {
            // Not enough quests in pool
            return false;
        }
        
        // Shuffle and pick random quests
        shuffle($allQuests);
        $selectedQuests = array_slice($allQuests, 0, self::MAX_DAILY_QUESTS);
        
        // Insert selected quests for the character
        $stmt = $this->db->prepare("
            INSERT INTO player_daily_quests (character_id, daily_quest_id, assigned_date, current_progress, status)
            VALUES (?, ?, ?, 0, 'ACTIVE')
        ");
        
        foreach ($selectedQuests as $quest) {
            $stmt->bind_param("iis", $characterId, $quest['id'], $today);
            $stmt->execute();
        }
        
        return true;
    }
    
    /**
     * Update progress for a specific objective type
     */
    public function updateProgress($characterId, $objectiveType, $amount = 1, $targetId = null)
    {
        $today = date('Y-m-d');
        
        // Build the query based on whether we need to match a specific target
        if ($targetId !== null) {
            $stmt = $this->db->prepare("
                SELECT pdq.id, pdq.current_progress, dq.objective_count
                FROM player_daily_quests pdq
                JOIN daily_quests dq ON pdq.daily_quest_id = dq.id
                WHERE pdq.character_id = ? 
                AND pdq.assigned_date = ?
                AND pdq.status = 'ACTIVE'
                AND dq.objective_type = ?
                AND (dq.objective_target IS NULL OR dq.objective_target = ?)
            ");
            $stmt->bind_param("issi", $characterId, $today, $objectiveType, $targetId);
        } else {
            $stmt = $this->db->prepare("
                SELECT pdq.id, pdq.current_progress, dq.objective_count
                FROM player_daily_quests pdq
                JOIN daily_quests dq ON pdq.daily_quest_id = dq.id
                WHERE pdq.character_id = ? 
                AND pdq.assigned_date = ?
                AND pdq.status = 'ACTIVE'
                AND dq.objective_type = ?
            ");
            $stmt->bind_param("iss", $characterId, $today, $objectiveType);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $quests = $result->fetch_all(MYSQLI_ASSOC);
        
        $completedQuests = [];
        
        foreach ($quests as $quest) {
            $newProgress = $quest['current_progress'] + $amount;
            
            // Check if objective is now completed
            if ($newProgress >= $quest['objective_count']) {
                // Mark as completed
                $updateStmt = $this->db->prepare("
                    UPDATE player_daily_quests 
                    SET current_progress = ?, status = 'COMPLETED', completed_at = NOW()
                    WHERE id = ?
                ");
                $updateStmt->bind_param("ii", $quest['objective_count'], $quest['id']);
                $completedQuests[] = $quest['id'];
            } else {
                // Update progress
                $updateStmt = $this->db->prepare("
                    UPDATE player_daily_quests 
                    SET current_progress = ?
                    WHERE id = ?
                ");
                $updateStmt->bind_param("ii", $newProgress, $quest['id']);
            }
            $updateStmt->execute();
        }
        
        return $completedQuests;
    }
    
    /**
     * Claim reward for a completed daily quest
     */
    public function claimReward($characterId, $playerDailyQuestId)
    {
        $today = date('Y-m-d');
        
        // Verify the quest belongs to this character, is for today, and is completed but not claimed
        $stmt = $this->db->prepare("
            SELECT pdq.*, dq.gold_reward
            FROM player_daily_quests pdq
            JOIN daily_quests dq ON pdq.daily_quest_id = dq.id
            WHERE pdq.id = ? 
            AND pdq.character_id = ?
            AND pdq.assigned_date = ?
            AND pdq.status = 'COMPLETED'
        ");
        $stmt->bind_param("iis", $playerDailyQuestId, $characterId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $quest = $result->fetch_assoc();
        
        if (!$quest) {
            return ['success' => false, 'message' => 'Quête non trouvée ou déjà réclamée'];
        }
        
        // Mark as claimed
        $updateStmt = $this->db->prepare("
            UPDATE player_daily_quests 
            SET status = 'CLAIMED', claimed_at = NOW()
            WHERE id = ?
        ");
        $updateStmt->bind_param("i", $playerDailyQuestId);
        $updateStmt->execute();
        
        // Add gold to character
        $goldReward = $quest['gold_reward'];
        $goldStmt = $this->db->prepare("
            UPDATE characters 
            SET gold = gold + ?
            WHERE id = ?
        ");
        $goldStmt->bind_param("di", $goldReward, $characterId);
        $goldStmt->execute();
        
        return [
            'success' => true, 
            'gold_reward' => $goldReward,
            'message' => "Vous avez reçu {$goldReward} pièces d'or !"
        ];
    }
    
    /**
     * Get count of completed/claimed daily quests for today
     */
    public function getDailyQuestStats($characterId)
    {
        $today = date('Y-m-d');
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('COMPLETED', 'CLAIMED') THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'CLAIMED' THEN 1 ELSE 0 END) as claimed
            FROM player_daily_quests
            WHERE character_id = ? AND assigned_date = ?
        ");
        $stmt->bind_param("is", $characterId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Triggered when a monster is killed
     */
    public function onMonsterKilled($characterId, $monsterId = null)
    {
        return $this->updateProgress($characterId, 'KILL_MONSTERS', 1, $monsterId);
    }
    
    /**
     * Triggered when gold is collected
     */
    public function onGoldCollected($characterId, $amount)
    {
        return $this->updateProgress($characterId, 'COLLECT_GOLD', $amount);
    }
    
    /**
     * Triggered when a dungeon is completed
     */
    public function onDungeonCompleted($characterId, $dungeonId = null)
    {
        return $this->updateProgress($characterId, 'COMPLETE_DUNGEON', 1, $dungeonId);
    }
    
    /**
     * Triggered when a location is visited
     */
    public function onLocationVisited($characterId, $locationId = null)
    {
        return $this->updateProgress($characterId, 'VISIT_LOCATIONS', 1, $locationId);
    }
    
    /**
     * Triggered when an item is used
     */
    public function onItemUsed($characterId, $itemId = null)
    {
        return $this->updateProgress($characterId, 'USE_ITEMS', 1, $itemId);
    }
}
