-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 14, 2025 at 07:35 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lead_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountants`
--

CREATE TABLE `accountants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `role` enum('Accountant','Senior Accountant') NOT NULL DEFAULT 'Accountant',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accountants`
--

INSERT INTO `accountants` (`id`, `name`, `email`, `contact_number`, `role`, `active`, `created_at`) VALUES
(1, 'TEST', 'accountant@demo.com', '123456', 'Accountant', 1, '2025-02-12 07:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` enum('contract','proposal','notes') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `lead_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 2, 'test', NULL, 'notes', '2025-01-27 15:12:13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'IT', '2025-01-26 06:52:51'),
(2, 'Restaurants', '2025-01-26 06:53:15'),
(3, 'TEST', '2025-02-08 18:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `contract_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `contract_type` varchar(100) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `hide_from_customer` tinyint(1) DEFAULT 0,
  `contract_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `project_id`, `customer_id`, `subject`, `contract_value`, `contract_type`, `start_date`, `end_date`, `description`, `hide_from_customer`, `contract_text`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 'Bridge Development', 1304.00, 'Legal', '2025-02-01', '2025-02-06', 'Development Contract for the bridge', 0, '<p>TEST Contract Text</p>', '2025-02-01 12:14:36', NULL),
(2, NULL, 2, 'Tower Development', 1300.00, 'Legal', '2025-01-01', '2025-03-15', 'Legal contract for the tower development', 0, '<p><strong>This Infrastructure Development Agreement</strong> (the “Agreement”) is made on this [Date], by and between:</p><p><strong>1. [Client Name/Company Name]</strong>, a company incorporated under the laws of [Country/State], having its principal office at [Address] (hereinafter referred to as “Client”),<br>AND<br><strong>2. [Contractor Name/Company Name]</strong>, a company incorporated under the laws of [Country/State], having its principal office at [Address] (hereinafter referred to as “Contractor”).</p>', '2025-02-01 12:27:30', '2025-02-01 13:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `contract_audit_trail`
--

CREATE TABLE `contract_audit_trail` (
  `id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `geolocation_data` text DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `device_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contract_audit_trail`
--

INSERT INTO `contract_audit_trail` (`id`, `contract_id`, `user_id`, `action`, `details`, `ip_address`, `geolocation_data`, `timezone`, `device_info`, `created_at`) VALUES
(1, 2, 5, 'Signature Added', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:34:42'),
(2, 2, 5, 'Signature Added', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:35:14'),
(3, 2, 5, 'Added comment', 'Great', NULL, NULL, NULL, NULL, '2025-02-01 12:39:32'),
(4, 2, 5, 'Signature Added', NULL, '::1', '[]', 'Europe/Berlin', '{\"browser\":\"Safari\",\"os\":\"Mac\"}', '2025-02-01 12:40:10'),
(5, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:50:13'),
(6, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 12:50:27'),
(7, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 13:22:38'),
(8, 2, 5, 'Signature Added', NULL, '::1', NULL, 'Europe/Berlin', '{\"browser\":\"Safari\",\"os\":\"Mac\"}', '2025-02-01 14:04:13'),
(9, 2, 5, 'Contract Updated', NULL, NULL, NULL, NULL, NULL, '2025-02-01 20:56:29'),
(10, 2, 5, 'Signature Added', NULL, '::1', NULL, 'Europe/Berlin', '{\"browser\":\"Safari\",\"os\":\"Mac\"}', '2025-02-01 20:56:42'),
(11, 2, 5, 'Added comment', 'Nice', NULL, NULL, NULL, NULL, '2025-02-01 20:59:21');

-- --------------------------------------------------------

--
-- Table structure for table `contract_signatures`
--

CREATE TABLE `contract_signatures` (
  `id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signature_data` text DEFAULT NULL,
  `signed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contract_signatures`
--

INSERT INTO `contract_signatures` (`id`, `contract_id`, `user_id`, `signature_data`, `signed_at`) VALUES
(7, 2, 5, 'data:image/png;base64,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', '2025-02-01 20:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `contract_status`
--

CREATE TABLE `contract_status` (
  `id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `status` enum('Draft','Sent','Signed','Active','Expired','Canceled') NOT NULL DEFAULT 'Draft',
  `status_changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contract_status`
--

INSERT INTO `contract_status` (`id`, `contract_id`, `status`, `status_changed_at`) VALUES
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

-- --------------------------------------------------------

--
-- Table structure for table `contract_types`
--

CREATE TABLE `contract_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contract_types`
--

INSERT INTO `contract_types` (`id`, `name`, `created_at`) VALUES
(1, 'Legal', '2025-02-01 07:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company` varchar(255) DEFAULT NULL,
  `last_interaction` timestamp NULL DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `social_media_profiles` text DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `created_at`, `company`, `last_interaction`, `address`, `social_media_profiles`, `age`, `gender`, `location`, `job_title`, `industry`, `profile_picture`) VALUES
(1, 'Jabbar2', 'jabbar@demo.com', '12312312', '2025-01-28 08:56:01', 'Jabbar Corporations', '2025-01-28 09:13:46', 'NYC', 'https://instagram.com', 44, 'Male', 'NYC', 'Chairman', 'IT', 'uploads/profile/6798a5f924b68_pexels-photo-771742.jpeg'),
(2, 'POP', 'pop@pop.com', '123123', '2025-01-29 11:57:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'test', 'test@demo.com', '12345', '2025-02-06 04:01:23', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_custom_fields`
--

CREATE TABLE `customer_custom_fields` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_interactions`
--

CREATE TABLE `customer_interactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `interaction_type` enum('Call','Email','Meeting','Other') NOT NULL,
  `details` text DEFAULT NULL,
  `interaction_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_interactions`
--

INSERT INTO `customer_interactions` (`id`, `customer_id`, `interaction_type`, `details`, `interaction_at`) VALUES
(1, 1, 'Email', 'Customer wants to discuss more over call.', '2025-01-28 09:18:57'),
(2, 1, 'Call', 'Customer wants to meet IRL', '2025-01-28 09:19:30'),
(3, 1, 'Meeting', 'Meeting went great! Wants to discuss more!', '2025-01-28 09:29:58'),
(4, 1, 'Email', 'He wants a landing page', '2025-01-30 01:47:38');

-- --------------------------------------------------------

--
-- Table structure for table `customer_preferences`
--

CREATE TABLE `customer_preferences` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `preference` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_preferences`
--

INSERT INTO `customer_preferences` (`id`, `customer_id`, `preference`, `created_at`) VALUES
(1, 1, 'Likes to talk', '2025-01-28 09:18:17'),
(2, 1, 'Wears great suit', '2025-01-28 09:29:29'),
(3, 1, 'blabbers', '2025-01-29 11:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `customer_tags`
--

CREATE TABLE `customer_tags` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `color` varchar(20) DEFAULT 'gray',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_tags`
--

INSERT INTO `customer_tags` (`id`, `customer_id`, `tag`, `color`, `created_at`) VALUES
(1, 1, 'VIP', 'gray', '2025-01-28 09:25:13'),
(2, 1, 'High value', 'red', '2025-01-28 09:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('internal','external') NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussions`
--

INSERT INTO `discussions` (`id`, `title`, `user_id`, `type`, `status`, `created_at`) VALUES
(1, 'Initial Documentation Request', 2, 'internal', 'closed', '2025-01-31 20:35:30'),
(3, 'Legal Document Request', 2, 'external', 'closed', '2025-01-31 21:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `discussion_attachments`
--

CREATE TABLE `discussion_attachments` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_messages`
--

CREATE TABLE `discussion_messages` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('user','employee','customer') NOT NULL DEFAULT 'user',
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_messages`
--

INSERT INTO `discussion_messages` (`id`, `discussion_id`, `user_id`, `user_type`, `message`, `sent_at`, `parent_id`) VALUES
(1, 1, 2, 'user', 'Hi Jabbar,\r\nPlease provide the initial documentation for your repo?', '2025-01-31 20:35:30', NULL),
(3, 3, 2, 'user', 'Hi Jabbar,\r\nPlease send your document', '2025-01-31 21:10:19', NULL),
(4, 3, 5, 'user', 'Where should I send it?', '2025-01-31 21:27:46', 3),
(5, 1, 5, 'user', 'Sure!', '2025-01-31 21:59:26', NULL),
(8, 3, 5, 'user', 'Sure', '2025-01-31 22:11:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discussion_participants`
--

CREATE TABLE `discussion_participants` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `participant_type` enum('user','employee','customer') NOT NULL,
  `last_viewed` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_participants`
--

INSERT INTO `discussion_participants` (`id`, `discussion_id`, `participant_id`, `participant_type`, `last_viewed`, `created_at`) VALUES
(1, 1, 1, 'user', NULL, '2025-01-31 20:35:30'),
(2, 1, 1, 'employee', NULL, '2025-01-31 20:35:30'),
(3, 1, 1, 'customer', NULL, '2025-01-31 20:35:30'),
(7, 3, 4, 'user', NULL, '2025-01-31 21:10:19'),
(8, 3, 2, 'employee', '2025-02-10 21:24:24', '2025-01-31 21:10:19'),
(9, 3, 1, 'customer', NULL, '2025-01-31 21:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'Jordan Belfort', 'sales@demo.com', '123456789', '2025-01-28 03:04:22'),
(2, 'David', 'david@employee.com', '123456678', '2025-01-28 04:04:54');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_mode` enum('Cash','Credit Card','Bank Transfer','Online Payment','Check') DEFAULT 'Cash',
  `transaction_nature` enum('Reimbursable','Business Expense','Personal Expense') DEFAULT 'Business Expense',
  `receipt_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `name`, `category_id`, `amount`, `expense_date`, `project_id`, `user_id`, `invoice_id`, `payment_mode`, `transaction_nature`, `receipt_path`, `notes`, `approval_status`, `created_at`) VALUES
(1, 'AWS Bill', 1, 1220.00, '2025-01-29', 6, 2, 4, 'Credit Card', 'Reimbursable', NULL, 'AWS server bill racked up', 'Pending', '2025-01-31 04:19:55'),
(2, 'Battery', 1, 10.00, '2025-01-16', NULL, 2, NULL, 'Cash', 'Business Expense', 'uploads/receipts/679c74f7303aa_360_F_176121489_0n5AF6Y7zVXVahgAv2q66OLv5Lf1FR15.jpg', 'Battery swap', 'Pending', '2025-01-31 07:00:07'),
(3, 'Firmware Purchased', 2, 1304.00, '2025-01-28', NULL, 2, NULL, 'Online Payment', 'Personal Expense', 'uploads/receipts/679c764b97598_360_F_176121489_0n5AF6Y7zVXVahgAv2q66OLv5Lf1FR15.jpg', 'Purchased the hardware firmware', 'Pending', '2025-01-31 07:05:47');

-- --------------------------------------------------------

--
-- Table structure for table `expense_approvals`
--

CREATE TABLE `expense_approvals` (
  `id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `approver_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Server Costs', '2025-01-31 03:31:40'),
(2, 'Software Purchases', '2025-01-31 03:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `expense_comments`
--

CREATE TABLE `expense_comments` (
  `id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_comments`
--

INSERT INTO `expense_comments` (`id`, `expense_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 3, 2, 'HMM', '2025-01-31 07:08:33'),
(2, 3, 2, 'Nice', '2025-01-31 07:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `featured_knowledge_base_articles`
--

CREATE TABLE `featured_knowledge_base_articles` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feature_attachments`
--

CREATE TABLE `feature_attachments` (
  `id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feature_comments`
--

CREATE TABLE `feature_comments` (
  `id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feature_comments`
--

INSERT INTO `feature_comments` (`id`, `feature_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 2, 'Nice feature bruh', '2025-02-12 19:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `feature_dependencies`
--

CREATE TABLE `feature_dependencies` (
  `id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `depends_on_feature_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feature_resources`
--

CREATE TABLE `feature_resources` (
  `id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `resource_type` enum('Hours','Budget') NOT NULL,
  `estimated_value` decimal(10,2) DEFAULT 0.00,
  `actual_value` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feature_resources`
--

INSERT INTO `feature_resources` (`id`, `feature_id`, `resource_type`, `estimated_value`, `actual_value`, `notes`, `created_at`) VALUES
(1, 1, 'Budget', 1500.00, 250.00, 'Need more than this', '2025-02-12 21:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `feature_subtasks`
--

CREATE TABLE `feature_subtasks` (
  `id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('To Do','In Progress','Completed','Blocked') NOT NULL DEFAULT 'To Do',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feature_subtasks`
--

INSERT INTO `feature_subtasks` (`id`, `feature_id`, `title`, `description`, `assigned_to`, `due_date`, `status`, `created_at`) VALUES
(1, 1, 'Purchase microphones', 'Microphone vendor partner purchase', 2, '2025-02-14 03:10:00', 'To Do', '2025-02-12 21:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `bill_to_name` varchar(255) NOT NULL,
  `bill_to_address` varchar(255) DEFAULT NULL,
  `bill_to_email` varchar(100) DEFAULT NULL,
  `bill_to_phone` varchar(20) DEFAULT NULL,
  `ship_to_address` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `tax_method` varchar(50) DEFAULT NULL,
  `tax` text DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `additional_charges` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `payment_terms` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `footer` text DEFAULT NULL,
  `billing_country` varchar(50) DEFAULT NULL,
  `discount_type` varchar(20) DEFAULT 'fixed',
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `template_name` varchar(255) DEFAULT 'default',
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('Unpaid','Partially Paid','Paid','Overdue') DEFAULT 'Unpaid',
  `payment_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `lead_id`, `customer_id`, `issue_date`, `due_date`, `bill_to_name`, `bill_to_address`, `bill_to_email`, `bill_to_phone`, `ship_to_address`, `subtotal`, `tax_method`, `tax`, `discount`, `additional_charges`, `total`, `payment_terms`, `notes`, `footer`, `billing_country`, `discount_type`, `discount_amount`, `created_at`, `template_name`, `paid_amount`, `status`, `payment_date`) VALUES
(3, 'INV-20250129-001', 1, NULL, '2025-01-29', '2025-01-29', 'John Doe', 'NYC', 'john@demo.com', '+91 123456789', 'NYC', 0.00, 'GST', '[\"8.00\",\"5.00\"]', 0.00, 30.00, 43.00, 'Due on Receipt', '', '', 'in', 'fixed', 0.00, '2025-01-29 09:08:58', 'contractor', 43.00, 'Paid', '2025-01-29 11:59:30'),
(4, 'INV-20250129-004', NULL, 1, '2025-01-29', '2025-02-13', 'Jabbar2', 'NYC', 'jabbar@demo.com', '12312312', '', 0.00, 'GST', '[\"18.00\",\"18.00\"]', 0.00, 0.00, 36.00, 'Net 15', '', '', 'in', 'percentage', 10.00, '2025-01-29 09:43:24', 'default', 24.00, 'Partially Paid', '2025-01-29 11:52:19'),
(13, 'INV-20250212-005', NULL, 3, '2025-02-12', '2025-02-13', 'test', '', 'test@demo.com', '12345', '', 0.00, 'Sales Tax', '[\"120.00\"]', 0.00, 0.00, 120.00, 'Net 15', '', '', 'us', 'fixed', 0.00, '2025-02-12 11:32:39', 'contractor', 120.00, 'Paid', '2025-02-12 11:48:12');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_service` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_service`, `quantity`, `unit_price`, `tax`, `discount`, `subtotal`) VALUES
(22, 3, 'Product 2', 2, 120.00, 8.00, 10.00, 0.00),
(23, 3, 'Product 3', 3, 200.00, 5.00, 10.00, 0.00),
(47, 4, 'Product 1', 10, 120.00, 18.00, 10.00, 0.00),
(48, 4, 'Product 2', 10, 100.00, 18.00, 0.00, 0.00),
(50, 13, 'TEST 123', 1, 1500.00, 120.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_settings`
--

CREATE TABLE `invoice_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `company_tagline` varchar(255) DEFAULT NULL,
  `company_address_line1` varchar(255) DEFAULT NULL,
  `company_address_line2` varchar(255) DEFAULT NULL,
  `company_phone_number` varchar(20) DEFAULT NULL,
  `overdue_charge_type` enum('percentage','fixed') DEFAULT NULL,
  `overdue_charge_amount` decimal(10,2) DEFAULT NULL,
  `overdue_charge_period` enum('monthly','daily','days') DEFAULT NULL,
  `thank_you_message` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_settings`
--

INSERT INTO `invoice_settings` (`id`, `company_name`, `company_logo`, `company_tagline`, `company_address_line1`, `company_address_line2`, `company_phone_number`, `overdue_charge_type`, `overdue_charge_amount`, `overdue_charge_period`, `thank_you_message`, `user_id`, `created_at`) VALUES
(1, 'RevenueSure', 'uploads/logo/679a05e10e149_DEMO-fin-change.png', 'Fo Sho', 'Building #5, Park Avenue Road', 'NY City', '+1 234-546-4554', 'percentage', 10.00, 'days', 'Thanks bro!', 2, '2025-01-29 10:41:37');

-- --------------------------------------------------------

--
-- Table structure for table `issue_attachments`
--

CREATE TABLE `issue_attachments` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_comments`
--

CREATE TABLE `issue_comments` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_articles`
--

CREATE TABLE `knowledge_base_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `visibility` enum('all','team','admin','draft') DEFAULT 'all',
  `access_level` enum('public','private') DEFAULT 'public',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `view_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base_articles`
--

INSERT INTO `knowledge_base_articles` (`id`, `title`, `content`, `category_id`, `visibility`, `access_level`, `user_id`, `created_at`, `updated_at`, `view_count`) VALUES
(1, 'How to login', '<p>This is a demo article</p>', 2, 'team', 'public', 2, '2025-01-31 18:01:02', '2025-02-06 14:17:10', 21);

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_article_ratings`
--

CREATE TABLE `knowledge_base_article_ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `rating` enum('upvote','downvote') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base_article_ratings`
--

INSERT INTO `knowledge_base_article_ratings` (`id`, `user_id`, `article_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 'upvote', '', '2025-01-31 18:01:08'),
(2, 2, 1, 'upvote', '', '2025-01-31 18:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_article_requests`
--

CREATE TABLE `knowledge_base_article_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('New Guide Needed','Existing Guide Needs Update','New FAQ Suggestion') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base_article_requests`
--

INSERT INTO `knowledge_base_article_requests` (`id`, `user_id`, `request_type`, `description`, `created_at`) VALUES
(1, 2, 'New Guide Needed', 'Need a new guide on the EC2 instance creation', '2025-01-31 19:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_article_tags`
--

CREATE TABLE `knowledge_base_article_tags` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base_article_tags`
--

INSERT INTO `knowledge_base_article_tags` (`id`, `article_id`, `tag`, `created_at`) VALUES
(1, 1, 'guide', '2025-01-31 19:00:12'),
(2, 1, 'login', '2025-01-31 19:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_article_versions`
--

CREATE TABLE `knowledge_base_article_versions` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_bookmarks`
--

CREATE TABLE `knowledge_base_bookmarks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base_bookmarks`
--

INSERT INTO `knowledge_base_bookmarks` (`id`, `user_id`, `article_id`, `notes`, `created_at`) VALUES
(2, 2, 1, 'Nice', '2025-01-31 18:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_categories`
--

CREATE TABLE `knowledge_base_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base_categories`
--

INSERT INTO `knowledge_base_categories` (`id`, `name`, `parent_id`, `created_at`) VALUES
(1, 'Handbok', NULL, '2025-01-31 17:58:14'),
(2, 'Server Guide', NULL, '2025-01-31 18:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('New','Contacted','Converted') DEFAULT 'New',
  `customer_id` int(11) DEFAULT NULL,
  `converted_by` int(11) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT 'Website',
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `name`, `phone`, `email`, `category_id`, `created_at`, `status`, `customer_id`, `converted_by`, `city`, `state`, `country`, `source`, `assigned_to`) VALUES
(1, 'John Doe', '+91 123456789', 'john@demo.com', 1, '2025-01-26 06:53:45', 'New', NULL, NULL, NULL, NULL, NULL, 'Website', NULL),
(2, 'Jane Dane', '+ 1 6458898766', 'jane@demo.com', 2, '2025-01-26 06:54:43', 'Converted', NULL, NULL, NULL, NULL, NULL, 'Website', NULL),
(5, 'TEST LEAD', '123345', 'test@assigneddemo.com', 1, '2025-01-28 03:57:53', 'Converted', NULL, NULL, NULL, NULL, NULL, 'Website', 1),
(6, 'jakegyk', '1231238123', 'jake@demo.com', 2, '2025-01-28 04:04:31', 'New', NULL, NULL, NULL, NULL, NULL, 'Website', 1),
(7, 'PowerLead', '1231823918', 'power@demo.com', 1, '2025-01-28 04:05:31', 'Converted', NULL, 1, NULL, NULL, NULL, 'Website', 2),
(8, 'Jabbar2', '12312312', 'jabbar@demo.com', 1, '2025-01-28 08:55:40', 'Converted', 1, 2, NULL, NULL, NULL, 'Website', 2),
(9, 'POP', '123123', 'pop@pop.com', 1, '2025-01-29 11:56:29', 'Converted', 2, 2, NULL, NULL, NULL, 'Website', 1),
(10, 'test2', '191919', 'asd@demo.com', 2, '2025-02-10 05:36:10', 'New', NULL, NULL, NULL, NULL, NULL, 'Website', 2);

-- --------------------------------------------------------

--
-- Table structure for table `lead_scores`
--

CREATE TABLE `lead_scores` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `website_visits` int(11) DEFAULT 0,
  `email_opens` int(11) DEFAULT 0,
  `form_submissions` int(11) DEFAULT 0,
  `total_score` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_scores`
--

INSERT INTO `lead_scores` (`id`, `lead_id`, `website_visits`, `email_opens`, `form_submissions`, `total_score`, `last_updated`) VALUES
(1, 2, 2, 1, 1, 7, '2025-01-27 11:08:55'),
(3, 5, 0, 1, 1, 5, '2025-01-28 03:59:32'),
(4, 8, 1, 1, 1, 6, '2025-01-29 12:01:11');

-- --------------------------------------------------------

--
-- Table structure for table `ledger_entries`
--

CREATE TABLE `ledger_entries` (
  `id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `debit_amount` decimal(10,2) DEFAULT 0.00,
  `credit_amount` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `category` enum('Revenue','Expense','Asset','Liability','Equity') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `invoice_id` int(11) DEFAULT NULL,
  `expense_id` int(11) DEFAULT NULL,
  `reconciliation_status` enum('Unreconciled','Matched','Discrepancy') NOT NULL DEFAULT 'Unreconciled',
  `transaction_type` enum('Invoice','Expense') NOT NULL,
  `requires_review` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ledger_entries`
--

INSERT INTO `ledger_entries` (`id`, `transaction_date`, `transaction_id`, `description`, `debit_amount`, `credit_amount`, `currency`, `category`, `created_at`, `invoice_id`, `expense_id`, `reconciliation_status`, `transaction_type`, `requires_review`) VALUES
(1, '2025-02-12', '', 'Payment received for Invoice #INV-20250212-005', 0.00, 120.00, 'us', 'Revenue', '2025-02-12 11:32:43', 13, NULL, 'Unreconciled', 'Invoice', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT 0,
  `related_type` enum('lead','customer','project','user') DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `content`, `category_id`, `is_shared`, `related_type`, `related_id`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Meeting Notes - Project Kickoff', '<p><strong>Meeting Summary:</strong><br>&nbsp;</p><ul><li><strong>Project Overview:</strong><br>John presented the project\'s objectives, focusing on improving customer experience through new software features. The main goal is to launch by Q3 2025.</li><li><strong>Team Roles:</strong><ul><li>John will lead the technical team.</li><li>Jane will manage the UX/UI design.</li><li>Alice will be the product owner, and Mark will handle project management.</li></ul></li><li><strong>Timeline:</strong><br>The key milestones were discussed. The first prototype is expected by March 2025, with the first round of testing planned for late May 2025.</li><li><strong>Tools &amp; Resources:</strong><br>The team agreed to use Asana for project management, GitHub for version control, and Figma for design collaboration.</li><li><strong>Action Items:</strong><ul><li>John to finalize the tech stack by Friday, February 16, 2025.</li><li>Jane to begin wireframing initial designs by February 20, 2025.</li><li>Mark to schedule a follow-up meeting next week for progress updates.</li></ul></li></ul><p><strong>Next Meeting:</strong></p><ul><li>Scheduled for February 20, 2025, at 10:00 AM.</li></ul>', 2, 0, 'project', NULL, 2, '2025-02-13 10:29:26', '2025-02-13 12:08:05');

-- --------------------------------------------------------

--
-- Table structure for table `note_access`
--

CREATE TABLE `note_access` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `has_access` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_attachments`
--

CREATE TABLE `note_attachments` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_categories`
--

CREATE TABLE `note_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `note_categories`
--

INSERT INTO `note_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Remember', '2025-02-13 07:17:11'),
(2, 'Meeting', '2025-02-13 10:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `note_comments`
--

CREATE TABLE `note_comments` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `note_tags`
--

CREATE TABLE `note_tags` (
  `id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `type` enum('task_reminder','lead_update','system') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `related_id`, `type`, `is_read`, `created_at`) VALUES
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

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('Credit Card','Bank Transfer','PayPal','Cheque') NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `payment_date`, `payment_method`, `transaction_id`, `amount`) VALUES
(1, 4, '2025-01-29 11:52:19', 'Credit Card', '1234', 24.00),
(2, 3, '2025-01-29 11:59:30', 'Cheque', '48848', 43.00),
(10, 13, '2025-02-12 11:32:43', 'Credit Card', '13940', 120.00),
(11, 13, '2025-02-12 11:48:12', 'Credit Card', '192849', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `assigned_lead_customer_id` int(11) DEFAULT NULL,
  `assigned_lead_customer_type` enum('lead','customer') DEFAULT NULL,
  `project_manager_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Not Started','In Progress','Completed','On Hold','Canceled') DEFAULT 'Not Started',
  `priority` enum('High','Medium','Low') DEFAULT 'Medium',
  `project_category_id` int(11) DEFAULT NULL,
  `billing_type` enum('Hourly','Fixed Price','Retainer') DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_id`, `name`, `assigned_lead_customer_id`, `assigned_lead_customer_type`, `project_manager_id`, `start_date`, `end_date`, `status`, `priority`, `project_category_id`, `billing_type`, `budget`, `description`, `created_at`) VALUES
(6, 'PROJ-20250129-001', 'Metro Infrastructure Project', 2, 'customer', 2, '2025-01-31', '2025-02-14', 'Not Started', 'Low', 1, 'Retainer', 10000.00, 'Metro Infrastructure ', '2025-01-29 20:52:11');

-- --------------------------------------------------------

--
-- Table structure for table `project_categories`
--

CREATE TABLE `project_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_categories`
--

INSERT INTO `project_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Industrialization', '2025-01-29 20:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `project_features`
--

CREATE TABLE `project_features` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `feature_id` varchar(50) NOT NULL,
  `feature_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('Planned','In Progress','Under Review','Completed','Deferred') DEFAULT 'Planned',
  `owner_id` int(11) DEFAULT NULL,
  `estimated_completion_date` date DEFAULT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_features`
--

INSERT INTO `project_features` (`id`, `project_id`, `feature_id`, `feature_title`, `description`, `priority`, `status`, `owner_id`, `estimated_completion_date`, `actual_completion_date`, `created_by`, `created_date`) VALUES
(1, 6, 'FEAT-20250212-001', 'Metro station with announcement features', 'Metro Stations should have an announcement feature to tell users the next station time', 'Low', 'Planned', 4, '2025-02-14', NULL, 2, '2025-02-12 19:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `project_issues`
--

CREATE TABLE `project_issues` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `issue_id` varchar(50) NOT NULL,
  `issue_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `issue_type` enum('Bug','Enhancement','Task','Improvement') NOT NULL DEFAULT 'Bug',
  `priority` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `status` enum('Open','In Progress','Resolved','Closed','Reopened') NOT NULL DEFAULT 'Open',
  `reported_by` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `related_feature_id` int(11) DEFAULT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolution_date` date DEFAULT NULL,
  `steps_to_reproduce` text DEFAULT NULL,
  `environment_version` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_issues`
--

INSERT INTO `project_issues` (`id`, `project_id`, `issue_id`, `issue_title`, `description`, `issue_type`, `priority`, `status`, `reported_by`, `assigned_to`, `related_feature_id`, `date_reported`, `resolution_date`, `steps_to_reproduce`, `environment_version`) VALUES
(1, 6, 'ISSUE-20250212-001', 'Track construction delay in Zone 4', 'The construction in Zone 4 has encountered unexpected delays due to weather conditions, which has caused a setback in the overall timeline. The issue may result in delays for the entire metro line completion.', 'Enhancement', 'High', 'Open', 2, 2, 1, '2025-02-12 20:57:38', '2025-02-15', '1) Review current construction status in Zone 4.\r\n2) Assess weather conditions over the last two weeks.\r\n3) Identify the impact on the work schedule.', 'Metro Infrastructure Project Version 2.3, Zone 4 Construction Site');

-- --------------------------------------------------------

--
-- Table structure for table `reconciliation_ledger_entries`
--

CREATE TABLE `reconciliation_ledger_entries` (
  `reconciliation_id` int(11) NOT NULL,
  `ledger_entry_id` int(11) NOT NULL,
  `difference_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reconciliation_records`
--

CREATE TABLE `reconciliation_records` (
  `id` int(11) NOT NULL,
  `reconciliation_date` date NOT NULL,
  `bank_statement_reference` varchar(255) DEFAULT NULL,
  `total_difference_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('High','Medium','Low') DEFAULT 'Low',
  `assigned_to` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('New','In Progress','Resolved','Closed') DEFAULT 'New',
  `expected_resolution_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `project_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `title`, `description`, `priority`, `assigned_to`, `category`, `status`, `expected_resolution_date`, `created_at`, `project_id`) VALUES
(1, 2, 'Write Documentation', 'Write a detailed documentation', 'Low', 2, 'Documentation Request', 'New', '2025-01-31', '2025-01-30 12:30:38', NULL),
(2, 2, 'Vendor Credits Purchase', 'Need to purchase credits', 'Medium', 2, 'Infrastructure', 'In Progress', '2025-02-01', '2025-01-30 14:21:52', 6);

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_attachments`
--

CREATE TABLE `support_ticket_attachments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_ticket_attachments`
--

INSERT INTO `support_ticket_attachments` (`id`, `ticket_id`, `file_name`, `file_path`, `created_at`) VALUES
(1, 1, '20-0129_DEMO.png', 'uploads/679c3db6a1e8a_20-0129_DEMO.png', '2025-01-31 03:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_comments`
--

CREATE TABLE `support_ticket_comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_ticket_comments`
--

INSERT INTO `support_ticket_comments` (`id`, `ticket_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 2, 'Hi guys, any update on this ticket?', '2025-01-30 12:30:50'),
(2, 1, 2, 'Its under development', '2025-01-30 14:10:52'),
(3, 1, 2, 'Hi team ?', '2025-01-31 03:03:42'),
(4, 1, 2, 'Yo', '2025-01-31 03:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_tasks`
--

CREATE TABLE `support_ticket_tasks` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('To Do','In Progress','Completed') DEFAULT 'To Do',
  `assigned_to` int(11) DEFAULT NULL,
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_ticket_tasks`
--

INSERT INTO `support_ticket_tasks` (`id`, `ticket_id`, `title`, `description`, `due_date`, `status`, `assigned_to`, `priority`, `created_at`) VALUES
(1, 2, 'Hire an engineer for this', 'Hire an angular developer', '2025-01-31 23:04:00', 'In Progress', 4, 'Medium', '2025-01-30 17:35:13');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `task_id` varchar(50) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `task_type` enum('Follow-Up','Meeting','Deadline') NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('To Do','In Progress','Completed','Blocked','Canceled','Pending') DEFAULT 'To Do',
  `estimated_hours` decimal(6,2) DEFAULT NULL,
  `effort_estimation` varchar(100) DEFAULT NULL,
  `billable` tinyint(1) DEFAULT 0,
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `lead_id`, `user_id`, `task_id`, `task_name`, `project_id`, `task_type`, `description`, `due_date`, `status`, `estimated_hours`, `effort_estimation`, `billable`, `priority`, `created_at`) VALUES
(2, NULL, 2, NULL, 'Read Training Manual', NULL, 'Meeting', 'Manual Book Read', '2025-01-29 15:54:00', 'To Do', 10.00, NULL, 0, 'Medium', '2025-01-28 10:27:02'),
(3, NULL, 2, 'TASK-20250129-003', 'Credits Loadup', NULL, 'Deadline', 'Loadup credits on the vendor page', '2025-01-31 02:49:00', 'Completed', 10.00, NULL, 1, 'Medium', '2025-01-29 21:20:37'),
(4, NULL, 2, 'TASK-20250129-004', 'Metro Bridge', NULL, 'Meeting', 'Build a metro bridge', '2025-02-14 02:59:00', 'Completed', 1000.00, NULL, 1, 'High', '2025-01-29 21:29:44'),
(5, NULL, 2, 'TASK-20250129-005', 'test task', NULL, 'Meeting', 'Test Task', '2025-02-02 03:12:00', 'In Progress', 1001.00, NULL, 1, 'Medium', '2025-01-29 21:42:31'),
(6, NULL, 2, 'TASK-20250129-006', 'Buy Steel', 6, 'Deadline', 'Buy steel for the bridge', '2025-02-03 03:23:00', 'To Do', 10.00, NULL, 1, 'High', '2025-01-29 21:53:41'),
(7, NULL, 2, 'TASK-20250129-007', 'testing Related stuff', 6, 'Follow-Up', 'Test related', '2025-02-01 03:34:00', 'In Progress', 10.00, NULL, 1, 'Low', '2025-01-29 22:05:22'),
(8, NULL, 2, 'TASK-20250129-008', 'procuring cement', 6, 'Follow-Up', 'procure cement after buying steel', '2025-01-31 03:36:00', 'Completed', 100.00, NULL, 1, 'Low', '2025-01-29 22:07:12'),
(9, NULL, 2, 'TASK-20250129-009', 'Buy water', 6, 'Follow-Up', 'Buy water for the plant', '2025-01-31 03:42:00', 'Blocked', 10.00, NULL, 0, 'High', '2025-01-29 22:13:15'),
(10, NULL, 2, 'TASK-20250129-010', 'fgf', 6, 'Follow-Up', 'jhj', '2025-01-31 03:50:00', 'Completed', 6.00, NULL, 0, 'Low', '2025-01-29 22:20:23'),
(11, NULL, 2, 'TASK-20250129-011', 'test', 6, 'Follow-Up', 'test', '2025-01-31 04:23:00', 'Canceled', 100.00, NULL, 0, 'Low', '2025-01-29 22:54:26'),
(12, NULL, 2, 'TASK-20250130-012', 'Transparent dashboard', 6, 'Follow-Up', 'Make transparent dashboard', '2025-01-31 16:25:00', 'To Do', 10.00, NULL, 0, 'Low', '2025-01-30 10:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `task_attachments`
--

CREATE TABLE `task_attachments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_comments`
--

CREATE TABLE `task_comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_custom_fields`
--

CREATE TABLE `task_custom_fields` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_dependencies`
--

CREATE TABLE `task_dependencies` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `depends_on_task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_priorities`
--

CREATE TABLE `task_priorities` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_tags`
--

CREATE TABLE `task_tags` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_time_logs`
--

CREATE TABLE `task_time_logs` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `hours_spent` decimal(4,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_departments`
--

CREATE TABLE `team_departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_departments`
--

INSERT INTO `team_departments` (`id`, `name`, `created_at`) VALUES
(1, 'Frontend', '2025-01-30 17:24:24');

-- --------------------------------------------------------

--
-- Table structure for table `team_roles`
--

CREATE TABLE `team_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_roles`
--

INSERT INTO `team_roles` (`id`, `name`, `created_at`) VALUES
(1, 'Support Role', '2025-01-30 14:46:00'),
(2, 'Development Team A', '2025-01-30 14:47:27'),
(3, 'Dev Team B', '2025-01-30 14:47:43'),
(4, 'Dev Team C', '2025-01-30 14:47:50'),
(5, 'QA Team', '2025-01-30 14:48:01'),
(6, 'Marketing Team', '2025-01-30 14:48:22'),
(7, 'Design and UI Team', '2025-01-30 14:48:36'),
(8, 'Team Lead', '2025-01-30 14:48:56');

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE `todos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `related_type` enum('task','lead','customer') DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todos`
--

INSERT INTO `todos` (`id`, `user_id`, `title`, `description`, `due_date`, `is_completed`, `related_type`, `related_id`, `created_at`) VALUES
(1, 2, 'Checkout Davos', 'It\'s the client meeting there today!', '2025-01-28 15:40:00', 1, 'lead', 8, '2025-01-28 10:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `credits` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `credits`, `created_at`, `role`, `profile_picture`, `role_id`, `department_id`) VALUES
(1, 'Peel Hullin', 'user@demo.com', '$2y$10$8z2JpAs7QU3aMkHL59SC.O4rjZevbePDApd7947XYt.LfdOVlvA7.', 100, '2025-01-26 05:53:17', 'user', NULL, 5, NULL),
(2, 'GGBOAI', 'admin@demo.com', '$2y$10$qtyaY8G3jceTluy42gCT.ey.SYmGAUcj5Oi3bnDxOxnCL.7w4nbJq', 0, '2025-01-26 06:17:01', 'admin', NULL, NULL, NULL),
(4, 'John The Support Man', 'john@support.com', '$2y$10$6mZ3cSv8FM7fxg3Ui6JwquHyYnTtHsx1H9ZxtaFYqHG/anoV0C1o.', 0, '2025-01-30 14:46:28', 'user', NULL, 1, NULL),
(5, 'admin2', 'admin2@demo.com', '$2y$10$xhjAQ4eMVDwSaP5gB2x0sus4lk/9uU7MJZF9NHcQ0o9cyZifO6.b6', 0, '2025-01-31 21:26:14', 'admin', 'public/uploads/profile/679d4030dfe71_M0ekXd9R_400x400.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_credits`
--

CREATE TABLE `user_credits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `credits` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountants`
--
ALTER TABLE `accountants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `contract_audit_trail`
--
ALTER TABLE `contract_audit_trail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contract_signatures`
--
ALTER TABLE `contract_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contract_status`
--
ALTER TABLE `contract_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_id` (`contract_id`);

--
-- Indexes for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_custom_fields`
--
ALTER TABLE `customer_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customer_interactions`
--
ALTER TABLE `customer_interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customer_preferences`
--
ALTER TABLE `customer_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customer_tags`
--
ALTER TABLE `customer_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `discussion_attachments`
--
ALTER TABLE `discussion_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `discussion_messages`
--
ALTER TABLE `discussion_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `discussion_participants`
--
ALTER TABLE `discussion_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `participant_id` (`participant_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `expense_approvals`
--
ALTER TABLE `expense_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_id` (`expense_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_comments`
--
ALTER TABLE `expense_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_id` (`expense_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `featured_knowledge_base_articles`
--
ALTER TABLE `featured_knowledge_base_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `feature_attachments`
--
ALTER TABLE `feature_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feature_id` (`feature_id`);

--
-- Indexes for table `feature_comments`
--
ALTER TABLE `feature_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feature_id` (`feature_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feature_dependencies`
--
ALTER TABLE `feature_dependencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feature_id` (`feature_id`),
  ADD KEY `depends_on_feature_id` (`depends_on_feature_id`);

--
-- Indexes for table `feature_resources`
--
ALTER TABLE `feature_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feature_id` (`feature_id`);

--
-- Indexes for table `feature_subtasks`
--
ALTER TABLE `feature_subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feature_id` (`feature_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `issue_attachments`
--
ALTER TABLE `issue_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_id` (`issue_id`);

--
-- Indexes for table `issue_comments`
--
ALTER TABLE `issue_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_id` (`issue_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);
ALTER TABLE `knowledge_base_articles` ADD FULLTEXT KEY `title` (`title`,`content`);
ALTER TABLE `knowledge_base_articles` ADD FULLTEXT KEY `title_2` (`title`,`content`);

--
-- Indexes for table `knowledge_base_article_ratings`
--
ALTER TABLE `knowledge_base_article_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `knowledge_base_article_requests`
--
ALTER TABLE `knowledge_base_article_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `knowledge_base_article_tags`
--
ALTER TABLE `knowledge_base_article_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `knowledge_base_article_versions`
--
ALTER TABLE `knowledge_base_article_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `knowledge_base_bookmarks`
--
ALTER TABLE `knowledge_base_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `knowledge_base_categories`
--
ALTER TABLE `knowledge_base_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_assigned_to` (`assigned_to`);

--
-- Indexes for table `lead_scores`
--
ALTER TABLE `lead_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `expense_id` (`expense_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `related_type` (`related_type`,`related_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `note_access`
--
ALTER TABLE `note_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `note_attachments`
--
ALTER TABLE `note_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `note_categories`
--
ALTER TABLE `note_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `note_comments`
--
ALTER TABLE `note_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `note_tags`
--
ALTER TABLE `note_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_id` (`project_id`),
  ADD KEY `project_manager_id` (`project_manager_id`),
  ADD KEY `project_category_id` (`project_category_id`);

--
-- Indexes for table `project_categories`
--
ALTER TABLE `project_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_features`
--
ALTER TABLE `project_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feature_id` (`feature_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `project_issues`
--
ALTER TABLE `project_issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issue_id` (`issue_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `related_feature_id` (`related_feature_id`);

--
-- Indexes for table `reconciliation_ledger_entries`
--
ALTER TABLE `reconciliation_ledger_entries`
  ADD PRIMARY KEY (`reconciliation_id`,`ledger_entry_id`),
  ADD KEY `ledger_entry_id` (`ledger_entry_id`);

--
-- Indexes for table `reconciliation_records`
--
ALTER TABLE `reconciliation_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `support_ticket_attachments`
--
ALTER TABLE `support_ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `support_ticket_comments`
--
ALTER TABLE `support_ticket_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `support_ticket_tasks`
--
ALTER TABLE `support_ticket_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `task_id` (`task_id`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `task_custom_fields`
--
ALTER TABLE `task_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `task_dependencies`
--
ALTER TABLE `task_dependencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `depends_on_task_id` (`depends_on_task_id`);

--
-- Indexes for table `task_priorities`
--
ALTER TABLE `task_priorities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_tags`
--
ALTER TABLE `task_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `task_time_logs`
--
ALTER TABLE `task_time_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `team_departments`
--
ALTER TABLE `team_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_roles`
--
ALTER TABLE `team_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `user_credits`
--
ALTER TABLE `user_credits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountants`
--
ALTER TABLE `accountants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contract_audit_trail`
--
ALTER TABLE `contract_audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contract_signatures`
--
ALTER TABLE `contract_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contract_status`
--
ALTER TABLE `contract_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `contract_types`
--
ALTER TABLE `contract_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_custom_fields`
--
ALTER TABLE `customer_custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_interactions`
--
ALTER TABLE `customer_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_preferences`
--
ALTER TABLE `customer_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_tags`
--
ALTER TABLE `customer_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `discussion_attachments`
--
ALTER TABLE `discussion_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_messages`
--
ALTER TABLE `discussion_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `discussion_participants`
--
ALTER TABLE `discussion_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expense_approvals`
--
ALTER TABLE `expense_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `expense_comments`
--
ALTER TABLE `expense_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `featured_knowledge_base_articles`
--
ALTER TABLE `featured_knowledge_base_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_attachments`
--
ALTER TABLE `feature_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_comments`
--
ALTER TABLE `feature_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feature_dependencies`
--
ALTER TABLE `feature_dependencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feature_resources`
--
ALTER TABLE `feature_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feature_subtasks`
--
ALTER TABLE `feature_subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `issue_attachments`
--
ALTER TABLE `issue_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_comments`
--
ALTER TABLE `issue_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `knowledge_base_article_ratings`
--
ALTER TABLE `knowledge_base_article_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `knowledge_base_article_requests`
--
ALTER TABLE `knowledge_base_article_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `knowledge_base_article_tags`
--
ALTER TABLE `knowledge_base_article_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `knowledge_base_article_versions`
--
ALTER TABLE `knowledge_base_article_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_base_bookmarks`
--
ALTER TABLE `knowledge_base_bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `knowledge_base_categories`
--
ALTER TABLE `knowledge_base_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lead_scores`
--
ALTER TABLE `lead_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `note_access`
--
ALTER TABLE `note_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_attachments`
--
ALTER TABLE `note_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_categories`
--
ALTER TABLE `note_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `note_comments`
--
ALTER TABLE `note_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `note_tags`
--
ALTER TABLE `note_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `project_categories`
--
ALTER TABLE `project_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_features`
--
ALTER TABLE `project_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_issues`
--
ALTER TABLE `project_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reconciliation_records`
--
ALTER TABLE `reconciliation_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support_ticket_attachments`
--
ALTER TABLE `support_ticket_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_ticket_comments`
--
ALTER TABLE `support_ticket_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `support_ticket_tasks`
--
ALTER TABLE `support_ticket_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `task_attachments`
--
ALTER TABLE `task_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_comments`
--
ALTER TABLE `task_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_custom_fields`
--
ALTER TABLE `task_custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_dependencies`
--
ALTER TABLE `task_dependencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_priorities`
--
ALTER TABLE `task_priorities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_tags`
--
ALTER TABLE `task_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_time_logs`
--
ALTER TABLE `task_time_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_departments`
--
ALTER TABLE `team_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `team_roles`
--
ALTER TABLE `team_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `todos`
--
ALTER TABLE `todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_credits`
--
ALTER TABLE `user_credits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_audit_trail`
--
ALTER TABLE `contract_audit_trail`
  ADD CONSTRAINT `contract_audit_trail_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_audit_trail_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_signatures`
--
ALTER TABLE `contract_signatures`
  ADD CONSTRAINT `contract_signatures_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_signatures_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_status`
--
ALTER TABLE `contract_status`
  ADD CONSTRAINT `contract_status_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_custom_fields`
--
ALTER TABLE `customer_custom_fields`
  ADD CONSTRAINT `customer_custom_fields_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_interactions`
--
ALTER TABLE `customer_interactions`
  ADD CONSTRAINT `customer_interactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_preferences`
--
ALTER TABLE `customer_preferences`
  ADD CONSTRAINT `customer_preferences_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_tags`
--
ALTER TABLE `customer_tags`
  ADD CONSTRAINT `customer_tags_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_attachments`
--
ALTER TABLE `discussion_attachments`
  ADD CONSTRAINT `discussion_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `discussion_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_messages`
--
ALTER TABLE `discussion_messages`
  ADD CONSTRAINT `discussion_messages_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_messages_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `discussion_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_participants`
--
ALTER TABLE `discussion_participants`
  ADD CONSTRAINT `discussion_participants_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_ibfk_4` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_approvals`
--
ALTER TABLE `expense_approvals`
  ADD CONSTRAINT `expense_approvals_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_approvals_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_comments`
--
ALTER TABLE `expense_comments`
  ADD CONSTRAINT `expense_comments_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `featured_knowledge_base_articles`
--
ALTER TABLE `featured_knowledge_base_articles`
  ADD CONSTRAINT `featured_knowledge_base_articles_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feature_attachments`
--
ALTER TABLE `feature_attachments`
  ADD CONSTRAINT `feature_attachments_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feature_comments`
--
ALTER TABLE `feature_comments`
  ADD CONSTRAINT `feature_comments_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feature_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feature_dependencies`
--
ALTER TABLE `feature_dependencies`
  ADD CONSTRAINT `feature_dependencies_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feature_dependencies_ibfk_2` FOREIGN KEY (`depends_on_feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feature_resources`
--
ALTER TABLE `feature_resources`
  ADD CONSTRAINT `feature_resources_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feature_subtasks`
--
ALTER TABLE `feature_subtasks`
  ADD CONSTRAINT `feature_subtasks_ibfk_1` FOREIGN KEY (`feature_id`) REFERENCES `project_features` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feature_subtasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  ADD CONSTRAINT `invoice_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_attachments`
--
ALTER TABLE `issue_attachments`
  ADD CONSTRAINT `issue_attachments_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `project_issues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `issue_comments`
--
ALTER TABLE `issue_comments`
  ADD CONSTRAINT `issue_comments_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `project_issues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  ADD CONSTRAINT `knowledge_base_articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_article_ratings`
--
ALTER TABLE `knowledge_base_article_ratings`
  ADD CONSTRAINT `knowledge_base_article_ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_base_article_ratings_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_article_requests`
--
ALTER TABLE `knowledge_base_article_requests`
  ADD CONSTRAINT `knowledge_base_article_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_article_tags`
--
ALTER TABLE `knowledge_base_article_tags`
  ADD CONSTRAINT `knowledge_base_article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_article_versions`
--
ALTER TABLE `knowledge_base_article_versions`
  ADD CONSTRAINT `knowledge_base_article_versions_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_base_article_versions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_bookmarks`
--
ALTER TABLE `knowledge_base_bookmarks`
  ADD CONSTRAINT `knowledge_base_bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `knowledge_base_bookmarks_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `knowledge_base_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `knowledge_base_categories`
--
ALTER TABLE `knowledge_base_categories`
  ADD CONSTRAINT `knowledge_base_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `knowledge_base_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `lead_scores`
--
ALTER TABLE `lead_scores`
  ADD CONSTRAINT `lead_scores_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD CONSTRAINT `ledger_entries_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ledger_entries_ibfk_2` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ledger_entries_ibfk_3` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ledger_entries_ibfk_4` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_access`
--
ALTER TABLE `note_access`
  ADD CONSTRAINT `note_access_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_attachments`
--
ALTER TABLE `note_attachments`
  ADD CONSTRAINT `note_attachments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_comments`
--
ALTER TABLE `note_comments`
  ADD CONSTRAINT `note_comments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `note_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `note_tags`
--
ALTER TABLE `note_tags`
  ADD CONSTRAINT `note_tags_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`project_manager_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`project_category_id`) REFERENCES `project_categories` (`id`);

--
-- Constraints for table `project_features`
--
ALTER TABLE `project_features`
  ADD CONSTRAINT `project_features_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_features_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_issues`
--
ALTER TABLE `project_issues`
  ADD CONSTRAINT `project_issues_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_issues_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_issues_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_issues_ibfk_4` FOREIGN KEY (`related_feature_id`) REFERENCES `project_features` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reconciliation_ledger_entries`
--
ALTER TABLE `reconciliation_ledger_entries`
  ADD CONSTRAINT `reconciliation_ledger_entries_ibfk_1` FOREIGN KEY (`reconciliation_id`) REFERENCES `reconciliation_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reconciliation_ledger_entries_ibfk_2` FOREIGN KEY (`ledger_entry_id`) REFERENCES `ledger_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_tickets_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `support_ticket_attachments`
--
ALTER TABLE `support_ticket_attachments`
  ADD CONSTRAINT `support_ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_ticket_comments`
--
ALTER TABLE `support_ticket_comments`
  ADD CONSTRAINT `support_ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_ticket_tasks`
--
ALTER TABLE `support_ticket_tasks`
  ADD CONSTRAINT `support_ticket_tasks_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_ticket_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD CONSTRAINT `task_attachments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD CONSTRAINT `task_comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_custom_fields`
--
ALTER TABLE `task_custom_fields`
  ADD CONSTRAINT `task_custom_fields_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_dependencies`
--
ALTER TABLE `task_dependencies`
  ADD CONSTRAINT `task_dependencies_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_dependencies_ibfk_2` FOREIGN KEY (`depends_on_task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_tags`
--
ALTER TABLE `task_tags`
  ADD CONSTRAINT `task_tags_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_time_logs`
--
ALTER TABLE `task_time_logs`
  ADD CONSTRAINT `task_time_logs_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_time_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `todos`
--
ALTER TABLE `todos`
  ADD CONSTRAINT `todos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `team_roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `team_departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_credits`
--
ALTER TABLE `user_credits`
  ADD CONSTRAINT `user_credits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
