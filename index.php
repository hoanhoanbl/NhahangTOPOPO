<?php
/**
 * File điểm vào chính của ứng dụng
 */

// DEBUG: Bật hiển thị lỗi để tìm nguyên nhân (TẮT SAU KHI FIX XONG)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bắt đầu session
session_start();

// Bao gồm file cấu hình
require_once 'config/config.php';

// Autoload classes
spl_autoload_register(function($class) {
    $paths = [
        'app/controllers/',
        'app/models/',
        'includes/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});

// Lấy tham số từ URL
$request = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Routing đơn giản
switch ($request) {
    case 'home':
        $controller = new HomeController();
        break;
        
    case 'booking':
        $controller = new BookingController();
        break;
        
    case 'auth':
        $controller = new AuthController();
        break;
        
    case 'menu':
        $controller = new MenuController();
        break;
        
    case 'menu2':
        $controller = new MenuController();
        $action = 'menu2';
        break;
        
    case 'contact':
        $controller = new ContactController();
        break;
        
    case 'branches':
        $controller = new BranchController();
        break;
        
    case 'admin':
        $controller = new AdminController();
        break;
        
    case 'nhanvien':
        $controller = new NhanVienController();
        $action = $_GET['action'];
        break;
    
    default:
        $controller = new HomeController();
        $action = 'notFound';
}

// Gọi phương thức tương ứng
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Debug: Log missing method
    error_log("Method '$action' not found in " . get_class($controller));
    $controller->index();
}
?>
