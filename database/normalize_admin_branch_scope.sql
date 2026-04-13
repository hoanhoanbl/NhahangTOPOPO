-- Optional migration for role-branch-scope hardening
-- Purpose: normalize admin records to a deterministic branch id while keeping admin global scope in app logic.
-- Current schema requires nhanvien.MaCoSo NOT NULL + FK, so NULL is not currently allowed.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS nhanvien_backup_admin_branch_scope AS
SELECT *
FROM nhanvien
WHERE ChucVu = 'admin';

-- Choose a fallback branch id deterministically (smallest MaCoSo)
SET @fallback_branch := (SELECT MIN(MaCoSo) FROM coso);

-- Normalize admin records with invalid/missing branch ids
UPDATE nhanvien n
LEFT JOIN coso c ON c.MaCoSo = n.MaCoSo
SET n.MaCoSo = @fallback_branch
WHERE n.ChucVu = 'admin'
  AND (n.MaCoSo IS NULL OR c.MaCoSo IS NULL);

COMMIT;

-- Verification query
SELECT MaNV, TenDN, MaCoSo, ChucVu
FROM nhanvien
WHERE ChucVu = 'admin'
ORDER BY MaNV;

-- Rollback guide (manual):
-- 1) START TRANSACTION;
-- 2) UPDATE nhanvien n
--    JOIN nhanvien_backup_admin_branch_scope b ON b.MaNV = n.MaNV
--    SET n.MaCoSo = b.MaCoSo
--    WHERE n.ChucVu = 'admin';
-- 3) COMMIT;
