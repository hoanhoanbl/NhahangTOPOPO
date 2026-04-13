<?php
/**
 * DepositCalculator
 * Stateless static methods — no DB dependency.
 * Reads rule values from BookingRulesModel.
 */
require_once __DIR__ . '/BookingRulesModel.php';

class DepositCalculator
{
    /**
     * Calculate deposit amount in VND.
     *
     * Rules:
     *   Weekend or holiday:     +100,000 VND flat
     *   Pre-order menu > 0:  +50% of menu total
     *
     * @param DateTime $date      Booking date
     * @param float  $menuTotal  Total menu order amount in VND
     * @return int   Deposit in VND
     */
    public static function calculate(DateTime $date, float $menuTotal): int
    {
        $deposit = 0;

        if (DateHelper::isWeekend($date) || DateHelper::isHoliday($date)) {
            $deposit += (int) BookingRulesModel::get('deposit_weekend');
        }

        if ($menuTotal > 0) {
            $pct = (int) BookingRulesModel::get('deposit_menu_percent');
            $deposit += (int) ($menuTotal * $pct / 100);
        }

        return $deposit;
    }
}
?>
