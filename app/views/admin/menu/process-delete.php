<?php
include dirname(__DIR__,4) . "/config/connect.php";

if(isset($_GET['MaMon'])){
    $id = $_GET['MaMon'];

    // Xóa món ăn khỏi menu_coso trước, sau đó xóa khỏi monan
    $sql = "DELETE FROM menu_coso WHERE MaMon = '$id'; DELETE FROM `monan` WHERE MaMon = '$id'";
    mysqli_multi_query($conn, $sql);
    echo "<script>window.location.href='?page=admin&section=menu';</script>";
    exit();
}
else{
    echo "Vui lòng nhập đầy đủ thông tin";
}
?>