<?php

function silkscreen_user_is_site_admin() {
    return current_user_can('manage_options');
}

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

add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);
