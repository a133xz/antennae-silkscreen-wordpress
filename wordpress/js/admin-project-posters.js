(function ($) {
  const cfg = typeof silkscreenPosters === 'object' ? silkscreenPosters : { max: 5 };
  const reorder = window.silkscreenAdminRowReorder || {};
  const itemSelector = '[data-ss-poster-row]';

  function countRows($root) {
    return $root.find(itemSelector).length;
  }

  function rowsList($root) {
    return $root.find('[data-ss-posters-rows]');
  }

  function refreshOrder($root) {
    if (typeof reorder.refresh === 'function') {
      reorder.refresh(rowsList($root), itemSelector);
    }
  }

  function openPicker(onSelect) {
    const frame = wp.media({
      title: cfg.frameTitle || 'Choose a poster',
      button: { text: cfg.frameButton || 'Use image' },
      multiple: false,
      library: { type: 'image' },
    });
    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();
      onSelect(attachment);
    });
    frame.open();
  }

  function setRowAttachment($row, attachment) {
    const id = attachment.id;
    const title = attachment.title || attachment.filename || 'Image';
    const thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

    $row.find('[data-ss-poster-id]').val(id);
    $row.find('[data-ss-poster-label]').text(title);

    const $preview = $row.find('[data-ss-poster-preview]');
    if ($preview.is('img')) {
      $preview.attr('src', thumb);
    } else {
      $preview.replaceWith($('<img>', { class: 'ss-poster-row__thumb', 'data-ss-poster-preview': '', src: thumb, alt: '' }));
    }
  }

  function bindRow($root, $row) {
    const $list = rowsList($root);

    $row.find('[data-ss-poster-pick]').on('click', function (e) {
      e.preventDefault();
      openPicker(function (attachment) {
        setRowAttachment($row, attachment);
      });
    });
    $row.find('[data-ss-poster-remove]').on('click', function (e) {
      e.preventDefault();
      $row.remove();
      refreshOrder($root);
    });

    if (typeof reorder.bind === 'function') {
      reorder.bind($row, $list, itemSelector);
    }
  }

  function addRow($root) {
    const $rows = rowsList($root);
    if (countRows($root) >= (cfg.max || 5)) {
      return;
    }
    const tpl = document.getElementById('ss-poster-row-tpl');
    if (!tpl || !$rows.length) {
      return;
    }
    const node = tpl.content.cloneNode(true);
    $rows.append(node);
    bindRow($root, $rows.children().last());
    refreshOrder($root);
  }

  $(function () {
    const $root = $('[data-ss-posters-root]');
    if (!$root.length) {
      return;
    }

    $root.find(itemSelector).each(function () {
      bindRow($root, $(this));
    });

    refreshOrder($root);

    $root.find('[data-ss-posters-add]').on('click', function (e) {
      e.preventDefault();
      addRow($root);
    });

    if (!countRows($root)) {
      addRow($root);
    }
  });
})(jQuery);