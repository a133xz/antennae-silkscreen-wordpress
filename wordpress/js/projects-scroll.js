(function () {
  const grid = document.querySelector('[data-projects-pile]');
  if (!grid) return;
  const count = Number(grid.dataset.count || '0');
  if (!count) return;

  const PILE_TOP_DEFAULT = 275;
  const PILE_STEP = 50;
  const PILE_ROW_H = 55;
  const PILE_GAP = 10;

  const titleCells = [...grid.querySelectorAll('.ss-pile-title-cell')];
  const stickies = [...grid.querySelectorAll('[data-pile-sticky]')];

  const pileStepAfter = (index, heights) => Math.max(PILE_STEP, (heights[index] || PILE_ROW_H) + PILE_GAP);
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

  const measure = () => {
    const heights = stickies.map((el) => Math.round(el.getBoundingClientRect().height));
    let pileTop = PILE_TOP_DEFAULT;
    if (window.scrollY === 0 && stickies[0]) {
      const stickyWrap = stickies[0].parentElement;
      if (stickyWrap) pileTop = Math.round(stickyWrap.getBoundingClientRect().top);
    }
    titleCells.forEach((cell, index) => {
      cell.style.gridColumn = '1';
      cell.style.gridRow = `${index + 1} / -1`;
      cell.style.marginBottom = `${pileMarginBottom(index, heights)}px`;
      cell.style.zIndex = String(index + 1);
      const sticky = stickies[index];
      if (sticky) sticky.style.top = `${pileStickyTop(index, heights, pileTop)}px`;
    });
    const stills = [...grid.querySelectorAll('[data-pile-still]')];
    stills.forEach((still, index) => {
      still.style.gridColumn = '2';
      still.style.gridRow = String(index + 1);
    });
  };

  stickies.forEach((el) => {
    el.style.position = 'sticky';
  });

  measure();
  window.addEventListener('resize', measure);
  window.addEventListener('scroll', measure, { passive: true });
})();
