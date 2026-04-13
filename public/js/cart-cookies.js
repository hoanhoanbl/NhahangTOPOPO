/**
 * Cart Cookies Manager
 * Quản lý lưu trữ giỏ hàng trong cookies
 */
class CartCookies {
    constructor() {
        this.CART_COOKIE_NAME = 'restaurant_cart';
        this.COOKIE_EXPIRE_DAYS = 7; // Cookies tồn tại 7 ngày
    }

    /**
     * Lưu giỏ hàng vào cookies
     * @param {Object} cartData - Dữ liệu giỏ hàng dạng { itemId: { name, price, quantity }, ... }
     */
    saveCart(cartData) {
        try {
            const cartString = JSON.stringify(cartData);
            const expirationDate = new Date();
            expirationDate.setDate(expirationDate.getDate() + this.COOKIE_EXPIRE_DAYS);

            document.cookie = `${this.CART_COOKIE_NAME}=${encodeURIComponent(cartString)}; expires=${expirationDate.toUTCString()}; path=/`;
            console.log('Cart saved to cookies successfully');
        } catch (error) {
            console.error('Error saving cart to cookies:', error);
        }
    }

    /**
     * Đọc giỏ hàng từ cookies
     * @returns {Object|null} - Dữ liệu giỏ hàng hoặc null nếu không có
     */
    loadCart() {
        try {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === this.CART_COOKIE_NAME) {
                    const cartString = decodeURIComponent(value);
                    const cartData = JSON.parse(cartString);
                    console.log('Cart loaded from cookies successfully');
                    return cartData;
                }
            }
            return null;
        } catch (error) {
            console.error('Error loading cart from cookies:', error);
            return null;
        }
    }

    /**
     * Xóa giỏ hàng khỏi cookies
     */
    clearCart() {
        try {
            document.cookie = `${this.CART_COOKIE_NAME}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
            console.log('Cart cleared from cookies successfully');
        } catch (error) {
            console.error('Error clearing cart from cookies:', error);
        }
    }

    /**
     * Kiểm tra xem có giỏ hàng trong cookies không
     * @returns {boolean}
     */
    hasCart() {
        const cartData = this.loadCart();
        return cartData !== null && Object.keys(cartData).length > 0;
    }

    /**
     * Thêm một item vào giỏ hàng trong cookies
     * @param {string} itemId - ID của món ăn
     * @param {Object} itemData - Dữ liệu của món ăn { name, price, quantity }
     */
    addItem(itemId, itemData) {
        const currentCart = this.loadCart() || {};

        if (currentCart[itemId]) {
            // Nếu món đã có, tăng số lượng
            currentCart[itemId].quantity += itemData.quantity;
        } else {
            // Nếu món chưa có, thêm mới
            currentCart[itemId] = { ...itemData };
        }

        this.saveCart(currentCart);
        return currentCart;
    }

    /**
     * Cập nhật số lượng của một item trong giỏ hàng
     * @param {string} itemId - ID của món ăn
     * @param {number} newQuantity - Số lượng mới
     */
    updateItemQuantity(itemId, newQuantity) {
        const currentCart = this.loadCart() || {};

        if (currentCart[itemId]) {
            if (newQuantity <= 0) {
                // Nếu số lượng <= 0, xóa item
                delete currentCart[itemId];
            } else {
                currentCart[itemId].quantity = newQuantity;
            }
            this.saveCart(currentCart);
        }

        return currentCart;
    }

    /**
     * Xóa một item khỏi giỏ hàng
     * @param {string} itemId - ID của món ăn
     */
    removeItem(itemId) {
        const currentCart = this.loadCart() || {};
        delete currentCart[itemId];
        this.saveCart(currentCart);
        return currentCart;
    }

    /**
     * Lấy tổng số lượng và tổng tiền của giỏ hàng
     * @returns {Object} - { totalQuantity, totalPrice }
     */
    getCartSummary() {
        const cartData = this.loadCart() || {};
        let totalQuantity = 0;
        let totalPrice = 0;

        for (const itemId in cartData) {
            const item = cartData[itemId];
            totalQuantity += item.quantity;
            totalPrice += (item.price * item.quantity);
        }

        return { totalQuantity, totalPrice };
    }
}

// Tạo instance global
window.CartCookies = new CartCookies();