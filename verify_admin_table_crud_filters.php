<?php
require_once __DIR__ . '/config/connect.php';

function pass($label) { echo "[PASS] {$label}\n"; }
function fail($label) { echo "[FAIL] {$label}\n"; }

$okAll = true;

mysqli_begin_transaction($conn);
try {
    $branchStmt = mysqli_prepare($conn, "INSERT INTO coso (TenCoSo, DienThoai, DiaChi, AnhUrl) VALUES ('SPX Admin Branch', '0000000000', 'SPX', 'spx')");
    mysqli_stmt_execute($branchStmt);
    $branchId = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($branchStmt);

    // Create
    $insert = mysqli_prepare($conn, "INSERT INTO ban (MaBanCode, MaCoSo, TenBan, ZoneBan, SucChua, SucChuaToiDa, OnlineBookable, GhepBanDuoc, TrangThai, GhiChu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $code = 'SPX-CRUD-01';
    $name = 'SPX Bàn 01';
    $zone = 'SPX-Z1';
    $s1 = 4; $s2 = 6; $online = 1; $merge = 1; $status = 'Active'; $note = 'ghi chu';
    mysqli_stmt_bind_param($insert, 'sissiiiiss', $code, $branchId, $name, $zone, $s1, $s2, $online, $merge, $status, $note);
    mysqli_stmt_execute($insert);
    $tableId = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($insert);

    // Update
    $upd = mysqli_prepare($conn, "UPDATE ban SET ZoneBan=?, SucChua=?, SucChuaToiDa=?, OnlineBookable=?, GhepBanDuoc=?, TrangThai=?, GhiChu=? WHERE MaBan=?");
    $zone2='SPX-Z2'; $s1u=5; $s2u=8; $online2=0; $merge2=0; $status2='Inactive'; $note2='updated';
    mysqli_stmt_bind_param($upd, 'siiiissi', $zone2, $s1u, $s2u, $online2, $merge2, $status2, $note2, $tableId);
    mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);

    $check = mysqli_prepare($conn, "SELECT * FROM ban WHERE MaBan=?");
    mysqli_stmt_bind_param($check, 'i', $tableId);
    mysqli_stmt_execute($check);
    $res = mysqli_stmt_get_result($check);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($check);

    $crudOk = $row
        && $row['MaBanCode'] === $code
        && $row['ZoneBan'] === $zone2
        && (int)$row['SucChua'] === $s1u
        && (int)$row['SucChuaToiDa'] === $s2u
        && (int)$row['OnlineBookable'] === $online2
        && (int)$row['GhepBanDuoc'] === $merge2
        && $row['TrangThai'] === $status2;

    if ($crudOk) { pass('Admin CRUD round-trip for full ban fields'); } else { fail('Admin CRUD round-trip for full ban fields'); $okAll=false; }

    // Filter/search composable + pagination
    $extraInsert = mysqli_prepare($conn, "INSERT INTO ban (MaBanCode, MaCoSo, TenBan, ZoneBan, SucChua, SucChuaToiDa, OnlineBookable, GhepBanDuoc, TrangThai, GhiChu) VALUES (?, ?, ?, ?, 2, 2, 1, 1, 'Active', '')");
    $codeA='SPX-FLT-A'; $nameA='Bàn Alpha'; $zoneA='SPX-Z2';
    mysqli_stmt_bind_param($extraInsert, 'siss', $codeA, $branchId, $nameA, $zoneA);
    mysqli_stmt_execute($extraInsert);
    $codeB='SPX-FLT-B'; $nameB='Bàn Beta'; $zoneB='SPX-Z9';
    mysqli_stmt_bind_param($extraInsert, 'siss', $codeB, $branchId, $nameB, $zoneB);
    mysqli_stmt_execute($extraInsert);
    mysqli_stmt_close($extraInsert);

    $keyword = '%SPX-FLT%';
    $q = mysqli_prepare($conn, "SELECT MaBanCode FROM ban WHERE MaCoSo=? AND ZoneBan=? AND TrangThai='Active' AND (MaBanCode LIKE ? OR TenBan LIKE ?) ORDER BY MaBan LIMIT 0, 10");
    mysqli_stmt_bind_param($q, 'isss', $branchId, $zoneA, $keyword, $keyword);
    mysqli_stmt_execute($q);
    $r = mysqli_stmt_get_result($q);
    $codes = [];
    while ($x = mysqli_fetch_assoc($r)) { $codes[] = $x['MaBanCode']; }
    mysqli_stmt_close($q);

    $filterOk = count($codes) === 1 && $codes[0] === $codeA;
    if ($filterOk) { pass('Filter/search composable and pagination-ready query path'); } else { fail('Filter/search composable and pagination-ready query path'); $okAll=false; }

    mysqli_rollback($conn);

    if (!$okAll) {
        exit(1);
    }
    echo "All admin CRUD/filter verifications passed.\n";
    exit(0);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    echo '[ERROR] ' . $e->getMessage() . "\n";
    exit(1);
}
