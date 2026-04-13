<?php
// File này thường chỉ cần kết nối để truy vấn nếu cần (nhưng ở đây không cần truy vấn Danh mục)
// include __DIR__ . "/connect.php"; 
?>

<!-- Modal sửa ưu đãi -->
<div class="modal fade" id="updateUuDaiModal<?=$row['MaUD']?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sửa ưu đãi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="?page=admin&section=uudai&action=process-update&MaUD=<?=$row['MaUD']?>" method="POST">
      <div class="modal-body">
      <div class="row g-3">

        <div class="col-md-12">
            <label class="form-label">Tiêu đề ưu đãi <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="TenMaUD" name="TenMaUD" value="<?=$row['TenMaUD']?>" maxlength="50" required>
            <small class="text-muted">Tên hiển thị của chương trình ưu đãi (tối đa 50 ký tự)</small>
        </div>
        <div class="col-md-12">
          <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
          <textarea class="form-control" rows="3" id="MoTa" name="MoTa" required><?=$row['MoTa']?></textarea>
          <small class="text-muted">Thông tin đầy đủ về ưu đãi</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="GiaTriGiam" name="GiaTriGiam" value="<?=$row['GiaTriGiam']?>" min="0" step="0.01" required>
          <small class="text-muted">Nhập số tiền hoặc phần trăm giảm</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
          <select class="form-control" name="LoaiGiamGia" required>
            <option value="phantram" <?=$row['LoaiGiamGia'] == 'phantram' ? 'selected' : ''?>>Phần trăm (%)</option>
            <option value="sotien" <?=$row['LoaiGiamGia'] == 'sotien' ? 'selected' : ''?>>Số tiền (VNĐ)</option>
          </select>
          <small class="text-muted">Chọn đơn vị tính giảm giá</small>
        </div>
        <div class="col-md-12">
          <label class="form-label">Điều kiện áp dụng</label>
          <input type="text" class="form-control" id="DieuKien" name="DieuKien" value="<?=$row['DieuKien']?>">
          <small class="text-muted">Điều kiện để khách hàng được áp dụng ưu đãi (không bắt buộc)</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
          <input type="date" class="form-control" id="NgayBD" name="NgayBD" value="<?=$row['NgayBD']?>" required>
          <small class="text-muted">Ngày bắt đầu có hiệu lực</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
          <input type="date" class="form-control" id="NgayKT" name="NgayKT" value="<?=$row['NgayKT']?>" required>
          <small class="text-muted">Ngày hết hiệu lực</small>
        </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-warning">Cập nhật ưu đãi</button>
        </div>
      </form>
    </div>
  </div>
</div>