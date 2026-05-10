<?php
// ============================================================
// SECTION: Bottom CTA Strip
// ============================================================
// Dark full-width call-to-action bar at the bottom of the page.
// Hidden automatically if the visitor is already logged in.
//
// Variables expected:
//   $creator  (array|null) — logged-in creator or null
// ============================================================

if (!empty($creator)) return;  // already logged in — don't show
?>

<section style="
    background:var(--color-primary-dark);
    padding:var(--space-2xl) 0;
    text-align:center;
">
    <div class="container">

        <p style="
            font-size:0.8125rem;font-weight:600;
            letter-spacing:0.1em;text-transform:uppercase;
            color:var(--color-gold-accent);margin-bottom:16px;
        ">Start Today — It's Free</p>

        <h2 style="
            font-family:var(--font-display);
            color:#FFFFFF;
            font-size:clamp(1.75rem,3vw,2.5rem);
            margin-bottom:16px;
            line-height:1.2;
        ">
            Ready to turn your style<br>into real income?
        </h2>

        <p style="
            color:rgba(255,255,255,0.6);
            font-size:1.0625rem;
            margin-bottom:var(--space-xl);
            max-width:480px;
            margin-left:auto;
            margin-right:auto;
        ">
            Join Indian fashion creators already earning affiliate commissions
            with their <?= e(SITE_NAME) ?> storefront.
        </p>

        <div style="
            display:flex;
            gap:var(--space-md);
            justify-content:center;
            flex-wrap:wrap;
        ">
            <a href="<?= site_url('signup') ?>" class="btn btn--gold btn--lg">
                Create Your Free Storefront
            </a>
            <a href="<?= site_url('login') ?>" class="btn btn--lg" style="
                background:transparent;
                color:rgba(255,255,255,0.8);
                border:2px solid rgba(255,255,255,0.3);
                border-radius:0;
            ">
                Already have an account? Log in
            </a>
        </div>

        <!-- Trust signals -->
        <div style="
            display:flex;
            gap:var(--space-xl);
            justify-content:center;
            margin-top:var(--space-xl);
            flex-wrap:wrap;
        ">
            <?php
            $trust = [
                '✓ Free forever',
                '✓ No credit card needed',
                '✓ Works with all affiliate programs',
                '✓ Live in under 5 minutes',
            ];
            foreach ($trust as $item):
            ?>
                <span style="
                    font-size:0.875rem;
                    color:rgba(255,255,255,0.5);
                "><?= e($item) ?></span>
            <?php endforeach; ?>
        </div>

    </div>
</section>
