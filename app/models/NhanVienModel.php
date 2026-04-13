<?php
/**
 * Model cho bảng nhân viên
 * Cung cấp các phương thức để tương tác với bảng NHANVIEN
 */
class NhanVienModel
{
    private $conn;
    private $table_name = "nhanvien";
    // Các thuộc tính của nhân viên
    public $MaNV;
    public $MaCoSo;
    public $TenDN;
    public $MatKhau;
    public $TenNhanVien;
    public $ChucVu;

    // Hàm khởi tạo với kết nối database
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả nhân viên
    public function getAll()
    {
        $query = "SELECT n.*, c.TenCoSo 
                  FROM " . $this->table_name . " n
                  LEFT JOIN coso c ON n.MaCoSo = c.MaCoSo
                  ORDER BY n.MaNV";
        
        $result = mysqli_query($this->conn, $query);
        
        return $result;
    }

    /**
     * Lấy thông tin một nhân viên theo MaNV
     * int $id Mã nhân viên
     * trả về boolean True nếu thành công
     */
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE MaNV = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row) {
            $this->MaNV = $row['MaNV'];
            $this->MaCoSo = $row['MaCoSo'];
            $this->TenDN = $row['TenDN'];
            $this->MatKhau = $row['MatKhau'];
            $this->TenNhanVien = $row['TenNhanVien'];
            $this->ChucVu = $row['ChucVu'];
            return true;
        }
        return false;
    }

    /*
     * Tạo nhân viên mới
     * trả về boolean True nếu thành công
     */
    public function create()
    {
        // Kiểm tra tên đăng nhập đã tồn tại chưa
        if ($this->usernameExists()) {
            return false;
        }
        
        // Hash mật khẩu trước khi lưu
        $hashed_password = password_hash($this->MatKhau, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table_name . "
                (MaCoSo, TenDN, MatKhau, TenNhanVien, ChucVu)
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $query);
        
        // Làm sạch dữ liệu
        $this->MaCoSo = htmlspecialchars(strip_tags($this->MaCoSo));
        $this->TenDN = htmlspecialchars(strip_tags($this->TenDN));
        $this->TenNhanVien = htmlspecialchars(strip_tags($this->TenNhanVien));
        $this->ChucVu = htmlspecialchars(strip_tags($this->ChucVu));
        
        // Ràng buộc các tham số
        mysqli_stmt_bind_param($stmt, "issss", $this->MaCoSo, $this->TenDN, $hashed_password, $this->TenNhanVien, $this->ChucVu);
        
        // Thực hiện truy vấn
        if (mysqli_stmt_execute($stmt)) {
            $this->MaNV = mysqli_insert_id($this->conn);
            return true;
        }
        
        return false;
    }

    /*
     * Cập nhật thông tin nhân viên
     * trả về boolean True nếu thành công
     */
    public function update()
    {
        // Kiểm tra nếu đổi tên đăng nhập
        $check_username = "SELECT TenDN FROM " . $this->table_name . " WHERE MaNV = ?";
        $stmt_check = mysqli_prepare($this->conn, $check_username);
        mysqli_stmt_bind_param($stmt_check, "i", $this->MaNV);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row = mysqli_fetch_assoc($result_check);
        
        if ($row['TenDN'] != $this->TenDN && $this->usernameExists()) {
            return false;
        }
        
        // Xây dựng câu truy vấn update
        if (!empty($this->MatKhau)) {
            // Có cập nhật mật khẩu
            $hashed_password = password_hash($this->MatKhau, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table_name . "
                     SET MaCoSo = ?, TenDN = ?, MatKhau = ?, TenNhanVien = ?, ChucVu = ?
                     WHERE MaNV = ?";
            
            $stmt = mysqli_prepare($this->conn, $query);
            
            // Làm sạch dữ liệu
            $this->MaCoSo = htmlspecialchars(strip_tags($this->MaCoSo));
            $this->TenDN = htmlspecialchars(strip_tags($this->TenDN));
            $this->TenNhanVien = htmlspecialchars(strip_tags($this->TenNhanVien));
            $this->ChucVu = htmlspecialchars(strip_tags($this->ChucVu));
            $this->MaNV = htmlspecialchars(strip_tags($this->MaNV));
            
            // Ràng buộc các tham số
            mysqli_stmt_bind_param($stmt, "issssi", $this->MaCoSo, $this->TenDN, $hashed_password, $this->TenNhanVien, $this->ChucVu, $this->MaNV);
        } else {
            // Không cập nhật mật khẩu
            $query = "UPDATE " . $this->table_name . "
                     SET MaCoSo = ?, TenDN = ?, TenNhanVien = ?, ChucVu = ?
                     WHERE MaNV = ?";
            
            $stmt = mysqli_prepare($this->conn, $query);
            
            // Làm sạch dữ liệu
            $this->MaCoSo = htmlspecialchars(strip_tags($this->MaCoSo));
            $this->TenDN = htmlspecialchars(strip_tags($this->TenDN));
            $this->TenNhanVien = htmlspecialchars(strip_tags($this->TenNhanVien));
            $this->ChucVu = htmlspecialchars(strip_tags($this->ChucVu));
            $this->MaNV = htmlspecialchars(strip_tags($this->MaNV));
            
            // Ràng buộc các tham số
            mysqli_stmt_bind_param($stmt, "isssi", $this->MaCoSo, $this->TenDN, $this->TenNhanVien, $this->ChucVu, $this->MaNV);
        }
        
        // Thực hiện truy vấn
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa nhân viên
     * trả về boolean True nếu thành công
     */
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE MaNV = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        $this->MaNV = htmlspecialchars(strip_tags($this->MaNV));
        mysqli_stmt_bind_param($stmt, "i", $this->MaNV);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Kiểm tra xem tên đăng nhập đã tồn tại chưa
     * trả về boolean True nếu tên đăng nhập đã tồn tại
     */
    private function usernameExists()
    {
        $query = "SELECT MaNV FROM " . $this->table_name . " WHERE TenDN = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $this->TenDN);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) > 0;
    }

    /**
     * Kiểm tra thông tin đăng nhập
     *  string $username Tên đăng nhập
     *  string $password Mật khẩu
     * trả về boolean True nếu thông tin đăng nhập đúng
     */
    public function login($username, $password)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE TenDN = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row && password_verify($password, $row['MatKhau'])) {
                $this->MaNV = $row['MaNV'];
                $this->MaCoSo = $row['MaCoSo'];
                $this->TenDN = $row['TenDN'];
                $this->MatKhau = $row['MatKhau'];
                $this->TenNhanVien = $row['TenNhanVien'];
                $this->ChucVu = $row['ChucVu'];
                return [true, $this->toArray()];
        } else {
            error_log("DEBUG MODEL - User NOT found in database");
            return [false, null];
        }
    }

    /**
     * Lấy danh sách nhân viên theo cơ sở
     *  int $MaCoSo Mã cơ sở
     */
    public function getByCoSo($MaCoSo)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE MaCoSo = ? ORDER BY MaNV";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $MaCoSo);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    /**
     * Chuyển đổi thuộc tính object thành array
     * @return array
     */
    public function toArray()
    {
        return [
            'MaNV' => $this->MaNV,
            'MaCoSo' => $this->MaCoSo,
            'TenDN' => $this->TenDN,
            'TenNhanVien' => $this->TenNhanVien,
            'ChucVu' => $this->ChucVu
        ];
    }
}
?>