# inaffi — Changelog

> All notable changes to this project are documented here.
> Format: [version] - date - summary

---

## [1.3.0] - 2026-05-12 - Documentation Rebuilt Clean (All 4 Files)

### Summary

All four documentation files fully rebuilt from scratch as clean, properly-formatted Markdown.
Previous versions had been corrupted — `02-ARCHITECTURE.md` was reduced to a single line,
`00-SESSION-START-PROMPT.md` and `01-MASTER-PROMPT.md` contained AI meta-commentary instead
of actual content. All files are now correct, complete, and properly committed.

### Files Rebuilt

- `00-SESSION-START-PROMPT.md` — 162 lines: role, WT v1 reference, project quick-start, build status, session rules, example prompts
- `01-MASTER-PROMPT.md` — 318 lines: product overview, full design system, all 6 section specs (with WT v1 detail), header/footer specs, platform colors, business rules, build status
- `02-ARCHITECTURE.md` — 400 lines: WT mapping, route map, section status, header/footer specs, design tokens, DB schema, folder tree, data flows, auth, security, deployment
- `03-DEVELOPER-GUIDE.md` — 399 lines: WT v1 reading guide, Tailwind→CSS translation, design tokens, PHP patterns, common tasks, section guide, deploy, troubleshooting (17 items), pending features

### No code changes this session — documentation only

---

## [1.2.0] - 2026-05-11 - Full Documentation Overhaul (WT v1 Aligned)

### Summary

Comprehensive documentation update to reflect the correct WT reference branch (`v1`, not `master`) and fully document all current and pending PHP features.

### Critical Correction: WT Reference Branch

- **Previous docs referenced `wt/master`** — this was wrong. `master` is the old v0.5.1 with outdated values.
- **All docs now reference `wt/v1`** — the correct redesign branch with the luxury dark theme.
- Fixed all gold color references: `#C9A96E` (v1) not `#D6B25E` (master)
- Fixed all background references: `#0A0C14` (v1) not `#0B0B0F` (master)

### Documentation Files Updated

#### `00-SESSION-START-PROMPT.md` — Full rewrite

- Corrected WT branch to `v1` with explicit warning about `master` being wrong
- Added table of exact design token values (gold, backgrounds, text, borders)
- Added key WT v1 file → `git show` command reference table
- Added current build status (✅ done / ❌ pending)
- Added session rules (7 rules to follow each session)
- Added example session prompts for next build tasks

#### `01-MASTER-PROMPT.md` — Full rewrite

- Complete product overview with creator + shopper value prop
- Full design system with exact CSS variable values from WT v1 `globals.css`
- Detailed layout specs for all 6 homepage sections (from WT v1 `app/page.tsx`)
- Header spec (from WT v1 `components/layout/Header.tsx`)
- Footer spec (from WT v1 `components/layout/Footer.tsx`)
- Platform badge colors (all 11 platforms, from WT v1 `lib/config.ts`)
- Business rules/constraints table
- `landing_images` table slot definitions
- Full build status table (✅ complete / ❌ pending with priorities)

#### `02-ARCHITECTURE.md` — Full rewrite

- WT v1 reference metadata table (repo RID, branch, fetch command, commit)
- Updated WT v1 vs WT_2 stack mapping table
- Full route map (14 routes) with WT v1 equivalents and auth requirements
- Homepage sections table with PHP file, status, and WT v1 section ID
- Header and footer specs (from WT v1 source)
- Design tokens table (exact values from WT v1 `globals.css` — confirmed correct)
- DB schema (4 tables + pending `landing_images`)
- Full folder structure with WT v1 equivalents per file
- Data flows: consumer storefront, creator publishing, homepage featured outfit
- Out-of-stock logic (matches WT v1)
- Auth flow (signup/login/protected routes)
- Security measures table
- Key architecture decisions with rationale
- Deployment workflow

#### `03-DEVELOPER-GUIDE.md` — Full rewrite

- First-thing-every-session checklist with exact `git show wt/v1:...` commands
- WT v1 reference guide: branch explanation, key files table
- Complete Tailwind → PHP CSS translation table (30+ mappings)
- Design tokens ready to copy into `styles.css `:root`
- Config system documentation
- PHP coding patterns (DB queries, output escaping, file paths, auth guards, CSRF, flash, image upload)
- Common tasks (rename platform, change theme, add platform/category, add page, etc.)
- Homepage section development guide with CSS structure
- Deploy workflow
- New feature checklist
- Documentation maintenance rules (which docs to update for each change type)
- Troubleshooting table (17 known issues with causes and fixes)
- Configurable elements quick reference
- Pending features table (from WT v1 — not yet in PHP)
- Git remotes reference

### No code changes this session — documentation only

---

## [1.1.0] - 2026-05-11 - Homepage Rebuild + WT Reference Integration

### Summary

Complete homepage redesign to exactly match the WT Next.js reference repository (v0.5.1).
All colors, layout, spacing, and component structure now sourced directly from WT source files.

### WT Reference Integration

- Established WT repo (`ri.stemma.main.repository.b2e8b5a3-4b79-4e84-ae4e-b75249b50c7a`) as official design/layout source of truth
- Added `git remote add wt` workflow to fetch WT source directly
- All future UI work starts with reading the relevant WT component first
- ⚠️ Note: this session used `wt/master` (v0.5.1) — corrected to `wt/v1` in v1.2.0

### Design System Changes (from WT `master` globals.css — see v1.2.0 correction)

| Token                    | Old Value | New Value (applied in v1.1) |
| ------------------------ | --------- | --------------------------- |
| `--color-background`     | `#FAF8F5` | `#0B0B0F`                   |
| `--color-surface`        | `#FFFFFF` | `#161618`                   |
| `--color-border`         | `#EDE9E3` | `#2A2A2E`                   |
| `--color-gold-accent`    | `#C9A96E` | `#D6B25E`                   |
| `--color-text-primary`   | `#1A1A1A` | `#F4EDE4`                   |
| `--color-text-secondary` | `#888888` | `#9A9490`                   |
| `--color-shop-btn-bg`    | `#1A1A1A` | `#D6B25E`                   |
| `--color-shop-btn-text`  | `#FFFFFF` | `#0B0B0F`                   |

> ⚠️ Values above from `wt/master`. Correct v1 values: gold=`#C9A96E`, bg=`#0A0C14`, surface=`#12141E`. See v1.2.0.

### Header (matches WT master Header.tsx)

- Gold Playfair Display logo left-aligned
- Text "Log in" + gold-outline "Join as Creator" button right
- `border-b border-border bg-surface` dark theme
- Removed 3-column nav — simplified to match WT

### Homepage (matches WT master page.tsx — pre-v1 layout)

- **Hero:** `flex lg:flex-row`, left 38% / right 62%
  - Left: h1 "Monetize Your Taste.", "Build→Share→Earn", gold-outline CTA "Start for Free"
  - Right: outfit card (image-left + product-list-right) + italic tagline
- **Brand strip:** `py-4`, `border-y bg-surface rounded-md`, 3x duplicate for seamless 20s loop
- **Info section:** `py-14→py-20`, `max-w-4xl`, 45/55 grid
  - Left: "How it works" — numbered steps 01/02/03
  - Right: "Earnings potential" — ₹45,000/month + avatar stack

### Footer (matches WT master Footer.tsx — 2-col, pre-v1)

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
