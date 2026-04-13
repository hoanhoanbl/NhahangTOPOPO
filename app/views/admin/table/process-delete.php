<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

$auth = adminAuth();
if (!$auth->can('table', 'delete')) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong co quyen xoa ban.');
}

$maban = isset($_GET['MaBan']) ? (int)$_GET['MaBan'] : 0;
if ($maban <= 0) {
    $_SESSION['error_message'] = 'Vui long nhap day du thong tin.';
    echo "<script>window.location.href='?page=admin&section=table';</script>";
    exit();
}

$checkStmt = mysqli_prepare($conn, "SELECT MaCoSo FROM ban WHERE MaBan = ? LIMIT 1");
mysqli_stmt_bind_param($checkStmt, "i", $maban);
mysqli_stmt_execute($checkStmt);
$checkRs = mysqli_stmt_get_result($checkStmt);
$banInfo = mysqli_fetch_assoc($checkRs) ?: [];
mysqli_stmt_close($checkStmt);

$targetBranch = (int)($banInfo['MaCoSo'] ?? 0);
if (!$auth->isAdmin() && !adminCanAccessBranch($targetBranch)) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong duoc xoa ban thuoc co so khac.');
}

mysqli_begin_transaction($conn);

try {
    $sql2 = "DELETE c FROM chitietdondatban c
             JOIN dondatban_ban dbb ON c.MaDon = dbb.MaDon
             WHERE dbb.MaBan = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $maban);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    $sql3 = "DELETE dd FROM dondatban dd
             JOIN dondatban_ban dbb ON dd.MaDon = dbb.MaDon
             WHERE dbb.MaBan = ?";
    $stmt3 = mysqli_prepare($conn, $sql3);
    mysqli_stmt_bind_param($stmt3, "i", $maban);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_close($stmt3);

    $sql1 = "DELETE FROM dondatban_ban WHERE MaBan = ?";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "i", $maban);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    $sql4 = "DELETE FROM ban WHERE MaBan = ?";
    $stmt4 = mysqli_prepare($conn, $sql4);
    mysqli_stmt_bind_param($stmt4, "i", $maban);
    mysqli_stmt_execute($stmt4);
    mysqli_stmt_close($stmt4);

    mysqli_commit($conn);
    $_SESSION['success_message'] = 'Xoa ban thanh cong!';
    echo "<script>window.location.href='?page=admin&section=table';</script>";
    exit();
} catch (Throwable $e) {
    mysqli_rollback($conn);
    $_SESSION['error_message'] = 'Co loi xay ra khi xoa ban: ' . $e->getMessage();
    echo "<script>window.location.href='?page=admin&section=table';</script>";
    exit();
}
?>


