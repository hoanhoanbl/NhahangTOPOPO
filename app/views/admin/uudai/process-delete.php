<?php
include "connect.php"; // Kết nối CSDL

if (isset($_GET['MaUD'])) {
    $maUuDai = $_GET['MaUD'];

    $sql = "DELETE FROM uudai WHERE MaUD = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maUuDai);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa ưu đãi thành công!'); window.location.href='?page=admin&section=uudai&status=delete_success';</script>";
    } else {
        echo "<script>alert('Lỗi: Không thể xóa ưu đãi!'); window.location.href='?page=admin&section=uudai&status=delete_failed';</script>";
    }
    $stmt->close();
} else {
    echo "<script>window.location.href='?page=admin&section=uudai';</script>";
}
exit();
?>