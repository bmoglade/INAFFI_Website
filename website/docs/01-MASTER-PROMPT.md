# inaffi — Master Prompt

> Product vision, design system, and current build status.
> This is a **living document** — update it every session.

---

## Product Overview

**inaffi.com** is an affiliate-driven creator storefront platform for the Indian fashion market.

Fashion influencers ("creators") build a personal storefront at `inaffi.com/username`.
They upload outfit photos and tag each item with affiliate links from major Indian e-commerce platforms.
Followers visit the storefront, shop complete outfits, and creators earn commission on every click.

### Core Value Proposition

| For Creators | For Shoppers |
|---|---|
| One place for all affiliate links | Shop complete looks — not individual items |
| Personal branded storefront | Verified links that never expire |
| Real-time analytics dashboard | Built-in price comparison across platforms |
| Brand collaboration opportunities | Curated by real fashion creators |

### Target Market

- **Country:** India
- **Creators:** Micro-influencers (10K–200K followers) to mid-tier (200K–2M)
- **Shoppers:** Fashion-forward Indian woman, 18–35, active on Instagram/Pinterest
- **Platforms:** Amazon, Flipkart, Myntra, Nykaa, Ajio, Meesho + 5 others

---

## Design System

**Source of truth:** WT `v1` branch → `website/app/globals.css` and `tailwind.config.ts`

> ⚠️ Always read from `wt/v1`. `wt/master` has different outdated values.

### Color Palette — Exact Values (from WT v1 `globals.css`)

```css
/* --- Core palette --- */
--color-background:        #0A0C14;   /* obsidian black — page bg */
--color-surface:           #12141E;   /* card bg */
--color-surface-elevated:  #1A1C28;   /* raised/nested card bg */
--color-gold-accent:       #C9A96E;   /* champagne gold — PRIMARY BRAND COLOR */
--color-gold-accent-hover: #B8944F;
--color-text-primary:      #F4EDE4;   /* warm ivory — main text */
--color-text-secondary:    #8A8680;   /* muted grey — supporting text */
--color-border:            #252730;
--color-border-light:      #1E2028;
--color-wine:              #4A2E35;

/* --- Section backgrounds --- */
--color-section-alt:       #0E1019;   /* alternating sections */
--color-section-darker:    #080A10;   /* footer */

/* --- Product cards + Shop button --- */
--color-product-card-bg:     #12141E;
--color-product-card-border: #252730;
--color-product-card-hover:  #C9A96E;
--color-shop-btn-bg:         #C9A96E;
--color-shop-btn-text:       #0A0C14;
--color-shop-btn-hover-bg:   #B8944F;

/* --- Gold glow effects --- */
--color-gold-glow:        rgba(201, 169, 110, 0.15);
--color-gold-glow-strong: rgba(201, 169, 110, 0.30);

/* --- Buttons --- */
--btn-radius: 9999px;   /* ALL buttons are pills — no exceptions */
```

### Typography

| Role | Font |
|---|---|
| Display / Headings | Playfair Display, Georgia, serif |
| Body / UI | DM Sans, system-ui, sans-serif |

### Layout

| Token | Value |
|---|---|
| Max content width | 1100px (`.container-content`) |
| Header height | 64px (`h-16`) |
| Standard section padding | `py-16` → `py-20` → `py-24` (mobile → sm → lg) |

### Key CSS Utility Classes

| Class | Purpose |
|---|---|
| `.container-content` | Max 1100px, centered, horizontal padding |
| `.gold-border-glow` | Gold glow border for premium cards |
| `.card-hover` | `translateY(-4px)` + shadow on hover |
| `.image-hover-zoom` | `scale(1.04)` on hover |
| `.animate-scroll-x` | Infinite horizontal scroll (brand strip) |
| `.section-gold-top` | Gold divider line at top of section |
| `.skeleton` | Pulsing loading skeleton |

---

## Homepage Layout (from WT v1 `app/page.tsx`)

6 sections + footer. **Must match WT v1 exactly.**

### Section 1: Hero

- **bg:** `--color-background`
- **layout:** `flex lg:flex-row` — left 35% / right 65%
- **left:** eyebrow "India's #1 Creator Platform" (gold, 11px, tracking-[0.25em]), h1 "Monetize Your Taste" (gold, `text-7xl`, `leading-[1.05]`), p "Create, influence, and earn — all in one place.", CTA "Join inaffi" (white border outline pill)
- **right:** 3-column collage of 6 images — alternating `-rotate-2`/`rotate-2`, hover resets to `rotate-0`. Sourced from `landing_images` slots `hero-1`–`hero-6`, fallback to outfit images from DB
- **padding:** `py-16 sm:py-20 lg:py-24`

### Section 2: How It Works

- **bg:** `--color-section-alt`
- **eyebrow:** "The Process" (gold, uppercase, `tracking-[0.3em]`)
- **heading:** "How {name} Works" + gold underline (`w-20 h-[3px]`)
- **cards:** 3 step cards numbered 01/02/03 with `→` arrows between (desktop), platform logos inside card 02
- **stats bar:** bordered 3-col box — "3x Higher Conversion Rate" | "1-Click Outfit Publishing" | "100% Commission Tracking"
- **padding:** `py-20 sm:py-24 lg:py-28`

### Section 3: For Creators

- **bg:** `--color-background`
- **layout:** `flex lg:flex-row` — left 45% copy / right 55% dashboard mockup card
- **eyebrow:** "For Creators" (gold)
- **heading:** "Turn Your Style Into Income"
- **bullets:** ✦ Personal storefront | ✦ One-click tagging from 5+ platforms | ✦ Real-time analytics | ✦ Brand collaboration opportunities
- **CTAs:** "Apply to Join" (gold filled pill) + "See How It Works" (border pill)
- **right:** `gold-border-glow` card — fake dashboard with creator info row, 3 stat boxes (₹24,500 / 156 clicks / 12 outfits), mini outfit cards
- **padding:** `py-16 sm:py-20 lg:py-24`

### Section 4: For Shoppers *(❌ Not yet built)*

- **bg:** `--color-section-alt`
- **layout:** `flex flex-col-reverse lg:flex-row` — left 45% phone mockup / right 55% copy
- **eyebrow:** "For Shoppers" (gold)
- **heading:** "Shop Complete Looks. Instantly."
- **left mockup:** phone frame (`aspect-[3/4] w-56`), floating product tags, "Shop This Look" badge, mini grid, platform logos
- **bullets:** 🔗 Verified links | 💰 Price comparison | 🛒 Seamless checkout | ☑️ Curated by creators
- **CTA:** "Explore Looks" (gold outline pill)
- **padding:** `py-16 sm:py-20 lg:py-24`

### Section 5: Pricing *(❌ Not yet built)*

- **bg:** `--color-background`
- **eyebrow:** "Pricing" (gold)
- **heading:** "Choose Your Plan", subtext "Start free. Scale as you grow."
- **decorative toggle:** Monthly | Yearly (Save 20%)
- **tiers:**
  - Starter — FREE FOREVER, ₹0, 4 features, "Get Started"
  - Pro — MOST POPULAR, ₹999/mo, 6 features, "Start Pro Trial" *(highlighted — gold border)*
  - Enterprise — Custom, 5 features, "Contact Us"
- **trust line:** "🔒 No credit card required · Cancel anytime · 14-day free trial on Pro"
- **padding:** `py-16 sm:py-20 lg:py-24`

### Section 6: Footer CTA

- **bg:** `--color-section-alt` + `section-gold-top` class
- **layout:** centered, `max-w-2xl`
- **SVG halo** (ellipse arc, `#C9A96E`) above heading
- **heading:** "Ready to Monetize Your Style?"
- **subtext:** "Join thousands of creators earning from their fashion influence."
- **CTA:** "Join inaffi" (gold filled pill, uppercase, `tracking-wider`)
- **social proof:** 4 overlapping avatar circles + "+10,000 creators"
- **hint:** "Free to join · No credit card needed"
- **padding:** `py-16 sm:py-20 lg:py-24`

### Footer

- **bg:** `--color-section-darker` (`#080A10`)
- **border:** `border-t border-border`
- **padding:** `py-12 lg:py-16`
- **4-column grid:**
  - Col 1 — Brand: SVG halo + name (Playfair, `tracking-[0.15em]`), tagline "One link. Complete looks. Zero friction.", social icons (Instagram, Pinterest, YouTube, Twitter)
  - Col 2 — Platform: How It Works, For Creators, For Shoppers, Pricing
  - Col 3 — Company: About Us, Partners, Contact
  - Col 4 — Support: Creator Guide, Affiliate Terms, Privacy Policy, Terms of Use
- **bottom bar:** © year inaffi | Platform logo badges (first 6, 32×32px, `bg-surface-elevated`)

---

## Header (from WT v1 `components/layout/Header.tsx`)

```
Sticky top-0 z-50
bg-background/80 backdrop-blur-md
border-b border-border-light
height: h-16 (64px)

Layout: [Left nav] | [Center logo — absolute] | [Right auth]

Left nav (hidden mobile, md+):
  "About us" | "Shop" | "Creators"  →  /#about, /#shoppers, /#creators

Center logo (absolute left-1/2 -translate-x-1/2):
  → landing_images slot "site-logo" image (h-10) if exists
  → Fallback: SVG halo (ellipse #C9A96E) + brand name text

Right auth:
  Logged out: "Join as creator" (gold outline pill) + "Log in" (text)
  Logged in:  "← Dashboard" (text) + "Log Out" (red text)
```

---

## Platform Configuration (from WT v1 `lib/config.ts`)

### Platforms (11 total)
Amazon, Flipkart, Myntra, Nykaa, Ajio, Meesho, Tata Cliq, Bewakoof, H&M, Zara, Other

### Platform Badge Colors

| Platform | Background | Text |
|---|---|---|
| Amazon | `#FF9900` | `#000000` |
| Flipkart | `#2874F0` | `#FFFFFF` |
| Myntra | `#FF3F6C` | `#FFFFFF` |
| Nykaa | `#FC2779` | `#FFFFFF` |
| Ajio | `#1A1A1A` | `#FFFFFF` |
| Meesho | `#9B2EFA` | `#FFFFFF` |
| Tata Cliq | `#E42574` | `#FFFFFF` |
| Bewakoof | `#FDD835` | `#000000` |
| H&M | `#E50010` | `#FFFFFF` |
| Zara | `#000000` | `#FFFFFF` |
| Other | `#666666` | `#FFFFFF` |

### Business Rules (from WT v1 `siteConfig`)

| Rule | Value |
|---|---|
| Max products per outfit | 15 |
| Max bio length | 120 chars |
| Max outfit title length | 60 chars |
| Max product name length | 40 chars |
| Username min/max | 3 / 20 chars |
| Max image upload | 5MB (PHP accepts 10MB → GD compresses) |

### Categories
All, Office, Casual, Festive, Beauty, Home, Other

---

## `landing_images` Table (from WT v1 migration `003` — pending in PHP)

```sql
landing_images — id, section, slot, image_url, alt_text,
                 display_order, metadata JSON, created_at
```

| Section | Slots |
|---|---|
| Hero | `hero-1` through `hero-6` |
| Header | `site-logo` |
| Creators section | `creator-profile`, `creator-screen` |
| Shoppers section | `shopper-main`, `shopper-grid-1` through `shopper-grid-6` |
| CTA avatars | `cta-avatar-1` through `cta-avatar-4` |

Until built: hero falls back to outfit images from DB; logo falls back to SVG halo + text.

---

## Build Status (as of 2026-05-12)

### ✅ Complete and live

| Feature | PHP File |
|---|---|
| Homepage hero | `components/sections/hero.php` |
| Brand strip | `components/sections/brand-strip.php` |
| How It Works | `components/sections/how-it-works.php` |
| Creator showcase (partial) | `components/sections/creator-showcase.php` |
| Footer CTA strip (partial) | `components/sections/cta-strip.php` |
| Header | `components/header.php` |
| Footer (2-col — needs update) | `components/footer.php` |
| Creator storefront | `storefront.php` |
| Auth — signup/login/logout | `signup.php`, `login.php`, `logout.php` |
| Dashboard — overview | `dashboard/index.php` |
| Dashboard — new/edit outfit | `dashboard/new-outfit.php`, `dashboard/edit-outfit.php` |
| Dashboard — settings | `dashboard/settings.php` |
| Click tracker | `go.php` |
| Admin panel | `admin/index.php` |
| AJAX toggles (stock/publish/feature) | `ajax/*.php` |
| Legal pages | `privacy.php`, `terms.php`, `disclosure.php` |

### ❌ Not yet built (priority order)

| Feature | Priority | WT v1 Reference |
|---|---|---|
| "For Shoppers" section | HIGH | `app/page.tsx` Section 4 `#shoppers` |
| "Pricing" section | HIGH | `app/page.tsx` Section 5 `#pricing` |
| Footer 4-column update | HIGH | `components/layout/Footer.tsx` |
| "For Creators" section v1 match | HIGH | `app/page.tsx` Section 3 `#creators` |
| `landing_images` table + schema | MEDIUM | migration `003` |
| Admin landing images upload UI | MEDIUM | admin panel extension |
| Header logo from DB | MEDIUM | `components/layout/Header.tsx` |

---

## Git Remotes

| Remote | Purpose |
|---|---|
| `origin` | Foundry — `ri.stemma.main.repository.0f6c5b1d-a7d2-44a2-ad1a-d9d8b69599c2` |
| `github` | GitHub — `bmoglade/INAFFI_Website` → Hostinger auto-deploy |
| `wt` | WT reference repo — READ ONLY — branch `v1` |

---

## Project Contacts

- **Owner:** Bhushan Moglade
- **Live site:** https://inaffi.com
- **Admin account:** `Admin@inaffi.com` (set `is_admin=1` in DB)
- **GitHub:** https://github.com/bmoglade/INAFFI_Website
