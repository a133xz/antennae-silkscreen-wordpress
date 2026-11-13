<?php
if (!empty($args) && is_array($args)) {
    $item = $args['item'] ?? array();
    $title_tag = $args['title_tag'] ?? 'h2';
    $scroll_reveal = !empty($args['scroll_reveal']);
} else {
    $item = isset($item) ? $item : array();
    $title_tag = isset($title_tag) ? $title_tag : 'h2';
    $scroll_reveal = !empty($scroll_reveal);
}
$layout = isset($item['layout']) ? $item['layout'] : 'caption_below';
$size = isset($item['size']) ? $item['size'] : 'medium';
list($w, $h) = silkscreen_news_frame_dimensions($size);
$kicker = trim(strtolower($item['kicker'] ?? '') . '  ' . ($item['date_label'] ?? ''));
$url = !empty($item['project_slug']) ? silkscreen_project_url($item['project_slug']) : '';
$card_class = 'ss-news-card group';
if ($layout === 'caption_overlay') {
    $card_class .= ' ss-news-card--overlay';
} elseif ($layout === 'compact') {
    $card_class .= ' ss-news-card--compact';
}
if ($scroll_reveal) {
    $card_class .= ' ss-news-reveal';
}
$wrap_open = $url ? '<a href="' . esc_url($url) . '" class="block">' : '';
$wrap_close = $url ? '</a>' : '';
?>
<article class="<?php echo esc_attr($card_class); ?>"<?php echo $scroll_reveal ? ' data-inview="false"' : ''; ?>>
    <?php echo $wrap_open; ?>
        <?php if (!empty($item['image']['src'])) : ?>
            <img src="<?php echo esc_url($item['image']['src']); ?>" alt="<?php echo esc_attr($item['image']['alt']); ?>" width="<?php echo (int) $w; ?>" height="<?php echo (int) $h; ?>" loading="lazy" />
        <?php endif; ?>
        <div class="ss-news-card__body">
            <p class="ss-news-card__kicker"><?php echo esc_html($kicker); ?></p>
            <<?php echo tag_escape($title_tag); ?> class="ss-news-card__title font-display"><?php echo esc_html($item['title']); ?></<?php echo tag_escape($title_tag); ?>>
            <?php if (!empty($item['excerpt'])) : ?>
                <p class="ss-news-card__excerpt"><?php echo esc_html($item['excerpt']); ?></p>
            <?php endif; ?>
        </div>
    <?php echo $wrap_close; ?>
</article>
