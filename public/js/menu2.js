// === NGUỒN DỮ LIỆU TRUNG TÂM (TOÀN CỤC) ===
const shoppingCart = {}; // { 1: { name: '...', price: 121000, quantity: 2 }, ... }
let totalCartQuantity = 0;
let totalCartPrice = 0;

// === BIẾN TOÀN CỤC CHO MODAL CHI TIẾT ===
let currentItemId = null;
let currentItemPrice = 0;
let currentItemName = '';

// === HÀM HELPER TOÀN CỤC ===
function formatPrice(price) {
    price = Math.round(price);
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}


// === LOGIC CHÍNH - BAO GỒM TẤT CẢ TRONG DOMCONTENTLOADED ===
document.addEventListener('DOMContentLoaded', function () {

    // === KHAI BÁO BIẾN DOM (Tất cả các hệ thống) ===
    const menuGrid = document.getElementById('menu2-grid');
    const stickyCartWidget = document.getElementById('menu2-sticky-cart-widget');
    const cartCountDisplay = document.getElementById('menu2-cart-item-count');
    const cartPriceDisplay = document.getElementById('menu2-cart-total-price');

    // Modal Món ăn
    const itemModal = document.getElementById('menu2-itemModal');
    const quantityInputModal = document.getElementById('menu2-quantity');
    const orderNowBtn = document.getElementById('menu2-orderNowBtn');

    // Modal Bill
    const billOverlay = document.getElementById('menu2-billOverlay');
    const billCloseBtn = document.getElementById('menu2-billCloseBtn');
    const billItemsContainer = document.getElementById('menu2-billItemsContainer');
    const billTotalPriceDisplay = document.getElementById('menu2-billTotalPriceDisplay');
    const billClearAllBtn = document.getElementById('menu2-billClearAllBtn');
    const proceedToBookingBtn = document.getElementById('menu2-proceedToBookingBtn');

    // Modal Đặt bàn
    const bookingOverlay = document.getElementById('menu2-bookingOverlay');
    const bookingGuestsDisplay = document.getElementById('menu2-booking-guests-display');

    // Modal Calendar
    const dateInput = document.getElementById('menu2-date-display-input');
    const dpOverlay = document.getElementById('menu2-datePickerOverlay');
    const dpGrid = document.getElementById('menu2-dp-days-grid');
    const dpMonthYearDisplay = document.getElementById('menu2-dp-current-month-year');
    const dpPrevMonthBtn = document.getElementById('menu2-dp-prev-month');
    const dpNextMonthBtn = document.getElementById('menu2-dp-next-month');
    const dpTodayBtn = document.getElementById('menu2-dp-today-btn');
    const dpCloseBtn = document.getElementById('menu2-dp-close-btn');

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // === HỆ THỐNG 2: LOGIC GIỎ HÀNG & BILL ===

    function recalculateCartTotals() {
        totalCartQuantity = 0;
        totalCartPrice = 0;
        for (const itemId in shoppingCart) {
            const item = shoppingCart[itemId];
            totalCartQuantity += item.quantity;
            totalCartPrice += (item.price * item.quantity);
        }
    }

    function updateCartWidgetUI() {
        if (totalCartQuantity > 0) {
            cartCountDisplay.textContent = `${totalCartQuantity} món tạm tính`;
            cartPriceDisplay.textContent = formatPrice(totalCartPrice) + 'đ';
            stickyCartWidget.classList.add('show');
        } else {
            stickyCartWidget.classList.remove('show');
        }
    }

    function renderBillItems() {
        billItemsContainer.innerHTML = '';
        if (Object.keys(shoppingCart).length === 0) {
            billItemsContainer.innerHTML = '<p style="text-align:center; color:#6c757d; padding: 20px 0;">Giỏ hàng của bạn đang trống.</p>';
            billTotalPriceDisplay.textContent = '0đ';
            return;
        }
        for (const itemId in shoppingCart) {
            const item = shoppingCart[itemId];
            const itemTotalPrice = item.price * item.quantity;
            const itemHtml = `
                    <div class="menu2-bill-item" data-item-id="${itemId}">
                        <div class="menu2-item-info">
                            <p class="menu2-item-name">${item.name}</p>
                            <p class="menu2-item-price">${formatPrice(item.price)}đ</p>
                        </div>
                        <div class="menu2-item-controls">
                            <div class="menu2-quantity-controls">
                                <button type="button" class="menu2-bill-qty-decrease" data-action="bill-qty-decrease">-</button>
                                <input type="number" value="${item.quantity}" min="1" readonly>
                                <button type="button" class="menu2-bill-qty-increase" data-action="bill-qty-increase">+</button>
                            </div>
                            <div class="menu2-item-total-price">${formatPrice(itemTotalPrice)}đ</div>
                            <div class="menu2-delete_item" data-action="delete-item"><i class="fas fa-trash-alt"></i></div>
                        </div>
                    </div>`;
            billItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
        }
        billTotalPriceDisplay.textContent = formatPrice(totalCartPrice) + 'đ';
    }

    function updateAllUI() {
        recalculateCartTotals();
        updateCartWidgetUI();
        if (billOverlay.classList.contains('show')) {
            renderBillItems();
        }
    }

    function addToCart(itemId, itemName, itemPrice, quantity = 1) {
        if (shoppingCart[itemId]) {
            shoppingCart[itemId].quantity += quantity;
        } else {
            shoppingCart[itemId] = { name: itemName, price: parseFloat(itemPrice), quantity: quantity };
        }
        updateAllUI();

        // Lưu vào cookies
        saveCartToCookies();
    }

    // --- Các hàm Mở/Đóng Modal ---
    function openModal(element, state) {
        if (element) {
            element.classList.toggle('show', state);
            document.body.style.overflow = (state && (
                (itemModal && itemModal.style.display === 'block') ||
                (billOverlay && billOverlay.classList.contains('show')) ||
                (dpOverlay && dpOverlay.classList.contains('show')) ||
                (bookingOverlay && bookingOverlay.classList.contains('show'))
            )) ? 'hidden' : 'auto';
        }
    }

    function openItemModal(id, name, price, description = '', imageUrl = '') {
        currentItemId = id;
        currentItemPrice = parseFloat(price);
        currentItemName = name;

        document.getElementById('menu2-modalItemName').textContent = name;
        document.getElementById('menu2-modalPrice').textContent = formatPrice(price) + 'đ';
        document.getElementById('menu2-modalImage').src = imageUrl || 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp';
        document.getElementById('menu2-modalDescription').textContent = description || 'Món ăn ngon tại nhà hàng';
        quantityInputModal.value = 1;
        itemModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeItemModal() {
        itemModal.style.display = 'none';
        if (!billOverlay.classList.contains('show') && !dpOverlay.classList.contains('show') && !bookingOverlay.classList.contains('show')) {
            document.body.style.overflow = 'auto';
        }
    }

    function openBillModal() {
        renderBillItems();
        openModal(billOverlay, true);
    }

    function closeBillModal() {
        console.log('Closing bill modal');
        openModal(billOverlay, false);
        if ((!itemModal || itemModal.style.display !== 'block') &&
            (!dpOverlay || !dpOverlay.classList.contains('show')) &&
            (!bookingOverlay || !bookingOverlay.classList.contains('show'))) {
            document.body.style.overflow = 'auto';
        }
    }

    function showBookingForm() {
        console.log('Showing booking form');
        openModal(bookingOverlay, true);
    }

    function closeBookingForm() {
        console.log('Showing booking form');
        openModal(bookingOverlay, false);
    }

    // --- Xử lý sự kiện chung bằng Event Delegation ---
    document.addEventListener('click', function (e) {
        const target = e.target;
        const action = target.dataset.action;

        // Xử lý nút "+ Đặt" TRƯỚC để ngăn modal mở ra
        if (target.closest('.menu2-btn-add-to-cart')) {
            e.stopPropagation(); // Ngăn modal mở ra khi bấm nút "+ Đặt"
            e.preventDefault(); // Ngăn các hành động mặc định khác
            const btn = target.closest('.menu2-btn-add-to-cart');
            addToCart(btn.dataset.id, btn.dataset.name, btn.dataset.price, 1);
            return; // Dừng xử lý các sự kiện khác
        }

        // Actions for menu cards - chỉ xử lý khi KHÔNG phải là nút "+ Đặt"
        const menuCard = target.closest('.menu2-card');
        if (menuCard && menuCard.dataset.action === 'open-modal') {
            openItemModal(
                menuCard.dataset.id,
                menuCard.dataset.name,
                menuCard.dataset.price,
                menuCard.dataset.description,
                menuCard.dataset.imageUrl
            );
        }

        // Actions for item modal
        if (action === 'decrease-quantity' && parseInt(quantityInputModal.value) > 1) {
            quantityInputModal.value--;
        }
        if (action === 'increase-quantity') {
            quantityInputModal.value++;
        }

        // Actions for booking form
        if (action === 'decrease-guests' && bookingGuestsDisplay) {
            let currentValue = parseInt(bookingGuestsDisplay.textContent);
            if (currentValue > 1) bookingGuestsDisplay.textContent = currentValue - 1;
        }
        if (action === 'increase-guests' && bookingGuestsDisplay) {
            let currentValue = parseInt(bookingGuestsDisplay.textContent);
            if (currentValue < 20) bookingGuestsDisplay.textContent = currentValue + 1;
        }
        if (action === 'close-booking-form') {
            closeBookingForm();
        }

        // Action for category tabs
        const tabBtn = target.closest('.menu2-tab-btn');
        if (tabBtn) {
            filterByCategory(tabBtn.dataset.category);
        }
    });

    // Sự kiện riêng cho các element không dùng delegation
    if (orderNowBtn) {
        orderNowBtn.addEventListener('click', () => {
            const quantity = parseInt(quantityInputModal.value);
            addToCart(currentItemId, currentItemName, currentItemPrice, quantity);
            closeItemModal();
        });
    }

    // Note: sticky cart widget, bill close button và clear all button
    // được xử lý bởi global-cart-manager.js

    if (billItemsContainer) {
        billItemsContainer.addEventListener('click', function (e) {
            const actionTarget = e.target.closest('[data-action]');
            if (!actionTarget) return;

            const action = actionTarget.dataset.action;
            const itemDiv = e.target.closest('.menu2-bill-item');
            if (!itemDiv) return;
            const itemId = itemDiv.dataset.itemId;

            if (action === 'bill-qty-increase') {
                shoppingCart[itemId].quantity += 1;
            } else if (action === 'bill-qty-decrease') {
                if (shoppingCart[itemId].quantity > 1) {
                    shoppingCart[itemId].quantity -= 1;
                } else {
                    delete shoppingCart[itemId];
                }
            } else if (action === 'delete-item') {
                delete shoppingCart[itemId];
            }
            updateAllUI();

            // Lưu vào cookies sau mỗi thay đổi
            saveCartToCookies();
        });
    }

    if (proceedToBookingBtn) {
        proceedToBookingBtn.addEventListener('click', function () {
            closeBillModal();
            showBookingForm();
        });
    }

    // Xử lý submit form đặt bàn
    const bookingForm = document.getElementById('menu2-bookingForm');
    const guestHidden = document.getElementById('menu2-guest-count-hidden');
    const dateHidden = document.getElementById('menu2-booking-date-hidden');
    const branchHidden = document.getElementById('menu2-branch-id-hidden');
    const totalHidden = document.getElementById('menu2-total-amount-hidden');
    const discountIdHidden = document.getElementById('menu2-discount-id-hidden');
    const finalAmountHidden = document.getElementById('menu2-final-amount-hidden');
    const cartHidden = document.getElementById('menu2-cart-items-hidden');
    const timeSelect = document.getElementById('menu2-time-select');

    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            // 1) GUEST COUNT
            const guestsDisplay = document.getElementById('menu2-booking-guests-display');
            if (guestHidden && guestsDisplay) {
                guestHidden.value = parseInt(guestsDisplay.textContent || '1', 10);
            }

            // 2) DATE (chuyển từ selectedDate sang yyyy-mm-dd)
            if (dateHidden && typeof selectedDate !== 'undefined' && selectedDate) {
                const y = selectedDate.getFullYear();
                const m = ('0' + (selectedDate.getMonth() + 1)).slice(-2);
                const d = ('0' + selectedDate.getDate()).slice(-2);
                dateHidden.value = `${y}-${m}-${d}`;
            }

            // 3) BRANCH ID từ URL (?coso=11)
            const urlParams = new URLSearchParams(window.location.search);
            if (branchHidden) {
                branchHidden.value = urlParams.get('coso') || '';
            }

            // 4) TOTAL + CART ITEMS từ shoppingCart
            let total = 0;
            const cartForPost = [];
            for (const id in shoppingCart) {
                const item = shoppingCart[id];
                total += item.price * item.quantity;
                cartForPost.push({
                    id: parseInt(id, 10),
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity
                });
            }
            if (totalHidden) totalHidden.value = Math.round(total);
            if (cartHidden) cartHidden.value = JSON.stringify(cartForPost);

            // 4.1) Lấy thông tin mã giảm giá (nếu có) từ global-cart-manager.js
            const currentDiscount = (typeof window.getCurrentDiscount === 'function') ? window.getCurrentDiscount() : null;
            if (currentDiscount) {
                if (discountIdHidden) discountIdHidden.value = currentDiscount.id || '';
                if (finalAmountHidden) finalAmountHidden.value = Math.round(currentDiscount.finalAmount || total);
            } else {
                if (discountIdHidden) discountIdHidden.value = '';
                if (finalAmountHidden) finalAmountHidden.value = Math.round(total);
            }

            // 5) Validate form trước khi submit

            // Validate tên khách hàng
            const nameInput = bookingForm.querySelector('input[name="customer_name"]');
            if (nameInput && nameInput.value.trim()) {
                const namePattern = /^[a-zA-ZÀ-ỹ\s]{2,50}$/;
                if (!namePattern.test(nameInput.value.trim())) {
                    e.preventDefault();
                    alert('Tên chỉ được chứa chữ cái và khoảng trắng, từ 2-50 ký tự');
                    nameInput.focus();
                    return false;
                }
            }

            // Validate số điện thoại
            const phoneInput = bookingForm.querySelector('input[name="customer_phone"]');
            if (phoneInput && phoneInput.value.trim()) {
                const phonePattern = /^0[0-9]{9}$/;
                if (!phonePattern.test(phoneInput.value.trim())) {
                    e.preventDefault();
                    alert('Số điện thoại phải có 10 số và bắt đầu bằng số 0');
                    phoneInput.focus();
                    return false;
                }
            }

            // Validate email (nếu có nhập)
            const emailInput = bookingForm.querySelector('input[name="customer_email"]');
            if (emailInput && emailInput.value.trim()) {
                const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                if (!emailPattern.test(emailInput.value.trim())) {
                    e.preventDefault();
                    alert('Email không đúng định dạng');
                    emailInput.focus();
                    return false;
                }
            }

            if (!timeSelect || !timeSelect.value) {
                e.preventDefault();
                alert('Vui lòng chọn giờ đặt bàn');
                return false;
            }

            if (Object.keys(shoppingCart).length === 0) {
                e.preventDefault();
                alert('Giỏ hàng trống, vui lòng chọn món ăn');
                return false;
            }
        });
    }



    // === HỆ THỐNG 3: LOGIC CALENDAR (GIỮ NGUYÊN, VÌ ĐÃ TỐT) ===
    const maxDate = new Date(today);
    maxDate.setMonth(maxDate.getMonth() + 2);
    let selectedDate = new Date(today);
    let currentCalendarDate = new Date(today);

    function formatDateForInput(date) {
        const monthName = date.toLocaleString('vi-VN', { month: 'long' });
        const day = ('0' + date.getDate()).slice(-2);
        return `${day} ${monthName}`;
    }

    if (dateInput) {
        dateInput.value = formatDateForInput(today);
    }

    function renderCalendar(year, month) {
        currentCalendarDate.setFullYear(year, month, 1);
        dpGrid.innerHTML = '';
        dpMonthYearDisplay.textContent = currentCalendarDate.toLocaleString('vi-VN', { month: 'long', year: 'numeric' });

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDayOfMonth; i++) {
            dpGrid.insertAdjacentHTML('beforeend', '<div class="menu2-dp-day empty"></div>');
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const cellDate = new Date(year, month, day);
            const cell = document.createElement('div');
            cell.className = 'menu2-dp-day';
            cell.textContent = day;
            cell.dataset.date = cellDate.toISOString();
            let classes = [];
            if (cellDate < today || cellDate > maxDate) classes.push('disabled');
            if (cellDate.getTime() === today.getTime()) classes.push('today');
            if (selectedDate && cellDate.getTime() === selectedDate.getTime()) classes.push('selected');
            cell.classList.add(...classes);
            dpGrid.appendChild(cell);
        }
        dpPrevMonthBtn.disabled = (year === today.getFullYear() && month === today.getMonth());
        dpNextMonthBtn.disabled = (year === maxDate.getFullYear() && month === maxDate.getMonth());
    }

    function closeDatePickerModal() {
        openModal(dpOverlay, false);
        if ((!itemModal || itemModal.style.display !== 'block') &&
            (!billOverlay || !billOverlay.classList.contains('show')) &&
            (!bookingOverlay || !bookingOverlay.classList.contains('show'))) {
            document.body.style.overflow = 'auto';
        }
    }

    if (dateInput) {
        dateInput.addEventListener('click', () => {
            renderCalendar(selectedDate.getFullYear(), selectedDate.getMonth());
            openModal(dpOverlay, true);
        });
    }

    if (dpCloseBtn) {
        dpCloseBtn.addEventListener('click', closeDatePickerModal);
    }

    if (dpPrevMonthBtn) {
        dpPrevMonthBtn.addEventListener('click', () => renderCalendar(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() - 1));
    }

    if (dpNextMonthBtn) {
        dpNextMonthBtn.addEventListener('click', () => renderCalendar(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() + 1));
    }

    if (dpTodayBtn) {
        dpTodayBtn.addEventListener('click', () => {
            selectedDate = new Date(today);
            if (dateInput) {
                dateInput.value = formatDateForInput(selectedDate);
            }
            closeDatePickerModal();
        });
    }

    if (dpGrid) {
        dpGrid.addEventListener('click', (e) => {
            const target = e.target.closest('.menu2-dp-day:not(.empty):not(.disabled)');
            if (!target) return;
            selectedDate = new Date(target.dataset.date);
            if (dateInput) {
                dateInput.value = formatDateForInput(selectedDate);
            }
            closeDatePickerModal();
        });
    }

    // === Đóng modal chung ===
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeItemModal();
            closeBillModal();
            closeDatePickerModal();
            closeBookingForm();
        }
    });

    window.addEventListener('click', function (event) {
        if (itemModal && event.target == itemModal) closeItemModal();
        if (billOverlay && event.target == billOverlay) closeBillModal();
        if (dpOverlay && event.target == dpOverlay) closeDatePickerModal();
        if (bookingOverlay && event.target == bookingOverlay) closeBookingForm();
    });

    // === HÀM HELPER CHO COOKIES ===
    function saveCartToCookies() {
        if (typeof window.CartCookies !== 'undefined') {
            window.CartCookies.saveCart(shoppingCart);

            // Cập nhật global cart display trên tất cả các trang
            if (typeof window.updateGlobalCart === 'function') {
                window.updateGlobalCart();
            }
        }
    }

    function loadCartFromCookies() {
        if (typeof window.CartCookies !== 'undefined') {
            const savedCart = window.CartCookies.loadCart();
            if (savedCart && Object.keys(savedCart).length > 0) {
                // Khôi phục dữ liệu vào biến global
                Object.keys(savedCart).forEach(itemId => {
                    shoppingCart[itemId] = savedCart[itemId];
                });

                // Cập nhật UI sau khi khôi phục
                updateAllUI();
                console.log('Cart restored from cookies:', savedCart);
            }
        }
    }

    function clearCartFromCookies() {
        if (typeof window.CartCookies !== 'undefined') {
            window.CartCookies.clearCart();

            // Cập nhật global cart display
            if (typeof window.updateGlobalCart === 'function') {
                window.updateGlobalCart();
            }
        }
    }

    // Làm cho các hàm cart có thể truy cập toàn cục
    window.saveCartToCookies = saveCartToCookies;
    window.loadCartFromCookies = loadCartFromCookies;
    window.clearCartFromCookies = clearCartFromCookies;

    // === KHÔI PHỤC GIỎ HÀNG TỪ COOKIES SAU KHI TẤT CẢ FUNCTIONS ĐÃ ĐƯỢC ĐỊNH NGHĨA ===
    loadCartFromCookies();

    // === SET THỜI GIAN MẶC ĐỊNH CHO INPUT TIME ===
    // Lấy thời gian hiện tại
    const now = new Date();
    const hours = ('0' + now.getHours()).slice(-2);
    const minutes = ('0' + now.getMinutes()).slice(-2);
    const currentTime = hours + ':' + minutes;

    // Set cho cả 2 input time (trong menu2.php và layout.php)
    const timeInput = document.getElementById('menu2-time-select');
    const timeInputLayout = document.getElementById('menu2-time-select-layout');

    if (timeInput) {
        timeInput.value = currentTime;
    }
    if (timeInputLayout) {
        timeInputLayout.value = currentTime;
    }

}); // --- KẾT THÚC DOMCONTENTLOADED ---

// === FUNCTION TOÀN CỤC CHO CATEGORY FILTERING ===
// Lưu ý: Các hàm này vẫn nằm ngoài DOMContentLoaded để có thể gọi từ nơi khác nếu cần
// nhưng logic gọi đã được chuyển vào trong qua event delegation.

function filterByCategory(categoryId) {
    const allTabs = document.querySelectorAll('.menu2-tab-btn');
    allTabs.forEach(tab => tab.classList.remove('active'));

    const activeTab = document.querySelector(`.menu2-tab-btn[data-category="${categoryId}"]`);
    if (activeTab) {
        activeTab.classList.add('active');
    }

    updatePageTitle(categoryId, activeTab);

    const urlParams = new URLSearchParams(window.location.search);
    const maCoSo = urlParams.get('coso') || '11';
    const url = `index.php?page=menu&action=getMenuData&coso=${maCoSo}&category=${categoryId}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Giả sử server luôn trả về một cấu trúc dữ liệu duy nhất
                // và ta sẽ có một hàm render duy nhất để xử lý
                renderMenuGrid(data.data, data.type);
            } else {
                console.error('Error fetching menu data:', data.message);
                document.getElementById('menu2-grid').innerHTML = `<div class="menu2-no-items"><p>Đã có lỗi xảy ra khi tải thực đơn.</p></div>`;
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            document.getElementById('menu2-grid').innerHTML = `<div class="menu2-no-items"><p>Lỗi mạng, không thể tải thực đơn.</p></div>`;
        });
}

function updatePageTitle(categoryId, activeTab) {
    const pageTitle = document.querySelector('.menu2-container h2');
    if (pageTitle) {
        if (categoryId === 'all') {
            pageTitle.textContent = ''; // Hoặc 'Tất cả món ăn'
        } else if (activeTab) {
            const categoryName = activeTab.querySelector('.menu2-tab-text').textContent;
            pageTitle.textContent = categoryName;
        }
    }
}

// HÀM MỚI: Hợp nhất 2 hàm updateMenuGrid và updateMenuGridGrouped
function renderMenuGrid(data, type) {
    const menuGrid = document.getElementById('menu2-grid');
    menuGrid.innerHTML = ''; // Xóa nội dung cũ

    if (type === 'grouped') {
        menuGrid.classList.remove('menu2-category-grid');
        if (Object.keys(data).length === 0) {
            menuGrid.innerHTML = '<div class="menu2-no-items"><p>Không có món ăn nào trong cơ sở này.</p></div>';
            return;
        }

        Object.keys(data).forEach(categoryName => {
            const items = data[categoryName];
            const categorySection = document.createElement('div');
            categorySection.className = 'menu2-category-section';

            let itemsHtml = '';
            items.forEach(item => itemsHtml += createMenuCardHtml(item));

            categorySection.innerHTML = `
                    <h3 class="menu2-category-title">${categoryName}</h3>
                    <div class="menu2-category-items">${itemsHtml}</div>
                `;
            menuGrid.appendChild(categorySection);
        });

    } else { // type === 'flat' hoặc mặc định
        menuGrid.classList.add('menu2-category-grid');
        if (data.length === 0) {
            menuGrid.innerHTML = '<div class="menu2-no-items"><p>Không có món ăn nào trong danh mục này.</p></div>';
            return;
        }
        let html = '';
        data.forEach(item => html += createMenuCardHtml(item));
        menuGrid.innerHTML = html;
    }
}

// HÀM MỚI: Tách logic tạo HTML của card ra riêng để tái sử dụng
function createMenuCardHtml(item) {
    const imageUrl = item.HinhAnhURL || 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp';
    // Dùng hàm escape để tránh lỗi XSS hoặc lỗi hiển thị nếu tên/mô tả chứa ký tự đặc biệt
    const escapedName = escapeHtml(item.TenMon || '');
    const escapedDescription = escapeHtml(item.MoTa || '');

    return `
            <div class="menu2-card" 
                data-action="open-modal"
                data-id="${item.MaMon}"
                data-name="${escapedName}"
                data-price="${item.Gia}"
                data-description="${escapedDescription}"
                data-image-url="${imageUrl}">
                <img src="${imageUrl}" alt="${escapedName}" onerror="this.src='https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp'">
                <div class="menu2-card-content">
                    <span class="menu2-card-name">${escapedName}</span>
                    <div class="menu2-card-price">
                        ${formatPrice(item.Gia)}đ
                    </div>
                    <div class="menu2-card-actions">
                        <div class="menu2-btn-add-to-cart" 
                            data-action="add-to-cart"
                            data-id="${item.MaMon}"
                            data-name="${escapedName}"
                            data-price="${item.Gia}">+ Đặt</div>
                    </div>
                </div>
            </div>
        `;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
}

// === HÀM HELPER CHO COOKIES ===
function saveCartToCookies() {
    if (typeof window.CartCookies !== 'undefined') {
        window.CartCookies.saveCart(shoppingCart);

        // Cập nhật global cart display trên tất cả các trang
        if (typeof window.updateGlobalCart === 'function') {
            window.updateGlobalCart();
        }
    }
}

function loadCartFromCookies() {
    if (typeof window.CartCookies !== 'undefined') {
        const savedCart = window.CartCookies.loadCart();
        if (savedCart && Object.keys(savedCart).length > 0) {
            // Khôi phục dữ liệu vào biến global
            Object.keys(savedCart).forEach(itemId => {
                shoppingCart[itemId] = savedCart[itemId];
            });

            // Cập nhật UI sau khi khôi phục
            updateAllUI();
            console.log('Cart restored from cookies:', savedCart);
        }
    }
}

function clearCartFromCookies() {
    if (typeof window.CartCookies !== 'undefined') {
        window.CartCookies.clearCart();

        // Cập nhật global cart display
        if (typeof window.updateGlobalCart === 'function') {
            window.updateGlobalCart();
        }
    }
}