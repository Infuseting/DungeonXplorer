-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Dec 16, 2025 at 05:21 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dungeon_xplorer`
--

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `appearance` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_played_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `gold` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_buffs`
--

CREATE TABLE `character_buffs` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `stat_modifiers` json DEFAULT NULL,
  `duration_type` enum('seconds','turns') NOT NULL,
  `duration_remaining` int NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_inventory`
--

CREATE TABLE `character_inventory` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `item_id` int NOT NULL,
  `location` enum('equipped','backpack','pockets') NOT NULL,
  `slot_name` enum('head','shoulders','amulet','chest','belt','legs','boots','ring_1','ring_2','main_hand','off_hand','gloves','bracers','backpack') DEFAULT NULL,
  `grid_x` tinyint DEFAULT NULL,
  `grid_y` tinyint DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `instance_stats` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_map_unlocks`
--

CREATE TABLE `character_map_unlocks` (
  `character_id` int NOT NULL,
  `map_point_id` int NOT NULL,
  `unlocked_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_saves`
--

CREATE TABLE `character_saves` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `save_name` varchar(255) NOT NULL,
  `save_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_skills`
--

CREATE TABLE `character_skills` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `skill_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_stats`
--

CREATE TABLE `character_stats` (
  `character_id` int NOT NULL,
  `level` int DEFAULT '1',
  `xp` int DEFAULT '0',
  `strength` int DEFAULT '10',
  `dexterity` int DEFAULT '10',
  `intelligence` int DEFAULT '10',
  `vitality` int DEFAULT '10',
  `current_hp` int DEFAULT '100',
  `skill_points` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `character_stats`
--
DELIMITER $$
CREATE TRIGGER `before_character_stats_insert` BEFORE INSERT ON `character_stats` FOR EACH ROW BEGIN
    -- Check if the new current_hp exceeds the vitality (which acts as max_hp)
    IF NEW.current_hp > NEW.vitality THEN
        -- If it does, cap the current_hp to the value of vitality
        SET NEW.current_hp = NEW.vitality;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_character_stats_update` BEFORE UPDATE ON `character_stats` FOR EACH ROW BEGIN
    -- Check if the updated current_hp is greater than the new vitality value
    IF NEW.current_hp > NEW.vitality THEN
        -- If so, cap the current_hp to match the new vitality (max HP)
        SET NEW.current_hp = NEW.vitality;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `character_story_loots_collected`
--

CREATE TABLE `character_story_loots_collected` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `node_id` int NOT NULL,
  `loot_id` int NOT NULL,
  `collected_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_story_monsters_killed`
--

CREATE TABLE `character_story_monsters_killed` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `node_id` int NOT NULL,
  `monster_id` int NOT NULL,
  `killed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_story_node_status`
--

CREATE TABLE `character_story_node_status` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `node_id` int NOT NULL,
  `is_visited` tinyint(1) DEFAULT '0',
  `monsters_cleared` tinyint(1) DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_story_progress`
--

CREATE TABLE `character_story_progress` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `story_id` int NOT NULL,
  `current_node_id` int NOT NULL,
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed` tinyint(1) DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `in_dungeon` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `base_stats_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dialogues`
--

CREATE TABLE `dialogues` (
  `id` int NOT NULL,
  `tree_id` int NOT NULL COMMENT 'Arbre de dialogue auquel appartient ce nœud',
  `parent_id` int DEFAULT NULL COMMENT 'ID du dialogue parent (NULL = racine de l''arbre)',
  `text` text NOT NULL COMMENT 'Texte dit par le PNJ ou texte de la réponse',
  `is_player_choice` tinyint(1) DEFAULT '0' COMMENT 'TRUE si c''est un choix du joueur, FALSE si c''est le PNJ qui parle',
  `choice_text` varchar(255) DEFAULT NULL COMMENT 'Texte du bouton de choix (si is_player_choice=TRUE)',
  `order_index` int DEFAULT '0' COMMENT 'Ordre d''affichage parmi les frères',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dialogue_trees`
--

CREATE TABLE `dialogue_trees` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Nom de l''arbre (ex: "Salutation marchand")',
  `description` text COMMENT 'Description de l''arbre de dialogue',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `type` enum('equipment','consumable','material') NOT NULL,
  `slot_type` enum('head','shoulders','amulet','chest','belt','legs','boots','ring','main_hand','off_hand','gloves','bracers','backpack','none') NOT NULL DEFAULT 'none',
  `two_handed` tinyint(1) NOT NULL DEFAULT '0',
  `width` tinyint NOT NULL DEFAULT '1',
  `height` tinyint NOT NULL DEFAULT '1',
  `weight` decimal(5,2) NOT NULL DEFAULT '0.00',
  `icon` varchar(255) DEFAULT NULL,
  `stats` json DEFAULT NULL,
  `max_stack` tinyint NOT NULL DEFAULT '1',
  `price` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `stat_ranges` json DEFAULT NULL,
  `is_purchasable` tinyint(1) NOT NULL DEFAULT '1',
  `effect_type` enum('none','heal','buff') DEFAULT 'none',
  `duration_type` enum('instant','seconds','turns') DEFAULT 'instant',
  `duration_value` int DEFAULT '0',
  `effect_value` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loot_tables`
--

CREATE TABLE `loot_tables` (
  `id` int NOT NULL,
  `source_type` enum('mob','dungeon') NOT NULL,
  `source_id` int NOT NULL,
  `item_id` int NOT NULL,
  `chance` int DEFAULT '10',
  `min_quantity` int DEFAULT '1',
  `max_quantity` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maps`
--

CREATE TABLE `maps` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `parent_map_id` int DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `parent_location_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `map_points`
--

CREATE TABLE `map_points` (
  `id` int NOT NULL,
  `map_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `x` int NOT NULL,
  `y` int NOT NULL,
  `type` enum('story','place','dungeon','npc','quest') NOT NULL,
  `target_id` int DEFAULT NULL,
  `sub_map_id` int DEFAULT NULL,
  `story_id` int DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT '0',
  `unlock_quest_id` int DEFAULT NULL,
  `unlock_condition_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `radius` int DEFAULT '20',
  `label` varchar(100) DEFAULT NULL,
  `description` text,
  `is_hidden` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monsters`
--

CREATE TABLE `monsters` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `level_min` int DEFAULT '1',
  `level_max` int DEFAULT '100',
  `base_stats_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `salle_path` varchar(128) DEFAULT NULL,
  `creature_type` varchar(50) DEFAULT 'neutral',
  `affinities` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `npcs`
--

CREATE TABLE `npcs` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('merchant','quest_giver','lore','guard','npc') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `texture` varchar(255) DEFAULT NULL COMMENT 'Chemin vers l''image du PNJ',
  `merchant_seed` int DEFAULT NULL COMMENT 'SEED pour génération inventaire marchand (NULL si non marchand)',
  `buy_rate_own` decimal(5,2) DEFAULT '0.05' COMMENT 'Taux rachat items vendus par le marchand (5%)',
  `buy_rate_other` decimal(5,2) DEFAULT '0.15' COMMENT 'Taux rachat autres items (15%)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `npc_dialogue_trees`
--

CREATE TABLE `npc_dialogue_trees` (
  `npc_id` int NOT NULL,
  `tree_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `npc_merchant_inventory`
--

CREATE TABLE `npc_merchant_inventory` (
  `npc_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int DEFAULT '1' COMMENT 'Quantité en stock (pour futurs développements)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `npc_quests`
--

CREATE TABLE `npc_quests` (
  `npc_id` int NOT NULL,
  `quest_id` int NOT NULL,
  `type` enum('GIVER','RECEIVER') NOT NULL DEFAULT 'GIVER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `player_quests`
--

CREATE TABLE `player_quests` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `quest_id` int NOT NULL,
  `current_stage_id` int DEFAULT NULL,
  `status` enum('ACTIVE','COMPLETED','FAILED') DEFAULT 'ACTIVE',
  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `player_quest_progress`
--

CREATE TABLE `player_quest_progress` (
  `id` int NOT NULL,
  `player_quest_id` int NOT NULL,
  `objective_id` int NOT NULL,
  `count_current` int DEFAULT '0',
  `is_completed` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `player_quest_progress`
--
DELIMITER $$
CREATE TRIGGER `after_quest_progress_update` AFTER UPDATE ON `player_quest_progress` FOR EACH ROW BEGIN
    DECLARE v_stage_id INT;
    DECLARE v_character_id INT;
    DECLARE v_user_id INT;
    DECLARE v_total_objectives INT;
    DECLARE v_completed_objectives INT;
    DECLARE done INT DEFAULT 0;
    DECLARE v_map_point_id INT;
    
    -- Cursor for map points to unlock
    DECLARE map_point_cursor CURSOR FOR
        SELECT qsu.map_point_id
        FROM quest_stage_unlocks qsu
        WHERE qsu.quest_stage_id = v_stage_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    -- Only proceed if the objective was just completed (changed from 0 to 1)
    IF NEW.is_completed = 1 AND OLD.is_completed = 0 THEN
        
        -- Get the stage_id from the objective
        SELECT stage_id INTO v_stage_id
        FROM quest_objectives
        WHERE id = NEW.objective_id;
        
        -- Get character_id and user_id from player_quests
        SELECT pq.character_id, c.user_id INTO v_character_id, v_user_id
        FROM player_quests pq
        JOIN characters c ON pq.character_id = c.id
        WHERE pq.id = NEW.player_quest_id;
        
        -- Count total objectives for this stage
        SELECT COUNT(*) INTO v_total_objectives
        FROM quest_objectives
        WHERE stage_id = v_stage_id;
        
        -- Count completed objectives for this player quest
        SELECT COUNT(*) INTO v_completed_objectives
        FROM player_quest_progress pqp
        JOIN quest_objectives qo ON pqp.objective_id = qo.id
        WHERE pqp.player_quest_id = NEW.player_quest_id
        AND qo.stage_id = v_stage_id
        AND pqp.is_completed = 1;
        
        -- If all objectives are completed, unlock map points
        IF v_total_objectives = v_completed_objectives THEN
            
            -- Open cursor and unlock all map points for this stage
            OPEN map_point_cursor;
            
            read_loop: LOOP
                FETCH map_point_cursor INTO v_map_point_id;
                
                IF done THEN
                    LEAVE read_loop;
                END IF;
                
                -- Insert unlock record if it doesn't exist
                INSERT IGNORE INTO character_map_unlocks 
                    (character_id, map_point_id, unlocked_at)
                VALUES 
                    (v_character_id, v_map_point_id, CURRENT_TIMESTAMP);
                    
            END LOOP;
            
            CLOSE map_point_cursor;
            
        END IF;
        
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `procedural_dungeon_templates`
--

CREATE TABLE `procedural_dungeon_templates` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `min_rooms` int DEFAULT '5',
  `max_rooms` int DEFAULT '15',
  `connection_density` float DEFAULT '0.3' COMMENT 'Pourcentage de connexions supplémentaires (0.0 à 1.0)',
  `allow_loops` tinyint(1) DEFAULT '1' COMMENT 'Autoriser les boucles dans le graphe',
  `allow_backtrack` tinyint(1) DEFAULT '1' COMMENT 'Autoriser le retour en arrière',
  `direction_types` json DEFAULT NULL COMMENT 'Types de directions possibles: ["north","south","east","west","up","down","custom"]',
  `room_themes` json DEFAULT NULL COMMENT 'Thèmes de pièces possibles avec probabilités',
  `difficulty_scaling` enum('fixed','linear','exponential') DEFAULT 'linear',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procedural_loot_pools`
--

CREATE TABLE `procedural_loot_pools` (
  `id` int NOT NULL,
  `template_id` int NOT NULL,
  `item_id` int NOT NULL,
  `drop_weight` int DEFAULT '100' COMMENT 'Poids de drop (plus élevé = plus fréquent)',
  `min_quantity` int DEFAULT '1',
  `max_quantity` int DEFAULT '1',
  `rarity` enum('common','uncommon','rare','epic','legendary') DEFAULT 'common',
  `boss_loot_only` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procedural_monster_pools`
--

CREATE TABLE `procedural_monster_pools` (
  `id` int NOT NULL,
  `template_id` int NOT NULL,
  `monster_name` varchar(255) NOT NULL,
  `min_level` int NOT NULL,
  `max_level` int NOT NULL,
  `spawn_weight` int DEFAULT '100' COMMENT 'Poids de spawn (plus élevé = plus fréquent)',
  `min_quantity` int DEFAULT '1',
  `max_quantity` int DEFAULT '3',
  `is_boss` tinyint(1) DEFAULT '0',
  `boss_room_only` tinyint(1) DEFAULT '0' COMMENT 'Spawn uniquement dans les salles boss',
  `monster_stats_base` json DEFAULT NULL COMMENT 'Stats de base du monstre',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procedural_room_images`
--

CREATE TABLE `procedural_room_images` (
  `id` int NOT NULL,
  `template_id` int NOT NULL,
  `theme` varchar(100) DEFAULT NULL COMMENT 'Thème de la pièce (cave, dungeon, temple, etc.)',
  `image_path` varchar(500) NOT NULL,
  `is_boss_room` tinyint(1) DEFAULT '0',
  `is_start_room` tinyint(1) DEFAULT '0',
  `is_end_room` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

CREATE TABLE `quests` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `min_level` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `intro_text` text COMMENT 'Text spoken by NPC when offering the quest',
  `xp_reward` int DEFAULT '0',
  `gold_reward` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest_objectives`
--

CREATE TABLE `quest_objectives` (
  `id` int NOT NULL,
  `stage_id` int NOT NULL,
  `type` enum('TALK_NPC','KILL_MONSTER','HAVE_ITEM','VISIT_LOCATION','DUNGEON_CLEAR') NOT NULL,
  `target_id` int DEFAULT NULL COMMENT 'ID of NPC, Monster, Item, or Location',
  `count_required` int DEFAULT '1',
  `description` varchar(255) DEFAULT NULL,
  `dialogue_tree_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest_prerequisites`
--

CREATE TABLE `quest_prerequisites` (
  `quest_id` int NOT NULL,
  `required_quest_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest_reward_items`
--

CREATE TABLE `quest_reward_items` (
  `id` int NOT NULL,
  `quest_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_quests`
--

CREATE TABLE `daily_quests` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `objective_type` enum('KILL_MONSTERS','COLLECT_GOLD','COMPLETE_DUNGEON','VISIT_LOCATIONS','USE_ITEMS') NOT NULL,
  `objective_target` int DEFAULT NULL COMMENT 'ID cible optionnel (monster_id, item_id, etc.)',
  `objective_count` int NOT NULL DEFAULT '1',
  `gold_reward` int NOT NULL DEFAULT '5',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `player_daily_quests`
--

CREATE TABLE `player_daily_quests` (
  `id` int NOT NULL,
  `character_id` int NOT NULL,
  `daily_quest_id` int NOT NULL,
  `assigned_date` date NOT NULL,
  `current_progress` int NOT NULL DEFAULT '0',
  `status` enum('ACTIVE','COMPLETED','CLAIMED') NOT NULL DEFAULT 'ACTIVE',
  `completed_at` timestamp NULL DEFAULT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest_stages`
--

CREATE TABLE `quest_stages` (
  `id` int NOT NULL,
  `quest_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `order_index` int DEFAULT '0',
  `rewards_json` json DEFAULT NULL COMMENT '{"xp": 100, "gold": 50, "items": [{"id": 1, "qty": 1}]}',
  `unlocks_json` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest_stage_unlocks`
--

CREATE TABLE `quest_stage_unlocks` (
  `id` int NOT NULL,
  `quest_stage_id` int NOT NULL,
  `map_point_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int NOT NULL,
  `class_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `type` enum('active','passive') DEFAULT 'active',
  `cost_mp` int DEFAULT '0',
  `cost_sp` int DEFAULT '1',
  `cooldown` int DEFAULT '0',
  `effect_type` varchar(50) DEFAULT NULL,
  `effect_value` int DEFAULT '0',
  `min_level` int DEFAULT '1',
  `parent_skill_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `type` enum('manual','procedural') NOT NULL DEFAULT 'manual',
  `difficulty_level` int DEFAULT '1',
  `min_level` int DEFAULT '1',
  `procedural_template_id` int DEFAULT NULL COMMENT 'ID du template si type=procedural',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_instances`
--

CREATE TABLE `story_instances` (
  `id` int NOT NULL,
  `story_id` int NOT NULL,
  `seed` bigint NOT NULL COMMENT 'Seed de génération pour reproductibilité',
  `instance_type` enum('shared','character') DEFAULT 'shared' COMMENT 'shared=tous les joueurs, character=unique par joueur',
  `character_id` int DEFAULT NULL COMMENT 'NULL si shared, ID du personnage si character',
  `generated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Date d''expiration (défaut +48h)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_nodes`
--

CREATE TABLE `story_nodes` (
  `id` int NOT NULL,
  `story_id` int NOT NULL,
  `story_instance_id` int DEFAULT NULL COMMENT 'NULL pour donjons manuels, ID instance pour procéduraux',
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `is_start_node` tinyint(1) DEFAULT '0',
  `is_end_node` tinyint(1) DEFAULT '0',
  `can_exit` tinyint(1) DEFAULT '0',
  `node_x` int DEFAULT '0' COMMENT 'Position X pour affichage graphique',
  `node_y` int DEFAULT '0' COMMENT 'Position Y pour affichage graphique',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_node_connections`
--

CREATE TABLE `story_node_connections` (
  `id` int NOT NULL,
  `from_node_id` int NOT NULL,
  `to_node_id` int NOT NULL,
  `direction_text` varchar(255) NOT NULL COMMENT 'Texte affiché pour cette direction',
  `order_index` int DEFAULT '0' COMMENT 'Ordre d''affichage des directions',
  `condition_type` enum('none','item','quest_active','quest_completed','quest_stage','monster_killed','level') DEFAULT 'none',
  `condition_value` varchar(255) DEFAULT NULL COMMENT 'ID de l''item, quest, ou niveau requis',
  `allow_return` tinyint(1) DEFAULT '0',
  `return_text` varchar(100) DEFAULT '',
  `return_condition_type` enum('none','item','level','quest_active','quest_completed','quest_stage','monster_killed') DEFAULT 'none',
  `return_condition_value` varchar(255) DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_node_loots`
--

CREATE TABLE `story_node_loots` (
  `id` int NOT NULL,
  `node_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `drop_chance` float DEFAULT '1' COMMENT 'Probabilité de drop (0.0 à 1.0)',
  `is_guaranteed` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_node_monsters`
--

CREATE TABLE `story_node_monsters` (
  `id` int NOT NULL,
  `node_id` int NOT NULL,
  `monster_name` varchar(255) NOT NULL,
  `monster_level` int NOT NULL,
  `monster_stats` json DEFAULT NULL COMMENT 'Stats du monstre (HP, ATK, DEF, etc.)',
  `quantity` int DEFAULT '1',
  `is_boss` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `can_flee` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_node_npcs`
--

CREATE TABLE `story_node_npcs` (
  `id` int NOT NULL,
  `node_id` int NOT NULL,
  `npc_id` int NOT NULL,
  `position_x` float DEFAULT '0',
  `position_y` float DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_social_accounts`
--

CREATE TABLE `user_social_accounts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `provider` enum('google','discord','github') COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `selector` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_validator` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_selector` (`selector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `houses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` int NOT NULL DEFAULT 0,
  `storage_slots` int NOT NULL DEFAULT 20,
  `furniture_slots` int NOT NULL DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `location_name` varchar(100) DEFAULT NULL,
  `map_x` int NOT NULL DEFAULT 54,
  `map_y` int NOT NULL DEFAULT -108,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `required_level` int NOT NULL DEFAULT 1,
  `workbench_price` int NOT NULL DEFAULT 5000,
  `workbench_required_level` int NOT NULL DEFAULT 10,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_houses`
--

CREATE TABLE `character_houses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_id` int NOT NULL,
  `house_id` int NOT NULL,
  `custom_name` varchar(100) DEFAULT NULL,
  `purchased_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_character_house` (`character_id`, `house_id`),
  KEY `fk_character_house_character` (`character_id`),
  KEY `fk_character_house_house` (`house_id`),
  CONSTRAINT `fk_character_house_character` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_character_house_house` FOREIGN KEY (`house_id`) REFERENCES `houses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `furniture_categories`
--

CREATE TABLE `furniture_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT '🪑',
  `sort_order` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `furniture`
--

CREATE TABLE `furniture` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` int NOT NULL DEFAULT 0,
  `icon` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `bonus_type` enum('storage','comfort','luck','xp','gold','defense','none') DEFAULT 'none',
  `bonus_value` int NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `required_level` int NOT NULL DEFAULT 1,
  `rarity` enum('common','uncommon','rare','epic','legendary') DEFAULT 'common',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_furniture_category` (`category_id`),
  CONSTRAINT `fk_furniture_category` FOREIGN KEY (`category_id`) REFERENCES `furniture_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_house_furniture`
--

CREATE TABLE `character_house_furniture` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_house_id` int NOT NULL,
  `furniture_id` int NOT NULL,
  `position_x` int DEFAULT 0,
  `position_y` int DEFAULT 0,
  `placed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_house_furniture_house` (`character_house_id`),
  KEY `fk_house_furniture_furniture` (`furniture_id`),
  CONSTRAINT `fk_house_furniture_house` FOREIGN KEY (`character_house_id`) REFERENCES `character_houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_house_furniture_furniture` FOREIGN KEY (`furniture_id`) REFERENCES `furniture` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `house_storage`
--

CREATE TABLE `house_storage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_house_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT 1,
  `slot_index` int NOT NULL DEFAULT 0,
  `stored_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_storage_house` (`character_house_id`),
  KEY `fk_storage_item` (`item_id`),
  CONSTRAINT `fk_storage_house` FOREIGN KEY (`character_house_id`) REFERENCES `character_houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_storage_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enchantments`
--

CREATE TABLE IF NOT EXISTS `enchantments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(255) DEFAULT 'assets/items/enchantment_default.png',
  `stat_modifiers` json DEFAULT NULL COMMENT 'Ex: {"damage": 10, "strength": 5}',
  `compatible_slot_types` json DEFAULT NULL COMMENT 'Ex: ["main_hand", "chest", "head"]',
  `rarity` enum('common','uncommon','rare','epic','legendary') DEFAULT 'common',
  `cost` int NOT NULL DEFAULT 100,
  `required_level` int NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `character_house_workbenches`
--

CREATE TABLE IF NOT EXISTS `character_house_workbenches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_house_id` int NOT NULL,
  `purchased_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_enchantments` int NOT NULL DEFAULT 0 COMMENT 'Nombre d''enchantements effectués',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_house_workbench` (`character_house_id`),
  KEY `fk_workbench_house` (`character_house_id`),
  CONSTRAINT `fk_workbench_house` FOREIGN KEY (`character_house_id`) REFERENCES `character_houses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_enchantments`
--

CREATE TABLE IF NOT EXISTS `item_enchantments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_inventory_id` int NOT NULL,
  `enchantment_id` int NOT NULL,
  `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_item_enchant_inventory` (`character_inventory_id`),
  KEY `fk_item_enchant_enchantment` (`enchantment_id`),
  CONSTRAINT `fk_item_enchant_inventory` FOREIGN KEY (`character_inventory_id`) REFERENCES `character_inventory` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_enchant_enchantment` FOREIGN KEY (`enchantment_id`) REFERENCES `enchantments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;







--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `character_buffs`
--
ALTER TABLE `character_buffs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `character_id` (`character_id`);

--
-- Indexes for table `character_inventory`
--
ALTER TABLE `character_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `character_id` (`character_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `character_map_unlocks`
--
ALTER TABLE `character_map_unlocks`
  ADD PRIMARY KEY (`map_point_id`,`character_id`),
  ADD KEY `character_id` (`character_id`);

--
-- Indexes for table `character_saves`
--
ALTER TABLE `character_saves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `character_id` (`character_id`);

--
-- Indexes for table `character_skills`
--
ALTER TABLE `character_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_char_skill` (`character_id`,`skill_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- Indexes for table `character_stats`
--
ALTER TABLE `character_stats`
  ADD PRIMARY KEY (`character_id`);

--
-- Indexes for table `character_story_loots_collected`
--
ALTER TABLE `character_story_loots_collected`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_character_node_loot` (`character_id`,`node_id`,`loot_id`),
  ADD KEY `node_id` (`node_id`),
  ADD KEY `loot_id` (`loot_id`),
  ADD KEY `idx_character_id` (`character_id`);

--
-- Indexes for table `character_story_monsters_killed`
--
ALTER TABLE `character_story_monsters_killed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kill` (`character_id`,`node_id`,`monster_id`);

--
-- Indexes for table `character_story_node_status`
--
ALTER TABLE `character_story_node_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_character_node` (`character_id`,`node_id`),
  ADD KEY `node_id` (`node_id`),
  ADD KEY `idx_character_id` (`character_id`);

--
-- Indexes for table `character_story_progress`
--
ALTER TABLE `character_story_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_character_story` (`character_id`,`story_id`),
  ADD KEY `story_id` (`story_id`),
  ADD KEY `current_node_id` (`current_node_id`),
  ADD KEY `idx_character_id` (`character_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `dialogues`
--
ALTER TABLE `dialogues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dialogues_tree` (`tree_id`),
  ADD KEY `idx_dialogues_parent` (`parent_id`);

--
-- Indexes for table `dialogue_trees`
--
ALTER TABLE `dialogue_trees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loot_tables`
--
ALTER TABLE `loot_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maps`
--
ALTER TABLE `maps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_location_id` (`parent_location_id`);

--
-- Indexes for table `map_points`
--
ALTER TABLE `map_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `map_id` (`map_id`),
  ADD KEY `sub_map_id` (`sub_map_id`),
  ADD KEY `story_id` (`story_id`);

--
-- Indexes for table `monsters`
--
ALTER TABLE `monsters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `npcs`
--
ALTER TABLE `npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_npcs_merchant_seed` (`merchant_seed`);

--
-- Indexes for table `npc_dialogue_trees`
--
ALTER TABLE `npc_dialogue_trees`
  ADD PRIMARY KEY (`npc_id`,`tree_id`),
  ADD KEY `tree_id` (`tree_id`);

--
-- Indexes for table `npc_merchant_inventory`
--
ALTER TABLE `npc_merchant_inventory`
  ADD PRIMARY KEY (`npc_id`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `npc_quests`
--
ALTER TABLE `npc_quests`
  ADD PRIMARY KEY (`npc_id`,`quest_id`,`type`),
  ADD KEY `idx_npc_quests_quest` (`quest_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `player_quests`
--
ALTER TABLE `player_quests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_player_quests_quest` (`quest_id`),
  ADD KEY `player_quests_ibfk_3` (`current_stage_id`),
  ADD KEY `character_id` (`character_id`);

--
-- Indexes for table `player_quest_progress`
--
ALTER TABLE `player_quest_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pqp_player_quest` (`player_quest_id`),
  ADD KEY `idx_pqp_objective` (`objective_id`);

--
-- Indexes for table `procedural_dungeon_templates`
--
ALTER TABLE `procedural_dungeon_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `procedural_loot_pools`
--
ALTER TABLE `procedural_loot_pools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_template_id` (`template_id`);

--
-- Indexes for table `procedural_monster_pools`
--
ALTER TABLE `procedural_monster_pools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_template_id` (`template_id`);

--
-- Indexes for table `procedural_room_images`
--
ALTER TABLE `procedural_room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_template_theme` (`template_id`,`theme`);

--
-- Indexes for table `quests`
--
ALTER TABLE `quests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quest_objectives`
--
ALTER TABLE `quest_objectives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quest_objectives_stage` (`stage_id`),
  ADD KEY `dialogue_tree_id` (`dialogue_tree_id`);

--
-- Indexes for table `quest_prerequisites`
--
ALTER TABLE `quest_prerequisites`
  ADD PRIMARY KEY (`quest_id`,`required_quest_id`),
  ADD KEY `idx_qp_required` (`required_quest_id`);

--
-- Indexes for table `quest_reward_items`
--
ALTER TABLE `quest_reward_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quest_id` (`quest_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `quest_stages`
--
ALTER TABLE `quest_stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quest_stages_quest` (`quest_id`);

--
-- Indexes for table `quest_stage_unlocks`
--
ALTER TABLE `quest_stage_unlocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_qsu_stage` (`quest_stage_id`),
  ADD KEY `idx_qsu_point` (`map_point_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `parent_skill_id` (`parent_skill_id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `story_instances`
--
ALTER TABLE `story_instances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_character_instance` (`story_id`,`character_id`),
  ADD KEY `character_id` (`character_id`),
  ADD KEY `idx_story_id` (`story_id`);

--
-- Indexes for table `story_nodes`
--
ALTER TABLE `story_nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_story_id` (`story_id`),
  ADD KEY `idx_instance_id` (`story_instance_id`);

--
-- Indexes for table `story_node_connections`
--
ALTER TABLE `story_node_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `to_node_id` (`to_node_id`),
  ADD KEY `idx_from_node` (`from_node_id`);

--
-- Indexes for table `story_node_loots`
--
ALTER TABLE `story_node_loots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `idx_node_id` (`node_id`);

--
-- Indexes for table `story_node_monsters`
--
ALTER TABLE `story_node_monsters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_node_id` (`node_id`);

--
-- Indexes for table `story_node_npcs`
--
ALTER TABLE `story_node_npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npc_id` (`npc_id`),
  ADD KEY `idx_node_id` (`node_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_social_accounts`
--
ALTER TABLE `user_social_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_social_account` (`provider`,`provider_user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_buffs`
--
ALTER TABLE `character_buffs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_inventory`
--
ALTER TABLE `character_inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_saves`
--
ALTER TABLE `character_saves`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_skills`
--
ALTER TABLE `character_skills`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_story_loots_collected`
--
ALTER TABLE `character_story_loots_collected`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_story_monsters_killed`
--
ALTER TABLE `character_story_monsters_killed`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_story_node_status`
--
ALTER TABLE `character_story_node_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_story_progress`
--
ALTER TABLE `character_story_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dialogues`
--
ALTER TABLE `dialogues`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dialogue_trees`
--
ALTER TABLE `dialogue_trees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loot_tables`
--
ALTER TABLE `loot_tables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maps`
--
ALTER TABLE `maps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `map_points`
--
ALTER TABLE `map_points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monsters`
--
ALTER TABLE `monsters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `npcs`
--
ALTER TABLE `npcs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `player_quests`
--
ALTER TABLE `player_quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `player_quest_progress`
--
ALTER TABLE `player_quest_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procedural_dungeon_templates`
--
ALTER TABLE `procedural_dungeon_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procedural_loot_pools`
--
ALTER TABLE `procedural_loot_pools`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procedural_monster_pools`
--
ALTER TABLE `procedural_monster_pools`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procedural_room_images`
--
ALTER TABLE `procedural_room_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quest_objectives`
--
ALTER TABLE `quest_objectives`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quest_reward_items`
--
ALTER TABLE `quest_reward_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quest_stages`
--
ALTER TABLE `quest_stages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quest_stage_unlocks`
--
ALTER TABLE `quest_stage_unlocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_instances`
--
ALTER TABLE `story_instances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_nodes`
--
ALTER TABLE `story_nodes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_node_connections`
--
ALTER TABLE `story_node_connections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_node_loots`
--
ALTER TABLE `story_node_loots`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_node_monsters`
--
ALTER TABLE `story_node_monsters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_node_npcs`
--
ALTER TABLE `story_node_npcs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_social_accounts`
--
ALTER TABLE `user_social_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `characters_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `character_buffs`
--
ALTER TABLE `character_buffs`
  ADD CONSTRAINT `character_buffs_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_inventory`
--
ALTER TABLE `character_inventory`
  ADD CONSTRAINT `character_inventory_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_map_unlocks`
--
ALTER TABLE `character_map_unlocks`
  ADD CONSTRAINT `character_map_unlocks_ibfk_2` FOREIGN KEY (`map_point_id`) REFERENCES `map_points` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_map_unlocks_character` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_saves`
--
ALTER TABLE `character_saves`
  ADD CONSTRAINT `character_saves_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_skills`
--
ALTER TABLE `character_skills`
  ADD CONSTRAINT `character_skills_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_stats`
--
ALTER TABLE `character_stats`
  ADD CONSTRAINT `character_stats_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_story_loots_collected`
--
ALTER TABLE `character_story_loots_collected`
  ADD CONSTRAINT `character_story_loots_collected_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_story_loots_collected_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_story_loots_collected_ibfk_3` FOREIGN KEY (`loot_id`) REFERENCES `story_node_loots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_story_node_status`
--
ALTER TABLE `character_story_node_status`
  ADD CONSTRAINT `character_story_node_status_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_story_node_status_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `character_story_progress`
--
ALTER TABLE `character_story_progress`
  ADD CONSTRAINT `character_story_progress_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_story_progress_ibfk_2` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `character_story_progress_ibfk_3` FOREIGN KEY (`current_node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dialogues`
--
ALTER TABLE `dialogues`
  ADD CONSTRAINT `dialogues_ibfk_1` FOREIGN KEY (`tree_id`) REFERENCES `dialogue_trees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dialogues_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `dialogues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maps`
--
ALTER TABLE `maps`
  ADD CONSTRAINT `maps_ibfk_1` FOREIGN KEY (`parent_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `map_points`
--
ALTER TABLE `map_points`
  ADD CONSTRAINT `map_points_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `map_points_ibfk_2` FOREIGN KEY (`sub_map_id`) REFERENCES `maps` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `map_points_ibfk_3` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `npc_dialogue_trees`
--
ALTER TABLE `npc_dialogue_trees`
  ADD CONSTRAINT `npc_dialogue_trees_ibfk_1` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npc_dialogue_trees_ibfk_2` FOREIGN KEY (`tree_id`) REFERENCES `dialogue_trees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `npc_merchant_inventory`
--
ALTER TABLE `npc_merchant_inventory`
  ADD CONSTRAINT `npc_merchant_inventory_ibfk_1` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npc_merchant_inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `npc_quests`
--
ALTER TABLE `npc_quests`
  ADD CONSTRAINT `npc_quests_ibfk_1` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npc_quests_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `player_quests`
--
ALTER TABLE `player_quests`
  ADD CONSTRAINT `fk_player_quests_character` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `player_quests_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `player_quests_ibfk_3` FOREIGN KEY (`current_stage_id`) REFERENCES `quest_stages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `player_quest_progress`
--
ALTER TABLE `player_quest_progress`
  ADD CONSTRAINT `player_quest_progress_ibfk_1` FOREIGN KEY (`player_quest_id`) REFERENCES `player_quests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `player_quest_progress_ibfk_2` FOREIGN KEY (`objective_id`) REFERENCES `quest_objectives` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `procedural_loot_pools`
--
ALTER TABLE `procedural_loot_pools`
  ADD CONSTRAINT `procedural_loot_pools_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `procedural_dungeon_templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `procedural_loot_pools_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `procedural_monster_pools`
--
ALTER TABLE `procedural_monster_pools`
  ADD CONSTRAINT `procedural_monster_pools_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `procedural_dungeon_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `procedural_room_images`
--
ALTER TABLE `procedural_room_images`
  ADD CONSTRAINT `procedural_room_images_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `procedural_dungeon_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quest_objectives`
--
ALTER TABLE `quest_objectives`
  ADD CONSTRAINT `quest_objectives_ibfk_1` FOREIGN KEY (`stage_id`) REFERENCES `quest_stages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quest_objectives_ibfk_2` FOREIGN KEY (`dialogue_tree_id`) REFERENCES `dialogue_trees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quest_prerequisites`
--
ALTER TABLE `quest_prerequisites`
  ADD CONSTRAINT `quest_prerequisites_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quest_prerequisites_ibfk_2` FOREIGN KEY (`required_quest_id`) REFERENCES `quests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quest_reward_items`
--
ALTER TABLE `quest_reward_items`
  ADD CONSTRAINT `quest_reward_items_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quest_reward_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Indexes for table `daily_quests`
--
ALTER TABLE `daily_quests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `player_daily_quests`
--
ALTER TABLE `player_daily_quests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_quest_per_day` (`character_id`, `daily_quest_id`, `assigned_date`),
  ADD KEY `idx_player_daily_date` (`character_id`, `assigned_date`),
  ADD KEY `idx_daily_quest_id` (`daily_quest_id`);

--
-- AUTO_INCREMENT for table `daily_quests`
--
ALTER TABLE `daily_quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `player_daily_quests`
--
ALTER TABLE `player_daily_quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for table `player_daily_quests`
--
ALTER TABLE `player_daily_quests`
  ADD CONSTRAINT `player_daily_quests_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `player_daily_quests_ibfk_2` FOREIGN KEY (`daily_quest_id`) REFERENCES `daily_quests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quest_stages`
--
ALTER TABLE `quest_stages`
  ADD CONSTRAINT `quest_stages_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quest_stage_unlocks`
--
ALTER TABLE `quest_stage_unlocks`
  ADD CONSTRAINT `quest_stage_unlocks_ibfk_1` FOREIGN KEY (`quest_stage_id`) REFERENCES `quest_stages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quest_stage_unlocks_ibfk_2` FOREIGN KEY (`map_point_id`) REFERENCES `map_points` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skills_ibfk_2` FOREIGN KEY (`parent_skill_id`) REFERENCES `skills` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `story_instances`
--
ALTER TABLE `story_instances`
  ADD CONSTRAINT `story_instances_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `story_instances_ibfk_2` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `story_nodes`
--
ALTER TABLE `story_nodes`
  ADD CONSTRAINT `story_nodes_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `story_node_connections`
--
ALTER TABLE `story_node_connections`
  ADD CONSTRAINT `story_node_connections_ibfk_1` FOREIGN KEY (`from_node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `story_node_connections_ibfk_2` FOREIGN KEY (`to_node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `story_node_loots`
--
ALTER TABLE `story_node_loots`
  ADD CONSTRAINT `story_node_loots_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `story_node_loots_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `story_node_monsters`
--
ALTER TABLE `story_node_monsters`
  ADD CONSTRAINT `story_node_monsters_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `story_node_npcs`
--
ALTER TABLE `story_node_npcs`
  ADD CONSTRAINT `story_node_npcs_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `story_nodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `story_node_npcs_ibfk_2` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_social_accounts`
--
ALTER TABLE `user_social_accounts`
  ADD CONSTRAINT `user_social_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;











-- --------------------------------------------------------
-- Default Data - Daily Quests
-- --------------------------------------------------------
INSERT INTO `daily_quests` (`id`, `name`, `description`, `objective_type`, `objective_target`, `objective_count`, `gold_reward`, `is_active`) VALUES
(1, 'Chasseur de Gobelins', 'Éliminez 3 monstres pour prouver votre valeur.', 'KILL_MONSTERS', NULL, 3, 5, 1),
(2, 'Explorateur Novice', 'Visitez 2 lieux différents sur la carte.', 'VISIT_LOCATIONS', NULL, 2, 5, 1),
(3, 'Aventurier du Donjon', 'Complétez un donjon en entier.', 'COMPLETE_DUNGEON', NULL, 1, 5, 1),
(4, 'Collecteur d''Or', 'Amassez 50 pièces d''or.', 'COLLECT_GOLD', NULL, 50, 5, 1),
(5, 'Maître des Potions', 'Utilisez 2 objets consommables.', 'USE_ITEMS', NULL, 2, 5, 1),
(6, 'Tueur de Bêtes', 'Éliminez 5 monstres de n''importe quel type.', 'KILL_MONSTERS', NULL, 5, 5, 1),
(7, 'Cartographe', 'Visitez 3 lieux différents.', 'VISIT_LOCATIONS', NULL, 3, 5, 1),
(8, 'Pilleur de Donjons', 'Terminez 2 donjons.', 'COMPLETE_DUNGEON', NULL, 2, 5, 1),
(9, 'Économe', 'Collectez 100 pièces d''or.', 'COLLECT_GOLD', NULL, 100, 5, 1),
(10, 'Exterminateur', 'Tuez 10 monstres.', 'KILL_MONSTERS', NULL, 10, 5, 1),
(11, 'Voyageur Infatigable', 'Visitez 5 lieux différents.', 'VISIT_LOCATIONS', NULL, 5, 5, 1),
(12, 'Alchimiste Amateur', 'Utilisez 3 objets consommables.', 'USE_ITEMS', NULL, 3, 5, 1),
(13, 'Chasseur Aguerri', 'Éliminez 7 créatures.', 'KILL_MONSTERS', NULL, 7, 5, 1),
(14, 'Conquérant', 'Complétez 3 donjons.', 'COMPLETE_DUNGEON', NULL, 3, 5, 1),
(15, 'Trésorier', 'Amassez 200 pièces d''or.', 'COLLECT_GOLD', NULL, 200, 5, 1);

-- --------------------------------------------------------
-- Default Data - Furniture Categories
-- --------------------------------------------------------
INSERT INTO `furniture_categories` (`name`, `icon`, `sort_order`) VALUES
('Rangement', '📦', 1),
('Décoration', '🖼️', 2),
('Confort', '🛋️', 3),
('Utilitaire', '⚒️', 4),
('Luxe', '👑', 5);

-- --------------------------------------------------------
-- Default Data - Houses
-- --------------------------------------------------------
INSERT INTO `houses` (`name`, `description`, `price`, `storage_slots`, `furniture_slots`, `image`, `location_name`, `map_x`, `map_y`, `required_level`, `workbench_price`, `workbench_required_level`) VALUES
('Petite Cabane', 'Une modeste cabane en bois, parfaite pour débuter. Simple mais fonctionnelle.', 500, 15, 3, 'assets/images/houses/cabin.png', 'Île du Minotaure', 54, -108, 1, 2000, 5),
('Maison de Village', 'Une confortable maison au cœur du village. Espace de vie agréable.', 2000, 25, 6, 'assets/images/houses/village_house.png', 'Village de Lunargent', 78, -93, 5, 3500, 8),
('Demeure du Marchand', 'Une belle demeure avec cave et grenier. Idéale pour les aventuriers prospères.', 5000, 40, 10, 'assets/images/houses/merchant_house.png', 'Cité de Valdris', 84, -77, 10, 5000, 10),
('Manoir Noble', 'Un manoir prestigieux avec de nombreuses pièces et un jardin privé.', 15000, 60, 15, 'assets/images/houses/manor.png', 'Quartier Noble', 99, -91, 20, 8000, 15),
('Château Ancestral', 'Un château majestueux digne des plus grands héros du royaume.', 50000, 100, 25, 'assets/images/houses/castle.png', 'Hautes Terres', 74, -94, 30, 12000, 20);

-- --------------------------------------------------------
-- Default Data - Furniture
-- --------------------------------------------------------
INSERT INTO `furniture` (`category_id`, `name`, `description`, `price`, `icon`, `bonus_type`, `bonus_value`, `rarity`, `required_level`) VALUES
-- Storage
(1, 'Coffre en Bois', 'Un simple coffre pour stocker vos affaires.', 100, '📦', 'storage', 5, 'common', 1),
(1, 'Armoire Rustique', 'Une armoire solide avec plusieurs étagères.', 300, '🗄️', 'storage', 10, 'uncommon', 3),
(1, 'Coffre Renforcé', 'Un coffre avec des renforts métalliques.', 800, '🗃️', 'storage', 15, 'rare', 8),
(1, 'Coffre-Fort Magique', 'Un coffre enchanté offrant une protection maximale.', 2500, '✨', 'storage', 25, 'epic', 15),
-- Decoration
(2, 'Tableau Simple', 'Un joli tableau pour décorer vos murs.', 50, '🖼️', 'comfort', 1, 'common', 1),
(2, 'Tapisserie Royale', 'Une magnifique tapisserie tissée à la main.', 400, '🎭', 'comfort', 3, 'uncommon', 5),
(2, 'Statue de Héros', 'Une statue représentant un héros légendaire.', 1500, '🗿', 'xp', 5, 'rare', 12),
(2, 'Trophée de Dragon', 'La tête empaillée d''un dragon vaincu.', 5000, '🐉', 'luck', 10, 'epic', 20),
-- Comfort
(3, 'Lit Simple', 'Un lit basique mais confortable.', 150, '🛏️', 'comfort', 2, 'common', 1),
(3, 'Fauteuil Moelleux', 'Un fauteuil parfait pour se reposer.', 250, '🪑', 'comfort', 3, 'common', 2),
(3, 'Lit Royal', 'Un lit somptueux digne d''un roi.', 2000, '👑', 'comfort', 10, 'epic', 15),
(3, 'Cheminée Magique', 'Une cheminée qui ne s''éteint jamais.', 1200, '🔥', 'comfort', 8, 'rare', 10),
-- Utility
(4, 'Établi d''Artisan', 'Un établi pour réparer vos équipements.', 500, '🔧', 'none', 0, 'uncommon', 5),
(4, 'Autel de Bénédiction', 'Un autel pour recevoir des bénédictions.', 3000, '⛪', 'luck', 5, 'rare', 12),
(4, 'Fontaine de Mana', 'Une fontaine qui régénère la magie.', 4000, '💧', 'xp', 10, 'epic', 18),
-- Luxurious
(5, 'Trône Doré', 'Un trône recouvert d''or pur.', 10000, '🪑', 'gold', 10, 'legendary', 25),
(5, 'Cristal des Anciens', 'Un cristal mystique aux pouvoirs incroyables.', 25000, '💎', 'xp', 20, 'legendary', 30),
(5, 'Portail Dimensionnel', 'Un portail permettant de voyager instantanément.', 50000, '🌀', 'none', 0, 'legendary', 35);

-- --------------------------------------------------------
-- Default Data - Enchantments
-- --------------------------------------------------------

INSERT INTO `enchantments` (`name`, `description`, `icon`, `stat_modifiers`, `compatible_slot_types`, `rarity`, `cost`, `required_level`) VALUES
-- Weapon Enchantments
('Tranchant', 'Augmente les dégâts de l''arme', 'assets/items/enchant_sharp.png', '{"damage": 5}', '["main_hand", "off_hand"]', 'common', 100, 1),
('Fureur', 'Augmente considérablement les dégâts', 'assets/items/enchant_fury.png', '{"damage": 15}', '["main_hand", "off_hand"]', 'uncommon', 300, 5),
('Destruction', 'Dégâts massifs', 'assets/items/enchant_destruction.png', '{"damage": 30, "critical_chance": 5}', '["main_hand", "off_hand"]', 'rare', 750, 10),
('Vampirisme', 'Vole de la vie à chaque coup', 'assets/items/enchant_vampirism.png', '{"lifesteal": 10}', '["main_hand", "off_hand"]', 'epic', 1500, 15),
('Apocalypse', 'Puissance dévastatrice', 'assets/items/enchant_apocalypse.png', '{"damage": 50, "critical_chance": 10, "critical_damage": 25}', '["main_hand"]', 'legendary', 5000, 25),

-- Armor Enchantments
('Protection', 'Augmente la défense', 'assets/items/enchant_protection.png', '{"defense": 5}', '["chest", "head", "shoulders", "legs", "boots", "gloves", "bracers"]', 'common', 100, 1),
('Fortification', 'Défense renforcée', 'assets/items/enchant_fortification.png', '{"defense": 12, "max_hp": 20}', '["chest", "head", "shoulders", "legs"]', 'uncommon', 300, 5),
('Bastion', 'Défense inébranlable', 'assets/items/enchant_bastion.png', '{"defense": 25, "max_hp": 50}', '["chest", "head"]', 'rare', 750, 10),
('Vitalité', 'Grande augmentation de vie', 'assets/items/enchant_vitality.png', '{"max_hp": 100, "hp_regen": 5}', '["chest", "amulet"]', 'epic', 1500, 15),
('Immortalité', 'Protection divine', 'assets/items/enchant_immortality.png', '{"defense": 40, "max_hp": 150, "damage_reduction": 10}', '["chest"]', 'legendary', 5000, 25),

-- Stat Enchantments
('Force', 'Augmente la force', 'assets/items/enchant_strength.png', '{"strength": 3}', '["gloves", "belt", "chest"]', 'common', 100, 1),
('Agilité', 'Augmente la dextérité', 'assets/items/enchant_agility.png', '{"dexterity": 3}', '["boots", "gloves", "legs"]', 'common', 100, 1),
('Intelligence', 'Augmente l''intelligence', 'assets/items/enchant_intelligence.png', '{"intelligence": 3}', '["head", "amulet"]', 'common', 100, 1),
('Sagesse', 'Augmente la sagesse', 'assets/items/enchant_wisdom.png', '{"wisdom": 3}', '["head", "amulet"]', 'common', 100, 1),
('Constitution', 'Augmente l''endurance', 'assets/items/enchant_constitution.png', '{"constitution": 3}', '["chest", "belt", "legs"]', 'common', 100, 1),

-- Special Enchantments
('Chance', 'Augmente la chance de loot', 'assets/items/enchant_luck.png', '{"luck": 5}', '["ring", "amulet"]', 'uncommon', 400, 5),
('Fortune', 'Grande chance de loot', 'assets/items/enchant_fortune.png', '{"luck": 15, "gold_bonus": 10}', '["ring", "amulet"]', 'rare', 1000, 10),
('Célérité', 'Augmente la vitesse', 'assets/items/enchant_speed.png', '{"speed": 10}', '["boots", "legs"]', 'uncommon', 350, 5),
('Évasion', 'Chance d''esquive', 'assets/items/enchant_evasion.png', '{"dodge_chance": 5}', '["boots", "chest", "legs"]', 'rare', 800, 10),
('Précision', 'Augmente le taux de critique', 'assets/items/enchant_precision.png', '{"critical_chance": 5}', '["gloves", "ring", "amulet"]', 'uncommon', 400, 5);


COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
