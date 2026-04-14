<?php
$title = 'Đặt bàn trực tuyến';
$additional_head = <<<'HTML'
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .booking-create-page { background: #f7f8fb; padding: 40px 16px 64px; flex: 1 0 auto; }
    .booking-container { max-width: 600px; margin: 0 auto; padding: 20px 0; }
    .form-card { background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
    .form-header { text-align: center; margin-bottom: 30px; }
    .form-header h1 { color: #2c5aa0; margin-bottom: 10px; }
    .form-group { margin-bottom: 20px; }
    .form-label { font-weight: 600; color: #333; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 10px; border: 2px solid #e9ecef; padding: 12px 15px; }
    .form-control:focus, .form-select:focus { border-color: #2c5aa0; box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.15); }
    .btn-submit { background: linear-gradient(135deg, #2c5aa0, #1e3d72); border: none; border-radius: 25px; padding: 15px 40px; font-size: 1.1rem; font-weight: bold; width: 100%; margin-top: 20px; }
    .btn-submit:hover { background: linear-gradient(135deg, #1e3d72, #2c5aa0); }
    .alert-info { background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 10px; }
    .required { color: #dc3545; }
    .input-icon { position: relative; }
    .input-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d; }
    .input-icon input { padding-left: 45px; }
</style>
HTML;
$additional_scripts = <<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const dateInput = document.querySelector('input[name="booking_date"]');
        if (dateInput) {
            dateInput.value = tomorrow.toISOString().split('T')[0];
        }

        const timeInput = document.querySelector('input[name="booking_time"]');
        if (timeInput) {
            timeInput.value = '19:00';
        }
    });

    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const phone = document.querySelector('input[name="customer_phone"]').value;
        const phoneRegex = /^[0-9]{10,11}$/;

        if (!phoneRegex.test(phone)) {
            e.preventDefault();
            alert('Số điện thoại không hợp lệ. Vui lòng nhập 10-11 số.');
            return;
        }

        const bookingDate = document.querySelector('input[name="booking_date"]').value;
        const bookingTime = document.querySelector('input[name="booking_time"]').value;

        if (bookingDate && bookingTime) {
            const bookingDateTime = new Date(bookingDate + 'T' + bookingTime);
            const now = new Date();

            if (bookingDateTime < now) {
                e.preventDefault();
                alert('Thời gian đặt bàn không thể trong quá khứ!');
            }
        }
    });
</script>
HTML;
?>

<section class="booking-create-page">
    <div class="booking-container">
        <div class="form-card">
            <div class="form-header">
                <h1><i class="fa-solid fa-calendar-check me-2" aria-hidden="true"></i>Đặt bàn trực tuyến</h1>
                <p class="text-muted">Vui lòng điền thông tin để đặt bàn</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation me-2" aria-hidden="true"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info me-2" aria-hidden="true"></i>
                <strong>Phí đặt bàn:</strong> 50,000đ - Sẽ được trừ vào hóa đơn khi quý khách dùng bữa
            </div>

            <form method="POST" action="?page=booking&action=store" id="bookingForm">
                <h5 class="text-primary mb-3">
                    <i class="fa-solid fa-user me-2" aria-hidden="true"></i>
                    Thông tin khách hàng
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Họ và tên <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fa-solid fa-user" aria-hidden="true"></i>
                                <input type="text" name="customer_name" class="form-control"
                                       placeholder="Nhập họ và tên" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Số điện thoại <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                <input type="tel" name="customer_phone" class="form-control"
                                       placeholder="Nhập số điện thoại" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                        <input type="email" name="customer_email" class="form-control"
                               placeholder="Nhập email (không bắt buộc)">
                    </div>
                </div>

                <h5 class="text-primary mb-3 mt-4">
                    <i class="fa-solid fa-calendar-days me-2" aria-hidden="true"></i>
                    Thông tin đặt bàn
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Ngày đặt <span class="required">*</span></label>
                            <input type="date" name="booking_date" class="form-control" required
                                   min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Giờ đặt <span class="required">*</span></label>
                            <input type="time" name="booking_time" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Số người <span class="required">*</span></label>
                            <select name="guest_count" class="form-select" required>
                                <option value="">Chọn số người</option>
                                <option value="1">1 người</option>
                                <option value="2">2 người</option>
                                <option value="3">3 người</option>
                                <option value="4">4 người</option>
                                <option value="5">5 người</option>
                                <option value="6">6 người</option>
                                <option value="7">7 người</option>
                                <option value="8">8 người</option>
                                <option value="10">10+ người</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Chi nhánh <span class="required">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Chọn chi nhánh</option>
                                <option value="2">10 Nguyễn Văn Huyên - Thanh Khê</option>
                                <option value="3">68 Láng Thượng - Ngũ Hành Sơn</option>
                                <option value="4">505 Minh Khai - Sơn Trà</option>
                                <option value="5">Nguyễn Hữu Thọ - Cẩm Lệ</option>
                                <option value="11">67A Phó Đức Chính - Hòa Xuân</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ghi chú đặc biệt</label>
                    <textarea name="notes" class="form-control" rows="4"
                              placeholder="Yêu cầu đặc biệt, dịp kỷ niệm, vị trí ngồi..."></textarea>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        Tôi đồng ý với <a href="#" class="text-primary">điều khoản sử dụng</a>
                        và <a href="#" class="text-primary">chính sách bảo mật</a> <span class="required">*</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="fa-solid fa-credit-card me-2" aria-hidden="true"></i>
                    Đặt bàn và thanh toán
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-muted mb-2">
                    <i class="fa-solid fa-headset me-2" aria-hidden="true"></i>
                    Cần hỗ trợ? Gọi ngay: <strong>0922.782.387</strong>
                </p>
                <p class="small text-muted">
                    Hotline hỗ trợ 24/7
                </p>
            </div>
        </div>
    </div>
</section>