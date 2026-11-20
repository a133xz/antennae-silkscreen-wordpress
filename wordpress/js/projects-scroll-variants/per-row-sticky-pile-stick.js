/**
 * Variant: per-row sticky line (--pile-stick).
 * See projects-scroll-variants/README.md
 */
(function () {
  const grid = document.querySelector('[data-projects-pile]');
  if (!grid) return;
  const count = Number(grid.dataset.count || '0');
  if (!count) return;

  const titleCells = [...grid.querySelectorAll('.ss-pile-title-cell')];
  const stills = [...grid.querySelectorAll('[data-pile-still]')];

  grid.style.gridTemplateRows = `repeat(${count}, auto)`;

  const place = () => {
    titleCells.forEach((cell, index) => {
      cell.style.gridColumn = '1';
      cell.style.gridRow = String(index + 1);
    });
    stills.forEach((still, index) => {
      still.style.gridColumn = '2';
      still.style.gridRow = String(index + 1);
    });
    const first = stills[0];
    if (!first) return;
    const line = Math.round(first.getBoundingClientRect().top + window.scrollY);
    grid.style.setProperty('--pile-stick', line + 'px');
  };

  place();
  window.addEventListener('resize', place);
  window.addEventListener('load', place);
})();