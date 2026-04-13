<?php
include dirname(__DIR__,4) . "/config/connect.php";
$canUuDaiCreate = isset($auth) && $auth->can('uudai', 'create');
$canUuDaiUpdate = isset($auth) && $auth->can('uudai', 'update');
$canUuDaiDelete = isset($auth) && $auth->can('uudai', 'delete');
$permissionNotice = null;

// Xử lý các action
if(isset($_GET['action'])){
    switch ($_GET['action']) {
        case 'view':
            include "view.php";
            return; // Dừng để không hiển thị danh sách
        case 'delete':
            if ($canUuDaiDelete) {
                include "process-delete.php";
            } else {
                $permissionNotice = 'Bạn không có quyền xóa ưu đãi.';
            }
            break;
        case 'process-update':
            if ($canUuDaiUpdate) {
                include "process-update.php";
            } else {
                $permissionNotice = 'Bạn không có quyền cập nhật ưu đãi.';
            }
            break;
        case 'process-create':
            if ($canUuDaiCreate) {
                include "process-create.php";
            } else {
                $permissionNotice = 'Bạn không có quyền thêm ưu đãi.';
            }
            break;
    }
}

//Modal thêm ưu đãi
if ($canUuDaiCreate) {
    include __DIR__ . "/create.php";
}

?>
<div class="container-fluid">
    <?php if (!empty($permissionNotice)): ?>
    <div class="alert alert-warning">
      <i class="fas fa-lock me-2"></i><?php echo htmlspecialchars($permissionNotice); ?>
    </div>
    <?php endif; ?>

    <!-- Search bar -->
  <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm ưu đãi trên trang hiện tại...">
    </div>
    <div class="col-md-6 text-end">
      <?php if ($canUuDaiCreate): ?>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUuDaiModal">
        <i class="fas fa-plus"></i> Thêm ưu đãi mới
      </button>
      <?php endif; ?>

    </div>
  </div>

    <?php
        // Logic điều hướng đã được chuyển lên dashboard.php
        // Phần này chỉ cần include file list.php để hiển thị danh sách
        include __DIR__ . "/list.php";
    ?>


    </div>

<script>
  // Tìm kiếm ưu đãi
  document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#uudaiTable tbody tr");
    let visibleCount = 0;
    
    rows.forEach(row => {
      // Bỏ qua hàng "không có dữ liệu"  
      if (row.cells.length === 1 && row.cells[0].colSpan > 1) {
        return;
      }
      
      let text = row.innerText.toLowerCase();
      if (text.includes(filter)) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });
    
    // Hiển thị thông báo nếu không tìm thấy kết quả
    let noResultRow = document.querySelector("#noSearchResult");
    if (visibleCount === 0 && filter.trim() !== "") {
      if (!noResultRow) {
        let tbody = document.querySelector("#uudaiTable tbody");
        let newRow = document.createElement("tr");
        newRow.id = "noSearchResult";
        newRow.innerHTML = `
          <td colspan="6" class="text-center text-muted py-4">
            <i class="fas fa-search fa-2x mb-2"></i>
            <br>
            Không tìm thấy ưu đãi phù hợp với từ khóa "<strong>${filter}</strong>"
            <br>
            <small>Thử tìm kiếm với từ khóa khác hoặc kiểm tra các trang khác</small>
          </td>
        `;
        tbody.appendChild(newRow);
      } else {
        noResultRow.querySelector("strong").textContent = filter;
        noResultRow.style.display = "";
      }
    } else if (noResultRow) {
      noResultRow.style.display = "none";
    }
  });
</script>

</div>
