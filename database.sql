-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Dec 05, 2025 at 03:01 PM
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

--
-- Dumping data for table `characters`
--

INSERT INTO `characters` (`id`, `user_id`, `class_id`, `name`, `appearance`, `created_at`, `last_played_at`, `gold`) VALUES
(43, 4, 2, 'Langlois', '{\"hair\":{\"redCyan\":140,\"greenMagenta\":80,\"blueYellow\":120},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-02 10:30:59', '2025-12-02 10:30:59', 0.00),
(44, 1, 3, 'Rachel', '{\"hair\":{\"redCyan\":200,\"greenMagenta\":0,\"blueYellow\":0},\"eyes\":{\"color\":\"blue\"},\"makeup\":{\"cicatrice_nez\":true,\"tatouage_coeur\":true}}', '2025-12-02 10:40:57', '2025-12-02 10:40:57', 0.00),
(45, 4, 4, 'Adolf', '{\"hair\":{\"redCyan\":200,\"greenMagenta\":200,\"blueYellow\":200},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-02 11:56:07', '2025-12-02 11:56:07', 0.00),
(46, 1, 4, 'GROK', '{\"hair\":{\"redCyan\":200,\"greenMagenta\":0,\"blueYellow\":0},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-02 12:11:45', '2025-12-02 12:11:45', 0.00),
(47, 16, 3, 'Lenny', '{\"hair\":{\"redCyan\":100,\"greenMagenta\":100,\"blueYellow\":100},\"eyes\":{\"color\":\"red\"},\"makeup\":{\"cicatrice_nez\":true,\"tatouage_coeur\":true}}', '2025-12-02 12:30:01', '2025-12-02 12:30:01', 0.00),
(49, 4, 3, 'Khalamite', '{\"hair\":{\"redCyan\":39,\"greenMagenta\":149,\"blueYellow\":200},\"eyes\":{\"color\":\"blue\"},\"makeup\":{\"tatouage_coeur\":true}}', '2025-12-02 22:54:13', '2025-12-02 22:54:13', 0.00),
(50, 7, 6, 'Rémy', '{\"hair\":{\"redCyan\":18,\"greenMagenta\":100,\"blueYellow\":100},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-03 09:30:19', '2025-12-03 09:30:19', 0.00),
(51, 7, 1, 'remynder', '{\"hair\":{\"redCyan\":183,\"greenMagenta\":107,\"blueYellow\":200},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-03 09:30:49', '2025-12-03 09:30:49', 0.00),
(52, 3, 4, 'tralalero tralalala', '{\"hair\":{\"redCyan\":49,\"greenMagenta\":107,\"blueYellow\":197},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-04 10:52:21', '2025-12-04 10:52:21', 0.00),
(53, 18, 8, 'Nécrose', '{\"hair\":{\"redCyan\":0,\"greenMagenta\":200,\"blueYellow\":200},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-04 21:27:50', '2025-12-04 21:27:50', 0.00),
(54, 19, 1, 'BAYHAR', '{\"hair\":{\"redCyan\":100,\"greenMagenta\":100,\"blueYellow\":100},\"eyes\":{\"color\":\"brown\"},\"makeup\":[]}', '2025-12-04 21:28:42', '2025-12-04 21:28:42', 0.00);

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

--
-- Dumping data for table `character_map_unlocks`
--

INSERT INTO `character_map_unlocks` (`character_id`, `map_point_id`, `unlocked_at`) VALUES
(43, 3, '2025-12-02 10:53:29'),
(44, 3, '2025-12-02 09:44:17'),
(46, 3, '2025-12-03 08:36:14'),
(47, 3, '2025-12-02 11:31:29'),
(49, 3, '2025-12-03 08:36:14'),
(52, 3, '2025-12-04 09:55:27'),
(53, 3, '2025-12-04 20:33:03'),
(54, 3, '2025-12-04 20:30:53');

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
  `vitality` int DEFAULT '10'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `character_stats`
--

INSERT INTO `character_stats` (`character_id`, `level`, `xp`, `strength`, `dexterity`, `intelligence`, `vitality`) VALUES
(43, 1, 0, 5, 10, 15, 10),
(44, 1, 0, 15, 10, 5, 15),
(45, 1, 0, 20, 5, 5, 20),
(46, 1, 0, 20, 5, 5, 20),
(47, 1, 0, 15, 10, 5, 15),
(49, 1, 0, 30, 10, 5, 15),
(50, 1, 0, 25, 20, 15, 10),
(51, 1, 0, 15, 5, 20, 15),
(52, 1, 0, 20, 5, 5, 20),
(53, 1, 0, 15, 15, 20, 10),
(54, 1, 0, 15, 5, 20, 15);

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

--
-- Dumping data for table `character_story_node_status`
--

INSERT INTO `character_story_node_status` (`id`, `character_id`, `node_id`, `is_visited`, `monsters_cleared`, `updated_at`) VALUES
(1, 46, 1, 1, 0, '2025-12-04 19:42:32'),
(2, 46, 2, 1, 0, '2025-12-04 20:50:05'),
(3, 46, 3, 1, 0, '2025-12-04 21:04:35'),
(5, 46, 17, 1, 0, '2025-12-05 12:27:32'),
(6, 46, 18, 1, 0, '2025-12-05 12:27:34'),
(7, 46, 19, 1, 0, '2025-12-05 12:27:38'),
(8, 46, 20, 1, 0, '2025-12-05 12:27:41'),
(9, 49, 1, 1, 0, '2025-12-05 13:58:16'),
(10, 49, 3, 1, 0, '2025-12-05 13:58:23'),
(11, 49, 6, 1, 0, '2025-12-05 13:58:37'),
(12, 49, 11, 1, 0, '2025-12-05 13:58:45');

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

--
-- Dumping data for table `character_story_progress`
--

INSERT INTO `character_story_progress` (`id`, `character_id`, `story_id`, `current_node_id`, `started_at`, `last_updated`, `completed`, `completed_at`, `in_dungeon`) VALUES
(2, 46, 1, 20, '2025-12-05 11:38:43', '2025-12-05 12:27:41', 0, NULL, 1),
(3, 49, 1, 11, '2025-12-05 13:58:16', '2025-12-05 13:58:45', 0, NULL, 1);

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

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `description`, `base_stats_json`, `created_at`) VALUES
(1, 'Paladins', 'Combattant maitrisant la magie, il lance de puissants sorts augmentant sa force.', '{\"strength\": 15, \"vitality\": 15, \"dexterity\": 5, \"intelligence\": 20}', '2025-11-30 18:51:55'),
(2, 'Mage', 'Un maître des arcanes capable de déchaîner des sorts dévastateurs.', '{\"strength\": 5, \"vitality\": 10, \"dexterity\": 10, \"intelligence\": 15}', '2025-11-22 13:21:02'),
(3, 'Guerrière', 'Une combattante robuste et polyvalente, idéal pour débuter.', '{\"strength\": 15, \"vitality\": 15, \"dexterity\": 10, \"intelligence\": 5}', '2025-11-22 13:21:02'),
(4, 'Orc', 'Un Combattant puissant, mais lent qui est capable d\'encaisser de lourds dégâts tout en assenant des attaques puissantes.', '{\"strength\": 20, \"vitality\": 20, \"dexterity\": 5, \"intelligence\": 5}', '2025-11-28 15:38:37'),
(5, 'Voleuse', 'Combattante redoutable, assenant des attaques sans relâche.', '{\"strength\": 20, \"vitality\": 10, \"dexterity\": 20, \"intelligence\": 10}', '2025-12-02 10:36:52'),
(6, 'Archère', 'Combattante agile, capable de décocher une flèche sur sa cible avec une précision déconcertante.', '{\"strength\": 25, \"vitality\": 10, \"dexterity\": 20, \"intelligence\": 15}', '2025-11-30 18:23:52'),
(7, 'Berseker', 'Combattant incontrôlable, il n\'a pas peur de la mort et se bâtera jusqu\'à son dernier souffle.', '{\"strength\": 20, \"vitality\": 20, \"dexterity\": 5, \"intelligence\": 5}', '2025-11-30 18:27:39'),
(8, 'Nécromancienne', 'Mage contrôlant l\'art de ramener les morts à la vie, son armée de revenant est sans pitié.', '{\"strength\": 15, \"vitality\": 10, \"dexterity\": 15, \"intelligence\": 20}', '2025-12-02 10:39:22');

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

--
-- Dumping data for table `dialogues`
--

INSERT INTO `dialogues` (`id`, `tree_id`, `parent_id`, `text`, `is_player_choice`, `choice_text`, `order_index`, `created_at`) VALUES
(5, 2, NULL, 'Vous avez besoin d\'informations ?', 0, NULL, 0, '2025-11-26 09:22:12'),
(6, 2, 5, 'blabla', 1, 'Quel est cet histoire de Minotaure ?', 0, '2025-11-26 09:22:31'),
(7, 2, 5, 'Où on le trouve ?', 1, 'Choix 2', 0, '2025-11-26 09:22:44'),
(8, 2, 6, 'Lorem Ipsum Dolor 1', 0, NULL, 0, '2025-11-26 12:34:43'),
(9, 2, 7, 'Lorem Ipsum Dolor 2', 0, NULL, 0, '2025-11-26 12:34:51'),
(10, 3, NULL, 'Vous avez besoin de quelque chose ?', 0, NULL, 0, '2025-11-26 21:50:13'),
(11, 3, 10, 'Effectivement, j\'ai été missionnée par le Roi pour trouver sa fille qui a disparu.', 1, 'Effectivement, j\'ai été missionnée par le Roi pour trouver sa fille qui a disparu.', 0, '2025-11-26 21:51:33'),
(12, 3, 11, 'Ah bon ? Elle a disparu, elle est venue me voir il y a quelques jours. C\'êtait la première fois que la princesse sortez du chateau pour venir voir un gueux seul et sans garde elle avait probablement quelque chose a caché ?', 0, NULL, 0, '2025-11-26 21:52:19'),
(13, 3, 12, 'Oui probablement, vous auriez une idée d\'où elle aurait pu aller ?', 1, 'Oui probablement, vous auriez une idée d\'où elle aurait pu aller ?', 0, '2025-11-26 21:54:08'),
(14, 3, 12, 'Ah bon ? Elle est venue vous voir ? Pour faire quoi ?', 1, 'Ah bon ? Elle est venue vous voir ? Pour faire quoi ?', 0, '2025-11-26 21:55:12'),
(15, 3, 13, 'Aucune idée, elle s\'est dirigée vers la sortie. Il est peut probable que les gardes l\'ai remarqué sinon il l\'aurait arreté. Vous devriez vous dirigez a la sortie Ouest. Dans les champs vous trouverez peut-être un agriculteur qui l\'a vu.', 0, NULL, 0, '2025-11-26 22:04:43'),
(16, 3, 14, 'Euh... Et bien elle m\'a acheté une petite dague... Et... Elle a precisé qu\'elle devait être suffisamment petite pour se cacher dans la manche de sa robe. Mais rien de plus desolé. Vous aurez peut-être plus de chance avec quelqu\'un d\'autre.', 0, NULL, 0, '2025-11-26 22:07:45'),
(17, 3, 16, 'C\'est à dire ?', 1, 'C\'est à dire ?', 0, '2025-11-26 22:08:01'),
(18, 3, 16, 'Passez une bonne journée', 1, 'Passez une bonne journée', 0, '2025-11-26 22:08:21'),
(20, 3, 17, 'Oui même si il est peut probable qu\'elle soit passés par les grilles, il est possible que vous trouviez un paysan qui en sache plus.', 0, NULL, 0, '2025-11-26 22:10:32'),
(21, 3, 10, 'Oui, je cherche la princesse. Vous auriez des informations ?', 1, 'Oui, je cherche la princesse. Vous auriez des informations ?', 0, '2025-11-26 22:14:46'),
(22, 3, 21, 'Euh. Et bien elle est probablement au chateau non ?', 0, NULL, 0, '2025-11-26 22:15:21'),
(23, 3, 22, 'Je viens justement du chateau. Et elle n\'y est pas. Elle a disparu.', 1, 'Je viens justement du chateau. Et elle n\'y est pas. Elle a disparu.', 0, '2025-11-26 22:17:25'),
(24, 3, 22, 'Et bien je viens justement du chateau et elle n\'y est pas.', 1, 'Et bien je viens justement du chateau et elle n\'y est pas.', 0, '2025-11-26 22:20:14'),
(25, 3, 24, 'Ah, et bien elle n\'est pas ici. Baladez-vous. Vous trouverez surement quelqu\'un qui l\'a vu.', 0, NULL, 0, '2025-11-26 22:20:50'),
(26, 3, 23, 'Ah elle a disparu ? Essayez d\'aller voir les paysans dans les champs. Avec un peu de chance ils vous aiderons si ils l\'ont vu. D\'autant plus qu\'elle est relativement aimé par les gueux.', 0, NULL, 0, '2025-11-26 22:22:12');

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

--
-- Dumping data for table `dialogue_trees`
--

INSERT INTO `dialogue_trees` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(2, 'Quels est cet histoire de Minotaure ?', '', '2025-11-26 09:20:07', '2025-11-26 09:21:59'),
(3, 'Savez-vous où est la Princesse ?', 'Liée a la quête de la princesse kidnappé', '2025-11-26 21:49:53', '2025-11-26 22:26:36');

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
  `is_purchasable` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(1, 'Épée Rouillée', 'Une vieille épée qui a vu des jours meilleurs.', 'equipment', 'main_hand', 0, 1, 3, 2.00, 'assets/items/item_6926d303ab893.png', '{\"damage\": 5, \"strength\": 2}', 1, NULL, '2025-11-22 20:14:34', '{\"strength\": {\"max\": 0, \"min\": 0}, \"vitality\": {\"max\": 0, \"min\": 0}, \"dexterity\": {\"max\": 0, \"min\": 0}, \"intelligence\": {\"max\": 0, \"min\": 0}}', 1),
(2, 'Sac à Dos en Cuir', 'Un sac simple mais robuste.', 'equipment', 'backpack', 0, 2, 2, 1.00, 'leather_backpack.png', '{\"capacity_width\": 6, \"capacity_height\": 4}', 1, NULL, '2025-11-22 20:14:34', NULL, 1),
(3, 'Potion de Soin', 'Restaure 50 PV.', 'consumable', 'none', 0, 1, 1, 0.50, 'health_potion.png', '{\"heal\": 50}', 5, NULL, '2025-11-22 20:14:34', NULL, 1),
(4, 'Plastron de Fer', 'Une armure lourde pour les guerriers.', 'equipment', 'chest', 0, 2, 3, 8.00, 'iron_chestplate.png', '{\"defense\": 15, \"vitality\": 5}', 1, NULL, '2025-11-22 20:14:34', NULL, 1),
(5, 'Grande Épée', 'Une épée massive nécessitant deux mains.', 'equipment', 'main_hand', 1, 1, 4, 5.00, 'assets/items/item_6926d22b4a0c5.png', '{\"damage\": 15, \"strength\": 5}', 1, NULL, '2025-11-22 23:28:31', '{\"strength\": {\"max\": 0, \"min\": 0}, \"vitality\": {\"max\": 0, \"min\": 0}, \"dexterity\": {\"max\": 0, \"min\": 0}, \"intelligence\": {\"max\": 0, \"min\": 0}}', 1),
(7, 'amulet01_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(8, 'amulet02_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(9, 'amulet03_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(10, 'amulet04_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(11, 'amulet05_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(12, 'amulet06_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(13, 'amulet07_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet07_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(14, 'amulet08_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet08_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(15, 'amulet09_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet09_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(16, 'amulet10_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet10_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(17, 'amulet11_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet11_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(18, 'amulet12_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet12_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(19, 'amulet13_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet13_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(20, 'amulet14_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet14_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(21, 'amulet15_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet15_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(22, 'amulet16_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/amulet16_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(23, 'p43_akkhanset_amulet_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p43_akkhanset_amulet_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(24, 'p43_retroamulet_001_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p43_retroamulet_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(25, 'p66_unique_amulet_001_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p66_unique_amulet_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(26, 'p66_unique_amulet_010_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p66_unique_amulet_010_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(27, 'p69_unique_amulet_02_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p69_unique_amulet_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(28, 'p6_unique_amulet_01_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p6_unique_amulet_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(29, 'p6_unique_amulet_03_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/p6_unique_amulet_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(30, 'ph_amulet_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/ph_amulet_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(31, 'unique_amulet_002_p1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_002_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(32, 'unique_amulet_003_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(33, 'unique_amulet_004_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(34, 'unique_amulet_005_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(35, 'unique_amulet_006_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(36, 'unique_amulet_007_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(37, 'unique_amulet_008_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(38, 'unique_amulet_009_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(39, 'unique_amulet_011_104_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_011_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(40, 'unique_amulet_012_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(41, 'unique_amulet_013_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_013_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(42, 'unique_amulet_014_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_014_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(43, 'unique_amulet_016_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_016_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(44, 'unique_amulet_101_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(45, 'unique_amulet_102_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(46, 'unique_amulet_103_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(47, 'unique_amulet_104_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(48, 'unique_amulet_105_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_105_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(49, 'unique_amulet_106_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_106_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(50, 'unique_amulet_107_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(51, 'unique_amulet_108_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_108_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(52, 'unique_amulet_109_x1_210_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_109_x1_210_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(53, 'unique_amulet_set_11_x1_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/unique_amulet_set_11_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(54, 'x1_amulet_norm_unique_25_demonhunter_male', NULL, 'equipment', 'amulet', 0, 1, 1, 1.00, 'assets/items/amulet/x1_amulet_norm_unique_25_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(55, 'axe_1h_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(56, 'axe_1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(57, 'axe_1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(58, 'axe_1h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(59, 'axe_1h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(60, 'axe_1h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(61, 'axe_1h_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(62, 'axe_1h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(63, 'axe_1h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/axe_1h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(64, 'p43_retroaxe_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/p43_retroaxe_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(65, 'p4_unique_axe_1h_102_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/p4_unique_axe_1h_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(66, 'transmogaxe_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/transmogaxe_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(67, 'unique_axe_1h_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(68, 'unique_axe_1h_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(69, 'unique_axe_1h_004_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_004_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(70, 'unique_axe_1h_005_p2_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_005_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(71, 'unique_axe_1h_006_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(72, 'unique_axe_1h_007_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(73, 'unique_axe_1h_013_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_013_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(74, 'unique_axe_1h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/axe-1h/unique_axe_1h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(75, 'axe_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(76, 'axe_2h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(77, 'axe_2h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(78, 'axe_2h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(79, 'axe_2h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(80, 'axe_2h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(81, 'axe_2h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/axe_2h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(82, 'p66_unique_axe_2h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/p66_unique_axe_2h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(83, 'p66_unique_axe_2h_011_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/p66_unique_axe_2h_011_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(84, 'transmogaxe_241_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/transmogaxe_241_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(85, 'transmogaxe_241_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/transmogaxe_241_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(86, 'transmogaxe_241_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/transmogaxe_241_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(87, 'unique_axe_2h_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/unique_axe_2h_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(88, 'unique_axe_2h_010_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/unique_axe_2h_010_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(89, 'unique_axe_2h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/axe-2h/unique_axe_2h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(90, 'belt_001_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(91, 'belt_002_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(92, 'belt_003_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(93, 'belt_004_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(94, 'belt_005_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(95, 'belt_006_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(96, 'belt_101_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(97, 'belt_102_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(98, 'belt_103_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(99, 'belt_104_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(100, 'belt_105_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(101, 'belt_201_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(102, 'belt_202_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(103, 'belt_203_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(104, 'belt_204_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(105, 'belt_205_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(106, 'belt_206_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(107, 'belt_207_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(108, 'belt_208_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/belt_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(109, 'p2_unique_belt_01_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p2_unique_belt_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(110, 'p2_unique_belt_02_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p2_unique_belt_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(111, 'p2_unique_belt_03_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p2_unique_belt_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(112, 'p2_unique_belt_04_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p2_unique_belt_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(113, 'p2_unique_belt_05_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p2_unique_belt_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(114, 'p2_unique_belt_06_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p2_unique_belt_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(115, 'p3_unique_belt_01_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p3_unique_belt_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(116, 'p42_crusader_foh_belt_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p42_crusader_foh_belt_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(117, 'p43_unique_belt_001_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p43_unique_belt_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(118, 'p43_unique_belt_005_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p43_unique_belt_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(119, 'p4_unique_belt_01_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(120, 'p4_unique_belt_02_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(121, 'p4_unique_belt_03_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(122, 'p4_unique_belt_04_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(123, 'p4_unique_belt_05_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(124, 'p4_unique_belt_06_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(125, 'p4_unique_belt_07_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p4_unique_belt_07_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(126, 'p610_unique_belt_008_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p610_unique_belt_008_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(127, 'p61_unique_belt_007_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p61_unique_belt_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(128, 'p61_unique_belt_01_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p61_unique_belt_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(129, 'p61_unique_belt_03_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p61_unique_belt_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(130, 'p66_unique_belt_012_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p66_unique_belt_012_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(131, 'p66_unique_belt_016_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p66_unique_belt_016_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(132, 'p69_unique_belt_005_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p69_unique_belt_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(133, 'p72_unique_belt_007_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p72_unique_belt_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(134, 'p74_unique_belt_013_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p74_unique_belt_013_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(135, 'p76_unique_belt_002_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/p76_unique_belt_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(136, 'ph_belt_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/ph_belt_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(137, 'unique_barbbelt_003_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_barbbelt_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(138, 'unique_belt_003_p1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_003_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(139, 'unique_belt_006_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(140, 'unique_belt_007_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(141, 'unique_belt_009_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(142, 'unique_belt_010_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(143, 'unique_belt_014_1xx_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_014_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(144, 'unique_belt_015_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_015_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(145, 'unique_belt_101_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(146, 'unique_belt_102_p2_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_102_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(147, 'unique_belt_103_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(148, 'unique_belt_104_p2_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_104_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(149, 'unique_belt_105_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_105_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(150, 'unique_belt_106_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_106_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(151, 'unique_belt_107_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(152, 'unique_belt_set_02_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/belt/unique_belt_set_02_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(153, 'boots_001_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(154, 'boots_002_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(155, 'boots_003_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(156, 'boots_004_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(157, 'boots_005_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(158, 'boots_006_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(159, 'boots_101_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(160, 'boots_102_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(161, 'boots_103_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(162, 'boots_104_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(163, 'boots_105_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(164, 'boots_201_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(165, 'boots_202_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(166, 'boots_203_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(167, 'boots_204_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(168, 'boots_205_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(169, 'boots_206_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(170, 'boots_207_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(171, 'boots_208_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/boots_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(172, 'p2_unique_boots_02_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p2_unique_boots_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(173, 'p4_unique_boots_001_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p4_unique_boots_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(174, 'p61_necro_unique_boots_21_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p61_necro_unique_boots_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(175, 'p61_unique_boots_01_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p61_unique_boots_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(176, 'p66_unique_boots_015_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p66_unique_boots_015_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(177, 'p66_unique_boots_017_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p66_unique_boots_017_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(178, 'p67_unique_boots_set_01_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p67_unique_boots_set_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(179, 'p67_unique_boots_set_02_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p67_unique_boots_set_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(180, 'p68_unique_boots_set_03_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p68_unique_boots_set_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:34', NULL, 1),
(181, 'p68_unique_boots_set_04_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p68_unique_boots_set_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(182, 'p68_unique_boots_set_05_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p68_unique_boots_set_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(183, 'p69_necro_set_5_boots_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p69_necro_set_5_boots_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(184, 'p69_unique_boots_set_06_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p69_unique_boots_set_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(185, 'p6_necro_set_1_boots_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p6_necro_set_1_boots_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(186, 'p6_necro_set_2_boots_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p6_necro_set_2_boots_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(187, 'p6_necro_set_3_boots_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p6_necro_set_3_boots_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(188, 'p6_necro_set_4_boots_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p6_necro_set_4_boots_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(189, 'p6_necro_unique_boots_22_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p6_necro_unique_boots_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(190, 'p71_unique_boots_010_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/p71_unique_boots_010_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(191, 'ph_boots_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/ph_boots_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(192, 'unique_boots_005_1xx_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_005_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1);
INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(193, 'unique_boots_007_p2_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_007_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(194, 'unique_boots_008_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(195, 'unique_boots_009_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(196, 'unique_boots_011_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(197, 'unique_boots_012_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(198, 'unique_boots_013_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_013_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(199, 'unique_boots_014_1xx_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_014_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(200, 'unique_boots_018_1xx_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_018_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(201, 'unique_boots_019_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_019_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(202, 'unique_boots_102_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(203, 'unique_boots_103_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(204, 'unique_boots_104_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(205, 'unique_boots_set_01_p1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_01_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(206, 'unique_boots_set_01_p2_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_01_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(207, 'unique_boots_set_01_p3_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_01_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(208, 'unique_boots_set_02_p2_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_02_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(209, 'unique_boots_set_02_p3_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_02_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(210, 'unique_boots_set_03_p2_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_03_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(211, 'unique_boots_set_03_p3_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_03_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(212, 'unique_boots_set_05_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_05_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(213, 'unique_boots_set_06_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(214, 'unique_boots_set_07_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_07_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(215, 'unique_boots_set_08_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_08_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(216, 'unique_boots_set_09_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_09_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(217, 'unique_boots_set_10_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_10_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(218, 'unique_boots_set_12_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_12_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(219, 'unique_boots_set_13_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_13_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(220, 'unique_boots_set_14_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_14_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(221, 'unique_boots_set_15_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_15_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(222, 'unique_boots_set_16_x1_demonhunter_male', NULL, 'equipment', 'boots', 0, 1, 1, 1.00, 'assets/items/boots/unique_boots_set_16_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(223, 'bow_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(224, 'bow_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(225, 'bow_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(226, 'bow_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(227, 'bow_005_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(228, 'bow_006_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(229, 'bow_301_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(230, 'bow_302_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/bow_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(231, 'p61_unique_bow_104_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/p61_unique_bow_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(232, 'p69_unique_bow_102_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/p69_unique_bow_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(233, 'p69_unique_bow_103_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/p69_unique_bow_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(234, 'p76_unique_bow_015_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/p76_unique_bow_015_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(235, 'unique_bow_001_p1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_001_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(236, 'unique_bow_005_p1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_005_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(237, 'unique_bow_007_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_007_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(238, 'unique_bow_008_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(239, 'unique_bow_009_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(240, 'unique_bow_010_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_010_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(241, 'unique_bow_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/bow/unique_bow_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(242, 'bracers_001_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(243, 'bracers_002_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(244, 'bracers_003_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(245, 'bracers_004_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(246, 'bracers_005_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(247, 'bracers_006_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(248, 'bracers_207_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(249, 'bracers_208_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/bracers_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(250, 'p2_unique_bracer_003_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p2_unique_bracer_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(251, 'p3_unique_bracer_101_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p3_unique_bracer_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(252, 'p3_unique_bracer_106_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p3_unique_bracer_106_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(253, 'p3_unique_bracer_107_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p3_unique_bracer_107_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(254, 'p4_unique_bracer_004_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p4_unique_bracer_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(255, 'p4_unique_bracer_103_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p4_unique_bracer_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(256, 'p4_unique_bracer_105_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p4_unique_bracer_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(257, 'p4_unique_bracer_106_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p4_unique_bracer_106_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(258, 'p4_unique_bracer_110_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p4_unique_bracer_110_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(259, 'p610_unique_bracer_006_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p610_unique_bracer_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(260, 'p610_unique_bracer_22_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p610_unique_bracer_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(261, 'p61_unique_bracer_103_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p61_unique_bracer_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(262, 'p61_unique_bracer_104_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p61_unique_bracer_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(263, 'p61_unique_bracer_105_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p61_unique_bracer_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(264, 'p61_unique_bracer_107_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p61_unique_bracer_107_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(265, 'p61_unique_bracer_108_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p61_unique_bracer_108_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(266, 'p61_unique_bracer_109_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p61_unique_bracer_109_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(267, 'p66_unique_bracer_009_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p66_unique_bracer_009_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(268, 'p67_unique_bracer_100_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p67_unique_bracer_100_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(269, 'p71_unique_bracer_108_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p71_unique_bracer_108_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(270, 'p72_unique_bracer_102_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p72_unique_bracer_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(271, 'p73_unique_bracer_101_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p73_unique_bracer_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(272, 'p74_unique_bracer_010_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p74_unique_bracer_010_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(273, 'p75_unique_bracer_spiketrap_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/p75_unique_bracer_spiketrap_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(274, 'ph_bracers_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/ph_bracers_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(275, 'unique_bracer_001_1xx_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_001_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(276, 'unique_bracer_002_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(277, 'unique_bracer_005_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(278, 'unique_bracer_007_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(279, 'unique_bracer_011_1xx_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_011_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(280, 'unique_bracer_101_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(281, 'unique_bracer_102_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(282, 'unique_bracer_103_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(283, 'unique_bracer_105_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_105_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(284, 'unique_bracer_106_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_106_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(285, 'unique_bracer_107_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(286, 'unique_bracer_set_02_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_set_02_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(287, 'unique_bracer_set_12_x1_demonhunter_male', NULL, 'equipment', 'bracers', 0, 1, 1, 1.00, 'assets/items/bracers/unique_bracer_set_12_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(288, 'ceremonialdagger_1h_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/ceremonialdagger_1h_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(289, 'ceremonialdagger_1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/ceremonialdagger_1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(290, 'ceremonialdagger_1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/ceremonialdagger_1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(291, 'ceremonialdagger_1h_004a_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/ceremonialdagger_1h_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(292, 'ceremonialdagger_1h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/ceremonialdagger_1h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(293, 'ceremonialdagger_1h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/ceremonialdagger_1h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(294, 'p1_ceremonialdagger_norm_unique_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/p1_ceremonialdagger_norm_unique_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(295, 'p4_unique_ceremonialdagger_008_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/p4_unique_ceremonialdagger_008_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(296, 'p4_unique_dagger_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/p4_unique_dagger_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(297, 'p65_ceremonialdagger_norm_unique_02_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/p65_ceremonialdagger_norm_unique_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(298, 'p68_unique_dagger_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/p68_unique_dagger_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(299, 'p72_unique_ceremonialdagger_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/p72_unique_ceremonialdagger_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(300, 'unique_ceremonialdagger_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(301, 'unique_ceremonialdagger_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(302, 'unique_ceremonialdagger_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(303, 'unique_ceremonialdagger_006_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_006_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(304, 'unique_ceremonialdagger_009_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(305, 'unique_ceremonialdagger_011_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_011_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(306, 'unique_ceremonialdagger_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(307, 'unique_ceremonialdagger_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/ceremonial-knife/unique_ceremonialdagger_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(308, 'chestarmor_001_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(309, 'chestarmor_002a_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_002a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(310, 'chestarmor_003_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(311, 'chestarmor_004_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(312, 'chestarmor_005a_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_005a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(313, 'chestarmor_006_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(314, 'chestarmor_101_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(315, 'chestarmor_102_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(316, 'chestarmor_103_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(317, 'chestarmor_104_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(318, 'chestarmor_105_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(319, 'chestarmor_201_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(320, 'chestarmor_202_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(321, 'chestarmor_203_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(322, 'chestarmor_204_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(323, 'chestarmor_205_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(324, 'chestarmor_206_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(325, 'chestarmor_207_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(326, 'chestarmor_208_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/chestarmor_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(327, 'p43_retroarmor_001_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p43_retroarmor_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(328, 'p43_retroarmor_002_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p43_retroarmor_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(329, 'p4_unique_chest_012_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p4_unique_chest_012_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(330, 'p4_unique_chest_018_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p4_unique_chest_018_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(331, 'p66_unique_chest_026_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p66_unique_chest_026_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(332, 'p67_unique_chest_set_01_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p67_unique_chest_set_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(333, 'p67_unique_chest_set_02_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p67_unique_chest_set_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(334, 'p68_unique_chest_set_03_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p68_unique_chest_set_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(335, 'p68_unique_chest_set_04_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p68_unique_chest_set_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(336, 'p68_unique_chest_set_05_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p68_unique_chest_set_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(337, 'p69_necro_set_5_chest_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p69_necro_set_5_chest_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(338, 'p6_necro_set_1_chest_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p6_necro_set_1_chest_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(339, 'p6_necro_set_2_chest_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p6_necro_set_2_chest_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(340, 'p6_necro_set_3_chest_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p6_necro_set_3_chest_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(341, 'p6_necro_set_4_chest_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p6_necro_set_4_chest_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(342, 'p6_necro_unique_chest_21_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p6_necro_unique_chest_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(343, 'p6_necro_unique_chest_22_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/p6_necro_unique_chest_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(344, 'ph_chestarmor_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/ph_chestarmor_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(345, 'unique_chest_001_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(346, 'unique_chest_002_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(347, 'unique_chest_006_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(348, 'unique_chest_010_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(349, 'unique_chest_013_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_013_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(350, 'unique_chest_014_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_014_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(351, 'unique_chest_015_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_015_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(352, 'unique_chest_016_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_016_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(353, 'unique_chest_019_1xx_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_019_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(354, 'unique_chest_025_1xx_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_025_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(355, 'unique_chest_027_1xx_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_027_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(356, 'unique_chest_101_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(357, 'unique_chest_102_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(358, 'unique_chest_set_01_p1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_01_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(359, 'unique_chest_set_01_p2_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_01_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(360, 'unique_chest_set_01_p3_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_01_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(361, 'unique_chest_set_02_p2_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_02_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(362, 'unique_chest_set_02_p3_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_02_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(363, 'unique_chest_set_03_p3_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_03_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(364, 'unique_chest_set_05_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_05_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(365, 'unique_chest_set_06_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:35', NULL, 1),
(366, 'unique_chest_set_07_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_07_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(367, 'unique_chest_set_08_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_08_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(368, 'unique_chest_set_09_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_09_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(369, 'unique_chest_set_10_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_10_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(370, 'unique_chest_set_11_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_11_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(371, 'unique_chest_set_13_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_13_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(372, 'unique_chest_set_14_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_14_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(373, 'unique_chest_set_15_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_15_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(374, 'unique_chest_set_16_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chest_set_16_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1);
INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(375, 'unique_chestarmor_028_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/chest-armor/unique_chestarmor_028_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(376, 'cloak_001_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/cloak_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(377, 'cloak_002_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/cloak_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(378, 'cloak_003_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/cloak_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(379, 'cloak_004a_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/cloak_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(380, 'cloak_205_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/cloak_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(381, 'cloak_206_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/cloak_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(382, 'p69_unique_chest_set_06_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/p69_unique_chest_set_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(383, 'unique_chest_set_03_p2_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_chest_set_03_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(384, 'unique_cloak_001_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_cloak_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(385, 'unique_cloak_002_p1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_cloak_002_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(386, 'unique_cloak_005_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_cloak_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(387, 'unique_cloak_006_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_cloak_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(388, 'unique_cloak_101_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_cloak_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(389, 'unique_cloak_102_x1_demonhunter_male', NULL, 'equipment', 'chest', 0, 1, 1, 1.00, 'assets/items/cloak/unique_cloak_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(390, 'crossbow_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(391, 'crossbow_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(392, 'crossbow_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(393, 'crossbow_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(394, 'crossbow_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(395, 'crossbow_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(396, 'crossbow_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(397, 'crossbow_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(398, 'crossbow_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/crossbow_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(399, 'p61_unique_xbow_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/p61_unique_xbow_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(400, 'p65_unique_xbow_102_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/p65_unique_xbow_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(401, 'p75_unique_xbow_101_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/p75_unique_xbow_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(402, 'transmogxbow_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/transmogxbow_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(403, 'unique_xbow_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/unique_xbow_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(404, 'unique_xbow_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/unique_xbow_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(405, 'unique_xbow_004_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/unique_xbow_004_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(406, 'unique_xbow_006_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/unique_xbow_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(407, 'unique_xbow_011_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/unique_xbow_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(408, 'unique_xbow_012_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/crossbow/unique_xbow_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(409, 'crusadershield_000_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(410, 'crusadershield_002_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(411, 'crusadershield_003_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(412, 'crusadershield_004_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(413, 'crusadershield_005_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(414, 'crusadershield_006_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(415, 'crusadershield_007_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(416, 'crusadershield_207_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/crusadershield_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(417, 'p1_crushield_norm_unique_02_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/p1_crushield_norm_unique_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(418, 'p4_unique_shield_set_01_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/p4_unique_shield_set_01_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(419, 'p61_crushield_norm_unique_01_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/p61_crushield_norm_unique_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(420, 'p61_unique_shield_106_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/p61_unique_shield_106_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(421, 'p65_unique_crushield_102_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/p65_unique_crushield_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(422, 'unique_crushield_101_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(423, 'unique_crushield_103_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(424, 'unique_crushield_104_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(425, 'unique_crushield_105_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_105_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(426, 'unique_crushield_106_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_106_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(427, 'unique_crushield_107_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(428, 'unique_crushield_108_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_crushield_108_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(429, 'unique_shield_103_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/crusader-shield/unique_shield_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(430, 'dagger_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(431, 'dagger_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(432, 'dagger_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(433, 'dagger_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(434, 'dagger_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(435, 'dagger_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(436, 'dagger_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(437, 'dagger_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(438, 'dagger_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/dagger_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(439, 'p610_unique_dagger_010_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/p610_unique_dagger_010_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(440, 'p61_unique_dagger_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/p61_unique_dagger_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(441, 'p61_unique_dagger_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/p61_unique_dagger_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(442, 'unique_dagger_006_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/unique_dagger_006_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(443, 'unique_dagger_007_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/unique_dagger_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(444, 'unique_dagger_008_104_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/unique_dagger_008_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(445, 'unique_dagger_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/unique_dagger_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(446, 'unique_dagger_104_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/unique_dagger_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(447, 'unique_offhand_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/dagger/unique_offhand_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(448, 'fistweapon_1h_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/fistweapon_1h_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(449, 'fistweapon_1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/fistweapon_1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(450, 'fistweapon_1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/fistweapon_1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(451, 'fistweapon_1h_004a_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/fistweapon_1h_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(452, 'fistweapon_1h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/fistweapon_1h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(453, 'fistweapon_1h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/fistweapon_1h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(454, 'p1_fistweapon_norm_unique_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p1_fistweapon_norm_unique_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(455, 'p41_unique_fist_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p41_unique_fist_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(456, 'p41_unique_fist_008_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p41_unique_fist_008_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(457, 'p43_unique_fist_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p43_unique_fist_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(458, 'p4_unique_fist_102_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p4_unique_fist_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(459, 'p61_unique_fist_009_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p61_unique_fist_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(460, 'p61_unique_fist_013_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p61_unique_fist_013_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(461, 'p67_fistweapon_norm_unique_02_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p67_fistweapon_norm_unique_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(462, 'p67_unique_fist_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/p67_unique_fist_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(463, 'transmogfist_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/transmogfist_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(464, 'transmogfist_241_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/transmogfist_241_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(465, 'unique_fist_004_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_004_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(466, 'unique_fist_005_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(467, 'unique_fist_010_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(468, 'unique_fist_011_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(469, 'unique_fist_012_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(470, 'unique_fist_015_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_015_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(471, 'unique_fist_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/fist-weapon/unique_fist_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(472, 'flail_1h_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/flail_1h_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(473, 'flail_1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/flail_1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(474, 'flail_1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/flail_1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(475, 'flail_1h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/flail_1h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(476, 'flail_1h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/flail_1h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(477, 'p1_flail1h_norm_unique_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/p1_flail1h_norm_unique_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(478, 'p61_unique_flail_1h_105_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/p61_unique_flail_1h_105_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(479, 'p67_unique_flail_1h_106_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/p67_unique_flail_1h_106_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(480, 'transmogflail_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/transmogflail_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(481, 'transmogflail_241_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/transmogflail_241_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(482, 'transmogflail_241_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/transmogflail_241_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(483, 'unique_flail_1h_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/unique_flail_1h_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(484, 'unique_flail_1h_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/unique_flail_1h_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(485, 'unique_flail_1h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/unique_flail_1h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(486, 'unique_flail_1h_104_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/unique_flail_1h_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(487, 'unique_flail_1h_107_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/flail-1h/unique_flail_1h_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(488, 'flail_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/flail_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(489, 'flail_2h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/flail_2h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(490, 'flail_2h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/flail_2h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(491, 'flail_2h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/flail_2h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(492, 'flail_2h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/flail_2h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(493, 'p4_unique_flail_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p4_unique_flail_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(494, 'p4_unique_flail_2h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p4_unique_flail_2h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(495, 'p4_unique_flail_2h_set_01_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p4_unique_flail_2h_set_01_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(496, 'p610_unique_flail_2h_101_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p610_unique_flail_2h_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(497, 'p61_unique_flail_2h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p61_unique_flail_2h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(498, 'p61_unique_flail_2h_104_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p61_unique_flail_2h_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(499, 'p65_flail2h_norm_unique_01_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/p65_flail2h_norm_unique_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(500, 'unique_flail_2h_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/flail-2h/unique_flail_2h_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(501, 'gloves_001_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(502, 'gloves_002_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(503, 'gloves_003_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(504, 'gloves_004a_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(505, 'gloves_005_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(506, 'gloves_006_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(507, 'gloves_101_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(508, 'gloves_102_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(509, 'gloves_103_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(510, 'gloves_104_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(511, 'gloves_105_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(512, 'gloves_201_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(513, 'gloves_202_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(514, 'gloves_203_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(515, 'gloves_204_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(516, 'gloves_205_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(517, 'gloves_206_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(518, 'gloves_207_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(519, 'gloves_208_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/gloves_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(520, 'p2_unique_gloves_01_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p2_unique_gloves_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(521, 'p2_unique_gloves_02_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p2_unique_gloves_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(522, 'p2_unique_gloves_03_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p2_unique_gloves_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(523, 'p2_unique_gloves_04_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p2_unique_gloves_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(524, 'p41_unique_gloves_002_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p41_unique_gloves_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(525, 'p41_unique_gloves_014_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p41_unique_gloves_014_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(526, 'p66_unique_gloves_007_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p66_unique_gloves_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(527, 'p66_unique_gloves_015_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p66_unique_gloves_015_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(528, 'p67_unique_gloves_set_01_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p67_unique_gloves_set_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(529, 'p67_unique_gloves_set_02_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p67_unique_gloves_set_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(530, 'p68_unique_gloves_set_03_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p68_unique_gloves_set_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(531, 'p68_unique_gloves_set_04_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p68_unique_gloves_set_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(532, 'p68_unique_gloves_set_05_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p68_unique_gloves_set_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(533, 'p69_necro_set_5_gloves_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p69_necro_set_5_gloves_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(534, 'p69_necro_unique_gloves_22_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p69_necro_unique_gloves_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(535, 'p69_unique_gloves_set_06_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p69_unique_gloves_set_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(536, 'p6_necro_set_1_gloves_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p6_necro_set_1_gloves_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(537, 'p6_necro_set_2_gloves_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p6_necro_set_2_gloves_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(538, 'p6_necro_set_3_gloves_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p6_necro_set_3_gloves_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(539, 'p6_necro_set_4_gloves_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p6_necro_set_4_gloves_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(540, 'p6_necro_unique_gloves_21_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/p6_necro_unique_gloves_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(541, 'ph_gloves_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/ph_gloves_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(542, 'unique_gloves_001_1xx_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_001_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(543, 'unique_gloves_003_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(544, 'unique_gloves_008_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(545, 'unique_gloves_009_1xx_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_009_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(546, 'unique_gloves_011_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(547, 'unique_gloves_017_1xx_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_017_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(548, 'unique_gloves_101_p2_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_101_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(549, 'unique_gloves_103_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(550, 'unique_gloves_set_01_p1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_01_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(551, 'unique_gloves_set_01_p2_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_01_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(552, 'unique_gloves_set_01_p3_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_01_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(553, 'unique_gloves_set_02_p2_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_02_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(554, 'unique_gloves_set_02_p3_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_02_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(555, 'unique_gloves_set_03_p2_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_03_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(556, 'unique_gloves_set_03_p3_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_03_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(557, 'unique_gloves_set_05_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_05_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1);
INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(558, 'unique_gloves_set_06_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(559, 'unique_gloves_set_07_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_07_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(560, 'unique_gloves_set_08_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_08_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(561, 'unique_gloves_set_09_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_09_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(562, 'unique_gloves_set_10_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_10_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(563, 'unique_gloves_set_11_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_11_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:36', NULL, 1),
(564, 'unique_gloves_set_12_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_12_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(565, 'unique_gloves_set_13_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_13_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(566, 'unique_gloves_set_14_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_14_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(567, 'unique_gloves_set_15_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_15_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(568, 'unique_gloves_set_16_x1_demonhunter_male', NULL, 'equipment', 'gloves', 0, 1, 1, 1.00, 'assets/items/gloves/unique_gloves_set_16_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(569, 'handxbow_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(570, 'handxbow_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(571, 'handxbow_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(572, 'handxbow_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(573, 'handxbow_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(574, 'handxbow_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(575, 'handxbow_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(576, 'handxbow_008_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_008_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(577, 'handxbow_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(578, 'handxbow_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/handxbow_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(579, 'p43_unique_handxbow_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/p43_unique_handxbow_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(580, 'p4_unique_handxbow_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/p4_unique_handxbow_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(581, 'p4_unique_handxbow_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/p4_unique_handxbow_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(582, 'p4_unique_handxbow_02_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/p4_unique_handxbow_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(583, 'p75_handxbow_norm_unique_03_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/p75_handxbow_norm_unique_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(584, 'unique_handxbow_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(585, 'unique_handxbow_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(586, 'unique_handxbow_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(587, 'unique_handxbow_004_p1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_004_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(588, 'unique_handxbow_006_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_006_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(589, 'unique_handxbow_012_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(590, 'unique_handxbow_016_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_016_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(591, 'unique_handxbow_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(592, 'unique_handxbow_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/hand-crossbow/unique_handxbow_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(593, 'helm_002_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(594, 'helm_003_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(595, 'helm_004a_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(596, 'helm_005_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(597, 'helm_006_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(598, 'helm_101_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(599, 'helm_102_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(600, 'helm_103_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(601, 'helm_104_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(602, 'helm_105_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(603, 'helm_201_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(604, 'helm_202_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(605, 'helm_203_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(606, 'helm_204_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(607, 'helm_205_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(608, 'helm_206_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(609, 'helm_207_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(610, 'helm_208_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/helm_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(611, 'p2_unique_helm_001_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p2_unique_helm_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(612, 'p43_retrohelm_001_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p43_retrohelm_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(613, 'p43_retrohelm_002_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p43_retrohelm_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(614, 'p43_retrohelm_003_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p43_retrohelm_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(615, 'p4_unique_helm_102_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p4_unique_helm_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(616, 'p4_unique_helm_103_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p4_unique_helm_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(617, 'p61_necro_unique_helm_22_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p61_necro_unique_helm_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(618, 'p66_unique_helm_012_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p66_unique_helm_012_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(619, 'p66_unique_helm_014_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p66_unique_helm_014_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(620, 'p67_unique_helm_set_01_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p67_unique_helm_set_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(621, 'p67_unique_helm_set_02_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p67_unique_helm_set_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(622, 'p68_unique_helm_set_03_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p68_unique_helm_set_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(623, 'p68_unique_helm_set_05_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p68_unique_helm_set_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(624, 'p69_necro_set_5_helm_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p69_necro_set_5_helm_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(625, 'p69_unique_helm_set_06_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p69_unique_helm_set_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(626, 'p6_necro_set_1_helm_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p6_necro_set_1_helm_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(627, 'p6_necro_set_2_helm_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p6_necro_set_2_helm_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(628, 'p6_necro_set_3_helm_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p6_necro_set_3_helm_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(629, 'p6_necro_set_4_helm_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p6_necro_set_4_helm_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(630, 'p6_necro_unique_helm_21_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p6_necro_unique_helm_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(631, 'p74_unique_helm_006_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p74_unique_helm_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(632, 'p74_unique_helm_015_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/p74_unique_helm_015_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(633, 'ph_helm_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/ph_helm_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(634, 'transmoghelm_001_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/transmoghelm_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(635, 'transmoghelm_002_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/transmoghelm_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(636, 'unique_helm_002_p1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_002_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(637, 'unique_helm_003_p2_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_003_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(638, 'unique_helm_004_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(639, 'unique_helm_007_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(640, 'unique_helm_008_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(641, 'unique_helm_009_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(642, 'unique_helm_010_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(643, 'unique_helm_011_1xx_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_011_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(644, 'unique_helm_016_1xx_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_016_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(645, 'unique_helm_102_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(646, 'unique_helm_103_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(647, 'unique_helm_set_01_p1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_01_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(648, 'unique_helm_set_01_p2_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_01_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(649, 'unique_helm_set_01_p3_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_01_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(650, 'unique_helm_set_02_p2_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_02_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(651, 'unique_helm_set_02_p3_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_02_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(652, 'unique_helm_set_03_p2_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_03_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(653, 'unique_helm_set_03_p3_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_03_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(654, 'unique_helm_set_05_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_05_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(655, 'unique_helm_set_06_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(656, 'unique_helm_set_07_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_07_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(657, 'unique_helm_set_08_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_08_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(658, 'unique_helm_set_09_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_09_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(659, 'unique_helm_set_10_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_10_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(660, 'unique_helm_set_11_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_11_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(661, 'unique_helm_set_12_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_12_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(662, 'unique_helm_set_13_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_13_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(663, 'unique_helm_set_14_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_14_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(664, 'unique_helm_set_15_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_15_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(665, 'unique_helm_set_16_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/helm/unique_helm_set_16_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(666, 'mace_1h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(667, 'mace_1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(668, 'mace_1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(669, 'mace_1h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(670, 'mace_1h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(671, 'mace_1h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(672, 'mace_1h_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(673, 'mace_1h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(674, 'mace_1h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/mace_1h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(675, 'p43_retrotransmog_wirtsleg_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/p43_retrotransmog_wirtsleg_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(676, 'p66_unique_mace_1h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/p66_unique_mace_1h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(677, 'transmogmace_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/transmogmace_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(678, 'unique_mace_1h_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(679, 'unique_mace_1h_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(680, 'unique_mace_1h_005_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(681, 'unique_mace_1h_007_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(682, 'unique_mace_1h_008_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(683, 'unique_mace_1h_009_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_009_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(684, 'unique_mace_1h_010_104_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_010_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(685, 'unique_mace_1h_011_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(686, 'unique_mace_1h_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(687, 'unique_mace_1h_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(688, 'unique_mace_1h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mace-1h/unique_mace_1h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(689, 'mace_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(690, 'mace_2h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(691, 'mace_2h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(692, 'mace_2h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(693, 'mace_2h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(694, 'mace_2h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(695, 'mace_2h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(696, 'mace_2h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/mace_2h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(697, 'unique_mace_2h_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(698, 'unique_mace_2h_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(699, 'unique_mace_2h_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(700, 'unique_mace_2h_006_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_006_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(701, 'unique_mace_2h_009_p2_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_009_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(702, 'unique_mace_2h_010_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(703, 'unique_mace_2h_012_p1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_012_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(704, 'unique_mace_2h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(705, 'unique_mace_2h_104_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mace-2h/unique_mace_2h_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(706, 'barbbelt_001_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/barbbelt_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(707, 'barbbelt_002_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/barbbelt_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(708, 'barbbelt_003_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/barbbelt_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(709, 'barbbelt_004a_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/barbbelt_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(710, 'barbbelt_205_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/barbbelt_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(711, 'barbbelt_206_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/barbbelt_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(712, 'p2_unique_barbbelt_001_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/p2_unique_barbbelt_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(713, 'p61_unique_barbbelt_eq_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/p61_unique_barbbelt_eq_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(714, 'p67_unique_barbbelt_005_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/p67_unique_barbbelt_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(715, 'p68_unique_barbbelt_006_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/p68_unique_barbbelt_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(716, 'unique_barbbelt_002_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/unique_barbbelt_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(717, 'unique_barbbelt_009_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/unique_barbbelt_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(718, 'unique_barbbelt_101_x1_demonhunter_male', NULL, 'equipment', 'belt', 0, 1, 1, 1.00, 'assets/items/mighty-belt/unique_barbbelt_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(719, 'mightyweapon1h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/mightyweapon1h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(720, 'mightyweapon1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/mightyweapon1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(721, 'mightyweapon1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/mightyweapon1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(722, 'mightyweapon1h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/mightyweapon1h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(723, 'mightyweapon1h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/mightyweapon1h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(724, 'mightyweapon1h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/mightyweapon1h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(725, 'p4_unique_mighty_1h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/p4_unique_mighty_1h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(726, 'p4_unique_mighty_1h_104_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/p4_unique_mighty_1h_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(727, 'p67_unique_mighty_1h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/p67_unique_mighty_1h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(728, 'p67_unique_mighty_1h_012_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/p67_unique_mighty_1h_012_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(729, 'p67_unique_mighty_1h_102_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/p67_unique_mighty_1h_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(730, 'unique_mighty_1h_001_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/unique_mighty_1h_001_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(731, 'unique_mighty_1h_010_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/unique_mighty_1h_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(732, 'unique_mighty_1h_011_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/unique_mighty_1h_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(733, 'unique_mighty_1h_015_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/unique_mighty_1h_015_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(734, 'unique_mighty_1h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/mighty-weapon-1h/unique_mighty_1h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(735, 'mightyweapon2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/mightyweapon2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(736, 'mightyweapon2h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/mightyweapon2h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(737, 'mightyweapon2h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/mightyweapon2h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(738, 'mightyweapon2h_004a_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/mightyweapon2h_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(739, 'mightyweapon2h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/mightyweapon2h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(740, 'mightyweapon2h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/mightyweapon2h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(741, 'p610_unique_mighty_2h_101_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/p610_unique_mighty_2h_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1);
INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(742, 'p61_unique_mighty_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/p61_unique_mighty_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(743, 'p61_unique_mighty_2h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/p61_unique_mighty_2h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(744, 'p68_unique_mighty_2h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/p68_unique_mighty_2h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(745, 'unique_mighty_2h_010_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/unique_mighty_2h_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(746, 'unique_mighty_2h_012_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/unique_mighty_2h_012_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(747, 'unique_mighty_2h_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/mighty-weapon-2h/unique_mighty_2h_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(748, 'orb_001_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/orb_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(749, 'orb_002_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/orb_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(750, 'orb_003_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/orb_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(751, 'orb_004a_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/orb_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(752, 'orb_205_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/orb_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:37', NULL, 1),
(753, 'orb_206_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/orb_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(754, 'p4_unique_orb_001_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/p4_unique_orb_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(755, 'p610_unique_orb_002_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/p610_unique_orb_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(756, 'p610_unique_orb_005_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/p610_unique_orb_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(757, 'p61_unique_orb_003_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/p61_unique_orb_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(758, 'p61_unique_orb_004_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/p61_unique_orb_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(759, 'p74_unique_orb_101_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/p74_unique_orb_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(760, 'ph_orb_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/ph_orb_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(761, 'unique_orb_001_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(762, 'unique_orb_004_1xx_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_004_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(763, 'unique_orb_011_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(764, 'unique_orb_012_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(765, 'unique_orb_102_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(766, 'unique_orb_103_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(767, 'unique_orb_set_06_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/orb/unique_orb_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(768, 'p2_unique_pants_01_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p2_unique_pants_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(769, 'p2_unique_pants_02_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p2_unique_pants_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(770, 'p2_unique_pants_03_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p2_unique_pants_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(771, 'p2_unique_pants_04_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p2_unique_pants_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(772, 'p41_unique_pants_001_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p41_unique_pants_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(773, 'p4_unique_pants_002_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p4_unique_pants_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(774, 'p61_necro_unique_pants_21_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p61_necro_unique_pants_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(775, 'p66_unique_pants_010_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p66_unique_pants_010_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(776, 'p66_unique_pants_012_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p66_unique_pants_012_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(777, 'p67_unique_pants_set_01_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p67_unique_pants_set_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(778, 'p67_unique_pants_set_02_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p67_unique_pants_set_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(779, 'p68_unique_pants_set_03_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p68_unique_pants_set_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(780, 'p68_unique_pants_set_04_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p68_unique_pants_set_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(781, 'p68_unique_pants_set_05_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p68_unique_pants_set_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(782, 'p69_necro_set_5_pants_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p69_necro_set_5_pants_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(783, 'p69_unique_pants_set_06_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p69_unique_pants_set_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(784, 'p6_necro_set_1_pants_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p6_necro_set_1_pants_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(785, 'p6_necro_set_2_pants_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p6_necro_set_2_pants_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(786, 'p6_necro_set_3_pants_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p6_necro_set_3_pants_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(787, 'p6_necro_set_4_pants_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p6_necro_set_4_pants_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(788, 'p7_necro_unique_pants_22_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/p7_necro_unique_pants_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(789, 'pants_001_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(790, 'pants_002a_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_002a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(791, 'pants_003_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(792, 'pants_004_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(793, 'pants_005_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(794, 'pants_006_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(795, 'pants_101_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(796, 'pants_102_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(797, 'pants_103_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(798, 'pants_104_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(799, 'pants_105_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(800, 'pants_201_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(801, 'pants_202_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(802, 'pants_203_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(803, 'pants_204_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(804, 'pants_205_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(805, 'pants_206_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(806, 'pants_207_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(807, 'pants_208_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/pants_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(808, 'ph_pants_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/ph_pants_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(809, 'unique_pants_005_1xx_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_005_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(810, 'unique_pants_006_p1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_006_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(811, 'unique_pants_007_p2_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_007_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(812, 'unique_pants_008_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(813, 'unique_pants_009_1xx_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_009_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(814, 'unique_pants_013_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_013_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(815, 'unique_pants_014_1xx_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_014_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(816, 'unique_pants_101_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(817, 'unique_pants_102_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(818, 'unique_pants_set_01_p1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_01_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(819, 'unique_pants_set_01_p2_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_01_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(820, 'unique_pants_set_01_p3_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_01_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(821, 'unique_pants_set_02_p2_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_02_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(822, 'unique_pants_set_02_p3_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_02_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(823, 'unique_pants_set_03_p2_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_03_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(824, 'unique_pants_set_03_p3_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_03_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(825, 'unique_pants_set_05_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_05_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(826, 'unique_pants_set_06_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(827, 'unique_pants_set_07_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_07_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(828, 'unique_pants_set_08_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_08_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(829, 'unique_pants_set_09_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_09_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(830, 'unique_pants_set_10_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_10_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(831, 'unique_pants_set_11_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_11_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(832, 'unique_pants_set_12_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_12_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(833, 'unique_pants_set_13_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_13_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(834, 'unique_pants_set_14_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_14_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(835, 'unique_pants_set_15_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_15_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(836, 'unique_pants_set_16_x1_demonhunter_male', NULL, 'equipment', 'legs', 0, 1, 1, 1.00, 'assets/items/pants/unique_pants_set_16_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(837, 'p4_unique_shoulder_101_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p4_unique_shoulder_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(838, 'p4_unique_shoulder_103_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p4_unique_shoulder_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(839, 'p66_unique_shoulder_008_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p66_unique_shoulder_008_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(840, 'p67_unique_shoulder_102_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p67_unique_shoulder_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(841, 'p67_unique_shoulder_set_01_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p67_unique_shoulder_set_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(842, 'p67_unique_shoulder_set_02_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p67_unique_shoulder_set_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(843, 'p68_unique_shoulder_set_03_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p68_unique_shoulder_set_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(844, 'p68_unique_shoulder_set_04_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p68_unique_shoulder_set_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(845, 'p68_unique_shoulder_set_05_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p68_unique_shoulder_set_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(846, 'p69_necro_set_5_shoulders_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p69_necro_set_5_shoulders_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(847, 'p69_necro_unique_shoulders_22_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p69_necro_unique_shoulders_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(848, 'p69_unique_shoulder_set_06_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p69_unique_shoulder_set_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(849, 'p6_necro_set_1_shoulders_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p6_necro_set_1_shoulders_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(850, 'p6_necro_set_2_shoulders_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p6_necro_set_2_shoulders_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(851, 'p6_necro_set_3_shoulders_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p6_necro_set_3_shoulders_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(852, 'p6_necro_set_4_shoulders_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p6_necro_set_4_shoulders_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(853, 'p6_necro_unique_shoulders_21_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/p6_necro_unique_shoulders_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(854, 'ph_shoulders_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/ph_shoulders_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(855, 'shoulders_002_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(856, 'shoulders_003_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(857, 'shoulders_004_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(858, 'shoulders_005_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(859, 'shoulders_006_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(860, 'shoulders_101_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(861, 'shoulders_102_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(862, 'shoulders_103_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(863, 'shoulders_104_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(864, 'shoulders_105_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_105_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(865, 'shoulders_201_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_201_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(866, 'shoulders_202_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_202_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(867, 'shoulders_203_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_203_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(868, 'shoulders_204_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_204_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(869, 'shoulders_205_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(870, 'shoulders_206_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(871, 'shoulders_207_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(872, 'shoulders_208_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/shoulders_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(873, 'transmogshoulders_001_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/transmogshoulders_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(874, 'unique_shoulder_001_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(875, 'unique_shoulder_002_p2_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_002_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(876, 'unique_shoulder_003_p1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_003_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(877, 'unique_shoulder_006_1xx_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_006_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(878, 'unique_shoulder_007_1xx_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_007_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(879, 'unique_shoulder_009_1xx_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_009_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(880, 'unique_shoulder_017_1xx_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_017_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(881, 'unique_shoulder_102_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(882, 'unique_shoulder_103_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(883, 'unique_shoulder_set_01_p1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_01_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(884, 'unique_shoulder_set_01_p2_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_01_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(885, 'unique_shoulder_set_01_p3_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_01_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(886, 'unique_shoulder_set_02_p2_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_02_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(887, 'unique_shoulder_set_02_p3_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_02_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(888, 'unique_shoulder_set_03_p2_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_03_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(889, 'unique_shoulder_set_03_p3_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_03_p3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(890, 'unique_shoulder_set_05_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_05_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(891, 'unique_shoulder_set_06_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_06_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(892, 'unique_shoulder_set_07_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_07_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(893, 'unique_shoulder_set_08_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_08_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(894, 'unique_shoulder_set_09_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_09_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(895, 'unique_shoulder_set_10_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_10_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(896, 'unique_shoulder_set_11_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_11_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(897, 'unique_shoulder_set_12_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_12_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(898, 'unique_shoulder_set_13_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_13_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(899, 'unique_shoulder_set_14_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_14_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(900, 'unique_shoulder_set_15_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_15_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(901, 'unique_shoulder_set_16_x1_demonhunter_male', NULL, 'equipment', 'shoulders', 0, 1, 1, 1.00, 'assets/items/pauldrons/unique_shoulder_set_16_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(902, 'healthpotionbottomless_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionbottomless_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(903, 'healthpotionlegendary_01_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(904, 'healthpotionlegendary_02_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(905, 'healthpotionlegendary_03_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(906, 'healthpotionlegendary_04_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(907, 'healthpotionlegendary_05_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(908, 'healthpotionlegendary_06_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(909, 'healthpotionlegendary_08_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_08_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(910, 'healthpotionlegendary_09_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_09_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(911, 'healthpotionlegendary_10_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_10_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(912, 'healthpotionlegendary_11_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/healthpotionlegendary_11_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(913, 'p2_healthpotionlegendary_07_demonhunter_male', NULL, 'consumable', 'none', 0, 1, 1, 1.00, 'assets/items/potion/p2_healthpotionlegendary_07_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(914, 'p41_unique_quiver_001_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p41_unique_quiver_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(915, 'p61_unique_quiver_007_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p61_unique_quiver_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(916, 'p65_unique_quiver_001_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p65_unique_quiver_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(917, 'p69_unique_quiver_004_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p69_unique_quiver_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(918, 'p69_unique_quiver_101_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p69_unique_quiver_101_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(919, 'p69_unique_quiver_103_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p69_unique_quiver_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(920, 'p72_unique_quiver_102_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/p72_unique_quiver_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(921, 'ph_quiver_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/ph_quiver_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(922, 'quiver_001_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(923, 'quiver_002_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(924, 'quiver_003_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(925, 'quiver_004_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1);
INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(926, 'quiver_005_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(927, 'quiver_206_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(928, 'quiver_207_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/quiver_207_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(929, 'unique_quiver_003_1xx_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/unique_quiver_003_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(930, 'unique_quiver_005_p1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/unique_quiver_005_p1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(931, 'unique_quiver_006_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/quiver/unique_quiver_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(932, 'p2_unique_ring_03_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p2_unique_ring_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(933, 'p2_unique_ring_04_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p2_unique_ring_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(934, 'p2_unique_ring_wizard_001_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p2_unique_ring_wizard_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(935, 'p42_unique_ring_haunt_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p42_unique_ring_haunt_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(936, 'p43_retroring_001_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p43_retroring_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(937, 'p43_retroring_002_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p43_retroring_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(938, 'p43_unique_ring_021_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p43_unique_ring_021_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(939, 'p4_unique_ring_02_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p4_unique_ring_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(940, 'p4_unique_ring_03_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p4_unique_ring_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(941, 'p61_unique_ring_01_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p61_unique_ring_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(942, 'p61_unique_ring_02_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p61_unique_ring_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(943, 'p61_unique_ring_03_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p61_unique_ring_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:38', NULL, 1),
(944, 'p61_unique_ring_05_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p61_unique_ring_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(945, 'p69_unique_ring_019_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p69_unique_ring_019_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(946, 'p6_unique_ring_01_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p6_unique_ring_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(947, 'p6_unique_ring_02_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p6_unique_ring_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(948, 'p6_unique_ring_03_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p6_unique_ring_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(949, 'p6_unique_ring_04_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p6_unique_ring_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(950, 'p74_unique_ring_007_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/p74_unique_ring_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(951, 'ph_ring_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ph_ring_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(952, 'ring_01_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(953, 'ring_02_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(954, 'ring_03_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(955, 'ring_04_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(956, 'ring_05_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_05_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(957, 'ring_06_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_06_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(958, 'ring_07_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_07_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(959, 'ring_08_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_08_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(960, 'ring_09_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_09_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(961, 'ring_10_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_10_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(962, 'ring_11_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_11_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(963, 'ring_13_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_13_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(964, 'ring_14_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_14_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(965, 'ring_16_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_16_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(966, 'ring_17_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_17_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(967, 'ring_19_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_19_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(968, 'ring_21_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_21_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(969, 'ring_22_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_22_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(970, 'ring_24_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_24_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(971, 'ring_25_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/ring_25_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(972, 'unique_ring_001_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(973, 'unique_ring_002_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(974, 'unique_ring_003_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(975, 'unique_ring_004_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(976, 'unique_ring_006_p2_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_006_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(977, 'unique_ring_010_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(978, 'unique_ring_011_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(979, 'unique_ring_017_p4_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_017_p4_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(980, 'unique_ring_018_p2_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_018_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(981, 'unique_ring_020_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_020_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(982, 'unique_ring_023_p2_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_023_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(983, 'unique_ring_024_104_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_024_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(984, 'unique_ring_101_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(985, 'unique_ring_102_p2_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_102_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(986, 'unique_ring_103_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(987, 'unique_ring_104_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(988, 'unique_ring_106_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_106_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(989, 'unique_ring_107_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(990, 'unique_ring_108_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_108_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(991, 'unique_ring_109_p2_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_109_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(992, 'unique_ring_set_001_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_set_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(993, 'unique_ring_set_002_x1_demonhunter_male', NULL, 'equipment', 'ring', 0, 1, 1, 1.00, 'assets/items/ring/unique_ring_set_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(994, 'p61_unique_scythe1h_03_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/p61_unique_scythe1h_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(995, 'p6_unique_scythe1h_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/p6_unique_scythe1h_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(996, 'p6_unique_scythe1h_02_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/p6_unique_scythe1h_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(997, 'p6_unique_scythe1h_04_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/p6_unique_scythe1h_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(998, 'scythe_1h_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/scythe_1h_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(999, 'scythe_1h_001_t2_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/scythe_1h_001_t2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1000, 'scythe_1h_001_t3_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/scythe_1h_001_t3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1001, 'scythe_1h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/scythe-1h/scythe_1h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1002, 'p61_unique_scythe2h_01_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/p61_unique_scythe2h_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1003, 'p61_unique_scythe2h_02_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/p61_unique_scythe2h_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1004, 'p61_unique_scythe2h_04_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/p61_unique_scythe2h_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1005, 'p6_unique_scythe2h_03_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/p6_unique_scythe2h_03_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1006, 'scythe_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/scythe_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1007, 'scythe_2h_001_t2_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/scythe_2h_001_t2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1008, 'scythe_2h_001_t3_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/scythe_2h_001_t3_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1009, 'scythe_2h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/scythe-2h/scythe_2h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1010, 'p2_unique_shield_002_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/p2_unique_shield_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1011, 'p61_unique_shield_007_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/p61_unique_shield_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1012, 'p6_unique_shield_01_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/p6_unique_shield_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1013, 'ph_shield_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/ph_shield_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1014, 'shield_000_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1015, 'shield_002_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1016, 'shield_003_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1017, 'shield_004_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1018, 'shield_005_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1019, 'shield_006_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1020, 'shield_007_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1021, 'shield_208_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_208_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1022, 'shield_209_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/shield_209_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1023, 'transmogshield_313_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/transmogshield_313_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1024, 'unique_shield_004_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1025, 'unique_shield_008_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1026, 'unique_shield_009_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_009_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1027, 'unique_shield_011_1xx_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_011_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1028, 'unique_shield_012_1xx_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_012_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1029, 'unique_shield_101_p2_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_101_p2_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1030, 'unique_shield_102_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1031, 'unique_shield_104_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1032, 'unique_shield_107_x1_demonhunter_male', NULL, 'equipment', 'off_hand', 0, 1, 1, 1.00, 'assets/items/shield/unique_shield_107_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1033, 'p3_unique_spear_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/p3_unique_spear_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1034, 'p4_unique_spear_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/p4_unique_spear_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1035, 'p610_unique_spear_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/p610_unique_spear_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1036, 'p6_unique_spear_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/p6_unique_spear_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1037, 'spear_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1038, 'spear_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1039, 'spear_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1040, 'spear_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1041, 'spear_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1042, 'spear_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1043, 'spear_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/spear_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1044, 'transmogspear_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/transmogspear_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1045, 'unique_spear_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/unique_spear_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1046, 'unique_spear_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/spear/unique_spear_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1047, 'p2_unique_staff_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/p2_unique_staff_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1048, 'p43_retrostaff_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/p43_retrostaff_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1049, 'p610_unique_staff_102_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/p610_unique_staff_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1050, 'p61_unique_staff_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/p61_unique_staff_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1051, 'p61_unique_staff_009_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/p61_unique_staff_009_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1052, 'p74_unique_staff_103_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/p74_unique_staff_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1053, 'staff_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1054, 'staff_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1055, 'staff_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1056, 'staff_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1057, 'staff_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1058, 'staff_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1059, 'staff_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1060, 'staff_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staff_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1061, 'staffofcow_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/staffofcow_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1062, 'transmogstaff_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/transmogstaff_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1063, 'unique_staff_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/unique_staff_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1064, 'unique_staff_002_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/unique_staff_002_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1065, 'unique_staff_006_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/unique_staff_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1066, 'unique_staff_007_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/unique_staff_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1067, 'unique_staff_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/unique_staff_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1068, 'unique_staff_104_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/staff/unique_staff_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1069, 'holiday_sword_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/holiday_sword_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1070, 'p3_unique_sword_1h_012_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p3_unique_sword_1h_012_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1071, 'p3_unique_sword_1h_104_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p3_unique_sword_1h_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1072, 'p43_retrosword_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p43_retrosword_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1073, 'p43_retrosword_1h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p43_retrosword_1h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1074, 'p4_unique_sword_1h_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p4_unique_sword_1h_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1075, 'p610_unique_sword_1h_107_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p610_unique_sword_1h_107_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1076, 'p61_unique_sword_1h_112_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/p61_unique_sword_1h_112_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1077, 'sword_1h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1078, 'sword_1h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1079, 'sword_1h_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1080, 'sword_1h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1081, 'sword_1h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1082, 'sword_1h_007_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_007_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1083, 'sword_1h_008_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_008_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1084, 'sword_1h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1085, 'sword_1h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/sword_1h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1086, 'transmogsword_241_001_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/transmogsword_241_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1087, 'transmogsword_241_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/transmogsword_241_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1088, 'transmogsword_241_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/transmogsword_241_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1089, 'transmogsword_241_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/transmogsword_241_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1090, 'transmogsword_241_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/transmogsword_241_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1091, 'unique_sword_1h_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1092, 'unique_sword_1h_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1093, 'unique_sword_1h_004_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1094, 'unique_sword_1h_007_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1095, 'unique_sword_1h_010_104_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_010_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1096, 'unique_sword_1h_011_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1097, 'unique_sword_1h_014_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_014_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1098, 'unique_sword_1h_017_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_017_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1099, 'unique_sword_1h_018_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_018_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1100, 'unique_sword_1h_019_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_019_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1101, 'unique_sword_1h_021_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_021_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1102, 'unique_sword_1h_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1103, 'unique_sword_1h_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1104, 'unique_sword_1h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1105, 'unique_sword_1h_109_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_109_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1106, 'unique_sword_1h_113_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_113_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1107, 'unique_sword_1h_promo_02_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_promo_02_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1108, 'unique_sword_1h_set_02_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_set_02_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1109, 'unique_sword_1h_set_03_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/sword-1h/unique_sword_1h_set_03_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1110, 'p61_unique_sword_2h_007_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/p61_unique_sword_2h_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1111, 'p61_unique_sword_2h_012_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/p61_unique_sword_2h_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1112, 'sword_2h_001_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1);
INSERT INTO `items` (`id`, `name`, `description`, `type`, `slot_type`, `two_handed`, `width`, `height`, `weight`, `icon`, `stats`, `max_stack`, `price`, `created_at`, `stat_ranges`, `is_purchasable`) VALUES
(1113, 'sword_2h_002_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1114, 'sword_2h_003_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1115, 'sword_2h_005_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1116, 'sword_2h_006_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1117, 'sword_2h_301_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1118, 'sword_2h_302_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/sword_2h_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1119, 'unique_sword_2h_001_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1120, 'unique_sword_2h_002_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1121, 'unique_sword_2h_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1122, 'unique_sword_2h_004_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1123, 'unique_sword_2h_008_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1124, 'unique_sword_2h_010_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_010_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1125, 'unique_sword_2h_011_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_011_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1126, 'unique_sword_2h_014_104_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_014_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1127, 'unique_sword_2h_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1128, 'unique_sword_2h_102_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1129, 'unique_sword_2h_103_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_103_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1130, 'unique_sword_2h_104_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 1, 1, 1, 1.00, 'assets/items/sword-2h/unique_sword_2h_104_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1131, 'p61_unique_voodoomask_102_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/p61_unique_voodoomask_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1132, 'p65_unique_voodoomask_101_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/p65_unique_voodoomask_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1133, 'p68_unique_helm_set_04_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/p68_unique_helm_set_04_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1134, 'unique_voodoomask_001_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/unique_voodoomask_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1135, 'unique_voodoomask_002_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/unique_voodoomask_002_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:39', NULL, 1),
(1136, 'unique_voodoomask_005_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/unique_voodoomask_005_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1137, 'unique_voodoomask_006_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/unique_voodoomask_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1138, 'unique_voodoomask_007_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/unique_voodoomask_007_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1139, 'unique_voodoomask_008_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/unique_voodoomask_008_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1140, 'voodoomask_001_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/voodoomask_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1141, 'voodoomask_002_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/voodoomask_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1142, 'voodoomask_003_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/voodoomask_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1143, 'voodoomask_004a_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/voodoomask_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1144, 'voodoomask_205_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/voodoomask_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1145, 'voodoomask_206_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/voodoo-mask/voodoomask_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1146, 'p1_wand_norm_unique_01_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p1_wand_norm_unique_01_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1147, 'p2_unique_wand_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p2_unique_wand_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1148, 'p42_unique_wand_003_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p42_unique_wand_003_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1149, 'p610_unique_wand_010_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p610_unique_wand_010_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1150, 'p61_unique_wand_101_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p61_unique_wand_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1151, 'p61_wand_norm_unique_02_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p61_wand_norm_unique_02_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1152, 'p68_unique_wand_102_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/p68_unique_wand_102_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1153, 'unique_wand_006_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/unique_wand_006_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1154, 'unique_wand_009_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/unique_wand_009_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1155, 'unique_wand_012_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/unique_wand_012_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1156, 'unique_wand_013_x1_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/unique_wand_013_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1157, 'unique_wand_018_1xx_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/unique_wand_018_1xx_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1158, 'wand_000_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_000_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1159, 'wand_002_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1160, 'wand_003_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1161, 'wand_004_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_004_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1162, 'wand_005_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_005_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1163, 'wand_006_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_006_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1164, 'wand_007a_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_007a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1165, 'wand_301_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_301_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1166, 'wand_302_demonhunter_male', NULL, 'equipment', 'main_hand', 0, 1, 1, 1.00, 'assets/items/wand/wand_302_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1167, 'p3_unique_wizardhat_003_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/p3_unique_wizardhat_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1168, 'p68_unique_wizardhat_103_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/p68_unique_wizardhat_103_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1169, 'p74_unique_wizardhat_104_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/p74_unique_wizardhat_104_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1170, 'unique_wizardhat_001_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/unique_wizardhat_001_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1171, 'unique_wizardhat_004_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/unique_wizardhat_004_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1172, 'unique_wizardhat_101_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/unique_wizardhat_101_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1173, 'unique_wizardhat_102_x1_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/unique_wizardhat_102_x1_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1174, 'wizardhat_001_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/wizardhat_001_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1175, 'wizardhat_002_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/wizardhat_002_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1176, 'wizardhat_003_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/wizardhat_003_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1177, 'wizardhat_004a_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/wizardhat_004a_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1178, 'wizardhat_205_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/wizardhat_205_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1),
(1179, 'wizardhat_206_demonhunter_male', NULL, 'equipment', 'head', 0, 1, 1, 1.00, 'assets/items/wizard-hat/wizardhat_206_demonhunter_male.png', '{\"strength\": 0, \"vitality\": 0, \"dexterity\": 0, \"intelligence\": 0}', 1, 10, '2025-11-27 16:20:40', NULL, 1);

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

--
-- Dumping data for table `maps`
--

INSERT INTO `maps` (`id`, `name`, `description`, `parent_map_id`, `image_path`, `parent_location_id`, `created_at`) VALUES
(1, 'Main World', 'The main game world', NULL, '/assets/map/main/map_config.json', NULL, '2025-11-24 23:45:29'),
(2, 'Ville d\'Ege', 'Ville d\'Ege', 1, '/assets/map/ege/map_config.json', NULL, '2025-11-25 13:48:30');

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

--
-- Dumping data for table `map_points`
--

INSERT INTO `map_points` (`id`, `map_id`, `name`, `x`, `y`, `type`, `target_id`, `sub_map_id`, `story_id`, `icon`, `is_locked`, `unlock_quest_id`, `unlock_condition_json`, `created_at`, `radius`, `label`, `description`, `is_hidden`) VALUES
(3, 1, 'Labyrinthe du Minotaure', 60, -102, 'story', NULL, NULL, 1, '', 0, NULL, NULL, '2025-11-24 23:46:12', 20, NULL, '', 1),
(4, 1, 'Ville d\'Ege', 47, -114, 'place', NULL, 2, NULL, '', 0, NULL, NULL, '2025-11-24 23:48:01', 20, NULL, '', 0),
(5, 2, 'Chateau', 257, -386, 'npc', 1, NULL, NULL, '', 0, NULL, NULL, '2025-11-25 14:15:58', 200, NULL, '', 0),
(6, 2, 'Forge de l\'Aube d\'Acier', 247, -199, 'npc', 2, NULL, NULL, '', 0, NULL, NULL, '2025-11-26 07:48:45', 50, NULL, '', 0);

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
  `salle_path` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `monsters`
--

INSERT INTO `monsters` (`id`, `name`, `image_path`, `level_min`, `level_max`, `base_stats_json`, `created_at`, `salle_path`) VALUES
(1, 'EL MINAUTORE', '/assets/images/monsters/minautoreAsset.png', 1, 100, '{\"attaque\": 10, \"defense\": 30, \"strength\": 20, \"vitality\": 50, \"dexterity\": 11, \"intelligence\": 10}', '2025-11-25 13:45:38', '/assets/images/monsters/salle_minautore.png'),
(2, 'CuistotEco+', '/assets/images/monsters/cuistoteco.png', 1, 100, '{\"attaque\": 10, \"defense\": 30, \"strength\": 20, \"vitality\": 50, \"dexterity\": 11, \"intelligence\": 10}', '2025-11-27 15:36:25', '/assets/images/monsters/salle_cuistot.png'),
(3, 'Fury', '/assets/images/monsters/fury.png', 1, 100, '{\"attaque\": 10, \"defense\": 30, \"strength\": 20, \"vitality\": 50, \"dexterity\": 11, \"intelligence\": 10}', '2025-11-27 15:48:11', '/assets/images/monsters/salle_fury.png'),
(4, 'Brocanteur', '/assets/images/monsters/brocanteur.png', 1, 100, '{\"attaque\": 10, \"defense\": 30, \"strength\": 20, \"vitality\": 50, \"dexterity\": 11, \"intelligence\": 10}', '2025-11-27 15:51:05', '/assets/images/monsters/sallebrocante.png'),
(5, 'Le Jumeaux Maléfique', '/assets/images/monsters/jumeaux.png', 1, 100, '{\"attaque\": 10, \"defense\": 30, \"strength\": 20, \"vitality\": 50, \"dexterity\": 11, \"intelligence\": 10}', '2025-11-27 18:02:13', NULL);

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

--
-- Dumping data for table `npcs`
--

INSERT INTO `npcs` (`id`, `name`, `role`, `created_at`, `texture`, `merchant_seed`, `buy_rate_own`, `buy_rate_other`) VALUES
(1, 'Roi Minos', 'quest_giver', '2025-11-26 07:32:29', 'assets/npcs/npc_6926ad0da1b10.png', 201749, 0.05, 0.15),
(2, 'Einrich', 'merchant', '2025-11-26 07:47:26', 'assets/npcs/npc_6926b08e9f860.png', 989728, 0.95, 0.85),
(3, 'Princesse Kazokouni', 'npc', '2025-11-26 15:59:15', NULL, 0, 0.05, 0.15);

-- --------------------------------------------------------

--
-- Table structure for table `npc_dialogue_trees`
--

CREATE TABLE `npc_dialogue_trees` (
  `npc_id` int NOT NULL,
  `tree_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `npc_dialogue_trees`
--

INSERT INTO `npc_dialogue_trees` (`npc_id`, `tree_id`, `created_at`) VALUES
(2, 3, '2025-11-27 09:34:16');

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

--
-- Dumping data for table `npc_merchant_inventory`
--

INSERT INTO `npc_merchant_inventory` (`npc_id`, `item_id`, `quantity`, `created_at`) VALUES
(2, 111, 1, '2025-11-28 13:32:23'),
(2, 172, 1, '2025-11-28 13:32:23'),
(2, 215, 1, '2025-11-28 13:32:23'),
(2, 254, 1, '2025-11-28 13:32:23'),
(2, 363, 1, '2025-11-28 13:32:23'),
(2, 385, 1, '2025-11-28 13:32:23'),
(2, 422, 1, '2025-11-28 13:32:23'),
(2, 497, 1, '2025-11-28 13:32:23'),
(2, 640, 1, '2025-11-28 13:32:23'),
(2, 645, 1, '2025-11-28 13:32:23'),
(2, 862, 1, '2025-11-28 13:32:23'),
(2, 890, 1, '2025-11-28 13:32:23'),
(2, 892, 1, '2025-11-28 13:32:23'),
(2, 899, 1, '2025-11-28 13:32:23'),
(2, 916, 1, '2025-11-28 13:32:23'),
(2, 935, 1, '2025-11-28 13:32:23'),
(2, 992, 1, '2025-11-28 13:32:23'),
(2, 1049, 1, '2025-11-28 13:32:23'),
(2, 1095, 1, '2025-11-28 13:32:23'),
(2, 1114, 1, '2025-11-28 13:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `npc_quests`
--

CREATE TABLE `npc_quests` (
  `npc_id` int NOT NULL,
  `quest_id` int NOT NULL,
  `type` enum('GIVER','RECEIVER') NOT NULL DEFAULT 'GIVER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `npc_quests`
--

INSERT INTO `npc_quests` (`npc_id`, `quest_id`, `type`) VALUES
(1, 1, 'GIVER');

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

--
-- Dumping data for table `player_quests`
--

INSERT INTO `player_quests` (`id`, `character_id`, `quest_id`, `current_stage_id`, `status`, `started_at`, `completed_at`) VALUES
(10, 44, 1, 4, 'ACTIVE', '2025-12-02 10:44:04', NULL),
(11, 43, 1, 4, 'ACTIVE', '2025-12-02 11:53:02', NULL),
(13, 47, 1, 4, 'ACTIVE', '2025-12-02 12:31:03', NULL),
(16, 46, 1, 4, 'ACTIVE', '2025-12-03 09:36:06', NULL),
(17, 52, 1, 4, 'ACTIVE', '2025-12-04 10:54:39', NULL),
(18, 54, 1, 4, 'ACTIVE', '2025-12-04 21:30:11', NULL),
(19, 53, 1, 4, 'ACTIVE', '2025-12-04 21:32:18', NULL),
(20, 49, 1, 4, 'ACTIVE', '2025-12-04 23:38:27', NULL);

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
-- Dumping data for table `player_quest_progress`
--

INSERT INTO `player_quest_progress` (`id`, `player_quest_id`, `objective_id`, `count_current`, `is_completed`) VALUES
(25, 10, 7, 1, 1),
(26, 10, 4, 0, 0),
(27, 10, 5, 0, 0),
(28, 11, 7, 1, 1),
(29, 11, 4, 0, 0),
(30, 11, 5, 0, 0),
(34, 13, 7, 1, 1),
(35, 13, 4, 0, 0),
(36, 13, 5, 0, 0),
(43, 16, 7, 1, 1),
(44, 16, 4, 0, 0),
(45, 16, 5, 0, 0),
(46, 17, 7, 1, 1),
(47, 17, 4, 0, 0),
(48, 17, 5, 0, 0),
(49, 18, 7, 1, 1),
(50, 18, 4, 0, 0),
(51, 18, 5, 0, 0),
(52, 19, 7, 1, 1),
(53, 19, 4, 0, 0),
(54, 19, 5, 0, 0),
(55, 20, 7, 1, 1),
(56, 20, 4, 0, 0),
(57, 20, 5, 0, 0);

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
  `intro_text` text COMMENT 'Text spoken by NPC when offering the quest'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quests`
--

INSERT INTO `quests` (`id`, `name`, `description`, `min_level`, `created_at`, `updated_at`, `intro_text`) VALUES
(1, 'Qui a kidnappé la princesse ?', 'La princesse a kidnappé quelques jours avant votre arrivée. Vous avez êtait missionnez par le Roi Minos pour la retrouver. Mais personne ne sait où elle est partie.', 1, '2025-11-26 15:24:51', '2025-11-26 20:39:31', 'Ma chère et tendre fille s\'est enfuit. Retrouvez là mercenaire et vous aurez un gracieux payement.');

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

--
-- Dumping data for table `quest_objectives`
--

INSERT INTO `quest_objectives` (`id`, `stage_id`, `type`, `target_id`, `count_required`, `description`, `dialogue_tree_id`) VALUES
(4, 4, 'KILL_MONSTER', 1, 1, 'Tuer le minotaure', NULL),
(5, 4, 'TALK_NPC', 3, 1, 'Parler a la princesse', NULL),
(6, 5, 'TALK_NPC', 1, 1, 'Parler au Roi Minos', NULL),
(7, 3, 'TALK_NPC', 2, 1, 'Parler au forgeron', 3);

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

--
-- Dumping data for table `quest_stages`
--

INSERT INTO `quest_stages` (`id`, `quest_id`, `name`, `description`, `order_index`, `rewards_json`, `unlocks_json`) VALUES
(3, 1, 'Parler au forgeron', 'La princesse a forcement fuit le chateau en passant par le coeur du village. Fouillez les moindres recoins et parler avec les habitants', 1, NULL, NULL),
(4, 1, 'Retrouvez la princesse dans le labyrinthe', 'Selon les informations du Forgeron elle serait venu acheter une dague avant de partir vers le labyrinthe du minotaure.', 2, NULL, NULL),
(5, 1, 'Ramener la princesse a son père', 'Vous avez retrouvez la princesse. Ramenez la a son père', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quest_stage_unlocks`
--

CREATE TABLE `quest_stage_unlocks` (
  `id` int NOT NULL,
  `quest_stage_id` int NOT NULL,
  `map_point_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quest_stage_unlocks`
--

INSERT INTO `quest_stage_unlocks` (`id`, `quest_stage_id`, `map_point_id`) VALUES
(3, 3, 3);

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

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `name`, `description`, `type`, `difficulty_level`, `min_level`, `procedural_template_id`, `created_at`, `updated_at`) VALUES
(1, 'Labyrinthe du Minotaure', 'Vous venez d’arriver sur la petite île de Crète et, devant vous, se dresse l’imposant labyrinthe dans lequel le Minotaure s’est retranché.  Vous tenez entre vos mains le destin d’une jeune femme, idole de tout un pays. Votre pas devient lourd et s’enfonce dans le sol, à la mesure de l’ampleur de la tâche qui vous est confiée.\r\nUne fois calmés, vous pénétrez à l’intérieur du labyrinthe.', 'manual', 2, 1, NULL, '2025-12-04 17:53:58', '2025-12-04 18:22:42');

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

--
-- Dumping data for table `story_nodes`
--

INSERT INTO `story_nodes` (`id`, `story_id`, `story_instance_id`, `name`, `description`, `image_path`, `is_start_node`, `is_end_node`, `can_exit`, `node_x`, `node_y`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Entrée', 'La première pièce est sombre est humide. Une atmosphère glaçante s’en dégage . Vous avez la chaire de poule.', '/assets/story-images/story_6932ca243d71c.png', 1, 0, 0, 0, 0, '2025-12-04 18:11:45', '2025-12-05 12:03:49'),
(2, 1, NULL, 'Clairière', 'L’atmosphère se réchauffe. Vous arrivez dans un espace ouvert et lumineux, où s’étend une petite clairière.\r\nVous avancez ; le chant des oiseaux remplit l’air et vous rassure. Au centre de la clairière se trouve un plastron (à adapter selon la classe). Deux choix s’offrent alors à vous : continuer dans ce décor, ou emprunter un chemin plus sombre qui s’enfonce dans les entrailles de la terre.', '/assets/story-images/story_6932ca3296d50.png', 0, 0, 0, 0, 0, '2025-12-04 18:40:17', '2025-12-05 12:04:03'),
(3, 1, NULL, 'Gameboy', ' Vous pénétrez dans un couloir étroit, empli d’une substance trouble et gluante dégoulinant sur les murs, telle de la confiture séchée au soleil. Elle borde une mare d’apparence cristalline, aux eaux légèrement troubles, mais tout de même agitées par quelques esprits qui hantent les lieux. Pourtant, plus vous avancez dans ce tunnel, plus l’obscurité s’épaissit. Soudain, une lueur vous interpelle.\r\n\r\nLà, posé sous l’un des rares rayons de soleil capables d’atteindre cet endroit, se trouve un objet incroyable : la cassette originale de Mario 64, édition GameBoy.\r\n\r\nSur votre gauche, une porte attire votre attention.\r\n', '/assets/story-images/story_6932ca2b6bee2.png', 0, 0, 0, 0, 0, '2025-12-04 19:13:24', '2025-12-05 12:03:56'),
(5, 1, NULL, 'Brocante', 'Une salle remplie de tables s’ouvre devant vous. Sur chacune d’elles reposent des dizaines de cassettes de jeux, toutes différentes. Au milieu de la pièce se tient un homme plutôt grand, barbu, et surtout d’apparence négligée. Soudain, il se retourne et se précipite vers vous, vous demandant immédiatement si « vous l’avez ».\r\nNe comprenant pas de quoi il parle, vous vous mettez sur la défensive. Il mentionne une certaine cassette dont il aurait absolument besoin.\r\nRefusant de céder à ses caprices, vous engagez le combat.', '', 0, 0, 0, 314, 68, '2025-12-05 09:53:33', '2025-12-05 11:59:53'),
(6, 1, NULL, 'Croisement', 'Votre aventure se poursuit le long du couloir et vous vous rapprochez de la fin. Devant vous, un croisement se dessine avec deux entrées.', '/assets/story-images/story_6932ca4624942.png', 0, 0, 0, 0, 0, '2025-12-05 09:55:36', '2025-12-05 12:04:23'),
(7, 1, NULL, 'Tresor du Brocanteur', 'Vous venez de vaincre le brocanteur et sa collection infinie de jeux vidéo. Avec un peu de chance, il vous a laissé un objet d’une valeur inestimable : une épée en or. Dès que vous l’aurez équipée, elle vous permettra de vous protéger contre les autres créatures qui hantent ces lieux.\r\nCette épée possède des statistiques défiant les armes des plus grands héros', '/assets/story-images/story_6932ca4090b9e.png', 0, 0, 0, 0, 0, '2025-12-05 09:58:45', '2025-12-05 12:04:17'),
(8, 1, NULL, 'Point d\'eau', 'Vous arrivez dans une salle étrangement calme.  Des rayons de lumière filtrent par des fissures dans le plafond, éclairant un petit bassin aux eaux parfaitement immobiles.\r\nSur le bord, quelqu’un a laissé une petite boîte ovale.  En l’ouvrant, vous découvrez une récompense inattendue. Vous prenez un instant pour vous reposer ; l’ambiance vous y invite presque.  Mais un hurlement lointain vous rappelle la raison de votre présence ici…\r\nVous reprenez la route, déterminé.', '/assets/story-images/story_6932cac33a740.png', 0, 0, 0, 0, 0, '2025-12-05 10:00:26', '2025-12-05 12:06:28'),
(9, 1, NULL, 'Fontaine Bis', 'Vous débouchez dans une petite salle calme, presque apaisante.  Ici, l’air est moins lourd, et le silence semble avoir été posé comme une couverture protectrice. Une fontaine en pierre trône au centre, son eau limpide coulant dans un murmure régulier. La lumière provenant du plafond — ou peut-être d’une magie inconnue — donne à la pièce une douce teinte dorée. \r\nL’endroit semble si tranquille que vous sentez vos muscles se détendre malgré vous. Rien ne bouge, rien ne menace… comme si la salle existait uniquement pour offrir un bref répit aux voyageurs audacieux.', '/assets/story-images/story_6932cabcf0d3f.png', 0, 0, 0, 0, 0, '2025-12-05 10:02:48', '2025-12-05 12:06:22'),
(10, 1, NULL, 'Statue', 'Un couloir étroit vous conduit vers une petite pièce baignée d’une lueur dorée. Au centre, une statue représentant un ancien héros de Crète lève une arme vers le ciel.\r\nÀ ses pieds, un simple mot est gravé :\r\n« Que celui qui poursuit la justice reçoive ce qu’il mérite. »\r\nUn compartiment s’ouvre soudain dans la base de la statue. Vous y trouvez un objet soigneusement enroulé dans un tissu rouge.\r\nUne fois l’objet en main, la lumière s’éteint comme si la statue venait d’accomplir son dernier devoir.', '/assets/story-images/story_6932cab716272.png', 0, 0, 0, 0, 0, '2025-12-05 10:05:03', '2025-12-05 12:06:16'),
(11, 1, NULL, 'Cuisine du Logis', 'Cette décision vous mène dans ce qui pourrait s’apparenter à la cuisine du logis. Un grand panel d’ingrédients est disposé sur la table, tandis que des rats et toutes sortes de petits insectes de bas niveau rodent le long des grandes poutres qui soutiennent la toiture.\r\nDevant la table se tient probablement le cuisinier. Vous l’ayant surpris par votre présence, il se retourne et lance : « Voilà un insecte qui pourrait me servir de repas, cela change des cafards habituels. ».\r\nComprenant que votre viande l’intéresse, vous vous empressez d’engager le combat. Le cuisinier, mage noir depuis les débuts de l’humanité, lance son sort : « Le sérano est très salé, Johan ! ». \r\n', '', 0, 0, 0, 643, 175, '2025-12-05 10:07:59', '2025-12-05 11:59:57'),
(12, 1, NULL, 'Salle Brumeuse', 'Vous entrez dans une salle légèrement brumeuse. L’air y est tiède, presque agréable, et un écho discret résonne à vos pas, comme si la pièce tentait de retenir chaque son. Sur les murs, des gravures anciennes représentent diverses scènes du labyrinthe : des héros perdus, des monstres oubliés, et au centre, l’ombre imposante du Minotaure. De petites lanternes suspendues au plafond fournissent une lumière tamisée, dansante, créant un jeu d’ombres presque hypnotique.\r\nAu fond de la pièce se trouvent deux ouvertures :\r\nl’une part vers un corridor plongeant dans l’obscurité,\r\nl’autre mène vers un passage plus lumineux.', '/assets/story-images/story_6932ca86d0038.png', 0, 0, 0, 0, 0, '2025-12-05 10:14:22', '2025-12-05 12:05:28'),
(13, 1, NULL, 'Miroir', 'Vous pénétrez dans une longue galerie où les murs sont si polis qu’ils reflètent votre silhouette. Au début, cela vous rassure… Puis, après quelques pas, un détail vous saute aux yeux : votre reflet ne marche plus comme vous.\r\nVotre reflet sort alors de la paroi comme s’il traversait une flaque d’eau. Une copie exacte de vous, mais aux yeux éteints et à l’aura sombre.\r\n« Enfin… je te rencontre. Le roi voulait un héros. Moi, je préfère un cadavre. »\r\nLe combat est inévitable. Le reflet reproduce vos gestes, vos attaques… mais les tord, les amplifie, les pervertit.', '', 0, 0, 0, 926, 313, '2025-12-05 10:16:59', '2025-12-05 12:00:06'),
(14, 1, NULL, 'Salle ornée', 'Ce couloir a été façonné différemment du reste du labyrinthe. Le sol est recouvert de grandes dalles parfaitement alignées, et les murs sont ornés de motifs géométriques rouges et noirs. Un souffle chaud traverse l’espace, comme si l’air extérieur parvenait à pénétrer dans ces lieux.\r\nVous vous retrouvez face à un mur bouché, sûrement une ancienne entrée rebouchée de peur de laisser les abominations du labyrinthe s’échapper.', '/assets/story-images/story_6932ca9f19d5c.png', 0, 0, 0, 0, 0, '2025-12-05 10:18:32', '2025-12-05 12:05:53'),
(15, 1, NULL, 'Coussin Rose', 'Une salle remplie de coussins roses s’ouvre à vous : tout est coloré et doux. La pièce rappelle les plus grands salons arabes.  Dans l’ombre d’un coin, un mouvement se dessine et se propage dans toute la pièce en un rien de temps. Sous le sol, une créature bondit. C’est le Fury, une espèce mi-homme, mi-renard, avec une queue aussi longue qu’un humain.\r\nVoulant jouer avec vous, il se prépare à bondir.  « Un conseil : défendez-vous ! »\r\n', '', 0, 0, 0, 492, 533, '2025-12-05 10:23:15', '2025-12-05 12:00:00'),
(16, 1, NULL, 'Salle Rocheuse', 'Le couloir débouche sur une salle creusée grossièrement dans la roche. L’air y est plus lourd, plus chaud, comme si vous descendiez lentement vers le cœur de la terre.\r\nAu sol, un squelette parfaitement aligné repose sur une dalle de marbre. À côté de lui, un sac de toile intact, miraculeusement préservé par le temps.\r\nVous ressentez un grondement sourd venant du fond du couloir. Le Minotaure n’est plus très loin…', '/assets/story-images/story_6932ca66276bc.png', 0, 0, 0, 0, 0, '2025-12-05 10:25:13', '2025-12-05 12:04:55'),
(17, 1, NULL, 'Ancien Charactere Salle', 'Vous avancez dans un couloir aux murs recouverts d’anciens caractères inconnus encore aujourd’hui. Certaines inscriptions s’animent brièvement, comme si une force résiduelle tentait de vous attirer dans leurs univers reniés. Un brouillard bleuâtre envahit peu à peu la pièce suivante. Au centre, une colonne en pierre renferme des milliers de pierres scintillantes.', '/assets/story-images/story_6932ca51c3046.png', 0, 0, 0, 0, 0, '2025-12-05 10:26:57', '2025-12-05 12:04:34'),
(18, 1, NULL, 'Salle Circulaire', 'Vous vous engagez dans une petite salle circulaire. Au centre, posé sur un autel en pierre fissurée, repose un petit coffre en bois vermoulu. Il semble banal… trop banal, même.\r\nLorsque vous l’ouvrez, un léger souffle de poussière s’en échappe, accompagné d’un couinement strident, comme si le coffre n’appréciait pas d’être réveillé après plusieurs siècles d’oubli.\r\nÀ l’intérieur, un objet scintille faiblement.', '/assets/story-images/story_6932ca5cc8cca.png', 0, 0, 0, 0, 0, '2025-12-05 10:28:26', '2025-12-05 12:04:46'),
(19, 1, NULL, 'Dedale Sinueux', 'Le couloir dans lequel vous progressez devient de plus en plus sinueux. Les murs se resserrent légèrement, formant par endroits des passages où vous devez vous décaler de côté pour avancer. Une faible lueur verte émane du sol : une mousse lumineuse recouvre la pierre comme une fine couche de poussière magique.\r\nUne odeur d’herbes sèches flotte dans l’air, contrastant avec l’humidité des salles précédentes. Vous sentez que vous vous enfoncez davantage dans les entrailles du labyrinthe.', '/assets/story-images/story_6932ca79ebf3e.png', 0, 0, 0, 0, 0, '2025-12-05 10:30:04', '2025-12-05 12:05:15'),
(20, 1, NULL, 'Piece du minothaure', 'Vous poussez la dernière porte du labyrinthe. Une chaleur écrasante vous frappe instantanément. Devant vous s’étend une immense salle circulaire, éclairée par des torches dont les flammes bleuâtres vibrent au rythme d’un grondement sourd.\r\nAu centre, enchaînée à une colonne de pierre… La princesse Kazokouni.\r\nElle lève la tête en entendant vos pas.\r\n« Vous… vous êtes venu ? J’avais commencé à perdre espoir… »\r\nAvant que vous puissiez répondre, le sol tremble violemment. Une silhouette herculéenne émerge de l’ombre, cornée, massive, menaçante.\r\nLe Minotaure. Il avance, chacun de ses pas faisant vibrer les dalles comme si la salle elle-même craignait sa présence.\r\nIl vous fixe, redresse sa hache énorme et grogne :\r\n« De tous les insectes qui se sont aventurés ici… tu es le seul assez stupide pour atteindre mon antre. »\r\nSa voix grave résonne dans la pièce entière.\r\n« Tu viens pour la princesse, n’est-ce pas ? Une faible humaine qui hurlait au début… puis qui s’est résignée. Elle est à moi désormais. »\r\nLa princesse s’insurge aussitôt :\r\n« À toi ?! Je t’ai juste demandé de me lâcher, énorme steak sur pattes ! Et arrête de renifler mes cheveux, c’est gênant ! »\r\nLe Minotaure grogne, vexé.\r\n« Silence, offrande ! »\r\nIl pointe sa hache vers vous, les yeux rougeoyant d’une fureur ancienne.\r\n« Tu penses pouvoir me vaincre ? Moi, gardien du Labyrinthe, maître de ces murs, terreur des royaumes ? J’ai écrasé des héros plus grands que toi. Certains ont encore leurs bottes coincées entre mes dents. »\r\nIl claque sa mâchoire dans un bruit sec, juste pour vous provoquer.\r\nLa princesse crie :\r\n« Ne l’écoute pas ! Il est juste gros, poilu et il sent le fromage fort ! Tu peux le faire ! »\r\nLe Minotaure se tourne vers elle, outré :\r\n« Je ne sens pas le fromage.\r\nJe suis le fromage. Le lait de la destruction. Le goût affiné de la mort. »\r\nIl se retourne vers vous, arrogant :\r\n« Approche, misérable. Viens me montrer comment un humain croit défier une légende. Je t’attends. »', '', 0, 1, 0, 1252, 722, '2025-12-05 10:32:37', '2025-12-05 12:00:14');

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

--
-- Dumping data for table `story_node_connections`
--

INSERT INTO `story_node_connections` (`id`, `from_node_id`, `to_node_id`, `direction_text`, `order_index`, `condition_type`, `condition_value`, `allow_return`, `return_text`, `return_condition_type`, `return_condition_value`, `created_at`) VALUES
(7, 5, 6, 'Se diriger vers le croisements', 0, 'none', '', 1, 'Prendre le chemin derrière vous', 'none', '', '2025-12-05 09:58:10'),
(8, 5, 7, 'Continuer vers la porte du fond', 0, 'none', '', 0, '', 'none', '', '2025-12-05 09:59:50'),
(9, 7, 8, 'Continuer tout droit', 0, 'none', '', 1, 'Rebrousser chemin', 'none', '', '2025-12-05 10:01:31'),
(10, 8, 9, 'Avancer tout droit', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:03:02'),
(11, 9, 10, 'Avancer tout droit', 0, 'none', '', 1, 'Revenir en arrière', 'none', '', '2025-12-05 10:05:12'),
(12, 6, 11, 'Prendre le chemin de gauche', 0, 'none', '', 1, 'Se diriger vers le croisement', 'none', '', '2025-12-05 10:08:34'),
(14, 11, 8, 'S\'engager a gauche', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:10:51'),
(15, 11, 9, 'S\'engager a droite', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:11:38'),
(16, 6, 12, 'Prendre le chemin de droite', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:14:26'),
(17, 12, 9, 'Aller a gauche', 0, 'none', '', 1, 'Se diriger vers la brume', 'none', '', '2025-12-05 10:15:18'),
(19, 13, 14, 'Aller tout droit', 0, 'none', '', 1, 'Revenir en arrière', 'none', '', '2025-12-05 10:19:58'),
(20, 1, 3, 'Suivre le tunnel de Gauche', 0, 'none', '', 1, 'Rebrousser Chemin', 'none', '', '2025-12-05 10:21:55'),
(21, 3, 5, 'Ouvrir la porte  a gauche et suivre le chemin', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:21:58'),
(22, 3, 6, 'Diriger vous vers la droite', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:22:01'),
(23, 1, 2, 'Monter les escaliers a gauche', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:22:06'),
(25, 12, 13, 'Aller tout droit', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:22:41'),
(26, 2, 15, 'Se diriger vers la galerie sombre', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:23:21'),
(27, 15, 12, 'Se diriger vers la brume', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:23:27'),
(28, 15, 16, 'Se diriger vers la grotte', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:26:05'),
(29, 16, 13, 'Suivre le chemin de gauche', 0, 'none', '', 1, 'Se diriger dans la grotte', 'none', '', '2025-12-05 10:26:12'),
(30, 2, 17, 'Suivre le chemin lumineux', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:28:12'),
(31, 17, 18, 'Prendre la porte derrière la colonne', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:29:09'),
(32, 18, 19, 'Se diriger vers le dedale', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:30:29'),
(33, 19, 16, 'Prendre le chemin des grottes a droite', 0, 'none', '', 1, 'Aller vers le dedale', 'none', '', '2025-12-05 10:32:17'),
(34, 19, 20, 'Traverser le pont', 0, 'none', '', 0, '', 'none', '', '2025-12-05 10:32:51'),
(35, 18, 16, 'Prendre le chemin vers la grotte', 0, 'none', '', 0, '', 'none', '', '2025-12-05 11:34:56');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
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

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Infuseting', 'Infuseting@gmail.com', '$2y$10$xhdudLBVyl261s/rtRzA8uw2NW7ZXhrcvHDMvMJuzt.CFNfB3uAGG', 'admin', '2025-11-22 11:42:09', '2025-12-02 09:53:57'),
(3, 'visiteur', 'visiteur@gmail.com', '$2y$10$AasOMl.ePOJdJT3kioWnceJQl/GsvVtlhBi90vylarJeqXQtttkRq', 'admin', '2025-11-22 15:33:37', '2025-11-26 14:55:44'),
(4, 'Antoine', 'azimatter@gmail.com', '$2y$10$TOW0qhiRubeCwg3bldy0IuFGwuwIdkpgFJXVhN7lbnwLZhcbaYnf2', 'admin', '2025-11-22 15:57:09', '2025-11-26 14:55:12'),
(5, 'bam', 'metoireullepra-8628@yopmail.com', '$2y$10$GNWOouXwmA0uWfJ1utezNeNSUt7xmnhlX0TCp3uN92PDMGWfdsH1C', '', '2025-11-23 00:18:12', '2025-11-23 00:18:12'),
(6, 'bramus', 'bramus@ootlook.com', '$2y$10$rx0Jsrep2Bgm1Olxe0Bo5e4e2eytMNkOHd5IpuR4YkXGMgY79A9eq', '', '2025-11-24 07:37:05', '2025-11-24 07:37:05'),
(7, 'remy', 'remyleber27220@gmail.com', '$2y$12$kFnFpwR5OhuvoN3UDBOYPuVbbph6d3J/ajDjn3nEtITHIDunMeZGq', 'admin', '2025-11-24 08:09:17', '2025-11-26 14:55:15'),
(8, 'woipy', 'woipy@gmail.com', '$2y$10$t/GZvgWfZlpZQJcGRPZHAOfQ95snbo49GWsGWm5zYd6u1clDe2f3K', '', '2025-11-24 09:34:01', '2025-11-24 09:34:01'),
(9, 'valentin', 'valentin@gmail.com', '$2y$10$qk.nsWNNq9X0EjrmJ8kTtutkjdNcxYET6/jZ.4WdC62gwJyq.pjwy', NULL, '2025-11-25 09:03:52', '2025-11-25 09:03:52'),
(10, 'Avlis', 'avlis@gmail.com', '$2y$10$GcxqRVV.DUMAp6GYGqwTguWXc1mg9aNEo1d6wi4X1PL9N/4vvJoNC', NULL, '2025-11-25 09:04:10', '2025-11-25 09:04:10'),
(11, 'Tgb', 'tristangrossin@gmail.com', '$2y$10$J4zpxnI1hs.O9onMO7AYOOov/P8unjDXnZ7RNqCrm94j1bvq6NzeW', NULL, '2025-11-25 09:04:24', '2025-11-25 09:04:24'),
(12, 'gorkem', 'zzzzzzzzzz@gmail.com', '$2y$10$Ncdu8VDKPXqLUQnd0ZxlK.ggIn6HGojBd4lOk9SlIo.FQkG.66G6q', NULL, '2025-11-25 12:52:29', '2025-11-25 12:52:29'),
(13, 'arthur', 'arthur.langlois@gmail.com', '$2y$10$UM0/zHyWVdmnOmioFKKhC.OQ/n5M81jAN6UQzJNMskjm.RJZBOXeK', 'admin', '2025-11-26 14:57:22', '2025-11-26 14:57:54'),
(16, 'Lenny', 'quesnel.lenny2@gmail.com', '$2y$10$EteAwSmBtTEYlROBHhcUZewIjVt0IGYHKCzPfMYse1ie3E1QH13mC', NULL, '2025-12-02 12:28:57', '2025-12-02 12:28:57'),
(17, 'Pyramide2chiasse', 'albansery27@gmail.com', '$2y$10$kg1PzwPekOP5JAM5F3vesukXGALRLphLi0N6vx5wiSMablddsnxSO', NULL, '2025-12-04 11:55:31', '2025-12-04 11:55:31'),
(18, 'Eckolevrai', 'buzzylintz@gmail.com', '$2y$10$MvO9.TXgw3yh./bGoWdZ9OsoiNf5r0oy3t3KqwV8VQXLK2wxFF20e', NULL, '2025-12-04 21:24:50', '2025-12-04 21:24:50'),
(19, 'bayhar', 'cyrpauand@yahoo.fr', '$2y$10$rHEv95zUx8G2KWRrdOiQhOlbqhfaAMU7py9kKpf8VxrrVKGOsLt.C', NULL, '2025-12-04 21:26:35', '2025-12-04 21:26:35');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `selector`, `hashed_validator`, `expires_at`, `created_at`) VALUES
(1, 1, '382ac0d2f5162ccfebe70fa7', '0f234cc7eaa111b16acf91ecf222ee9a1ef52ab4452b019bb05b3db41ba7a11f', '2025-12-22 15:01:33', '2025-11-22 15:01:33'),
(6, 3, '47377d57dcc882bd6d6f71a6', '6a943037aacefeb1f248a0bfa1eadc36aca02d72c95d30a752643e47a840c193', '2025-12-22 15:34:15', '2025-11-22 15:34:15'),
(7, 3, 'dd3ca441952ec5d994b0ee7a', '068e54728e68ae220b5d73adf98cc5b756ac9331e697d3f18319c37854b78f63', '2025-12-22 15:37:00', '2025-11-22 15:37:00'),
(9, 4, 'eff1b64219d554c52de77d5b', '1161be25029e36210eaad8e7ccbbcdb5544c0b8c9399859e66d6fe200184b123', '2025-12-22 15:57:21', '2025-11-22 15:57:21'),
(11, 1, '250aeb7cbd2929b272fcb142', '8fa26411bbf2cd7832aa39ab49c6617731b060a4a44f3e0b816b2a48dbbbda40', '2025-12-22 18:44:46', '2025-11-22 18:44:46'),
(15, 1, '51d7822b4071fc941e6251bf', 'bcb1f920cef33734cca63765e42734c9075713ffb2d5ec60240570031ab304b9', '2025-12-22 20:39:01', '2025-11-22 20:39:01'),
(17, 1, 'a14338b0fe5cd76ec088d544', '8d4433c4b9635f20255978f1dfa5291eaa968922081e7c153f01cbda1cf90fc3', '2025-12-22 20:59:16', '2025-11-22 20:59:16'),
(18, 1, '0678feb855d1abc2ee1e73d7', '684b4f839a7f19b07cf5507ee292cb62daf3eea70e5220038c07cefd337a14b1', '2025-12-22 21:06:53', '2025-11-22 21:06:53'),
(21, 1, 'eb6627a58a0bce1afe784cbc', 'fce51308d074e8c18232f518391488c46499dae5c0463bb4225064acd66d6b5a', '2025-12-22 23:29:20', '2025-11-22 23:29:20'),
(23, 3, '4103f4a0e10b9262ec230a64', '6c91222035e181cebacaa6abebf8548b73e6a55a2ba0e97b966a99b3b7fdd856', '2025-12-23 00:50:58', '2025-11-23 00:50:58'),
(24, 3, 'b468885dcc0a27c5dcd7be38', '8cd9c524eca3644bbe9d1b0673929a686f393ec527a68388b80a514c266d090e', '2025-12-23 11:13:44', '2025-11-23 11:13:44'),
(27, 5, '64a8d75223345bcf6e9b4fe8', '3e82bad3d83d8fd8f699809c2c63a68145b80fce4a2b0283a1b04b26b2c01770', '2025-12-23 22:51:34', '2025-11-23 22:51:34'),
(28, 3, 'b7288744a1283189375345f9', '90be57335e1c8a8243d2db613efcdc39894b158b593d3eb1209fd665f1d77fc6', '2025-12-24 06:54:55', '2025-11-24 06:54:55'),
(30, 1, 'e3f7abb17fe172b6f7b28462', '201fc9d103ca48b81ac58172cea7950c770131bd095f3e19dcba0e0e226b4ca0', '2025-12-24 07:23:57', '2025-11-24 07:23:58'),
(33, 7, 'eb0e1b8b3afa8f6e8e7e5e5d', '4d997c883a8e1b3d9fd15ae5a29d708f422fae1ec9ab6f578699e37fbd822c4d', '2025-12-24 08:25:27', '2025-11-24 08:25:26'),
(34, 7, 'dd7cb313f344cbc729a1ed37', 'cea5ee1555e3e016cb9bbbe0f36898e3f9cd985cacb888583c1f6ad3a7d765aa', '2025-12-24 08:27:44', '2025-11-24 08:27:43'),
(35, 8, '6325fc7cc0a26a997b9cd357', 'f59edc0183114d3a088eaa79cb33c35481a307e6851c1b401c9fcea91e687cbb', '2025-12-24 09:34:06', '2025-11-24 09:34:06'),
(36, 8, '96a228d86eb0f58df77270cf', '8b2ab3a62c1b154300b664a66c8206fa9102c54ef93724161a1097d4e28a3225', '2025-12-24 10:38:29', '2025-11-24 10:38:29'),
(37, 3, '7c18005e1730429c8c909848', '89bcf2db242781309259ad1edb54ca2636c43fa2541ebb49c399a5034c753039', '2025-12-24 11:09:37', '2025-11-24 11:09:37'),
(38, 8, '0c70ef8f9e63212908a3443d', 'be77178eb9dd04b056e5ef9b2872af5bdc03fc2336c1923f86073fa0302434e1', '2025-12-24 11:09:42', '2025-11-24 11:09:42'),
(42, 3, 'fc20eb46b88c0e388ddb2f14', 'd704fb41d54ca35724a66a2a6ff8b38b34dafb0ef771c6005f091313184fda2d', '2025-12-24 15:59:17', '2025-11-24 15:59:17'),
(43, 3, 'eabe26f6af3eb75e79f5591a', '730cd9fc06cd9571c043879b74c2eafd6488b5792d2ec1b4b349addca9c341d0', '2025-12-24 18:17:22', '2025-11-24 18:17:22'),
(44, 6, 'd7e7dd14dd213ab36f87b992', '4416fe73e4c24f898afd4be6fed952922a692c2a658129efc91226a83bc62e83', '2025-12-24 19:10:15', '2025-11-24 19:10:15'),
(45, 1, '7971b23315bc09e0d3ed7403', 'be44873b8b68e9d2d1a34d5ad5bef44db91c21a6315c22d6b9914ce2412cd13c', '2025-12-24 20:11:20', '2025-11-24 20:11:20'),
(46, 1, 'a8d7ee1593a1e532372a92dc', '8426b9e5660474439c7252715c5aba906a48d39214233e30d063e51f54693f28', '2025-12-24 20:48:51', '2025-11-24 20:48:51'),
(47, 3, '7d30c4d4b987e377ed9ee34f', 'c920999afba66e2a69d53ca2b96abeaca998f1714a48e3c8501ccc1491df4dfa', '2025-12-24 20:50:14', '2025-11-24 20:50:14'),
(49, 1, '7bdc3c065bb8af664ba8b501', 'dbb49c65f6552b524d3a93fff65caa00a41cabe4a541afd9fb12d4ac0eed2392', '2025-12-24 21:25:52', '2025-11-24 21:25:52'),
(50, 1, '4d00c54b2625d6e5dd9f7960', 'f92b2c9a150ff973e9089dbb3f0820e8c79bf074959f7ecc1bf21c36430bbaa3', '2025-12-24 23:00:05', '2025-11-24 23:00:05'),
(51, 1, 'fdd7519a61a4ba754fa74629', '1c08f21ffff2d3eadb0a3b67ef3674d22d0c062a29a53dd129b1b025fbae6c66', '2025-12-24 23:48:17', '2025-11-24 23:48:17'),
(52, 1, 'c24be626ffc7dae8ea27567d', 'a30dd9c96515209de0dceb30ef9ec254b4fcb09d0d08aa6cdc41ffe134a2f1d8', '2025-12-25 00:01:41', '2025-11-25 00:01:41'),
(53, 1, '6e682b5ad2f86421590cc412', 'b7a1bed03d28b7ac9c38e1e016c350e6d5b36a3e03a8f544215549f9da9adbe8', '2025-12-25 07:07:56', '2025-11-25 07:07:56'),
(54, 3, 'aae62fb9a9bbf95277926e3f', '38655a569d9495053f16d44dfe2f5436c21759de4b8220bb542e3c07e9fcd073', '2025-12-25 09:03:09', '2025-11-25 09:03:09'),
(55, 9, '1f4fec9252ea33cacd92c638', 'a7cba21b498795b6f2cd2ebab9a239c3a6a7fe514ee563d7c249544258186c08', '2025-12-25 09:04:04', '2025-11-25 09:04:04'),
(56, 11, 'c578f5355358ee8925238ba3', 'b772ea44f3f7b2d2eb7e52001055f1f82d1b21a3aed6d126969f1a640cb77ee7', '2025-12-25 09:04:41', '2025-11-25 09:04:41'),
(57, 10, '8bd2eec2e4ad2fde97e82f50', '2d77f52d4782f7fbae23ff93a3db1193f2030d94d2649258b8434f229588b64c', '2025-12-25 09:04:43', '2025-11-25 09:04:43'),
(59, 8, '803e7b14c882e1c7d40a84d1', '04f20e1a3d9cbf87a5e6738e7ea51bc88a44b0e0e52983ade57e4544dbd9c7c6', '2025-12-25 11:49:20', '2025-11-25 11:49:20'),
(60, 7, 'cc2c201cdb8179e1d7024d95', '94f7d86287bc726654c02ccb9794a171551d74c0b78c0b9a964efaabf26a1d91', '2025-12-25 12:31:38', '2025-11-25 12:31:36'),
(63, 12, '9a8cefc94cdc93cd4f6801fa', '43b0cd6b69562fa3ea2d9bec1f4aeabf9c1b2390f50810374f30fc2b31c68c57', '2025-12-25 12:52:37', '2025-11-25 12:52:37'),
(67, 4, '678c6a44eba5644c45cde502', 'a1162e79438c97ccee67cf2cb0f4f01103ae60d158339a48b13a47bbd96bad95', '2025-12-25 13:23:02', '2025-11-25 13:23:02'),
(68, 1, 'a1451f44923f9ed9cd220631', 'cc1c9653ae1f132bdf1ff1b92ff53bc370734f84a81a2ca34d061b4ba595c520', '2025-12-25 13:23:03', '2025-11-25 13:23:02'),
(74, 1, '4f0d0cc527f0b462de25f24e', '0ae825ea540fd26632340ab87814626404e82bd0f151d1920bc71966d0c72e3b', '2025-12-25 14:30:08', '2025-11-25 14:30:08'),
(77, 1, '1bd932d3d18b7fbdbc5b9cd7', 'de00149529c2132602d1d5485378e78a43be9e9e10cb27be1857801e8aa7195b', '2025-12-25 17:12:33', '2025-11-25 17:12:33'),
(80, 1, '82881d59fab4773f5f3fdc53', '6c811053fadfdb9394c7e35b5e078b527cc55ea0ba4d97423b36edba0901ede1', '2025-12-26 08:27:58', '2025-11-26 08:27:58'),
(81, 11, '6d93fe0a563e918b845ea8e6', '910e7ea12a8bee3bd054be7619292c8b54ae91c8fa7db9abe8a79dd5fe41bbcc', '2025-12-26 08:43:18', '2025-11-26 08:43:18'),
(87, 7, '6fe17ca0a1433fcfcc41bd97', '5cd7e1d3902d27236577a9c7568cf03c4e2aae11a18d5152029ca9b5e642b5d3', '2025-12-26 09:58:46', '2025-11-26 09:58:46'),
(91, 1, 'b6e9cf243a590e24019bcbaf', '802191b5a3b3603062ca9d26af298f773fc16442ca712c7a879fcabf3538e052', '2025-12-26 12:03:29', '2025-11-26 12:03:28'),
(95, 1, '250503fcce245070a965395d', '2d0d270726a5db33b2d0820a39513e648a885de3baa0a8e3e1a209778bc8150c', '2025-12-26 14:20:47', '2025-11-26 14:20:47'),
(99, 13, 'c67fbbc7950a1de87622406a', '873a0323764bc2677935f517aa158723083b90de30dca2e4b78f006be31fb825', '2025-12-26 14:57:37', '2025-11-26 14:57:37'),
(100, 3, 'a702d659cf8677c557e58e91', 'd9f1b360a7b6adcd12b3832e09503f00e33379ed524d832bcebdd792dca239a6', '2025-12-26 15:11:13', '2025-11-26 15:11:13'),
(102, 3, '851af1f956ea673d0bb8e9e8', 'fff2e020df2aa2e4472adb47abded7daded05deb405a2f814885b08bc330bafb', '2025-12-26 15:38:47', '2025-11-26 15:38:47'),
(104, 1, '6e4d824b968d7d2492a7b134', 'd7e81b2b3f1d211218967c4e1a9e46cd2aa9eb2e981430d5cb008c911b67884f', '2025-12-26 16:07:39', '2025-11-26 16:07:39'),
(105, 3, '643cd9cae917c66d5bdfb8d9', 'a397ec6e675d2b442010c7d623b4c869bd146b66400100a78fc4a769cc31cbc5', '2025-12-26 18:17:36', '2025-11-26 18:17:36'),
(107, 13, '4bca463fbe30b1e1a629ee33', 'af568a6620208e30d684593d811d0edfd0ee604e0af2eca067392268f26410ce', '2025-12-26 20:57:27', '2025-11-26 20:57:27'),
(109, 3, '4678be28cd4d94332bff6d89', '20a6d51adf28c6928b7011b302d2b50cd43ca98d0eff60f5a12f4053ecd15242', '2025-12-26 21:26:28', '2025-11-26 21:26:28'),
(114, 4, 'af378adce0b4fe50fde2c686', 'c63e49716a1073b88f59a5b4fe094be8420a163698a7b3cbecf7dabf825d2525', '2025-12-26 21:52:35', '2025-11-26 21:52:35'),
(119, 1, 'ab1d53e71b5502799c689394', '43e2622a974f2f98ec87962dbc6319465af2bda9c66bef2800d9a49ac26fe60b', '2025-12-26 23:36:49', '2025-11-26 23:36:49'),
(137, 4, '724e24c20f12d30ab9166f11', '38d21f1fb56b99951ff72366fb0142ade5bd943285f3bb9ee8cc5c483b2df45c', '2025-12-27 15:27:48', '2025-11-27 15:27:46'),
(141, 8, 'c82130c3c8edb027a07a0a25', '0fc6a5358ec3c2b200ce7f1e2a7ddf85f77f02bf0f3783e672adbe79a258b4a0', '2025-12-27 19:12:10', '2025-11-27 19:12:10'),
(144, 4, 'b0dc48224265ac7d2a832932', '6f5ce2c348c9f79eeb61900b7cf8a0bd2ab8ee56640575297e5237a23ecd2d4b', '2025-12-27 23:58:37', '2025-11-27 23:58:34'),
(150, 1, 'f216d0e6d1db4aa7c9578ec0', 'fb1ae95dfa2bc5338c6de43cf51465f6372c8715f3043f95d7d25602a426b04b', '2025-12-28 12:55:53', '2025-11-28 12:55:53'),
(156, 3, 'cf38c08b6e6f2a4cbd9f4283', '4b92ef7d8bfd42d5519cb4eb835a6e1aeae933bf3e4aaf35e942e6d2fbcf68de', '2025-12-28 14:16:56', '2025-11-28 14:16:56'),
(162, 1, '2bd7cf1c4e28218ed0988876', '5aa1be6086711cc179935385565fd4e30a24f56ea5aae03538c24e69494a5469', '2025-12-28 15:11:59', '2025-11-28 15:11:59'),
(167, 4, '0552b012c9c28317b7f2aff5', '73b3df7d4f69e29626e215698d35dd901aa7d48cd1f36973820c413e880086d1', '2025-12-28 18:12:35', '2025-11-28 18:12:35'),
(170, 3, 'ac3a0943f238cb8023afa658', 'c60ac28720c985a4fd14c6002d29f4d617c8a3665b1f0fae94d7343671a85633', '2025-12-28 23:24:11', '2025-11-28 23:24:11'),
(171, 3, '61b99e962edb6adef6c4032a', 'b2d6f28ff0a9eb575a9d6dd804f4820b5859e4396be40d874d24a81685020013', '2025-12-29 10:43:37', '2025-11-29 10:43:37'),
(173, 3, 'e127cc8a82f4361a8f4127f8', '3bf5cc07361fe486c9f9f4a15cd0201b462224d91dc5465c2edcef2268f4a6da', '2025-12-29 21:40:08', '2025-11-29 21:40:08'),
(175, 7, 'b7086d77159d85484ed99108', '5c6e371bced674167094bb7e2b571482e720749aa70c8789cbecf826172ae73a', '2025-12-30 18:32:15', '2025-11-30 18:32:15'),
(177, 8, '1542966014888e4e52e6596c', 'a517e3ba14221cbe1b7ebc61428c0517fe7c1a419d4042bac5df52c57bb0f41e', '2026-01-01 07:38:28', '2025-12-02 07:38:28'),
(185, 1, '7bc9b9d52438276f3fc4374f', 'a824291a48834a796e57eca39e10ac018ba52799c020321aab679c4da9bd7967', '2026-01-01 09:47:36', '2025-12-02 09:47:39'),
(190, 3, '7fcfdd77e9e4cd51135b3e7a', '5a270f45d133680481e0ece57bb48fbdd847da117999d42b1d66390f34e5205c', '2026-01-01 10:17:55', '2025-12-02 10:17:55'),
(193, 1, '9d2046feb88b41cef1313400', 'ff6d5711237a025c956f3d3448309a9cf9772125149904c77b96f991730e3774', '2026-01-01 10:33:17', '2025-12-02 10:33:20'),
(196, 4, '9a31c27e0708d9b8594be1ff', '1d91ea26a238f232866b101828e047cfb19afc9bfe8b96ba676b57078a8aceac', '2026-01-01 11:52:43', '2025-12-02 11:52:43'),
(197, 1, '3c2d3c70a5a0a53edbd98b0c', 'd7949866f9147a1590f196ac443f39a94988058285d6fc7fe593395e59096eed', '2026-01-01 12:11:07', '2025-12-02 12:11:07'),
(198, 16, '1c46e10a28dbe06427b53416', '77a0781d9bdd7af94ceb6792e0b841dd70f49a96954ecf6dee074e69d654a368', '2026-01-01 12:29:08', '2025-12-02 12:29:08'),
(210, 4, '49cf320e76367eb44768e3bd', '56ae3904e95194902249973785f841491b8c57d141bf3eaaa8da44909ab588ea', '2026-01-01 20:42:30', '2025-12-02 20:42:29'),
(216, 7, 'c450a7c7c88064ec96dd5aa2', 'fb944980b172915542ea2a27c5cc3d13cc9bc131ab1964b87bfb35d0d3209dce', '2026-01-02 08:49:33', '2025-12-03 08:49:33'),
(221, 1, 'c6544e507ca823ddd32c7a09', 'bf78e066c40cc18bd70f84bb413350f6f10bd0dcf8aa4c06f8817f49335eea1a', '2026-01-02 13:22:00', '2025-12-03 13:22:00'),
(223, 7, '92f57ea549eedc00ab90f76b', '2ec44f92a4cc689bc4fed2ea2329a81d2974a80c7e030ba4f8344e1ece05db89', '2026-01-02 21:35:02', '2025-12-03 21:35:01'),
(224, 7, '8f205391f3b5ebfd93927285', '062623744cbf8b2bb5a03722b4e9f2dfb0c3ebc89e176361611f59d838deb777', '2026-01-02 21:35:49', '2025-12-03 21:35:49'),
(235, 17, '29f771bff87f4c40497a6498', '6f075061efb25082833ebcc23efe6622062815d4e791b9b612c24fbc12244a72', '2026-01-03 11:55:57', '2025-12-04 11:55:57'),
(244, 1, 'fac2ab0fe9849d859b376ec5', '4683c60a95d3bb55f73b65526ceb284ba55ecd496a3310534523cd851e77e876', '2026-01-03 21:04:30', '2025-12-04 21:04:31'),
(245, 3, 'cb4166ebdfcc66d2d940b4bf', '912cd0d07b5d5ccb167a948035e1cc9b33276237696fba0434c3507db0a8ce22', '2026-01-03 21:17:12', '2025-12-04 21:17:12'),
(246, 3, 'f673de0ed96b441fee97c991', '848d1262985e1de101a18ea576d8cf65e0c451aa223c48bdd0af8b8c065bf11f', '2026-01-03 21:17:50', '2025-12-04 21:17:50'),
(247, 1, '4a89bf89c12911f86d5694ef', 'a036bb3b588473da3875b1bf64d44bff52560684a3bcbce3daa62b8dbb2194df', '2026-01-03 21:18:37', '2025-12-04 21:18:37'),
(248, 18, 'd6f9b8ec921c10926d1170b3', '0f3bbf72235b598452a04c52024fda066b4ce3a5c366d295336bcd54d7eb8fb1', '2026-01-03 21:25:00', '2025-12-04 21:25:00'),
(249, 19, '01555097cb30a679d1096ea8', 'bf7f535dfa325db822b235bc517fd490ba8898f7c74004bc2f40cba54fb73c14', '2026-01-03 21:27:01', '2025-12-04 21:27:01'),
(250, 7, 'ea29dca746587a8edce36ea1', '96f21f2597fa2155c20fd6b4f5a901c9058aeec03203a5078a5a57f5daef9c24', '2026-01-03 21:29:33', '2025-12-04 21:29:33'),
(253, 4, '082ce4c64d1875d37c6a3636', '4bb8131a3700838ead461fdd82fefbc44bc4e887973e9c0260e5511149421c7e', '2026-01-03 23:52:56', '2025-12-04 23:52:54'),
(255, 3, 'f6b26c66eecfe2dd1e16c507', '72f0c8a50d10c24f0e964893e09a85c70cebf3a4ab8b33e7deaf4430384fa1da', '2026-01-04 07:08:32', '2025-12-05 07:08:32'),
(261, 4, '68896711f35277df98eaef82', 'e4eff73c123093ed88df76be740e69b209b616ffac1ccce15020e3f6819a43fa', '2026-01-04 09:19:29', '2025-12-05 09:19:29'),
(265, 3, '818aff3cb02de452a8744d10', '9a5f03fc1df8ebc18df44cdb1bb4b97709d437f3c1f1c45be5e9cd9a71a4e1bf', '2026-01-04 11:02:29', '2025-12-05 11:02:29'),
(270, 1, 'dcc9295869353b7e0e77c112', 'd8b6a949fe6a38e0b89d79474c9f19ff30d7067a2e14237ba7f07f47dc1d5e0f', '2026-01-04 11:43:24', '2025-12-05 11:43:24'),
(276, 1, 'f2ba39f4aae2db22b7b7c002', 'b75284de72dad399caf896886973fb8fa3dbe035fd29de9d0af9f1fcaa0bf93c', '2026-01-04 12:14:18', '2025-12-05 12:14:18'),
(277, 1, '090a68eb0d448b448ca5bbf5', '7531eb0bf77b2651e6590a7da7acbf4a72468e33ab93bbde360a4063f596b5af', '2026-01-04 12:18:32', '2025-12-05 12:18:32'),
(280, 1, '538eaa648e200de2338dbd59', 'ef1b9f691ef156f5c81d70e63236e465b76873b3461eab7b4653b78fdfc161b4', '2026-01-04 12:48:09', '2025-12-05 12:48:09'),
(281, 4, '0a53260336a897bb4d99631c', 'be2730192682893f071f3f9d1038306928330b2491242368b632a9d6757d02f1', '2026-01-04 13:48:13', '2025-12-05 13:48:13'),
(282, 4, '56caae4b78ae147125469e39', '9428e8dd229398d601171cfe6f86ccef379eca632a8125fd00709d0cc6169137', '2026-01-04 13:57:51', '2025-12-05 13:57:51'),
(283, 4, '85e4f9d94a7fbcdb25545d82', 'b37ab8d730f98ea32ed0b034e88e12bccfdf95ba1f84b858a56d9278d70ab0da', '2026-01-04 13:59:17', '2025-12-05 13:59:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

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
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `character_inventory`
--
ALTER TABLE `character_inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `character_story_loots_collected`
--
ALTER TABLE `character_story_loots_collected`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `character_story_node_status`
--
ALTER TABLE `character_story_node_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `character_story_progress`
--
ALTER TABLE `character_story_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dialogues`
--
ALTER TABLE `dialogues`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `dialogue_trees`
--
ALTER TABLE `dialogue_trees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1180;

--
-- AUTO_INCREMENT for table `loot_tables`
--
ALTER TABLE `loot_tables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maps`
--
ALTER TABLE `maps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `map_points`
--
ALTER TABLE `map_points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `monsters`
--
ALTER TABLE `monsters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `npcs`
--
ALTER TABLE `npcs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `player_quests`
--
ALTER TABLE `player_quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `player_quest_progress`
--
ALTER TABLE `player_quest_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quest_objectives`
--
ALTER TABLE `quest_objectives`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quest_stages`
--
ALTER TABLE `quest_stages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quest_stage_unlocks`
--
ALTER TABLE `quest_stage_unlocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `story_instances`
--
ALTER TABLE `story_instances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_nodes`
--
ALTER TABLE `story_nodes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `story_node_connections`
--
ALTER TABLE `story_node_connections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

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
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
