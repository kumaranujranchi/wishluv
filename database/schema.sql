-- SQL Schema for Wishluv Buildcon Website
-- Re-initialize these tables in Hostinger phpMyAdmin

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30";

-- --------------------------------------------------------

--
-- Table structure for table `contact_inquiries`
--

CREATE TABLE IF NOT EXISTS `contact_inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `source` varchar(100) DEFAULT 'contact_form',
  `status` varchar(50) DEFAULT 'new',
  `priority` varchar(20) DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE IF NOT EXISTS `job_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `position_applied` varchar(255) NOT NULL,
  `education` varchar(255) DEFAULT NULL,
  `current_salary` decimal(15,2) DEFAULT NULL,
  `expected_salary` decimal(15,2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `resume_path` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'applied',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
