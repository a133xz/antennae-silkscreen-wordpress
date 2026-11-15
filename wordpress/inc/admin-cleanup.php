<?php

function silkscreen_user_is_site_admin() {
    return current_user_can('manage_options');
}

add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', function ($menu_order) {
    if (!is_array($menu_order)) {
        return $menu_order;
    }
    $pages_key = array_search('edit.php?post_type=page', $menu_order, true);
    $media_key = array_search('upload.php', $menu_order, true);
    if ($pages_key === false || $media_key === false || $pages_key < $media_key) {
        return $menu_order;
    }
    unset($menu_order[$pages_key]);
    $menu_order = array_values($menu_order);
    $media_key = array_search('upload.php', $menu_order, true);
    if ($media_key === false) {
        return $menu_order;
    }
    array_splice($menu_order, $media_key, 0, 'edit.php?post_type=page');
    return $menu_order;
});

add_action('admin_menu', function () {
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');

    if (silkscreen_user_is_site_admin()) {
        return;
    }

    remove_menu_page('themes.php');
    remove_menu_page('plugins.php');
    remove_menu_page('users.php');
    remove_menu_page('tools.php');
    remove_menu_page('options-general.php');
}, 999);

add_action('admin_init', function () {
    if (silkscreen_user_is_site_admin()) {
        return;
    }

    global $pagenow;

    if ($pagenow === 'profile.php') {
        return;
    }

    if ($pagenow === 'user-edit.php') {
        $user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
        if ($user_id === 0 || $user_id === get_current_user_id()) {
            return;
        }
    }

    $restricted = array(
        'themes.php',
        'theme-editor.php',
        'customize.php',
        'nav-menus.php',
        'widgets.php',
        'site-editor.php',
        'plugins.php',
        'plugin-install.php',
        'plugin-editor.php',
        'users.php',
        'user-new.php',
        'user-edit.php',
        'tools.php',
        'import.php',
        'export.php',
        'site-health.php',
        'export-personal-data.php',
        'erase-personal-data.php',
        'options-general.php',
        'options-writing.php',
        'options-reading.php',
        'options-discussion.php',
        'options-media.php',
        'options-permalink.php',
        'options-privacy.php',
        'update-core.php',
    );

    if (in_array($pagenow, $restricted, true)) {
        wp_safe_redirect(admin_url());
        exit;
    }
});

add_action('admin_bar_menu', function ($admin_bar) {
    $admin_bar->remove_node('new-post');
    $admin_bar->remove_node('comments');

    if (!silkscreen_user_is_site_admin()) {
        $admin_bar->remove_node('customize');
        $admin_bar->remove_node('updates');
    }
}, 999);

add_action('init', function () {
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}, 100);

add_action('admin_head', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== SILKSCREEN_NEWS_POST_TYPE) {
        return;
    }
    echo '<style>#titlediv { display: none; }</style>';
});

/**
 * Keep the Placement meta box directly above Post attributes (menu order).
 */
add_action('do_meta_boxes', function ($post_type, $context = null) {
    if ($context !== 'side') {
        return;
    }

    $placement_boxes = array();
    if ($post_type === SILKSCREEN_PROJECT_POST_TYPE) {
        $placement_boxes[] = 'acf-group_silkscreen_2026_project_sidebar';
    }
    if ($post_type === SILKSCREEN_NEWS_POST_TYPE) {
        $placement_boxes[] = 'acf-group_silkscreen_2026_news_sidebar';
    }
    if (!$placement_boxes) {
        return;
    }

    global $wp_meta_boxes;
    if (empty($wp_meta_boxes[$post_type]['side']) || !is_array($wp_meta_boxes[$post_type]['side'])) {
        return;
    }

    $side = &$wp_meta_boxes[$post_type]['side'];
    $priorities = array('high', 'sorted', 'core', 'default', 'low');

    foreach ($placement_boxes as $box_id) {
        $box = null;
        $from_priority = null;
        foreach ($priorities as $priority) {
            if (empty($side[$priority][$box_id])) {
                continue;
            }
            $box = $side[$priority][$box_id];
            $from_priority = $priority;
            unset($side[$priority][$box_id]);
            break;
        }
        if (!$box) {
            continue;
        }

        if (!isset($side['core']) || !is_array($side['core'])) {
            $side['core'] = array();
        }

        $reordered = array();
        $inserted = false;
        foreach ($side['core'] as $id => $meta_box) {
            if ($id === 'pageparentdiv' && !$inserted) {
                $reordered[$box_id] = $box;
                $inserted = true;
            }
            $reordered[$id] = $meta_box;
        }
        if (!$inserted) {
            $reordered[$box_id] = $box;
        }
        $side['core'] = $reordered;

        if ($from_priority && $from_priority !== 'core' && isset($side[$from_priority]) && $side[$from_priority] === array()) {
            unset($side[$from_priority]);
        }
    }
}, 100, 2);

add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);
