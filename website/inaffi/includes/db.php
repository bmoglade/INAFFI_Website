<?php
// ============================================================
// inaffi.com — Database Connection (PDO)
// ============================================================
// Usage in any PHP file:
//   require_once '../includes/db.php';   (from dashboard/)
//   require_once 'includes/db.php';      (from public_html root)
//   $stmt = get_db()->prepare('SELECT ...');
//
// ENVIRONMENT BEHAVIOUR:
//   Live (Hostinger) — DB always connects. get_db() returns PDO.
//   Local dev (no MySQL) — connection fails silently. get_db() returns null.
//     db_available() returns false → callers fall back to static mockup data.
//     The live site is never affected — Hostinger always has MySQL running.
// ============================================================

/**
 * Returns a singleton PDO connection to MySQL, or null if unavailable.
 * On live server this always returns a PDO object.
 * On local dev with no MySQL this returns null (graceful fallback).
 */
function get_db(): ?PDO {
    static $pdo       = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;   // return whatever we got last time (PDO or null)
    }

    $attempted = true;

    // Ensure config is loaded
    if (!defined('DB_HOST')) {
        // Config not loaded — this is a hard error in any environment
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
            error_log('[inaffi DB] Connection failed: ' . $e->getMessage());
        $pdo = null;
        // On live server this should never happen.
        // On local dev with no MySQL this is expected — callers check db_available().
        }

    return $pdo;
    }

/**
 * Returns true if a working DB connection exists.
 *
 * Usage pattern — always check this before any DB call in pages
 * that have a static fallback (e.g. homepage):
 *
 *   if (db_available()) {
 *       // run DB query
 *   } else {
 *       // use static mockup (local dev only)
 *   }
 *
 * Pages that REQUIRE a DB (dashboard, storefront, etc.) do NOT need this —
 * they are only accessible when logged in, which itself requires a DB.
 * On live Hostinger this always returns true.
 */
function db_available(): bool {
    static $result = null;
    if ($result === null) {
        $result = (get_db() !== null);
    }
    return $result;
}

