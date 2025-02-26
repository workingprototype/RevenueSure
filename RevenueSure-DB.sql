-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table lead_platform.accountants
CREATE TABLE IF NOT EXISTS `accountants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('Accountant','Senior Accountant') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Accountant',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.accountants: ~0 rows (approximately)
REPLACE INTO `accountants` (`id`, `name`, `email`, `contact_number`, `role`, `active`, `created_at`) VALUES
	(1, 'TEST', 'accountant@demo.com', '123456', 'Accountant', 1, '2025-02-12 07:23:42');

-- Dumping structure for table lead_platform.ai_conversations
CREATE TABLE IF NOT EXISTS `ai_conversations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ai_conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.ai_conversations: ~0 rows (approximately)
REPLACE INTO `ai_conversations` (`id`, `user_id`, `title`, `created_at`) VALUES
	(1, 2, NULL, '2025-02-26 17:19:33');

-- Dumping structure for table lead_platform.ai_messages
CREATE TABLE IF NOT EXISTS `ai_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` int NOT NULL,
  `sender` enum('user','ai') COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  CONSTRAINT `ai_messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.ai_messages: ~0 rows (approximately)
REPLACE INTO `ai_messages` (`id`, `conversation_id`, `sender`, `message`, `sent_at`) VALUES
	(1, 1, 'user', 'Explain this code:\n\n```\n <div class="node" id="node"></div>\n    <div class="cursor" id="cursor"></div>\n\n	<div class="back-to-top zoom-cursor magnetic"></div>\n\n	<!-- JavaScripts -->\n  <?php\n  include("footerjs.php");\n```', '2025-02-26 17:20:08'),
	(2, 1, 'ai', 'Let me break down this code snippet:\n\n1. HTML Elements:\n```html\n<div class="node" id="node"></div>\n<div class="cursor" id="cursor"></div>\n```\nThese are two div elements that are likely used for custom cursor functionality:\n- The "node" div might be used as a following point or interaction element\n- The "cursor" div probably represents a custom cursor implementation\n\n2. Back to Top Button:\n```html\n<div class="back-to-top zoom-cursor magnetic"></div>\n```\nThis is a "back to top" button with three CSS classes:\n- `back-to-top`: Main class for the button functionality\n- `zoom-cursor`: Likely adds a zoom effect when hovering\n- `magnetic`: Probably adds a magnetic/attraction effect on cursor interaction\n\n3. PHP Include:\n```php\n<?php\ninclude("footerjs.php");\n```\nThis is a PHP include statement that:\n- Imports the contents of a file named "footerjs.php"\n- This file likely contains JavaScript code/references\n- Common practice to include JavaScript files at the bottom of the page for better page loading performance\n\nThis code appears to be part of a webpage that implements:\n- Custom cursor interactions\n- A back-to-top button with special effects\n- JavaScript functionality loaded through a separate PHP file\n\nThe combination suggests this is part of a modern, interactive website with custom cursor effects and navigation features.', '2025-02-26 17:20:15');

-- Dumping structure for table lead_platform.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_type` enum('contract','proposal','notes') COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.attachments: ~0 rows (approximately)
REPLACE INTO `attachments` (`id`, `lead_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
	(1, 2, 'test', NULL, 'notes', '2025-01-27 15:12:13');

-- Dumping structure for table lead_platform.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.categories: ~3 rows (approximately)
REPLACE INTO `categories` (`id`, `name`, `created_at`) VALUES
	(1, 'IT', '2025-01-26 06:52:51'),
	(2, 'Restaurants', '2025-01-26 06:53:15'),
	(3, 'TEST', '2025-02-08 18:22:10');

-- Dumping structure for table lead_platform.contracts
CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contract_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `contract_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `hide_from_customer` tinyint(1) DEFAULT '0',
  `contract_text` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contracts: ~2 rows (approximately)
REPLACE INTO `contracts` (`id`, `project_id`, `customer_id`, `subject`, `contract_value`, `contract_type`, `start_date`, `end_date`, `description`, `hide_from_customer`, `contract_text`, `created_at`, `updated_at`) VALUES
	(1, 6, 1, 'Bridge Development', 1304.00, 'Legal', '2025-02-01', '2025-02-06', 'Development Contract for the bridge', 0, '<p>TEST Contract Text</p>', '2025-02-01 12:14:36', NULL),
	(2, NULL, 2, 'Tower Development', 1300.00, 'Legal', '2025-01-01', '2025-03-15', 'Legal contract for the tower development', 0, '<p><strong>This Infrastructure Development Agreement</strong> (the “Agreement”) is made on this [Date], by and between:</p><p><strong>1. [Client Name/Company Name]</strong>, a company incorporated under the laws of [Country/State], having its principal office at [Address] (hereinafter referred to as “Client”),<br>AND<br><strong>2. [Contractor Name/Company Name]</strong>, a company incorporated under the laws of [Country/State], having its principal office at [Address] (hereinafter referred to as “Contractor”).</p>', '2025-02-01 12:27:30', '2025-02-01 13:22:38'),
	(4, 8, 2, 'Screen Development', 12455.00, 'Legal', '2025-02-21', '2025-02-28', 'This is for development of a screen.', 0, '<p>This is the development of a screen</p><figure class="table"><table><tbody><tr><td>et</td><td>tes</td><td>&nbsp;</td><td>rwa</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>tes</td><td>&nbsp;</td></tr><tr><td>tse</td><td>tsee</td><td>&nbsp;</td><td>&nbsp;</td><td>t</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>t</td><td>&nbsp;</td><td>&nbsp;</td><td>tes</td><td>&nbsp;</td><td>te</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table></figure>', '2025-02-21 08:54:35', NULL);

-- Dumping structure for table lead_platform.contract_audit_trail
CREATE TABLE IF NOT EXISTS `contract_audit_trail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `geolocation_data` text COLLATE utf8mb4_general_ci,
  `timezone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device_info` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `contract_audit_trail_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_audit_trail_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contract_audit_trail: ~11 rows (approximately)
REPLACE INTO `contract_audit_trail` (`id`, `contract_id`, `user_id`, `action`, `details`, `ip_address`, `geolocation_data`, `timezone`, `device_info`, `created_at`) VALUES
	(1, 2, 5, 'Signature Added', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:34:42'),
	(2, 2, 5, 'Signature Added', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:35:14'),
	(3, 2, 5, 'Added comment', 'Great', NULL, NULL, NULL, NULL, '2025-02-01 12:39:32'),
	(4, 2, 5, 'Signature Added', NULL, '::1', '[]', 'Europe/Berlin', '{"browser":"Safari","os":"Mac"}', '2025-02-01 12:40:10'),
	(5, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:50:13'),
	(6, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:50:27'),
	(7, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 13:22:38'),
	(8, 2, 5, 'Signature Added', NULL, '::1', NULL, 'Europe/Berlin', '{"browser":"Safari","os":"Mac"}', '2025-02-01 14:04:13'),
	(9, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 20:56:29'),
	(10, 2, 5, 'Signature Added', NULL, '::1', NULL, 'Europe/Berlin', '{"browser":"Safari","os":"Mac"}', '2025-02-01 20:56:42'),
	(11, 2, 5, 'Added comment', 'Nice', NULL, NULL, NULL, NULL, '2025-02-01 20:59:21'),
	(15, 4, 2, 'Added comment', 'This looks nice', NULL, NULL, NULL, NULL, '2025-02-21 08:54:51'),
	(16, 4, 2, 'Signature Added', NULL, '::1', NULL, 'UTC', '{"browser":"Chrome","os":"Windows"}', '2025-02-21 08:55:24');

-- Dumping structure for table lead_platform.contract_signatures
CREATE TABLE IF NOT EXISTS `contract_signatures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_id` int NOT NULL,
  `user_id` int NOT NULL,
  `signature_data` text COLLATE utf8mb4_general_ci,
  `signed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `contract_signatures_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_signatures_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contract_signatures: ~0 rows (approximately)
REPLACE INTO `contract_signatures` (`id`, `contract_id`, `user_id`, `signature_data`, `signed_at`) VALUES
	(7, 2, 5, 'data:image/png;base64,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', '2025-02-01 20:56:42'),
	(9, 4, 2, 'data:image/png;base64,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', '2025-02-21 08:55:24');

-- Dumping structure for table lead_platform.contract_status
CREATE TABLE IF NOT EXISTS `contract_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_id` int NOT NULL,
  `status` enum('Draft','Sent','Signed','Active','Expired','Canceled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  `status_changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`),
  CONSTRAINT `contract_status_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contract_status: ~21 rows (approximately)
REPLACE INTO `contract_status` (`id`, `contract_id`, `status`, `status_changed_at`) VALUES
	(1, 1, 'Draft', '2025-02-01 12:14:36'),
	(2, 1, 'Sent', '2025-02-01 12:23:24'),
	(3, 1, 'Active', '2025-02-01 12:23:27'),
	(4, 1, 'Sent', '2025-02-01 12:23:31'),
	(5, 1, 'Draft', '2025-02-01 12:23:33'),
	(6, 1, 'Sent', '2025-02-01 12:23:35'),
	(7, 1, 'Active', '2025-02-01 12:23:42'),
	(8, 1, 'Draft', '2025-02-01 12:26:04'),
	(9, 1, 'Active', '2025-02-01 12:26:08'),
	(10, 2, 'Draft', '2025-02-01 12:27:30'),
	(11, 2, 'Active', '2025-02-01 12:28:34'),
	(12, 2, 'Active', '2025-02-01 12:30:25'),
	(13, 2, 'Active', '2025-02-01 12:34:42'),
	(14, 2, 'Active', '2025-02-01 12:35:14'),
	(15, 2, 'Active', '2025-02-01 12:40:10'),
	(16, 2, 'Active', '2025-02-01 14:04:13'),
	(17, 2, 'Active', '2025-02-01 20:56:42'),
	(21, 1, 'Draft', '2025-02-21 08:52:58'),
	(22, 4, 'Draft', '2025-02-21 08:54:35'),
	(23, 4, 'Sent', '2025-02-21 08:54:56'),
	(24, 4, 'Active', '2025-02-21 08:55:24'),
	(25, 4, 'Draft', '2025-02-21 08:56:35'),
	(26, 4, 'Expired', '2025-02-21 08:56:39'),
	(27, 4, 'Expired', '2025-02-21 08:56:45');

-- Dumping structure for table lead_platform.contract_types
CREATE TABLE IF NOT EXISTS `contract_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contract_types: ~0 rows (approximately)
REPLACE INTO `contract_types` (`id`, `name`, `created_at`) VALUES
	(1, 'Legal', '2025-02-01 07:54:34');

-- Dumping structure for table lead_platform.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_interaction` timestamp NULL DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `social_media_profiles` text COLLATE utf8mb4_general_ci,
  `age` int DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `industry` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.customers: ~5 rows (approximately)
REPLACE INTO `customers` (`id`, `name`, `email`, `phone`, `created_at`, `company`, `last_interaction`, `address`, `social_media_profiles`, `age`, `gender`, `location`, `job_title`, `industry`, `profile_picture`) VALUES
	(1, 'Jabbar2', 'jabbar@demo.com', '12312312', '2025-01-28 08:56:01', 'Jabbar Corporations', '2025-01-28 09:13:46', 'NYC', 'https://instagram.com', 44, 'Male', 'NYC', 'Chairman', 'IT', 'public/uploads/profile/6798a5f924b68_pexels-photo-771742.jpeg'),
	(2, 'POP', 'pop@pop.com', '123123', '2025-01-29 11:57:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(3, 'test', 'test@demo.com', '12345', '2025-02-06 04:01:23', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(4, 'TESTConvert2', 'asdt@demo.com', '1231231', '2025-02-14 15:44:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(5, 'testconvert4', 'asdasd@asdasdasd.com', '2312312313', '2025-02-14 15:46:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public/uploads/profile/67b4c05970cf7_angienotion-1707764852790.png');

-- Dumping structure for table lead_platform.customer_custom_fields
CREATE TABLE IF NOT EXISTS `customer_custom_fields` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `field_value` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_custom_fields_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.customer_custom_fields: ~0 rows (approximately)

-- Dumping structure for table lead_platform.customer_interactions
CREATE TABLE IF NOT EXISTS `customer_interactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `interaction_type` enum('Call','Email','Meeting','Other') COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `interaction_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_interactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.customer_interactions: ~4 rows (approximately)
REPLACE INTO `customer_interactions` (`id`, `customer_id`, `interaction_type`, `details`, `interaction_at`) VALUES
	(1, 1, 'Email', 'Customer wants to discuss more over call.', '2025-01-28 09:18:57'),
	(2, 1, 'Call', 'Customer wants to meet IRL', '2025-01-28 09:19:30'),
	(3, 1, 'Meeting', 'Meeting went great! Wants to discuss more!', '2025-01-28 09:29:58'),
	(4, 1, 'Email', 'He wants a landing page', '2025-01-30 01:47:38');

-- Dumping structure for table lead_platform.customer_preferences
CREATE TABLE IF NOT EXISTS `customer_preferences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `preference` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_preferences_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.customer_preferences: ~3 rows (approximately)
REPLACE INTO `customer_preferences` (`id`, `customer_id`, `preference`, `created_at`) VALUES
	(1, 1, 'Likes to talk', '2025-01-28 09:18:17'),
	(2, 1, 'Wears great suit', '2025-01-28 09:29:29'),
	(3, 1, 'blabbers', '2025-01-29 11:54:05');

-- Dumping structure for table lead_platform.customer_tags
CREATE TABLE IF NOT EXISTS `customer_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'gray',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `customer_tags_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.customer_tags: ~2 rows (approximately)
REPLACE INTO `customer_tags` (`id`, `customer_id`, `tag`, `color`, `created_at`) VALUES
	(1, 1, 'VIP', 'gray', '2025-01-28 09:25:13'),
	(2, 1, 'High value', 'red', '2025-01-28 09:30:26');

-- Dumping structure for table lead_platform.discussions
CREATE TABLE IF NOT EXISTS `discussions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `type` enum('internal','external') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.discussions: ~2 rows (approximately)
REPLACE INTO `discussions` (`id`, `title`, `user_id`, `type`, `status`, `created_at`) VALUES
	(1, 'Initial Documentation Request', 2, 'internal', 'closed', '2025-01-31 20:35:30'),
	(3, 'Legal Document Request', 2, 'external', 'open', '2025-01-31 21:10:19');

-- Dumping structure for table lead_platform.discussion_attachments
CREATE TABLE IF NOT EXISTS `discussion_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `discussion_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `discussion_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.discussion_attachments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.discussion_messages
CREATE TABLE IF NOT EXISTS `discussion_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `discussion_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_type` enum('user','employee','customer') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `parent_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discussion_id` (`discussion_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `discussion_messages_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discussion_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discussion_messages_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `discussion_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.discussion_messages: ~5 rows (approximately)
REPLACE INTO `discussion_messages` (`id`, `discussion_id`, `user_id`, `user_type`, `message`, `sent_at`, `parent_id`) VALUES
	(1, 1, 2, 'user', 'Hi Jabbar,\r\nPlease provide the initial documentation for your repo?', '2025-01-31 20:35:30', NULL),
	(3, 3, 2, 'user', 'Hi Jabbar,\r\nPlease send your document', '2025-01-31 21:10:19', NULL),
	(4, 3, 5, 'user', 'Where should I send it?', '2025-01-31 21:27:46', 3),
	(5, 1, 5, 'user', 'Sure!', '2025-01-31 21:59:26', NULL),
	(8, 3, 5, 'user', 'Sure', '2025-01-31 22:11:30', NULL);

-- Dumping structure for table lead_platform.discussion_participants
CREATE TABLE IF NOT EXISTS `discussion_participants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `discussion_id` int NOT NULL,
  `participant_id` int NOT NULL,
  `participant_type` enum('user','employee','customer') COLLATE utf8mb4_general_ci NOT NULL,
  `last_viewed` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `discussion_id` (`discussion_id`),
  KEY `participant_id` (`participant_id`),
  CONSTRAINT `discussion_participants_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.discussion_participants: ~6 rows (approximately)
REPLACE INTO `discussion_participants` (`id`, `discussion_id`, `participant_id`, `participant_type`, `last_viewed`, `created_at`) VALUES
	(1, 1, 1, 'user', NULL, '2025-01-31 20:35:30'),
	(2, 1, 1, 'employee', NULL, '2025-01-31 20:35:30'),
	(3, 1, 1, 'customer', NULL, '2025-01-31 20:35:30'),
	(7, 3, 4, 'user', NULL, '2025-01-31 21:10:19'),
	(8, 3, 2, 'employee', '2025-02-21 08:59:29', '2025-01-31 21:10:19'),
	(9, 3, 1, 'customer', NULL, '2025-01-31 21:10:19');

-- Dumping structure for table lead_platform.documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.documents: ~8 rows (approximately)
REPLACE INTO `documents` (`id`, `title`, `content`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'Collaborative Doc', '<p><strong>Personalised Face Swapped Image Generation &amp; Video Re-Dubbed Ad&nbsp;</strong><br><strong>Cost and Deliverables Breakdown</strong><br><br><br><strong>Software Deliverables: Video Re-Dubbed Ad Cost and Deliverables Breakdown</strong><br>&nbsp;</p><p><br><strong>Web : Microsite + ML Audio model + ML video lip sync model + video format</strong></p><p><strong>transcoding, video editing (trimming and concatenation)</strong><br>&nbsp;</p><p>1. <strong>Functionality: </strong>Enable the creation of personalized video advertisements where</p><p>the actor appears to say the user’s name and pronoun, providing a customized</p><p>user experience.<br>&nbsp;</p><p>2. <strong>Voice Consistency:</strong> A custom-trained speech model generates audio in the</p><p>actor’s voice, ensuring consistency in tone, pitch, and style.<br>&nbsp;</p><p>3. <strong>Lip-Syncing:</strong> Synchronize the generated audio with the actor’s lip movements</p><p>in the video to create a seamless and realistic personalized ad.<br><br><strong>Video Processing:</strong> Stitch the audio and video together, optimize the final</p><p>output for size, and deliver it to the user through a microsite with playback,</p><p>sharing, and download options.</p><p><br><strong>Software Deliverables: Face Swapped Image Generation</strong><br><br>&nbsp;</p><p><strong>Web: Face Generation System + Image Processing Tools</strong></p><p>&nbsp;</p><p>1. <strong>Functionality:</strong> Enable users to upload a selfie or image, which is then transformed into a personalized illustration with face-swapping onto a predefined character/artwork.<br>&nbsp;</p><p>2. <strong>Face Swapping:</strong> A custom-trained ML model swaps the user’s face onto the target illustration while preserving facial features, expressions, and lighting consistency.</p><p><br>3. <strong>Artwork Integration:</strong> Seamlessly attach the swapped face to the illustration and combine all elements (e.g., background, accessories) to finalize the artwork.</p><p><br><strong>Technical Workflow: Video Re-Dubbed Ad</strong><br>&nbsp;</p><p><strong>Step 1: User Input Collection</strong><br>&nbsp;</p><p><strong>Input:</strong> The user provides their name and gender through a web-based interface.</p><p><strong>Process:</strong><br>&nbsp;</p><p>1. Validate the user input (e.g., name length, allowed characters).</p><p>2. Determine the appropriate pronoun based on the selected gender.<br>&nbsp;</p><p><strong>Output:</strong> User’s name and pronoun, ready for audio generation.<br>&nbsp;</p><p><strong>Step 2: Audio Generation Using TTS</strong></p><p><strong>Process:</strong><br>&nbsp;</p><p>3. Use a custom-trained speech / audio model to generate audio of the actor</p><p>saying the user’s name and pronoun.<br>4. Ensure the generated audio matches the actor’s voice in tone, pitch, and</p><p>emotional delivery.<br>&nbsp;</p><p><strong>Output:</strong> A personalized audio file with the actor’s voice.<br>&nbsp;</p><p><strong>Step 3: Lip-Syncing the Audio with Video</strong></p><p><strong>Process:</strong><br>&nbsp;</p><p>5. Integrate the generated audio with the pre-recorded video of the actor.</p><p>6. Use a custom deep learning-based lip-syncing model to align the actor’s lip</p><p>movements with the new audio.</p><p>7. Automatically adjust video elements to ensure smooth synchronization.<br>&nbsp;</p><p><strong>Output:</strong> A synchronized video where the actor appears to say the user’s name and</p><p>pronoun.<br>&nbsp;</p><p><strong>Step 4: Video Stitching and Optimization</strong></p><p><strong>Process:</strong><br>&nbsp;</p><p>8. Stitch the synchronized audio and video together to create a seamless</p><p>personalized video ad.</p><p>9. Compress and optimize the video file for efficient streaming and download.</p><p><br><strong>Output:</strong> A finalized, optimized video ad ready for delivery.</p><p><br><strong>Step 5: User Delivery and Interaction</strong></p><p><strong>Process:</strong><br>&nbsp;</p><p>1. Display the personalized video ad on the microsite’s built-in video player for</p><p>immediate playback.</p><p>2. Provide options for the user to:</p><p>- Share the video directly to social media platforms.</p><p>- Download the video in a standard format (e.g., MP4).</p><p>3. Use a secure, scalable file delivery system (e.g., AWS S3, Cloudflare R2) for</p><p>hosting and distribution.<br>&nbsp;</p><p>• <strong>Output:</strong> A personalized video ad experience with playback, sharing, and</p><p>download capabilities.<br><br><br>&nbsp;</p><p><strong>Technical Workflow : Personalised Face Swapped Image Generation</strong></p><p>&nbsp;</p><p><strong>Step 1: Source Image Preparation</strong></p><p>&nbsp;</p><p><strong>Input:</strong> Predefined illustration template with a base face for swapping.</p><p>Process:<br>&nbsp;</p><p>1. Validate and preprocess the source image (e.g., detect face coordinates, adjust resolution).</p><p>2. Extract facial landmarks and lighting data from the source template.</p><p><br><strong>Output: </strong>Source face data ready for swapping.<br>&nbsp;</p><p><strong>Step 2: User Image Upload &amp; Face Detection</strong></p><p><br>&nbsp;</p><p><strong>Input:</strong> User uploads a selfie/image via the microsite.</p><p><br><strong>Process:</strong><br>&nbsp;</p><p>1. Validate image quality (resolution, brightness, face visibility).<br>2. Detect and extract the user’s face, including landmarks, expressions, and orientation.<br>3. Output: User’s facial data extracted for swapping.</p><p><br><strong>Step 3: Face Swapping via ML Model</strong></p><p>&nbsp;</p><p><strong>Process:</strong><br>&nbsp;</p><p>1. Use face-swapping model to align the user’s face with the source template’s structure.<br>2. Adjust skin tone, lighting, and shadows to match the illustration’s style.<br>&nbsp;</p><p><strong>Output:</strong> A face-swapped image integrated into the source template.</p><p><br><strong>Step 4: Artwork Finalization</strong></p><p>&nbsp;</p><p><strong>Process:</strong><br>&nbsp;</p><p>1. Blend the swapped face with the illustration’s elements (e.g., hair, accessories, background).<br>2. Apply post-processing filters to ensure visual consistency (e.g., color correction, sharpening).<br>&nbsp;</p><p><strong>Output:</strong> A finalized, personalized illustration.</p><p><br><strong>Step 5: User Delivery</strong></p><p>&nbsp;</p><p>Process:</p><p><br>1. Display the generated artwork on the microsite as fixed image on to the Re-dubbed video output.</p><p>2. Deliver the final video that which can be either played back within the site ( in-built player or as downloadable content)<br><br><br><strong>Cost Breakdown:</strong><br>&nbsp;</p><p><strong>A. Audio Generation Cost: </strong>₹5 per audio generation.</p><p><strong>B. Video Lip-Syncing Generation Cost:</strong> ₹15 per video generation.</p><p><strong>C. Image Generation + Face Swapping Cost:</strong> ₹10 per generation (Image + faceswap)</p><p><br><strong>A + B = Development Cost: </strong>₹6,50,000 (one-time).</p><p><strong>C: Development Cost:</strong> ₹3,00,000 (one-time).<br><br><strong>Total Development Cost: </strong>₹9,50,000<br><br><strong>Total Generation Cost (A+B+C):</strong> ₹30 Final Output<br>&nbsp;</p>', 2, '2025-02-21 06:53:20', '2025-02-26 17:38:40'),
	(2, 'test2', '<figure class="table"><table><tbody><tr><td>&nbsp;</td><td>test</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>f</td><td>&nbsp;</td><td>sdf</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>sdf</td><td>&nbsp;</td><td>sd</td><td>&nbsp;</td><td>df</td><td>test</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>df</td><td>s</td><td>sdf</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table></figure><p>Test</p><p>This works, right? asdasdas</p>', 2, '2025-02-21 12:49:44', '2025-02-24 18:10:57'),
	(3, 'test', '<p>Hello there Fellas!</p>', 5, '2025-02-21 15:14:06', '2025-02-22 19:03:51'),
	(4, 'collab bro', '<p>&lt;p&gt;wsoon this stuff&amp;nbsp;&lt;/p&gt;</p>', 5, '2025-02-21 15:14:51', '2025-02-21 15:15:18'),
	(5, 'tes', 'df', 2, '2025-02-21 22:14:01', NULL),
	(6, 'test', '<h2>Meeting Notes</h2><h2>Attendees</h2><ul><li>Name, Title</li></ul><h2>Agenda</h2><ul><li>Topic 1</li><li>Topic 2</li></ul><h2>Discussion</h2><p>Key points and action items...</p>', 2, '2025-02-21 22:15:52', NULL),
	(7, 'Portfolio Test', '<p>.portfolio { font-family: Arial, sans-serif; padding: 20px; } .portfolio-grid { display: flex; flex-wrap: wrap; gap: 20px; } .portfolio-item { flex: 1 1 calc(33% - 20px); border: 1px solid #ccc; padding: 10px; }&nbsp;</p><h2>Portfolio</h2><h2>Project 1</h2><p>Description of project 1...</p><h2>Project 2</h2><p>Description of project 2...</p><h2>Project 3</h2><p>Description of project 3...</p>', 2, '2025-02-21 22:20:19', '2025-02-21 22:20:54'),
	(8, 'ytyt', '<h2>Monthly Newsletter</h2><p>Welcome to our monthly newsletter. Here are the</p><p>&nbsp;</p><p>&nbsp;highlights...</p>', 2, '2025-02-21 23:48:12', '2025-02-22 13:08:28');

-- Dumping structure for table lead_platform.document_collaborators
CREATE TABLE IF NOT EXISTS `document_collaborators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_id` int NOT NULL,
  `user_id` int NOT NULL,
  `permissions` enum('read','write') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'read',
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_user` (`document_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `document_collaborators_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_collaborators_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.document_collaborators: ~2 rows (approximately)
REPLACE INTO `document_collaborators` (`id`, `document_id`, `user_id`, `permissions`) VALUES
	(6, 2, 5, 'read'),
	(8, 3, 2, 'read');

-- Dumping structure for table lead_platform.document_versions
CREATE TABLE IF NOT EXISTS `document_versions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_id` int NOT NULL,
  `version` int NOT NULL DEFAULT '1',
  `content` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.document_versions: ~0 rows (approximately)
REPLACE INTO `document_versions` (`id`, `document_id`, `version`, `content`, `created_at`, `user_id`) VALUES
	(1, 2, 1, '<p>test Doc cone</p>', '2025-02-21 12:49:44', 2);

-- Dumping structure for table lead_platform.drawing_boards
CREATE TABLE IF NOT EXISTS `drawing_boards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `elements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `drawing_boards_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.drawing_boards: ~4 rows (approximately)
REPLACE INTO `drawing_boards` (`id`, `title`, `elements`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'test', NULL, 2, '2025-02-23 10:28:07', NULL),
	(2, 'test', '{"elements":[{"type":"text","version":4,"versionNonce":678103853,"isDeleted":true,"id":"Ta7uBD_Jcd3TcCg-rM7BU","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":79.27081298828125,"y":629.3333435058594,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":10,"height":25,"seed":737135021,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740431389338,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"center","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1},{"type":"text","version":2,"versionNonce":1552726275,"isDeleted":true,"id":"fvsfGDs-2vBvZv65jrbDT","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":229.27081298828125,"y":442,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":10,"height":25,"seed":500818925,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740431391207,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"center","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1}],"appState":{"showWelcomeScreen":true,"theme":"light","collaborators":[],"currentChartType":"bar","currentItemBackgroundColor":"transparent","currentItemEndArrowhead":"arrow","currentItemFillStyle":"solid","currentItemFontFamily":1,"currentItemFontSize":20,"currentItemOpacity":100,"currentItemRoughness":1,"currentItemStartArrowhead":null,"currentItemStrokeColor":"#1e1e1e","currentItemRoundness":"round","currentItemStrokeStyle":"solid","currentItemStrokeWidth":2,"currentItemTextAlign":"center","cursorButton":"up","activeEmbeddable":null,"draggingElement":null,"editingElement":null,"editingGroupId":null,"editingLinearElement":null,"activeTool":{"type":"selection","customType":null,"locked":false,"lastActiveTool":null},"penMode":false,"penDetected":false,"errorMessage":null,"exportBackground":true,"exportScale":1,"exportEmbedScene":false,"exportWithDarkMode":false,"fileHandle":null,"gridSize":null,"isBindingEnabled":true,"defaultSidebarDockedPreference":false,"isLoading":false,"isResizing":false,"isRotating":false,"lastPointerDownWith":"mouse","multiElement":null,"name":"Untitled-2025-02-25-0239","contextMenu":null,"openMenu":null,"openPopup":null,"openSidebar":null,"openDialog":null,"pasteDialog":{"shown":false,"data":null},"previousSelectedElementIds":{},"resizingElement":null,"scrolledOutside":false,"scrollX":0,"scrollY":600,"selectedElementIds":{},"selectedGroupIds":{},"selectedElementsAreBeingDragged":false,"selectionElement":null,"shouldCacheIgnoreZoom":false,"showStats":false,"startBoundElement":null,"suggestedBindings":[],"frameRendering":{"enabled":true,"clip":true,"name":true,"outline":true},"frameToHighlight":null,"editingFrame":null,"elementsToHighlight":null,"toast":null,"viewBackgroundColor":"#ffffff","zenModeEnabled":false,"zoom":{"value":1},"viewModeEnabled":false,"pendingImageElementId":null,"showHyperlinkPopup":false,"selectedLinearElement":null,"snapLines":[],"originSnapOffset":null,"objectsSnapModeEnabled":false,"offsetLeft":923.7291870117188,"offsetTop":284,"width":964.53125,"height":1049.8646240234375}}', 2, '2025-02-23 10:42:27', '2025-02-24 21:19:43'),
	(3, 'asd', '{"elements":[{"type":"diamond","version":34,"versionNonce":785661395,"isDeleted":true,"id":"8ctBVelLaCtTrjEow_zNx","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":321.60418701171875,"y":135.33331298828125,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":45.3333740234375,"height":109.3333740234375,"seed":1399492541,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740311794193,"link":null,"locked":false},{"type":"diamond","version":58,"versionNonce":99974365,"isDeleted":true,"id":"MrRcQi-s5RsBtJTeRwHfX","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":455.60418701171875,"y":134.66668701171875,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":184.6666259765625,"height":56,"seed":1876312115,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740311794193,"link":null,"locked":false},{"type":"freedraw","version":297,"versionNonce":466815859,"isDeleted":true,"id":"zxTFR-kclQNkPGEtX7hYt","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":602.2708129882812,"y":232.66668701171875,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":395.333251953125,"height":186,"seed":1826090195,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740311794193,"link":null,"locked":false,"points":[[0,0],[-0.6666259765625,0],[-5.333251953125,-1.3333740234375],[-9.333251953125,-2.66668701171875],[-15.333251953125,-4],[-23.333251953125,-6],[-31.333251953125,-7.3333740234375],[-40.6666259765625,-10],[-50.6666259765625,-12],[-58.6666259765625,-14],[-66,-15.3333740234375],[-73.333251953125,-16.66668701171875],[-82,-18],[-87.333251953125,-19.3333740234375],[-90,-19.3333740234375],[-92.6666259765625,-20],[-94,-20],[-96,-20],[-96.6666259765625,-20],[-97.333251953125,-20],[-98,-20],[-99.333251953125,-20],[-101.333251953125,-20],[-102.6666259765625,-20],[-107.333251953125,-19.3333740234375],[-110.6666259765625,-18.66668701171875],[-114,-18.66668701171875],[-119.333251953125,-18],[-123.333251953125,-18],[-127.333251953125,-18],[-130.6666259765625,-18],[-132.6666259765625,-18],[-134,-18],[-134.6666259765625,-18],[-136,-18],[-137.333251953125,-18],[-138,-18],[-138.6666259765625,-18],[-138.6666259765625,-17.3333740234375],[-139.333251953125,-16.66668701171875],[-140.6666259765625,-16],[-141.333251953125,-14],[-142,-12.66668701171875],[-142.6666259765625,-12.66668701171875],[-144,-11.3333740234375],[-144.6666259765625,-10],[-146,-9.3333740234375],[-146,-8.66668701171875],[-146.6666259765625,-8.66668701171875],[-147.333251953125,-8],[-148,-8],[-148,-7.3333740234375],[-148.6666259765625,-6.66668701171875],[-148.6666259765625,-6],[-149.333251953125,-5.3333740234375],[-150,-5.3333740234375],[-150,-4.66668701171875],[-150.6666259765625,-4.66668701171875],[-151.333251953125,-4],[-152,-3.3333740234375],[-153.333251953125,-2.66668701171875],[-154.6666259765625,-1.3333740234375],[-156.6666259765625,-0.66668701171875],[-158.6666259765625,2],[-161.333251953125,3.33331298828125],[-163.333251953125,6],[-165.333251953125,7.33331298828125],[-168.6666259765625,10],[-170,11.33331298828125],[-172,12.6666259765625],[-173.333251953125,14],[-173.333251953125,14.6666259765625],[-174,14.6666259765625],[-175.333251953125,15.33331298828125],[-176,16],[-177.333251953125,16.6666259765625],[-178,18],[-180,18.6666259765625],[-181.333251953125,19.33331298828125],[-182.6666259765625,21.33331298828125],[-184,22],[-185.333251953125,22.6666259765625],[-186.6666259765625,23.33331298828125],[-188,24],[-188.6666259765625,24],[-189.333251953125,24.6666259765625],[-190.6666259765625,25.33331298828125],[-191.333251953125,25.33331298828125],[-192.6666259765625,26],[-194.6666259765625,26],[-196,26],[-196.6666259765625,26.6666259765625],[-197.333251953125,26.6666259765625],[-198.6666259765625,27.33331298828125],[-200,28],[-200.6666259765625,28.6666259765625],[-202,30],[-204,30],[-204.6666259765625,30.6666259765625],[-206.6666259765625,32],[-208,32.6666259765625],[-210,33.33331298828125],[-212,34],[-215.333251953125,34.6666259765625],[-218.6666259765625,35.33331298828125],[-223.333251953125,36.6666259765625],[-228.6666259765625,38.6666259765625],[-233.333251953125,40],[-238,40.6666259765625],[-244,43.33331298828125],[-248,44.6666259765625],[-253.333251953125,46],[-258,48.6666259765625],[-262,49.33331298828125],[-265.333251953125,50],[-268.6666259765625,51.33331298828125],[-272,52],[-275.333251953125,52.6666259765625],[-278,53.33331298828125],[-281.333251953125,53.33331298828125],[-285.333251953125,53.33331298828125],[-289.333251953125,53.33331298828125],[-293.333251953125,54],[-296.6666259765625,54.6666259765625],[-300.6666259765625,54.6666259765625],[-304,55.33331298828125],[-307.333251953125,56],[-311.333251953125,56.6666259765625],[-315.333251953125,56.6666259765625],[-320,58],[-324,58.6666259765625],[-329.333251953125,59.33331298828125],[-333.333251953125,60],[-337.333251953125,60.6666259765625],[-341.333251953125,60.6666259765625],[-344.6666259765625,60.6666259765625],[-346,60.6666259765625],[-348,60.6666259765625],[-350,60.6666259765625],[-350.6666259765625,60.6666259765625],[-352.6666259765625,59.33331298828125],[-354.6666259765625,58.6666259765625],[-356.6666259765625,57.33331298828125],[-358.6666259765625,55.33331298828125],[-360.6666259765625,54],[-362,51.33331298828125],[-364,49.33331298828125],[-366,46.6666259765625],[-367.333251953125,43.33331298828125],[-370,39.33331298828125],[-372,32.6666259765625],[-376,26],[-378,21.33331298828125],[-381.333251953125,13.33331298828125],[-385.333251953125,4.6666259765625],[-388.6666259765625,-2.66668701171875],[-391.333251953125,-8.66668701171875],[-393.333251953125,-12.66668701171875],[-394.6666259765625,-16.66668701171875],[-395.333251953125,-18.66668701171875],[-395.333251953125,-21.3333740234375],[-395.333251953125,-23.3333740234375],[-395.333251953125,-24.66668701171875],[-395.333251953125,-27.3333740234375],[-394.6666259765625,-29.3333740234375],[-393.333251953125,-32.66668701171875],[-392.6666259765625,-34.66668701171875],[-391.333251953125,-37.3333740234375],[-389.333251953125,-40.66668701171875],[-388.6666259765625,-42],[-387.333251953125,-44],[-385.333251953125,-46],[-384.6666259765625,-48],[-382,-50],[-379.333251953125,-52],[-376.6666259765625,-53.3333740234375],[-373.333251953125,-54.66668701171875],[-370,-56],[-367.333251953125,-56.66668701171875],[-364.6666259765625,-58],[-361.333251953125,-58.66668701171875],[-358,-58.66668701171875],[-355.333251953125,-58.66668701171875],[-352.6666259765625,-59.3333740234375],[-349.333251953125,-59.3333740234375],[-345.333251953125,-59.3333740234375],[-342,-59.3333740234375],[-338,-59.3333740234375],[-334,-59.3333740234375],[-330.6666259765625,-59.3333740234375],[-327.333251953125,-59.3333740234375],[-324,-59.3333740234375],[-322,-59.3333740234375],[-320,-59.3333740234375],[-318,-59.3333740234375],[-316,-59.3333740234375],[-314.6666259765625,-59.3333740234375],[-312,-58.66668701171875],[-310.6666259765625,-58.66668701171875],[-309.333251953125,-58.66668701171875],[-307.333251953125,-58],[-306,-57.3333740234375],[-304,-56.66668701171875],[-302,-56.66668701171875],[-300.6666259765625,-56.66668701171875],[-299.333251953125,-56.66668701171875],[-296.6666259765625,-56.66668701171875],[-294.6666259765625,-56.66668701171875],[-292.6666259765625,-56.66668701171875],[-289.333251953125,-56.66668701171875],[-287.333251953125,-56.66668701171875],[-285.333251953125,-58],[-284.6666259765625,-58.66668701171875],[-282.6666259765625,-60],[-281.333251953125,-61.3333740234375],[-280.6666259765625,-62.66668701171875],[-280,-64.66668701171875],[-279.333251953125,-68],[-278.6666259765625,-70.66668701171875],[-278,-74.66668701171875],[-278,-78.66668701171875],[-278,-82],[-278,-86],[-278,-89.3333740234375],[-278,-94],[-277.333251953125,-98],[-277.333251953125,-100.66668701171875],[-276.6666259765625,-104],[-276,-106],[-276,-108.00003051757812],[-275.333251953125,-110.00003051757812],[-275.333251953125,-111.3333740234375],[-274.6666259765625,-113.3333740234375],[-274.6666259765625,-114.00003051757812],[-274,-114.00003051757812],[-274,-114.66668701171875],[-274,-115.3333740234375],[-273.333251953125,-116.00003051757812],[-273.333251953125,-116.66668701171875],[-272.6666259765625,-118.00003051757812],[-272,-118.00003051757812],[-270.6666259765625,-118.66668701171875],[-270,-120.00003051757812],[-269.333251953125,-120.66668701171875],[-268.6666259765625,-121.3333740234375],[-267.333251953125,-122.00003051757812],[-266.6666259765625,-122.00003051757812],[-265.333251953125,-122.66668701171875],[-264.6666259765625,-123.3333740234375],[-263.333251953125,-123.3333740234375],[-261.333251953125,-123.3333740234375],[-260.6666259765625,-124.00003051757812],[-258,-124.00003051757812],[-256,-124.00003051757812],[-251.333251953125,-124.00003051757812],[-247.333251953125,-124.00003051757812],[-242.6666259765625,-124.66668701171875],[-238,-124.66668701171875],[-233.333251953125,-124.66668701171875],[-230,-124.66668701171875],[-224,-124.66668701171875],[-220.6666259765625,-124.66668701171875],[-213.333251953125,-124.66668701171875],[-210,-124.66668701171875],[-205.333251953125,-124.66668701171875],[-200,-124.66668701171875],[-196,-124.66668701171875],[-192,-124.66668701171875],[-188.6666259765625,-125.3333740234375],[-186.6666259765625,-125.3333740234375],[-184.6666259765625,-125.3333740234375],[-183.333251953125,-125.3333740234375],[-182.6666259765625,-125.3333740234375],[-180.6666259765625,-125.3333740234375],[-179.333251953125,-125.3333740234375],[-178,-125.3333740234375],[-176,-125.3333740234375],[-174,-124.66668701171875],[-170.6666259765625,-124.00003051757812],[-167.333251953125,-123.3333740234375],[-163.333251953125,-122.66668701171875],[-158.6666259765625,-122.00003051757812],[-154,-121.3333740234375],[-149.333251953125,-121.3333740234375],[-142.6666259765625,-120.00003051757812],[-134.6666259765625,-119.3333740234375],[-129.333251953125,-118.66668701171875],[-122.6666259765625,-118.00003051757812],[-116.6666259765625,-116.66668701171875],[-111.333251953125,-116.00003051757812],[-107.333251953125,-116.00003051757812],[-104,-116.00003051757812],[-102,-116.00003051757812],[-100,-116.00003051757812],[-100,-116.00003051757812]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":198,"versionNonce":1299135261,"isDeleted":false,"id":"zzm7mAT0Th9b4xYVFw5IE","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":282.93756103515625,"y":234,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":600,"height":98,"seed":1189553427,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740311796374,"link":null,"locked":false,"points":[[0,0],[0,-0.66668701171875],[0.6666259765625,-0.66668701171875],[1.333251953125,-0.66668701171875],[1.333251953125,-1.33331298828125],[2,-2],[2.6666259765625,-4.66668701171875],[4,-7.33331298828125],[5.333251953125,-12.66668701171875],[7.333251953125,-18.66668701171875],[10,-26.66668701171875],[15.333251953125,-34.66668701171875],[18.6666259765625,-39.33331298828125],[24,-46],[30,-51.33331298828125],[36.6666259765625,-56.66668701171875],[45.333251953125,-62],[57.333251953125,-66],[72.6666259765625,-68.66668701171875],[87.333251953125,-70],[102.6666259765625,-70],[123.333251953125,-70],[142.6666259765625,-68],[159.333251953125,-64.66668701171875],[180.6666259765625,-56],[188.6666259765625,-52],[230.6666259765625,-25.33331298828125],[248,-12.66668701171875],[263.333251953125,-1.33331298828125],[275.333251953125,6],[282.6666259765625,10],[289.333251953125,12.66668701171875],[293.333251953125,14],[295.333251953125,14.66668701171875],[296,14.66668701171875],[297.333251953125,13.33331298828125],[298.6666259765625,11.33331298828125],[299.333251953125,8],[299.333251953125,3.33331298828125],[299.333251953125,-1.33331298828125],[299.333251953125,-5.33331298828125],[297.333251953125,-10.66668701171875],[294.6666259765625,-16.66668701171875],[290.6666259765625,-20.66668701171875],[285.333251953125,-26.66668701171875],[277.333251953125,-32],[270,-35.33331298828125],[262,-38],[251.333251953125,-40],[240,-42],[228.6666259765625,-42],[217.333251953125,-39.33331298828125],[204,-30.66668701171875],[198,-25.33331298828125],[190,-16],[183.333251953125,-4.66668701171875],[180,6],[179.333251953125,14],[179.333251953125,19.33331298828125],[180.6666259765625,22],[184,24.66668701171875],[186.6666259765625,26.66668701171875],[190,27.33331298828125],[195.333251953125,27.33331298828125],[203.333251953125,26.66668701171875],[216,22],[222,19.33331298828125],[237.333251953125,12.66668701171875],[250,4.66668701171875],[260,-2.66668701171875],[264.6666259765625,-6.66668701171875],[268.6666259765625,-14],[270,-19.33331298828125],[270,-24.66668701171875],[267.333251953125,-28.66668701171875],[261.333251953125,-33.33331298828125],[257.333251953125,-37.33331298828125],[248.6666259765625,-40.66668701171875],[240.6666259765625,-42.66668701171875],[230,-44.66668701171875],[216.6666259765625,-45.33331298828125],[207.333251953125,-45.33331298828125],[198,-42.66668701171875],[190,-36],[186.6666259765625,-31.33331298828125],[183.333251953125,-22.66668701171875],[183.333251953125,-15.33331298828125],[186,-9.33331298828125],[190,-4],[196,1.33331298828125],[201.333251953125,5.33331298828125],[208.6666259765625,8.66668701171875],[215.333251953125,10],[222.6666259765625,11.33331298828125],[230.6666259765625,11.33331298828125],[238,11.33331298828125],[246.6666259765625,9.33331298828125],[254,6.66668701171875],[260,4],[264.6666259765625,1.33331298828125],[267.333251953125,-1.33331298828125],[270,-3.33331298828125],[272,-6],[272,-8],[272,-9.33331298828125],[271.333251953125,-10],[270.6666259765625,-10.66668701171875],[268.6666259765625,-11.33331298828125],[267.333251953125,-11.33331298828125],[265.333251953125,-12],[262.6666259765625,-12],[260.6666259765625,-10.66668701171875],[259.333251953125,-10],[258.6666259765625,-8.66668701171875],[258.6666259765625,-6.66668701171875],[258.6666259765625,-5.33331298828125],[260,-4],[262.6666259765625,-3.33331298828125],[265.333251953125,-2],[272.6666259765625,0],[277.333251953125,0],[284,0.66668701171875],[290.6666259765625,0.66668701171875],[297.333251953125,0.66668701171875],[302,0.66668701171875],[304.6666259765625,-0.66668701171875],[306,-2],[306.6666259765625,-2],[306.6666259765625,-2.66668701171875],[305.333251953125,-3.33331298828125],[302.6666259765625,-4],[300,-4],[294.6666259765625,-4],[289.333251953125,-4],[285.333251953125,-4],[282,-2.66668701171875],[279.333251953125,-2],[278,-0.66668701171875],[278,1.33331298828125],[278,2],[278.6666259765625,3.33331298828125],[280.6666259765625,4.66668701171875],[284,6],[286,7.33331298828125],[289.333251953125,8],[292,8.66668701171875],[292.6666259765625,8.66668701171875],[290.6666259765625,8.66668701171875],[287.333251953125,9.33331298828125],[280,10],[272.6666259765625,10.66668701171875],[266.6666259765625,10.66668701171875],[256,10.66668701171875],[248,12.66668701171875],[239.333251953125,12.66668701171875],[233.333251953125,13.33331298828125],[228,14.66668701171875],[224.6666259765625,15.33331298828125],[222,15.33331298828125],[220,16],[218,17.33331298828125],[216.6666259765625,17.33331298828125],[216,17.33331298828125],[215.333251953125,17.33331298828125],[214.6666259765625,17.33331298828125],[214,18],[213.333251953125,20],[211.333251953125,20.66668701171875],[209.333251953125,21.33331298828125],[207.333251953125,22.66668701171875],[206,24.66668701171875],[203.333251953125,25.33331298828125],[202,26.66668701171875],[202,27.33331298828125],[202,28],[205.333251953125,28],[213.333251953125,28],[232,28],[253.333251953125,26.66668701171875],[287.333251953125,24.66668701171875],[335.333251953125,21.33331298828125],[361.333251953125,19.33331298828125],[404.6666259765625,14.66668701171875],[448.6666259765625,8.66668701171875],[489.333251953125,2],[520.6666259765625,-2],[554.6666259765625,-8],[571.333251953125,-11.33331298828125],[587.333251953125,-14],[598,-16],[600,-17.33331298828125],[599.333251953125,-17.33331298828125],[598.6666259765625,-17.33331298828125],[598,-17.33331298828125],[597.333251953125,-17.33331298828125],[596.6666259765625,-16.66668701171875],[596.6666259765625,-16.66668701171875]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":89,"versionNonce":1076833320,"isDeleted":false,"id":"OrwXyGwoqCLnQxwKuUxSF","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":154.93756103515625,"y":284,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":285.333251953125,"height":24.66668701171875,"seed":1440436824,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740311938771,"link":null,"locked":false,"points":[[0,0],[1.333251953125,-0.66668701171875],[2,-0.66668701171875],[3.333251953125,-0.66668701171875],[4,-0.66668701171875],[11.333251953125,-0.66668701171875],[20,0],[30,0.66668701171875],[43.333251953125,2],[62.6666259765625,3.33331298828125],[78,5.33331298828125],[88.6666259765625,5.33331298828125],[100.6666259765625,5.33331298828125],[106.6666259765625,5.33331298828125],[110,4.66668701171875],[110,4],[110,3.33331298828125],[107.333251953125,2.66668701171875],[101.333251953125,1.33331298828125],[94.6666259765625,-0.66668701171875],[86.6666259765625,-1.33331298828125],[78,-2],[70,-2],[64,-2],[59.333251953125,-0.66668701171875],[56.6666259765625,0.66668701171875],[54.6666259765625,1.33331298828125],[54.6666259765625,2.66668701171875],[54.6666259765625,3.33331298828125],[55.333251953125,4],[58,6],[63.333251953125,8.66668701171875],[70,11.33331298828125],[78,12.66668701171875],[88.6666259765625,13.33331298828125],[96,13.33331298828125],[103.333251953125,13.33331298828125],[110,12.66668701171875],[114.6666259765625,11.33331298828125],[116.6666259765625,10.66668701171875],[118.6666259765625,9.33331298828125],[120,9.33331298828125],[125.333251953125,9.33331298828125],[130.6666259765625,9.33331298828125],[138,10],[146.6666259765625,10],[156.6666259765625,10],[164.6666259765625,10],[168,10],[172,10],[175.333251953125,10],[178.6666259765625,10.66668701171875],[179.333251953125,10.66668701171875],[181.333251953125,10.66668701171875],[182.6666259765625,11.33331298828125],[184,12.66668701171875],[186.6666259765625,12.66668701171875],[190,14.66668701171875],[194.6666259765625,16],[201.333251953125,17.33331298828125],[208.6666259765625,18.66668701171875],[213.333251953125,19.33331298828125],[222.6666259765625,20],[231.333251953125,20.66668701171875],[241.333251953125,20.66668701171875],[244,20.66668701171875],[252,20.66668701171875],[256,20.66668701171875],[258,20],[260,20],[261.333251953125,20],[262,20],[262.6666259765625,20],[263.333251953125,20],[264,20],[264.6666259765625,20],[265.333251953125,20],[266,20.66668701171875],[266,21.33331298828125],[267.333251953125,21.33331298828125],[268,22.66668701171875],[269.333251953125,22.66668701171875],[272,22.66668701171875],[274.6666259765625,22.66668701171875],[278.6666259765625,22.66668701171875],[282,22.66668701171875],[285.333251953125,22.66668701171875],[285.333251953125,22.66668701171875]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":59,"versionNonce":1047405864,"isDeleted":false,"id":"HGP-U-AWTY810QSEZgpbw","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":585.6041870117188,"y":308.66668701171875,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":286.6666259765625,"height":18.66668701171875,"seed":589561688,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740311939496,"link":null,"locked":false,"points":[[0,0],[14,0],[17.3333740234375,0],[21.3333740234375,0],[27.3333740234375,0],[34.6666259765625,0],[42,0],[49.3333740234375,0],[58,0],[68,0],[76,-1.3333740234375],[83.3333740234375,-2.66668701171875],[91.3333740234375,-4],[97.3333740234375,-5.3333740234375],[103.3333740234375,-6.66668701171875],[108.6666259765625,-8.66668701171875],[111.3333740234375,-10],[112.6666259765625,-12],[114,-12.66668701171875],[114.6666259765625,-13.3333740234375],[115.3333740234375,-14],[116,-14],[117.3333740234375,-14],[122,-11.3333740234375],[127.3333740234375,-9.3333740234375],[136.6666259765625,-6.66668701171875],[143.3333740234375,-5.3333740234375],[161.3333740234375,-2.66668701171875],[179.3333740234375,-1.3333740234375],[192,-1.3333740234375],[208,-1.3333740234375],[222,-1.3333740234375],[234,-1.3333740234375],[242,-2.66668701171875],[247.3333740234375,-4],[252,-4.66668701171875],[254,-6.66668701171875],[254.6666259765625,-6.66668701171875],[256.6666259765625,-6.66668701171875],[257.3333740234375,-7.3333740234375],[258.6666259765625,-8.66668701171875],[260.6666259765625,-8.66668701171875],[263.3333740234375,-9.3333740234375],[266.6666259765625,-9.3333740234375],[269.3333740234375,-9.3333740234375],[273.3333740234375,-10],[278,-11.3333740234375],[282,-12],[284,-14],[285.3333740234375,-15.3333740234375],[286.6666259765625,-16],[286.6666259765625,-16.66668701171875],[286.6666259765625,-18],[286.6666259765625,-18.66668701171875],[285.3333740234375,-18.66668701171875],[284.6666259765625,-18.66668701171875],[283.3333740234375,-18.66668701171875],[283.3333740234375,-18.66668701171875]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"text","version":3,"versionNonce":870888792,"isDeleted":true,"id":"Cz4l8hQbeMIhIQ-vQrsZa","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":266.93756103515625,"y":189.33331298828125,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":10,"height":25,"seed":1408382552,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740311952973,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"left","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1}],"appState":{"showWelcomeScreen":false,"theme":"light","collaborators":[],"currentChartType":"bar","currentItemBackgroundColor":"transparent","currentItemEndArrowhead":"arrow","currentItemFillStyle":"solid","currentItemFontFamily":1,"currentItemFontSize":20,"currentItemOpacity":100,"currentItemRoughness":1,"currentItemStartArrowhead":null,"currentItemStrokeColor":"#1e1e1e","currentItemRoundness":"round","currentItemStrokeStyle":"solid","currentItemStrokeWidth":2,"currentItemTextAlign":"left","cursorButton":"up","activeEmbeddable":null,"draggingElement":null,"editingElement":null,"editingGroupId":null,"editingLinearElement":null,"activeTool":{"type":"selection","customType":null,"locked":false,"lastActiveTool":null},"penMode":false,"penDetected":false,"errorMessage":null,"exportBackground":true,"exportScale":1,"exportEmbedScene":false,"exportWithDarkMode":false,"fileHandle":null,"gridSize":null,"isBindingEnabled":true,"defaultSidebarDockedPreference":false,"isLoading":false,"isResizing":false,"isRotating":false,"lastPointerDownWith":"mouse","multiElement":null,"name":"Untitled-2025-02-23-1726","contextMenu":null,"openMenu":null,"openPopup":null,"openSidebar":null,"openDialog":null,"pasteDialog":{"shown":false,"data":null},"previousSelectedElementIds":{},"resizingElement":null,"scrolledOutside":false,"scrollX":0,"scrollY":0,"selectedElementIds":{},"selectedGroupIds":{},"selectedElementsAreBeingDragged":false,"selectionElement":null,"shouldCacheIgnoreZoom":false,"showStats":false,"startBoundElement":null,"suggestedBindings":[],"frameRendering":{"enabled":true,"clip":true,"name":true,"outline":true},"frameToHighlight":null,"editingFrame":null,"elementsToHighlight":null,"toast":null,"viewBackgroundColor":"#ffffff","zenModeEnabled":false,"zoom":{"value":1},"viewModeEnabled":false,"pendingImageElementId":null,"showHyperlinkPopup":false,"selectedLinearElement":null,"snapLines":[],"originSnapOffset":null,"objectsSnapModeEnabled":false,"offsetLeft":933.7291870117188,"offsetTop":312.66668701171875,"width":964.53125,"height":498.66668701171875}}', 2, '2025-02-23 11:13:21', '2025-02-23 12:16:46'),
	(4, 'TESTST', '{"elements":[{"type":"diamond","version":34,"versionNonce":46449332,"isDeleted":false,"id":"SHNfgka0JxwiNZ-lEVgiE","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":406.93756103515625,"y":130.66665649414062,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":200,"height":160.00003051757812,"seed":1827237126,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"ellipse","version":50,"versionNonce":182776460,"isDeleted":false,"id":"bxf34UivGNM7vpcbDOuzz","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":141.60418701171875,"y":192,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":239.3333740234375,"height":35.33331298828125,"seed":464676442,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"freedraw","version":140,"versionNonce":939674676,"isDeleted":false,"id":"nSPVGSxn8UPMSfdpE38LG","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":212.93756103515625,"y":313.33331298828125,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":240.00006103515625,"height":70,"seed":455921277,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[1.333251953125,0],[2.6666259765625,0],[8,0.66668701171875],[15.333251953125,2],[24,2],[37.333251953125,2.66668701171875],[54,3.3333740234375],[66.6666259765625,4],[81.333251953125,4],[96.6666259765625,4],[106.6666259765625,4],[116,4],[118.6666259765625,4],[118.6666259765625,3.3333740234375],[116.6666259765625,3.3333740234375],[112,2.66668701171875],[104,2.66668701171875],[89.333251953125,2],[77.333251953125,0],[55.333251953125,-1.33331298828125],[36,-1.33331298828125],[18.6666259765625,-1.33331298828125],[3.333251953125,-1.33331298828125],[-5.3333740234375,-1.33331298828125],[-8.666748046875,-1.33331298828125],[-10.666748046875,-0.6666259765625],[-11.3333740234375,0],[-10.666748046875,2.66668701171875],[-7.3333740234375,3.3333740234375],[-1.3333740234375,4.66668701171875],[7.333251953125,6.66668701171875],[15.333251953125,8],[24,9.3333740234375],[35.333251953125,10.66668701171875],[46.6666259765625,10.66668701171875],[58.6666259765625,11.3333740234375],[71.333251953125,11.3333740234375],[80.6666259765625,11.3333740234375],[89.333251953125,11.3333740234375],[97.333251953125,11.3333740234375],[103.333251953125,11.3333740234375],[106,11.3333740234375],[106.6666259765625,11.3333740234375],[108,11.3333740234375],[109.333251953125,11.3333740234375],[111.333251953125,11.3333740234375],[114,11.3333740234375],[116.6666259765625,8.66668701171875],[118.6666259765625,6.66668701171875],[120,3.3333740234375],[122,-0.6666259765625],[122,-6],[122,-10.6666259765625],[120,-14.6666259765625],[118.6666259765625,-18.6666259765625],[114.6666259765625,-23.33331298828125],[109.333251953125,-28.6666259765625],[100,-34.6666259765625],[91.333251953125,-40],[78,-44.6666259765625],[62.6666259765625,-49.33331298828125],[38.6666259765625,-52.6666259765625],[10,-56],[-12.666748046875,-58],[-41.3333740234375,-58.6666259765625],[-64.666748046875,-58],[-77.3333740234375,-54.6666259765625],[-91.3333740234375,-47.33331298828125],[-100,-40],[-108.666748046875,-32],[-113.3333740234375,-24.6666259765625],[-116,-19.33331298828125],[-118.00006103515625,-15.33331298828125],[-118.00006103515625,-12],[-117.3333740234375,-10],[-114.666748046875,-8],[-112,-6],[-104.666748046875,-3.33331298828125],[-96,-0.6666259765625],[-86,0.66668701171875],[-69.3333740234375,1.3333740234375],[-58.666748046875,1.3333740234375],[-42.666748046875,1.3333740234375],[-28,0.66668701171875],[-18,-1.33331298828125],[-11.3333740234375,-4.6666259765625],[-8.666748046875,-8],[-7.3333740234375,-10],[-7.3333740234375,-12.6666259765625],[-11.3333740234375,-18],[-12.666748046875,-20],[-16.666748046875,-23.33331298828125],[-23.3333740234375,-27.33331298828125],[-30,-31.33331298828125],[-40,-34],[-46,-36.6666259765625],[-51.3333740234375,-38],[-58,-38.6666259765625],[-62.666748046875,-38.6666259765625],[-66,-38.6666259765625],[-66.666748046875,-38.6666259765625],[-68.666748046875,-36.6666259765625],[-68.666748046875,-35.33331298828125],[-68.666748046875,-32],[-66.666748046875,-28.6666259765625],[-62,-24],[-57.3333740234375,-20.6666259765625],[-52,-17.33331298828125],[-46.666748046875,-15.33331298828125],[-41.3333740234375,-13.33331298828125],[-36.666748046875,-12.6666259765625],[-34,-12.6666259765625],[-32,-12.6666259765625],[-30.666748046875,-14.6666259765625],[-30,-15.33331298828125],[-30,-17.33331298828125],[-30,-20],[-32,-23.33331298828125],[-35.3333740234375,-26.6666259765625],[-38,-29.33331298828125],[-42.666748046875,-32.6666259765625],[-46.666748046875,-35.33331298828125],[-50.666748046875,-37.33331298828125],[-53.3333740234375,-38],[-55.3333740234375,-38],[-56,-38],[-56,-37.33331298828125],[-56.666748046875,-36.6666259765625],[-56.666748046875,-35.33331298828125],[-57.3333740234375,-33.33331298828125],[-57.3333740234375,-31.33331298828125],[-56.666748046875,-29.33331298828125],[-55.3333740234375,-28],[-54.666748046875,-27.33331298828125],[-54.666748046875,-26.6666259765625],[-54.666748046875,-26.6666259765625]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":5,"versionNonce":2034155788,"isDeleted":false,"id":"-e1asNmUzp6zWMQ7tGk0i","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":168.93756103515625,"y":270.66668701171875,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":0.0001,"height":0.0001,"seed":494237245,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0.0001,0.0001]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":5,"versionNonce":1339205044,"isDeleted":false,"id":"HgZiXu7na8afyO_kk4Ia_","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":778.2708129882812,"y":231.33331298828125,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":0.0001,"height":0.0001,"seed":561537885,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0.0001,0.0001]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":6,"versionNonce":1213906828,"isDeleted":false,"id":"GaUay9Lcif_iDocsScm1m","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":567.6041870117188,"y":294,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":120,"height":6,"seed":1200061486,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[120,6],[120,6]],"lastCommittedPoint":null,"simulatePressure":true,"pressures":[]},{"type":"freedraw","version":38,"versionNonce":257675060,"isDeleted":false,"id":"lZb4FgukrEje5CjVTztmf","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":93.8021240234375,"y":156.33331298828125,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":266.6666259765625,"height":26.6666259765625,"seed":1062581701,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0.6666259765625,0],[2,0],[4,-0.6666259765625],[6.6666259765625,-2],[10,-2.6666259765625],[14.6666259765625,-3.33331298828125],[20,-4.6666259765625],[26,-5.33331298828125],[34.6666259765625,-6],[42,-6.6666259765625],[64.6666259765625,-8],[78.6666259765625,-8],[107.33331298828125,-8],[124.6666259765625,-8],[152.6666259765625,-9.33331298828125],[167.33331298828125,-9.33331298828125],[178.6666259765625,-10],[190.6666259765625,-11.33331298828125],[211.33331298828125,-14],[220,-15.33331298828125],[230.6666259765625,-16.6666259765625],[244,-19.33331298828125],[247.33331298828125,-20],[250,-20.6666259765625],[256,-22],[258,-23.33331298828125],[260,-24],[261.33331298828125,-24],[264,-24],[264.6666259765625,-24.6666259765625],[265.33331298828125,-25.33331298828125],[266,-25.33331298828125],[266.6666259765625,-26.6666259765625],[266.6666259765625,-26.6666259765625]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":19,"versionNonce":7532044,"isDeleted":false,"id":"3XTfeG4EYC7vnNQUiIpsY","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":521.1354370117188,"y":255.6667022705078,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":2.66668701171875,"height":64.66668701171875,"seed":18798405,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0,-0.66668701171875],[0,-1.3333740234375],[0.66668701171875,-3.3333740234375],[1.33331298828125,-4.66668701171875],[1.33331298828125,-9.3333740234375],[1.33331298828125,-12],[1.33331298828125,-14.66668701171875],[2,-20.66668701171875],[2,-23.3333740234375],[2.66668701171875,-34.66668701171875],[2.66668701171875,-48.66668701171875],[2.66668701171875,-55.3333740234375],[2.66668701171875,-60.66668701171875],[2,-64.66668701171875],[2,-64.66668701171875]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":13,"versionNonce":654559412,"isDeleted":false,"id":"W82VNzizXkgK9m-iQrrgs","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":481.13543701171875,"y":217.00001525878906,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":66.66668701171875,"height":5.33331298828125,"seed":1060715685,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0.66668701171875,0],[4,0.66668701171875],[16,1.33331298828125],[24,1.33331298828125],[60,-0.66668701171875],[63.33331298828125,-1.33331298828125],[65.33331298828125,-4],[66.66668701171875,-4],[66.66668701171875,-4]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":14,"versionNonce":310463628,"isDeleted":false,"id":"DFYVbExFUWccfWQZIP11-","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":655.2884521484375,"y":168.7719268798828,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":38.596435546875,"height":1.403533935546875,"seed":302981539,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0.7017822265625,0],[1.4034423828125,0],[2.80712890625,0],[4.912353515625,0],[7.017578125,0],[10.5263671875,0.70184326171875],[14.73681640625,0.70184326171875],[33.6842041015625,1.403533935546875],[37.1929931640625,1.403533935546875],[38.596435546875,1.403533935546875]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":30,"versionNonce":1248891444,"isDeleted":false,"id":"lKotHwQiWJZJH8-aSHKCm","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":663.0076904296875,"y":142.1053009033203,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":112.28076171875,"height":7.71929931640625,"seed":450307363,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0.7017822265625,0],[2.105224609375,-1.40350341796875],[4.912353515625,-2.807037353515625],[9.122802734375,-3.5087890625],[14.7369384765625,-4.91229248046875],[26.666748046875,-4.91229248046875],[33.6842041015625,-5.614044189453125],[41.403564453125,-5.614044189453125],[49.82470703125,-5.614044189453125],[57.5439453125,-5.614044189453125],[65.26318359375,-5.614044189453125],[80,-5.614044189453125],[84.2105712890625,-5.614044189453125],[87.7193603515625,-5.614044189453125],[92.631591796875,-5.614044189453125],[95.4385986328125,-5.614044189453125],[97.5439453125,-5.614044189453125],[101.052734375,-5.614044189453125],[102.4561767578125,-5.614044189453125],[103.859619140625,-5.614044189453125],[105.26318359375,-5.614044189453125],[107.368408203125,-5.614044189453125],[108.77197265625,-5.614044189453125],[110.1754150390625,-5.614044189453125],[112.28076171875,-6.3157958984375],[112.28076171875,-7.71929931640625]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":18,"versionNonce":1077926668,"isDeleted":false,"id":"P2hPA2_nUr5d7NJQ7qxYT","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":429.8021240234375,"y":353.3333260118962,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":49.33331298828125,"height":1.3333740234375,"seed":770351132,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[5.33331298828125,0.66668701171875],[6,0.66668701171875],[8,0.66668701171875],[10.6666259765625,1.3333740234375],[12.6666259765625,1.3333740234375],[17.33331298828125,1.3333740234375],[24,1.3333740234375],[33.33331298828125,1.3333740234375],[38,1.3333740234375],[41.33331298828125,1.3333740234375],[43.33331298828125,1.3333740234375],[46,1.3333740234375],[49.33331298828125,1.3333740234375],[49.33331298828125,1.3333740234375]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":19,"versionNonce":1216883636,"isDeleted":false,"id":"Ngw-R25udcBDQk8i6E8O4","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":557.8021240234375,"y":369.3333260118962,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":157.3333740234375,"height":6.6666259765625,"seed":312010780,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[2,-2],[1.3333740234375,-2],[-0.6666259765625,-2],[-4.6666259765625,-2.6666259765625],[-10,-3.33331298828125],[-28.66668701171875,-5.33331298828125],[-40.66668701171875,-6],[-54.66668701171875,-6],[-89.3333740234375,-6.6666259765625],[-106,-6.6666259765625],[-130,-6.6666259765625],[-141.3333740234375,-6.6666259765625],[-148.66668701171875,-6.6666259765625],[-154.66668701171875,-6.6666259765625],[-155.3333740234375,-6.6666259765625]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":330,"versionNonce":69769612,"isDeleted":false,"id":"0qIN2R7G1bI37s1W8pUio","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":254.46875,"y":496.00000855326675,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":208,"height":83.3333740234375,"seed":1525651795,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[-3.33331298828125,0],[-4,0.66668701171875],[-2.6666259765625,1.33331298828125],[0,2],[12,2],[33.3333740234375,3.33331298828125],[44.66668701171875,3.33331298828125],[64.66668701171875,3.33331298828125],[73.3333740234375,2.66668701171875],[81.3333740234375,0.66668701171875],[88.66668701171875,-2.66668701171875],[90.66668701171875,-4],[90.66668701171875,-4.66668701171875],[88.66668701171875,-7.33331298828125],[85.3333740234375,-10],[78,-14.66668701171875],[54,-25.33331298828125],[44,-28.66668701171875],[30.66668701171875,-32],[17.3333740234375,-35.33331298828125],[-4,-36],[-16,-36],[-24.6666259765625,-34.66668701171875],[-32.6666259765625,-32],[-41.33331298828125,-25.33331298828125],[-43.33331298828125,-22],[-43.33331298828125,-18],[-40.6666259765625,-10],[-36.6666259765625,-5.33331298828125],[-32.6666259765625,-1.33331298828125],[-24.6666259765625,4.66668701171875],[-15.33331298828125,10.66668701171875],[-1.33331298828125,16],[13.3333740234375,18.66668701171875],[46.66668701171875,20.66668701171875],[58.66668701171875,20.66668701171875],[70,19.33331298828125],[81.3333740234375,17.33331298828125],[92,14],[103.3333740234375,6.66668701171875],[107.3333740234375,-1.33331298828125],[107.3333740234375,-4.66668701171875],[106.66668701171875,-9.33331298828125],[97.3333740234375,-21.33331298828125],[91.3333740234375,-26.66668701171875],[82.66668701171875,-33.33331298828125],[61.3333740234375,-45.33331298828125],[50,-50.66668701171875],[12.66668701171875,-60],[-8,-62.66668701171875],[-32.6666259765625,-62.66668701171875],[-44.6666259765625,-62],[-62,-54],[-66,-48.66668701171875],[-68.6666259765625,-38],[-68.6666259765625,-32],[-65.33331298828125,-24],[-60.6666259765625,-18],[-56.6666259765625,-14],[-49.33331298828125,-7.33331298828125],[-34,0],[-25.33331298828125,2.66668701171875],[-14,4],[-5.33331298828125,4],[9.3333740234375,4],[20.66668701171875,2.66668701171875],[40,-6],[42.66668701171875,-8.66668701171875],[44,-12],[44,-16],[42,-18.66668701171875],[38.66668701171875,-21.33331298828125],[33.3333740234375,-25.33331298828125],[26,-28.66668701171875],[11.3333740234375,-33.33331298828125],[-4,-35.33331298828125],[-12.6666259765625,-35.33331298828125],[-23.33331298828125,-35.33331298828125],[-30,-35.33331298828125],[-36,-33.33331298828125],[-42,-28.66668701171875],[-44.6666259765625,-20],[-43.33331298828125,-8],[-40.6666259765625,-3.33331298828125],[-36,0.66668701171875],[-31.33331298828125,4],[-25.33331298828125,7.33331298828125],[-19.33331298828125,8.66668701171875],[-2,11.33331298828125],[8,11.33331298828125],[24,11.33331298828125],[31.3333740234375,9.33331298828125],[37.3333740234375,7.33331298828125],[43.3333740234375,1.33331298828125],[45.3333740234375,-1.33331298828125],[45.3333740234375,-4.66668701171875],[42.66668701171875,-10.66668701171875],[32.66668701171875,-20],[29.3333740234375,-22.66668701171875],[23.3333740234375,-27.33331298828125],[16,-31.33331298828125],[5.3333740234375,-36],[-16,-42.66668701171875],[-34,-45.33331298828125],[-43.33331298828125,-45.33331298828125],[-50,-44],[-60,-35.33331298828125],[-61.33331298828125,-30.66668701171875],[-62,-18.66668701171875],[-60,-12],[-57.33331298828125,-6.66668701171875],[-47.33331298828125,4],[-42,8],[-32,15.33331298828125],[-28,16],[-19.33331298828125,17.33331298828125],[-16.6666259765625,17.33331298828125],[-12,16.66668701171875],[-10,16.66668701171875],[-9.33331298828125,15.33331298828125],[-9.33331298828125,12],[-9.33331298828125,10],[-13.33331298828125,5.33331298828125],[-16,4],[-28,-2],[-33.33331298828125,-3.33331298828125],[-52.6666259765625,-6.66668701171875],[-64,-7.33331298828125],[-71.33331298828125,-7.33331298828125],[-88,-4],[-96,0.66668701171875],[-99.33331298828125,5.33331298828125],[-99.33331298828125,6.66668701171875],[-98,6.66668701171875],[-96,8],[-93.33331298828125,8.66668701171875],[-83.33331298828125,9.33331298828125],[-78.6666259765625,9.33331298828125],[-75.33331298828125,9.33331298828125],[-72.6666259765625,8.66668701171875],[-70.6666259765625,7.33331298828125],[-70,6],[-70,5.33331298828125],[-70,4.66668701171875],[-70.6666259765625,3.33331298828125],[-75.33331298828125,-0.66668701171875],[-78.6666259765625,-2.66668701171875],[-85.33331298828125,-4.66668701171875],[-90,-4.66668701171875],[-94.6666259765625,-4.66668701171875],[-100,-4.66668701171875],[-100.6666259765625,-4],[-99.33331298828125,-3.33331298828125],[-96.6666259765625,-1.33331298828125],[-94.6666259765625,-0.66668701171875],[-85.33331298828125,2.66668701171875],[-80,3.33331298828125],[-74,3.33331298828125],[-70.6666259765625,4],[-65.33331298828125,4],[-65.33331298828125,3.33331298828125],[-67.33331298828125,2],[-69.33331298828125,0],[-77.33331298828125,-3.33331298828125],[-80.6666259765625,-5.33331298828125],[-86,-8],[-96.6666259765625,-10.66668701171875],[-100,-11.33331298828125],[-99.33331298828125,-10],[-96,-8],[-86,-4.66668701171875],[-80,-3.33331298828125],[-68,-2],[-39.33331298828125,0.66668701171875],[-26,0],[-24,-1.33331298828125],[-20,-4.66668701171875],[-20,-6],[-20.6666259765625,-6.66668701171875],[-25.33331298828125,-11.33331298828125],[-29.33331298828125,-13.33331298828125],[-34,-14.66668701171875],[-38.6666259765625,-16.66668701171875],[-46.6666259765625,-16.66668701171875],[-49.33331298828125,-16],[-50.6666259765625,-14.66668701171875],[-51.33331298828125,-12],[-50.6666259765625,-10],[-49.33331298828125,-8],[-47.33331298828125,-5.33331298828125],[-43.33331298828125,-3.33331298828125],[-25.33331298828125,0],[-16.6666259765625,0],[-10.6666259765625,0],[-3.33331298828125,0],[6.66668701171875,-3.33331298828125],[9.3333740234375,-6],[10.66668701171875,-7.33331298828125],[10.66668701171875,-9.33331298828125],[10.66668701171875,-12],[9.3333740234375,-14.66668701171875],[6.66668701171875,-16],[2.66668701171875,-18.66668701171875],[-6,-21.33331298828125],[-10,-22],[-15.33331298828125,-22.66668701171875],[-20,-22.66668701171875],[-24.6666259765625,-22.66668701171875],[-27.33331298828125,-22.66668701171875],[-30,-22],[-30,-20],[-30,-18.66668701171875],[-28.6666259765625,-18],[-17.33331298828125,-14],[-10,-12.66668701171875],[-1.33331298828125,-11.33331298828125],[17.3333740234375,-11.33331298828125],[24,-11.33331298828125],[32,-12],[39.3333740234375,-14.66668701171875],[40.66668701171875,-18.66668701171875],[40.66668701171875,-21.33331298828125],[38.66668701171875,-22.66668701171875],[33.3333740234375,-26],[27.3333740234375,-28.66668701171875],[17.3333740234375,-32],[11.3333740234375,-32.66668701171875],[-1.33331298828125,-34],[-11.33331298828125,-33.33331298828125],[-14.6666259765625,-32],[-17.33331298828125,-30],[-17.33331298828125,-26],[-16.6666259765625,-24.66668701171875],[-13.33331298828125,-22],[-10,-20.66668701171875],[-5.33331298828125,-18],[-1.33331298828125,-16.66668701171875],[4,-15.33331298828125],[9.3333740234375,-14.66668701171875],[16.66668701171875,-14.66668701171875],[18,-15.33331298828125],[18,-16.66668701171875],[17.3333740234375,-18],[12.66668701171875,-22],[8.66668701171875,-24],[4,-27.33331298828125],[-2.6666259765625,-29.33331298828125],[-8.6666259765625,-32],[-14.6666259765625,-34.66668701171875],[-19.33331298828125,-36],[-24.6666259765625,-36],[-30.6666259765625,-36],[-33.33331298828125,-36],[-33.33331298828125,-34],[-32.6666259765625,-32.66668701171875],[-26.6666259765625,-29.33331298828125],[-22.6666259765625,-27.33331298828125],[-18,-25.33331298828125],[-8,-22],[-1.33331298828125,-21.33331298828125],[3.3333740234375,-21.33331298828125],[6.66668701171875,-21.33331298828125],[9.3333740234375,-21.33331298828125],[10,-22],[10,-25.33331298828125],[10,-27.33331298828125],[5.3333740234375,-32],[2,-34],[-0.6666259765625,-36],[-4,-37.33331298828125],[-10.6666259765625,-40],[-18,-40.66668701171875],[-20,-40.66668701171875],[-22.6666259765625,-36.66668701171875],[-22.6666259765625,-35.33331298828125],[-22.6666259765625,-33.33331298828125],[-18.6666259765625,-30.66668701171875],[-15.33331298828125,-28.66668701171875],[-12.6666259765625,-27.33331298828125],[-6,-25.33331298828125],[-2,-24],[1.3333740234375,-24],[4.66668701171875,-24],[4.66668701171875,-24.66668701171875],[4.66668701171875,-25.33331298828125],[4.66668701171875,-26.66668701171875],[1.3333740234375,-28.66668701171875],[-0.6666259765625,-30.66668701171875],[-4,-32.66668701171875],[-7.33331298828125,-34.66668701171875],[-16.6666259765625,-39.33331298828125],[-22,-40.66668701171875],[-26.6666259765625,-42.66668701171875],[-33.33331298828125,-42.66668701171875],[-37.33331298828125,-42.66668701171875],[-42.6666259765625,-40.66668701171875],[-43.33331298828125,-37.33331298828125],[-42.6666259765625,-32],[-34.6666259765625,-24.66668701171875],[-26,-19.33331298828125],[-20,-17.33331298828125],[-16,-15.33331298828125],[-10,-14],[-8.6666259765625,-14],[-6.6666259765625,-14],[-6,-16.66668701171875],[-6,-18.66668701171875],[-7.33331298828125,-20],[-12,-26],[-15.33331298828125,-29.33331298828125],[-18.6666259765625,-32.66668701171875],[-23.33331298828125,-35.33331298828125],[-25.33331298828125,-36],[-27.33331298828125,-36],[-29.33331298828125,-36],[-32,-34.66668701171875],[-32,-33.33331298828125],[-31.33331298828125,-32.66668701171875],[-30,-32],[-29.33331298828125,-31.33331298828125],[-28.6666259765625,-30],[-28,-29.33331298828125],[-26.6666259765625,-29.33331298828125],[-25.33331298828125,-28.66668701171875],[-24.6666259765625,-28],[-24.6666259765625,-28]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"freedraw","version":19,"versionNonce":594580788,"isDeleted":false,"id":"1hF78e1aaD14qRyGbiRAO","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":431.41119384765625,"y":484.90569397807144,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":3.72039794921875,"height":51.341796875,"seed":561012307,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"points":[[0,0],[0,0.74407958984375],[0,2.976318359375],[0.74407958984375,7.4407958984375],[1.4881591796875,12.64947509765625],[1.4881591796875,23.81072998046875],[1.4881591796875,29.019287109375],[1.4881591796875,32.73974609375],[0.74407958984375,36.4600830078125],[0,40.1805419921875],[0,43.1568603515625],[-1.4881591796875,46.13323974609375],[-1.4881591796875,48.365478515625],[-2.23223876953125,49.8536376953125],[-2.23223876953125,51.341796875],[-2.23223876953125,51.341796875]],"lastCommittedPoint":null,"simulatePressure":false,"pressures":[1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0]},{"type":"diamond","version":59,"versionNonce":2037745676,"isDeleted":false,"id":"0qkX9frO0803BsXK4rvlR","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":195.53668212890625,"y":-160.95885404180098,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":164.4425048828125,"height":188.9971923828125,"seed":1961317875,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[{"id":"olrxlfW6nJBVNNxW4YpGw","type":"arrow"},{"id":"HH6laNoXbue3f5VHpl5me","type":"arrow"}],"updated":1740326234966,"link":null,"locked":false},{"type":"arrow","version":107,"versionNonce":680513204,"isDeleted":false,"id":"olrxlfW6nJBVNNxW4YpGw","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":8.77166748046875,"y":-49.346305213675976,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":167.41888427734375,"height":9.67315673828125,"seed":333218643,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"startBinding":null,"endBinding":{"elementId":"0qkX9frO0803BsXK4rvlR","focus":-0.01663959532271607,"gap":19.47911679336304},"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":"arrow","points":[[0,0],[167.41888427734375,-9.67315673828125]]},{"type":"arrow","version":63,"versionNonce":1142990476,"isDeleted":false,"id":"HH6laNoXbue3f5VHpl5me","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":568.3225708007812,"y":-55.299002967582226,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":199.41436767578125,"height":9.673095703125,"seed":1297576637,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"startBinding":null,"endBinding":{"elementId":"0qkX9frO0803BsXK4rvlR","focus":-0.031040820519847776,"gap":7.712997433487722},"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":"arrow","points":[[0,0],[-199.41436767578125,-9.673095703125]]},{"type":"line","version":18,"versionNonce":143382580,"isDeleted":false,"id":"9jwfkzJetGg_Xt0o8EzJl","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":213.39471435546875,"y":-67.20433744023848,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":154.76934814453125,"height":7.44085693359375,"seed":792416947,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"startBinding":null,"endBinding":null,"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":null,"points":[[0,0],[154.76934814453125,-7.44085693359375]]},{"type":"ellipse","version":39,"versionNonce":2028205324,"isDeleted":false,"id":"hQHYBriGNuJUL1LDpSh5w","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":218.16668701171875,"y":-322.25645699332773,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":186.6666259765625,"height":116,"seed":1237806705,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":46,"versionNonce":1867166132,"isDeleted":false,"id":"DuwiLbxkigkroXLOtzaz8","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":348.621826171875,"y":-458.51957484629156,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":107.368408203125,"height":118.59646606445312,"seed":1714046386,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"ellipse","version":72,"versionNonce":87651212,"isDeleted":false,"id":"SM3bnBJHRwUC51TGQJ1TO","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":267.2183837890625,"y":-425.53712245371344,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":312.2806396484375,"height":65.96493530273438,"seed":718946994,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":52,"versionNonce":468867892,"isDeleted":false,"id":"fF2j0LtqBMUSb8LcYTFh6","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":149.323486328125,"y":-240.97575160410406,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":19.649169921875,"height":119.29827880859375,"seed":11360168,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":26,"versionNonce":391848460,"isDeleted":true,"id":"sOujjovdvd16tVGONtaEY","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":20,"angle":0,"x":76.341064453125,"y":-480.97575160410406,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":16.842041015625,"height":167.01754760742188,"seed":1521922472,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":60,"versionNonce":2028524724,"isDeleted":true,"id":"gpbI1PSyrjjuT3OODY4Ah","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":20,"angle":0,"x":63.0076904296875,"y":-472.55470057871344,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":0.7017822265625,"height":162.8070068359375,"seed":1001687512,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"rectangle","version":60,"versionNonce":105054348,"isDeleted":false,"id":"MkABd3KVPGJhWigst26Be","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":207.604248046875,"y":-599.4670329880203,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":209.122802734375,"height":58.24560546875,"seed":1948288595,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"rectangle","version":74,"versionNonce":869539380,"isDeleted":false,"id":"WJH7vdq7Lwj-TpvYHV4J5","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":202.6920166015625,"y":-620.5196758102859,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":213.3333740234375,"height":80.70181274414062,"seed":2092207645,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"rectangle","version":84,"versionNonce":967511820,"isDeleted":false,"id":"J7rPgo5Sy5t0CAFi5FrHu","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":193.5692138671875,"y":-628.9406963180984,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":225.9649658203125,"height":85.614013671875,"seed":96827677,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"rectangle","version":269,"versionNonce":1436170164,"isDeleted":false,"id":"Ux7w9F35KsWRU0hWF1FId","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":271.65106201171875,"y":-759.2798686813795,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":262.45623779296875,"height":101.75445556640625,"seed":652235741,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"ellipse","version":30,"versionNonce":302861708,"isDeleted":false,"id":"kmT4hJWg116XXN6wJa7rW","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":532.5165405273438,"y":-568.4143901657546,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":276.666748046875,"height":76,"seed":1029049223,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":34,"versionNonce":1516314932,"isDeleted":true,"id":"YhvOsZZSYryE9jRXdgV6z","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":20,"angle":0,"x":-103.12744140625,"y":-515.2564828052759,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":34.38604736328125,"height":160.70181274414062,"seed":885014454,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"frame","version":36,"versionNonce":1352907788,"isDeleted":true,"id":"xUP3Xj6qo1LroI_9NEdrK","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":0,"opacity":20,"angle":0,"x":-151.3028564453125,"y":-518.4845406666041,"strokeColor":"#bbb","backgroundColor":"transparent","width":269.3333740234375,"height":109.33331298828125,"seed":523448155,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"name":null},{"type":"arrow","version":59,"versionNonce":1234098868,"isDeleted":false,"id":"JVWZKAisZl7H8qJXOdJ1E","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-331.7403564453125,"y":-448.81783839609625,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":203.3333740234375,"height":84.66668701171875,"seed":649302483,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"startBinding":null,"endBinding":null,"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":"arrow","points":[[0,0],[203.3333740234375,84.66668701171875]]},{"type":"rectangle","version":70,"versionNonce":2137545356,"isDeleted":false,"id":"sJCLrh98q2PWIMar0k9NI","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-645.2130126953123,"y":-846.38512471495,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":415.9999999999999,"height":151.11114501953114,"seed":2023899955,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"rectangle","version":86,"versionNonce":1305044020,"isDeleted":false,"id":"VpNR-I1om7KrWjofg3Njv","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-670.1014404296873,"y":-842.8295827227626,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":463.9999999999999,"height":151.11114501953125,"seed":952474589,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":68,"versionNonce":1494558988,"isDeleted":false,"id":"1FXcgAYM44ZFrMwjF1-tY","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-577.6571044921873,"y":-905.0518117266688,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":312.8889160156249,"height":330.66668701171864,"seed":251528029,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"ellipse","version":41,"versionNonce":1291238836,"isDeleted":false,"id":"kAjWXZweywIQg-06dcDmX","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-636.3236083984373,"y":-922.8295827227626,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":508.4445800781249,"height":346.6667480468749,"seed":1061035133,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"arrow","version":80,"versionNonce":1495410456,"isDeleted":false,"id":"fz0Fn7vCZSAWfMtDCiX4u","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-565.159945986574,"y":-737.7184377032315,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":111.94766571313664,"height":62.44445800781227,"seed":53626429,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740327669701,"link":null,"locked":false,"startBinding":{"elementId":"EvQtTetTrrnEwSjFZQTc-","focus":-0.7828893923740466,"gap":18.696989533444707},"endBinding":null,"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":"arrow","points":[[0,0],[111.94766571313664,-62.44445800781227]]},{"type":"line","version":25,"versionNonce":3991348,"isDeleted":false,"id":"ciSMJrEPF5njoUOdU17cb","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-719.8790283203123,"y":-865.9406056719813,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":478.2219238281249,"height":186.6666259765625,"seed":672492851,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false,"startBinding":null,"endBinding":null,"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":null,"points":[[0,0],[478.2219238281249,186.6666259765625]]},{"type":"rectangle","version":105,"versionNonce":1459197976,"isDeleted":false,"id":"r8ruirSSvA1Jl1hgGcV_G","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-573.2919747488837,"y":-1014.9126951669589,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":594.8552246093748,"height":446.8083496093749,"seed":534556574,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740327626876,"link":null,"locked":false},{"type":"diamond","version":72,"versionNonce":201449652,"isDeleted":false,"id":"r_ly1Noi9syF_fCXOFGvQ","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-491.17065429687483,"y":-1036.4754567462,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":229.40588378906244,"height":521.4986572265624,"seed":1357595202,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"text","version":45,"versionNonce":461738900,"isDeleted":false,"id":"sUiDo36OuTWQMV6VNP8Qp","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-499.39571126302064,"y":-977.790154011825,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":53.39997863769531,"height":25,"seed":1646152798,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740333479719,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"BRUH","textAlign":"right","verticalAlign":"top","containerId":null,"originalText":"BRUH","lineHeight":1.25,"baseline":17},{"type":"rectangle","version":151,"versionNonce":99071000,"isDeleted":false,"id":"4gZm563re8lDjpevDK2ol","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-764.056004115513,"y":-1011.2433784451463,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":734.4443766276041,"height":100,"seed":266736390,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740327648928,"link":null,"locked":false},{"type":"rectangle","version":26,"versionNonce":1839350540,"isDeleted":false,"id":"kBtpnLZThZsqYHkpPh6G7","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-501.8339175269714,"y":-1085.687856798011,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":685.5556233723958,"height":77.77781168619799,"seed":278970522,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"diamond","version":21,"versionNonce":1725973428,"isDeleted":false,"id":"P0rPTfdZUtVMv87-VgFAN","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-614.056004115513,"y":-1087.910045111813,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":379.9999999999999,"height":92.22218831380201,"seed":758512646,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"ellipse","version":26,"versionNonce":687735180,"isDeleted":false,"id":"uz2RV8izIKwYuUgni5zqS","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-447.3891339983262,"y":-1321.7990526801734,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":404.4445800781249,"height":265.5555216471355,"seed":557434931,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740326234966,"link":null,"locked":false},{"type":"text","version":4,"versionNonce":1381525352,"isDeleted":true,"id":"zSMxIPjSZdPMS5a9ZSMAn","fillStyle":"solid","strokeWidth":2,"strokeStyle":"solid","roughness":1,"opacity":100,"angle":0,"x":-541.2781924293158,"y":-749.0214481697867,"strokeColor":"#1e1e1e","backgroundColor":"transparent","width":10,"height":25,"seed":292903784,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740327632265,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"right","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1},{"type":"rectangle","version":154,"versionNonce":1751466264,"isDeleted":false,"id":"EvQtTetTrrnEwSjFZQTc-","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-718.5002790178575,"y":-719.0214481697867,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":444.444580078125,"height":367.7777099609375,"seed":2046816536,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[{"id":"fz0Fn7vCZSAWfMtDCiX4u","type":"arrow"}],"updated":1740327672132,"link":null,"locked":false},{"type":"text","version":2,"versionNonce":139000088,"isDeleted":true,"id":"doBFvV7x6hpBq1U6T104r","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-724.6115257626491,"y":-557.3547815031202,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":10,"height":25,"seed":799291496,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740327669156,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"right","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1},{"type":"diamond","version":6,"versionNonce":1715547529,"isDeleted":false,"id":"8IVNKLd3Kf9TbaQV5JlkU","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-313.5004824683783,"y":-3180.6880565232404,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":155.55562337239587,"height":473.33333333333303,"seed":332948391,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740329229104,"link":null,"locked":false},{"type":"arrow","version":10,"versionNonce":324561545,"isDeleted":false,"id":"0PlRfKHb_JBr6xNI-jy6g","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":390.9439958844862,"y":-2822.9103465623034,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":621.1110432942708,"height":171.11104329427098,"seed":386480743,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740329225384,"link":null,"locked":false,"startBinding":null,"endBinding":null,"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":"arrow","points":[[0,0],[-621.1110432942708,-171.11104329427098]]},{"type":"text","version":2,"versionNonce":349081993,"isDeleted":true,"id":"wqSG8wOqO9jhu070qFWbj","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-7.944757370722186,"y":-3256.2436798956364,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":10,"height":25,"seed":15016711,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740329226668,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"right","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1},{"type":"text","version":2,"versionNonce":1086869833,"isDeleted":true,"id":"Egs4d_oyaLZw93e0NWNXU","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-455.7226707821804,"y":-3129.57696236634,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":10,"height":25,"seed":407075527,"groupIds":[],"frameId":null,"roundness":null,"boundElements":[],"updated":1740329228334,"link":null,"locked":false,"fontSize":20,"fontFamily":1,"text":"","textAlign":"right","verticalAlign":"top","containerId":null,"originalText":"","lineHeight":1.25,"baseline":1},{"type":"arrow","version":12,"versionNonce":1446759975,"isDeleted":false,"id":"9DfaOND1lANjyMq9LwhYX","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-447.9448590959825,"y":-2787.3547231899074,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":84.44447835286456,"height":218.88895670572947,"seed":539050025,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740329228747,"link":null,"locked":false,"startBinding":null,"endBinding":null,"lastCommittedPoint":null,"startArrowhead":null,"endArrowhead":"arrow","points":[[0,0],[84.44447835286456,-218.88895670572947]]},{"type":"diamond","version":7,"versionNonce":965319015,"isDeleted":false,"id":"aJNmcb4rD54-KUzJ9uve_","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-284.6115257626492,"y":-3191.799201542772,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":517.7778116861979,"height":167.77781168619777,"seed":266076839,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740329230242,"link":null,"locked":false},{"type":"diamond","version":16,"versionNonce":274040104,"isDeleted":false,"id":"YFi8pzmBJqSMZMunwi5Oo","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-245.1669456845242,"y":-1496.7992536968363,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":148.88875325520837,"height":296.6667175292969,"seed":1924395048,"groupIds":[],"frameId":null,"roundness":{"type":2},"boundElements":[],"updated":1740329350579,"link":null,"locked":false},{"type":"rectangle","version":8,"versionNonce":926740588,"isDeleted":false,"id":"TG6guYS7yjm3tOiGofJlN","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":105.38857596261118,"y":-1534.021442010638,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":247.7777099609375,"height":331.11104329427076,"seed":1428860652,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740329596780,"link":null,"locked":false},{"type":"rectangle","version":17,"versionNonce":1573227806,"isDeleted":false,"id":"aueXXHSw3rRyB36-mPY84","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-156.83371407645132,"y":-964.0215077744297,"strokeColor":"#1971c2","backgroundColor":"#ffc9c9","width":376.66666666666674,"height":231.11114501953125,"seed":1368033566,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740329842582,"link":null,"locked":false},{"type":"rectangle","version":62,"versionNonce":35940300,"isDeleted":false,"id":"GqBln1ESX6z3wut_7Lksz","fillStyle":"cross-hatch","strokeWidth":2,"strokeStyle":"solid","roughness":2,"opacity":100,"angle":0,"x":-674.6115257626491,"y":-1180.6880917893222,"strokeColor":"#f08c00","backgroundColor":"#ffc9c9","width":1097.7777099609373,"height":1277.777830759684,"seed":456538868,"groupIds":[],"frameId":null,"roundness":{"type":3},"boundElements":[],"updated":1740330677836,"link":null,"locked":false}],"appState":{"showWelcomeScreen":true,"theme":"light","collaborators":[],"currentChartType":"bar","currentItemBackgroundColor":"#ffc9c9","currentItemEndArrowhead":"arrow","currentItemFillStyle":"cross-hatch","currentItemFontFamily":1,"currentItemFontSize":20,"currentItemOpacity":100,"currentItemRoughness":2,"currentItemStartArrowhead":null,"currentItemStrokeColor":"#f08c00","currentItemRoundness":"round","currentItemStrokeStyle":"solid","currentItemStrokeWidth":2,"currentItemTextAlign":"right","cursorButton":"up","activeEmbeddable":null,"draggingElement":null,"editingElement":null,"editingGroupId":null,"editingLinearElement":null,"activeTool":{"type":"selection","customType":null,"locked":false,"lastActiveTool":null},"penMode":false,"penDetected":false,"errorMessage":null,"exportBackground":true,"exportScale":1,"exportEmbedScene":false,"exportWithDarkMode":false,"fileHandle":null,"gridSize":null,"isBindingEnabled":true,"defaultSidebarDockedPreference":false,"isLoading":false,"isResizing":false,"isRotating":false,"lastPointerDownWith":"mouse","multiElement":null,"name":"Untitled-2025-02-23-1814","contextMenu":null,"openMenu":null,"openPopup":null,"openSidebar":null,"openDialog":null,"pasteDialog":{"shown":false,"data":null},"previousSelectedElementIds":{"GqBln1ESX6z3wut_7Lksz":true},"resizingElement":null,"scrolledOutside":false,"scrollX":910.0628807431178,"scrollY":675.6881967928376,"selectedElementIds":{"GqBln1ESX6z3wut_7Lksz":true},"selectedGroupIds":{},"selectedElementsAreBeingDragged":false,"selectionElement":null,"shouldCacheIgnoreZoom":false,"showStats":false,"startBoundElement":null,"suggestedBindings":[],"frameRendering":{"enabled":true,"clip":true,"name":true,"outline":true},"frameToHighlight":null,"editingFrame":null,"elementsToHighlight":null,"toast":null,"viewBackgroundColor":"#fdf8f6","zenModeEnabled":false,"zoom":{"value":0.6000000000000001},"viewModeEnabled":false,"pendingImageElementId":null,"showHyperlinkPopup":false,"selectedLinearElement":null,"snapLines":[],"originSnapOffset":null,"objectsSnapModeEnabled":false,"offsetLeft":923.7291870117188,"offsetTop":310,"width":964.53125,"height":1049.8646240234375}}', 2, '2025-02-23 12:17:38', '2025-02-23 17:58:09'),
	(5, 'tesasdac', NULL, 5, '2025-02-24 21:20:37', NULL);

-- Dumping structure for table lead_platform.drawing_board_collaborators
CREATE TABLE IF NOT EXISTS `drawing_board_collaborators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drawing_board_id` int NOT NULL,
  `user_id` int NOT NULL,
  `permissions` enum('read','write') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'read',
  PRIMARY KEY (`id`),
  UNIQUE KEY `drawing_board_user` (`drawing_board_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `drawing_board_collaborators_ibfk_1` FOREIGN KEY (`drawing_board_id`) REFERENCES `drawing_boards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drawing_board_collaborators_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.drawing_board_collaborators: ~0 rows (approximately)
REPLACE INTO `drawing_board_collaborators` (`id`, `drawing_board_id`, `user_id`, `permissions`) VALUES
	(1, 4, 5, 'read');

-- Dumping structure for table lead_platform.emails
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sender` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `recipient` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `body` text COLLATE utf8mb4_general_ci NOT NULL,
  `sent_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `has_attachment` tinyint(1) NOT NULL DEFAULT '0',
  `mailbox` enum('inbox','sent','drafts','trash','spam') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inbox',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_id` (`message_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `emails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.emails: ~0 rows (approximately)
REPLACE INTO `emails` (`id`, `user_id`, `message_id`, `sender`, `recipient`, `subject`, `body`, `sent_date`, `is_read`, `has_attachment`, `mailbox`, `created_at`) VALUES
	(1, 2, '<JlpfohaTVXTyLAnU9lMJTg@notifications.google.com>', 'Google <privacy-noreply@google.com>', '@', 'An update to your Google Account', '[image: Google] support@milddreams.com Hi Mild, We wanted to let you know that we\'re changing the country associated with your Google Account. We associate your Google Account with a region so that we can better provide our services to you. Your region association will change within 30 days. Current: *India* New: *Greece* If you disagree with this update, you can submit a request to change your region . If the region associated with your account doesn\'t match your place of residence, it could be because you\'ve moved within the last year, live and work in different regions or live near a territorial border. This also means that the Google company responsible for your information and for complying with applicable privacy laws is changing from *Google LLC* to *Google Ireland Ltd*. This may affect your use of our products and services, depending on your region\'s laws and regulations. - Some features may no longer be available to you, or you may need to verify your age - Depending on the laws in your region, your account may need to be supervised by a parent - If you use Family Link to manage a supervised account , a change of region to your account will also change the region for the supervised account You can find more information about your region association by reviewing the Google Terms of Service or checking out our FAQs page . Thanks, The Google Support team This email was sent to support@milddreams.com to provide an update about your Google Account. © 2025 Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA .awl a {color: #FFFFFF; text-decoration: none;} .abml a {color: #000000; font-family: Roboto-Medium,Helvetica,Arial,sans-serif; font-weight: bold; text-decoration: none;} .adgl a {color: rgba(0, 0, 0, 0.87); text-decoration: none;} .afal a {color: #b0b0b0; text-decoration: none;} @media screen and (min-width: 600px) {.v2sp {padding: 6px 30px 0px;} .v2rsp {padding: 0px 10px;}} @media screen and (min-width: 600px) {.mdv2rw {padding: 40px 40px;}} support@milddreams.com Hi Mild,We wanted to let you know that we\'re changing the country associated with your Google Account.We associate your Google Account with a region so that we can better provide our services to you. Your region association will change within 30 days.Current: IndiaNew: GreeceIf you disagree with this update, you can submit a request to change your region. If the region associated with your account doesn\'t match your place of residence, it could be because you\'ve moved within the last year, live and work in different regions or live near a territorial border. This also means that the Google company responsible for your information and for complying with applicable privacy laws is changing from Google LLC to Google Ireland Ltd.This may affect your use of our products and services, depending on your region\'s laws and regulations.Some features may no longer be available to you, or you may need to verify your ageDepending on the laws in your region, your account may need to be supervised by a parentIf you use Family Link to manage a supervised account, a change of region to your account will also change the region for the supervised accountYou can find more information about your region association by reviewing the Google Terms of Service or checking out our FAQs page.Thanks,The Google Support teamThis email was sent to support@milddreams.com to provide an update about your Google Account.&copy; 2025 Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA', '2025-01-11 09:13:08', 0, 0, 'inbox', '2025-02-14 13:46:58');

-- Dumping structure for table lead_platform.employees
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.employees: ~2 rows (approximately)
REPLACE INTO `employees` (`id`, `name`, `email`, `phone`, `created_at`, `profile_picture`) VALUES
	(1, 'Jordan Belfort', 'sales@demo.com', '123456789', '2025-01-28 03:04:22', 'public/uploads/profile/67b47f8d93efa_appreciation.png'),
	(2, 'David', 'david@employee.com', '123456678', '2025-01-28 04:04:54', 'public/uploads/profile/1024full-eric-cartman.jpg');

-- Dumping structure for table lead_platform.expenses
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `project_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `invoice_id` int DEFAULT NULL,
  `payment_mode` enum('Cash','Credit Card','Bank Transfer','Online Payment','Check') COLLATE utf8mb4_general_ci DEFAULT 'Cash',
  `transaction_nature` enum('Reimbursable','Business Expense','Personal Expense') COLLATE utf8mb4_general_ci DEFAULT 'Business Expense',
  `receipt_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `approval_status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `project_id` (`project_id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_ibfk_4` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.expenses: ~3 rows (approximately)
REPLACE INTO `expenses` (`id`, `name`, `category_id`, `amount`, `expense_date`, `project_id`, `user_id`, `invoice_id`, `payment_mode`, `transaction_nature`, `receipt_path`, `notes`, `approval_status`, `created_at`) VALUES
	(1, 'AWS Bill', 1, 1220.00, '2025-01-29', 6, 2, 4, 'Credit Card', 'Reimbursable', NULL, 'AWS server bill racked up', 'Pending', '2025-01-31 04:19:55'),
	(2, 'Battery', 1, 10.00, '2025-01-16', NULL, 2, NULL, 'Cash', 'Business Expense', 'uploads/receipts/679c74f7303aa_360_F_176121489_0n5AF6Y7zVXVahgAv2q66OLv5Lf1FR15.jpg', 'Battery swap', 'Pending', '2025-01-31 07:00:07'),
	(3, 'Firmware Purchased', 2, 1304.00, '2025-01-28', NULL, 2, NULL, 'Online Payment', 'Personal Expense', 'uploads/receipts/679c764b97598_360_F_176121489_0n5AF6Y7zVXVahgAv2q66OLv5Lf1FR15.jpg', 'Purchased the hardware firmware', 'Pending', '2025-01-31 07:05:47');

-- Dumping structure for table lead_platform.expense_approvals
CREATE TABLE IF NOT EXISTS `expense_approvals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expense_id` int NOT NULL,
  `approver_id` int NOT NULL,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `rejection_reason` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `expense_id` (`expense_id`),
  KEY `approver_id` (`approver_id`),
  CONSTRAINT `expense_approvals_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expense_approvals_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.expense_approvals: ~0 rows (approximately)

-- Dumping structure for table lead_platform.expense_categories
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.expense_categories: ~2 rows (approximately)
REPLACE INTO `expense_categories` (`id`, `name`, `created_at`) VALUES
	(1, 'Server Costs', '2025-01-31 03:31:40'),
	(2, 'Software Purchases', '2025-01-31 03:57:15');

-- Dumping structure for table lead_platform.expense_comments
CREATE TABLE IF NOT EXISTS `expense_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expense_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `expense_id` (`expense_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `expense_comments_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expense_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.expense_comments: ~2 rows (approximately)
REPLACE INTO `expense_comments` (`id`, `expense_id`, `user_id`, `comment`, `created_at`) VALUES
	(1, 3, 2, 'HMM', '2025-01-31 07:08:33'),
	(2, 3, 2, 'Nice', '2025-01-31 07:11:27');

-- Dumping structure for table lead_platform.featured_knowledge_base_articles
CREATE TABLE IF NOT EXISTS `featured_knowledge_base_articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `featured_knowledge_base_articles_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.featured_knowledge_base_articles: ~0 rows (approximately)

-- Dumping structure for table lead_platform.feature_attachments
CREATE TABLE IF NOT EXISTS `feature_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  CONSTRAINT `feature_attachments_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.feature_attachments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.feature_comments
CREATE TABLE IF NOT EXISTS `feature_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `feature_comments_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feature_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.feature_comments: ~0 rows (approximately)
REPLACE INTO `feature_comments` (`id`, `feature_id`, `user_id`, `comment`, `created_at`) VALUES
	(1, 1, 2, 'Nice feature bruh', '2025-02-12 19:22:00');

-- Dumping structure for table lead_platform.feature_dependencies
CREATE TABLE IF NOT EXISTS `feature_dependencies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `depends_on_feature_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  KEY `depends_on_feature_id` (`depends_on_feature_id`),
  CONSTRAINT `feature_dependencies_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feature_dependencies_ibfk_2` FOREIGN KEY (`depends_on_feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.feature_dependencies: ~0 rows (approximately)

-- Dumping structure for table lead_platform.feature_resources
CREATE TABLE IF NOT EXISTS `feature_resources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `resource_type` enum('Hours','Budget') COLLATE utf8mb4_general_ci NOT NULL,
  `estimated_value` decimal(10,2) DEFAULT '0.00',
  `actual_value` decimal(10,2) DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  CONSTRAINT `feature_resources_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.feature_resources: ~0 rows (approximately)
REPLACE INTO `feature_resources` (`id`, `feature_id`, `resource_type`, `estimated_value`, `actual_value`, `notes`, `created_at`) VALUES
	(1, 1, 'Budget', 1500.00, 250.00, 'Need more than this', '2025-02-12 21:41:03');

-- Dumping structure for table lead_platform.feature_subtasks
CREATE TABLE IF NOT EXISTS `feature_subtasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `feature_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `assigned_to` int DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('To Do','In Progress','Completed','Blocked') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'To Do',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `feature_subtasks_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feature_subtasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.feature_subtasks: ~0 rows (approximately)
REPLACE INTO `feature_subtasks` (`id`, `feature_id`, `title`, `description`, `assigned_to`, `due_date`, `status`, `created_at`) VALUES
	(1, 1, 'Purchase microphones', 'Microphone vendor partner purchase', 2, '2025-02-14 03:10:00', 'To Do', '2025-02-12 21:40:30');

-- Dumping structure for table lead_platform.invoices
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `lead_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `bill_to_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `bill_to_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bill_to_email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bill_to_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ship_to_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `tax_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax` text COLLATE utf8mb4_general_ci,
  `discount` decimal(10,2) DEFAULT '0.00',
  `additional_charges` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  `payment_terms` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `footer` text COLLATE utf8mb4_general_ci,
  `billing_country` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `discount_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'fixed',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `template_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default',
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `status` enum('Unpaid','Partially Paid','Paid','Overdue') COLLATE utf8mb4_general_ci DEFAULT 'Unpaid',
  `payment_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.invoices: ~3 rows (approximately)
REPLACE INTO `invoices` (`id`, `invoice_number`, `lead_id`, `customer_id`, `issue_date`, `due_date`, `bill_to_name`, `bill_to_address`, `bill_to_email`, `bill_to_phone`, `ship_to_address`, `subtotal`, `tax_method`, `tax`, `discount`, `additional_charges`, `total`, `payment_terms`, `notes`, `footer`, `billing_country`, `discount_type`, `discount_amount`, `created_at`, `template_name`, `paid_amount`, `status`, `payment_date`) VALUES
	(3, 'INV-20250129-001', 1, NULL, '2025-01-29', '2025-01-29', 'John Doe', 'NYC', 'john@demo.com', '+91 123456789', 'NYC', 0.00, 'GST', '["8.00","5.00"]', 0.00, 30.00, 43.00, 'Due on Receipt', '', '', 'in', 'fixed', 0.00, '2025-01-29 09:08:58', 'contractor', 43.00, 'Paid', '2025-01-29 11:59:30'),
	(4, 'INV-20250129-004', NULL, 1, '2025-01-29', '2025-02-13', 'Jabbar2', 'NYC', 'jabbar@demo.com', '12312312', '', 0.00, 'GST', '["18.00","18.00"]', 0.00, 0.00, 36.00, 'Net 15', '', '', 'in', 'percentage', 10.00, '2025-01-29 09:43:24', 'default', 24.00, 'Overdue', '2025-01-29 11:52:19'),
	(13, 'INV-20250212-005', NULL, 3, '2025-02-12', '2025-02-13', 'test', '', 'test@demo.com', '12345', '', 0.00, 'Sales Tax', '["120.00"]', 0.00, 0.00, 120.00, 'Net 15', '', '', 'us', 'fixed', 0.00, '2025-02-12 11:32:39', 'contractor', 120.00, 'Paid', '2025-02-12 11:48:12');

-- Dumping structure for table lead_platform.invoice_items
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `product_service` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.invoice_items: ~5 rows (approximately)
REPLACE INTO `invoice_items` (`id`, `invoice_id`, `product_service`, `quantity`, `unit_price`, `tax`, `discount`, `subtotal`) VALUES
	(22, 3, 'Product 2', 2, 120.00, 8.00, 10.00, 0.00),
	(23, 3, 'Product 3', 3, 200.00, 5.00, 10.00, 0.00),
	(47, 4, 'Product 1', 10, 120.00, 18.00, 10.00, 0.00),
	(48, 4, 'Product 2', 10, 100.00, 18.00, 0.00, 0.00),
	(50, 13, 'TEST 123', 1, 1500.00, 120.00, 0.00, 0.00);

-- Dumping structure for table lead_platform.invoice_settings
CREATE TABLE IF NOT EXISTS `invoice_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_tagline` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_address_line1` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_address_line2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_phone_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `overdue_charge_type` enum('percentage','fixed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `overdue_charge_amount` decimal(10,2) DEFAULT NULL,
  `overdue_charge_period` enum('monthly','daily','days') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `thank_you_message` text COLLATE utf8mb4_general_ci,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `invoice_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.invoice_settings: ~0 rows (approximately)
REPLACE INTO `invoice_settings` (`id`, `company_name`, `company_logo`, `company_tagline`, `company_address_line1`, `company_address_line2`, `company_phone_number`, `overdue_charge_type`, `overdue_charge_amount`, `overdue_charge_period`, `thank_you_message`, `user_id`, `created_at`) VALUES
	(1, 'RevenueSure', 'uploads/logo/679a05e10e149_DEMO-fin-change.png', 'Fo Sho', 'Building #5, Park Avenue Road', 'NY City', '+1 234-546-4554', 'percentage', 10.00, 'days', 'Thanks bro!', 2, '2025-01-29 10:41:37');

-- Dumping structure for table lead_platform.issue_attachments
CREATE TABLE IF NOT EXISTS `issue_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  CONSTRAINT `issue_attachments_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `project_issues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.issue_attachments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.issue_comments
CREATE TABLE IF NOT EXISTS `issue_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `issue_id` (`issue_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `issue_comments_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `project_issues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `issue_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.issue_comments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.knowledge_base_articles
CREATE TABLE IF NOT EXISTS `knowledge_base_articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci,
  `category_id` int DEFAULT NULL,
  `visibility` enum('all','team','admin','draft') COLLATE utf8mb4_general_ci DEFAULT 'all',
  `access_level` enum('public','private') COLLATE utf8mb4_general_ci DEFAULT 'public',
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `view_count` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title` (`title`,`content`),
  FULLTEXT KEY `title_2` (`title`,`content`),
  CONSTRAINT `knowledge_base_articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_articles: ~0 rows (approximately)
REPLACE INTO `knowledge_base_articles` (`id`, `title`, `content`, `category_id`, `visibility`, `access_level`, `user_id`, `created_at`, `updated_at`, `view_count`) VALUES
	(1, 'How to login', '<p>This is a demo article</p>', 2, 'team', 'public', 2, '2025-01-31 18:01:02', '2025-02-19 15:11:52', 32);

-- Dumping structure for table lead_platform.knowledge_base_article_ratings
CREATE TABLE IF NOT EXISTS `knowledge_base_article_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `article_id` int NOT NULL,
  `rating` enum('upvote','downvote') COLLATE utf8mb4_general_ci NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `knowledge_base_article_ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_base_article_ratings_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_article_ratings: ~2 rows (approximately)
REPLACE INTO `knowledge_base_article_ratings` (`id`, `user_id`, `article_id`, `rating`, `comment`, `created_at`) VALUES
	(1, 2, 1, 'upvote', '', '2025-01-31 18:01:08'),
	(2, 2, 1, 'upvote', '', '2025-01-31 18:01:10');

-- Dumping structure for table lead_platform.knowledge_base_article_requests
CREATE TABLE IF NOT EXISTS `knowledge_base_article_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `request_type` enum('New Guide Needed','Existing Guide Needs Update','New FAQ Suggestion') COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `knowledge_base_article_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_article_requests: ~0 rows (approximately)
REPLACE INTO `knowledge_base_article_requests` (`id`, `user_id`, `request_type`, `description`, `created_at`) VALUES
	(1, 2, 'New Guide Needed', 'Need a new guide on the EC2 instance creation', '2025-01-31 19:41:34');

-- Dumping structure for table lead_platform.knowledge_base_article_tags
CREATE TABLE IF NOT EXISTS `knowledge_base_article_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `tag` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `knowledge_base_article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_article_tags: ~2 rows (approximately)
REPLACE INTO `knowledge_base_article_tags` (`id`, `article_id`, `tag`, `created_at`) VALUES
	(1, 1, 'guide', '2025-01-31 19:00:12'),
	(2, 1, 'login', '2025-01-31 19:00:12');

-- Dumping structure for table lead_platform.knowledge_base_article_versions
CREATE TABLE IF NOT EXISTS `knowledge_base_article_versions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `user_id` int NOT NULL,
  `version` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `knowledge_base_article_versions_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_base_article_versions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_article_versions: ~0 rows (approximately)

-- Dumping structure for table lead_platform.knowledge_base_bookmarks
CREATE TABLE IF NOT EXISTS `knowledge_base_bookmarks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `article_id` int NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `knowledge_base_bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_base_bookmarks_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_bookmarks: ~1 rows (approximately)
REPLACE INTO `knowledge_base_bookmarks` (`id`, `user_id`, `article_id`, `notes`, `created_at`) VALUES
	(3, 2, 1, '', '2025-02-18 07:08:42');

-- Dumping structure for table lead_platform.knowledge_base_categories
CREATE TABLE IF NOT EXISTS `knowledge_base_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `knowledge_base_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `knowledge_base_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.knowledge_base_categories: ~2 rows (approximately)
REPLACE INTO `knowledge_base_categories` (`id`, `name`, `parent_id`, `created_at`) VALUES
	(1, 'Handbok', NULL, '2025-01-31 17:58:14'),
	(2, 'Server Guide', NULL, '2025-01-31 18:00:19');

-- Dumping structure for table lead_platform.leads
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('New','Contacted','Converted') COLLATE utf8mb4_general_ci DEFAULT 'New',
  `customer_id` int DEFAULT NULL,
  `converted_by` int DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `source` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Website',
  `assigned_to` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `fk_assigned_to` (`assigned_to`),
  CONSTRAINT `fk_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.leads: ~9 rows (approximately)
REPLACE INTO `leads` (`id`, `name`, `phone`, `email`, `category_id`, `created_at`, `status`, `customer_id`, `converted_by`, `city`, `state`, `country`, `source`, `assigned_to`) VALUES
	(1, 'John Doe', '+91 123456789', 'john@demo.com', 1, '2025-01-26 06:53:45', 'New', NULL, NULL, NULL, NULL, NULL, 'Website', NULL),
	(2, 'Jane Dane', '+ 1 6458898766', 'jane@demo.com', 2, '2025-01-26 06:54:43', 'Converted', NULL, NULL, NULL, NULL, NULL, 'Website', NULL),
	(5, 'TEST LEAD', '123345', 'test@assigneddemo.com', 1, '2025-01-28 03:57:53', 'Converted', NULL, NULL, NULL, NULL, NULL, 'Website', 1),
	(6, 'jakegyk', '1231238123', 'jake@demo.com', 2, '2025-01-28 04:04:31', 'New', NULL, NULL, NULL, NULL, NULL, 'Website', 1),
	(7, 'PowerLead', '1231823918', 'power@demo.com', 1, '2025-01-28 04:05:31', 'Converted', NULL, 1, NULL, NULL, NULL, 'Website', 2),
	(8, 'Jabbar2', '12312312', 'jabbar@demo.com', 2, '2025-01-28 08:55:40', 'Converted', 1, 2, NULL, NULL, NULL, 'Website', 2),
	(9, 'POP', '123123', 'pop@pop.com', 1, '2025-01-29 11:56:29', 'Converted', 2, 2, NULL, NULL, NULL, 'Website', 1),
	(10, 'test2', '191919', 'asd@demo.com', 2, '2025-02-10 05:36:10', 'New', NULL, NULL, NULL, NULL, NULL, 'Website', 2),
	(12, 'testconvert4', '2312312313', 'asdasd@asdasdasd.com', 2, '2025-02-14 15:46:40', 'Converted', 5, 2, NULL, NULL, NULL, 'Website', 1);

-- Dumping structure for table lead_platform.lead_scores
CREATE TABLE IF NOT EXISTS `lead_scores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int NOT NULL,
  `website_visits` int DEFAULT '0',
  `email_opens` int DEFAULT '0',
  `form_submissions` int DEFAULT '0',
  `total_score` int DEFAULT '0',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  CONSTRAINT `lead_scores_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.lead_scores: ~3 rows (approximately)
REPLACE INTO `lead_scores` (`id`, `lead_id`, `website_visits`, `email_opens`, `form_submissions`, `total_score`, `last_updated`) VALUES
	(1, 2, 2, 1, 1, 7, '2025-01-27 11:08:55'),
	(3, 5, 0, 1, 1, 5, '2025-01-28 03:59:32'),
	(4, 8, 2, 1, 1, 7, '2025-02-18 05:06:56');

-- Dumping structure for table lead_platform.ledger_entries
CREATE TABLE IF NOT EXISTS `ledger_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `debit_amount` decimal(10,2) DEFAULT '0.00',
  `credit_amount` decimal(10,2) DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'USD',
  `category` enum('Revenue','Expense','Asset','Liability','Equity') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invoice_id` int DEFAULT NULL,
  `expense_id` int DEFAULT NULL,
  `reconciliation_status` enum('Unreconciled','Matched','Discrepancy') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Unreconciled',
  `transaction_type` enum('Invoice','Expense') COLLATE utf8mb4_general_ci NOT NULL,
  `requires_review` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `expense_id` (`expense_id`),
  CONSTRAINT `ledger_entries_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ledger_entries_ibfk_2` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ledger_entries_ibfk_3` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ledger_entries_ibfk_4` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.ledger_entries: ~0 rows (approximately)
REPLACE INTO `ledger_entries` (`id`, `transaction_date`, `transaction_id`, `description`, `debit_amount`, `credit_amount`, `currency`, `category`, `created_at`, `invoice_id`, `expense_id`, `reconciliation_status`, `transaction_type`, `requires_review`) VALUES
	(1, '2025-02-12', '', 'Payment received for Invoice #INV-20250212-005', 0.00, 120.00, 'us', 'Revenue', '2025-02-12 11:32:43', 13, NULL, 'Unreconciled', 'Invoice', 0);

-- Dumping structure for table lead_platform.notes
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci,
  `category_id` int DEFAULT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT '0',
  `related_type` enum('lead','customer','project','user') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `related_id` int DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `style` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'style1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `related_type` (`related_type`,`related_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.notes: ~2 rows (approximately)
REPLACE INTO `notes` (`id`, `title`, `content`, `category_id`, `is_shared`, `related_type`, `related_id`, `created_by`, `created_at`, `updated_at`, `style`) VALUES
	(2, 'Meeting Notes - Project Kickoff', '<h2><strong>Meeting Summary:</strong><br>&nbsp;</h2><ul><li><strong>Project Overview:</strong><br>John presented the project\'s objectives, focusing on improving customer experience through new software features. The main goal is to launch by Q3 2025.</li><li><strong>Team Roles:</strong><ul><li>John will lead the technical team.</li><li>Jane will manage the UX/UI design.</li><li>Alice will be the product owner, and Mark will handle project management.</li></ul></li><li><strong>Timeline:</strong><br>The key milestones were discussed. The first prototype is expected by March 2025, with the first round of testing planned for late May 2025.</li><li><strong>Tools &amp; Resources:</strong><br>The team agreed to use Asana for project management, GitHub for version control, and Figma for design collaboration.</li><li><strong>Action Items:</strong><ul><li>John to finalize the tech stack by Friday, February 16, 2025.</li><li>Jane to begin wireframing initial designs by February 20, 2025.</li><li>Mark to schedule a follow-up meeting next week for progress updates.</li></ul></li></ul><p><strong>Next Meeting:</strong></p><ul><li>Scheduled for February 20, 2025, at 10:00 AM.</li></ul>', 2, 0, 'project', NULL, 2, '2025-02-13 10:29:26', '2025-02-18 18:33:23', 'style4'),
	(8, 'Server Credentials', '<p>Here\'s the server credentials:</p><p>IP: 1.1.1.1</p><p>User: root</p><p>Password: test@123</p>', 6, 0, 'project', 6, 2, '2025-02-20 10:24:29', NULL, 'style1');

-- Dumping structure for table lead_platform.note_access
CREATE TABLE IF NOT EXISTS `note_access` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `user_id` int NOT NULL,
  `has_access` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `note_access_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `note_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.note_access: ~0 rows (approximately)

-- Dumping structure for table lead_platform.note_attachments
CREATE TABLE IF NOT EXISTS `note_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`),
  CONSTRAINT `note_attachments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.note_attachments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.note_categories
CREATE TABLE IF NOT EXISTS `note_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.note_categories: ~5 rows (approximately)
REPLACE INTO `note_categories` (`id`, `name`, `created_at`) VALUES
	(1, 'Remember', '2025-02-13 07:17:11'),
	(2, 'Meeting', '2025-02-13 10:23:35'),
	(3, 'test', '2025-02-14 06:37:15'),
	(4, 'test123', '2025-02-14 06:40:08'),
	(5, 'personal life', '2025-02-14 07:36:16'),
	(6, 'Credentials', '2025-02-20 08:50:10');

-- Dumping structure for table lead_platform.note_comments
CREATE TABLE IF NOT EXISTS `note_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `note_comments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `note_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.note_comments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.note_tags
CREATE TABLE IF NOT EXISTS `note_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note_id` int NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`),
  CONSTRAINT `note_tags_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.note_tags: ~0 rows (approximately)

-- Dumping structure for table lead_platform.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `related_id` int DEFAULT NULL,
  `type` enum('task_reminder','lead_update','system') COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.notifications: ~17 rows (approximately)
REPLACE INTO `notifications` (`id`, `user_id`, `message`, `related_id`, `type`, `is_read`, `created_at`) VALUES
	(1, 2, 'Reminder: Task \'jkj\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 1, '2025-01-29 10:24:00'),
	(2, 2, 'Reminder: Task \'jkj\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 1, '2025-01-29 10:24:00'),
	(3, 2, 'Reminder: Task \'jkj\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 1, '2025-01-29 10:24:00'),
	(4, 2, 'Reminder: Task \'Manual Book Read\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 1, '2025-01-29 10:24:00'),
	(5, 2, 'Reminder: Task \'Buy steel for the bridge\' is due on 2025-02-03 03:23:00.', 6, 'task_reminder', 1, '2025-02-02 21:53:00'),
	(6, 2, 'Reminder: Task \'procure cement after buying steel\' is due on 2025-01-31 03:36:00.', 8, 'task_reminder', 0, '2025-01-30 22:06:00'),
	(7, 2, 'Reminder: Task \'test\' is due on 2025-01-31 04:23:00.', 11, 'task_reminder', 1, '2025-01-30 22:53:00'),
	(8, 2, 'Reminder: Task \'Buy water for the plant\' is due on 2025-01-31 03:42:00.', 9, 'task_reminder', 1, '2025-01-30 22:12:00'),
	(9, 2, 'Reminder: Task \'Buy steel for the bridge\' is due on 2025-02-03 03:23:00.', 6, 'task_reminder', 1, '2025-02-02 21:53:00'),
	(10, 2, 'Reminder: Task \'Manual Book Read\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 0, '2025-01-29 10:24:00'),
	(11, 2, 'Reminder: Task \'procure cement after buying steel\' is due on 2025-01-31 03:36:00.', 8, 'task_reminder', 0, '2025-01-30 22:06:00'),
	(12, 2, 'Reminder: Task \'procure cement after buying steel\' is due on 2025-01-31 03:36:00.', 8, 'task_reminder', 0, '2025-01-30 22:06:00'),
	(13, 2, 'Reminder: Task \'Manual Book Read\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 0, '2025-01-29 10:24:00'),
	(14, 2, 'Reminder: Task \'procure cement after buying steel\' is due on 2025-01-31 03:36:00.', 8, 'task_reminder', 0, '2025-01-30 22:06:00'),
	(15, 2, 'Reminder: Task \'Make transparent dashboard\' is due on 2025-01-31 16:25:00.', 12, 'task_reminder', 1, '2025-01-31 10:55:00'),
	(16, 2, 'Reminder: Task \'procure cement after buying steel\' is due on 2025-01-31 03:36:00.', 8, 'task_reminder', 0, '2025-01-30 22:06:00'),
	(17, 2, 'Reminder: Task \'Manual Book Read\' is due on 2025-01-29 15:54:00.', 2, 'task_reminder', 0, '2025-01-29 10:24:00');

-- Dumping structure for table lead_platform.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` enum('Credit Card','Bank Transfer','PayPal','Cheque') COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.payments: ~4 rows (approximately)
REPLACE INTO `payments` (`id`, `invoice_id`, `payment_date`, `payment_method`, `transaction_id`, `amount`) VALUES
	(1, 4, '2025-01-29 11:52:19', 'Credit Card', '1234', 24.00),
	(2, 3, '2025-01-29 11:59:30', 'Cheque', '48848', 43.00),
	(10, 13, '2025-02-12 11:32:43', 'Credit Card', '13940', 120.00),
	(11, 13, '2025-02-12 11:48:12', 'Credit Card', '192849', 0.00);

-- Dumping structure for table lead_platform.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `assigned_lead_customer_id` int DEFAULT NULL,
  `assigned_lead_customer_type` enum('lead','customer') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_manager_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Not Started','In Progress','Completed','On Hold','Canceled') COLLATE utf8mb4_general_ci DEFAULT 'Not Started',
  `priority` enum('High','Medium','Low') COLLATE utf8mb4_general_ci DEFAULT 'Medium',
  `project_category_id` int DEFAULT NULL,
  `billing_type` enum('Hourly','Fixed Price','Retainer') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`),
  KEY `project_manager_id` (`project_manager_id`),
  KEY `project_category_id` (`project_category_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`project_manager_id`) REFERENCES `users` (`id`),
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`project_category_id`) REFERENCES `project_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.projects: ~3 rows (approximately)
REPLACE INTO `projects` (`id`, `project_id`, `name`, `assigned_lead_customer_id`, `assigned_lead_customer_type`, `project_manager_id`, `start_date`, `end_date`, `status`, `priority`, `project_category_id`, `billing_type`, `budget`, `description`, `created_at`) VALUES
	(6, 'PROJ-20250129-001', 'Metro Infrastructure Project', 2, 'customer', 2, '2025-01-31', '2025-02-14', 'In Progress', 'High', 1, 'Retainer', 10000.00, 'Metro Infrastructure ', '2025-01-29 20:52:11'),
	(7, 'PROJ-20250219-007', 'Network Systems Development', 7, 'lead', 4, '2025-02-21', '2025-02-27', 'Not Started', 'Medium', 1, 'Fixed Price', 15000.00, 'Client wants a systems engineer to deploy their large scale system to AWS.', '2025-02-19 11:27:41'),
	(8, 'PROJ-20250219-008', 'OLED Monitor Development', 2, 'customer', 2, '2025-02-19', '2025-02-27', 'Completed', 'High', 1, 'Hourly', 500.00, 'The goal of this project is to design, develop, and manufacture an advanced OLED (Organic Light Emitting Diode) monitor, aiming to revolutionize the display market by offering superior image quality, enhanced energy efficiency, and a more flexible design compared to traditional LCD and LED monitors.\r\nOLED technology utilizes organic compounds that emit light when an electric current is applied, enabling thinner, lighter displays with better contrast ratios, vibrant colors, and faster refresh rates. This project will focus on creating a monitor that not only delivers stunning visual quality but also addresses key aspects such as durability, energy consumption, and user experience.\r\n', '2025-02-19 11:29:35');

-- Dumping structure for table lead_platform.project_categories
CREATE TABLE IF NOT EXISTS `project_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.project_categories: ~0 rows (approximately)
REPLACE INTO `project_categories` (`id`, `name`, `created_at`) VALUES
	(1, 'Industrialization', '2025-01-29 20:51:22');

-- Dumping structure for table lead_platform.project_features
CREATE TABLE IF NOT EXISTS `project_features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `feature_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `feature_title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `priority` enum('Low','Medium','High','Critical') COLLATE utf8mb4_general_ci DEFAULT 'Medium',
  `status` enum('Planned','In Progress','Under Review','Completed','Deferred') COLLATE utf8mb4_general_ci DEFAULT 'Planned',
  `owner_id` int DEFAULT NULL,
  `estimated_completion_date` date DEFAULT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feature_id` (`feature_id`),
  KEY `project_id` (`project_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `project_features_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_features_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.project_features: ~0 rows (approximately)
REPLACE INTO `project_features` (`id`, `project_id`, `feature_id`, `feature_title`, `description`, `priority`, `status`, `owner_id`, `estimated_completion_date`, `actual_completion_date`, `created_by`, `created_date`) VALUES
	(1, 6, 'FEAT-20250212-001', 'Metro station with announcement features', 'Metro Stations should have an announcement feature to tell users the next station time', 'Low', 'Planned', 4, '2025-02-14', NULL, 2, '2025-02-12 19:19:46'),
	(2, 8, 'FEAT-20250221-002', 'UI tracker', 'Need a UI tracker', 'Medium', 'In Progress', 1, '2025-02-22', NULL, 2, '2025-02-21 13:53:14');

-- Dumping structure for table lead_platform.project_issues
CREATE TABLE IF NOT EXISTS `project_issues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `issue_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `issue_title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `issue_type` enum('Bug','Enhancement','Task','Improvement') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Bug',
  `priority` enum('Low','Medium','High','Critical') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Medium',
  `status` enum('Open','In Progress','Resolved','Closed','Reopened') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Open',
  `reported_by` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `related_feature_id` int DEFAULT NULL,
  `date_reported` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolution_date` date DEFAULT NULL,
  `steps_to_reproduce` text COLLATE utf8mb4_general_ci,
  `environment_version` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issue_id` (`issue_id`),
  KEY `project_id` (`project_id`),
  KEY `reported_by` (`reported_by`),
  KEY `assigned_to` (`assigned_to`),
  KEY `related_feature_id` (`related_feature_id`),
  CONSTRAINT `project_issues_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_issues_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_issues_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_issues_ibfk_4` FOREIGN KEY (`related_feature_id`) REFERENCES `project_features` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.project_issues: ~0 rows (approximately)
REPLACE INTO `project_issues` (`id`, `project_id`, `issue_id`, `issue_title`, `description`, `issue_type`, `priority`, `status`, `reported_by`, `assigned_to`, `related_feature_id`, `date_reported`, `resolution_date`, `steps_to_reproduce`, `environment_version`) VALUES
	(1, 6, 'ISSUE-20250212-001', 'Track construction delay in Zone 4', 'The construction in Zone 4 has encountered unexpected delays due to weather conditions, which has caused a setback in the overall timeline. The issue may result in delays for the entire metro line completion.', 'Enhancement', 'High', 'Open', 2, 2, 1, '2025-02-12 20:57:38', '2025-02-15', '1) Review current construction status in Zone 4.\r\n2) Assess weather conditions over the last two weeks.\r\n3) Identify the impact on the work schedule.', 'Metro Infrastructure Project Version 2.3, Zone 4 Construction Site');

-- Dumping structure for table lead_platform.reconciliation_ledger_entries
CREATE TABLE IF NOT EXISTS `reconciliation_ledger_entries` (
  `reconciliation_id` int NOT NULL,
  `ledger_entry_id` int NOT NULL,
  `difference_amount` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`reconciliation_id`,`ledger_entry_id`),
  KEY `ledger_entry_id` (`ledger_entry_id`),
  CONSTRAINT `reconciliation_ledger_entries_ibfk_1` FOREIGN KEY (`reconciliation_id`) REFERENCES `reconciliation_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reconciliation_ledger_entries_ibfk_2` FOREIGN KEY (`ledger_entry_id`) REFERENCES `ledger_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.reconciliation_ledger_entries: ~0 rows (approximately)

-- Dumping structure for table lead_platform.reconciliation_records
CREATE TABLE IF NOT EXISTS `reconciliation_records` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reconciliation_date` date NOT NULL,
  `bank_statement_reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_difference_amount` decimal(10,2) DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.reconciliation_records: ~0 rows (approximately)

-- Dumping structure for table lead_platform.subtasks
CREATE TABLE IF NOT EXISTS `subtasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `assigned_to` int DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.subtasks: ~0 rows (approximately)

-- Dumping structure for table lead_platform.support_tickets
CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `priority` enum('High','Medium','Low') COLLATE utf8mb4_general_ci DEFAULT 'Low',
  `assigned_to` int DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('New','In Progress','Resolved','Closed') COLLATE utf8mb4_general_ci DEFAULT 'New',
  `expected_resolution_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.support_tickets: ~2 rows (approximately)
REPLACE INTO `support_tickets` (`id`, `user_id`, `title`, `description`, `priority`, `assigned_to`, `category`, `status`, `expected_resolution_date`, `created_at`, `project_id`) VALUES
	(1, 2, 'Write Documentation', 'Write a detailed documentation', 'Low', 2, 'Documentation Request', 'New', '2025-01-31', '2025-01-30 12:30:38', NULL),
	(2, 2, 'Vendor Credits Purchase', 'Need to purchase credits', 'Medium', 2, 'Infrastructure', 'In Progress', '2025-02-01', '2025-01-30 14:21:52', 6);

-- Dumping structure for table lead_platform.support_ticket_attachments
CREATE TABLE IF NOT EXISTS `support_ticket_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `support_ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.support_ticket_attachments: ~0 rows (approximately)
REPLACE INTO `support_ticket_attachments` (`id`, `ticket_id`, `file_name`, `file_path`, `created_at`) VALUES
	(1, 1, '20-0129_DEMO.png', 'uploads/679c3db6a1e8a_20-0129_DEMO.png', '2025-01-31 03:04:22');

-- Dumping structure for table lead_platform.support_ticket_comments
CREATE TABLE IF NOT EXISTS `support_ticket_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `support_ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.support_ticket_comments: ~4 rows (approximately)
REPLACE INTO `support_ticket_comments` (`id`, `ticket_id`, `user_id`, `comment`, `created_at`) VALUES
	(1, 1, 2, 'Hi guys, any update on this ticket?', '2025-01-30 12:30:50'),
	(2, 1, 2, 'Its under development', '2025-01-30 14:10:52'),
	(3, 1, 2, 'Hi team ?', '2025-01-31 03:03:42'),
	(4, 1, 2, 'Yo', '2025-01-31 03:11:23'),
	(5, 2, 2, 'test', '2025-02-18 16:33:23');

-- Dumping structure for table lead_platform.support_ticket_tasks
CREATE TABLE IF NOT EXISTS `support_ticket_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `due_date` datetime DEFAULT NULL,
  `status` enum('To Do','In Progress','Completed') COLLATE utf8mb4_general_ci DEFAULT 'To Do',
  `assigned_to` int DEFAULT NULL,
  `priority` enum('Low','Medium','High') COLLATE utf8mb4_general_ci DEFAULT 'Medium',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `support_ticket_tasks_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.support_ticket_tasks: ~0 rows (approximately)
REPLACE INTO `support_ticket_tasks` (`id`, `ticket_id`, `title`, `description`, `due_date`, `status`, `assigned_to`, `priority`, `created_at`) VALUES
	(1, 2, 'Hire an engineer for this', 'Hire an angular developer', '2025-01-31 23:04:00', 'In Progress', 4, 'Medium', '2025-01-30 17:35:13');

-- Dumping structure for table lead_platform.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `task_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `task_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `task_type` enum('Follow-Up','Meeting','Deadline') COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `due_date` datetime DEFAULT NULL,
  `status` enum('To Do','In Progress','Completed','Blocked','Canceled','Pending') COLLATE utf8mb4_general_ci DEFAULT 'To Do',
  `estimated_hours` decimal(6,2) DEFAULT NULL,
  `effort_estimation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `billable` tinyint(1) DEFAULT '0',
  `priority` enum('Low','Medium','High') COLLATE utf8mb4_general_ci DEFAULT 'Medium',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_id` (`task_id`),
  KEY `lead_id` (`lead_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.tasks: ~11 rows (approximately)
REPLACE INTO `tasks` (`id`, `lead_id`, `user_id`, `task_id`, `task_name`, `project_id`, `task_type`, `description`, `due_date`, `status`, `estimated_hours`, `effort_estimation`, `billable`, `priority`, `created_at`) VALUES
	(2, NULL, 2, NULL, 'Read Training Manual', NULL, 'Meeting', 'Manual Book Read', '2025-01-29 15:54:00', 'To Do', 10.00, NULL, 0, 'Medium', '2025-01-28 10:27:02'),
	(3, NULL, 2, 'TASK-20250129-003', 'Credits Loadup', NULL, 'Deadline', 'Loadup credits on the vendor page', '2025-01-31 02:49:00', 'Completed', 10.00, NULL, 1, 'Medium', '2025-01-29 21:20:37'),
	(4, NULL, 2, 'TASK-20250129-004', 'Metro Bridge', NULL, 'Meeting', 'Build a metro bridge', '2025-02-14 02:59:00', 'Completed', 1000.00, NULL, 1, 'High', '2025-01-29 21:29:44'),
	(5, NULL, 2, 'TASK-20250129-005', 'test task', NULL, 'Meeting', 'Test Task', '2025-02-02 03:12:00', 'In Progress', 1001.00, NULL, 1, 'Medium', '2025-01-29 21:42:31'),
	(6, NULL, 2, 'TASK-20250129-006', 'Buy Steel', 6, 'Deadline', 'Buy steel for the bridge', '2025-02-03 03:23:00', 'To Do', 10.00, NULL, 1, 'High', '2025-01-29 21:53:41'),
	(7, NULL, 2, 'TASK-20250129-007', 'testing Related stuff2', 6, 'Follow-Up', 'Test related', '2025-02-01 03:34:00', 'In Progress', 10.00, NULL, 1, 'Low', '2025-01-29 22:05:22'),
	(8, NULL, 2, 'TASK-20250129-008', 'procuring cement', 6, 'Follow-Up', 'procure cement after buying steel', '2025-01-31 03:36:00', 'Completed', 100.00, NULL, 1, 'Low', '2025-01-29 22:07:12'),
	(9, NULL, 2, 'TASK-20250129-009', 'Buy water', 6, 'Follow-Up', 'Buy water for the plant', '2025-01-31 03:42:00', 'Completed', 10.00, NULL, 0, 'High', '2025-01-29 22:13:15'),
	(10, NULL, 2, 'TASK-20250129-010', 'fgf', 6, 'Follow-Up', 'jhj', '2025-01-31 03:50:00', 'Completed', 6.00, NULL, 0, 'Low', '2025-01-29 22:20:23'),
	(11, NULL, 2, 'TASK-20250129-011', 'test', 6, 'Follow-Up', 'test', '2025-01-31 04:23:00', 'Canceled', 100.00, NULL, 0, 'Low', '2025-01-29 22:54:26'),
	(12, NULL, 2, 'TASK-20250130-012', 'Transparent dashboard', 6, 'Follow-Up', 'Make transparent dashboard', '2025-01-31 16:25:00', 'In Progress', 10.00, NULL, 0, 'Low', '2025-01-30 10:55:30');

-- Dumping structure for table lead_platform.task_attachments
CREATE TABLE IF NOT EXISTS `task_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `task_attachments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_attachments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.task_comments
CREATE TABLE IF NOT EXISTS `task_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `task_comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_comments: ~0 rows (approximately)

-- Dumping structure for table lead_platform.task_custom_fields
CREATE TABLE IF NOT EXISTS `task_custom_fields` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `field_value` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `task_custom_fields_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_custom_fields: ~0 rows (approximately)

-- Dumping structure for table lead_platform.task_dependencies
CREATE TABLE IF NOT EXISTS `task_dependencies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `depends_on_task_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `depends_on_task_id` (`depends_on_task_id`),
  CONSTRAINT `task_dependencies_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_dependencies_ibfk_2` FOREIGN KEY (`depends_on_task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_dependencies: ~0 rows (approximately)

-- Dumping structure for table lead_platform.task_priorities
CREATE TABLE IF NOT EXISTS `task_priorities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `color` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_priorities: ~0 rows (approximately)

-- Dumping structure for table lead_platform.task_tags
CREATE TABLE IF NOT EXISTS `task_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `tag` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `task_tags_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_tags: ~0 rows (approximately)

-- Dumping structure for table lead_platform.task_time_logs
CREATE TABLE IF NOT EXISTS `task_time_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int NOT NULL,
  `user_id` int NOT NULL,
  `log_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `hours_spent` decimal(4,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `task_time_logs_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_time_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.task_time_logs: ~0 rows (approximately)

-- Dumping structure for table lead_platform.team_departments
CREATE TABLE IF NOT EXISTS `team_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.team_departments: ~0 rows (approximately)
REPLACE INTO `team_departments` (`id`, `name`, `created_at`) VALUES
	(1, 'Frontend', '2025-01-30 17:24:24');

-- Dumping structure for table lead_platform.team_roles
CREATE TABLE IF NOT EXISTS `team_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.team_roles: ~8 rows (approximately)
REPLACE INTO `team_roles` (`id`, `name`, `created_at`) VALUES
	(1, 'Support Role', '2025-01-30 14:46:00'),
	(2, 'Development Team A', '2025-01-30 14:47:27'),
	(3, 'Dev Team B', '2025-01-30 14:47:43'),
	(4, 'Dev Team C', '2025-01-30 14:47:50'),
	(5, 'QA Team', '2025-01-30 14:48:01'),
	(6, 'Marketing Team', '2025-01-30 14:48:22'),
	(7, 'Design and UI Team', '2025-01-30 14:48:36'),
	(8, 'Team Lead', '2025-01-30 14:48:56');

-- Dumping structure for table lead_platform.todos
CREATE TABLE IF NOT EXISTS `todos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `due_date` datetime DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  `related_type` enum('task','lead','customer') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `related_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `todos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.todos: ~0 rows (approximately)
REPLACE INTO `todos` (`id`, `user_id`, `title`, `description`, `due_date`, `is_completed`, `related_type`, `related_id`, `created_at`) VALUES
	(1, 2, 'Checkout Davos', 'It\'s the client meeting there today!', '2025-01-28 15:40:00', 0, 'lead', 8, '2025-01-28 10:09:21');

-- Dumping structure for table lead_platform.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `credits` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `imap_server` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imap_port` int DEFAULT NULL,
  `imap_username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imap_password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `smtp_server` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `smtp_port` int DEFAULT NULL,
  `smtp_username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `smtp_password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `smtp_security` enum('tls','ssl','none') COLLATE utf8mb4_general_ci DEFAULT 'tls',
  `theme` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'default',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `department_id` (`department_id`),
  KEY `id` (`id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `team_roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `team_departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.users: ~4 rows (approximately)
REPLACE INTO `users` (`id`, `username`, `email`, `password`, `credits`, `created_at`, `role`, `profile_picture`, `role_id`, `department_id`, `imap_server`, `imap_port`, `imap_username`, `imap_password`, `smtp_server`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_security`, `theme`) VALUES
	(1, 'Peel Hullin', 'user@demo.com', '$2y$10$8z2JpAs7QU3aMkHL59SC.O4rjZevbePDApd7947XYt.LfdOVlvA7.', 100, '2025-01-26 05:53:17', 'user', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tls', 'default'),
	(2, 'Bruh', 'admin@demo.com', '$2y$10$qtyaY8G3jceTluy42gCT.ey.SYmGAUcj5Oi3bnDxOxnCL.7w4nbJq', 0, '2025-01-26 06:17:01', 'admin', 'public/uploads/profile/67b5719a63938_angienotion-1707764852790.png', NULL, NULL, 'imap.hostinger.com', 993, 'support@milddreams.com', 'WDhxb3Qzci8yMktrVnFzVjNTNXZlUT09OjpMM66cNwmRZOa/gFsJRawd', 'smtp.hostinger.com', 465, 'support@milddreams.com', 'N0gyQ2FQNnhaSmV2L0JaOTR4TFMvZz09Ojp7h/Xxialq5pmPSjggg5+6', 'ssl', 'default'),
	(4, 'John The Support Man', 'john@support.com', '$2y$10$6mZ3cSv8FM7fxg3Ui6JwquHyYnTtHsx1H9ZxtaFYqHG/anoV0C1o.', 0, '2025-01-30 14:46:28', 'user', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tls', 'default'),
	(5, 'admin2', 'admin2@demo.com', '$2y$10$xhjAQ4eMVDwSaP5gB2x0sus4lk/9uU7MJZF9NHcQ0o9cyZifO6.b6', 0, '2025-01-31 21:26:14', 'admin', 'public/uploads/profile/679d4030dfe71_M0ekXd9R_400x400.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tls', 'default');

-- Dumping structure for table lead_platform.user_credits
CREATE TABLE IF NOT EXISTS `user_credits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `credits` int DEFAULT '0',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_credits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.user_credits: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
