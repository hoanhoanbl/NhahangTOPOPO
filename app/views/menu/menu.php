<?php
$title = "Quán Nhậu Tự Do - Thực Đơn";
$page_title = "Quán Nhậu Tự Do - Thực Đơn";
?>

<link rel="stylesheet" href="public/css/pages/menu.css">

<!-- Modal chọn cơ sở -->
<div id="branchModal" class="branch-modal">
    <div class="branch-modal-content">
        <div class="branch-modal-header">
            <h2>Chọn cơ sở để xem thực đơn</h2>
            <span class="branch-modal-close">&times;</span>
        </div>
        <div class="branch-modal-body">
            <div class="branch-grid">
                <?php if (isset($branches) && is_array($branches)): ?>
                    <?php foreach ($branches as $branch): ?>
                        <div class="branch-card" data-branch-id="<?= $branch['MaCoSo'] ?>">
                            <div class="branch-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="branch-info">
                                <h3 class="branch-name"><?= htmlspecialchars($branch['TenCoSo']) ?></h3>
                                <p class="branch-address"><?= htmlspecialchars($branch['DiaChi']) ?></p>
                            </div>
                                <i class="fas fa-chevron-right"></i>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<main class="menu-page">
    <div class="menu-container">
        <div class="menu-welcome">
            <h1>Chào mừng đến với Nhà hàng</h1>
            <p>Vui lòng chọn cơ sở gần bạn để xem thực đơn và đặt bàn</p>
            <button id="chooseBranchBtn" class="choose-branch-btn">
                <i class="fas fa-utensils"></i>
                Xem Thực Đơn
            </button>
        </div>
    </div>
</main>

<script>
// Xử lý lựa chọn và lưu cơ sở
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('branchModal');
    const chooseBranchBtn = document.getElementById('chooseBranchBtn');
    const closeBtn = document.querySelector('.branch-modal-close');
    const branchCards = document.querySelectorAll('.branch-card');

    // Kiểm tra cơ sở đã được chọn từ localStorage
    const savedBranch = localStorage.getItem('selectedBranch');
    
    if (savedBranch) {
        // Nếu đã có cơ sở được lưu, chuyển hướng ngay lập tức
        const branchData = JSON.parse(savedBranch);
        window.location.href = '?page=menu2&coso=' + branchData.id;
        return; // Dừng việc thực thi code dưới
    }

    // Chỉ hiển thị modal nếu chưa có cơ sở được chọn
    if (modal) {
        modal.style.display = 'block';
    }

    // Hiển thị modal khi bấm nút "Xem Thực Đơn"
    if (chooseBranchBtn) {
        chooseBranchBtn.addEventListener('click', function() {
            modal.style.display = 'block';
        });
    }

    // Đóng modal
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    // Đóng modal khi click bên ngoài
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Xử lý khi chọn cơ sở
    branchCards.forEach(function(card) {
        card.addEventListener('click', function() {
            const branchId = this.getAttribute('data-branch-id');
            const branchName = this.querySelector('.branch-name').textContent;
            const branchAddress = this.querySelector('.branch-address').textContent;
            
            // Lưu thông tin cơ sở đã chọn vào localStorage
            const branchData = {
                id: branchId,
                name: branchName,
                address: branchAddress,
                timestamp: Date.now()
            };
            localStorage.setItem('selectedBranch', JSON.stringify(branchData));
            
            // Chuyển hướng đến menu2 với tham số cơ sở đã chọn
            window.location.href = '?page=menu2&coso=' + branchId;
        });
    });
});
</script>
