(function () {
  const node = document.querySelector('[data-project-band-root]');
  const shell = node && node.closest('[data-theme]');
  if (!node || !(shell instanceof HTMLElement)) return;

  const LIGHT = 255;
  const footer = shell.querySelector(':scope > footer');
  const main = shell.querySelector('main');
  const footerEl = footer instanceof HTMLElement ? footer : null;
  const mainEl = main instanceof HTMLElement ? main : null;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let frame = 0;

  const mixGround = (amount) => {
    const t = Math.min(1, Math.max(0, amount));
    const c = Math.round(LIGHT * (1 - t));
    return amount >= 1 ? '#000' : `rgb(${c},${c},${c})`;
  };

  // iOS changes visualViewport / innerHeight as the address bar collapses. Using
  // that live height makes the fade distance shrink mid-scroll, so the ground
  // snaps to black and back. Keep the largest layout height seen instead.
  let mobileVh = 0;
  const layoutHeight = () =>
    Math.max(window.innerHeight || 0, document.documentElement.clientHeight || 0) || 1;

  const scrollProgress = (sectionTop) => {
    const mobile = window.matchMedia('(max-width: 767px)').matches;
    if (mobile) {
      const h = layoutHeight();
      if (h > mobileVh) mobileVh = h;
      const vh = mobileVh;
      const scrollY = Math.max(0, window.scrollY || document.documentElement.scrollTop || 0);
      const maxScroll = Math.max(1, document.documentElement.scrollHeight - h);
      const fadeDistance = Math.min(vh * 1.25, maxScroll);
      const linear = Math.min(1, scrollY / fadeDistance);
      return linear * linear * (3 - 2 * linear);
    }
    const vh = window.visualViewport?.height || layoutHeight();
    const fadeBegin = vh * 0.55;
    const fadeScroll = vh * 0.95;
    if (sectionTop > fadeBegin) return 0;
    return Math.min(1, Math.max(0, (fadeBegin - sectionTop) / fadeScroll));
  };

  const paint = (bg) => {
    shell.style.backgroundColor = bg;
    node.style.backgroundColor = bg;
    if (footerEl) footerEl.style.backgroundColor = bg;
    if (mainEl) mainEl.style.backgroundColor = bg;
    document.documentElement.style.setProperty('--canvas', bg);
    document.documentElement.style.backgroundColor = bg;
    document.body.style.backgroundColor = bg;
  };

  const apply = (amount) => {
    paint(mixGround(amount));
    shell.dataset.projectBand = amount >= 0.65 ? 'dark' : 'light';
  };

  const update = () => {
    frame = 0;
    const amount = scrollProgress(node.getBoundingClientRect().top);
    apply(reduce ? (amount >= 0.5 ? 1 : 0) : amount);
  };

  const onScroll = () => {
    if (frame) return;
    frame = window.requestAnimationFrame(update);
  };

  update();
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  window.visualViewport?.addEventListener('scroll', onScroll);
  window.visualViewport?.addEventListener('resize', onScroll);
})();
