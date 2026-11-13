(function () {
  const header = document.querySelector('[data-site-header]');
  if (!header) return;

  const syncHeight = () => {
    document.documentElement.style.setProperty(
      '--site-header-height',
      `${Math.round(header.getBoundingClientRect().height)}px`,
    );
  };

  syncHeight();
  if (typeof ResizeObserver !== 'undefined') {
    const ro = new ResizeObserver(syncHeight);
    ro.observe(header);
  }
  window.addEventListener('resize', syncHeight);

  if (header.hasAttribute('data-static-mobile-header') || header.classList.contains('ss-header--after-hero')) {
    return;
  }
  if (window.matchMedia('(min-width: 1300px)').matches) return;

  let lastY = window.scrollY;
  let offset = 0;
  const maxHide = 120;

  const onScroll = () => {
    const y = window.scrollY;
    const delta = y - lastY;
    if (y <= 0) {
      offset = 0;
    } else if (delta > 0) {
      offset = Math.min(maxHide, offset + delta);
    } else {
      offset = Math.max(0, offset + delta);
    }
    header.style.setProperty('--header-offset', `${offset}px`);
    lastY = y;
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();
