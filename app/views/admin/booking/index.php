<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__, 4) . "/config/connect.php";
require_once dirname(__DIR__, 3) . '/models/TableStatusManager.php';
require_once dirname(__DIR__, 3) . '/models/BookingModel.php';
require_once dirname(__DIR__) . '/common/branch-auth.php';
$authBranch = adminAuth();
$isGlobalAdmin = $authBranch->isAdmin();
$sessionBranchId = $authBranch->getCurrentBranchId();
if (!$isGlobalAdmin && $sessionBranchId <= 0) {
    adminDenyAndRedirect('?page=admin&section=booking', 'Không tìm thấy cơ sở được phân quyền cho tài khoản này.');
}

// ============================================================
// AJAX: Create new booking
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_booking') {
    header('Content-Type: application/json; charset=UTF-8');

    $requestedMaCoSo = isset($_POST['maCoSo']) ? (int)$_POST['maCoSo'] : 0;
    $maCoSo = $authBranch->resolveScopedBranchId($requestedMaCoSo);
    $maBan = isset($_POST['maBan']) ? (int)$_POST['maBan'] : 0;
    $tenKH = isset($_POST['tenKH']) ? trim($_POST['tenKH']) : '';
    $sdt = isset($_POST['sdt']) ? trim($_POST['sdt']) : '';
    $soLuongKH = isset($_POST['soLuongKH']) ? (int)$_POST['soLuongKH'] : 0;
    $ngayDat = isset($_POST['ngayDat']) ? trim($_POST['ngayDat']) : '';
    $gioBatDau = isset($_POST['gioBatDau']) ? trim($_POST['gioBatDau']) : '';
    $ghiChu = isset($_POST['ghiChu']) ? trim($_POST['ghiChu']) : '';

    if ($maCoSo <= 0 || $maBan <= 0 || $tenKH === '' || $sdt === '' || $soLuongKH <= 0 || $ngayDat === '' || $gioBatDau === '') {
        echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.']);
        exit;
    }

    if (!$isGlobalAdmin && $requestedMaCoSo > 0 && $requestedMaCoSo !== $sessionBranchId) {
        $authBranch->denyBranchAccess('Bạn không được tạo đơn đặt cho cơ sở khác.');
        echo json_encode(['success' => false, 'message' => 'Không được tạo booking cho cơ sở khác.']);
        exit;
    }

    try {
        $stmtTable = mysqli_prepare($conn, 'SELECT MaBan FROM ban WHERE MaBan = ? AND MaCoSo = ? LIMIT 1');
        mysqli_stmt_bind_param($stmtTable, 'ii', $maBan, $maCoSo);
        mysqli_stmt_execute($stmtTable);
        $resTable = mysqli_stmt_get_result($stmtTable);
        $tableRow = mysqli_fetch_assoc($resTable);
        mysqli_stmt_close($stmtTable);

        if (!$tableRow) {
            throw new Exception('Bàn không thuộc cơ sở hợp lệ.');
        }

        $currentUser = $_SESSION['user'] ?? [];
        $bookingModel = new BookingModel($conn);
        $maDon = $bookingModel->createBookingRecord([
            'tenKH' => $tenKH,
            'sdt' => $sdt,
            'maCoSo' => $maCoSo,
            'soLuongKH' => $soLuongKH,
            'thoiGianBatDau' => $ngayDat . ' ' . $gioBatDau . ':00',
            'ghiChu' => $ghiChu,
            'status' => 'cho_xac_nhan',
            'selectedTables' => [$maBan],
            'actor_type' => 'staff',
            'actor_id' => $currentUser['MaNV'] ?? null,
            'actor_name' => $currentUser['TenNhanVien'] ?? ($currentUser['HoTen'] ?? ($currentUser['TenDangNhap'] ?? 'Admin')),
            'source' => 'admin_booking_calendar',
        ]);

        if (!$maDon) {
            throw new Exception($bookingModel->getLastError() ?: 'Không thể tạo đơn đặt bàn.');
        }

        echo json_encode(['success' => true, 'message' => 'Tạo đơn đặt bàn thành công!', 'maDon' => $maDon]);
    } catch (Throwable $e) {
        error_log('[BOOKING][CREATE] ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => ($e->getMessage() ?: 'Lỗi khi tạo đơn đặt bàn.')]);
    }
    exit;
}

// ============================================================
// Page Load: fetch branches, tables, bookings
// ============================================================
// ============================================================
// Page Load: fetch branches, tables, bookings
// ============================================================

// Branches
$listCoSo = TableStatusManager::layDanhSachCoSo();
if (!$isGlobalAdmin) {
    $listCoSo = array_values(array_filter($listCoSo, function ($coSo) use ($sessionBranchId) {
        return (int)($coSo['MaCoSo'] ?? 0) === $sessionBranchId;
    }));
}

// Default branch
$requestedBranch = isset($_GET['maCoSo']) ? (int)$_GET['maCoSo'] : 0;
$maCoSoHienTai = $authBranch->resolveScopedBranchId($requestedBranch > 0 ? $requestedBranch : (isset($listCoSo[0]) ? (int)$listCoSo[0]['MaCoSo'] : 0));
if (!$isGlobalAdmin && $requestedBranch > 0 && $requestedBranch !== $sessionBranchId) {
    adminDenyAndRedirect('?page=admin&section=booking', 'Bạn không được xem booking của cơ sở khác.');
}

// Tables for the selected branch
$tables = [];
if ($maCoSoHienTai > 0) {
    $stmtBan = mysqli_prepare($conn, "SELECT MaBan, TenBan, SucChua FROM ban WHERE MaCoSo = ? ORDER BY TenBan");
    mysqli_stmt_bind_param($stmtBan, "i", $maCoSoHienTai);
    mysqli_stmt_execute($stmtBan);
    $resBan = mysqli_stmt_get_result($stmtBan);
    while ($r = mysqli_fetch_assoc($resBan)) {
        $tables[] = $r;
    }
    mysqli_stmt_close($stmtBan);
}

// Bookings for the selected branch (last 90 days to +90 days)
$bookings = [];
if ($maCoSoHienTai > 0) {
    $tuNgay = date('Y-m-d', strtotime('-90 days'));
    $denNgay = date('Y-m-d', strtotime('+90 days'));

    $sql = "SELECT dd.MaDon, dd.MaKH, dd.SoLuongKH, dd.ThoiGianBatDau,
                   dd.TrangThai, dd.GhiChu,
                   kh.TenKH, kh.SDT,
                   b.TenBan, b.MaBan
            FROM dondatban dd
            JOIN khachhang kh ON dd.MaKH = kh.MaKH
            JOIN dondatban_ban dbb ON dd.MaDon = dbb.MaDon
            JOIN ban b ON dbb.MaBan = b.MaBan
            WHERE dd.MaCoSo = ?
              AND DATE(dd.ThoiGianBatDau) BETWEEN ? AND ?
            ORDER BY dd.ThoiGianBatDau";
    $stmtBook = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmtBook, "iss", $maCoSoHienTai, $tuNgay, $denNgay);
    mysqli_stmt_execute($stmtBook);
    $resBook = mysqli_stmt_get_result($stmtBook);
    while ($r = mysqli_fetch_assoc($resBook)) {
        $bookings[] = $r;
    }
    mysqli_stmt_close($stmtBook);
}
?>
<!-- Toast UI Calendar CSS -->
<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
<style>
    :root {
        --colorPrimary: #1B4E30;
    }
    /* Booking page header */
    .booking-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    /* Branch filter */
    .branch-filter {
        max-width: 280px;
    }
    /* Calendar controls row */
    .calendar-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }
    .calendar-controls .btn {
        padding: 0.3rem 0.75rem;
        font-size: 0.875rem;
    }
    .calendar-controls .btn.active {
        background-color: var(--colorPrimary);
        color: #fff;
        border-color: var(--colorPrimary);
    }
    /* Calendar container */
    #calendar-container {
        height: 680px;
        min-height: 680px;
        overflow: hidden;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    #calendar-container > div,
    #calendar-container .toastui-calendar-layout {
        height: 100% !important;
    }
    @media (max-width: 991.98px) {
        #calendar-container {
            height: 560px;
            min-height: 560px;
        }
    }
    /* Toast UI Calendar overrides for Vietnamese */
    .toastui-calendar-weekday-schedule-list {
        font-size: 0.8rem;
    }
    /* Event colors */
    .booking-event {
        font-size: 0.8rem;
        border-radius: 4px;
        padding: 2px 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .booking-confirmed {
        background-color: #d4edda !important;
        border-left: 3px solid #28a745 !important;
        color: #155724 !important;
    }
    .booking-pending {
        background-color: #fff3cd !important;
        border-left: 3px solid #ffc107 !important;
        color: #856404 !important;
    }
    .booking-cancelled {
        background-color: #e9ecef !important;
        border-left: 3px solid #6c757d !important;
        color: #6c757d !important;
        opacity: 0.7;
    }
    /* Modal form */
    .form-label {
        font-weight: 500;
    }
    /* Sidebar collapse compatibility */
    .main-content.collapsed #calendar-container {
        height: 620px;
        min-height: 620px;
    }
</style>

<div class="card shadow-sm">
    <div class="card-body">

        <!-- Page Header + Branch Filter -->
        <div class="booking-header">
            <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch đặt bàn</h4>
            <form method="GET" class="branch-filter">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="section" value="booking">
                <label class="form-label small mb-1">Cơ sở:</label>
                <select name="maCoSo" class="form-select" onchange="this.form.submit()" id="branchSelect" <?php echo $isGlobalAdmin ? '' : 'disabled'; ?>>
                    <option value="">-- Chọn cơ sở --</option>
                    <?php foreach ($listCoSo as $cs): ?>
                        <option value="<?= $cs['MaCoSo'] ?>" <?= ($cs['MaCoSo'] == $maCoSoHienTai) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cs['TenCoSo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$isGlobalAdmin): ?>
                    <input type="hidden" name="maCoSo" value="<?= (int)$maCoSoHienTai ?>">
                <?php endif; ?>
            </form>
        </div>

        <?php if ($maCoSoHienTai <= 0): ?>
            <div class="alert alert-info mb-0">
                Vui lòng chọn cơ sở để xem lịch đặt bàn.
            </div>
        <?php else: ?>

            <!-- Add Booking Button -->
            <div class="mb-3 text-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                    <i class="fas fa-plus"></i> Thêm đơn đặt bàn
                </button>
            </div>

            <!-- Calendar Controls -->
            <div class="calendar-controls">
                <button class="btn btn-outline-secondary" id="btnPrev"><i class="fas fa-chevron-left"></i></button>
                <button class="btn btn-outline-secondary" id="btnToday">Hôm nay</button>
                <button class="btn btn-outline-secondary" id="btnNext"><i class="fas fa-chevron-right"></i></button>
                <div class="vr"></div>
                <button class="btn btn-primary active" id="btnDay">Ngày</button>
                <button class="btn btn-outline-primary" id="btnWeek">Tuần</button>
                <button class="btn btn-outline-primary" id="btnMonth">Tháng</button>
            </div>

            <!-- Calendar -->
            <div id="calendar-container"></div>

        <?php endif; ?>

    </div>
</div>

<!-- ============================================================ -->
<!-- Add Booking Modal -->
<!-- ============================================================ -->
<div class="modal fade" id="addBookingModal" tabindex="-1" aria-labelledby="addBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--colorPrimary); color: white;">
                <h5 class="modal-title" id="addBookingModalLabel"><i class="fas fa-calendar-plus me-2"></i>Thêm đơn đặt bàn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bookingForm" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_booking">
                    <input type="hidden" name="maCoSo" id="formMaCoSo" value="<?= $maCoSoHienTai ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tenKH" id="formTenKH" required placeholder="VD: Nguyễn Văn A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="sdt" id="formSDT" required placeholder="VD: 0912345678" pattern="[0-9]{9,11}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số khách <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="soLuongKH" id="formSoLuongKH" required min="1" max="50" placeholder="VD: 4">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Bàn <span class="text-danger">*</span></label>
                            <select class="form-select" name="maBan" id="formMaBan" required>
                                <option value="">-- Chọn bàn --</option>
                                <?php foreach ($tables as $t): ?>
                                    <option value="<?= $t['MaBan'] ?>"><?= htmlspecialchars($t['TenBan']) ?> (<?= $t['SucChua'] ?> người)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày đặt <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ngayDat" id="formNgayDat" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="gioBatDau" id="formGioBatDau" required value="18:00">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="ghiChu" id="formGhiChu" rows="2" placeholder="VD: Cần ghế cho trẻ em"></textarea>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="formError"></div>
                    <div class="alert alert-success mt-3 d-none" id="formSuccess"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success" id="btnSaveBooking">
                        <i class="fas fa-save"></i> Lưu đặt bàn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ============================================================ -->
<!-- Booking Detail Modal -->
<!-- ============================================================ -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--colorPrimary); color: white;">
                <h5 class="modal-title" id="bookingDetailModalLabel"><i class="fas fa-calendar-check me-2"></i>Chi tiết đặt bàn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Mã đơn</dt>
                    <dd class="col-sm-8" id="detailBookingCode">-</dd>

                    <dt class="col-sm-4">Bàn</dt>
                    <dd class="col-sm-8" id="detailBookingTable">-</dd>

                    <dt class="col-sm-4">Thời gian</dt>
                    <dd class="col-sm-8" id="detailBookingTime">-</dd>

                    <dt class="col-sm-4">Khách hàng</dt>
                    <dd class="col-sm-8" id="detailBookingCustomer">-</dd>

                    <dt class="col-sm-4">Số điện thoại</dt>
                    <dd class="col-sm-8" id="detailBookingPhone">-</dd>

                    <dt class="col-sm-4">Số khách</dt>
                    <dd class="col-sm-8" id="detailBookingGuests">-</dd>

                    <dt class="col-sm-4">Trạng thái</dt>
                    <dd class="col-sm-8" id="detailBookingStatus">-</dd>

                    <dt class="col-sm-4">Ghi chú</dt>
                    <dd class="col-sm-8 mb-0" id="detailBookingNote">Không có</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Toast UI Calendar JS -->
<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
<script>
(function() {
    'use strict';
    // ============================================================
    // Build calendars (tables) from PHP
    // ============================================================
    const calendarTables = <?= json_encode(array_map(function($t) {
        return [
            'id'   => (string)$t['MaBan'],
            'name' => htmlspecialchars($t['TenBan']) . ' (' . $t['SucChua'] . ' người)',
            'backgroundColor' => '#1B4E30',
            'dragBackgroundColor' => '#1B4E30',
            'borderColor' => '#163f27',
            'color' => '#ffffff'
        ];
    }, $tables)) ?>;

    // ============================================================
    // Build calendar events (bookings) from PHP
    // ============================================================
    function buildEvents() {
        const raw = <?= json_encode(array_map(function($b) {
            // Status: confirmed=da_xac_nhan, pending=cho_xac_nhan, cancelled/finished=others
            $status = $b['TrangThai'] ?? '';
            if ($status === 'da_xac_nhan') {
                $cls = 'booking-confirmed';
                $bg  = '#d4edda';
            } elseif ($status === 'cho_xac_nhan') {
                $cls = 'booking-pending';
                $bg  = '#fff3cd';
            } else {
                $cls = 'booking-cancelled';
                $bg  = '#e9ecef';
            }
            $tenKH  = htmlspecialchars($b['TenKH'] ?? '');
            $maDon  = $b['MaDon'];
            $soKH   = $b['SoLuongKH'];
            $gioBD  = date('H:i', strtotime($b['ThoiGianBatDau']));
            $ghiChu = $b['GhiChu'] ? htmlspecialchars($b['GhiChu']) : '';
            return [
                'id'           => (string)$b['MaDon'],
                'calendarId'   => (string)$b['MaBan'],
                'title'        => "#{$maDon} - " . ($b['TenBan'] ?? ''),
                'body'         => "{$tenKH} - {$soKH} khách",
                'start'        => date('Y-m-d\\TH:i:s', strtotime($b['ThoiGianBatDau'])),
                'end'          => date('Y-m-d\\TH:i:s', strtotime($b['ThoiGianBatDau'] . ' +2 hours')), 
                'category'     => 'time',
                'isPending'    => ($status === 'cho_xac_nhan'),
                'isFocused'    => false,
                'isVisible'   => true,
                'isReadOnly'   => true,
                'customClass'  => $cls,
                'backgroundColor' => $bg,
                'dragBackgroundColor' => $bg,
                'borderColor'  => 'transparent',
                'color'        => '#212529',
                'raw'          => [
                    'maDon'  => $maDon,
                    'tenKH'  => $tenKH,
                    'sdt'    => $b['SDT'] ?? '',
                    'soKH'   => $soKH,
                    'gio'    => $gioBD,
                    'trangThai' => $status,
                    'trangThaiLabel' => $status === 'da_xac_nhan' ? 'Đã xác nhận' : ($status === 'cho_xac_nhan' ? 'Chờ xác nhận' : 'Khác'),
                    'ghiChu' => $ghiChu,
                    'tenBan' => $b['TenBan'] ?? '',
                    'ngayGio' => date('d/m/Y H:i', strtotime($b['ThoiGianBatDau'])),
                ]
            ];
        }, $bookings)) ?>;

        return raw;
    }

    const allEvents = buildEvents();

    // ============================================================
    // Vietnamese i18n for Toast UI Calendar
    // ============================================================
    const vnI18n = {
        'en': {
            titles: {
                day: 'Ngày',
                week: 'Tuần',
                month: 'Tháng',
                today: 'Hôm nay',
                previous: 'Trước',
                next: 'Sau',
                todayLabel: 'Hôm nay'
            },
            daynames: ['CN', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'],
            monthnames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                         'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            abbreviationsDaynames: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
        }
    };
    // ============================================================
    // Initialize Calendar
    // ============================================================
    const calendar = new tui.Calendar(document.getElementById('calendar-container'), {
        defaultView: 'day',
        useCreationPopup: false,
        useDetailPopup: false,
        isReadOnly: true,
        usageStatistics: false,
        calendars: calendarTables,
        i18n: vnI18n['en'],
        week: {
            hourStart: 6,
            hourEnd: 24,
            eventView: ['time'],
            taskView: false
        },
        theme: {
            'week.timegridOneHour.height': '60px',
            'week.timegridLeft.width': '120px',
            'week.daygridLeft.width': '120px',
            'common.backgroundColor': '#ffffff',
            'week.holidayExceptThisMonth.color': '#ff4040',
            'week.weekend.backgroundColor': '#fafafa'
        }
    });

    if (allEvents.length > 0) {
        calendar.createEvents(allEvents);
    }

    function renderCalendarLayout() {
        window.requestAnimationFrame(function() {
            calendar.render();
        });
    }

    renderCalendarLayout();
    window.addEventListener('load', renderCalendarLayout);
    window.addEventListener('resize', renderCalendarLayout);

    // ============================================================
    // View toggle buttons
    // ============================================================
    const btnDay   = document.getElementById('btnDay');
    const btnWeek  = document.getElementById('btnWeek');
    const btnMonth = document.getElementById('btnMonth');

    function setActiveBtn(btn) {
        [btnDay, btnWeek, btnMonth].forEach(b => b.classList.remove('btn-primary', 'active'));
        [btnDay, btnWeek, btnMonth].forEach(b => b.classList.add('btn-outline-primary'));
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary', 'active');
    }

    btnDay.addEventListener('click', function() {
        calendar.changeView('day', true);
        setActiveBtn(btnDay);
    });
    btnWeek.addEventListener('click', function() {
        calendar.changeView('week', true);
        setActiveBtn(btnWeek);
    });
    btnMonth.addEventListener('click', function() {
        calendar.changeView('month', true);
        setActiveBtn(btnMonth);
    });

    // ============================================================
    // Navigation
    // ============================================================
    document.getElementById('btnPrev').addEventListener('click', function() {
        calendar.prev();
    });
    document.getElementById('btnNext').addEventListener('click', function() {
        calendar.next();
    });
    document.getElementById('btnToday').addEventListener('click', function() {
        calendar.today();
    });

    // ============================================================
    // Detail popup on hover (custom tooltip via raw data)
    // ============================================================
    calendar.on('clickEvent', function(calendarEvent) {
        const raw = calendarEvent.event.raw || {};

        if (!bookingDetailModal) {
            return;
        }

        document.getElementById('detailBookingCode').textContent = raw.maDon ? '#' + raw.maDon : '-';
        document.getElementById('detailBookingTable').textContent = raw.tenBan || '-';
        document.getElementById('detailBookingTime').textContent = raw.ngayGio || '-';
        document.getElementById('detailBookingCustomer').textContent = raw.tenKH || '-';
        document.getElementById('detailBookingPhone').textContent = raw.sdt || '-';
        document.getElementById('detailBookingGuests').textContent = raw.soKH ? (raw.soKH + ' khách') : '-';
        document.getElementById('detailBookingStatus').textContent = raw.trangThaiLabel || raw.trangThai || '-';
        document.getElementById('detailBookingNote').textContent = raw.ghiChu || 'Không có';

        bookingDetailModal.show();
    });

    // ============================================================
    // Booking form submission via AJAX
    // ============================================================
    const bookingForm   = document.getElementById('bookingForm');
    const formError     = document.getElementById('formError');
    const formSuccess   = document.getElementById('formSuccess');
    const btnSaveBooking = document.getElementById('btnSaveBooking');
    const addBookingModal = document.getElementById('addBookingModal');
    const bookingDetailModalEl = document.getElementById('bookingDetailModal');
    const bookingDetailModal = bookingDetailModalEl ? new bootstrap.Modal(bookingDetailModalEl) : null;

    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Native HTML5 validation check
        if (!bookingForm.checkValidity()) {
            bookingForm.reportValidity();
            return;
        }

        formError.classList.add('d-none');
        formSuccess.classList.add('d-none');
        btnSaveBooking.disabled = true;
        btnSaveBooking.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

        const formData = new FormData(bookingForm);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            return response.text().then(function(text) {
                let data = null;

                try {
                    data = JSON.parse(text);
                } catch (error) {
                    throw new Error('INVALID_JSON_RESPONSE');
                }

                if (!response.ok) {
                    throw new Error(data.message || 'REQUEST_FAILED');
                }

                return data;
            });
        })
        .then(function(data) {
            if (data.success) {
                formSuccess.textContent = data.message;
                formSuccess.classList.remove('d-none');
                bookingForm.reset();
                document.getElementById('formMaCoSo').value = <?= $maCoSoHienTai ?>;
                document.getElementById('formNgayDat').value = '<?= date('Y-m-d') ?>';
                document.getElementById('formGioBatDau').value = '18:00';

                // Reload page to refresh calendar
                setTimeout(function() {
                    window.location.reload();
                }, 1200);
            } else {
                formError.textContent = data.message;
                formError.classList.remove('d-none');
                btnSaveBooking.disabled = false;
                btnSaveBooking.innerHTML = '<i class="fas fa-save"></i> Lưu đặt bàn';
            }
        })
        .catch(function(error) {
            formError.textContent = error && error.message && error.message !== 'INVALID_JSON_RESPONSE'
                ? error.message
                : 'Phản hồi máy chủ không hợp lệ. Vui lòng thử lại.';
            formError.classList.remove('d-none');
            btnSaveBooking.disabled = false;
            btnSaveBooking.innerHTML = '<i class="fas fa-save"></i> Lưu đặt bàn';
        });
    });

    function resetBookingForm() {
        bookingForm.reset();
        formError.classList.add('d-none');
        formSuccess.classList.add('d-none');
        btnSaveBooking.disabled = false;
        btnSaveBooking.innerHTML = '<i class="fas fa-save"></i> Lưu đặt bàn';
        document.getElementById('formMaCoSo').value = <?= $maCoSoHienTai ?>;
        document.getElementById('formNgayDat').value = '<?= date('Y-m-d') ?>';
        document.getElementById('formGioBatDau').value = '18:00';
    }

    if (addBookingModal) {
        addBookingModal.addEventListener('show.bs.modal', resetBookingForm);
    }

    // ============================================================
    // Sidebar collapse compatibility
    // ============================================================
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    if (sidebar && mainContent) {
        const checkCollapsed = function() {
            if (sidebar.classList.contains('collapsed') || mainContent.classList.contains('expanded')) {
                document.getElementById('calendar-container')?.style && (document.getElementById('calendar-container').style.width = '100%');
                calendar.render();
            }
        };
        new MutationObserver(checkCollapsed).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        checkCollapsed();
    }

})();
</script>



