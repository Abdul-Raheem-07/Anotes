-- Run this once in phpMyAdmin (or via `mysql -u root notes < schema.sql`)
-- against a database named `notes`.
-- NOTE: if you already created these tables before, use migration.sql
-- instead so you don't lose existing data.

CREATE DATABASE IF NOT EXISTS `notes` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `notes`;

CREATE TABLE IF NOT EXISTS `notes` (
  `sno` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `tstamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `reminders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `remind_date` DATE NOT NULL,
  `remind_time` TIME NOT NULL,
  `repeat_option` ENUM('none','daily','weekly','monthly','custom') DEFAULT 'none',
  `custom_days` INT DEFAULT NULL,
  `is_done` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;