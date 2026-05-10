<?php
// ============================================================
// SECTION: How It Works
// ============================================================
// 4 numbered steps explaining the creator workflow.
// Layout: full-width centered, max 800px
//
// To edit: change the steps array below.
// To add a step: add another entry to $steps.
// To remove a step: delete its entry.
// ============================================================

$steps = [
    [
        'num'   => '1',
        'title' => 'Create your free storefront',
        'desc'  => 'Sign up in 60 seconds. Get your personal URL — '
                   . SITE_URL . '/yourname — instantly.',
    ],
    [
        'num'   => '2',
        'title' => 'Build your outfit looks',
        'desc'  => 'Upload outfit photos and paste affiliate links from Amazon, '
                   . 'Flipkart, Myntra, Nykaa and more. Up to 15 products per look.',
    ],
    [
        'num'   => '3',
        'title' => 'Share your link everywhere',
        'desc'  => 'One link in your Instagram bio, YouTube description, or Reels. '
                   . 'Your followers see everything in one clean page.',
    ],
    [
        'num'   => '4',
        'title' => 'Earn affiliate commissions',
        'desc'  => 'Every click goes directly to Amazon, Flipkart, or Myntra. '
                   . 'You earn from your affiliate programs — directly, no middleman.',
    ],
];
?>

<section class="section" style="background:var(--color-surface);border-top:1px solid var(--color-border);">
    <div class="container">

        <!-- Section heading -->
        <div style="text-align:center;max-width:560px;margin:0 auto var(--space-2xl);">
            <p style="
                font-size:0.8125rem;
                font-weight:600;
                letter-spacing:0.1em;
                text-transform:uppercase;
                color:var(--color-gold-accent);
                margin-bottom:12px;
            ">How It Works</p>
            <h2 style="margin-bottom:12px;">
                From Post to Purchase
                <span style="color:var(--color-gold-accent);">in 4 Steps</span>
            </h2>
            <p style="color:var(--color-text-secondary);font-size:1.0625rem;">
                No tech skills needed. No monthly fees. Just your style and your links.
            </p>
        </div>

        <!-- Steps grid -->
        <div style="
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:var(--space-lg);
            position:relative;
        ">
            <!-- Connecting line (desktop) -->
            <div style="
                position:absolute;
                top:32px;
                left:calc(12.5% + 16px);
                right:calc(12.5% + 16px);
                height:2px;
                background:var(--color-border);
                z-index:0;
            " aria-hidden="true"></div>

            <?php foreach ($steps as $i => $step): ?>
            <div class="animate-fade-in-up" style="
                text-align:center;
                position:relative;
                z-index:1;
                animation-delay:<?= $i * 0.1 ?>s;
            ">
                <!-- Step number circle -->
                <div style="
                    width:64px;height:64px;
                    background:var(--color-primary-dark);
                    color:#FFFFFF;
                    border-radius:50%;
                    display:inline-flex;
                    align-items:center;
                    justify-content:center;
                    font-family:var(--font-display);
                    font-size:1.5rem;
                    font-weight:700;
                    margin-bottom:var(--space-lg);
                    border:4px solid var(--color-surface);
                    box-shadow:var(--shadow-md);
                "><?= e($step['num']) ?></div>

                <h4 style="margin-bottom:8px;font-size:1rem;">
                    <?= e($step['title']) ?>
                </h4>
                <p style="
                    font-size:0.875rem;
                    color:var(--color-text-secondary);
                    line-height:1.6;
                    margin:0;
                "><?= e($step['desc']) ?></p>
            </div>
            <?php endforeach; ?>

        </div>

        <!-- CTA below steps -->
        <div style="text-align:center;margin-top:var(--space-2xl);">
            <a href="<?= site_url('signup') ?>" class="btn btn--gold btn--lg">
                Start Building Your Storefront
            </a>
        </div>

    </div>
</section>
