(function ($) {
  function updateOrderButtons($container, itemSelector) {
    const $rows = $container.children(itemSelector);
    $rows.each(function (index) {
      const $row = $(this);
      $row.find('[data-ss-row-up]').prop('disabled', index === 0);
      $row.find('[data-ss-row-down]').prop('disabled', index === $rows.length - 1);
    });
  }

  function moveRow($row, direction) {
    const $sibling = direction < 0 ? $row.prev() : $row.next();
    if (!$sibling.length) {
      return;
    }
    if (direction < 0) {
      $row.insertBefore($sibling);
    } else {
      $row.insertAfter($sibling);
    }
  }

  function bindOrderControls($row, $container, itemSelector) {
    $row.find('[data-ss-row-up]').on('click', function (e) {
      e.preventDefault();
      moveRow($row, -1);
      updateOrderButtons($container, itemSelector);
    });
    $row.find('[data-ss-row-down]').on('click', function (e) {
      e.preventDefault();
      moveRow($row, 1);
      updateOrderButtons($container, itemSelector);
    });
  }

  window.silkscreenAdminRowReorder = {
    bind: bindOrderControls,
    refresh: updateOrderButtons,
  };
})(jQuery);