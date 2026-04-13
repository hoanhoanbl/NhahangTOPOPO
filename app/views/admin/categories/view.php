<?php
include dirname(__DIR__,4) . "/config/connect.php";
$canMenuUpdate = isset($auth) && $auth->can('menu', 'update');
$canMenuDelete = isset($auth) && $auth->can('menu', 'delete');

// Include modal sửa danh mục
if ($canMenuUpdate) {
    include __DIR__ . "/update.php";
}

// Cấu hình phân trang
$recordsPerPage = 10; // Số bản ghi trên mỗi trang
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = max(0, ($page - 1) * $recordsPerPage);

// Đếm tổng số bản ghi
$countSql = "SELECT COUNT(*) as total FROM `danhmuc` WHERE 1";
$countResult = mysqli_query($conn, $countSql);
$totalRecords = mysqli_fetch_array($countResult)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Lấy dữ liệu danh mục với số lượng món ăn
$sql = "SELECT dm.*, COUNT(ma.MaMon) as SoLuongMon 
        FROM `danhmuc` dm 
        LEFT JOIN `monan` ma ON dm.MaDM = ma.MaDM 
        GROUP BY dm.MaDM 
        ORDER BY dm.TenDM 
        LIMIT $offset, $recordsPerPage";
$result = mysqli_query($conn, $sql);
$categories = [];
while($row = mysqli_fetch_array($result)){
    $categories[] = $row;
}
?>

<!-- Hiển thị danh sách danh mục -->
<div class="card shadow p-4">
    <h4 class="mb-3">Danh sách danh mục món ăn (<?php echo $totalRecords; ?> danh mục)</h4>
    <table class="table table-bordered align-middle text-center" id="categoryTable">
      <thead class="table-dark">
        <tr>
          <th width="5%">STT</th>
          <th width="30%">Tên Danh Mục</th>
          <th width="15%">Số Lượng Món</th>
          <th width="50%">Hành Động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($categories) > 0): ?>
          <?php foreach ($categories as $index => $category): ?>
            <tr>
              <td><?php echo $offset + $index + 1; ?></td>
              <td class="text-start fw-bold"><?php echo htmlspecialchars($category['TenDM']); ?></td>
              <td>
                <span class="badge bg-secondary"><?php echo $category['SoLuongMon']; ?> món</span>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-2" role="group">
                  <button type="button" 
                          class="btn btn-primary" 
                          onclick="viewCategoryDetails('<?php echo $category['MaDM']; ?>', '<?php echo htmlspecialchars($category['TenDM'], ENT_QUOTES); ?>')">
                    <i class="fas fa-eye"></i> Xem món
                  </button>
                  <?php if ($canMenuUpdate): ?>
                  <button type="button" 
                          class="btn btn-warning" 
                          data-bs-toggle="modal" 
                          data-bs-target="#updateCategoryModal"
                          onclick="loadCategoryData(
                            '<?php echo $category['MaDM']; ?>', 
                            '<?php echo htmlspecialchars($category['TenDM'], ENT_QUOTES); ?>'
                          )">
                    <i class="fas fa-edit"></i> Sửa
                  </button>
                  <?php endif; ?>
                  <?php if ($canMenuDelete): ?>
                  <button type="button" 
                          class="btn btn-danger"
                          onclick="confirmDelete('<?php echo $category['MaDM']; ?>', '<?php echo htmlspecialchars($category['TenDM'], ENT_QUOTES); ?>', <?php echo $category['SoLuongMon']; ?>)">
                    <i class="fas fa-trash"></i> Xóa
                  </button>
                  <?php endif; ?>
                  <?php if (!$canMenuUpdate && !$canMenuDelete): ?>
                  <span class="badge bg-secondary d-flex align-items-center">Chỉ xem</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center text-muted py-4">
              <i class="fas fa-tags fa-2x mb-2"></i>
              <br>
              Chưa có danh mục nào trong hệ thống
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Category pagination">
      <ul class="pagination pagination-sm justify-content-center mt-3">
        <!-- Nút Previous -->
        <?php if ($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=admin&section=categories&p=<?php echo $page - 1; ?>">
              <i class="fas fa-chevron-left"></i> Trước
            </a>
          </li>
        <?php endif; ?>

        <!-- Các số trang -->
        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1) {
          echo '<li class="page-item"><a class="page-link" href="?page=admin&section=categories&p=1">1</a></li>';
          if ($start > 2) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
        }
        
        for ($i = $start; $i <= $end; $i++):
        ?>
          <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=admin&section=categories&p=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php 
        endfor;
        
        if ($end < $totalPages) {
          if ($end < $totalPages - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
          echo '<li class="page-item"><a class="page-link" href="?page=admin&section=categories&p=' . $totalPages . '">' . $totalPages . '</a></li>';
        }
        ?>

        <!-- Nút Next -->
        <?php if ($page < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=admin&section=categories&p=<?php echo $page + 1; ?>">
              Sau <i class="fas fa-chevron-right"></i>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>
</div>

<script>
function confirmDelete(maDM, tenDM, soLuongMon) {
    let message = `Bạn có chắc chắn muốn xóa danh mục "${tenDM}"?`;
    
    if (soLuongMon > 0) {
        message += `\n\nCảnh báo: Danh mục này có ${soLuongMon} món ăn. Việc xóa danh mục sẽ ảnh hưởng đến các món ăn này!`;
    }
    
    message += `\n\nHành động này không thể hoàn tác!`;
    
    if (confirm(message)) {
        window.location.href = `?page=admin&section=categories&action=delete&MaDM=${maDM}`;
    }
}

function viewCategoryDetails(maDM, tenDM) {
    // Chuyển đến trang quản lý menu với filter theo danh mục
    window.location.href = `?page=admin&section=menu&category=${maDM}`;
}
</script>
