<?php
// ============================================================
// inaffi.com — Database Connection (PDO)
// ============================================================
// Usage in any PHP file:
//   require_once '../includes/db.php';   (from dashboard/)
//   require_once 'includes/db.php';      (from public_html root)
//   $stmt = get_db()->prepare('SELECT ...');
// ============================================================

/**
 * Returns a singleton PDO connection to MySQL.
 * Connection is created once and reused for the request lifetime.
 */
function get_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        // Ensure config is loaded
        if (!defined('DB_HOST')) {
            throw new RuntimeException('Database config not loaded. Include config.php first.');
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // throw exceptions on error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // return arrays by default
                PDO::ATTR_EMULATE_PREPARES   => false,                    // use real prepared statements
                PDO::MYSQL_ATTR_FOUND_ROWS   => true,                     // UPDATE returns matched rows
            ]);
        } catch (PDOException $e) {
            // Log error but never expose credentials to browser
            // In local dev (no DB) — set $pdo to false so callers can check
            error_log('[inaffi DB] Connection failed: ' . $e->getMessage());
            $pdo = false;
        }
    }

    // Return false if connection failed (local dev with no DB)
    return $pdo ?: null;
}

/**
 * Returns true if a DB connection is available.
 * Use this to gracefully skip DB calls in local dev.
 */
function db_available(): bool {
    static $checked = null;
    if ($checked !== null) return $checked;
    try {
        get_db();
        $checked = get_db() !== null;
    } catch (Exception $e) {
        $checked = false;
    }
    return $checked;
}
