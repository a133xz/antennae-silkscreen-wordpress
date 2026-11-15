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
    $columns['ss_director'] = 'Director';
    $columns['ss_featured'] = 'Featured';
    return $columns;
});

add_action('manage_' . SILKSCREEN_PROJECT_POST_TYPE . '_posts_custom_column', function ($column, $post_id) {
    if ($column === 'ss_director') {
        echo esc_html(function_exists('get_field') ? (string) get_field('director', $post_id) : '');
    }
    if ($column === 'ss_featured') {
        echo function_exists('get_field') && get_field('featured', $post_id) ? 'Yes' : '—';
    }
}, 10, 2);

add_filter('manage_' . SILKSCREEN_NEWS_POST_TYPE . '_posts_columns', function ($columns) {
    $columns['ss_layout'] = 'Layout';
    $columns['ss_size'] = 'Masonry size';
    $columns['ss_featured'] = 'Featured';
    return $columns;
});

add_action('manage_' . SILKSCREEN_NEWS_POST_TYPE . '_posts_custom_column', function ($column, $post_id) {
    if ($column === 'ss_layout' && function_exists('get_field')) {
        echo esc_html((string) get_field('news_layout', $post_id));
    }
    if ($column === 'ss_size' && function_exists('get_field')) {
        echo esc_html((string) get_field('masonry_size', $post_id));
    }
    if ($column === 'ss_featured') {
        echo function_exists('get_field') && get_field('featured', $post_id) ? 'Yes' : '—';
    }
}, 10, 2);
