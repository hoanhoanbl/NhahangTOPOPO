<?php

require_once __DIR__ . '/../../config/database.php'; 
require_once __DIR__ . '/../models/NhanVienModel.php'; 
require_once __DIR__ . '/../../includes/BaseController.php'; 

class AuthController extends BaseController 
{
    private $nhanVienModel;
    private $db;

    public function __construct() {
        // Khởi tạo kết nối DB và NhanVienModel
        $database = new Database();
        $this->db = $database->getConnection();
        $this->nhanVienModel = new NhanVienModel($this->db);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hiển thị form đăng nhập
    public function login()
    {
        if ($this->isLoggedIn()) {
            // Chuyển hướng dựa trên vai trò
            $user = $_SESSION['user'];
            $role = $this->normalizeRole($user['ChucVu'] ?? '');
            if ($role === 'admin' || $role === 'manager' || $role === 'receptionist') {
                $this->redirect('index.php?page=admin&action=dashboard');
            } else if ($user['ChucVu'] === 'nhan_vien') {
                $this->redirect('index.php?page=nhanvien&action=dashboard&section=dashboard');
            } else {
                // Logout nếu vai trò không hợp lệ
                $this->logout();
            }
            return;
        }
        include __DIR__ . '/../../login.php';
        exit;
    }
    
    // Xử lý thông tin đăng nhập từ form
    public function authenticate() 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=auth&action=login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

     
        if (empty($username) || empty($password)) {
            error_log("DEBUG LOGIN - Empty username or password");
            $_SESSION['error_message'] = 'Vui lòng nhập đầy đủ thông tin.';
            $this->redirect('index.php?page=auth&action=login');
            return;
        }

        // Sử dụng NhanVienModel để xác thực
        list($isSuccess, $nhanVienData) = $this->nhanVienModel->login($username, $password);

        if ($nhanVienData) {
            error_log("DEBUG LOGIN - User data: " . json_encode($nhanVienData));
        }

        if ($isSuccess) {
            // Đăng nhập thành công
            $_SESSION['user'] = $nhanVienData;
            $_SESSION['is_logged_in'] = true;
            
            if ($remember) {
                // Xử lý "Ghi nhớ đăng nhập" (tương tự logic cũ)
                $token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 ngày
                setcookie('remember_token', $token, $expires, '/');
                setcookie('remember_user', $nhanVienData['MaNV'], $expires, '/');
            }
            
            // Phân quyền dựa trên ChucVu
            $role = $this->normalizeRole($nhanVienData['ChucVu'] ?? '');
            if ($role === 'admin' || $role === 'manager' || $role === 'receptionist') {
                $this->redirect('index.php?page=admin&action=dashboard');
            } else if ($nhanVienData['ChucVu'] === 'nhan_vien') {
                $this->redirect('index.php?page=nhanvien&action=dashboard&section=dashboard');
            } else {
                // Trường hợp không xác định được vai trò
                $_SESSION['error_message'] = 'Vai trò không hợp lệ. Vui lòng liên hệ quản trị viên.';
                $this->redirect('index.php?page=auth&action=login');
            }
            return;
        }
        
        // Đăng nhập thất bại
        error_log("DEBUG LOGIN - Login failed, redirecting to login page");
        $_SESSION['error_message'] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        $this->redirect('index.php?page=auth&action=login');
    }
    
    public function logout() 
    {
        // Xóa cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
            setcookie('remember_user', '', time() - 3600, '/');
        }
        session_destroy();
        
        // Khởi tạo lại session để lưu thông báo
        session_start();
        $_SESSION['success_message'] = 'Đăng xuất thành công!';
        $this->redirect('index.php?page=auth&action=login');
    }

    public function isLoggedIn() {
        if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
            return true;
        }
        // Kiểm tra cookie "Ghi nhớ đăng nhập"
        if (isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
            $maNV = $_COOKIE['remember_user'];
            if ($this->nhanVienModel->getById($maNV)) {
                $_SESSION['user'] = $this->nhanVienModel->toArray();
                $_SESSION['is_logged_in'] = true;
                return true;
            }
        }
        return false;
    }

    // Middleware yêu cầu đăng nhập
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập để tiếp tục.';
            $this->redirect('index.php?page=auth&action=login');
            exit;
        }
    }

    // Middleware yêu cầu quyền admin
    public function requireAdmin() {
        $this->requireAuth();
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] !== 'admin') {
            $_SESSION['error_message'] = 'Bạn không có quyền truy cập trang này.';
            $this->redirect('index.php?page=auth&action=login');
            exit;
        }
    }

    // Middleware yêu cầu quyền nhân viên
    public function requireNhanVien() {
        $this->requireAuth();

        $role = $this->normalizeRole($_SESSION['user']['ChucVu'] ?? '');
        if (!isset($_SESSION['user']) || $role !== 'receptionist') {
            $_SESSION['error_message'] = 'Bạn không có quyền truy cập trang này.';
            $this->redirect('index.php?page=auth&action=login');
            exit;
        }
    }

    // Kiểm tra xem user có phải admin không
    public function isAdmin() {
        return $this->isLoggedIn() &&
               isset($_SESSION['user']) &&
               $this->normalizeRole($_SESSION['user']['ChucVu'] ?? '') === 'admin';
    }

    // Kiểm tra xem user có phải nhân viên không
    public function isNhanVien() {
        return $this->isLoggedIn() &&
               isset($_SESSION['user']) &&
               $this->normalizeRole($_SESSION['user']['ChucVu'] ?? '') === 'receptionist';
    }

    public function getCurrentRole(): string
    {
        return $this->normalizeRole($_SESSION['user']['ChucVu'] ?? '');
    }

    public function getCurrentBranchId(): int
    {
        return (int)($_SESSION['user']['MaCoSo'] ?? 0);
    }

    public function isBranchScopedUser(): bool
    {
        return !$this->isAdmin();
    }

    public function resolveScopedBranchId(int $requestedBranchId = 0): int
    {
        if ($this->isAdmin()) {
            return max(0, $requestedBranchId);
        }
        return $this->getCurrentBranchId();
    }

    public function canAccessBranch(int $targetBranchId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        $currentBranchId = $this->getCurrentBranchId();
        return $currentBranchId > 0 && $targetBranchId > 0 && $currentBranchId === $targetBranchId;
    }

    public function denyBranchAccess(string $message = 'Ban khong co quyen truy cap du lieu cua co so khac.'): void
    {
        $_SESSION['error_message'] = $message;
        $userId = $_SESSION['user']['MaNV'] ?? 'unknown';
        $role = $this->getCurrentRole();
        $branch = $this->getCurrentBranchId();
        error_log("[AUTH][DENY_BRANCH] user={$userId} role={$role} branch={$branch} message={$message}");
    }

    // =========================================================
    // RBAC — Permission matrix (in-memory, no DB lookup)
    // =========================================================
    // Format: PERMISSIONS[role][resource][action] = bool
    private static array $PERMISSIONS = [
        'admin' => [
            'dashboard' => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'booking'  => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'table'   => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'menu'    => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'branch'  => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'staff'   => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'report'  => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
            'uudai'   => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>true],
        ],
        'manager' => [
            'dashboard' => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>false],
            'booking'  => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>false],
            'table'   => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>false],
            'menu'    => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>false],
            'branch'  => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
            'staff'   => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
            'report'  => ['read'=>true, 'create'=>true, 'update'=>true, 'delete'=>false],
            'uudai'   => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
        ],
        'receptionist' => [
            'dashboard' => ['read'=>true, 'create'=>true, 'update'=>false,'delete'=>false],
            'booking'  => ['read'=>true, 'create'=>true, 'update'=>false,'delete'=>false],
            'table'   => ['read'=>true, 'create'=>true, 'update'=>false,'delete'=>false],
            'menu'    => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
            'branch'  => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
            'staff'   => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
            'report'  => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
            'uudai'   => ['read'=>true, 'create'=>false,'update'=>false,'delete'=>false],
        ],
    ];

    /**
     * Check if the current session user can perform $action on $resource.
     * Admin always returns true. Unknown role/resource returns false.
     *
     * @param string $resource e.g. 'booking', 'menu', 'table', 'branch', 'staff', 'report', 'uudai', 'dashboard'
     * @param string $action 'read'|'create'|'update'|'delete'
     * @return bool
     */
    public function can(string $resource, string $action = 'read'): bool
    {
        // Admin bypasses all checks
        if ($this->isAdmin()) {
            return true;
        }
        $role = $this->normalizeRole($_SESSION['user']['ChucVu'] ?? '');

        // Priority 1: DB permissions table
        $dbPermissions = $this->getDbPermissions();
        if (!empty($dbPermissions)) {
            $col = match ($action) {
                'read' => 'can_view',
                'create' => 'can_create',
                'update' => 'can_update',
                'delete' => 'can_delete',
                default => null
            };
            if ($col === null) {
                return false;
            }
            return (bool)($dbPermissions[$role][$resource][$col] ?? false);
        }

        // Fallback: in-memory matrix
        $actions = self::$PERMISSIONS[$role][$resource] ?? [];
        return $actions[$action] ?? false;
    }

    /**
     * Append branch-scoping clause to a raw SQL query.
     * Admin sees all branches. Manager/Receptionist only see their branch.
     *
     * @param string $sql   Raw SQL starting with SELECT ... (no trailing WHERE unless noted)
     * @param string $alias Table alias or empty string
     * @return string SQL with branch filter appended
     */
    public function scopeByBranch(string $sql, string $alias = ''): string
    {
        // Admin: no filter
        if ($this->isAdmin()) {
            return $sql;
        }
        $maCoSo = $this->getCurrentBranchId();
        if ($maCoSo <= 0) {
            return $sql; // fallback: no filter if MaCoSo missing
        }
        $col = $alias ? "$alias.MaCoSo" : "MaCoSo";
        $hasWhere = stripos($sql, ' where ') !== false;
        $joiner = $hasWhere ? ' AND ' : ' WHERE ';
        return $sql . $joiner . $col . ' = ' . $maCoSo;
    }

    public function normalizeRoleValue(string $role): string
    {
        return $this->normalizeRole($role);
    }

    private function normalizeRole(string $role): string
    {
        $value = strtolower(trim($role));
        return match ($value) {
            'admin' => 'admin',
            'manager', 'quan_ly', 'quanli', 'quan_li' => 'manager',
            'receptionist', 'le_tan', 'letan', 'nhan_vien' => 'receptionist',
            default => $value
        };
    }

    private function getDbPermissions(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $cache = [];
        if (!$this->db) {
            return $cache;
        }

        $tableCheck = @mysqli_query($this->db, "SHOW TABLES LIKE 'permissions'");
        if (!$tableCheck || mysqli_num_rows($tableCheck) === 0) {
            return $cache;
        }

        $sql = "SELECT role, resource, can_view, can_create, can_update, can_delete FROM permissions";
        $result = @mysqli_query($this->db, $sql);
        if (!$result) {
            return $cache;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $role = $this->normalizeRole($row['role'] ?? '');
            $resource = strtolower(trim($row['resource'] ?? ''));
            if ($role === '' || $resource === '') {
                continue;
            }
            $cache[$role][$resource] = [
                'can_view' => (int)$row['can_view'] === 1,
                'can_create' => (int)$row['can_create'] === 1,
                'can_update' => (int)$row['can_update'] === 1,
                'can_delete' => (int)$row['can_delete'] === 1,
            ];
        }

        return $cache;
    }
}
