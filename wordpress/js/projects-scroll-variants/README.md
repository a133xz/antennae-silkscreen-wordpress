# Projects index — scroll layout variants

The projects archive (`template-parts/projects-scroll.php`) supports several desktop behaviours. **Production** uses `../projects-scroll.js` only. Everything in this folder is **saved for later** — copy a variant over `projects-scroll.js`, apply the CSS notes below, then hard-refresh the projects page (script uses `filemtime`).

---

## Active (production): `../projects-scroll.js`

**Sticky stack** with **PILE_GAP 36**, titles aligned to the first still, row-span cells. When more than **4** titles are in the pile, the stack **lifts smoothly** as you scroll so older names leave the top instead of freezing in the viewport.

**CSS (current `style.css`):**

```css
.ss-pile-title {
  position: sticky;
  /* no --pile-stick */
}

.ss-pile-title-cell {
  grid-column: 1;
  /* row span is set in JS: grid-row: n / -1 */
}
```

---

## Saved for later: `per-row-sticky-pile-stick.js`

**Animation you liked (2026-11-20).** Each title is a normal grid row (no row-span). Every title shares **one** sticky line via `--pile-stick`, set from the first still’s position. Titles scroll with their row, then stick together on one horizontal line — simpler motion, no stacked offsets in JS.

**When to use:** Client prefers this feel over the sticky pile.

**Switch:**

1. `cp wordpress/js/projects-scroll-variants/per-row-sticky-pile-stick.js wordpress/js/projects-scroll.js`
2. Apply this CSS block (replace the pile section rules for titles):

```css
.ss-pile-title {
  position: sticky;
  top: var(--pile-stick, 0px);
  margin-bottom: 72px;
  text-decoration: none;
  color: inherit;
}

.ss-pile-title-cell {
  grid-column: 1;
  align-self: stretch;
}

.ss-pile-title-cell--last .ss-pile-title {
  margin-bottom: 0;
}

@media (min-width: 1310px) {
  .ss-pile-title__name {
    line-height: 1;
  }
}
```

**JS notes:** No inline `top` on `[data-pile-sticky]`; each title/still pair is one grid row (`grid-row: 1..n`). Enqueued handle: `silkscreen-2026-projects-scroll` in `functions.php` (unchanged when swapping files).

---

## Saved for later: `stacked-titles-sticky-viewport.js`

**Original sticky pile before the “4-deep lift”.** Same stack look as production, but the pile can grow at a **fixed viewport Y** (can feel “stuck in the center”). No `MAX_STACK` lift.

**CSS:** Same as production sticky (`.ss-pile-title { position: sticky; }`, no `--pile-stick`, row-span via JS).

---

## Saved for later: `stacked-titles-rising.js`

**Scroll-linked stack (transform).** Visual stack with **36px** gap, but titles use `translate3d` so older names move up with their poster instead of CSS sticky offsets. Different motion — you asked to go back to sticky; kept here in case you revisit.

**CSS if you switch:**

```css
.ss-pile-title {
  position: relative;
  text-decoration: none;
  color: inherit;
}

.ss-pile-title-cell {
  grid-column: 1;
  align-self: start;
}
```

---

## Quick reference

| File | Stack style | Titles move up after 4? |
|------|-------------|-------------------------|
| `../projects-scroll.js` (active) | Sticky + JS offsets | Yes (smooth lift) |
| `per-row-sticky-pile-stick.js` | Shared `--pile-stick` | N/A (different UX) |
| `stacked-titles-sticky-viewport.js` | Sticky, no lift cap | No |
| `stacked-titles-rising.js` | Transform pile | Continuous (all depths) |