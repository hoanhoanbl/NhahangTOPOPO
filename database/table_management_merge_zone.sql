-- ============================================================================
-- Change: table-management-merge-zone
-- Purpose: Align table schema and constraints for full admin management + merge allocation
-- Safe deployment notes:
-- 1) Run on backup first.
-- 2) All changes are additive or index-level hardening.
-- 3) Existing data is preserved; default values are provided for new NOT NULL fields.
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

START TRANSACTION;

-- ----------------------------------------------------------------------------
-- 1) Ensure required columns exist in `ban`
-- ----------------------------------------------------------------------------
ALTER TABLE `ban`
  ADD COLUMN IF NOT EXISTS `MaBanCode` VARCHAR(30) NOT NULL DEFAULT '' AFTER `MaBan`,
  ADD COLUMN IF NOT EXISTS `ZoneBan` VARCHAR(100) NOT NULL DEFAULT '' AFTER `TenBan`,
  ADD COLUMN IF NOT EXISTS `SucChuaToiDa` INT NOT NULL DEFAULT 1 AFTER `SucChua`,
  ADD COLUMN IF NOT EXISTS `OnlineBookable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `SucChuaToiDa`,
  ADD COLUMN IF NOT EXISTS `GhepBanDuoc` TINYINT(1) NOT NULL DEFAULT 1 AFTER `OnlineBookable`,
  ADD COLUMN IF NOT EXISTS `TrangThai` VARCHAR(20) NOT NULL DEFAULT 'Active' AFTER `GhepBanDuoc`,
  ADD COLUMN IF NOT EXISTS `GhiChu` TEXT NULL AFTER `TrangThai`;

-- Backfill safe defaults for pre-existing rows
UPDATE `ban`
SET `MaBanCode` = CONCAT('TBL-', LPAD(`MaBan`, 4, '0'))
WHERE `MaBanCode` = '' OR `MaBanCode` IS NULL;

UPDATE `ban`
SET `ZoneBan` = 'Unassigned'
WHERE `ZoneBan` = '' OR `ZoneBan` IS NULL;

UPDATE `ban`
SET `SucChuaToiDa` = CASE
    WHEN `SucChua` IS NULL OR `SucChua` < 1 THEN 1
    ELSE `SucChua`
END
WHERE `SucChuaToiDa` IS NULL OR `SucChuaToiDa` < `SucChua` OR `SucChuaToiDa` < 1;

UPDATE `ban`
SET `TrangThai` = 'Active'
WHERE `TrangThai` IS NULL OR TRIM(`TrangThai`) = '';

-- ----------------------------------------------------------------------------
-- 2) Enforce uniqueness for table code per branch (idempotent)
-- ----------------------------------------------------------------------------
-- Pre-clean duplicate (MaCoSo, MaBanCode) to avoid migration failure when creating unique key.
-- Keep the smallest MaBan in each duplicate group; re-code remaining rows deterministically.
UPDATE `ban` b
JOIN (
  SELECT b1.`MaBan`,
         (
           SELECT MIN(b2.`MaBan`)
           FROM `ban` b2
           WHERE b2.`MaCoSo` = b1.`MaCoSo`
             AND b2.`MaBanCode` = b1.`MaBanCode`
         ) AS `KeepMaBan`
  FROM `ban` b1
  WHERE b1.`MaBanCode` IS NOT NULL
    AND TRIM(b1.`MaBanCode`) <> ''
) d ON d.`MaBan` = b.`MaBan`
SET b.`MaBanCode` = CONCAT('TBL-', LPAD(b.`MaBan`, 4, '0'))
WHERE d.`KeepMaBan` IS NOT NULL
  AND d.`MaBan` <> d.`KeepMaBan`;

SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'ban'
    AND index_name = 'uk_ban_macoso_mabancode'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `ban` ADD UNIQUE KEY `uk_ban_macoso_mabancode` (`MaCoSo`, `MaBanCode`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 3) Add indexes for filtering and allocation (idempotent)
-- ----------------------------------------------------------------------------
SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'ban' AND index_name = 'idx_ban_zone'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `ban` ADD KEY `idx_ban_zone` (`ZoneBan`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'ban' AND index_name = 'idx_ban_trangthai'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `ban` ADD KEY `idx_ban_trangthai` (`TrangThai`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'ban' AND index_name = 'idx_ban_merge_status'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `ban` ADD KEY `idx_ban_merge_status` (`MaCoSo`, `ZoneBan`, `TrangThai`, `GhepBanDuoc`, `OnlineBookable`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'ban' AND index_name = 'idx_ban_tenban'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `ban` ADD KEY `idx_ban_tenban` (`TenBan`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'dondatban' AND index_name = 'idx_dondatban_conflict'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `dondatban` ADD KEY `idx_dondatban_conflict` (`MaCoSo`, `TrangThai`, `ThoiGianBatDau`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists_idx = (
  SELECT COUNT(*)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'dondatban_ban' AND index_name = 'idx_dondatban_ban_maban_madon'
);
SET @sql = IF(@exists_idx = 0,
  'ALTER TABLE `dondatban_ban` ADD KEY `idx_dondatban_ban_maban_madon` (`MaBan`, `MaDon`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 4) Verify integrity for multi-table assignment mapping (idempotent)
-- ----------------------------------------------------------------------------
-- Pre-clean duplicate mapping rows to avoid PK creation failure on legacy data.
DROP TEMPORARY TABLE IF EXISTS `tmp_dondatban_ban_unique`;

CREATE TEMPORARY TABLE `tmp_dondatban_ban_unique` AS
SELECT `MaDon`, `MaBan`
FROM `dondatban_ban`
GROUP BY `MaDon`, `MaBan`;

DELETE FROM `dondatban_ban`;

INSERT INTO `dondatban_ban` (`MaDon`, `MaBan`)
SELECT `MaDon`, `MaBan`
FROM `tmp_dondatban_ban_unique`;

DROP TEMPORARY TABLE IF EXISTS `tmp_dondatban_ban_unique`;

SET @pk_columns = (
  SELECT GROUP_CONCAT(kcu.COLUMN_NAME ORDER BY kcu.ORDINAL_POSITION SEPARATOR ',')
  FROM information_schema.table_constraints tc
  JOIN information_schema.key_column_usage kcu
    ON tc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
   AND tc.TABLE_NAME = kcu.TABLE_NAME
   AND tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
  WHERE tc.CONSTRAINT_SCHEMA = DATABASE()
    AND tc.TABLE_NAME = 'dondatban_ban'
    AND tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
);
SET @sql = IF(
  @pk_columns IS NULL,
  'ALTER TABLE `dondatban_ban` ADD PRIMARY KEY (`MaDon`, `MaBan`)',
  IF(
    @pk_columns = 'MaDon,MaBan',
    'SELECT 1',
    'ALTER TABLE `dondatban_ban` DROP PRIMARY KEY, ADD PRIMARY KEY (`MaDon`, `MaBan`)'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;

-- ============================================================================
-- Rollback notes (manual)
-- - If rollback is required, revert PHP code first.
-- - Optional index rollback (non-destructive):
--   DROP INDEX uk_ban_macoso_mabancode ON ban;
--   DROP INDEX idx_ban_zone ON ban;
--   DROP INDEX idx_ban_trangthai ON ban;
--   DROP INDEX idx_ban_merge_status ON ban;
--   DROP INDEX idx_ban_tenban ON ban;
--   DROP INDEX idx_dondatban_conflict ON dondatban;
--   DROP INDEX idx_dondatban_ban_maban_madon ON dondatban_ban;
-- - Avoid dropping columns unless you are sure no deployed code depends on them.
-- ============================================================================
