<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__, 4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";
require_once __DIR__ . '/table-input-helper.php';

$auth = adminAuth();
if (!$auth->can('table', 'create')) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong co quyen them ban.');
}

$payload = tableCollectInput($_POST);
if (!$auth->isAdmin()) {
    $payload['MaCoSo'] = $auth->getCurrentBranchId();
}

if (!$auth->isAdmin() && !adminCanAccessBranch((int)$payload['MaCoSo'])) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong duoc thao tac ban thuoc co so khac.');
}

$errors = tableValidateInput($conn, $payload);
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
    echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
    exit();
}

$sql = "INSERT INTO ban (MaBanCode, MaCoSo, TenBan, ZoneBan, SucChua, SucChuaToiDa, OnlineBookable, GhepBanDuoc, TrangThai, GhiChu)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    $_SESSION['error_message'] = 'Khong the chuan bi cau lenh them ban.';
    echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    'sissiiiiss',
    $payload['MaBanCode'],
    $payload['MaCoSo'],
    $payload['TenBan'],
    $payload['ZoneBan'],
    $payload['SucChua'],
    $payload['SucChuaToiDa'],
    $payload['OnlineBookable'],
    $payload['GhepBanDuoc'],
    $payload['TrangThai'],
    $payload['GhiChu']
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = 'Them ban thanh cong.';
} else {
    $_SESSION['error_message'] = 'Loi them ban: ' . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
echo "<script>window.location.href='?page=admin&section=table&action=view';</script>";
exit();
?>


