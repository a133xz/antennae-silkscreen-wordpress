/**
 * Same as active projects-scroll.js — pile stacks with gap; older titles move up
 * instead of freezing in the viewport center.
 */
(function () {
  const grid = document.querySelector('[data-projects-pile]');
  if (!grid) return;
  const count = Number(grid.dataset.count || '0');
  if (!count) return;

  const PILE_GAP = 36;
  const titleCells = [...grid.querySelectorAll('.ss-pile-title-cell')];
  const titles = [...grid.querySelectorAll('[data-pile-sticky]')];
  const stills = [...grid.querySelectorAll('[data-pile-still]')];
  if (!titles.length || titles.length !== stills.length) return;

  let stickLine = 0;

  const refreshStickLine = () => {
    const first = stills[0];
    if (!first) return;
    const top = Math.round(first.getBoundingClientRect().top);
    if (top > 0) stickLine = top;
  };

  grid.style.gridTemplateRows = `repeat(${count}, auto)`;
  titleCells.forEach((cell, index) => {
    cell.style.gridColumn = '1';
    cell.style.gridRow = String(index + 1);
    cell.style.marginBottom = '0';
    cell.style.zIndex = String(index + 1);
  });
  stills.forEach((still, index) => {
    still.style.gridColumn = '2';
    still.style.gridRow = String(index + 1);
  });
  titles.forEach((el) => {
    el.style.position = 'relative';
    el.style.top = '';
    el.style.transform = '';
  });

  const apply = () => {
    if (!stickLine) refreshStickLine();
    const line = stickLine;
    const heights = titles.map((el) => Math.round(el.offsetHeight));
    const tops = stills.map((el) => Math.round(el.getBoundingClientRect().top));
    const bottoms = stills.map((el) => Math.round(el.getBoundingClientRect().bottom));
    const positions = new Array(count);

    for (let i = count - 1; i >= 0; i -= 1) {
      let y = tops[i] < line ? line : tops[i];
      const ownEnd = bottoms[i] - heights[i];
      if (y > ownEnd) y = ownEnd;
      if (i < count - 1) {
        const limit = positions[i + 1] - heights[i] - PILE_GAP;
        if (y > limit) y = limit;
      }
      positions[i] = y;
    }

    titles.forEach((el, index) => {
      const delta = Math.round(positions[index] - tops[index]);
      el.style.transform = delta ? `translate3d(0, ${delta}px, 0)` : '';
    });
  };

  let frame = 0;
  const schedule = () => {
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(apply);
  };

  refreshStickLine();
  apply();
  window.addEventListener('resize', () => {
    refreshStickLine();
    schedule();
  });
  window.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('load', schedule);
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(schedule);
  }
  if (typeof ResizeObserver !== 'undefined') {
    const observer = new ResizeObserver(schedule);
    stills.forEach((el) => observer.observe(el));
    titles.forEach((el) => observer.observe(el));
  }
})();