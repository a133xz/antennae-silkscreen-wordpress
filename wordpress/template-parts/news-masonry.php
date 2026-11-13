<?php
if (!empty($args) && is_array($args)) {
    $items = $args['items'] ?? array();
    $title_tag = $args['title_tag'] ?? 'h2';
    $scroll_reveal = !empty($args['scroll_reveal']);
} else {
    $items = isset($items) ? $items : array();
    $title_tag = isset($title_tag) ? $title_tag : 'h2';
    $scroll_reveal = !empty($scroll_reveal);
}
?>
<ul class="ss-news-grid">
    <?php foreach ($items as $item) : ?>
        <li class="<?php echo esc_attr(silkscreen_news_masonry_class($item['size'])); ?>">
            <?php
            get_template_part('template-parts/news-card', null, array(
                'item' => $item,
                'title_tag' => $title_tag,
                'scroll_reveal' => $scroll_reveal,
            ));
            ?>
        </li>
    <?php endforeach; ?>
</ul>
