<?php
require_once __DIR__ . '/BookingModel.php';
require_once __DIR__ . '/../../config/config.php'; // Ã„ÂÃ¡Â»Âc env vÃƒÂ  helper

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
     * LÃ¡ÂºÂ¥y kÃ¡ÂºÂ¿t nÃ¡Â»â€˜i database
     * @return mysqli
     */
    private static function getConnection() {
        // Ã„ÂÃ¡Â»Âc cÃ¡ÂºÂ¥u hÃƒÂ¬nh DB tÃ¡Â»Â« biÃ¡ÂºÂ¿n mÃƒÂ´i trÃ†Â°Ã¡Â»Âng (.env) vÃ¡Â»â€ºi giÃƒÂ¡ trÃ¡Â»â€¹ mÃ¡ÂºÂ·c Ã„â€˜Ã¡Â»â€¹nh
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
     * KiÃ¡Â»Æ’m tra trÃ¡ÂºÂ¡ng thÃƒÂ¡i bÃƒÂ n dÃ¡Â»Â±a vÃƒÂ o thÃ¡Â»Âi gian bÃ¡ÂºÂ¯t Ã„â€˜Ã¡ÂºÂ§u Ã„â€˜Ã¡ÂºÂ·t bÃƒÂ n
     * BÃƒÂ n Ã„â€˜Ã†Â°Ã¡Â»Â£c coi lÃƒÂ  Ã„â€˜ÃƒÂ£ Ã„â€˜Ã¡ÂºÂ·t nÃ¡ÂºÂ¿u cÃƒÂ³ Ã„â€˜Ã†Â¡n Ã„â€˜Ã¡ÂºÂ·t trong khoÃ¡ÂºÂ£ng 2 giÃ¡Â»Â tÃ¡Â»â€ºi
     * @param int $maBan MÃƒÂ£ bÃƒÂ n
     * @return string 'trong' hoÃ¡ÂºÂ·c 'da_dat'
     */
    public static function kiemTraTrangThaiBan($maBan) { 
        $conn = self::getConnection();

        // TÃƒÂ­nh thÃ¡Â»Âi gian hiÃ¡Â»â€¡n tÃ¡ÂºÂ¡i + 2 giÃ¡Â»Â
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
     * LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch bÃƒÂ n theo cÃ†Â¡ sÃ¡Â»Å¸ vÃ¡Â»â€ºi trÃ¡ÂºÂ¡ng thÃƒÂ¡i dÃ¡Â»Â±a vÃƒÂ o thÃ¡Â»Âi gian Ã„â€˜Ã¡ÂºÂ·t bÃƒÂ n
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @return array Danh sÃƒÂ¡ch bÃƒÂ n vÃ¡Â»â€ºi trÃ¡ÂºÂ¡ng thÃƒÂ¡i
     */
    public static function layBanTheoCoSo($maCoSo) {
        $conn = self::getConnection();

        // TÃƒÂ­nh thÃ¡Â»Âi gian hiÃ¡Â»â€¡n tÃ¡ÂºÂ¡i + 2 giÃ¡Â»Â
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
     * CÃ¡ÂºÂ­p nhÃ¡ÂºÂ­t trÃ¡ÂºÂ¡ng thÃƒÂ¡i bÃƒÂ n - tÃ¡ÂºÂ¡o hoÃ¡ÂºÂ·c xÃƒÂ³a Ã„â€˜Ã†Â¡n Ã„â€˜Ã¡ÂºÂ·t bÃƒÂ n admin Ã„â€˜Ã¡Â»Æ’ Ã„â€˜ÃƒÂ¡nh dÃ¡ÂºÂ¥u trÃ¡ÂºÂ¡ng thÃƒÂ¡i
     * @param int $maBan MÃƒÂ£ bÃƒÂ n
     * @param string $trangThai TrÃ¡ÂºÂ¡ng thÃƒÂ¡i ('trong' hoÃ¡ÂºÂ·c 'da_dat')
     * @return bool
     */
    public static function capNhatTrangThaiBan($maBan, $trangThai) {
        $conn = self::getConnection();
        $bookingModel = new BookingModel($conn);

        if ($trangThai === 'da_dat') {
            $sqlGetCoSo = "SELECT MaCoSo FROM ban WHERE MaBan = ?";
            $stmtGetCoSo = mysqli_prepare($conn, $sqlGetCoSo);
            mysqli_stmt_bind_param($stmtGetCoSo, "i", $maBan);
            mysqli_stmt_execute($stmtGetCoSo);
            $result = mysqli_stmt_get_result($stmtGetCoSo);
            $ban = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmtGetCoSo);

            $maCoSo = isset($ban['MaCoSo']) ? (int)$ban['MaCoSo'] : 0;
            if ($maCoSo <= 0) {
                return false;
            }

            $maKH = self::getOrCreateAdminCustomer($conn);
            $maDon = $bookingModel->createBookingRecord([
                'maKH' => $maKH,
                'maCoSo' => $maCoSo,
                'soLuongKH' => 1,
                'thoiGianBatDau' => date('Y-m-d H:i:s'),
                'ghiChu' => 'Admin danh dau ban da dat',
                'status' => 'da_xac_nhan',
                'selectedTables' => [$maBan],
                'actor_type' => 'system',
                'actor_name' => 'Admin',
                'source' => 'table_status_manual_book',
            ]);

            return (bool)$maDon;
        }

        if ($trangThai === 'trong') {
            $query = "SELECT DISTINCT dd.MaDon, dd.MaCoSo
                      FROM dondatban dd
                      JOIN dondatban_ban dbb ON dd.MaDon = dbb.MaDon
                      WHERE dbb.MaBan = ?
                        AND dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $maBan);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $activeBookings = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $activeBookings[] = $row;
            }
            mysqli_stmt_close($stmt);

            foreach ($activeBookings as $booking) {
                $changeResult = $bookingModel->changeStatus(
                    (int)$booking['MaDon'],
                    (int)$booking['MaCoSo'],
                    'hoan_thanh',
                    [
                        'actor_type' => 'system',
                        'actor_name' => 'Admin',
                        'note' => 'Giai phong ban thu cong tu man trang thai ban.',
                        'source' => 'table_status_manual_release',
                        'metadata' => [
                            'tableId' => (int)$maBan,
                        ],
                    ]
                );

                if (empty($changeResult['success'])) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch cÃ†Â¡ sÃ¡Â»Å¸
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
     * LÃ¡ÂºÂ¥y thÃƒÂ´ng tin cÃ†Â¡ sÃ¡Â»Å¸ theo mÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @return array|null ThÃƒÂ´ng tin cÃ†Â¡ sÃ¡Â»Å¸
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
     * LÃ¡ÂºÂ¥y thÃƒÂ´ng tin cÃ†Â¡ bÃ¡ÂºÂ£n cÃ¡Â»Â§a bÃƒÂ n
     * @param int $maBan MÃƒÂ£ bÃƒÂ n
     * @return array|null ThÃƒÂ´ng tin bÃƒÂ n
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
     * LÃ¡ÂºÂ¥y thÃƒÂ´ng tin chi tiÃ¡ÂºÂ¿t cÃ¡Â»Â§a bÃƒÂ n bao gÃ¡Â»â€œm trÃ¡ÂºÂ¡ng thÃƒÂ¡i hiÃ¡Â»â€¡n tÃ¡ÂºÂ¡i
     * @param int $maBan MÃƒÂ£ bÃƒÂ n
     * @return array|null ThÃƒÂ´ng tin bÃƒÂ n chi tiÃ¡ÂºÂ¿t
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
    // CÃƒÂC HÃƒâ‚¬M Ã„ÂÃ†Â¯Ã¡Â»Â¢C GÃ¡Â»ËœPVÃƒâ‚¬O TÃ¡Â»Âª TableModel.php
    // =================================================================

    /**
     * LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch bÃƒÂ n trÃ¡Â»â€˜ng cÃ¡Â»Â§a cÃ†Â¡ sÃ¡Â»Å¸ khi tÃ¡ÂºÂ¡o Ã„â€˜Ã†Â¡n Ã„â€˜Ã¡ÂºÂ·t bÃƒÂ n (tÃ¡Â»Â« TableModel)
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @param string $ngayDat NgÃƒÂ y Ã„â€˜Ã¡ÂºÂ·t (Y-m-d)
     * @param string $gioDat GiÃ¡Â»Â Ã„â€˜Ã¡ÂºÂ·t (H:i)
     * @param int $soNguoi SÃ¡Â»â€˜ ngÃ†Â°Ã¡Â»Âi
     * @return array Danh sÃƒÂ¡ch bÃƒÂ n trÃ¡Â»â€˜ng
     */
    public static function layBanTrong($maCoSo, $ngayDat, $gioDat, $soNguoi = 1) {
        $conn = self::getConnection();
        
        try {
            // LÃ¡ÂºÂ¥y tÃ¡ÂºÂ¥t cÃ¡ÂºÂ£ bÃƒÂ n cÃ¡Â»Â§a cÃ†Â¡ sÃ¡Â»Å¸
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
            
            // LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch bÃƒÂ n Ã„â€˜ÃƒÂ£ Ã„â€˜Ã†Â°Ã¡Â»Â£c Ã„â€˜Ã¡ÂºÂ·t trong khoÃ¡ÂºÂ£ng thÃ¡Â»Âi gian
            $bookedTables = self::layBanDaDat($maCoSo, $ngayDat, $gioDat);
            
            // LÃ¡Â»Âc bÃ¡Â»Â cÃƒÂ¡c bÃƒÂ n Ã„â€˜ÃƒÂ£ Ã„â€˜Ã†Â°Ã¡Â»Â£c Ã„â€˜Ã¡ÂºÂ·t
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
     * LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch bÃƒÂ n Ã„â€˜ÃƒÂ£ Ã„â€˜Ã†Â°Ã¡Â»Â£c Ã„â€˜Ã¡ÂºÂ·t trong khoÃ¡ÂºÂ£ng thÃ¡Â»Âi gian (Ã‚Â±2 giÃ¡Â»Â) (tÃ¡Â»Â« TableModel)
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @param string $ngayDat NgÃƒÂ y Ã„â€˜Ã¡ÂºÂ·t (Y-m-d)
     * @param string $gioDat GiÃ¡Â»Â Ã„â€˜Ã¡ÂºÂ·t (H:i)
     * @return array Danh sÃƒÂ¡ch mÃƒÂ£ bÃƒÂ n Ã„â€˜ÃƒÂ£ Ã„â€˜Ã¡ÂºÂ·t
     */
    public static function layBanDaDat($maCoSo, $ngayDat, $gioDat) {
        $conn = self::getConnection();
        
        try {
            // TÃƒÂ­nh toÃƒÂ¡n khoÃ¡ÂºÂ£ng thÃ¡Â»Âi gian xung Ã„â€˜Ã¡Â»â„¢t (Ã‚Â±2 giÃ¡Â»Â)
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
     * KiÃ¡Â»Æ’m tra xem bÃƒÂ n cÃƒÂ³ sÃ¡ÂºÂµn vÃƒÂ o thÃ¡Â»Âi Ã„â€˜iÃ¡Â»Æ’m cÃ¡Â»Â¥ thÃ¡Â»Æ’ khÃƒÂ´ng (tÃ¡Â»Â« TableModel)
     * @param int $maBan MÃƒÂ£ bÃƒÂ n
     * @param string $ngayDat NgÃƒÂ y Ã„â€˜Ã¡ÂºÂ·t (Y-m-d)
     * @param string $gioDat GiÃ¡Â»Â Ã„â€˜Ã¡ÂºÂ·t (H:i)
     * @return bool True nÃ¡ÂºÂ¿u bÃƒÂ n cÃƒÂ³ sÃ¡ÂºÂµn
     */
    public static function kiemTraBanCoSan($maBan, $ngayDat, $gioDat) {
        try {
            // LÃ¡ÂºÂ¥y thÃƒÂ´ng tin bÃƒÂ n Ã„â€˜Ã¡Â»Æ’ biÃ¡ÂºÂ¿t cÃ†Â¡ sÃ¡Â»Å¸
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
     * LÃ¡ÂºÂ¥y tÃ¡ÂºÂ¥t cÃ¡ÂºÂ£ bÃƒÂ n cÃ¡Â»Â§a mÃ¡Â»â„¢t cÃ†Â¡ sÃ¡Â»Å¸ (tÃ¡Â»Â« TableModel) - tÃ†Â°Ã†Â¡ng tÃ¡Â»Â± layBanTheoCoSo nhÃ†Â°ng khÃƒÂ´ng cÃƒÂ³ trÃ¡ÂºÂ¡ng thÃƒÂ¡i
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @return array Danh sÃƒÂ¡ch bÃƒÂ n
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
     * LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch bÃƒÂ n trÃ¡Â»â€˜ng theo logic thÃ¡Â»Âi gian thÃ¡Â»Â±c (khÃƒÂ´ng cÃƒÂ³ Ã„â€˜Ã†Â¡n Ã„â€˜Ã¡ÂºÂ·t trong vÃƒÂ²ng 2 giÃ¡Â»Â tÃ¡Â»â€ºi)
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @return array Danh sÃƒÂ¡ch bÃƒÂ n trÃ¡Â»â€˜ng
     */
    public static function layBanTrongTheoThoiGian($maCoSo) {
        $conn = self::getConnection();
        
        try {
            // TÃƒÂ­nh thÃ¡Â»Âi gian hiÃ¡Â»â€¡n tÃ¡ÂºÂ¡i + 2 giÃ¡Â»Â
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
     * LÃ¡ÂºÂ¥y danh sÃƒÂ¡ch bÃƒÂ n khÃƒÂ´ng cÃƒÂ³ trong dondatban_ban (tÃ¡Â»Â« TableModel)
     * @param int $maCoSo MÃƒÂ£ cÃ†Â¡ sÃ¡Â»Å¸
     * @return array Danh sÃƒÂ¡ch bÃƒÂ n chÃ†Â°a Ã„â€˜Ã†Â°Ã¡Â»Â£c Ã„â€˜Ã¡ÂºÂ·t
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
     * HÃƒÂ m helper Ã„â€˜Ã¡Â»Æ’ tÃ¡ÂºÂ¡o hoÃ¡ÂºÂ·c lÃ¡ÂºÂ¥y khÃƒÂ¡ch hÃƒÂ ng admin (cÃ¡ÂºÂ§n thiÃ¡ÂºÂ¿t cho capNhatTrangThaiBan)
     */
    private static function getOrCreateAdminCustomer($conn) {
        // KiÃ¡Â»Æ’m tra xem Ã„â€˜ÃƒÂ£ cÃƒÂ³ khÃƒÂ¡ch hÃƒÂ ng admin chÃ†Â°a
        $sql = "SELECT MaKH FROM khachhang WHERE TenKH = 'Admin System' AND Email = 'admin@system.local'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['MaKH'];
        } else {
            // TÃ¡ÂºÂ¡o khÃƒÂ¡ch hÃƒÂ ng admin mÃ¡Â»â€ºi
            $sql = "INSERT INTO khachhang (TenKH, Email, SDT) VALUES ('Admin System', 'admin@system.local', '0000000000')";
            mysqli_query($conn, $sql);
            return mysqli_insert_id($conn);
        }
    }

    // XÃƒÂ³a cÃƒÂ¡c Ã„â€˜Ã†Â¡n Ã„â€˜Ã¡ÂºÂ·t bÃƒÂ n quÃƒÂ¡ hÃ¡ÂºÂ¡n thÃ¡Â»Âi gian
    public static function xoaDonDatBanQuaHan($maCoSo = null) {
        $conn = self::getConnection();
        $bookingModel = new BookingModel($conn);

        try {
            $sql = "SELECT dd.MaDon, dd.MaCoSo, dd.ThoiGianBatDau, dd.TrangThai, cs.TenCoSo
                    FROM dondatban dd
                    JOIN coso cs ON dd.MaCoSo = cs.MaCoSo
                    WHERE dd.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                      AND TIMESTAMPDIFF(SECOND, dd.ThoiGianBatDau, NOW()) > 30";

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
            while ($row = mysqli_fetch_assoc($result)) {
                $expiredOrders[] = $row;
            }
            mysqli_stmt_close($stmt);

            if (empty($expiredOrders)) {
                return [
                    'success' => true,
                    'deleted_count' => 0,
                    'message' => 'Khong co don dat ban nao qua han'
                ];
            }

            $cancelledCount = 0;
            foreach ($expiredOrders as $order) {
                $changeResult = $bookingModel->changeStatus(
                    (int)$order['MaDon'],
                    (int)$order['MaCoSo'],
                    'da_huy',
                    [
                        'actor_type' => 'system',
                        'actor_name' => 'System',
                        'note' => 'Tu dong huy do qua han.',
                        'require_reason' => false,
                        'source' => 'booking_expiry_cleanup',
                        'metadata' => [
                            'expiredAt' => date('Y-m-d H:i:s'),
                        ],
                    ]
                );

                if (!empty($changeResult['success'])) {
                    $cancelledCount++;
                }
            }

            return [
                'success' => true,
                'deleted_count' => $cancelledCount,
                'expired_orders' => $expiredOrders,
                'details' => [
                    'cancelled' => $cancelledCount,
                    'evaluated' => count($expiredOrders)
                ],
                'message' => "Da tu dong huy {$cancelledCount} don dat ban qua han"
            ];
        } catch (Exception $e) {
            error_log("Error in xoaDonDatBanQuaHan: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Co loi xay ra khi cleanup don dat ban qua han'
            ];
        }
    }}
