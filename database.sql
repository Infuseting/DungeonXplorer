-- Database Export: dungeon_xplorer
-- Generated: 2025-11-26 07:08:46

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- --------------------------------------------------------
-- Table structure for table `character_appearance`
-- --------------------------------------------------------

CREATE TABLE `character_appearance` (
  `character_id` int NOT NULL,
  `skin_color` varchar(20) DEFAULT '#ffdbac',
  `hair_style` varchar(50) DEFAULT 'bald',
  `hair_color` varchar(20) DEFAULT '#000000',
  `eye_color` varchar(20) DEFAULT '#000000',
  `face_style` varchar(50) DEFAULT 'default',
  PRIMARY KEY (`character_id`),
  CONSTRAINT `character_appearance_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `character_inventory`
-- --------------------------------------------------------

CREATE TABLE `character_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_id` int NOT NULL,
  `item_id` int NOT NULL,
  `location` enum('equipped','backpack','pockets') NOT NULL,
  `slot_name` enum('head','shoulders','amulet','chest','belt','legs','boots','ring_1','ring_2','main_hand','off_hand','gloves','bracers','backpack') DEFAULT NULL,
  `grid_x` tinyint DEFAULT NULL,
  `grid_y` tinyint DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `instance_stats` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_id` (`character_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `character_inventory_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `character_inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `character_stats`
-- --------------------------------------------------------

CREATE TABLE `character_stats` (
  `character_id` int NOT NULL,
  `level` int DEFAULT '1',
  `xp` int DEFAULT '0',
  `strength` int DEFAULT '10',
  `dexterity` int DEFAULT '10',
  `intelligence` int DEFAULT '10',
  `vitality` int DEFAULT '10',
  PRIMARY KEY (`character_id`),
  CONSTRAINT `character_stats_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `characters`
-- --------------------------------------------------------

CREATE TABLE `characters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_played_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `gold` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `characters_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `classes`
-- --------------------------------------------------------

CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `base_stats_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `dialogue_trees`
-- --------------------------------------------------------

CREATE TABLE `dialogue_trees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Nom de l''arbre (ex: "Salutation marchand")',
  `description` text COMMENT 'Description de l''arbre de dialogue',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `dialogues`
-- --------------------------------------------------------

CREATE TABLE `dialogues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tree_id` int NOT NULL COMMENT 'Arbre de dialogue auquel appartient ce n┼ôud',
  `parent_id` int DEFAULT NULL COMMENT 'ID du dialogue parent (NULL = racine de l''arbre)',
  `text` text NOT NULL COMMENT 'Texte dit par le PNJ ou texte de la r├®ponse',
  `is_player_choice` tinyint(1) DEFAULT '0' COMMENT 'TRUE si c''est un choix du joueur, FALSE si c''est le PNJ qui parle',
  `choice_text` varchar(255) DEFAULT NULL COMMENT 'Texte du bouton de choix (si is_player_choice=TRUE)',
  `order_index` int DEFAULT '0' COMMENT 'Ordre d''affichage parmi les fr├¿res',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dialogues_tree` (`tree_id`),
  KEY `idx_dialogues_parent` (`parent_id`),
  CONSTRAINT `dialogues_ibfk_1` FOREIGN KEY (`tree_id`) REFERENCES `dialogue_trees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dialogues_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `dialogues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `items`
-- --------------------------------------------------------

CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `is_purchasable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `stat_ranges` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `loot_tables`
-- --------------------------------------------------------

CREATE TABLE `loot_tables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_type` enum('mob','dungeon') NOT NULL,
  `source_id` int NOT NULL,
  `item_id` int NOT NULL,
  `chance` int DEFAULT '10',
  `min_quantity` int DEFAULT '1',
  `max_quantity` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `map_points`
-- --------------------------------------------------------

CREATE TABLE `map_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `map_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `x` int NOT NULL,
  `y` int NOT NULL,
  `type` enum('story','place','dungeon','npc','quest') NOT NULL,
  `target_id` int DEFAULT NULL,
  `sub_map_id` int DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT '0',
  `unlock_quest_id` int DEFAULT NULL,
  `unlock_condition_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `radius` int DEFAULT '20',
  `label` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  KEY `sub_map_id` (`sub_map_id`),
  CONSTRAINT `map_points_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `map_points_ibfk_2` FOREIGN KEY (`sub_map_id`) REFERENCES `maps` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `maps`
-- --------------------------------------------------------

CREATE TABLE `maps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `parent_map_id` int DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `parent_location_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_location_id` (`parent_location_id`),
  CONSTRAINT `maps_ibfk_1` FOREIGN KEY (`parent_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `monsters`
-- --------------------------------------------------------

CREATE TABLE `monsters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `level_min` int DEFAULT '1',
  `level_max` int DEFAULT '100',
  `base_stats_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `npc_dialogue_trees`
-- --------------------------------------------------------

CREATE TABLE `npc_dialogue_trees` (
  `npc_id` int NOT NULL,
  `tree_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`npc_id`,`tree_id`),
  KEY `tree_id` (`tree_id`),
  CONSTRAINT `npc_dialogue_trees_ibfk_1` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `npc_dialogue_trees_ibfk_2` FOREIGN KEY (`tree_id`) REFERENCES `dialogue_trees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `npc_merchant_inventory`
-- --------------------------------------------------------

CREATE TABLE `npc_merchant_inventory` (
  `npc_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int DEFAULT '1' COMMENT 'Quantit├® en stock (pour futurs d├®veloppements)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`npc_id`,`item_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `npc_merchant_inventory_ibfk_1` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `npc_merchant_inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `npcs`
-- --------------------------------------------------------

CREATE TABLE `npcs` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `texture` varchar(255) DEFAULT NULL COMMENT 'Chemin vers l''image du PNJ',
  `merchant_seed` int DEFAULT NULL COMMENT 'SEED pour g├®n├®ration inventaire marchand (NULL si non marchand)',
  `buy_rate_own` decimal(5,2) DEFAULT '0.05' COMMENT 'Taux rachat items vendus par le marchand (5%)',
  `buy_rate_other` decimal(5,2) DEFAULT '0.15' COMMENT 'Taux rachat autres items (15%)',
  PRIMARY KEY (`id`),
  KEY `idx_npcs_merchant_seed` (`merchant_seed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `user_tokens`
-- --------------------------------------------------------

CREATE TABLE `user_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_validator` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `password_resets`
-- --------------------------------------------------------

CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
