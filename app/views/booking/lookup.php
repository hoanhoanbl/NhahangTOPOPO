<?php
$title = $title ?? 'Tra cứu đặt bàn';
$additional_head = <<<'HTML'
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .booking-lookup-page {
        flex: 1 0 auto;
        display: flex;
        align-items: flex-start;
        padding: 48px 16px 64px;
        background: linear-gradient(180deg, #f7faf8 0%, #eef3ef 100%);
    }
    .booking-lookup-card {
        max-width: 560px;
        width: 100%;
        margin: 0 auto;
        border: 0;
        border-radius: 24px;
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.08);
    }
    .booking-lookup-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #e8f5eb;
        color: #12613a;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .booking-lookup-btn {
        border-radius: 999px;
        padding: 12px 20px;
        font-weight: 600;
    }
    .booking-lookup-help {
        font-size: 0.95rem;
        color: #5f6b66;
    }
</style>
HTML;
?>

<section class="booking-lookup-page">
    <div class="card booking-lookup-card">
        <div class="card-body p-4 p-md-5">
            <div class="booking-lookup-badge mb-3">
                <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                Tra cứu booking của bạn
            </div>

            <h1 class="h3 mb-3">Xem chi tiết và hủy đặt bàn</h1>
            <p class="booking-lookup-help mb-4">
                Nhập mã đặt bàn và số điện thoại đã dùng khi đặt bàn. Hệ thống sẽ đưa bạn tới trang chi tiết booking để xem lịch sử và thao tác tiếp.
            </p>

            <?php if (!empty($lookupError)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($lookupError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="?page=booking&action=lookup" class="row g-3">
                <div class="col-12">
                    <label for="booking_code" class="form-label fw-semibold">Mã đặt bàn</label>
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        id="booking_code"
                        name="booking_code"
                        value="<?= htmlspecialchars($lookupCode ?? '') ?>"
                        placeholder="Ví dụ: DB133 hoặc 133"
                        required
                    >
                </div>

                <div class="col-12">
                    <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                    <input
                        type="text"
                        class="form-control form-control-lg"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($lookupPhone ?? '') ?>"
                        placeholder="Nhập số điện thoại đã đặt bàn"
                        required
                    >
                </div>

                <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end pt-2">
                    <a href="?page=home" class="btn btn-outline-secondary booking-lookup-btn">Về trang chủ</a>
                    <button type="submit" class="btn btn-success booking-lookup-btn">
                        <i class="fas fa-arrow-right me-2" aria-hidden="true"></i>Tra cứu booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
