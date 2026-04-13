<?php
// Lấy danh sách danh mục để hiển thị trong dropdown
$query = "SELECT * FROM danhmuc ORDER BY TenDM";
$result = mysqli_query($conn, $query);
$list_danh_muc = [];
while($row = mysqli_fetch_array($result)){
    $list_danh_muc[] = $row;
}
?>

 <!-- Modal thêm cơ sở -->
 <div class="modal fade" id="addBranchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"> <i class="fas fa-utensils me-2"></i> Thêm món ăn mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="?page=admin&section=menu&action=process-create" method="POST">
      <div class="modal-body">
      <div class="row g-3">

      <div class="col-md-12">
            <label for="MaCoSo">Danh mục</label>
            <select class="form-control" id="MaDanhMuc" name="MaDanhMuc" required>
                <option value="">-- Chọn Danh Mục Món --</option>
                <?php foreach ($list_danh_muc as $danhmuc): ?>
                    <option value="<?php echo $danhmuc['MaDM']; ?>"><?php echo $danhmuc['TenDM']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tên món ăn</label>
          <input type="text" class="form-control" placeholder="Ví dụ: Hải sản, Nướng, Lẩu" id="TenMonAn" name="TenMonAn" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">URL hình ảnh</label>
          <input type="text" class="form-control" placeholder="Ví dụ: https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271649402242.webp" id="AnhMonAn" name="AnhMonAn" required>
        </div>
        <!-- <div class="col-md-6">
          <label class="form-label">Ảnh món ăn</label>
          <input type="file" class="form-control" id="AnhMonAn" name="AnhMonAn">
        </div> -->
        <div class="col-12">
          <label class="form-label">Mô tả</label>
          <textarea class="form-control" rows="2" placeholder="Mô tả ngắn gọn" id="MoTaMonAn" name="MoTaMonAn" required></textarea>
        </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Hủy</button>
        <button type="submit" class="btn" style="background-color: #21A256; border-color: #21A256; color: white;"><i class="fas fa-save"></i> Thêm món ăn</button>
        </div>
      </form>
    </div>
  </div>
</div>




