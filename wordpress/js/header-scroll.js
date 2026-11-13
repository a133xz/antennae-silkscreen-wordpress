(function () {
  const header = document.querySelector('[data-site-header]');
  if (!header || header.hasAttribute('data-static-mobile-header')) return;
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

  const measure = () => {
    document.documentElement.style.setProperty('--site-header-height', `${header.offsetHeight}px`);
  };
  measure();
  window.addEventListener('resize', measure);
})();
