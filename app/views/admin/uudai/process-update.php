<?php
include __DIR__ . "/connect.php"; // Kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['MaUD'])) {
    $maUuDai = $_GET['MaUD'];
    
    // Lấy dữ liệu từ form
    $tenMaUD = $_POST['TenMaUD'] ?? '';
    $moTa = $_POST['MoTa'] ?? '';
    $giaTriGiam = $_POST['GiaTriGiam'] ?? 0;
    $loaiGiamGia = $_POST['LoaiGiamGia'] ?? 'phantram';
    $dieuKien = $_POST['DieuKien'] ?? null;
    $ngayBatDau = $_POST['NgayBD'] ?? '';
    $ngayKetThuc = $_POST['NgayKT'] ?? '';

    // Validate
    if (!empty($tenMaUD) && !empty($moTa) && !empty($ngayBatDau) && !empty($ngayKetThuc)) {
        $sql = "UPDATE uudai SET TenMaUD = ?, MoTa = ?, GiaTriGiam = ?, LoaiGiamGia = ?, DieuKien = ?, NgayBD = ?, NgayKT = ? WHERE MaUD = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssssi", $tenMaUD, $moTa, $giaTriGiam, $loaiGiamGia, $dieuKien, $ngayBatDau, $ngayKetThuc, $maUuDai);
        
        if ($stmt->execute()) {
            echo "<script>alert('Cập nhật ưu đãi thành công!'); window.location.href='?page=admin&section=uudai&status=update_success';</script>";
        } else {
            echo "<script>alert('Lỗi: Không thể cập nhật ưu đãi!'); window.location.href='?page=admin&section=uudai&status=update_failed';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
    }
} else {
    echo "<script>window.location.href='?page=admin&section=uudai';</script>";
}
exit();
?>