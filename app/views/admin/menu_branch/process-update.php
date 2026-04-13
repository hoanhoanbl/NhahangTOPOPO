<?php
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

$auth = adminAuth();
if (!$auth->can('menu', 'update')) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong co quyen cap nhat menu theo co so.');
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
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong duoc cap nhat menu cua co so khac.');
}

$sql = "UPDATE menu_coso SET Gia = ?, TinhTrang = ? WHERE MaCoSo = ? AND MaMon = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isii", $gia, $tinhTrang, $maCoSo, $maMon);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "<script>window.location.href='?page=admin&section=menu_branch&branch_id=$maCoSo';</script>";
exit();
?>



