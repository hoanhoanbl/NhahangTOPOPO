<?php

function tableAllowedStatuses(): array
{
    return ['Active', 'Inactive'];
}

function tableNormalizeFlag($value): int
{
    return ((string)$value === '1' || $value === 1) ? 1 : 0;
}

function tableIsValidFlag($value): bool
{
    if ($value === 0 || $value === 1) {
        return true;
    }

    if (is_string($value)) {
        $trimmed = trim($value);
        return $trimmed === '0' || $trimmed === '1';
    }

    return false;
}

function tableCollectInput(array $source): array
{
    return [
        'MaCoSo' => (int)($source['MaCoSo'] ?? 0),
        'MaBanCode' => trim((string)($source['MaBanCode'] ?? '')),
        'TenBan' => trim((string)($source['TenBan'] ?? '')),
        'ZoneBan' => trim((string)($source['ZoneBan'] ?? '')),
        'SucChua' => (int)($source['SucChua'] ?? 0),
        'SucChuaToiDa' => (int)($source['SucChuaToiDa'] ?? 0),
        'OnlineBookableRaw' => $source['OnlineBookable'] ?? null,
        'GhepBanDuocRaw' => $source['GhepBanDuoc'] ?? null,
        'OnlineBookable' => tableNormalizeFlag($source['OnlineBookable'] ?? 0),
        'GhepBanDuoc' => tableNormalizeFlag($source['GhepBanDuoc'] ?? 0),
        'TrangThai' => trim((string)($source['TrangThai'] ?? '')),
        'GhiChu' => trim((string)($source['GhiChu'] ?? '')),
    ];
}

function tableValidateInput(mysqli $conn, array $payload, ?int $excludeMaBan = null): array
{
    $errors = [];

    if ($payload['MaCoSo'] <= 0) {
        $errors[] = 'Cơ sở không hợp lệ.';
    }
    if ($payload['MaBanCode'] === '') {
        $errors[] = 'Mã bàn là bắt buộc.';
    }
    if ($payload['TenBan'] === '') {
        $errors[] = 'Tên bàn là bắt buộc.';
    }
    if ($payload['ZoneBan'] === '') {
        $errors[] = 'Zone bàn là bắt buộc.';
    }
    if ($payload['SucChua'] <= 0) {
        $errors[] = 'Sức chứa phải lớn hơn 0.';
    }
    if ($payload['SucChuaToiDa'] <= 0) {
        $errors[] = 'Sức chứa tối đa phải lớn hơn 0.';
    }
    if ($payload['SucChuaToiDa'] < $payload['SucChua']) {
        $errors[] = 'Sức chứa tối đa phải lớn hơn hoặc bằng sức chứa.';
    }

    if (!tableIsValidFlag($payload['OnlineBookableRaw'])) {
        $errors[] = 'OnlineBookable chỉ được nhận 0 hoặc 1.';
    }
    if (!tableIsValidFlag($payload['GhepBanDuocRaw'])) {
        $errors[] = 'GhepBanDuoc chỉ được nhận 0 hoặc 1.';
    }

    if (!in_array($payload['TrangThai'], tableAllowedStatuses(), true)) {
        $errors[] = 'Trạng thái bàn không hợp lệ.';
    }

    if ($payload['MaCoSo'] > 0 && $payload['MaBanCode'] !== '') {
        if ($excludeMaBan === null) {
            $sql = "SELECT MaBan FROM ban WHERE MaCoSo = ? AND MaBanCode = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'is', $payload['MaCoSo'], $payload['MaBanCode']);
        } else {
            $sql = "SELECT MaBan FROM ban WHERE MaCoSo = ? AND MaBanCode = ? AND MaBan <> ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isi', $payload['MaCoSo'], $payload['MaBanCode'], $excludeMaBan);
        }

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result && mysqli_fetch_assoc($result)) {
                $errors[] = 'Mã bàn đã tồn tại trong cùng cơ sở.';
            }
            mysqli_stmt_close($stmt);
        }
    }

    return $errors;
}

