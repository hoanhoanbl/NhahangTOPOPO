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
        // Khá»Ÿi táº¡o káº¿t ná»‘i DB vÃ  cÃ¡c Model
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

    // Hiá»ƒn thá»‹ dashboard cho nhÃ¢n viÃªn
    public function dashboard()
    {
        // Kiá»ƒm tra quyá»n truy cáº­p - chá»‰ cho phÃ©p nhÃ¢n viÃªn
        $this->authController->requireNhanVien();
        
        $currentUser = $_SESSION['user'];
        $maCoSo = $currentUser['MaCoSo'];
        
        // Láº¥y thá»‘ng kÃª dashboard
        $dashboardData = $this->getDashboardStatistics($maCoSo);

        
        // Xá»­ lÃ½ section hiá»ƒn thá»‹
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
        
        // Truyá»n dá»¯ liá»‡u cho view
        include __DIR__ . '/../views/nhanvien/dashboard.php';
        exit;
    }

    // Hiá»ƒn thá»‹ profile nhÃ¢n viÃªn
    public function profile()
    {
        $this->authController->requireNhanVien();
        
        $currentUser = $_SESSION['user'];
        
        // Láº¥y thÃ´ng tin chi tiáº¿t nhÃ¢n viÃªn
        if ($this->nhanVienModel->getById($currentUser['MaNV'])) {
            $nhanVienData = $this->nhanVienModel->toArray();
        } else {
            $_SESSION['error_message'] = 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin nhÃ¢n viÃªn.';
            $this->redirect('index.php?page=nhanvien&action=dashboard&section=dashboard');
            return;
        }

        include __DIR__ . '/../views/nhanvien/profile.php';
        exit;
    }



    // Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n Ä‘áº·t bÃ n
    public function updateBookingStatus()
{
    $this->authController->requireNhanVien();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error_message'] = 'Phuong thuc khong hop le.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    $maDon = (int)($_POST['maDon'] ?? 0);
    $status = trim((string)($_POST['status'] ?? ''));
    $reason = trim((string)($_POST['reason'] ?? ''));
    $redirectToDetail = (int)($_POST['redirect_to_detail'] ?? 0);

    if ($maDon <= 0 || $status === '') {
        $_SESSION['error_message'] = 'Thieu thong tin can thiet.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    $validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'da_huy', 'hoan_thanh'];
    if (!in_array($status, $validStatuses, true)) {
        $_SESSION['error_message'] = 'Trang thai khong hop le.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    try {
        $currentUser = $_SESSION['user'];
        $result = $this->bookingModel->changeStatus(
            $maDon,
            (int)$currentUser['MaCoSo'],
            $status,
            [
                'actor_type' => 'staff',
                'actor_id' => $currentUser['MaNV'] ?? null,
                'actor_name' => $currentUser['TenNhanVien'] ?? ($currentUser['HoTen'] ?? ($currentUser['TenDangNhap'] ?? 'Nhan vien')),
                'note' => $reason,
                'require_reason' => ($status === 'da_huy'),
                'source' => 'nhanvien_booking_status',
            ]
        );

        if (!empty($result['success'])) {
            $_SESSION['success_message'] = $result['message'] ?? "Cap nhat trang thai don #{$maDon} thanh cong!";
        } else {
            $_SESSION['error_message'] = $result['message'] ?? 'Khong the cap nhat trang thai don dat ban.';
        }
    } catch (Exception $e) {
        error_log('Error in NhanVienController::updateBookingStatus: ' . $e->getMessage());
        $_SESSION['error_message'] = 'Co loi xay ra. Vui long thu lai.';
    }

    if ($redirectToDetail > 0) {
        $this->redirect('index.php?page=nhanvien&action=viewBookingDetail&id=' . $redirectToDetail);
        return;
    }

    $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
}

    // Xem chi tiet don dat ban
public function viewBookingDetail()
{
    $this->authController->requireNhanVien();

    $maDon = (int)($_GET['id'] ?? 0);
    if ($maDon <= 0) {
        $_SESSION['error_message'] = 'Ma don dat ban khong hop le.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    try {
        $currentUser = $_SESSION['user'];
        $booking = $this->bookingModel->getBookingDetail($maDon, $currentUser['MaCoSo']);

        if (!$booking) {
            $_SESSION['error_message'] = 'Khong tim thay don dat ban hoac ban khong co quyen xem.';
            $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
            return;
        }

        $menuItems = $this->bookingModel->getMenuItemsForBooking($maDon, $currentUser['MaCoSo']);
        $bookingTimeline = $this->bookingModel->getBookingTimeline($maDon, $currentUser['MaCoSo']);
    } catch (Exception $e) {
        error_log('Error loading booking detail in Controller: ' . $e->getMessage());
        $_SESSION['error_message'] = 'Co loi he thong xay ra khi tai thong tin don dat ban.';
        $this->redirect('index.php?page=nhanvien&action=dashboard&section=bookings');
        return;
    }

    include __DIR__ . '/../views/nhanvien/booking_detail.php';
    exit;
}


private function getDashboardStatistics($maCoSo)
    {
        try {
            // Láº¥y thÃ´ng tin cÆ¡ sá»Ÿ
            $coSoInfo = $this->branchModel->getById($maCoSo);
            
            // Láº¥y cÃ¡c thá»‘ng kÃª booking
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

    // Láº¥y danh sÃ¡ch Ä‘Æ¡n Ä‘áº·t bÃ n vá»›i phÃ¢n trang vÃ  lá»c
    private function getBookingsList($maCoSo)
    {
        try {
            // PhÃ¢n trang
            $page = isset($_GET['booking_page']) ? (int)$_GET['booking_page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            // CÃ¡c filter
            $statusFilter = $_GET['status_filter'] ?? 'all';
            $timeFilter = $_GET['time_filter'] ?? 'hom_nay';
            $searchKeyword = $_GET['search'] ?? '';
            
            // Láº¥y danh sÃ¡ch booking
            $bookings = $this->bookingModel->getBookingsByBranch(
                $maCoSo, 
                $limit, 
                $offset, 
                $statusFilter, 
                $timeFilter, 
                $searchKeyword
            );
            
            // Äáº¿m tá»•ng sá»‘
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

    // Láº¥y thÃ´ng tin profile nhÃ¢n viÃªn
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

// TÃ¬m kiáº¿m mÃ³n Äƒn trong menu
    public function searchMenu()
    {
        // Kiá»ƒm tra quyá»n truy cáº­p
        $this->authController->requireNhanVien();
        
        // Äáº£m báº£o Ä‘Ã¢y lÃ  AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['error' => 'Chá»‰ cháº¥p nháº­n AJAX request']);
            return;
        }
        
        try {
            $currentUser = $_SESSION['user'];
            $maCoSo = $currentUser['MaCoSo'];
            
            // Láº¥y tham sá»‘ tÃ¬m kiáº¿m
            $tenMon = $_GET['tenMon'] ?? '';
            
            // Validate input
            $tenMon = trim($tenMon);
            if (strlen($tenMon) > 100) {
                throw new Exception('TÃªn mÃ³n Äƒn quÃ¡ dÃ i');
            }
            
            // TÃ¬m kiáº¿m mÃ³n Äƒn
            $menuItems = $this->menuModel->searchMenuItems($maCoSo, $tenMon);
            
            // Chuáº©n bá»‹ response
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
            exit; // Quan trá»ng: dá»«ng execution sau khi tráº£ vá» JSON
            
        } catch (Exception $e) {
            error_log("Error in searchMenu: ------------4----------" . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'CÃ³ lá»—i xáº£y ra khi tÃ¬m kiáº¿m: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit; // Quan trá»ng: dá»«ng execution sau khi tráº£ vá» JSON
        }
    }

// Táº¡o Ä‘Æ¡n táº¡i quÃ¡n
public function createOrder()
{
    // Kiá»ƒm tra quyá»n truy cáº­p
    $this->authController->requireNhanVien();

    // Kiá»ƒm tra phÆ°Æ¡ng thá»©c POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error'   => 'Chá»‰ cháº¥p nháº­n phÆ°Æ¡ng thá»©c POST'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Äáº£m báº£o Ä‘Ã¢y lÃ  AJAX request
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'Chá»‰ cháº¥p nháº­n AJAX request'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        // 1. Láº¥y dá»¯ liá»‡u JSON tá»« request body
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        if (!$data || empty($data['cartItems'])) {
            throw new Exception('Dá»¯ liá»‡u khÃ´ng há»£p lá»‡ hoáº·c giá» hÃ ng trá»‘ng');
        }

        $cartItems    = $data['cartItems'];
        $customerInfo = $data['customerInfo'] ?? [];
        $bookingInfo  = $data['bookingInfo'] ?? [];

        // 2. DÃ¹ng giÃ¡ trá»‹ máº·c Ä‘á»‹nh náº¿u khÃ´ng cÃ³ thÃ´ng tin khÃ¡ch hÃ ng
        $customerName     = $customerInfo['name'];
        $customerPhone    = $customerInfo['phone'];
        $customerEmail    = $customerInfo['email'];
        $notes            = $customerInfo['notes'];
        
        // 3. ThÃ´ng tin Ä‘áº·t bÃ n
        $bookingDate      = $bookingInfo['date'] ?? '';
        $bookingTime      = $bookingInfo['time'] ?? '';
        $numberOfGuests   = $bookingInfo['guests'] ?? 1;
        $selectedTables   = $bookingInfo['selectedTables'] ?? [];

        $currentUser = $_SESSION['user'];

        // 4. Gá»i Model Ä‘á»ƒ xá»­ lÃ½ nghiá»‡p vá»¥
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

        // 4. Tráº£ vá» response
        if ($maDon) {
            http_response_code(201); // 201 Created
            echo json_encode([
                'success' => true,
                'data'    => [
                    'maDon'   => $maDon,
                    'message' => 'Táº¡o Ä‘Æ¡n hÃ ng thÃ nh cÃ´ng!'
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('KhÃ´ng thá»ƒ táº¡o Ä‘Æ¡n hÃ ng. Vui lÃ²ng thá»­ láº¡i.');
        }
    } catch (Exception $e) {
        // Xá»­ lÃ½ lá»—i phÃ¡t sinh
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

    /**
     * Quáº£n lÃ½ tráº¡ng thÃ¡i bÃ n cho nhÃ¢n viÃªn
     */
    public function table_status()
    {
        // Sá»­ dá»¥ng TableStatusController Ä‘á»ƒ xá»­ lÃ½
        require_once __DIR__ . '/TableStatusController.php';
        $tableStatusController = new TableStatusController();
        $tableStatusController->index();
    }

    /**
     * Cáº­p nháº­t tráº¡ng thÃ¡i bÃ n
     */
    public function update_table_status()
    {
        // Sá»­ dá»¥ng TableStatusController Ä‘á»ƒ xá»­ lÃ½
        require_once __DIR__ . '/TableStatusController.php';
        $tableStatusController = new TableStatusController();
        $tableStatusController->updateStatus();
    }

    /**
     * Láº¥y chi tiáº¿t bÃ n (AJAX)
     */
    public function get_table_details()
    {
        // Sá»­ dá»¥ng TableStatusController Ä‘á»ƒ xá»­ lÃ½
        require_once __DIR__ . '/TableStatusController.php';
        $tableStatusController = new TableStatusController();
        $tableStatusController->getTableDetails();
    }

    /**
     * Láº¥y danh sÃ¡ch bÃ n trá»‘ng theo logic má»›i (AJAX)
     */
    public function getAvailableTables()
    {
        header('Content-Type: application/json');
        
        try {
            // Kiá»ƒm tra quyá»n truy cáº­p
            $this->authController->requireNhanVien();
            
            $currentUser = $_SESSION['user'];
            $maCoSo = $currentUser['MaCoSo'];
            
            // Láº¥y danh sÃ¡ch bÃ n trá»‘ng theo logic má»›i (khÃ´ng cÃ³ Ä‘Æ¡n Ä‘áº·t trong vÃ²ng 2 giá» tá»›i)
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
                'error' => 'CÃ³ lá»—i xáº£y ra khi láº¥y danh sÃ¡ch bÃ n: ' . $e->getMessage()
            ]);
        }
    }

}