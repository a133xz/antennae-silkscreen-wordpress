(function () {
  const root = document.querySelector('.ss-hero');
  if (!root) return;
  const slides = root.querySelectorAll('.ss-hero__slide');
  const scrollBtn = root.querySelector('[data-hero-scroll]');
  const hold = 800;

  if (slides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    let index = 0;
    window.setInterval(() => {
      slides[index].classList.remove('is-active');
      index = (index + 1) % slides.length;
      slides[index].classList.add('is-active');
    }, hold);
  }

  const scrollToHeader = () => {
    const header = document.querySelector('[data-site-header]');
    if (!header) return;
    const target = header.getBoundingClientRect().top + window.scrollY;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      window.scrollTo(0, target);
      return;
    }
    const start = window.scrollY;
    const distance = target - start;
    const duration = 1200;
    const t0 = performance.now();
    const step = (now) => {
      const t = Math.min((now - t0) / duration, 1);
      const eased = 1 - (1 - t) ** 3;
      window.scrollTo(0, start + distance * eased);
      if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  scrollBtn?.addEventListener('click', scrollToHeader);
})();
