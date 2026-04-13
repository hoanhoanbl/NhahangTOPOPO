<?php
$sql_coso = "SELECT * FROM coso ORDER BY TenCoSo";
$result_coso = mysqli_query($conn, $sql_coso);
$currentBranchId = isset($_SESSION['user']['MaCoSo']) ? (int)$_SESSION['user']['MaCoSo'] : 0;
$isAdminRole = isset($auth) ? $auth->isAdmin() : false;
?>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addUserModalLabel">
          <i class="fas fa-user-plus"></i> Thêm Nhân Viên Mới
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addUserForm" action="?page=admin&section=users&action=process-create" method="POST">
          <div class="form-group mb-3">
            <label for="MaCoSo">Cơ Sở</label>
            <select class="form-control" id="MaCoSo" name="MaCoSo" <?php echo $isAdminRole ? '' : 'required disabled'; ?>>
              <option value="">-- Chọn Cơ Sở --</option>
              <?php while ($coso = mysqli_fetch_assoc($result_coso)): ?>
                <option value="<?php echo (int)$coso['MaCoSo']; ?>" <?php echo ($currentBranchId === (int)$coso['MaCoSo']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($coso['TenCoSo']); ?>
                </option>
              <?php endwhile; ?>
            </select>
            <?php if (!$isAdminRole): ?>
              <input type="hidden" name="MaCoSo" value="<?php echo $currentBranchId; ?>">
            <?php endif; ?>
            <small class="text-muted">Admin có thể để trống cơ sở khi tạo tài khoản admin; quản lý/lễ tân bắt buộc 1 cơ sở.</small>
          </div>

          <div class="form-group mb-3">
            <label for="TenNhanVien">Tên Nhân Viên</label>
            <input type="text" class="form-control" id="TenNhanVien" name="TenNhanVien" required>
          </div>

          <div class="form-group mb-3">
            <label for="TenDN">Tên Đăng Nhập</label>
            <input type="text" class="form-control" id="TenDN" name="TenDN" required>
            <small class="text-muted">Tên đăng nhập phải là duy nhất trong hệ thống.</small>
          </div>

          <div class="form-group mb-3">
            <label for="MatKhau">Mật Khẩu</label>
            <input type="password" class="form-control" id="MatKhau" name="MatKhau" required>
          </div>

          <div class="form-group mb-3">
            <label for="XacNhanMatKhau">Xác Nhận Mật Khẩu</label>
            <input type="password" class="form-control" id="XacNhanMatKhau" name="XacNhanMatKhau" required>
          </div>

          <div class="form-group mb-3">
            <label>Chức Vụ</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="ChucVu" id="ChucVuReceptionist" value="receptionist" checked>
              <label class="form-check-label" for="ChucVuReceptionist">Lễ Tân</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="ChucVu" id="ChucVuManager" value="manager">
              <label class="form-check-label" for="ChucVuManager">Quản Lý</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="ChucVu" id="ChucVuAdmin" value="admin">
              <label class="form-check-label" for="ChucVuAdmin">Quản Trị Viên</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Hủy</button>
        <button type="submit" form="addUserForm" class="btn" style="background-color: #21A256; border-color: #21A256; color: white;">
          <i class="fas fa-save"></i> Thêm Nhân Viên
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('MatKhau').value;
    const confirmPassword = document.getElementById('XacNhanMatKhau').value;
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu và xác nhận mật khẩu không khớp!');
    }
});

const roleInputs = document.querySelectorAll('input[name="ChucVu"]');
const branchSelect = document.getElementById('MaCoSo');
const isAdminUser = <?php echo $isAdminRole ? 'true' : 'false'; ?>;

function syncBranchRequirementForRole() {
    if (!isAdminUser || !branchSelect) return;
    const selectedRole = document.querySelector('input[name="ChucVu"]:checked')?.value || '';
    branchSelect.required = selectedRole !== 'admin';
}

roleInputs.forEach((input) => input.addEventListener('change', syncBranchRequirementForRole));
syncBranchRequirementForRole();
</script>
