<?php
require_once __DIR__ . '/app/helpers/DateHelper.php';

function test($label, $result) {
    echo "$label: " . ($result ? "PASS" : "FAIL") . PHP_EOL;
}

function pass($label) { test($label, true); }
function fail($label) { test($label, false); }

// isOpen scenarios
$d1 = new DateTime('2026-04-28 10:00'); // Tuesday
test("Weekday 10:00 isOpen", DateHelper::isOpen($d1));

$d2 = new DateTime('2026-04-29 08:59'); // Wednesday
test("Weekday 08:59 isOpen = false", !DateHelper::isOpen($d2));

$d3 = new DateTime('2026-04-29 22:00'); // Wednesday
test("Weekday 22:00 isOpen = false", !DateHelper::isOpen($d3));

$d4 = new DateTime('2026-04-30 12:00'); // 30/4 holiday
test("30/4 isHoliday = true", DateHelper::isHoliday($d4));
test("30/4 isOpen = false", !DateHelper::isOpen($d4));

// Weekend
$d5 = new DateTime('2026-04-25 10:00'); // Saturday
test("Saturday isWeekend = true", DateHelper::isWeekend($d5));

$d6 = new DateTime('2026-04-26 10:00'); // Sunday
test("Sunday isWeekend = true", DateHelper::isWeekend($d6));

$d7 = new DateTime('2026-04-27 10:00'); // Monday
test("Monday isWeekend = false", !DateHelper::isWeekend($d7));

// getHolidays keys
$hols = DateHelper::getHolidays(2026);
echo "2026-01-01: " . ($hols['2026-01-01'] ?? 'MISSING') . PHP_EOL;
echo "2026-04-30: " . ($hols['2026-04-30'] ?? 'MISSING') . PHP_EOL;
echo "2026-05-01: " . ($hols['2026-05-01'] ?? 'MISSING') . PHP_EOL;
$tetCount = count(array_filter($hols, fn($v) => strpos($v, 'Tết Nguyên Đán') !== false));
echo "Tết Nguyên Đán entries: $tetCount (expected 6)" . PHP_EOL;
?>
