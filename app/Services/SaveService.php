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
        // 1. Gather Data
        $data = [];

        // Character Data (Base + Stats)
        $charModel = new Character();
        $char = $charModel->findById($characterId);
        $data['character_stats'] = $char; // Includes vitality, strength, etc. from join usually? 
                                          // findById returns joined data from character_stats. 
                                          // Let's verify specifically what we need to restore.
                                          // We need: current_hp, xp, level, gold, attributes.

        // Inventory
        $invModel = new Inventory();
        $data['inventory'] = $invModel->getCharacterInventory($characterId);

        // Skills
        $skillModel = new Skill();
        $data['skills'] = $skillModel->getUnlockedSkills($characterId);

        // Story Progress
        $progModel = new StoryProgress();
        $data['progress'] = $progModel->getActiveStory($characterId); // Save active story state

        // 2. Serialize
        $json = json_encode($data);

        // 3. Save to DB
        $stmt = $this->db->prepare("INSERT INTO character_saves (character_id, save_name, save_data) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $characterId, $saveName, $json);
        
        return $stmt->execute();
    }

    /**
     * Load a Save Game
     */
    public function loadSave($saveId, $characterId)
    {
        // 1. Fetch Save
        $stmt = $this->db->prepare("SELECT * FROM character_saves WHERE id = ? AND character_id = ?");
        $stmt->bind_param("ii", $saveId, $characterId);
        $stmt->execute();
        $save = $stmt->get_result()->fetch_assoc();

        if (!$save) return false;

        $data = json_decode($save['save_data'], true);
        if (!$data) return false;

        $this->db->begin_transaction();

        try {
            // 2. Restore Stats
            // We assume character row exists. We update it.
            $stats = $data['character_stats'];
            $stmtUpdate = $this->db->prepare("UPDATE character_stats SET 
                level = ?, experience = ?, skill_points = ?, gold = ?, current_hp = ?,
                vitality = ?, strength = ?, dexterity = ?, intelligence = ?, wisdom = ?, luck = ?
                WHERE character_id = ?");
            
            $stmtUpdate->bind_param("iiiidiiiiiii", 
                $stats['level'], $stats['experience'], $stats['skill_points'], $stats['gold'], $stats['current_hp'],
                $stats['vitality'], $stats['strength'], $stats['dexterity'], $stats['intelligence'], $stats['wisdom'], $stats['luck'],
                $characterId
            );
            $stmtUpdate->execute();

            // 3. Restore Inventory
            // Wipe current inventory
            $this->db->query("DELETE FROM character_inventory WHERE character_id = $characterId");
            
            // Re-insert
            if (!empty($data['inventory'])) {
                $stmtInv = $this->db->prepare("INSERT INTO character_inventory (character_id, item_id, quantity, is_equipped, slot) VALUES (?, ?, ?, ?, ?)");
                foreach ($data['inventory'] as $item) {
                     $slot = $item['slot'] ?? null; // Handle null slot
                     // Note: getCharacterInventory usually joins items. We need item_id.
                     $itemId = $item['item_id'] ?? $item['id']; // Depends on fetch structure. 
                     // Inventory::getCharacterInventory selects `ci.*, i.name...`. So `item_id` should be there.
                     
                     // Warning: $item['is_equipped'] might be boolean or int.
                     $isEquipped = $item['is_equipped'] ? 1 : 0;
                     
                     $stmtInv->bind_param("iiisi", $characterId, $itemId, $item['quantity'], $isEquipped, $slot);
                     $stmtInv->execute();
                }
            }

            // 4. Restore Skills
            // Wipe current skills
            $this->db->query("DELETE FROM character_skills WHERE character_id = $characterId");
            
            // Re-insert
            if (!empty($data['skills'])) {
                $stmtSkill = $this->db->prepare("INSERT INTO character_skills (character_id, skill_id) VALUES (?, ?)");
                foreach ($data['skills'] as $skill) {
                    // getUnlockedSkills returns joined data. id is skill_id usually if selected from skills?
                    // Or ci.skill_id.
                    // Let's assume $skill['id'] is the skill ID.
                    $skillId = $skill['id'];
                    $stmtSkill->bind_param("ii", $characterId, $skillId);
                    $stmtSkill->execute();
                }
            }

            // 5. Restore Progress
            // We only support restoring ONE active story for now (from valid save)
            // Or maybe we verify the story still exists?
            // "Active Story" restoration requires table `story_progress`.
            // Wipe progress for this story? Or all progress?
            // "Remise au dernier point de sauvegarde" -> Restore the SAVED progress state.
            
            if (!empty($data['progress'])) {
                $prog = $data['progress'];
                // Update or Insert
                // Check if exists
                $check = $this->db->query("SELECT id FROM story_progress WHERE character_id = $characterId AND story_id = " . $prog['story_id']);
                if ($check->num_rows > 0) {
                     // Update
                     $stmtProg = $this->db->prepare("UPDATE story_progress SET current_node_id = ?, status = ?, visited_nodes = ?, collected_loots = ?, monsters_killed = ? WHERE character_id = ? AND story_id = ?");
                     $stmtProg->bind_param("issssii", 
                        $prog['current_node_id'], $prog['status'], $prog['visited_nodes'], $prog['collected_loots'], $prog['monsters_killed'],
                        $characterId, $prog['story_id']
                     );
                     $stmtProg->execute();
                } else {
                     // Insert
                     $stmtProg = $this->db->prepare("INSERT INTO story_progress (character_id, story_id, current_node_id, status, visited_nodes, collected_loots, monsters_killed) VALUES (?, ?, ?, ?, ?, ?, ?)");
                     $stmtProg->bind_param("iiissss",
                        $characterId, $prog['story_id'], $prog['current_node_id'], $prog['status'], $prog['visited_nodes'], $prog['collected_loots'], $prog['monsters_killed']
                     );
                     $stmtProg->execute();
                }
            }
            
            $this->db->commit();
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
