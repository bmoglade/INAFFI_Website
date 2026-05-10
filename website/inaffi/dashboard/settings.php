<?php
// ============================================================
// inaffi.com — Dashboard: Profile Settings
// ============================================================

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/upload.php';

require_login();
$creator     = get_current_creator();
$active_page = 'settings';
$errors      = [];
$success     = false;

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $display_name    = trim($_POST['display_name']    ?? '');
    $username        = strtolower(trim($_POST['username'] ?? ''));
    $bio             = trim($_POST['bio']             ?? '');
    $instagram       = ltrim(trim($_POST['instagram_handle'] ?? ''), '@');
    $youtube         = ltrim(trim($_POST['youtube_handle']   ?? ''), '@');
    $facebook        = ltrim(trim($_POST['facebook_handle']  ?? ''), '@');
    $pinterest       = ltrim(trim($_POST['pinterest_handle'] ?? ''), '@');

    // Validate
    if (!$display_name) $errors[] = 'Display name is required.';
    elseif (mb_strlen($display_name) > 100) $errors[] = 'Display name max 100 chars.';

    if (!$username) {
        $errors[] = 'Username is required.';
    } elseif (!is_valid_username($username)) {
        $errors[] = 'Username: 3–30 chars, letters/numbers/underscore only.';
    } elseif ($username !== $creator['username']) {
        $chk = get_db()->prepare('SELECT id FROM creators WHERE username=? AND id != ? LIMIT 1');
        $chk->execute([$username, $creator['id']]);
        if ($chk->fetch()) $errors[] = 'That username is already taken.';
    }

    if ($bio && mb_strlen($bio) > MAX_BIO_LENGTH) {
        $errors[] = 'Bio max ' . MAX_BIO_LENGTH . ' characters.';
    }

    if (empty($errors)) {
        // Profile photo
        $profile_image = $creator['profile_image'];
        if (!empty($_FILES['profile_image']['tmp_name'])) {
            if ($profile_image) delete_image($profile_image);
            $profile_image = save_image($_FILES['profile_image'], 'profiles');
        }

        $stmt = get_db()->prepare('
            UPDATE creators SET
                display_name     = ?,
                username         = ?,
                bio              = ?,
                profile_image    = ?,
                instagram_handle = ?,
                youtube_handle   = ?,
                facebook_handle  = ?,
                pinterest_handle = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $display_name, $username, $bio, $profile_image,
            $instagram, $youtube, $facebook, $pinterest,
            $creator['id'],
        ]);

        // Reload creator data
        $stmt2 = get_db()->prepare('SELECT * FROM creators WHERE id = ? LIMIT 1');
        $stmt2->execute([$creator['id']]);
        $creator = $stmt2->fetch();

        set_flash('Profile updated successfully!', 'success');
        redirect(site_url('dashboard/settings'));
    }
}

$csrf  = generate_csrf_token();
$title = 'Settings — ' . SITE_NAME;
require_once '../components/header.php';
require_once '../components/dashboard-sidebar.php';
?>

<div class="dashboard-layout">
    <div class="dashboard-main">

        <h1 class="dashboard-page-title">Profile Settings</h1>

        <?php render_flash(); ?>

        <?php if ($errors): ?>
            <div class="flash flash--error">
                <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" style="max-width:600px;">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

            <!-- Profile photo -->
            <div class="outfit-form__section">
                <h2 class="outfit-form__section-title">Profile Photo</h2>
                <div style="display:flex;align-items:center;gap:var(--space-lg);margin-bottom:var(--space-lg);">
                    <?php if (!empty($creator['profile_image'])): ?>
                        <img
                            src="<?= e(site_url($creator['profile_image'])) ?>"
                            id="profile-preview"
                            alt="Profile photo"
                            style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--color-border);"
                        >
                    <?php else: ?>
                        <div id="profile-preview" style="
                            width:80px;height:80px;border-radius:50%;
                            background:var(--color-border);
                            display:flex;align-items:center;justify-content:center;
                            font-family:var(--font-display);font-size:2rem;font-weight:700;
                            color:var(--color-text-secondary);
                        "><?= e(mb_strtoupper(mb_substr($creator['display_name'],0,1))) ?></div>
                    <?php endif; ?>
                    <div class="image-upload-wrap" style="flex:1;">
                        <input type="file" name="profile_image" accept="image/*"
                            data-image-input="profile-preview">
                        <p class="image-upload-hint">JPG, PNG or WebP · Auto-compressed to 300×300</p>
                    </div>
                </div>
            </div>

            <!-- Basic info -->
            <div class="outfit-form__section">
                <h2 class="outfit-form__section-title">Basic Info</h2>

                <div class="form-group">
                    <label class="form-label" for="display_name">
                        Display Name <span class="required">*</span>
                    </label>
                    <input type="text" id="display_name" name="display_name"
                        class="form-input" value="<?= e($creator['display_name']) ?>"
                        maxlength="100" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">
                        Username <span class="required">*</span>
                    </label>
                    <div style="position:relative;">
                        <span style="
                            position:absolute;left:12px;top:50%;transform:translateY(-50%);
                            color:var(--color-text-secondary);pointer-events:none;
                        "><?= e(rtrim(SITE_URL,'/')) ?>/</span>
                        <input type="text" id="username" name="username"
                            class="form-input"
                            value="<?= e($creator['username']) ?>"
                            maxlength="30" pattern="[a-z0-9_]{3,30}" required
                            style="padding-left:<?= strlen(rtrim(SITE_URL,'/')) + 20 ?>px;">
                    </div>
                    <p class="form-hint">Changing this changes your public URL.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="bio">
                        Bio
                        <span style="color:var(--color-text-secondary);font-weight:400;">
                            (max <?= MAX_BIO_LENGTH ?> chars)
                        </span>
                    </label>
                    <textarea id="bio" name="bio" class="form-textarea"
                        maxlength="<?= MAX_BIO_LENGTH ?>"
                        placeholder="Indian fashion creator | Sharing everyday looks"
                        rows="3"><?= e($creator['bio'] ?? '') ?></textarea>
                    <p class="form-hint" id="bio-count">
                        <?= mb_strlen($creator['bio'] ?? '') ?> / <?= MAX_BIO_LENGTH ?>
                    </p>
                    <script>
                        document.getElementById('bio').addEventListener('input', function() {
                            document.getElementById('bio-count').textContent =
                                this.value.length + ' / <?= MAX_BIO_LENGTH ?>';
                        });
                    </script>
                </div>
            </div>

            <!-- Social links -->
            <div class="outfit-form__section">
                <h2 class="outfit-form__section-title">Social Links</h2>
                <p style="color:var(--color-text-secondary);font-size:0.875rem;margin-bottom:var(--space-lg);">
                    Add your handles (without @). They'll appear on your storefront.
                </p>

                <?php
                $socials = [
                    ['instagram_handle', 'Instagram', 'instagram.com/'],
                    ['youtube_handle',   'YouTube',   'youtube.com/@'],
                    ['facebook_handle',  'Facebook',  'facebook.com/'],
                    ['pinterest_handle', 'Pinterest', 'pinterest.com/'],
                ];
                foreach ($socials as [$field, $label, $base]):
                ?>
                <div class="form-group">
                    <label class="form-label" for="<?= $field ?>"><?= e($label) ?></label>
                    <div style="display:flex;align-items:center;border:1.5px solid var(--color-border);border-radius:4px;overflow:hidden;">
                        <span style="padding:10px 12px;background:var(--color-background);color:var(--color-text-secondary);font-size:0.875rem;white-space:nowrap;border-right:1px solid var(--color-border);">
                            <?= e($base) ?>
                        </span>
                        <input type="text" id="<?= $field ?>" name="<?= $field ?>"
                            class="form-input"
                            value="<?= e($creator[$field] ?? '') ?>"
                            placeholder="yourhandle"
                            maxlength="100"
                            style="border:none;border-radius:0;">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="display:flex;gap:var(--space-md);">
                <button type="submit" class="btn btn--primary btn--lg">Save Changes</button>
                <a href="<?= site_url('dashboard') ?>" class="btn btn--ghost btn--lg">Cancel</a>
            </div>
        </form>

    </div>
</div>

<?php require_once '../components/footer.php'; ?>
