<?php
// ============================================================
// SECTION: Info — How it works + Earnings potential
// Matches WT reference: py-14 sm:py-16 lg:py-20
// 45/55 grid, max-w-4xl centered
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

<section class="info-section">
    <div class="container-content">
        <div class="info-inner">

            <!-- 45%: How it works -->
            <div class="info-col">
                <h2 class="info-heading">How it works</h2>
                <div class="info-steps">
                    <?php foreach ($steps as $step): ?>
                    <div class="info-step-row">
                        <span class="info-step-num">
                            <?= str_pad(e($step['num']), 2, '0', STR_PAD_LEFT) ?>
                        </span>
                        <span class="info-step-text"><?= e($step['title']) ?></span>
                    </div>
            <?php endforeach; ?>
        </div>
            </div>

            <!-- 55%: Earnings potential -->
            <div class="info-col">
                <h2 class="info-heading">Earnings potential</h2>
                <div class="info-earnings">
                    <p class="info-earnings__amount">
                        ₹45,000<span class="info-earnings__period">/month</span>
                    </p>
                    <p class="info-earnings__sub">
                        Earned by active creators in affiliate commissions
                    </p>
                    <div class="info-creators">
                        <div class="info-creators__avatars">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <div class="info-creators__avatar"></div>
                            <?php endfor; ?>
        </div>
                        <span class="info-creators__label">
                            10K+ creators earning with <?= e(SITE_NAME) ?>
                        </span>
    </div>
                </div>
            </div>

        </div>
    </div>
</section>

