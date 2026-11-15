(function () {
  const PANEL_MS = 320;

  const openBtn = document.querySelector('[data-mobile-nav-open]');
  const panel = document.querySelector('[data-mobile-nav-panel]');
  if (!openBtn || !panel) return;

  if (panel.parentElement !== document.body) {
    document.body.appendChild(panel);
  }

  const closeBtn = panel.querySelector('[data-mobile-nav-close]');
  const root = document.documentElement;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const duration = reduceMotion ? 0 : PANEL_MS;

  let isOpen = false;
  let isFinishingClose = false;
  let scrollY = 0;
  let closeTimer;

  function lockScroll() {
    scrollY = window.scrollY;
    document.body.dataset.scrollLockY = String(scrollY);
    root.setAttribute('data-scroll-locked', '');

    // At the top, overflow hidden doesn't move the page. position:fixed does,
    // and that shift is what you see at the start of the open fade.
    if (scrollY <= 0) {
      root.style.overflow = 'hidden';
      document.body.style.overflow = 'hidden';
      return;
    }

    const gap = Math.max(0, window.innerWidth - document.documentElement.clientWidth);
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
    if (gap) document.body.style.paddingRight = `${gap}px`;
  }

  function unlockScroll() {
    const y = Number(document.body.dataset.scrollLockY || scrollY || 0);
    root.style.overflow = '';
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';
    document.body.style.paddingRight = '';
    delete document.body.dataset.scrollLockY;
    root.removeAttribute('data-scroll-locked');
    if (y > 0) window.scrollTo(0, y);
  }

  function setPanelVisible(visible) {
    panel.dataset.visible = visible ? 'true' : 'false';
    panel.setAttribute('aria-hidden', visible ? 'false' : 'true');
    openBtn.setAttribute('aria-expanded', visible ? 'true' : 'false');

    if (visible) {
      panel.removeAttribute('inert');
      panel.removeAttribute('data-closing');
      root.setAttribute('data-mobile-nav-open', '');
      panel.querySelectorAll('a, button').forEach((el) => {
        if (el === closeBtn) return;
        el.removeAttribute('tabindex');
      });
    } else if (isOpen) {
      panel.removeAttribute('inert');
    } else {
      panel.setAttribute('inert', '');
      panel.querySelectorAll('a').forEach((el) => el.setAttribute('tabindex', '-1'));
    }
  }

  function finishClose() {
    if (isOpen || isFinishingClose) return;
    isFinishingClose = true;
    panel.removeAttribute('data-closing');
    root.removeAttribute('data-mobile-nav-open');
    const y = Number(document.body.dataset.scrollLockY || scrollY || 0);
    root.setAttribute('data-menu-scroll-restore', '');
    unlockScroll();
    window.dispatchEvent(
      new CustomEvent('silkscreen-menu-scroll-restore', { detail: { y } }),
    );
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        root.removeAttribute('data-menu-scroll-restore');
        openBtn.focus({ preventScroll: true });
      });
    });
    panel.dataset.animating = 'false';
    isFinishingClose = false;
  }

  function onPanelTransitionEnd(event) {
    if (event.target !== panel) return;
    if (event.propertyName !== 'opacity') return;
    if (!isOpen && panel.dataset.visible === 'false') {
      window.clearTimeout(closeTimer);
      finishClose();
    }
  }

  panel.addEventListener('transitionend', onPanelTransitionEnd);

  function openMenu() {
    if (isOpen) return;
    isOpen = true;
    window.clearTimeout(closeTimer);
    panel.dataset.animating = 'true';
    lockScroll();

    const reveal = () => {
      requestAnimationFrame(() => {
        setPanelVisible(true);
      });
    };

    if (reduceMotion) {
      reveal();
      return;
    }
    requestAnimationFrame(() => requestAnimationFrame(reveal));
  }

  function closeMenu() {
    if (!isOpen) return;
    isFinishingClose = false;
    isOpen = false;
    panel.setAttribute('data-closing', '');
    setPanelVisible(false);

    window.clearTimeout(closeTimer);
    if (duration === 0) {
      finishClose();
      return;
    }
    closeTimer = window.setTimeout(finishClose, duration + 50);
  }

  openBtn.addEventListener('click', openMenu);
  closeBtn?.addEventListener('click', closeMenu);
  panel.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', closeMenu);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen) closeMenu();
  });

  setPanelVisible(false);
  panel.setAttribute('inert', '');
  panel.querySelectorAll('a').forEach((el) => el.setAttribute('tabindex', '-1'));
})();