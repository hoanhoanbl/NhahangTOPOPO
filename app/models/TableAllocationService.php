<?php

class TableAllocationService
{
    public static function allocateForBooking(mysqli $conn, int $branchId, int $guestCount, string $bookingDateTime, int $leadHours, bool $onlineOnly = true, int $maxMergeTables = 4): array|false
    {
        if ($guestCount <= 0) {
            return false;
        }

        $leadHours = max(1, $leadHours);
        $timeStart = date('Y-m-d H:i:s', strtotime($bookingDateTime . " -{$leadHours} hours"));
        $timeEnd = date('Y-m-d H:i:s', strtotime($bookingDateTime . " +{$leadHours} hours"));

        $tables = self::fetchCandidateTables($conn, $branchId, $onlineOnly);
        if (empty($tables)) {
            return false;
        }

        $conflictIds = self::getConflictingTableIds($conn, $branchId, $timeStart, $timeEnd, $leadHours);
        $available = array_values(array_filter($tables, static function ($table) use ($conflictIds) {
            return !in_array((int)$table['MaBan'], $conflictIds, true);
        }));

        if (empty($available)) {
            return false;
        }

        $singleCandidates = array_values(array_filter($available, static function ($table) use ($guestCount) {
            return (int)$table['SucChua'] >= $guestCount;
        }));

        if (!empty($singleCandidates)) {
            usort($singleCandidates, static function ($a, $b) use ($guestCount) {
                $extraA = (int)$a['SucChua'] - $guestCount;
                $extraB = (int)$b['SucChua'] - $guestCount;
                if ((int)$a['SucChua'] !== (int)$b['SucChua']) {
                    return (int)$a['SucChua'] <=> (int)$b['SucChua'];
                }
                if ($extraA !== $extraB) {
                    return $extraA <=> $extraB;
                }
                return (int)$a['MaBan'] <=> (int)$b['MaBan'];
            });

            return [
                'mode' => 'single',
                'zone' => $singleCandidates[0]['ZoneBan'] ?? '',
                'tables' => [self::tablePayload($singleCandidates[0])],
                'capacity' => (int)$singleCandidates[0]['SucChua'],
            ];
        }

        $mergeable = array_values(array_filter($available, static function ($table) {
            return (int)$table['GhepBanDuoc'] === 1 && strtolower(trim((string)$table['TrangThai'])) === 'active';
        }));

        if (empty($mergeable)) {
            return false;
        }

        $zones = [];
        foreach ($mergeable as $table) {
            $zone = trim((string)($table['ZoneBan'] ?? ''));
            if ($zone === '') {
                continue;
            }
            $zones[$zone][] = $table;
        }

        if (empty($zones)) {
            return false;
        }

        $bestPlan = null;
        foreach ($zones as $zone => $tablesInZone) {
            $plan = self::bestCombinationForZone($tablesInZone, $guestCount, $maxMergeTables);
            if (!$plan) {
                continue;
            }

            $candidateScore = [
                count($plan['tables']),
                $plan['capacity'] - $guestCount,
                strtolower((string)$zone),
                self::tableSetKey($plan['tables']),
            ];

            if ($bestPlan === null) {
                $bestPlan = ['zone' => $zone, 'tables' => $plan['tables'], 'capacity' => $plan['capacity'], 'score' => $candidateScore];
                continue;
            }

            if (self::compareScore($candidateScore, $bestPlan['score']) < 0) {
                $bestPlan = ['zone' => $zone, 'tables' => $plan['tables'], 'capacity' => $plan['capacity'], 'score' => $candidateScore];
            }
        }

        if ($bestPlan === null) {
            return false;
        }

        return [
            'mode' => 'merge',
            'zone' => $bestPlan['zone'],
            'tables' => array_map([self::class, 'tablePayload'], $bestPlan['tables']),
            'capacity' => $bestPlan['capacity'],
        ];
    }

    public static function isTableAvailable(mysqli $conn, int $branchId, int $tableId, string $bookingDateTime, int $leadHours): bool
    {
        $leadHours = max(1, $leadHours);
        $timeStart = date('Y-m-d H:i:s', strtotime($bookingDateTime . " -{$leadHours} hours"));
        $timeEnd = date('Y-m-d H:i:s', strtotime($bookingDateTime . " +{$leadHours} hours"));

        $sql = "SELECT 1
                FROM dondatban d
                INNER JOIN dondatban_ban db ON d.MaDon = db.MaDon
                WHERE d.MaCoSo = ?
                  AND db.MaBan = ?
                  AND d.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                  AND d.ThoiGianBatDau <= ?
                  AND TIMESTAMPADD(HOUR, ?, d.ThoiGianBatDau) >= ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'iisis', $branchId, $tableId, $timeEnd, $leadHours, $timeStart);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $hasConflict = $result && mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return !$hasConflict;
    }

    private static function fetchCandidateTables(mysqli $conn, int $branchId, bool $onlineOnly): array
    {
        $onlineFlag = $onlineOnly ? 1 : 0;
        $sql = "SELECT MaBan, MaBanCode, MaCoSo, TenBan, ZoneBan, SucChua, SucChuaToiDa, OnlineBookable, GhepBanDuoc, TrangThai
                FROM ban
                WHERE MaCoSo = ?
                  AND LOWER(TRIM(TrangThai)) = 'active'
                  AND (? = 0 OR OnlineBookable = 1)
                ORDER BY SucChua ASC, MaBan ASC";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ii', $branchId, $onlineFlag);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $rows;
    }

    private static function getConflictingTableIds(mysqli $conn, int $branchId, string $timeStart, string $timeEnd, int $leadHours): array
    {
        $sql = "SELECT DISTINCT db.MaBan
                FROM dondatban d
                INNER JOIN dondatban_ban db ON d.MaDon = db.MaDon
                WHERE d.MaCoSo = ?
                  AND d.TrangThai IN ('cho_xac_nhan', 'da_xac_nhan')
                  AND d.ThoiGianBatDau <= ?
                  AND TIMESTAMPADD(HOUR, ?, d.ThoiGianBatDau) >= ?";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'isis', $branchId, $timeEnd, $leadHours, $timeStart);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $ids = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ids[] = (int)$row['MaBan'];
        }
        mysqli_stmt_close($stmt);

        return $ids;
    }

    private static function bestCombinationForZone(array $tables, int $guestCount, int $maxMergeTables): array|false
    {
        $maxMergeTables = max(2, $maxMergeTables);
        usort($tables, static function ($a, $b) {
            $capCmp = (int)$a['SucChua'] <=> (int)$b['SucChua'];
            if ($capCmp !== 0) {
                return $capCmp;
            }
            return (int)$a['MaBan'] <=> (int)$b['MaBan'];
        });

        $best = null;
        $current = [];
        self::searchCombinations($tables, $guestCount, $maxMergeTables, 0, $current, $best);

        return $best ?: false;
    }

    private static function searchCombinations(array $tables, int $guestCount, int $maxMergeTables, int $start, array &$current, ?array &$best): void
    {
        $count = count($current);
        if ($count > $maxMergeTables) {
            return;
        }

        $capacity = 0;
        foreach ($current as $item) {
            $capacity += (int)$item['SucChua'];
        }

        if ($count >= 2 && $capacity >= $guestCount) {
            $candidate = [
                'tables' => $current,
                'capacity' => $capacity,
                'score' => [$count, $capacity - $guestCount, self::tableSetKey($current)],
            ];

            if ($best === null || self::compareScore($candidate['score'], $best['score']) < 0) {
                $best = $candidate;
            }
            return;
        }

        for ($i = $start; $i < count($tables); $i++) {
            $current[] = $tables[$i];
            self::searchCombinations($tables, $guestCount, $maxMergeTables, $i + 1, $current, $best);
            array_pop($current);
        }
    }

    private static function tablePayload(array $table): array
    {
        return [
            'MaBan' => (int)$table['MaBan'],
            'TenBan' => (string)($table['TenBan'] ?? ''),
            'ZoneBan' => (string)($table['ZoneBan'] ?? ''),
            'SucChua' => (int)($table['SucChua'] ?? 0),
            'MaBanCode' => (string)($table['MaBanCode'] ?? ''),
        ];
    }

    private static function tableSetKey(array $tables): string
    {
        $ids = array_map(static fn($table) => (int)$table['MaBan'], $tables);
        sort($ids, SORT_NUMERIC);
        return implode('-', $ids);
    }

    private static function compareScore(array $left, array $right): int
    {
        $length = min(count($left), count($right));
        for ($i = 0; $i < $length; $i++) {
            if ($left[$i] === $right[$i]) {
                continue;
            }
            return $left[$i] <=> $right[$i];
        }
        return count($left) <=> count($right);
    }
}
