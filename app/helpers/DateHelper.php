<?php
/**
 * DateHelper
 * Holiday, weekend, and open-hours detection for Vietnamese restaurant booking.
 * Hardcoded fixed holidays (no DB dependency).
 */

class DateHelper
{
    /**
     * Returns all fixed Vietnamese holidays for a given year.
     * Key = YYYY-MM-DD, Value = holiday label.
     * Tết Nguyên Đán (Lunar New Year) is approximated using solar dates.
     * TODO: Update Tết range annually or add to booking_rules table.
     *
     * @param int $year
     * @return array<string, string>
     */
    public static function getHolidays(int $year): array
    {
        return [
            "$year-01-01" => 'Tết Dương lịch',
            // Tết Nguyên Đán 2026 — approximated solar dates for 除夕 through 初四
            "$year-01-28" => 'Tết Nguyên Đán',
            "$year-01-29" => 'Tết Nguyên Đán',
            "$year-01-30" => 'Tết Nguyên Đán',
            "$year-01-31" => 'Tết Nguyên Đán',
            "$year-02-01" => 'Tết Nguyên Đán',
            "$year-02-02" => 'Tết Nguyên Đán',
            "$year-04-30" => 'Ngày 30/4',
            "$year-05-01" => 'Ngày 01/5',
        ];
    }

    /**
     * Check if a given date is a Vietnamese fixed holiday.
     *
     * @param DateTime $date
     * @return bool
     */
    public static function isHoliday(DateTime $date): bool
    {
        $key = $date->format('Y-m-d');
        $holidays = self::getHolidays((int) $date->format('Y'));
        return isset($holidays[$key]);
    }

    /**
     * Check if a given date is Saturday (w=6) or Sunday (w=0).
     *
     * @param DateTime $date
     * @return bool
     */
    public static function isWeekend(DateTime $date): bool
    {
        $w = (int) $date->format('w'); // 0=Sunday, 6=Saturday
        return $w === 0 || $w === 6;
    }

    /**
     * Check if the restaurant is open at the given DateTime.
     * Rules: not a holiday, hour between 09:00 and 21:59 (closes at 22:00).
     *
     * @param DateTime $datetime
     * @return bool
     */
    public static function isOpen(DateTime $datetime): bool
    {
        // Closed on holidays
        if (self::isHoliday($datetime)) {
            return false;
        }

        $hour = (int) $datetime->format('G'); // 0-23
        return $hour >= 9 && $hour < 22;
    }
}
?>
