<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/BranchModel.php';
require_once __DIR__ . '/../../includes/BaseController.php';

class BranchController extends BaseController
{
    private $db;
    private $coSo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->coSo = new BranchModel($this->db);
    }

    public function index()
    {
        $page_title = "Quán Nhậu Tự Do - Hệ Thống Cơ Sở";
        
        // Lấy dữ liệu cơ sở từ database
        $stmt = $this->coSo->getAll();
        $branches_data = [];
        while ($row = mysqli_fetch_assoc($stmt)) {
            $branches_data[] = $row;
        }
        
        // Chuyển đổi dữ liệu database thành format hiển thị
        $branches = [];
        foreach($branches_data as $branch_data) {
            $branches[] = [
                'id' => $branch_data['MaCoSo'],
                'name' => $branch_data['TenCoSo'],
                'district' => $branch_data['DiaChi'],
                'hotline' => $branch_data['DienThoai'],
                'image' => $branch_data['AnhUrl'],
            ];
        }
        
        // Fallback to sample data if no data in database
        if(empty($branches)) {
            $branches = [
                [
                    'id' => 1,
                    'name' => '67A Phó Đức Chính',
                    'district' => '',
                    'hotline' => '*1986',
                    'image' => 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2023/08/202308051004475343.webp',
                ]
            ];
        }
        
        // **[SỬA]** Tạo danh sách các quận để tạo tab lọc
        $branch_districts = [];
        foreach($branches as $branch) {
            if(!empty($branch['district']) && !in_array($branch['district'], $branch_districts)) {
                $branch_districts[] = $branch['district'];
            }
        }
        sort($branch_districts); // Sắp xếp tên quận theo alphabet cho dễ nhìn

        // **[SỬA]** Lấy quận được chọn từ URL
        $selected_district = $_GET['district'] ?? 'all';
        
        // Render view
        $this->render('branches/index', [
            'page_title' => $page_title,
            'branches' => $branches,
            'branch_districts' => $branch_districts, // Truyền danh sách quận
            'selected_district' => $selected_district
        ]);
    }

    // Hàm để js gọi API lấy danh sách theo cơ sở mà không cần load lại trang
    public function api()
    {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        try {
            // Lấy tham số address từ URL
            $address = $_GET['address'] ?? 'all';
            
            // Lấy dữ liệu cơ sở từ database
            $stmt = $this->coSo->getAll();
            $branches_data = [];
            while ($row = mysqli_fetch_assoc($stmt)) {
                $branches_data[] = $row;
            }
            
            // Chuyển đổi dữ liệu database thành format hiển thị
            $branches = [];
            foreach($branches_data as $branch_data) {
                $branch = [
                    'id' => $branch_data['MaCoSo'],
                    'name' => $branch_data['TenCoSo'],
                    'district' => $branch_data['DiaChi'],
                    'hotline' => $branch_data['DienThoai'],
                    'image' => $branch_data['AnhUrl'],
                ];
                
                // Lọc theo address nếu không phải "all"
                if ($address === 'all' || strtolower($branch['district']) === strtolower($address)) {
                    $branches[] = $branch;
                }
            }
            
            // Fallback to sample data if no data in database
            if(empty($branches) && empty($branches_data)) {
                $sample_branch = [
                    'id' => 1,
                    'name' => '67A Phó Đức Chính',
                    'district' => 'Hòa Xuân',
                    'hotline' => '*1986',
                    'image' => 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2023/08/202308051004475343.webp',
                ];
                
                // Chỉ thêm sample data nếu nó match với filter
                if ($address === 'all' || strtolower($sample_branch['district']) === strtolower($address)) {
                    $branches[] = $sample_branch;
                }
            }
            
            // Trả về JSON response
            echo json_encode([
                'success' => true,
                'data' => $branches,
                'message' => 'Data loaded successfully'
            ]);
            
        } catch (Exception $e) {
            // Trả về error response
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
        
        exit; // Dừng execution để tránh output thêm HTML
    }
}