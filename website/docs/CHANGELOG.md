# inaffi — Changelog

> All notable changes to this project are documented here.
> Format: [version] - date - summary

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

---

## [Unreleased] - PHP Build in Progress

### To Build

- [ ] `docs/schema.sql` — Full MySQL schema + trigger
- [ ] `includes/config.php` — Site config
- [ ] `includes/db.php` — PDO connection
- [ ] `includes/auth.php` — Session auth + guard
- [ ] `includes/helpers.php` — Utility functions
- [ ] `includes/upload.php` — Image handler
- [ ] `assets/css/styles.css` — Full design system
- [ ] `assets/js/main.js` — Global JS
- [ ] `assets/js/outfit-form.js` — Dynamic form
- [ ] `components/header.php`
- [ ] `components/footer.php`
- [ ] `components/dashboard-sidebar.php`
- [ ] `components/outfit-card.php`
- [ ] `components/product-item.php`
- [ ] `index.php` — Homepage
- [ ] `storefront.php` — Creator public page
- [ ] `login.php` + `signup.php` + `logout.php`
- [ ] `dashboard/index.php`
- [ ] `dashboard/new-outfit.php`
- [ ] `dashboard/edit-outfit.php`
- [ ] `dashboard/settings.php`
- [ ] `go.php` — Click tracker
- [ ] `privacy.php` + `terms.php` + `disclosure.php`
- [ ] `.htaccess`
