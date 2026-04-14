<?php
$statusLabel = 'Đang xử lý';
$statusClass = 'bg-secondary text-white';

if (!empty($booking['TrangThai'])) {
    switch ($booking['TrangThai']) {
        case 'da_xac_nhan':
            $statusLabel = 'Đã xác nhận';
            $statusClass = 'bg-primary text-white';
            break;
        case 'cho_xac_nhan':
            $statusLabel = 'Chờ xác nhận';
            $statusClass = 'bg-warning text-dark';
            break;
        case 'hoan_thanh':
            $statusLabel = 'Hoàn thành';
            $statusClass = 'bg-success text-white';
            break;
        case 'da_huy':
            $statusLabel = 'Đã hủy';
            $statusClass = 'bg-danger text-white';
            break;
    }
}

$timeline = $bookingHistory['timeline'] ?? [];
$canCancel = $booking && in_array($booking['TrangThai'] ?? '', ['cho_xac_nhan', 'da_xac_nhan'], true);
$title = 'Chi tiết đặt bàn';
$additional_head = <<<'HTML'
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .booking-success-page {
        background: #f7f8fb;
        padding: 40px 16px 64px;
    }
    .success-container { max-width: 860px; margin: 0 auto; padding: 24px 0; }
    .success-icon { font-size: 4.5rem; color: #198754; margin-bottom: 20px; }
    .info-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,.08); margin: 20px 0; }
    .status-badge { display: inline-block; padding: 8px 16px; border-radius: 999px; font-weight: 600; }
    .btn-custom { border-radius: 999px; padding: 12px 24px; font-weight: 600; }
    .timeline { position: relative; margin-top: 12px; }
    .timeline::before { content: ''; position: absolute; left: 10px; top: 6px; bottom: 6px; width: 2px; background: #dfe3e8; }
    .timeline-item { position: relative; padding-left: 34px; margin-bottom: 16px; }
    .timeline-dot { position: absolute; left: 3px; top: 6px; width: 16px; height: 16px; border-radius: 50%; background: #198754; border: 3px solid #d1fae5; }
    .timeline-meta { font-size: .875rem; color: #6c757d; }
    .cancel-box { background: #fff8f8; border: 1px solid #f5c2c7; border-radius: 16px; padding: 20px; }
</style>
HTML;
$additional_scripts = <<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
HTML;
?>

<section class="booking-success-page">
    <div class="success-container text-center">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
        </div>

        <h1 class="text-success mb-3">Đặt bàn thành công!</h1>
        <p class="lead text-muted mb-4">Thông tin booking của bạn đã được lưu vào hệ thống.</p>

        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if ($booking): ?>
        <div class="info-card text-start">
            <h5 class="text-primary mb-3"><i class="fa-solid fa-receipt me-2" aria-hidden="true"></i>Thông tin đặt bàn</h5>

            <div class="row mb-2">
                <div class="col-sm-4"><strong>Mã đặt bàn:</strong></div>
                <div class="col-sm-8">#DB<?= (int)$booking['MaDon'] ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Khách hàng:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['TenKH'] ?? '') ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Số điện thoại:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['SDT'] ?? '') ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Chi nhánh:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['TenCoSo'] ?? '') ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Thời gian:</strong></div>
                <div class="col-sm-8"><?= !empty($booking['ThoiGianBatDau']) ? date('d/m/Y H:i', strtotime($booking['ThoiGianBatDau'])) : '-' ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Số người:</strong></div>
                <div class="col-sm-8"><?= (int)($booking['SoLuongKH'] ?? 0) ?> người</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><strong>Bàn:</strong></div>
                <div class="col-sm-8"><?= htmlspecialchars($booking['DanhSachBan'] ?? 'Chưa gán bàn') ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4"><strong>Trạng thái:</strong></div>
                <div class="col-sm-8"><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span></div>
            </div>
            <?php if (!empty($booking['GhiChu'])): ?>
            <div class="row">
                <div class="col-sm-4"><strong>Ghi chú:</strong></div>
                <div class="col-sm-8"><?= nl2br(htmlspecialchars($booking['GhiChu'])) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="info-card text-start">
            <h5 class="text-info mb-3"><i class="fa-solid fa-clock-rotate-left me-2" aria-hidden="true"></i>Lịch sử booking</h5>
            <?php if (!empty($timeline)): ?>
                <div class="timeline">
                    <?php foreach ($timeline as $event): ?>
                        <div class="timeline-item">
                            <span class="timeline-dot"></span>
                            <div class="fw-semibold"><?= htmlspecialchars($event['action'] ?? '') ?></div>
                            <div class="timeline-meta">
                                <?= !empty($event['createdAt']) ? date('d/m/Y H:i', strtotime($event['createdAt'])) : '-' ?>
                                <?php if (!empty($event['actorName'])): ?> - <?= htmlspecialchars($event['actorName']) ?><?php endif; ?>
                                <?php if (!empty($event['actorType'])): ?> (<?= htmlspecialchars($event['actorType']) ?>)<?php endif; ?>
                            </div>
                            <?php if (!empty($event['note'])): ?>
                                <div><?= nl2br(htmlspecialchars($event['note'])) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-muted">Chưa có lịch sử booking để hiển thị.</div>
            <?php endif; ?>
        </div>

        <?php if ($canCancel): ?>
        <div class="info-card text-start">
            <div class="cancel-box">
                <h5 class="text-danger mb-3"><i class="fa-solid fa-ban me-2" aria-hidden="true"></i>Hủy đặt bàn</h5>
                <form method="POST" action="?page=booking&action=cancel" class="row g-3">
                    <input type="hidden" name="booking_id" value="<?= (int)$booking['MaDon'] ?>">
                    <div class="col-md-6">
                        <label class="form-label">Xác thực số điện thoại</label>
                        <input type="text" class="form-control" name="customer_phone_verify" required placeholder="Nhập đúng số điện thoại đã đặt bàn">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lý do hủy</label>
                        <input type="text" class="form-control" name="cancel_reason" required placeholder="Ví dụ: thay đổi kế hoạch">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-outline-danger btn-custom">Hủy đặt bàn</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
            <?php if ($booking && ($booking['TrangThai'] ?? '') === 'cho_xac_nhan'): ?>
            <a href="?page=booking&action=payment&id=<?= (int)$booking['MaDon'] ?>" class="btn btn-success btn-custom">
                <i class="fa-solid fa-credit-card me-2" aria-hidden="true"></i>Thanh toán ngay
            </a>
            <?php endif; ?>
            <a href="?page=booking&action=create" class="btn btn-primary btn-custom">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Đặt bàn mới
            </a>
            <a href="?page=home" class="btn btn-outline-secondary btn-custom">
                <i class="fa-solid fa-house me-2" aria-hidden="true"></i>Về trang chủ
            </a>
        </div>
    </div>
</section>