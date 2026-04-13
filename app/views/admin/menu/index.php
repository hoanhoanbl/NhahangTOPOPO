<?php
include dirname(__DIR__,4) . "/config/connect.php";
$canMenuCreate = isset($auth) && $auth->can('menu', 'create');
$canMenuUpdate = isset($auth) && $auth->can('menu', 'update');
$canMenuDelete = isset($auth) && $auth->can('menu', 'delete');
$permissionNotice = null;

// Xá»­ lÃ½ cÃ¡c action
if(isset($_GET['action'])){
    switch ($_GET['action']) {
        case 'delete':
            if ($canMenuDelete) {
                include "process-delete.php";
            } else {
                $permissionNotice = 'Bạn không có quyền xóa món ăn.';
            }
            break;
        case 'process-update':
            if ($canMenuUpdate) {
                include "process-update.php";
            } else {
                $permissionNotice = 'Bạn không có quyền cập nhật món ăn.';
            }
            break;
        case 'process-create':
            if ($canMenuCreate) {
                include "process-create.php";
            } else {
                $permissionNotice = 'Bạn không có quyền thêm món ăn.';
            }
            break;
    }
}

//Modal thÃªm mÃ³n Äƒn
if ($canMenuCreate) {
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
      <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm món ăn trên trang hiện tại...">
      <!-- <small class="text-muted">
        <i class="fas fa-info-circle"></i> TÃ¬m kiáº¿m chá»‰ hoáº¡t Ä‘á»™ng trÃªn trang hiá»‡n táº¡i
      </small> -->
    </div>
    <div class="col-md-6 text-end">
      <a href="?page=admin&section=menu_branch" class="btn btn-info">
        <i class="fas fa-store"></i> Quản lý Menu theo Cơ sở
      </a>
      <?php if ($canMenuCreate): ?>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBranchModal">
        <i class="fas fa-plus"></i> Thêm món ăn mới
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
                    if ($canMenuCreate) {
                        include __DIR__ . "/create.php";
                    } else {
                        include __DIR__ . "/view.php";
                    }
                    break;
                case 'update':
                    if ($canMenuUpdate) {
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
            // Máº·c Ä‘á»‹nh hiá»ƒn thá»‹ danh sÃ¡ch mÃ³n Äƒn khi khÃ´ng cÃ³ tham sá»‘ action
            include __DIR__ . "/view.php";
        }
    ?>


    </div>

<script>
  // TÃ¬m kiáº¿m mÃ³n Äƒn
  document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#menuTable tbody tr");
    let visibleCount = 0;
    
    rows.forEach(row => {
      // Bá» qua hÃ ng "khÃ´ng cÃ³ dá»¯ liá»‡u"  
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
    
    // Hiá»ƒn thá»‹ thÃ´ng bÃ¡o náº¿u khÃ´ng tÃ¬m tháº¥y káº¿t quáº£
    let noResultRow = document.querySelector("#noSearchResult");
    if (visibleCount === 0 && filter.trim() !== "") {
      if (!noResultRow) {
        let tbody = document.querySelector("#menuTable tbody");
        let newRow = document.createElement("tr");
        newRow.id = "noSearchResult";
        newRow.innerHTML = `
          <td colspan="5" class="text-center text-muted py-4">
            <i class="fas fa-search fa-2x mb-2"></i>
            <br>
            KhÃ´ng tÃ¬m tháº¥y mÃ³n Äƒn phÃ¹ há»£p vá»›i tá»« khÃ³a "<strong>${filter}</strong>"
            <br>
            <small>Thá»­ tÃ¬m kiáº¿m vá»›i tá»« khÃ³a khÃ¡c hoáº·c kiá»ƒm tra cÃ¡c trang khÃ¡c</small>
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


