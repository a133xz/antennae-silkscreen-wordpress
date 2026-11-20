/**
 * Projects index — sticky stack (PILE_GAP 36).
 * After 4 titles are stuck, pile shifts up; last project is excluded from driving lift.
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
  const pileStickyTop = (index, heights, pileTop) => {
    let top = pileTop;
    for (let k = 0; k < index; k += 1) top += pileStepAfter(k, heights);
    return top;
  };
  const pileMarginBottom = (index, heights) => {
    let mb = 0;
    for (let k = index; k < count - 1; k += 1) mb += pileStepAfter(k, heights);
    return mb;
  };

  let pileTop = 0;

  const stuckCount = () => {
    let n = 0;
    for (let i = 0; i < count; i += 1) {
      if (stills[i].getBoundingClientRect().top < pileTop) n += 1;
      else break;
    }
    return n;
  };

  const stackLift = (heights) => {
    if (!pileTop || count <= MAX_STACK) return 0;
    const stuck = stuckCount();
    if (stuck <= MAX_STACK) return 0;

    let lift = 0;
    const lastDriver = count - 2;
    for (let k = MAX_STACK; k < stuck && k <= lastDriver; k += 1) {
      lift += pileStepAfter(k - 1, heights);
    }
    const enterIdx = stuck;
    if (enterIdx < count - 1) {
      const step = pileStepAfter(enterIdx - 1, heights);
      const stillTop = stills[enterIdx].getBoundingClientRect().top;
      const anchor = pileTop - lift;
      if (stillTop < anchor) lift += Math.min(step, anchor - stillTop);
    }
    return lift;
  };

  grid.style.gridTemplateRows = `repeat(${count}, auto)`;
  stickies.forEach((el) => {
    el.style.position = 'sticky';
    el.style.transform = '';
  });

  let applied = '';
  const apply = () => {
    const heights = stickies.map((el) => Math.round(el.getBoundingClientRect().height));
    const firstStill = stills[0];
    if (window.scrollY === 0 && firstStill) {
      const imageTop = Math.round(firstStill.getBoundingClientRect().top);
      if (imageTop > 0) pileTop = imageTop;
    } else if (!pileTop && firstStill) {
      const imageTop = Math.round(firstStill.getBoundingClientRect().top);
      if (imageTop > 0) pileTop = imageTop;
    }
    if (!pileTop) return;

    const base = pileTop - stackLift(heights);
    const signature = `${Math.round(base)}|${heights.join(',')}`;
    if (signature === applied) return;
    applied = signature;

    titleCells.forEach((cell, index) => {
      cell.style.gridColumn = '1';
      cell.style.gridRow = `${index + 1} / -1`;
      cell.style.marginBottom = `${pileMarginBottom(index, heights)}px`;
      cell.style.zIndex = String(index + 1);
      const sticky = stickies[index];
      if (sticky) {
        sticky.style.top = `${pileStickyTop(index, heights, base)}px`;
        sticky.style.transform = '';
      }
    });
    stills.forEach((still, index) => {
      still.style.gridColumn = '2';
      still.style.gridRow = String(index + 1);
    });
  };

  let frame = 0;
  const schedule = () => {
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(apply);
  };

  apply();
  schedule();
  window.addEventListener('resize', schedule);
  window.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('load', schedule);
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(schedule);
  }
  if (typeof ResizeObserver !== 'undefined') {
    const observer = new ResizeObserver(schedule);
    stickies.forEach((el) => observer.observe(el));
  }
})();