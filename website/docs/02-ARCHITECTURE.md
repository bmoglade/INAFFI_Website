# inaffi — Architecture

> PHP/MySQL re-implementation of the **WT `v1` branch** (Next.js/Supabase).
> The WT `v1` branch is the single source of truth for all design, layout,
> theme values, feature set, and data flows.

---

## WT Reference Branch

| Item | Value |
|---|---|
| Repo RID | `ri.stemma.main.repository.b2e8b5a3-4b79-4e84-ae4e-b75249b50c7a` |
| **Reference branch** | **`v1`** — NOT `master` |
| Fetch command | `git fetch wt v1` then `git show wt/v1:website/...` |

---

## WT v1 vs WT_2 (PHP) — Stack Mapping

| Concern | WT v1 (Next.js) | WT_2 PHP equivalent |
|---|---|---|
| Colors / tokens | `app/globals.css` `:root` | `assets/css/styles.css` `:root` |
| Homepage layout | `app/page.tsx` | `index.php` + `components/sections/` |
| Header | `components/layout/Header.tsx` | `components/header.php` |
| Footer | `components/layout/Footer.tsx` | `components/footer.php` |
| Config | `lib/config.ts` | `includes/config.php` |
| DB queries | Supabase client | PDO prepared statements |
| Auth | Supabase Auth (JWT) | PHP Sessions + bcrypt |
| Images | Supabase Storage | `uploads/` folder + GD compress |
| Routes | Next.js file-based | `.htaccess` RewriteRules |
| Auth guard | `middleware.ts` | `require_login()` / `require_admin()` |
| Landing images | `landing_images` Supabase table | `landing_images` MySQL table (pending) |

---

## Route Map

| URL | PHP File | Auth | Description |
|---|---|---|---|
| `/` | `index.php` | Public | Homepage |
| `/:username` | `storefront.php` | Public | Creator storefront |
| `/login` | `login.php` | Public | Login form |
| `/signup` | `signup.php` | Public | Signup form |
| `/logout` | `logout.php` | Auth | Destroy session |
| `/go` | `go.php` | Public | Click tracker + redirect |
| `/dashboard` | `dashboard/index.php` | Protected | Stats + outfit list |
| `/dashboard/new-outfit` | `dashboard/new-outfit.php` | Protected | Create outfit |
| `/dashboard/edit-outfit` | `dashboard/edit-outfit.php` | Protected | Edit outfit |
| `/dashboard/settings` | `dashboard/settings.php` | Protected | Profile settings |
| `/admin` | `admin/index.php` | Admin | Site settings |
| `/privacy` | `privacy.php` | Public | Privacy policy |
| `/terms` | `terms.php` | Public | Terms of Use |
| `/disclosure` | `disclosure.php` | Public | Affiliate disclosure |

---

## Homepage Sections (WT v1 `app/page.tsx`)

| # | Section | WT v1 Anchor | PHP File | Status |
|---|---|---|---|---|
| 1 | Hero — collage + headline | *(top)* | `sections/hero.php` | Built (needs `landing_images`) |
| 2 | How It Works — 3 steps + stats | `#how-it-works` | `sections/how-it-works.php` | Basic built |
| 3 | For Creators — dashboard mockup | `#creators` | `sections/creator-showcase.php` | Needs v1 update |
| 4 | For Shoppers — phone mockup | `#shoppers` | *(not built)* | ❌ Pending |
| 5 | Pricing — 3 tiers | `#pricing` | *(not built)* | ❌ Pending |
| 6 | Footer CTA — "Ready to Monetize?" | *(bottom)* | `sections/cta-strip.php` | Needs v1 update |
| – | Footer — 4-col | – | `components/footer.php` | Currently 2-col |

---

## Header (from WT v1 `Header.tsx`)

```
Sticky top-0 z-50, bg-background/80 backdrop-blur-md, border-b border-border-light, h-16

[Left nav: About us | Shop | Creators] [Center logo — absolute] [Right auth]

Left nav (hidden mobile, visible md+):
  href="/#about"    → About us
  href="/#shoppers" → Shop
  href="/#creators" → Creators

Center logo (absolute left-1/2 -translate-x-1/2):
  IF landing_images WHERE slot='site-logo' → <img h-10>
  ELSE → SVG halo (ellipse #C9A96E, strokeWidth 1.5) + brand name (Playfair, tracking-[0.15em])

Right auth:
  Logged out: "Join as creator" (border-gold-accent px-5 py-2 rounded-full) + "Log in" (text)
  Logged in:  "← Dashboard" (text) + "Log Out" (red text, client component)
```

---

## Footer (from WT v1 `Footer.tsx`)

```
bg: #080A10  border-top: border border-border  padding: py-12 lg:py-16

4-column grid (1→2→4 col breakpoints):
  Col 1 — Brand
    SVG halo + brand name (Playfair, tracking-[0.15em], text-text-primary)
    Tagline: "One link. Complete looks. Zero friction." (text-text-secondary)
    Social icons row: Instagram · Pinterest · YouTube · Twitter
  Col 2 — Platform
    How It Works · For Creators · For Shoppers · Pricing
  Col 3 — Company
    About Us · Partners · Contact
  Col 4 — Support
    Creator Guide · Affiliate Terms · Privacy Policy · Terms of Use

Bottom bar (mt-12 border-t border-border pt-6):
  Left:  © {year} inaffi. All rights reserved.
  Right: Platform logo badges (first 6, 32×32px, bg-surface-elevated rounded-md)
```

---

## Design Tokens (from WT v1 `globals.css` — exact values)

```css
--color-background:        #0A0C14;
--color-surface:           #12141E;
--color-surface-elevated:  #1A1C28;
--color-gold-accent:       #C9A96E;   /* ← USE THIS. NOT #D6B25E (that is wt/master) */
--color-gold-accent-hover: #B8944F;
--color-text-primary:      #F4EDE4;
--color-text-secondary:    #8A8680;
--color-border:            #252730;
--color-border-light:      #1E2028;
--color-wine:              #4A2E35;
--color-section-alt:       #0E1019;
--color-section-darker:    #080A10;
--color-shop-btn-bg:       #C9A96E;
--color-shop-btn-text:     #0A0C14;
--color-gold-glow:         rgba(201, 169, 110, 0.15);
--color-gold-glow-strong:  rgba(201, 169, 110, 0.30);
--btn-radius:              9999px;
```

---

## Database Schema

### Current tables (in `docs/schema.sql`)

```sql
creators  — id, username, email, password_hash, bio, profile_image,
            instagram/youtube/facebook/pinterest handle, is_admin, created_at

outfits   — id, creator_id, title, category, image,
            is_published, is_featured, created_at, updated_at

products  — id, outfit_id, name, platform, affiliate_url,
            price, image, display_order, in_stock

clicks    — id, product_id, outfit_id, creator_id,
            clicked_at, user_agent, referrer
```

### Pending: `landing_images` (WT v1 migration `003`)

```sql
CREATE TABLE landing_images (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section      VARCHAR(50)  NOT NULL,       -- 'hero', 'header', 'creators', 'shoppers', 'cta'
    slot         VARCHAR(50)  NOT NULL UNIQUE, -- 'hero-1', 'site-logo', etc.
    image_url    VARCHAR(500) DEFAULT NULL,
    alt_text     VARCHAR(255) DEFAULT NULL,
    display_order TINYINT UNSIGNED DEFAULT 0,
    metadata     JSON         DEFAULT NULL,
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

### Triggers

- `trg_single_featured_outfit` — BEFORE UPDATE: enforces only 1 `is_featured=1` at a time
- `trg_single_featured_outfit_insert` — BEFORE INSERT: same

---

## Folder Structure

```
public_html/
├── .htaccess                        ← ROOT: forwards → /website/inaffi/
└── website/
    ├── docs/
    │   ├── 00-SESSION-START-PROMPT.md
    │   ├── 01-MASTER-PROMPT.md
    │   ├── 02-ARCHITECTURE.md       ← THIS FILE
    │   ├── 03-DEVELOPER-GUIDE.md
    │   ├── CHANGELOG.md
    │   └── schema.sql
    └── inaffi/                      ← PHP app root
        ├── .htaccess                ← App routing (RewriteBase /website/inaffi/)
        ├── index.php                ← Homepage      → WT: app/page.tsx
        ├── storefront.php           ← Storefront    → WT: app/[username]/page.tsx
        ├── login.php / signup.php / logout.php
        ├── go.php                   ← Click tracker → WT: app/go/[productId]/route.ts
        ├── 404.php / privacy.php / terms.php / disclosure.php
        │
        ├── includes/                → WT: lib/
        │   ├── config.php           ← NOT in git (DB credentials)
        │   ├── config.example.php   ← Committed template
        │   ├── db.php               ← PDO singleton
        │   ├── auth.php             ← Session auth + guards
        │   ├── helpers.php          ← e(), site_url(), redirect(), flash()
        │   ├── upload.php           ← GD image compress + save
        │   ├── analytics.php        ← GA4 snippet
        │   └── landing-mockup.php   ← Static fallback data (no featured outfit)
        │
        ├── components/              → WT: components/
        │   ├── header.php           → WT: Header.tsx
        │   ├── footer.php           → WT: Footer.tsx
        │   ├── auth-header.php      → WT: AuthHeader.tsx
        │   ├── dashboard-sidebar.php
        │   ├── outfit-card.php      → WT: OutfitCard.tsx
        │   ├── product-item.php     → WT: ProductItem.tsx
        │   ├── platform-badge.php
        │   ├── category-filter.php
        │   └── sections/            → WT: app/page.tsx sections
        │       ├── hero.php         ← Section 1
        │       ├── brand-strip.php  ← Brand logo strip
        │       ├── how-it-works.php ← Section 2
        │       ├── creator-showcase.php ← Section 3
        │       ├── testimonials.php ← (custom, not in WT v1)
        │       └── cta-strip.php    ← Section 6
        │
        ├── dashboard/
        │   ├── index.php
        │   ├── new-outfit.php
        │   ├── edit-outfit.php
        │   └── settings.php
        │
        ├── admin/
        │   └── index.php
        │
        ├── ajax/
        │   ├── toggle-stock.php
        │   ├── toggle-publish.php
        │   └── toggle-featured.php
        │
        └── assets/
            ├── css/styles.css       ← Design system → WT: globals.css + tailwind
            ├── js/main.js
            ├── js/outfit-form.js
            └── images/
                ├── platforms/       ← 64×64px PNG logos
                ├── logo.png
                └── favicon.png
```

---

## Data Flows

### Consumer Views Storefront

```
GET /:username
  → .htaccess → storefront.php?username=...
  → SELECT creator WHERE username = ?  (404 if not found)
  → SELECT outfits WHERE creator_id = ? AND is_published = 1
  → SELECT products WHERE outfit_id IN (...)
  → Filter: hide OOS products; hide outfits where all products OOS
  → Render HTML

Consumer clicks "Shop ↗"
  → GET /go?p=product_id
  → SELECT affiliate_url WHERE id = ? AND in_stock = 1
  → INSERT INTO clicks (product_id, outfit_id, creator_id, user_agent, referrer)
  → 302 redirect → affiliate URL
```

### Creator Publishes Outfit

```
POST /dashboard/new-outfit
  → verify_csrf_token()
  → Validate inputs
  → save_image() → GD compress → uploads/outfits/<uuid>.jpg
  → DB transaction:
      INSERT INTO outfits (...)
      INSERT INTO products (...) × N
  → set_flash('Outfit created!', 'success')
  → redirect('/dashboard')
```

### Homepage Featured Outfit

```
GET /
  → SELECT o.*, c.username FROM outfits o JOIN creators c
    WHERE o.is_featured=1 AND o.is_published=1 LIMIT 1
  → Found: use real DB data
  → Not found: load includes/landing-mockup.php (static fallback)
  → $display_outfit + $display_products → sections/hero.php
```

---

## Out-of-Stock Logic (matches WT v1)

| Context | Behaviour |
|---|---|
| Public storefront | OOS products hidden; outfit hidden if all products OOS |
| `go.php` | Checks `in_stock=1` before redirecting; 404 if OOS |
| Dashboard | All products shown regardless; AJAX toggle per product |

---

## Authentication Flow

```
Signup:
  POST email + password + display_name + username
  → validate (unique username, format)
  → password_hash($pass, PASSWORD_BCRYPT)
  → INSERT INTO creators
  → session_regenerate_id() → $_SESSION['creator_id'] = $id
  → redirect('/dashboard')

Login:
  POST email + password
  → SELECT WHERE email = ?
  → password_verify($pass, $hash)
  → session → redirect('/dashboard')

Guards:
  require_login()  → redirect /login if no session
  require_admin()  → require_login() + check is_admin = 1
```

---

## Security Measures

| Measure | Implementation |
|---|---|
| CSRF | `generate_csrf_token()` + `verify_csrf_token()` on all POST forms |
| SQL injection | PDO prepared statements — never interpolation |
| XSS | `e()` = `htmlspecialchars()` on all output |
| Path traversal | `save_image()` generates random UUID filename |
| Direct file access | `.htaccess` blocks `includes/` and `docs/` |
| Credentials | `config.php` in `.gitignore` |
| Sessions | `httponly=true`, `samesite=Strict`, `secure` on HTTPS |
| Image validation | finfo MIME type check (not file extension) |

---

## Key Architecture Decisions

| Decision | Reason |
|---|---|
| PHP sessions (not JWT) | No library needed on shared hosting |
| INT AUTO_INCREMENT PKs | Better MySQL performance vs UUIDs |
| `dirname(__DIR__)` for paths | Works at any Hostinger deploy path |
| GD for image compression | Built into PHP — no install needed |
| Vanilla JS only | No build step on shared hosting |
| `RewriteBase /website/inaffi/` | Hostinger deploys to subdirectory |
| AJAX for stock/publish/feature toggles | No full-page reload |
| Username catch-all route last | Must come after all specific routes in `.htaccess` |

---

## Deployment

```
1. Edit code in Foundry IDE
2. git add . && git commit -m "..."
3. git push origin master                              ← Foundry (WT_2 repo)
4. git -c http.sslVerify=false push github main        ← GitHub
5. hPanel → Git → Deploy/Pull                          ← Hostinger pulls GitHub
6. Test: https://inaffi.com

NOT in git (live on Hostinger only):
  public_html/website/inaffi/includes/config.php    ← DB credentials
  public_html/website/inaffi/uploads/               ← User-uploaded images

Root .htaccess:
  RewriteEngine On
  RewriteCond %{REQUEST_URI} !^/website/inaffi/
  RewriteRule ^(.*)$ /website/inaffi/$1 [L,QSA]

App .htaccess (RewriteBase /website/inaffi/):
  ... all route rules ...
  RewriteRule ^@?([a-zA-Z0-9_]{3,30})/?$ storefront.php?username=$1 [L,QSA]
```

---

## Git Remotes

| Remote | URL | Purpose |
|---|---|---|
| `origin` | Foundry `ri.stemma...WT_2` | Primary — push here first |
| `github` | `github.com/bmoglade/INAFFI_Website` | Hostinger auto-deploy source |
| `wt` | Foundry `ri.stemma...WT` | Reference — READ ONLY — use branch `v1` |
