-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.1.2-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for pos_cafe
DROP DATABASE IF EXISTS `pos_cafe`;
CREATE DATABASE IF NOT EXISTS `pos_cafe` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `pos_cafe`;

-- Dumping structure for table pos_cafe.audit_logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) unsigned DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `user_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type` (`entity_type`),
  KEY `entity_id` (`entity_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.audit_logs: ~19 rows (approximately)
INSERT INTO `audit_logs` (`id`, `entity_type`, `entity_id`, `action`, `description`, `payload`, `user_id`, `created_at`) VALUES
	(1, 'recipe', 1, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"raw_material_id":1,"qty":10,"waste_pct":2,"note":""}]}', 1, '2025-12-10 05:21:04'),
	(2, 'recipe', 3, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"raw_material_id":1,"qty":10,"waste_pct":1,"note":""}]}', 1, '2025-12-10 05:21:40'),
	(3, 'menu', 7, 'create', 'Menu create #7', '{"name":"Kopi Campur","menu_category_id":1,"sku":"COF-MIX","price":8000,"is_active":1}', 1, '2025-12-12 08:37:08'),
	(4, 'recipe', 7, 'create', 'Recipe create for menu #7', '{"recipe":{"menu_id":7,"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"recipe","child_recipe_id":3,"raw_material_id":null,"qty":0.5,"waste_pct":0.5,"note":""},{"item_type":"recipe","child_recipe_id":6,"raw_material_id":null,"qty":0.5,"waste_pct":0.5,"note":""},{"item_type":"raw","raw_material_id":3,"child_recipe_id":null,"qty":25,"waste_pct":0.25,"note":""}]}', 1, '2025-12-12 08:38:33'),
	(5, 'recipe', 7, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"recipe","child_recipe_id":3,"raw_material_id":null,"qty":1,"waste_pct":0.5,"note":""}]}', 1, '2025-12-12 08:41:07'),
	(6, 'recipe', 7, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"recipe","child_recipe_id":3,"raw_material_id":null,"qty":1,"waste_pct":0.5,"note":""},{"item_type":"recipe","child_recipe_id":6,"raw_material_id":null,"qty":1,"waste_pct":0.5,"note":""}]}', 1, '2025-12-12 08:41:30'),
	(7, 'recipe', 7, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"recipe","child_recipe_id":3,"raw_material_id":null,"qty":1,"waste_pct":0.5,"note":""},{"item_type":"recipe","child_recipe_id":6,"raw_material_id":null,"qty":1,"waste_pct":0.5,"note":""},{"item_type":"raw","raw_material_id":3,"child_recipe_id":null,"qty":100,"waste_pct":0.1,"note":""}]}', 1, '2025-12-12 08:42:35'),
	(8, 'recipe', 1, 'update', 'Recipe update for menu #', '{"recipe":{"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"raw","raw_material_id":1,"child_recipe_id":null,"qty":10,"waste_pct":2,"note":""},{"item_type":"recipe","child_recipe_id":7,"raw_material_id":null,"qty":1,"waste_pct":5,"note":""}]}', 1, '2025-12-12 10:26:33'),
	(9, 'menu', 8, 'create', 'Menu create #8', '{"name":"JUS Jeruk","menu_category_id":2,"sku":"JUS-JER","price":5000,"is_active":1}', 1, '2025-12-13 08:39:42'),
	(10, 'user', 5, 'create', 'User create #5', '{"username":"GSG","full_name":"Grangsang Sotyarmadhani","email":"grangsang1991@gmail.com","role_id":1,"active":1}', 1, '2025-12-25 12:30:24'),
	(11, 'user', 1, 'update', 'User update #1', '{"username":"superadmin","full_name":"TEMU RASA CAFE","email":"temu.rasa.cafe@gmail.com"}', 5, '2025-12-25 12:33:36'),
	(12, 'recipe', 8, 'create', 'Recipe create #8', '{"recipe":{"menu_id":9,"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"raw","raw_material_id":7,"child_recipe_id":null,"qty":1,"waste_pct":0,"note":""}]}', 1, '2025-12-25 13:48:23'),
	(13, 'menu', 9, 'update', 'Menu update #9', '{"name":"Mie Goreng","menu_category_id":3,"sku":"FD-MG","price":16000,"is_active":1}', 1, '2025-12-25 15:04:37'),
	(14, 'menu', 12, 'create', 'Menu create #12', '{"name":"Mie Instant","menu_category_id":3,"sku":null,"price":0,"is_active":1}', 1, '2025-12-25 15:05:38'),
	(15, 'recipe', 11, 'create', 'Recipe create #11', '{"recipe":{"menu_id":12,"yield_qty":1,"yield_unit":"porsi","notes":null},"items":[{"item_type":"raw","raw_material_id":14,"child_recipe_id":null,"qty":300,"waste_pct":0,"note":""}]}', 1, '2025-12-25 15:52:15'),
	(16, 'user', 5, 'update', 'User update #5', '{"username":"GSG","full_name":"Grangsang","email":"grangsang1991@gmail.com","role_id":1}', 1, '2025-12-26 02:27:32'),
	(17, 'user', 5, 'update', 'User update #5', '{"username":"GGS","full_name":"Grangsang","email":"grangsang1991@gmail.com","role_id":1}', 1, '2025-12-26 02:27:41'),
	(18, 'user', 5, 'update', 'User update #5', '{"username":"GSG","full_name":"Grangsang Sotyaramadhani","email":"grangsang1991@gmail.com","role_id":1}', 5, '2025-12-26 02:28:13'),
	(19, 'user', 5, 'update', 'User update #5', '{"username":"GSG","full_name":"Grangsang Sotyaramadhani","email":"grangsang1991@gmail.com","role_id":2}', 1, '2025-12-26 02:28:36');

-- Dumping structure for table pos_cafe.brands
DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.brands: ~12 rows (approximately)
INSERT INTO `brands` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Indomie Goreng', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(2, 'Mie Sedaap Goreng', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(3, 'Telur', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(4, 'Sosis', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(5, 'Bakso', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(6, 'Kopi Kapal Api', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(7, 'Kopi ABC', 1, '2025-12-25 14:30:35', '2025-12-25 14:30:35'),
	(8, 'Indomie', 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(9, 'Mie Sedaap', 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(10, 'Kapal Api', 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(11, 'ABC', 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(12, 'Mie Sedap', 1, '2025-12-25 15:02:11', '2025-12-25 15:02:11');

-- Dumping structure for table pos_cafe.customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.customers: ~3 rows (approximately)
INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Tamu', NULL, NULL, 1, '2025-12-26 00:12:43', '2025-12-26 00:12:43'),
	(2, 'Ari', '08113336419', 'ari@gmail.com', 1, '2025-12-26 00:19:23', '2025-12-26 00:19:23'),
	(3, 'Budi', NULL, NULL, 1, '2025-12-26 00:19:34', '2025-12-26 00:19:34');

-- Dumping structure for table pos_cafe.menus
DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `menu_category_id` int(10) unsigned NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_menu_category_id_foreign` (`menu_category_id`),
  KEY `name` (`name`),
  CONSTRAINT `menus_menu_category_id_foreign` FOREIGN KEY (`menu_category_id`) REFERENCES `menu_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.menus: ~12 rows (approximately)
INSERT INTO `menus` (`id`, `name`, `menu_category_id`, `sku`, `price`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Espresso', 1, 'COF-ESP', 18000.00, 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(2, 'Caffè Latte', 1, 'COF-LAT', 25000.00, 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(3, 'Iced Chocolate', 2, 'NC-CHOC', 26000.00, 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(4, 'French Fries', 3, 'SN-FR', 22000.00, 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(5, 'Kopi Susu', 1, NULL, 30000.00, 1, '2025-12-08 10:11:13', '2025-12-08 10:11:13'),
	(6, 'Jus Alpukat', 2, 'JUS-AVO', 5000.00, 1, '2025-12-10 03:18:13', '2025-12-10 03:18:13'),
	(7, 'Kopi Campur', 1, 'COF-MIX', 8000.00, 1, '2025-12-12 08:37:08', '2025-12-12 08:37:08'),
	(8, 'JUS Jeruk', 2, 'JUS-JER', 5000.00, 1, '2025-12-13 08:39:42', '2025-12-13 08:39:42'),
	(9, 'Mie Goreng', 3, 'FD-MG', 16000.00, 1, '2025-12-25 13:42:48', '2025-12-25 15:04:36'),
	(10, 'Mie Kuah', 3, 'FD-MK', 16000.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(11, 'Kopi Panas', 1, 'COF-KP', 12000.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(12, 'Mie Instant', 3, NULL, 0.00, 1, '2025-12-25 15:05:38', '2025-12-25 15:05:38');

-- Dumping structure for table pos_cafe.menu_categories
DROP TABLE IF EXISTS `menu_categories`;
CREATE TABLE IF NOT EXISTS `menu_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.menu_categories: ~3 rows (approximately)
INSERT INTO `menu_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Coffee', 'Menu berbasis kopi', '2025-12-06 07:05:53', '2025-12-10 06:13:29'),
	(2, 'Non-Coffee', 'Minuman tanpa kopi', '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(3, 'Snack', 'Makanan ringan', '2025-12-06 07:05:53', '2025-12-06 07:05:53');

-- Dumping structure for table pos_cafe.menu_options
DROP TABLE IF EXISTS `menu_options`;
CREATE TABLE IF NOT EXISTS `menu_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `price_delta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `variant_id` int(10) unsigned NOT NULL,
  `qty_multiplier` decimal(12,4) NOT NULL DEFAULT 1.0000,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `variant_id` (`variant_id`),
  CONSTRAINT `menu_options_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `menu_option_groups` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `menu_options_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `raw_material_variants` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.menu_options: ~17 rows (approximately)
INSERT INTO `menu_options` (`id`, `group_id`, `name`, `price_delta`, `variant_id`, `qty_multiplier`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Indomie Goreng', 0.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(2, 1, 'Mie Sedaap Goreng', 0.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(3, 1, 'Telur', 3000.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(4, 1, 'Sosis', 4000.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(5, 1, 'Bakso', 4000.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(6, 2, 'Telur', 3000.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(7, 2, 'Sosis', 4000.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(8, 2, 'Bakso', 4000.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(9, 1, 'Kopi Kapal Api', 0.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(10, 1, 'Kopi ABC', 0.00, 1, 1.0000, 0, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(11, 9, 'Indomie Goreng', 5000.00, 8, 1.0000, 1, 1, '2025-12-25 15:36:12', '2025-12-25 15:51:11'),
	(12, 9, 'Indomie Soto', 5000.00, 14, 1.0000, 2, 1, '2025-12-25 15:36:12', '2025-12-25 15:51:11'),
	(13, 9, 'Mie Sedap Goreng', 5000.00, 13, 1.0000, 3, 1, '2025-12-25 15:36:12', '2025-12-25 15:51:11'),
	(14, 9, 'Mie Sedap Soto', 5000.00, 15, 1.0000, 4, 1, '2025-12-25 15:36:12', '2025-12-25 15:51:11'),
	(15, 10, 'Telur', 1000.00, 9, 1.0000, 1, 1, '2025-12-25 15:41:43', '2025-12-25 15:51:11'),
	(16, 10, 'Sosis', 2000.00, 10, 1.0000, 2, 1, '2025-12-25 15:41:43', '2025-12-25 15:51:11'),
	(17, 10, 'Bakso', 1000.00, 11, 1.0000, 3, 1, '2025-12-25 15:41:43', '2025-12-25 15:51:11');

-- Dumping structure for table pos_cafe.menu_option_groups
DROP TABLE IF EXISTS `menu_option_groups`;
CREATE TABLE IF NOT EXISTS `menu_option_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `min_select` int(11) NOT NULL DEFAULT 0,
  `max_select` int(11) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `show_on_kitchen_ticket` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `menu_option_groups_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.menu_option_groups: ~10 rows (approximately)
INSERT INTO `menu_option_groups` (`id`, `menu_id`, `name`, `is_required`, `min_select`, `max_select`, `sort_order`, `show_on_kitchen_ticket`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Pilih Mie', 1, 1, 1, 1, 1, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(2, 1, 'Tambah Topping', 0, 0, 3, 2, 1, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(3, 1, 'Pilih Kopi Sachet', 1, 1, 1, 1, 1, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48'),
	(4, 9, 'Pilih Mie', 1, 1, 1, 1, 1, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(5, 9, 'Tambah Topping', 0, 0, 3, 2, 1, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(6, 10, 'Pilih Mie', 1, 1, 1, 1, 1, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(7, 10, 'Tambah Topping', 0, 0, 3, 2, 1, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(8, 11, 'Pilih Kopi Sachet', 1, 1, 1, 1, 1, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(9, 12, 'Mie Instant', 1, 1, 1, 1, 1, 1, '2025-12-25 15:36:12', '2025-12-25 15:51:11'),
	(10, 12, 'Add-on', 0, 0, 0, 2, 1, 1, '2025-12-25 15:41:43', '2025-12-25 15:51:11');

-- Dumping structure for table pos_cafe.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.migrations: ~40 rows (approximately)
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
	(1, '2025-12-06-065542', 'App\\Database\\Migrations\\CreateRolesTable', 'default', 'App', 1765004479, 1),
	(2, '2025-12-06-065542', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1765004479, 1),
	(3, '2025-12-06-065543', 'App\\Database\\Migrations\\CreateMenuCategoriesTable', 'default', 'App', 1765004479, 1),
	(4, '2025-12-06-065543', 'App\\Database\\Migrations\\CreateMenusTable', 'default', 'App', 1765004479, 1),
	(5, '2025-12-06-065543', 'App\\Database\\Migrations\\CreateRawMaterialsTable', 'default', 'App', 1765004479, 1),
	(6, '2025-12-06-065543', 'App\\Database\\Migrations\\CreateUnitsTable', 'default', 'App', 1765004479, 1),
	(7, '2025-12-06-082712', 'App\\Database\\Migrations\\CreatePurchaseItemsTable', 'default', 'App', 1765009807, 2),
	(8, '2025-12-06-082712', 'App\\Database\\Migrations\\CreatePurchasesTable', 'default', 'App', 1765009807, 2),
	(9, '2025-12-06-082712', 'App\\Database\\Migrations\\CreateSuppliersTable', 'default', 'App', 1765009807, 2),
	(10, '2025-12-07-113613', 'App\\Database\\Migrations\\CreateStockMovementsTable', 'default', 'App', 1765107415, 3),
	(11, '2025-12-07-120647', 'App\\Database\\Migrations\\CreateRecipeItemsTable', 'default', 'App', 1765109711, 4),
	(12, '2025-12-07-120647', 'App\\Database\\Migrations\\CreateRecipesTable', 'default', 'App', 1765109711, 4),
	(13, '2025-12-08-084739', 'App\\Database\\Migrations\\CreateSalesTable', 'default', 'App', 1765183720, 5),
	(14, '2025-12-08-084807', 'App\\Database\\Migrations\\CreateSaleItemsTable', 'default', 'App', 1765183720, 5),
	(15, '2025-12-10-000000', 'App\\Database\\Migrations\\CreateOverheadsTable', 'default', 'App', 1765286158, 6),
	(16, '2025-12-10-000001', 'App\\Database\\Migrations\\CreateOverheadCategoriesTable', 'default', 'App', 1765286526, 7),
	(17, '2025-12-10-000002', 'App\\Database\\Migrations\\AddCategoryIdToOverheads', 'default', 'App', 1765286526, 7),
	(18, '2025-12-10-105030', 'App\\Database\\Migrations\\AddStatusToSalesTable', 'default', 'App', 1765340852, 8),
	(19, '2025-12-10-120125', 'App\\Database\\Migrations\\CreateAuditLogsTable', 'default', 'App', 1765343577, 9),
	(20, '2025-12-10-131555', 'App\\Database\\Migrations\\RemoveSortOrderFromMenuCategories', 'default', 'App', 1765528270, 10),
	(21, '2025-12-12-080000', 'App\\Database\\Migrations\\AddSubrecipeSupport', 'default', 'App', 1765528270, 10),
	(22, '2025-12-15-200000', 'App\\Database\\Migrations\\CreatePayrollsTable', 'default', 'App', 1765809087, 11),
	(23, '2025-12-19-000000', 'App\\Database\\Migrations\\CreatePasswordResetsTable', 'default', 'App', 1766458919, 12),
	(24, '2025-12-25-000001', 'App\\Database\\Migrations\\AllowNullPasswordHashInUsers', 'default', 'App', 1766665749, 13),
	(25, '2025-12-26-080000', 'App\\Database\\Migrations\\CreateRawMaterialVariantsTable', 'default', 'App', 1766670140, 14),
	(26, '2025-12-26-080100', 'App\\Database\\Migrations\\CreateMenuOptionGroupsTable', 'default', 'App', 1766670140, 14),
	(27, '2025-12-26-080200', 'App\\Database\\Migrations\\CreateMenuOptionsTable', 'default', 'App', 1766670140, 14),
	(28, '2025-12-26-080300', 'App\\Database\\Migrations\\CreateSaleItemOptionsTable', 'default', 'App', 1766670140, 14),
	(29, '2025-12-26-090000', 'App\\Database\\Migrations\\AddVariantToPurchaseItems', 'default', 'App', 1766672907, 15),
	(30, '2025-12-26-091000', 'App\\Database\\Migrations\\CreateBrandsTable', 'default', 'App', 1766672907, 15),
	(31, '2025-12-26-092000', 'App\\Database\\Migrations\\SplitBrandVariantOnRawMaterialVariants', 'default', 'App', 1766673035, 16),
	(32, '2025-12-26-093000', 'App\\Database\\Migrations\\AddBrandFkToRawMaterialVariants', 'default', 'App', 1766673035, 16),
	(33, '2025-12-27-090000', 'App\\Database\\Migrations\\AddVariantStockAndFlags', 'default', 'App', 1766674840, 17),
	(34, '2025-12-27-101000', 'App\\Database\\Migrations\\AddQtyPrecisionToRawMaterials', 'default', 'App', 1766689684, 18),
	(35, '2025-12-27-110000', 'App\\Database\\Migrations\\CreateCustomersTable', 'default', 'App', 1766707963, 19),
	(36, '2025-12-27-110100', 'App\\Database\\Migrations\\AddCustomerIdToSales', 'default', 'App', 1766707963, 19),
	(37, '2025-12-27-111000', 'App\\Database\\Migrations\\AddEmailToCustomers', 'default', 'App', 1766708162, 20),
	(38, '2025-12-27-112000', 'App\\Database\\Migrations\\AddPaymentFieldsToSales', 'default', 'App', 1766709530, 21),
	(39, '2025-12-27-113000', 'App\\Database\\Migrations\\AddKitchenStatusToSales', 'default', 'App', 1766710212, 22),
	(40, '2025-12-27-114000', 'App\\Database\\Migrations\\AddItemNoteToSaleItems', 'default', 'App', 1766712172, 23);

-- Dumping structure for table pos_cafe.overheads
DROP TABLE IF EXISTS `overheads`;
CREATE TABLE IF NOT EXISTS `overheads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trans_date` date NOT NULL,
  `category_id` int(11) unsigned DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_overheads_category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.overheads: ~2 rows (approximately)
INSERT INTO `overheads` (`id`, `trans_date`, `category_id`, `category`, `description`, `amount`, `created_at`, `updated_at`) VALUES
	(1, '2025-12-09', 1, 'Listrik', NULL, 100000.00, '2025-12-09 13:23:48', '2025-12-09 13:23:48'),
	(2, '2025-12-09', 3, 'PDAM', NULL, 50000.00, '2025-12-09 13:24:01', '2025-12-09 13:24:01'),
	(3, '2025-12-09', 2, 'Internet', NULL, 200000.00, '2025-12-09 13:24:11', '2025-12-09 13:24:11');

-- Dumping structure for table pos_cafe.overhead_categories
DROP TABLE IF EXISTS `overhead_categories`;
CREATE TABLE IF NOT EXISTS `overhead_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.overhead_categories: ~3 rows (approximately)
INSERT INTO `overhead_categories` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Listrik', 1, '2025-12-09 13:23:08', '2025-12-12 10:16:57'),
	(2, 'Internet', 1, '2025-12-09 13:23:17', '2025-12-12 10:19:43'),
	(3, 'PDAM', 1, '2025-12-09 13:23:24', '2025-12-12 10:17:18');

-- Dumping structure for table pos_cafe.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `request_ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`),
  KEY `used_at` (`used_at`),
  CONSTRAINT `password_resets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.password_resets: ~5 rows (approximately)
INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`, `request_ip`, `user_agent`) VALUES
	(1, 1, '806064bb615c91f79c48b7737ad58a8ae55e5b67d858549c6938491d602a4e7b', '2025-12-23 04:07:37', '2025-12-23 03:08:26', '2025-12-23 03:07:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
	(2, 1, '3795255d15c63d362e789215654a985e40595d274718bffef9bbe214f29093e7', '2025-12-23 10:20:48', NULL, '2025-12-23 09:20:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
	(3, 1, '27b46c5e6170ec89a452b6df85b9e3e14de298939ab2f1789990716dab15296d', '2025-12-25 12:08:04', NULL, '2025-12-25 11:08:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
	(4, 1, '2de3ab2e4c7a88259ab1e98d8e8e8eb9ee6bd9875150058be5d68991da06bcbf', '2025-12-25 12:41:50', NULL, '2025-12-25 11:41:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
	(5, 5, 'ab0b85a7721ec3b85fe957ce28ca0a9677d114e3767e85afe8baaba030ba9cd2', '2025-12-25 13:31:09', '2025-12-25 12:32:24', '2025-12-25 12:31:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36');

-- Dumping structure for table pos_cafe.payrolls
DROP TABLE IF EXISTS `payrolls`;
CREATE TABLE IF NOT EXISTS `payrolls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `period_month` varchar(7) NOT NULL,
  `pay_date` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_period` (`user_id`,`period_month`),
  CONSTRAINT `payrolls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.payrolls: ~2 rows (approximately)
INSERT INTO `payrolls` (`id`, `user_id`, `period_month`, `pay_date`, `amount`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 2, '2025-12', '2025-12-15', 1500000.00, NULL, '2025-12-15 14:32:27', '2025-12-15 14:32:27'),
	(2, 3, '2025-12', '2025-12-15', 1500000.00, 'Desember 2025', '2025-12-15 14:33:15', '2025-12-15 14:33:15');

-- Dumping structure for table pos_cafe.purchases
DROP TABLE IF EXISTS `purchases`;
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `purchase_date` date NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.purchases: ~6 rows (approximately)
INSERT INTO `purchases` (`id`, `supplier_id`, `purchase_date`, `invoice_no`, `total_amount`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-12-06', NULL, 100.00, NULL, '2025-12-06 08:44:59', '2025-12-06 08:44:59'),
	(2, 2, '2025-12-07', NULL, 60000.00, NULL, '2025-12-07 11:47:08', '2025-12-07 11:47:08'),
	(3, 3, '2025-12-08', NULL, 810000.00, NULL, '2025-12-08 10:09:44', '2025-12-08 10:09:44'),
	(4, 4, '2025-12-07', 'PO-2025-001', 410.00, 'Seed data pembelian awal', '2025-12-08 17:21:36', '2025-12-08 17:21:36'),
	(5, 5, '2025-12-08', 'PO-2025-002', 210.00, 'Seed data pembelian kedua', '2025-12-08 17:21:36', '2025-12-08 17:21:36'),
	(6, 5, '2025-12-09', NULL, 300000.00, NULL, '2025-12-09 13:08:04', '2025-12-09 13:08:04'),
	(7, 5, '2025-12-25', NULL, 30000.00, NULL, '2025-12-25 15:03:18', '2025-12-25 15:03:18');

-- Dumping structure for table pos_cafe.purchase_items
DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `raw_material_id` int(10) unsigned NOT NULL,
  `raw_material_variant_id` int(10) unsigned DEFAULT NULL,
  `qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `raw_material_id` (`raw_material_id`),
  KEY `idx_purchase_items_variant` (`raw_material_variant_id`),
  CONSTRAINT `fk_purchase_items_variant` FOREIGN KEY (`raw_material_variant_id`) REFERENCES `raw_material_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.purchase_items: ~6 rows (approximately)
INSERT INTO `purchase_items` (`id`, `purchase_id`, `raw_material_id`, `raw_material_variant_id`, `qty`, `unit_cost`, `total_cost`) VALUES
	(1, 1, 1, NULL, 1.000, 100.00, 100.00),
	(2, 2, 1, NULL, 200.000, 300.00, 60000.00),
	(3, 3, 2, NULL, 3000.000, 120.00, 360000.00),
	(4, 3, 3, NULL, 1000.000, 450.00, 450000.00),
	(5, 6, 3, NULL, 100.000, 3000.00, 300000.00),
	(6, 7, 16, 8, 10.000, 3000.00, 30000.00);

-- Dumping structure for table pos_cafe.raw_materials
DROP TABLE IF EXISTS `raw_materials`;
CREATE TABLE IF NOT EXISTS `raw_materials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `qty_precision` tinyint(1) DEFAULT 0,
  `current_stock` decimal(15,3) NOT NULL DEFAULT 0.000,
  `min_stock` decimal(15,3) NOT NULL DEFAULT 0.000,
  `cost_last` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cost_avg` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `has_variants` tinyint(1) DEFAULT 0,
  `brand_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `idx_raw_materials_brand` (`brand_id`),
  CONSTRAINT `fk_raw_materials_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.raw_materials: ~17 rows (approximately)
INSERT INTO `raw_materials` (`id`, `name`, `unit_id`, `qty_precision`, `current_stock`, `min_stock`, `cost_last`, `cost_avg`, `is_active`, `created_at`, `updated_at`, `has_variants`, `brand_id`) VALUES
	(1, 'Kopi Arabica', 1, 0, 1702.783, 1000.000, 300.00, 209.04, 1, '2025-12-06 08:20:21', '2025-12-15 14:44:29', 0, NULL),
	(2, 'Gula', 1, 0, 2908.200, 1000.000, 120.00, 120.00, 1, '2025-12-08 10:08:28', '2025-12-09 13:06:46', 0, NULL),
	(3, 'Susu', 2, 0, 49.896, 500.000, 3000.00, 2095.16, 1, '2025-12-08 10:08:49', '2025-12-15 14:44:29', 0, NULL),
	(4, 'Alpukat', 3, 0, 3.396, 3.000, 2000.00, 2000.00, 1, '2025-12-10 03:19:15', '2025-12-15 14:44:29', 0, NULL),
	(5, 'Susu Kental Manis (Coklat)', 2, 0, 968.837, 500.000, 200.00, 200.00, 1, '2025-12-10 03:20:00', '2025-12-15 14:44:29', 0, NULL),
	(6, 'Kopi Kapal Api', 3, 0, 100.000, 50.000, 1000.00, 0.00, 1, '2025-12-16 07:33:31', '2025-12-16 07:33:48', 0, NULL),
	(7, 'Indomie Goreng', 3, 0, 1000.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:47:31', 0, NULL),
	(8, 'Mie Sedaap Goreng', 3, 0, 1000.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:47:42', 0, NULL),
	(9, 'Telur', 3, 0, 44.000, 10.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-26 01:23:51', 0, NULL),
	(10, 'Sosis', 3, 0, 96.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-26 00:42:30', 0, NULL),
	(11, 'Bakso', 3, 0, 99.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-26 00:23:49', 0, NULL),
	(12, 'Kopi ABC', 3, 0, 0.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0, NULL),
	(13, 'Bumbu Mie', 1, 0, 0.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0, NULL),
	(14, 'Air', 2, 0, 9997600.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-26 01:23:51', 0, NULL),
	(15, 'Minyak Goreng', 2, 0, 0.000, 0.000, 0.00, 0.00, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0, NULL),
	(16, 'Mie Instan', 3, 0, 30.000, 0.000, 3000.00, 750.00, 1, '2025-12-25 14:27:23', '2025-12-26 01:23:51', 1, NULL),
	(17, 'Kopi Sachet', 3, 0, 0.000, 0.000, 0.00, 0.00, 1, '2025-12-25 14:27:23', '2025-12-25 14:27:23', 0, NULL);

-- Dumping structure for table pos_cafe.raw_material_variants
DROP TABLE IF EXISTS `raw_material_variants`;
CREATE TABLE IF NOT EXISTS `raw_material_variants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `raw_material_id` int(10) unsigned NOT NULL,
  `brand_id` int(10) unsigned DEFAULT NULL,
  `variant_name` varchar(150) NOT NULL,
  `sku_code` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `current_stock` decimal(15,3) DEFAULT 0.000,
  `min_stock` decimal(15,3) DEFAULT 0.000,
  PRIMARY KEY (`id`),
  KEY `raw_material_id` (`raw_material_id`),
  KEY `brand_id` (`brand_id`),
  KEY `idx_raw_material_variants_brand` (`brand_id`),
  CONSTRAINT `fk_raw_material_variants_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `raw_material_variants_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.raw_material_variants: ~15 rows (approximately)
INSERT INTO `raw_material_variants` (`id`, `raw_material_id`, `brand_id`, `variant_name`, `sku_code`, `is_active`, `created_at`, `updated_at`, `current_stock`, `min_stock`) VALUES
	(1, 1, 1, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(2, 1, 2, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(3, 1, 3, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(4, 1, 4, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(5, 1, 5, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(6, 6, 6, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(7, 1, 7, 'Original', NULL, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', 0.000, 0.000),
	(8, 16, 8, 'Goreng', NULL, 1, '2025-12-25 14:30:43', '2025-12-26 01:23:51', 12.000, 2.000),
	(9, 9, NULL, 'Telur', NULL, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43', 0.000, 0.000),
	(10, 10, NULL, 'Sosis', NULL, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43', 0.000, 0.000),
	(11, 11, NULL, 'Bakso', NULL, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43', 0.000, 0.000),
	(12, 17, 1, 'Original', NULL, 1, '2025-12-25 14:30:43', '2025-12-25 14:30:43', 0.000, 0.000),
	(13, 16, 12, 'Rendang', NULL, 1, '2025-12-25 15:02:11', '2025-12-25 15:02:11', 10.000, 2.000),
	(14, 16, 8, 'Soto', NULL, 1, '2025-12-25 15:02:11', '2025-12-26 00:23:49', 4.000, 1.000),
	(15, 16, 12, 'Soto', NULL, 1, '2025-12-25 15:02:11', '2025-12-26 00:40:06', 4.000, 1.000);

-- Dumping structure for table pos_cafe.recipes
DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL,
  `yield_qty` decimal(10,3) NOT NULL DEFAULT 1.000,
  `yield_unit` varchar(20) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_id` (`menu_id`),
  CONSTRAINT `recipes_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.recipes: ~11 rows (approximately)
INSERT INTO `recipes` (`id`, `menu_id`, `yield_qty`, `yield_unit`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 2, 1.000, 'porsi', NULL, '2025-12-07 12:58:10', '2025-12-12 10:26:33'),
	(2, 5, 1.000, 'porsi', NULL, '2025-12-08 10:12:02', '2025-12-08 10:12:02'),
	(3, 1, 1.000, 'porsi', NULL, '2025-12-08 17:21:36', '2025-12-10 05:21:40'),
	(4, 3, 1.000, 'porsi', NULL, '2025-12-08 17:21:36', '2025-12-08 17:21:36'),
	(5, 4, 1.000, 'porsi', NULL, '2025-12-08 17:21:36', '2025-12-08 17:21:36'),
	(6, 6, 1.000, 'porsi', NULL, '2025-12-10 03:21:36', '2025-12-10 03:21:36'),
	(7, 7, 1.000, 'porsi', NULL, '2025-12-12 08:38:33', '2025-12-12 08:42:35'),
	(8, 9, 1.000, 'porsi', NULL, '2025-12-25 13:48:23', '2025-12-25 13:48:23'),
	(9, 10, 1.000, 'porsi', NULL, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(10, 11, 1.000, 'porsi', NULL, '2025-12-25 14:30:43', '2025-12-25 14:30:43'),
	(11, 12, 1.000, 'porsi', NULL, '2025-12-25 15:52:15', '2025-12-25 15:52:15');

-- Dumping structure for table pos_cafe.recipe_items
DROP TABLE IF EXISTS `recipe_items`;
CREATE TABLE IF NOT EXISTS `recipe_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` int(10) unsigned NOT NULL,
  `item_type` varchar(10) DEFAULT 'raw',
  `raw_material_id` int(10) unsigned DEFAULT NULL,
  `child_recipe_id` int(10) unsigned DEFAULT NULL,
  `qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `waste_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_id` (`recipe_id`),
  KEY `raw_material_id` (`raw_material_id`),
  KEY `idx_recipe_items_child_recipe` (`child_recipe_id`),
  KEY `idx_recipe_items_item_type` (`item_type`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.recipe_items: ~16 rows (approximately)
INSERT INTO `recipe_items` (`id`, `recipe_id`, `item_type`, `raw_material_id`, `child_recipe_id`, `qty`, `waste_pct`, `note`) VALUES
	(4, 2, 'raw', 1, NULL, 50.000, 2.00, ''),
	(5, 2, 'raw', 2, NULL, 10.000, 2.00, ''),
	(6, 2, 'raw', 3, NULL, 100.000, 5.00, ''),
	(7, 6, 'raw', 4, NULL, 0.500, 5.00, ''),
	(8, 6, 'raw', 5, NULL, 10.000, 2.00, ''),
	(10, 3, 'raw', 1, NULL, 10.000, 1.00, ''),
	(17, 7, 'recipe', NULL, 3, 1.000, 0.50, ''),
	(18, 7, 'recipe', NULL, 6, 1.000, 0.50, ''),
	(19, 7, 'raw', 3, NULL, 100.000, 0.10, ''),
	(20, 1, 'raw', 1, NULL, 10.000, 2.00, ''),
	(21, 1, 'recipe', NULL, 7, 1.000, 5.00, ''),
	(22, 8, 'raw', 7, NULL, 1.000, 0.00, ''),
	(23, 1, 'raw', 13, NULL, 15.000, 0.00, 'Bumbu dasar'),
	(24, 1, 'raw', 14, NULL, 250.000, 0.00, 'Kuah'),
	(25, 1, 'raw', 14, NULL, 200.000, 0.00, 'Air panas'),
	(26, 11, 'raw', 14, NULL, 300.000, 0.00, '');

-- Dumping structure for table pos_cafe.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.roles: ~3 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'owner', 'Pemilik cafe, akses penuh', '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(2, 'staff', 'Staff operasional (kasir, pembelian, stok)', '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(3, 'auditor', 'Auditor, akses baca/report saja', '2025-12-06 07:05:53', '2025-12-06 07:05:53');

-- Dumping structure for table pos_cafe.sales
DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_date` date NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT 1,
  `payment_method` varchar(20) DEFAULT 'cash',
  `amount_paid` decimal(15,2) DEFAULT 0.00,
  `change_amount` decimal(15,2) DEFAULT 0.00,
  `kitchen_status` varchar(20) DEFAULT 'open',
  `kitchen_done_at` datetime DEFAULT NULL,
  `customer_name` varchar(120) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `void_reason` text DEFAULT NULL,
  `voided_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sales_customer` (`customer_id`),
  CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.sales: ~21 rows (approximately)
INSERT INTO `sales` (`id`, `sale_date`, `invoice_no`, `customer_id`, `payment_method`, `amount_paid`, `change_amount`, `kitchen_status`, `kitchen_done_at`, `customer_name`, `total_amount`, `total_cost`, `notes`, `status`, `void_reason`, `voided_at`, `created_at`, `updated_at`) VALUES
	(1, '2025-12-08', NULL, 1, 'cash', 65000.00, 0.00, 'done', '2025-12-08 09:13:43', 'Tamu', 65000.00, 0.00, NULL, 'completed', NULL, NULL, '2025-12-08 09:13:43', '2025-12-08 09:13:43'),
	(2, '2025-12-08', NULL, 1, 'cash', 150000.00, 0.00, 'done', '2025-12-08 10:12:40', 'Tamu', 150000.00, 0.00, NULL, 'completed', NULL, NULL, '2025-12-08 10:12:40', '2025-12-08 10:12:40'),
	(8, '2025-12-08', NULL, 1, 'cash', 25000.00, 0.00, 'done', '2025-12-08 14:00:26', 'Tamu', 25000.00, 639.66, NULL, 'completed', NULL, NULL, '2025-12-08 14:00:26', '2025-12-08 14:00:26'),
	(9, '2025-12-08', NULL, 1, 'cash', 36000.00, 0.00, 'done', '2025-12-08 16:33:33', 'Tamu', 36000.00, 0.00, NULL, 'completed', NULL, NULL, '2025-12-08 16:33:33', '2025-12-08 16:33:33'),
	(15, '2025-12-08', NULL, 1, 'cash', 30000.00, 0.00, 'done', '2025-12-08 16:43:28', 'Tamu', 30000.00, 59135.04, NULL, 'completed', NULL, NULL, '2025-12-08 16:43:28', '2025-12-08 16:43:28'),
	(16, '2025-12-08', NULL, 1, 'cash', 100000.00, 0.00, 'done', '2025-12-08 16:50:32', 'Tamu', 100000.00, 2558.65, NULL, 'completed', NULL, NULL, '2025-12-08 16:50:32', '2025-12-08 16:50:32'),
	(17, '2025-12-09', NULL, 1, 'cash', 50000.00, 0.00, 'done', '2025-12-09 12:49:35', 'Tamu', 50000.00, 59135.04, NULL, 'completed', NULL, NULL, '2025-12-09 12:49:35', '2025-12-09 12:49:35'),
	(18, '2025-12-09', NULL, 1, 'cash', 60000.00, 0.00, 'done', '2025-12-09 12:49:57', 'Tamu', 60000.00, 59135.04, NULL, 'completed', NULL, NULL, '2025-12-09 12:49:57', '2025-12-09 12:49:57'),
	(19, '2025-12-09', NULL, 1, 'cash', 30000.00, 0.00, 'done', '2025-12-09 13:06:46', 'Tamu', 30000.00, 59135.04, NULL, 'completed', NULL, NULL, '2025-12-09 13:06:46', '2025-12-09 13:06:46'),
	(20, '2025-12-10', NULL, 1, 'cash', 12000.00, 0.00, 'done', '2025-12-10 03:22:17', 'Tamu', 12000.00, 6180.00, NULL, 'completed', NULL, NULL, '2025-12-10 03:22:17', '2025-12-10 03:22:17'),
	(21, '2025-12-10', NULL, 1, 'cash', 6000.00, 0.00, 'done', '2025-12-10 04:37:49', 'Tamu', 6000.00, 3090.00, NULL, 'void', 'Cancel by customer', '2025-12-10 04:38:25', '2025-12-10 04:37:49', '2025-12-10 04:38:25'),
	(22, '2025-12-12', NULL, 1, 'cash', 8000.00, 0.00, 'done', '2025-12-12 08:39:02', 'Tamu', 8000.00, 55123.60, NULL, 'void', NULL, '2025-12-15 13:12:10', '2025-12-12 08:39:02', '2025-12-15 13:12:10'),
	(23, '2025-12-12', NULL, 1, 'cash', 8000.00, 0.00, 'done', '2025-12-12 08:43:13', 'Tamu', 8000.00, 214952.83, NULL, 'void', 'Cancel by customer', '2025-12-15 13:16:09', '2025-12-12 08:43:13', '2025-12-15 13:16:09'),
	(24, '2025-12-15', NULL, 1, 'cash', 25000.00, 0.00, 'done', '2025-12-15 14:44:29', 'Tamu', 25000.00, 227832.68, NULL, 'completed', NULL, NULL, '2025-12-15 14:44:29', '2025-12-15 14:44:29'),
	(25, '2025-12-25', NULL, 1, 'cash', 6000.00, 0.00, 'done', '2025-12-25 15:58:34', 'Tamu', 6000.00, 750.00, NULL, 'completed', NULL, NULL, '2025-12-25 15:58:34', '2025-12-25 15:58:34'),
	(26, '2025-12-26', NULL, 3, 'cash', 6000.00, 0.00, 'done', '2025-12-26 00:21:39', 'Budi', 6000.00, 750.00, NULL, 'completed', NULL, NULL, '2025-12-26 00:21:39', '2025-12-26 00:21:39'),
	(27, '2025-12-26', NULL, 2, 'cash', 16000.00, 0.00, 'done', '2025-12-26 00:23:49', 'Ari', 16000.00, 1500.00, NULL, 'completed', NULL, NULL, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(28, '2025-12-26', NULL, 3, 'cash', 20000.00, 8000.00, 'done', '2025-12-26 00:40:06', 'Budi', 12000.00, 1500.00, NULL, 'completed', NULL, NULL, '2025-12-26 00:40:06', '2025-12-26 00:40:06'),
	(29, '2025-12-26', NULL, 1, 'qris', 7000.00, 0.00, 'done', '2025-12-26 00:42:30', 'Tamu', 7000.00, 750.00, NULL, 'completed', NULL, NULL, '2025-12-26 00:42:30', '2025-12-26 00:42:30'),
	(30, '2025-12-26', NULL, 1, 'cash', 100000.00, 94000.00, 'done', '2025-12-26 00:52:15', 'Tamu', 6000.00, 750.00, NULL, 'completed', NULL, NULL, '2025-12-26 00:51:38', '2025-12-26 00:52:15'),
	(31, '2025-12-26', NULL, 1, 'qris', 12000.00, 0.00, 'open', NULL, 'Tamu', 12000.00, 1500.00, NULL, 'completed', NULL, NULL, '2025-12-26 01:23:51', '2025-12-26 01:23:51');

-- Dumping structure for table pos_cafe.sale_items
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) unsigned NOT NULL,
  `menu_id` int(10) unsigned NOT NULL,
  `qty` decimal(10,2) NOT NULL DEFAULT 1.00,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `hpp_snapshot` decimal(15,2) NOT NULL DEFAULT 0.00,
  `item_note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.sale_items: ~26 rows (approximately)
INSERT INTO `sale_items` (`id`, `sale_id`, `menu_id`, `qty`, `price`, `subtotal`, `hpp_snapshot`, `item_note`) VALUES
	(1, 1, 2, 1.00, 25000.00, 25000.00, 639.66, NULL),
	(2, 1, 1, 1.00, 18000.00, 18000.00, 0.00, NULL),
	(3, 1, 4, 1.00, 22000.00, 22000.00, 0.00, NULL),
	(4, 2, 5, 5.00, 30000.00, 150000.00, 59135.04, NULL),
	(13, 8, 2, 1.00, 25000.00, 25000.00, 639.66, NULL),
	(14, 9, 1, 2.00, 18000.00, 36000.00, 0.00, NULL),
	(20, 15, 5, 1.00, 30000.00, 30000.00, 59135.04, NULL),
	(21, 16, 2, 4.00, 25000.00, 100000.00, 639.66, NULL),
	(22, 17, 5, 1.00, 50000.00, 50000.00, 59135.04, NULL),
	(23, 18, 5, 1.00, 60000.00, 60000.00, 59135.04, NULL),
	(24, 19, 5, 1.00, 30000.00, 30000.00, 59135.04, NULL),
	(25, 20, 6, 2.00, 6000.00, 12000.00, 3090.00, NULL),
	(26, 21, 6, 1.00, 6000.00, 6000.00, 3090.00, NULL),
	(27, 22, 7, 1.00, 8000.00, 8000.00, 55123.60, NULL),
	(28, 23, 7, 1.00, 8000.00, 8000.00, 214952.83, NULL),
	(29, 24, 2, 1.00, 25000.00, 25000.00, 227832.68, NULL),
	(30, 25, 12, 1.00, 6000.00, 6000.00, 750.00, NULL),
	(31, 26, 12, 1.00, 6000.00, 6000.00, 750.00, NULL),
	(32, 27, 12, 1.00, 7000.00, 7000.00, 750.00, NULL),
	(33, 27, 12, 1.00, 9000.00, 9000.00, 750.00, NULL),
	(34, 28, 12, 1.00, 5000.00, 5000.00, 750.00, NULL),
	(35, 28, 12, 1.00, 7000.00, 7000.00, 750.00, NULL),
	(36, 29, 12, 1.00, 7000.00, 7000.00, 750.00, NULL),
	(37, 30, 12, 1.00, 6000.00, 6000.00, 750.00, NULL),
	(38, 31, 12, 1.00, 6000.00, 6000.00, 750.00, 'Nyemek'),
	(39, 31, 12, 1.00, 6000.00, 6000.00, 750.00, NULL);

-- Dumping structure for table pos_cafe.sale_item_options
DROP TABLE IF EXISTS `sale_item_options`;
CREATE TABLE IF NOT EXISTS `sale_item_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_item_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `qty_selected` decimal(12,4) NOT NULL DEFAULT 1.0000,
  `option_name_snapshot` varchar(150) NOT NULL,
  `price_delta_snapshot` decimal(12,2) NOT NULL DEFAULT 0.00,
  `variant_id_snapshot` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_item_id` (`sale_item_id`),
  KEY `option_id` (`option_id`),
  KEY `variant_id_snapshot` (`variant_id_snapshot`),
  CONSTRAINT `sale_item_options_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `menu_options` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `sale_item_options_sale_item_id_foreign` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `sale_item_options_variant_id_snapshot_foreign` FOREIGN KEY (`variant_id_snapshot`) REFERENCES `raw_material_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.sale_item_options: ~21 rows (approximately)
INSERT INTO `sale_item_options` (`id`, `sale_item_id`, `option_id`, `qty_selected`, `option_name_snapshot`, `price_delta_snapshot`, `variant_id_snapshot`, `created_at`, `updated_at`) VALUES
	(1, 30, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-25 15:58:34', '2025-12-25 15:58:34'),
	(2, 30, 15, 1.0000, 'Telur', 1000.00, 9, '2025-12-25 15:58:34', '2025-12-25 15:58:34'),
	(3, 31, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 00:21:39', '2025-12-26 00:21:39'),
	(4, 31, 15, 1.0000, 'Telur', 1000.00, 9, '2025-12-26 00:21:39', '2025-12-26 00:21:39'),
	(5, 32, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(6, 32, 16, 1.0000, 'Sosis', 2000.00, 10, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(7, 33, 12, 1.0000, 'Indomie Soto', 5000.00, 14, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(8, 33, 15, 1.0000, 'Telur', 1000.00, 9, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(9, 33, 16, 1.0000, 'Sosis', 2000.00, 10, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(10, 33, 17, 1.0000, 'Bakso', 1000.00, 11, '2025-12-26 00:23:49', '2025-12-26 00:23:49'),
	(11, 34, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 00:40:06', '2025-12-26 00:40:06'),
	(12, 35, 14, 1.0000, 'Mie Sedap Soto', 5000.00, 15, '2025-12-26 00:40:06', '2025-12-26 00:40:06'),
	(13, 35, 16, 1.0000, 'Sosis', 2000.00, 10, '2025-12-26 00:40:06', '2025-12-26 00:40:06'),
	(14, 36, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 00:42:30', '2025-12-26 00:42:30'),
	(15, 36, 16, 1.0000, 'Sosis', 2000.00, 10, '2025-12-26 00:42:30', '2025-12-26 00:42:30'),
	(16, 37, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 00:51:38', '2025-12-26 00:51:38'),
	(17, 37, 15, 1.0000, 'Telur', 1000.00, 9, '2025-12-26 00:51:38', '2025-12-26 00:51:38'),
	(18, 38, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 01:23:51', '2025-12-26 01:23:51'),
	(19, 38, 15, 1.0000, 'Telur', 1000.00, 9, '2025-12-26 01:23:51', '2025-12-26 01:23:51'),
	(20, 39, 11, 1.0000, 'Indomie Goreng', 5000.00, 8, '2025-12-26 01:23:51', '2025-12-26 01:23:51'),
	(21, 39, 15, 1.0000, 'Telur', 1000.00, 9, '2025-12-26 01:23:51', '2025-12-26 01:23:51');

-- Dumping structure for table pos_cafe.stock_movements
DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `raw_material_id` int(10) unsigned NOT NULL,
  `movement_type` enum('IN','OUT') NOT NULL,
  `qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `ref_type` varchar(50) NOT NULL,
  `ref_id` int(10) unsigned NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `raw_material_variant_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `raw_material_id` (`raw_material_id`),
  KEY `ref_type_ref_id` (`ref_type`,`ref_id`),
  KEY `idx_stock_movements_variant` (`raw_material_variant_id`),
  CONSTRAINT `fk_stock_movements_variant` FOREIGN KEY (`raw_material_variant_id`) REFERENCES `raw_material_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.stock_movements: ~80 rows (approximately)
INSERT INTO `stock_movements` (`id`, `raw_material_id`, `movement_type`, `qty`, `ref_type`, `ref_id`, `note`, `created_at`, `raw_material_variant_id`) VALUES
	(1, 1, 'IN', 200.000, 'purchase', 2, 'Pembelian dari supplier ID 2', '2025-12-07 11:47:08', NULL),
	(2, 1, 'OUT', 3.060, 'sale', 1, 'Penjualan menu ID 2 (sale_item 1)', NULL, NULL),
	(3, 2, 'IN', 3000.000, 'purchase', 3, 'Pembelian dari supplier ID 3', '2025-12-08 10:09:44', NULL),
	(4, 3, 'IN', 1000.000, 'purchase', 3, 'Pembelian dari supplier ID 3', '2025-12-08 10:09:44', NULL),
	(5, 1, 'OUT', 255.000, 'sale', 2, 'Penjualan menu ID 5 (sale_item 4)', NULL, NULL),
	(6, 2, 'OUT', 51.000, 'sale', 2, 'Penjualan menu ID 5 (sale_item 4)', NULL, NULL),
	(7, 3, 'OUT', 525.000, 'sale', 2, 'Penjualan menu ID 5 (sale_item 4)', NULL, NULL),
	(22, 1, 'OUT', 3.060, 'sale', 8, 'Penjualan menu ID 2 (sale_item 13)', NULL, NULL),
	(23, 1, 'OUT', 51.000, 'sale', 15, 'Penjualan menu ID 5 (sale_item 20)', '2025-12-08 16:43:28', NULL),
	(24, 2, 'OUT', 10.200, 'sale', 15, 'Penjualan menu ID 5 (sale_item 20)', '2025-12-08 16:43:28', NULL),
	(25, 3, 'OUT', 105.000, 'sale', 15, 'Penjualan menu ID 5 (sale_item 20)', '2025-12-08 16:43:28', NULL),
	(26, 1, 'OUT', 12.240, 'sale', 16, 'Penjualan menu ID 2 (sale_item 21)', '2025-12-08 16:50:32', NULL),
	(27, 1, 'OUT', 51.000, 'sale', 17, 'Penjualan menu ID 5 (sale_item 22)', '2025-12-09 12:49:35', NULL),
	(28, 2, 'OUT', 10.200, 'sale', 17, 'Penjualan menu ID 5 (sale_item 22)', '2025-12-09 12:49:35', NULL),
	(29, 3, 'OUT', 105.000, 'sale', 17, 'Penjualan menu ID 5 (sale_item 22)', '2025-12-09 12:49:35', NULL),
	(30, 1, 'OUT', 51.000, 'sale', 18, 'Penjualan menu ID 5 (sale_item 23)', '2025-12-09 12:49:57', NULL),
	(31, 2, 'OUT', 10.200, 'sale', 18, 'Penjualan menu ID 5 (sale_item 23)', '2025-12-09 12:49:57', NULL),
	(32, 3, 'OUT', 105.000, 'sale', 18, 'Penjualan menu ID 5 (sale_item 23)', '2025-12-09 12:49:57', NULL),
	(33, 1, 'OUT', 51.000, 'sale', 19, 'Penjualan menu ID 5 (sale_item 24)', '2025-12-09 13:06:46', NULL),
	(34, 2, 'OUT', 10.200, 'sale', 19, 'Penjualan menu ID 5 (sale_item 24)', '2025-12-09 13:06:46', NULL),
	(35, 3, 'OUT', 105.000, 'sale', 19, 'Penjualan menu ID 5 (sale_item 24)', '2025-12-09 13:06:46', NULL),
	(36, 3, 'IN', 100.000, 'purchase', 6, 'Pembelian dari supplier ID 5', '2025-12-09 13:08:04', NULL),
	(37, 4, 'OUT', 1.050, 'sale', 20, 'Penjualan menu ID 6 (sale_item 25)', '2025-12-10 03:22:17', NULL),
	(38, 5, 'OUT', 20.400, 'sale', 20, 'Penjualan menu ID 6 (sale_item 25)', '2025-12-10 03:22:17', NULL),
	(39, 4, 'OUT', 0.525, 'sale', 21, 'Penjualan menu ID 6 (sale_item 26)', '2025-12-10 04:37:49', NULL),
	(40, 5, 'OUT', 10.200, 'sale', 21, 'Penjualan menu ID 6 (sale_item 26)', '2025-12-10 04:37:49', NULL),
	(41, 4, 'IN', 0.525, 'sale_void', 21, 'Void penjualan #21', '2025-12-10 04:38:25', NULL),
	(42, 5, 'IN', 10.200, 'sale_void', 21, 'Void penjualan #21', '2025-12-10 04:38:25', NULL),
	(43, 1, 'OUT', 5.075, 'sale', 22, 'Penjualan menu ID 7 (sale_item 27)', '2025-12-12 08:39:02', NULL),
	(44, 4, 'OUT', 0.264, 'sale', 22, 'Penjualan menu ID 7 (sale_item 27)', '2025-12-12 08:39:02', NULL),
	(45, 5, 'OUT', 5.126, 'sale', 22, 'Penjualan menu ID 7 (sale_item 27)', '2025-12-12 08:39:02', NULL),
	(46, 3, 'OUT', 25.063, 'sale', 22, 'Penjualan menu ID 7 (sale_item 27)', '2025-12-12 08:39:02', NULL),
	(47, 1, 'OUT', 10.151, 'sale', 23, 'Penjualan menu ID 7 (sale_item 28)', '2025-12-12 08:43:13', NULL),
	(48, 4, 'OUT', 0.528, 'sale', 23, 'Penjualan menu ID 7 (sale_item 28)', '2025-12-12 08:43:13', NULL),
	(49, 5, 'OUT', 10.251, 'sale', 23, 'Penjualan menu ID 7 (sale_item 28)', '2025-12-12 08:43:13', NULL),
	(50, 3, 'OUT', 100.100, 'sale', 23, 'Penjualan menu ID 7 (sale_item 28)', '2025-12-12 08:43:13', NULL),
	(51, 1, 'IN', 5.075, 'sale_void', 22, 'Void penjualan #22', '2025-12-15 13:12:10', NULL),
	(52, 4, 'IN', 0.264, 'sale_void', 22, 'Void penjualan #22', '2025-12-15 13:12:10', NULL),
	(53, 5, 'IN', 5.126, 'sale_void', 22, 'Void penjualan #22', '2025-12-15 13:12:10', NULL),
	(54, 3, 'IN', 25.063, 'sale_void', 22, 'Void penjualan #22', '2025-12-15 13:12:10', NULL),
	(55, 1, 'IN', 10.151, 'sale_void', 23, 'Void penjualan #23', '2025-12-15 13:16:09', NULL),
	(56, 4, 'IN', 0.528, 'sale_void', 23, 'Void penjualan #23', '2025-12-15 13:16:09', NULL),
	(57, 5, 'IN', 10.251, 'sale_void', 23, 'Void penjualan #23', '2025-12-15 13:16:09', NULL),
	(58, 3, 'IN', 100.100, 'sale_void', 23, 'Void penjualan #23', '2025-12-15 13:16:09', NULL),
	(59, 1, 'OUT', 20.858, 'sale', 24, 'Penjualan menu ID 2 (sale_item 29)', '2025-12-15 14:44:29', NULL),
	(60, 4, 'OUT', 0.554, 'sale', 24, 'Penjualan menu ID 2 (sale_item 29)', '2025-12-15 14:44:29', NULL),
	(61, 5, 'OUT', 10.764, 'sale', 24, 'Penjualan menu ID 2 (sale_item 29)', '2025-12-15 14:44:29', NULL),
	(62, 3, 'OUT', 105.105, 'sale', 24, 'Penjualan menu ID 2 (sale_item 29)', '2025-12-15 14:44:29', NULL),
	(63, 16, 'IN', 10.000, 'purchase', 7, 'Pembelian dari supplier ID 5', '2025-12-25 15:03:18', 8),
	(64, 14, 'OUT', 300.000, 'sale', 25, 'Penjualan menu ID 12 (sale_item 30)', '2025-12-25 15:58:34', NULL),
	(65, 16, 'OUT', 1.000, 'sale', 25, 'Add-on Indomie Goreng (sale_item 30)', '2025-12-25 15:58:34', 8),
	(66, 9, 'OUT', 1.000, 'sale', 25, 'Add-on Telur (sale_item 30)', '2025-12-25 15:58:34', NULL),
	(67, 14, 'OUT', 300.000, 'sale', 26, 'Penjualan menu ID 12 (sale_item 31)', '2025-12-26 00:21:39', NULL),
	(68, 16, 'OUT', 1.000, 'sale', 26, 'Add-on Indomie Goreng (sale_item 31)', '2025-12-26 00:21:39', 8),
	(69, 9, 'OUT', 1.000, 'sale', 26, 'Add-on Telur (sale_item 31)', '2025-12-26 00:21:39', NULL),
	(70, 14, 'OUT', 300.000, 'sale', 27, 'Penjualan menu ID 12 (sale_item 32)', '2025-12-26 00:23:49', NULL),
	(71, 16, 'OUT', 1.000, 'sale', 27, 'Add-on Indomie Goreng (sale_item 32)', '2025-12-26 00:23:49', 8),
	(72, 10, 'OUT', 1.000, 'sale', 27, 'Add-on Sosis (sale_item 32)', '2025-12-26 00:23:49', NULL),
	(73, 14, 'OUT', 300.000, 'sale', 27, 'Penjualan menu ID 12 (sale_item 33)', '2025-12-26 00:23:49', NULL),
	(74, 16, 'OUT', 1.000, 'sale', 27, 'Add-on Indomie Soto (sale_item 33)', '2025-12-26 00:23:49', 14),
	(75, 9, 'OUT', 1.000, 'sale', 27, 'Add-on Telur (sale_item 33)', '2025-12-26 00:23:49', NULL),
	(76, 10, 'OUT', 1.000, 'sale', 27, 'Add-on Sosis (sale_item 33)', '2025-12-26 00:23:49', NULL),
	(77, 11, 'OUT', 1.000, 'sale', 27, 'Add-on Bakso (sale_item 33)', '2025-12-26 00:23:49', NULL),
	(78, 14, 'OUT', 300.000, 'sale', 28, 'Penjualan menu ID 12 (sale_item 34)', '2025-12-26 00:40:06', NULL),
	(79, 16, 'OUT', 1.000, 'sale', 28, 'Add-on Indomie Goreng (sale_item 34)', '2025-12-26 00:40:06', 8),
	(80, 14, 'OUT', 300.000, 'sale', 28, 'Penjualan menu ID 12 (sale_item 35)', '2025-12-26 00:40:06', NULL),
	(81, 16, 'OUT', 1.000, 'sale', 28, 'Add-on Mie Sedap Soto (sale_item 35)', '2025-12-26 00:40:06', 15),
	(82, 10, 'OUT', 1.000, 'sale', 28, 'Add-on Sosis (sale_item 35)', '2025-12-26 00:40:06', NULL),
	(83, 14, 'OUT', 300.000, 'sale', 29, 'Penjualan menu ID 12 (sale_item 36)', '2025-12-26 00:42:30', NULL),
	(84, 16, 'OUT', 1.000, 'sale', 29, 'Add-on Indomie Goreng (sale_item 36)', '2025-12-26 00:42:30', 8),
	(85, 10, 'OUT', 1.000, 'sale', 29, 'Add-on Sosis (sale_item 36)', '2025-12-26 00:42:30', NULL),
	(86, 14, 'OUT', 300.000, 'sale', 30, 'Penjualan menu ID 12 (sale_item 37)', '2025-12-26 00:51:39', NULL),
	(87, 16, 'OUT', 1.000, 'sale', 30, 'Add-on Indomie Goreng (sale_item 37)', '2025-12-26 00:51:39', 8),
	(88, 9, 'OUT', 1.000, 'sale', 30, 'Add-on Telur (sale_item 37)', '2025-12-26 00:51:39', NULL),
	(89, 14, 'OUT', 300.000, 'sale', 31, 'Penjualan menu ID 12 (sale_item 38)', '2025-12-26 01:23:51', NULL),
	(90, 16, 'OUT', 1.000, 'sale', 31, 'Add-on Indomie Goreng (sale_item 38)', '2025-12-26 01:23:51', 8),
	(91, 9, 'OUT', 1.000, 'sale', 31, 'Add-on Telur (sale_item 38)', '2025-12-26 01:23:51', NULL),
	(92, 14, 'OUT', 300.000, 'sale', 31, 'Penjualan menu ID 12 (sale_item 39)', '2025-12-26 01:23:51', NULL),
	(93, 16, 'OUT', 1.000, 'sale', 31, 'Add-on Indomie Goreng (sale_item 39)', '2025-12-26 01:23:51', 8),
	(94, 9, 'OUT', 1.000, 'sale', 31, 'Add-on Telur (sale_item 39)', '2025-12-26 01:23:51', NULL);

-- Dumping structure for table pos_cafe.suppliers
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.suppliers: ~3 rows (approximately)
INSERT INTO `suppliers` (`id`, `name`, `phone`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
	(2, 'SUPPLIER A', NULL, NULL, 1, '2025-12-07 10:55:13', '2025-12-07 10:55:13'),
	(3, 'SUPPLIER B', NULL, NULL, 1, '2025-12-08 10:09:03', '2025-12-08 10:09:03'),
	(4, 'PT Maju Jaya', '0812-3456-7890', 'Jl. Kopi No. 1, Jakarta', 1, '2025-12-08 17:21:36', '2025-12-08 17:21:36'),
	(5, 'CV Bahan Sejahtera', '0813-2222-3333', 'Jl. Susu No. 5, Bandung', 1, '2025-12-08 17:21:36', '2025-12-08 17:21:36');

-- Dumping structure for table pos_cafe.units
DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_name` (`short_name`),
  KEY `idx_units_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.units: ~3 rows (approximately)
INSERT INTO `units` (`id`, `name`, `short_name`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Gram', 'gr', 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(2, 'Mililiter', 'ml', 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53'),
	(3, 'Pieces', 'pcs', 1, '2025-12-06 07:05:53', '2025-12-06 07:05:53');

-- Dumping structure for table pos_cafe.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table pos_cafe.users: ~6 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `role_id`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'superadmin', '$2y$10$ta5f2VcOiVE8uyiP7ykEF.IFxq2oNcOiKZPIZIUUVXHEL22RQ3nrK', 'TEMU RASA CAFE', 'temu.rasa.cafe@gmail.com', 1, 1, '2025-12-06 07:05:53', '2025-12-25 12:33:36', NULL),
	(2, 'staff1', '$2y$10$hxATDpUOCYtJ3yBfzKLy9.yKW3VcUP9sgEWJAUefItPwG.p8DB3oK', 'Staff Operasional 1', 'staff1@example.com', 2, 1, '2025-12-09 13:49:37', '2025-12-09 13:49:37', NULL),
	(3, 'staff2', '$2y$10$.EYKKSKOF8nGry9PhrfubO.ZplUI70bn.79NuPog2JTxEHAboDiNi', 'Staff Operasional 2', 'staff2@example.com', 2, 1, '2025-12-09 13:49:37', '2025-12-09 13:49:37', NULL),
	(4, 'auditor', '$2y$10$c4UzPJFdrG1/fcD8kSCJT.4WV/og2HdRRg90Dvk8LcF8b0gTswfWe', 'Auditor', 'auditor@example.com', 3, 1, '2025-12-09 13:49:37', '2025-12-09 13:49:37', NULL),
	(5, 'GSG', '$2y$10$jEd5BH0FJ9zvsHG1RyGwlu00f.u0NgeQECui/OcKYmBsXRDRhBQLK', 'Grangsang Sotyaramadhani', 'grangsang1991@gmail.com', 2, 1, '2025-12-25 12:30:24', '2025-12-26 02:28:36', NULL),
	(6, 'owner', '$2y$10$byYpTUfFyBonzbjdswmOfeAaZ9J73cx0.l2NrZdLhBQ3.4Sk4VXVW', 'Owner Cafe', 'owner@example.com', 1, 1, '2025-12-25 13:42:48', '2025-12-25 13:42:48', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
