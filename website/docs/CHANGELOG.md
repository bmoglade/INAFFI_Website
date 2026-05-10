# StyleLink — Changelog

> All notable changes to this project are documented here.
> Format: [version] - date - summary

---

## [0.2.1] - 2025-XX-XX - UX Fixes & Navigation Improvements

### Added

- **Shareable Outfit Links** — Each published outfit now has a copyable link (`site.com/username?look=outfitId`) shown on:
  - Dashboard Overview page (below each outfit)
  - Public Storefront page (at bottom of each outfit card)
  - Creators can use these links in Instagram reels, ads, or bio
- **"← Dashboard" link in Header** — When creator is logged in and views storefront, header shows link back to dashboard
- **"← Overview" button on Edit page** — After saving, easy navigation back to dashboard
- **Success message after edit** — Shows confirmation + links to Overview and Storefront
- **"My Storefront" in sidebar nav** — Always visible, opens in new tab
- **Storefront URL badge** — Shown at bottom of sidebar for quick reference
- **CopyLinkInput component** — Reusable client component for copyable URLs
- **AuthHeader component** — Minimal header for login/signup pages (no "Join as Creator" shown)
- **Test Users SQL** — Script with instructions to create 5 test accounts

### Changed

- **Product Card Layout** — Reordered to match reference: Platform badge (top) → Image → Name → Shop button
- **Header (public pages)** — Now session-aware: shows "← Dashboard" for logged-in creators, "Log in / Join" for visitors
- **Login/Signup pages** — Use AuthHeader (no "Join as Creator" button visible on auth pages)
- **Edit Outfit flow** — No longer redirects away after save; stays on page with success message + nav links
- **Product image upload** — Fixed state batching bug (both `image_file` and `image_url` now update in single setState call)
- **Product image upload** — Fixed file extension detection for compressed blobs + added explicit `contentType`

### Removed

- **Google OAuth buttons** — Temporarily hidden from Login and Signup pages (was showing error because not configured in Supabase). Code is preserved in comments — re-enable when Google OAuth is set up in Supabase Dashboard.

### Notes

- **Google OAuth can be re-enabled later:** Uncomment the Google button code in `app/login/page.tsx` and `app/signup/page.tsx` after configuring: Supabase Dashboard → Authentication → Providers → Google → Enable + add OAuth credentials from Google Cloud Console.

---

## [0.2.0] - 2025-XX-XX - Phase 2 Refinements (User Feedback)

### Added

- **Product Images** — Each product in an outfit can now have its own image (optional upload)
- **"Shop This Item" button** — Clear CTA under each product card guiding users to buy
- **Ad Space (Desktop)** — Left/right sidebar gutters on storefront for running ads
- **Ad Space (Mobile)** — Placeholder slots between outfits every 2nd card
- **Outfit Published URL** — After creating an outfit, shows copyable link for creator to use in bio/reels/ads
- **Facebook Handle** — Added to creator profile (settings + public storefront display)
- **Pinterest Handle** — Added to creator profile (settings + public storefront display)
- **Logout Button** — Added to dashboard sidebar so creators can sign out
- **CSS Custom Properties** — All colors now defined as CSS variables in `globals.css` for easy theme changes

### Changed

- **Product Grid Layout** — Changed from 3-column to **5-column (desktop) / 3-column (tablet) / 2-column (mobile)** to match reference design
- **Product Card Design** — New card-style layout: platform badge on top, image, name, shop button (matches reference)
- **Outfit Card Layout** — Removed total price display, moved category badge to image overlay
- **Color System** — Migrated from hardcoded Tailwind colors to CSS custom properties (change once → applies everywhere)
- **Product Card Colors** — Now configurable via CSS variables (`--color-product-card-bg`, `--color-product-card-border`, etc.)
- **Storefront Width** — Wider container (1440px) on desktop to accommodate ad gutters

### Removed

- **"Shop This Look" button** — Removed from outfit cards (individual product links are clearer)
- **Price display** — Removed from product cards and outfit total (Phase 1 scope reduction)
- **In-Stock Toggle** — Removed from create outfit form (defaults to in-stock; future: auto-detect from store)
- **next/image** in CreatorProfileHeader — Replaced with `<img>` for proxy compatibility

### Database Migration Required

Run `supabase/migrations/002_add_product_images_and_social_handles.sql`:

- Added `image_url` column to `products` table
- Added `facebook_handle` column to `creators` table
- Added `pinterest_handle` column to `creators` table

---

## [0.1.0] - 2025-01-XX - Phase 1 Complete (Baseline)

### Added

- **Public Storefront** (`/[username]`) — SSR page showing creator's published outfits with category filter
- **Authentication** — Email + password signup/login via Supabase Auth, Google OAuth support
- **Creator Dashboard** (`/dashboard`) — Stats (outfits, products, weekly clicks) + outfit list with publish/draft toggle
- **Create Outfit** (`/dashboard/outfits/new`) — Form with image upload, category, 1-15 products with affiliate links
- **Edit Outfit** (`/dashboard/outfits/[id]/edit`) — Pre-filled form, update/delete functionality
- **Profile Settings** (`/dashboard/settings`) — Display name, username, bio, profile photo, social handles
- **Click Tracking** (`/go/[productId]`) — Logs click + redirects to affiliate URL (302)
- **Out-of-Stock** — Toggle per product, hidden from public storefront, outfit hidden if all products OOS
- **Homepage** (`/`) — Value proposition, how-it-works, CTA for creators
- **Legal Pages** — Privacy policy, terms of service, affiliate disclosure
- **Google Analytics** — GA4 via gtag.js, conditional loading
- **Image Compression** — Client-side compression via browser-image-compression (target <500KB)
- **Responsive Design** — Mobile-first, Tailwind CSS, works on all screen sizes

### Technical Foundation

- Next.js 14.2.21 (App Router)
- React 18.2.0 (pinned)
- Supabase (PostgreSQL + Auth + Storage)
- TypeScript strict mode
- Tailwind CSS with custom design system
- Corporate proxy workarounds documented and implemented
- Centralized config (`lib/config.ts`) — single file rebrand
- Row Level Security on all database tables

### Database Schema

- `creators` — User profiles with username, bio, social handles
- `outfits` — Looks with title, category, image, publish state
- `products` — Items with name, platform, affiliate URL, price, stock status
- `clicks` — Tracking with product/outfit/creator IDs, user agent, referrer

---

## [0.3.0] - 2025-05-02 - Production Deployment to Vercel 🚀

### Added

- **Vercel Production Deployment** — Site is now live at `https://stylelink-phi.vercel.app/`
- **`vercel.json`** — Added framework detection config (`"framework": "nextjs"`) so Vercel correctly identifies the project
- **Resilient Middleware** — Middleware now has try/catch error handling and env var checks; if Supabase is unavailable, requests pass through instead of crashing with 500 error
- **GitHub Remote** — Repository now pushes to GitHub (`bmoglade/StyleLink_website`) in addition to Foundry; Vercel auto-deploys on every push to `main`

### Fixed

- **TypeScript type error in `lib/supabase-server.ts`** — Added explicit type annotation for `cookiesToSet` parameter (`{ name: string; value: string; options?: any }[]`) to fix build failure
- **TypeScript type error in `middleware.ts`** — Same `cookiesToSet` type annotation fix
- **MIDDLEWARE_INVOCATION_FAILED (500 error)** — Middleware now gracefully handles missing env vars and Supabase connection failures instead of crashing the entire site

### Deployment Setup

- **Vercel Config**: Framework = Next.js, Root Directory = `website`, Build = `npm run build`, Output = `.next`
- **Environment Variables on Vercel**: `NEXT_PUBLIC_SUPABASE_URL`, `NEXT_PUBLIC_SUPABASE_ANON_KEY`, `SUPABASE_SERVICE_ROLE_KEY`, `NEXT_PUBLIC_GA_MEASUREMENT_ID`, `NEXT_PUBLIC_APP_URL`, `NODE_ENV`
- **Supabase Auth Config**: Site URL and Redirect URLs updated to production Vercel URL
- **Auto-deploy pipeline**: Push to GitHub `main` → Vercel auto-builds and deploys

### Notes

- `NODE_TLS_REJECT_UNAUTHORIZED=0` must NEVER be set on Vercel — it is a local dev workaround only
- `NEXT_PUBLIC_APP_URL` on Vercel is set to `https://stylelink-phi.vercel.app/`
- Git SSL workaround for corporate proxy: `git -c http.sslVerify=false push github main`

---

## [0.4.0] - 2025-05-02 - UI/UX Improvements & Mobile Responsive Dashboard

### Added

- **Mobile Responsive Dashboard** — Sidebar hidden by default on mobile, toggleable via hamburger ☰ menu. Overlay darkens background when sidebar is open. Closes on route change or tap outside.
- **Mobile Header Bar** — Fixed header on dashboard mobile view with brand name + hamburger toggle
- **LogoutButton Component** — New client component (`components/layout/LogoutButton.tsx`) for logout on public pages
- **Logout on Public Pages** — "Log Out" button now visible in header on storefront/homepage when logged in (Issue #11)
- **Dashboard Category Filters** — Filter pills (All, Office, Casual, Festive, Beauty, Home, Other) on overview page to quickly find outfits (Issue #10)
- **DashboardOutfitList Component** — New client component (`components/dashboard/DashboardOutfitList.tsx`) with category filter + outfit count indicator
- **Google OAuth Buttons** — "Continue with Google" on login page, "Sign up with Google" on signup page with Google logo and "or" divider (requires Supabase Google provider setup)

### Changed

- **Homepage Header** — "Log in" now styled as bordered button (equal visual weight to "Join as Creator") (Issue #6)
- **Header (logged in)** — Shows "← Dashboard" + "Log Out" instead of just "← Dashboard" (Issue #5, #11)
- **Product Button Text** — "Shop This Item ↗" simplified to "Shop ↗" (Issue #13)
- **Platform Badge Alignment** — Centered within product card instead of left-aligned (Issue #14)
- **Outfit Image (Mobile)** — Increased height from h-72 to h-80/h-96 for better visibility on mobile (Issue #3)
- **Product Grid (Mobile)** — Removed max-height restriction so all products are visible without scrolling (Issue #3)
- **My Storefront Link** — Now opens in same tab instead of new window; user navigates back via "← Dashboard" in header
- **Post-Logout Redirect** — Now redirects to homepage `/` instead of `/login` (Issue #4)
- **Logout Button (Sidebar)** — Redesigned as prominent red button (full width, white text on red background) (Issue #5)
- **Edit Outfit — In-Stock Toggle** — Moved from top-right corner (was overlapping Remove button) to bottom of product card, visually disabled with "auto-managed" label
- **Edit Outfit — Remove Button** — Now has full space in top-right corner with no overlap

### Removed

- **Duplicate "View your public storefront →"** — Removed from bottom of dashboard overview page (already in sidebar) (Issue #12)

### New Files

- `website/components/layout/LogoutButton.tsx` — Client component for header logout
- `website/components/dashboard/DashboardOutfitList.tsx` — Outfit list with category filters

### Navigation Behavior

All internal links open in **same tab**:
| Click | Behaviour |
|-------|-----------|
| My Storefront (sidebar) | Same tab |
| Overview, New Outfit, Settings | Same tab |
| ← Dashboard (header) | Same tab |
| StyleLink logo | Same tab → homepage |
| **Shop ↗** (product) | **New tab** (external: Amazon/Flipkart) |
| **Social links** (Instagram etc.) | **New tab** (external) |

### Google OAuth Setup Required

To enable Google login, complete these steps:
1. Google Cloud Console → Create OAuth 2.0 credentials
2. Set redirect URI: `https://<SUPABASE_URL>/auth/v1/callback`
3. Supabase Dashboard → Authentication → Providers → Google → Enable
4. Paste Client ID + Client Secret
5. Buttons are already visible in code — no code changes needed

---

## [0.5.0] - 2026-05-04 - Landing Page Redesign + Admin-Featured Outfit

### Added

- **Admin-Featured Outfit on Homepage** — Admin can mark any outfit as "featured" from the dashboard. The homepage automatically displays that outfit's image + products (no manual file editing needed).
  - New toggle: ⭐ "Feature on Landing Page" in both Create and Edit outfit forms
  - Database trigger ensures only ONE outfit is featured at a time
  - Fallback: if no outfit is featured, static mockup data is displayed
- **`is_featured` column on outfits table** — Boolean flag, default false. Migration: `002_featured_outfit.sql`
- **`lib/queries.ts`** — Server-side query module. First function: `getFeaturedOutfit()` — reads featured outfit + products from DB
- **`lib/landing-mockup.ts`** — Static fallback data for homepage (used only when no featured outfit is set)
- **Platform Logo System** — Square store logos on product cards. Loads from `/images/platforms/<platform>.png`. Fallback: colored square with first letter.
  - `platformLogos` config in `lib/config.ts` — maps platform names to image paths
  - Created `public/images/platforms/` folder (add logo PNGs here)
- **Landing page image folder** — `public/images/landing/` for outfit + product images (static fallback only)
  - `README.md` inside folder with specs and instructions
- **Outfit image in homepage card** — Left panel shows outfit image (from DB or static file)
- **Product thumbnails in homepage card** — Each product row shows its image

### Changed

- **Homepage Outfit Card — WearThis-style layout** — Two-panel: outfit image (left) + product list (right)
- **Product Card (`ProductItem.tsx`)** — Redesigned:
  - Image hero at top (full width)
  - Product name below
  - Bottom row: square store logo + platform name (left) | Shop ↗ button (right)
  - Changed from circle to square logos
- **Homepage is now `async` Server Component** — Queries database for featured outfit
- **Removed ALL prices from landing page** — No individual prices, no total. Clean visual showcase only.
- **Platform logos: circle → square** — Both homepage mockup and real product cards use square logos now
- **Landing page mockup data** — Moved from hardcoded in `page.tsx` to separate `lib/landing-mockup.ts` (single source of truth for fallback content)

### Fixed

- **"Event handlers cannot be passed to Client Component props"** — Removed `onError`/`onMouseEnter` from Server Component (`page.tsx`). Added `"use client"` to `ProductItem.tsx` and `OutfitCard.tsx` which need interactivity.
- **Images not loading on landing page** — File extension mismatch (code referenced `.svg` but actual files were `.jpg`/`.png`). Fixed paths to match actual files.

### Database Migration Required

Run in Supabase SQL Editor:

```sql
ALTER TABLE outfits ADD COLUMN IF NOT EXISTS is_featured BOOLEAN DEFAULT false;
CREATE INDEX IF NOT EXISTS idx_outfits_featured ON outfits(is_featured) WHERE is_featured = true;

CREATE OR REPLACE FUNCTION ensure_single_featured_outfit()
RETURNS TRIGGER AS $$
BEGIN
  IF NEW.is_featured = true THEN
    UPDATE outfits SET is_featured = false WHERE id != NEW.id AND is_featured = true;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_single_featured_outfit ON outfits;
CREATE TRIGGER trg_single_featured_outfit
  BEFORE INSERT OR UPDATE OF is_featured ON outfits
  FOR EACH ROW
  WHEN (NEW.is_featured = true)
  EXECUTE FUNCTION ensure_single_featured_outfit();
```

**⚠️ IMPORTANT: Run this migration BEFORE deploying the new code. The app will error ("Database error querying schema") if the `is_featured` column doesn't exist.**

### New Files

| File | Purpose |
|------|---------|
| `website/lib/queries.ts` | Server-side DB queries (getFeaturedOutfit) |
| `website/lib/landing-mockup.ts` | Static fallback data for homepage |
| `website/supabase/migrations/002_featured_outfit.sql` | Migration: add is_featured column + trigger |
| `website/public/images/platforms/` | Folder for e-commerce store logo PNGs |
| `website/public/images/landing/` | Folder for static landing page images (fallback) |
| `website/public/images/landing/README.md` | Instructions for image specs |

### How to Use (Admin Flow)

1. Run the migration SQL above
2. Login as admin at `/login`
3. Create an outfit at `/dashboard/outfits/new`
4. Toggle ⭐ "Feature on Landing Page" → ON
5. Publish — homepage auto-displays this outfit
6. To change: edit outfit or create new one with ⭐ toggled on

### Known Issue

- **"Database error querying schema" on login** — This occurs if the `is_featured` column hasn't been added to the database yet. Run the migration SQL above to fix.
- **Admin user creation via SQL** — Supabase `crypt()` function can be unreliable. Recommended: sign up through the website UI at `/signup` instead.

---

## [0.5.1] - 2026-05-05 - Homepage Layout Polish + New Platforms

### Added

- **Scrolling Brand Logo Strip** — Infinite horizontal scroll of e-commerce platform logos between hero and info sections
  - "Shop from trusted platforms" label above
  - Auto-reads from `platformLogos` config — add new logo image → strip auto-updates
  - CSS `@keyframes scroll-x` animation (20s loop)
- **4 New E-Commerce Platforms** — Tata Cliq, Bewakoof, H&M, Zara
  - Added to `siteConfig.platforms` (appears in product creation dropdown)
  - Added to `platformColors` (brand colors for badges)
  - Added to `platformLogos` (logo paths — add PNGs to display)
- **"Shop from trusted platforms"** — Label text above logo strip

### Changed

- **Homepage Layout → Option C (Stacked Sections):**
  - Section 1 (Hero): 38%/62% horizontal split, content-driven height (no forced min-height)
  - Divider: Thin brand logo strip constrained to content width
  - Section 2 (Info): 45%/55% split, centered max-w-4xl
- **Removed dead space** — `min-h-[60vh]` removed from hero (was forcing empty gap below content)
- **Logo strip constrained** — Now uses same `container-content` width as hero and info sections (was full-width edge-to-edge, looked disproportionate)
- **"Build → Share → Earn"** — Now golden (`text-gold-accent`) + `font-semibold` (was grey/light)
- **Tagline quote** — Now golden, `font-display`, `font-semibold`, `text-base` (was small grey text, easily missed)
- **Outfit image** — Changed to `object-contain` with flex centering (smaller images center vertically, larger images scale down to fit)
- **Section 2 background** — Changed to `bg-background` (same as hero) for unified feel
- **All sections aligned** — Share identical horizontal boundaries via `container-content`
- **Consistent vertical padding** — Hero and Info both use `py-14/16/20`, strip uses `py-4`

### Fixed

- **Logo strip background bleeding full-width** — The `bg-surface` + `border-y` was on the `<section>` (full page width), making the strip background run edge-to-edge which looked weird. Moved background/border styling to the inner `container-content` div so it matches the bounded width of hero and info sections. Added `rounded-md` for clean edges.
- **Logo strip too faint** — Increased opacity from 40% to 80% (clearly visible now)
- **Logo strip too wide** — Wrapped in `container-content` to match other sections
- **Scattered alignment** — All content now shares same max-width container

### Platform Logos

To add logos for the new platforms, save PNGs to:
```
website/public/images/platforms/
  ├── tatacliq.png
  ├── bewakoof.png
  ├── hm.png
  └── zara.png
```

---

## [Unreleased] - Changes in Progress

> Add entries here as new features/fixes are implemented.
> Move to a versioned section when a batch of changes is deployed.

### Added

- (none yet)

### Changed

- (none yet)

### Fixed

- (none yet)

### Removed

- (none yet)

---

## Changelog Format Guide

When adding entries, use this format:
