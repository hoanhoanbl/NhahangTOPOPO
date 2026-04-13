<?php
// Include AdminBranchController để lấy dữ liệu
require_once __DIR__ . '/../../../controllers/admin/AdminBranchController.php';

// Khởi tạo controller và lấy dữ liệu branches
$controller = new AdminBranchController();
$branches = $controller->getBranches();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý cơ sở</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table td, .table th {
      vertical-align: middle;
    }
    .status-active { color: green; font-weight: bold; }
    .status-inactive { color: red; font-weight: bold; }
    .branch-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }
    .btn-sm {
      margin: 2px;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4 text-center">Hệ thống quản lý cơ sở</h2>


  <!-- Search bar -->
  <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm cơ sở...">
    </div>
    <div class="col-md-6 text-end">
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBranchModal">
        <i class="fas fa-plus"></i> Thêm cơ sở mới
      </button>
    </div>
  </div>

  <!-- Danh sách cơ sở -->
  <div class="card shadow p-4">
    <h4 class="mb-3">Danh sách cơ sở (<?php echo count($branches); ?> cơ sở)</h4>
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center" id="branchTable">
        <thead class="table-dark">
          <tr>
            <th>Mã cơ sở</th>
            <th>Tên cơ sở</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <!-- <th>Ảnh</th> -->
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($branches)): ?>
            <?php foreach ($branches as $index => $branch): ?>
              <tr>
                <td><?php echo htmlspecialchars($branch['MaCoSo']); ?></td>
                <td class="text-start">
                  <strong><?php echo htmlspecialchars($branch['TenCoSo']); ?></strong>
                </td>
                <td class="text-start"><?php echo htmlspecialchars($branch['DiaChi']); ?></td>
                <td><?php echo htmlspecialchars($branch['DienThoai']); ?></td>
                <!-- <td>
                  <?php if (!empty($branch['AnhUrl'])): ?>
                    <img src="<?php echo htmlspecialchars($branch['AnhUrl']); ?>" 
                         alt="Ảnh cơ sở" class="branch-image">
                  <?php else: ?>
                    <img src="https://via.placeholder.com/60x60?text=No+Image" 
                         alt="Không có ảnh" class="branch-image">
                  <?php endif; ?>
                </td> -->
                <td>
                
                  <button class="btn btn-warning btn-sm" 
                          onclick="editBranch(<?php echo $branch['MaCoSo']; ?>)" 
                          title="Chỉnh sửa">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-sm" 
                          onclick="deleteBranch(<?php echo $branch['MaCoSo']; ?>)" 
                          title="Xóa">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                <i class="fas fa-store-slash fa-3x mb-3"></i>
                <br>
                Chưa có cơ sở nào trong hệ thống
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Phân trang (nếu cần) -->
    <?php if (count($branches) > 10): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item disabled"><a class="page-link">Trước</a></li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">Sau</a></li>
      </ul>
    </nav>
    <?php endif; ?>
  </div>
</div>

<!-- Modal thêm cơ sở -->
<div class="modal fade" id="addBranchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Thêm cơ sở mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addBranchForm" onsubmit="event.preventDefault(); addBranch();">
          <div class="mb-3">
            <label for="tenCoSo" class="form-label">Tên cơ sở <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="tenCoSo" required>
          </div>
          <div class="mb-3">
            <label for="diaChi" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="diaChi" required>
          </div>
          <div class="mb-3">
            <label for="dienThoai" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
              <input type="text" id="dienThoai" class="form-control"  required pattern="0[0-9]{9}" maxlength="10" placeholder="Ví dụ: 0922782387">
            <small class="text-muted">Nhập 10 chữ số</small>
          </div>
          <div class="mb-3" style="display: none;">
            <label for="anhUrl" class="form-label">URL ảnh</label>
            <input type="url" class="form-control" id="anhUrl" placeholder="https://example.com/image.jpg" maxlength="100">
            <small class="text-muted">Tối đa 100 ký tự</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Thêm cơ sở</button>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<!-- Modal chỉnh sửa cơ sở -->
<div class="modal fade" id="editBranchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chỉnh sửa cơ sở</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editBranchForm" onsubmit="event.preventDefault(); updateBranch();">
          <input type="hidden" id="editMaCoSo">
          <div class="mb-3">
            <label for="editTenCoSo" class="form-label">Tên cơ sở <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editTenCoSo" required>
          </div>
          <div class="mb-3">
            <label for="editDiaChi" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editDiaChi" required>
          </div>
          <div class="mb-3">
            <label for="editDienThoai" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" id="editDienThoai" required pattern="0[0-9]{9}" maxlength="10" placeholder="Ví dụ: 0922782387">
            <small class="text-muted">Nhập 10 chữ số</small>
          </div>
          <div class="mb-3" style="display: none;">
            <label for="editAnhUrl" class="form-label">URL ảnh</label>
            <input type="url" class="form-control" id="editAnhUrl" placeholder="https://example.com/image.jpg" maxlength="100">
            <small class="text-muted">Tối đa 100 ký tự</small>
          </div> 
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-warning" >Cập nhật</button>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Tìm kiếm cơ sở
  document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#branchTable tbody tr");
    
    rows.forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // Xem chi tiết cơ sở
  function viewBranch(id) {
    fetch(`app/controllers/admin/AdminBranchController.php?action=get_data`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const branch = data.data.find(b => b.MaCoSo == id);
          if (branch) {
            alert(`Chi tiết cơ sở:\nTên: ${branch.TenCoSo}\nĐịa chỉ: ${branch.DiaChi}\nSĐT: ${branch.DienThoai}`);
          }
        }
      })
      .catch(error => console.error('Lỗi:', error));
  }

  // Chỉnh sửa cơ sở
  function editBranch(id) {
    console.log('editBranch called with id:', id);
    
    // Đường dẫn tuyệt đối để tránh lỗi khi include vào dashboard
    fetch(`app/controllers/admin/AdminBranchController.php?action=get_data`)
      .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Get as text first to check for HTML errors
      })
      .then(text => {
        console.log('Raw response:', text);
        try {
          const data = JSON.parse(text);
          console.log('API response data:', data);
          if (data.success) {
            const branch = data.data.find(b => b.MaCoSo == id);
            console.log('Found branch:', branch);
            if (branch) {
              // Điền dữ liệu vào form edit
              document.getElementById('editMaCoSo').value = branch.MaCoSo;
              document.getElementById('editTenCoSo').value = branch.TenCoSo;
              document.getElementById('editDiaChi').value = branch.DiaChi;
              document.getElementById('editDienThoai').value = branch.DienThoai;
              document.getElementById('editAnhUrl').value = branch.AnhUrl || '';
              
              // Hiển thị modal edit
              const modal = new bootstrap.Modal(document.getElementById('editBranchModal'));
              console.log('Showing modal');
              modal.show();
            } else {
              console.error('Branch not found for id:', id);
              alert('Không tìm thấy cơ sở với ID: ' + id);
            }
          } else {
            console.error('API returned error:', data.message);
            alert('Lỗi API: ' + data.message);
          }
        } catch (parseError) {
          console.error('JSON parse error:', parseError);
          console.error('Response was not JSON:', text);
          alert('Lỗi: Server trả về dữ liệu không đúng định dạng JSON');
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        alert('Lỗi khi tải dữ liệu: ' + error.message);
      });
  }

  // Xóa cơ sở
  function deleteBranch(id) {
    if (confirm('Bạn có chắc chắn muốn xóa cơ sở này?')) {
      const formData = new FormData();
      formData.append('maCoSo', id);

      fetch('app/controllers/admin/AdminBranchController.php?action=delete', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          refreshPage();
        }
      })
      .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra khi xóa cơ sở');
      });
    }
  }

  // Thêm cơ sở mới
  function addBranch() {
    const tenCoSo = document.getElementById('tenCoSo').value;
    const diaChi = document.getElementById('diaChi').value;
    const dienThoai = document.getElementById('dienThoai').value;
    const anhUrl = document.getElementById('anhUrl').value;

    if (tenCoSo && diaChi && dienThoai) {
      const formData = new FormData();
      formData.append('tenCoSo', tenCoSo);
      formData.append('diaChi', diaChi);
      formData.append('dienThoai', dienThoai);
      formData.append('anhUrl', anhUrl);

      fetch('app/controllers/admin/AdminBranchController.php?action=add', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('addBranchModal')).hide();
          document.getElementById('addBranchForm').reset();
          refreshPage();
        }
      })
      .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra khi thêm cơ sở');
      });
    } else {
      alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
    }
  }

  // Cập nhật cơ sở
  function updateBranch() {
    const maCoSo = document.getElementById('editMaCoSo').value;
    const tenCoSo = document.getElementById('editTenCoSo').value;
    const diaChi = document.getElementById('editDiaChi').value;
    const dienThoai = document.getElementById('editDienThoai').value;
    const anhUrl = document.getElementById('editAnhUrl').value;

    if (maCoSo && tenCoSo && diaChi && dienThoai) {
      const formData = new FormData();
      formData.append('maCoSo', maCoSo);
      formData.append('tenCoSo', tenCoSo);
      formData.append('diaChi', diaChi);
      formData.append('dienThoai', dienThoai);
      formData.append('anhUrl', anhUrl);

      fetch('app/controllers/admin/AdminBranchController.php?action=update', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('editBranchModal')).hide();
          refreshPage();
        }
      })
      .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra khi cập nhật cơ sở');
      });
    } else {
      alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
    }
  }

  // Refresh trang sau khi thêm/sửa/xóa
  function refreshPage() {
    location.reload();
  }
</script>

</body>
</html>
