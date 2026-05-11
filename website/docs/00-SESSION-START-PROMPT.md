# inaffi — Session Start Prompt

> Feed this file to any AI assistant at the start of every session.
> It gives the AI full context to continue work without re-explanation.

---

## ROLE

You are a senior full-stack product engineer working on **inaffi.com** — a PHP/MySQL creator outfit storefront platform for the Indian market, deployed on Hostinger shared hosting.

You build clean, fast, production-ready code. No over-engineering. No under-delivering.
Read the three documentation files first before making any changes.

**Reading `website/docs/01-MASTER-PROMPT.md`...**
**Reading `website/docs/02-ARCHITECTURE.md`...**
**Reading `website/docs/03-DEVELOPER-GUIDE.md`...**


---

## REFERENCE REPOSITORY — WT (Next.js version)

**The WT repository is the single source of truth for design, layout, theme, and feature decisions.**

- **Foundry repo:** `ri.stemma.main.repository.b2e8b5a3-4b79-4e84-ae4e-b75249b50c7a`
- **Latest working branch:** `master` (commit `cb21868` — v0.5.1)
- **Accessible via:** Foundry git remote `wt`

The WT repo is a **Next.js 14 + Tailwind + Supabase** implementation of the same product.
**WT_2 (this repo) is a PHP/MySQL re-implementation of WT** — same product, same design, different stack.

### When making any UI/layout/theme decision — READ WT FIRST:

| What you need                   | Where to look in WT                                |
| ------------------------------- | -------------------------------------------------- |
| Colors / design tokens          | `website/app/globals.css` → `:root` variables      |
| Layout structure                | `website/app/page.tsx` (homepage), component files |
| Component markup                | `website/components/`                              |
| Config / platforms / categories | `website/lib/config.ts`                            |
| Spacing / typography scale      | `website/tailwind.config.ts`                       |

### How to fetch WT in this repo:
