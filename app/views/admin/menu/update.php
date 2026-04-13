<!-- Modal sửa món ăn -->
<div class="modal fade" id="updateMenuModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sửa món ăn</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="updateMenuForm" method="POST">
      <div class="modal-body">
      <div class="row g-3">
        <div class="col-md-12">
            <label for="MaDanhMuc">Danh mục</label>
            <select class="form-control" id="updateMaDanhMuc" name="MaDanhMuc" required>
                <option value="">-- Chọn Danh Mục Món --</option>
                <?php 
                // Lấy danh sách danh mục
                $sql_dm = "SELECT * FROM `danhmuc`";
                $result_dm = mysqli_query($conn, $sql_dm);
                while($dm = mysqli_fetch_array($result_dm)): 
                ?>
                    <option value="<?php echo $dm['MaDM']; ?>"><?php echo $dm['TenDM']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tên món ăn</label>
          <input type="text" class="form-control" placeholder="Ví dụ: Hải sản, Nướng, Lẩu" id="updateTenMonAn" name="TenMonAn" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">URL hình ảnh</label>
          <input type="text" class="form-control" placeholder="Ví dụ: https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271649402242.webp" id="updateAnhMonAn" name="AnhMonAn" required>
        </div>
        <div class="col-12">
          <label class="form-label">Mô tả</label>
          <textarea class="form-control" rows="2" placeholder="Mô tả ngắn gọn" id="updateMoTaMonAn" name="MoTaMonAn" required></textarea>
        </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn" style="background-color: #FFA827; border-color: #FFA827; color: #333;">Cập nhật món ăn</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function loadMenuData(mamon, tenmon, anhmon, mota, madm) {
    // Cập nhật action của form
    document.getElementById('updateMenuForm').action = '?page=admin&section=menu&action=process-update&MaMon=' + mamon;
    
    // Điền dữ liệu vào form
    document.getElementById('updateTenMonAn').value = tenmon;
    document.getElementById('updateAnhMonAn').value = anhmon;
    document.getElementById('updateMoTaMonAn').value = mota;
    document.getElementById('updateMaDanhMuc').value = madm;
}
</script>