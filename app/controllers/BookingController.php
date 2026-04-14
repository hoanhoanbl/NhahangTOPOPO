<?php
require_once __DIR__ . '/../models/BookingModel.php';

class BookingController extends BaseController
{
    private $bookingModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        $this->bookingModel = new BookingModel($db);
    }

    private function normalizeBookingCode($rawCode)
    {
        $normalized = strtoupper(trim((string)$rawCode));
        $normalized = preg_replace('/\s+/', '', $normalized);
        $normalized = ltrim($normalized, '#');

        if (strpos($normalized, 'DB') === 0) {
            $normalized = substr($normalized, 2);
        }

        if ($normalized === '' || !ctype_digit($normalized)) {
            return 0;
        }

        return max(0, (int)$normalized);
    }

    public function index()
    {
        $this->render('booking/index');
    }

    public function create()
    {
        $this->render('booking/create');
    }

    public function lookup()
    {
        $data = [
            'title' => 'Tra cứu đặt bàn',
            'lookupError' => null,
            'lookupCode' => '',
            'lookupPhone' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['lookupCode'] = trim($_POST['booking_code'] ?? '');
            $data['lookupPhone'] = trim($_POST['phone'] ?? '');

            if ($data['lookupCode'] === '' || $data['lookupPhone'] === '') {
                $data['lookupError'] = 'Vui lòng nhập mã đặt bàn và số điện thoại.';
                $this->render('booking/lookup', $data);
                return;
            }

            $bookingId = $this->normalizeBookingCode($data['lookupCode']);
            $booking = $bookingId > 0 ? $this->bookingModel->getBookingDetail($bookingId) : null;
            $bookingPhone = trim((string)($booking['SDT'] ?? ''));

            if (!$booking || $bookingPhone === '' || $bookingPhone !== $data['lookupPhone']) {
                $data['lookupError'] = 'Không tìm thấy booking phù hợp với thông tin đã nhập.';
                $this->render('booking/lookup', $data);
                return;
            }

            $this->redirect('?page=booking&action=success&id=' . (int)$booking['MaDon']);
            return;
        }

        $this->render('booking/lookup', $data);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $customerName = $_POST['customer_name'] ?? '';
                $customerPhone = $_POST['customer_phone'] ?? '';
                $customerEmail = $_POST['customer_email'] ?? '';
                $maCoSo = (int)($_POST['branch_id'] ?? 1);
                $soLuongKH = (int)($_POST['guest_count'] ?? 1);
                $thoiGianBatDau = trim(($_POST['booking_date'] ?? '') . ' ' . ($_POST['booking_time'] ?? ''));
                $ghiChu = $_POST['notes'] ?? '';

                $bookingId = $this->bookingModel->createBookingRecord([
                    'tenKH' => $customerName,
                    'sdt' => $customerPhone,
                    'email' => $customerEmail,
                    'maCoSo' => $maCoSo,
                    'soLuongKH' => $soLuongKH,
                    'thoiGianBatDau' => $thoiGianBatDau,
                    'ghiChu' => $ghiChu,
                    'status' => 'cho_xac_nhan',
                    'actor_type' => 'customer',
                    'actor_name' => $customerName !== '' ? $customerName : 'Khách hàng',
                    'source' => 'public_booking',
                ]);

                if ($bookingId) {
                    $this->redirect('?page=booking&action=payment&id=' . $bookingId);
                    return;
                }

                throw new Exception($this->bookingModel->getLastError() ?: 'Không thể tạo booking');
            } catch (Exception $e) {
                $this->render('booking/create', ['error' => $e->getMessage()]);
            }
        }
    }

    public function payment()
    {
        $bookingId = $_GET['id'] ?? null;

        if (!$bookingId) {
            $this->redirect('?page=booking');
            return;
        }

        $booking = $this->bookingModel->getBookingDetail($bookingId);

        if (!$booking || $booking['TrangThai'] !== 'cho_xac_nhan') {
            $this->redirect('?page=booking');
            return;
        }

        $this->render('booking/payment', ['booking' => $booking]);
    }

    public function checkPaymentStatus()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            echo json_encode(['error' => 'Missing booking_id']);
            return;
        }

        $booking = $this->bookingModel->getById($bookingId);

        if (!$booking) {
            echo json_encode(['payment_status' => 'booking_not_found']);
            return;
        }

        $paymentStatus = ($booking->TrangThai === 'da_xac_nhan') ? 'Paid' : 'Unpaid';
        echo json_encode(['payment_status' => $paymentStatus]);
    }

    public function success()
    {
        $bookingId = $_GET['id'] ?? null;
        $booking = null;
        $bookingHistory = null;

        if ($bookingId) {
            $booking = $this->bookingModel->getBookingDetail($bookingId);
            $bookingHistory = $this->bookingModel->getCustomerHistoryContract($bookingId);
        }

        $this->render('booking/success', [
            'booking' => $booking,
            'bookingHistory' => $bookingHistory,
        ]);
    }

    public function cancel()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?page=booking');
            return;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $phone = trim($_POST['customer_phone_verify'] ?? '');
        $reason = trim($_POST['cancel_reason'] ?? '');

        if ($bookingId <= 0) {
            $_SESSION['error_message'] = 'Mã booking không hợp lệ.';
            $this->redirect('?page=booking');
            return;
        }

        $booking = $this->bookingModel->getBookingDetail($bookingId);
        if (!$booking) {
            $_SESSION['error_message'] = 'Không tìm thấy đơn đặt bàn.';
            $this->redirect('?page=booking');
            return;
        }

        if ($phone === '' || trim((string)($booking['SDT'] ?? '')) !== $phone) {
            $_SESSION['error_message'] = 'Số điện thoại xác thực không khớp với đơn đặt bàn.';
            $this->redirect('?page=booking&action=success&id=' . $bookingId);
            return;
        }

        $result = $this->bookingModel->changeStatus((int)$booking['MaDon'], (int)$booking['MaCoSo'], 'da_huy', [
            'actor_type' => 'customer',
            'actor_name' => $booking['TenKH'] ?? 'Khách hàng',
            'note' => $reason,
            'require_reason' => true,
            'source' => 'public_booking_cancel',
            'metadata' => [
                'customerCancellation' => true,
            ],
        ]);

        if ($result['success']) {
            $_SESSION['success_message'] = 'Hủy đặt bàn thành công.';
        } else {
            $_SESSION['error_message'] = $result['message'] ?? 'Không thể hủy đặt bàn.';
        }

        $this->redirect('?page=booking&action=success&id=' . $bookingId);
    }
}
