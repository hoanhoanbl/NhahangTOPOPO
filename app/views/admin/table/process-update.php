<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__, 4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";
require_once __DIR__ . '/table-input-helper.php';

$auth = adminAuth();
if (!$auth->can('table', 'update')) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong co quyen cap nhat ban.');
}

$maBan = isset($_GET['MaBan']) ? (int)$_GET['MaBan'] : 0;
if ($maBan <= 0) {
    $_SESSION['error_message'] = 'Ma ban khong hop le.';
    echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
    exit();
}

$existingStmt = mysqli_prepare($conn, "SELECT MaCoSo FROM ban WHERE MaBan = ? LIMIT 1");
mysqli_stmt_bind_param($existingStmt, 'i', $maBan);
mysqli_stmt_execute($existingStmt);
$existingRs = mysqli_stmt_get_result($existingStmt);
$existingRow = mysqli_fetch_assoc($existingRs) ?: [];
mysqli_stmt_close($existingStmt);
$targetBranch = (int)($existingRow['MaCoSo'] ?? 0);

if (!$auth->isAdmin() && !adminCanAccessBranch($targetBranch)) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong duoc cap nhat ban thuoc co so khac.');
}

$payload = tableCollectInput($_POST);
if (!$auth->isAdmin()) {
    $payload['MaCoSo'] = $auth->getCurrentBranchId();
}

$errors = tableValidateInput($conn, $payload, $maBan);
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
    echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
    exit();
}

$sql = "UPDATE ban
        SET MaBanCode = ?,
            MaCoSo = ?,
            TenBan = ?,
            ZoneBan = ?,
            SucChua = ?,
            SucChuaToiDa = ?,
            OnlineBookable = ?,
            GhepBanDuoc = ?,
            TrangThai = ?,
            GhiChu = ?
        WHERE MaBan = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    $_SESSION['error_message'] = 'Khong the chuan bi cau lenh cap nhat ban.';
    echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    'sissiiiissi',
    $payload['MaBanCode'],
    $payload['MaCoSo'],
    $payload['TenBan'],
    $payload['ZoneBan'],
    $payload['SucChua'],
    $payload['SucChuaToiDa'],
    $payload['OnlineBookable'],
    $payload['GhepBanDuoc'],
    $payload['TrangThai'],
    $payload['GhiChu'],
    $maBan
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = 'Cap nhat ban thanh cong.';
} else {
    $_SESSION['error_message'] = 'Loi cap nhat: ' . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
exit();
?>


