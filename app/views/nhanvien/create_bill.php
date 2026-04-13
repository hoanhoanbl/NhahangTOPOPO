
<style>
/* Styles cho phần đơn đặt bàn */
.booking-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.filter-row {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 500;
    color: #374151;
    font-size: 0.9rem;
}

.filter-input, .filter-select {
    padding: 0.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.9rem;
    min-width: 150px;
}

.filter-input:focus, .filter-select:focus {
    outline: none;
    border-color: #21A256;
}

.filter-btn {
    background: #21A256;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    margin-top: auto;
}

.filter-btn:hover {
    background: #1B8B47;
}

.bookings-table {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: between;
    align-items: center;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e293b;
}

.table-stats {
    color: #64748b;
    font-size: 0.9rem;
}

.table-container {
    overflow-x: auto;
}


.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.pending {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.confirmed {
    background: #dcfce7;
    color: #16a34a;
}

.status-badge.cancelled {
    background: #fecaca;
    color: #dc2626;
}

.status-badge.completed {
    background: #dbeafe;
    color: #2563eb;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-info {
    background: #3b82f6;
    color: white;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-sm:hover {
    opacity: 0.8;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: #f8fafc;
}

.pagination a, .pagination span {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    text-decoration: none;
    color: #374151;
}

.pagination a:hover {
    background: #f3f4f6;
}

.pagination .current {
    background: #21A256;
    color: white;
    border-color: #21A256;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #cbd5e1;
}

.booking-detail {
    font-size: 0.875rem;
    line-height: 1.4;
}

.customer-info {
    font-weight: 500;
    color: #1e293b;
}

.booking-time {
    color: #059669;
    font-weight: 500;
}

.table-count {
    color: #6b7280;
    font-size: 0.8rem;
}
  .menu2-grid {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }
    
    /* For specific category tabs - use grid layout */
    .menu2-grid.menu2-category-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
         margin: 22px;
    }
    
    /* Category sections for "Tất Cả" tab */
    .menu2-category-section {
        margin-bottom: 30px;
    }
    
    .menu2-category-title {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-left: 10px;
        border-left: 4px solid var(--colorYellow);
    }
    
    .menu2-category-items {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }
    
    .menu2-no-items {
        text-align: center;
        padding: 40px 20px;
        background: #fff;
        border-radius: 10px;
        color: #666;
        font-size: 16px;
    }
/* ---- Bắt đầu CSS được sửa đổi ---- */

.menu2-card {
    background: #fff;
    display: flex;
    height: 120px; /* Giữ chiều cao cố định để layout ổn định */
    border-radius: 12px; /* Bo góc mềm mại hơn */
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08); /* Thêm bóng mờ nhẹ cho đẹp */
    margin-bottom: 15px; /* Thêm khoảng cách giữa các card */
}

.menu2-card img {
    width: 110px;
    height: 100%;
    object-fit: cover;
    /* Không cần bo góc ở đây vì đã có overflow:hidden ở card cha */
}

.menu2-card-content {
    flex: 1; /* Chiếm hết không gian còn lại */
    padding: 12px 15px; /* Thêm padding trên/dưới và trái/phải */
    display: flex;
    flex-direction: column;
    /* Đây là thuộc tính quan trọng nhất: */
    justify-content: space-between; /* Đẩy info lên trên và actions xuống dưới */
    box-sizing: border-box;
}

/* Nhóm chứa tên và giá */
.menu2-card-info {
    /* Không cần style đặc biệt, chỉ để nhóm các phần tử */
}

.menu2-card-name {
     margin-bottom: 4px;
        font-size: 17px;
        color: #333;
}

.menu2-card-price {
    display: block;
    font-weight: 500;
    color: #1B4E30;
}

.menu2-card-actions {
    /* Đẩy toàn bộ vùng actions sang bên phải */
    display: flex;
    justify-content: flex-end;
}

.menu2-btn-add-to-cart {
    border: 1px solid gainsboro;
        padding: 3px 12px;
        border-radius: 16px;
        font-size: 12px;
        cursor: pointer;
        display: inline-block;
}

.menu2-btn-add-to-cart:hover {
    background-color: #f0f0f0;
    border-color: #ccc;
}

/* ---- Kết thúc CSS được sửa đổi ---- */
/* sticky-cart-widget ----------------- */

    #sticky-cart-widget {
position: fixed;
top: 50%;
right: 0px;
left: auto !important;
transform: translateY(-50%);
display: inline-flex;
background: #1B4E30;
color: #fff;
border-radius: 8px 0 0 8px;
box-shadow: 0 4px 15px rgba(0,0,0,.2);
cursor: pointer; 
overflow: hidden; 
opacity: 0; 
transition: opacity 0.3s ease, transform 0.3s ease;
padding: 12px 16px;
z-index: 999;
}

#sticky-cart-widget.show {
  opacity: 1;
  transform: translateY(-50%);
}

.cart-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

#cart-item-count {
  font-size: 14px;
  opacity: 0.8;
  font-weight: 500;
}

#cart-total-price {
  font-size: 19px;
  font-weight: 700;
}

 
    /* === CSS CHO BILL MODAL (MỚI THÊM TỪ FILE CỦA BẠN) === */

    /* --- Lớp phủ mờ phía sau --- */
    .create-bill {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        justify-content: center;
        align-items: center;
        z-index: var(--z-index-modal); /* Cao hơn modal chi tiết */

        /* Logic ẩn/hiện */
        display: none; 
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }
    .create-bill.show {
        display: flex;
        opacity: 1;
    }

    /* --- Khung Bill chính --- */
    .create-bill-modal {
        background-color: #ffffff;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        animation: billFadeIn 0.3s ease-out; /* Đổi tên animation để tránh trùng */
        display: flex;
        flex-direction: column;
    }

    @keyframes billFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    /* --- Header của Bill --- */
    .create-bill-header {
        background-color: #f39c12;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        border-radius: 12px 12px 0 0;
    }

    .create-bill-title {
        color: #212529;
        font-size: 1.5rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .create-bill-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bill-save-button {
        background-color: transparent;
        border: 1px solid #212529;
        color: #212529;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 0.8rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .menu2-close-button {
        background-color: #ffffff;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-size: 1rem;
        font-weight: bold;
        color: #212529;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* --- Thân của Bill --- */
    .bill-body {
        padding: 25px;
        flex: 1;
        overflow-y: auto;
        min-height: 0;
        scroll-behavior: smooth;
    }

    /* Custom scrollbar */
    .bill-body::-webkit-scrollbar { width: 4px; }
    .bill-body::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .bill-body::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    .bill-body::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

    /* Phần tổng tiền */
    .bill-total-summary {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .bill-total-left .bill-total-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 0;
    }

    .bill-total-left .bill-total-note {
        font-size: 11px;
        color: #6c757d;
        margin-top: 5px;
        max-width: 250px;
    }
    
    .bill-total-right .bill-total-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #212529;
        text-align: right;
    }

    .bill-total-right .bill-clear-bill {
        font-size: 0.8rem;
        color: #6c757d;
        text-decoration: none;
        display: inline-block;
        margin-top: 5px;
        cursor: pointer;
    }
    .bill-clear-bill i {
        margin-right: 5px;
    }
    .bill-clear-bill:hover {
        color: #c0392b; /* Màu đỏ khi hover */
    }

    /* Danh sách món ăn */
    .bill-items {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .menu2-bill-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .menu2-bill-item:last-child {
        border-bottom: none;
    }

    .menu2-item-info { width: 40%; }
    .menu2-item-info .menu2-item-name {
        font-weight: bold;
        margin: 0;
        font-size: 0.95rem; /* Giảm kích thước chữ 1 chút */
    }
    .menu2-item-info .menu2-item-price {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .menu2-item-total-price {
        font-weight: bold;
        font-size: 0.9rem;
    }
    .menu2-delete_item {
        cursor: pointer;
        padding: 5px 10px;
        color: #c0392b; /* Màu đỏ cho dễ thấy */
        font-size: 0.9rem;
    }

    .menu2-item-controls {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .menu2-btn-increase, .menu2-btn-decrease {
        background: #f39c12;
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .menu2-btn-increase:hover, .menu2-btn-decrease:hover {
        background: #e67e22;
    }

    .menu2-btn-decrease:disabled {
        background: #bdc3c7;
        cursor: not-allowed;
    }

    .menu2-quantity {
        min-width: 30px;
        text-align: center;
        font-weight: bold;
        font-size: 14px;
    }

    /* --- Footer của Bill --- */
    .bill-footer {
        background-color: #f8f9fa;
        padding: 20px 25px;
        text-align: center;
        border-top: 1px solid #e9ecef;
        flex-shrink: 0;
        border-radius: 0 0 12px 12px;
    }

    .bill-cta-button {
        background-color: #f39c12;
        color: black;
        border: none;
        padding: 15px;
        width: 100%; /* Cho nút full-width */
        border-radius: 30px;
        cursor: pointer;
        text-transform: uppercase;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .bill-footer-note {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .bill-footer-note span {
        font-weight: bold;
        color: #212529;
    }

    /* Styles cho modal thông tin khách hàng */
    #customer-info-modal {
        z-index: 1100; /* Cao hơn modal bill */
    }

    #customer-info-modal .create-bill-modal {
        max-width: 600px;
        max-height: 95vh;
    }

    #customer-info-modal .bill-body {
        max-height: 70vh;
    }

    #customer-info-modal input:focus,
    #customer-info-modal textarea:focus {
        outline: none;
        border-color: #21A256;
        box-shadow: 0 0 0 2px rgba(33, 162, 86, 0.2);
    }

    #customer-info-modal input:invalid {
        border-color: #ef4444;
    }

    #customer-info-modal input:invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    #customer-info-modal .bill-cta-button:disabled {
        background-color: #bdc3c7;
        cursor: not-allowed;
    }

    /* === CSS CHO BOOKING FORM NV (Đồng nhất với menu2-bookingOverlay) === */
    .nv-booking-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: var(--z-index-overlay, 1200);
    }

    .nv-booking-overlay.show {
        display: flex; 
        opacity: 1;  
    }

    .nv-booking-form-container {
        background-color: white;
        width: 90%;
        max-width: 600px;
        min-width: 500px;
        border-radius: 12px;
        max-height: 90vh;
        position: relative;
        animation: fadeIn 0.3s ease-out;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .nv-form-header {
        background-color: #f39c12;
        color: white;
        padding: 20px 30px;
        border-radius: 12px 12px 0 0;
        flex-shrink: 0;
    }

    .nv-form-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: white;
        margin: 0;
        text-align: center;
    }

    .nv-form-body {
        flex: 1;
        overflow-y: auto;
        padding: 30px;
        min-height: 0;
        scroll-behavior: smooth;
    }

    .nv-form-body::-webkit-scrollbar {
        width: 6px;
    }

    .nv-form-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }

    .nv-form-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 6px;
    }

    .nv-form-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .nv-form-footer {
        background-color: #f8f9fa;
        padding: 20px 30px;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 12px 12px;
        flex-shrink: 0;
    }

    .nv-form-section-title {
        font-size: 1rem;
        font-weight: 500;
        color: #495057;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
    }

    .nv-form-section-title i {
        color: #f39c12;
        font-size: 1.1rem;
    }

    .nv-form-group {
        margin-bottom: 22px;
    }

    .nv-form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.8rem;
        color: #868e96;
    }

    .nv-form-row {
        display: grid;
        grid-template-columns: 1fr 1.3fr 1fr;
        gap: 20px;
        align-items: end;
    }

    .nv-form-input, .nv-form-select, .nv-form-textarea {
        width: 100%;
        padding: 8px 16px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.81rem;
        color: #495057;
        background-color: #fff;
        box-sizing: border-box;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .nv-form-textarea {
        width: 100%;
        height: 80px;
        padding: 8px 16px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.81rem;
        color: #495057;
        background-color: #fff;
        box-sizing: border-box;
        font-weight: 500;
    }

    .nv-form-row .nv-form-select,
    .nv-form-row .nv-input-with-icon {
        height: 35px;
        box-sizing: border-box;
    }

    .nv-form-row .nv-input-with-icon .nv-form-input {
        height: 100%;
    }

    .nv-form-input:focus, .nv-form-select:focus, .nv-form-textarea:focus {
        outline: none;
        border-color: #f39c12;
    }

    .nv-form-input::placeholder, .nv-form-textarea::placeholder {
        color: #adb5bd;
        font-weight: 400;
    }

    .nv-form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f39c12' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 1.1em;
        cursor: pointer;
    }

    .nv-input-with-icon {
        position: relative;
    }

    .nv-input-with-icon i {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #f39c12;
        pointer-events: none;
        font-size: 1.1rem;
    }

    .nv-input-with-icon .nv-form-input {
        padding-right: 45px;
        cursor: pointer;
    }

    .nv-form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        margin: 0;
    }

    .nv-btn {
        padding: 10px 20px;
        font-size: 0.9rem;
        border-radius: 30px;
        cursor: pointer;
        border: none;
        text-transform: uppercase;
    }

    .nv-btn-secondary {
        background-color: transparent;
        color: #6c757d;
    }

    .nv-btn-primary {
        background-color: #f39c12;
        color: black;
    }

    .nv-btn-primary:hover {
        background-color: #e67e22;
    }

    .nv-btn-primary:disabled,
    .nv-btn-primary.disabled {
        background-color: #bdc3c7 !important;
        cursor: not-allowed !important;
        opacity: 0.6;
        color: #6c757d !important;
    }

    .nv-btn-primary:disabled:hover,
    .nv-btn-primary.disabled:hover {
        background-color: #bdc3c7 !important;
    }

    /* === CSS CHO PHẦN CHỌN BÀN === */
    .nv-tables-loading {
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }

    .nv-tables-loading i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .nv-tables-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 10px;
    }

    .nv-table-item {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        text-align: center;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .nv-table-item:hover {
        border-color: #f39c12;
        box-shadow: 0 2px 8px rgba(243, 156, 18, 0.1);
    }

    .nv-table-item.selected {
        border-color: #f39c12;
        background: #fef9e7;
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        transform: translateY(-2px);
    }

    .nv-table-item.selected::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 12px;
        background: #f39c12;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .nv-table-info {
        width: 100%;
    }

    .nv-table-name {
        font-weight: 600;
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 8px;
    }

    .nv-table-capacity {
        font-size: 0.9rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .nv-table-capacity i {
        color: #f39c12;
    }

    .nv-tables-empty {
        text-align: center;
        padding: 30px;
        color: #6c757d;
    }

    .nv-tables-empty i {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #dee2e6;
    }

    .nv-tables-error {
        text-align: center;
        padding: 20px;
        color: #dc3545;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
    }

    .nv-tables-error i {
        margin-right: 8px;
    }

@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-input, .filter-select {
        min-width: auto;
        width: 100%;
    }
    
    .table-container {
        font-size: 0.875rem;
    }
    
    th, td {
        padding: 0.75rem 0.5rem;
    }

    /* Responsive cho booking form */
    .nv-booking-form-container {
        width: 95%;
        min-width: auto;
        max-height: 95vh;
    }

    .nv-form-header {
        padding: 15px 20px;
    }

    .nv-form-title {
        font-size: 1.5rem;
    }

    .nv-form-body {
        padding: 20px;
    }

    .nv-form-footer {
        padding: 15px 20px;
    }

    .nv-form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .nv-form-row .nv-form-select,
    .nv-form-row .nv-input-with-icon {
        height: auto;
        min-height: 35px;
    }

    .nv-form-actions {
        flex-direction: column;
        gap: 12px;
    }

    .nv-btn {
        width: 100%;
        text-align: center;
    }

    .nv-tables-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .nv-table-item {
        padding: 15px;
        min-height: 80px;
    }

    .nv-table-name {
        font-size: 1rem;
    }

    .nv-table-capacity {
        font-size: 0.8rem;
    }
    
}
</style>
<!-- Booking Filters -->
<div class="booking-filters">
    <form id="menu-search-form" method="GET" action="">
        <input type="hidden" name="page" value="nhanvien">
        <input type="hidden" name="action" value="dashboard">
        <input type="hidden" name="section" value="bookings">
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="search">Tìm kiếm món ăn</label>
                <input type="text" id="search" name="search" class="filter-input" 
                       placeholder="Nhập tên món ăn..." 
                       value="">
            </div>
            
            <button type="button" id="search-btn" class="filter-btn">
                <i class="fas fa-search"></i>
                Tìm
            </button>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="bookings-table">
    <div class="table-header">
        <h3 class="table-title">Danh sách món ăn</h3>
    </div>
    
    <!-- Loading indicator -->
    <div id="loading-indicator" style="display: none; text-align: center; padding: 2rem;">
        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #21A256;"></i>
        <p style="margin-top: 1rem; color: #666;">Đang tìm kiếm...</p>
    </div>
    
    <div class="table-container">
         <?php
// Tạm thời ẩn dữ liệu mẫu - sẽ được thay thế bằng kết quả tìm kiếm AJAX
?>

<div class="menu2-grid menu2-category-grid" id="menu2-grid">
    <!-- Kết quả tìm kiếm sẽ được hiển thị ở đây bằng JavaScript -->
    <div class="empty-state" style="grid-column: 1 / -1;">
        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
        <h3>Hãy tìm kiếm món ăn</h3>
        <p>Nhập tên món ăn vào ô tìm kiếm để bắt đầu</p>
        <!-- Test buttons -->
        <button onclick="testAddToCart()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #21A256; color: white; border: none; border-radius: 6px; cursor: pointer; margin-right: 10px;">
            Test - Thêm món giả vào giỏ hàng
        </button>
        <button onclick="testOpenBillModal()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #f39c12; color: white; border: none; border-radius: 6px; cursor: pointer; margin-right: 10px;">
            Test - Mở Bill Modal
        </button>
        <button onclick="testShowBookingForm()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Test - Mở Booking Form
        </button>
    </div>
</div>


<div id="create-bill" class="create-bill">
    <div class="create-bill-modal">
        <header class="create-bill-header">
            <div class="create-bill-title"><i class="fas fa-receipt"></i><span>Tạm tính</span></div>
            <div class="create-bill-header-actions">
                <button class="bill-save-button"><i class="fas fa-download"></i> LƯU VỀ MÁY</button>
                <button id="bill-CloseBtn" class="menu2-close-button"><i class="fas fa-times"></i></button>
            </div>
        </header>
        <section class="bill-body">
            <div class="bill-total-summary">
                <div class="bill-total-left">
                    <h3 class="bill-total-title">Tổng tiền</h3>
                    <p class="bill-total-note">Đơn giá tạm tính chỉ mang tính chất tham khảo.</p>
                </div>
                <div class="bill-total-right">
                    <div id="bill-TotalPriceDisplay" class="bill-total-price">0đ</div>
                    <a id="bill-ClearAllBtn" href="#" class="bill-clear-bill"><i class="fas fa-trash-alt"></i> Xoá hết tạm tính</a>
                </div>
            </div>
            <div id="bill-temsContainer" class="bill-items"></div>
        </section>
        <footer class="bill-footer">
            <button id="bill-proceedToBookingBtn" class="bill-cta-button">Tạo đơn</button>
            <p class="bill-footer-note">Hoặc gọi <span>*1986</span> để đặt bàn</p>
        </footer>
    </div>
</div>

<div id="booking-info-nv" class="nv-booking-overlay">
    <div class="nv-booking-form-container">
        <!-- Header cố định -->
        <div class="nv-form-header">
            <h1 class="nv-form-title">Đặt bàn</h1>
        </div>

        <!-- Body có scroll -->
        <div class="nv-form-body">
            <form id="nv-bookingForm">
                <div class="nv-form-section">
                    <h3 class="nv-form-section-title"><i class="fas fa-user"></i>Thông tin khách hàng</h3>
                    <div class="nv-form-group">
                        <input type="text" class="nv-form-input" placeholder="Tên khách (tuỳ chọn)">
                    </div>
                    <div class="nv-form-group">
                        <input type="tel" class="nv-form-input" placeholder="Số điện thoại (tuỳ chọn)">
                    </div>
                </div>

                <div class="nv-form-section">
                    <h3 class="nv-form-section-title"><i class="fas fa-calendar-check"></i>Thông tin đặt bàn</h3>
                    <div class="nv-form-row">
                        <div class="nv-form-group">
                            <label>Số lượng người</label>
                            <input type="number" class="nv-form-input" id="nv-booking-guests-input" value="1" min="1" placeholder="Nhập số lượng người">
                        </div>
                        <div class="nv-form-group">
                            <label for="nv-date-display-input">Ngày đặt bàn</label>
                            <div class="nv-input-with-icon">
                               <input type="datetime-local" name="thoiGianBatDau" class="form-control" id="currentDateTime" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="nv-form-group">
                    <label>Ghi chú</label>
                    <textarea class="nv-form-textarea" placeholder="Ghi chú thêm..."></textarea>
                </div>

                <div class="nv-form-section">
                    <h3 class="nv-form-section-title"><i class="fas fa-chair"></i>Chọn bàn</h3>
                    <div id="nv-available-tables-container">
                        <div class="nv-tables-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Đang tải danh sách bàn...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer cố định -->
        <div class="nv-form-footer">
            <div class="nv-form-actions">
                <button type="button" class="nv-btn nv-btn-secondary" data-action="close-booking-form">Đóng</button>
                <button type="submit" class="nv-btn nv-btn-primary" id="nv-booking-submit-btn">Đặt bàn</button>
            </div>
        </div>
    </div>
</div>

<div id="sticky-cart-widget">
    <div class="cart-info">
        <span id="cart-item-count">0 món tạm tính</span>
        <strong id="cart-total-price">0đ</strong>
    </div>
</div>

   
</div>

<script>

     // Lấy đối tượng input
    const datetimeInput = document.getElementById('currentDateTime');

    // Tạo một đối tượng Date mới
    const now = new Date();

    // Định dạng ngày giờ theo chuẩn "YYYY-MM-DDTHH:mm" mà input type="datetime-local" yêu cầu
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');

    const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

    // Gán giá trị đã được định dạng vào input
    datetimeInput.value = formattedDateTime;
// JavaScript functions cho các thao tác đơn đặt bàn
function viewBookingDetail(maDon) {
    // Chuyển hướng đến trang chi tiết
    window.location.href = 'index.php?page=nhanvien&action=viewBookingDetail&id=' + maDon;
}

// Tự động submit form khi thay đổi thời gian (chỉ nếu element tồn tại)
const timeFilterElement = document.getElementById('time_filter');
if (timeFilterElement) {
    timeFilterElement.addEventListener('change', function() {
        this.closest('form').submit();
    });
}

// Tự động submit form khi thay đổi trạng thái (chỉ nếu element tồn tại)
const statusFilterElement = document.getElementById('status_filter');
if (statusFilterElement) {
    statusFilterElement.addEventListener('change', function() {
        this.closest('form').submit();
    });
}

function confirmBooking(maDon) {
    if (confirm('Bạn có chắc chắn muốn xác nhận đơn đặt bàn #' + maDon + '?')) {
        // TODO: Implement confirm booking
        updateBookingStatus(maDon, 'da_xac_nhan');
    }
}

function cancelBooking(maDon) {
    const reason = prompt('Lý do hủy đơn đặt bàn #' + maDon + ':');
    if (reason !== null && reason.trim() !== '') {
        // TODO: Implement cancel booking
        updateBookingStatus(maDon, 'da_huy', reason);
    }
}

function completeBooking(maDon) {
    if (confirm('Đánh dấu đơn đặt bàn #' + maDon + ' đã hoàn thành?')) {
        // TODO: Implement complete booking
        updateBookingStatus(maDon, 'hoan_thanh');
    }
}

function updateBookingStatus(maDon, status, reason = '') {
    // Tạo form ẩn để submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?page=nhanvien&action=updateBookingStatus';
    
    const maDonInput = document.createElement('input');
    maDonInput.type = 'hidden';
    maDonInput.name = 'maDon';
    maDonInput.value = maDon;
    form.appendChild(maDonInput);
    
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);
    
    if (reason) {
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// === KHỞI TẠO DỮ LIỆU GIỎ HÀNG ===
const shoppingCart = {}; // { 1: { name: '...', price: 121000, quantity: 2 }, ... }
let totalCartQuantity = 0;
let totalCartPrice = 0;

// === HÀM HELPER ===
function formatPrice(price) {
    price = Math.round(price);
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function setCurrentDateAndTime() {
    const now = new Date();
    
    // Format date (dd/mm/yyyy)
    const day = now.getDate().toString().padStart(2, '0');
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const year = now.getFullYear();
    const dateString = `${day}/${month}/${year}`;
    
    // Format time (HH:mm)
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const timeString = `${hours}:${minutes}`;
    
    // Set values
    const dateInput = document.getElementById('nv-date-display-input');
    const timeInput = document.getElementById('nv-time-display-input');
    
    if (dateInput) dateInput.value = dateString;
    if (timeInput) timeInput.value = timeString;
}

function setupQuantityControls() {
    const quantityInput = document.getElementById('nv-booking-guests-input');
    
    if (!quantityInput) return;
    
    // Xử lý input từ bàn phím - bỏ giới hạn số lượng
    quantityInput.addEventListener('input', function() {
        let value = parseInt(this.value);
        if (isNaN(value) || value < 1) {
            this.value = 1;
        }
    });
    
    // Xử lý blur để đảm bảo có giá trị
    quantityInput.addEventListener('blur', function() {
        if (!this.value || parseInt(this.value) < 1) {
            this.value = 1;
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    
    // Set ngày và giờ hiện tại
    setCurrentDateAndTime();
    
    // Setup quantity controls
    setupQuantityControls();
    
    const stickyCartWidget = document.getElementById('sticky-cart-widget');
    const cartCountDisplay = document.getElementById('cart-item-count');
    const cartPriceDisplay = document.getElementById('cart-total-price');
    
    // Kiểm tra xem các element có tồn tại không
    if (!stickyCartWidget || !cartCountDisplay || !cartPriceDisplay) {
        console.warn('Một hoặc nhiều element của sticky cart widget không tồn tại');
        return;
    }
    
    // === HÀM TÍNH TOÁN GIỎ HÀNG ===
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
        console.log('updateCartWidgetUI called - totalCartQuantity:', totalCartQuantity);
        console.log('stickyCartWidget element:', stickyCartWidget);
        console.log('cartCountDisplay element:', cartCountDisplay);
        console.log('cartPriceDisplay element:', cartPriceDisplay);
        
        if (totalCartQuantity > 0) {
            cartCountDisplay.textContent = `${totalCartQuantity} món tạm tính`;
            cartPriceDisplay.textContent = formatPrice(totalCartPrice) + 'đ';
            stickyCartWidget.classList.add('show');
            console.log('Added show class to sticky cart widget');
            console.log('Widget classes after adding show:', stickyCartWidget.className);
        } else {
            stickyCartWidget.classList.remove('show');
            console.log('Removed show class from sticky cart widget');
        }
    }
    
    function updateAllUI() {
        recalculateCartTotals();
        console.log('After recalculate - totalCartQuantity:', totalCartQuantity, 'totalCartPrice:', totalCartPrice);
        updateCartWidgetUI();
        // Kiểm tra xem modal bill có đang mở không trước khi update
        const billOverlay = document.getElementById('create-bill');
        if (billOverlay && billOverlay.classList.contains('show')) {
            updateBillModalContent();
        }
    }

    function addToCart(itemId, itemName, itemPrice, quantity = 1) {
        if (shoppingCart[itemId]) {
            shoppingCart[itemId].quantity += quantity;
        } else {
            shoppingCart[itemId] = { name: itemName, price: parseFloat(itemPrice), quantity: quantity };
        }
        console.log('Added to cart:', itemId, itemName, 'Total quantity:', totalCartQuantity);
        updateAllUI();
    }

    // Expose test functions globally
    window.testAddToCartGlobal = function() {
        console.log('Adding test item to cart...');
        addToCart('test1', 'Món test', 50000, 1);
    };

    window.testOpenBillModalGlobal = function() {
        console.log('Opening bill modal...');
        showBillModal();
    };
    
    // === HÀM MỞ MODAL BILL ===
    function openBillModal() {
        if (totalCartQuantity > 0) {
            updateBillModalContent();
            showBillModal();
        } else {
            alert('Giỏ hàng trống!');
        }
    }

    // === HÀM CẬP NHẬT NỘI DUNG MODAL BILL ===
    function updateBillModalContent() {
        const billTotalDisplay = document.getElementById('bill-TotalPriceDisplay');
        const billItemsContainer = document.getElementById('bill-temsContainer');
        
        if (billTotalDisplay) {
            billTotalDisplay.textContent = formatPrice(totalCartPrice) + 'đ';
        }
        
        if (billItemsContainer) {
            billItemsContainer.innerHTML = '';
            
            for (const itemId in shoppingCart) {
                const item = shoppingCart[itemId];
                const itemTotal = item.price * item.quantity;
                
                const billItem = document.createElement('div');
                billItem.className = 'menu2-bill-item';
                billItem.innerHTML = `
                    <div class="menu2-item-info">
                        <p class="menu2-item-name">${item.name}</p>
                        <p class="menu2-item-price">${formatPrice(item.price)}đ/món</p>
                    </div>
                    <div class="menu2-item-controls">
                        <button class="menu2-btn-decrease" data-id="${itemId}">-</button>
                        <span class="menu2-quantity">${item.quantity}</span>
                        <button class="menu2-btn-increase" data-id="${itemId}">+</button>
                    </div>
                    <div class="menu2-item-total-price">${formatPrice(itemTotal)}đ</div>
                    <div class="menu2-delete_item" data-id="${itemId}">
                        <i class="fas fa-trash"></i>
                    </div>
                `;
                
                billItemsContainer.appendChild(billItem);
            }
        }
    }

    // === HÀM HIỂN THỊ/ẨN MODAL BILL ===
    function showBillModal() {
        const billOverlay = document.getElementById('create-bill');
        if (billOverlay) {
            billOverlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // Ngăn scroll trang chính
        }
    }

    // hideBillModal đã được chuyển ra ngoài DOMContentLoaded

    // === HÀM XỬ LÝ THAY ĐỔI SỐ LƯỢNG TRONG MODAL ===
    function increaseQuantity(itemId) {
        if (shoppingCart[itemId]) {
            shoppingCart[itemId].quantity += 1;
            updateAllUI();
            updateBillModalContent();
        }
    }

    function decreaseQuantity(itemId) {
        if (shoppingCart[itemId] && shoppingCart[itemId].quantity > 1) {
            shoppingCart[itemId].quantity -= 1;
            updateAllUI();
            updateBillModalContent();
        } else if (shoppingCart[itemId] && shoppingCart[itemId].quantity === 1) {
            // Nếu chỉ còn 1 món, xóa khỏi giỏ hàng
            removeFromCart(itemId);
        }
    }

    function removeFromCart(itemId) {
        if (shoppingCart[itemId]) {
            delete shoppingCart[itemId];
            updateAllUI();
            updateBillModalContent();
            
            // Nếu giỏ hàng trống, đóng modal
            if (totalCartQuantity === 0) {
                hideBillModal();
            }
        }
    }

    function clearAllCart() {
        if (confirm('Bạn có chắc chắn muốn xóa tất cả món ăn trong tạm tính?')) {
            Object.keys(shoppingCart).forEach(key => delete shoppingCart[key]);
            updateAllUI();
            hideBillModal();
        }
    }

    function clearCartSilently() {
       Object.keys(shoppingCart).forEach(key => delete shoppingCart[key]);
        updateAllUI();
    }
    
    // === XỬ LÝ SỰ KIỆN ===
    // Xử lý click nút "+ Đặt"
    document.addEventListener('click', function (e) {
        const target = e.target;
        
        // Xử lý nút "+ Đặt" 
        if (target.closest('.menu2-btn-add-to-cart')) {
            e.stopPropagation(); 
            e.preventDefault(); 
            const btn = target.closest('.menu2-btn-add-to-cart');
            addToCart(btn.dataset.id, btn.dataset.name, btn.dataset.price, 1);
            return; 
        }
        
        // Xử lý nút tăng số lượng trong modal
        if (target.closest('.menu2-btn-increase')) {
            e.stopPropagation();
            e.preventDefault();
            const btn = target.closest('.menu2-btn-increase');
            increaseQuantity(btn.dataset.id);
            return;
        }
        
        // Xử lý nút giảm số lượng trong modal
        if (target.closest('.menu2-btn-decrease')) {
            e.stopPropagation();
            e.preventDefault();
            const btn = target.closest('.menu2-btn-decrease');
            decreaseQuantity(btn.dataset.id);
            return;
        }
        
        // Xử lý nút xóa món trong modal
        if (target.closest('.menu2-delete_item')) {
            e.stopPropagation();
            e.preventDefault();
            const btn = target.closest('.menu2-delete_item');
            removeFromCart(btn.dataset.id);
            return;
        }
        
        // Xử lý nút đóng modal
        if (target.closest('#bill-CloseBtn')) {
            e.stopPropagation();
            e.preventDefault();
            hideBillModal();
            return;
        }
        
        // Xử lý nút xóa tất cả trong modal
        if (target.closest('#bill-ClearAllBtn')) {
            e.stopPropagation();
            e.preventDefault();
            clearAllCart();
            return;
        }
        
        // Xử lý click vào overlay để đóng modal
        if (target.closest('.create-bill') && !target.closest('.create-bill-modal')) {
            hideBillModal();
            return;
        }
    });
    
    // Xử lý phím ESC để đóng modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const billOverlay = document.getElementById('create-bill');
            if (billOverlay && billOverlay.classList.contains('show')) {
                hideBillModal();
            }
        }
    });
    
    stickyCartWidget.addEventListener('click', openBillModal);
    
    // Xử lý nút "Tạo đơn" - hiển thị form booking
    const proceedToBookingBtn = document.getElementById('bill-proceedToBookingBtn');
    console.log('proceedToBookingBtn found:', proceedToBookingBtn);
    
    if (proceedToBookingBtn) {
        proceedToBookingBtn.addEventListener('click', function(e) {
            console.log('Tạo đơn button clicked!');
            console.log('totalCartQuantity:', totalCartQuantity);
            
            // Test: Luôn hiển thị form booking để debug
            console.log('Calling showBookingForm...');
            showBookingForm();
            
            // Original logic (commented for testing)
            // if (totalCartQuantity > 0) {
            //     console.log('Calling showBookingForm...');
            //     showBookingForm();
            // } else {
            //     alert('Giỏ hàng trống! Vui lòng thêm món ăn trước khi tạo đơn.');
            // }
        });
    } else {
        console.error('bill-proceedToBookingBtn element not found!');
    }

    // Xử lý form booking submit - tạo đơn thực sự
    const bookingForm = document.getElementById('nv-bookingForm');
    const bookingSubmitBtn = document.getElementById('nv-booking-submit-btn');
    console.log('bookingForm:', bookingForm);
    console.log('bookingSubmitBtn:', bookingSubmitBtn);
    if (bookingForm && bookingSubmitBtn) {
        // Xử lý click nút submit (nằm ngoài form)
        bookingSubmitBtn.addEventListener('click', function(e) {
            console.log('Booking submit button clicked!');
            e.preventDefault();
            handleCreateOrderWithBookingInfo();
        });
        
        // Xử lý submit form (backup)
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleCreateOrderWithBookingInfo();
        });
    }
    
});

// === HÀM HELPER CHUNG ===
function hideBillModal() {
    const billOverlay = document.getElementById('create-bill');
    if (billOverlay) {
        billOverlay.classList.remove('show');
        document.body.style.overflow = ''; // Cho phép scroll lại
    }
}

// === TEST FUNCTIONS ===
function testAddToCart() {
    console.log('Test button clicked');
    // Tìm function addToCart trong scope của DOMContentLoaded
    if (typeof window.testAddToCartGlobal === 'function') {
        window.testAddToCartGlobal();
    } else {
        console.error('testAddToCartGlobal not found');
    }
}

function testOpenBillModal() {
    console.log('Test open bill modal clicked');
    if (typeof window.testOpenBillModalGlobal === 'function') {
        window.testOpenBillModalGlobal();
    } else {
        console.error('testOpenBillModalGlobal not found');
    }
}

function testShowBookingForm() {
    console.log('Direct test showBookingForm');
    showBookingForm();
}

// === CÁC HÀM XỬ LÝ BOOKING FORM ===
function showBookingForm() {
    console.log('showBookingForm called');
    const bookingOverlay = document.getElementById('booking-info-nv');
    console.log('bookingOverlay found:', bookingOverlay);
    
    if (bookingOverlay) {
        // Đóng bill modal trước khi mở booking form
        hideBillModal();
        
        // Hiển thị booking form
        bookingOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        console.log('Booking form should be visible now');
        
        // Load danh sách bàn nếu có đủ thông tin
        setTimeout(() => {
            loadAvailableTables();
        }, 100);
        
        // Đặt trạng thái ban đầu cho nút đặt bàn (disabled vì chưa chọn bàn)
        const submitBtn = document.getElementById('nv-booking-submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');
            submitBtn.textContent = 'Đặt bàn';
        }
        
        // Focus vào input đầu tiên
        const firstInput = bookingOverlay.querySelector('input[type="text"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 300);
        }
    } else {
        console.error('booking-info-nv element not found!');
    }
}

function hideBookingForm() {
    const bookingOverlay = document.getElementById('booking-info-nv');
    if (bookingOverlay) {
        bookingOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Xử lý sự kiện đóng booking form
document.addEventListener('click', function(e) {
    if (e.target.closest('[data-action="close-booking-form"]')) {
        e.preventDefault();
        hideBookingForm();
    }
    
    // Đóng khi click vào overlay
    const bookingOverlay = document.getElementById('booking-info-nv');
    if (bookingOverlay && e.target === bookingOverlay) {
        hideBookingForm();
    }
});

// Xử lý số lượng khách đã được chuyển sang setupQuantityControls()

// Xử lý phím ESC để đóng booking form
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const bookingOverlay = document.getElementById('booking-info-nv');
        if (bookingOverlay && bookingOverlay.classList.contains('show')) {
            hideBookingForm();
        }
    }
});

// === XỬ LÝ DATE PICKER CHO BOOKING FORM ===
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('nv-date-display-input');
    if (dateInput) {
        // Set ngày mặc định là ngày mai
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = formatDateForDisplay(tomorrow);
        
        // Xử lý click vào input để mở date picker native
        dateInput.addEventListener('click', function() {
            // Tạo input date ẩn để mở native date picker
            const hiddenDateInput = document.createElement('input');
            hiddenDateInput.type = 'date';
            hiddenDateInput.style.position = 'absolute';
            hiddenDateInput.style.left = '-9999px';
            hiddenDateInput.min = formatDateForInput(new Date()); // Không cho chọn ngày trong quá khứ
            
            // Set ngày hiện tại của input
            const currentDate = parseDateFromDisplay(dateInput.value);
            if (currentDate) {
                hiddenDateInput.value = formatDateForInput(currentDate);
            }
            
            document.body.appendChild(hiddenDateInput);
            
            hiddenDateInput.addEventListener('change', function() {
                if (this.value) {
                    const selectedDate = new Date(this.value);
                    dateInput.value = formatDateForDisplay(selectedDate);
                    // Không cần load lại danh sách bàn vì hiển thị tất cả bàn
                }
                document.body.removeChild(this);
            });
            
            hiddenDateInput.click();
        });
    }

    // Không cần xử lý thay đổi giờ và số lượng khách vì hiển thị tất cả bàn
});

function formatDateForDisplay(date) {
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function parseDateFromDisplay(dateStr) {
    const parts = dateStr.split('/');
    if (parts.length === 3) {
        const day = parseInt(parts[0]);
        const month = parseInt(parts[1]) - 1; // Month is 0-indexed
        const year = parseInt(parts[2]);
        return new Date(year, month, day);
    }
    return null;
}

// === XỬ LÝ DANH SÁCH BÀN TRỐNG THEO LOGIC MỚI ===
async function loadAvailableTables() {
    const tablesContainer = document.getElementById('nv-available-tables-container');
    
    if (!tablesContainer) {
        return;
    }
    
    // Hiển thị loading
    tablesContainer.innerHTML = `
        <div class="nv-tables-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Đang tải danh sách bàn trống...</span>
        </div>
    `;
    
    try {
        const response = await fetch(`index.php?page=nhanvien&action=getAvailableTables`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            renderAvailableTables(data.data.tables);
        } else {
            showTablesError(data.error || 'Có lỗi xảy ra khi tải danh sách bàn');
        }
        
    } catch (error) {
        console.error('Error loading available tables:', error);
        showTablesError('Không thể kết nối đến server. Vui lòng thử lại!');
    }
}

function renderAvailableTables(tables) {
    const tablesContainer = document.getElementById('nv-available-tables-container');
    
    if (!tables || tables.length === 0) {
        tablesContainer.innerHTML = `
            <div class="nv-tables-empty">
                <i class="fas fa-chair"></i>
                <h4>Không có bàn trống</h4>
                <p>Tất cả các bàn hiện tại đã được đặt hoặc có lịch đặt trong vòng 2 giờ tới.</p>
            </div>
        `;
        return;
    }
    
    const tablesGrid = document.createElement('div');
    tablesGrid.className = 'nv-tables-grid';
    
    tables.forEach(table => {
        const tableItem = document.createElement('div');
        tableItem.className = 'nv-table-item';
        tableItem.dataset.tableId = table.MaBan;
        tableItem.dataset.tableName = table.TenBan;
        tableItem.dataset.selected = 'false';
        
        tableItem.innerHTML = `
            <div class="nv-table-info">
                <div class="nv-table-name">${table.TenBan}</div>
                <div class="nv-table-capacity">
                    <i class="fas fa-users"></i>
                    <span>Tối đa ${table.SucChua} người</span>
                </div>
            </div>
        `;
        
        // Xử lý click vào table item
        tableItem.addEventListener('click', function() {
            handleTableSelection(this);
        });
        
        tablesGrid.appendChild(tableItem);
    });
    
    tablesContainer.innerHTML = '';
    tablesContainer.appendChild(tablesGrid);
    
    // Khởi tạo trạng thái ban đầu cho counter và nút submit
    updateSelectedTablesCount();
}

function handleTableSelection(tableItem) {
    const isSelected = tableItem.dataset.selected === 'true';
    const tableId = tableItem.dataset.tableId;
    const tableName = tableItem.dataset.tableName;
    
    if (!isSelected) {
        // Chọn bàn
        tableItem.dataset.selected = 'true';
        tableItem.classList.add('selected');
        addSelectedTableDisplay(tableId, tableName);
    } else {
        // Bỏ chọn bàn
        tableItem.dataset.selected = 'false';
        tableItem.classList.remove('selected');
        removeSelectedTableDisplay(tableId);
    }
    
    // Cập nhật counter
    updateSelectedTablesCount();
}

function updateSelectedTablesCount() {
    const selectedTables = document.querySelectorAll('.nv-table-item[data-selected="true"]');
    const count = selectedTables.length;
    console.log(`Đã chọn ${count} bàn`);
    
    // Cập nhật trạng thái nút đặt bàn
    const submitBtn = document.getElementById('nv-booking-submit-btn');
    if (submitBtn) {
        if (count === 0) {
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');
            submitBtn.textContent = 'Đặt bàn';
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('disabled');
            submitBtn.textContent = 'Đặt bàn';
        }
    }
}

function addSelectedTableDisplay(tableId, tableName) {
    // Tạo display element cho bàn đã chọn (nếu cần hiển thị)
    console.log(`Đã chọn bàn: ${tableName} (ID: ${tableId})`);
}

function removeSelectedTableDisplay(tableId) {
    // Xóa display element của bàn (nếu cần hiển thị)
    console.log(`Bỏ chọn bàn ID: ${tableId}`);
}

function showTablesError(message) {
    const tablesContainer = document.getElementById('nv-available-tables-container');
    tablesContainer.innerHTML = `
        <div class="nv-tables-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
        </div>
    `;
}

function getSelectedTables() {
    const selectedTables = document.querySelectorAll('.nv-table-item[data-selected="true"]');
    const tablesList = [];
    
    selectedTables.forEach(tableItem => {
        tablesList.push({
            maBan: tableItem.dataset.tableId,
            tenBan: tableItem.dataset.tableName
        });
    });
    
    return tablesList;
}

// === CHỨC NĂNG TẠO ĐƠN VỚI THÔNG TIN BOOKING ===
async function handleCreateOrderWithBookingInfo() {
    
    // Kiểm tra giỏ hàng không trống
    if (totalCartQuantity === 0) {
        alert('Giỏ hàng trống! Vui lòng thêm món ăn trước khi tạo đơn.');
        return;
    }
    
    // Lấy thông tin từ form
    const form = document.getElementById('nv-bookingForm');
    const formData = new FormData(form);
    
    // Validate form data
    const customerName = form.querySelector('input[type="text"]').value.trim();
    const customerPhone = form.querySelector('input[type="tel"]').value.trim();
    const currentDateTime = document.getElementById('currentDateTime').value.trim();
    const guests = parseInt(document.getElementById('nv-booking-guests-input').value);
    const notes = form.querySelector('textarea').value.trim();
    
    // Tách ngày và giờ từ datetime-local input
    const selectedDate = currentDateTime ? currentDateTime.split('T')[0] : '';
    const selectedTime = currentDateTime ? currentDateTime.split('T')[1] : '';
    
    // Validate required fields
    const selectedTables = getSelectedTables();
    
    // Bắt buộc phải chọn ít nhất một bàn
    if (selectedTables.length === 0) {
        alert('Vui lòng chọn ít nhất một bàn trước khi đặt bàn!');
        return;
    }
    
    // Kiểm tra thời gian khi đã chọn bàn
    if (!currentDateTime) {
        alert('Vui lòng chọn ngày và giờ đặt bàn!');
        return;
    }
    
    // Chuẩn bị dữ liệu giỏ hàng
    const cartItems = [];
    for (const itemId in shoppingCart) {
        const item = shoppingCart[itemId];
        cartItems.push({
            id: itemId,
            name: item.name,
            price: item.price,
            quantity: item.quantity
        });
    }
    
    // Chuẩn bị dữ liệu gửi lên server
    const orderData = {
        customerInfo: {
            name: customerName ,
            phone: customerPhone ,
            email: '',
            notes: notes || 'Đặt bàn tại quán'
        },
        bookingInfo: {
            date: selectedDate,
            time: selectedTime,
            guests: guests,
            selectedTables: selectedTables
        },
        cartItems: cartItems
    };
    
    // Hiển thị loading trên nút submit
    const submitBtn = document.getElementById('nv-booking-submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo đơn...';
    submitBtn.disabled = true;
    
    try {
        // Gửi request tạo đơn
        const response = await fetch('index.php?page=nhanvien&action=createOrder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(orderData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Thành công
            alert(`Tạo đơn đặt bàn thành công! Mã đơn: ${result.data.maDon}`);
            
            // Reset giỏ hàng
            Object.keys(shoppingCart).forEach(key => delete shoppingCart[key]);
            totalCartQuantity = 0;
            totalCartPrice = 0;
            
            // Cập nhật UI
            const stickyCartWidget = document.getElementById('sticky-cart-widget');
            if (stickyCartWidget) {
                stickyCartWidget.classList.remove('show');
            }
            
            // Đóng các modal/form
            hideBookingForm();
            
            // Reset form
            form.reset();
            const guestsInput = document.getElementById('nv-booking-guests-input');
            if (guestsInput) {
                guestsInput.value = '1';
            }
            
            // Reset ngày giờ hiện tại
            setCurrentDateAndTime();
            
            // Có thể chuyển hướng đến trang chi tiết đơn
            if (confirm('Bạn có muốn xem chi tiết đơn đặt bàn vừa tạo không?')) {
                window.location.href = `index.php?page=nhanvien&action=viewBookingDetail&id=${result.data.maDon}`;
            }
            
        } else {
            // Lỗi từ server
            alert('Có lỗi xảy ra: ' + (result.error || 'Không thể tạo đơn đặt bàn'));
        }
        
    } catch (error) {
        console.error('Error creating order:', error);
        alert('Có lỗi xảy ra khi tạo đơn. Vui lòng thử lại!');
    } finally {
        // Khôi phục nút submit
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// === CHỨC NĂNG TÌM KIẾM MENU ===
let currentSearchQuery = '';
let isSearching = false;

function formatPrice(price) {
    price = Math.round(price);
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function renderMenuItems(items) {
    const menuGrid = document.getElementById('menu2-grid');
    if (!menuGrid) return;
    
    menuGrid.innerHTML = '';
    
    if (items.length === 0) {
        menuGrid.innerHTML = `
            <div class="empty-state" style="grid-column: 1 / -1;">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                <h3>Không tìm thấy món ăn nào</h3>
                <p>Thử tìm kiếm với từ khóa khác hoặc nhấn nút "Xóa" để reset</p>
            </div>
        `;
        return;
    }
    
    items.forEach(item => {
        const menuCard = document.createElement('div');
        menuCard.className = 'menu2-card';
        menuCard.setAttribute('data-action', 'open-modal');
        menuCard.setAttribute('data-id', item.MaMon);
        menuCard.setAttribute('data-name', item.TenMon);
        menuCard.setAttribute('data-price', item.Gia);
        menuCard.setAttribute('data-description', item.MoTa || '');
        menuCard.setAttribute('data-image-url', item.HinhAnhURL || 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp');
        
          menuCard.innerHTML = `
        <img src="${item.HinhAnhURL || 'URL_HINH_ANH_MAC_DINH'}" 
             alt="${item.TenMon}"
             onerror="this.src='URL_HINH_ANH_MAC_DINH'">
        
        <div class="menu2-card-content">
            <div class="menu2-card-info">
                <span class="menu2-card-name">${item.TenMon}</span>
                <span class="menu2-card-price">${formatPrice(item.Gia)}đ</span>
            </div>
            <div class="menu2-card-actions">
                <div class="menu2-btn-add-to-cart" 
                     data-action="add-to-cart"
                     data-id="${item.MaMon}"
                     data-name="${item.TenMon}"
                     data-price="${item.Gia}">+ Đặt</div>
            </div>
        </div>
    `;
        
        menuGrid.appendChild(menuCard);
    });
}

function updateSearchInfo(totalItems, query) {
    const searchInfo = document.getElementById('search-info');
    if (!searchInfo) return;
    
    if (query.trim() === '') {
        searchInfo.textContent = 'Hãy tìm kiếm món ăn';
    } else {
        searchInfo.textContent = `Tìm thấy ${totalItems} món ăn cho "${query}"`;
    }
}

function showLoading(show = true) {
    const loadingIndicator = document.getElementById('loading-indicator');
    const menuGrid = document.getElementById('menu2-grid');
    
    if (loadingIndicator) {
        loadingIndicator.style.display = show ? 'block' : 'none';
    }
    
    if (menuGrid && show) {
        menuGrid.style.opacity = '0.5';
    } else if (menuGrid) {
        menuGrid.style.opacity = '1';
    }
}

async function performSearch(query) {
    if (isSearching) return;
    
    try {
        isSearching = true;
        showLoading(true);
        
        const params = new URLSearchParams({
            tenMon: query.trim()
        });
        
        const url = `index.php?page=nhanvien&action=searchMenu&${params}`;
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            renderMenuItems(data.data.items);
            updateSearchInfo(data.data.items.length, query);
            currentSearchQuery = query;
        } else {
            throw new Error(data.error || 'Có lỗi xảy ra khi tìm kiếm');
        }
        
    } catch (error) {
        console.error('Search error:', error);
        
        // Hiển thị thông báo lỗi
        const menuGrid = document.getElementById('menu2-grid');
        if (menuGrid) {
            menuGrid.innerHTML = `
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: #ef4444;"></i>
                    <h3>Có lỗi xảy ra</h3>
                    <p>${error.message}</p>
                    <button onclick="performSearch('${query}')" 
                            style="margin-top: 1rem; padding: 0.5rem 1rem; background: #21A256; color: white; border: none; border-radius: 6px; cursor: pointer;">
                        Thử lại
                    </button>
                </div>
            `;
        }
        
        updateSearchInfo(0, query);
        
    } finally {
        isSearching = false;
        showLoading(false);
    }
}

// Xử lý sự kiện tìm kiếm
document.addEventListener('DOMContentLoaded', function() {
    const billOverlay = document.getElementById('create-bill');
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const query = searchInput ? searchInput.value.trim() : '';
            if (query === '') {
                alert('Vui lòng nhập tên món ăn để tìm kiếm');
                return;
            }
            performSearch(query);
        });
    }
    

    
    if (searchInput) {
        // Tìm kiếm khi nhấn Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                performSearch(query);
            }
        });
    }
    
    // Khởi tạo giao diện ban đầu
    const menuGrid = document.getElementById('menu2-grid');
    if (menuGrid) {
        menuGrid.innerHTML = `
            <div class="empty-state" style="grid-column: 1 / -1;">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                <h3>Hãy tìm kiếm món ăn</h3>
                <p>Nhập tên món ăn và nhấn nút "Tìm" hoặc phím Enter</p>
            </div>
        `;
    }
});
</script>