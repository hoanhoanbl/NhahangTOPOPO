<?php

require_once __DIR__ . '/../../config/database.php'; 
require_once __DIR__ . '/../models/NhanVienModel.php'; 
require_once __DIR__ . '/../models/BookingModel.php'; 
require_once __DIR__ . '/../models/BranchModel.php'; 
require_once __DIR__ . '/../models/MenuModel.php'; 
require_once __DIR__ . '/../models/TableStatusManager.php'; 
require_once __DIR__ . '/../../includes/BaseController.php'; 
require_once __DIR__ . '/AuthController.php'; 

class NhanVienController extends BaseController 
{
    private $nhanVienModel;
    private $bookingModel;
    private $branchModel;
    private $menuModel;
    private $tableStatusManager;
    private $authController;
    private $db;

    public function __construct() {
        // Khởi tạo kết nối DB và các Model
        $database = new Database();
        $this->db = $database->getConnection();
        $this->nhanVienModel = new NhanVienModel($this->db);
        $this->bookingModel = new BookingModel($this->db);
        $this->branchModel = new BranchModel($this->db);
        $this->menuModel = new MenuModel($this->db);
        $this->tableStatusManager = new TableStatusManager($this->db);
        $this->authController = new AuthController();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hiển thị dashboard cho nhân viên
    public function dashboard()
    {
        // Kiểm tra quyền truy cập - chỉ cho phép nhân viên
        $this->authController->requireNhanVien();
        
        $currentUser = $_SESSION['user'];
        $maCoSo = $currentUser['MaCoSo'];
        
        // Lấy thống kê dashboard
        $dashboardData = $this->getDashboardStatistics($maCoSo);

        
        // Xử lý section hiển thị
        $section = $_GET['section'] ?? 'overview';
        error_log("run Section: " . $section);
        
        switch ($section) {
            case 'dashboard':
                $cleanupResult = TableStatusManager::xoaDonDatBanQuaHan($maCoSo);
            case 'bookings':
                $cleanupResult = TableStatusManager::xoaDonDatBanQuaHan($maCoSo);
                if ($cleanupResult['success'] && $cleanupResult['deleted_count'] > 0) {
                    $_SESSION['info_message'] = $cleanupResult['message'];
                }
                $bookingsData = $this->getBookingsList($maCoSo);
                break;
            
            case 'create_bill':
                $createBillResult = TableStatusManager::xoaDonDatBanQuaHan($maCoSo);
            case 'profile':
                $profileData = $this->getProfileData($currentUser['MaNV']);
                break;
            case 'table_status':
                require_once __DIR__ . '/TableStatusController.php';
                $tableStatusController = new TableStatusController();
                $cleanupResult = TableStatusManager::xoaDonDatBanQuaHan($maCoSo);
                $tableStatusData = $tableStatusController->getTableStatusData();
                break;
            default:
                $section = 'dashboard';
                break;
        }
        
        // Truyền dữ liệu cho view
        include __DIR__ . '/../views/nhanvien/dashboard.php';
        exit;
    }

    // Hiển thị profile nhân viên
    public function profile()
    {
        $this->authController->requireNhanVien();
        
        $currentUser = $_SESSION['user'];
        
        // Lấy thông tin chi tiết nhân viên
        if ($this->nhanVienModel->getById($currentUser['MaNV'])) {
            $nhanVienData = $this->nhanVienModel->toArray();
        } else {
            $_SESSION['error_message'] = 'Không tìm thấy thông tin nhân viên.';
            $this->redirect('index.php?page=nhanvien&action=dashboard&section=dashboard');
            return;
        }

        include __DIR__ . '/../views/nhanvien/profile.php';
        exit;
    }



    // Cập nhật trạng thái đơn đặt bàn
    public function updateBookingStatus()
{
    $this->authController->requireNhanVien();
    
    // 1. Validation (Giữ nguyên, đây là nhiệm vụ của Controller)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error_message'] = 'Phương thức không hợp lệ.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }
    
    $maDon = $_POST['maDon'] ?? '';
    $status = $_POST['status'] ?? '';
    $reason = $_POST['reason'] ?? '';

    if (empty($maDon) || empty($status)) {
        $_SESSION['error_message'] = 'Thiếu thông tin cần thiết.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    $validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'da_huy', 'hoan_thanh'];
    if (!in_array($status, $validStatuses)) {
        $_SESSION['error_message'] = 'Trạng thái không hợp lệ.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    // 2. Tương tác với Model (Gọn gàng hơn rất nhiều)
    try {
        $currentUser = $_SESSION['user'];
        
        // Gọi phương thức duy nhất trong Model
        $affectedRows = $this->bookingModel->updateStatus(
            $maDon,
            $currentUser['MaCoSo'],
            $status,
            $currentUser['MaNV'],
            $reason
        );

        // 3. Xử lý kết quả trả về từ Model
        if ($affectedRows > 0) {
            // Cập nhật thành công (affectedRows = 1)
            $_SESSION['success_message'] = "Cập nhật trạng thái đơn #{$maDon} thành công!";
        } else if ($affectedRows === 0) {
            // Không có dòng nào được cập nhật => không tìm thấy đơn hoặc không có quyền
            $_SESSION['error_message'] = 'Không tìm thấy đơn đặt bàn hoặc bạn không có quyền cập nhật.';
        } else {
            // $affectedRows là false, có lỗi exception xảy ra ở Model
            $_SESSION['error_message'] = 'Có lỗi hệ thống xảy ra khi cập nhật trạng thái.';
        }

    } catch (Exception $e) {
        error_log("Error in NhanVienController::updateBookingStatus: " . $e->getMessage());
        $_SESSION['error_message'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
    }

    // 4. Redirect (Giữ nguyên)
    $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
}

    // Xem chi tiết đơn đặt bàn

public function viewBookingDetail()
{
    $this->authController->requireNhanVien();
    
    // 1. Validation & Lấy thông tin đầu vào
    $maDon = $_GET['id'] ?? '';
    if (empty($maDon)) {
        $_SESSION['error_message'] = 'Mã đơn đặt bàn không hợp lệ.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    try {
        $currentUser = $_SESSION['user'];
        
        // 2. Gọi Model để lấy dữ liệu (KHÔNG CÒN SQL Ở ĐÂY)
        // Lấy thông tin chính của đơn đặt bàn
        $booking = $this->bookingModel->getBookingDetail($maDon, $currentUser['MaCoSo']);
        
        // 3. Kiểm tra kết quả và quyền truy cập
        if (!$booking) {
            $_SESSION['error_message'] = 'Không tìm thấy đơn đặt bàn hoặc bạn không có quyền xem.';
            $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
            return;
        }

        // Lấy danh sách món ăn liên quan
        $menuItems = $this->bookingModel->getMenuItemsForBooking($maDon, $currentUser['MaCoSo']);

    } catch (Exception $e) {
        error_log("Error loading booking detail in Controller: " . $e->getMessage());
        $_SESSION['error_message'] = 'Có lỗi hệ thống xảy ra khi tải thông tin đơn đặt bàn.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    // 4. Truyền dữ liệu cho View để hiển thị
    include __DIR__ . '/../views/nhanvien/booking_detail.php';
    exit;
}




    // Lấy thống kê dashboard
    private function getDashboardStatistics($maCoSo)
    {
        try {
            // Lấy thông tin cơ sở
            $coSoInfo = $this->branchModel->getById($maCoSo);
            
            // Lấy các thống kê booking
            $totalBooking = $this->bookingModel->countBookingsByBranch($maCoSo);
            $todayNewBookings = $this->bookingModel->countTodayBookingsByBranch($maCoSo);
            $completedBookings = $this->bookingModel->countCompletedBookingsByBranch($maCoSo);
            $pendingBookings = $this->bookingModel->countPendingBookingsByBranch($maCoSo);
            $confirmedBookings = $this->bookingModel->countConfirmedBookingsByBranch($maCoSo);
            $upcomingBookings = $this->bookingModel->countUpcomingBookingsByBranch($maCoSo);
            
            return [
                'coSoInfo' => $coSoInfo,
                'totalBooking' => $totalBooking,
                'todayNewBookings' => $todayNewBookings,
                'completedBookings' => $completedBookings,
                'pendingBookings' => $pendingBookings,
                'confirmedBookings' => $confirmedBookings,
                'upcomingBookings' => $upcomingBookings
            ];
        } catch (Exception $e) {
            error_log("Error getting dashboard statistics: " . $e->getMessage());
            return [
                'coSoInfo' => 'hello',
                'totalBooking' => 0,
                'todayNewBookings' => 0,
                'completedBookings' => 0,
                'pendingBookings' => 0,
                'confirmedBookings' => 0
            ];
        }
    }

    // Lấy danh sách đơn đặt bàn với phân trang và lọc
    private function getBookingsList($maCoSo)
    {
        try {
            // Phân trang
            $page = isset($_GET['booking_page']) ? (int)$_GET['booking_page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            // Các filter
            $statusFilter = $_GET['status_filter'] ?? 'all';
            $timeFilter = $_GET['time_filter'] ?? 'hom_nay';
            $searchKeyword = $_GET['search'] ?? '';
            
            // Lấy danh sách booking
            $bookings = $this->bookingModel->getBookingsByBranch(
                $maCoSo, 
                $limit, 
                $offset, 
                $statusFilter, 
                $timeFilter, 
                $searchKeyword
            );
            
            // Đếm tổng số
            $totalBookings = $this->bookingModel->countBookingsByBranchWithFilter(
                $maCoSo, 
                $statusFilter, 
                $timeFilter, 
                $searchKeyword
            );
            
            $totalPages = ceil($totalBookings / $limit);
            
            return [
                'bookingsList' => $bookings,
                'totalBookings' => $totalBookings,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit
            ];
        } catch (Exception $e) {
            error_log("Error getting bookings list: " . $e->getMessage());
            return [
                'bookingsList' => [],
                'totalBookings' => 0,
                'totalPages' => 0,
                'currentPage' => 1,
                'limit' => 10
            ];
        }
    }

    // Lấy thông tin profile nhân viên
    private function getProfileData($maNV)
    {
        try {
            $nhanVien = $this->nhanVienModel->getById($maNV);
            if ($nhanVien) {
                return $this->nhanVienModel->toArray();
            }
            return null;
        } catch (Exception $e) {
            error_log("Error getting profile data: " . $e->getMessage());
            return null;
        }
    }

// Tìm kiếm món ăn trong menu
    public function searchMenu()
    {
        // Kiểm tra quyền truy cập
        $this->authController->requireNhanVien();
        
        // Đảm bảo đây là AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['error' => 'Chỉ chấp nhận AJAX request']);
            return;
        }
        
        try {
            $currentUser = $_SESSION['user'];
            $maCoSo = $currentUser['MaCoSo'];
            
            // Lấy tham số tìm kiếm
            $tenMon = $_GET['tenMon'] ?? '';
            
            // Validate input
            $tenMon = trim($tenMon);
            if (strlen($tenMon) > 100) {
                throw new Exception('Tên món ăn quá dài');
            }
            
            // Tìm kiếm món ăn
            $menuItems = $this->menuModel->searchMenuItems($maCoSo, $tenMon);
            
            // Chuẩn bị response
            $response = [
                'success' => true,
                'data' => [
                    'items' => $menuItems,
                ],
                'query' => [
                    'tenMon' => $tenMon,
                    'maCoSo' => $maCoSo
                ]
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit; // Quan trọng: dừng execution sau khi trả về JSON
            
        } catch (Exception $e) {
            error_log("Error in searchMenu: ------------4----------" . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi tìm kiếm: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit; // Quan trọng: dừng execution sau khi trả về JSON
        }
    }

// Tạo đơn tại quán
public function createOrder()
{
    // Kiểm tra quyền truy cập
    $this->authController->requireNhanVien();

    // Kiểm tra phương thức POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error'   => 'Chỉ chấp nhận phương thức POST'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Đảm bảo đây là AJAX request
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'Chỉ chấp nhận AJAX request'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        // 1. Lấy dữ liệu JSON từ request body
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        if (!$data || empty($data['cartItems'])) {
            throw new Exception('Dữ liệu không hợp lệ hoặc giỏ hàng trống');
        }

        $cartItems    = $data['cartItems'];
        $customerInfo = $data['customerInfo'] ?? [];
        $bookingInfo  = $data['bookingInfo'] ?? [];

        // 2. Dùng giá trị mặc định nếu không có thông tin khách hàng
        $customerName     = $customerInfo['name'];
        $customerPhone    = $customerInfo['phone'];
        $customerEmail    = $customerInfo['email'];
        $notes            = $customerInfo['notes'];
        
        // 3. Thông tin đặt bàn
        $bookingDate      = $bookingInfo['date'] ?? '';
        $bookingTime      = $bookingInfo['time'] ?? '';
        $numberOfGuests   = $bookingInfo['guests'] ?? 1;
        $selectedTables   = $bookingInfo['selectedTables'] ?? [];

        $currentUser = $_SESSION['user'];

        // 4. Gọi Model để xử lý nghiệp vụ
        // $khachHangModel = new KhachHangModel($this->db);
        $maDon = $this->bookingModel->createBookingWithTables(
            $customerName,
            $customerPhone,
            $customerEmail,
            $currentUser['MaCoSo'],
            $currentUser['MaNV'],
            $cartItems,
            $notes,
            $bookingDate,
            $bookingTime,
            $numberOfGuests,
            $selectedTables
        );

        // 4. Trả về response
        if ($maDon) {
            http_response_code(201); // 201 Created
            echo json_encode([
                'success' => true,
                'data'    => [
                    'maDon'   => $maDon,
                    'message' => 'Tạo đơn hàng thành công!'
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('Không thể tạo đơn hàng. Vui lòng thử lại.');
        }
    } catch (Exception $e) {
        // Xử lý lỗi phát sinh
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

    /**
     * Quản lý trạng thái bàn cho nhân viên
     */
    public function table_status()
    {
        // Sử dụng TableStatusController để xử lý
        require_once __DIR__ . '/TableStatusController.php';
        $tableStatusController = new TableStatusController();
        $tableStatusController->index();
    }

    /**
     * Cập nhật trạng thái bàn
     */
    public function update_table_status()
    {
        // Sử dụng TableStatusController để xử lý
        require_once __DIR__ . '/TableStatusController.php';
        $tableStatusController = new TableStatusController();
        $tableStatusController->updateStatus();
    }

    /**
     * Lấy chi tiết bàn (AJAX)
     */
    public function get_table_details()
    {
        // Sử dụng TableStatusController để xử lý
        require_once __DIR__ . '/TableStatusController.php';
        $tableStatusController = new TableStatusController();
        $tableStatusController->getTableDetails();
    }

    /**
     * Lấy danh sách bàn trống theo logic mới (AJAX)
     */
    public function getAvailableTables()
    {
        header('Content-Type: application/json');
        
        try {
            // Kiểm tra quyền truy cập
            $this->authController->requireNhanVien();
            
            $currentUser = $_SESSION['user'];
            $maCoSo = $currentUser['MaCoSo'];
            
            // Lấy danh sách bàn trống theo logic mới (không có đơn đặt trong vòng 2 giờ tới)
            $availableTables = TableStatusManager::layBanTrongTheoThoiGian($maCoSo);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'tables' => $availableTables,
                    'total' => count($availableTables),
                    'params' => [
                        'maCoSo' => $maCoSo
                    ]
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error in getAvailableTables: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi lấy danh sách bàn: ' . $e->getMessage()
            ]);
        }
    }

}