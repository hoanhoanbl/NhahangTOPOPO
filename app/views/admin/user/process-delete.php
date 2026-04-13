<?php
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

$auth = adminAuth();
if (!$auth->can('staff', 'delete')) {
    adminDenyAndRedirect('?page=admin&section=users', 'Ban khong co quyen xoa nhan vien.');
}

$maNV = isset($_GET['MaNV']) ? (int)$_GET['MaNV'] : 0;
if ($maNV <= 0) {
    echo "<script>alert('Khong tim thay thong tin nhan vien can xoa!'); window.location.href='?page=admin&section=users';</script>";
    exit();
}

$targetStmt = mysqli_prepare($conn, "SELECT MaCoSo, ChucVu FROM nhanvien WHERE MaNV = ? LIMIT 1");
mysqli_stmt_bind_param($targetStmt, "i", $maNV);
mysqli_stmt_execute($targetStmt);
$targetRs = mysqli_stmt_get_result($targetStmt);
$targetUser = mysqli_fetch_assoc($targetRs) ?: [];
mysqli_stmt_close($targetStmt);

if (!$targetUser) {
    echo "<script>alert('Nhan vien khong ton tai!'); window.location.href='?page=admin&section=users';</script>";
    exit();
}

$targetBranch = (int)($targetUser['MaCoSo'] ?? 0);
$targetRole = $auth->normalizeRoleValue((string)($targetUser['ChucVu'] ?? ''));

if (!$auth->isAdmin() && !adminCanAccessBranch($targetBranch)) {
    adminDenyAndRedirect('?page=admin&section=users', 'Ban khong duoc xoa nhan vien cua co so khac.');
}

$checkAdminSql = "SELECT COUNT(*) AS admin_count FROM nhanvien WHERE ChucVu = 'admin'";
$checkResult = mysqli_query($conn, $checkAdminSql);
$adminCount = (int)(mysqli_fetch_assoc($checkResult)['admin_count'] ?? 0);

if ($targetRole === 'admin' && $adminCount <= 1) {
    echo "<script>alert('Khong the xoa admin cuoi cung trong he thong!'); window.location.href='?page=admin&section=users';</script>";
    exit();
}

$deleteStmt = mysqli_prepare($conn, "DELETE FROM nhanvien WHERE MaNV = ?");
mysqli_stmt_bind_param($deleteStmt, "i", $maNV);
if (mysqli_stmt_execute($deleteStmt)) {
    echo "<script>alert('Xoa nhan vien thanh cong!'); window.location.href='?page=admin&section=users';</script>";
} else {
    echo "<script>alert('Co loi xay ra: " . mysqli_error($conn) . "'); window.location.href='?page=admin&section=users';</script>";
}
mysqli_stmt_close($deleteStmt);
?>


