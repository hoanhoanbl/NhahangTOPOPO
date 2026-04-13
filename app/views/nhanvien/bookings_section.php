<?php
// File này được include trong dashboard.php để hiển thị section đơn đặt bàn
// Dữ liệu được truyền từ controller qua biến $bookingsData

// Lấy dữ liệu bookings từ controller
$bookingsList = $bookingsData['bookingsList'] ?? [];
$totalBookings = $bookingsData['totalBookings'] ?? 0;
$totalPages = $bookingsData['totalPages'] ?? 0;
$currentPage = $bookingsData['currentPage'] ?? 1;
$limit = $bookingsData['limit'] ?? 10;

// Lấy các filter parameters
$statusFilter = $_GET['status_filter'] ?? 'all';
$timeFilter = $_GET['time_filter'] ?? 'hom_nay';
$searchKeyword = $_GET['search'] ?? '';
?>

<style>
/* Styles cho phần đơn đặt bàn */
.booking-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.filter-row {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: #374151;
    font-size: 0.9rem;
}

.filter-input, .filter-select {
    padding: 0.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.9rem;
    min-width: 150px;
}

.filter-input:focus, .filter-select:focus {
    outline: none;
    border-color: #21A256;
}

.filter-btn {
    background: #21A256;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    margin-top: auto;
}

.filter-btn:hover {
    background: #1B8B47;
}

.bookings-table {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: between;
    align-items: center;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e293b;
}

.table-stats {
    color: #64748b;
    font-size: 0.9rem;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

th {
    background: #f8fafc;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

tr:hover {
    background: #f8fafc;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.pending {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.confirmed {
    background: #dcfce7;
    color: #16a34a;
}

.status-badge.cancelled {
    background: #fecaca;
    color: #dc2626;
}

.status-badge.completed {
    background: #dbeafe;
    color: #2563eb;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-sm:hover {
    opacity: 0.8;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: #f8fafc;
}

.pagination a, .pagination span {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    text-decoration: none;
    color: #374151;
}

.pagination a:hover {
    background: #f3f4f6;
}

.pagination .current {
    background: #21A256;
    color: white;
    border-color: #21A256;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #cbd5e1;
}

.booking-detail {
    font-size: 0.875rem;
    line-height: 1.4;
}

.customer-info {
    font-weight: 500;
    color: #1e293b;
}

.booking-time {
    color: #059669;
    font-weight: 500;
}

.table-count {
    color: #6b7280;
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-input, .filter-select {
        min-width: auto;
        width: 100%;
    }
    
    .table-container {
        font-size: 0.875rem;
    }
    
    th, td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<!-- Booking Filters -->
<div class="booking-filters">
    <form method="GET" action="">
        <input type="hidden" name="page" value="nhanvien">
        <input type="hidden" name="action" value="dashboard">
        <input type="hidden" name="section" value="bookings">
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="time_filter">Thời gian</label>
                <select id="time_filter" name="time_filter" class="filter-select">
                    <option value="hom_nay" <?php echo $timeFilter === 'hom_nay' ? 'selected' : ''; ?>>Đơn hôm nay</option>
                    <option value="dat_truoc" <?php echo $timeFilter === 'dat_truoc' ? 'selected' : ''; ?>>Đơn đặt trước</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status_filter">Trạng thái</label>
                <select id="status_filter" name="status_filter" class="filter-select">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                    <option value="cho_xac_nhan" <?php echo $statusFilter === 'cho_xac_nhan' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                    <option value="da_xac_nhan" <?php echo $statusFilter === 'da_xac_nhan' ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="da_huy" <?php echo $statusFilter === 'da_huy' ? 'selected' : ''; ?>>Đã hủy</option>
                    <option value="hoan_thanh" <?php echo $statusFilter === 'hoan_thanh' ? 'selected' : ''; ?>>Hoàn thành</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="search">Tìm kiếm</label>
                <input type="text" id="search" name="search" class="filter-input" 
                       placeholder="Tên KH, SĐT, Mã đơn..." 
                       value="<?php echo htmlspecialchars($searchKeyword); ?>">
            </div>
            
            <button type="submit" class="filter-btn">
                <i class="fas fa-search"></i>
                Tìm
            </button>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="bookings-table">
    <div class="table-header">
        <h3 class="table-title">Danh sách đơn đặt bàn</h3>
    </div>
    
    <div class="table-container">
        <?php if (!empty($bookingsList)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Thời gian băt đầu</th>
                        <th>Số khách</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookingsList as $booking): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo htmlspecialchars($booking['MaDon']); ?></strong>
                            </td>
                            <td>
                                <div class="booking-detail">
                                    <div class="customer-info">
                                        <?php echo htmlspecialchars($booking['TenKH'] ?? 'N/A'); ?>
                                    </div>
                                    
                                </div>
                            </td>
                            <td>
                                <div class="booking-time">
                                    <i class="fas fa-clock"></i>
                                    <?php echo NhanVienHelper::formatDateTime($booking['ThoiGianBatDau']); ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo number_format($booking['SoLuongKH']); ?></strong> người
                            </td>
                            
                            <td>
                                <?php echo NhanVienHelper::getStatusBadge($booking['TrangThai']); ?>
                            </td>
                           
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?page=nhanvien&action=viewBookingDetail&id=<?php echo $booking['MaDon']; ?>" class="btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                        Xem
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Không có đơn đặt bàn nào</h3>
                <p>Hiện tại chưa có đơn đặt bàn nào phù hợp với bộ lọc của bạn.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=nhanvien&action=dashboard&section=bookings&booking_page=<?php echo $currentPage - 1; ?>&status_filter=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchKeyword); ?>&time_filter=<?php echo urlencode($timeFilter); ?>">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <?php if ($i === $currentPage): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=nhanvien&action=dashboard&section=bookings&booking_page=<?php echo $i; ?>&status_filter=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchKeyword); ?>&time_filter=<?php echo urlencode($timeFilter); ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=nhanvien&action=dashboard&section=bookings&booking_page=<?php echo $currentPage + 1; ?>&status_filter=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchKeyword); ?>&time_filter=<?php echo urlencode($timeFilter); ?>">
                    Sau <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// JavaScript functions cho các thao tác đơn đặt bàn
function viewBookingDetail(maDon) {
    // Chuyển hướng đến trang chi tiết
    window.location.href = 'index.php?page=nhanvien&action=viewBookingDetail&id=' + maDon;
}

// Tự động submit form khi thay đổi thời gian
document.getElementById('time_filter').addEventListener('change', function() {
    this.closest('form').submit();
});

// Tự động submit form khi thay đổi trạng thái
document.getElementById('status_filter').addEventListener('change', function() {
    this.closest('form').submit();
});

function confirmBooking(maDon) {
    if (confirm('Bạn có chắc chắn muốn xác nhận đơn đặt bàn #' + maDon + '?')) {
        // TODO: Implement confirm booking
        updateBookingStatus(maDon, 'da_xac_nhan');
    }
}

function cancelBooking(maDon) {
    const reason = prompt('Lý do hủy đơn đặt bàn #' + maDon + ':');
    if (reason !== null && reason.trim() !== '') {
        // TODO: Implement cancel booking
        updateBookingStatus(maDon, 'da_huy', reason);
    }
}

function completeBooking(maDon) {
    if (confirm('Đánh dấu đơn đặt bàn #' + maDon + ' đã hoàn thành?')) {
        // TODO: Implement complete booking
        updateBookingStatus(maDon, 'hoan_thanh');
    }
}

function updateBookingStatus(maDon, status, reason = '') {
    // Tạo form ẩn để submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?page=nhanvien&action=updateBookingStatus';
    
    const maDonInput = document.createElement('input');
    maDonInput.type = 'hidden';
    maDonInput.name = 'maDon';
    maDonInput.value = maDon;
    form.appendChild(maDonInput);
    
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);
    
    if (reason) {
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}
</script>