<?php
include dirname(__DIR__,4) . "/config/connect.php";

if(
    !empty($_POST['TenMonAn']) &&
    !empty($_POST['AnhMonAn']) &&
    // !empty($_POST['MoTaMonAn']) &&
    !empty($_POST['MaDanhMuc'])){
        
        $mamon = $_GET['MaMon'];
        $tenmon = $_POST['TenMonAn'];
        $anhmmon = $_POST['AnhMonAn'];
        $mota = $_POST['MoTaMonAn'] ?? '';
        $madm = $_POST['MaDanhMuc'];

        $sql = "UPDATE `monan` SET `TenMon`='$tenmon', `HinhAnhURL`='$anhmmon', `MoTa`='$mota', `MaDM`='$madm' WHERE `MaMon`='$mamon'";
        
        if(mysqli_query($conn, $sql)){
            echo "<script>window.location.href='?page=admin&section=menu';</script>";
            exit();
        } else {
            echo "Lỗi cập nhật: " . mysqli_error($conn);
        }
    }
    else{
        echo "Vui lòng nhập đầy đủ thông tin!";
    }
?>