(function () {
  const header = document.querySelector('[data-site-header]');
  if (!header) return;

  const NAV_DESKTOP = '(min-width: 1300px)';
  const SETTLE_MS = 160;

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

  if (header.hasAttribute('data-static-mobile-header')) {
    return;
  }

  const root = document.documentElement;
  const desktop = window.matchMedia(NAV_DESKTOP);

  function pinStart() {
    const hero = document.querySelector(
      '.ss-hero[aria-roledescription="carousel"]',
    );
    return hero ? hero.offsetTop + hero.offsetHeight : 0;
  }

  let lastY = window.scrollY;
  let offset = 0;
  let resync = false;
  let settleTimer;

  function apply(value, animate) {
    offset = value;
    if (animate) {
      header.setAttribute('data-header-settling', '');
    } else {
      header.removeAttribute('data-header-settling');
    }
    header.style.setProperty('--header-offset', `${value}px`);
  }

  function settle() {
    const height = header.offsetHeight;
    if (offset <= 0 || offset >= height) {
      return;
    }
    const hiddenRoom = window.scrollY - pinStart();
    apply(offset > height / 2 && hiddenRoom >= height ? height : 0, true);
  }

  function onScroll() {
    const y = window.scrollY;

    if (root.hasAttribute('data-scroll-locked')) {
      resync = true;
      apply(0);
      return;
    }
    if (resync) {
      resync = false;
      lastY = y;
      return;
    }

    const delta = y - lastY;
    lastY = y;

    if (desktop.matches) {
      apply(0);
      return;
    }

    const height = header.offsetHeight;
    const room = Math.max(0, y - pinStart());
    apply(Math.min(Math.max(offset + delta, 0), height, room));

    window.clearTimeout(settleTimer);
    settleTimer = window.setTimeout(settle, SETTLE_MS);
  }

  function onDesktopChange() {
    if (desktop.matches) {
      apply(0);
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  desktop.addEventListener('change', onDesktopChange);
  onScroll();
})();