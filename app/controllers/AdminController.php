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
        
        // Render admin dashboard độc lập (không sử dụng layout)
        include dirname(__DIR__) . '/views/admin/dashboard.php';
        exit;
    }
}
