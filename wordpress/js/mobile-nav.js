(function () {
  const openBtn = document.querySelector('[data-mobile-nav-open]');
  let panel = document.querySelector('[data-mobile-nav-panel]');
  if (!openBtn || !panel) return;

  if (panel.parentElement !== document.body) {
    document.body.appendChild(panel);
  }

  const closeBtn = panel.querySelector('[data-mobile-nav-close]');

  const setOpen = (open) => {
    openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    panel.setAttribute('aria-hidden', open ? 'false' : 'true');
    panel.dataset.visible = open ? 'true' : 'false';
    document.documentElement.toggleAttribute('data-mobile-nav-open', open);
    if (open) {
      const y = window.scrollY;
      document.body.dataset.scrollLockY = String(y);
      document.body.style.position = 'fixed';
      document.body.style.top = `-${y}px`;
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
      document.documentElement.setAttribute('data-scroll-locked', '');
      closeBtn?.focus({ preventScroll: true });
    } else {
      const y = Number(document.body.dataset.scrollLockY || '0');
      document.body.style.position = '';
      document.body.style.top = '';
      document.body.style.left = '';
      document.body.style.right = '';
      document.body.style.width = '';
      document.documentElement.removeAttribute('data-scroll-locked');
      window.scrollTo(0, y);
    }
  };

  openBtn.addEventListener('click', () => setOpen(true));
  closeBtn?.addEventListener('click', () => setOpen(false));
  panel.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && panel.dataset.visible === 'true') setOpen(false);
  });
})();
