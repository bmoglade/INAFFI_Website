<?php
// ============================================================
// SECTION: Brand Strip
// ============================================================
// Infinite scrolling row of platform logos.
// Auto-reads all platforms from config — add a platform once
// in config.php and it appears here automatically.
//
// Matches WT reference: py-4, border-y border-border bg-surface
// rounded-md, "Shop from trusted platforms" label, 3x duplicated
// list for seamless infinite scroll
//
// No variables required — reads global $PLATFORMS,
// $PLATFORM_COLORS from config.php
// ============================================================

global $PLATFORMS, $PLATFORM_COLORS;
$strip_platforms = array_filter($PLATFORMS, fn($p) => $p !== 'Other');
?>

<section class="brand-strip-section">
    <div class="container-content">
        <div class="brand-strip-box">

            <p class="brand-strip-label">Shop from trusted platforms</p>

            <div class="brand-strip__overflow">
                <!--
                    Track is duplicated (rendered three times) so the
                CSS animation creates a seamless infinite loop.
                When the first copy scrolls off-screen left,
                the second copy seamlessly takes its place.
            -->
            <div class="brand-strip__track">
                <?php
                    // Render ALL platforms three times for seamless loop
                    // (matches WT: a-, b-, c- keys)
                    for ($copy = 0; $copy < 3; $copy++):
                        foreach ($strip_platforms as $platform):
                            $colors   = $PLATFORM_COLORS[$platform] ?? ['#666666', '#FFFFFF'];
                            $logo_rel = 'assets/images/platforms/' . strtolower(str_replace([' ', '&'], ['', ''], $platform)) . '.png';
                            $logo_abs = rtrim(dirname(dirname(__FILE__)), '/') . '/' . $logo_rel;
                            $has_logo = file_exists($logo_abs);
                ?>
                        <div class="brand-strip__item" aria-hidden="<?= $copy > 0 ? 'true' : 'false' ?>">
                            <?php if ($has_logo): ?>
                                <img
                                    src="<?= e(site_url($logo_rel)) ?>"
                                    alt="<?= e($platform) ?>"
                                    class="brand-strip__logo"
                                    loading="lazy"
                                >
                            <?php else: ?>
                        <!-- Coloured initial fallback -->
                        <span
                                    class="brand-strip__logo-text"
                                    style="color:<?= e($colors[0]) ?>;"
                            aria-hidden="true"
                                ><?= e($platform) ?></span>
                            <?php endif; ?>

                            <span class="brand-strip__name"><?= e($platform) ?></span>
                    </div>
                <?php
                    endforeach;
                endfor;
                ?>
            </div>
        </div>

    </div>
    </div>
</section>

