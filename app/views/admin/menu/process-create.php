<?php
include dirname(__DIR__,4) . "/config/connect.php";
?>

<?php
    if(!empty($_POST['MaDanhMuc'])&&
    !empty($_POST['TenMonAn'])&&
    !empty($_POST['AnhMonAn'])&&
    !empty($_POST['MoTaMonAn'])){
        $danhmuc = $_POST['MaDanhMuc'];
        $tenmon = $_POST['TenMonAn'];
        $anhmon = $_POST['AnhMonAn'];
        $mota = $_POST['MoTaMonAn'];

        $sql = "INSERT INTO `monan`(`MaDM`, `TenMon`, `MoTa`, `HinhAnhURL`) VALUES ('$danhmuc','$tenmon','$mota','$anhmon')";

        mysqli_query($conn, $sql);
        echo "<script>window.location.href='?page=admin&section=menu';</script>";
        exit();
    }
    else{
        echo "Vui lòng nhập đầy đủ thông tin";
    }
    
    

?>