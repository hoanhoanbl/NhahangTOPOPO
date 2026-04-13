<!-- Modal thêm danh mục -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addCategoryModalLabel">
          <i class="fas fa-tag"></i> Thêm Danh Mục Mới
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addCategoryForm" action="?page=admin&section=categories&action=process-create" method="POST">
          <div class="form-group mb-3">
            <label for="TenDM">Tên Danh Mục</label>
            <input type="text" class="form-control" id="TenDM" name="TenDM" required 
                   placeholder="Ví dụ: Hải sản, Đồ nướng, Lẩu..." maxlength="100">
            <small class="text-muted">Tên danh mục không được trùng lặp. Tối đa 100 ký tự.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Hủy
        </button>
        <button type="submit" form="addCategoryForm" class="btn" style="background-color: #21A256; border-color: #21A256; color: white;">
          <i class="fas fa-save"></i> Thêm Danh Mục
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Validation form thêm danh mục
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    const tenDM = document.getElementById('TenDM').value.trim();
    
    if (tenDM === '') {
        e.preventDefault();
        alert('Vui lòng nhập tên danh mục!');
    } else if (tenDM.length < 2) {
        e.preventDefault();
        alert('Tên danh mục phải có ít nhất 2 ký tự!');
    }
});
</script>
