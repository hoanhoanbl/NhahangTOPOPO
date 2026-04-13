<?php
include dirname(__DIR__,4) . "/config/connect.php";
$canMenuUpdate = isset($auth) && $auth->can('menu', 'update');
$canMenuDelete = isset($auth) && $auth->can('menu', 'delete');

// Include modal sửa món ăn
if ($canMenuUpdate) {
    include __DIR__ . "/update.php";
}

// Cấu hình phân trang
$recordsPerPage = 10; // Số bản ghi trên mỗi trang
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = max(0, ($page - 1) * $recordsPerPage);

// Đếm tổng số bản ghi
$countSql = "SELECT COUNT(*) as total FROM `monan` WHERE 1";
$countResult = mysqli_query($conn, $countSql);
$totalRecords = mysqli_fetch_array($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Lấy dữ liệu với phân trang
// Truy vấn dưới đây thực hiện lấy dữ liệu các món ăn từ bảng `monan` (bảng lưu thông tin món ăn trong hệ thống).
// - $offset: vị trí bản ghi bắt đầu lấy (tính dựa vào trang hiện tại, dùng cho phân trang)
// - $recordsPerPage: số lượng bản ghi sẽ lấy ra trên mỗi trang
$sql = "SELECT * FROM `monan` WHERE 1 LIMIT $offset, $recordsPerPage";
$result = mysqli_query($conn, $sql);
$menuItems = [];
while($row = mysqli_fetch_array($result)){
    $menuItems[] = $row;
}
?>
<!-- Hiển thị danh sách món ăn -->
<div class="card shadow p-4">
    <h4 class="mb-3">Danh sách món ăn (<?php echo $totalRecords; ?> món)</h4>
    <table class="table table-bordered align-middle text-center" id="menuTable">
      <thead class="table-dark">
        <tr>
          <th width="5%">ID</th>
          <th width="20%">Tên món ăn</th>
          <th width="35%">Mô tả</th>
          <th>Ảnh</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($menuItems)): ?>
          <?php foreach($menuItems as $row): ?>
            <?php
            // Giới hạn mô tả
            $motaFull = $row['MoTa'] ?? '';
            $maxLen = 50;  // số ký tự tối đa
            if (mb_strlen($motaFull, 'UTF-8') > $maxLen) {
                $motaShort = mb_substr($motaFull, 0, $maxLen, 'UTF-8') . '...';
            } else {
                $motaShort = $motaFull;
            }
            ?>
            <tr>
              <td><?=$row['MaMon']?></td>
              <td><?=$row['TenMon']?></td>
              <td><?=$motaShort?></td>
              <td><img src="<?=$row['HinhAnhURL']?>" width="80" height="80" class="img-fluid rounded"></td>
              <td>
              <div class="d-flex justify-content-center gap-2" role="group">
              <?php if ($canMenuUpdate): ?>
              <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateMenuModal" 
                          onclick="loadMenuData(<?=$row['MaMon']?>, '<?=htmlspecialchars($row['TenMon'], ENT_QUOTES)?>', '<?=htmlspecialchars($row['HinhAnhURL'], ENT_QUOTES)?>', '<?=htmlspecialchars($row['MoTa'], ENT_QUOTES)?>', '<?=$row['MaDM']?>')">
              <i class="fas fa-edit"></i> Sửa
              </button>
              <?php endif; ?>

              <?php if ($canMenuDelete): ?>
              <a class="btn btn-danger" href="?page=admin&section=menu&action=delete&MaMon=<?=$row['MaMon']?>" onclick="return confirm('Bạn có chắc chắn muốn xóa món ăn \'<?=htmlspecialchars($row['TenMon'], ENT_QUOTES)?>\' không?');"><i class="fas fa-trash"></i> Xoá</a>
              <?php endif; ?>
              <?php if (!$canMenuUpdate && !$canMenuDelete): ?>
              <span class="badge bg-secondary">Chỉ xem</span>
              <?php endif; ?>
              </div>
            </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              <i class="fas fa-utensils fa-3x mb-3"></i>
              <br>
              Chưa có món ăn nào trong hệ thống
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Phân trang (chỉ hiện khi có nhiều hơn 1 trang) -->
    <?php if ($totalPages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <!-- Nút Trước -->
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="<?php echo ($page <= 1) ? '#' : '?page=admin&section=menu&p=' . ($page - 1); ?>">Trước</a>
        </li>
        
        <!-- Các trang số -->
        <?php
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        
        // Hiện trang đầu nếu không ở gần đầu
        if ($startPage > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=admin&section=menu&p=1">1</a></li>';
            if ($startPage > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        // Hiện các trang xung quanh trang hiện tại
        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i == $page) ? 'active' : '';
            echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=admin&section=menu&p=' . $i . '">' . $i . '</a></li>';
        }
        
        // Hiện trang cuối nếu không ở gần cuối
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="?page=admin&section=menu&p=' . $totalPages . '">' . $totalPages . '</a></li>';
        }
        ?>
        
        <!-- Nút Sau -->
        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="<?php echo ($page >= $totalPages) ? '#' : '?page=admin&section=menu&p=' . ($page + 1); ?>">Sau</a>
        </li>
      </ul>
    </nav>
    <?php endif; ?>
   </div>
