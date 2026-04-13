<?php
// File này hiển thị chi tiết đơn đặt bàn
// Biến $booking và $menuItems đã được truyền từ controller
// Include helper functions
require_once __DIR__ . '/NhanVienHelper.php';

if (!isset($booking) || !$booking) {
    $_SESSION['error_message'] = 'Không tìm thấy thông tin đơn đặt bàn.';
    header('Location: index.php?page=nhanvien&action=dashboard&section=bookings');
    exit;
}

// Tính tổng tiền
$tongTien = 0;
if (isset($menuItems) && !empty($menuItems)) {
    foreach ($menuItems as $item) {
        $tongTien += $item['SoLuong'] * $item['DonGia'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn đặt bàn #<?php echo htmlspecialchars($booking['MaDon']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            background: linear-gradient(135deg, #1B4E30 0%, #21A256 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-info h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .header-info p {
            opacity: 0.9;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .content {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem;
        }

        .detail-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #21A256;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #64748b;
        }

        .detail-value {
            color: #1e293b;
            font-weight: 500;
        }

        .menu-section {
            grid-column: 1 / -1;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
        }

        .menu-table {
            width: 100%;
            margin-top: 1rem;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .menu-table th,
        .menu-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .menu-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #475569;
        }

        .menu-table tr:hover {
            background: #f8fafc;
        }

        .price-highlight {
            color: #059669;
            font-weight: 600;
        }

        .total-section {
            background: linear-gradient(135deg, #1B4E30 0%, #21A256 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
            border-radius: 12px;
            margin: 1rem 0;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
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
            gap: 1rem;
            padding: 2rem;
            justify-content: center;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #21A256;
            color: white;
        }

        .btn-primary:hover {
            background: #1B8B47;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-info {
            background: #3b82f6;
            color: white;
        }

        .btn-info:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .empty-menu {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .notes-section {
            grid-column: 1 / -1;
            background: #fffbeb;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #f59e0b;
        }

        .notes-content {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #fbbf24;
            margin-top: 0.5rem;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .menu-table {
                font-size: 0.875rem;
            }

            .menu-table th,
            .menu-table td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Print styles */
        @media print {
            .header .back-btn,
            .action-buttons {
                display: none !important;
            }
            
            body {
                background: white;
            }
            
            .content {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-info">
                <h1>Đơn đặt bàn #<?php echo htmlspecialchars($booking['MaDon']); ?></h1>
                <p>Chi tiết thông tin đơn đặt bàn và món ăn</p>
            </div>
            <a href="index.php?page=nhanvien&action=dashboard&section=bookings" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="detail-grid">
                <!-- Thông tin khách hàng -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Thông tin khách hàng
                    </h3>
                    <div class="detail-row">
                        <span class="detail-label">Tên khách hàng:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['TenKH'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Số điện thoại:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['SDT'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['EmailKH'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Số lượng khách:</span>
                        <span class="detail-value"><?php echo number_format($booking['SoLuongKH']); ?> người</span>
                    </div>
                </div>

                <!-- Thông tin đặt bàn -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-check"></i>
                        Thông tin đặt bàn
                    </h3>
                    <div class="detail-row">
                        <span class="detail-label">Thời gian đặt:</span>
                        <span class="detail-value"><?php echo NhanVienHelper::formatDateTime($booking['ThoiGianBatDau']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Bàn đã đặt:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['DanhSachBan'] ?? 'Chưa chọn bàn'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Trạng thái:</span>
                        <span class="detail-value"><?php echo NhanVienHelper::getStatusBadge($booking['TrangThai']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ngày tạo đơn:</span>
                        <span class="detail-value"><?php echo NhanVienHelper::formatDateTime($booking['ThoiGianTao']); ?></span>
                    </div>
                    <?php if (!empty($booking['NhanVienXacNhan'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Nhân viên xác nhận:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['NhanVienXacNhan']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Danh sách món ăn -->
                <div class="menu-section">
                    <h3 class="section-title">
                        <i class="fas fa-utensils"></i>
                        Danh sách món ăn đã đặt
                    </h3>
                    <?php if (!empty($menuItems)): ?>
                        <table class="menu-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên món</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($item['TenMon'] ?? 'Món ăn #' . $item['MaMon']); ?></td>
                                        <td><?php echo number_format($item['SoLuong']); ?></td>
                                        <td class="price-highlight"><?php echo NhanVienHelper::formatCurrency($item['DonGia']); ?></td>
                                        <td class="price-highlight"><?php echo NhanVienHelper::formatCurrency($item['SoLuong'] * $item['DonGia']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                        <h3>Tổng: <?php echo NhanVienHelper::formatCurrency($tongTien); ?></h3>
                    <?php else: ?>
                        <div class="empty-menu">
                            <i class="fas fa-utensils" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                            <h4>Chưa có món ăn nào được đặt</h4>
                            <p>Đơn đặt bàn này chưa có thông tin về món ăn.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ghi chú -->
                <?php if (!empty($booking['GhiChu'])): ?>
                <div class="notes-section">
                    <h3 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Ghi chú
                    </h3>
                    <div class="notes-content">
                        <?php echo htmlspecialchars($booking['GhiChu']); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <?php if ($booking['TrangThai'] === 'cho_xac_nhan'): ?>
                    <button class="btn btn-primary" onclick="confirmBooking(<?php echo $booking['MaDon']; ?>)">
                        <i class="fas fa-check"></i>
                        Xác nhận đơn
                    </button>
                    <button class="btn btn-danger" onclick="cancelBooking(<?php echo $booking['MaDon']; ?>)">
                        <i class="fas fa-times"></i>
                        Hủy đơn
                    </button>
                <?php elseif ($booking['TrangThai'] === 'da_xac_nhan'): ?>
                    <button class="btn btn-warning" onclick="completeBooking(<?php echo $booking['MaDon']; ?>)">
                        <i class="fas fa-check-double"></i>
                        Hoàn thành
                    </button>
                <?php endif; ?>
                
                <?php if (!empty($menuItems)): ?>
                    <button class="btn btn-info" onclick="printKitchenSlip()">
                        <i class="fas fa-print"></i>
                        In phiếu bếp
                    </button>
                <?php endif; ?>
                
                <button class="btn btn-secondary" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i>
                    In đơn hàng
                </button>
            </div>
        </div>
    </div>

    <script>
        // Xác nhận đơn đặt bàn
        function confirmBooking(maDon) {
            if (confirm('Bạn có chắc chắn muốn xác nhận đơn đặt bàn #' + maDon + '?')) {
                updateBookingStatus(maDon, 'da_xac_nhan');
            }
        }

        // Hủy đơn đặt bàn
        function cancelBooking(maDon) {
            const reason = prompt('Lý do hủy đơn đặt bàn #' + maDon + ':');
            if (reason !== null && reason.trim() !== '') {
                updateBookingStatus(maDon, 'da_huy', reason);
            }
        }

        // Hoàn thành đơn đặt bàn
        function completeBooking(maDon) {
            if (confirm('Đánh dấu đơn đặt bàn #' + maDon + ' đã hoàn thành?')) {
                updateBookingStatus(maDon, 'hoan_thanh');
            }
        }

        // Cập nhật trạng thái đơn đặt bàn
        function updateBookingStatus(maDon, status, reason = '') {
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
            
            // Thêm redirect để quay về trang detail sau khi update
            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect_to_detail';
            redirectInput.value = maDon;
            form.appendChild(redirectInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        // In phiếu bếp
        function printKitchenSlip() {
            const kitchenWindow = window.open('', '_blank', 'width=800,height=600');
            const maDon = <?php echo json_encode($booking['MaDon']); ?>;
            const tenKH = <?php echo json_encode($booking['TenKH'] ?? 'N/A'); ?>;
            const thoiGian = <?php echo json_encode(NhanVienHelper::formatDateTime($booking['ThoiGianBatDau'])); ?>;
            const soKhach = <?php echo json_encode($booking['SoLuongKH']); ?>;
            const danhSachBan = <?php echo json_encode($booking['DanhSachBan'] ?? 'Chưa chọn bàn'); ?>;
            
            <?php if (!empty($menuItems)): ?>
            const menuItems = <?php echo json_encode($menuItems); ?>;
            <?php else: ?>
            const menuItems = [];
            <?php endif; ?>

            let kitchenHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Phiếu bếp - Đơn #${maDon}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                        .header h1 { margin: 0; font-size: 24px; }
                        .info { margin-bottom: 20px; }
                        .info div { margin: 5px 0; }
                        .menu-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        .menu-table th, .menu-table td { border: 1px solid #000; padding: 8px; text-align: left; }
                        .menu-table th { background: #f0f0f0; font-weight: bold; }
                        .notes { margin-top: 20px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>PHIẾU BẾP</h1>
                        <div>Đơn đặt bàn #${maDon}</div>
                    </div>
                    
                    <div class="info">
                        <div><strong>Khách hàng:</strong> ${tenKH}</div>
                        <div><strong>Thời gian:</strong> ${thoiGian}</div>
                        <div><strong>Số khách:</strong> ${soKhach} người</div>
                        <div><strong>Bàn:</strong> ${danhSachBan}</div>
                        <div><strong>Thời gian in:</strong> ${new Date().toLocaleString('vi-VN')}</div>
                    </div>
            `;

            if (menuItems.length > 0) {
                kitchenHTML += `
                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên món</th>
                                <th>Số lượng</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                menuItems.forEach((item, index) => {
                    kitchenHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.TenMon || 'Món ăn #' + item.MaMon}</td>
                            <td><strong>${item.SoLuong}</strong></td>
                            <td></td>
                        </tr>
                    `;
                });

                kitchenHTML += `
                        </tbody>
                    </table>
                `;
            } else {
                kitchenHTML += '<div class="notes">Không có món ăn nào được đặt.</div>';
            }

            <?php if (!empty($booking['GhiChu'])): ?>
            kitchenHTML += `
                <div class="notes">
                    <strong>Ghi chú đặc biệt:</strong><br>
                    <?php echo addslashes(htmlspecialchars($booking['GhiChu'])); ?>
                </div>
            `;
            <?php endif; ?>

            kitchenHTML += `
                </body>
                </html>
            `;

            kitchenWindow.document.write(kitchenHTML);
            kitchenWindow.document.close();
            kitchenWindow.focus();
            kitchenWindow.print();
        }

        // Auto-focus và hiệu ứng
        document.addEventListener('DOMContentLoaded', function() {
            // Hiệu ứng xuất hiện
            const sections = document.querySelectorAll('.detail-section, .menu-section, .notes-section');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>