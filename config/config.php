<?php
/**
 * Cấu hình chung của ứng dụng
 */

// Khởi động session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cấu hình múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình đường dẫn
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Đọc file .env nếu tồn tại
function loadEnvFile($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Bỏ qua comment
        }
        
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
        }
    }
}

// Tải file .env
loadEnvFile(ROOT_PATH . '/.env');

// Function để lấy giá trị từ env với fallback
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Cấu hình URL
define('BASE_URL', env('BASE_URL', 'http://localhost/DatBanNH'));
define('ASSETS_URL', BASE_URL . '/public');

// Cấu hình ứng dụng
define('APP_NAME', 'Hệ thống đặt bàn nhà hàng');
define('APP_VERSION', '1.0.0');

// Cấu hình email - Sử dụng biến môi trường
define('SMTP_HOST', env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', (int)env('SMTP_PORT', 465));
define('SMTP_USERNAME', env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', env('SMTP_PASSWORD', ''));

// Bao gồm file database
require_once CONFIG_PATH . '/database.php';

// Hàm helper
function redirect($url) {
    header("Location: " . BASE_URL . "/" . ltrim($url, '/'));
    exit();
}

function asset($path) {
    return ASSETS_URL . "/" . ltrim($path, '/');
}

function url($path = '') {
    return BASE_URL . "/" . ltrim($path, '/');
}

function isActivePage($page) {
    $currentPage = $_GET['page'] ?? 'home';
    return $currentPage === $page ? 'active' : '';
}

function view($view, $data = []) {
    extract($data);
    $viewFile = APP_PATH . '/views/' . $view . '.php';
    
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        echo "View không tồn tại: " . $view;
    }
}
?>
