<?php

require_once __DIR__ . '/../models/TableStatusManager.php';
require_once __DIR__ . '/../../includes/BaseController.php';
require_once __DIR__ . '/AuthController.php';

class TableStatusController extends BaseController {
    private $tableStatusModel;
    private $authController;

    public function __construct() {
        // Khởi tạo Model và Auth Controller
        $this->tableStatusModel = new TableStatusManager();
        $this->authController = new AuthController();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Hiển thị trang quản lý trạng thái bàn cho nhân viên
     */
    public function index() {
        // Kiểm tra quyền nhân viên
        $this->authController->requireNhanVien();
        
        // Lấy thông tin nhân viên từ session
        $nhanVien = $_SESSION['user'];
        $maCoSo = $nhanVien['MaCoSo'];
        
        // Lấy thông tin cơ sở của nhân viên
        $thongTinCoSo = $this->tableStatusModel->layThongTinCoSo($maCoSo);
        
        // Cleanup các đơn đặt bàn quá hạn trước khi hiển thị trạng thái
        $cleanupResult = TableStatusManager::xoaDonDatBanQuaHan($maCoSo);
        if ($cleanupResult['success'] && $cleanupResult['deleted_count'] > 0) {
            $_SESSION['info_message'] = $cleanupResult['message'];
        }
        
        // Lấy danh sách bàn với trạng thái (tự động dựa vào thời gian)
        $banList = $this->tableStatusModel->layBanTheoCoSo($maCoSo);
        
        // Tính toán thống kê
        $thongKe = $this->tinhThongKe($banList);
        
        // Truyền dữ liệu tới view
        $data = [
            'nhanVien' => $nhanVien,
            'thongTinCoSo' => $thongTinCoSo,
            'banList' => $banList,
            'thongKe' => $thongKe
        ];
        $this->loadView('nhanvien/table_status', $data);
    }

    /**
     * Lấy dữ liệu table status để sử dụng trong dashboard
     * @return array
     */
    public function getTableStatusData() {
        // Kiểm tra quyền nhân viên
        $this->authController->requireNhanVien();
        
        // Lấy thông tin nhân viên từ session
        $nhanVien = $_SESSION['user'];
        $maCoSo = $nhanVien['MaCoSo'];
        
        // Lấy thông tin cơ sở của nhân viên
        $thongTinCoSo = $this->tableStatusModel->layThongTinCoSo($maCoSo);
        
        // Lấy danh sách bàn với trạng thái (tự động dựa vào thời gian)
        $banList = $this->tableStatusModel->layBanTheoCoSo($maCoSo);
        
        // Tính toán thống kê
        $thongKe = $this->tinhThongKe($banList);
        
        return [
            'nhanVien' => $nhanVien,
            'thongTinCoSo' => $thongTinCoSo,
            'banList' => $banList,
            'thongKe' => $thongKe
        ];
    }

    /**
     * Cập nhật trạng thái bàn
     */
    public function updateStatus() {
        // Kiểm tra quyền nhân viên
        $this->authController->requireNhanVien();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=nhanvien&action=dashboard&section=table_status');
            return;
        }
        
        // Lấy thông tin nhân viên từ session
        $nhanVien = $_SESSION['user'];
        $maCoSoNhanVien = $nhanVien['MaCoSo'];
        
        // Lấy dữ liệu từ form
        $maBan = (int)($_POST['maBan'] ?? 0);
        $trangThai = $_POST['trangThai'] ?? '';
        
        // Validate dữ liệu
        if ($maBan <= 0 || empty($trangThai)) {
            $_SESSION['error_message'] = 'Dữ liệu không hợp lệ!';
            $this->redirectBack();
            return;
        }
        
        // Kiểm tra bàn có thuộc cơ sở của nhân viên không
        $banInfo = $this->tableStatusModel->layThongTinBan($maBan);
        if (!$banInfo || $banInfo['MaCoSo'] != $maCoSoNhanVien) {
            $_SESSION['error_message'] = 'Bạn không có quyền thao tác với bàn này!';
            $this->redirectBack();
            return;
        }
        
        // Thực hiện cập nhật
        $result = $this->tableStatusModel->capNhatTrangThaiBan($maBan, $trangThai);
        
        if ($result) {
            $_SESSION['success_message'] = 'Cập nhật trạng thái bàn thành công!';
        } else {
            $_SESSION['error_message'] = 'Có lỗi xảy ra khi cập nhật trạng thái bàn!';
        }
        
        // Redirect về trang quản lý
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=table_status');
    }

    /**
     * Lấy chi tiết bàn (AJAX)
     */
    public function getTableDetails() {
        // Kiểm tra quyền nhân viên
        $this->authController->requireNhanVien();
        
        $maBan = (int)($_GET['maBan'] ?? 0);
        
        if ($maBan <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Mã bàn không hợp lệ']);
            return;
        }
        
        // Lấy thông tin nhân viên từ session
        $nhanVien = $_SESSION['user'];
        $maCoSoNhanVien = $nhanVien['MaCoSo'];
        
        // Lấy thông tin bàn
        $banInfo = $this->tableStatusModel->layThongTinBanChiTiet($maBan);
        
        // Kiểm tra quyền truy cập
        if (!$banInfo || $banInfo['MaCoSo'] != $maCoSoNhanVien) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền xem thông tin bàn này']);
            return;
        }
        
        header('Content-Type: application/json');
        echo json_encode($banInfo);
    }

    /**
     * Tính toán thống kê bàn
     */
    private function tinhThongKe($banList) {
        $tongBan = count($banList);
        $banTrong = count(array_filter($banList, function($ban) { 
            return $ban['TrangThai'] == 'trong'; 
        }));
        $banDaDat = $tongBan - $banTrong;
        
        return [
            'tongBan' => $tongBan,
            'banTrong' => $banTrong,
            'banDaDat' => $banDaDat
        ];
    }

    /**
     * Redirect về trang trước đó
     */
    private function redirectBack() {
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=table_status');
    }

    /**
     * Load view với dữ liệu
     */
    private function loadView($viewPath, $data = []) {
        // Extract dữ liệu để sử dụng trong view
        extract($data);
        
        // Include view file
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: " . $viewFile);
        }
    }
}