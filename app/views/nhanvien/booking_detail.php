<?php
// File nÃ y hiá»ƒn thá»‹ chi tiáº¿t Ä‘Æ¡n Ä‘áº·t bÃ n
// Biáº¿n $booking vÃ  $menuItems Ä‘Ã£ Ä‘Æ°á»£c truyá»n tá»« controller
// Include helper functions
require_once __DIR__ . '/NhanVienHelper.php';

if (!isset($booking) || !$booking) {
    $_SESSION['error_message'] = 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin Ä‘Æ¡n Ä‘áº·t bÃ n.';
    header('Location: index.php?page=nhanvien&action=dashboard&section=bookings');
    exit;
}

// TÃ­nh tá»•ng tiá»n
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
    <title>Chi tiáº¿t Ä‘Æ¡n Ä‘áº·t bÃ n #<?php echo htmlspecialchars($booking['MaDon']); ?></title>
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


        .timeline-section {
            grid-column: 1 / -1;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #2563eb;
        }

        .timeline-list {
            position: relative;
            margin-top: 1rem;
            padding-left: 1rem;
        }

        .timeline-list::before {
            content: '';
            position: absolute;
            top: 0.25rem;
            bottom: 0.25rem;
            left: 0.4rem;
            width: 2px;
            background: #cbd5e1;
        }

        .timeline-item {
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 1rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.35rem;
            width: 0.8rem;
            height: 0.8rem;
            border-radius: 999px;
            background: #2563eb;
            border: 2px solid #dbeafe;
        }

        .timeline-meta {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.125rem;
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
                <h1>ÄÆ¡n Ä‘áº·t bÃ n #<?php echo htmlspecialchars($booking['MaDon']); ?></h1>
                <p>Chi tiáº¿t thÃ´ng tin Ä‘Æ¡n Ä‘áº·t bÃ n vÃ  mÃ³n Äƒn</p>
            </div>
            <a href="index.php?page=nhanvien&action=dashboard&section=bookings" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Quay láº¡i
            </a>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="detail-grid">
                <!-- ThÃ´ng tin khÃ¡ch hÃ ng -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        ThÃ´ng tin khÃ¡ch hÃ ng
                    </h3>
                    <div class="detail-row">
                        <span class="detail-label">TÃªn khÃ¡ch hÃ ng:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['TenKH'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sá»‘ Ä‘iá»‡n thoáº¡i:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['SDT'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['EmailKH'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sá»‘ lÆ°á»£ng khÃ¡ch:</span>
                        <span class="detail-value"><?php echo number_format($booking['SoLuongKH']); ?> ngÆ°á»i</span>
                    </div>
                </div>

                <!-- ThÃ´ng tin Ä‘áº·t bÃ n -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-check"></i>
                        ThÃ´ng tin Ä‘áº·t bÃ n
                    </h3>
                    <div class="detail-row">
                        <span class="detail-label">Thá»i gian Ä‘áº·t:</span>
                        <span class="detail-value"><?php echo NhanVienHelper::formatDateTime($booking['ThoiGianBatDau']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">BÃ n Ä‘Ã£ Ä‘áº·t:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['DanhSachBan'] ?? 'ChÆ°a chá»n bÃ n'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tráº¡ng thÃ¡i:</span>
                        <span class="detail-value"><?php echo NhanVienHelper::getStatusBadge($booking['TrangThai']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">NgÃ y táº¡o Ä‘Æ¡n:</span>
                        <span class="detail-value"><?php echo NhanVienHelper::formatDateTime($booking['ThoiGianTao']); ?></span>
                    </div>
                    <?php if (!empty($booking['NhanVienXacNhan'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">NhÃ¢n viÃªn xÃ¡c nháº­n:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['NhanVienXacNhan']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Danh sÃ¡ch mÃ³n Äƒn -->
                <div class="menu-section">
                    <h3 class="section-title">
                        <i class="fas fa-utensils"></i>
                        Danh sÃ¡ch mÃ³n Äƒn Ä‘Ã£ Ä‘áº·t
                    </h3>
                    <?php if (!empty($menuItems)): ?>
                        <table class="menu-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>TÃªn mÃ³n</th>
                                    <th>Sá»‘ lÆ°á»£ng</th>
                                    <th>ÄÆ¡n giÃ¡</th>
                                    <th>ThÃ nh tiá»n</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($item['TenMon'] ?? 'MÃ³n Äƒn #' . $item['MaMon']); ?></td>
                                        <td><?php echo number_format($item['SoLuong']); ?></td>
                                        <td class="price-highlight"><?php echo NhanVienHelper::formatCurrency($item['DonGia']); ?></td>
                                        <td class="price-highlight"><?php echo NhanVienHelper::formatCurrency($item['SoLuong'] * $item['DonGia']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                        <h3>Tá»•ng: <?php echo NhanVienHelper::formatCurrency($tongTien); ?></h3>
                    <?php else: ?>
                        <div class="empty-menu">
                            <i class="fas fa-utensils" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                            <h4>ChÆ°a cÃ³ mÃ³n Äƒn nÃ o Ä‘Æ°á»£c Ä‘áº·t</h4>
                            <p>ÄÆ¡n Ä‘áº·t bÃ n nÃ y chÆ°a cÃ³ thÃ´ng tin vá» mÃ³n Äƒn.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ghi chÃº -->
                <?php if (!empty($booking['GhiChu'])): ?>
                <div class="notes-section">
                    <h3 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Ghi chÃº
                    </h3>
                    <div class="notes-content">
                        <?php echo htmlspecialchars($booking['GhiChu']); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>


                <div class="timeline-section">
                    <h3 class="section-title">
                        <i class="fas fa-clock-rotate-left"></i>
                        Lịch sử booking
                    </h3>
                    <?php if (!empty($bookingTimeline)): ?>
                        <div class="timeline-list">
                            <?php foreach ($bookingTimeline as $event): ?>
                                <div class="timeline-item">
                                    <div><strong><?php echo htmlspecialchars($event['Action'] ?? 'status_changed'); ?></strong></div>
                                    <div class="timeline-meta">
                                        <?php echo !empty($event['CreatedAt']) ? NhanVienHelper::formatDateTime($event['CreatedAt']) : 'N/A'; ?>
                                            <?php if (!empty($event['ActorName'])): ?> - <?php echo htmlspecialchars($event['ActorName']); ?><?php endif; ?>
                                        <?php if (!empty($event['ActorType'])): ?> (<?php echo htmlspecialchars($event['ActorType']); ?>)<?php endif; ?>
                                    </div>
                                    <?php if (!empty($event['FromStatus']) || !empty($event['ToStatus'])): ?>
                                        <div class="timeline-meta"><?php echo htmlspecialchars(($event['FromStatus'] ?: 'new') . ' -> ' . ($event['ToStatus'] ?: 'new')); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($event['Note'])): ?>
                                        <div><?php echo nl2br(htmlspecialchars($event['Note'])); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-menu">
                            <i class="fas fa-clock-rotate-left" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                            <p>Chưa có lịch sử booking nào được ghi nhận.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <!-- Action Buttons -->
            <div class="action-buttons">
                <?php if ($booking['TrangThai'] === 'cho_xac_nhan'): ?>
                    <button class="btn btn-primary" onclick="confirmBooking(<?php echo $booking['MaDon']; ?>)">
                        <i class="fas fa-check"></i>
                        XÃ¡c nháº­n Ä‘Æ¡n
                    </button>
                    <button class="btn btn-danger" onclick="cancelBooking(<?php echo $booking['MaDon']; ?>)">
                        <i class="fas fa-times"></i>
                        Há»§y Ä‘Æ¡n
                    </button>
                <?php elseif ($booking['TrangThai'] === 'da_xac_nhan'): ?>
                    <button class="btn btn-warning" onclick="completeBooking(<?php echo $booking['MaDon']; ?>)">
                        <i class="fas fa-check-double"></i>
                        HoÃ n thÃ nh
                    </button>
                <?php endif; ?>
                
                <?php if (!empty($menuItems)): ?>
                    <button class="btn btn-info" onclick="printKitchenSlip()">
                        <i class="fas fa-print"></i>
                        In phiáº¿u báº¿p
                    </button>
                <?php endif; ?>
                
                <button class="btn btn-secondary" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i>
                    In Ä‘Æ¡n hÃ ng
                </button>
            </div>
        </div>
    </div>

    <script>
        // XÃ¡c nháº­n Ä‘Æ¡n Ä‘áº·t bÃ n
        function confirmBooking(maDon) {
            if (confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ¡c nháº­n Ä‘Æ¡n Ä‘áº·t bÃ n #' + maDon + '?')) {
                updateBookingStatus(maDon, 'da_xac_nhan');
            }
        }

        // Há»§y Ä‘Æ¡n Ä‘áº·t bÃ n
        function cancelBooking(maDon) {
            const reason = prompt('LÃ½ do há»§y Ä‘Æ¡n Ä‘áº·t bÃ n #' + maDon + ':');
            if (reason !== null && reason.trim() !== '') {
                updateBookingStatus(maDon, 'da_huy', reason);
            }
        }

        // HoÃ n thÃ nh Ä‘Æ¡n Ä‘áº·t bÃ n
        function completeBooking(maDon) {
            if (confirm('ÄÃ¡nh dáº¥u Ä‘Æ¡n Ä‘áº·t bÃ n #' + maDon + ' Ä‘Ã£ hoÃ n thÃ nh?')) {
                updateBookingStatus(maDon, 'hoan_thanh');
            }
        }

        // Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n Ä‘áº·t bÃ n
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
            
            // ThÃªm redirect Ä‘á»ƒ quay vá» trang detail sau khi update
            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect_to_detail';
            redirectInput.value = maDon;
            form.appendChild(redirectInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        // In phiáº¿u báº¿p
        function printKitchenSlip() {
            const kitchenWindow = window.open('', '_blank', 'width=800,height=600');
            const maDon = <?php echo json_encode($booking['MaDon']); ?>;
            const tenKH = <?php echo json_encode($booking['TenKH'] ?? 'N/A'); ?>;
            const thoiGian = <?php echo json_encode(NhanVienHelper::formatDateTime($booking['ThoiGianBatDau'])); ?>;
            const soKhach = <?php echo json_encode($booking['SoLuongKH']); ?>;
            const danhSachBan = <?php echo json_encode($booking['DanhSachBan'] ?? 'ChÆ°a chá»n bÃ n'); ?>;
            
            <?php if (!empty($menuItems)): ?>
            const menuItems = <?php echo json_encode($menuItems); ?>;
            <?php else: ?>
            const menuItems = [];
            <?php endif; ?>

            let kitchenHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Phiáº¿u báº¿p - ÄÆ¡n #${maDon}</title>
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
                        <h1>PHIáº¾U Báº¾P</h1>
                        <div>ÄÆ¡n Ä‘áº·t bÃ n #${maDon}</div>
                    </div>
                    
                    <div class="info">
                        <div><strong>KhÃ¡ch hÃ ng:</strong> ${tenKH}</div>
                        <div><strong>Thá»i gian:</strong> ${thoiGian}</div>
                        <div><strong>Sá»‘ khÃ¡ch:</strong> ${soKhach} ngÆ°á»i</div>
                        <div><strong>BÃ n:</strong> ${danhSachBan}</div>
                        <div><strong>Thá»i gian in:</strong> ${new Date().toLocaleString('vi-VN')}</div>
                    </div>
            `;

            if (menuItems.length > 0) {
                kitchenHTML += `
                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>TÃªn mÃ³n</th>
                                <th>Sá»‘ lÆ°á»£ng</th>
                                <th>Ghi chÃº</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                menuItems.forEach((item, index) => {
                    kitchenHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.TenMon || 'MÃ³n Äƒn #' + item.MaMon}</td>
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
                kitchenHTML += '<div class="notes">KhÃ´ng cÃ³ mÃ³n Äƒn nÃ o Ä‘Æ°á»£c Ä‘áº·t.</div>';
            }

            <?php if (!empty($booking['GhiChu'])): ?>
            kitchenHTML += `
                <div class="notes">
                    <strong>Ghi chÃº Ä‘áº·c biá»‡t:</strong><br>
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

        // Auto-focus vÃ  hiá»‡u á»©ng
        document.addEventListener('DOMContentLoaded', function() {
            // Hiá»‡u á»©ng xuáº¥t hiá»‡n
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
