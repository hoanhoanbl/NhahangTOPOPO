<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__, 4) . "/config/connect.php";
$canTableCreate = isset($auth) && $auth->can('table', 'create');
$canTableUpdate = isset($auth) && $auth->can('table', 'update');
$canTableDelete = isset($auth) && $auth->can('table', 'delete');
$permissionNotice = null;

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if ($canTableDelete) {
                include "process-delete.php";
            } else {
                $permissionNotice = 'Bạn không có quyền xóa bàn.';
            }
            break;
        case 'process-update':
            if ($canTableUpdate) {
                include "process-update.php";
            } else {
                $permissionNotice = 'Bạn không có quyền cập nhật bàn.';
            }
            break;
        case 'process-create':
            if ($canTableCreate) {
                include "process-create.php";
            } else {
                $permissionNotice = 'Bạn không có quyền thêm bàn.';
            }
            break;
    }
}

if ($canTableCreate) {
    include __DIR__ . "/create.php";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container-fluid">
  <?php if (!empty($permissionNotice)): ?>
    <div class="alert alert-warning">
      <i class="fas fa-lock me-2"></i><?php echo htmlspecialchars($permissionNotice); ?>
    </div>
  <?php endif; ?>

  <div class="row mb-3">
    <div class="col-md-12 text-end">
      <?php if ($canTableCreate): ?>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTableModal">
          <i class="fas fa-plus"></i> Thêm bàn mới
        </button>
      <?php endif; ?>
    </div>
  </div>

  <?php
  if (isset($_GET['action'])) {
      switch ($_GET['action']) {
          case 'view':
              include __DIR__ . "/view.php";
              break;
          case 'create':
              if ($canTableCreate) {
                  include __DIR__ . "/create.php";
              } else {
                  include __DIR__ . "/view.php";
              }
              break;
          case 'update':
              if ($canTableUpdate) {
                  include __DIR__ . "/update.php";
              } else {
                  include __DIR__ . "/view.php";
              }
              break;
          case 'status':
              include __DIR__ . "/status.php";
              break;
          default:
              include __DIR__ . "/view.php";
              break;
      }
  } else {
      include __DIR__ . "/view.php";
  }
  ?>
</div>

