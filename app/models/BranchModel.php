<?php

class BranchModel {
    private $conn;
    private $table_name = "coso";

    // Thuộc tính của Model, tương ứng với các cột trong bảng
    public $MaCoSo;
    public $TenCoSo;
    public $DiaChi;
    public $AnhUrl;
    public $DienThoai;

    public function __construct($db) {
        $this->conn = $db;
    }


    /**
     * Lấy tất cả cơ sở
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE TenCoSo != '' ORDER BY MaCoSo ASC";
        $result = mysqli_query($this->conn, $query);
        return $result;
    }

    /**
     * Lấy cơ sở theo địa chỉ (tìm kiếm quận trong địa chỉ)
     */
    public function getByAddress($address) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE DiaChi LIKE ? AND TenCoSo != '' ORDER BY MaCoSo ASC";
        $stmt = mysqli_prepare($this->conn, $query);
        $searchTerm = '%' . $address . '%';
        mysqli_stmt_bind_param($stmt, "s", $searchTerm);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    
    /**
     * Lấy tóm tắt địa chỉ
     */
    public function getAddressSummary() {
        $query = "SELECT DiaChi AS address, COUNT(*) AS count 
                  FROM " . $this->table_name . " 
                  GROUP BY DiaChi";
        $result = mysqli_query($this->conn, $query);
        return $result;
    }

    /**
     * Lấy cơ sở theo ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE MaCoSo = ? LIMIT 0,1";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if($row) {
        error_log("Executing query: " . $query . " with ID: " . $id);

            $this->MaCoSo = $row['MaCoSo'];
            $this->TenCoSo = $row['TenCoSo'];
            $this->DiaChi = $row['DiaChi'];
            $this->DienThoai = $row['DienThoai'];
            $this->AnhUrl = $row['AnhUrl'];
            
            return [
                'MaCoSo' => $this->MaCoSo,
                'TenCoSo' => $this->TenCoSo,
                'DiaChi' => $this->DiaChi,
                'DienThoai' => $this->DienThoai,
                'AnhUrl' => $this->AnhUrl
            ];
        }
        return false;
    }


    /**
     * Thêm cơ sở mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET TenCoSo=?, DiaChi=?, DienThoai=?, AnhUrl=?";

        $stmt = mysqli_prepare($this->conn, $query);

        // Làm sạch dữ liệu đầu vào
        $this->TenCoSo = htmlspecialchars(strip_tags($this->TenCoSo));
        $this->DiaChi = htmlspecialchars(strip_tags($this->DiaChi));
        $this->DienThoai = htmlspecialchars(strip_tags($this->DienThoai));
        $this->AnhUrl = htmlspecialchars(strip_tags($this->AnhUrl));

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssss", $this->TenCoSo, $this->DiaChi, $this->DienThoai, $this->AnhUrl);

        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        return false;
    }

    /**
     * Cập nhật cơ sở
     */
    public function update() {
        // SỬA LỖI: Bỏ dấu phẩy thừa trước AnhUrl và thêm MaCoSo vào bindParam
        $query = "UPDATE " . $this->table_name . " 
                 SET TenCoSo=?, DiaChi=?, DienThoai=?, AnhUrl=?
                 WHERE MaCoSo=?";

        $stmt = mysqli_prepare($this->conn, $query);

        // Làm sạch dữ liệu
        $this->MaCoSo = htmlspecialchars(strip_tags($this->MaCoSo));
        $this->TenCoSo = htmlspecialchars(strip_tags($this->TenCoSo));
        $this->DiaChi = htmlspecialchars(strip_tags($this->DiaChi));
        $this->DienThoai = htmlspecialchars(strip_tags($this->DienThoai));
        $this->AnhUrl = htmlspecialchars(strip_tags($this->AnhUrl));

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssssi", $this->TenCoSo, $this->DiaChi, $this->DienThoai, $this->AnhUrl, $this->MaCoSo);

        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        return false;
    }

    /**
     * Xóa cơ sở
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE MaCoSo = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        
        $this->MaCoSo = htmlspecialchars(strip_tags($this->MaCoSo));
        
        mysqli_stmt_bind_param($stmt, "i", $this->MaCoSo);

        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        return false;
    }
}
?>