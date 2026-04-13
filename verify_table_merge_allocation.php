<?php
require_once __DIR__ . '/config/connect.php';
require_once __DIR__ . '/app/models/TableAllocationService.php';

mysqli_set_charset($conn, 'utf8mb4');

function logResult(string $label, bool $ok): void {
    echo ($ok ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
}

function clearTestTables(mysqli $conn): void {
    $sql = "DELETE FROM ban WHERE MaBanCode LIKE 'SPX-%'";
    mysqli_query($conn, $sql);
}

function insertTable(mysqli $conn, array $t): int {
    $sql = "INSERT INTO ban (MaBanCode, MaCoSo, TenBan, ZoneBan, SucChua, SucChuaToiDa, OnlineBookable, GhepBanDuoc, TrangThai, GhiChu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'sissiiiiss',
        $t['MaBanCode'],
        $t['MaCoSo'],
        $t['TenBan'],
        $t['ZoneBan'],
        $t['SucChua'],
        $t['SucChuaToiDa'],
        $t['OnlineBookable'],
        $t['GhepBanDuoc'],
        $t['TrangThai'],
        $t['GhiChu']
    );
    mysqli_stmt_execute($stmt);
    $id = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

$allOk = true;
$branchId = 0;
$bookingDateTime = '2026-04-20 19:00:00';
$leadHours = 2;

mysqli_begin_transaction($conn);
try {
    $branchStmt = mysqli_prepare($conn, "INSERT INTO coso (TenCoSo, DienThoai, DiaChi, AnhUrl) VALUES ('SPX Test Branch', '0000000000', 'SPX', 'spx')");
    mysqli_stmt_execute($branchStmt);
    $branchId = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($branchStmt);

    clearTestTables($conn);

    // Scenario 1: single-table first
    $t1 = insertTable($conn, ['MaBanCode'=>'SPX-S1-04','MaCoSo'=>$branchId,'TenBan'=>'SPX S1 4','ZoneBan'=>'Z1','SucChua'=>4,'SucChuaToiDa'=>4,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);
    $t2 = insertTable($conn, ['MaBanCode'=>'SPX-S1-06','MaCoSo'=>$branchId,'TenBan'=>'SPX S1 6','ZoneBan'=>'Z1','SucChua'=>6,'SucChuaToiDa'=>6,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);

    $plan = TableAllocationService::allocateForBooking($conn, $branchId, 3, $bookingDateTime, $leadHours, true, 4);
    $ok = $plan && $plan['mode'] === 'single' && (int)$plan['tables'][0]['MaBan'] === $t1;
    logResult('Single-table priority', $ok);
    $allOk = $allOk && $ok;

    clearTestTables($conn);

    // Scenario 2: merge success in same zone
    $m1 = insertTable($conn, ['MaBanCode'=>'SPX-M1-04','MaCoSo'=>$branchId,'TenBan'=>'SPX M1 4','ZoneBan'=>'ZA','SucChua'=>4,'SucChuaToiDa'=>4,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);
    $m2 = insertTable($conn, ['MaBanCode'=>'SPX-M1-06','MaCoSo'=>$branchId,'TenBan'=>'SPX M1 6','ZoneBan'=>'ZA','SucChua'=>6,'SucChuaToiDa'=>6,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);
    insertTable($conn, ['MaBanCode'=>'SPX-M1-03','MaCoSo'=>$branchId,'TenBan'=>'SPX M1 3','ZoneBan'=>'ZB','SucChua'=>3,'SucChuaToiDa'=>3,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);

    $plan = TableAllocationService::allocateForBooking($conn, $branchId, 10, $bookingDateTime, $leadHours, true, 4);
    $ids = $plan ? array_map(static fn($x)=>(int)$x['MaBan'], $plan['tables']) : [];
    sort($ids);
    $expected = [$m1, $m2];
    sort($expected);
    $ok = $plan && $plan['mode'] === 'merge' && $ids === $expected;
    logResult('Same-zone merge success', $ok);
    $allOk = $allOk && $ok;

    clearTestTables($conn);

    // Scenario 3: cross-zone rejection
    insertTable($conn, ['MaBanCode'=>'SPX-X1-06','MaCoSo'=>$branchId,'TenBan'=>'SPX X1 6','ZoneBan'=>'Z1','SucChua'=>6,'SucChuaToiDa'=>6,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);
    insertTable($conn, ['MaBanCode'=>'SPX-X2-06','MaCoSo'=>$branchId,'TenBan'=>'SPX X2 6','ZoneBan'=>'Z2','SucChua'=>6,'SucChuaToiDa'=>6,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);

    $plan = TableAllocationService::allocateForBooking($conn, $branchId, 10, $bookingDateTime, $leadHours, true, 4);
    $ok = ($plan === false);
    logResult('Cross-zone merge rejected', $ok);
    $allOk = $allOk && $ok;

    clearTestTables($conn);

    // Scenario 4: inactive rejection
    insertTable($conn, ['MaBanCode'=>'SPX-I1-08','MaCoSo'=>$branchId,'TenBan'=>'SPX I1 8','ZoneBan'=>'ZI','SucChua'=>8,'SucChuaToiDa'=>8,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Inactive','GhiChu'=>'']);
    $plan = TableAllocationService::allocateForBooking($conn, $branchId, 6, $bookingDateTime, $leadHours, true, 4);
    $ok = ($plan === false);
    logResult('Inactive table rejected', $ok);
    $allOk = $allOk && $ok;

    clearTestTables($conn);

    // Scenario 5: non-mergeable rejection when no single table fits
    insertTable($conn, ['MaBanCode'=>'SPX-N1-04','MaCoSo'=>$branchId,'TenBan'=>'SPX N1 4','ZoneBan'=>'ZN','SucChua'=>4,'SucChuaToiDa'=>4,'OnlineBookable'=>1,'GhepBanDuoc'=>0,'TrangThai'=>'Active','GhiChu'=>'']);
    insertTable($conn, ['MaBanCode'=>'SPX-N2-04','MaCoSo'=>$branchId,'TenBan'=>'SPX N2 4','ZoneBan'=>'ZN','SucChua'=>4,'SucChuaToiDa'=>4,'OnlineBookable'=>1,'GhepBanDuoc'=>0,'TrangThai'=>'Active','GhiChu'=>'']);
    $plan = TableAllocationService::allocateForBooking($conn, $branchId, 7, $bookingDateTime, $leadHours, true, 4);
    $ok = ($plan === false);
    logResult('Non-mergeable tables rejected for merge', $ok);
    $allOk = $allOk && $ok;

    clearTestTables($conn);

    // Scenario 6: ranking behavior (fewest tables, then least extra seats)
    $r1 = insertTable($conn, ['MaBanCode'=>'SPX-R1-02','MaCoSo'=>$branchId,'TenBan'=>'SPX R1 2','ZoneBan'=>'ZR','SucChua'=>2,'SucChuaToiDa'=>2,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);
    $r2 = insertTable($conn, ['MaBanCode'=>'SPX-R2-04','MaCoSo'=>$branchId,'TenBan'=>'SPX R2 4','ZoneBan'=>'ZR','SucChua'=>4,'SucChuaToiDa'=>4,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);
    $r3 = insertTable($conn, ['MaBanCode'=>'SPX-R3-06','MaCoSo'=>$branchId,'TenBan'=>'SPX R3 6','ZoneBan'=>'ZR','SucChua'=>6,'SucChuaToiDa'=>6,'OnlineBookable'=>1,'GhepBanDuoc'=>1,'TrangThai'=>'Active','GhiChu'=>'']);

    $plan = TableAllocationService::allocateForBooking($conn, $branchId, 8, $bookingDateTime, $leadHours, true, 4);
    $ids = $plan ? array_map(static fn($x)=>(int)$x['MaBan'], $plan['tables']) : [];
    sort($ids);
    $expected = [$r1, $r3];
    sort($expected);
    $ok = $plan && $ids === $expected;
    logResult('Ranking: fewest tables then least extra seats', $ok);
    $allOk = $allOk && $ok;

    // Scenario 7: persistence multiple tables for one booking
    $bookingStmt = mysqli_prepare($conn, "INSERT INTO dondatban (MaKH, MaCoSo, SoLuongKH, ThoiGianBatDau, GhiChu, TrangThai, ThoiGianTao) VALUES (2, ?, 8, ?, 'SPX verify', 'cho_xac_nhan', NOW())");
    mysqli_stmt_bind_param($bookingStmt, 'is', $branchId, $bookingDateTime);
    mysqli_stmt_execute($bookingStmt);
    $bookingId = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($bookingStmt);

    if (!$plan || empty($plan['tables'])) {
        throw new RuntimeException('No plan to persist');
    }

    $insertMap = mysqli_prepare($conn, "INSERT INTO dondatban_ban (MaDon, MaBan) VALUES (?, ?)");
    foreach ($plan['tables'] as $tb) {
        $tbId = (int)$tb['MaBan'];
        mysqli_stmt_bind_param($insertMap, 'ii', $bookingId, $tbId);
        mysqli_stmt_execute($insertMap);
    }
    mysqli_stmt_close($insertMap);

    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS c FROM dondatban_ban WHERE MaDon = ?");
    mysqli_stmt_bind_param($checkStmt, 'i', $bookingId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    $countRows = (int)(mysqli_fetch_assoc($checkResult)['c'] ?? 0);
    mysqli_stmt_close($checkStmt);

    $ok = $countRows === count($plan['tables']) && $countRows >= 2;
    logResult('Persistence: one booking stores multiple table rows', $ok);
    $allOk = $allOk && $ok;

    mysqli_rollback($conn);

    if (!$allOk) {
        exit(1);
    }

    echo "All allocation and persistence verifications passed." . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    echo '[ERROR] ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
