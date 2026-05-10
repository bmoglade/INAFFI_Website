olve this cd # StyleLink — Architecture Document (Updated)

> Last updated after Phase 1 completion.
> This reflects the ACTUAL built system, including all changes made during development.

---

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        VERCEL (Hosting)                       │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │              Next.js 14 App Router (SSR)                  │ │
│  │                                                          │ │
│  │  ┌──────────┐  ┌──────────┐  ┌───────────────────────┐ │ │
│  │  │  Public  │  │  Auth    │  │  Dashboard (Protected)│ │ │
│  │  │  Pages   │  │  Pages   │  │  Pages                │ │ │
│  │  └────┬─────┘  └────┬─────┘  └────┬──────────────────┘ │ │
│  │       │              │              │                     │ │
│  │       ▼              ▼              ▼                     │ │
│  │  ┌──────────────────────────────────────────────────┐   │ │
│  │  │         Supabase Client (lib/supabase.ts)        │   │ │
│  │  │         + Server Client (lib/supabase-server.ts) │   │ │
│  │  └──────────────────────┬───────────────────────────┘   │ │
│  └─────────────────────────┼───────────────────────────────┘ │
└────────────────────────────┼─────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    SUPABASE (Backend)                         │
│  ┌────────────┐  ┌──────────────┐  ┌────────────────────┐  │
│  │ PostgreSQL │  │  Auth        │  │  Storage            │  │
│  │ (Database) │  │  (Sessions)  │  │  (Images)           │  │
│  │            │  │              │  │                      │  │
│  │ - creators │  │ - Email/Pass │  │ - outfit-images/     │  │
│  │ - outfits  │  │ - Google     │  │ - profile-photos/    │  │
│  │ - products │  │   OAuth      │  │                      │  │
│  │ - clicks   │  │              │  │  Both PUBLIC buckets │  │
│  └────────────┘  └──────────────┘  └────────────────────┘  │
└─────────────────────────────────────────────────────────────┘

External:
┌──────────────────┐
│ Google Analytics │  ← Client-side only (gtag.js)
│ (GA4)            │  ← Only loads when NEXT_PUBLIC_GA_MEASUREMENT_ID is set
└──────────────────┘
```

---

## Route Map

| Route                          | Type          | Auth      | Description                                                   |
| ------------------------------ | ------------- | --------- | ------------------------------------------------------------- |
| `/`                            | SSR (async)   | Public    | Homepage with featured outfit from DB + scrolling brand strip |
| `/[username]`                  | SSR           | Public    | Creator storefront (MOST IMPORTANT PAGE)                      |
| `/login`                       | Client        | Public    | Email + Google login                                          |
| `/signup`                      | Client        | Public    | Signup + username setup                                       |
| `/auth/callback`               | Route Handler | Public    | OAuth callback                                                |
| `/dashboard`                   | SSR           | Protected | Stats + outfit list                                           |
| `/dashboard/outfits/new`       | Client        | Protected | Create outfit form                                            |
| `/dashboard/outfits/[id]/edit` | Client        | Protected | Edit outfit form                                              |
| `/dashboard/settings`          | Client        | Protected | Profile settings                                              |
| `/go/[productId]`              | Route Handler | Public    | Click tracking + redirect                                     |
| `/privacy`                     | SSR           | Public    | Privacy policy                                                |
| `/terms`                       | SSR           | Public    | Terms of service                                              |
| `/disclosure`                  | SSR           | Public    | Affiliate disclosure                                          |

---

## Data Flow Diagrams

### Consumer Viewing an Outfit

```
Consumer visits /priya_styles
        │
        ▼
Middleware: getSession() from cookie (no network call)
        │
        ▼
Server Component fetches from Supabase:
  1. Creator profile WHERE username = 'priya_styles'
  2. Outfits WHERE creator_id = X AND is_published = true
  3. Products WHERE outfit_id IN (...) — SEPARATE query (not nested)
  4. Combine in JavaScript
        │
        ▼
Filter: Only show products WHERE in_stock = true
Filter: Only show outfits with at least 1 in-stock product
        │
        ▼
Render page server-side → Send HTML to browser
        │
        ▼
Consumer clicks product arrow (↗)
        │
        ▼
Browser navigates to /go/[productId]
        │
        ▼
Route Handler:
  1. Fetch product.affiliate_url (using admin client)
  2. Check in_stock = true
  3. INSERT INTO clicks (product_id, outfit_id, creator_id, user_agent, referrer)
  4. Return 302 Redirect → affiliate_url
        │
        ▼
Consumer lands on Amazon/Flipkart/etc.
```

### Creator Publishing an Outfit

```
Creator logged in → /dashboard/outfits/new
        │
        ▼
Fill form: title, category, image, products (1-15)
        │
        ▼
Client-side:
  1. Validate all fields
  2. Compress image (browser-image-compression, target < 500KB)
  3. Upload image to Supabase Storage → get public URL
        │
        ▼
  4. INSERT INTO outfits (title, category, image_url, creator_id)
  5. INSERT INTO products (...) × N rows
        │
        ▼
window.location.href = "/[username]" (full reload for cookie sync)
```

---

## Database Schema

```sql
-- creators: One row per influencer
creators (
  id UUID PK,
  auth_id UUID UNIQUE → auth.users(id),
  username TEXT UNIQUE,
  display_name TEXT,
  bio TEXT,
  profile_image_url TEXT,
  instagram_handle TEXT,
  youtube_handle TEXT,
  facebook_handle TEXT,       -- Added in migration 002
  pinterest_handle TEXT,      -- Added in migration 002
  created_at TIMESTAMPTZ
)

-- outfits: Looks/collections
outfits (
  id UUID PK,
  creator_id UUID → creators(id),
  title TEXT,
  category TEXT,          -- "Office", "Casual", "Festive", "Beauty", "Home", "Other"
  image_url TEXT,
  is_published BOOLEAN,   -- false = draft, hidden from public
  is_featured BOOLEAN,    -- true = displayed on homepage (only one at a time)
  created_at TIMESTAMPTZ,
  updated_at TIMESTAMPTZ
)

-- products: Items within an outfit
products (
  id UUID PK,
  outfit_id UUID → outfits(id),
  name TEXT,
  platform TEXT,          -- "Amazon", "Flipkart", "Myntra", "Nykaa", "Ajio", "Meesho", "Other"
  affiliate_url TEXT,
  price TEXT,             -- Stored as string (e.g., "₹1,499") — currently not displayed
  image_url TEXT,         -- Optional product image (Added in migration 002)
  display_order INTEGER,
  in_stock BOOLEAN,       -- false = hidden from public storefront
  created_at TIMESTAMPTZ
)

-- clicks: Tracking
clicks (
  id UUID PK,
  product_id UUID → products(id),
  outfit_id UUID → outfits(id),
  creator_id UUID → creators(id),
  clicked_at TIMESTAMPTZ,
  user_agent TEXT,
  referrer TEXT
)
```

### Row Level Security (RLS)

- **creators**: Public read, authenticated users insert/update own
- **outfits**: Public read published, creators CRUD own
- **products**: Public read all, creators CRUD own (via outfit ownership)
- **clicks**: Anyone can INSERT (public tracking), creators read own

### Storage Policies

- **outfit-images**: Public SELECT, authenticated INSERT/UPDATE
- **profile-photos**: Public SELECT, authenticated INSERT/UPDATE

---

## Folder Structure (Actual)

```
website/
├── app/
│   ├── [username]/              ← Public storefront (SSR)
│   │   ├── page.tsx             ← Server component: fetches data
│   │   ├── StorefrontContent.tsx← Client component: category filter + ad layout
│   │   └── not-found.tsx        ← 404 for invalid usernames
│   ├── auth/callback/route.ts   ← OAuth redirect handler
│   ├── dashboard/
│   │   ├── layout.tsx           ← Sidebar wrapper
│   │   ├── page.tsx             ← Stats + outfit list (SSR)
│   │   ├── outfits/new/page.tsx ← Create form (client)
│   │   ├── outfits/[id]/edit/page.tsx ← Edit form (client)
│   │   └── settings/page.tsx    ← Profile form (client)
│   ├── go/[productId]/route.ts  ← Click redirect API
│   ├── login/page.tsx           ← Login form (client)
│   ├── signup/page.tsx          ← Signup + username (client)
│   ├── privacy/page.tsx         ← Static legal page
│   ├── terms/page.tsx           ← Static legal page
│   ├── disclosure/page.tsx      ← Static legal page
│   ├── layout.tsx               ← Root layout (fonts, GA, metadata)
│   ├── globals.css              ← Tailwind + custom CSS + scroll-x keyframes
│   └── page.tsx                 ← Homepage (hero + brand strip + info sections)
├── components/
│   ├── analytics/GoogleAnalytics.tsx
│   ├── creator/
│   │   ├── CategoryFilter.tsx   ← Filter pills (client)
│   │   └── CreatorProfileHeader.tsx
│   ├── layout/
│   │   ├── Header.tsx           ← Session-aware (shows Dashboard link if logged in)
│   │   ├── AuthHeader.tsx       ← Minimal header for login/signup pages
│   │   ├── Footer.tsx
│   │   └── DashboardSidebar.tsx ← Client: nav + storefront link + logout
│   ├── outfit/
│   │   ├── OutfitCard.tsx       ← Main card (image + products)
│   │   ├── OutfitGrid.tsx       ← List of cards
│   │   ├── ProductGrid.tsx      ← Grid within card
│   │   └── ProductItem.tsx      ← Single product row
│   └── ui/
│       ├── Badge.tsx            ← Platform + Category badges
│       ├── Button.tsx
│       ├── CopyLinkInput.tsx    ← Client: copyable URL input + button
│       ├── Input.tsx
│       ├── Select.tsx
│       ├── Skeleton.tsx
│       └── Toggle.tsx
├── lib/
│   ├── config.ts               ← ALL branding + limits + platform logos (single source of truth)
│   ├── landing-mockup.ts       ← Static fallback data for homepage outfit card
│   ├── queries.ts              ← Server-side DB queries (getFeaturedOutfit)
│   ├── supabase.ts             ← Browser client
│   ├── supabase-server.ts      ← Server client + Admin client
│   ├── types.ts                ← TypeScript interfaces
│   └── utils.ts                ← Helpers (cn, formatPrice, truncate, etc.)
├── public/
│   └── images/
│       ├── platforms/          ← E-commerce store logo PNGs (64×64px)
│       └── landing/            ← Static fallback images for homepage card
├── supabase/
│   ├── migrations/
│   │   ├── 001_initial_schema.sql
│   │   └── 002_featured_outfit.sql
│   ├── seed.sql
│   └── test-data/insert_test_data.sql
├── docs/                        ← Project documentation
├── middleware.ts                ← Auth protection + session refresh (with error handling)
├── vercel.json                 ← Vercel framework detection config
│
│   NEW in v0.4.0:
├── components/layout/LogoutButton.tsx  ← Client component for header logout
├── components/dashboard/DashboardOutfitList.tsx ← Outfit list + category filter
│
│   NEW in v0.5.0/v0.5.1:
├── lib/queries.ts               ← Server-side DB queries (getFeaturedOutfit)
├── lib/landing-mockup.ts        ← Static fallback data for homepage
├── public/images/platforms/     ← E-commerce store logo PNGs (64×64px)
├── public/images/landing/       ← Static fallback images for homepage card
├── next.config.mjs             ← Next.js config (must be .mjs, not .ts)
├── tailwind.config.ts
├── package.json
├── .env.example
└── README.md
```

---

## Key Technical Decisions

| Decision                                       | Rationale                                                                  |
| ---------------------------------------------- | -------------------------------------------------------------------------- |
| `<img>` over `next/image`                      | Corporate proxy blocks server-side image optimization                      |
| `getSession()` over `getUser()` in middleware  | `getUser()` makes network call that fails behind SSL proxy                 |
| Separate queries for outfits + products        | Nested Supabase select (`outfits.select("*, products(*)")`) fails with RLS |
| `window.location.href` for post-login redirect | `router.push()` doesn't trigger full page reload needed for cookie sync    |
| React 18.2.0 (pinned)                          | React 18.3.x incompatible with Next.js 14.2 App Router                     |
| Fonts via `<link>` tags                        | `next/font/google` fails behind corporate proxy                            |
| `NODE_TLS_REJECT_UNAUTHORIZED=0` in dev        | Required for local development behind corporate SSL inspection             |
| Homepage as async Server Component             | Queries DB for featured outfit; avoids client-side loading flicker          |
| CSS `@keyframes` for scrolling brand strip     | Pure CSS animation, no JS overhead, infinite loop                          |
| Brand strip bg inside container (not section)  | Prevents background from bleeding full page width                          |

---

## Out-of-Stock Logic

```
Creator marks product as "Out of Stock" (in_stock = false)
        │
        ▼
Public storefront:
  - Product is HIDDEN (not shown at all)
  - If ALL products in outfit are out of stock → ENTIRE outfit card hidden
  - Total price recalculates based on in-stock items only
  - Stats count only in-stock products

Dashboard:
  - Shows ALL products regardless of stock status
  - Toggle switch to mark in/out of stock
```

---

## Authentication Flow

```
Signup:
  1. Email + password → Supabase creates auth.user
  2. Auto-login (email confirmation DISABLED for dev)
  3. Prompt: "Choose your URL" → username input
  4. Validate uniqueness → INSERT into creators table
  5. window.location.href = "/dashboard"

Login:
  1. Email + password → Supabase returns session token
  2. Token stored in cookie (sb-*-auth-token)
  3. window.location.href = "/dashboard"
  4. Middleware reads cookie → allows access

Protected Routes:
  - Middleware checks getSession() on every /dashboard/* request
  - No session → redirect to /login?redirect=/dashboard
  - Has session → allow through
```

---

## Deployment

### Local Development

```bash
cd website
$env:NODE_TLS_REJECT_UNAUTHORIZED=0  # Windows (if behind corporate proxy)
pnpm dev
# → http://localhost:3000
```

### Production (Vercel) — LIVE ✅

**Live URL:** https://stylelink-phi.vercel.app/
**GitHub Repo:** https://github.com/bmoglade/StyleLink_website
**Auto-deploy:** Every push to `main` triggers Vercel build + deploy

```
Vercel Project Settings:
  - Framework Preset: Next.js
  - Root Directory: website
  - Build Command: npm run build
  - Output Directory: .next
  - Install Command: npm install
  - Node.js Version: 18.x

Environment Variables (on Vercel dashboard):
  - NEXT_PUBLIC_SUPABASE_URL
  - NEXT_PUBLIC_SUPABASE_ANON_KEY
  - SUPABASE_SERVICE_ROLE_KEY
  - NEXT_PUBLIC_GA_MEASUREMENT_ID
  - NEXT_PUBLIC_APP_URL = https://stylelink-phi.vercel.app/
  - NODE_ENV = production
```

**Deploy workflow:**

```bash
# Make changes locally
git add .
git commit -m "description of change"
git -c http.sslVerify=false push github main   # Corporate proxy workaround
# Vercel auto-deploys in ~1-2 minutes
```

**Zero code changes between dev and production.** Only environment variables differ.

### Supabase Production Auth Config

In Supabase Dashboard → Authentication → URL Configuration:

- **Site URL:** `https://stylelink-phi.vercel.app`
- **Redirect URLs:** `https://stylelink-phi.vercel.app/**`
