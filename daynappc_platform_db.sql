-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 05, 2026 at 11:57 PM
-- Server version: 8.0.42-cll-lve
-- PHP Version: 8.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `daynappc_platform_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_number_sequence`
--

CREATE TABLE `account_number_sequence` (
  `prefix` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_account_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_access_token`
--

CREATE TABLE `admin_user_access_token` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `device_uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int NOT NULL,
  `actor_id` int DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_uid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary`
--

CREATE TABLE `beneficiary` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiary`
--

INSERT INTO `beneficiary` (`id`, `user_id`, `uid`, `display_name`, `phone_number`, `category`, `created_at`, `last_modified_at`) VALUES
(3, 1, '019ba9ef-6eca-7749-8428-47bcbdf37b4c', 'Apipa', '+2349068914252', NULL, '2026-01-10 22:03:19', '2026-01-10 22:03:19'),
(8, 1, '019bbb10-e155-7bc8-bc59-7de75f655e7e', 'Fadeelah', '9022211217', NULL, '2026-01-14 05:53:24', '2026-01-14 05:53:24'),
(14, 2, '019bbded-100f-7d02-8c20-4659c6e7ceb9', 'Abdul', '08033496917', NULL, '2026-01-14 19:13:08', '2026-01-14 19:13:08'),
(15, 1, '019c233e-8119-7e74-b9f9-029667f7f1af', 'Ibrahim', '09033065454', NULL, '2026-02-03 11:23:44', '2026-02-03 11:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `debt_collection`
--

CREATE TABLE `debt_collection` (
  `id` int NOT NULL,
  `debtor_id` int NOT NULL,
  `creditor_id` int NOT NULL,
  `created_by_id` int NOT NULL,
  `phone_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `amount_unpaid` decimal(10,0) NOT NULL,
  `confirmation_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmation_status_message` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `debt_collection`
--

INSERT INTO `debt_collection` (`id`, `debtor_id`, `creditor_id`, `created_by_id`, `phone_number`, `amount`, `description`, `status`, `uid`, `created_at`, `last_modified_at`, `amount_unpaid`, `confirmation_status`, `confirmation_status_message`) VALUES
(86, 1, 2, 1, '09022211217', 300000, 'test', 'paid', '019c0344-3e0f-75da-aa3a-6961397c1196', '2026-01-28 06:22:09', '2026-01-28 06:22:35', 0, 'accepted', ''),
(87, 1, 2, 2, '08033496917', 1000000, 'test', 'partial', '019c053f-3703-73d3-b858-902f007cabfe', '2026-01-28 15:35:54', '2026-01-28 15:36:39', 50000, 'accepted', ''),
(88, 3, 1, 3, '08033496917', 550000, 'ggg', 'partial', '019c05ed-4a7e-7685-9557-170c5051f9e9', '2026-01-28 18:46:03', '2026-01-28 19:05:35', 400000, 'accepted', ''),
(89, 2, 1, 2, '08033496917', 2000000, 'yesns', 'partial', '019c068d-a71c-7f68-a92c-060484861b46', '2026-01-28 21:41:12', '2026-01-28 21:41:35', 50000, 'accepted', ''),
(90, 1, 2, 1, '09022211217', 2000000, 'xajsj', 'partial', '019c0f3f-33f8-723b-ad69-5cd772dd9e1d', '2026-01-30 14:12:06', '2026-01-30 14:12:26', 850000, 'accepted', ''),
(91, 2, 1, 2, '08033496917', 5000000, 'hdh', 'partial', '019c1429-8fd5-7e64-ba77-7b454117c5b5', '2026-01-31 13:06:34', '2026-01-31 13:07:28', 3050000, 'accepted', ''),
(92, 3, 1, 3, '08033496917', 300000, 'mmm', 'partial', '019c1ca2-09a5-75f8-981e-236200cf2ede', '2026-02-02 04:35:07', '2026-02-02 07:56:16', 200000, 'accepted', ''),
(93, 1, 3, 3, '08033496917', 320000, 'ggtt', 'unpaid', '019c1cbf-2198-7644-9041-6b0886e69ca3', '2026-02-02 05:06:53', '2026-02-02 07:56:13', 320000, 'accepted', ''),
(94, 1, 2, 1, '09022211217', 2000000, 'cefane', 'partial', '019c1d5b-4205-73da-9d58-094071782276', '2026-02-02 07:57:25', '2026-02-02 07:58:58', 1000000, 'accepted', ''),
(95, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c2090-6165-7f9e-baf6-1647b86275e4', '2026-02-02 22:54:18', '2026-02-02 23:15:02', 1000000, 'cancelled', NULL),
(96, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c2090-7c50-7e6e-af19-1b78bb5382fd', '2026-02-02 22:54:25', '2026-02-02 23:17:21', 1000000, 'rejected', NULL),
(97, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c2091-50a5-774a-a9a6-ee8f98a8a80c', '2026-02-02 22:55:20', '2026-02-02 23:17:13', 1000000, 'rejected', NULL),
(98, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c2094-c084-7ba9-b556-6335711610c8', '2026-02-02 22:59:05', '2026-02-02 23:15:52', 1000000, 'cancelled', NULL),
(99, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c2094-ff13-773d-a483-f188c64bc639', '2026-02-02 22:59:21', '2026-02-02 23:17:05', 1000000, 'rejected', NULL),
(100, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c2099-e5e9-7fd8-be55-0997f2d25783', '2026-02-02 23:04:42', '2026-02-02 23:15:33', 1000000, 'cancelled', NULL),
(101, 2, 1, 2, '08033496917', 1000000, 'test', 'unpaid', '019c20a2-df5f-70d1-a0d6-8179762ba041', '2026-02-02 23:14:30', '2026-02-02 23:15:17', 1000000, 'cancelled', NULL),
(102, 2, 1, 2, '08033496917', 150000, 'a testing', 'unpaid', '019c20ad-a5d6-764c-b142-01082f698293', '2026-02-02 23:26:17', '2026-02-02 23:28:30', 150000, 'cancelled', NULL),
(103, 2, 1, 2, '08033496917', 130000, 'das', 'unpaid', '019c20ae-7e47-77b3-9277-92a57879776c', '2026-02-02 23:27:12', '2026-02-02 23:28:16', 130000, 'cancelled', NULL),
(104, 2, 1, 2, '08033496917', 1000000, 'tete', 'unpaid', '019c20b0-2625-74da-bba0-d91dcdb95e63', '2026-02-02 23:29:00', '2026-02-02 23:54:39', 1000000, 'accepted', NULL),
(105, 2, 1, 2, '08033496917', 123400, 'yutiu', 'unpaid', '019c20b2-1f6c-787c-84b5-abc2e7eb44ee', '2026-02-02 23:31:10', '2026-02-02 23:58:55', 123400, 'accepted', NULL),
(106, 2, 1, 2, '08033496917', 1000000, 'teteqy', 'partial', '019c20c3-ab19-734d-b33d-53d8e0e10e43', '2026-02-02 23:50:20', '2026-02-02 23:54:57', 900000, 'accepted', NULL),
(107, 1, 2, 2, '08033496917', 1200000, 'hhjkj', 'unpaid', '019c20c4-a441-70af-848d-5665951d05b1', '2026-02-02 23:51:23', '2026-02-02 23:54:47', 1200000, 'accepted', NULL),
(108, 3, 1, 3, '08033496917', 240000, 'ggg', 'unpaid', '019c2453-cc7c-7942-a3f5-60e372e2d2d3', '2026-02-03 16:26:37', '2026-02-03 16:55:31', 240000, 'accepted', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `debt_collection_payment`
--

CREATE TABLE `debt_collection_payment` (
  `id` int NOT NULL,
  `debt_collection_id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `channel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_reference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_by_id` int DEFAULT NULL,
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `debt_collection_payment`
--

INSERT INTO `debt_collection_payment` (`id`, `debt_collection_id`, `uid`, `amount`, `channel`, `status`, `payment_reference`, `created_at`, `last_modified_at`, `created_by_id`, `is_acknowledged`) VALUES
(54, 89, '019c137a-d755-784b-bcb5-64be0ddacb81', 50000, 'wallet', 'approved', 'DAYN-TRF-7946773142-31012026-095543', '2026-01-31 09:55:43', '2026-01-31 09:55:43', 2, 1),
(55, 90, '019c13a3-cf0a-758d-913e-d407c60eaf3d', 100000, 'wallet', 'approved', 'DAYN-TRF-6655092133-31012026-104028', '2026-01-31 10:40:28', '2026-01-31 10:40:28', 1, 1),
(56, 90, '019c13a4-b400-7793-8e55-28e49d7f69c0', 150000, 'wallet', 'approved', 'DAYN-TRF-4119275784-31012026-104127', '2026-01-31 10:41:27', '2026-01-31 10:41:27', 1, 1),
(57, 90, '019c13b8-9a01-7f12-b9ca-3ad147a20326', 50000, 'wallet', 'approved', 'DAYN-TRF-3153859841-31012026-110311', '2026-01-31 11:03:11', '2026-01-31 11:03:11', 1, 1),
(58, 90, '019c13bb-fc14-7520-9c84-e112455f9bb4', 20000, 'wallet', 'approved', 'DAYN-TRF-3973341569-31012026-110652', '2026-01-31 11:06:52', '2026-01-31 11:06:52', 1, 1),
(59, 90, '019c13d8-2e54-7c34-a0cb-3c47172604b9', 80000, 'wallet', 'approved', 'DAYN-TRF-6508979659-31012026-113740', '2026-01-31 11:37:40', '2026-01-31 11:37:40', 1, 1),
(60, 91, '019c142b-5c03-782d-a0f6-3fac1e36a24c', 1000000, 'wallet', 'approved', 'DAYN-TRF-2782406013-31012026-130831', '2026-01-31 13:08:31', '2026-01-31 13:08:31', 2, 1),
(61, 91, '019c147a-51c1-77b7-9929-6fbd5c14469f', 50000, 'wallet', 'approved', 'DAYN-TRF-5368902851-31012026-143446', '2026-01-31 14:34:46', '2026-01-31 14:34:46', 2, 1),
(65, 91, '019c15fb-7de7-79b5-8202-105fd57c9ff7', 50000, 'wallet', 'approved', 'DAYN-TRF-5382242024-31012026-213529', '2026-01-31 21:35:29', '2026-01-31 21:35:29', 2, 1),
(66, 91, '019c15fc-4af4-7dd4-9da0-61dad5d38839', 100000, 'wallet', 'approved', 'DAYN-TRF-1647600697-31012026-213621', '2026-01-31 21:36:21', '2026-01-31 21:36:21', 2, 1),
(68, 91, '019c162f-bf5b-7a95-afab-f6bfa4f0651e', 150000, 'cash', 'approved', NULL, '2026-01-31 22:32:33', '2026-01-31 23:34:44', 2, 1),
(71, 90, '019c1635-062f-7d7b-bdd0-cbe3ab8f1dcc', 100000, 'wallet', 'approved', 'DAYN-TRF-3795895217-31012026-223819', '2026-01-31 22:38:19', '2026-01-31 22:38:19', 1, 1),
(72, 90, '019c1635-2b28-757a-8b35-33f55baf1f9e', 100000, 'wallet', 'approved', 'DAYN-TRF-0952300813-31012026-223829', '2026-01-31 22:38:29', '2026-01-31 22:38:29', 1, 1),
(73, 90, '019c166f-94de-780c-9084-86349965fe81', 100000, 'cash', 'approved', NULL, '2026-01-31 23:42:17', '2026-01-31 23:43:24', 1, 1),
(74, 91, '019c1671-d55a-769f-9267-e2b3ae20bddc', 100000, 'cash', 'approved', NULL, '2026-01-31 23:44:44', '2026-01-31 23:46:29', 2, 1),
(75, 91, '019c1674-b339-7002-a012-1b35149e4a68', 100000, 'cash', 'approved', NULL, '2026-01-31 23:47:52', '2026-02-01 20:32:39', 2, 1),
(76, 91, '019c1684-fcd2-78dc-98bd-3aeca696ee20', 50000, 'wallet', 'approved', 'DAYN-TRF-6904746121-01022026-000540', '2026-02-01 00:05:40', '2026-02-01 00:05:40', 2, 1),
(77, 91, '019c1686-ea7a-77e1-a0e4-e402cd6a55a2', 100000, 'wallet', 'approved', 'DAYN-TRF-9971291754-01022026-000746', '2026-02-01 00:07:46', '2026-02-01 00:07:46', 2, 1),
(82, 91, '019c19d4-4404-7506-b105-812b3d04d80e', 100000, 'wallet', 'approved', 'DAYN-TRF-4274452269-01022026-153107', '2026-02-01 15:31:07', '2026-02-01 15:31:07', 2, 1),
(84, 91, '019c1a66-43e3-7b2e-83bc-8dbc21468d13', 100000, 'cash', 'approved', NULL, '2026-02-01 18:10:35', '2026-02-01 20:31:37', 2, 1),
(85, 90, '019c1ae9-e4c4-7adb-9335-9f4eb395b00c', 50000, 'wallet', 'approved', 'DAYN-TRF-2981978339-01022026-203422', '2026-02-01 20:34:22', '2026-02-01 22:08:18', 1, 1),
(86, 90, '019c1aef-701b-7901-9c8e-7ee3d8be3aff', 50000, 'wallet', 'approved', 'DAYN-TRF-1068864544-01022026-204025', '2026-02-01 20:40:25', '2026-02-01 22:08:03', 1, 1),
(87, 90, '019c1af0-e427-7fb7-a63c-5d9f3a23bcb5', 50000, 'wallet', 'approved', 'DAYN-TRF-1875620573-01022026-204200', '2026-02-01 20:42:00', '2026-02-01 22:07:44', 1, 1),
(88, 91, '019c1b12-db0b-78f4-aaab-167d936c68fe', 50000, 'wallet', 'approved', 'DAYN-TRF-3322726849-01022026-211906', '2026-02-01 21:19:06', '2026-02-01 21:42:22', 2, 1),
(89, 88, '019c1ca0-b72d-7d99-8362-81acd83ab7ec', 100000, 'wallet', 'approved', 'DAYN-TRF-9695363802-02022026-043340', '2026-02-02 04:33:40', '2026-02-02 07:56:27', 3, 1),
(90, 88, '019c1ca1-0bc3-746a-b7aa-93de35bb689c', 50000, 'cash', 'approved', NULL, '2026-02-02 04:34:02', '2026-02-02 07:56:23', 3, 1),
(91, 94, '019c1d5f-4094-7d58-ae4b-6233027f898e', 1000000, 'wallet', 'approved', 'DAYN-TRF-2044771971-02022026-080147', '2026-02-02 08:01:47', '2026-02-02 22:53:05', 1, 1),
(92, 106, '019c233b-db6a-7fcb-a4d8-22183f6af4ce', 100000, 'wallet', 'approved', 'DAYN-TRF-1841948882-03022026-112051', '2026-02-03 11:20:51', '2026-02-03 11:22:10', 2, 1),
(93, 92, '019c2454-7383-7498-a64d-bb40432dfee3', 100000, 'wallet', 'approved', 'DAYN-TRF-7370008126-03022026-162720', '2026-02-03 16:27:20', '2026-02-03 16:55:38', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password_token`
--

CREATE TABLE `forgot_password_token` (
  `id` int NOT NULL,
  `username` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_in_minutes` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactional_account`
--

CREATE TABLE `payment_transactional_account` (
  `id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_specified` decimal(10,0) NOT NULL,
  `amount_charged` decimal(10,0) NOT NULL,
  `fee` decimal(10,0) NOT NULL,
  `fee_rate` decimal(10,0) NOT NULL,
  `fee_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_response_object` json NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `bank_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_transactional_account`
--

INSERT INTO `payment_transactional_account` (`id`, `uid`, `account_number`, `account_name`, `customer_name`, `amount_specified`, `amount_charged`, `fee`, `fee_rate`, `fee_type`, `provider`, `provider_reference`, `transaction_reference`, `provider_response_object`, `created_at`, `last_modified_at`, `bank_name`, `bank_code`) VALUES
(53, '019c147d-fa5b-7f4f-b861-e9693838c93d', '3319787938', 'Fad', 'Fad', 5000000, 5075000, 75000, 2, 'Ratio', 'monnify', 'DAYN-FND-8177568671-31012026-143845', 'DAYN-FND-8177568671-31012026-143845', '{\"amount\": \"5075000\", \"bankCode\": \"035\", \"bankName\": \"Wema bank\", \"currency\": \"NGN\", \"createdAt\": \"2026-01-31T14:38:46+00:00\", \"accountName\": \"Fad\", \"accountNumber\": \"3319787938\", \"providerAccountReference\": \"DAYN-FND-8177568671-31012026-143845\"}', '2026-01-31 14:38:46', '2026-01-31 14:38:46', 'Wema bank', '035'),
(54, '019c232f-e59f-75e7-9477-752b2007ec8f', '3393860190', 'Fad', 'Fad', 100000, 101500, 1500, 2, 'Ratio', 'monnify', 'DAYN-FND-8963642204-03022026-110746', 'DAYN-FND-8963642204-03022026-110746', '{\"amount\": \"1015\", \"bankCode\": \"035\", \"bankName\": \"Wema bank\", \"currency\": \"NGN\", \"createdAt\": \"2026-02-03T11:07:47+00:00\", \"accountName\": \"Fad\", \"accountNumber\": \"3393860190\", \"providerAccountReference\": \"DAYN-FND-8963642204-03022026-110746\"}', '2026-02-03 11:07:47', '2026-02-03 11:07:47', 'Wema bank', '035'),
(55, '019c233b-1cb8-7353-b2ce-268b05d676f8', '0016119666', 'Fad', 'Fad', 100000, 101500, 1500, 2, 'Ratio', 'monnify', 'DAYN-FND-6710000574-03022026-112001', 'DAYN-FND-6710000574-03022026-112001', '{\"amount\": \"1015\", \"bankCode\": \"035\", \"bankName\": \"Wema bank\", \"currency\": \"NGN\", \"createdAt\": \"2026-02-03T11:20:02+00:00\", \"accountName\": \"Fad\", \"accountNumber\": \"0016119666\", \"providerAccountReference\": \"DAYN-FND-6710000574-03022026-112001\"}', '2026-02-03 11:20:02', '2026-02-03 11:20:02', 'Wema bank', '035');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reserved_account_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_account_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_bank_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_bank_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_account_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `first_name`, `last_name`, `uid`, `phone_number`, `reserved_account_number`, `reserved_account_name`, `reserved_bank_name`, `reserved_bank_code`, `reserved_account_reference`) VALUES
(1, 'abdulhakeemimran@hotmail.com', '[]', '$2y$13$ZcbT0j36cdzRNZsYcpWEk.rVEY/H7v6ar7/oxnAj1mJ6ayfUQzFa.', 'Abdulhakeem', 'Imran', '019b83fa-e557-7669-9808-7cda96503f28', '08033496917', NULL, NULL, NULL, NULL, NULL),
(2, 'abdulhakeemimran@gmail.com', '[]', '$2y$13$w7gIFCIvHcvqbnvoozFQVOOiiVyFppFx6ksI8UZ7rt4HG6uW.Bugq', 'Fadeelah', 'Dalha', '019b840f-74dc-7a71-860e-5064215f8dec', '09022211217', NULL, NULL, NULL, NULL, NULL),
(3, 'khalyfaia@gmail.com', '[]', '$2y$13$8gdVzCPdiDnICoarrVG6f.7zaKiJYkFO9xufZToeB39Hjaw6kicVO', 'Abidu', 'Imrana', '019b8416-75e4-74ff-9df4-167b02963363', '09068914252', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_access_token`
--

CREATE TABLE `user_access_token` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `device_uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_access_token`
--

INSERT INTO `user_access_token` (`id`, `user_id`, `access_token`, `expires_at`, `device_uid`) VALUES
(2, 1, '019b83fa-ea0b-7450-a197-dc6db49c08f8', '2026-01-04 13:10:17', '019b7732-2315-7b40-9929-76ffc005cba7'),
(3, 1, '019b83fc-099b-7362-8e91-977c7894ef91', '2026-01-04 13:11:31', '019b83fc-0211-7655-adf3-c5457b3bc5b0'),
(5, 2, '019b840f-7953-71cc-b681-374ba161610d', '2026-01-04 13:32:45', '019b7732-2315-7b40-9929-76ffc005cba7'),
(14, 1, '019b847e-caf3-74b7-8045-79d807741106', '2026-01-04 15:34:20', '019b8419-4cac-7cd8-a70d-82f072a46b3a'),
(21, 2, '019b857f-e861-74d8-82dc-9b8b69033b64', '2026-01-04 20:15:10', '019b857f-d67a-7af3-8164-d594de2ef1ae'),
(22, 1, '019b8580-905d-7b96-93b4-4fe3aa1582da', '2026-01-04 20:15:53', '019b8580-879a-74bd-a9fb-df10c3dbcf11'),
(25, 2, '019b8615-f53b-73f5-9999-8dddcb4def82', '2026-01-04 22:59:04', '019b847f-e7fd-747a-b2f7-83a331279df3'),
(26, 1, '019b8623-920e-74e4-a071-1b83c01e53ee', '2026-01-04 23:13:56', '019b860e-6109-79ec-b071-b5d138727385'),
(30, 2, '019b8f18-059a-7141-8cc8-1605d419ef9d', '2026-01-06 16:57:54', '019b8f17-fb84-7f5e-9711-290cb8ed2ece'),
(35, 2, '019b8f7c-a65a-7168-b2d8-e5d726955afc', '2026-01-06 18:47:49', '019b8f46-cd57-7dc6-ab93-9a29b882ef94'),
(36, 2, '019b8f86-f9d6-7056-ac67-ccc1f91f9333', '2026-01-06 18:59:06', '019b8f86-eae2-7416-b132-0beaa5d30362'),
(37, 2, '019b8fa2-a7d9-71e5-a24a-ff3df07c729f', '2026-01-06 19:29:20', '019b8fa2-9cc9-7523-98ba-b92f8872cd47'),
(38, 2, '019b8fd2-48a9-73fc-9ec4-4d552414e261', '2026-01-06 20:21:21', '019b8fd2-3a5a-7b9d-8288-98502692da9e'),
(39, 2, '019b93be-ab97-76a0-a934-7c74e1a63beb', '2026-01-07 14:38:25', '019b93be-9b99-7be6-ab98-583fc5b67e69'),
(44, 2, '019b944c-6564-7cb9-8804-f6998c3f7c4d', '2026-01-07 17:13:13', '019b9416-5129-7b79-bfbc-461e1615a8e5'),
(47, 2, '019b9491-5ecd-737a-b2f7-08ae1fe1ff7b', '2026-01-07 18:28:33', '019b9473-e4f7-7c63-87ce-98c1734cd899'),
(48, 2, '019b94d0-6bf1-75ce-afe6-d77c134c8cd1', '2026-01-07 19:37:25', '019b94d0-5e97-7325-bbf5-0f8af497ed2b'),
(51, 1, '019b9504-c4ce-7133-aa80-ef04703a4d41', '2026-01-07 20:34:36', '019b8f21-3c4c-7c8b-8979-53e64a238aa8'),
(52, 2, '019b9506-b591-728d-8feb-26edce64f309', '2026-01-07 20:36:43', '019b9506-aeab-7d0a-80c1-415860d220ef'),
(53, 2, '019b9525-d6c0-75d5-a03e-7d23aef79c50', '2026-01-07 21:10:43', '019b9525-c81c-7016-9928-1f46c42715b7'),
(56, 2, '019b954b-361c-73cd-99a8-1de8a1bd81bf', '2026-01-07 21:51:32', '019b954b-2470-7794-a560-c65990f16b92'),
(57, 2, '019b9556-8030-7da6-97c8-3920834c99d6', '2026-01-07 22:03:52', '019b9556-71c4-7d90-93c7-83e3ca316b19'),
(60, 2, '019b959c-1ef8-7529-9736-6e5fb74320e6', '2026-01-07 23:19:55', '019b9593-0b3f-70e0-9b7e-922da090868d'),
(64, 2, '019b95da-6f35-7a19-b758-0eafced55972', '2026-01-08 00:27:59', '019b95da-6442-77fa-a4a2-beb0719bafbb'),
(66, 1, '019b95f5-e015-765f-9e50-65be86a4a3fe', '2026-01-08 00:57:57', '019b959e-0a2d-75f1-b333-598ab63f104e'),
(67, 2, '019b95f8-b96d-7b40-a213-6037b8113687', '2026-01-08 01:01:04', '019b95f8-ac79-7629-8962-6a7ea20eb196'),
(69, 1, '019b960f-3d98-79bc-88f8-ecc20bb951c1', '2026-01-08 01:25:39', '019b960f-3271-7d8d-955b-6869bf6dc05d'),
(71, 2, '019b9619-2b67-7c38-8950-225ce4fac1a7', '2026-01-08 01:36:30', '019b9619-2475-724c-a1ff-3c9eeba5742e'),
(72, 1, '019b961a-10a1-7e7c-8bc8-07b790e790e2', '2026-01-08 01:37:29', '019b961a-082a-760a-afba-6014fd0342e9'),
(73, 2, '019b962d-c5f4-7fa5-aa07-c7fc377ed401', '2026-01-08 01:59:00', '019b9602-2a7f-7e1e-8690-3a85d361447f'),
(75, 2, '019b962f-b3ae-75e1-9d02-d5faedcdce63', '2026-01-08 02:01:07', '019b962f-a871-77b8-ba38-0033280458d1'),
(79, 2, '019b9671-b540-7e22-b8ee-f1f2848af1af', '2026-01-08 03:13:13', '019b9656-f23a-729b-a253-58170a0e6ce5'),
(88, 2, '019b9930-99fd-711d-a366-57178925f424', '2026-01-08 16:00:57', '019b9930-7d12-7137-8731-c0d268333ac8'),
(89, 2, '019b994c-583f-7293-b3ff-99b43263aee8', '2026-01-08 16:31:16', '019b994c-4a64-75d7-bb59-3ae8709f02b6'),
(91, 2, '019b9c15-a515-750c-8bdc-5ce1f7b081c0', '2026-01-09 05:30:22', '019b9c15-9996-78a3-8ec9-9190197cebe3'),
(94, 2, '019b9c32-6191-7f7e-a6c3-164d6a03918f', '2026-01-09 06:01:46', '019b9c32-59e9-7645-bf49-0b598e17ad6b'),
(98, 2, '019b9d72-bedb-76e4-a3eb-bda67501c406', '2026-01-09 11:51:41', '019b9d5d-c059-72b1-9450-2d8f1e1b2151'),
(99, 2, '019b9d98-b5e6-7d9b-bd01-4790b2d540cc', '2026-01-09 12:33:09', '019b9d98-ab54-78d3-a349-111abc4c08c2'),
(101, 2, '019b9edf-8d7f-7a9f-ab54-fdccd1507931', '2026-01-09 18:30:09', '019b9edf-7fa6-7729-a020-be7aa7f14266'),
(103, 2, '019b9ef3-18a6-734d-93da-ce8756b83ef9', '2026-01-09 18:51:30', '019b9ef2-fc0a-7289-b144-69a51cb04d64'),
(105, 2, '019b9f92-8f21-7f9d-95f8-97efd3c4806a', '2026-01-09 21:45:40', '019b9f92-7f7e-70f4-a601-31040382e960'),
(106, 1, '019b9f94-1bbb-7cbd-b366-c7a4227d16b6', '2026-01-09 21:47:22', '019b9f94-12a6-7520-87ca-02aa93cabd10'),
(108, 1, '019b9ffc-c9a2-73b7-b16b-45db1d503f7b', '2026-01-09 23:41:42', '019b962e-72de-7688-b24a-69ba6ce22d16'),
(109, 2, '019b9ffe-cc32-7ab8-bfc1-e05837e91c2e', '2026-01-09 23:43:54', '019b9ffe-c8f5-7299-a9af-fee2e6ed83c7'),
(110, 1, '019b9fff-c40c-70b1-a5d3-5bb876533804', '2026-01-09 23:44:57', '019b9fff-ad8c-745a-82ad-a462f1e7400e'),
(111, 2, '019ba000-e61d-7c12-91fa-023b39b648ab', '2026-01-09 23:46:12', '019ba000-e36d-77f8-9918-463123c46a6d'),
(112, 2, '019ba001-b8c0-705a-844d-02181b758755', '2026-01-09 23:47:06', '019ba001-5ab7-75ce-8f04-698bf1f776d6'),
(116, 3, '019ba11f-8978-7b9b-aadb-340cc96969e7', '2026-01-10 04:59:17', '019b76c0-83cf-7516-8488-6102914dcc34'),
(121, 2, '019ba3f3-bac7-782a-a74d-85a52088a679', '2026-01-10 18:10:17', '019ba3f3-a869-79ca-96ef-fde1936ba751'),
(122, 1, '019ba3f6-0aff-7841-a3a5-2be95514bc94', '2026-01-10 18:12:49', '019ba3f6-0353-792b-a948-cdcdf36e097a'),
(123, 2, '019ba3f7-02e1-7857-8771-ed87285384c6', '2026-01-10 18:13:52', '019ba3f6-fb32-73eb-8756-0097e3e89965'),
(128, 2, '019ba841-b7a7-72fa-aea4-65ff24522d83', '2026-01-11 14:13:57', '019ba83c-402e-78f2-8278-f8990dfb3836'),
(129, 2, '019ba844-1137-7936-b105-882a923319e5', '2026-01-11 14:16:31', '019ba844-01a5-7430-96fe-452a021af414'),
(130, 2, '019ba850-3c77-7781-83d4-9358d30a0d03', '2026-01-11 14:29:49', '019ba850-31f1-7c3a-a1c0-41863109e7c0'),
(131, 2, '019ba868-dcbc-797c-9a46-4457e71d3e60', '2026-01-11 14:56:43', '019ba868-d122-79f7-8c57-e15a29157ec4'),
(132, 2, '019ba883-d36b-7fdc-b3e7-a2f3c9b46143', '2026-01-11 15:26:10', '019ba883-ab29-76a5-863a-5cb1e6ea9b3b'),
(133, 2, '019ba88e-c62e-7373-9ba7-11c13678472d', '2026-01-11 15:38:07', '019ba88e-b98a-75e9-bde8-32e9c56b25e1'),
(134, 1, '019ba88f-b510-7433-9c93-96220b5148b7', '2026-01-11 15:39:08', '019ba88f-8fe1-761b-b135-ca574910e985'),
(135, 2, '019ba8fe-054c-7a27-855f-651e75e76ed4', '2026-01-11 17:39:38', '019ba8fd-f69c-7915-9e81-5e2cd78f443e'),
(136, 2, '019ba961-bbd2-7b9a-b270-382d77091d31', '2026-01-11 19:28:33', '019ba961-ae65-7ee2-990f-5a18264df442'),
(137, 2, '019ba9af-fc4b-7045-a9b3-60a8d516cb02', '2026-01-11 20:54:01', '019ba9af-e98b-7b12-b4fd-2eb0e4d25026'),
(142, 2, '019bace8-8379-7322-8480-a88b908c7112', '2026-01-12 11:54:37', '019bace8-738b-7e63-a7bb-654f6d8daa30'),
(144, 2, '019bad6d-bb53-700a-a0f8-67ccaf9a45ed', '2026-01-12 14:20:08', '019bad6d-953b-7804-90de-fe20248d610c'),
(146, 3, '019bae88-2b31-717e-9914-d77481c10f39', '2026-01-12 19:28:38', '019ba13e-3f1f-7002-babe-a263bff5e57d'),
(148, 2, '019baee6-ad7d-736d-9627-4699aeddca64', '2026-01-12 21:11:51', '019baee6-9c8c-7834-a44e-e7f1a806231e'),
(149, 2, '019bb149-f52b-7138-add1-130ae50443cd', '2026-01-13 08:19:32', '019bb149-e532-7126-b215-b3e976df8490'),
(150, 1, '019bb14c-73b0-772c-ad11-7f5684090d5c', '2026-01-13 08:22:16', '019bb14c-68ad-77e1-ac11-8e76f6b7783c'),
(151, 2, '019bb19f-6066-7e6b-9b98-336d0040955f', '2026-01-13 09:52:50', '019bb19f-3bd9-7ee4-8119-6eb5d0645087'),
(154, 1, '019bb1cf-fea0-740c-b8b1-5a2927cc530e', '2026-01-13 10:45:57', '019ba13a-127c-711a-9ee0-b8b292223f68'),
(155, 2, '019bb1d0-3f72-746b-b02f-ecc1c7b57fb7', '2026-01-13 10:46:13', '019bb1b6-5c0d-7454-bc90-987fc2de97a7'),
(156, 1, '019bb22b-30c0-7c8b-8268-11c901c26211', '2026-01-13 12:25:33', '019bb222-bf3f-7531-b0ac-c88bf303ee9d'),
(157, 2, '019bb2ce-19ef-7a64-997b-c7d64cb8c397', '2026-01-13 15:23:30', '019bb2ce-08dd-715a-b74c-ee84bf478a16'),
(168, 2, '019bb2f4-83b9-75af-8bd3-d8a3d1795eb6', '2026-01-13 16:05:27', '019bb2e6-6494-75a6-8963-ed5dc9be27c0'),
(172, 1, '019bb2f9-72ae-79b8-992e-2c44acb9dca9', '2026-01-13 16:10:50', '019bb2e6-6494-75a6-8963-ed5dc9be27c0'),
(175, 2, '019bb310-f8ce-7f6d-95e1-c0512b48cb32', '2026-01-13 16:36:32', '019bb305-3658-7949-accc-979153e19f1b'),
(176, 2, '019bb33f-4d10-762b-ab18-8752e7d11743', '2026-01-13 17:27:08', '019bb33f-1556-7df1-9a9f-b598a4f4a630'),
(177, 2, '019bb37b-4f91-70c2-8073-16ba59d265d6', '2026-01-13 18:32:41', '019bb37b-3d5b-73b1-893b-648f12914ec4'),
(179, 2, '019bb400-ff9f-790e-9375-d1d37fcb99da', '2026-01-13 20:58:42', '019bb3e5-dc6f-7d98-a901-9d822a73a86c'),
(180, 2, '019bb44e-b2be-7231-a782-fde27b52082b', '2026-01-13 22:23:35', '019bb44e-a7eb-70ab-ab59-270ea4541be5'),
(181, 1, '019bb6e1-df50-7de2-b56f-2a0f290f124e', '2026-01-14 10:23:34', '019bb6e1-30e3-7460-9d17-badc30d2f1e6'),
(183, 2, '019bb6fc-7719-71d1-ac4d-e42552473382', '2026-01-14 10:52:37', '019bb6e3-0e3c-7a51-baf0-7962444150bb'),
(185, 1, '019bb7a3-1114-7736-b47e-84e6a89371a8', '2026-01-14 13:54:35', '019bb78d-ef18-7f6d-9b00-83a71fb6c0aa'),
(186, 1, '019bb7da-3ef0-73ea-b6bc-8504b73fc467', '2026-01-14 14:54:52', '019bb7da-3570-72bd-b5ed-d3450ffd1dab'),
(187, 1, '019bb8b7-6f10-7006-9d93-03aca1737516', '2026-01-14 18:56:27', '019bb8b7-6506-7b07-afcd-64aae30af1b6'),
(191, 2, '019bb8cb-397f-7fac-8141-4149b39f7a67', '2026-01-14 19:18:04', '019bb8cb-3121-7b0d-8a09-f37c543fd47e'),
(193, 2, '019bb91b-c9df-72f9-8182-e3cf0378bc6a', '2026-01-14 20:46:04', '019bb8ba-674c-78a5-8617-56cc5f22ba2a'),
(209, 2, '019bbc04-473f-766f-a354-6428eb29ef1e', '2026-01-15 10:19:15', '019bbb0f-1c62-7ac7-a1c1-27700d37406d'),
(210, 2, '019bbc58-6d3e-7e0b-acd6-3b273ebddfca', '2026-01-15 11:51:10', '019bbc58-6109-7f50-b8d4-2f9151f80ec1'),
(212, 2, '019bbc85-6d42-7b9c-9156-e595303ea3f8', '2026-01-15 12:40:19', '019bbc6b-bab2-7dea-a384-999dede6583a'),
(216, 2, '019bbcf1-1fa5-7f26-b85c-82895e295136', '2026-01-15 14:37:57', '019bbcf1-1315-7b5c-b20f-4946ea437e22'),
(218, 2, '019bbd1e-3cd9-77d0-ba96-50ed1f96c7c9', '2026-01-15 15:27:14', '019bbd1e-2f09-74d0-8157-c60f10a74395'),
(219, 2, '019bbd2e-5409-7bdf-9030-92a6b5c58dbd', '2026-01-15 15:44:48', '019bbd2e-4736-7879-8a3f-e4be331fd86c'),
(220, 2, '019bbd51-5d14-7f70-8bb5-1a19cb8a0a6f', '2026-01-15 16:23:04', '019bbd51-4545-715a-a805-f4c8182e31e8'),
(223, 2, '019bbdf3-3fb6-7f25-a3c9-af63f55ac93c', '2026-01-15 19:19:54', '019bbdd8-f5d9-7c64-93eb-7f8c3783a015'),
(228, 1, '019bbe61-5eab-7e11-afb5-8aef415710bc', '2026-01-15 21:20:10', '019bbb10-2405-7147-a40d-c2ea503c8224'),
(239, 2, '019bc13b-7be4-748e-9f34-9f4307201588', '2026-01-16 10:37:39', '019bc105-46f8-7afc-98b5-08e96323b327'),
(240, 2, '019bc153-42e1-7c03-8596-c9b995e47092', '2026-01-16 11:03:37', '019bc153-32ca-7687-ac55-ef5e0486fa2c'),
(245, 3, '019bc208-31a9-7236-a8e6-e9db64be62a7', '2026-01-16 14:21:15', '019bb8c1-4c03-7269-8069-42fd97afc6fa'),
(247, 2, '019bc612-6af6-7a47-a018-70604c81e709', '2026-01-17 09:10:54', '019bc5fe-a70d-7712-8f10-66f4791ebcab'),
(248, 2, '019bd70f-dc61-744c-be8a-3e4c96e15fba', '2026-01-20 16:21:39', '019bd70f-bef0-778d-8bc4-ec56f75f1c73'),
(250, 2, '019bd7fa-d5a3-70ac-ab5e-a3bfc5c4df5e', '2026-01-20 20:38:18', '019bd7df-9029-77b9-9778-f46b2004b6c7'),
(251, 2, '019bd85c-67a3-7278-97ac-1e16a9835588', '2026-01-20 22:24:53', '019bd85c-5cf4-75ca-9abf-a5d02a2f3010'),
(255, 2, '019bd8ee-3f2c-79cf-bd30-dfc689c01f21', '2026-01-21 01:04:11', '019bd894-9333-7686-b6a2-c682e00a90ca'),
(259, 2, '019bdb97-99d4-7a77-b9a6-c967a024d505', '2026-01-21 13:28:24', '019bdb50-6860-7609-a1ac-ba76bdb10815'),
(260, 2, '019bdbcd-52f1-78ad-b339-333ead6c5d0f', '2026-01-21 14:27:05', '019bdbcd-40b4-7ba3-8ae5-5cb4d8acdb18'),
(261, 2, '019bdd97-7f81-723f-bd67-b14d09d1a912', '2026-01-21 22:47:31', '019bdd97-70a6-78a2-8db4-3eeb658a3198'),
(264, 2, '019bddf1-abbe-7577-8e8c-ae14307b4487', '2026-01-22 00:26:01', '019bddbb-4ee4-7d24-baf0-971666536b16'),
(265, 2, '019be04a-3ac1-738e-9fff-076e3426da44', '2026-01-22 11:21:59', '019be04a-2e28-7645-98ca-68f19acc4dff'),
(266, 2, '019be05e-d2ef-786f-9850-c36990c95c84', '2026-01-22 11:44:29', '019be05e-b8ee-76bf-9eb1-24288b691116'),
(267, 2, '019be082-8f1e-7c79-847b-5545a3aa9be7', '2026-01-22 12:23:31', '019be082-814b-7265-b6bf-b84f01e4bacb'),
(268, 2, '019be098-379c-7e34-b664-79ea8a8da827', '2026-01-22 12:47:10', '019be098-230c-7cbd-a2b7-0449accba6a6'),
(270, 1, '019be132-2dd4-7b42-93de-39579c2466a9', '2026-01-22 15:35:20', '019be132-2017-78b9-99d1-2ed0c126178f'),
(272, 3, '019be15c-8f0e-715f-a278-999a7175e39e', '2026-01-22 16:21:38', '019be15c-626b-70cd-aa5a-0daee6610e5e'),
(275, 3, '019be17f-7cf7-70b1-b075-baeb51d7b346', '2026-01-22 16:59:47', '019be0fc-1d9a-7328-994b-ce24eccaa03d'),
(278, 1, '019be1f6-5c80-706b-bf27-d56cfda034ca', '2026-01-22 19:09:37', '019be15c-e9ae-71a8-8e4d-ed2a4fa29d67'),
(281, 2, '019be21d-12af-7361-b9d5-0da165dc12f7', '2026-01-22 19:51:54', '019be21c-3012-7a8c-a5f8-d2ff68f1a435'),
(282, 3, '019be22d-61c2-7470-82ad-41e4b3ec15bb', '2026-01-22 20:09:43', '019be193-ff3f-74ce-bff8-2a4e0217a3c9'),
(289, 2, '019be2cf-41c8-7eb0-8d19-39ba14f6e8af', '2026-01-22 23:06:32', '019be2cd-653f-7477-a20d-c50a9d8c3e8a'),
(290, 3, '019be2d7-3d72-7595-b559-e5c164231cac', '2026-01-22 23:15:15', '019be2b3-ac9d-7ac8-9536-4412bf32a0a6'),
(294, 1, '019be459-99eb-7d1b-be8b-c2872328aca2', '2026-01-23 06:17:16', '019be216-6237-7166-99ea-c0f7fb3f2816'),
(296, 2, '019be4f4-76b3-7683-a55a-f5dfe6c34fb8', '2026-01-23 09:06:25', '019be4d8-f912-78b8-91bb-87a8c4ff8ae0'),
(298, 2, '019be5c4-17d2-7147-a966-7c04ade0a7fa', '2026-01-23 12:53:12', '019be5c3-69b0-7aea-bb96-003b1bb61434'),
(299, 2, '019be5dc-8cb6-7438-883f-bc9ce66b7545', '2026-01-23 13:19:55', '019be5db-fad5-7c03-be6d-66497d765a87'),
(300, 1, '019be5de-723c-78c4-a63a-fce34a84ac8e', '2026-01-23 13:21:59', '019be5de-6445-7861-b51d-43b2cd592c76'),
(301, 2, '019be5df-97b9-756f-8416-459e14f289c8', '2026-01-23 13:23:14', '019be5df-8f7a-7701-b40a-f2df36ff6bf1'),
(302, 1, '019be5e0-a3ff-779a-be80-f91b72574a1b', '2026-01-23 13:24:23', '019be5e0-9791-7fdd-b555-f602fc20af4f'),
(304, 2, '019be5f7-892b-71e7-8663-fc6082469028', '2026-01-23 13:49:23', '019be5e1-4f06-712c-918c-743acff0729d'),
(305, 3, '019be648-ac20-7750-a7df-04f92eedf7ab', '2026-01-23 15:18:00', '019be648-5e35-7edc-920a-9510e0137e7b'),
(306, 2, '019be725-8301-7880-b737-f9ffee43c7c1', '2026-01-23 19:19:13', '019be724-17a3-7871-9b31-1b9c80ff5e74'),
(308, 2, '019be796-6f39-754c-95df-a0b3c300adee', '2026-01-23 21:22:34', '019be774-69e5-7ad1-840e-687d99740894'),
(315, 3, '019be82c-eeed-7382-bb52-a893d8eea623', '2026-01-24 00:06:57', '019be7c4-710e-7606-afba-0d99cab4badb'),
(318, 2, '019beb45-5cb6-7dd3-ae43-57613e298be9', '2026-01-24 14:32:30', '019beb29-da88-7c78-bc64-33187d30eee6'),
(321, 1, '019beba1-bf53-729c-afb7-bb7fba174199', '2026-01-24 16:13:24', '019be4f4-924f-751e-bfe3-41ab0f9ac5a8'),
(323, 2, '019bec23-ce2b-766a-a30b-b33f4dec6d70', '2026-01-24 18:35:28', '019be7be-9a9a-74ea-a2ca-c19622755071'),
(326, 2, '019bed4a-c630-7fef-be3e-59c497cdd817', '2026-01-24 23:57:39', '019bed2f-4c02-7208-a68e-8cfcb7245d06'),
(330, 1, '019bee0b-3ba2-77e1-97ba-3d405604320f', '2026-01-25 03:27:52', '019bed30-81d9-7544-b8d6-dacab57cae86'),
(333, 2, '019bf026-4a1e-70c9-aa15-ec0560eb3b64', '2026-01-25 13:16:39', '019befd2-baab-7ca8-ba41-c4c8bb1a6729'),
(335, 2, '019bf05d-5717-75a9-9291-f8fc063cc92a', '2026-01-25 14:16:47', '019bf049-1af9-7d79-85d6-94cf00747513'),
(338, 2, '019bf09c-cd29-7be5-a895-90a6f7707b3e', '2026-01-25 15:26:06', '019bf066-cb58-7313-b34b-fc3d251382c5'),
(340, 2, '019bf1de-4a21-73f3-8ea2-f67eb09e337b', '2026-01-25 21:17:15', '019bf1c2-cff2-7346-8119-8d92de10603c'),
(341, 2, '019bf211-3d0b-79b8-9448-4aac22234f2e', '2026-01-25 22:12:54', '019bf20e-50b6-7bde-a6a9-fd2082fae341'),
(343, 2, '019bf23e-991c-7ded-8a21-9085a6510f22', '2026-01-25 23:02:27', '019bf223-1f60-7750-bf5d-8f52b25429e0'),
(345, 2, '019bf4c8-b0b8-79cd-9c11-961ac6413b3f', '2026-01-26 10:52:31', '019bf4ad-3782-7f2d-8acd-5f2be7b402e6'),
(346, 2, '019bf4de-8f9e-7fd1-b9f9-b442e45ee4ef', '2026-01-26 11:16:25', '019bf4de-8f24-75a7-b863-eca17a452ca5'),
(348, 1, '019bf4e4-23d3-746d-8fe8-1e75648d535e', '2026-01-26 11:22:30', '019bf4e2-875c-7a1d-81ff-d4f0ec70f19f'),
(349, 2, '019bf532-46a9-7e3f-ad7f-3227b2a12a3d', '2026-01-26 12:47:51', '019bf531-e1b7-7502-88a0-19eb5209dd97'),
(350, 2, '019bf645-5cb6-7195-b58d-efbb3ab9a746', '2026-01-26 17:48:19', '019bf645-1aec-7bde-81e3-7fc1dcf86360'),
(351, 1, '019bf646-a8db-7900-bd12-938848c64d67', '2026-01-26 17:49:44', '019bf646-a8bc-7e2f-aa1e-cdc7bfac7c81'),
(356, 1, '019bf6e9-d0d6-7ba1-91ea-4235843d6396', '2026-01-26 20:47:57', '019bf6c8-acfa-7159-a5b3-4d107cadaff8'),
(357, 2, '019bf6e9-ebda-78b7-be6c-c2b81fe467ba', '2026-01-26 20:48:04', '019bf6b9-3360-73ac-a644-efa57f8bd8e3'),
(358, 3, '019bf704-b131-7472-a1e5-9a8e5db05254', '2026-01-26 21:17:18', '019bf694-8ad9-7d99-8ec0-49b313c140c0'),
(361, 2, '019bf8e2-8ce9-7b4b-b44e-5b7263832059', '2026-01-27 05:59:15', '019bf8e1-e594-786b-b317-76550e24c775'),
(362, 1, '019bf8e5-db62-7a83-83cd-2b35d4cb0dd1', '2026-01-27 06:02:52', '019bf8e5-d726-708d-af57-a90b49f241b2'),
(363, 1, '019bf8e8-872e-75aa-966b-6cc6ccc2bb90', '2026-01-27 06:05:47', '019bf8e8-83ee-7a27-ab7e-c62f0649ed3d'),
(364, 2, '019bf8ea-f198-77c2-9dc5-86a5a89a1ae4', '2026-01-27 06:08:25', '019bf8ea-228e-7300-8c2d-d7e3686cc9b4'),
(368, 1, '019bf918-d619-7e25-b14a-7566a67c0f27', '2026-01-27 06:58:33', '019bf8fc-c9d0-79c8-917c-9f470f68dc7f'),
(369, 2, '019bf941-b2e7-79d4-befe-526954e5e831', '2026-01-27 07:43:11', '019bf941-55ef-7791-a5d2-afcf1c9d316e'),
(370, 1, '019bf954-48fc-790e-94e9-8bdac9bc6aa0', '2026-01-27 08:03:29', '019bf954-477b-7629-b595-849fd4c81555'),
(371, 2, '019bf959-83b3-7bcd-b3aa-9f0b54ad6a42', '2026-01-27 08:09:11', '019bf959-7fea-7573-bf4b-83b8a8c1edf3'),
(372, 1, '019bf964-1908-7393-95e2-9c76ae5e5f90', '2026-01-27 08:20:45', '019bf964-157a-7dc2-9dd6-9a2de34996f4'),
(373, 2, '019bf965-07aa-7d7f-9c56-fd41fa3c24ad', '2026-01-27 08:21:46', '019bf965-0639-7901-9187-617d33b6ffca'),
(375, 1, '019bf978-c428-7592-a68f-3adde54955ff', '2026-01-27 08:43:19', '019bf96d-30c1-7c79-80fd-c9eceee093ef'),
(376, 2, '019bf979-4149-7efd-a581-062a4d758fa8', '2026-01-27 08:43:52', '019bf979-3c3e-75d6-ad01-155ffc1bd206'),
(377, 1, '019bf97d-2434-73fa-8306-e9bd22651b42', '2026-01-27 08:48:06', '019bf97d-1d82-73da-8789-b6131bf20b86'),
(378, 2, '019bf97f-5a6c-767a-9bcd-3602df631a3f', '2026-01-27 08:50:31', '019bf97f-58ae-7723-a1a9-18c2253fff55'),
(379, 1, '019bf981-2a05-7df3-b93a-1d145f082a08', '2026-01-27 08:52:30', '019bf981-2601-7cea-848e-322f02166465'),
(381, 2, '019bf994-3b2f-724f-aa17-6dabbb05e95e', '2026-01-27 09:13:19', '019bf981-be9b-7758-bcbe-7371a2b71c68'),
(382, 1, '019bf99b-7e9a-7afa-a7c5-2b9cbda1366e', '2026-01-27 09:21:15', '019bf99b-7953-7c4d-ad1f-bf8af4f7cb87'),
(383, 2, '019bf99c-b017-78f3-983b-5368b66b874c', '2026-01-27 09:22:34', '019bf99c-abc5-7cac-ae4d-80ebfb9e0659'),
(384, 1, '019bf9ad-02c4-73b2-b57a-6ab1ce0b0554', '2026-01-27 09:40:23', '019bf9ac-fea9-715d-aa7a-96ee09cd0053'),
(385, 2, '019bf9ad-8da4-7069-a866-622b099b51d6', '2026-01-27 09:40:59', '019bf9ad-8bc5-74b1-b6a6-de62a990a04c'),
(387, 1, '019bf9af-b419-71f5-afe6-bda288fe7178', '2026-01-27 09:43:20', '019bf9af-a4c1-7edc-b910-3e3193afeee7'),
(388, 1, '019bf9b0-738c-76cb-81c8-c7a9ec8c792b', '2026-01-27 09:44:09', '019bf9b0-6f3c-736a-8078-cc517caa9282'),
(389, 2, '019bf9cd-ff32-7694-9dc2-938b0a27f8c6', '2026-01-27 10:16:25', '019bf9cc-e8e5-74fa-b6a6-33fb8d493b6c'),
(390, 2, '019bf9d4-80ff-7989-9df7-3314bcb71e8d', '2026-01-27 10:23:32', '019bf9d4-7b8a-7eb4-8949-fedf269d778d'),
(391, 1, '019bf9d4-fe3d-7b59-825c-7dac746ade74', '2026-01-27 10:24:04', '019bf9d4-f9d0-787f-9080-49b2e09ba322'),
(393, 2, '019bfa81-a112-75fb-b9b8-c8bb7b8e229d', '2026-01-27 13:32:38', '019bfa81-9da2-7db2-8514-bd49c5c0d187'),
(394, 1, '019bfa88-3c1b-7f75-9efc-50754dc26493', '2026-01-27 13:39:50', '019bf73e-84ab-7741-8aec-066ae7e8a5f4'),
(397, 1, '019bfaa2-bd29-7a47-8112-f88063c3f7a3', '2026-01-27 14:08:47', '019bfa88-be1c-726e-8fee-37bd77150f16'),
(398, 2, '019bfaaf-2767-7e3f-be8b-6322b1295e3f', '2026-01-27 14:22:21', '019bfa93-a0e5-77bc-83d1-20fabacd8f25'),
(401, 1, '019bfae6-0bb1-7cd7-9a3a-e4574d0d768e', '2026-01-27 15:22:18', '019bfabc-29fd-7ea2-957c-8fea7ea5047a'),
(402, 1, '019bfb6f-5866-7061-b9f4-87aff983fbc5', '2026-01-27 17:52:16', '019bfb6e-b181-7fb0-b95f-3eb8e3091212'),
(403, 2, '019bfb84-5680-7eac-af26-eef7a497a624', '2026-01-27 18:15:12', '019bfb84-093e-7d1c-abe1-6bbe67bd0090'),
(404, 2, '019bfb93-998a-709b-9047-a24b26642f20', '2026-01-27 18:31:52', '019bfb93-9454-782c-8eaf-d46d905908ce'),
(405, 2, '019bfbbf-16c5-7116-98d1-35c6d6b190a1', '2026-01-27 19:19:23', '019bfbbe-9bb8-77be-b4e1-d6310830f3a7'),
(406, 2, '019bfc2a-3816-78dc-9f2e-267a9cf0fb52', '2026-01-27 21:16:23', '019bfc29-ded6-7fd9-b2ae-4db950e86b96'),
(407, 2, '019bfc4d-c080-7d98-98ff-f4e25f51b5cc', '2026-01-27 21:55:12', '019bfc4c-9385-742d-b943-e0e072132fe5'),
(408, 2, '019bfd5a-9c28-7210-8578-5564bfad85ad', '2026-01-28 02:48:52', '019bfd59-1707-7de1-831c-b8fc0d0adb9d'),
(409, 2, '019bfe8f-17e0-77e7-9fe2-e791ba599c21', '2026-01-28 08:25:49', '019bfe8e-cceb-7d19-af08-e9513f62aea9'),
(410, 2, '019bfea3-8fef-7845-9637-52be5ca2a22f', '2026-01-28 08:48:10', '019bfea3-1fd1-7252-bce6-fab0b89e55c3'),
(411, 2, '019bfed8-705a-7f7e-8760-965120e60dfb', '2026-01-28 09:45:56', '019bfed7-d366-7ca4-a336-38495c35bf9a'),
(412, 2, '019bfef8-be41-7f26-86bc-d7bd336cfcad', '2026-01-28 10:21:13', '019bfef7-0a4d-7f31-8d56-16a79fd6a067'),
(414, 2, '019bff33-39a7-739a-a303-713742d082f4', '2026-01-28 11:25:05', '019bff17-bcf8-70e4-8ee7-022b0692f330'),
(415, 2, '019bff4d-fd49-7a58-af0e-3cc0e732afdb', '2026-01-28 11:54:19', '019bff4d-978f-72e0-939a-277d5e0b9118'),
(417, 2, '019bffd5-69f9-7cc2-8318-a6ac07c1d21e', '2026-01-28 14:22:15', '019bffb9-83ee-72ed-b6e6-c14f3812b064'),
(418, 1, '019c0054-f3e7-7ba3-80c5-2102fd7d3bc7', '2026-01-28 16:41:33', '019c0047-975c-75d9-953f-85582c33e463'),
(419, 1, '019c0156-85ed-77ea-8e40-e340584cb5ad', '2026-01-28 21:22:53', '019c014d-c802-766e-b2ca-aa19fea88050'),
(421, 1, '019c0343-34ff-759d-bf28-4ab95d5c7dbb', '2026-01-29 06:21:02', '019c033f-9a48-78c1-8b83-0bccd00d7340'),
(422, 2, '019c03df-627c-7736-a101-55de1ef330ea', '2026-01-29 09:11:37', '019c033c-8c8e-791f-aa03-5bb801a220f4'),
(423, 1, '019c03ea-1fc0-738f-8986-76c0f718db9c', '2026-01-29 09:23:21', '019c03e9-6572-787a-a5f7-394d2c258cc8'),
(424, 2, '019c03f6-30af-7f94-8f5d-a63e963ab976', '2026-01-29 09:36:31', '019c03ef-13f3-7661-9d68-d08c8dd84155'),
(425, 1, '019c0415-ab3e-70ea-a112-a58df513c5cc', '2026-01-29 10:10:54', '019c0414-ce2e-7e35-9dee-37bbf69c10b8'),
(426, 1, '019c043b-019f-7b25-b410-606a4bcfc264', '2026-01-29 10:51:41', '019c0437-62f8-70e0-b3d7-5af4aa0a3fbd'),
(427, 2, '019c0473-e0a0-762b-bdba-576b3e8cc7ca', '2026-01-29 11:53:48', '019c0473-4481-7bb7-b205-bdd44800f289'),
(429, 2, '019c04ed-2dae-7c5d-a2d0-62b64f7c9d02', '2026-01-29 14:06:18', '019c04ec-db8c-7c73-9b2c-2c026b8137db'),
(430, 1, '019c04ef-71ad-7c74-8c34-8ec54387f3bc', '2026-01-29 14:08:47', '019c04e9-ff07-7624-bc88-434d220ec603'),
(431, 2, '019c0504-39fb-7ea2-9038-9b62938d9117', '2026-01-29 14:31:29', '019c0504-2f24-70b5-9816-5c3f613743fd'),
(432, 1, '019c0505-aa7a-7ec8-93d1-b471dba37ba8', '2026-01-29 14:33:03', '019c0505-a29a-7e43-a343-a00035b5f753'),
(434, 2, '019c0533-46eb-79c1-968b-8ea829d5d563', '2026-01-29 15:22:52', '019c0506-d486-7c81-98dc-888644bb5567'),
(435, 2, '019c053e-012a-7262-a7e8-80e8dd4335a8', '2026-01-29 15:34:35', '019c053d-9ce7-7858-a8cd-aeebd7172a8e'),
(436, 1, '019c053f-8ba4-71b4-8580-2975a4efd259', '2026-01-29 15:36:16', '019c053f-8b47-712d-8fba-f52dda9422b8'),
(437, 2, '019c0540-ca0f-7991-9313-65641b3c3e6c', '2026-01-29 15:37:38', '019c0540-c86b-7072-8361-627ef8411673'),
(438, 2, '019c0541-236f-74c6-bd6d-edbe0f57f3ff', '2026-01-29 15:38:00', '019c0541-22ae-76f9-90ae-9644b09de7df'),
(439, 1, '019c0541-cb0f-79d1-8dc1-f740f2c140b3', '2026-01-29 15:38:43', '019c0541-a5f7-729a-a79e-c345d14c6ee9'),
(440, 2, '019c0565-9ed8-78b8-9434-284ff1d46253', '2026-01-29 16:17:51', '019c0564-f218-7a72-a648-d8a8f597a5bf'),
(441, 1, '019c0566-b10b-7c93-8e16-18c867af6436', '2026-01-29 16:19:02', '019c0566-2ae8-7c9c-be9f-4d492a7caff7'),
(442, 2, '019c0568-642f-7fc3-b779-6f36527a1b24', '2026-01-29 16:20:53', '019c0568-6309-7a7f-a1b1-d9dff3160427'),
(443, 1, '019c056b-4750-7301-b113-bd4fa3b3ef3a', '2026-01-29 16:24:02', '019c056b-44ca-79da-b9e0-eca1b27fde7f'),
(444, 2, '019c0571-9be6-7392-bad9-8a52cf35f4af', '2026-01-29 16:30:57', '019c0571-9966-7002-a38d-f75bbc3d7b65'),
(445, 1, '019c0572-cfe0-78f0-9ace-4b46f9249951', '2026-01-29 16:32:16', '019c0572-9901-7f24-bc63-fc91de664f7b'),
(446, 1, '019c0577-8193-71e6-bcea-c4e67eed051c', '2026-01-29 16:37:23', '019c0577-6a79-7d3d-a8d0-9ad6849898a1'),
(447, 3, '019c05ec-ba19-7169-90f6-d04ff973d61a', '2026-01-29 18:45:26', '019c05ea-55bc-7ca9-aaf8-89b9893783f1'),
(448, 3, '019c05ee-942d-7bcb-b852-c1a47e77b516', '2026-01-29 18:47:27', '019c05ed-e9be-7931-b2e0-34b9337f2efa'),
(449, 2, '019c05f9-8d05-7132-8ad8-56d435232c7a', '2026-01-29 18:59:26', '019c05f7-3f72-704d-9290-ce02f700b62d'),
(451, 2, '019c0600-1314-7b6f-a1ba-1ea7b2042b54', '2026-01-29 19:06:34', '019c0600-12f6-7c18-a2e4-b4adc9ec5c2b'),
(452, 1, '019c0636-b81b-73dc-9328-2b303993b603', '2026-01-29 20:06:15', '019c05ff-0878-7920-9f0d-ddc27fa03fe6'),
(453, 1, '019c0688-ad5c-7d66-a894-a68995416d27', '2026-01-29 21:35:46', '019c0686-50a3-7c57-82b2-49f297ad95c2'),
(455, 2, '019c06f1-fcb2-7882-b482-c6d2c0d5b0cc', '2026-01-29 23:30:48', '019c068a-be1c-7fa9-8add-6f83d1c5f83b'),
(456, 1, '019c082a-9115-7ef1-b15d-b27633f15102', '2026-01-30 05:12:13', '019c07ce-b50c-7b8c-ae6e-244401541d3a'),
(458, 2, '019c0832-d064-7fec-a576-837fb1e3d717', '2026-01-30 05:21:13', '019c082b-67b1-7577-827b-7419d6aa7413'),
(459, 1, '019c0836-d0d9-73d4-bd92-bf15538ae5bd', '2026-01-30 05:25:36', '019c0835-c38e-76c2-b6a3-a278df9720f9'),
(460, 2, '019c0837-c52b-737d-b2df-4b9e173b4d62', '2026-01-30 05:26:38', '019c0837-c74d-7a5d-a651-e0183eb7ed08'),
(461, 1, '019c0843-ca92-7a23-b12c-d459d1fa98bf', '2026-01-30 05:39:46', '019c0843-cc35-725f-a866-0ec7455cb993'),
(463, 2, '019c086c-b166-7d21-a975-2d4302bccb3b', '2026-01-30 06:24:26', '019c0864-0343-7217-a8e1-b9c5e6ecbaca'),
(464, 1, '019c086f-c141-7042-91cb-3a43bbdfa9a2', '2026-01-30 06:27:47', '019c086f-c238-7ae7-8772-35cf48277610'),
(465, 2, '019c0870-60b9-7613-a937-0d8e16dbc47e', '2026-01-30 06:28:28', '019c0870-633d-79c8-9b99-0452be0ae8d2'),
(466, 1, '019c0871-0606-72c2-9a65-ac96a7d62707', '2026-01-30 06:29:10', '019c0871-069a-78b2-a0c7-35f0fca2cafb'),
(467, 2, '019c088d-89b3-7e10-bff2-9d7857686467', '2026-01-30 07:00:19', '019c088a-8be3-7a8f-ad0d-dbb93c9f1f91'),
(468, 1, '019c088e-6d73-7354-97e8-4c9010f7d298', '2026-01-30 07:01:17', '019c088e-6c08-7bfb-9747-e01b7e75dd98'),
(471, 1, '019c08e4-8ec1-70d5-b151-4daae89c9341', '2026-01-30 08:35:22', '019c08ad-a08c-70a7-ac3c-7d47f780db06'),
(472, 1, '019c08ff-401e-7037-952e-4c7074334894', '2026-01-30 09:04:31', '019c08fd-78fb-7926-bda3-2a7a0b4c9bca'),
(473, 2, '019c090c-8970-76eb-8220-c85c78f1d4d5', '2026-01-30 09:19:02', '019c090c-8931-7937-a79b-c255f1d9c5d5'),
(474, 2, '019c090d-cdc7-7e92-a167-b386563912b2', '2026-01-30 09:20:25', '019c090d-c90b-7b5a-b753-b102b06971d5'),
(475, 2, '019c0911-6873-736a-a633-2041bf8edbba', '2026-01-30 09:24:21', '019c0911-665d-730c-9425-c3a66d60f964'),
(476, 2, '019c0912-040a-7395-9b73-5671ed844b0b', '2026-01-30 09:25:01', '019c0912-0155-76b9-9195-d4289e5b62d4'),
(477, 1, '019c0912-b6bd-7be3-9216-816a512230e2', '2026-01-30 09:25:47', '019c0912-b247-71a5-8384-edbb33449dfe'),
(479, 2, '019c094b-f27d-764b-98cc-17e85c6370f1', '2026-01-30 10:28:18', '019c0924-8b8c-74dc-aa0f-815b7ca3cd00'),
(480, 2, '019c0962-cc68-7a37-9ff3-381282472e29', '2026-01-30 10:53:15', '019c0962-4fed-7941-9de1-ed6d41176327'),
(481, 1, '019c0963-b104-7c0e-9a29-232394eb4526', '2026-01-30 10:54:14', '019c0963-8d0a-7477-9011-55793424aee9'),
(482, 2, '019c0964-e1f4-7926-94f6-a7318851ded1', '2026-01-30 10:55:32', '019c0964-db22-78a2-9421-6771ca4322a0'),
(483, 2, '019c0990-f875-749a-b73f-1ae5f71be673', '2026-01-30 11:43:41', '019c098f-f0e1-7328-a5e6-429a4eacfc1c'),
(484, 1, '019c0991-f270-7da4-8a6b-931922c3426c', '2026-01-30 11:44:45', '019c0991-e5db-711d-afe7-0ac380915231'),
(485, 2, '019c0992-65ef-7d9e-a17d-be62f8cf9474', '2026-01-30 11:45:15', '019c0992-6227-770c-9142-fbd85d11469a'),
(486, 2, '019c09c3-a658-7b99-98ab-71a6c265b04a', '2026-01-30 12:39:03', '019c09c2-7320-7cab-86ca-44f948a44169'),
(487, 2, '019c09cd-5e7c-7e35-8b47-2c599c1ff9fd', '2026-01-30 12:49:39', '019c09cb-365c-797d-9c85-47e9cbc4d6e2'),
(488, 1, '019c09cf-511d-7756-bf22-8c697147cf43', '2026-01-30 12:51:47', '019c09cf-103d-746a-a23b-05652c9f5eea'),
(489, 1, '019c09d5-c7a5-78cf-8eb1-ad9df8f03087', '2026-01-30 12:58:51', '019c09d5-2835-7f76-be36-6f378cb0ea5b'),
(490, 2, '019c09e1-04d9-79c4-a9b1-a1187969be85', '2026-01-30 13:11:07', '019c09e1-0465-73bd-9462-42a210fa18e8'),
(492, 1, '019c09e6-ad59-796b-ad8e-4e78ccbf4bd6', '2026-01-30 13:17:18', '019c09e2-6a8f-75e5-ac2b-39dc955fa873'),
(493, 2, '019c09f0-a7ee-7af9-a4e6-f5e2b0a10342', '2026-01-30 13:28:12', '019c09f0-5606-75fd-8ab4-31cef24229ee'),
(495, 1, '019c0a02-719f-7ddd-96c8-8831417d55f1', '2026-01-30 13:47:38', '019c09f1-6f03-7997-a9b1-a7f4dcafe3ba'),
(496, 1, '019c0a20-b371-7a78-87f9-43972173546e', '2026-01-30 14:20:41', '019c0a1f-97f1-7f0d-a2df-fba26a155699'),
(497, 2, '019c0a21-9367-796c-a908-7aecb6fbe28b', '2026-01-30 14:21:38', '019c0a21-90ce-7608-9786-64a65310906a'),
(498, 1, '019c0a22-013c-727b-9505-cd7cb53ce31c', '2026-01-30 14:22:06', '019c0a21-e7ac-722e-b2b1-0f07447b5724'),
(499, 2, '019c0a22-50d2-7450-a3ab-89ddfb52b024', '2026-01-30 14:22:27', '019c0a22-4dee-7996-bba8-b2975888ed3a'),
(500, 2, '019c0a23-27a2-7ae8-8efc-5ca28467c9d9', '2026-01-30 14:23:22', '019c0a23-2685-730d-8916-a91b1430c9e6'),
(501, 1, '019c0a23-61da-72d4-a01b-4d133a61b789', '2026-01-30 14:23:36', '019c0a23-60e1-7718-82e6-1c8de60774af'),
(502, 1, '019c0a32-1532-7673-a595-2cdba803e478', '2026-01-30 14:39:40', '019c0a32-02f9-7f99-8a16-5f3059b4fc79'),
(503, 2, '019c0a35-9ed6-7e27-bec8-8fe7adf7874f', '2026-01-30 14:43:32', '019c0a35-9e30-7ee3-8b7d-e69b6d7ddb99'),
(504, 2, '019c0a37-50b6-770a-a4ad-b3027dd70bef', '2026-01-30 14:45:23', '019c0a37-4836-72d5-b1f0-fddb99e290ec'),
(505, 1, '019c0a37-9f7e-7faa-b278-7bcae9c5cfef', '2026-01-30 14:45:43', '019c0a37-9b9c-7573-842d-3429d3645e8d'),
(507, 1, '019c0a3b-1114-76bc-95e2-9805ced795db', '2026-01-30 14:49:29', '019c0a39-5718-743f-a442-03f6ae37523d'),
(508, 1, '019c0a3f-6cb7-7a7b-b8c0-9c317031b26c', '2026-01-30 14:54:14', '019c0a3f-6bb2-7ab5-a8ef-a16bc663c8b3'),
(509, 2, '019c0a3f-d9d0-7ec1-af27-e1451f93a90d', '2026-01-30 14:54:42', '019c0a3f-c9a0-7ba0-8dc4-0d87ed971b42'),
(510, 1, '019c0a41-bb49-7145-8621-9d58281f53a8', '2026-01-30 14:56:45', '019c0a41-8b41-7c48-a037-84723a3f0f51'),
(511, 2, '019c0a72-1972-7c9b-b59f-3e55286a6b2f', '2026-01-30 15:49:35', '019c0a70-c2b4-7c30-97f0-6abae5021d77'),
(512, 2, '019c0b4d-c0d3-7e88-86db-0514eef9c54b', '2026-01-30 19:49:30', '019c0b4d-1775-7ed0-8bc9-21b00bedf5c8'),
(513, 1, '019c0b4e-bcc8-7882-a8c6-5afe00bcae72', '2026-01-30 19:50:35', '019c0b4e-a998-7ad1-8ebb-d8e104054e3e'),
(514, 2, '019c0b5a-8aed-7f12-9da5-c890225e8826', '2026-01-30 20:03:29', '019c0b5a-8738-722c-9b1a-8d3c902b1448'),
(515, 2, '019c0b7d-828c-721c-a2e1-7273ec225ab8', '2026-01-30 20:41:40', '019c0b72-a08e-781d-8ea2-3f71e34d772c'),
(516, 2, '019c0bf3-f3aa-7275-9ccb-7432cd4c84a4', '2026-01-30 22:51:02', '019c0bf3-0d93-71f1-8e2a-80d9d6330235'),
(517, 1, '019c0c05-e115-74a5-8e71-9f190c492e0c', '2026-01-30 23:10:37', '019c0c05-a58b-7cdf-8b83-feb8d5489f48'),
(518, 1, '019c0c11-22d4-70d9-8b36-49ba9755e266', '2026-01-30 23:22:55', '019c0c11-2156-7a56-af25-3dc5bf89ca5e'),
(519, 2, '019c0c11-dd79-7ec6-8dfc-cfabcdbcebc6', '2026-01-30 23:23:43', '019c0c11-dbdf-70c2-b06d-a43faf317f17'),
(520, 1, '019c0c13-cd08-79cb-8cc1-4259e5db1f19', '2026-01-30 23:25:50', '019c0c13-c92c-7b0f-a315-132fb880b7db'),
(521, 2, '019c0e71-10ec-7a59-aafb-5ffc606ac759', '2026-01-31 10:26:56', '019c0e6f-f108-769a-84cc-4d74a704b0d3'),
(522, 1, '019c0ea5-9259-7be7-b30c-1547e8fa25b1', '2026-01-31 11:24:17', '019c0ea4-7bb5-7b3b-8dcd-a532e032997d'),
(523, 2, '019c0eb7-8df9-7565-8a4e-90879b8ba236', '2026-01-31 11:43:56', '019c0eb4-efff-7b06-b7d1-de36c04dee81'),
(524, 2, '019c0f37-d15d-7cb0-97fb-68f532a4cf6f', '2026-01-31 14:04:02', '019c0f34-25f1-7952-b023-6796dbb82f43'),
(525, 1, '019c0f38-7036-7003-98ae-754e2a4eb396', '2026-01-31 14:04:42', '019c0f38-6667-7498-9ee7-c147fa86b556'),
(526, 2, '019c0f38-f78b-7687-b996-071514e45097', '2026-01-31 14:05:17', '019c0f38-f17f-7959-9dd5-45d468db614e'),
(527, 2, '019c0f3e-2460-75d8-b68c-0edf5746faaa', '2026-01-31 14:10:56', '019c0f3d-a8c7-79f4-ac9f-6e6e6d799264'),
(528, 1, '019c0f3e-80a6-73d8-972d-eb4d10dd27c6', '2026-01-31 14:11:20', '019c0f3e-77b4-75bc-af9f-7a909b1235c3'),
(529, 2, '019c0fc3-7f92-7d6a-a5aa-038bc9626e88', '2026-01-31 16:36:36', '019c0fc2-7d86-73bd-9a9c-60117cc7aca7'),
(530, 1, '019c0fc5-531a-75e2-b70b-ce9d8ac8168e', '2026-01-31 16:38:36', '019c0fc5-4977-7b17-b56d-290e8c353651'),
(531, 1, '019c0fd2-a67b-7c65-b37c-bb5f1409cfcd', '2026-01-31 16:53:09', '019c0fd1-6a63-718d-bf16-fbbdf974dfd8'),
(532, 1, '019c0fd3-d512-7d32-bfea-5cfc95acb18d', '2026-01-31 16:54:26', '019c0fd3-cc9c-7d53-8405-3db7703da062'),
(533, 2, '019c0fd4-3a4d-7e8a-a6cd-255fbae6fb1e', '2026-01-31 16:54:52', '019c0fd4-33d4-77dd-98a1-dbb78e4fe52f'),
(534, 1, '019c1020-478c-753c-b799-80f3b674d24c', '2026-01-31 18:17:56', '019c101f-3b4d-793f-a91d-b2e735ec057f'),
(535, 2, '019c1084-0515-739e-9da6-662c8441a81f', '2026-01-31 20:06:53', '019c1082-35e9-7863-b5c5-505b525abc0f'),
(537, 2, '019c12bb-beb1-71c2-9d78-ff1f34232e5e', '2026-02-01 06:26:59', '019c12b7-6f86-78bd-a7ef-f9b7cdde409e'),
(538, 2, '019c12fd-02d3-7d98-ae0c-8472be1f2897', '2026-02-01 07:38:17', '019c12a8-9854-7fa9-9487-4347a78d46b5'),
(539, 1, '019c1303-56fe-78a3-bff5-c6da3b7ea7d8', '2026-02-01 07:45:11', '019c1303-4f5e-7a05-b575-3534d9c006b6'),
(541, 2, '019c1324-fb84-7449-bfb4-702ef9d37ce6', '2026-02-01 08:21:56', '019c1324-3e52-7fc3-9c24-b605f92bef3d'),
(543, 2, '019c1375-dda1-75b6-bf54-5e9880d22013', '2026-02-01 09:50:17', '019c1314-8c85-7296-b3d3-da25cfbe5fc7'),
(544, 1, '019c137b-3b3c-7e6a-9969-7afd53cbf8c4', '2026-02-01 09:56:09', '019c137b-3596-71e8-9f04-87f7144f72c1'),
(545, 2, '019c13a0-40fa-7685-a325-fc79d4a39558', '2026-02-01 10:36:35', '019c139f-c0a3-7e5a-8690-37c70bcc76bd'),
(546, 2, '019c13a0-e019-71dd-9608-6af2f09ecff4', '2026-02-01 10:37:16', '019c13a0-d6e8-7883-926b-0c128dc622d2'),
(547, 1, '019c13a3-3bb9-7aa7-9bee-1df4604ff277', '2026-02-01 10:39:50', '019c13a1-1e9e-71b0-898e-69e9697ff13e'),
(548, 2, '019c13d8-a490-7f38-a495-42e039c49541', '2026-02-01 11:38:10', '019c13d8-9be1-743c-8734-bb20244edd11'),
(549, 2, '019c1428-ce0b-7627-9385-c845de67f1fd', '2026-02-01 13:05:44', '019c1428-8dbe-78ac-a145-223704144c45'),
(550, 1, '019c1429-f74b-7bc6-9394-e0df7d4c765f', '2026-02-01 13:07:00', '019c1429-ec55-7e5c-96d1-0f5717d05b46'),
(551, 2, '019c142a-e6e2-79e1-ab85-cb59c5799386', '2026-02-01 13:08:01', '019c142a-da69-7c78-84df-4f3fa08a374c'),
(552, 1, '019c142b-b627-7f15-9e7d-db60cfde1dbb', '2026-02-01 13:08:54', '019c142b-ac6a-7548-98a3-13aabd01ba50'),
(554, 2, '019c1474-c1f1-7b16-8966-27eb5cf0bcc5', '2026-02-01 14:28:42', '019c1466-be30-7031-b03f-1b086b27e06a'),
(555, 1, '019c147b-f212-708a-b978-2cd435ae6b96', '2026-02-01 14:36:33', '019c147b-e8a9-73ab-8580-634ace0f4d70'),
(556, 2, '019c147d-7082-7e42-8775-f2c40c077512', '2026-02-01 14:38:11', '019c147d-6834-704d-bd7f-fdd6cb1b1ea4'),
(557, 2, '019c1488-c27d-70d6-9177-5d797d2d0b03', '2026-02-01 14:50:32', '019c1488-85e6-7e2c-8c40-e37a2207d402'),
(558, 1, '019c148f-02c0-780c-a361-bb5adaf1321f', '2026-02-01 14:57:22', '019c148e-faba-7db6-b04c-8d6ce6c405df'),
(559, 2, '019c1490-13eb-766d-8d74-d86f3fd241f0', '2026-02-01 14:58:32', '019c1490-0c5e-755b-98df-29e9d66d0cb5'),
(560, 1, '019c1498-c8c7-7c34-97f0-0bacc780dfc2', '2026-02-01 15:08:03', '019c1498-bf94-75e0-9c4e-e475528a12c0'),
(561, 2, '019c1499-122d-7163-9b16-3455028605fd', '2026-02-01 15:08:21', '019c1499-0b65-7cba-b976-5d59b5bbf94e'),
(562, 2, '019c149c-5515-740b-9651-2a1086ca2190', '2026-02-01 15:11:55', '019c149b-ee2d-76c5-86bb-1e31da3c2a16'),
(563, 2, '019c14b7-67d3-7c3e-a475-294fcc36f26f', '2026-02-01 15:41:29', '019c14b2-0694-7655-9193-8fc962a25608'),
(564, 1, '019c15d3-26ab-7e4d-9ef9-27c813d00683', '2026-02-01 20:51:25', '019c15cf-f4e6-7dd9-8f42-50dd311da1d8'),
(565, 2, '019c15d3-dba6-71fe-8d0c-4d7302363d09', '2026-02-01 20:52:11', '019c15d3-d882-7960-bc63-0cee5bef8016'),
(566, 2, '019c15fb-23e4-77b5-b286-941553228e7f', '2026-02-01 21:35:06', '019c15fa-b898-7d16-bce1-353946d581a5'),
(567, 2, '019c160a-80d9-7ff5-8922-b671217bf481', '2026-02-01 21:51:53', '019c160a-0d06-7ae2-8362-1e5691ebad91'),
(568, 1, '019c160b-5102-7b1e-85c5-e1c96b2f9818', '2026-02-01 21:52:46', '019c160b-4b42-7eda-a331-13d8d28bc590'),
(569, 1, '019c161d-61b1-7579-9fe9-8f7c325d3398', '2026-02-01 22:12:30', '019c161c-0375-71e0-b747-273e15133692'),
(570, 1, '019c161f-060a-7d8e-81bd-9a90b85ba2f3', '2026-02-01 22:14:17', '019c161d-cf3c-710a-94e8-01440ff18f6c'),
(571, 2, '019c161f-5aa7-74c5-8955-0a8b9199f7a8', '2026-02-01 22:14:39', '019c161f-54bf-7982-820c-84baf752e993'),
(572, 1, '019c1622-3a08-7cfa-b065-ae3c35ccb8d4', '2026-02-01 22:17:47', '019c1622-3499-7163-9b30-5477769e383a'),
(573, 2, '019c1623-3021-74da-a443-b1fc0ed96199', '2026-02-01 22:18:50', '019c1623-2af8-7aca-b0e4-4fe7fa620c08'),
(574, 2, '019c1630-3261-7ebe-8030-a3dbb9b49a46', '2026-02-01 22:33:03', '019c1630-2ec0-74b5-87ef-44825520b888'),
(575, 2, '019c1630-9b1e-7e48-b091-458700c47bc1', '2026-02-01 22:33:30', '019c1630-95e4-751c-bcf1-a8ed4c5d3846'),
(576, 1, '019c1631-5388-7e26-b852-497e82d08adc', '2026-02-01 22:34:17', '019c1631-4d8b-7bd2-a971-ec507bea920f'),
(578, 2, '019c1637-7df3-7618-9a55-6796fa16e638', '2026-02-01 22:41:01', '019c1635-7f75-71da-81d6-5b1530a2132d'),
(579, 1, '019c1668-60e7-78db-a0b6-37478d3d45e1', '2026-02-01 23:34:25', '019c1667-fc92-7c03-9888-962501ec746f'),
(580, 2, '019c1669-cc8d-7451-8dcb-45110a6d4510', '2026-02-01 23:35:58', '019c1669-c6e4-7e62-a3dc-52cf5b4c2f66'),
(581, 1, '019c166b-89f6-78b4-be61-c14b49fbcd5c', '2026-02-01 23:37:52', '019c166b-85e9-76be-a997-5a4b058f990b'),
(582, 1, '019c1670-0a28-7be9-97ba-3f9b7e228367', '2026-02-01 23:42:47', '019c1670-066b-7f0b-ae27-0a3fe3f9fb1f'),
(583, 2, '019c1670-6d6d-7216-a757-324fe6880e97', '2026-02-01 23:43:12', '019c1670-6830-7d25-ba92-f2848fd8e2eb'),
(584, 1, '019c1672-a034-7122-93db-5a4611ceabcd', '2026-02-01 23:45:36', '019c1672-9c3d-7712-ae37-38e69ce40381'),
(586, 2, '019c1683-7422-77d7-9274-c94e3f63ce16', '2026-02-02 00:03:59', '019c1673-f862-7f70-b70f-591eb16b236e'),
(587, 1, '019c1685-88fc-74c6-ba91-6c0f766c5f7b', '2026-02-02 00:06:16', '019c1685-8740-796d-bd08-5a3253a7d366'),
(588, 2, '019c1686-12c8-772c-913f-68e91124efef', '2026-02-02 00:06:51', '019c1686-0ea9-7e13-8fc7-92403d182a00'),
(589, 1, '019c1687-4b06-7635-8830-514324b3dbf3', '2026-02-02 00:08:11', '019c1687-491d-7f11-beb2-77a726aa6a25'),
(590, 2, '019c1691-04f2-76e9-ade6-80828f84112f', '2026-02-02 00:18:48', '019c1691-0067-70bf-a3d7-df241df9588a'),
(591, 2, '019c1694-2187-7533-8e57-df37893e9cbb', '2026-02-02 00:22:12', '019c1694-1aa8-76eb-9383-f18d19fe6d50'),
(592, 2, '019c19d3-a694-76bf-9a69-252d5328cdf8', '2026-02-02 15:30:27', '019c19d2-be64-7b64-bba2-f1cf84610e9f'),
(593, 1, '019c19d4-bc96-7706-8db1-f3fe261a479b', '2026-02-02 15:31:38', '019c19d4-b415-705d-bd73-73fb6cabe3ee'),
(594, 1, '019c1a56-6e56-705d-8bf1-7228028f64cd', '2026-02-02 17:53:17', '019c1a56-0569-7fa5-bf23-d63e3fed8ca9'),
(595, 2, '019c1a62-ed49-7952-b97a-aff3bfaac81b', '2026-02-02 18:06:56', '019c1a61-d66a-7ac7-9718-512a7b1e9990'),
(597, 1, '019c1a67-2741-78bd-99c9-80df6aabd303', '2026-02-02 18:11:33', '019c1a66-76f7-745e-a05b-0701ba61848a'),
(598, 1, '019c1a79-2052-7636-8763-08eb9688d0d3', '2026-02-02 18:31:11', '019c1a77-f1d0-71ff-9b3c-6431dacf8ea4'),
(600, 2, '019c1ac3-da91-732a-a9e7-58ad30ed4077', '2026-02-02 19:52:49', '019c1aa8-40f4-7dca-b3fa-128a2021752e'),
(601, 1, '019c1aca-c232-7620-8aa8-5ac00a3d0ecc', '2026-02-02 20:00:21', '019c1ac9-db7c-7e59-a681-96f221e6c1af'),
(602, 2, '019c1aef-e9e2-723c-98c3-354d9df17de4', '2026-02-02 20:40:56', '019c1aef-e0ac-70e6-9b2a-c7777501214c'),
(603, 1, '019c1af0-819a-710b-92f4-300548481b65', '2026-02-02 20:41:35', '019c1af0-782d-7a1c-acf1-8e02a4b1cd89'),
(604, 2, '019c1af1-7e99-74bc-a02a-99e41a50fc52', '2026-02-02 20:42:40', '019c1af1-77a8-7822-bd9c-f8374944c2aa'),
(605, 1, '019c1af4-9de4-7cc2-92f3-f9571cb1119b', '2026-02-02 20:46:04', '019c1af4-9301-7ebb-8cf4-a7575067bfac'),
(607, 2, '019c1b00-d059-7ed8-bd85-7c885d0abafd', '2026-02-02 20:59:24', '019c1af6-493a-7222-90fa-4c53d428206b'),
(608, 1, '019c1b10-8866-7e59-9b91-a94932d7db9e', '2026-02-02 21:16:34', '019c1b10-6dc8-785f-8d22-b02f8c388854'),
(609, 2, '019c1b12-1c26-7ba4-a7d6-e03be45570dd', '2026-02-02 21:18:17', '019c1b12-1385-7cbc-afdb-c45f37b99f6b'),
(611, 1, '019c1b1c-463d-7da5-9739-733ee98a5b22', '2026-02-02 21:29:23', '019c1b13-fba0-7ad6-a77e-fa5d55b6c8cb'),
(613, 2, '019c1b57-b36c-70ba-ae07-94cd17d124d8', '2026-02-02 22:34:18', '019c1b3c-344d-7989-843d-5fc5c82f44ad'),
(616, 2, '019c1d4a-9b9c-703a-8f25-c94d965aab7c', '2026-02-03 07:39:14', '019c1d4a-9644-7d44-a020-1e1df4a04008'),
(618, 1, '019c1d5a-0378-76c9-89b0-014f006a1314', '2026-02-03 07:56:04', '019c1d5a-0068-7671-a7ab-3311f4fd794a'),
(619, 2, '019c1d5f-a000-748d-981a-b2019fc7beee', '2026-02-03 08:02:12', '019c1d57-c06e-7ead-a06b-ec61b119eec2'),
(620, 3, '019c1fbe-03ad-7dfb-ae1a-b1201a15ce2a', '2026-02-03 19:04:32', '019c1ca0-8549-70aa-b703-c5d67bce007a'),
(622, 2, '019c2017-5ba5-75c6-8795-060187364648', '2026-02-03 20:42:07', '019c1ffb-e133-7a1e-b9a5-566b6cdbe465'),
(623, 2, '019c2021-d91f-7733-83f2-96d5660ae5ea', '2026-02-03 20:53:35', '019c2021-6765-737e-8862-bf8c18b578d3'),
(624, 2, '019c208e-fd6f-7a32-8287-07f4c667f72a', '2026-02-03 22:52:47', '019c208e-b459-7901-b8af-59771e5d35f0'),
(625, 2, '019c2099-5a03-7c89-b73b-23e4ace681ec', '2026-02-03 23:04:06', '019c2099-53fd-79c5-b315-b0c200c403a3'),
(626, 1, '019c20a4-a282-7404-84fd-e191367daa70', '2026-02-03 23:16:26', '019c20a4-9a0c-7fec-bfe9-7fea71c9b0b9'),
(627, 2, '019c20ad-167c-7f7b-bbf1-4447be465c86', '2026-02-03 23:25:40', '019c20ad-0d66-7d78-ae03-24bf4225cfa8'),
(628, 2, '019c20bb-0e9b-7bf6-ac4c-2d048e4c1d03', '2026-02-03 23:40:55', '019c20bb-07f1-75a2-88a0-e539710cd1ac'),
(630, 1, '019c20c7-f12a-78c0-b68f-523d76980cb6', '2026-02-03 23:55:00', '019c20c6-2861-73ab-9827-d3ef3e62aa56'),
(631, 2, '019c20ca-ec30-75ac-82ea-a260ef56beaf', '2026-02-03 23:58:15', '019c20ca-e5d9-7cd4-81d8-eab216e73eb8'),
(632, 1, '019c20cb-6490-73e6-91b5-f2a5addff9ec', '2026-02-03 23:58:46', '019c20cb-5b3f-7744-b486-bc7260055916'),
(633, 2, '019c20dc-5bbf-7b86-8c1d-003192ad45b9', '2026-02-04 00:17:18', '019c20dc-5145-7d54-b1a3-c3206e094e3d'),
(635, 1, '019c20e4-3d87-77eb-a9d1-0c275c398235', '2026-02-04 00:25:54', '019c20dd-df10-7018-bd1f-b0793fb1ec43'),
(636, 2, '019c2101-5f03-72c3-a299-006f331e8032', '2026-02-04 00:57:43', '019c2100-0dc9-7bb0-b2a7-cd5f127eeddc'),
(637, 1, '019c2102-a55f-70ef-9bab-cd214e38dd39', '2026-02-04 00:59:07', '019c2102-9d22-7937-91c5-72840a23750f'),
(639, 2, '019c2337-8d81-71e6-85d2-50eff9101213', '2026-02-04 11:16:09', '019c231c-0c3a-7b59-8497-a818658b430f'),
(640, 2, '019c233a-e4e4-7062-830f-b3d36bc7770f', '2026-02-04 11:19:48', '019c233a-d9cf-7b3d-a7c4-3925ccaa7554'),
(641, 1, '019c233c-7c89-75a0-bfbb-c37bf6664c16', '2026-02-04 11:21:32', '019c233c-73f1-7b18-ae33-d25e6c11f647'),
(642, 1, '019c2351-13b9-7471-93fd-683bed1f0f1a', '2026-02-04 11:44:01', '019c2350-7e48-7beb-8abe-333347232861'),
(643, 1, '019c235f-881e-7f0a-a42b-66587691c8c0', '2026-02-04 11:59:49', '019c235f-7d33-7d75-8189-d45f516623a2'),
(647, 3, '019c249e-f439-70ac-b370-93b160e8fdb3', '2026-02-04 17:48:42', '019c2452-73ea-7972-8e4b-62d0b2cee764'),
(650, 1, '019c24da-bce1-7b02-ae9b-0c743a5c1c68', '2026-02-04 18:54:00', '019c246e-1dff-7f94-96fd-8dc860997da3'),
(653, 2, '019c2502-9187-7af0-8ffe-af87bfcdc04a', '2026-02-04 19:37:31', '019c24cb-e620-7c12-a6d4-287202c38f68'),
(654, 1, '019c2544-1c5e-744e-8f88-e3a9b14919d0', '2026-02-04 20:49:06', '019c24f9-e5b6-7a69-b2c2-3d52f202dbca'),
(655, 2, '019c27a1-64a0-735e-b227-60dd7a8d147f', '2026-02-05 07:50:14', '019c27a1-5c96-731f-acc8-35c486107262'),
(657, 2, '019c27bb-d29f-72a8-925c-7cb2fabef954', '2026-02-05 08:19:06', '019c27a8-3065-7748-abc6-e5d5117606fd'),
(658, 2, '019c27cd-198c-7e48-9741-29e21ab18ac8', '2026-02-05 08:37:58', '019c27cb-2650-71c1-8297-489201fee174'),
(659, 2, '019c27f4-ea80-7f8e-ade8-aa179d70c23b', '2026-02-05 09:21:28', '019c27f4-4c95-7564-bbd0-32ed916a5876'),
(660, 2, '019c2807-00a2-7cfb-a6a8-81bca4c8ccad', '2026-02-05 09:41:13', '019c2805-1eb4-7663-88b9-a1601563a0c7'),
(661, 1, '019c280b-279a-7a22-9b71-d61d79941a15', '2026-02-05 09:45:45', '019c280b-14ae-751b-843b-adffa37d2300'),
(662, 2, '019c280c-0426-7a58-bf11-8f8897bb8a93', '2026-02-05 09:46:42', '019c280b-f921-76a8-a239-4e27564550aa'),
(663, 1, '019c280c-c126-7402-8920-f9de77ab2119', '2026-02-05 09:47:30', '019c280c-7219-7c6b-b0aa-c62d52c3d973'),
(664, 1, '019c2827-ccbc-7a63-b3dd-cf66b2759080', '2026-02-05 10:17:02', '019c2826-6a08-7961-9720-e73d593ad791'),
(665, 2, '019c2829-2925-71c8-9ba4-53737179d4e0', '2026-02-05 10:18:32', '019c2829-1dc3-7b34-99d5-fde5eda25df5'),
(666, 1, '019c282c-2d08-7730-88cd-8291f99f3ef8', '2026-02-05 10:21:49', '019c282c-24ec-7ec8-8835-35fa84941168'),
(672, 1, '019c287c-759f-797b-b00e-2a30fa235b5b', '2026-02-05 11:49:31', '019c287b-8fee-7cca-ac5b-d96631c7bc25'),
(673, 2, '019c287d-12bf-7f1b-9160-63886b59f472', '2026-02-05 11:50:11', '019c287d-0b00-7f6e-a60a-d65015c45c53'),
(674, 2, '019c289e-7d9b-7edd-8f48-6fe1d669ac8c', '2026-02-05 12:26:41', '019c2898-a990-7380-b3ae-dd0f40815f3d'),
(675, 1, '019c289f-dec2-7361-ab25-697458a81541', '2026-02-05 12:28:11', '019c289f-2d37-7723-9923-a3b3e31b6f65'),
(676, 1, '019c28a0-f3d4-7921-82ba-ab35b6b7612f', '2026-02-05 12:29:22', '019c28a0-ea89-7e20-a65d-b6193260b572'),
(677, 1, '019c28a1-93c0-70da-9ef3-a233aa884a9a', '2026-02-05 12:30:03', '019c28a1-89c9-70cf-b062-bea4568fe12f'),
(680, 2, '019c28b4-4531-703b-a51c-205b52446940', '2026-02-05 12:50:28', '019c28b3-b6c8-761b-9cbf-329e39c60bd3'),
(681, 1, '019c28b6-76ec-7ba4-9402-f36bb6bda596', '2026-02-05 12:52:52', '019c28b6-6c63-711d-89eb-0058a582b37d'),
(682, 2, '019c28ba-468e-7834-90d2-180e4ca8c1d2', '2026-02-05 12:57:02', '019c28b8-c885-7c59-a97c-408cbfaa221c'),
(683, 1, '019c28bc-378f-7c22-827d-e16dd90d6d00', '2026-02-05 12:59:09', '019c28bc-2fcb-751b-9670-c23e09f578dc'),
(684, 2, '019c28c3-7664-7e4b-9e09-0d78d3e168d4', '2026-02-05 13:07:04', '019c28c3-685f-78bb-8d36-7f3dee36927f'),
(686, 2, '019c28c8-3b71-7355-af85-0ee0d3b6da1d', '2026-02-05 13:12:17', 'debug-device-uid-123'),
(687, 1, '019c28c8-4307-7037-bfab-3e843c36f393', '2026-02-05 13:12:18', 'debug-device-uid-123'),
(688, 1, '019c28df-cd70-7168-87cc-898fe32091d4', '2026-02-05 13:38:01', '019c28c4-f571-76fd-bc9d-a5e2ed654f47'),
(689, 2, '019c29f6-3c6e-7565-b8be-7949bcc0c3c7', '2026-02-05 18:42:09', '019c29f5-bd62-75d3-9e59-3e76c2bf72c4'),
(690, 2, '019c29f6-c480-74f1-aab0-04f515f9f2fb', '2026-02-05 18:42:43', '019c29f6-ba7a-7751-9d67-b4b46c44e4c4'),
(691, 2, '019c2a02-f03f-7be7-bf6a-63e1b895684d', '2026-02-05 18:56:01', '019c2a02-72e3-79af-bcd6-72c52e329a8e'),
(692, 2, '019c2a03-9825-72be-a08d-d971f2eca3a3', '2026-02-05 18:56:44', '019c2a03-7226-7b20-bce5-680cc693f471'),
(693, 2, '019c2a17-3ab4-7fdb-8052-405de17194cb', '2026-02-05 19:18:11', '019c2a16-d7a7-7225-91a0-b902c2eaeccf'),
(694, 2, '019c2a17-9f1c-77ab-a5a3-2fd358d901c3', '2026-02-05 19:18:37', '019c2a17-951a-737e-8dbd-ae3233670629'),
(695, 2, '019c2a17-fc17-71e6-9cd8-3274f633271e', '2026-02-05 19:19:00', '019c2a17-eed7-70fe-b9b2-d7125300406f'),
(696, 2, '019c2a20-1142-7a44-972a-4bb168c2a5a7', '2026-02-05 19:27:50', '019c2a1f-f7cc-763a-adf9-181f0ba9ef2d'),
(697, 2, '019c2a26-839d-7512-9889-e3b690b6918c', '2026-02-05 19:34:53', '019c2a26-7929-7b65-b009-6f751c800a7c'),
(698, 2, '019c2a2c-9865-7324-9f03-dbac0d1e10da', '2026-02-05 19:41:31', '019c2a2c-8b1e-78c3-b05d-7291fb28c7cf'),
(699, 2, '019c2a3f-4d63-702e-a31b-cd1cdf3b8f42', '2026-02-05 20:01:57', '019c2a3d-1e0a-794b-b6d9-da327d4b491f'),
(700, 2, '019c2a4b-95be-7a9f-833d-a8d78aea46c6', '2026-02-05 20:15:22', '019c2a4a-47d1-7f2f-b073-8f61a1e736c9'),
(701, 2, '019c2c44-e3ff-7195-b84d-31a9790937b9', '2026-02-06 05:27:18', '019c2c40-faad-79bb-9ee4-5959cbd52ab3'),
(702, 2, '019c2c4f-cef1-7f80-a4c5-f29c720f89d2', '2026-02-06 05:39:13', '019c2c4f-055b-7a03-b991-600e4f1d03d4'),
(703, 2, '019c2c53-2e16-7262-a7d0-9b4d438394a0', '2026-02-06 05:42:54', '019c2c53-21be-74ec-9afd-0bdcf1bd55d8'),
(704, 2, '019c2c77-a228-71a7-87e3-90996f71ae4b', '2026-02-06 06:22:43', '019c2c77-1096-78e9-a70c-7590ae595b20'),
(705, 2, '019c2c7f-8f86-7462-8979-b711d82cf593', '2026-02-06 06:31:23', '019c2c7d-3f26-7bb9-b67a-c39859fc9816'),
(706, 1, '019c2c86-cfd8-7da2-a683-d7b4cbdcf13e', '2026-02-06 06:39:18', '019c2968-e5e6-772b-bbd6-b9156215ca95'),
(707, 2, '019c2cbc-f174-7d15-96d8-d0ac312a57db', '2026-02-06 07:38:26', '019c2cbc-adac-7948-b625-974210ce53a0'),
(708, 1, '019c2cd5-5047-7331-8866-331f3e4cb23b', '2026-02-06 08:05:03', '019c2cd5-1cf2-73f8-b4ca-c6ed639500b8'),
(709, 2, '019c2d03-3989-7a2d-bdc4-b51335e12b55', '2026-02-06 08:55:12', '019c2d03-32f2-719f-b4b1-7f770a7e8e77'),
(710, 2, '019c2d8d-a298-7670-846e-676f3157134a', '2026-02-06 11:26:22', '019c2d8a-6b17-7514-acfb-0a4641d42812'),
(711, 1, '019c2e89-186c-7530-85a2-d9282f53e273', '2026-02-06 16:01:02', '019c2e88-dfcb-70c0-9438-9bea71454373'),
(712, 2, '019c2ea4-71d7-7c52-98ca-804824243f90', '2026-02-06 16:30:54', '019c2ea2-f817-7d16-ad37-87502669825a'),
(713, 2, '019c2ed7-9d47-766e-a0f8-caea55fc5f23', '2026-02-06 17:26:48', '019c2ed7-2430-7be4-beb8-cc91bfc67dae'),
(714, 1, '019c2f40-52f2-760c-add4-0c1839dbd031', '2026-02-06 19:21:10', '019c2f40-2af7-7e80-97b4-c46fcbb64ca7'),
(715, 3, '019c2fb4-650f-794d-81e2-ed450678e0fa', '2026-02-06 21:27:57', '019c29b3-7fbb-72a0-8998-e20378e5540c'),
(716, 3, '019c2fb4-c6e1-7be6-a22c-a57be2885d27', '2026-02-06 21:28:22', '019c2fb4-be00-7d39-b65c-1e3eb874332c');

-- --------------------------------------------------------

--
-- Table structure for table `user_bank_account`
--

CREATE TABLE `user_bank_account` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `account_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_recipient_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack_subaccount_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_bank_account`
--

INSERT INTO `user_bank_account` (`id`, `user_id`, `account_number`, `account_name`, `bank_code`, `currency`, `gateway_recipient_code`, `bank_name`, `paystack_subaccount_code`) VALUES
(1, 1, '3065486709', 'IMRAN ABDULHAKEEM', '011', 'NGN', 'RCP_5r1eivjoho88jnr', 'First Bank of Nigeria', NULL),
(2, 2, '8033496917', 'ABDULHAKEEM  IMRAN', '999992', 'NGN', 'RCP_z25sov8dmcth0hk', 'OPay Digital Services Limited (OPay)', NULL),
(3, 3, '3135358316', 'IMRANA ABIDU', '011', 'NGN', 'RCP_ne8xjvx6yt28emy', 'First Bank of Nigeria', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_invitation`
--

CREATE TABLE `user_invitation` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `invitation_sent` tinyint(1) NOT NULL,
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_invitation`
--

INSERT INTO `user_invitation` (`id`, `user_id`, `uid`, `phone_number`, `created_at`, `invitation_sent`, `last_modified_at`) VALUES
(1, 3, '019bb8c4-9cd9-7425-b15e-e520b4defdb3', '8033496917', '2026-01-13 19:10:51', 0, '2026-01-13 19:10:51');

-- --------------------------------------------------------

--
-- Table structure for table `user_wallet`
--

CREATE TABLE `user_wallet` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `balance` decimal(10,0) NOT NULL,
  `transaction_pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_wallet`
--

INSERT INTO `user_wallet` (`id`, `user_id`, `balance`, `transaction_pin`) VALUES
(1, 1, 2700000, '$argon2id$v=19$m=65536,t=4,p=1$195mm0fGAOlOPR1q5It78w$+iC3Z1NuCHkVsR8b3GO4PUc7O9ib/JyRLbuiAe8VR6c'),
(2, 2, 5900000, '$argon2id$v=19$m=65536,t=4,p=1$Z5NEauLtHpRzN9Z9XpfbsQ$p2ojbcutTVkeNz5zYCHvXOj6wJOA57703D2xhWBaYH4'),
(3, 3, 300000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallet_funding`
--

CREATE TABLE `wallet_funding` (
  `id` int NOT NULL,
  `wallet_id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `narration` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_message` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_reference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_response_object` json DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `transaction_fee` decimal(10,0) NOT NULL,
  `transaction_channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_funding`
--

INSERT INTO `wallet_funding` (`id`, `wallet_id`, `uid`, `reference`, `amount`, `narration`, `status`, `status_message`, `gateway_reference`, `gateway_response_object`, `created_at`, `last_modified_at`, `transaction_fee`, `transaction_channel`) VALUES
(39, 2, '019c147d-f756-741b-b357-69fdcd6990ed', 'DAYN-FND-8177568671-31012026-143845', 5000000, 'Wallet Funding for DAYN-FND-8177568671-31012026-143845', 'success', 'Transaction Successful', NULL, '{\"fee\": 0.0, \"vat\": 0.0, \"currency\": \"NGN\", \"amountPaid\": 5075000.0, \"stampDutyFee\": 0.0, \"amountSpecified\": 5075000.0, \"paymentStartDate\": \"2026-01-31T14:40:43.000+00:00\", \"transactionStatus\": \"successful\", \"transactionReference\": \"MNFY|68|20260131154043|000334\", \"paymentCompletionDate\": \"2026-01-31T14:40:43.000+00:00\", \"providerTransactionId\": \"MNFY|68|20260131154043|000334\"}', '2026-01-31 14:38:45', '2026-01-31 14:38:45', 75000, 'bank_transfer'),
(40, 2, '019c232f-e374-73b4-b3d5-64c2ab0b3a28', 'DAYN-FND-8963642204-03022026-110746', 100000, 'Wallet Funding for DAYN-FND-8963642204-03022026-110746', 'processing', 'Transaction in Processing', NULL, NULL, '2026-02-03 11:07:46', '2026-02-03 11:07:46', 1500, 'bank_transfer'),
(41, 2, '019c233b-1a6b-765b-82aa-3b3d2f0a36dd', 'DAYN-FND-6710000574-03022026-112001', 100000, 'Wallet Funding for DAYN-FND-6710000574-03022026-112001', 'success', 'Transaction Successful', NULL, '{\"fee\": 0.0, \"vat\": 0.0, \"currency\": \"NGN\", \"amountPaid\": 101500.0, \"stampDutyFee\": 0.0, \"amountSpecified\": 101500.0, \"paymentStartDate\": \"2026-02-03T11:20:18.000+00:00\", \"transactionStatus\": \"successful\", \"transactionReference\": \"MNFY|16|20260203122017|000175\", \"paymentCompletionDate\": \"2026-02-03T11:20:18.000+00:00\", \"providerTransactionId\": \"MNFY|16|20260203122017|000175\"}', '2026-02-03 11:20:01', '2026-02-03 11:20:01', 1500, 'bank_transfer');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_payout`
--

CREATE TABLE `wallet_payout` (
  `id` int NOT NULL,
  `wallet_id` int NOT NULL,
  `receiving_bank_account_id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `narration` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_response_object` json DEFAULT NULL,
  `status_message` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `last_modified_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `transaction_fee` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transfer`
--

CREATE TABLE `wallet_transfer` (
  `id` int NOT NULL,
  `sender_wallet_id` int NOT NULL,
  `receiver_wallet_id` int NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_transfer`
--

INSERT INTO `wallet_transfer` (`id`, `sender_wallet_id`, `receiver_wallet_id`, `uid`, `amount`, `created_at`, `reference`) VALUES
(1, 2, 1, '019bf9d3-b1a4-72ba-9ba6-0797a9f107b0', 100000, '2026-01-26 10:22:39', 'DAYN-TRF-2063331541-26012026-102239'),
(2, 2, 1, '019bf9d4-3776-7eaa-aafb-afa113eddea7', 50000, '2026-01-26 10:23:13', 'DAYN-TRF-4335631533-26012026-102313'),
(3, 1, 2, '019c0345-4147-724e-8592-e934601f7aca', 100000, '2026-01-28 06:23:16', 'DAYN-TRF-3722269850-28012026-062316'),
(4, 1, 2, '019c0504-1bd2-76a1-8bb1-fd739f880ec5', 100000, '2026-01-28 14:31:21', 'DAYN-TRF-7478600387-28012026-143121'),
(5, 1, 2, '019c0505-f285-7642-8c69-ebe5ca8e51bd', 50000, '2026-01-28 14:33:21', 'DAYN-TRF-8689648578-28012026-143321'),
(6, 1, 2, '019c0570-0cae-7a0f-8fbb-785f9793fe08', 100000, '2026-01-28 16:29:15', 'DAYN-TRF-4464094576-28012026-162915'),
(7, 1, 2, '019c0570-6685-7d3b-a143-fa9af3399a71', 100000, '2026-01-28 16:29:38', 'DAYN-TRF-5797344485-28012026-162938'),
(8, 1, 2, '019c068b-77c7-720a-8cdf-1d8324103c07', 100000, '2026-01-28 21:38:49', 'DAYN-TRF-3852011618-28012026-213849'),
(9, 1, 2, '019c068c-8c01-73d4-8304-38ed0708a319', 100000, '2026-01-28 21:40:00', 'DAYN-TRF-1968358546-28012026-214000'),
(10, 2, 1, '019c068e-94d6-7dc6-a7e3-5f209739854b', 100000, '2026-01-28 21:42:13', 'DAYN-TRF-8193621131-28012026-214213'),
(11, 1, 2, '019c068f-d386-78ab-b3d9-89970e8f9423', 100000, '2026-01-28 21:43:35', 'DAYN-TRF-9802464175-28012026-214335'),
(12, 1, 2, '019c082b-0280-78d2-9fb9-3ce94a89e207', 150000, '2026-01-29 05:12:42', 'DAYN-TRF-3739583961-29012026-051242'),
(13, 1, 2, '019c0837-5f19-7c9e-8931-9d35d3f89b39', 140000, '2026-01-29 05:26:12', 'DAYN-TRF-9369078737-29012026-052612'),
(14, 2, 1, '019c0843-603f-79fa-bf44-00fa7967ca52', 135000, '2026-01-29 05:39:19', 'DAYN-TRF-0314041309-29012026-053919'),
(15, 2, 1, '019c0912-4ea7-7925-be2b-ad63c86cd24b', 165000, '2026-01-29 09:25:20', 'DAYN-TRF-3809111653-29012026-092520'),
(16, 2, 1, '019c09f1-1449-7ba0-b8fb-fb6cdcff3724', 100000, '2026-01-29 13:28:40', 'DAYN-TRF-4194557618-29012026-132840'),
(17, 2, 1, '019c0a22-cd99-7a50-8f70-2058a3f12589', 190000, '2026-01-29 14:22:58', 'DAYN-TRF-6267652216-29012026-142258'),
(18, 1, 2, '019c0a31-a702-7d0d-907d-0513978bb72b', 50000, '2026-01-29 14:39:12', 'DAYN-TRF-7456432324-29012026-143912'),
(19, 2, 1, '019c0a36-ac8c-7266-a2a8-8fb7488a3b2b', 50000, '2026-01-29 14:44:41', 'DAYN-TRF-7034561246-29012026-144441'),
(20, 2, 1, '019c0a41-25d1-7b57-bce7-1d4eb1ec001b', 160000, '2026-01-29 14:56:07', 'DAYN-TRF-4667763457-29012026-145607'),
(21, 2, 1, '019c0bff-8c2d-78eb-b9e4-41de1362e3bf', 100000, '2026-01-29 23:03:42', 'DAYN-TRF-7823271663-29012026-230342'),
(22, 1, 2, '019c0c06-a21d-719c-9073-a58bff3af44f', 60000, '2026-01-29 23:11:27', 'DAYN-TRF-4128118712-29012026-231127'),
(23, 1, 2, '019c0c0f-869a-7f25-b4b3-50c0ebdbf042', 50000, '2026-01-29 23:21:10', 'DAYN-TRF-4338042449-29012026-232110'),
(24, 2, 1, '019c0e72-1d95-7cd8-9433-154282992b3f', 200000, '2026-01-30 10:28:05', 'DAYN-TRF-3861225257-30012026-102805'),
(25, 2, 1, '019c0e72-b1d9-764d-ad4c-4e97dcc44a26', 50000, '2026-01-30 10:28:43', 'DAYN-TRF-2781410742-30012026-102843'),
(26, 1, 2, '019c0ea6-972c-7cbd-be73-fb93d3ae572d', 50000, '2026-01-30 11:25:24', 'DAYN-TRF-4615094232-30012026-112524'),
(27, 2, 1, '019c0f39-519f-764c-a995-4bbe37bc3536', 50000, '2026-01-30 14:05:40', 'DAYN-TRF-4329609016-30012026-140540'),
(28, 2, 1, '019c0f43-c02b-779c-b036-5f473984b12f', 100000, '2026-01-30 14:17:04', 'DAYN-TRF-7451118021-30012026-141704'),
(29, 2, 1, '019c0f45-02e9-7418-ab37-41c475d6d09c', 50000, '2026-01-30 14:18:26', 'DAYN-TRF-6864657459-30012026-141826'),
(30, 2, 1, '019c0f45-390c-7950-b58c-d4c2333c01d7', 50000, '2026-01-30 14:18:40', 'DAYN-TRF-8698349056-30012026-141840'),
(31, 2, 1, '019c0f60-0826-7f41-90ec-ee76b6aa56ae', 50000, '2026-01-30 14:47:57', 'DAYN-TRF-5977449824-30012026-144757'),
(32, 2, 1, '019c0f60-fbd5-7c86-9231-1e555ba9a101', 50000, '2026-01-30 14:49:00', 'DAYN-TRF-7016451588-30012026-144900'),
(33, 2, 1, '019c0f64-a0d9-730b-ac6c-fd1d0ce09d85', 50000, '2026-01-30 14:52:58', 'DAYN-TRF-5214305657-30012026-145258'),
(34, 2, 1, '019c0f69-3bd3-74cc-97e5-a5461f9092d3', 50000, '2026-01-30 14:58:00', 'DAYN-TRF-9005339329-30012026-145800'),
(35, 1, 2, '019c0fcd-94cc-7ca4-997b-8e566ea3b540', 100000, '2026-01-30 16:47:37', 'DAYN-TRF-0757246434-30012026-164737'),
(36, 2, 1, '019c0feb-c072-7ad7-ad5c-e3322df9a8a1', 150000, '2026-01-30 17:20:34', 'DAYN-TRF-0041592418-30012026-172034'),
(37, 1, 2, '019c1021-71fa-7e4b-8ee0-4feb72260376', 150000, '2026-01-30 18:19:13', 'DAYN-TRF-5286017694-30012026-181913'),
(38, 1, 2, '019c102e-2fe5-78c8-b0f9-6a6dcfe2b296', 50000, '2026-01-30 18:33:08', 'DAYN-TRF-1080682105-30012026-183308'),
(39, 2, 1, '019c12ff-9ecb-73cd-a8dd-d21604f48aca', 50000, '2026-01-31 07:41:08', 'DAYN-TRF-9868018405-31012026-074108'),
(40, 2, 1, '019c137a-d755-784b-bcb5-64be0cab3ea0', 50000, '2026-01-31 09:55:43', 'DAYN-TRF-7946773142-31012026-095543'),
(41, 1, 2, '019c13a3-cf0a-758d-913e-d407c52eaaa4', 100000, '2026-01-31 10:40:28', 'DAYN-TRF-6655092133-31012026-104028'),
(42, 1, 2, '019c13a4-b400-7793-8e55-28e49bbc3b2c', 150000, '2026-01-31 10:41:27', 'DAYN-TRF-4119275784-31012026-104127'),
(43, 1, 2, '019c13b8-9a01-7f12-b9ca-3ad146961ba2', 50000, '2026-01-31 11:03:11', 'DAYN-TRF-3153859841-31012026-110311'),
(44, 1, 2, '019c13bb-fc14-7520-9c84-e1124450ae4f', 20000, '2026-01-31 11:06:52', 'DAYN-TRF-3973341569-31012026-110652'),
(45, 1, 2, '019c13d8-2e54-7c34-a0cb-3c4716caaaf9', 80000, '2026-01-31 11:37:40', 'DAYN-TRF-6508979659-31012026-113740'),
(46, 2, 1, '019c142b-5c03-782d-a0f6-3fac1db54b3d', 1000000, '2026-01-31 13:08:31', 'DAYN-TRF-2782406013-31012026-130831'),
(47, 2, 1, '019c147a-51c1-77b7-9929-6fbd5b9c90e7', 50000, '2026-01-31 14:34:46', 'DAYN-TRF-5368902851-31012026-143446'),
(48, 2, 1, '019c15fb-7de7-79b5-8202-105fd4da0c09', 50000, '2026-01-31 21:35:29', 'DAYN-TRF-5382242024-31012026-213529'),
(49, 2, 1, '019c15fc-4af4-7dd4-9da0-61dad50e4e0a', 100000, '2026-01-31 21:36:21', 'DAYN-TRF-1647600697-31012026-213621'),
(50, 1, 2, '019c1635-062f-7d7b-bdd0-cbe3aaec7f2d', 100000, '2026-01-31 22:38:19', 'DAYN-TRF-3795895217-31012026-223819'),
(51, 1, 2, '019c1635-2b28-757a-8b35-33f55a0cfd05', 100000, '2026-01-31 22:38:29', 'DAYN-TRF-0952300813-31012026-223829'),
(52, 2, 1, '019c1684-fcd2-78dc-98bd-3aeca56347d0', 50000, '2026-02-01 00:05:40', 'DAYN-TRF-6904746121-01022026-000540'),
(53, 2, 1, '019c1686-ea7a-77e1-a0e4-e402cc367d3d', 100000, '2026-02-01 00:07:46', 'DAYN-TRF-9971291754-01022026-000746'),
(54, 2, 1, '019c19d4-4404-7506-b105-812b3b52348a', 100000, '2026-02-01 15:31:07', 'DAYN-TRF-4274452269-01022026-153107'),
(55, 1, 2, '019c1ae9-e4c4-7adb-9335-9f4eb221d5e9', 50000, '2026-02-01 20:34:22', 'DAYN-TRF-2981978339-01022026-203422'),
(56, 1, 2, '019c1aef-701b-7901-9c8e-7ee3d76a4535', 50000, '2026-02-01 20:40:25', 'DAYN-TRF-1068864544-01022026-204025'),
(57, 1, 2, '019c1af0-e427-7fb7-a63c-5d9f38ff09eb', 50000, '2026-02-01 20:42:00', 'DAYN-TRF-1875620573-01022026-204200'),
(58, 2, 1, '019c1b12-db0a-7b13-8015-181a8e691cc8', 50000, '2026-02-01 21:19:06', 'DAYN-TRF-3322726849-01022026-211906'),
(59, 3, 1, '019c1ca0-b72c-7f45-aad6-5bcd066ff939', 100000, '2026-02-02 04:33:40', 'DAYN-TRF-9695363802-02022026-043340'),
(60, 1, 2, '019c1d5f-4094-7d58-ae4b-623300e64eca', 1000000, '2026-02-02 08:01:47', 'DAYN-TRF-2044771971-02022026-080147'),
(61, 2, 1, '019c233b-db69-717e-af47-57f5b042a916', 100000, '2026-02-03 11:20:51', 'DAYN-TRF-1841948882-03022026-112051'),
(62, 3, 1, '019c2454-7382-7a2d-a1fc-f30a93e70580', 100000, '2026-02-03 16:27:20', 'DAYN-TRF-7370008126-03022026-162720');

-- --------------------------------------------------------

--
-- Table structure for table `witness_binding`
--

CREATE TABLE `witness_binding` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `witness_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `witness_binding`
--

INSERT INTO `witness_binding` (`id`, `user_id`, `witness_id`) VALUES
(1, 1, 1),
(2, 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_number_sequence`
--
ALTER TABLE `account_number_sequence`
  ADD PRIMARY KEY (`prefix`);

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_ADMIN_USERNAME` (`username`);

--
-- Indexes for table `admin_user_access_token`
--
ALTER TABLE `admin_user_access_token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8EED56C2A76ED395` (`user_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1899612A10DAF24A` (`actor_id`);

--
-- Indexes for table `beneficiary`
--
ALTER TABLE `beneficiary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7ABF446AA76ED395` (`user_id`);

--
-- Indexes for table `debt_collection`
--
ALTER TABLE `debt_collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AA8D331BB043EC6B` (`debtor_id`),
  ADD KEY `IDX_AA8D331BDF91AC92` (`creditor_id`),
  ADD KEY `IDX_AA8D331BB03A8386` (`created_by_id`);

--
-- Indexes for table `debt_collection_payment`
--
ALTER TABLE `debt_collection_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_398377F3115013A6` (`debt_collection_id`),
  ADD KEY `FK_CREATED_BY_USER` (`created_by_id`),
  ADD KEY `idx_visible` (`is_acknowledged`,`status`,`channel`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `forgot_password_token`
--
ALTER TABLE `forgot_password_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_transactional_account`
--
ALTER TABLE `payment_transactional_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_CLIENT_USERNAME` (`username`),
  ADD UNIQUE KEY `UNIQ_RESERVED_ACCOUNT_REF` (`reserved_account_reference`);

--
-- Indexes for table `user_access_token`
--
ALTER TABLE `user_access_token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_366EA16AA76ED395` (`user_id`);

--
-- Indexes for table `user_bank_account`
--
ALTER TABLE `user_bank_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D36E4208A76ED395` (`user_id`);

--
-- Indexes for table `user_invitation`
--
ALTER TABLE `user_invitation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_567AA74EA76ED395` (`user_id`);

--
-- Indexes for table `user_wallet`
--
ALTER TABLE `user_wallet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_193A8922A76ED395` (`user_id`);

--
-- Indexes for table `wallet_funding`
--
ALTER TABLE `wallet_funding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A20CF4C0712520F3` (`wallet_id`);

--
-- Indexes for table `wallet_payout`
--
ALTER TABLE `wallet_payout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FE8E3646712520F3` (`wallet_id`),
  ADD KEY `IDX_FE8E36466B13A89B` (`receiving_bank_account_id`);

--
-- Indexes for table `wallet_transfer`
--
ALTER TABLE `wallet_transfer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B49117B42B2CC405` (`sender_wallet_id`),
  ADD KEY `IDX_B49117B4BCCA839B` (`receiver_wallet_id`);

--
-- Indexes for table `witness_binding`
--
ALTER TABLE `witness_binding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_64C4FDFCA76ED395` (`user_id`),
  ADD KEY `IDX_64C4FDFCF28D7E1C` (`witness_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_user_access_token`
--
ALTER TABLE `admin_user_access_token`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beneficiary`
--
ALTER TABLE `beneficiary`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `debt_collection`
--
ALTER TABLE `debt_collection`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `debt_collection_payment`
--
ALTER TABLE `debt_collection_payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `forgot_password_token`
--
ALTER TABLE `forgot_password_token`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_transactional_account`
--
ALTER TABLE `payment_transactional_account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_access_token`
--
ALTER TABLE `user_access_token`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=717;

--
-- AUTO_INCREMENT for table `user_bank_account`
--
ALTER TABLE `user_bank_account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_invitation`
--
ALTER TABLE `user_invitation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_wallet`
--
ALTER TABLE `user_wallet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wallet_funding`
--
ALTER TABLE `wallet_funding`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `wallet_payout`
--
ALTER TABLE `wallet_payout`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_transfer`
--
ALTER TABLE `wallet_transfer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `witness_binding`
--
ALTER TABLE `witness_binding`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_user_access_token`
--
ALTER TABLE `admin_user_access_token`
  ADD CONSTRAINT `FK_8EED56C2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `admin_user` (`id`);

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `FK_1899612A10DAF24A` FOREIGN KEY (`actor_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `beneficiary`
--
ALTER TABLE `beneficiary`
  ADD CONSTRAINT `FK_7ABF446AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `debt_collection`
--
ALTER TABLE `debt_collection`
  ADD CONSTRAINT `FK_AA8D331BB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_AA8D331BB043EC6B` FOREIGN KEY (`debtor_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_AA8D331BDF91AC92` FOREIGN KEY (`creditor_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `debt_collection_payment`
--
ALTER TABLE `debt_collection_payment`
  ADD CONSTRAINT `FK_398377F3115013A6` FOREIGN KEY (`debt_collection_id`) REFERENCES `debt_collection` (`id`),
  ADD CONSTRAINT `FK_CREATED_BY_USER` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_access_token`
--
ALTER TABLE `user_access_token`
  ADD CONSTRAINT `FK_366EA16AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_bank_account`
--
ALTER TABLE `user_bank_account`
  ADD CONSTRAINT `FK_D36E4208A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_invitation`
--
ALTER TABLE `user_invitation`
  ADD CONSTRAINT `FK_567AA74EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_wallet`
--
ALTER TABLE `user_wallet`
  ADD CONSTRAINT `FK_193A8922A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `wallet_funding`
--
ALTER TABLE `wallet_funding`
  ADD CONSTRAINT `FK_A20CF4C0712520F3` FOREIGN KEY (`wallet_id`) REFERENCES `user_wallet` (`id`);

--
-- Constraints for table `wallet_payout`
--
ALTER TABLE `wallet_payout`
  ADD CONSTRAINT `FK_FE8E36466B13A89B` FOREIGN KEY (`receiving_bank_account_id`) REFERENCES `user_bank_account` (`id`),
  ADD CONSTRAINT `FK_FE8E3646712520F3` FOREIGN KEY (`wallet_id`) REFERENCES `user_wallet` (`id`);

--
-- Constraints for table `wallet_transfer`
--
ALTER TABLE `wallet_transfer`
  ADD CONSTRAINT `FK_B49117B42B2CC405` FOREIGN KEY (`sender_wallet_id`) REFERENCES `user_wallet` (`id`),
  ADD CONSTRAINT `FK_B49117B4BCCA839B` FOREIGN KEY (`receiver_wallet_id`) REFERENCES `user_wallet` (`id`);

--
-- Constraints for table `witness_binding`
--
ALTER TABLE `witness_binding`
  ADD CONSTRAINT `FK_64C4FDFCA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_64C4FDFCF28D7E1C` FOREIGN KEY (`witness_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
