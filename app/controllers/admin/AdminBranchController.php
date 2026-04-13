<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/BranchModel.php';
require_once __DIR__ . '/../../../includes/BaseController.php'; 

class AdminBranchController extends BaseController
{
    private $db;
    private $coSo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->coSo = new BranchModel($this->db);

        // >> BẢO MẬT: Đây là nơi lý tưởng để kiểm tra quyền admin
        // session_start();
        // if (!isset($_SESSION['user_is_admin']) || $_SESSION['user_is_admin'] !== true) {
        //     // Chuyển hướng về trang đăng nhập hoặc hiển thị lỗi
        //     die('Truy cập bị từ chối!');
        // }
    }

    /**
     * Hiển thị trang quản lý cơ sở chính
     */
    public function index()
    {
        $page_title = "Quản lý cơ sở - Admin";
        $stmt = $this->coSo->getAll();
        $branches = [];
        while ($row = mysqli_fetch_assoc($stmt)) {
            $branches[] = $row;
        }
        // Render view của trang admin
        // $this->render('admin/branches/index', [
        //     'page_title' => $page_title,
        //     'branches' => $branches
        // ]);
    }

    /**
     * Lấy dữ liệu branches mà không render view
     * @return array
     */
    public function getBranches()
    {
        $stmt = $this->coSo->getAll();
        $branches = [];
        while ($row = mysqli_fetch_assoc($stmt)) {
            $branches[] = $row;
        }
        return $branches;
    }
    /**
     * API để lấy dữ liệu cơ sở cho bảng admin (JSON response)
     */
    public function get_data()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $stmt = $this->coSo->getAll();
            $branches = [];
            while ($row = mysqli_fetch_assoc($stmt)) {
                $branches[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $branches,
                'count' => count($branches)
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * API để thêm cơ sở mới
     */
    public function add()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
            return;
        }

        // Gán dữ liệu từ POST vào thuộc tính của Model
        $this->coSo->TenCoSo = $_POST['tenCoSo'] ?? '';
        $this->coSo->DiaChi = $_POST['diaChi'] ?? '';
        $this->coSo->DienThoai = $_POST['dienThoai'] ?? '';
        $this->coSo->AnhUrl = $_POST['anhUrl'] ?? '';

        if (empty($this->coSo->TenCoSo) || empty($this->coSo->DiaChi) || empty($this->coSo->DienThoai)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
            return;
        }

        // Gọi phương thức create từ Model
        if ($this->coSo->create()) {
            echo json_encode(['success' => true, 'message' => 'Thêm cơ sở thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm cơ sở']);
        }
    }

     public function update()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
            return;
        }

        // Gán dữ liệu từ POST vào các thuộc tính của Model
        $this->coSo->MaCoSo = $_POST['maCoSo'] ?? '';
        $this->coSo->TenCoSo = $_POST['tenCoSo'] ?? '';
        $this->coSo->DiaChi = $_POST['diaChi'] ?? '';
        $this->coSo->DienThoai = $_POST['dienThoai'] ?? '';
        $this->coSo->AnhUrl = $_POST['anhUrl'] ?? '';

        if (empty($this->coSo->MaCoSo) || empty($this->coSo->TenCoSo) || empty($this->coSo->DiaChi)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
            return;
        }

        // === THAY ĐỔI QUAN TRỌNG: Gọi phương thức update() từ Model ===
        if ($this->coSo->update()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật cơ sở thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật cơ sở']);
        }
    }

   public function delete()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
            return;
        }

        // Gán MaCoSo vào thuộc tính của Model
        $this->coSo->MaCoSo = $_POST['maCoSo'] ?? '';

        if (empty($this->coSo->MaCoSo)) {
            echo json_encode(['success' => false, 'message' => 'Mã cơ sở không được để trống']);
            return;
        }
        
        // === THAY ĐỔI QUAN TRỌNG: Gọi phương thức delete() từ Model ===
        if ($this->coSo->delete()) {
            echo json_encode(['success' => true, 'message' => 'Xóa cơ sở thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa cơ sở']);
        }
    }
}

// Xử lý request nếu được gọi trực tiếp
if (basename($_SERVER['PHP_SELF']) === 'AdminBranchController.php') {
    $controller = new AdminBranchController();
    $action = $_GET['action'] ?? 'index';
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
    }
    exit;
}
?>