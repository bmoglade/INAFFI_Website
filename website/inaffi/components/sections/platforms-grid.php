<?php
// ============================================================
// SECTION: Platforms Grid
// ============================================================
// Shows all supported e-commerce platforms as a visual grid
// with logo + name + brand color badge.
// Stats row at bottom: platform count, max products, free.
//
// Auto-reads from config.php — add a platform once
// in config.php and it appears here automatically.
//
// No variables required — reads global config arrays.
// ============================================================

global $PLATFORMS, $PLATFORM_COLORS, $PLATFORM_LOGOS;

// All platforms except "Other"
$display_platforms = array_filter($PLATFORMS, fn($p) => $p !== 'Other');
$platform_count    = count($display_platforms);
?>

<section class="section" style="background:var(--color-background);">
    <div class="container">

        <!-- Heading -->
        <div style="text-align:center;max-width:500px;margin:0 auto var(--space-2xl);">
            <p style="
                font-size:0.8125rem;font-weight:600;
                letter-spacing:0.1em;text-transform:uppercase;
                color:var(--color-gold-accent);margin-bottom:12px;
            ">Supported Platforms</p>
            <h2 style="margin-bottom:12px;">
                All Your Favourite Stores,
                <span style="color:var(--color-gold-accent);">One Link</span>
            </h2>
            <p style="color:var(--color-text-secondary);">
                Add products from any of these platforms to your outfit looks.
            </p>
        </div>

        <!-- Platform logo grid -->
        <div style="
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:var(--space-md);
            margin-bottom:var(--space-2xl);
        ">
            <?php foreach ($display_platforms as $i => $pname):
                $colors = $PLATFORM_COLORS[$pname] ?? ['#666666','#FFFFFF'];
            ?>
            <div class="animate-fade-in-up" style="
                animation-delay:<?= ($i % 5) * 0.07 ?>s;
                background:var(--color-surface);
                border:1px solid var(--color-border);
                border-radius:var(--radius-md);
                padding:var(--space-lg) var(--space-md);
                display:flex;
                flex-direction:column;
                align-items:center;
                gap:10px;
                text-align:center;
                transition:border-color var(--transition-fast),
                           box-shadow var(--transition-fast);
            "
            onmouseover="this.style.borderColor='<?= e($colors[0]) ?>';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
            onmouseout="this.style.borderColor='var(--color-border)';this.style.boxShadow='none'"
            >
                <?php render_platform_logo($pname, 48); ?>

                <span style="
                    font-size:0.8125rem;
                    font-weight:600;
                    color:var(--color-text-primary);
                "><?= e($pname) ?></span>

                <!-- Brand colour dot -->
                <span style="
                    display:inline-block;
                    width:8px;height:8px;
                    border-radius:50%;
                    background:<?= e($colors[0]) ?>;
                " aria-hidden="true"></span>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- Stats row -->
        <div style="
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:var(--space-lg);
            background:var(--color-primary-dark);
            border-radius:var(--radius-md);
            padding:var(--space-xl);
        ">
            <?php
            $stats = [
                ['value' => $platform_count . '+', 'label' => 'Platforms Supported'],
                ['value' => '15',                  'label' => 'Products per Look'],
                ['value' => '∞',                   'label' => 'Outfits You Can Create'],
                ['value' => 'Free',                'label' => 'Forever, No Credit Card'],
            ];
            foreach ($stats as $i => $stat):
            ?>
            <div style="text-align:center;" class="animate-fade-in-up delay-<?= $i+1 ?>">
                <p style="
                    font-family:var(--font-display);
                    font-size:2rem;
                    font-weight:700;
                    color:var(--color-gold-accent);
                    line-height:1;
                    margin-bottom:8px;
                "><?= e($stat['value']) ?></p>
                <p style="
                    font-size:0.875rem;
                    color:rgba(255,255,255,0.6);
                    margin:0;
                "><?= e($stat['label']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
