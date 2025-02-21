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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contracts: ~2 rows (approximately)
REPLACE INTO `contracts` (`id`, `project_id`, `customer_id`, `subject`, `contract_value`, `contract_type`, `start_date`, `end_date`, `description`, `hide_from_customer`, `contract_text`, `created_at`, `updated_at`) VALUES
	(1, 6, 1, 'Bridge Development', 1304.00, 'Legal', '2025-02-01', '2025-02-06', 'Development Contract for the bridge', 0, '<p>TEST Contract Text</p>', '2025-02-01 12:14:36', NULL),
	(2, NULL, 2, 'Tower Development', 1300.00, 'Legal', '2025-01-01', '2025-03-15', 'Legal contract for the tower development', 0, '<p><strong>This Infrastructure Development Agreement</strong> (the “Agreement”) is made on this [Date], by and between:</p><p><strong>1. [Client Name/Company Name]</strong>, a company incorporated under the laws of [Country/State], having its principal office at [Address] (hereinafter referred to as “Client”),<br>AND<br><strong>2. [Contractor Name/Company Name]</strong>, a company incorporated under the laws of [Country/State], having its principal office at [Address] (hereinafter referred to as “Contractor”).</p>', '2025-02-01 12:27:30', '2025-02-01 13:22:38');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
	(11, 2, 5, 'Added comment', 'Nice', NULL, NULL, NULL, NULL, '2025-02-01 20:59:21');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contract_signatures: ~0 rows (approximately)
REPLACE INTO `contract_signatures` (`id`, `contract_id`, `user_id`, `signature_data`, `signed_at`) VALUES
	(7, 2, 5, 'data:image/png;base64,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', '2025-02-01 20:56:42');

-- Dumping structure for table lead_platform.contract_status
CREATE TABLE IF NOT EXISTS `contract_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_id` int NOT NULL,
  `status` enum('Draft','Sent','Signed','Active','Expired','Canceled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  `status_changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`),
  CONSTRAINT `contract_status_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.contract_status: ~17 rows (approximately)
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
	(17, 2, 'Active', '2025-02-01 20:56:42');

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
	(8, 3, 2, 'employee', '2025-02-19 18:08:42', '2025-01-31 21:10:19'),
	(9, 3, 1, 'customer', NULL, '2025-01-31 21:10:19');

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
	(8, 'Jabbar2', '12312312', 'jabbar@demo.com', 1, '2025-01-28 08:55:40', 'Converted', 1, 2, NULL, NULL, NULL, 'Website', 2),
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

-- Dumping data for table lead_platform.notes: ~1 rows (approximately)
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lead_platform.project_features: ~0 rows (approximately)
REPLACE INTO `project_features` (`id`, `project_id`, `feature_id`, `feature_title`, `description`, `priority`, `status`, `owner_id`, `estimated_completion_date`, `actual_completion_date`, `created_by`, `created_date`) VALUES
	(1, 6, 'FEAT-20250212-001', 'Metro station with announcement features', 'Metro Stations should have an announcement feature to tell users the next station time', 'Low', 'Planned', 4, '2025-02-14', NULL, 2, '2025-02-12 19:19:46');

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
	(7, NULL, 2, 'TASK-20250129-007', 'testing Related stuff', 6, 'Follow-Up', 'Test related', '2025-02-01 03:34:00', 'In Progress', 10.00, NULL, 1, 'Low', '2025-01-29 22:05:22'),
	(8, NULL, 2, 'TASK-20250129-008', 'procuring cement', 6, 'Follow-Up', 'procure cement after buying steel', '2025-01-31 03:36:00', 'To Do', 100.00, NULL, 1, 'Low', '2025-01-29 22:07:12'),
	(9, NULL, 2, 'TASK-20250129-009', 'Buy water', 6, 'Follow-Up', 'Buy water for the plant', '2025-01-31 03:42:00', 'Blocked', 10.00, NULL, 0, 'High', '2025-01-29 22:13:15'),
	(10, NULL, 2, 'TASK-20250129-010', 'fgf', 6, 'Follow-Up', 'jhj', '2025-01-31 03:50:00', 'Completed', 6.00, NULL, 0, 'Low', '2025-01-29 22:20:23'),
	(11, NULL, 2, 'TASK-20250129-011', 'test', 6, 'Follow-Up', 'test', '2025-01-31 04:23:00', 'Canceled', 100.00, NULL, 0, 'Low', '2025-01-29 22:54:26'),
	(12, NULL, 2, 'TASK-20250130-012', 'Transparent dashboard', 6, 'Follow-Up', 'Make transparent dashboard', '2025-01-31 16:25:00', 'To Do', 10.00, NULL, 0, 'Low', '2025-01-30 10:55:30');

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
