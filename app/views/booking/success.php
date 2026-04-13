<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt bàn thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .success-container { max-width: 600px; margin: 50px auto; padding: 30px; }
        .success-icon { font-size: 5rem; color: #28a745; margin-bottom: 20px; }
        .info-card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); margin: 20px 0; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; margin: 10px 0; }
        .status-paid { background: #d1edff; color: #0066cc; }
        .btn-custom { border-radius: 25px; padding: 12px 30px; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
    
    <div class="success-container text-center">
        
        <!-- Icon thành công -->
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        
        <h1 class="text-success mb-3">Đặt bàn thành công!</h1>
        <p class="lead text-muted mb-4">Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi</p>
        
        <?php if ($booking): ?>
        
        <!-- Thông tin đặt bàn -->
        <div class="info-card text-start">
            <h5 class="text-primary mb-3">
                <i class="bi bi-receipt"></i>
                Thông tin đặt bàn
            </h5>
            
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Mã đặt bàn:</strong></div>
                <div class="col-sm-8">#DB<?= $booking['MaDon'] ?></div>
            </div>
            
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Khách hàng:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['TenKH']) ?></div>
            </div>
            
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Số điện thoại:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['SDT']) ?></div>
            </div>
            
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Chi nhánh:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['TenCoSo']) ?></div>
            </div>
            
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Thời gian:</strong></div>
                <div class="col-sm-8"><?= date('d/m/Y \l\ú\c H:i', strtotime($booking['ThoiGianBatDau'])) ?></div>
            </div>
            
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Số người:</strong></div>
                <div class="col-sm-8"><?= $booking['SoLuongKH'] ?> người</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-4"><strong>Trạng thái:</strong></div>
                <div class="col-sm-8">
                    <?php 
                    switch($booking['TrangThai']) {
                        case 'da_xac_nhan':
                            echo '<span class="status-badge status-paid">Đã thanh toán & Xác nhận</span>';
                            break;
                        case 'cho_xac_nhan':
                            echo '<span class="status-badge bg-warning text-dark">Chờ thanh toán</span>';
                            break;
                        case 'hoan_thanh':
                            echo '<span class="status-badge bg-success text-white">Hoàn thành</span>';
                            break;
                        case 'da_huy':
                            echo '<span class="status-badge bg-danger text-white">Đã hủy</span>';
                            break;
                        default:
                            echo '<span class="status-badge bg-secondary text-white">Đang xử lý</span>';
                    }
                    ?>
                </div>
            </div>
            
            <?php if (!empty($booking['GhiChu'])): ?>
            <div class="row">
                <div class="col-sm-4"><strong>Ghi chú:</strong></div>
                <div class="col-sm-8"><?= nl2br(htmlspecialchars($booking['GhiChu'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php endif; ?>
        
        <!-- Hướng dẫn tiếp theo -->
        <div class="info-card">
            <h5 class="text-info mb-3">
                <i class="bi bi-info-circle-fill"></i>
                Hướng dẫn tiếp theo
            </h5>
            
            <?php if ($booking && $booking['TrangThai'] === 'da_xac_nhan'): ?>
            <ul class="text-start list-unstyled">
                <li class="mb-2">
                    <i class="bi bi-check text-success me-2"></i>
                    Bàn của bạn đã được xác nhận và giữ chỗ
                </li>
                <li class="mb-2">
                    <i class="bi bi-clock text-primary me-2"></i>
                    Vui lòng có mặt tại nhà hàng đúng giờ đã đặt
                </li>
                <li class="mb-2">
                    <i class="bi bi-telephone text-info me-2"></i>
                    Hotline hỗ trợ: <strong>0922.782.387</strong>
                </li>
            </ul>
            <?php else: ?>
            <ul class="text-start list-unstyled">
                <li class="mb-2">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Bạn cần hoàn thành thanh toán để xác nhận đặt bàn
                </li>
                <li class="mb-2">
                    <i class="bi bi-credit-card text-primary me-2"></i>
                    <a href="?page=booking&action=payment&id=<?= $booking['MaDon'] ?>">
                        Thanh toán ngay
                    </a>
                </li>
            </ul>
            <?php endif; ?>
        </div>
        
        <!-- Buttons -->
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <?php if ($booking && $booking['TrangThai'] === 'cho_xac_nhan'): ?>
            <a href="?page=booking&action=payment&id=<?= $booking['MaDon'] ?>" 
               class="btn btn-success btn-custom">
                <i class="bi bi-credit-card me-2"></i>
                Thanh toán ngay
            </a>
            <?php endif; ?>
            
            <a href="?page=booking&action=create" class="btn btn-primary btn-custom">
                <i class="bi bi-plus-circle me-2"></i>
                Đặt bàn mới
            </a>
            
            <a href="?page=home" class="btn btn-outline-secondary btn-custom">
                <i class="bi bi-house me-2"></i>
                Về trang chủ
            </a>
        </div>
        
        <!-- Footer thông tin liên hệ -->
        <div class="text-center mt-5">
            <p class="text-muted mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>
                Hệ thống nhà hàng cao cấp
            </p>
            <p class="small text-muted">
                Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi!
            </p>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
