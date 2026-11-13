(function () {
  const frame = document.querySelector('[data-project-slideshow]');
  if (!frame) return;

  const images = [...frame.querySelectorAll('[data-still]')];
  const tabs = [...document.querySelectorAll('[data-still-tab]')];
  if (!images.length) return;

  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let index = 0;
  let timer = 0;

  const show = (next) => {
    index = (next + images.length) % images.length;
    images.forEach((img, imageIndex) => {
      const active = imageIndex === index;
      img.classList.toggle('is-active', active);
      img.alt = active ? img.dataset.alt || '' : '';
    });
    tabs.forEach((tab, tabIndex) => {
      tab.setAttribute('aria-selected', tabIndex === index ? 'true' : 'false');
    });
  };

  const arm = () => {
    window.clearInterval(timer);
    if (reduce || images.length < 2) return;
    timer = window.setInterval(() => show(index + 1), 3000);
  };

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      show(Number(tab.dataset.stillTab));
      arm();
    });
  });

  arm();
})();
