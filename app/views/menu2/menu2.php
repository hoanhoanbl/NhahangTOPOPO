<main class="menu2-page">
    <section class="menu2-tab-section">
        <div class="container">
            <div class="menu2-tab-navigation">
                <button class="menu2-tab-btn <?= empty($selectedCategory) || $selectedCategory === 'all' ? 'active' : '' ?>"
                        data-category="all">
                    <span class="menu2-tab-text">TẤT CẢ</span>
                </button>
                <?php if (isset($categories) && is_array($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <?php if ($category['TenDM'] !== 'TẤT CẢ'): ?>
                            <button class="menu2-tab-btn <?= $selectedCategory == $category['MaDM'] ? 'active' : '' ?>"
                                    data-category="<?= $category['MaDM'] ?>">
                                <span class="menu2-tab-text"><?= htmlspecialchars($category['TenDM']) ?></span>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="menu2-container">
        <h2 class="menu2-title">
            <?php
                if (!empty($selectedCategory) && $selectedCategory !== 'all') {
                    $currentCategoryName = 'Danh mục';
                    if (isset($categories) && is_array($categories)) {
                        foreach ($categories as $category) {
                            if ($category['MaDM'] == $selectedCategory) {
                                $currentCategoryName = $category['TenDM'];
                                break;
                            }
                        }
                    }
                    echo htmlspecialchars($currentCategoryName);
                }
            ?>
        </h2>
        <div class="menu2-grid<?= (!empty($selectedCategory) && $selectedCategory !== 'all') ? ' menu2-category-grid' : '' ?>" id="menu2-grid">
            <?php if (empty($selectedCategory) || $selectedCategory === 'all'): ?>
                <?php if (isset($groupedMenuItems) && is_array($groupedMenuItems) && count($groupedMenuItems) > 0): ?>
                    <?php foreach ($groupedMenuItems as $categoryName => $items): ?>
                        <div class="menu2-category-section">
                            <h3 class="menu2-category-title"><?= htmlspecialchars($categoryName) ?></h3>
                            <div class="menu2-category-items">
                                <?php foreach ($items as $item): ?>
                                    <div class="menu2-card"
                                         data-action="open-modal"
                                         data-id="<?= $item['MaMon'] ?>"
                                         data-name="<?= htmlspecialchars($item['TenMon']) ?>"
                                         data-price="<?= $item['Gia'] ?>"
                                         data-description="<?= htmlspecialchars($item['MoTa']) ?>"
                                         data-image-url="<?= $item['HinhAnhURL'] ?>">
                                        <img src="<?= $item['HinhAnhURL'] ?>" alt="<?= htmlspecialchars($item['TenMon']) ?>" onerror="this.src='https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp'">
                                        <div class="menu2-card-content">
                                            <span class="menu2-card-name"><?= htmlspecialchars($item['TenMon']) ?></span>
                                            <div class="menu2-card-price">
                                                <?= number_format($item['Gia'], 0, ',', '.') ?>đ
                                            </div>
                                            <div class="menu2-card-actions">
                                                <div class="menu2-btn-add-to-cart"
                                                     data-action="add-to-cart"
                                                     data-id="<?= $item['MaMon'] ?>"
                                                     data-name="<?= htmlspecialchars($item['TenMon']) ?>"
                                                     data-price="<?= $item['Gia'] ?>">+ Đặt</div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="menu2-no-items"><p>Không có món ăn nào trong cơ sở này.</p></div>
                <?php endif; ?>
            <?php else: ?>
                <?php if (isset($menuItems) && is_array($menuItems) && count($menuItems) > 0): ?>
                    <?php foreach ($menuItems as $item): ?>
                         <div class="menu2-card"
                              data-action="open-modal"
                              data-id="<?= $item['MaMon'] ?>"
                              data-name="<?= htmlspecialchars($item['TenMon']) ?>"
                              data-price="<?= $item['Gia'] ?>"
                              data-description="<?= htmlspecialchars($item['MoTa']) ?>"
                              data-image-url="<?= $item['HinhAnhURL'] ?>">
                            <img src="<?= $item['HinhAnhURL'] ?>" alt="<?= htmlspecialchars($item['TenMon']) ?>" onerror="this.src='https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp'">
                            <div class="menu2-card-content">
                                <span class="menu2-card-name"><?= htmlspecialchars($item['TenMon']) ?></span>
                                <div class="menu2-card-price">
                                     <?= number_format($item['Gia'], 0, ',', '.') ?>đ
                                </div>
                                <div class="menu2-card-actions">
                                    <div class="menu2-btn-add-to-cart"
                                         data-action="add-to-cart"
                                         data-id="<?= $item['MaMon'] ?>"
                                         data-name="<?= htmlspecialchars($item['TenMon']) ?>"
                                         data-price="<?= $item['Gia'] ?>">+ Đặt</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="menu2-no-items"><p>Không có món ăn nào trong danh mục này.</p></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- ============================================================ -->
<!-- BOOKING OVERLAY -->
<!-- ============================================================ -->
<style>
    /* Deposit warning */
    .menu2-deposit-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 6px;
        padding: 8px 12px;
        margin-bottom: 10px;
        font-size: 0.875rem;
        color: #856404;
        display: none;
    }
    .menu2-deposit-warning.visible { display: block; }
    .menu2-deposit-warning i { color: #ffc107; }
</style>

<div id="menu2-bookingOverlay" class="menu2-booking-overlay">
    <div class="menu2-booking-form-container">
        <h1 class="menu2-form-title">Đặt bàn</h1>
        <form id="menu2-bookingForm" action="app/views/menu2/process-create.php" method="POST">
            <div class="menu2-form-section">
                <h3 class="menu2-form-section-title"><i class="fas fa-user"></i>Thông tin của bạn</h3>
                <div class="menu2-form-group">
                    <input name="customer_name"
                           type="text"
                           class="menu2-form-input"
                           placeholder="Tên của bạn"
                           pattern="^[a-zA-ZÀ-ỹ\s]{2,50}$"
                           title="Tên chỉ chứa chữ cái và khoảng trắng, từ 2-50 ký tự"
                           minlength="2"
                           maxlength="50"
                           required>
                </div>
                <div class="menu2-form-group">
                    <input name="customer_phone"
                           type="tel"
                           class="menu2-form-input"
                           placeholder="Số điện thoại"
                           pattern="^0[0-9]{9}$"
                           title="Số điện thoại phải có 10 số và bắt đầu bằng số 0"
                           maxlength="10"
                           required>
                </div>
                <div class="menu2-form-group">
                    <input name="customer_email"
                           type="email"
                           class="menu2-form-input"
                           placeholder="Email (không bắt buộc)"
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                           title="Email không hợp lệ">
                </div>
            </div>

            <div class="menu2-form-section">
                <h3 class="menu2-form-section-title"><i class="fas fa-calendar-check"></i>Thông tin đặt bàn</h3>

                <!-- Chi nhánh -->
                <div class="menu2-form-group">
                    <label for="menu2-branch-select">Chi nhánh</label>
                    <select id="menu2-branch-select" class="menu2-form-input" <?php if (empty($branches)) echo 'disabled'; ?>>
                        <option value=""><?php if (empty($branches)) { echo 'Không có chi nhánh nào'; } else { echo '-- Chọn chi nhánh --'; } ?></option>
                        <?php if (isset($branches) && is_array($branches)): ?>
                            <?php foreach ($branches as $b): ?>
                                <option value="<?= $b['MaCoSo'] ?>"
                                    <?= ($b['MaCoSo'] == $maCoSo) ? 'selected' : '' ?>
                                    data-address="<?= htmlspecialchars($b['DiaChi'] ?? '') ?>">
                                    <?= htmlspecialchars($b['TenCoSo']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="menu2-form-row">
                    <div class="menu2-form-group">
                        <label>Số lượng người</label>
                        <div class="menu2-quantity-selector">
                            <button type="button" data-action="decrease-guests">-</button>
                            <div class="menu2-quantity-display" id="menu2-booking-guests-display">1</div>
                            <button type="button" data-action="increase-guests">+</button>
                        </div>
                        <input type="hidden" name="guest_count" id="menu2-guest-count-hidden" value="1">
                    </div>
                    <div class="menu2-form-group">
                        <label for="menu2-date-display-input">Chọn ngày</label>
                        <div class="menu2-input-with-icon">
                            <input type="text" class="menu2-form-input" id="menu2-date-display-input" readonly>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <input type="hidden" name="booking_date" id="menu2-booking-date-hidden">
                    </div>
                    <div class="menu2-form-group">
                        <label>Chọn giờ</label>
                        <input type="time" name="booking_time" class="menu2-form-input" id="menu2-time-select" required>
                    </div>
                </div>

                <!-- Deposit warning — shown when T7/CN/Lễ or cart has items -->
                <div id="menu2-deposit-warning" class="menu2-deposit-warning">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="menu2-deposit-text"></span>
                </div>
            </div>

            <textarea name="notes" class="menu2-form-textarea" placeholder="Ghi chú"></textarea>

            <!-- Hidden inputs để truyền dữ liệu -->
            <input type="hidden" name="branch_id" id="menu2-branch-id-hidden" value="<?= (int)$maCoSo ?>">
            <input type="hidden" name="total_amount" id="menu2-total-amount-hidden">
            <input type="hidden" name="discount_id" id="menu2-discount-id-hidden" value="">
            <input type="hidden" name="final_amount" id="menu2-final-amount-hidden" value="">
            <input type="hidden" name="cart_items" id="menu2-cart-items-hidden">

            <div class="menu2-form-actions">
                <button type="button" class="menu2-btn menu2-btn-secondary" data-action="close-booking-form">Đóng</button>
                <button type="submit" class="menu2-btn menu2-btn-primary">Đặt bàn ngay</button>
            </div>
        </form>
    </div>
</div>

<div id="menu2-itemModal" class="menu2-modal">
    <div class="menu2-modal-box">
         <div class="menu2-modal-image">
             <img id="menu2-modalImage" src="" alt="Hình ảnh món ăn">
         </div>
         <div class="menu2-modal-body">
             <div class="menu2-modal-info">
                 <p id="menu2-modalItemName"></p>
                 <div class="menu2-modal-price-quantity">
                     <p class="menu2-modal-price"><span id="menu2-modalPrice">0đ</span></p>
                     <div class="menu2-modal-quantity">
                        <div class="menu2-quantity-controls">
                             <button type="button" data-action="decrease-quantity">-</button>
                             <input type="number" id="menu2-quantity" value="1" min="1" readonly>
                             <button type="button" data-action="increase-quantity">+</button>
                         </div>
                     </div>
                 </div>
                 <hr>
                 <div class="menu2-modal-description">
                     <h4>Mô tả món ăn:</h4>
                     <p id="menu2-modalDescription">...</p>
                 </div>
                 <div class="menu2-modal-actions">
                     <button class="menu2-btn-order-now" id="menu2-orderNowBtn">Thêm vào giỏ</button>
                 </div>
             </div>
         </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // ============================================================
    // BRANCH SELECTOR — visible dropdown in booking overlay
    // ============================================================
    var branchSelect = document.getElementById('menu2-branch-select');
    var branchHidden = document.getElementById('menu2-branch-id-hidden');

    if (branchSelect && branchHidden) {
        // Initialize hidden field from current selection
        branchHidden.value = branchSelect.value || '<?= (int)$maCoSo ?>';

        branchSelect.addEventListener('change', function() {
            var selectedOption = branchSelect.options[branchSelect.selectedIndex];
            var newBranchId = branchSelect.value;

            // Update hidden input
            branchHidden.value = newBranchId;

            // Reload page with new branch so menu items are filtered
            var currentUrl = new URL(window.location.href);
            if (newBranchId) {
                currentUrl.searchParams.set('coso', newBranchId);
            }
            window.location.href = currentUrl.toString();
        });
    }

    // ============================================================
    // DEPOSIT PREVIEW — shown when date/time changes
    // Deposit rules (matches DepositCalculator):
    //   +100,000 VND if Saturday or Sunday or holiday
    //   +50% of cart total if cart is not empty
    // ============================================================
    var DEPOSIT_WEEKEND = 100000;
    var DEPOSIT_MENU_PCT = 0.5;

    // Vietnamese fixed holidays for client-side preview (2026)
    var HOLIDAYS = {
        '2026-01-01': 'New Year',
        '2026-01-28': 'Tet Eve',
        '2026-01-29': 'Tet Day 1',
        '2026-01-30': 'Tet Day 2',
        '2026-01-31': 'Tet Day 3',
        '2026-02-01': 'Tet Day 4',
        '2026-02-02': 'Tet Day 5',
        '2026-04-30': 'Reunification Day',
        '2026-05-01': 'Labor Day'
    };

    function isHoliday(dateStr) {
        return !!HOLIDAYS[dateStr];
    }

    function isWeekend(dateStr) {
        var d = new Date(dateStr + 'T00:00:00');
        var w = d.getDay(); // 0=Sun, 6=Sat
        return w === 0 || w === 6;
    }

    function getCartTotal() {
        var cart = window.menu2Cart || [];
        return cart.reduce(function(sum, item) {
            return sum + (item.price * item.quantity);
        }, 0);
    }

    function updateDepositWarning() {
        var dateInput = document.getElementById('menu2-booking-date-hidden');
        var depositEl = document.getElementById('menu2-deposit-warning');
        var depositText = document.getElementById('menu2-deposit-text');

        if (!dateInput || !depositEl || !depositText) return;

        var dateVal = dateInput.value;
        if (!dateVal) {
            depositEl.classList.remove('visible');
            return;
        }

        var isWeekendDay = isWeekend(dateVal);
        var isHolidayDay = isHoliday(dateVal);
        var cartTotal = getCartTotal();

        var deposit = 0;
        var parts = [];

        if (isWeekendDay || isHolidayDay) {
            deposit += DEPOSIT_WEEKEND;
            parts.push('100,000đ cuối tuần/lễ');
        }

        if (cartTotal > 0) {
            var menuDeposit = Math.round(cartTotal * DEPOSIT_MENU_PCT);
            deposit += menuDeposit;
            parts.push('50% đặt món (' + formatVND(menuDeposit) + ')');
        }

        if (deposit > 0) {
            depositText.textContent = 'Cọc dự kiến: ' + formatVND(deposit) + ' (' + parts.join(' + ') + ')';
            depositEl.classList.add('visible');
        } else {
            depositText.textContent = 'Không cần đặt cọc (ngày thường, không đặt món)';
            depositEl.classList.add('visible');
        }
    }

    function formatVND(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + 'đ';
    }

    // Recalculate deposit when date changes
    var datePickerInput = document.getElementById('menu2-date-display-input');
    if (datePickerInput) {
        // Watch for changes via the hidden field (updated by existing JS datepicker)
        var observer = new MutationObserver(function() {
            updateDepositWarning();
        });
        var hiddenDate = document.getElementById('menu2-booking-date-hidden');
        if (hiddenDate) {
            observer.observe(hiddenDate, { attributes: true, attributeFilter: ['value'] });
        }
        // Also check on date display click
        datePickerInput.addEventListener('change', updateDepositWarning);
    }

    // Also recalculate when cart changes
    var originalAddToCart = window.addToCartItem;
    if (typeof window.addToCartItem === 'function') {
        var _orig = window.addToCartItem;
        window.addToCartItem = function() {
            _orig.apply(this, arguments);
            updateDepositWarning();
        };
    }

})();
</script>
