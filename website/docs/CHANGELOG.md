# inaffi — Changelog

> All notable changes to this project are documented here.
> Format: [version] - date - summary

---

## [1.1.0] - 2026-05-11 - Homepage Rebuild + WT Reference Integration

### Summary

Complete homepage redesign to exactly match the WT Next.js reference repository (v0.5.1).
All colors, layout, spacing, and component structure now sourced directly from WT source files.

### WT Reference Integration

- Established WT repo (`ri.stemma.main.repository.b2e8b5a3-4b79-4e84-ae4e-b75249b50c7a`) as official design/layout source of truth
- Added `git remote add wt` workflow to fetch WT source directly
- All future UI work starts with reading the relevant WT component first

### Design System Changes (from WT globals.css)

| Token                    | Old Value | New Value (WT) |
| ------------------------ | --------- | -------------- |
| `--color-background`     | `#FAF8F5` | `#0B0B0F`      |
| `--color-surface`        | `#FFFFFF` | `#161618`      |
| `--color-border`         | `#EDE9E3` | `#2A2A2E`      |
| `--color-gold-accent`    | `#C9A96E` | `#D6B25E`      |
| `--color-text-primary`   | `#1A1A1A` | `#F4EDE4`      |
| `--color-text-secondary` | `#888888` | `#9A9490`      |
| `--color-shop-btn-bg`    | `#1A1A1A` | `#D6B25E`      |
| `--color-shop-btn-text`  | `#FFFFFF` | `#0B0B0F`      |

### Header (matches WT Header.tsx)

- Gold Playfair Display logo left-aligned
- Text "Log in" + gold-outline "Join as Creator" button right
- `border-b border-border bg-surface` dark theme
- Removed 3-column nav — simplified to match WT

### Homepage (matches WT page.tsx exactly)

- **Hero:** `flex lg:flex-row`, left 38% / right 62%
  - Left: h1 "Monetize Your Taste.", "Build→Share→Earn", gold-outline CTA "Start for Free"
  - Right: outfit card (image-left + product-list-right) + italic tagline
- **Brand strip:** `py-4`, `border-y bg-surface rounded-md`, 3x duplicate for seamless 20s loop
- **Info section:** `py-14→py-20`, `max-w-4xl`, 45/55 grid
  - Left: "How it works" — numbered steps 01/02/03
  - Right: "Earnings potential" — ₹45,000/month + avatar stack

### Footer (matches WT Footer.tsx)

- `border-t border-border bg-surface`
- Gold brand name + tagline
- Privacy · Terms · Disclosure links
- Copyright line with year

### Admin Panel

- Cleaned up layout with section labels (SITE SETTINGS / CONTENT / HOMEPAGE)
- Cards have clear titles with emoji icons
- Featured outfit card shows gold border when active, dashed border when empty

### Deployment Fixes

- `RewriteBase /website/inaffi/` — fixed for Hostinger subdirectory deploy
- `dirname(__DIR__)` replaces `DOCUMENT_ROOT` for all file path resolution
- Root `public_html/.htaccess` forwards all traffic to app folder
- GitHub (`bmoglade/INAFFI_Website`) → Hostinger auto-deploy pipeline active

### Documentation Updated

- `00-SESSION-START-PROMPT.md` — full rewrite with WT reference instructions, live state, design tokens, workflow rules
- `01-MASTER-PROMPT.md` — WT reference section added, design system from WT, homepage layout documented, current status updated
- `02-ARCHITECTURE.md` — WT vs WT_2 mapping table, updated folder structure, WT token values, deployment workflow
- `03-DEVELOPER-GUIDE.md` — WT reading instructions, Tailwind→CSS translation table, updated patterns, troubleshooting

---

## [1.0.0] - 2026-05-10 - PHP/MySQL Migration (Full Rebuild)

### Migration Summary

Complete technology migration from Next.js/Supabase/Vercel to PHP/MySQL/Hostinger.
All features, design, and product concepts preserved. Technology stack replaced entirely.

### Stack Changes

| Layer         | Before                         | After                                     |
| ------------- | ------------------------------ | ----------------------------------------- |
| Server        | Next.js 14 (Node.js) on Vercel | PHP 8.x on Hostinger                      |
| Database      | Supabase (PostgreSQL)          | Hostinger MySQL                           |
| Auth          | Supabase Auth (JWT)            | PHP Sessions + bcrypt                     |
| Image Storage | Supabase Storage buckets       | Local uploads/ folder (GD compression)    |
| Frontend      | React + Tailwind CSS           | HTML + CSS custom properties + Vanilla JS |
| Deployment    | GitHub → Vercel auto-deploy    | FTP upload to public_html/                |
| Domain        | stylelink-phi.vercel.app       | inaffi.com                                |
| Build Step    | `pnpm build`                   | None                                      |

### Documentation Updated

- `01-MASTER-PROMPT.md` — Rewritten for PHP/MySQL role and tech stack
- `02-ARCHITECTURE.md` — Full PHP architecture: routes, folder structure, DB schema, data flows, security
- `03-DEVELOPER-GUIDE.md` — PHP patterns, config system, deployment, troubleshooting
- `00-SESSION-START-PROMPT.md` — Updated context summary and example prompts

### Architecture Decisions

- PHP sessions over JWT (simpler on shared hosting, no library needed)
- INT AUTO_INCREMENT PKs over UUIDs (better MySQL performance)
- PDO prepared statements (security, consistency)
- GD library for image compression (built into PHP, no install needed)
- Vanilla JS only (no build step, works on shared hosting)
- `.htaccess` URL rewriting for clean URLs
- `includes/config.php` as single source of truth (mirrors original `lib/config.ts`)
- Local `uploads/` folder (Hostinger 20GB disk, no external storage API)
- CSRF tokens on all POST forms (security best practice)

### Features Preserved (All from v0.5.1)

- ✅ Public creator storefront (/username)
- ✅ Creator auth (email + password)
- ✅ Creator dashboard (stats + outfit list)
- ✅ Create / Edit outfit (up to 15 products)
- ✅ Profile settings (bio, photo, social handles)
- ✅ Click tracking (/go?p=product_id)
- ✅ Homepage with featured outfit + scrolling brand strip
- ✅ Out-of-stock hiding
- ✅ Dashboard category filters
- ✅ Mobile responsive (hamburger sidebar)
- ✅ Admin-featured outfit on homepage
- ✅ 10 platforms (Amazon, Flipkart, Myntra, Nykaa, Ajio, Meesho, Tata Cliq, Bewakoof, H&M, Zara)
- ✅ Google Analytics 4
- ✅ Legal pages (Privacy, Terms, Disclosure)
- ✅ Shareable outfit links
- ✅ Design system (identical colors, fonts, animations)

### Files Built

- `docs/schema.sql` — Full MySQL schema + triggers
- `includes/config.php` — Site config (DB, platforms, categories)
- `includes/db.php` — PDO singleton
- `includes/auth.php` — Session auth + guards
- `includes/helpers.php` — e(), site_url(), redirect(), flash()
- `includes/upload.php` — GD image compress + save
- `assets/css/styles.css` — Full design system
- `assets/js/main.js` — Global JS (AJAX toggles, copy links, etc.)
- `assets/js/outfit-form.js` — Dynamic product form
- `components/header.php` / `footer.php` / all component partials
- `components/sections/` — All homepage sections
- `index.php` / `storefront.php` / `login.php` / `signup.php` / `logout.php`
- `dashboard/` — All 4 dashboard pages
- `admin/index.php` — Admin panel
- `ajax/` — 3 AJAX toggle endpoints
- `.htaccess` — URL rewriting

---

## Previous Version History (Next.js era — for reference only)

### [0.5.1] - 2026-05-05 - Homepage Layout Polish + New Platforms

- Scrolling brand logo strip (infinite CSS animation)
- 4 new platforms: Tata Cliq, Bewakoof, H&M, Zara
- Homepage layout: 3 stacked sections (Hero → Brand strip → Info)
- Golden styling for "Build → Share → Earn" and tagline

### [0.5.0] - 2026-05-04 - Landing Page Redesign + Admin-Featured Outfit

- Admin-featured outfit system (⭐ toggle → auto-displays on homepage)
- WearThis-style homepage outfit card (image left + product list right)
- Platform logo system (square logos on product cards)
- DB trigger: only one featured outfit at a time

### [0.4.0] - 2025-05-02 - UI/UX Improvements

- Mobile responsive dashboard (hamburger menu)
- Dashboard category filters
- Google OAuth buttons (UI only, needs Supabase config)
- Logout accessible from all pages

### [0.3.0] - 2025-05-02 - Production Deployment

- Deployed to Vercel at stylelink-phi.vercel.app
- GitHub → Vercel auto-deploy pipeline

### [0.2.1] - 2025-XX-XX - UX Fixes

- Shareable outfit links
- Session-aware header

### [0.2.0] - 2025-XX-XX - Phase 2

- Product images
- Ad space slots
- Social handles (Facebook, Pinterest)
- CSS custom properties

### [0.1.0] - 2025-01-XX - Phase 1 Complete

- Full Next.js/Supabase baseline built and working
