<?php
include __DIR__ . "/connect.php";

// Kiểm tra xem MaUuDai có được truyền qua URL hay không
if (!isset($_GET['MaUD']) || empty($_GET['MaUD'])) {
    echo '<div class="alert alert-danger">Lỗi: Không tìm thấy Mã Ưu đãi.</div>';
    exit;
}

$mauudai = mysqli_real_escape_string($conn, $_GET['MaUD']);

// Truy vấn thông tin chi tiết Ưu đãi
$sql = "SELECT * FROM `uudai` WHERE MaUD = '$mauudai'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo '<div class="alert alert-warning">Lỗi: Ưu đãi không tồn tại.</div>';
    exit;
}

$uudai = mysqli_fetch_array($result);

// Định dạng ngày tháng
$ngay_bd_hien_thi = date('d/m/Y', strtotime($uudai['NgayBD']));
$ngay_kt_hien_thi = date('d/m/Y', strtotime($uudai['NgayKT']));
?>

<div class="card shadow p-4">
    <h3 class="mb-4 text-success">
        <i class="fas fa-eye me-2"></i> Chi tiết Ưu đãi: <strong><?= htmlspecialchars($uudai['TenMaUD']) ?></strong>
    </h3>
    
    <div class="row">
        <div class="col-md-4 text-center mb-4">
            <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 250px;">
                <i class="fas fa-gift fa-5x text-muted"></i>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-borderless table-striped">
                <tbody>
                    <tr>
                        <th width="30%">Mã Ưu đãi (ID)</th>
                        <td><?= htmlspecialchars($uudai['MaUD']) ?></td>
                    </tr>
                    <tr>
                        <th>Tiêu đề Ưu đãi</th>
                        <td><strong><?= htmlspecialchars($uudai['TenMaUD']) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Mức giảm giá</th>
                        <td class="fw-bold text-danger"><?= htmlspecialchars($uudai['GiaTriGiam']) ?><?= $uudai['LoaiGiamGia'] == 'phantram' ? '%' : ' VNĐ' ?></td>
                    </tr>
                    <tr>
                        <th>Ngày Bắt đầu</th>
                        <td><?= $ngay_bd_hien_thi ?></td>
                    </tr>
                    <tr>
                        <th>Ngày Kết thúc</th>
                        <td><?= $ngay_kt_hien_thi ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <h5 class="mt-4 border-bottom pb-2">Mô tả chi tiết và điều kiện áp dụng</h5>
    <div class="p-3 bg-light rounded">
        <p style="white-space: pre-wrap;"><?= htmlspecialchars($uudai['MoTa']) ?></p>
    </div>

    <div class="mt-4 text-end">
        <a href="?page=admin&section=uudai" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại Danh sách
        </a>
        <a href="?page=admin&section=uudai&action=edit&MaUD=<?= $mauudai ?>" class="btn btn-warning ms-2">
            <i class="fas fa-edit"></i> Sửa Ưu đãi này
        </a>
    </div>
</div>