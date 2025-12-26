<?php

namespace App\Services;

use App\Config\Database;
use App\Models\Character;
use App\Models\Inventory;
use App\Models\Skill;
use App\Models\StoryProgress;

class SaveService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a Save Game
     */
    public function createSave($characterId, $saveName = "AutoSave")
    {
        $data = [];

        $charModel = new Character();
        $char = $charModel->findById($characterId);
        $data['character_stats'] = $char;

        $invModel = new Inventory();
        $data['inventory'] = $invModel->getCharacterInventory($characterId);

        $skillModel = new Skill();
        $data['skills'] = $skillModel->getUnlockedSkills($characterId);

        $progModel = new StoryProgress();
        $data['progress'] = $progModel->getActiveStory($characterId);

        // Save map unlocks
        $stmt = $this->db->prepare("SELECT map_point_id FROM character_map_unlocks WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $data['map_unlocks'] = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'map_point_id');

        // Save player quests and their progress
        $stmt = $this->db->prepare("SELECT * FROM player_quests WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $playerQuests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $data['player_quests'] = [];

        foreach ($playerQuests as $pq) {
            $pqId = $pq['id'];
            $stmt2 = $this->db->prepare("SELECT * FROM player_quest_progress WHERE player_quest_id = ?");
            $stmt2->bind_param("i", $pqId);
            $stmt2->execute();
            $progressRows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
            $pq['progress_rows'] = $progressRows;
            $data['player_quests'][] = $pq;
        }

        // Save story node statuses, loots collected and monsters killed
        $stmt = $this->db->prepare("SELECT * FROM character_story_node_status WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $data['node_statuses'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM character_story_loots_collected WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $data['loots_collected'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM character_story_monsters_killed WHERE character_id = ?");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        $data['monsters_killed'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $json = json_encode($data);

        $stmt = $this->db->prepare("INSERT INTO character_saves (character_id, save_name, save_data) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $characterId, $saveName, $json);
        
        return $stmt->execute();
    }

    /**
     * Load a Save Game
     */
    public function loadSave($saveId, $characterId)
    {
        $stmt = $this->db->prepare("SELECT * FROM character_saves WHERE id = ? AND character_id = ?");
        $stmt->bind_param("ii", $saveId, $characterId);
        $stmt->execute();
        $save = $stmt->get_result()->fetch_assoc();

        if (!$save) return false;

        $data = json_decode($save['save_data'], true);
        if (!$data) return false;

        $this->db->begin_transaction();

        try {
            $stats = $data['character_stats'];

            // Update core character stats (schema uses xp instead of experience, gold is stored on characters table)
            $stmtUpdate = $this->db->prepare("UPDATE character_stats SET 
                level = ?, xp = ?, skill_points = ?, current_hp = ?, vitality = ?, strength = ?, dexterity = ?, intelligence = ?
                WHERE character_id = ?");

            $level = $stats['level'] ?? ($stats['level'] ?? 1);
            $xp = $stats['experience'] ?? $stats['xp'] ?? 0;
            $skillPoints = $stats['skill_points'] ?? $stats['skillPoints'] ?? 0;
            $currentHp = $stats['current_hp'] ?? $stats['currentHp'] ?? ($stats['vitality'] ?? 0);
            $vitality = $stats['vitality'] ?? $stats['vitality'] ?? 10;
            $strength = $stats['strength'] ?? 10;
            $dexterity = $stats['dexterity'] ?? 10;
            $intelligence = $stats['intelligence'] ?? 10;

            $stmtUpdate->bind_param("iiiiiiiii",
                $level, $xp, $skillPoints, $currentHp, $vitality, $strength, $dexterity, $intelligence, $characterId
            );
            $stmtUpdate->execute();

            // Update character gold if present
            if (isset($stats['gold'])) {
                $stmtGold = $this->db->prepare("UPDATE characters SET gold = ? WHERE id = ?");
                $stmtGold->bind_param("di", $stats['gold'], $characterId);
                $stmtGold->execute();
            }

            $this->db->query("DELETE FROM character_inventory WHERE character_id = $characterId");

            if (!empty($data['inventory'])) {
                // Prepare insertion matching current schema: (character_id, item_id, location, slot_name, grid_x, grid_y, quantity, instance_stats)
                $stmtInv = $this->db->prepare("INSERT INTO character_inventory (character_id, item_id, location, slot_name, grid_x, grid_y, quantity, instance_stats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                // Support both formats: saved inventory may contain 'equipped' and 'inventory' keys, or be a flat list
                $inventoryItems = [];
                if (isset($data['inventory']['equipped']) || isset($data['inventory']['inventory'])) {
                    if (isset($data['inventory']['equipped']) && is_array($data['inventory']['equipped'])) {
                        foreach ($data['inventory']['equipped'] as $slotName => $item) {
                            $item['location'] = 'equipped';
                            $item['slot_name'] = $slotName;
                            $inventoryItems[] = $item;
                        }
                    }
                    if (isset($data['inventory']['inventory']) && is_array($data['inventory']['inventory'])) {
                        foreach ($data['inventory']['inventory'] as $item) {
                            $inventoryItems[] = $item;
                        }
                    }
                } elseif (is_array($data['inventory'])) {
                    $inventoryItems = $data['inventory'];
                }

                foreach ($inventoryItems as $item) {
                    $itemId = $item['item_id'] ?? $item['id'] ?? null;
                    if (!$itemId) continue;

                    $location = $item['location'] ?? ($item['location'] ?? 'backpack');
                    $slotName = $item['slot_name'] ?? $item['slot'] ?? null;
                    $gridX = isset($item['grid_x']) ? $item['grid_x'] : (isset($item['gridX']) ? $item['gridX'] : null);
                    $gridY = isset($item['grid_y']) ? $item['grid_y'] : (isset($item['gridY']) ? $item['gridY'] : null);
                    $quantity = $item['quantity'] ?? 1;
                    // instance_stats may already be JSON or an array
                    if (is_array($item['instance_stats'] ?? null)) {
                        $instanceStatsJson = json_encode($item['instance_stats']);
                    } elseif (!empty($item['instance_stats'])) {
                        $instanceStatsJson = $item['instance_stats'];
                    } else {
                        $instanceStatsJson = '{}';
                    }

                    $stmtInv->bind_param("iissiiis", $characterId, $itemId, $location, $slotName, $gridX, $gridY, $quantity, $instanceStatsJson);
                    $stmtInv->execute();
                }
            }

            $this->db->query("DELETE FROM character_skills WHERE character_id = $characterId");
            
            if (!empty($data['skills'])) {
                $stmtSkill = $this->db->prepare("INSERT INTO character_skills (character_id, skill_id) VALUES (?, ?)");
                foreach ($data['skills'] as $skill) {
                                                                                $skillId = $skill['id'];
                    $stmtSkill->bind_param("ii", $characterId, $skillId);
                    $stmtSkill->execute();
                }
            }

                                                                                    
            // Story progress: adapt to current schema in `character_story_progress`
            if (!empty($data['progress']) && is_array($data['progress'])) {
                $prog = $data['progress'];
                if (isset($prog['story_id'])) {
                    $storyId = $prog['story_id'];
                    $currentNode = $prog['current_node_id'] ?? $prog['current_node'] ?? null;

                    $check = $this->db->prepare("SELECT id FROM character_story_progress WHERE character_id = ? AND story_id = ?");
                    $check->bind_param("ii", $characterId, $storyId);
                    $check->execute();
                    $res = $check->get_result();

                    if ($res && $res->num_rows > 0) {
                        $stmtProg = $this->db->prepare("UPDATE character_story_progress SET current_node_id = ?, in_dungeon = ?, last_updated = NOW() WHERE character_id = ? AND story_id = ?");
                        $inDungeon = $prog['in_dungeon'] ?? 1;
                        $stmtProg->bind_param("iiii", $currentNode, $inDungeon, $characterId, $storyId);
                        $stmtProg->execute();
                    } else {
                        $stmtProg = $this->db->prepare("INSERT INTO character_story_progress (character_id, story_id, current_node_id, started_at, in_dungeon) VALUES (?, ?, ?, NOW(), ?)");
                        $inDungeon = $prog['in_dungeon'] ?? 1;
                        $stmtProg->bind_param("iiii", $characterId, $storyId, $currentNode, $inDungeon);
                        $stmtProg->execute();
                    }
                }
            }

            // map_unlocks restored after quests/progress (moved later)

            // Always remove existing player quests and progress for this character (we'll restore from save)
            $stmt = $this->db->prepare("SELECT id FROM player_quests WHERE character_id = ?");
            $stmt->bind_param("i", $characterId);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $ids = array_column($rows, 'id');

            if (!empty($ids)) {
                $in = implode(',', array_map('intval', $ids));
                $this->db->query("DELETE FROM player_quest_progress WHERE player_quest_id IN ($in)");
            }
            $stmtDel = $this->db->prepare("DELETE FROM player_quests WHERE character_id = ?");
            $stmtDel->bind_param("i", $characterId);
            $stmtDel->execute();

            // Insert saved quests and their progress (if any)
            if (!empty($data['player_quests']) && is_array($data['player_quests'])) {
                $stmtInsertPQ = $this->db->prepare("INSERT INTO player_quests (character_id, quest_id, current_stage_id, status, started_at, completed_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsertProg = $this->db->prepare("INSERT INTO player_quest_progress (player_quest_id, objective_id, count_current, is_completed) VALUES (?, ?, ?, ?)");

                foreach ($data['player_quests'] as $pq) {
                    $questId = $pq['quest_id'] ?? $pq['questId'] ?? 0;
                    $currentStage = $pq['current_stage_id'] ?? $pq['current_stage_id'] ?? null;
                    $status = $pq['status'] ?? 'ACTIVE';
                    $startedAt = $pq['started_at'] ?? null;
                    $completedAt = $pq['completed_at'] ?? null;

                    $stmtInsertPQ->bind_param("iiisss", $characterId, $questId, $currentStage, $status, $startedAt, $completedAt);
                    $stmtInsertPQ->execute();
                    $newPqId = $this->db->insert_id;

                    if (!empty($pq['progress_rows']) && is_array($pq['progress_rows'])) {
                        foreach ($pq['progress_rows'] as $pr) {
                            $objectiveId = $pr['objective_id'] ?? $pr['objectiveId'] ?? 0;
                            $countCurrent = $pr['count_current'] ?? 0;
                            $isCompleted = $pr['is_completed'] ? 1 : 0;
                            $stmtInsertProg->bind_param("iiii", $newPqId, $objectiveId, $countCurrent, $isCompleted);
                            $stmtInsertProg->execute();
                        }
                    }
                }
            }

            // Restore story node statuses (clear existing then insert saved)
            $stmtDel = $this->db->prepare("DELETE FROM character_story_node_status WHERE character_id = ?");
            $stmtDel->bind_param("i", $characterId);
            $stmtDel->execute();

            if (!empty($data['node_statuses']) && is_array($data['node_statuses'])) {
                $stmtIns = $this->db->prepare("INSERT INTO character_story_node_status (character_id, node_id, is_visited, monsters_cleared) VALUES (?, ?, ?, ?)");
                foreach ($data['node_statuses'] as $ns) {
                    $nodeId = $ns['node_id'] ?? $ns['nodeId'] ?? 0;
                    $isVisited = $ns['is_visited'] ? 1 : 0;
                    $monstersCleared = $ns['monsters_cleared'] ?? 0;
                    $stmtIns->bind_param("iiii", $characterId, $nodeId, $isVisited, $monstersCleared);
                    $stmtIns->execute();
                }
            }

            // Restore loots collected (clear existing then insert saved)
            $stmtDel = $this->db->prepare("DELETE FROM character_story_loots_collected WHERE character_id = ?");
            $stmtDel->bind_param("i", $characterId);
            $stmtDel->execute();

            if (!empty($data['loots_collected']) && is_array($data['loots_collected'])) {
                $stmtIns = $this->db->prepare("INSERT INTO character_story_loots_collected (character_id, node_id, loot_id, collected_at) VALUES (?, ?, ?, ?)");
                foreach ($data['loots_collected'] as $lc) {
                    $nodeId = $lc['node_id'] ?? 0;
                    $lootId = $lc['loot_id'] ?? $lc['lootId'] ?? 0;
                    $collectedAt = $lc['collected_at'] ?? date('Y-m-d H:i:s');
                    $stmtIns->bind_param("iiis", $characterId, $nodeId, $lootId, $collectedAt);
                    $stmtIns->execute();
                }
            }

            // Restore monsters killed (clear existing then insert saved)
            $stmtDel = $this->db->prepare("DELETE FROM character_story_monsters_killed WHERE character_id = ?");
            $stmtDel->bind_param("i", $characterId);
            $stmtDel->execute();

            if (!empty($data['monsters_killed']) && is_array($data['monsters_killed'])) {
                $stmtIns = $this->db->prepare("INSERT INTO character_story_monsters_killed (character_id, node_id, monster_id, killed_at) VALUES (?, ?, ?, ?)");
                foreach ($data['monsters_killed'] as $mk) {
                    $nodeId = $mk['node_id'] ?? 0;
                    $monsterId = $mk['monster_id'] ?? $mk['monsterId'] ?? 0;
                    $killedAt = $mk['killed_at'] ?? date('Y-m-d H:i:s');
                    $stmtIns->bind_param("iiis", $characterId, $nodeId, $monsterId, $killedAt);
                    $stmtIns->execute();
                }
            }

            // Finally restore map unlocks (do this last so DB triggers from inserting quest progress don't leave extra unlocks)
            if (!empty($data['map_unlocks']) && is_array($data['map_unlocks'])) {
                $stmtDel = $this->db->prepare("DELETE FROM character_map_unlocks WHERE character_id = ?");
                $stmtDel->bind_param("i", $characterId);
                $stmtDel->execute();

                $stmtIns = $this->db->prepare("INSERT INTO character_map_unlocks (character_id, map_point_id, unlocked_at) VALUES (?, ?, ?)");
                foreach ($data['map_unlocks'] as $mpId) {
                    $unlockedAt = date('Y-m-d H:i:s');
                    $stmtIns->bind_param("iis", $characterId, $mpId, $unlockedAt);
                    $stmtIns->execute();
                }
            }
            
            $this->db->commit();

            // Final enforcement: ensure map_unlocks exactly match saved snapshot (run after commit)
            try {
                if (isset($data['map_unlocks']) && is_array($data['map_unlocks'])) {
                    $stmtFinalDel = $this->db->prepare("DELETE FROM character_map_unlocks WHERE character_id = ?");
                    $stmtFinalDel->bind_param("i", $characterId);
                    $stmtFinalDel->execute();

                    if (!empty($data['map_unlocks'])) {
                        $stmtFinalIns = $this->db->prepare("INSERT INTO character_map_unlocks (character_id, map_point_id, unlocked_at) VALUES (?, ?, ?)");
                        foreach ($data['map_unlocks'] as $mpId) {
                            $unlockedAt = date('Y-m-d H:i:s');
                            $stmtFinalIns->bind_param("iis", $characterId, $mpId, $unlockedAt);
                            $stmtFinalIns->execute();
                        }
                    }
                }

                // Debug logs: dump final map unlocks and quests for this character
                $stmtLog = $this->db->prepare("SELECT map_point_id FROM character_map_unlocks WHERE character_id = ?");
                $stmtLog->bind_param("i", $characterId);
                $stmtLog->execute();
                $mapUnlocks = array_column($stmtLog->get_result()->fetch_all(MYSQLI_ASSOC), 'map_point_id');

                $stmtLog2 = $this->db->prepare("SELECT id, quest_id, status FROM player_quests WHERE character_id = ?");
                $stmtLog2->bind_param("i", $characterId);
                $stmtLog2->execute();
                $playerQuests = $stmtLog2->get_result()->fetch_all(MYSQLI_ASSOC);

                error_log("[SaveService] loadSave completed for character {$characterId}. map_unlocks=" . json_encode($mapUnlocks) . " player_quests=" . json_encode($playerQuests));
            } catch (\Exception $le) {
                error_log("[SaveService] loadSave debug log failed: " . $le->getMessage());
            }

            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Load Save Error: " . $e->getMessage());
            return false;
        }
    }

    public function listSaves($characterId)
    {
        $stmt = $this->db->prepare("SELECT id, save_name, created_at FROM character_saves WHERE character_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $characterId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
