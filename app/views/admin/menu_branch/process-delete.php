<?php
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

$auth = adminAuth();
if (!$auth->can('menu', 'delete')) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong co quyen xoa mon khoi menu co so.');
}

$maCoSo = isset($_GET['MaCoSo']) ? (int)$_GET['MaCoSo'] : 0;
$maMon = isset($_GET['MaMon']) ? (int)$_GET['MaMon'] : 0;

if (!$auth->isAdmin()) {
    $maCoSo = $auth->getCurrentBranchId();
}

if ($maCoSo <= 0 || $maMon <= 0) {
    echo "Thieu thong tin de xoa!";
    exit();
}

if (!$auth->isAdmin() && !adminCanAccessBranch($maCoSo)) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong duoc xoa mon cua co so khac.');
}

$sql = "DELETE FROM menu_coso WHERE MaCoSo = ? AND MaMon = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $maCoSo, $maMon);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "<script>window.location.href='?page=admin&section=menu_branch&branch_id=$maCoSo';</script>";
exit();
?>


