/**
 * Projects index — sticky stack (PILE_GAP 36).
 * After 4 titles are stuck, pile shifts up smoothly; last project does not drive lift.
 */
(function () {
  const grid = document.querySelector('[data-projects-pile]');
  if (!grid) return;
  const count = Number(grid.dataset.count || '0');
  if (!count) return;

  const PILE_STEP = 50;
  const PILE_ROW_H = 55;
  const PILE_GAP = 36;
  const MAX_STACK = 4;

  const titleCells = [...grid.querySelectorAll('.ss-pile-title-cell')];
  const stickies = [...grid.querySelectorAll('[data-pile-sticky]')];
  const stills = [...grid.querySelectorAll('[data-pile-still]')];

  const pileStepAfter = (index, heights) =>
    Math.max(PILE_STEP, (heights[index] || PILE_ROW_H) + PILE_GAP);
  const pileStickyTop = (index, heights, baseTop) => {
    let top = baseTop;
    for (let k = 0; k < index; k += 1) top += pileStepAfter(k, heights);
    return top;
  };
  const pileMarginBottom = (index, heights) => {
    let mb = 0;
    for (let k = index; k < count - 1; k += 1) mb += pileStepAfter(k, heights);
    return mb;
  };

  let pileTop = 0;

  /** Scroll-linked lift (subpixel); last still never contributes. */
  const stackLift = (heights) => {
    if (!pileTop || count <= MAX_STACK) return 0;
    let lift = 0;
    const lastDriver = count - 2;
    for (let i = MAX_STACK; i <= lastDriver; i += 1) {
      const step = pileStepAfter(i - 1, heights);
      const stillTop = stills[i].getBoundingClientRect().top;
      const anchor = pileTop - lift;
      if (stillTop >= anchor) break;
      lift += Math.min(step, anchor - stillTop);
    }
    return lift;
  };

  grid.style.gridTemplateRows = `repeat(${count}, auto)`;
  stickies.forEach((el) => {
    el.style.position = 'sticky';
    el.style.transform = '';
  });

  let layoutSig = '';

  const refreshPileTop = () => {
    const firstStill = stills[0];
    if (!firstStill) return;
    const imageTop = firstStill.getBoundingClientRect().top;
    if (window.scrollY === 0 && imageTop > 0) pileTop = imageTop;
    else if (!pileTop && imageTop > 0) pileTop = imageTop;
  };

  const applyLayout = (heights) => {
    const sig = heights.join(',');
    if (sig === layoutSig) return;
    layoutSig = sig;
    titleCells.forEach((cell, index) => {
      cell.style.gridColumn = '1';
      cell.style.gridRow = `${index + 1} / -1`;
      cell.style.marginBottom = `${pileMarginBottom(index, heights)}px`;
      cell.style.zIndex = String(index + 1);
    });
    stills.forEach((still, index) => {
      still.style.gridColumn = '2';
      still.style.gridRow = String(index + 1);
    });
  };

  const apply = () => {
    refreshPileTop();
    if (!pileTop) return;

    const heights = stickies.map((el) => Math.round(el.getBoundingClientRect().height));
    applyLayout(heights);

    const base = pileTop - stackLift(heights);
    stickies.forEach((sticky, index) => {
      const top = pileStickyTop(index, heights, base);
      sticky.style.top = `${top.toFixed(2)}px`;
    });
  };

  let frame = 0;
  const schedule = () => {
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(apply);
  };

  apply();
  schedule();
  window.addEventListener('resize', () => {
    layoutSig = '';
    schedule();
  });
  window.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('load', schedule);
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(schedule);
  }
  if (typeof ResizeObserver !== 'undefined') {
    const observer = new ResizeObserver(() => {
      layoutSig = '';
      schedule();
    });
    stickies.forEach((el) => observer.observe(el));
  }
})();