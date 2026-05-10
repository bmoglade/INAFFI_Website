<?php
// ============================================================
// inaffi.com — Platform Badge Component
// ============================================================
// Usage:
//   $platform = 'Amazon';
//   require 'components/platform-badge.php';
//
// Or as a function (include once, call many times):
//   render_platform_badge('Flipkart');
// ============================================================

/**
 * Render a coloured platform badge span.
 */
function render_platform_badge(string $platform): void {
    $style = platform_badge_style($platform);
    echo '<span class="badge" style="' . $style . '">' . e($platform) . '</span>';
}

/**
 * Render a square platform logo (image or fallback coloured square).
 *
 * @param string $platform   Platform name
 * @param int    $size       Size in px (default 40)
 */
function render_platform_logo(string $platform, int $size = 40): void {
    $logo_url = platform_logo_url($platform);
    $style    = platform_badge_style($platform);  // bg + text color for fallback

    // Extract background color from style string
    preg_match('/background:([^;]+)/', $style, $m);
    $bg = $m[1] ?? '#666666';

    if ($logo_url) {
        echo '<img'
            . ' src="' . e(site_url($logo_url)) . '"'
            . ' alt="' . e($platform) . '"'
            . ' width="' . $size . '"'
            . ' height="' . $size . '"'
            . ' style="border-radius:4px;object-fit:contain;"'
            . ' loading="lazy"'
            . ' onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'"'
            . '>';
        // Fallback hidden by default, shown if image 404s
        echo '<span'
            . ' style="display:none;width:' . $size . 'px;height:' . $size . 'px;'
            . 'background:' . e($bg) . ';border-radius:4px;'
            . 'align-items:center;justify-content:center;'
            . 'font-weight:700;color:#fff;font-size:' . round($size * 0.4) . 'px;"'
            . '>'
            . e(platform_initial($platform))
            . '</span>';
    } else {
        // No logo configured — show coloured square with initial
        echo '<span'
            . ' style="display:inline-flex;width:' . $size . 'px;height:' . $size . 'px;'
            . 'background:' . e($bg) . ';border-radius:4px;'
            . 'align-items:center;justify-content:center;'
            . 'font-weight:700;color:#fff;font-size:' . round($size * 0.4) . 'px;"'
            . '>'
            . e(platform_initial($platform))
            . '</span>';
    }
}
