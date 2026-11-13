<?php

/**
 * News layout types (matches Next caption modes + compact row).
 * - caption_below: title/kicker under the image (default masonry).
 * - caption_overlay: type on top of the image.
 * - compact: narrow/small tile with tighter type (shop-window style).
 */
function silkscreen_news_layout_choices() {
    return array(
        'caption_below' => 'Caption below image',
        'caption_overlay' => 'Caption overlay on image',
        'compact' => 'Compact tile',
    );
}

function silkscreen_news_from_post(WP_Post $post) {
    $id = $post->ID;

    $image = silkscreen_image('news_image', $id);
    if (!$image && has_post_thumbnail($id)) {
        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
        if ($thumb) {
            $image = array('src' => $thumb[0], 'alt' => get_post_meta($id, '_wp_attachment_image_alt', true) ?: $post->post_title);
        }
    }
    if (!$image) {
        $image = array('src' => '', 'alt' => '');
    }

    $layout = function_exists('get_field') ? (string) get_field('news_layout', $id) : 'caption_below';
    if ($layout === '') {
        $layout = 'caption_below';
    }

    $size = function_exists('get_field') ? (string) get_field('masonry_size', $id) : 'medium';
    if ($layout === 'compact' && !in_array($size, array('narrow', 'small'), true)) {
        $size = 'small';
    }

    $project_slug = '';
    if (function_exists('get_field')) {
        $linked = get_field('linked_project', $id);
        if ($linked instanceof WP_Post) {
            $project_slug = $linked->post_name;
        } elseif (is_numeric($linked)) {
            $linked_post = get_post((int) $linked);
            if ($linked_post) {
                $project_slug = $linked_post->post_name;
            }
        }
    }

    return array(
        'id' => $id,
        'title' => $post->post_title,
        'kicker' => function_exists('get_field') ? (string) get_field('kicker', $id) : '',
        'date_label' => function_exists('get_field') ? (string) get_field('date_label', $id) : '',
        'image' => $image,
        'size' => $size,
        'layout' => $layout,
        'excerpt' => has_excerpt($id) ? get_the_excerpt($id) : (function_exists('get_field') ? (string) get_field('excerpt', $id) : ''),
        'project_slug' => $project_slug,
        'featured' => silkscreen_bool('featured', $id, false),
        'order' => (int) $post->menu_order,
    );
}

function silkscreen_news_query($args = array()) {
    $defaults = array(
        'post_type' => SILKSCREEN_NEWS_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
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

function silkscreen_featured_news($limit = 3) {
    $all = silkscreen_news_query();
    $featured = array_values(array_filter($all, function ($item) {
        return !empty($item['featured']);
    }));
    if (count($featured) < $limit) {
        foreach ($all as $item) {
            if (count($featured) >= $limit) {
                break;
            }
            if (in_array($item, $featured, true)) {
                continue;
            }
            $featured[] = $item;
        }
    }
    return array_slice($featured, 0, $limit);
}
