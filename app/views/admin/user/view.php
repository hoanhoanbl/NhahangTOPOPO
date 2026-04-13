<?php
include dirname(__DIR__,4) . "/config/connect.php";
$canStaffUpdate = isset($auth) && $auth->can('staff', 'update');
$canStaffDelete = isset($auth) && $auth->can('staff', 'delete');

// Include modal sửa nhân viên
if ($canStaffUpdate) {
    include __DIR__ . "/update.php";
}

// Cấu hình phân trang
$recordsPerPage = 10; // Số bản ghi trên mỗi trang
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = max(0, ($page - 1) * $recordsPerPage);

// Đếm tổng số bản ghi
$countSql = "SELECT COUNT(*) as total FROM `nhanvien` WHERE 1";
$countResult = mysqli_query($conn, $countSql);
$totalRecords = mysqli_fetch_array($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Lấy dữ liệu nhân viên với thông tin cơ sở
$sql = "SELECT nv.*, cs.TenCoSo 
        FROM `nhanvien` nv 
        LEFT JOIN `coso` cs ON nv.MaCoSo = cs.MaCoSo 
        ORDER BY nv.TenNhanVien 
        LIMIT $offset, $recordsPerPage";
$result = mysqli_query($conn, $sql);
$users = [];
while($row = mysqli_fetch_array($result)){
    $users[] = $row;
}
?>

<!-- Hiển thị danh sách nhân viên -->
<div class="card shadow p-4">
    <h4 class="mb-3">Danh sách nhân viên (<?php echo $totalRecords; ?> người)</h4>
    <table class="table table-bordered align-middle text-center" id="userTable">
      <thead class="table-dark">
        <tr>
          <th width="5%">STT</th>
          <th width="20%">Tên Nhân Viên</th>
          <th width="15%">Tên Đăng Nhập</th>
          <th width="20%">Cơ Sở</th>
          <th width="15%">Chức Vụ</th>
          <th width="25%">Hành Động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($users) > 0): ?>
          <?php foreach ($users as $index => $user): ?>
            <tr>
              <td><?php echo $offset + $index + 1; ?></td>
              <td class="text-start fw-bold"><?php echo htmlspecialchars($user['TenNhanVien']); ?></td>
              <td class="text-start"><?php echo htmlspecialchars($user['TenDN']); ?></td>
              <td class="text-start"><?php echo htmlspecialchars($user['TenCoSo'] ?? 'Không có'); ?></td>
              <td>
                <?php if($user['ChucVu'] == 'admin'): ?>
                  <span class="badge bg-danger">Quản trị viên</span>
                <?php elseif ($user['ChucVu'] == 'manager' || $user['ChucVu'] == 'quan_ly'): ?>
                  <span class="badge bg-warning text-dark">Quản lý</span>
                <?php elseif ($user['ChucVu'] == 'receptionist' || $user['ChucVu'] == 'le_tan' || $user['ChucVu'] == 'nhan_vien'): ?>
                  <span class="badge bg-primary">Lễ tân</span>
                <?php else: ?>
                  <span class="badge bg-secondary"><?php echo htmlspecialchars($user['ChucVu']); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-2" role="group">
                  <?php if ($canStaffUpdate): ?>
                  <button type="button" 
                          class="btn btn-warning" 
                          data-bs-toggle="modal" 
                          data-bs-target="#updateUserModal"
                          onclick="loadUserData(
                            '<?php echo $user['MaNV']; ?>', 
                            '<?php echo htmlspecialchars($user['TenNhanVien'], ENT_QUOTES); ?>', 
                            '<?php echo htmlspecialchars($user['TenDN'], ENT_QUOTES); ?>', 
                            '<?php echo $user['MaCoSo']; ?>', 
                            '<?php echo $user['ChucVu']; ?>'
                          )">
                    <i class="fas fa-edit"></i> Sửa
                  </button>
                  <?php endif; ?>
                  <?php if ($canStaffDelete): ?>
                  <button type="button" 
                          class="btn btn-danger"
                          onclick="confirmDelete('<?php echo $user['MaNV']; ?>', '<?php echo htmlspecialchars($user['TenNhanVien'], ENT_QUOTES); ?>')">
                    <i class="fas fa-trash"></i> Xóa
                  </button>
                  <?php endif; ?>
                  <?php if (!$canStaffUpdate && !$canStaffDelete): ?>
                  <span class="badge bg-secondary d-flex align-items-center">Chỉ xem</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <i class="fas fa-users fa-2x mb-2"></i>
              <br>
              Chưa có nhân viên nào trong hệ thống
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="User pagination">
      <ul class="pagination pagination-sm justify-content-center mt-3">
        <!-- Nút Previous -->
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=admin&section=users&p=<?php echo $page - 1; ?>">
              <i class="fas fa-chevron-left"></i> Trước
            </a>
          </li>
        <?php endif; ?>

        <!-- Các số trang -->
        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1) {
          echo '<li class="page-item"><a class="page-link" href="?page=admin&section=users&p=1">1</a></li>';
          if ($start > 2) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
        }
        
        for ($i = $start; $i <= $end; $i++):
        ?>
          <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=admin&section=users&p=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php 
        endfor;
        
        if ($end < $totalPages) {
          if ($end < $totalPages - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
          echo '<li class="page-item"><a class="page-link" href="?page=admin&section=users&p=' . $totalPages . '">' . $totalPages . '</a></li>';
        }
        ?>

        <!-- Nút Next -->
        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=admin&section=users&p=<?php echo $page + 1; ?>">
              Sau <i class="fas fa-chevron-right"></i>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>
</div>

<script>
function confirmDelete(maNV, tenNhanVien) {
    if (confirm(`Bạn có chắc chắn muốn xóa nhân viên "${tenNhanVien}"?\n\nHành động này không thể hoàn tác!`)) {
        window.location.href = `?page=admin&section=users&action=delete&MaNV=${maNV}`;
    }
}
</script>
