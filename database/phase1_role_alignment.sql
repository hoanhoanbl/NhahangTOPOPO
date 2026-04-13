-- Align nhanvien.ChucVu with RBAC roles (admin/manager/receptionist)
-- Safe to run multiple times.

SET time_zone = "+07:00";

-- 1) Expand enum temporarily to include legacy value for update safety
ALTER TABLE `nhanvien`
MODIFY COLUMN `ChucVu` ENUM('admin','manager','receptionist','nhan_vien') NOT NULL DEFAULT 'receptionist';

-- 2) Migrate legacy role values
UPDATE `nhanvien`
SET `ChucVu` = 'receptionist'
WHERE `ChucVu` = 'nhan_vien';

-- 3) Lock enum to 3 canonical roles
ALTER TABLE `nhanvien`
MODIFY COLUMN `ChucVu` ENUM('admin','manager','receptionist') NOT NULL DEFAULT 'receptionist';
