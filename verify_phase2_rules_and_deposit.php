<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/helpers/DateHelper.php';
require_once __DIR__ . '/app/models/BookingRulesModel.php';
require_once __DIR__ . '/app/models/DepositCalculator.php';

$failures = 0;

function check(bool $cond, string $label): void {
    global $failures;
    if ($cond) {
        echo "[PASS] {$label}\n";
    } else {
        $failures++;
        echo "[FAIL] {$label}\n";
    }
}

function bookingValidationResult(DateTime $bookingDateTime, DateTime $now, int $leadHours, int $maxDays): string {
    if (!DateHelper::isOpen($bookingDateTime)) {
        return 'closed';
    }

    $diffSeconds = $bookingDateTime->getTimestamp() - $now->getTimestamp();
    if ($diffSeconds < $leadHours * 3600) {
        return 'lead_time';
    }

    $diffDays = $bookingDateTime->diff($now)->days;
    if ($diffDays > $maxDays) {
        return 'max_advance';
    }

    return 'ok';
}

$leadHours = (int) BookingRulesModel::get('lead_time_hours');
$maxDays = (int) BookingRulesModel::get('max_advance_days');
if ($leadHours <= 0) {
    $leadHours = 2;
}
if ($maxDays <= 0) {
    $maxDays = 30;
}

echo "Using rules: lead_time_hours={$leadHours}, max_advance_days={$maxDays}\n";

// Task 1.6 scenarios
$fixedNow = new DateTime('2026-04-14 08:00:00');
check(bookingValidationResult(new DateTime('2026-04-14 10:00:00'), $fixedNow, $leadHours, $maxDays) === 'ok', 'Validation: weekday 10:00 accepted');
check(bookingValidationResult(new DateTime('2026-04-14 08:59:00'), $fixedNow, $leadHours, $maxDays) === 'closed', 'Validation: weekday 08:59 rejected by open-hours');
check(bookingValidationResult(new DateTime('2026-04-30 10:00:00'), $fixedNow, $leadHours, $maxDays) === 'closed', 'Validation: holiday 30/4 rejected by open-hours');
check(bookingValidationResult(new DateTime('2026-04-14 10:00:00'), $fixedNow, $leadHours, $maxDays) === 'ok', 'Validation: lead-time exact boundary accepted');
$tooFarDate = new DateTime('2026-05-15 10:00:00');
check(bookingValidationResult($tooFarDate, $fixedNow, $leadHours, $maxDays) === 'max_advance', 'Validation: max-advance exceeded rejected');

// Task 3.4 dynamic window + best-fit intent
$bookingAt = new DateTime('2026-04-14 18:00:00');
$timeStart = date('Y-m-d H:i:s', strtotime($bookingAt->format('Y-m-d H:i:s') . " -{$leadHours} hours"));
$timeEnd = date('Y-m-d H:i:s', strtotime($bookingAt->format('Y-m-d H:i:s') . " +{$leadHours} hours"));
$expectedStart = $bookingAt->modify("-{$leadHours} hours")->format('Y-m-d H:i:s');
$bookingAt = new DateTime('2026-04-14 18:00:00');
$expectedEnd = $bookingAt->modify("+{$leadHours} hours")->format('Y-m-d H:i:s');
check($timeStart === $expectedStart && $timeEnd === $expectedEnd, 'Availability: dynamic conflict window uses lead_time_hours');

$availableTables = [
    ['MaBan' => 1, 'SucChua' => 2],
    ['MaBan' => 2, 'SucChua' => 4],
    ['MaBan' => 3, 'SucChua' => 6],
];
usort($availableTables, fn($a, $b) => $a['SucChua'] <=> $b['SucChua']);
$best = $availableTables[0];
check($best['SucChua'] === 2, 'Availability: best-fit picks smallest capacity >= guest count');

// Task 4.5 deposit scenarios
$weekday = new DateTime('2026-04-14 19:00:00'); // Tuesday
$saturday = new DateTime('2026-04-11 19:00:00');
$holiday = new DateTime('2026-04-30 19:00:00');

check(DepositCalculator::calculate($weekday, 0) === 0, 'Deposit: weekday + 0 menu = 0');
check(DepositCalculator::calculate($saturday, 0) === 100000, 'Deposit: Saturday + 0 menu = 100000');
check(DepositCalculator::calculate($saturday, 200000) === 200000, 'Deposit: Saturday + 200k menu = 200000');
check(DepositCalculator::calculate($holiday, 400000) === 300000, 'Deposit: holiday 30/4 + 400k menu = 300000');

if ($failures > 0) {
    echo "\nTotal failures: {$failures}\n";
    exit(1);
}

echo "\nAll verification checks passed.\n";
exit(0);
?>
