<?php
// ============================================================
// inaffi.com — Login Page
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

// Already logged in — go to dashboard
if (is_logged_in()) redirect(site_url('dashboard'));

$error    = '';
$email_val = '';

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $email_val = $email;

    if (!$email || !$password) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = get_db()->prepare('SELECT * FROM creators WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $creator = $stmt->fetch();

        if ($creator && password_verify($password, $creator['password_hash'])) {
            login_creator((int) $creator['id']);
            $redirect = get_param('redirect', site_url('dashboard'));
            redirect($redirect);
        } else {
            $error = 'Incorrect email or password. Please try again.';
        }
    }
}

$csrf  = generate_csrf_token();
$title = 'Log in — ' . SITE_NAME;
require_once 'components/auth-header.php';
?>

<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-card__title">Welcome back</h1>
        <p class="auth-card__subtitle">Log in to your <?= e(SITE_NAME) ?> account</p>

        <?php if ($error): ?>
            <div class="flash flash--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

            <div class="form-group">
                <label class="form-label" for="email">
                    Email address <span class="required">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    value="<?= e($email_val) ?>"
                    placeholder="you@example.com"
                    required
                    autocomplete="email"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">
                    Password <span class="required">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Your password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--lg">
                Log in
            </button>
        </form>

        <p class="auth-footer-link">
            Don't have an account?
            <a href="<?= site_url('signup') ?>">Join as a Creator</a>
        </p>

    </div>
</div>

<?php require_once 'components/footer.php'; ?>
