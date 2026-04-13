<?php
// Lấy danh sách món ăn chưa có ở cơ sở này
$sql = "SELECT m.MaMon, m.TenMon, m.HinhAnhURL, dm.TenDM 
        FROM monan m 
        LEFT JOIN danhmuc dm ON m.MaDM = dm.MaDM 
        WHERE m.MaMon NOT IN (
            SELECT MaMon FROM menu_coso WHERE MaCoSo = $selected_branch_id
        )
        ORDER BY m.TenMon";
$result = mysqli_query($conn, $sql);
$available_dishes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $available_dishes[] = $row;
}
?>

<!-- Modal thêm món vào cơ sở -->
<div class="modal fade" id="addDishToBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Thêm món vào thực đơn cơ sở</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=admin&section=menu_branch&action=process-create&branch_id=<?php echo $selected_branch_id; ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="MaCoSo" value="<?php echo $selected_branch_id; ?>">

                    <?php if (empty($available_dishes)): ?>
                        <div class="alert alert-warning">
                            Tất cả các món ăn đã được thêm vào cơ sở này.
                        </div>
                    <?php else: ?>
                        
                        <!-- Ô tìm kiếm -->
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchDish" placeholder="Tìm kiếm món ăn...">
                        </div>

                        <!-- Danh sách món ăn -->
                        <input type="hidden" name="MaMon" id="selectedDishId" required>
                        <div style="max-height: 400px; overflow-y: auto;" class="border rounded p-2">
                            <div class="row g-2">
                            <?php foreach ($available_dishes as $dish): ?>
                                <div class="col-md-6 dish-item">
                                    <div class="card dish-card h-100" onclick="selectDish(this)" style="cursor: pointer;">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <!-- 1. Bọc radio button trong một div để kiểm soát vị trí -->
                                                <div class="me-2">
                                                    <input class="form-check-input" type="radio" name="MaMon"
                                                        id="dish<?php echo $dish['MaMon']; ?>"
                                                        value="<?php echo $dish['MaMon']; ?>" required>
                                                </div>

                                                <!-- 2. Bọc hình ảnh và text trong một div chiếm hết phần còn lại -->
                                                <div class="flex-grow-1">
                                                    <div class="row g-0">
                                                        <div class="col-4">
                                                            <img src="<?php echo htmlspecialchars($dish['HinhAnhURL']); ?>"
                                                                class="img-fluid rounded"
                                                                style="height: 80px; width: 100%; object-fit: cover;">
                                                        </div>
                                                        <div class="col-8">
                                                            <div class="ps-2">
                                                                <h6 class="card-title mb-1" style="font-size: 0.9rem;">
                                                                    <?php echo htmlspecialchars($dish['TenMon']); ?>
                                                                </h6>
                                                                <p class="card-text mb-0">
                                                                    <small class="text-muted">
                                                                        <?php echo htmlspecialchars($dish['TenDM'] ?? 'Chưa phân loại'); ?>
                                                                    </small>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Thông tin giá và tình trạng -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Giá bán (VNĐ):</label>
                                <input type="number" class="form-control" name="Gia" min="0" step="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tình trạng:</label>
                                <select class="form-select" name="TinhTrang" required>
                                    <option value="con_hang">Còn hàng</option>
                                    <option value="het_hang">Hết hàng</option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" <?php echo empty($available_dishes) ? 'disabled' : ''; ?>>
                        Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tìm kiếm món ăn đơn giản
document.getElementById('searchDish').addEventListener('keyup', function() {
    let keyword = this.value.toLowerCase();
    let items = document.querySelectorAll('.dish-item');
    
    items.forEach(function(item) {
        let text = item.textContent.toLowerCase();
        if (text.includes(keyword)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
