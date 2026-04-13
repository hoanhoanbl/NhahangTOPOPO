<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Quán Nướng TOPOPO'; ?></title>
   
    <!-- Critical CSS - Design tokens and variables (highest priority) -->
    <link rel="preload" href="<?php echo asset('css/constants.css'); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="<?php echo asset('css/layout/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/layout/footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/components/buttons.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/pages/home.css'); ?>">
    <!-- <link rel="stylesheet" href="<?php echo asset('css/pages/menu.css'); ?>"> -->
    <link rel="stylesheet" href="<?php echo asset('css/pages/menu2.css'); ?>">
    
    <!-- Page-specific CSS -->
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>

    
    
    <!-- External CSS (lowest priority) -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- Page-specific head content -->
    <?php if (isset($additional_head)): ?>
        <?php echo $additional_head; ?>
    <?php endif; ?>
</head>
<body >
    <!-- Include Header Component -->
    <?php include 'header.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>
    
    <!-- Include Footer Component -->
    <?php include 'footer.php'; ?>
    
    <!-- Global Sticky Cart Widget -->
    <div id="menu2-sticky-cart-widget">
        <div class="menu2-cart-info">
            <span id="menu2-cart-item-count">0 món tạm tính</span>
            <strong id="menu2-cart-total-price">0đ</strong>
        </div>
    </div>

    <!-- Global Bill Modal -->
    <div id="menu2-billOverlay" class="menu2-bill-overlay">
        <div class="menu2-bill-modal">
            <header class="menu2-bill-header">
                <div class="menu2-bill-title"><i class="fas fa-receipt"></i><span>Tạm tính</span></div>
                <div class="menu2-header-actions">
                    <button class="menu2-save-button"><i class="fas fa-download"></i> LƯU VỀ MÁY</button>
                    <button id="menu2-billCloseBtn" class="menu2-close-button"><i class="fas fa-times"></i></button>
                </div>
            </header>
            <section class="menu2-bill-body">
                <div class="menu2-bill-total-summary">
                    <div class="menu2-total-left">
                        <h3 class="menu2-total-title">Tổng tiền</h3>
                        <p class="menu2-total-note">Đơn giá tạm tính chỉ mang tính chất tham khảo.</p>
                    </div>
                    <div class="menu2-total-right">
                        <div id="menu2-billTotalPriceDisplay" class="menu2-total-price">0đ</div>
                        <a id="menu2-billClearAllBtn" href="#" class="menu2-clear-bill"><i class="fas fa-trash-alt"></i> Xoá hết tạm tính</a>
                    </div>
                </div>
                
                <!-- Phần mã giảm giá -->
                <div class="menu2-discount-section">
                    <div class="menu2-discount-input-wrapper">
                        <input type="text" id="menu2-discountCode" class="menu2-discount-input" placeholder="Nhập mã giảm giá">
                        <button type="button" id="menu2-applyDiscountBtn" class="menu2-apply-discount-btn">Áp dụng</button>
                    </div>
                    <div id="menu2-discountMessage" class="menu2-discount-message"></div>
                    <div id="menu2-discountDetails" class="menu2-discount-details" style="display: none;">
                        <div class="menu2-discount-row">
                            <span>Tạm tính:</span>
                            <span id="menu2-subtotalPrice">0đ</span>
                        </div>
                        <div class="menu2-discount-row discount-highlight">
                            <span>Giảm giá (<span id="menu2-discountPercent">0%</span>):</span>
                            <span id="menu2-discountAmount">-0đ</span>
                        </div>
                        <div class="menu2-discount-row total-row">
                            <span><strong>Tổng thanh toán:</strong></span>
                            <span id="menu2-finalPrice"><strong>0đ</strong></span>
                        </div>
                        <button type="button" id="menu2-removeDiscountBtn" class="menu2-remove-discount-btn">
                            <i class="fas fa-times"></i> Bỏ mã giảm giá
                        </button>
                    </div>
                </div>

                <div id="menu2-billItemsContainer" class="menu2-bill-items"></div>
            </section>
            <footer class="menu2-bill-footer">
                              <button id="menu2-proceedToBookingBtn" class="menu2-cta-button">ĐẶT Online</button>

                <p class="menu2-footer-note">Hoặc gọi <span>*1986</span> để đặt bàn</p>
            </footer>
        </div>
    </div>

    <div id="menu2-bookingOverlay" class="menu2-booking-overlay">
        <div class="menu2-booking-form-container">
            <h1 class="menu2-form-title">Đặt bàn</h1>
            <form id="menu2-bookingForm">
                <div class="menu2-form-section">
                    <h3 class="menu2-form-section-title"><i class="fas fa-user"></i>Thông tin của bạn</h3>
                    <div class="menu2-form-group">
                        <input type="text" class="menu2-form-input" placeholder="Tên của bạn" required>
                    </div>
                    <div class="menu2-form-group">
                        <input type="tel" class="menu2-form-input" placeholder="Số điện thoại" required>
                    </div>
                </div>

                <div class="menu2-form-section">
                    <h3 class="menu2-form-section-title"><i class="fas fa-calendar-check"></i>Thông tin đặt bàn</h3>
                    <div class="menu2-form-row">
                        <div class="menu2-form-group">
                            <label>Số lượng người</label>
                            <div class="menu2-quantity-selector">
                                <button type="button" data-action="decrease-guests">-</button>
                                <div class="menu2-quantity-display" id="menu2-booking-guests-display">1</div>
                                <button type="button" data-action="increase-guests">+</button>
                            </div>
                        </div>
                        <div class="menu2-form-group">
                            <label for="menu2-date-display-input">Chọn ngày</label>
                            <div class="menu2-input-with-icon">
                                <input type="text" class="menu2-form-input" id="menu2-date-display-input" readonly>
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        <div class="menu2-form-group">
                            <label>Chọn giờ</label>
                            <input type="time" class="menu2-form-input" id="menu2-time-select-layout" required>
                        </div>
                    </div>
                </div>
                <textarea class="menu2-form-textarea" placeholder="Ghi chú"></textarea>
                <div class="menu2-form-actions">
                    <button type="button" class="menu2-btn menu2-btn-secondary" data-action="close-booking-form">Đóng</button>
                    <button type="submit" class="menu2-btn menu2-btn-primary">Đặt bàn ngay</button>
                </div>
            </form>
        </div>
    </div>

    <div id="menu2-datePickerOverlay" class="menu2-date-picker-overlay">
        <div id="menu2-datePickerModal" class="menu2-date-picker-modal">
            <div class="menu2-dp-header">
                <button type="button" id="menu2-dp-prev-month" class="menu2-dp-nav"><i class="fas fa-chevron-left"></i></button>
                <span id="menu2-dp-current-month-year"></span>
                <button type="button" id="menu2-dp-next-month" class="menu2-dp-nav"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="menu2-dp-weekdays">
                <div>CN</div><div>T2</div><div>T3</div><div>T4</div><div>T5</div><div>T6</div><div>T7</div>
            </div>
            <div id="menu2-dp-days-grid" class="menu2-dp-days-grid"></div>
            <div class="menu2-dp-footer">
                <button type="button" id="menu2-dp-today-btn" class="menu2-dp-btn menu2-dp-btn-secondary">Hôm nay</button>
                <button type="button" id="menu2-dp-close-btn" class="menu2-dp-btn menu2-dp-btn-primary">Đóng</button>
            </div>
        </div>
    </div>
    
    <!-- Cart Cookies Manager - Load on all pages -->
    <script src="<?php echo asset('js/cart-cookies.js'); ?>"></script>
    <script src="<?php echo asset('js/global-cart-manager.js'); ?>"></script>
    <script src="<?php echo asset('js/menu2.js'); ?>"></script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($additional_scripts)): ?>
        <?php echo $additional_scripts; ?>
    <?php endif; ?>
</body>
</html>
