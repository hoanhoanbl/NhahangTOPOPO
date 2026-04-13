<?php

require_once __DIR__ . '/AuthController.php';

class AdminController extends BaseController 
{
    private $authController;

    public function __construct() 
    {
        $this->authController = new AuthController();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function index() 
    {
        $this->dashboard();
    }
    
    public function dashboard() 
    {
        // Kiểm tra đăng nhập và quyền dashboard
        $this->authController->requireAuth();
        if (!$this->authController->can('dashboard', 'read')) {
            $_SESSION['error_message'] = 'Bạn không có quyền truy cập dashboard.';
            $this->redirect('index.php?page=auth&action=login');
            return;
        }

        $section = $_GET['section'] ?? 'dashboard';
        $isBookingCreateRequest = $section === 'booking'
            && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
            && (($_POST['action'] ?? '') === 'create_booking');

        // Booking create must bypass dashboard layout so the client receives pure JSON.
        if ($isBookingCreateRequest) {
            include dirname(__DIR__) . '/views/admin/booking/index.php';
            exit;
        }
        
        // Render admin dashboard độc lập (không sử dụng layout)
        include dirname(__DIR__) . '/views/admin/dashboard.php';
        exit;
    }
}
