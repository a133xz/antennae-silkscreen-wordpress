<?php

add_action('init', function () {
    register_post_type(SILKSCREEN_PROJECT_POST_TYPE, array(
        'labels' => array(
            'name' => 'Projects',
            'singular_name' => 'Project',
            'add_new_item' => 'Add project',
            'edit_item' => 'Edit project',
            'new_item' => 'New project',
            'view_item' => 'View project',
            'search_items' => 'Search projects',
            'not_found' => 'No projects found',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-video-alt3',
        'has_archive' => 'projects',
        'rewrite' => array('slug' => 'projects', 'with_front' => false),
        'supports' => array('title', 'page-attributes'),
        'show_in_rest' => false,
    ));

    register_post_type(SILKSCREEN_NEWS_POST_TYPE, array(
        'labels' => array(
            'name' => 'News',
            'singular_name' => 'News item',
            'add_new_item' => 'Add news',
            'edit_item' => 'Edit news',
            'new_item' => 'New news item',
            'view_item' => 'View news',
            'search_items' => 'Search news',
            'not_found' => 'No news found',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-megaphone',
        'has_archive' => 'news',
        'rewrite' => array('slug' => 'news', 'with_front' => false),
        'supports' => array('title', 'page-attributes'),
        'show_in_rest' => false,
    ));
});

add_filter('manage_' . SILKSCREEN_PROJECT_POST_TYPE . '_posts_columns', function ($columns) {
    $ordered = array();
    foreach ($columns as $key => $label) {
        $ordered[$key] = $label;
        if ($key === 'title') {
            $ordered['menu_order'] = 'Order';
        }
    }
    $ordered['ss_director'] = 'Director';
    $ordered['ss_featured'] = 'Featured';
    return $ordered;
});

add_action('manage_' . SILKSCREEN_PROJECT_POST_TYPE . '_posts_custom_column', function ($column, $post_id) {
    if ($column === 'menu_order') {
        echo (int) get_post_field('menu_order', $post_id);
    }
    if ($column === 'ss_director') {
        echo esc_html(function_exists('get_field') ? (string) get_field('director', $post_id) : '');
    }
    if ($column === 'ss_featured') {
        echo function_exists('get_field') && get_field('featured', $post_id) ? 'Yes' : '—';
    }
}, 10, 2);

add_filter('manage_edit-' . SILKSCREEN_PROJECT_POST_TYPE . '_sortable_columns', function ($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
});

add_action('pre_get_posts', function ($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    $post_type = $query->get('post_type');
    if ($post_type !== SILKSCREEN_PROJECT_POST_TYPE && $post_type !== SILKSCREEN_NEWS_POST_TYPE) {
        return;
    }
    if ($query->get('orderby')) {
        return;
    }
    $query->set('orderby', 'menu_order title');
    $query->set('order', 'ASC');
});

/**
 * Give this item the chosen Order and renumber the rest so no two share a number.
 * Choosing 2 makes the previous 2 become 3, then 4, and so on.
 */
function silkscreen_shift_menu_order($post_type, $post_id, $old_order, $new_order) {
    unset($old_order);
    $others = get_posts(array(
        'post_type' => $post_type,
        'post_status' => array('publish', 'draft', 'pending', 'private', 'future'),
        'posts_per_page' => -1,
        'post__not_in' => array((int) $post_id),
        'orderby' => array(
            'menu_order' => 'ASC',
            'title' => 'ASC',
            'ID' => 'ASC',
        ),
        'suppress_filters' => true,
    ));

    $position = 1;
    foreach ($others as $other) {
        if ($position === (int) $new_order) {
            $position++;
        }
        $current = (int) $other->menu_order;
        if ($current !== $position) {
            global $wpdb;
            $wpdb->update(
                $wpdb->posts,
                array('menu_order' => $position),
                array('ID' => (int) $other->ID),
                array('%d'),
                array('%d')
            );
            clean_post_cache((int) $other->ID);
        }
        $position++;
    }
}

add_action('pre_post_update', function ($post_id, $data) {
    static $shifting = false;

    if ($shifting || wp_is_post_revision($post_id)) {
        return;
    }
    $post_type = $data['post_type'] ?? get_post_type($post_id);
    if ($post_type !== SILKSCREEN_PROJECT_POST_TYPE && $post_type !== SILKSCREEN_NEWS_POST_TYPE) {
        return;
    }
    if (!isset($_POST['menu_order'], $data['menu_order'])) {
        return;
    }
    if ((int) $data['menu_order'] !== (int) wp_unslash($_POST['menu_order'])) {
        return;
    }

    $new_order = (int) $data['menu_order'];
    $old_order = (int) get_post_field('menu_order', $post_id);
    if ($new_order === $old_order) {
        return;
    }

    $shifting = true;
    silkscreen_shift_menu_order($post_type, $post_id, $old_order, $new_order);
    $shifting = false;
}, 10, 2);

add_filter('manage_' . SILKSCREEN_NEWS_POST_TYPE . '_posts_columns', function ($columns) {
    $ordered = array();
    foreach ($columns as $key => $label) {
        $ordered[$key] = $label;
        if ($key === 'title') {
            $ordered['menu_order'] = 'Order';
        }
    }
    $ordered['ss_size'] = 'Size';
    $ordered['ss_featured'] = 'Featured';
    return $ordered;
});

add_filter('manage_edit-' . SILKSCREEN_NEWS_POST_TYPE . '_sortable_columns', function ($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
});

add_action('manage_' . SILKSCREEN_NEWS_POST_TYPE . '_posts_custom_column', function ($column, $post_id) {
    if ($column === 'menu_order') {
        echo (int) get_post_field('menu_order', $post_id);
    }
    if ($column === 'ss_size' && function_exists('get_field')) {
        $size = (string) get_field('masonry_size', $post_id);
        $choices = silkscreen_news_tile_size_choices();
        echo esc_html(isset($choices[$size]) ? $choices[$size] : ($size !== '' ? $size : '—'));
    }
    if ($column === 'ss_featured') {
        echo function_exists('get_field') && get_field('featured', $post_id) ? 'Yes' : '—';
    }
}, 10, 2);
