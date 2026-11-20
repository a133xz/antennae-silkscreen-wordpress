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
$size = isset($item['size']) ? $item['size'] : 'medium';
list($w, $h) = silkscreen_news_frame_dimensions($size);
$kicker = trim(strtolower($item['kicker'] ?? '') . '  ' . ($item['date_label'] ?? ''));
$has_excerpt = !empty($item['excerpt']);
$link = !empty($item['url']) ? $item['url'] : '';
$link_external = $link !== '' && preg_match('#^https?://#i', $link);
$card_class = 'ss-news-card';
if ($has_excerpt) {
    $card_class .= ' ss-news-card--has-excerpt';
}
if ($scroll_reveal) {
    $card_class .= ' ss-news-reveal';
}
?>
<article class="<?php echo esc_attr($card_class); ?>"<?php echo $scroll_reveal ? ' data-inview="false"' : ''; ?>>
    <?php if ($link) : ?>
        <a class="ss-news-card__link" href="<?php echo esc_url($link); ?>"<?php echo $link_external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
    <?php endif; ?>
    <?php if (!empty($item['image']['src'])) : ?>
        <img src="<?php echo esc_url($item['image']['src']); ?>" alt="<?php echo esc_attr($item['image']['alt']); ?>" width="<?php echo (int) $w; ?>" height="<?php echo (int) $h; ?>" loading="lazy" />
    <?php endif; ?>
    <div class="ss-news-card__body">
        <?php if ($kicker !== '') : ?>
            <p class="ss-news-card__kicker"><?php echo esc_html($kicker); ?></p>
        <?php endif; ?>
        <?php if (!empty($item['title'])) : ?>
            <<?php echo tag_escape($title_tag); ?> class="ss-news-card__title font-display"><?php echo esc_html($item['title']); ?></<?php echo tag_escape($title_tag); ?>>
        <?php endif; ?>
        <?php if (!empty($item['excerpt'])) : ?>
            <p class="ss-news-card__excerpt"><?php echo esc_html($item['excerpt']); ?></p>
        <?php endif; ?>
    </div>
    <?php if ($link) : ?>
        </a>
    <?php endif; ?>
</article>
