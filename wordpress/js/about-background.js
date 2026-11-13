(function () {
  var layer = document.querySelector('[data-about-layer]');
  if (!layer) return;

  var DESKTOP_REST = 'rgb(242 169 211)';
  var TOUCH_REST = 'rgb(148 209 222)';
  var GROUNDS = [
    [242, 169, 211],
    [148, 209, 222],
    [245, 251, 198],
  ];
  var AUTO_MS = 9000;
  var root = document.documentElement;

  function coarsePointer() {
    return window.matchMedia('(hover: none) and (pointer: coarse)').matches;
  }

  function groundAt(amount) {
    var clamped = Math.min(1, Math.max(0, amount));
    var scaled = clamped * (GROUNDS.length - 1);
    var index = Math.min(GROUNDS.length - 2, Math.floor(scaled));
    var local = scaled - index;
    var from = GROUNDS[index];
    var to = GROUNDS[index + 1];
    var channels = from.map(function (channel, i) {
      return Math.round(channel + (to[i] - channel) * local);
    });
    return 'rgb(' + channels.join(' ') + ')';
  }

  var touchLike = coarsePointer();
  var rest = touchLike ? TOUCH_REST : DESKTOP_REST;
  root.setAttribute('data-about-active', '');
  root.style.setProperty('--about-ground', rest);
  layer.style.backgroundColor = rest;

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  var autoFrame = 0;
  var autoStart = 0;

  function paint(color, moving) {
    layer.style.backgroundColor = color;
    root.style.setProperty('--about-ground', color);
    if (moving) {
      layer.setAttribute('data-moving', '');
      root.setAttribute('data-about-moving', '');
    } else {
      layer.removeAttribute('data-moving');
      root.removeAttribute('data-about-moving');
    }
  }

  function startAuto() {
    autoStart = performance.now();
    var tick = function (now) {
      var t = ((now - autoStart) % AUTO_MS) / AUTO_MS;
      var amount = t < 0.5 ? t * 2 : (1 - t) * 2;
      paint(groundAt(amount), true);
      autoFrame = requestAnimationFrame(tick);
    };
    autoFrame = requestAnimationFrame(tick);
  }

  function stopAuto() {
    if (autoFrame) cancelAnimationFrame(autoFrame);
    autoFrame = 0;
  }

  function onMove(event) {
    if (reduce.matches || event.pointerType === 'touch') return;
    if (event.target instanceof Element && event.target.closest('[data-mobile-nav-panel]')) return;
    var x = event.clientX / Math.max(window.innerWidth, 1);
    var y = event.clientY / Math.max(window.innerHeight, 1);
    paint(groundAt(y * 0.72 + x * 0.28), true);
  }

  function onLeave() {
    paint(rest, false);
  }

  paint(rest, false);

  if (touchLike) {
    if (!reduce.matches) startAuto();
    else paint(TOUCH_REST, false);
  } else {
    window.addEventListener('pointermove', onMove);
    root.addEventListener('pointerleave', onLeave);
  }
})();
