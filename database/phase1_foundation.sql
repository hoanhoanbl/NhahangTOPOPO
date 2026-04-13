-- ================================================================
-- Phase 1 Foundation: RBAC + Booking Rules
-- Run this migration ONCE after backing up your database
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ================================================================
-- 1. permissions table
-- ================================================================
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role` ENUM('admin','manager','receptionist') NOT NULL,
    `resource` VARCHAR(50) NOT NULL,
    `can_view` TINYINT(1) NOT NULL DEFAULT 1,
    `can_create` TINYINT(1) NOT NULL DEFAULT 0,
    `can_update` TINYINT(1) NOT NULL DEFAULT 0,
    `can_delete` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_role_resource` (`role`, `resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 2. Seed permissions
-- ================================================================

-- Admin: full access to everything
INSERT INTO `permissions` (`role`, `resource`, `can_view`, `can_create`, `can_update`, `can_delete`) VALUES
('admin', 'dashboard', 1, 1, 1, 1),
('admin', 'booking',  1, 1, 1, 1),
('admin', 'table',   1, 1, 1, 1),
('admin', 'menu',    1, 1, 1, 1),
('admin', 'branch',  1, 1, 1, 1),
('admin', 'staff',   1, 1, 1, 1),
('admin', 'report',  1, 1, 1, 1),
('admin', 'uudai',   1, 1, 1, 1)
ON DUPLICATE KEY UPDATE `can_view`=VALUES(`can_view`), `can_create`=VALUES(`can_create`), `can_update`=VALUES(`can_update`), `can_delete`=VALUES(`can_delete`);

-- Manager: booking/table/menu CRUD, no branch/staff/uudai
INSERT INTO `permissions` (`role`, `resource`, `can_view`, `can_create`, `can_update`, `can_delete`) VALUES
('manager', 'dashboard', 1, 1, 1, 0),
('manager', 'booking',  1, 1, 1, 0),
('manager', 'table',   1, 1, 1, 0),
('manager', 'menu',    1, 1, 1, 0),
('manager', 'branch',  1, 0, 0, 0),
('manager', 'staff',   1, 0, 0, 0),
('manager', 'report',  1, 1, 1, 0),
('manager', 'uudai',   1, 0, 0, 0)
ON DUPLICATE KEY UPDATE `can_view`=VALUES(`can_view`), `can_create`=VALUES(`can_create`), `can_update`=VALUES(`can_update`), `can_delete`=VALUES(`can_delete`);

-- Receptionist: booking + table read/create, no update/delete on anything
INSERT INTO `permissions` (`role`, `resource`, `can_view`, `can_create`, `can_update`, `can_delete`) VALUES
('receptionist', 'dashboard', 1, 1, 0, 0),
('receptionist', 'booking',  1, 1, 0, 0),
('receptionist', 'table',   1, 1, 0, 0),
('receptionist', 'menu',    1, 0, 0, 0),
('receptionist', 'branch',  1, 0, 0, 0),
('receptionist', 'staff',   1, 0, 0, 0),
('receptionist', 'report',  1, 0, 0, 0),
('receptionist', 'uudai',   1, 0, 0, 0)
ON DUPLICATE KEY UPDATE `can_view`=VALUES(`can_view`), `can_create`=VALUES(`can_create`), `can_update`=VALUES(`can_update`), `can_delete`=VALUES(`can_delete`);

-- ================================================================
-- 3. booking_rules table
-- ================================================================
CREATE TABLE IF NOT EXISTS `booking_rules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `rule_key` VARCHAR(50) NOT NULL UNIQUE,
    `rule_value` VARCHAR(255) NOT NULL,
    `description` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 4. Seed booking_rules
-- ================================================================
INSERT INTO `booking_rules` (`rule_key`, `rule_value`, `description`) VALUES
('lead_time_hours',      '2',    'Minimum hours before booking time'),
('max_advance_days',     '30',   'Maximum days in advance a booking can be made'),
('deposit_weekend',     '100000', 'Fixed deposit amount (VND) for weekends and holidays'),
('deposit_menu_percent', '50',   'Deposit as percentage of menu total when pre-ordering')
ON DUPLICATE KEY UPDATE `rule_value`=VALUES(`rule_value`), `description`=VALUES(`description`);
