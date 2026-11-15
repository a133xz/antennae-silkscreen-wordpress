<?php
/**
 * Template Name: Shop
 */
if (!is_admin() && !is_user_logged_in()) {
    wp_safe_redirect(silkscreen_shop_url());
    exit;
}
wp_safe_redirect(admin_url('post.php?post=' . get_the_ID() . '&action=edit'));
exit;
