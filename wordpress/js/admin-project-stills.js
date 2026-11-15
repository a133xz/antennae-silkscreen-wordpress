(function ($) {
  const cfg = typeof silkscreenStills === 'object' ? silkscreenStills : { max: 5 };

  function countRows($root) {
    return $root.find('[data-ss-still-row]').length;
  }

  function openPicker(onSelect) {
    const frame = wp.media({
      title: cfg.frameTitle || 'Choose a still',
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

    $row.find('[data-ss-still-id]').val(id);
    $row.find('[data-ss-still-label]').text(title);

    const $preview = $row.find('[data-ss-still-preview]');
    if ($preview.is('img')) {
      $preview.attr('src', thumb);
    } else {
      $preview.replaceWith($('<img>', { class: 'ss-still-row__thumb', 'data-ss-still-preview': '', src: thumb, alt: '' }));
    }
  }

  function bindRow($row) {
    $row.find('[data-ss-still-pick]').on('click', function (e) {
      e.preventDefault();
      openPicker(function (attachment) {
        setRowAttachment($row, attachment);
      });
    });
    $row.find('[data-ss-still-remove]').on('click', function (e) {
      e.preventDefault();
      $row.remove();
    });
  }

  function addRow($root) {
    const $rows = $root.find('[data-ss-stills-rows]');
    if (countRows($root) >= (cfg.max || 5)) {
      return;
    }
    const tpl = document.getElementById('ss-still-row-tpl');
    if (!tpl || !$rows.length) {
      return;
    }
    const node = tpl.content.cloneNode(true);
    $rows.append(node);
    bindRow($rows.children().last());
  }

  $(function () {
    const $root = $('[data-ss-stills-root]');
    if (!$root.length) {
      return;
    }

    $root.find('[data-ss-still-row]').each(function () {
      bindRow($(this));
    });

    $root.find('[data-ss-stills-add]').on('click', function (e) {
      e.preventDefault();
      addRow($root);
    });

    if (!countRows($root)) {
      addRow($root);
    }
  });
})(jQuery);