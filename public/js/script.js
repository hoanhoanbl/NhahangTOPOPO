// // JavaScript cho Website Đặt Bàn Nhà Hàng

// // Giỏ hàng tạm thời
// let tempCart = [];

// // Thêm món vào giỏ hàng tạm thời
// function addToTempCart(id, name, price) {
//     // Kiểm tra xem món đã có trong giỏ hàng chưa
//     const existingItem = tempCart.find(item => item.id === id);

//     if (existingItem) {
//         // Nếu đã có, tăng số lượng
//         existingItem.quantity += 1;
//         showMessage(`Đã tăng số lượng ${name} trong giỏ hàng!`, 'success');
//     } else {
//         // Nếu chưa có, thêm mới
//         tempCart.push({
//             id: id,
//             name: name,
//             price: price,
//             quantity: 1
//         });
//         showMessage(`Đã thêm ${name} vào giỏ hàng!`, 'success');
//     }

//     // Cập nhật hiển thị giỏ hàng
//     updateCartDisplay();
// }

// // Hiển thị thông báo
// function showMessage(message, type = 'info') {
//     // Tạo element thông báo
//     const messageEl = document.createElement('div');
//     messageEl.className = `message message-${type}`;
//     messageEl.textContent = message;
//     messageEl.style.cssText = `
//         position: fixed;
//         top: 20px;
//         right: 20px;
//         background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
//         color: white;
//         padding: 15px 20px;
//         border-radius: 5px;
//         z-index: 9999;
//         box-shadow: 0 2px 10px rgba(0,0,0,0.2);
//         transition: opacity 0.3s ease;
//     `;

//     document.body.appendChild(messageEl);

//     // Tự động ẩn sau 3 giây
//     setTimeout(() => {
//         messageEl.style.opacity = '0';
//         setTimeout(() => {
//             document.body.removeChild(messageEl);
//         }, 300);
//     }, 3000);
// }

// // Cập nhật hiển thị giỏ hàng
// function updateCartDisplay() {
//     const cartCount = tempCart.reduce((total, item) => total + item.quantity, 0);
//     const cartTotal = tempCart.reduce((total, item) => total + (item.price * item.quantity), 0);

//     // Cập nhật số lượng trong giỏ hàng (nếu có element)
//     const cartCountEl = document.querySelector('.cart-count');
//     if (cartCountEl) {
//         cartCountEl.textContent = cartCount;
//         cartCountEl.style.display = cartCount > 0 ? 'inline' : 'none';
//     }

//     console.log('Giỏ hàng hiện tại:', tempCart);
//     console.log('Tổng số món:', cartCount);
//     console.log('Tổng tiền:', new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(cartTotal));
// }

// // Xem giỏ hàng
// function viewCart() {
//     if (tempCart.length === 0) {
//         showMessage('Giỏ hàng của bạn đang trống!', 'info');
//         return;
//     }

//     let cartHTML = '<div class="cart-modal"><div class="cart-content"><h3>Giỏ hàng của bạn</h3><div class="cart-items">';

//     tempCart.forEach(item => {
//         cartHTML += `
//             <div class="cart-item">
//                 <span class="item-name">${item.name}</span>
//                 <span class="item-quantity">x${item.quantity}</span>
//                 <span class="item-price">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price * item.quantity)}</span>
//                 <button onclick="removeFromCart('${item.id}')" class="remove-btn">Xóa</button>
//             </div>
//         `;
//     });

//     const total = tempCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
//     cartHTML += `</div><div class="cart-total">Tổng cộng: ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total)}</div>`;
//     cartHTML += '<div class="cart-actions"><button onclick="closeCart()" class="btn-secondary">Đóng</button><button onclick="proceedToBooking()" class="btn-primary">Đặt bàn</button></div>';
//     cartHTML += '</div></div>';

//     document.body.insertAdjacentHTML('beforeend', cartHTML);
// }

// // Xóa món khỏi giỏ hàng
// function removeFromCart(id) {
//     tempCart = tempCart.filter(item => item.id !== id);
//     updateCartDisplay();
//     closeCart();
//     showMessage('Đã xóa món khỏi giỏ hàng!', 'success');
// }

// // Đóng modal giỏ hàng
// function closeCart() {
//     const cartModal = document.querySelector('.cart-modal');
//     if (cartModal) {
//         cartModal.remove();
//     }
// }

// // Chuyển đến trang đặt bàn
// function proceedToBooking() {
//     if (tempCart.length === 0) {
//         showMessage('Giỏ hàng trống!', 'error');
//         return;
//     }

//     // Lưu giỏ hàng vào sessionStorage
//     sessionStorage.setItem('tempCart', JSON.stringify(tempCart));

//     // Chuyển đến trang đặt bàn
//     window.location.href = '?page=booking';
// }

// // Khởi tạo khi trang load
// document.addEventListener('DOMContentLoaded', function () {
//     // Khôi phục giỏ hàng từ sessionStorage nếu có
//     const savedCart = sessionStorage.getItem('tempCart');
//     if (savedCart) {
//         tempCart = JSON.parse(savedCart);
//         updateCartDisplay();
//     }

//     console.log('Menu page loaded successfully!');
// });