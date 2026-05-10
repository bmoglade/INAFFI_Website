<?php
// ============================================================
// inaffi.com — Signup Page
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

// Already logged in
if (is_logged_in()) redirect(site_url('dashboard'));

$errors   = [];
$vals     = ['display_name' => '', 'username' => '', 'email' => ''];

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $display_name = trim($_POST['display_name'] ?? '');
    $username     = strtolower(trim($_POST['username'] ?? ''));
    $email        = strtolower(trim($_POST['email']    ?? ''));
    $password     = $_POST['password']  ?? '';
    $password2    = $_POST['password2'] ?? '';

    $vals = compact('display_name', 'username', 'email');

    // ── Validation ──────────────────────────────────────────
    if (!$display_name) {
        $errors['display_name'] = 'Your name is required.';
    } elseif (mb_strlen($display_name) > 100) {
        $errors['display_name'] = 'Name must be under 100 characters.';
    }

    if (!$username) {
        $errors['username'] = 'Username is required.';
    } elseif (!is_valid_username($username)) {
        $errors['username'] = 'Username must be 3–30 characters: letters, numbers, underscore only.';
    } else {
        $chk = get_db()->prepare('SELECT id FROM creators WHERE username = ? LIMIT 1');
        $chk->execute([$username]);
        if ($chk->fetch()) $errors['username'] = 'That username is already taken.';
    }

    if (!$email) {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        $chk = get_db()->prepare('SELECT id FROM creators WHERE email = ? LIMIT 1');
        $chk->execute([$email]);
        if ($chk->fetch()) $errors['email'] = 'An account with this email already exists.';
    }

    if (!$password) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    if ($password && $password !== $password2) {
        $errors['password2'] = 'Passwords do not match.';
    }

    // ── Create account ───────────────────────────────────────
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = get_db()->prepare('
            INSERT INTO creators (display_name, username, email, password_hash)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$display_name, $username, $email, $hash]);
        $new_id = (int) get_db()->lastInsertId();

        login_creator($new_id);
        set_flash('Welcome to ' . SITE_NAME . '! Your storefront is ready.', 'success');
        redirect(site_url('dashboard'));
    }
}

$csrf  = generate_csrf_token();
$title = 'Create Account — ' . SITE_NAME;
require_once 'components/auth-header.php';
?>

<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-card__title">Create your storefront</h1>
        <p class="auth-card__subtitle">Free forever. Live in under 5 minutes.</p>

        <form method="POST" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

            <!-- Display name -->
            <div class="form-group">
                <label class="form-label" for="display_name">
                    Your Name <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="display_name"
                    name="display_name"
                    class="form-input"
                    value="<?= e($vals['display_name']) ?>"
                    placeholder="Priya Sharma"
                    maxlength="100"
                    required
                    autofocus
                >
                <?php if (!empty($errors['display_name'])): ?>
                    <p class="form-error"><?= e($errors['display_name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="username">
                    Username <span class="required">*</span>
                </label>
                <div style="position:relative;">
                    <span style="
                        position:absolute;left:12px;top:50%;
                        transform:translateY(-50%);
                        color:var(--color-text-secondary);
                        font-size:0.9375rem;
                        pointer-events:none;
                    "><?= e(rtrim(SITE_URL, '/')) ?>/</span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        value="<?= e($vals['username']) ?>"
                        placeholder="priya_styles"
                        maxlength="30"
                        pattern="[a-z0-9_]{3,30}"
                        required
                        autocomplete="username"
                        style="padding-left:<?= strlen(rtrim(SITE_URL, '/')) + 20 ?>px;"
                    >
                </div>
                <p class="form-hint">3–30 characters. Letters, numbers, underscore only.</p>
                <?php if (!empty($errors['username'])): ?>
                    <p class="form-error"><?= e($errors['username']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">
                    Email Address <span class="required">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    value="<?= e($vals['email']) ?>"
                    placeholder="you@example.com"
                    required
                    autocomplete="email"
                >
                <?php if (!empty($errors['email'])): ?>
                    <p class="form-error"><?= e($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">
                    Password <span class="required">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Minimum 8 characters"
                    minlength="8"
                    required
                    autocomplete="new-password"
                >
                <?php if (!empty($errors['password'])): ?>
                    <p class="form-error"><?= e($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Confirm password -->
            <div class="form-group">
                <label class="form-label" for="password2">
                    Confirm Password <span class="required">*</span>
                </label>
                <input
                    type="password"
                    id="password2"
                    name="password2"
                    class="form-input"
                    placeholder="Repeat your password"
                    required
                    autocomplete="new-password"
                >
                <?php if (!empty($errors['password2'])): ?>
                    <p class="form-error"><?= e($errors['password2']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--lg">
                Create My Storefront
            </button>

            <p style="font-size:0.8125rem;color:var(--color-text-secondary);text-align:center;margin-top:var(--space-md);">
                By signing up you agree to our
                <a href="<?= site_url('terms') ?>" style="color:var(--color-gold-accent);">Terms</a>
                and
                <a href="<?= site_url('privacy') ?>" style="color:var(--color-gold-accent);">Privacy Policy</a>.
            </p>
        </form>

        <p class="auth-footer-link">
            Already have an account?
            <a href="<?= site_url('login') ?>">Log in</a>
        </p>

    </div>
</div>

<?php require_once 'components/footer.php'; ?>
