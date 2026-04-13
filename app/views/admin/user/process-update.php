<?php
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>window.location.href='?page=admin&section=users';</script>";
    exit();
}

$auth = adminAuth();
if (!$auth->can('staff', 'update')) {
    adminDenyAndRedirect('?page=admin&section=users', 'Ban khong co quyen cap nhat nhan vien.');
}

$maNV = isset($_GET['MaNV']) ? (int)$_GET['MaNV'] : 0;
$maCoSo = isset($_POST['MaCoSo']) ? (int)$_POST['MaCoSo'] : 0;
$tenNhanVien = trim((string)($_POST['TenNhanVien'] ?? ''));
$tenDN = trim((string)($_POST['TenDN'] ?? ''));
$matKhau = (string)($_POST['MatKhau'] ?? '');
$chucVu = $auth->normalizeRoleValue(trim((string)($_POST['ChucVu'] ?? '')));

if ($maNV <= 0 || $tenNhanVien === '' || $tenDN === '' || $chucVu === '') {
    echo "<script>alert('Vui long nhap day du thong tin!'); window.history.back();</script>";
    exit();
}

$allowedRoles = ['admin', 'manager', 'receptionist'];
if (!in_array($chucVu, $allowedRoles, true)) {
    echo "<script>alert('Vai tro khong hop le!'); window.history.back();</script>";
    exit();
}

if (!$auth->isAdmin()) {
    $maCoSo = $auth->getCurrentBranchId();
} elseif ($chucVu !== 'admin' && $maCoSo <= 0) {
    echo "<script>alert('Quan ly va le tan bat buoc phai chon co so hop le.'); window.history.back();</script>";
    exit();
}

if ($maCoSo <= 0) {
    $fallbackBranch = $auth->getCurrentBranchId();
    if ($fallbackBranch <= 0) {
        $rsBranch = mysqli_query($conn, "SELECT MaCoSo FROM coso ORDER BY MaCoSo ASC LIMIT 1");
        $rowBranch = $rsBranch ? mysqli_fetch_assoc($rsBranch) : null;
        $fallbackBranch = (int)($rowBranch['MaCoSo'] ?? 0);
    }
    $maCoSo = $fallbackBranch;
}

if ($maCoSo <= 0) {
    echo "<script>alert('Khong tim thay co so hop le de cap nhat tai khoan.'); window.history.back();</script>";
    exit();
}

if (!$auth->isAdmin() && !adminCanAccessBranch($maCoSo)) {
    adminDenyAndRedirect('?page=admin&section=users', 'Ban khong duoc cap nhat nhan vien thuoc co so khac.');
}

$checkSql = "SELECT COUNT(*) AS count FROM nhanvien WHERE TenDN = ? AND MaNV != ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "si", $tenDN, $maNV);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

if ((int)($row['count'] ?? 0) > 0) {
    echo "<script>alert('Ten dang nhap da ton tai! Vui long chon ten khac.'); window.history.back();</script>";
    exit();
}

if ($matKhau !== '') {
    $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);
    $sql = "UPDATE nhanvien
            SET MaCoSo = ?, TenDN = ?, MatKhau = ?, TenNhanVien = ?, ChucVu = ?
            WHERE MaNV = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssi", $maCoSo, $tenDN, $hashedPassword, $tenNhanVien, $chucVu, $maNV);
} else {
    $sql = "UPDATE nhanvien
            SET MaCoSo = ?, TenDN = ?, TenNhanVien = ?, ChucVu = ?
            WHERE MaNV = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssi", $maCoSo, $tenDN, $tenNhanVien, $chucVu, $maNV);
}

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Cap nhat thong tin nhan vien thanh cong!'); window.location.href='?page=admin&section=users';</script>";
} else {
    echo "<script>alert('Co loi xay ra: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

mysqli_stmt_close($stmt);
?>


