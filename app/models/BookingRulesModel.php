<?php
/**
 * BookingRulesModel
 * Reads booking configuration from `booking_rules` table with request-level static cache.
 * Requires Database connection.
 */
require_once __DIR__ . '/../../config/database.php';

class BookingRulesModel
{
    /** @var array|null Static cache for current request */
    private static ?array $cached = null;

    /**
     * Get a rule value by key. Loads all rules on first call.
     *
     * @param string $key rule_key column value
     * @return mixed rule_value or null if not found
     */
    public static function get(string $key): mixed
    {
        if (self::$cached === null) {
            self::loadAll();
        }
        return self::$cached[$key] ?? null;
    }

    /**
     * Load all rules from DB into static cache. Called once per request.
     */
    private static function loadAll(): void
    {
        $database = new Database();
        $db = $database->getConnection();
        $result = mysqli_query($db, "SELECT rule_key, rule_value FROM booking_rules");
        self::$cached = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                self::$cached[$row['rule_key']] = $row['rule_value'];
            }
        }
    }

    /**
     * Reset cache — useful for testing.
     */
    public static function resetCache(): void
    {
        self::$cached = null;
    }
}
?>
