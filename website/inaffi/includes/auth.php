<?php
// ============================================================
// inaffi.com — Authentication & Session Management
// ============================================================
// Include at the top of EVERY protected page:
//   require_once '../includes/auth.php';
//   require_login();
//   $creator = get_current_creator();
// ============================================================

// Start session securely (called once, safe to call multiple times)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,               // expires when browser closes
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),  // HTTPS only in production
        'httponly' => true,            // not accessible via JavaScript
        'samesite' => 'Strict',        // CSRF protection
    ]);
    session_start();
}


// ── Core Auth Functions ──────────────────────────────────────

/**
 * Check if the current visitor is logged in.
 */
function is_logged_in(): bool {
    return isset($_SESSION['creator_id']) && is_numeric($_SESSION['creator_id']);
}

/**
 * Require login — redirect to /login if not authenticated.
 * Call at the top of every dashboard page.
 *
 * @param string $redirect  URL to send back to after login (default: /dashboard)
 */
function require_login(string $redirect = '/dashboard'): void {
    if (!is_logged_in()) {
        $location = '/login?redirect=' . urlencode($redirect);
        header('Location: ' . $location, true, 302);
        exit();
    }
}

/**
 * Require admin — redirect to /dashboard if not admin.
 * Call at the top of every /admin page.
 */
function require_admin(): void {
    require_login('/admin');
    $creator = get_current_creator();
    if (empty($creator['is_admin'])) {
        header('Location: /dashboard', true, 302);
        exit();
    }
}

/**
 * Get the full creator row for the currently logged-in user.
 * Returns null if not logged in.
 */
function get_current_creator(): ?array {
    if (!is_logged_in()) {
        return null;
    }

    // Cache in session-level static to avoid repeated DB calls per request
    static $creator = null;
    if ($creator !== null) {
        return $creator;
    }

    require_once __DIR__ . '/db.php';

    if (!db_available()) return null;

    $stmt = get_db()->prepare('SELECT * FROM creators WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $_SESSION['creator_id']]);
    $creator = $stmt->fetch() ?: null;

    // Session is valid but creator was deleted — log out
    if ($creator === null) {
        session_destroy();
        header('Location: /login', true, 302);
        exit();
    }

    return $creator;
}

/**
 * Log in a creator by setting session data.
 * Call after verifying password.
 */
function login_creator(int $creator_id): void {
    // Regenerate session ID on login (prevents session fixation)
    session_regenerate_id(true);
    $_SESSION['creator_id'] = $creator_id;
}

/**
 * Log out — destroy session and redirect to homepage.
 */
function logout(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: /', true, 302);
    exit();
}
