<?php
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

$auth = adminAuth();
if (!$auth->can('menu', 'create')) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong co quyen them mon vao menu co so.');
}

$maCoSo = isset($_POST['MaCoSo']) ? (int)$_POST['MaCoSo'] : 0;
$maMon = isset($_POST['MaMon']) ? (int)$_POST['MaMon'] : 0;
$gia = isset($_POST['Gia']) ? (int)$_POST['Gia'] : -1;
$tinhTrang = trim((string)($_POST['TinhTrang'] ?? ''));

if (!$auth->isAdmin()) {
    $maCoSo = $auth->getCurrentBranchId();
}

if ($maCoSo <= 0 || $maMon <= 0 || $gia < 0 || !in_array($tinhTrang, ['con_hang', 'het_hang'], true)) {
    echo "Vui long nhap day du thong tin hop le!";
    exit();
}

if (!$auth->isAdmin() && !adminCanAccessBranch($maCoSo)) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong duoc them mon vao co so khac.');
}

$checkSql = "SELECT 1 FROM menu_coso WHERE MaCoSo = ? AND MaMon = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $maCoSo, $maMon);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$exists = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

if (!$exists) {
    $insertSql = "INSERT INTO menu_coso (MaCoSo, MaMon, Gia, TinhTrang) VALUES (?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($insertStmt, "iiis", $maCoSo, $maMon, $gia, $tinhTrang);
    mysqli_stmt_execute($insertStmt);
    mysqli_stmt_close($insertStmt);
}

echo "<script>window.location.href='?page=admin&section=menu_branch&branch_id=$maCoSo';</script>";
exit();
?>


