<?php

namespace App\Models;

use App\Config\Database;

class ProceduralTemplate
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all templates
     * 
     * @return array
     */
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM procedural_dungeon_templates ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find template by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM procedural_dungeon_templates WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $template = $result->fetch_assoc();
        if ($template) {
            $template['direction_types'] = json_decode($template['direction_types'], true);
            $template['room_themes'] = json_decode($template['room_themes'], true);
        }
        
        return $template;
    }

    /**
     * Create a new template
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO procedural_dungeon_templates 
             (name, description, min_rooms, max_rooms, connection_density, allow_loops, allow_backtrack, direction_types, room_themes, difficulty_scaling) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $directionTypes = json_encode($data['direction_types']);
        $roomThemes = json_encode($data['room_themes']);
        
        $stmt->bind_param(
            "ssiidiiiss", 
            $data['name'], 
            $data['description'], 
            $data['min_rooms'], 
            $data['max_rooms'],
            $data['connection_density'],
            $data['allow_loops'],
            $data['allow_backtrack'],
            $directionTypes,
            $roomThemes,
            $data['difficulty_scaling']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update a template
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE procedural_dungeon_templates 
             SET name = ?, description = ?, min_rooms = ?, max_rooms = ?, connection_density = ?, 
                 allow_loops = ?, allow_backtrack = ?, direction_types = ?, room_themes = ?, difficulty_scaling = ? 
             WHERE id = ?"
        );
        
        $directionTypes = json_encode($data['direction_types']);
        $roomThemes = json_encode($data['room_themes']);
        
        $stmt->bind_param(
            "ssiidiiissi", 
            $data['name'], 
            $data['description'], 
            $data['min_rooms'], 
            $data['max_rooms'],
            $data['connection_density'],
            $data['allow_loops'],
            $data['allow_backtrack'],
            $directionTypes,
            $roomThemes,
            $data['difficulty_scaling'],
            $id
        );
        
        return $stmt->execute();
    }

    /**
     * Delete a template
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM procedural_dungeon_templates WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get monster pools for a template
     * 
     * @param int $templateId
     * @return array
     */
    public function getMonsterPools($templateId)
    {
        $stmt = $this->db->prepare("SELECT * FROM procedural_monster_pools WHERE template_id = ?");
        $stmt->bind_param("i", $templateId);
        $stmt->execute();
        $result = $stmt->get_result();
        $pools = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($pools as &$pool) {
            if ($pool['monster_stats_base']) {
                $pool['monster_stats_base'] = json_decode($pool['monster_stats_base'], true);
            }
        }
        
        return $pools;
    }

    /**
     * Get loot pools for a template
     * 
     * @param int $templateId
     * @return array
     */
    public function getLootPools($templateId)
    {
        $stmt = $this->db->prepare(
            "SELECT plp.*, i.name, i.icon, i.rarity 
             FROM procedural_loot_pools plp
             JOIN items i ON plp.item_id = i.id
             WHERE plp.template_id = ?"
        );
        $stmt->bind_param("i", $templateId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get room images for a template
     * 
     * @param int $templateId
     * @param string|null $theme
     * @return array
     */
    public function getRoomImages($templateId, $theme = null)
    {
        $sql = "SELECT * FROM procedural_room_images WHERE template_id = ?";
        $params = ["i", $templateId];
        
        if ($theme) {
            $sql .= " AND theme = ?";
            $params[0] .= "s";
            $params[] = $theme;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add monster pool
     */
    public function addMonsterPool($templateId, $data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO procedural_monster_pools (template_id, monster_name, min_level, max_level, spawn_weight, min_quantity, max_quantity, is_boss, boss_room_only) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $isBoss = isset($data['is_boss']) ? 1 : 0;
        $bossOnly = isset($data['boss_room_only']) ? 1 : 0;
        
        $stmt->bind_param(
            "isiiiiiii", 
            $templateId, 
            $data['monster_name'], 
            $data['min_level'], 
            $data['max_level'],
            $data['spawn_weight'],
            $data['min_quantity'],
            $data['max_quantity'],
            $isBoss,
            $bossOnly
        );
        
        return $stmt->execute();
    }

    /**
     * Delete monster pool
     */
    public function deleteMonsterPool($poolId)
    {
        $stmt = $this->db->prepare("DELETE FROM procedural_monster_pools WHERE id = ?");
        $stmt->bind_param("i", $poolId);
        return $stmt->execute();
    }

    /**
     * Add loot pool
     */
    public function addLootPool($templateId, $data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO procedural_loot_pools (template_id, item_id, drop_weight, min_quantity, max_quantity, rarity, boss_loot_only) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $bossOnly = isset($data['boss_loot_only']) ? 1 : 0;
        
        $stmt->bind_param(
            "iiiiisi", 
            $templateId, 
            $data['item_id'], 
            $data['drop_weight'], 
            $data['min_quantity'],
            $data['max_quantity'],
            $data['rarity'],
            $bossOnly
        );
        
        return $stmt->execute();
    }

    /**
     * Delete loot pool
     */
    public function deleteLootPool($poolId)
    {
        $stmt = $this->db->prepare("DELETE FROM procedural_loot_pools WHERE id = ?");
        $stmt->bind_param("i", $poolId);
        return $stmt->execute();
    }
}
