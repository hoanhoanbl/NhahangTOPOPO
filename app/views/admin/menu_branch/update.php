<!-- Modal Sửa món ăn trong Menu cơ sở -->
<div class="modal fade" id="updateDishInBranchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Cập nhật món ăn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateDishForm" action="?page=admin&section=menu_branch&action=process-update&branch_id=<?php echo $selected_branch_id; ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="MaCoSo" value="<?php echo $selected_branch_id; ?>">
                    <input type="hidden" id="update_MaMon" name="MaMon">

                    <div class="mb-3">
                        <label class="form-label">Tên món ăn:</label>
                        <p class="form-control-plaintext" id="update_TenMon"><strong></strong></p>
                    </div>

                    <div class="mb-3">
                        <label for="update_Gia" class="form-label">Giá bán (VNĐ):</label>
                        <input type="number" class="form-control" id="update_Gia" name="Gia" placeholder="Ví dụ: 50000" min="0" step="1" required>
                        <small class="text-muted">Nhập giá từ 0 trở lên (VND chỉ số nguyên)</small>
                    </div>

                    <div class="mb-3">
                        <label for="update_TinhTrang" class="form-label">Tình trạng:</label>
                        <select class="form-select" id="update_TinhTrang" name="TinhTrang" required>
                            <option value="con_hang">Còn hàng</option>
                            <option value="het_hang">Hết hàng</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Hủy</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>