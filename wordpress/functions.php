<?php

require get_template_directory() . '/inc/defaults.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/cpt.php';
require get_template_directory() . '/inc/settings.php';
require get_template_directory() . '/inc/projects.php';
require get_template_directory() . '/inc/news.php';
require get_template_directory() . '/inc/shop.php';
require get_template_directory() . '/inc/fields.php';
require get_template_directory() . '/inc/admin-cleanup.php';
require get_template_directory() . '/inc/admin-project-credits.php';
require get_template_directory() . '/inc/admin-project-stills.php';
require get_template_directory() . '/inc/seed-demo.php';
require get_template_directory() . '/inc/admin-demo.php';

add_filter('acf/update_value', function ($value, $post_id, $field) {
    if (!is_array($field) || !is_string($value)) {
        return $value;
    }
    $text_names = array('director', 'synopsis', 'trailer_url', 'tickets_url', 'contact_intro', 'about_statement');
    if (in_array($field['name'] ?? '', $text_names, true)) {
        return silkscreen_sanitize_unicode_text($value);
    }
    return $value;
}, 5, 3);

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});

add_filter('use_block_editor_for_post', '__return_false');
add_filter('use_block_editor_for_post_type', function ($use, $post_type) {
  if ($post_type === 'page' || $post_type === SILKSCREEN_PROJECT_POST_TYPE || $post_type === SILKSCREEN_NEWS_POST_TYPE) {
    return false;
  }
  return $use;
}, 10, 2);

add_action('init', function () {
    remove_post_type_support('page', 'editor');
    add_rewrite_rule('^shop/([^/]+)/?$', 'index.php?silkscreen_shop_product=$matches[1]', 'top');
    add_rewrite_rule('^shop/?$', 'index.php?silkscreen_shop=1', 'top');
    if (get_option('silkscreen_2026_rewrite') !== '1') {
        flush_rewrite_rules(false);
        update_option('silkscreen_2026_rewrite', '1');
    }
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'silkscreen_shop';
    $vars[] = 'silkscreen_shop_product';
    return $vars;
});

add_filter('template_include', function ($template) {
    if (get_query_var('silkscreen_shop_product')) {
        return get_template_directory() . '/page-shop-product.php';
    }
    if (get_query_var('silkscreen_shop')) {
        return get_template_directory() . '/page-shop.php';
    }
    return $template;
});

add_filter('show_admin_bar', function ($show) {
    if (is_admin()) {
        return $show;
    }
    return false;
});

remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

add_action('after_switch_theme', function () {
    flush_rewrite_rules(false);
    update_option('silkscreen_2026_rewrite', '1');
    do_action('litespeed_purge_all');
});

add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');

    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style(
        'silkscreen-2026',
        get_stylesheet_uri(),
        array(),
        filemtime($dir . '/style.css')
    );

    wp_enqueue_script(
        'silkscreen-2026-mobile-nav',
        $uri . '/js/mobile-nav.js',
        array(),
        filemtime($dir . '/js/mobile-nav.js'),
        true
    );

    wp_enqueue_script(
        'silkscreen-2026-header',
        $uri . '/js/header-scroll.js',
        array(),
        filemtime($dir . '/js/header-scroll.js'),
        true
    );

    if (is_front_page()) {
        wp_enqueue_script(
            'silkscreen-2026-hero',
            $uri . '/js/hero.js',
            array(),
            filemtime($dir . '/js/hero.js'),
            true
        );
    }

    if (is_post_type_archive(SILKSCREEN_PROJECT_POST_TYPE) || is_page_template('page-projects.php')) {
        wp_enqueue_script(
            'silkscreen-2026-projects-scroll',
            $uri . '/js/projects-scroll.js',
            array(),
            filemtime($dir . '/js/projects-scroll.js'),
            true
        );
    }

    if (is_singular(SILKSCREEN_PROJECT_POST_TYPE)) {
        wp_enqueue_script(
            'silkscreen-2026-project-band',
            $uri . '/js/project-band.js',
            array(),
            filemtime($dir . '/js/project-band.js'),
            true
        );
        wp_enqueue_script(
            'silkscreen-2026-project-slideshow',
            $uri . '/js/project-slideshow.js',
            array(),
            filemtime($dir . '/js/project-slideshow.js'),
            true
        );
    }

    if (is_post_type_archive(SILKSCREEN_NEWS_POST_TYPE) || is_page_template('page-news.php')) {
        wp_enqueue_script(
            'silkscreen-2026-news-reveal',
            $uri . '/js/news-reveal.js',
            array(),
            filemtime($dir . '/js/news-reveal.js'),
            true
        );
    }

    if (is_page_template('page-about.php')) {
        wp_enqueue_script(
            'silkscreen-2026-about',
            $uri . '/js/about-background.js',
            array(),
            filemtime($dir . '/js/about-background.js'),
            true
        );
    }

    if (silkscreen_is_shop_route()) {
        wp_enqueue_script(
            'silkscreen-2026-shop-menu',
            $uri . '/js/shop-menu.js',
            array(),
            filemtime($dir . '/js/shop-menu.js'),
            true
        );
    }
});

add_action('wp_head', function () {
    if (!is_front_page()) {
        return;
    }
    $statement = silkscreen_about_statement();
    echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($statement)) . '">' . "\n";
}, 1);
