<?php
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>window.location.href='?page=admin&section=users';</script>";
    exit();
}

$auth = adminAuth();
if (!$auth->can('staff', 'create')) {
    adminDenyAndRedirect('?page=admin&section=users', 'Ban khong co quyen them nhan vien.');
}

$tenNhanVien = trim((string)($_POST['TenNhanVien'] ?? ''));
$tenDN = trim((string)($_POST['TenDN'] ?? ''));
$matKhau = (string)($_POST['MatKhau'] ?? '');
$chucVu = $auth->normalizeRoleValue(trim((string)($_POST['ChucVu'] ?? '')));
$maCoSo = isset($_POST['MaCoSo']) ? (int)$_POST['MaCoSo'] : 0;

if ($tenNhanVien === '' || $tenDN === '' || $matKhau === '' || $chucVu === '') {
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

// DB schema currently requires MaCoSo NOT NULL + FK for every user, including admin.
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
    echo "<script>alert('Khong tim thay co so hop le de gan tai khoan.'); window.history.back();</script>";
    exit();
}

if (!$auth->isAdmin() && !adminCanAccessBranch($maCoSo)) {
    adminDenyAndRedirect('?page=admin&section=users', 'Ban khong duoc gan nhan vien vao co so khac.');
}

$checkSql = "SELECT COUNT(*) AS count FROM nhanvien WHERE TenDN = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "s", $tenDN);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

if ((int)($row['count'] ?? 0) > 0) {
    echo "<script>alert('Ten dang nhap da ton tai! Vui long chon ten khac.'); window.history.back();</script>";
    exit();
}

$hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);
$sql = "INSERT INTO nhanvien (MaCoSo, TenDN, MatKhau, TenNhanVien, ChucVu) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "issss", $maCoSo, $tenDN, $hashedPassword, $tenNhanVien, $chucVu);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Them nhan vien thanh cong!'); window.location.href='?page=admin&section=users';</script>";
} else {
    echo "<script>alert('Co loi xay ra: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

mysqli_stmt_close($stmt);
?>


