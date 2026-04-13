<?php
include "connect.php"; // Kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form và làm sạch
    $tenMaUD = trim($_POST['TenMaUD'] ?? '');
    $moTa = trim($_POST['MoTa'] ?? '');
    $giaTriGiam = floatval($_POST['GiaTriGiam'] ?? 0);
    $loaiGiamGia = $_POST['LoaiGiamGia'] ?? 'phantram';
    $dieuKien = !empty($_POST['DieuKien']) ? trim($_POST['DieuKien']) : null;
    $ngayBatDau = $_POST['NgayBD'] ?? '';
    $ngayKetThuc = $_POST['NgayKT'] ?? '';

    // Validate dữ liệu chi tiết
    $errors = [];
    
    if (empty($tenMaUD)) {
        $errors[] = "Tên ưu đãi không được để trống";
    } elseif (strlen($tenMaUD) > 50) {
        $errors[] = "Tên ưu đãi không được quá 50 ký tự";
    }
    
    if (empty($moTa)) {
        $errors[] = "Mô tả ưu đãi không được để trống";
    }
    
    if ($giaTriGiam <= 0) {
        $errors[] = "Giá trị giảm giá phải lớn hơn 0";
    }
    
    if ($loaiGiamGia == 'phantram' && $giaTriGiam > 100) {
        $errors[] = "Giảm giá phần trăm không được vượt quá 100%";
    }
    
    if (empty($ngayBatDau) || empty($ngayKetThuc)) {
        $errors[] = "Ngày bắt đầu và ngày kết thúc không được để trống";
    } elseif (strtotime($ngayBatDau) >= strtotime($ngayKetThuc)) {
        $errors[] = "Ngày bắt đầu phải trước ngày kết thúc";
    }
    
    if (!in_array($loaiGiamGia, ['phantram', 'sotien'])) {
        $errors[] = "Loại giảm giá không hợp lệ";
    }

    if (empty($errors)) {
        // Chuẩn bị câu lệnh SQL để chèn dữ liệu
        $sql = "INSERT INTO uudai (TenMaUD, MoTa, GiaTriGiam, LoaiGiamGia, DieuKien, NgayBD, NgayKT) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        // Gán giá trị và thực thi
        $stmt->bind_param("ssdssss", $tenMaUD, $moTa, $giaTriGiam, $loaiGiamGia, $dieuKien, $ngayBatDau, $ngayKetThuc);
        
        if ($stmt->execute()) {
            // Thành công, chuyển hướng về trang danh sách
            echo "<script>alert('Thêm ưu đãi thành công!'); window.location.href='?page=admin&section=uudai&status=add_success';</script>";
        } else {
            // Lỗi, chuyển hướng với thông báo lỗi
            echo "<script>alert('Lỗi: Không thể thêm ưu đãi!'); window.location.href='?page=admin&section=uudai&status=add_failed';</script>";
        }
        $stmt->close();
    } else {
        // Dữ liệu không hợp lệ
        $errorMsg = implode('\\n', $errors);
        echo "<script>alert('Lỗi:\\n" . $errorMsg . "'); window.history.back();</script>";
    }
} else {
    // Không phải là POST request
    echo "<script>window.location.href='?page=admin&section=uudai';</script>";
}
exit();
?>