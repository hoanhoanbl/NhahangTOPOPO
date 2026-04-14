<?php
/**
 * File: app/views/menu2/process-create.php
 * Xu ly tao dat ban tu form menu2
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /DatBanNH/index.php?page=menu2');
    exit();
}

require_once dirname(__DIR__, 3) . '/config/connect.php';

require_once __DIR__ . '/../../helpers/DateHelper.php';
require_once __DIR__ . '/../../models/BookingRulesModel.php';
require_once __DIR__ . '/../../models/DepositCalculator.php';
require_once __DIR__ . '/../../models/BookingModel.php';
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

    $bookingDateTimeStr = $bookingDate . ' ' . $bookingTime;
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

    $selectedTables = [];
    foreach ($allocation['tables'] as $table) {
        $tableId = (int)($table['MaBan'] ?? 0);
        if ($tableId <= 0) {
            throw new Exception('Dữ liệu bàn gán không hợp lệ.');
        }

        if (!TableAllocationService::isTableAvailable($conn, $branchId, $tableId, $bookingDateTimeStr, $leadHours)) {
            throw new Exception('Bàn vừa được đặt bởi khách khác. Vui lòng thử lại.');
        }

        $selectedTables[] = $tableId;
    }

    $bookingModel = new BookingModel($conn);
    $bookingId = $bookingModel->createBookingRecord([
        'tenKH' => $customerName,
        'sdt' => $customerPhone,
        'email' => $customerEmail,
        'maCoSo' => $branchId,
        'soLuongKH' => $guestCount,
        'thoiGianBatDau' => $bookingDateTimeStr,
        'ghiChu' => $notes,
        'status' => 'cho_xac_nhan',
        'selectedTables' => $selectedTables,
        'cartItems' => $cartItems,
        'actor_type' => 'customer',
        'actor_name' => $customerName !== '' ? $customerName : 'Khách hàng',
        'source' => 'menu2_public_booking',
    ]);

    if (!$bookingId) {
        throw new Exception($bookingModel->getLastError() ?: 'Không thể tạo đơn đặt bàn.');
    }

    $menuTotal = array_sum(array_map(static fn($item) => ((float)($item['price'] ?? 0)) * ((int)($item['quantity'] ?? 0)), $cartItems));
    $deposit = DepositCalculator::calculate($bookingDateTime, $menuTotal);

    if ($deposit > 0) {
        header("Location: ../../../index.php?page=booking&action=payment&id={$bookingId}");
    } else {
        header("Location: ../../../index.php?page=booking&action=success&id={$bookingId}");
    }
    exit();
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
