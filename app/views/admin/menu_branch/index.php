<?php
include dirname(__DIR__, 4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";

$authBranch = adminAuth();
$isGlobalAdmin = $authBranch->isAdmin();
$sessionBranchId = $authBranch->getCurrentBranchId();
if (!$isGlobalAdmin && $sessionBranchId <= 0) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Khong tim thay co so duoc phan quyen cho tai khoan nay.');
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'process-create':
            include __DIR__ . "/process-create.php";
            break;
        case 'process-update':
            include __DIR__ . "/process-update.php";
            break;
        case 'delete':
            include __DIR__ . "/process-delete.php";
            break;
    }
}

$sql_branches = "SELECT * FROM coso";
if (!$isGlobalAdmin) {
    $sql_branches .= " WHERE MaCoSo = " . (int)$sessionBranchId;
}
$sql_branches .= " ORDER BY TenCoSo";
$result_branches = mysqli_query($conn, $sql_branches);
$branches = [];
while ($row = mysqli_fetch_assoc($result_branches)) {
    $branches[] = $row;
}

$requestedBranchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;
$selected_branch_id = adminScopedBranchId($requestedBranchId);
if (!$isGlobalAdmin && $requestedBranchId > 0 && $requestedBranchId !== $sessionBranchId) {
    adminDenyAndRedirect('?page=admin&section=menu_branch', 'Ban khong duoc quan ly menu cua co so khac.');
}
if ($selected_branch_id <= 0 && !$isGlobalAdmin) {
    $selected_branch_id = $sessionBranchId;
}
?>

<div class="container-fluid">
    <div class="card shadow p-4">
        <h3 class="mb-4">Quản lý Menu theo Cơ sở</h3>

        <form action="" method="GET">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="section" value="menu_branch">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label for="branch_id" class="form-label"><strong>Chọn một cơ sở để quản lý:</strong></label>
                    <select class="form-select" id="branch_id" name="branch_id" onchange="this.form.submit()" <?php echo $isGlobalAdmin ? '' : 'disabled'; ?>>
                        <option value="">-- Vui lòng chọn cơ sở --</option>
                        <?php foreach ($branches as $branch) : ?>
                            <option value="<?php echo (int)$branch['MaCoSo']; ?>" <?php echo ((int)$selected_branch_id === (int)$branch['MaCoSo']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($branch['TenCoSo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$isGlobalAdmin): ?>
                        <input type="hidden" name="branch_id" value="<?php echo (int)$selected_branch_id; ?>">
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <hr>

        <?php
        if ($selected_branch_id > 0) {
            include __DIR__ . "/view.php";
        } else {
            echo '<div class="alert alert-info text-center" role="alert">';
            echo '  <i class="fas fa-info-circle fa-2x mb-3"></i><br>';
            echo '  Vui lòng chọn một cơ sở từ danh sách ở trên để xem và quản lý thực đơn chi tiết.';
            echo '</div>';
        }
        ?>
    </div>
</div>



