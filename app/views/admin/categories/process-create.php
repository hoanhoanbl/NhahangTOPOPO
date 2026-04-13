<?php
include dirname(__DIR__,4) . "/config/connect.php";

// Kiểm tra xem dữ liệu đã được gửi chưa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra trường bắt buộc
    if (!empty($_POST['TenDM'])) {
        
        $tenDM = trim($_POST['TenDM']);
        $moTa = trim($_POST['MoTa']) ?? '';
        
        // Kiểm tra tên danh mục đã tồn tại chưa
        $checkSql = "SELECT COUNT(*) as count FROM `danhmuc` WHERE `TenDM` = '$tenDM'";
        $checkResult = mysqli_query($conn, $checkSql);
        $row = mysqli_fetch_assoc($checkResult);
        
        if ($row['count'] > 0) {
            echo "<script>alert('Tên danh mục đã tồn tại! Vui lòng chọn tên khác.'); window.history.back();</script>";
        } else {
            // Thực hiện INSERT
            $sql = "INSERT INTO `danhmuc`(`TenDM`) VALUES ('$tenDM')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Thêm danh mục thành công!'); window.location.href = '?page=admin&section=categories';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra: " . mysqli_error($conn) . "'); window.history.back();</script>";
            }
        }
    } else {
        echo "<script>alert('Vui lòng nhập tên danh mục!'); window.history.back();</script>";
    }
} else {
    // Nếu không phải POST request, chuyển hướng về trang chính (dùng JS)
    echo "<script>window.location.href='?page=admin&section=categories';</script>";
    exit();
}
?>
