(function ($) {
  const cfg = typeof silkscreenContactEmails === 'object' ? silkscreenContactEmails : { max: 12 };
  const reorder = window.silkscreenAdminRowReorder || {};
  const itemSelector = '[data-ss-contact-row]';

  function rowsList($root) {
    return $root.find('[data-ss-contact-rows]');
  }

  function countRows($root) {
    return $root.find(itemSelector).length;
  }

  function refreshOrder($root) {
    if (typeof reorder.refresh === 'function') {
      reorder.refresh(rowsList($root), itemSelector);
    }
  }

  function bindRow($root, $row) {
    $row.find('[data-ss-contact-remove]').on('click', function (e) {
      e.preventDefault();
      $row.remove();
      refreshOrder($root);
    });
    if (typeof reorder.bind === 'function') {
      reorder.bind($row, rowsList($root), itemSelector);
    }
  }

  function addRow($root) {
    if (countRows($root) >= (cfg.max || 12)) {
      return;
    }
    const tpl = document.getElementById('ss-contact-email-tpl');
    const $rows = rowsList($root);
    if (!tpl || !$rows.length) {
      return;
    }
    $rows.append(tpl.content.cloneNode(true));
    bindRow($root, $rows.children().last());
    refreshOrder($root);
  }

  $(function () {
    const $root = $('[data-ss-contact-root]');
    if (!$root.length) {
      return;
    }

    $root.find(itemSelector).each(function () {
      bindRow($root, $(this));
    });
    refreshOrder($root);

    $root.find('[data-ss-contact-add]').on('click', function (e) {
      e.preventDefault();
      addRow($root);
    });

    if (!countRows($root)) {
      addRow($root);
    }
  });
})(jQuery);
