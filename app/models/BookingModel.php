<?php

class BookingModel
{
    private $conn;
    private $table = 'dondatban';
    private $auditTable = 'booking_audit_logs';
    private $lastError = null;

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
        $this->ensureAuditSchema();
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    private function clearLastError()
    {
        $this->lastError = null;
    }

    private function setLastError($message)
    {
        $this->lastError = $message;
    }

    private function ensureAuditSchema()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->auditTable}` (
                    `Id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `MaDon` INT(11) NOT NULL,
                    `MaCoSo` INT(11) NOT NULL,
                    `Action` VARCHAR(50) NOT NULL,
                    `FromStatus` VARCHAR(50) DEFAULT NULL,
                    `ToStatus` VARCHAR(50) DEFAULT NULL,
                    `ActorType` VARCHAR(50) DEFAULT NULL,
                    `ActorId` INT(11) DEFAULT NULL,
                    `ActorName` VARCHAR(255) DEFAULT NULL,
                    `Note` TEXT DEFAULT NULL,
                    `MetadataJson` LONGTEXT DEFAULT NULL,
                    `CreatedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`Id`),
                    KEY `idx_booking_audit_madon_created` (`MaDon`, `CreatedAt`),
                    KEY `idx_booking_audit_branch_created` (`MaCoSo`, `CreatedAt`),
                    CONSTRAINT `fk_booking_audit_logs_booking` FOREIGN KEY (`MaDon`) REFERENCES `dondatban` (`MaDon`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        if (!mysqli_query($this->conn, $sql)) {
            error_log("Error ensuring booking audit schema: " . mysqli_error($this->conn));
        }
    }

    private function normalizeBookingDateTime($bookingDate, $bookingTime)
    {
        $bookingDate = trim((string)$bookingDate);
        $bookingTime = trim((string)$bookingTime);

        if ($bookingDate === '' && $bookingTime === '') {
            return date('Y-m-d H:i:s');
        }

        if ($bookingDate !== '' && $bookingTime === '') {
            return $bookingDate . ' 00:00:00';
        }

        if (strpos($bookingDate, '/') !== false) {
            $dateArray = explode('/', $bookingDate);
            if (count($dateArray) === 3) {
                return sprintf('%04d-%02d-%02d %s:00', (int)$dateArray[2], (int)$dateArray[1], (int)$dateArray[0], $bookingTime);
            }
        }

        return $bookingDate . ' ' . $bookingTime . ':00';
    }

    private function findOrCreateCustomer($tenKh, $sdt, $email, $fallbackCustomerId = 2)
    {
        $tenKh = trim((string)$tenKh);
        $sdt = trim((string)$sdt);
        $email = trim((string)$email);

        if ($tenKh === '' && $sdt === '') {
            return (int)$fallbackCustomerId;
        }

        if ($sdt !== '') {
            $checkQuery = "SELECT MaKH FROM khachhang WHERE SDT = ? LIMIT 1";
            $checkStmt = mysqli_prepare($this->conn, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "s", $sdt);
            mysqli_stmt_execute($checkStmt);
            $result = mysqli_stmt_get_result($checkStmt);
            $existingCustomer = mysqli_fetch_assoc($result);
            mysqli_stmt_close($checkStmt);

            if ($existingCustomer) {
                return (int)$existingCustomer['MaKH'];
            }
        }

        $insertQuery = "INSERT INTO khachhang (TenKH, SDT, Email) VALUES (?, ?, ?)";
        $insertStmt = mysqli_prepare($this->conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "sss", $tenKh, $sdt, $email);

        if (!mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            throw new Exception('Không thể tạo thông tin khách hàng.');
        }

        $maKH = (int)mysqli_insert_id($this->conn);
        mysqli_stmt_close($insertStmt);
        return $maKH;
    }

    private function normalizeSelectedTables($selectedTables)
    {
        $normalized = [];

        foreach ((array)$selectedTables as $table) {
            if (is_array($table)) {
                $maBan = isset($table['maBan']) ? (int)$table['maBan'] : (isset($table['MaBan']) ? (int)$table['MaBan'] : 0);
            } else {
                $maBan = (int)$table;
            }

            if ($maBan > 0) {
                $normalized[$maBan] = $maBan;
            }
        }

        return array_values($normalized);
    }

    private function insertBookingTables($maDon, $maCoSo, array $selectedTables, $validateBranch = true)
    {
        if (empty($selectedTables)) {
            return;
        }

        $insertTableQuery = "INSERT INTO dondatban_ban (MaDon, MaBan) VALUES (?, ?)";
        $tableStmt = mysqli_prepare($this->conn, $insertTableQuery);

        foreach ($selectedTables as $maBan) {
            if ($validateBranch) {
                $tableCheck = mysqli_prepare($this->conn, "SELECT MaBan FROM ban WHERE MaBan = ? AND MaCoSo = ? LIMIT 1");
                mysqli_stmt_bind_param($tableCheck, "ii", $maBan, $maCoSo);
                mysqli_stmt_execute($tableCheck);
                $tableResult = mysqli_stmt_get_result($tableCheck);
                $tableRow = mysqli_fetch_assoc($tableResult);
                mysqli_stmt_close($tableCheck);

                if (!$tableRow) {
                    throw new Exception('Bàn không thuộc cơ sở hợp lệ.');
                }
            }

            mysqli_stmt_bind_param($tableStmt, "ii", $maDon, $maBan);
            if (!mysqli_stmt_execute($tableStmt)) {
                throw new Exception('Không thể gán bàn cho đơn đặt bàn.');
            }
        }

        mysqli_stmt_close($tableStmt);
    }

    private function resolveMenuPrice($maMon, $maCoSo)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT Gia FROM menu_coso WHERE MaMon = ? AND MaCoSo = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ii", $maMon, $maCoSo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return isset($row['Gia']) ? (float)$row['Gia'] : 0.0;
    }

    private function insertBookingItems($maDon, $maCoSo, array $cartItems)
    {
        if (empty($cartItems)) {
            return;
        }

        $insertItemQuery = "INSERT INTO chitietdondatban (MaDon, MaMon, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
        $itemStmt = mysqli_prepare($this->conn, $insertItemQuery);

        foreach ($cartItems as $item) {
            $maMon = (int)($item['id'] ?? $item['MaMon'] ?? 0);
            $soLuong = (int)($item['quantity'] ?? $item['SoLuong'] ?? 0);

            if ($maMon <= 0 || $soLuong <= 0) {
                throw new Exception('Dữ liệu món ăn không hợp lệ.');
            }

            $donGia = isset($item['price']) ? (float)$item['price'] : (float)($item['DonGia'] ?? 0);
            if ($donGia <= 0) {
                $donGia = $this->resolveMenuPrice($maMon, $maCoSo);
            }

            mysqli_stmt_bind_param($itemStmt, "iiid", $maDon, $maMon, $soLuong, $donGia);
            if (!mysqli_stmt_execute($itemStmt)) {
                throw new Exception('Không thể thêm món ăn vào đơn hàng.');
            }
        }

        mysqli_stmt_close($itemStmt);
    }

    private function fetchAssignedTables($maDon)
    {
        $query = "SELECT b.MaBan, b.TenBan, b.SucChua
                  FROM dondatban_ban db
                  JOIN ban b ON db.MaBan = b.MaBan
                  WHERE db.MaDon = ?
                  ORDER BY b.TenBan";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $maDon);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $tables = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $tables[] = [
                'maBan' => (int)$row['MaBan'],
                'tenBan' => $row['TenBan'],
                'sucChua' => (int)$row['SucChua'],
            ];
        }

        mysqli_stmt_close($stmt);
        return $tables;
    }

    private function getBookingAuditSource($maDon, $maCoSo = null)
    {
        $query = "SELECT d.*, kh.TenKH, kh.SDT, kh.Email AS EmailKH,
                         nv.TenNhanVien AS NhanVienXacNhan,
                         cs.TenCoSo, cs.DiaChi AS DiaChiCoSo
                  FROM {$this->table} d
                  LEFT JOIN khachhang kh ON d.MaKH = kh.MaKH
                  LEFT JOIN nhanvien nv ON d.MaNV_XacNhan = nv.MaNV
                  LEFT JOIN coso cs ON d.MaCoSo = cs.MaCoSo
                  WHERE d.MaDon = ?";

        $params = [$maDon];
        $types = "i";

        if ($maCoSo !== null) {
            $query .= " AND d.MaCoSo = ?";
            $params[] = (int)$maCoSo;
            $types .= "i";
        }

        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $booking = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($stmt);

        if ($booking) {
            $booking['Tables'] = $this->fetchAssignedTables((int)$maDon);
        }

        return $booking;
    }

    private function writeAuditLog($maDon, $maCoSo, $action, $fromStatus, $toStatus, array $context = [], array $bookingSnapshot = [])
    {
        $metadata = [
            'booking' => [
                'maDon' => (int)$maDon,
                'maCoSo' => (int)$maCoSo,
                'tenCoSo' => $bookingSnapshot['TenCoSo'] ?? null,
                'status' => $toStatus,
                'guestCount' => isset($bookingSnapshot['SoLuongKH']) ? (int)$bookingSnapshot['SoLuongKH'] : null,
                'startAt' => $bookingSnapshot['ThoiGianBatDau'] ?? null,
                'createdAt' => $bookingSnapshot['ThoiGianTao'] ?? null,
                'note' => $bookingSnapshot['GhiChu'] ?? null,
                'source' => $context['source'] ?? null,
            ],
            'customer' => [
                'maKh' => isset($bookingSnapshot['MaKH']) ? (int)$bookingSnapshot['MaKH'] : null,
                'name' => $bookingSnapshot['TenKH'] ?? null,
                'phone' => $bookingSnapshot['SDT'] ?? null,
                'email' => $bookingSnapshot['EmailKH'] ?? null,
            ],
            'tables' => $bookingSnapshot['Tables'] ?? [],
        ];

        if (!empty($context['metadata']) && is_array($context['metadata'])) {
            $metadata = array_replace_recursive($metadata, $context['metadata']);
        }

        $metadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $actorType = $context['actor_type'] ?? null;
        $actorId = isset($context['actor_id']) ? (int)$context['actor_id'] : null;
        $actorName = $context['actor_name'] ?? null;
        $note = $context['note'] ?? null;

        $query = "INSERT INTO {$this->auditTable}
                    (MaDon, MaCoSo, Action, FromStatus, ToStatus, ActorType, ActorId, ActorName, Note, MetadataJson)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "iissssisss",
            $maDon,
            $maCoSo,
            $action,
            $fromStatus,
            $toStatus,
            $actorType,
            $actorId,
            $actorName,
            $note,
            $metadataJson
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception('Không thể ghi lịch sử booking.');
        }

        mysqli_stmt_close($stmt);
    }

    public function recordAuditEvent($maDon, $maCoSo, $action, $fromStatus, $toStatus, array $context = [])
    {
        try {
            $snapshot = $this->getBookingAuditSource($maDon, $maCoSo);
            if (!$snapshot) {
                $this->setLastError('Không tìm thấy booking để ghi lịch sử.');
                return false;
            }

            $this->writeAuditLog(
                (int)$maDon,
                (int)$maCoSo,
                (string)$action,
                $fromStatus,
                $toStatus,
                $context,
                $snapshot
            );

            return true;
        } catch (Exception $e) {
            $this->setLastError($e->getMessage());
            error_log("Error in BookingModel::recordAuditEvent: " . $e->getMessage());
            return false;
        }
    }

    public function createBookingRecord(array $payload)
    {
        $this->clearLastError();

        $maCoSo = (int)($payload['maCoSo'] ?? 0);
        $status = (string)($payload['status'] ?? 'cho_xac_nhan');
        $soLuongKH = (int)($payload['soLuongKH'] ?? 1);
        $ghiChu = (string)($payload['ghiChu'] ?? '');
        $thoiGianBatDau = trim((string)($payload['thoiGianBatDau'] ?? ''));
        $selectedTables = $this->normalizeSelectedTables($payload['selectedTables'] ?? []);
        $cartItems = (array)($payload['cartItems'] ?? []);
        $maNV = array_key_exists('maNV_XacNhan', $payload) && $payload['maNV_XacNhan'] !== null && $payload['maNV_XacNhan'] !== ''
            ? (int)$payload['maNV_XacNhan']
            : null;

        if ($maCoSo <= 0 || $soLuongKH <= 0 || $thoiGianBatDau === '') {
            $this->setLastError('Thiếu thông tin tạo booking.');
            return false;
        }

        mysqli_begin_transaction($this->conn);

        try {
            $maKH = isset($payload['maKH'])
                ? (int)$payload['maKH']
                : $this->findOrCreateCustomer(
                    $payload['tenKH'] ?? '',
                    $payload['sdt'] ?? '',
                    $payload['email'] ?? '',
                    $payload['fallbackCustomerId'] ?? 2
                );

            if ($maNV === null) {
                $query = "INSERT INTO {$this->table}
                            (MaKH, MaCoSo, ThoiGianBatDau, ThoiGianTao, TrangThai, SoLuongKH, GhiChu)
                          VALUES (?, ?, ?, CONVERT_TZ(NOW(), '+00:00', '+07:00'), ?, ?, ?)";
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "iissis", $maKH, $maCoSo, $thoiGianBatDau, $status, $soLuongKH, $ghiChu);
            } else {
                $query = "INSERT INTO {$this->table}
                            (MaKH, MaCoSo, MaNV_XacNhan, ThoiGianBatDau, ThoiGianTao, TrangThai, SoLuongKH, GhiChu)
                          VALUES (?, ?, ?, ?, CONVERT_TZ(NOW(), '+00:00', '+07:00'), ?, ?, ?)";
                $stmt = mysqli_prepare($this->conn, $query);
                mysqli_stmt_bind_param($stmt, "iiissis", $maKH, $maCoSo, $maNV, $thoiGianBatDau, $status, $soLuongKH, $ghiChu);
            }

            if (!mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                throw new Exception('Không thể tạo đơn đặt bàn.');
            }

            $maDon = (int)mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);

            $this->insertBookingItems($maDon, $maCoSo, $cartItems);
            $this->insertBookingTables($maDon, $maCoSo, $selectedTables, (bool)($payload['validateTableBranch'] ?? true));

            $snapshot = $this->getBookingAuditSource($maDon);
            $this->writeAuditLog(
                $maDon,
                $maCoSo,
                'created',
                null,
                $status,
                [
                    'actor_type' => $payload['actor_type'] ?? null,
                    'actor_id' => $payload['actor_id'] ?? null,
                    'actor_name' => $payload['actor_name'] ?? null,
                    'note' => $payload['audit_note'] ?? ($ghiChu !== '' ? $ghiChu : null),
                    'source' => $payload['source'] ?? 'unknown',
                    'metadata' => [
                        'booking' => [
                            'source' => $payload['source'] ?? 'unknown',
                        ],
                    ],
                ],
                $snapshot ?: []
            );

            mysqli_commit($this->conn);
            return $maDon;
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            $this->setLastError($e->getMessage());
            error_log("Error in BookingModel::createBookingRecord: " . $e->getMessage());
            return false;
        }
    }

    public function changeStatus($maDon, $maCoSo, $status, array $context = [])
    {
        $this->clearLastError();

        $validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'da_huy', 'hoan_thanh'];
        if (!in_array($status, $validStatuses, true)) {
            return [
                'success' => false,
                'affectedRows' => 0,
                'message' => 'Trạng thái không hợp lệ.',
            ];
        }

        $booking = $this->getBookingAuditSource($maDon, $maCoSo);
        if (!$booking) {
            return [
                'success' => false,
                'affectedRows' => 0,
                'message' => 'Không tìm thấy đơn đặt bàn hoặc bạn không có quyền cập nhật.',
            ];
        }

        $currentStatus = (string)$booking['TrangThai'];
        $allowedTransitions = [
            'cho_xac_nhan' => ['da_xac_nhan', 'da_huy'],
            'da_xac_nhan' => ['da_huy', 'hoan_thanh'],
            'da_huy' => [],
            'hoan_thanh' => [],
        ];

        if ($currentStatus === $status) {
            return [
                'success' => false,
                'affectedRows' => 0,
                'message' => 'Đơn đặt bàn đã ở trạng thái này.',
            ];
        }

        if (!in_array($status, $allowedTransitions[$currentStatus] ?? [], true)) {
            return [
                'success' => false,
                'affectedRows' => 0,
                'message' => 'Chuyển trạng thái không hợp lệ.',
            ];
        }

        $actorType = $context['actor_type'] ?? null;
        $reason = trim((string)($context['note'] ?? ''));
        $requireReason = array_key_exists('require_reason', $context)
            ? (bool)$context['require_reason']
            : ($status === 'da_huy' && $actorType !== 'system');

        if ($status === 'da_huy' && $requireReason && $reason === '') {
            return [
                'success' => false,
                'affectedRows' => 0,
                'message' => 'Vui lòng nhập lý do hủy đơn.',
            ];
        }

        mysqli_begin_transaction($this->conn);

        try {
            $query = "UPDATE {$this->table} SET TrangThai = ?";
            $params = [$status];
            $types = "s";

            if (isset($context['actor_id']) && $context['actor_id']) {
                $query .= ", MaNV_XacNhan = ?";
                $params[] = (int)$context['actor_id'];
                $types .= "i";
            }

            $query .= " WHERE MaDon = ? AND MaCoSo = ?";
            $params[] = (int)$maDon;
            $params[] = (int)$maCoSo;
            $types .= "ii";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);

            if (!mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                throw new Exception('Không thể cập nhật trạng thái booking.');
            }

            $affectedRows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);

            if ($affectedRows <= 0) {
                mysqli_rollback($this->conn);
                return [
                    'success' => false,
                    'affectedRows' => 0,
                    'message' => 'Không có thay đổi nào được ghi nhận.',
                ];
            }

            $updatedSnapshot = $this->getBookingAuditSource($maDon, $maCoSo);
            $actionMap = [
                'da_xac_nhan' => 'confirmed',
                'da_huy' => 'cancelled',
                'hoan_thanh' => 'completed',
                'cho_xac_nhan' => 'reopened',
            ];

            $this->writeAuditLog(
                $maDon,
                $maCoSo,
                $actionMap[$status] ?? 'status_changed',
                $currentStatus,
                $status,
                [
                    'actor_type' => $actorType,
                    'actor_id' => $context['actor_id'] ?? null,
                    'actor_name' => $context['actor_name'] ?? null,
                    'note' => $reason !== '' ? $reason : null,
                    'source' => $context['source'] ?? 'booking_status_update',
                    'metadata' => $context['metadata'] ?? [],
                ],
                $updatedSnapshot ?: $booking
            );

            mysqli_commit($this->conn);

            return [
                'success' => true,
                'affectedRows' => $affectedRows,
                'message' => 'Cập nhật trạng thái đơn thành công.',
                'booking' => $updatedSnapshot,
            ];
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            $this->setLastError($e->getMessage());
            error_log("Error in BookingModel::changeStatus: " . $e->getMessage());
            return [
                'success' => false,
                'affectedRows' => 0,
                'message' => 'Có lỗi hệ thống xảy ra khi cập nhật trạng thái.',
            ];
        }
    }

    public function getBookingTimeline($maDon, $maCoSo = null)
    {
        try {
            $query = "SELECT * FROM {$this->auditTable} WHERE MaDon = ?";
            $params = [$maDon];
            $types = "i";

            if ($maCoSo !== null) {
                $query .= " AND MaCoSo = ?";
                $params[] = (int)$maCoSo;
                $types .= "i";
            }

            $query .= " ORDER BY CreatedAt ASC, Id ASC";

            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $timeline = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['Metadata'] = !empty($row['MetadataJson']) ? json_decode($row['MetadataJson'], true) : [];
                $timeline[] = $row;
            }

            mysqli_stmt_close($stmt);
            return $timeline;
        } catch (Exception $e) {
            error_log("Error in BookingModel::getBookingTimeline: " . $e->getMessage());
            return [];
        }
    }

    public function getCustomerHistoryContract($maDon, $maCoSo = null)
    {
        $booking = $this->getBookingDetail($maDon, $maCoSo);
        if (!$booking) {
            return null;
        }

        $timeline = $this->getBookingTimeline($maDon, $maCoSo);

        return [
            'booking' => [
                'id' => (int)$booking['MaDon'],
                'branchId' => (int)$booking['MaCoSo'],
                'branchName' => $booking['TenCoSo'] ?? '',
                'status' => $booking['TrangThai'] ?? '',
                'startAt' => $booking['ThoiGianBatDau'] ?? '',
                'guestCount' => isset($booking['SoLuongKH']) ? (int)$booking['SoLuongKH'] : 0,
                'note' => $booking['GhiChu'] ?? '',
                'tables' => $booking['DanhSachBan'] ?? '',
            ],
            'customer' => [
                'name' => $booking['TenKH'] ?? '',
                'phone' => $booking['SDT'] ?? '',
                'email' => $booking['EmailKH'] ?? '',
            ],
            'timeline' => array_map(function ($event) {
                return [
                    'action' => $event['Action'],
                    'fromStatus' => $event['FromStatus'],
                    'toStatus' => $event['ToStatus'],
                    'actorType' => $event['ActorType'],
                    'actorName' => $event['ActorName'],
                    'note' => $event['Note'],
                    'createdAt' => $event['CreatedAt'],
                    'snapshot' => $event['Metadata'] ?? [],
                ];
            }, $timeline),
        ];
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
                     WHERE MaCoSo = ? AND DATE(ThoiGianBatDau) = ? AND TrangThai NOT IN ('hoan_thanh', 'da_huy')";
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
                     WHERE MaCoSo = ? AND DATE(ThoiGianBatDau) > ? AND TrangThai NOT IN ('hoan_thanh', 'da_huy')";
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

    public function updateStatus($maDon, $maCoSo, $status, $maNVXacNhan = null, $ghiChu = null)
    {
        $result = $this->changeStatus($maDon, $maCoSo, $status, [
            'actor_type' => $maNVXacNhan ? 'staff' : 'system',
            'actor_id' => $maNVXacNhan,
            'note' => $ghiChu,
            'source' => 'booking_model_update_status',
        ]);

        return $result['success'] ? $result['affectedRows'] : false;
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


    public function createBookingWithTables($TenKh, $SDT, $Email, $maCoSo, $maNV, $cartItems, $ghiChu = '', $bookingDate = '', $bookingTime = '', $numberOfGuests = 1, $selectedTables = [])
    {
        return $this->createBookingRecord([
            'tenKH' => $TenKh,
            'sdt' => $SDT,
            'email' => $Email,
            'maCoSo' => $maCoSo,
            'maNV_XacNhan' => $maNV,
            'soLuongKH' => $numberOfGuests,
            'thoiGianBatDau' => $this->normalizeBookingDateTime($bookingDate, $bookingTime),
            'ghiChu' => $ghiChu,
            'status' => 'da_xac_nhan',
            'selectedTables' => $selectedTables,
            'cartItems' => $cartItems,
            'actor_type' => 'staff',
            'actor_id' => $maNV,
            'source' => 'nhanvien_create_order',
        ]);
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
