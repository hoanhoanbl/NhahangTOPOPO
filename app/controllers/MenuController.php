<?php
require_once __DIR__ . '/../../config/database.php';

class MenuController extends BaseController 
{
    private $db;
    private $menuModel; 

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->menuModel = new MenuModel($this->db); 
    }
    public function index() 
    {
        // Lấy danh sách tất cả cơ sở để hiển thị trong modal
        $branches = $this->getBranches();
        
        $this->render('menu/menu', [
            'branches' => $branches
        ]);
    }
    
    public function show() 
    {
        $menuId = $_GET['id'] ?? null;
        $this->render('menu/show', ['menuId' => $menuId]);
    }
    
    public function category() 
    {
        $category = $_GET['category'] ?? 'all';
        $this->render('menu/category', ['category' => $category]);
    }

     public function menu2() 
    {
        // Lấy mã cơ sở
        $maCoSo = $_GET['coso'] ?? 21;
        $selectedCategory = $_GET['category'] ?? 'all';
        
        // === THAY ĐỔI: GỌI DỮ LIỆU TỪ MODEL ===
        $categories = $this->menuModel->findCategoriesByCoSo($maCoSo);
        $menuItems = $this->menuModel->findMenuItemsByCoSoAndCategory($maCoSo, $selectedCategory);
        $groupedMenuItems = $this->menuModel->findMenuItemsGroupedByCategory($maCoSo);
        
        // Script riêng cho menu2
        // $additional_scripts = '<script src="' . asset('js/menu2.js') . '"></script>';
        
        // Truyền dữ liệu cho View
        $this->render('menu2/menu2', [
            'categories' => $categories,
            'menuItems' => $menuItems,
            'groupedMenuItems' => $groupedMenuItems,
            'selectedCategory' => $selectedCategory,
            'maCoSo' => $maCoSo,
            'branches' => $this->getBranches(),
        ]);
    }
    
    // API endpoint để lấy dữ liệu JSON cho AJAX
    public function getMenuData() 
    {
        header('Content-Type: application/json');
        
        $maCoSo = $_GET['coso'] ?? 11;
        $category = $_GET['category'] ?? 'all';
        
        // === THAY ĐỔI: GỌI DỮ LIỆU TỪ MODEL ===
        if ($category === 'all') {
            $groupedMenuItems = $this->menuModel->findMenuItemsGroupedByCategory($maCoSo);
            echo json_encode([
                'success' => true,
                'data' => $groupedMenuItems,
                'type' => 'grouped'
            ]);
        } else {
            $menuItems = $this->menuModel->findMenuItemsByCoSoAndCategory($maCoSo, $category);
            echo json_encode([
                'success' => true,
                'data' => $menuItems,
                'type' => 'list'
            ]);
        }
    }
    
    /**
     * API: Trả về danh sách cơ sở dạng JSON cho dropdown global
     */
    public function branches()
    {
        header('Content-Type: application/json');
        try {
            $branches = $this->getBranches();
            echo json_encode([
                'success' => true,
                'data' => $branches
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể tải danh sách cơ sở'
            ]);
        }
    }
    
    /**
     * API: Kiểm tra và áp dụng mã giảm giá
     */
    public function validateDiscount()
    {
        header('Content-Type: application/json');
        
        // Lấy mã giảm giá từ request
        $discountCode = $_POST['code'] ?? '';
        $totalAmount = floatval($_POST['total'] ?? 0);
        
        if (empty($discountCode)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá'
            ]);
            return;
        }
        
        // Chuẩn hóa mã (viết hoa, bỏ khoảng trắng)
        $discountCode = strtoupper(trim($discountCode));
        
        // Truy vấn mã giảm giá từ database
        // Tìm theo cột TenMaUD (mã khách hàng nhập: GIAM10, GIAM20...)
        $sql = "SELECT MaUD, TenMaUD, MoTa, GiaTriGiam, LoaiGiamGia, DieuKien, NgayBD, NgayKT 
                FROM uudai 
                WHERE TenMaUD = ? 
                AND NgayBD <= CURDATE() 
                AND NgayKT >= CURDATE()";
        
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, 's', $discountCode);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Tính toán giá trị giảm
            $discountValue = floatval($row['GiaTriGiam']);
            $discountType = $row['LoaiGiamGia'];
            
            if ($discountType === 'phantram') {
                // Giảm theo phần trăm
                $discountAmount = ($totalAmount * $discountValue) / 100;
                $discountPercent = $discountValue;
            } else {
                // Giảm theo số tiền cố định
                $discountAmount = $discountValue;
                $discountPercent = ($totalAmount > 0) ? ($discountValue / $totalAmount) * 100 : 0;
            }
            
            // Đảm bảo số tiền giảm không vượt quá tổng tiền
            $discountAmount = min($discountAmount, $totalAmount);
            $finalAmount = max(0, $totalAmount - $discountAmount);
            
            echo json_encode([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công!',
                'data' => [
                    'id' => $row['MaUD'],  // ID số (để lưu vào đơn hàng)
                    'code' => $row['TenMaUD'],  // Mã giảm giá (GIAM10, GIAM20...)
                    'description' => $row['MoTa'],
                    'discountType' => $discountType,
                    'discountValue' => $discountValue,
                    'discountAmount' => $discountAmount,
                    'discountPercent' => round($discountPercent, 1),
                    'subtotal' => $totalAmount,
                    'finalAmount' => $finalAmount
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }
    }
    
    /**
     * Lấy danh sách tất cả cơ sở
     */
    private function getBranches() 
    {
        $sql = "SELECT MaCoSo, TenCoSo, DiaChi FROM coso WHERE TenCoSo != '' ORDER BY TenCoSo ASC";
        $result = mysqli_query($this->db, $sql);
        $branches = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $branches[] = $row;
        }
        return $branches;
    }
}