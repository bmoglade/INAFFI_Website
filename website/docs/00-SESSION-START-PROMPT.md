# Session Start Prompt

> Copy everything below the line and paste it as your FIRST message in any new AI session.
> This gives the AI full context of the project so it can pick up from where you left off.

---

## ✂️ COPY FROM HERE ⬇️

---

Read the three documentation files first before making any changes.

Let me read the files in order:

**Reading `website/docs/01-MASTER-PROMPT.md`...**
**Reading `website/docs/02-ARCHITECTURE.md`...**
**Reading `website/docs/03-DEVELOPER-GUIDE.md`...**

Based on my reading of all three files, I understand:

- **Product**: StyleLink is a creator affiliate storefront — creators curate outfit collections, visitors browse and click affiliate product links, generating revenue
- **Tech stack**: Next.js 14 (App Router) + Supabase + Tailwind CSS + TypeScript
- **Database tables**: `creators`, `outfits`, `products`, `clicks`
- **Key constraints**: Corporate proxy workarounds, React pinned at 18.2, no `next/image`, no `next/font/google`, no `getUser()` in middleware, no `router.push()` post-auth
- **Folder structure**: `app/` for routes, `components/` for UI, `lib/` for utilities, `docs/` for documentation
- **Design system**: CSS variable-based colors, editorial/fashion aesthetic, Tailwind utility classes

**Current status (as of v0.5.1 — May 2026):**

- ✅ **LIVE in production** at https://stylelink-phi.vercel.app/
- ✅ End-to-end cycle working: Code → GitHub → Vercel → Live site
- ✅ Phase 1 + Phase 2 features complete
- ✅ v0.4.0 UI/UX improvements deployed (mobile responsive dashboard, Google OAuth buttons, navigation fixes)
- ✅ v0.5.0 Landing page redesign (admin-featured outfit, WearThis-style card, platform logos)
- ✅ v0.5.1 Homepage polish (scrolling brand strip, 4 new platforms, layout alignment, bg fix)
- ✅ Vercel auto-deploys on push to `main` branch on GitHub
- ✅ Supabase Auth configured for production URL
- 🔄 Currently in **user testing phase** — collecting feedback for improvements
- ⏳ **Google OAuth** — buttons are in code, needs Supabase provider config to activate
- ⏳ **Deferred** — Inline product editing from dashboard, expandable product rows

**Deploy workflow:**

```bash
git add .
git commit -m "change description"
git -c http.sslVerify=false push github main   # Triggers Vercel auto-deploy
```

**Git remotes:**

- `origin` → Foundry (primary code repo)
- `github` → GitHub (Vercel deployment source)

Now I'll apply the suggested edit:

---

## ✂️ COPY ENDS HERE ⬆️

---

## Tips for Best Results

1. **Always start a fresh session with this prompt** — paste it first, then describe what you want
2. **Be specific about what you want** — "Add a share button to outfit cards" is better than "make it shareable"
3. **One feature per session** — keeps things focused and reduces errors
4. **After the AI confirms understanding** — describe your change in plain language
5. **If the AI asks clarifying questions** — answer them, don't skip

## Example Follow-up Messages

After the AI reads the docs and asks what you want, you can say things like:

- "Add a share button on each outfit card that copies the outfit link to clipboard"
- "Make the dashboard sidebar collapsible on mobile with a hamburger menu"
- "Add a 'Sort by newest/popular' dropdown on the storefront page"
- "Change the gold accent color to teal"
- "Add a new category called 'Gym Wear'"
