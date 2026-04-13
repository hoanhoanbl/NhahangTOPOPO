<?php
/**
 * File: app/views/menu2/process-create.php
 * Xử lý tạo đặt bàn từ form menu2
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DatBanNH/index.php?page=menu2');
    exit();
}

include __DIR__ . '../../../../config/connect.php';

require_once __DIR__ . '/../../helpers/DateHelper.php';
require_once __DIR__ . '/../../models/BookingRulesModel.php';
require_once __DIR__ . '/../../models/DepositCalculator.php';
require_once __DIR__ . '/../../models/TableAllocationService.php';

$customerName  = trim($_POST['customer_name'] ?? '');
$customerPhone = trim($_POST['customer_phone'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');
$branchId      = (int)($_POST['branch_id'] ?? 0);
$guestCount    = (int)($_POST['guest_count'] ?? 1);
$bookingDate   = trim($_POST['booking_date'] ?? '');
$bookingTime   = trim($_POST['booking_time'] ?? '');
$notes         = trim($_POST['notes'] ?? '');
$cartItems     = json_decode($_POST['cart_items'] ?? '[]', true);

if ($customerName === '' || $customerPhone === '' || $branchId <= 0 || $bookingDate === '' || $bookingTime === '') {
    echo 'Thiếu thông tin bắt buộc.';
    exit();
}

if (empty($cartItems) || !is_array($cartItems)) {
    echo 'Giỏ hàng trống.';
    exit();
}

$transactionStarted = false;

try {
    $bookingDateTime = DateTime::createFromFormat('Y-m-d H:i', $bookingDate . ' ' . $bookingTime);
    if (!$bookingDateTime) {
        throw new Exception('Định dạng ngày giờ không hợp lệ.');
    }

    if (!DateHelper::isOpen($bookingDateTime)) {
        throw new Exception('Nhà hàng đóng cửa vào giờ này. Vui lòng chọn giờ từ 9:00 đến 21:59.');
    }

    $now = new DateTime();
    $leadHours = (int)BookingRulesModel::get('lead_time_hours');
    if ($leadHours <= 0) {
        $leadHours = 2;
    }

    $diffSeconds = $bookingDateTime->getTimestamp() - $now->getTimestamp();
    if ($diffSeconds < $leadHours * 3600) {
        throw new Exception("Vui lòng đặt trước tối thiểu {$leadHours} tiếng.");
    }

    $maxDays = (int)BookingRulesModel::get('max_advance_days');
    if ($maxDays <= 0) {
        $maxDays = 30;
    }

    $diffDays = $bookingDateTime->diff($now)->days;
    if ($diffDays > $maxDays) {
        throw new Exception("Chỉ hỗ trợ đặt trước tối đa {$maxDays} ngày.");
    }

    mysqli_begin_transaction($conn);
    $transactionStarted = true;

    $maKH = createOrGetCustomer($conn, $customerName, $customerPhone, $customerEmail);
    $bookingDateTimeStr = $bookingDate . ' ' . $bookingTime;

    $bookingId = createBooking($conn, $maKH, $branchId, $guestCount, $bookingDateTimeStr, $notes);

    $allocation = TableAllocationService::allocateForBooking(
        $conn,
        $branchId,
        $guestCount,
        $bookingDateTimeStr,
        $leadHours,
        true,
        4
    );

    if (!$allocation || empty($allocation['tables'])) {
        throw new Exception('Rất tiếc! Không còn bàn trống phù hợp với thời gian bạn chọn. Vui lòng chọn thời gian khác.');
    }

    assignTablesToBooking($conn, $bookingId, $branchId, $bookingDateTimeStr, $leadHours, $allocation['tables']);

    addMenuItemsToBooking($conn, $bookingId, $branchId, $cartItems);

    $menuTotal = array_sum(array_map(static fn($item) => ((float)$item['price']) * ((int)$item['quantity']), $cartItems));
    $deposit = DepositCalculator::calculate($bookingDateTime, $menuTotal);

    mysqli_commit($conn);
    $transactionStarted = false;

    if ($deposit > 0) {
        header("Location: ../../../sepay/sepay_payment.php?booking_id={$bookingId}&amount={$deposit}");
    } else {
        header("Location: ../../../app/views/booking/success.php?id={$bookingId}");
    }
    exit();
} catch (Exception $e) {
    if ($transactionStarted) {
        mysqli_rollback($conn);
    }
    echo $e->getMessage();
    exit();
}

function createOrGetCustomer(mysqli $conn, string $name, string $phone, string $email): int
{
    $stmt = mysqli_prepare($conn, "SELECT MaKH FROM khachhang WHERE SDT = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return (int)$row['MaKH'];
    }
    mysqli_stmt_close($stmt);

    $stmt2 = mysqli_prepare($conn, "INSERT INTO khachhang (TenKH, SDT, Email) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, 'sss', $name, $phone, $email);
    if (!mysqli_stmt_execute($stmt2)) {
        mysqli_stmt_close($stmt2);
        throw new Exception('Không thể tạo thông tin khách hàng.');
    }
    $id = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmt2);

    if ($id <= 0) {
        throw new Exception('Không thể tạo thông tin khách hàng.');
    }
    return $id;
}

function createBooking(mysqli $conn, int $maKH, int $maCoSo, int $soLuongKH, string $thoiGianBatDau, string $ghiChu): int
{
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO dondatban (MaKH, MaCoSo, SoLuongKH, ThoiGianBatDau, GhiChu, TrangThai, ThoiGianTao)
         VALUES (?, ?, ?, ?, ?, 'cho_xac_nhan', NOW())"
    );
    mysqli_stmt_bind_param($stmt, 'iiiss', $maKH, $maCoSo, $soLuongKH, $thoiGianBatDau, $ghiChu);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception('Không thể tạo đơn đặt bàn.');
    }

    $id = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if ($id <= 0) {
        throw new Exception('Không thể tạo đơn đặt bàn.');
    }
    return $id;
}

function assignTablesToBooking(mysqli $conn, int $bookingId, int $branchId, string $bookingDateTime, int $leadHours, array $tables): void
{
    $insertSql = "INSERT INTO dondatban_ban (MaDon, MaBan) VALUES (?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    if (!$insertStmt) {
        throw new Exception('Không thể chuẩn bị gán bàn.');
    }

    foreach ($tables as $table) {
        $tableId = (int)($table['MaBan'] ?? 0);
        if ($tableId <= 0) {
            mysqli_stmt_close($insertStmt);
            throw new Exception('Dữ liệu bàn gán không hợp lệ.');
        }

        // Re-check conflict in transactional flow to reduce race condition impact.
        if (!TableAllocationService::isTableAvailable($conn, $branchId, $tableId, $bookingDateTime, $leadHours)) {
            mysqli_stmt_close($insertStmt);
            throw new Exception('Bàn vừa được đặt bởi khách khác. Vui lòng thử lại.');
        }

        mysqli_stmt_bind_param($insertStmt, 'ii', $bookingId, $tableId);
        if (!mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            throw new Exception('Không thể gán bàn cho đơn đặt bàn.');
        }
    }

    mysqli_stmt_close($insertStmt);
}

function addMenuItemsToBooking(mysqli $conn, int $bookingId, int $branchId, array $cartItems): void
{
    foreach ($cartItems as $item) {
        $menuId = (int)($item['id'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 0);
        if ($menuId <= 0 || $quantity <= 0) {
            throw new Exception('Dữ liệu món ăn không hợp lệ.');
        }

        $priceStmt = mysqli_prepare($conn, "SELECT Gia FROM menu_coso WHERE MaMon = ? AND MaCoSo = ?");
        mysqli_stmt_bind_param($priceStmt, 'ii', $menuId, $branchId);
        mysqli_stmt_execute($priceStmt);
        $priceResult = mysqli_stmt_get_result($priceStmt);

        $currentPrice = (float)($item['price'] ?? 0);
        if ($priceRow = mysqli_fetch_assoc($priceResult)) {
            $currentPrice = (float)$priceRow['Gia'];
        }
        mysqli_stmt_close($priceStmt);

        $insertStmt = mysqli_prepare(
            $conn,
            "INSERT INTO chitietdondatban (MaDon, MaMon, SoLuong, DonGia) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($insertStmt, 'iiid', $bookingId, $menuId, $quantity, $currentPrice);

        if (!mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            throw new Exception('Không thể thêm món ăn: ' . ($item['name'] ?? $menuId));
        }
        mysqli_stmt_close($insertStmt);
    }
}
?>
