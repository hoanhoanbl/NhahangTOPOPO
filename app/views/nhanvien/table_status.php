<?php

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
    <h4 class="mb-4">Quản lý trạng thái bàn theo thời gian thực</h4>
    
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
    
    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> <?=$_SESSION['info_message']?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['info_message']); ?>
    <?php endif; ?>
    
    <!-- Form chọn thời gian -->
    <form method="GET" class="mb-4">
        <input type="hidden" name="page" value="nhanvien">
        <input type="hidden" name="action" value="dashboard">
        <input type="hidden" name="section" value="table_status">
        
        <div class="row">
            <!-- Hiển thị thông tin cơ sở của nhân viên -->
            <div class="col-md-9">
                <label class="form-label">Cơ sở làm việc:</label>
                <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong><?=htmlspecialchars($thongTinCoSo['TenCoSo'] ?? '')?></strong>
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="fas fa-sync-alt"></i> Làm mới
                </button>
            </div>
        </div>
    </form>

    <!-- Hiển thị thông tin cơ sở và logic trạng thái -->
    <div class="alert alert-info">
        <strong>Cơ sở:</strong> <?=htmlspecialchars($thongTinCoSo['TenCoSo'] ?? '')?> | 
        <strong>Thời gian hiện tại:</strong> <?=date('d/m/Y H:i')?><br>
        <small><i class="fas fa-info-circle"></i> Bàn được coi là "Đã đặt" nếu có đơn đặt bàn trong vòng 2 giờ tới (tránh đặt trùng thời gian)</small>
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
                                            onclick="updateTableStatus(<?=$ban['MaBan']?>, 'da_dat')"
                                            title="Đánh dấu bàn đã đặt">
                                        <i class="fas fa-user-plus"></i> Đánh dấu đã đặt
                                    </button>
                                <?php else: ?>
                                    <!-- Bàn đã đặt - hiển thị nút đánh dấu trống -->
                                    <button class="btn btn-outline-success btn-sm" 
                                            onclick="updateTableStatus(<?=$ban['MaBan']?>, 'trong')"
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
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5><?=$thongKe['tongBan']?></h5>
                        <p class="mb-0">Tổng số bàn</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5><?=$thongKe['banTrong']?></h5>
                        <p class="mb-0">Bàn trống</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5><?=$thongKe['banDaDat']?></h5>
                        <p class="mb-0">Bàn đã đặt</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Form ẩn để cập nhật trạng thái -->
<form id="updateStatusForm" method="POST" action="index.php?page=nhanvien&action=update_table_status" style="display: none;">
    <input type="hidden" name="maBan" id="updateMaBan">
    <input type="hidden" name="trangThai" id="updateTrangThai">
</form>

<script>
function updateTableStatus(maBan, trangThai) {
    const action = trangThai === 'trong' ? 'đánh dấu trống' : 'đánh dấu đã đặt';
    const message = `Bạn có chắc chắn muốn ${action} bàn này?`;
    
    if (confirm(message)) {
        // Hiển thị loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        button.disabled = true;
        document.getElementById('updateMaBan').value = maBan;
        document.getElementById('updateTrangThai').value = trangThai;
        document.getElementById('updateStatusForm').submit();
    }
}

function showTableDetails(maBan, tenBan, trangThai) {
    // Gọi AJAX để lấy chi tiết bàn từ server
    fetch(`index.php?page=nhanvien&action=get_table_details&maBan=${maBan}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            const statusText = trangThai === 'trong' ? 'Trống' : 'Đã đặt';
            const statusClass = trangThai === 'trong' ? 'success' : 'warning';
            
            const details = `
                <div class="table-details">
                    <h6><i class="fas fa-utensils"></i> Chi tiết bàn</h6>
                    <p><strong>Mã bàn:</strong> ${maBan}</p>
                    <p><strong>Tên bàn:</strong> ${tenBan}</p>
                    <p><strong>Sức chứa:</strong> ${data.SucChua || 'N/A'} người</p>
                    <p><strong>Trạng thái:</strong> <span class="badge bg-${statusClass}">${statusText}</span></p>
                    <p><strong>Thời gian hiện tại:</strong> <?=date('d/m/Y H:i')?></p>
                </div>
            `;
            
            // Tạo modal để hiển thị chi tiết
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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi lấy thông tin bàn');
        });
}

// Đảm bảo thời gian form được cập nhật khi submit
document.addEventListener('DOMContentLoaded', function() {
    console.log('Table status page loaded successfully');
});
</script>
