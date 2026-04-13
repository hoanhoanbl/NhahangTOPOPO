<?php

require_once __DIR__ . '/../models/TableStatusManager.php';

class AdminTableStatusController extends BaseController 
{
    public function index() 
    {
        // Lấy danh sách cơ sở
        $listCoSo = TableStatusManager::layDanhSachCoSo();
        
        // Lấy tham số từ URL
        $maCoSo = isset($_GET['maCoSo']) ? (int)$_GET['maCoSo'] : 0;
        $thoiGianBatDau = isset($_GET['thoiGianBatDau']) ? $_GET['thoiGianBatDau'] : date('Y-m-d H:i');
        $thoiGianKetThuc = isset($_GET['thoiGianKetThuc']) ? $_GET['thoiGianKetThuc'] : date('Y-m-d H:i', strtotime('+2 hours'));
        
        $banList = [];
        $tenCoSo = '';
        
        if ($maCoSo > 0) {
            // Lấy thông tin cơ sở
            global $conn;
            $sql = "SELECT TenCoSo FROM coso WHERE MaCoSo = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $coSo = mysqli_fetch_assoc($result);
            $tenCoSo = $coSo['TenCoSo'] ?? '';
            
            // Lấy danh sách bàn với trạng thái
            $banList = TableStatusManager::layBanTheoCoSo($maCoSo, $thoiGianBatDau, $thoiGianKetThuc);
        }
        
        $this->render('admin/table/status', [
            'listCoSo' => $listCoSo,
            'maCoSo' => $maCoSo,
            'tenCoSo' => $tenCoSo,
            'thoiGianBatDau' => $thoiGianBatDau,
            'thoiGianKetThuc' => $thoiGianKetThuc,
            'banList' => $banList
        ]);
    }
    
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maBan = (int)$_POST['maBan'];
            $thoiGianBatDau = $_POST['thoiGianBatDau'];
            $thoiGianKetThuc = $_POST['thoiGianKetThuc'];
            $trangThai = $_POST['trangThai'];
            
            $result = TableStatusManager::capNhatTrangThaiBan($maBan, $thoiGianBatDau, $thoiGianKetThuc, $trangThai);
            
            if ($result) {
                $_SESSION['success_message'] = 'Cập nhật trạng thái bàn thành công!';
            } else {
                $_SESSION['error_message'] = 'Có lỗi xảy ra khi cập nhật trạng thái bàn!';
            }
            
            // Redirect về trang quản lý trạng thái bàn
            $redirectUrl = "?page=admin&section=table&action=status&maCoSo=" . $_POST['maCoSo'] . 
                          "&thoiGianBatDau=" . urlencode($thoiGianBatDau) . 
                          "&thoiGianKetThuc=" . urlencode($thoiGianKetThuc);
            header("Location: $redirectUrl");
            exit;
        }
    }
}
?>
