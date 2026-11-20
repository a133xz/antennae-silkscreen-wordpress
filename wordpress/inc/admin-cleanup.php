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
 * @param array<string, mixed>|false $order Saved screen order from user meta.
 * @param list<string>               $preferred_side Box IDs in order (after Publish).
 */
function silkscreen_merge_side_meta_box_order($order, array $preferred_side) {
    if (!is_array($order)) {
        $order = array('side' => '', 'normal' => '', 'advanced' => '');
    }
    $existing = array();
    if (!empty($order['side']) && is_string($order['side'])) {
        $existing = array_filter(array_map('trim', explode(',', $order['side'])));
    }
    $tail = array_values(array_diff($existing, $preferred_side));
    $order['side'] = implode(',', array_merge($preferred_side, $tail));
    return $order;
}

/**
 * Move sidebar boxes into the high stack immediately below Publish.
 *
 * @param list<string> $box_ids_ordered
 */
function silkscreen_pin_side_meta_boxes_below_publish($post_type, array $box_ids_ordered) {
    global $wp_meta_boxes;
    if (empty($wp_meta_boxes[$post_type]['side']) || !is_array($wp_meta_boxes[$post_type]['side'])) {
        return;
    }

    $side = &$wp_meta_boxes[$post_type]['side'];
    $priorities = array('high', 'sorted', 'core', 'default', 'low');
    $collected = array();

    foreach ($box_ids_ordered as $box_id) {
        foreach ($priorities as $priority) {
            if (empty($side[$priority][$box_id])) {
                continue;
            }
            $collected[$box_id] = $side[$priority][$box_id];
            unset($side[$priority][$box_id]);
            if (isset($side[$priority]) && $side[$priority] === array()) {
                unset($side[$priority]);
            }
            break;
        }
    }
    if (!$collected) {
        return;
    }

    if (!isset($side['high']) || !is_array($side['high'])) {
        $side['high'] = array();
    }

    $high = $side['high'];
    $new_high = array();
    $inserted = false;

    foreach ($high as $id => $box) {
        $new_high[$id] = $box;
        if ($id === 'submitdiv' && !$inserted) {
            foreach ($collected as $box_id => $box_def) {
                $new_high[$box_id] = $box_def;
            }
            $inserted = true;
        }
    }

    if (!$inserted) {
        foreach ($collected as $box_id => $box_def) {
            $new_high[$box_id] = $box_def;
        }
    }

    $side['high'] = $new_high;
}

function silkscreen_project_side_meta_boxes() {
    return array(
        'submitdiv',
        'acf-group_silkscreen_2026_project_sidebar',
    );
}

function silkscreen_news_side_meta_boxes() {
    return array(
        'submitdiv',
        'acf-group_silkscreen_2026_news_sidebar',
    );
}

function silkscreen_place_meta_box_before($order, $box_id, $before_id) {
    if (!is_array($order)) {
        $order = array('side' => '', 'normal' => '', 'advanced' => '');
    }
    $normal = array();
    if (!empty($order['normal']) && is_string($order['normal'])) {
        $normal = array_values(array_filter(array_map('trim', explode(',', $order['normal']))));
    }
    $normal = array_values(array_diff($normal, array($box_id)));
    $index = array_search($before_id, $normal, true);
    if ($index === false) {
        $normal[] = $box_id;
    } else {
        array_splice($normal, $index, 0, array($box_id));
    }
    $order['normal'] = implode(',', $normal);
    return $order;
}

add_filter('get_user_option_meta-box-order_' . SILKSCREEN_PROJECT_POST_TYPE, function ($order) {
    $order = silkscreen_merge_side_meta_box_order($order, silkscreen_project_side_meta_boxes());
    return silkscreen_place_meta_box_before($order, 'silkscreen_project_posters', 'silkscreen_project_credits');
});

add_filter('get_user_option_meta-box-order_' . SILKSCREEN_NEWS_POST_TYPE, function ($order) {
    return silkscreen_merge_side_meta_box_order($order, silkscreen_news_side_meta_boxes());
});

add_action('do_meta_boxes', function ($post_type, $context = null) {
    if ($context !== 'side') {
        return;
    }
    if ($post_type === SILKSCREEN_PROJECT_POST_TYPE) {
        silkscreen_pin_side_meta_boxes_below_publish($post_type, array(
            'acf-group_silkscreen_2026_project_sidebar',
        ));
    }
    if ($post_type === SILKSCREEN_NEWS_POST_TYPE) {
        silkscreen_pin_side_meta_boxes_below_publish($post_type, array(
            'acf-group_silkscreen_2026_news_sidebar',
        ));
    }
}, 999, 2);

add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);
