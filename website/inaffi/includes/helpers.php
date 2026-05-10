<?php
// ============================================================
// inaffi.com — Helper / Utility Functions
// ============================================================

// ── Output Escaping ──────────────────────────────────────────

/**
 * Escape a string for safe HTML output.
 * Use on EVERY user-supplied value before echoing it.
 *
 * Usage: echo e($creator['display_name']);
 */
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Echo an escaped string directly.
 * Usage: ee($creator['display_name']);
 */
function ee(string $s): void {
    echo htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


// ── CSRF Protection ──────────────────────────────────────────

/**
 * Generate a CSRF token and store it in the session.
 * Call in any page that has a POST form, then echo the token
 * as a hidden input field.
 *
 * Usage in page:   $csrf = generate_csrf_token();
 * Usage in form:   <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
 */
function generate_csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify the CSRF token submitted with a POST form.
 * Dies with 403 if invalid — call before any DB write.
 *
 * Usage: verify_csrf_token($_POST['csrf_token'] ?? '');
 */
function verify_csrf_token(string $submitted): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $stored = $_SESSION['csrf_token'] ?? '';
    if (!hash_equals($stored, $submitted)) {
        http_response_code(403);
        die('Invalid request. Please go back and try again.');
    }
}


// ── String Utilities ─────────────────────────────────────────

/**
 * Truncate a string to a max length, appending ellipsis if cut.
 *
 * Usage: echo truncate($bio, 80);
 */
function truncate(string $s, int $max, string $suffix = '…'): string {
    if (mb_strlen($s) <= $max) return $s;
    return mb_substr($s, 0, $max - mb_strlen($suffix)) . $suffix;
}

/**
 * Convert a string to a safe URL slug.
 * e.g. "My Outfit 2024!" → "my-outfit-2024"
 */
function slugify(string $s): string {
    $s = mb_strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
    $s = preg_replace('/[\s-]+/', '-', $s);
    return trim($s, '-');
}

/**
 * Validate a username: 3-30 chars, only a-z, 0-9, underscore.
 */
function is_valid_username(string $u): bool {
    return (bool) preg_match('/^[a-z0-9_]{3,30}$/', $u);
}


// ── Platform Helpers ─────────────────────────────────────────

/**
 * Get the badge inline style for a platform.
 * Returns a style string: "background:#FF9900;color:#000000"
 *
 * Usage: <span style="<?= platform_badge_style('Amazon') ?>">Amazon</span>
 */
function platform_badge_style(string $platform): string {
    global $PLATFORM_COLORS;
    $colors = $PLATFORM_COLORS[$platform] ?? ['#666666', '#FFFFFF'];
    return sprintf('background:%s;color:%s', $colors[0], $colors[1]);
}

/**
 * Get the logo img src for a platform.
 * Returns full URL or empty string if no logo configured.
 */
function platform_logo_url(string $platform): string {
    global $PLATFORM_LOGOS;
    if (!isset($PLATFORM_LOGOS[$platform])) return '';
    $path = '/assets/images/platforms/' . $PLATFORM_LOGOS[$platform];
    // Return only if file exists on disk
    $file = $_SERVER['DOCUMENT_ROOT'] . $path;
    return file_exists($file) ? $path : '';
}

/**
 * Get the first letter of a platform name (for logo fallback).
 */
function platform_initial(string $platform): string {
    return mb_strtoupper(mb_substr($platform, 0, 1));
}


// ── URL & Request Helpers ────────────────────────────────────

/**
 * Get the current full URL.
 */
function current_url(): string {
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Get the base site URL from config.
 */
function site_url(string $path = ''): string {
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Redirect to a URL and exit.
 */
function redirect(string $url, int $code = 302): void {
    header('Location: ' . $url, true, $code);
    exit();
}

/**
 * Get a sanitized GET parameter or default value.
 */
function get_param(string $key, string $default = ''): string {
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : $default;
}

/**
 * Get a sanitized POST parameter or default value.
 */
function post_param(string $key, string $default = ''): string {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}


// ── Flash Messages ───────────────────────────────────────────
// One-time messages shown after a redirect (e.g. "Outfit saved!")

/**
 * Set a flash message to be shown on the next page load.
 * Type: 'success' | 'error' | 'info'
 */
function set_flash(string $message, string $type = 'success'): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

/**
 * Get and clear the flash message (call once per page load).
 * Returns null if no flash message set.
 */
function get_flash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['flash'])) return null;
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Render the flash message HTML if one exists.
 * Call once in header.php or at top of page content.
 */
function render_flash(): void {
    $flash = get_flash();
    if (!$flash) return;
    $type    = e($flash['type']);
    $message = e($flash['message']);
    echo "<div class=\"flash flash--{$type}\" role=\"alert\">{$message}</div>";
}
