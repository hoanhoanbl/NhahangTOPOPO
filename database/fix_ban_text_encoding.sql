-- ============================================================================
-- Script: fix_ban_text_encoding.sql
-- Purpose:
--   Repair corrupted text in `ban` table (`TenBan`, `ZoneBan`, `GhiChu`)
--   with a safe workflow:
--   - backup first
--   - charset hardening
--   - best-effort mojibake decode
--   - targeted manual mapping for lossy "?" strings
--
-- IMPORTANT:
--   MySQL DDL causes implicit commit. Do NOT rely on transaction rollback for DDL.
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ----------------------------------------------------------------------------
-- 0) Backup FIRST (before any ALTER/UPDATE)
-- ----------------------------------------------------------------------------
SET @backup_name = CONCAT('ban_backup_encoding_', DATE_FORMAT(NOW(), '%Y%m%d_%H%i%S'));
SET @sql_backup = CONCAT('CREATE TABLE `', @backup_name, '` AS SELECT * FROM `ban`');
PREPARE stmt FROM @sql_backup; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 1) Charset/collation hardening (future writes)
-- ----------------------------------------------------------------------------
ALTER TABLE `ban` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2) Preview suspicious rows
-- ----------------------------------------------------------------------------
SELECT `MaBan`, `TenBan`, `ZoneBan`, `GhiChu`
FROM `ban`
WHERE
    `TenBan` REGEXP '\\?|Ã|Â|Æ|Ä'
 OR `ZoneBan` REGEXP '\\?|Ã|Â|Æ|Ä'
 OR `GhiChu` REGEXP '\\?|Ã|Â|Æ|Ä';

-- ----------------------------------------------------------------------------
-- 3) Best-effort decode for mojibake rows
-- ----------------------------------------------------------------------------
UPDATE `ban`
SET
    `TenBan` = CONVERT(CAST(CONVERT(`TenBan` USING latin1) AS BINARY) USING utf8mb4),
    `ZoneBan` = CONVERT(CAST(CONVERT(`ZoneBan` USING latin1) AS BINARY) USING utf8mb4),
    `GhiChu` = CASE
        WHEN `GhiChu` IS NULL THEN NULL
        ELSE CONVERT(CAST(CONVERT(`GhiChu` USING latin1) AS BINARY) USING utf8mb4)
    END
WHERE
    `TenBan` REGEXP 'Ã|Â|Æ|Ä'
 OR `ZoneBan` REGEXP 'Ã|Â|Æ|Ä'
 OR `GhiChu` REGEXP 'Ã|Â|Æ|Ä';

-- ----------------------------------------------------------------------------
-- 4) Manual mapping for lossy strings containing '?'
--    Use UTF-8-safe literals via UNHEX + CONVERT.
-- ----------------------------------------------------------------------------
UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('54E1BAA76E672031202D2054726F6E67206E68C3A0') USING utf8mb4)
WHERE `ZoneBan` LIKE 'T?ng 1 - Trong%';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('54E1BAA76E672032202D205468616E67206DC3A179') USING utf8mb4)
WHERE `ZoneBan` LIKE 'T?ng 2 - Thang%';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('54E1BAA76E672033') USING utf8mb4)
WHERE `ZoneBan` = 'T?ng 3';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('53C3A26E207468C6B0E1BBA36E67206E676FC3A069207472E1BB9D69') USING utf8mb4)
WHERE `ZoneBan` = 'Sân th??ng ngoài tr?i';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('54E1BAA76E67207472E1BB8774') USING utf8mb4)
WHERE `ZoneBan` = 'T?ng tr?t';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('4CE1BAA7752031') USING utf8mb4)
WHERE `ZoneBan` = 'L?u 1';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('42C3A36920C491E1BAAD75207865202F206E676FC3A069207472E1BB9D69') USING utf8mb4)
WHERE `ZoneBan` = 'B?i ??u xe / ngo?i tr?i';

UPDATE `ban`
SET `ZoneBan` = CONVERT(UNHEX('42C3A36920C491E1BAAD752078652074E1BAA76E67207468C6B0E1BBA36E67') USING utf8mb4)
WHERE `ZoneBan` = 'B?i ??u xe t?ng th??ng';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('47E1BAA76E2063E1BBAD612073E1BB95') USING utf8mb4)
WHERE `GhiChu` = 'G?n c?a s?';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('47E1BAA76E2062616E2063C3B46E67') USING utf8mb4)
WHERE `GhiChu` = 'G?n ban công' OR `GhiChu` = 'G?n ban c?ng';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('5068C3B26E67207269C3AA6E67206E68E1BB8F') USING utf8mb4)
WHERE `GhiChu` = 'Phòng riêng nh?';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('5068C3B26E67205649502063C3B32072C3A86D') USING utf8mb4)
WHERE `GhiChu` LIKE 'Phòng VIP c% r%m';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('C490616E672073E1BBAD61') USING utf8mb4)
WHERE `GhiChu` = '?ang s?a';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('566965772068E1BB932054C3A279') USING utf8mb4)
WHERE `GhiChu` LIKE 'View h? T%';

UPDATE `ban`
SET `GhiChu` = CONVERT(UNHEX('5068C3B26E672068E1BB8D7020322068E1BB9970') USING utf8mb4)
WHERE `GhiChu` = 'Phòng h?i h?p';

-- ----------------------------------------------------------------------------
-- 5) Optional normalization
-- ----------------------------------------------------------------------------
UPDATE `ban` SET `GhiChu` = NULL WHERE `GhiChu` IN ('NULL', 'null', '');

-- ----------------------------------------------------------------------------
-- 6) Post-check
-- ----------------------------------------------------------------------------
SELECT `MaBan`, `TenBan`, `ZoneBan`, `GhiChu`
FROM `ban`
WHERE
    `TenBan` REGEXP '\\?|Ã|Â|Æ|Ä'
 OR `ZoneBan` REGEXP '\\?|Ã|Â|Æ|Ä'
 OR `GhiChu` REGEXP '\\?|Ã|Â|Æ|Ä';

-- ----------------------------------------------------------------------------
-- Manual restore from backup table
--   1) SHOW TABLES LIKE 'ban_backup_encoding_%';
--   2) SET FOREIGN_KEY_CHECKS = 0;
--   3) DELETE FROM dondatban_ban;
--   4) DELETE FROM ban;
--   5) INSERT INTO ban SELECT * FROM <backup_table_name>;
--   6) SET FOREIGN_KEY_CHECKS = 1;
-- ----------------------------------------------------------------------------
