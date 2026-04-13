<?php
include "connect.php";

// Kiểm tra xem có MaDM được truyền qua GET không
if (isset($_GET['MaDM'])) {
    $maDM = $_GET['MaDM'];
    
    // Kiểm tra xem danh mục có món ăn không
    $checkMenuSql = "SELECT COUNT(*) as menu_count FROM `monan` WHERE `MaDM` = '$maDM'";
    $checkResult = mysqli_query($conn, $checkMenuSql);
    $menuCount = mysqli_fetch_assoc($checkResult)['menu_count'];
    
    if ($menuCount > 0) {
        // Có món ăn trong danh mục - hỏi xác nhận
        echo "<script>
            if (confirm('Danh mục này có {$menuCount} món ăn. Bạn có muốn xóa danh mục và chuyển tất cả món ăn về \"Không có danh mục\"?')) {
                window.location.href = '?page=admin&section=categories&action=delete&MaDM={$maDM}&force=true';
            } else {
                window.location.href = '?page=admin&section=categories';
            }
        </script>";
        exit();
    }
    
    // Nếu có tham số force=true, thực hiện xóa và cập nhật món ăn
    if (isset($_GET['force']) && $_GET['force'] === 'true') {
        // Cập nhật tất cả món ăn của danh mục này về NULL hoặc danh mục mặc định
        $updateMenuSql = "UPDATE `monan` SET `MaDM` = NULL WHERE `MaDM` = '$maDM'";
        mysqli_query($conn, $updateMenuSql);
    }
    
    // Thực hiện xóa danh mục
    $sql = "DELETE FROM `danhmuc` WHERE `MaDM` = '$maDM'";
    
    if (mysqli_query($conn, $sql)) {
        $affectedRows = mysqli_affected_rows($conn);
        if ($affectedRows > 0) {
            echo "<script>alert('Xóa danh mục thành công!'); window.location.href = '?page=admin&section=categories';</script>";
        } else {
            echo "<script>alert('Không tìm thấy danh mục cần xóa!'); window.location.href = '?page=admin&section=categories';</script>";
        }
    } else {
        echo "<script>alert('Có lỗi xảy ra: " . mysqli_error($conn) . "'); window.location.href = '?page=admin&section=categories';</script>";
    }
} else {
    echo "<script>alert('Không tìm thấy thông tin danh mục cần xóa!'); window.location.href = '?page=admin&section=categories';</script>";
}
?>
