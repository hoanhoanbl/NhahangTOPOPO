<?php

class BookingModel
{
    private $conn;
    private $table = 'dondatban';

    // Booking properties
    public $MaDon;
    public $MaKH;
    public $MaCoSo;
    public $ThoiGianBatDau;
    public $ThoiGianKetThuc;
    public $SoLuongKH;
    public $TrangThai;
    public $GhiChu;
    public $ThoiGianTao;
    public $MaNV_XacNhan;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy booking theo ID
    public function getById($maDon)
    {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE MaDon = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $maDon);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                $this->MaDon = $row['MaDon'];
                $this->MaKH = $row['MaKH'];
                $this->MaCoSo = $row['MaCoSo'];
                $this->ThoiGianBatDau = $row['ThoiGianBatDau'];
                $this->ThoiGianKetThuc = $row['ThoiGianKetThuc'];
                $this->SoLuongKH = $row['SoLuongKH'];
                $this->TrangThai = $row['TrangThai'];
                $this->GhiChu = $row['GhiChu'];
                $this->ThoiGianTao = $row['ThoiGianTao'];
                $this->MaNV_XacNhan = $row['MaNV_XacNhan'];
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in BookingModel::getById: " . $e->getMessage());
            return false;
        }
    }

    // Đếm tổng số đơn đặt bàn theo cơ sở
    public function countBookingsByBranch($maCoSo)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE MaCoSo = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error in BookingModel::countBookingsByBranch: " . $e->getMessage());
            return 0;
        }
    }
    public function countTodayBookingsByBranch($maCoSo)
    {
        try {
            $today = date('Y-m-d');
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE MaCoSo = ? AND DATE(ThoiGianBatDau) = ? AND TrangThai != 'hoan_thanh'";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "is", $maCoSo, $today);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error in BookingModel::countTodayBookingsByBranch: " . $e->getMessage());
            return 0;
        }
    }

    // Đếm đơn đặt trước
    public function countUpcomingBookingsByBranch($maCoSo)
    {
        try {
            $today = date('Y-m-d');
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE MaCoSo = ? AND DATE(ThoiGianBatDau) > ? AND TrangThai != 'hoan_thanh'";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "is", $maCoSo, $today);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error in BookingModel::countTodayBookingsByBranch: " . $e->getMessage());
            return 0;
        }
    }
    // Đếm đơn đã hoàn thành theo cơ sở
    public function countCompletedBookingsByBranch($maCoSo)
    {
        try {
            $today = date('Y-m-d');
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE MaCoSo = ? AND DATE(ThoiGianTao) = ? AND TrangThai = 'hoan_thanh'";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "is", $maCoSo, $today);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error in BookingModel::countTodayBookingsByBranch: " . $e->getMessage());
            return 0;
        }
    }

    // Đếm đơn chờ xác nhận theo cơ sở
    public function countPendingBookingsByBranch($maCoSo)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE MaCoSo = ? AND TrangThai = 'cho_xac_nhan'";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error in BookingModel::countPendingBookingsByBranch: " . $e->getMessage());
            return 0;
        }
    }

    // Đếm đơn đã xác nhận theo cơ sở
    public function countConfirmedBookingsByBranch($maCoSo)
    {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE MaCoSo = ? AND TrangThai = 'da_xac_nhan'";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error in BookingModel::countConfirmedBookingsByBranch: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy danh sách đơn đặt bàn theo cơ sở với filter
    public function getBookingsByBranch($maCoSo, $limit = 10, $offset = 0, $statusFilter = 'all', $timeFilter = 'hom_nay', $searchKeyword = '')
    {
        try {
            $whereConditions = ["d.MaCoSo = ?"];
            $params = [$maCoSo];
            $types = "i";

            // Filter theo trạng thái
            if ($statusFilter !== 'all') {
                $whereConditions[] = "d.TrangThai = ?";
                $params[] = $statusFilter;
                $types .= "s";
            }

            // Filter theo thời gian
            if ($timeFilter === 'hom_nay') {
                $whereConditions[] = "DATE(d.ThoiGianBatDau) = CURDATE()";
            } elseif ($timeFilter === 'dat_truoc') {
                $whereConditions[] = "DATE(d.ThoiGianBatDau) > CURDATE()";
            }

            // Search keyword
            if (!empty($searchKeyword)) {
                $whereConditions[] = "(kh.TenKH LIKE ? OR kh.SDT LIKE ? OR d.MaDon LIKE ?)";
                $searchTerm = "%$searchKeyword%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            $whereClause = implode(" AND ", $whereConditions);

            $query = "SELECT d.*, kh.TenKH, kh.SDT, kh.Email as EmailKH,
                             GROUP_CONCAT(CONCAT(b.TenBan, ' (', b.SucChua, ' người)') SEPARATOR ', ') as DanhSachBan
                      FROM " . $this->table . " d 
                      LEFT JOIN khachhang kh ON d.MaKH = kh.MaKH
                      LEFT JOIN dondatban_ban db ON d.MaDon = db.MaDon
                      LEFT JOIN ban b ON db.MaBan = b.MaBan
                      WHERE $whereClause
                      GROUP BY d.MaDon
                      ORDER BY d.ThoiGianTao DESC 
                      LIMIT ? OFFSET ?";

            // Thêm limit và offset vào params
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $bookings = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $bookings[] = $row;
            }
            
            return $bookings;

        } catch (Exception $e) {
            error_log("Error in BookingModel::getBookingsByBranch: " . $e->getMessage());
            return [];
        }
    }

    // Đếm số đơn đặt bàn theo cơ sở với filter
    public function countBookingsByBranchWithFilter($maCoSo, $statusFilter = 'all', $timeFilter = 'hom_nay', $searchKeyword = '')
    {
        try {
            $whereConditions = ["d.MaCoSo = ?"];
            $params = [$maCoSo];
            $types = "i";

            // Filter theo trạng thái
            if ($statusFilter !== 'all') {
                $whereConditions[] = "d.TrangThai = ?";
                $params[] = $statusFilter;
                $types .= "s";
            }

            // Filter theo thời gian
            if ($timeFilter === 'hom_nay') {
                $whereConditions[] = "DATE(d.ThoiGianBatDau) = CURDATE()";
            } elseif ($timeFilter === 'dat_truoc') {
                $whereConditions[] = "DATE(d.ThoiGianBatDau) > CURDATE()";
            }

            // Search keyword
            if (!empty($searchKeyword)) {
                $whereConditions[] = "(kh.TenKH LIKE ? OR kh.SDT LIKE ? OR d.MaDon LIKE ?)";
                $searchTerm = "%$searchKeyword%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            $whereClause = implode(" AND ", $whereConditions);

            $query = "SELECT COUNT(*) as total FROM " . $this->table . " d 
                     LEFT JOIN khachhang kh ON d.MaKH = kh.MaKH 
                     WHERE $whereClause";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result_set = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_assoc($result_set);
            return $result['total'];

        } catch (Exception $e) {
            error_log("Error in BookingModel::countBookingsByBranchWithFilter: " . $e->getMessage());
            return 0;
        }
    }

    // Cập nhật trạng thái đơn đặt bàn
public function updateStatus($maDon, $maCoSo, $status, $maNVXacNhan = null, $ghiChu = null)
{
    try {
        // Xây dựng câu lệnh query
        $query = "UPDATE " . $this->table . " SET TrangThai = ?";
        $params = [$status];
        $types = "s";

        if ($maNVXacNhan !== null) {
            $query .= ", MaNV_XacNhan = ?";
            $params[] = $maNVXacNhan;
            $types .= "i";
        }

        if (!empty($ghiChu)) {
            // Thêm ghi chú mới vào ghi chú cũ
            $query .= ", GhiChu = CONCAT(IFNULL(GhiChu, ''), ?)";
            $ghiChuFormatted = "\n[Lý do - " . date('d/m/Y H:i') . "]: " . $ghiChu;
            $params[] = $ghiChuFormatted;
            $types .= "s";
        }

        // THAY ĐỔI QUAN TRỌNG NHẤT: Thêm MaCoSo vào mệnh đề WHERE
        $query .= " WHERE MaDon = ? AND MaCoSo = ?";
        $params[] = $maDon;
        $params[] = $maCoSo;
        $types .= "ii";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        $result = mysqli_stmt_execute($stmt);

        // Trả về số dòng bị ảnh hưởng. Nếu là 0, tức là không update được (do sai MaDon hoặc sai MaCoSo)
        return mysqli_stmt_affected_rows($stmt);

    } catch (Exception $e) {
        error_log("Error in BookingModel::updateStatus: " . $e->getMessage());
        return false; // Trả về false nếu có lỗi exception
    }
}

    // Lấy chi tiết đơn đặt bàn với thông tin khách hàng và bàn
    public function getBookingDetail($maDon, $maCoSo = null)
    {
        try {
            $query = "SELECT d.*, kh.TenKH, kh.SDT, kh.Email as EmailKH,
                             GROUP_CONCAT(CONCAT(b.TenBan, ' (', b.SucChua, ' người)') SEPARATOR ', ') as DanhSachBan,
                             nv.TenNhanVien as NhanVienXacNhan,
                             cs.TenCoSo, cs.DiaChi as DiaChiCoSo
                      FROM " . $this->table . " d 
                      LEFT JOIN khachhang kh ON d.MaKH = kh.MaKH
                      LEFT JOIN dondatban_ban db ON d.MaDon = db.MaDon
                      LEFT JOIN ban b ON db.MaBan = b.MaBan
                      LEFT JOIN nhanvien nv ON d.MaNV_XacNhan = nv.MaNV
                      LEFT JOIN coso cs ON d.MaCoSo = cs.MaCoSo
                      WHERE d.MaDon = ?";
            
            $params = [$maDon];
            $types = "i";

            if ($maCoSo !== null) {
                $query .= " AND d.MaCoSo = ?";
                $params[] = $maCoSo;
                $types .= "i";
            }

            $query .= " GROUP BY d.MaDon";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            return mysqli_fetch_assoc($result);

        } catch (Exception $e) {
            error_log("Error in BookingModel::getBookingDetail: " . $e->getMessage());
            return false;
        }
    }

// LẤY DANH SÁCH MÓN ĂN cho một đơn đặt bàn.
public function getMenuItemsForBooking($maDon, $maCoSo)
{
    try {
        $query = "SELECT 
                    m.TenMon, 
                    dm.DonGia, /* Lấy giá đã lưu tại thời điểm đặt */
                    dm.SoLuong, 
                    (dm.DonGia * dm.SoLuong) as ThanhTien
                FROM chitietdondatban dm
                JOIN monan m ON dm.MaMon = m.MaMon
                WHERE dm.MaDon = ?
                ORDER BY m.TenMon";

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $maDon);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $menuItems = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $menuItems[] = $row;
        }
        
        return $menuItems;

    } catch (Exception $e) {
        error_log("Error in BookingModel::getMenuItemsForBooking: " . $e->getMessage());
        return false;
    }
}


// Tạo đơn đặt bàn với thông tin bàn
public function createBookingWithTables($TenKh, $SDT, $Email, $maCoSo, $maNV, $cartItems, $ghiChu = '', $bookingDate = '', $bookingTime = '', $numberOfGuests = 1, $selectedTables = [])
{
    // Bắt đầu transaction
    mysqli_begin_transaction($this->conn);

    try {
        // 1. Chuẩn bị thời gian đặt bàn
        $thoiGianBatDau = '';
        if ($bookingDate && $bookingTime){
            // Kiểm tra format của ngày từ frontend
            if (strpos($bookingDate, '/') !== false) {
                // Format dd/mm/yyyy - chuyển đổi sang yyyy-mm-dd
                $dateArray = explode('/', $bookingDate);
                if (count($dateArray) === 3) {
                    $formattedDate = $dateArray[2] . '-' . $dateArray[1]. '-' . $dateArray[0];
                    $thoiGianBatDau = $formattedDate . ' ' . $bookingTime . ':00';
                }
            } else {
                // Format yyyy-mm-dd - sử dụng trực tiếp
                $thoiGianBatDau = $bookingDate . ' ' . $bookingTime . ':00';
            }
        }
        
        if (empty($thoiGianBatDau)) {
            $thoiGianBatDau = date('Y-m-d H:i:s'); // Sử dụng thời gian hiện tại nếu không có
        }

        // 1. Tạo hoặc lấy khách hàng
        $maKH = null;
        
        // Kiểm tra xem khách hàng đã tồn tại chưa (dựa trên SDT nếu có, hoặc tên)
        if (!empty($SDT)) {
            error_log("----------------------------Debug phone: Checking existing customer by phone: $SDT");
            $checkQuery = "SELECT MaKH FROM khachhang WHERE SDT = ?";
            $checkStmt = mysqli_prepare($this->conn, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "s", $SDT);
            mysqli_stmt_execute($checkStmt);
            $result = mysqli_stmt_get_result($checkStmt);
            $existingCustomer = mysqli_fetch_assoc($result);
            
            if ($existingCustomer) {
                $maKH = $existingCustomer['MaKH'];
                error_log("----------------------------Debug: Existing customer check by phone executed. $maKH: " . print_r($existingCustomer, true));
            }
        }
        if(empty($TenKh) && empty($SDT)){
          $maKH = 2;
        }
        
        // Nếu chưa tồn tại, tạo khách hàng mới
        if ($maKH === null) {
            $insertQuery = "INSERT INTO khachhang (TenKH, SDT, Email) VALUES (?, ?, ?)";
            $insertStmt = mysqli_prepare($this->conn, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, "sss", $TenKh, $SDT, $Email);
            mysqli_stmt_execute($insertStmt);
            $maKH = mysqli_insert_id($this->conn);
        }
        
        // 2. Tạo bản ghi trong bảng `dondatban`
        $query = "INSERT INTO " . $this->table . "(MaKH, MaCoSo, MaNV_XacNhan, ThoiGianBatDau, ThoiGianTao, TrangThai, SoLuongKH, GhiChu) 
                  VALUES (?, ?, ?, ?, CONVERT_TZ(NOW(), '+00:00', '+07:00'), 'da_xac_nhan', ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "iiisis", $maKH, $maCoSo, $maNV, $thoiGianBatDau, $numberOfGuests, $ghiChu);
        mysqli_stmt_execute($stmt);
        
        $maDon = mysqli_insert_id($this->conn);

        // 3. Thêm các món ăn vào bảng `chitietdondatban`
        if (!empty($cartItems)) {
            $insertItemQuery = "INSERT INTO chitietdondatban (MaDon, MaMon, SoLuong, DonGia) 
                                VALUES (?, ?, ?, 
                                    (SELECT Gia FROM menu_coso WHERE MaMon = ? AND MaCoSo = ?)
                                )";
            $itemStmt = mysqli_prepare($this->conn, $insertItemQuery);

            foreach ($cartItems as $item) {
                mysqli_stmt_bind_param($itemStmt, "iiiii", $maDon, $item['id'], $item['quantity'], $item['id'], $maCoSo);
                
                if (!mysqli_stmt_execute($itemStmt)) {
                    throw new Exception('Không thể thêm món ăn vào đơn hàng.');
                }
            }
        }

        // 4. Thêm thông tin bàn vào bảng `dondatban_ban`
        if (!empty($selectedTables)) {
            $insertTableQuery = "INSERT INTO dondatban_ban (MaDon, MaBan) VALUES (?, ?)";
            $tableStmt = mysqli_prepare($this->conn, $insertTableQuery);

            foreach ($selectedTables as $table) {
                $maBan = $table['maBan'];
                mysqli_stmt_bind_param($tableStmt, "ii", $maDon, $maBan);
                
                if (!mysqli_stmt_execute($tableStmt)) {
                    throw new Exception('Không thể thêm thông tin bàn vào đơn đặt bàn.');
                }
            }
        }

        mysqli_commit($this->conn);
        return $maDon;
    } catch (Exception $e) {
        mysqli_rollback($this->conn);
        error_log("Error in BookingModel::createBookingWithTables: " . $e->getMessage());
        error_log("Error details - Customer: $TenKh, Phone: $SDT, CoSo: $maCoSo, NV: $maNV");
        error_log("Error details - Cart items count: " . count($cartItems));
        error_log("Error details - Selected tables count: " . count($selectedTables));
        return false;
    }
}

    // Chuyển object thành array
    public function toArray()
    {
        return [
            'MaDon' => $this->MaDon,
            'MaKH' => $this->MaKH,
            'MaCoSo' => $this->MaCoSo,
            'ThoiGianBatDau' => $this->ThoiGianBatDau,
            'ThoiGianKetThuc' => $this->ThoiGianKetThuc,
            'SoLuongKH' => $this->SoLuongKH,
            'TrangThai' => $this->TrangThai,
            'GhiChu' => $this->GhiChu,
            'ThoiGianTao' => $this->ThoiGianTao,
            'MaNV_XacNhan' => $this->MaNV_XacNhan
        ];
    }
}

?>