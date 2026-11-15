(function () {
  const cfg = typeof silkscreenCredits === 'object' ? silkscreenCredits : { roles: {}, max: 24 };

  function root() {
    return document.querySelector('[data-ss-credits-root]');
  }

  function rowsContainer(el) {
    return el.querySelector('[data-ss-credits-rows]');
  }

  function tpl() {
    return document.getElementById('ss-credit-row-tpl');
  }

  function syncCustomVisibility(row) {
    const select = row.querySelector('[data-ss-credit-role]');
    const customWrap = row.querySelector('.ss-credit-row__custom');
    if (!select || !customWrap) return;
    if (select.value === 'custom') {
      row.classList.remove('ss-credit-row--custom-hidden');
    } else {
      row.classList.add('ss-credit-row--custom-hidden');
    }
  }

  function bindRow(row) {
    const select = row.querySelector('[data-ss-credit-role]');
    const remove = row.querySelector('[data-ss-credits-remove]');
    if (select) {
      select.addEventListener('change', () => syncCustomVisibility(row));
    }
    if (remove) {
      remove.addEventListener('click', () => {
        row.remove();
      });
    }
    syncCustomVisibility(row);
  }

  function addRow(container) {
    const template = tpl();
    if (!template || !container) return;
    const count = container.querySelectorAll('[data-ss-credit-row]').length;
    if (count >= (cfg.max || 24)) return;

    const fragment = template.content.cloneNode(true);
    const row = fragment.querySelector('[data-ss-credit-row]');
    if (!row) return;
    container.appendChild(fragment);
    const appended = container.lastElementChild;
    if (appended) bindRow(appended);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const el = root();
    if (!el) return;
    const container = rowsContainer(el);
    if (!container) return;

    container.querySelectorAll('[data-ss-credit-row]').forEach(bindRow);

    const addBtn = el.querySelector('[data-ss-credits-add]');
    if (addBtn) {
      addBtn.addEventListener('click', () => addRow(container));
    }

    if (!container.querySelector('[data-ss-credit-row]')) {
      addRow(container);
    }
  });
})();