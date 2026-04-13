<!-- Modal sửa danh mục -->
<div class="modal fade" id="updateCategoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
          <i class="fas fa-edit"></i> Sửa Danh Mục
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="updateCategoryForm" method="POST">
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="form-label">Tên Danh Mục</label>
          <input type="text" class="form-control" id="updateTenDM" name="TenDM" required maxlength="100">
          <small class="text-muted">Tên danh mục không được trùng lặp. Tối đa 100 ký tự.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Hủy
        </button>
        <button type="submit" class="btn" style="background-color: #FFA827; border-color: #FFA827; color: #333;">
          <i class="fas fa-save"></i> Cập nhật danh mục
        </button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
function loadCategoryData(maDM, tenDM) {
    // Cập nhật action của form
    document.getElementById('updateCategoryForm').action = '?page=admin&section=categories&action=process-update&MaDM=' + maDM;
    
    // Điền dữ liệu vào form
    document.getElementById('updateTenDM').value = tenDM;
}

// Kiểm tra validation khi submit form update
document.getElementById('updateCategoryForm').addEventListener('submit', function(e) {
    const tenDM = document.getElementById('updateTenDM').value.trim();
    
    if (tenDM === '') {
        e.preventDefault();
        alert('Vui lòng nhập tên danh mục!');
    } else if (tenDM.length < 2) {
        e.preventDefault();
        alert('Tên danh mục phải có ít nhất 2 ký tự!');
    }
});
</script>
