<?php
// ============================================================
// inaffi.com — Admin Panel
// ============================================================
// Only accessible to creators with is_admin = 1
// Manages: site name, tagline, colors, categories, platforms
// ============================================================

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

require_admin();
$creator     = get_current_creator();
$active_page = 'admin';

global $CATEGORIES, $PLATFORMS, $PLATFORM_COLORS;

// ── Config file path ─────────────────────────────────────────
// Admin saves settings by rewriting config.php
// This is safe because config.php is behind .htaccess + not web-accessible
$config_file = __DIR__ . '/../includes/config.php';

$errors  = [];
$success = false;

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $action = $_POST['action'] ?? '';

    // ── Save site identity ───────────────────────────────────
    if ($action === 'identity') {
        $site_name    = trim($_POST['site_name']    ?? '');
        $site_tagline = trim($_POST['site_tagline'] ?? '');
        $site_url     = rtrim(trim($_POST['site_url'] ?? ''), '/');

        if (!$site_name)    $errors[] = 'Site name is required.';
        if (!$site_tagline) $errors[] = 'Tagline is required.';
        if (!$site_url || !filter_var($site_url, FILTER_VALIDATE_URL))
            $errors[] = 'Valid site URL required.';

        if (empty($errors)) {
            update_config_constant('SITE_NAME',    $site_name);
            update_config_constant('SITE_TAGLINE', $site_tagline);
            update_config_constant('SITE_URL',     $site_url);
            set_flash('Site identity saved!', 'success');
            redirect(site_url('admin'));
        }
    }

    // ── Save colors ──────────────────────────────────────────
    if ($action === 'colors') {
        $color_vars = [
            '--color-background', '--color-surface', '--color-primary-dark',
            '--color-gold-accent', '--color-text-primary', '--color-text-secondary',
            '--color-border', '--color-shop-btn-bg', '--color-shop-btn-text',
        ];
        $css_file = __DIR__ . '/../assets/css/styles.css';
        $css      = file_get_contents($css_file);

        foreach ($color_vars as $var) {
            $key   = str_replace(['--color-', '-'], ['', '_'], $var);
            $value = trim($_POST['color_' . $key] ?? '');
            if (!$value || !preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) continue;
            // Replace value in :root block
            $css = preg_replace(
                '/('. preg_quote($var, '/') . '\s*:\s*)#[0-9A-Fa-f]{3,6}/',
                '$1' . $value,
                $css
            );
        }

        file_put_contents($css_file, $css);
        set_flash('Colors saved! Reload the page to see changes.', 'success');
        redirect(site_url('admin'));
    }

    // ── Save categories ──────────────────────────────────────
    if ($action === 'categories') {
        $cats = array_filter(array_map('trim', $_POST['categories'] ?? []), fn($c) => $c !== '');
        $cats = array_values(array_unique($cats));
        if (count($cats) < 1) $errors[] = 'At least one category required.';
        if (empty($errors)) {
            update_config_array('CATEGORIES', $cats, $config_file);
            set_flash('Categories saved!', 'success');
            redirect(site_url('admin'));
        }
    }
}

// ── Read current colors from CSS ─────────────────────────────
$css_content   = file_get_contents(__DIR__ . '/../assets/css/styles.css');
$current_colors = [];
preg_match_all('/(--color-[a-z-]+)\s*:\s*(#[0-9A-Fa-f]{3,6})/', $css_content, $matches, PREG_SET_ORDER);
foreach ($matches as $m) {
    $current_colors[$m[1]] = $m[2];
}

$csrf  = generate_csrf_token();
$title = 'Admin Panel — ' . SITE_NAME;
require_once '../components/header.php';
require_once '../components/dashboard-sidebar.php';
?>

<!-- Mobile bar must be OUTSIDE dashboard-layout -->
<div class="dashboard-layout">
    <div class="dashboard-main">

        <h1 class="dashboard-page-title">
            ⚙️ Admin Panel
            <span style="font-size:0.875rem;font-weight:400;color:var(--color-text-secondary);">
                — Site owner settings
            </span>
        </h1>

        <?php render_flash(); ?>

        <?php if ($errors): ?>
            <div class="flash flash--error">
                <?php foreach ($errors as $e): ?><p><?= e($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ── Section: Site Identity ──────────────────────── -->
        <div class="admin-section-label">SITE SETTINGS</div>
        <div class="admin-grid">

            <!-- ── Site Identity ──────────────────────────── -->
            <div class="admin-card">
                <h2 class="admin-card__title">🏷️ Site Identity</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="identity">

                    <div class="form-group">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-input"
                            value="<?= e(SITE_NAME) ?>" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="site_tagline" class="form-input"
                            value="<?= e(SITE_TAGLINE) ?>" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Site URL</label>
                        <input type="url" name="site_url" class="form-input"
                            value="<?= e(SITE_URL) ?>" required>
                        <p class="form-hint">No trailing slash. e.g. https://inaffi.com</p>
                    </div>
                    <button type="submit" class="btn btn--primary">Save Identity</button>
                </form>
            </div>
            <!-- ── Color Theme ────────────────────────────── -->
            <div class="admin-card">
                <h2 class="admin-card__title">🎨 Color Theme</h2>
                <p style="font-size:0.875rem;color:var(--color-text-secondary);margin-bottom:var(--space-md);">
                    Pick colors — preview updates live. Click Save to apply site-wide.
                </p>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="colors">

                    <?php
                    $color_labels = [
                        '--color-background'    => 'Page Background',
                        '--color-surface'       => 'Card / Panel',
                        '--color-primary-dark'  => 'Primary Dark (buttons)',
                        '--color-gold-accent'   => 'Gold Accent (CTA)',
                        '--color-text-primary'  => 'Text Primary',
                        '--color-text-secondary'=> 'Text Secondary',
                        '--color-border'        => 'Border / Divider',
                        '--color-shop-btn-bg'   => 'Shop Button Background',
                        '--color-shop-btn-text' => 'Shop Button Text',
                    ];
                    foreach ($color_labels as $var => $label):
                        $val     = $current_colors[$var] ?? '#000000';
                        $key     = str_replace(['--color-', '-'], ['', '_'], $var);
                    ?>
                    <div class="color-picker-row">
                        <label for="color_<?= e($key) ?>"><?= e($label) ?></label>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="color-preview" style="display:inline-block;width:24px;height:24px;border-radius:4px;border:1px solid var(--color-border);background:<?= e($val) ?>;"></span>
                            <input
                                type="color"
                                id="color_<?= e($key) ?>"
                                name="color_<?= e($key) ?>"
                                value="<?= e($val) ?>"
                                data-color-var="<?= e($var) ?>"
                            >
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn--primary" style="margin-top:var(--space-md);">
                        Save Colors
                    </button>
                </form>
            </div>
        </div><!-- /admin-grid -->

        <!-- ── Section: Content ────────────────────────────── -->
        <div class="admin-section-label" style="margin-top:var(--space-xl);">CONTENT</div>
        <div class="admin-grid">

            <!-- ── Categories ────────────────────────────── -->
            <div class="admin-card">
                <h2 class="admin-card__title">📂 Categories</h2>
                <p style="font-size:0.875rem;color:var(--color-text-secondary);margin-bottom:var(--space-md);">
                    These appear in outfit dropdowns and storefront filter pills.
                </p>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="categories">

                    <div
                        data-tag-manager
                        style="margin-bottom:var(--space-md);"
                    >
                        <div class="tag-list" data-tag-list>
                            <?php foreach ($CATEGORIES as $cat): ?>
                                <span class="tag-item" data-tag-value="<?= e($cat) ?>">
                                    <?= e($cat) ?>
                                    <button type="button" class="tag-item__remove" data-tag-remove>×</button>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <div class="tag-add-row">
                            <input type="text" class="form-input" data-tag-input
                                placeholder="New category…" maxlength="50">
                            <button type="button" class="btn btn--outline" data-tag-add>Add</button>
                        </div>

                        <div data-tag-hidden data-tag-hidden="categories">
                            <?php foreach ($CATEGORIES as $cat): ?>
                                <input type="hidden" name="categories[]"
                                    value="<?= e($cat) ?>"
                                    data-tag-for="<?= e($cat) ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary">Save Categories</button>
                </form>
            </div>

            <!-- ── Platforms Info ─────────────────────────── -->
            <div class="admin-card">
                <h2 class="admin-card__title">🛍️ Platforms</h2>
                <p style="font-size:0.875rem;color:var(--color-text-secondary);margin-bottom:var(--space-md);">
                    Current platforms. To add/remove, edit
                    <code style="background:var(--color-background);padding:2px 6px;border-radius:3px;font-size:0.8125rem;">includes/config.php</code>
                    and upload logos to
                    <code style="background:var(--color-background);padding:2px 6px;border-radius:3px;font-size:0.8125rem;">assets/images/platforms/</code>
                </p>
                <div class="tag-list" style="gap:8px;">
                    <?php foreach ($PLATFORMS as $p):
                        $colors = $PLATFORM_COLORS[$p] ?? ['#666','#fff'];
                    ?>
                        <span class="badge" style="
                            background:<?= e($colors[0]) ?>;
                            color:<?= e($colors[1]) ?>;
                            padding:5px 12px;
                            font-size:0.8125rem;
                            border-radius:4px;
                        ">
                            <?= e($p) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

        </div><!-- /admin-grid -->

        <!-- ── Section: Homepage ───────────────────────────── -->
        <div class="admin-section-label" style="margin-top:var(--space-xl);">HOMEPAGE</div>

        <!-- ── Featured Outfit ───────────────────────── -->
        <div class="admin-card" style="margin-bottom:var(--space-xl);">
            <h2 class="admin-card__title">⭐ Featured Outfit on Homepage</h2>
            <p style="font-size:0.875rem;color:var(--color-text-secondary);margin-bottom:var(--space-md);">
                Go to <a href="<?= site_url('dashboard') ?>" style="color:var(--color-gold-accent);font-weight:500;">Dashboard</a>
                → find any outfit → click <strong>⭐ Feature</strong> to display it on the homepage.
                Only one outfit can be featured at a time.
            </p>

            <?php
            // Show currently featured outfit
            $stmt = get_db()->prepare('
                SELECT o.title, o.category, c.display_name, c.username
                FROM outfits o JOIN creators c ON c.id = o.creator_id
                WHERE o.is_featured = 1 LIMIT 1
            ');
            $stmt->execute();
            $featured = $stmt->fetch();
            ?>

            <?php if ($featured): ?>
                <div style="
                    display:flex;align-items:center;gap:var(--space-md);
                    padding:var(--space-md);
                    background:var(--color-background);
                    border:2px solid var(--color-gold-accent);
                    border-radius:var(--radius-sm);
                ">
                    <span style="font-size:2rem;">⭐</span>
                    <div>
                        <p style="font-weight:600;margin-bottom:2px;font-size:1rem;">
                            <?= e($featured['title']) ?>
                        </p>
                        <p style="font-size:0.875rem;color:var(--color-text-secondary);margin:0;">
                            <?= e($featured['category']) ?> &middot;
                            by <strong><?= e($featured['display_name']) ?></strong>
                            (@<?= e($featured['username']) ?>)
                        </p>
                    </div>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn--outline btn--sm" style="margin-left:auto;">
                        Change
                    </a>
                </div>
            <?php else: ?>
                <div style="
                    padding:var(--space-lg);
                    background:var(--color-background);
                    border:2px dashed var(--color-border);
                    border-radius:var(--radius-sm);
                    text-align:center;
                    color:var(--color-text-secondary);
                ">
                    <p style="font-size:2rem;margin-bottom:8px;">🚫</p>
                    <p style="margin:0;">No outfit is currently featured.</p>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn--primary btn--sm" style="margin-top:12px;">
                        Go to Dashboard → Feature an Outfit
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- /dashboard-main -->
</div><!-- /dashboard-layout -->

<?php require_once '../components/footer.php'; ?>

<?php
// ── Helper: update a define() constant value in config.php ──
function update_config_constant(string $name, string $value): void {
    $file    = __DIR__ . '/../includes/config.php';
    $content = file_get_contents($file);
    $escaped = addslashes($value);
    $content = preg_replace(
        "/define\(\s*'" . preg_quote($name, '/') . "'\s*,\s*'[^']*'\s*\)/",
        "define('" . $name . "', '" . $escaped . "')",
        $content
    );
    file_put_contents($file, $content);
}

function update_config_array(string $name, array $values, string $file): void {
    $content = file_get_contents($file);
    $lines   = array_map(fn($v) => "    '" . addslashes($v) . "',", $values);
    $block   = "\$" . $name . " = [\n" . implode("\n", $lines) . "\n];\n";

    // Replace the existing array
    $content = preg_replace(
        '/\$' . preg_quote($name, '/') . '\s*=\s*\[[^\]]*\]\s*;/s',
        $block,
        $content
    );
    file_put_contents($file, $content);
}
?>

