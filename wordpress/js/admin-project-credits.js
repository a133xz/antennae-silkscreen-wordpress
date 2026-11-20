(function ($) {
  const cfg = typeof silkscreenCredits === 'object' ? silkscreenCredits : { roles: {}, max: 24 };
  const reorder = window.silkscreenAdminRowReorder || {};
  const itemSelector = '[data-ss-credit-row]';

  function root() {
    return document.querySelector('[data-ss-credits-root]');
  }

  function rowsContainer(el) {
    return el.querySelector('[data-ss-credits-rows]');
  }

  function tpl() {
    return document.getElementById('ss-credit-row-tpl');
  }

  function refreshOrder(container) {
    if (typeof reorder.refresh === 'function') {
      reorder.refresh($(container), itemSelector);
    }
  }

  function syncCustomVisibility(row) {
    const select = row.querySelector('[data-ss-credit-role]');
    if (!select) return;
    if (select.value === 'custom') {
      row.classList.remove('ss-credit-row--custom-hidden');
    } else {
      row.classList.add('ss-credit-row--custom-hidden');
    }
  }

  function bindRow(container, row) {
    const $row = $(row);
    const select = row.querySelector('[data-ss-credit-role]');
    const remove = row.querySelector('[data-ss-credits-remove]');

    if (select) {
      select.addEventListener('change', () => syncCustomVisibility(row));
    }
    if (remove) {
      remove.addEventListener('click', (e) => {
        e.preventDefault();
        row.remove();
        refreshOrder(container);
      });
    }
    syncCustomVisibility(row);

    if (typeof reorder.bind === 'function') {
      reorder.bind($row, $(container), itemSelector);
    }
  }

  function addRow(container) {
    const template = tpl();
    if (!template || !container) return;
    const count = container.querySelectorAll(itemSelector).length;
    if (count >= (cfg.max || 24)) return;

    const fragment = template.content.cloneNode(true);
    if (!fragment.querySelector(itemSelector)) return;
    container.appendChild(fragment);
    const appended = container.lastElementChild;
    if (appended) {
      bindRow(container, appended);
      refreshOrder(container);
    }
  }

  $(function () {
    const el = root();
    if (!el) return;
    const container = rowsContainer(el);
    if (!container) return;

    container.querySelectorAll(itemSelector).forEach((row) => bindRow(container, row));
    refreshOrder(container);

    const addBtn = el.querySelector('[data-ss-credits-add]');
    if (addBtn) {
      addBtn.addEventListener('click', () => addRow(container));
    }

    if (!container.querySelector(itemSelector)) {
      addRow(container);
    }
  });
})(jQuery);