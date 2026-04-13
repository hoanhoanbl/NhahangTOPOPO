<?php
$query = "SELECT * FROM coso ORDER BY TenCoSo";
$result = mysqli_query($conn, $query);
$list_coso = [];
while ($row = mysqli_fetch_array($result)) {
    $list_coso[] = $row;
}
$isGlobalAdmin = isset($auth) ? $auth->isAdmin() : false;
$currentBranchId = isset($_SESSION['user']['MaCoSo']) ? (int)$_SESSION['user']['MaCoSo'] : 0;
?>

<div class="modal fade" id="addTableModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-utensils me-2"></i> Thêm bàn mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="?page=admin&section=table&action=process-create" method="POST">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="MaCoSo">Cơ sở</label>
              <select class="form-select" id="MaCoSo" name="MaCoSo" required <?php echo $isGlobalAdmin ? '' : 'disabled'; ?>>
                <option value="">-- Chọn cơ sở --</option>
                <?php foreach ($list_coso as $coso): ?>
                  <option value="<?php echo (int)$coso['MaCoSo']; ?>" <?php echo ($currentBranchId === (int)$coso['MaCoSo']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($coso['TenCoSo']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (!$isGlobalAdmin): ?>
                <input type="hidden" name="MaCoSo" value="<?php echo $currentBranchId; ?>">
              <?php endif; ?>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="MaBanCode">Mã bàn</label>
              <input type="text" class="form-control" id="MaBanCode" name="MaBanCode" placeholder="VD: TBL-0201" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="TenBan">Tên bàn</label>
              <input type="text" class="form-control" id="TenBan" name="TenBan" placeholder="VD: Bàn A01" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="ZoneBan">Khu vực bàn</label>
              <input type="text" class="form-control" id="ZoneBan" name="ZoneBan" placeholder="VD: Tầng 1 - Trong nhà" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="SucChua">Sức chứa</label>
              <input type="number" class="form-control" id="SucChua" name="SucChua" min="1" max="100" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="SucChuaToiDa">Sức chứa tối đa</label>
              <input type="number" class="form-control" id="SucChuaToiDa" name="SucChuaToiDa" min="1" max="100" required>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="OnlineBookable">Đặt online</label>
              <select class="form-select" id="OnlineBookable" name="OnlineBookable" required>
                <option value="1" selected>Có</option>
                <option value="0">Không</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="GhepBanDuoc">Cho phép ghép</label>
              <select class="form-select" id="GhepBanDuoc" name="GhepBanDuoc" required>
                <option value="1" selected>Có</option>
                <option value="0">Không</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="TrangThai">Trạng thái</label>
              <select class="form-select" id="TrangThai" name="TrangThai" required>
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label" for="GhiChu">Ghi chú</label>
              <textarea class="form-control" id="GhiChu" name="GhiChu" rows="2" placeholder="Ghi chú thêm (nếu có)"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Hủy</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Thêm bàn</button>
        </div>
      </form>
    </div>
  </div>
</div>
