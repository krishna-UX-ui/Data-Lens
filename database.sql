-- ==========================================================
-- DataLens Authentication Database Schema
-- Database: datalens
-- Compatible with XAMPP MySQL / MariaDB and phpMyAdmin
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `datalens` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `datalens`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed default demo user (Password: DataLens2026!)
-- --------------------------------------------------------

INSERT INTO `users` (`full_name`, `email`, `password_hash`)
SELECT 'Alex Morgan', 'demo@datalens.ai', '$2y$10$BHMzThv.sJ7hyFvYTaW1fOsoXrlTXEkkzBiG1Ms7gzLe4b3rSDkz.'
WHERE NOT EXISTS (
    SELECT 1 FROM `users` WHERE `email` = 'demo@datalens.ai'
);
