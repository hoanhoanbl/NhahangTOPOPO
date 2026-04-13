<?php
$currentBranchId = isset($_SESSION['user']['MaCoSo']) ? (int)$_SESSION['user']['MaCoSo'] : 0;
$isAdminRole = isset($auth) ? $auth->isAdmin() : false;
?>

<div class="modal fade" id="updateUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="fas fa-user-edit"></i> Sửa Thông Tin Nhân Viên</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="updateUserForm" method="POST">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-12">
            <label for="updateMaCoSo">Cơ Sở</label>
            <select class="form-control" id="updateMaCoSo" name="MaCoSo" <?php echo $isAdminRole ? '' : 'required disabled'; ?>>
              <option value="">-- Chọn Cơ Sở --</option>
              <?php
              $sql_coso = "SELECT * FROM coso ORDER BY TenCoSo";
              $result_coso = mysqli_query($conn, $sql_coso);
              while($coso = mysqli_fetch_assoc($result_coso)):
              ?>
                <option value="<?php echo (int)$coso['MaCoSo']; ?>" <?php echo ($currentBranchId === (int)$coso['MaCoSo']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($coso['TenCoSo']); ?>
                </option>
              <?php endwhile; ?>
            </select>
            <?php if (!$isAdminRole): ?>
              <input type="hidden" name="MaCoSo" value="<?php echo $currentBranchId; ?>">
            <?php endif; ?>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tên Nhân Viên</label>
            <input type="text" class="form-control" id="updateTenNhanVien" name="TenNhanVien" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tên Đăng Nhập</label>
            <input type="text" class="form-control" id="updateTenDN" name="TenDN" required>
            <small class="text-muted">Tên đăng nhập phải là duy nhất trong hệ thống.</small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Mật Khẩu Mới</label>
            <input type="password" class="form-control" id="updateMatKhau" name="MatKhau">
            <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu.</small>
          </div>

          <div class="col-md-6">
            <label class="form-label">Xác Nhận Mật Khẩu</label>
            <input type="password" class="form-control" id="updateXacNhanMatKhau" name="XacNhanMatKhau">
          </div>

          <div class="col-12">
            <label>Chức Vụ</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="ChucVu" id="updateChucVuReceptionist" value="receptionist">
              <label class="form-check-label" for="updateChucVuReceptionist">Lễ Tân</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="ChucVu" id="updateChucVuManager" value="manager">
              <label class="form-check-label" for="updateChucVuManager">Quản Lý</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="ChucVu" id="updateChucVuAdmin" value="admin">
              <label class="form-check-label" for="updateChucVuAdmin">Quản Trị Viên</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Hủy</button>
        <button type="submit" class="btn" style="background-color: #FFA827; border-color: #FFA827; color: #333;">
          <i class="fas fa-save"></i> Cập nhật nhân viên
        </button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
function loadUserData(maNV, tenNhanVien, tenDN, maCoSo, chucVu) {
    document.getElementById('updateUserForm').action = '?page=admin&section=users&action=process-update&MaNV=' + maNV;
    document.getElementById('updateTenNhanVien').value = tenNhanVien;
    document.getElementById('updateTenDN').value = tenDN;

    const branchSelect = document.getElementById('updateMaCoSo');
    if (branchSelect && !branchSelect.disabled) {
        branchSelect.value = maCoSo;
    }

    if (chucVu === 'admin') {
        document.getElementById('updateChucVuAdmin').checked = true;
    } else if (chucVu === 'manager') {
        document.getElementById('updateChucVuManager').checked = true;
    } else {
        document.getElementById('updateChucVuReceptionist').checked = true;
    }

    document.getElementById('updateMatKhau').value = '';
    document.getElementById('updateXacNhanMatKhau').value = '';
    syncUpdateBranchRequirementForRole();
}

document.getElementById('updateUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('updateMatKhau').value;
    const confirmPassword = document.getElementById('updateXacNhanMatKhau').value;
    if (password !== '' && password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu và xác nhận mật khẩu không khớp!');
    }
});

const updateRoleInputs = document.querySelectorAll('input[name="ChucVu"]');
const updateBranchSelect = document.getElementById('updateMaCoSo');
const isUpdateAdminUser = <?php echo $isAdminRole ? 'true' : 'false'; ?>;

function syncUpdateBranchRequirementForRole() {
    if (!isUpdateAdminUser || !updateBranchSelect) return;
    const selectedRole = document.querySelector('input[name="ChucVu"]:checked')?.value || '';
    updateBranchSelect.required = selectedRole !== 'admin';
}

updateRoleInputs.forEach((input) => input.addEventListener('change', syncUpdateBranchRequirementForRole));
syncUpdateBranchRequirementForRole();
</script>
