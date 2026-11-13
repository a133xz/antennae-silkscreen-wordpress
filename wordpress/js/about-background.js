(function () {
  const layer = document.querySelector('[data-about-layer]');
  if (!layer) return;
  document.documentElement.setAttribute('data-about-active', '');
  const grounds = ['#f2a9d3', '#7eb6ff', '#f5e6a8'];
  let index = 0;

  const setGround = (color) => {
    layer.style.backgroundColor = color;
    document.documentElement.style.setProperty('--about-ground', color);
  };

  setGround(grounds[0]);

  window.addEventListener(
    'pointermove',
    (event) => {
      const x = event.clientX / window.innerWidth;
      const next = x < 0.33 ? 0 : x < 0.66 ? 1 : 2;
      if (next !== index) {
        index = next;
        setGround(grounds[index]);
      }
    },
    { passive: true },
  );
})();
