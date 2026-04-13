<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__,4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";
$authBranch = adminAuth();
$isGlobalAdmin = $authBranch->isAdmin();
$sessionBranchId = $authBranch->getCurrentBranchId();
if (!$isGlobalAdmin && $sessionBranchId <= 0) {
    adminDenyAndRedirect('?page=admin&section=table&action=status', 'Khong tim thay co so duoc phan quyen cho tai khoan nay.');
}
$canUpdateTableStatus = isset($auth) ? $auth->can('table', 'update') : $authBranch->can('table', 'update');

// Xử lý cập nhật trạng thái bàn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maBan']) && isset($_POST['trangThai'])) {
    if (!$canUpdateTableStatus) {
        adminDenyAndRedirect('?page=admin&section=table&action=status', 'Ban khong co quyen cap nhat trang thai ban.');
    }
    require_once dirname(__DIR__,3) . '/models/TableStatusManager.php';
    
    $maBan = (int)$_POST['maBan'];
    $thoiGianBatDau = $_POST['thoiGianBatDau'];
    $thoiGianKetThuc = $_POST['thoiGianKetThuc'];
    $trangThai = $_POST['trangThai'];
    
    $targetBranch = adminScopedBranchId((int)($_POST['maCoSo'] ?? 0));
    if (!$isGlobalAdmin) {
        $stmtCheck = mysqli_prepare($conn, "SELECT MaCoSo FROM ban WHERE MaBan = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtCheck, "i", $maBan);
        mysqli_stmt_execute($stmtCheck);
        $resCheck = mysqli_stmt_get_result($stmtCheck);
        $banRow = mysqli_fetch_assoc($resCheck) ?: [];
        mysqli_stmt_close($stmtCheck);
        if ((int)($banRow['MaCoSo'] ?? 0) !== $sessionBranchId) {
            adminDenyAndRedirect('?page=admin&section=table&action=status', 'Ban khong duoc cap nhat trang thai ban cua co so khac.');
        }
        $targetBranch = $sessionBranchId;
    }

    $result = TableStatusManager::capNhatTrangThaiBan($maBan, $thoiGianBatDau, $thoiGianKetThuc, $trangThai);
    
    if ($result) {
        $_SESSION['success_message'] = 'Cập nhật trạng thái bàn thành công!';
    } else {
        $_SESSION['error_message'] = 'Có lỗi xảy ra khi cập nhật trạng thái bàn!';
    }
    
    // Sử dụng JavaScript redirect thay vì PHP header
    echo '<script>window.location.href = "?page=admin&section=table&action=status&maCoSo=' . (int)$targetBranch . '&thoiGianBatDau=' . urlencode($thoiGianBatDau) . '&thoiGianKetThuc=' . urlencode($thoiGianKetThuc) . '";</script>';
    exit;
}

// Khởi tạo các biến cần thiết
$listCoSo = [];
$maCoSo = 0;
$tenCoSo = '';
$thoiGianBatDau = date('Y-m-d H:i');
$thoiGianKetThuc = date('Y-m-d H:i', strtotime('+2 hours'));
$banList = [];

// Lấy danh sách cơ sở
require_once dirname(__DIR__,3) . '/models/TableStatusManager.php';
$listCoSo = TableStatusManager::layDanhSachCoSo();

// Lấy tham số từ URL
$requestedBranch = isset($_GET['maCoSo']) ? (int)$_GET['maCoSo'] : 0;
$maCoSo = adminScopedBranchId($requestedBranch);
if (!$isGlobalAdmin && $requestedBranch > 0 && $requestedBranch !== $sessionBranchId) {
    adminDenyAndRedirect('?page=admin&section=table&action=status', 'Ban khong duoc xem trang thai ban cua co so khac.');
}

if (isset($_GET['thoiGianBatDau'])) {
    $thoiGianBatDau = $_GET['thoiGianBatDau'];
}

if (isset($_GET['thoiGianKetThuc'])) {
    $thoiGianKetThuc = $_GET['thoiGianKetThuc'];
}

// Nếu đã chọn cơ sở, lấy danh sách bàn
if ($maCoSo > 0) {
    // Lấy thông tin cơ sở
    $sql = "SELECT TenCoSo FROM coso WHERE MaCoSo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $maCoSo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $coSo = mysqli_fetch_assoc($result);
    $tenCoSo = $coSo['TenCoSo'] ?? '';
    
    // Lấy danh sách bàn với trạng thái
    $banList = TableStatusManager::layBanTheoCoSo($maCoSo, $thoiGianBatDau, $thoiGianKetThuc);
    
    // Nếu có maCoSo từ URL nhưng chưa có thời gian, tự động load với thời gian mặc định
    if (empty($thoiGianBatDau) || $thoiGianBatDau == date('Y-m-d H:i')) {
        $thoiGianBatDau = date('Y-m-d H:i');
        $thoiGianKetThuc = date('Y-m-d H:i', strtotime('+2 hours'));
        $banList = TableStatusManager::layBanTheoCoSo($maCoSo, $thoiGianBatDau, $thoiGianKetThuc);
    }
}
?>

<style>
.table-details {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}

.status-trong {
    background-color: #28a745;
}

.status-da-dat {
    background-color: #ffc107;
}
</style>

<div class="card shadow p-4">
    <h4 class="mb-4">Quản lý trạng thái bàn theo thời gian</h4>
    
    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?=$_SESSION['success_message']?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?=$_SESSION['error_message']?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <!-- Form chọn cơ sở và thời gian -->
    <form method="GET" class="mb-4">
        <input type="hidden" name="page" value="admin">
        <input type="hidden" name="section" value="table">
        <input type="hidden" name="action" value="status">
        
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Chọn cơ sở:</label>
                <select name="maCoSo" class="form-select" required onchange="this.form.submit()" <?php echo $isGlobalAdmin ? '' : 'disabled'; ?>>
                    <option value="">-- Chọn cơ sở --</option>
                    <?php foreach($listCoSo as $coSo): ?>
                        <option value="<?=$coSo['MaCoSo']?>" <?=($maCoSo == $coSo['MaCoSo']) ? 'selected' : ''?>>
                            <?=$coSo['TenCoSo']?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$isGlobalAdmin): ?>
                    <input type="hidden" name="maCoSo" value="<?= (int)$maCoSo ?>">
                <?php endif; ?>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Thời gian bắt đầu:</label>
                <input type="datetime-local" name="thoiGianBatDau" class="form-control" 
                       value="<?=htmlspecialchars($thoiGianBatDau)?>" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Thời gian kết thúc:</label>
                <input type="datetime-local" name="thoiGianKetThuc" class="form-control" 
                       value="<?=htmlspecialchars($thoiGianKetThuc)?>" required>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="fas fa-search"></i> Xem
                </button>
            </div>
        </div>
    </form>

    <?php if ($maCoSo > 0): ?>
        <div class="alert alert-info">
            <strong>Cơ sở:</strong> <?=$tenCoSo?> | 
            <strong>Thời gian:</strong> <?=date('d/m/Y H:i', strtotime($thoiGianBatDau))?> - <?=date('d/m/Y H:i', strtotime($thoiGianKetThuc))?>
        </div>

        <!-- Bảng danh sách bàn -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th width="10%">Mã bàn</th>
                        <th width="25%">Tên bàn</th>
                        <th width="15%">Sức chứa</th>
                        <th width="20%">Trạng thái hiện tại</th>
                        <th width="30%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($banList)): ?>
                        <?php foreach($banList as $ban): ?>
                            <tr>
                                <td><?=$ban['MaBan']?></td>
                                <td><?=$ban['TenBan']?></td>
                                <td>
                                    <span class="badge bg-info"><?=$ban['SucChua']?> người</span>
                                </td>
                                <td>
                                    <?php if($ban['TrangThai'] == 'trong'): ?>
                                        <span class="badge bg-success fs-6">
                                            <span class="status-indicator status-trong"></span>Trống
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning fs-6">
                                            <span class="status-indicator status-da-dat"></span>Đã đặt
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($ban['TrangThai'] == 'trong'): ?>
                                        <!-- Bàn trống - hiển thị nút đánh dấu đã đặt -->
                                        <button class="btn btn-outline-warning btn-sm" 
                                                onclick="updateTableStatus(<?=$ban['MaBan']?>, 'da_dat', '<?=$thoiGianBatDau?>', '<?=$thoiGianKetThuc?>', <?=$maCoSo?>)"
                                                title="Đánh dấu bàn đã đặt">
                                            <i class="fas fa-user-plus"></i> Đánh dấu đã đặt
                                        </button>
                                    <?php else: ?>
                                        <!-- Bàn đã đặt - hiển thị nút đánh dấu trống -->
                                        <button class="btn btn-outline-success btn-sm" 
                                                onclick="updateTableStatus(<?=$ban['MaBan']?>, 'trong', '<?=$thoiGianBatDau?>', '<?=$thoiGianKetThuc?>', <?=$maCoSo?>)"
                                                title="Đánh dấu bàn trống">
                                            <i class="fas fa-user-minus"></i> Đánh dấu trống
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Nút xem chi tiết -->
                                    <button class="btn btn-outline-info btn-sm ms-1" 
                                            onclick="showTableDetails(<?=$ban['MaBan']?>, '<?=$ban['TenBan']?>', '<?=$ban['TrangThai']?>')"
                                            title="Xem chi tiết bàn">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-utensils fa-3x mb-3"></i>
                                <br>
                                Chưa có bàn nào trong cơ sở này
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Thống kê -->
        <?php if (!empty($banList)): ?>
            <?php
            $tongBan = count($banList);
            $banTrong = count(array_filter($banList, function($ban) { return $ban['TrangThai'] == 'trong'; }));
            $banDaDat = $tongBan - $banTrong;
            ?>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5><?=$tongBan?></h5>
                            <p class="mb-0">Tổng số bàn</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5><?=$banTrong?></h5>
                            <p class="mb-0">Bàn trống</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5><?=$banDaDat?></h5>
                            <p class="mb-0">Bàn đã đặt</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h5>Vui lòng chọn cơ sở để xem danh sách bàn</h5>
        </div>
    <?php endif; ?>
</div>

<!-- Form ẩn để cập nhật trạng thái -->
<form id="updateStatusForm" method="POST" style="display: none;">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="section" value="table">
    <input type="hidden" name="action" value="updateStatus">
    <input type="hidden" name="maBan" id="updateMaBan">
    <input type="hidden" name="thoiGianBatDau" id="updateThoiGianBatDau">
    <input type="hidden" name="thoiGianKetThuc" id="updateThoiGianKetThuc">
    <input type="hidden" name="trangThai" id="updateTrangThai">
    <input type="hidden" name="maCoSo" id="updateMaCoSo">
</form>

<script>
function updateTableStatus(maBan, trangThai, thoiGianBatDau, thoiGianKetThuc, maCoSo) {
    const action = trangThai === 'trong' ? 'đánh dấu trống' : 'đánh dấu đã đặt';
    const message = `Bạn có chắc chắn muốn ${action} bàn này?\n\nThời gian: ${thoiGianBatDau} - ${thoiGianKetThuc}`;
    
    if (confirm(message)) {
        // Hiển thị loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        button.disabled = true;
        
        document.getElementById('updateMaBan').value = maBan;
        document.getElementById('updateThoiGianBatDau').value = thoiGianBatDau;
        document.getElementById('updateThoiGianKetThuc').value = thoiGianKetThuc;
        document.getElementById('updateTrangThai').value = trangThai;
        document.getElementById('updateMaCoSo').value = maCoSo;
        document.getElementById('updateStatusForm').submit();
    }
}

function showTableDetails(maBan, tenBan, trangThai) {
    const statusText = trangThai === 'trong' ? 'Trống' : 'Đã đặt';
    const statusClass = trangThai === 'trong' ? 'success' : 'warning';
    
    const details = `
        <div class="table-details">
            <h6><i class="fas fa-utensils"></i> Chi tiết bàn</h6>
            <p><strong>Mã bàn:</strong> ${maBan}</p>
            <p><strong>Tên bàn:</strong> ${tenBan}</p>
            <p><strong>Trạng thái:</strong> <span class="badge bg-${statusClass}">${statusText}</span></p>
            <p><strong>Thời gian:</strong> ${document.querySelector('input[name="thoiGianBatDau"]').value} - ${document.querySelector('input[name="thoiGianKetThuc"]').value}</p>
        </div>
    `;
    
    // Tạo modal hoặc alert
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết bàn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ${details}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Xóa modal sau khi đóng
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Auto submit form khi thay đổi cơ sở
document.addEventListener('DOMContentLoaded', function() {
    const coSoSelect = document.querySelector('select[name="maCoSo"]');
    if (coSoSelect && coSoSelect.value) {
        // Form sẽ tự động submit khi chọn cơ sở
        console.log('Cơ sở đã được chọn:', coSoSelect.value);
    }
});
</script>





