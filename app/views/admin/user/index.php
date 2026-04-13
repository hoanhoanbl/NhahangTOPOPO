<?php
include dirname(__DIR__,4) . "/config/connect.php";
$canStaffCreate = isset($auth) && $auth->can('staff', 'create');
$canStaffUpdate = isset($auth) && $auth->can('staff', 'update');
$canStaffDelete = isset($auth) && $auth->can('staff', 'delete');
$permissionNotice = null;

// Xử lý các action
if(isset($_GET['action'])){
    switch ($_GET['action']) {
        case 'delete':
            if ($canStaffDelete) {
                include "process-delete.php";
            } else {
                $permissionNotice = 'Bạn không có quyền xóa nhân viên.';
            }
            break;
        case 'process-update':
            if ($canStaffUpdate) {
                include "process-update.php";
            } else {
                $permissionNotice = 'Bạn không có quyền cập nhật nhân viên.';
            }
            break;
        case 'process-create':
            if ($canStaffCreate) {
                include "process-create.php";
            } else {
                $permissionNotice = 'Bạn không có quyền thêm nhân viên.';
            }
            break;
    }
}

//Modal thêm nhân viên
if ($canStaffCreate) {
    include __DIR__ . "/create.php";
}

?>
<div class="container-fluid">
    <?php if (!empty($permissionNotice)): ?>
    <div class="alert alert-warning">
      <i class="fas fa-lock me-2"></i><?php echo htmlspecialchars($permissionNotice); ?>
    </div>
    <?php endif; ?>

    <!-- Search bar và nút thêm -->
  <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm nhân viên...">
    </div>
    <div class="col-md-6 text-end">
      <?php if ($canStaffCreate): ?>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus"></i> Thêm nhân viên mới
      </button>
      <?php endif; ?>
    </div>
  </div>

    <?php
        if(isset($_GET['action'])){
            switch ($_GET['action']) {
                case 'view':
                    include __DIR__ . "/view.php";
                    break;
                case 'create':
                    if ($canStaffCreate) {
                        include __DIR__ . "/create.php";
                    } else {
                        include __DIR__ . "/view.php";
                    }
                    break;
                case 'update':
                    if ($canStaffUpdate) {
                        include __DIR__ . "/update.php";
                    } else {
                        include __DIR__ . "/view.php";
                    }
                    break;
                default:
                    include "view.php";
                    break;
            }
        } else {
            // Mặc định hiển thị danh sách nhân viên khi không có tham số action
            include __DIR__ . "/view.php";
        }
    ?>

    </div>

<script>
  // Tìm kiếm nhân viên
  document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#userTable tbody tr");
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
        let tbody = document.querySelector("#userTable tbody");
        let newRow = document.createElement("tr");
        newRow.id = "noSearchResult";
        newRow.innerHTML = `
          <td colspan="5" class="text-center text-muted py-4">
            <i class="fas fa-search fa-2x mb-2"></i>
            <br>
            Không tìm thấy nhân viên phù hợp với từ khóa "<strong>${filter}</strong>"
            <br>
            <small>Thử tìm kiếm với từ khóa khác</small>
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
