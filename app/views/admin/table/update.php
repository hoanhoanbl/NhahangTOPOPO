<?php
$isGlobalAdmin = isset($auth) ? $auth->isAdmin() : false;
$currentBranchId = isset($_SESSION['user']['MaCoSo']) ? (int)$_SESSION['user']['MaCoSo'] : 0;
?>
<div class="modal fade" id="updateTableModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sửa bàn</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="updateTableForm" method="POST">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="updateMaCoSo">Cơ sở</label>
              <select class="form-select" id="updateMaCoSo" name="MaCoSo" required <?php echo $isGlobalAdmin ? '' : 'disabled'; ?>>
                <option value="">-- Chọn cơ sở --</option>
                <?php
                $sql_coso = "SELECT * FROM coso ORDER BY TenCoSo";
                $result_coso = mysqli_query($conn, $sql_coso);
                while ($coso = mysqli_fetch_array($result_coso)):
                ?>
                  <option value="<?php echo (int)$coso['MaCoSo']; ?>" <?php echo ($currentBranchId === (int)$coso['MaCoSo']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($coso['TenCoSo']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <?php if (!$isGlobalAdmin): ?>
                <input type="hidden" name="MaCoSo" value="<?php echo $currentBranchId; ?>">
              <?php endif; ?>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="updateMaBanCode">Mã bàn</label>
              <input type="text" class="form-control" id="updateMaBanCode" name="MaBanCode" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="updateTenBan">Tên bàn</label>
              <input type="text" class="form-control" id="updateTenBan" name="TenBan" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="updateZoneBan">Khu vực bàn</label>
              <input type="text" class="form-control" id="updateZoneBan" name="ZoneBan" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="updateSucChua">Sức chứa</label>
              <input type="number" class="form-control" id="updateSucChua" name="SucChua" min="1" max="100" required>
            </div>

            <div class="col-md-6">
              <label class="form-label" for="updateSucChuaToiDa">Sức chứa tối đa</label>
              <input type="number" class="form-control" id="updateSucChuaToiDa" name="SucChuaToiDa" min="1" max="100" required>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="updateOnlineBookable">Đặt online</label>
              <select class="form-select" id="updateOnlineBookable" name="OnlineBookable" required>
                <option value="1">Có</option>
                <option value="0">Không</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="updateGhepBanDuoc">Cho phép ghép</label>
              <select class="form-select" id="updateGhepBanDuoc" name="GhepBanDuoc" required>
                <option value="1">Có</option>
                <option value="0">Không</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label" for="updateTrangThai">Trạng thái</label>
              <select class="form-select" id="updateTrangThai" name="TrangThai" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label" for="updateGhiChu">Ghi chú</label>
              <textarea class="form-control" id="updateGhiChu" name="GhiChu" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn" style="background-color: #FFA827; border-color: #FFA827; color: #333;">Cập nhật bàn</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function loadTableData(button) {
  const raw = button.getAttribute('data-table') || '{}';
  const table = JSON.parse(raw);

  document.getElementById('updateTableForm').action = '?page=admin&section=table&action=process-update&MaBan=' + table.MaBan;
  const branchSelect = document.getElementById('updateMaCoSo');
  if (branchSelect && !branchSelect.disabled) {
      branchSelect.value = table.MaCoSo || '';
  }
  document.getElementById('updateMaBanCode').value = table.MaBanCode || '';
  document.getElementById('updateTenBan').value = table.TenBan || '';
  document.getElementById('updateZoneBan').value = table.ZoneBan || '';
  document.getElementById('updateSucChua').value = table.SucChua || '';
  document.getElementById('updateSucChuaToiDa').value = table.SucChuaToiDa || '';
  document.getElementById('updateOnlineBookable').value = String(table.OnlineBookable ?? 0);
  document.getElementById('updateGhepBanDuoc').value = String(table.GhepBanDuoc ?? 0);
  document.getElementById('updateTrangThai').value = table.TrangThai || 'Active';
  document.getElementById('updateGhiChu').value = table.GhiChu || '';
}
</script>
