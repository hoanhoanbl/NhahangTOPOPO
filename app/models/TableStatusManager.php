<?php
require_once __DIR__ . '/../../config/config.php'; // Äá»c env vÃ  helper

class TableStatusManager {
    private $conn;

    public function __construct($connection = null) {
        if ($connection) {
            $this->conn = $connection;
        } else {
            $this->conn = self::getConnection();
        }
    }
    
    /**
     * Láº¥y káº¿t ná»‘i database
     * @return mysqli
     */
    private static function getConnection() {
        // Äá»c cáº¥u hÃ¬nh DB tá»« biáº¿n mÃ´i trÆ°á»ng (.env) vá»›i giÃ¡ trá»‹ máº·c Ä‘á»‹nh
        $host = env('DB_HOST', 'localhost');
        $user = env('DB_USER', 'root');
        $pass = env('DB_PASS', '');
        $database = env('DB_NAME', 'booking_restaurant');
        $port = env('DB_PORT', '3306');

        $conn = mysqli_connect($host, $user, $pass, $database, $port);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        mysqli_set_charset($conn, "utf8mb4");
        mysqli_query($conn, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        mysqli_query($conn, "SET time_zone = '+07:00'");

        
        return $conn;
    }
    
    /**
     * Kiá»ƒm tra tráº¡ng thÃ¡i bÃ n dá»±a vÃ o thá»i gian báº¯t Ä‘áº§u Ä‘áº·t bÃ n
     * BÃ n Ä‘Æ°á»£c coi lÃ  Ä‘Ã£ Ä‘áº·t náº¿u cÃ³ Ä‘Æ¡n Ä‘áº·t trong khoáº£ng 2 giá» tá»›i
     * @param int $maBan MÃ£ bÃ n
     * @return string 'trong' hoáº·c 'da_dat'
     */
    public static function kiemTraTrangThaiBan($maBan) { 
        $conn = self::getConnection();

        // TÃ­nh thá»i gian hiá»‡n táº¡i + 2 giá»
        $thoiGianHienTai = date('Y-m-d H:i:s');
        $thoiGianCong2Gio = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $sql = "SELECT COUNT(*) as so_don_dat
                FROM dondatban_ban dbb
                JOIN dondatban dd ON dbb.MaDon = dd.MaDon
                WHERE dbb.MaBan = ?
                AND dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                AND dd.ThoiGianBatDau <= ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $maBan, $thoiGianCong2Gio);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['so_don_dat'] > 0 ? 'da_dat' : 'trong';
    }

    /**
     * Láº¥y danh sÃ¡ch bÃ n theo cÆ¡ sá»Ÿ vá»›i tráº¡ng thÃ¡i dá»±a vÃ o thá»i gian Ä‘áº·t bÃ n
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @return array Danh sÃ¡ch bÃ n vá»›i tráº¡ng thÃ¡i
     */
    public static function layBanTheoCoSo($maCoSo) {
        $conn = self::getConnection();

        // TÃ­nh thá»i gian hiá»‡n táº¡i + 2 giá»
        $thoiGianCong2Gio = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $sql = "SELECT b.*,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM dondatban_ban dbb
                        JOIN dondatban dd ON dbb.MaDon = dd.MaDon
                        WHERE dbb.MaBan = b.MaBan
                        AND dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                        AND dd.ThoiGianBatDau <= ?
                    ) THEN 'da_dat'
                    ELSE 'trong'
                END as TrangThai
                FROM ban b
                WHERE b.MaCoSo = ?
                ORDER BY b.MaBan";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $thoiGianCong2Gio, $maCoSo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $banList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $banList[] = $row;
        }

        return $banList;
    }

    /**
     * Cáº­p nháº­t tráº¡ng thÃ¡i bÃ n - táº¡o hoáº·c xÃ³a Ä‘Æ¡n Ä‘áº·t bÃ n admin Ä‘á»ƒ Ä‘Ã¡nh dáº¥u tráº¡ng thÃ¡i
     * @param int $maBan MÃ£ bÃ n
     * @param string $trangThai Tráº¡ng thÃ¡i ('trong' hoáº·c 'da_dat')
     * @return bool
     */
    public static function capNhatTrangThaiBan($maBan, $trangThai) {
        $conn = self::getConnection();

        if ($trangThai == 'da_dat') {
         

            // Láº¥y MaCoSo tá»« bÃ n
            $sqlGetCoSo = "SELECT MaCoSo FROM ban WHERE MaBan = ?";
            $stmtGetCoSo = mysqli_prepare($conn, $sqlGetCoSo);
            mysqli_stmt_bind_param($stmtGetCoSo, "i", $maBan);
            mysqli_stmt_execute($stmtGetCoSo);
            $result = mysqli_stmt_get_result($stmtGetCoSo);
            $ban = mysqli_fetch_assoc($result);
            $maCoSo = $ban['MaCoSo'];

            // Táº¡o hoáº·c láº¥y khÃ¡ch hÃ ng admin
            $maKH = self::getOrCreateAdminCustomer($conn);

            // Táº¡o Ä‘Æ¡n Ä‘áº·t bÃ n admin Ä‘á»ƒ Ä‘Ã¡nh dáº¥u bÃ n Ä‘Ã£ Ä‘áº·t
            $sql = "INSERT INTO dondatban (MaKH, MaCoSo, SoLuongKH, ThoiGianBatDau, ThoiGianTao, TrangThai, GhiChu) 
                    VALUES (?, ?, 1, NOW(), NOW(), 'da_xac_nhan', 'Admin Ä‘Ã¡nh dáº¥u bÃ n Ä‘Ã£ Ä‘áº·t')";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $maKH, $maCoSo);
            mysqli_stmt_execute($stmt);
            $maDon = mysqli_insert_id($conn);

            // ThÃªm bÃ n vÃ o Ä‘Æ¡n Ä‘áº·t
            $sql2 = "INSERT INTO dondatban_ban (MaDon, MaBan) VALUES (?, ?)";
            $stmt2 = mysqli_prepare($conn, $sql2);
            mysqli_stmt_bind_param($stmt2, "ii", $maDon, $maBan);
            return mysqli_stmt_execute($stmt2);
        } 
        else {
            // XÃ³a táº¥t cáº£ cÃ¡c Ä‘Æ¡n Ä‘áº·t bÃ n (cáº£ admin vÃ  nhÃ¢n viÃªn) Ä‘á»ƒ Ä‘Ã¡nh dáº¥u bÃ n trá»‘ng
            mysqli_begin_transaction($conn);
            
            try {
                // Láº¥y danh sÃ¡ch MaDon cáº§n xÃ³a
                $getMaDonSql = "SELECT DISTINCT dd.MaDon 
                               FROM dondatban dd
                               JOIN dondatban_ban dbb ON dd.MaDon = dbb.MaDon
                               WHERE dbb.MaBan = ? 
                               AND dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')";
                $getMaDonStmt = mysqli_prepare($conn, $getMaDonSql);
                mysqli_stmt_bind_param($getMaDonStmt, "i", $maBan);
                mysqli_stmt_execute($getMaDonStmt);
                $result = mysqli_stmt_get_result($getMaDonStmt);
                
                $maDonList = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $maDonList[] = $row['MaDon'];
                }
                
                if (!empty($maDonList)) {
                    $placeholders = str_repeat('?,', count($maDonList) - 1) . '?';
                   
                    // XÃ³a dondatban_ban
                    $deleteBanSql = "DELETE FROM dondatban_ban WHERE MaDon IN ($placeholders)";
                    $deleteBanStmt = mysqli_prepare($conn, $deleteBanSql);
                    mysqli_stmt_bind_param($deleteBanStmt, str_repeat('i', count($maDonList)), ...$maDonList);
                    mysqli_stmt_execute($deleteBanStmt);
                }
                
                mysqli_commit($conn);
                return true;
            } catch (Exception $e) {
                mysqli_rollback($conn);
                return false;
            }
        }
    }


    /**
     * Láº¥y danh sÃ¡ch cÆ¡ sá»Ÿ
     * @return array
     */
    public static function layDanhSachCoSo() {
        $conn = self::getConnection();
        
        $sql = "SELECT * FROM coso ORDER BY TenCoSo";
        $result = mysqli_query($conn, $sql);
        
        $coSoList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $coSoList[] = $row;
        }
        
        return $coSoList;
    }

    /**
     * Láº¥y thÃ´ng tin cÆ¡ sá»Ÿ theo mÃ£ cÆ¡ sá»Ÿ
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @return array|null ThÃ´ng tin cÆ¡ sá»Ÿ
     */
    public static function layThongTinCoSo($maCoSo) {
        $conn = self::getConnection();
        
        $sql = "SELECT * FROM coso WHERE MaCoSo = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $maCoSo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Láº¥y thÃ´ng tin cÆ¡ báº£n cá»§a bÃ n
     * @param int $maBan MÃ£ bÃ n
     * @return array|null ThÃ´ng tin bÃ n
     */
    public static function layThongTinBan($maBan) {
        $conn = self::getConnection();
        
        $sql = "SELECT b.*, c.TenCoSo 
                FROM ban b 
                JOIN coso c ON b.MaCoSo = c.MaCoSo 
                WHERE b.MaBan = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $maBan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Láº¥y thÃ´ng tin chi tiáº¿t cá»§a bÃ n bao gá»“m tráº¡ng thÃ¡i hiá»‡n táº¡i
     * @param int $maBan MÃ£ bÃ n
     * @return array|null ThÃ´ng tin bÃ n chi tiáº¿t
     */
    public static function layThongTinBanChiTiet($maBan) {
        $conn = self::getConnection();
        
        $sql = "SELECT b.*, c.TenCoSo,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM dondatban_ban dbb
                        JOIN dondatban dd ON dbb.MaDon = dd.MaDon
                        WHERE dbb.MaBan = b.MaBan
                        AND dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                    ) THEN 'da_dat'
                    ELSE 'trong'
                END as TrangThaiHienTai
                FROM ban b 
                JOIN coso c ON b.MaCoSo = c.MaCoSo 
                WHERE b.MaBan = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $maBan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    // =================================================================
    // CÃC HÃ€M ÄÆ¯á»¢C Gá»˜PVÃ€O Tá»ª TableModel.php
    // =================================================================

    /**
     * Láº¥y danh sÃ¡ch bÃ n trá»‘ng cá»§a cÆ¡ sá»Ÿ khi táº¡o Ä‘Æ¡n Ä‘áº·t bÃ n (tá»« TableModel)
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @param string $ngayDat NgÃ y Ä‘áº·t (Y-m-d)
     * @param string $gioDat Giá» Ä‘áº·t (H:i)
     * @param int $soNguoi Sá»‘ ngÆ°á»i
     * @return array Danh sÃ¡ch bÃ n trá»‘ng
     */
    public static function layBanTrong($maCoSo, $ngayDat, $gioDat, $soNguoi = 1) {
        $conn = self::getConnection();
        
        try {
            // Láº¥y táº¥t cáº£ bÃ n cá»§a cÆ¡ sá»Ÿ
            $sql = "SELECT MaBan, TenBan, SucChua FROM ban WHERE MaCoSo = ? AND SucChua >= ? ORDER BY TenBan";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $maCoSo, $soNguoi);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $allTables = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $allTables[] = $row;
            }
            
            if (empty($allTables)) {
                return [];
            }
            
            // Láº¥y danh sÃ¡ch bÃ n Ä‘Ã£ Ä‘Æ°á»£c Ä‘áº·t trong khoáº£ng thá»i gian
            $bookedTables = self::layBanDaDat($maCoSo, $ngayDat, $gioDat);
            
            // Lá»c bá» cÃ¡c bÃ n Ä‘Ã£ Ä‘Æ°á»£c Ä‘áº·t
            $availableTables = [];
            foreach ($allTables as $table) {
                if (!in_array($table['MaBan'], $bookedTables)) {
                    $availableTables[] = $table;
                }
            }
            
            return $availableTables;
            
        } catch (Exception $e) {
            error_log("Error in layBanTrong: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Láº¥y danh sÃ¡ch bÃ n Ä‘Ã£ Ä‘Æ°á»£c Ä‘áº·t trong khoáº£ng thá»i gian (Â±2 giá») (tá»« TableModel)
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @param string $ngayDat NgÃ y Ä‘áº·t (Y-m-d)
     * @param string $gioDat Giá» Ä‘áº·t (H:i)
     * @return array Danh sÃ¡ch mÃ£ bÃ n Ä‘Ã£ Ä‘áº·t
     */
    public static function layBanDaDat($maCoSo, $ngayDat, $gioDat) {
        $conn = self::getConnection();
        
        try {
            // TÃ­nh toÃ¡n khoáº£ng thá»i gian xung Ä‘á»™t (Â±2 giá»)
            $timeStart = date('H:i', strtotime($gioDat . ' -2 hours'));
            $timeEnd = date('H:i', strtotime($gioDat . ' +2 hours'));
            
            $sql = "SELECT DISTINCT ddb.MaBan 
                   FROM dondatban ddb 
                   INNER JOIN ban b ON ddb.MaBan = b.MaBan 
                   WHERE b.MaCoSo = ? 
                   AND DATE(ddb.ThoiGianDat) = ? 
                   AND (
                       (TIME(ddb.ThoiGianDat) BETWEEN ? AND ?) OR
                       (TIME(ddb.ThoiGianDat) = ?)
                   )
                   AND ddb.TrangThai NOT IN ('da_huy', 'hoan_thanh')";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issss", $maCoSo, $ngayDat, $timeStart, $timeEnd, $gioDat);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $bookedTables = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $bookedTables[] = $row['MaBan'];
            }
            
            return $bookedTables;
            
        } catch (Exception $e) {
            error_log("Error in layBanDaDat: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiá»ƒm tra xem bÃ n cÃ³ sáºµn vÃ o thá»i Ä‘iá»ƒm cá»¥ thá»ƒ khÃ´ng (tá»« TableModel)
     * @param int $maBan MÃ£ bÃ n
     * @param string $ngayDat NgÃ y Ä‘áº·t (Y-m-d)
     * @param string $gioDat Giá» Ä‘áº·t (H:i)
     * @return bool True náº¿u bÃ n cÃ³ sáºµn
     */
    public static function kiemTraBanCoSan($maBan, $ngayDat, $gioDat) {
        try {
            // Láº¥y thÃ´ng tin bÃ n Ä‘á»ƒ biáº¿t cÆ¡ sá»Ÿ
            $tableInfo = self::layThongTinBan($maBan);
            if (!$tableInfo) {
                return false;
            }
            
            $bookedTables = self::layBanDaDat($tableInfo['MaCoSo'], $ngayDat, $gioDat);
            
            return !in_array($maBan, $bookedTables);
            
        } catch (Exception $e) {
            error_log("Error in kiemTraBanCoSan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Láº¥y táº¥t cáº£ bÃ n cá»§a má»™t cÆ¡ sá»Ÿ (tá»« TableModel) - tÆ°Æ¡ng tá»± layBanTheoCoSo nhÆ°ng khÃ´ng cÃ³ tráº¡ng thÃ¡i
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @return array Danh sÃ¡ch bÃ n
     */
    public static function layTatCaBanTheoCoSo($maCoSo) {
        $conn = self::getConnection();
        
        try {
            $sql = "SELECT MaBan, TenBan, SucChua FROM ban WHERE MaCoSo = ? ORDER BY TenBan";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $tables = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $tables[] = $row;
            }
            
            return $tables;
            
        } catch (Exception $e) {
            error_log("Error in layTatCaBanTheoCoSo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Láº¥y danh sÃ¡ch bÃ n trá»‘ng theo logic thá»i gian thá»±c (khÃ´ng cÃ³ Ä‘Æ¡n Ä‘áº·t trong vÃ²ng 2 giá» tá»›i)
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @return array Danh sÃ¡ch bÃ n trá»‘ng
     */
    public static function layBanTrongTheoThoiGian($maCoSo) {
        $conn = self::getConnection();
        
        try {
            // TÃ­nh thá»i gian hiá»‡n táº¡i + 2 giá»
            $thoiGianCong2Gio = date('Y-m-d H:i:s', strtotime('+2 hours'));
            
            $sql = "SELECT b.MaBan, b.TenBan, b.SucChua 
                   FROM ban b 
                   WHERE b.MaCoSo = ? 
                   AND NOT EXISTS (
                       SELECT 1
                       FROM dondatban_ban dbb
                       JOIN dondatban dd ON dbb.MaDon = dd.MaDon
                       WHERE dbb.MaBan = b.MaBan
                       AND dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                       AND dd.ThoiGianBatDau <= ?
                   )
                   ORDER BY b.TenBan";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $maCoSo, $thoiGianCong2Gio);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $tables = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $tables[] = $row;
            }
            
            return $tables;
            
        } catch (Exception $e) {
            error_log("Error in layBanTrongTheoThoiGian: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Láº¥y danh sÃ¡ch bÃ n khÃ´ng cÃ³ trong dondatban_ban (tá»« TableModel)
     * @param int $maCoSo MÃ£ cÆ¡ sá»Ÿ
     * @return array Danh sÃ¡ch bÃ n chÆ°a Ä‘Æ°á»£c Ä‘áº·t
     */
    public static function layBanChuaDuocDat($maCoSo) {
        $conn = self::getConnection();
        
        try {
            $sql = "SELECT b.MaBan, b.TenBan, b.SucChua 
                   FROM ban b 
                   LEFT JOIN dondatban_ban ddb ON b.MaBan = ddb.MaBan 
                   WHERE b.MaCoSo = ? AND ddb.MaBan IS NULL 
                   ORDER BY b.TenBan";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $tables = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $tables[] = $row;
            }
            
            return $tables;
            
        } catch (Exception $e) {
            error_log("Error in layBanChuaDuocDat: " . $e->getMessage());
            return [];
        }
    }

    /**
     * HÃ m helper Ä‘á»ƒ táº¡o hoáº·c láº¥y khÃ¡ch hÃ ng admin (cáº§n thiáº¿t cho capNhatTrangThaiBan)
     */
    private static function getOrCreateAdminCustomer($conn) {
        // Kiá»ƒm tra xem Ä‘Ã£ cÃ³ khÃ¡ch hÃ ng admin chÆ°a
        $sql = "SELECT MaKH FROM khachhang WHERE TenKH = 'Admin System' AND Email = 'admin@system.local'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['MaKH'];
        } else {
            // Táº¡o khÃ¡ch hÃ ng admin má»›i
            $sql = "INSERT INTO khachhang (TenKH, Email, SDT) VALUES ('Admin System', 'admin@system.local', '0000000000')";
            mysqli_query($conn, $sql);
            return mysqli_insert_id($conn);
        }
    }

    // XÃ³a cÃ¡c Ä‘Æ¡n Ä‘áº·t bÃ n quÃ¡ háº¡n thá»i gian
    public static function xoaDonDatBanQuaHan($maCoSo = null) {
        $conn = self::getConnection();
        
        try {
            mysqli_begin_transaction($conn);
            
            // TÃ­nh thá»i gian háº¿t háº¡n (1 phÃºt trÆ°á»›c thá»i Ä‘iá»ƒm hiá»‡n táº¡i)
            $currentTime = date('Y-m-d H:i:s');
            
            // TÃ¬m cÃ¡c Ä‘Æ¡n Ä‘áº·t bÃ n quÃ¡ háº¡n (Ä‘Ã£ qua thá»i gian báº¯t Ä‘áº§u hÆ¡n 60 giÃ¢y)
            $sql = "SELECT dd.MaDon, dd.ThoiGianBatDau, dd.MaCoSo, cs.TenCoSo
                    FROM dondatban dd
                    JOIN coso cs ON dd.MaCoSo = cs.MaCoSo
                    WHERE dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                    AND TIMESTAMPDIFF(SECOND, dd.ThoiGianBatDau, NOW()) > 30";
            
            
            // Náº¿u cÃ³ mÃ£ cÆ¡ sá»Ÿ cá»¥ thá»ƒ
            if ($maCoSo !== null) {
                $sql .= " AND dd.MaCoSo = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $maCoSo);
            } else {
                $stmt = mysqli_prepare($conn, $sql);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $expiredOrders = [];
            $maDonList = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $expiredOrders[] = $row;
                $maDonList[] = $row['MaDon'];
            }
            
            if (empty($maDonList)) {
                mysqli_commit($conn);
                return [
                    'success' => true,
                    'deleted_count' => 0,
                    'message' => 'KhÃ´ng cÃ³ Ä‘Æ¡n Ä‘áº·t bÃ n nÃ o quÃ¡ háº¡n'
                ];
            }
            
            // Táº¡o placeholders cho IN clause
            $placeholders = str_repeat('?,', count($maDonList) - 1) . '?';
            $paramTypes = str_repeat('i', count($maDonList));
            
           
            
            // Cáº­p nháº­t tráº¡ng thÃ¡i Ä‘Æ¡n Ä‘áº·t bÃ n thÃ nh 'da_huy' thay vÃ¬ xÃ³a hoÃ n toÃ n
            $updateDonSql = "UPDATE dondatban 
                            SET TrangThai = 'hoan_thanh', 
                                GhiChu = CONCAT(IFNULL(GhiChu, ''), ' [Tá»± Ä‘á»™ng há»§y do quÃ¡ háº¡n]')
                            WHERE MaDon IN ($placeholders)";
            $updateDonStmt = mysqli_prepare($conn, $updateDonSql);
            mysqli_stmt_bind_param($updateDonStmt, $paramTypes, ...$maDonList);
            mysqli_stmt_execute($updateDonStmt);
            $updatedDon = mysqli_stmt_affected_rows($updateDonStmt);
            
            mysqli_commit($conn);

            $deletedBanSql = "DELETE FROM dondatban_ban WHERE MaDon IN ($placeholders)";
            $deletedBanStmt = mysqli_prepare($conn, $deletedBanSql);
            mysqli_stmt_bind_param($deletedBanStmt, $paramTypes, ...$maDonList);
            mysqli_stmt_execute($deletedBanStmt);
            $deletedBan = mysqli_stmt_affected_rows($deletedBanStmt);

            mysqli_commit($conn);

            // Log thÃ´ng tin cleanup
            // error_log("TableStatusManager: ÄÃ£ cleanup " . count($maDonList) . " Ä‘Æ¡n Ä‘áº·t bÃ n quÃ¡ háº¡n");
            // foreach ($expiredOrders as $order) {
            //     error_log("- MaDon: {$order['MaDon']}, ThoiGianBatDau: {$order['ThoiGianBatDau']}, CoSo: {$order['TenCoSo']}");
            // }
            
            return [
                'success' => true,
                'deleted_count' => count($maDonList),
                'expired_orders' => $expiredOrders,
                'details' => [
                    'ban_deleted' => $deletedBan,
                    'don_updated' => $updatedDon
                ],
                'message' => "ÄÃ£ cleanup " . count($maDonList) . " Ä‘Æ¡n Ä‘áº·t bÃ n quÃ¡ háº¡n"
            ];
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            error_log("Error in xoaDonDatBanQuaHan: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'CÃ³ lá»—i xáº£y ra khi cleanup Ä‘Æ¡n Ä‘áº·t bÃ n quÃ¡ háº¡n'
            ];
        }
    }

    
}
