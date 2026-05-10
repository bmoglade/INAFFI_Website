<?php
// ============================================================
// SECTION: Testimonials / Creator Quotes
// ============================================================
// Static creator quotes — edit the $testimonials array below
// to add, remove, or update quotes.
//
// To disable: comment out its line in index.php
// To add more: add another entry to $testimonials array
// ============================================================

$testimonials = [
    [
        'name'     => 'Priya Sharma',
        'handle'   => '@priya.styles',
        'platform' => 'Instagram',
        'quote'    => 'I used to lose clicks because followers couldn\'t find the right product. Now my bio link does all the work. My Myntra commission doubled in the first month.',
        'avatar'   => '',   // leave empty for initial fallback
        'initial'  => 'P',
        'color'    => '#FF3F6C',
    ],
    [
        'name'     => 'Ananya Kapoor',
        'handle'   => '@ananyakapoor.ootd',
        'platform' => 'YouTube',
        'quote'    => 'My followers always asked "where did you get that?" — now I just say "link in bio" and everything is there. So simple, so clean.',
        'avatar'   => '',
        'initial'  => 'A',
        'color'    => '#FF0000',
    ],
    [
        'name'     => 'Meera Joshi',
        'handle'   => '@meerajoshi.fashion',
        'platform' => 'Instagram',
        'quote'    => 'I tried Linktree and it looked messy. This is beautiful — one page, my outfits, my products. My audience loves it and my Amazon clicks are up.',
        'avatar'   => '',
        'initial'  => 'M',
        'color'    => '#9B2EFA',
    ],
];
?>

<section class="section" style="background:var(--color-background);">
    <div class="container">

        <!-- Heading -->
        <div style="text-align:center;max-width:500px;margin:0 auto var(--space-2xl);">
            <p style="
                font-size:0.8125rem;font-weight:600;
                letter-spacing:0.1em;text-transform:uppercase;
                color:var(--color-gold-accent);margin-bottom:12px;
            ">Creator Stories</p>
            <h2 style="margin-bottom:12px;">
                What Creators
                <span style="color:var(--color-gold-accent);">Are Saying</span>
            </h2>
        </div>

        <!-- Testimonial cards -->
        <div style="
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:var(--space-lg);
        ">
            <?php foreach ($testimonials as $i => $t): ?>
            <div class="animate-fade-in-up" style="
                animation-delay:<?= $i * 0.1 ?>s;
                background:var(--color-surface);
                border:1px solid var(--color-border);
                border-radius:var(--radius-md);
                padding:var(--space-xl);
                display:flex;
                flex-direction:column;
                gap:var(--space-md);
                position:relative;
            ">
                <!-- Quote mark -->
                <div style="
                    font-family:var(--font-display);
                    font-size:4rem;
                    color:var(--color-gold-accent);
                    line-height:1;
                    opacity:0.3;
                    position:absolute;
                    top:16px;right:20px;
                " aria-hidden="true">"</div>

                <!-- Quote text -->
                <p style="
                    font-size:0.9375rem;
                    color:var(--color-text-primary);
                    line-height:1.7;
                    font-style:italic;
                    margin:0;
                    flex:1;
                ">"<?= e($t['quote']) ?>"</p>

                <!-- Author -->
                <div style="
                    display:flex;
                    align-items:center;
                    gap:var(--space-md);
                    padding-top:var(--space-md);
                    border-top:1px solid var(--color-border);
                ">
                    <!-- Avatar -->
                    <?php if (!empty($t['avatar'])): ?>
                        <img
                            src="<?= e($t['avatar']) ?>"
                            alt="<?= e($t['name']) ?>"
                            style="width:44px;height:44px;border-radius:50%;object-fit:cover;"
                        >
                    <?php else: ?>
                        <div style="
                            width:44px;height:44px;
                            border-radius:50%;
                            background:<?= e($t['color']) ?>;
                            display:flex;align-items:center;justify-content:center;
                            font-weight:700;color:#fff;font-size:1.125rem;
                            flex-shrink:0;
                        "><?= e($t['initial']) ?></div>
                    <?php endif; ?>

                    <div>
                        <p style="
                            font-weight:600;
                            font-size:0.9375rem;
                            color:var(--color-text-primary);
                            margin-bottom:2px;
                        "><?= e($t['name']) ?></p>
                        <p style="
                            font-size:0.8125rem;
                            color:var(--color-text-secondary);
                        "><?= e($t['handle']) ?> · <?= e($t['platform']) ?></p>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
