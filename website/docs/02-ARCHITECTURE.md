**includes/config.php**

---

## WT Reference Branch

| Item | Value |
|---|---|
| Repo RID | `ri.stemma.main.repository.b2e8b5a3-4b79-4e84-ae4e-b75249b50c7a` |
| **Reference branch** | **`v1`** (not `master`) |
| Fetch command | `git fetch wt v1` then `git show wt/v1:website/...` |
| Latest commit | `3706344` |

---

## WT v1 vs WT_2 (PHP) — Full Stack Mapping

| Concern | WT v1 (Next.js reference) | WT_2 PHP equivalent |
|---|---|---|
| Colors / tokens | `app/globals.css` `:root` | `assets/css/styles.css` `:root` |
| Homepage layout | `app/page.tsx` | `index.php` + `components/sections/` |
| Header | `components/layout/Header.tsx` | `components/header.php` |
| Footer | `components/layout/Footer.tsx` | `components/footer.php` |
| Config | `lib/config.ts` | `includes/config.php` |
| DB queries | `lib/queries.ts` (Supabase) | PDO in each PHP file |
| Auth | Supabase Auth (JWT, cookies) | `includes/auth.php` (PHP sessions) |
| Images | Supabase Storage (public URLs) | `uploads/` folder + GD compress |
| Routes | Next.js file-based routing | `.htaccess` RewriteRules |
| Middleware (auth guard) | `middleware.ts` | `require_login()` / `require_admin()` |
| Landing images (admin) | `landing_images` Supabase table | `landing_images` MySQL table (pending) |
| Logo upload (admin) | `landing_images` slot `site-logo` | `landing_images` slot `site-logo` (pending) |

---

## Route Map

| URL | PHP File | WT v1 Equivalent | Auth | Description |
|---|---|---|---|---|
| `/` | `index.php` | `app/page.tsx` | Public | Homepage |
| `/:username` | `storefront.php` | `app/[username]/page.tsx` | Public | Creator storefront |
| `/login` | `login.php` | `app/login/page.tsx` | Public | Login form |
| `/signup` | `signup.php` | `app/signup/page.tsx` | Public | Signup form |
| `/logout` | `logout.php` | client-side + route | Auth | Destroy session |
| `/go` | `go.php` | `app/go/[productId]/route.ts` | Public | Click tracker + redirect |
| `/dashboard` | `dashboard/index.php` | `app/dashboard/page.tsx` | Protected | Stats + outfit list |
| `/dashboard/new-outfit` | `dashboard/new-outfit.php` | `app/dashboard/outfits/new/page.tsx` | Protected | Create outfit |
| `/dashboard/edit-outfit` | `dashboard/edit-outfit.php` | `app/dashboard/outfits/[id]/edit/page.tsx` | Protected | Edit outfit |
| `/dashboard/settings` | `dashboard/settings.php` | `app/dashboard/settings/page.tsx` | Protected | Profile settings |
| `/admin` | `admin/index.php` | *(custom)* | Admin | Site settings |
| `/privacy` | `privacy.php` | `app/privacy/page.tsx` | Public | Privacy policy |
| `/terms` | `terms.php` | `app/terms/page.tsx` | Public | Terms |
| `/disclosure` | `disclosure.php` | `app/disclosure/page.tsx` | Public | Disclosure |

---

## Homepage Sections (from WT v1 `app/page.tsx`)

| # | Section | WT v1 ID | PHP Component | Status |
|---|---|---|---|---|
| 1 | **Hero** — collage + headline | *(top)* | `sections/hero.php` | Built (needs landing_images) |
| 2 | **How It Works** — 3 steps + stats bar | `#how-it-works` | `sections/how-it-works.php` | Basic — needs stats bar |
| 3 | **For Creators** — "Turn Your Style Into Income" | `#creators` | `sections/creator-showcase.php` | Needs v1 update |
| 4 | **For Shoppers** — "Shop Complete Looks" | `#shoppers` | *(not built)* | ❌ Pending |
| 5 | **Pricing** — 3 visual tiers | `#pricing` | *(not built)* | ❌ Pending |
| 6 | **Footer CTA** — "Ready to Monetize?" | *(bottom)* | `sections/cta-strip.php` | Needs v1 update |
| 7 | **Footer** — 4-column layout | — | `components/footer.php` | Currently 2-col |

---

## Header (from WT v1 `components/layout/Header.tsx`)

```
Sticky top, bg-background/80 backdrop-blur-md, border-b border-border-light, h-16

Layout: [Left nav] | [Centre logo — absolute centered] | [Right auth]

Left nav (hidden mobile, md+):
  About us | Shop | Creators  -> /#about, /#shoppers, /#creators

Centre logo (absolute left-1/2 -translate-x-1/2):
  -> If landing_images slot "site-logo" has image: <img> h-10
  -> Else: SVG halo (ellipse, #C9A96E stroke) above brand name text

Right auth:
  NOT logged in: "Join as creator" (gold outline pill) + "Log in" (text)
  Logged in:     "<- Dashboard" (text) + "Log Out" (red text)
```

---

## Footer (from WT v1 `components/layout/Footer.tsx`)

```
bg: bg-section-darker (#080A10)
border-top: border-t border-border
padding: py-12 lg:py-16

4-column grid:
  Col 1: Brand — SVG halo + name (Playfair, tracking-[0.15em]) + tagline
                 "One link. Complete looks. Zero friction."
                 social icons: Instagram, Pinterest, YouTube, Twitter
  Col 2: Platform — How It Works, For Creators, For Shoppers, Pricing
  Col 3: Company — About Us, Partners, Contact
  Col 4: Support — Creator Guide, Affiliate Terms, Privacy Policy, Terms of Use

Bottom bar:
  Left:  © year INAFFI. All rights reserved.
  Right: Platform logo badges (first 6, 32x32px, bg-surface-elevated tiles)
```

---

## Design Tokens (from WT v1 `app/globals.css` — EXACT values)

```css
/* --- Core palette --- */
--color-background:        #0A0C14;
--color-surface:           #12141E;
--color-surface-elevated:  #1A1C28;
--color-primary-dark:      #F4EDE4;
--color-gold-accent:       #C9A96E;   /* <- PRIMARY BRAND — use this, NOT #D6B25E */
--color-gold-accent-hover: #B8944F;
--color-text-primary:      #F4EDE4;
--color-text-secondary:    #8A8680;
--color-border:            #252730;
--color-border-light:      #1E2028;
--color-wine:              #4A2E35;

/* --- Section backgrounds --- */
--color-section-alt:       #0E1019;
--color-section-darker:    #080A10;

/* --- Product / Shop button --- */
--color-product-card-bg:   #12141E;
--color-product-card-border: #252730;
--color-product-card-hover:  #C9A96E;
--color-shop-btn-bg:       #C9A96E;
--color-shop-btn-text:     #0A0C14;
--color-shop-btn-hover-bg: #B8944F;

/* --- Gold glow effects --- */
--color-gold-glow:         rgba(201, 169, 110, 0.15);
--color-gold-glow-strong:  rgba(201, 169, 110, 0.30);

/* --- Buttons --- */
--btn-radius: 9999px;   /* ALL buttons are pills */

/* --- Fonts --- */
font-display: 'Playfair Display', Georgia, serif
font-body:    'DM Sans', system-ui, sans-serif

/* --- Layout --- */
max-width content: 1100px
header height: 64px
```

> WARNING: `wt/v1` gold = `#C9A96E`. `wt/master` gold = `#D6B25E`. Always use v1.

---

## Database Schema

### Current PHP Tables

```sql
creators   — id INT, username, email, password_hash, bio,
             profile_image, social handles, is_admin TINYINT

outfits    — id INT, creator_id, title, category, image,
             is_published, is_featured, created_at, updated_at

products   — id INT, outfit_id, name, platform, affiliate_url,
             price, image, display_order, in_stock

clicks     — id INT, product_id, outfit_id, creator_id,
             clicked_at, user_agent, referrer
```

### Pending: `landing_images` Table (from WT v1 migration `003`)

```sql
landing_images — id, section, slot, image_url, alt_text,
                 display_order, metadata JSON, created_at
```

**Slots:**

| Section | Slots |
|---|---|
| Hero collage | `hero-1` through `hero-6` |
| Site logo | `site-logo` |
| Creator section | `creator-profile`, `creator-screen` |
| Shopper section | `shopper-main`, `shopper-grid-1` through `shopper-grid-6` |
| CTA section | `cta-avatar-1` through `cta-avatar-4` |

### DB Triggers

- `trg_single_featured_outfit` — BEFORE UPDATE: only 1 `is_featured=1` at a time
- `trg_single_featured_outfit_insert` — BEFORE INSERT: same

---

## Folder Structure

```
public_html/
├── .htaccess                  ← ROOT: forwards traffic → /website/inaffi/
└── website/
    ├── docs/
    │   ├── 00-SESSION-START-PROMPT.md
    │   ├── 01-MASTER-PROMPT.md
    │   ├── 02-ARCHITECTURE.md
    │   ├── 03-DEVELOPER-GUIDE.md
    │   ├── CHANGELOG.md
    │   └── schema.sql
    └── inaffi/                ← PHP app root
        ├── .htaccess          ← App routing (RewriteBase /website/inaffi/)
        ├── index.php          ← Homepage (→ WT: app/page.tsx)
        ├── storefront.php     ← (→ WT: app/[username]/page.tsx)
        ├── login.php / signup.php / logout.php / go.php
        ├── 404.php / privacy.php / terms.php / disclosure.php
        ├── includes/          ← (→ WT: lib/)
        │   ├── config.php         ← NOT in git
        │   ├── config.example.php
        │   ├── db.php / auth.php / helpers.php
        │   ├── upload.php / analytics.php / landing-mockup.php
        ├── components/        ← (→ WT: components/)
        │   ├── header.php / footer.php / auth-header.php
        │   ├── dashboard-sidebar.php / outfit-card.php
        │   ├── product-item.php / platform-badge.php / category-filter.php
        │   └── sections/
        │       ├── hero.php / brand-strip.php / how-it-works.php
        │       ├── creator-showcase.php / testimonials.php / cta-strip.php
        ├── dashboard/
        │   ├── index.php / new-outfit.php / edit-outfit.php / settings.php
        ├── admin/
        │   └── index.php
        ├── ajax/
        │   ├── toggle-stock.php / toggle-publish.php / toggle-featured.php
        └── assets/
            ├── css/styles.css
            ├── js/main.js / js/outfit-form.js
            └── images/
                ├── platforms/     ← Platform logos (64×64px PNG)
                ├── logo.png       ← Optional site logo
                └── favicon.png
```

---

## Data Flows

### Consumer Viewing a Storefront

```
GET /username
  → .htaccess → storefront.php?username=username
  → Fetch creator, 404 if not found
  → Fetch published outfits + all products (single IN query)
  → Filter: out-of-stock products hidden; all-OOS outfits hidden
  → Render HTML

Consumer clicks "Shop ↗"
  → GET /go?p=product_id
  → go.php: fetch affiliate_url WHERE in_stock=1
  → INSERT INTO clicks(...)
  → 302 redirect to affiliate URL
```

### Creator Publishing an Outfit

```
POST /dashboard/new-outfit
  → verify_csrf_token()
  → Validate fields
  → save_image() → GD compress → uploads/outfits/<random>.jpg
  → DB transaction: INSERT outfits + INSERT products × N
  → set_flash('Outfit created!') → redirect('/dashboard')
```

### Homepage Featured Outfit

```
GET /
  → SELECT * FROM outfits WHERE is_featured=1 AND is_published=1 LIMIT 1
  → Found: use DB data
  → Not found: load landing-mockup.php (static fallback)
  → Pass $display_outfit + $display_products to hero.php
```

---

## Out-of-Stock Logic (matches WT v1)

- Creator sets `in_stock=0` via AJAX toggle in dashboard
- Public storefront: product hidden; outfit hidden if ALL products OOS
- `go.php`: checks `in_stock=1` before redirecting
- Dashboard: all products shown; toggle switch per product

---

## Authentication Flow

```
Signup: POST → validate → password_hash(bcrypt) → INSERT creators
        → session_regenerate_id() → $_SESSION['creator_id'] = $id → /dashboard

Login:  POST → SELECT by email → password_verify() → session → /dashboard

Guards:
  require_login()  → redirect /login if no session
  require_admin()  → require_login() + check is_admin=1
```

---

## Security Measures

| Measure | Implementation |
|---|---|
| CSRF | `generate_csrf_token()` + `verify_csrf_token()` on all POST forms |
| SQL injection | PDO prepared statements only |
| XSS | `e()` = `htmlspecialchars()` on all output |
| Path traversal | `save_image()` generates random filename |
| Credentials | `config.php` in `.gitignore` |
| Sessions | `httponly=true`, `samesite=Strict`, `secure` on HTTPS |
| Image validation | finfo MIME check (not extension) |

---

## Key Architecture Decisions

| Decision | Reason | WT v1 Equivalent |
|---|---|---|
| PHP sessions over JWT | No library on shared hosting | Supabase Auth cookies |
| INT AUTO_INCREMENT PKs | Better MySQL perf vs UUIDs | UUID PKs in Supabase |
| `dirname(__DIR__)` for paths | Works at any deploy path | N/A (Vercel root) |
| GD for image compress | Built into PHP | browser-image-compression npm |
| Vanilla JS only | No build step | React + Tailwind |
| `RewriteBase /website/inaffi/` | Hostinger subdirectory deploy | N/A |
| `container-content` class | Matches WT utility (max-w-1100, px-24) | Tailwind `container-content` |
| AJAX toggles | No full reload for stock/publish/feature | React `useState` + Supabase |
| Username route catch-all last | Must come after all other routes | Next.js `[username]` dynamic route |

---

## Deployment

```
1. Edit code (Foundry IDE — WT_2 repo)
2. git add . && git commit -m "..."
3. git push origin master                              → Foundry
4. git -c http.sslVerify=false push github main        → GitHub
5. hPanel → Git → Deploy/Pull                          → Hostinger
6. Test at https://inaffi.com

Files NOT in git (live on Hostinger only):
  public_html/website/inaffi/includes/config.php   ← DB credentials
  public_html/website/inaffi/uploads/              ← User images

Root .htaccess:
  RewriteEngine On
  RewriteCond %{REQUEST_URI} !^/website/inaffi/
  RewriteRule ^(.*)$ /website/inaffi/$1 [L,QSA]
```

---

## Git Remotes

| Remote | URL | Purpose |
|---|---|---|
| `origin` | Foundry (`ri.stemma...WT_2`) | Primary — push here first |
| `github` | `github.com/bmoglade/INAFFI_Website` | Hostinger auto-deploy source |
| `wt` | Foundry (`ri.stemma...WT`) | Reference repo — READ ONLY |
