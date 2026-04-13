<?php
include dirname(__DIR__,4) . "/config/connect.php";

// Kiểm tra xem dữ liệu đã được gửi chưa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra các trường bắt buộc
    if (!empty($_POST['TenDM']) && isset($_GET['MaDM'])) {
        
        $maDM = $_GET['MaDM'];
        $tenDM = trim($_POST['TenDM']);
        $moTa = trim($_POST['MoTa']) ?? '';
        
        // Kiểm tra tên danh mục đã tồn tại chưa (trừ bản ghi hiện tại)
        $checkSql = "SELECT COUNT(*) as count FROM `danhmuc` WHERE `TenDM` = '$tenDM' AND `MaDM` != '$maDM'";
        $checkResult = mysqli_query($conn, $checkSql);
        $row = mysqli_fetch_assoc($checkResult);
        
        if ($row['count'] > 0) {
            echo "<script>alert('Tên danh mục đã tồn tại! Vui lòng chọn tên khác.'); window.history.back();</script>";
        } else {
            // Cập nhật danh mục
            $sql = "UPDATE `danhmuc` SET `TenDM`='$tenDM' WHERE `MaDM`='$maDM'";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Cập nhật danh mục thành công!'); window.location.href = '?page=admin&section=categories';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra: " . mysqli_error($conn) . "'); window.history.back();</script>";
            }
        }
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
    }
} else {
    // Nếu không phải POST request, chuyển hướng về trang chính (dùng JS)
    echo "<script>window.location.href='?page=admin&section=categories';</script>";
    exit();
}
?>
