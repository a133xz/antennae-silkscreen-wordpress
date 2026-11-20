# Projects index — scroll layout variants

The projects archive (`template-parts/projects-scroll.php`) supports two desktop behaviors. Only one runs in production: copy the chosen `.js` file over `../projects-scroll.js` and apply the matching CSS notes below.

## Active (default): `stacked-titles.js`

Titles **stack** in the left column as you scroll: each sticky title gets a calculated `top` offset and the column cells span the full grid so names pile with **36px** gap between blocks (tuned spacing). First title aligns to the top of the first still (not a fixed 275px). Remeasures on resize, scroll, load, `document.fonts.ready`, and `ResizeObserver` on title blocks.

**CSS:** `.ss-pile-title` uses `position: sticky` only — **no** `top: var(--pile-stick)` and **no** `margin-bottom: 72px` on pile titles. See main `style.css` “Projects index pile” block.

## Saved for later: `per-row-sticky-pile-stick.js`

Each title row is a normal grid row; every title shares one sticky line via `--pile-stick` (set from the first still’s document position). Simpler motion: titles scroll with their row, then stick together on one horizontal line. Client-preferred **animation feel** as of 2026-11-20 — kept here to restore quickly.

**CSS to use with this variant:**

```css
.ss-pile-title {
  position: sticky;
  top: var(--pile-stick, 0px);
  margin-bottom: 72px;
  /* … */
}

.ss-pile-title-cell {
  grid-column: 1;
  align-self: stretch;
}

.ss-pile-title-cell--last .ss-pile-title {
  margin-bottom: 0;
}
```

**JS:** No inline `top` on `[data-pile-sticky]`; grid rows are `1..n` per title/still pair (no `grid-row: n / -1` on title cells).

## Switching

1. `cp wordpress/js/projects-scroll-variants/<variant>.js wordpress/js/projects-scroll.js`
2. Adjust the pile CSS in `wordpress/style.css` per the section above.
3. Hard-refresh the projects page (script is enqueued with `filemtime`).