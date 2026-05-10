<?php
// ============================================================
// SECTION: Brand Strip
// ============================================================
// Infinite scrolling row of platform logos.
// Auto-reads all platforms from config — add a platform once
// in config.php and it appears here automatically.
//
// No variables required — reads global $PLATFORM_LOGOS,
// $PLATFORM_COLORS from config.php
// ============================================================

global $PLATFORM_LOGOS, $PLATFORM_COLORS;
?>

<section class="brand-strip">
    <div class="container">

        <p class="brand-strip__label">Shop from trusted platforms</p>

        <div class="brand-strip__overflow">
            <!--
                Track is duplicated (rendered twice) so the
                CSS animation creates a seamless infinite loop.
                When the first copy scrolls off-screen left,
                the second copy seamlessly takes its place.
            -->
            <div class="brand-strip__track">
                <?php
                // Render ALL platforms twice for seamless loop
                for ($loop = 0; $loop < 2; $loop++):
                    foreach ($PLATFORM_LOGOS as $pname => $logo_file):
                        $logo_url = site_url('assets/images/platforms/' . $logo_file);
                        $bg_color = $PLATFORM_COLORS[$pname][0] ?? '#666666';
                ?>
                    <div class="brand-strip__item" aria-hidden="<?= $loop > 0 ? 'true' : 'false' ?>">
                        <img
                            src="<?= e($logo_url) ?>"
                            alt="<?= e($pname) ?>"
                            class="brand-strip__logo"
                            loading="lazy"
                            onerror="
                                this.style.display='none';
                                this.nextElementSibling.style.display='flex';
                            "
                        >
                        <!-- Coloured initial fallback -->
                        <span
                            class="brand-strip__logo-fallback"
                            style="background:<?= e($bg_color) ?>;display:none;"
                            aria-hidden="true"
                        ><?= e(platform_initial($pname)) ?></span>

                        <span class="brand-strip__name"><?= e($pname) ?></span>
                    </div>
                <?php
                    endforeach;
                endfor;
                ?>
            </div>
        </div>

    </div>
</section>
