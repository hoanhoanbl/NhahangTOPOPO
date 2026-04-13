/**
 * Global Cart Manager
 * Quản lý giỏ hàng toàn cục và sticky widget trên tất cả các trang
 */
(function () {
    'use strict';

    // Chờ DOM và CartCookies sẵn sàng
    document.addEventListener('DOMContentLoaded', function () {
        // Kiểm tra xem CartCookies có sẵn không
        if (typeof window.CartCookies === 'undefined') {
            console.warn('CartCookies not loaded, global cart manager disabled');
            return;
        }

        initGlobalCart();
    });

    // Biến lưu trữ thông tin mã giảm giá hiện tại
    let currentDiscount = null;

    function initGlobalCart() {
        const stickyCartWidget = document.getElementById('menu2-sticky-cart-widget');
        const cartCountDisplay = document.getElementById('menu2-cart-item-count');
        const cartPriceDisplay = document.getElementById('menu2-cart-total-price');

        if (!stickyCartWidget || !cartCountDisplay || !cartPriceDisplay) {
            console.warn('Global cart elements not found');
            return;
        }

        // Khôi phục và hiển thị giỏ hàng từ cookies
        updateGlobalCartDisplay();

        // Xử lý click vào sticky cart widget để mở bill modal
        stickyCartWidget.addEventListener('click', function () {
            openGlobalBillModal();
        });

        // Xử lý đóng bill modal
        const billCloseBtn = document.getElementById('menu2-billCloseBtn');
        const billOverlay = document.getElementById('menu2-billOverlay');
        const billClearAllBtn = document.getElementById('menu2-billClearAllBtn');

        if (billCloseBtn) {
            billCloseBtn.addEventListener('click', closeGlobalBillModal);
        }

        if (billOverlay) {
            // Đóng modal khi click vào overlay
            billOverlay.addEventListener('click', function (e) {
                if (e.target === billOverlay) {
                    closeGlobalBillModal();
                }
            });
        }

        if (billClearAllBtn) {
            billClearAllBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('Bạn có chắc chắn muốn xoá tất cả các món trong giỏ hàng tạm?')) {
                    window.CartCookies.clearCart();
                    currentDiscount = null; // Reset mã giảm giá
                    updateGlobalCartDisplay();
                    renderGlobalBillItems();
                }
            });
        }

        // Xử lý áp dụng mã giảm giá
        const applyDiscountBtn = document.getElementById('menu2-applyDiscountBtn');
        const removeDiscountBtn = document.getElementById('menu2-removeDiscountBtn');

        if (applyDiscountBtn) {
            applyDiscountBtn.addEventListener('click', handleApplyDiscount);
        }

        if (removeDiscountBtn) {
            removeDiscountBtn.addEventListener('click', handleRemoveDiscount);
        }

        // Cập nhật hiển thị định kỳ (trong trường hợp có thay đổi từ tab khác)
        setInterval(updateGlobalCartDisplay, 5000);
    }

    function updateGlobalCartDisplay() {
        const summary = window.CartCookies.getCartSummary();
        const stickyCartWidget = document.getElementById('menu2-sticky-cart-widget');
        const cartCountDisplay = document.getElementById('menu2-cart-item-count');
        const cartPriceDisplay = document.getElementById('menu2-cart-total-price');

        if (!stickyCartWidget || !cartCountDisplay || !cartPriceDisplay) {
            return;
        }

        if (summary.totalQuantity > 0) {
            cartCountDisplay.textContent = `${summary.totalQuantity} món tạm tính`;
            cartPriceDisplay.textContent = formatPrice(summary.totalPrice) + 'đ';
            stickyCartWidget.classList.add('show');
        } else {
            stickyCartWidget.classList.remove('show');
        }
    }

    // Helper function to format price
    function formatPrice(price) {
        price = Math.round(price);
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Functions xử lý bill modal
    function openGlobalBillModal() {
        const billOverlay = document.getElementById('menu2-billOverlay');
        if (billOverlay) {
            renderGlobalBillItems();
            billOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeGlobalBillModal() {
        const billOverlay = document.getElementById('menu2-billOverlay');
        if (billOverlay) {
            billOverlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    }

    function renderGlobalBillItems() {
        const billItemsContainer = document.getElementById('menu2-billItemsContainer');
        const billTotalPriceDisplay = document.getElementById('menu2-billTotalPriceDisplay');

        if (!billItemsContainer || !billTotalPriceDisplay) return;

        const cartData = window.CartCookies.loadCart() || {};
        const summary = window.CartCookies.getCartSummary();

        billItemsContainer.innerHTML = '';

        if (Object.keys(cartData).length === 0) {
            billItemsContainer.innerHTML = '<p style="text-align:center; color:#6c757d; padding: 20px 0;">Giỏ hàng của bạn đang trống.</p>';
            billTotalPriceDisplay.textContent = '0đ';
            return;
        }

        for (const itemId in cartData) {
            const item = cartData[itemId];
            const itemTotalPrice = item.price * item.quantity;
            const itemHtml = `
                <div class="menu2-bill-item" data-item-id="${itemId}">
                    <div class="menu2-item-info">
                        <p class="menu2-item-name">${escapeHtml(item.name)}</p>
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

        billTotalPriceDisplay.textContent = formatPrice(summary.totalPrice) + 'đ';

        // Add event listeners cho các nút trong bill items
        addBillItemEventListeners();
        
        // Cập nhật hiển thị mã giảm giá (nếu có)
        if (currentDiscount) {
            // Tính lại với tổng tiền mới
            const newDiscount = {...currentDiscount};
            newDiscount.subtotal = summary.totalPrice;
            
            if (newDiscount.discountType === 'phantram') {
                newDiscount.discountAmount = (summary.totalPrice * newDiscount.discountValue) / 100;
            } else {
                newDiscount.discountAmount = Math.min(newDiscount.discountValue, summary.totalPrice);
            }
            
            newDiscount.finalAmount = Math.max(0, summary.totalPrice - newDiscount.discountAmount);
            currentDiscount = newDiscount;
            updateDiscountDisplay();
        }
    }

    function addBillItemEventListeners() {
        const billItemsContainer = document.getElementById('menu2-billItemsContainer');
        if (!billItemsContainer) return;

        // Remove existing listeners để tránh duplicate
        const existingContainer = billItemsContainer.cloneNode(true);
        billItemsContainer.parentNode.replaceChild(existingContainer, billItemsContainer);

        existingContainer.addEventListener('click', function (e) {
            const actionTarget = e.target.closest('[data-action]');
            if (!actionTarget) return;

            const action = actionTarget.dataset.action;
            const itemDiv = e.target.closest('.menu2-bill-item');
            if (!itemDiv) return;

            const itemId = itemDiv.dataset.itemId;
            const cartData = window.CartCookies.loadCart() || {};

            if (!cartData[itemId]) return;

            if (action === 'bill-qty-increase') {
                window.CartCookies.updateItemQuantity(itemId, cartData[itemId].quantity + 1);
            } else if (action === 'bill-qty-decrease') {
                if (cartData[itemId].quantity > 1) {
                    window.CartCookies.updateItemQuantity(itemId, cartData[itemId].quantity - 1);
                } else {
                    window.CartCookies.removeItem(itemId);
                }
            } else if (action === 'delete-item') {
                window.CartCookies.removeItem(itemId);
            }

            updateGlobalCartDisplay();
            renderGlobalBillItems();
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // === XỬ LÝ MÃ GIẢM GIÁ ===
    
    function handleApplyDiscount() {
        const discountInput = document.getElementById('menu2-discountCode');
        const discountMessage = document.getElementById('menu2-discountMessage');
        const applyBtn = document.getElementById('menu2-applyDiscountBtn');
        
        const code = discountInput.value.trim();
        
        if (!code) {
            showDiscountMessage('Vui lòng nhập mã giảm giá', 'error');
            return;
        }
        
        const summary = window.CartCookies.getCartSummary();
        if (summary.totalQuantity === 0) {
            showDiscountMessage('Giỏ hàng của bạn đang trống', 'error');
            return;
        }
        
        // Disable button và hiện trạng thái loading
        applyBtn.disabled = true;
        applyBtn.textContent = 'Đang kiểm tra...';
        
        // Gọi API kiểm tra mã
        fetch('index.php?page=menu&action=validateDiscount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `code=${encodeURIComponent(code)}&total=${summary.totalPrice}`
        })
        .then(response => response.json())
        .then(data => {
            applyBtn.disabled = false;
            applyBtn.textContent = 'Áp dụng';
            
            if (data.success) {
                currentDiscount = data.data;
                showDiscountMessage(data.message, 'success');
                updateDiscountDisplay();
                discountInput.value = ''; // Xóa input sau khi áp dụng thành công
            } else {
                showDiscountMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            applyBtn.disabled = false;
            applyBtn.textContent = 'Áp dụng';
            showDiscountMessage('Có lỗi xảy ra, vui lòng thử lại', 'error');
        });
    }
    
    function handleRemoveDiscount() {
        currentDiscount = null;
        showDiscountMessage('Đã bỏ mã giảm giá', 'success');
        updateDiscountDisplay();
        
        // Xóa message sau 2 giây
        setTimeout(() => {
            const discountMessage = document.getElementById('menu2-discountMessage');
            if (discountMessage) {
                discountMessage.className = 'menu2-discount-message';
                discountMessage.textContent = '';
            }
        }, 2000);
    }
    
    function showDiscountMessage(message, type) {
        const discountMessage = document.getElementById('menu2-discountMessage');
        if (discountMessage) {
            discountMessage.textContent = message;
            discountMessage.className = `menu2-discount-message ${type}`;
        }
    }
    
    function updateDiscountDisplay() {
        const discountDetails = document.getElementById('menu2-discountDetails');
        const billTotalPrice = document.getElementById('menu2-billTotalPriceDisplay');
        
        if (!currentDiscount) {
            // Không có mã giảm giá
            if (discountDetails) {
                discountDetails.style.display = 'none';
            }
            
            // Hiển thị tổng tiền gốc
            const summary = window.CartCookies.getCartSummary();
            if (billTotalPrice) {
                billTotalPrice.textContent = formatPrice(summary.totalPrice) + 'đ';
            }
        } else {
            // Có mã giảm giá
            if (discountDetails) {
                discountDetails.style.display = 'block';
            }
            
            // Cập nhật các giá trị
            const subtotalEl = document.getElementById('menu2-subtotalPrice');
            const discountPercentEl = document.getElementById('menu2-discountPercent');
            const discountAmountEl = document.getElementById('menu2-discountAmount');
            const finalPriceEl = document.getElementById('menu2-finalPrice');
            
            if (subtotalEl) {
                subtotalEl.textContent = formatPrice(currentDiscount.subtotal) + 'đ';
            }
            if (discountPercentEl) {
                discountPercentEl.textContent = currentDiscount.discountPercent + '%';
            }
            if (discountAmountEl) {
                discountAmountEl.textContent = '-' + formatPrice(currentDiscount.discountAmount) + 'đ';
            }
            if (finalPriceEl) {
                finalPriceEl.innerHTML = '<strong>' + formatPrice(currentDiscount.finalAmount) + 'đ</strong>';
            }
            if (billTotalPrice) {
                billTotalPrice.textContent = formatPrice(currentDiscount.finalAmount) + 'đ';
            }
        }
    }

    // Expose global functions
    window.updateGlobalCart = updateGlobalCartDisplay;
    window.openGlobalBillModal = openGlobalBillModal;
    window.closeGlobalBillModal = closeGlobalBillModal;
    window.getCurrentDiscount = () => currentDiscount; // Export để sử dụng khi đặt bàn

})();