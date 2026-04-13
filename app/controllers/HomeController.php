<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/BranchModel.php';

class HomeController extends BaseController 
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
        // Lấy dữ liệu cơ sở từ database
        $stmt = $this->coSo->getAll();
        $branches_data = [];
        while ($row = mysqli_fetch_assoc($stmt)) {
            $branches_data[] = $row;
        }
        
        // Nhóm cơ sở theo địa chỉ
        $grouped_branches = [];
        $location_counts = [];
        
        foreach($branches_data as $branch_data) {
            if(empty($branch_data['TenCoSo'])) continue;
            
            $district = $branch_data['DiaChi'];
            
            // Khởi tạo group nếu chưa có
            if (!isset($grouped_branches[$district])) {
                $grouped_branches[$district] = [];
                $location_counts[$district] = 0;
            }
            
            // Thêm cơ sở vào group
            $grouped_branches[$district][] = [
                'id' => $branch_data['MaCoSo'],
                'name' => $branch_data['TenCoSo'],
                'address' => $branch_data['DiaChi'],
                'phone' => $branch_data['DienThoai'],
                'image' => $branch_data['AnhUrl'],
            ];
            
            $location_counts[$district]++;
            
        }
        
        // Tính tổng số cơ sở
        $total_branches = array_sum($location_counts);

        
        $this->render('home/index', [
            'grouped_branches' => $grouped_branches,
            'location_counts' => $location_counts,
            'total_branches' => $total_branches
        ]);
    }
    
    public function about() 
    {
        $this->render('home/about');
    }
    
    public function notFound() 
    {
        http_response_code(404);
        $this->render('errors/404');
    }
}
