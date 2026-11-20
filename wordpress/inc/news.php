<?php

function silkscreen_news_from_post(WP_Post $post) {
    $id = $post->ID;

    $image = silkscreen_image('news_image', $id);
    if (!$image) {
        $image = array('src' => '', 'alt' => '');
    }

    $size = function_exists('get_field') ? (string) get_field('masonry_size', $id) : 'medium';
    $allowed = array_keys(silkscreen_news_tile_size_choices());
    if ($size === 'small' || in_array($size, $allowed, true)) {
        // Legacy `small` keeps its short frame; new items use the five sizes only.
    } elseif ($size === '') {
        $size = 'medium';
    } else {
        $size = 'medium';
    }

    $excerpt = get_post_field('post_excerpt', $id);
    if ($excerpt === '' && function_exists('get_field')) {
        $excerpt = (string) get_field('excerpt', $id);
    }

    return array(
        'id' => $id,
        'title' => $post->post_title,
        'kicker' => function_exists('get_field') ? (string) get_field('kicker', $id) : '',
        'date_label' => function_exists('get_field') ? (string) get_field('date_label', $id) : '',
        'image' => $image,
        'size' => $size,
        'excerpt' => $excerpt,
        'url' => function_exists('get_field') ? esc_url_raw((string) get_field('news_url', $id)) : '',
        'featured' => silkscreen_bool('featured', $id, false),
        'order' => (int) $post->menu_order,
    );
}

function silkscreen_news_query($args = array()) {
    $defaults = array(
        'post_type' => SILKSCREEN_NEWS_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    );
    $query = new WP_Query(array_merge($defaults, $args));
    $items = array();
    foreach ($query->posts as $post) {
        $items[] = silkscreen_news_from_post($post);
    }
    wp_reset_postdata();
    return $items;
}

add_filter('acf/load_value/name=news_headline', function ($value, $post_id) {
    if (get_post_type($post_id) !== SILKSCREEN_NEWS_POST_TYPE) {
        return $value;
    }
    return get_post_field('post_title', $post_id);
}, 10, 2);

function silkscreen_news_sync_headline_to_post($value, $post_id) {
    if (get_post_type($post_id) !== SILKSCREEN_NEWS_POST_TYPE) {
        return $value;
    }
    remove_filter('acf/update_value/name=news_headline', 'silkscreen_news_sync_headline_to_post', 10);
    wp_update_post(array(
        'ID' => $post_id,
        'post_title' => is_string($value) ? $value : '',
        'menu_order' => (int) get_post_field('menu_order', $post_id),
    ));
    add_filter('acf/update_value/name=news_headline', 'silkscreen_news_sync_headline_to_post', 10, 2);
    return null;
}
add_filter('acf/update_value/name=news_headline', 'silkscreen_news_sync_headline_to_post', 10, 2);

add_filter('acf/load_value/name=news_excerpt', function ($value, $post_id) {
    if (get_post_type($post_id) !== SILKSCREEN_NEWS_POST_TYPE) {
        return $value;
    }
    return get_post_field('post_excerpt', $post_id);
}, 10, 2);

function silkscreen_news_sync_excerpt_to_post($value, $post_id) {
    if (get_post_type($post_id) !== SILKSCREEN_NEWS_POST_TYPE) {
        return $value;
    }
    remove_filter('acf/update_value/name=news_excerpt', 'silkscreen_news_sync_excerpt_to_post', 10);
    wp_update_post(array(
        'ID' => $post_id,
        'post_excerpt' => is_string($value) ? $value : '',
        'menu_order' => (int) get_post_field('menu_order', $post_id),
    ));
    add_filter('acf/update_value/name=news_excerpt', 'silkscreen_news_sync_excerpt_to_post', 10, 2);
    return null;
}
add_filter('acf/update_value/name=news_excerpt', 'silkscreen_news_sync_excerpt_to_post', 10, 2);

/**
 * Home shows featured news in Order. Size sets each column span.
 */
function silkscreen_featured_news($limit = 3) {
    $featured = array_values(array_filter(silkscreen_news_query(), function ($item) {
        return !empty($item['featured']);
    }));
    return array_slice($featured, 0, $limit);
}
