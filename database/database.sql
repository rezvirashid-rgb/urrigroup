-- =====================================================================
--  URRI GROUP — Database schema (Phase 1)
--  Import this file into your MySQL / MariaDB database
--  (e.g. via phpMyAdmin > Import, or the mysql CLI).
--
--  Character set: utf8mb4 (full Unicode, incl. emoji & Bangla).
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ---------------------------------------------------------------------
--  Table: contact_submissions
--  Stores messages sent through the website contact form.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_submissions` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120)  NOT NULL,
  `company`       VARCHAR(150)  DEFAULT NULL,
  `email`         VARCHAR(150)  NOT NULL,
  `phone`         VARCHAR(40)   DEFAULT NULL,
  `business_area` VARCHAR(60)   NOT NULL,
  `subject`       VARCHAR(160)  NOT NULL,
  `message`       TEXT          NOT NULL,
  `ip_address`    VARCHAR(45)   DEFAULT NULL,   -- supports IPv4 and IPv6
  `user_agent`    VARCHAR(255)  DEFAULT NULL,
  `status`        ENUM('New','Contacted','Closed') NOT NULL DEFAULT 'New',
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status`     (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_email`      (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
