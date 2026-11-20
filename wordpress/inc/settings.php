<?php

function silkscreen_contact_emails_from_post($post_id) {
    if (!$post_id) {
        return array();
    }

    if (metadata_exists('post', $post_id, SILKSCREEN_CONTACT_EMAILS_META)) {
        $rows = silkscreen_contact_email_rows_from_meta($post_id);
        return array_values(array_filter($rows, function ($row) {
            return !empty($row['address']);
        }));
    }

    return array_values(array_filter(silkscreen_contact_legacy_email_rows($post_id), function ($row) {
        return !empty($row['address']);
    }));
}

function silkscreen_settings() {
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $front_id = silkscreen_front_id();

    $emails = silkscreen_contact_emails_from_post(silkscreen_contact_page_id());
    if (!$emails) {
        $emails = silkscreen_default_contact_emails();
    }

    $settings = array(
        'copyright' => silkscreen_text('copyright', $front_id, SILKSCREEN_COPYRIGHT),
        'social_handle' => silkscreen_text('social_handle', $front_id, SILKSCREEN_SOCIAL_HANDLE),
        'social_url' => silkscreen_url('social_url', $front_id, SILKSCREEN_SOCIAL_URL),
        'emails' => $emails,
        'hero' => silkscreen_default_hero_slides(),
    );

    return $settings;
}

function silkscreen_posters_page_id() {
    static $page_id = null;
    if ($page_id !== null) {
        return $page_id;
    }
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => array('publish', 'draft', 'private'),
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-posters.php',
        'no_found_rows' => true,
        'fields' => 'ids',
    ));
    $page_id = $pages ? (int) $pages[0] : 0;
    return $page_id;
}

function silkscreen_posters_in_menu() {
    $page_id = silkscreen_posters_page_id();
    if (!$page_id || !metadata_exists('post', $page_id, 'posters_in_menu')) {
        return true;
    }
    return silkscreen_bool('posters_in_menu', $page_id, true);
}

function silkscreen_about_page_id() {
    static $page_id = null;
    if ($page_id !== null) {
        return $page_id;
    }
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => array('publish', 'draft', 'private'),
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-about.php',
        'no_found_rows' => true,
        'fields' => 'ids',
    ));
    $page_id = $pages ? (int) $pages[0] : 0;
    return $page_id;
}

/**
 * About headline: About page ACF → approved default copy.
 */
function silkscreen_about_statement($page_id = 0) {
    if (!$page_id) {
        $page_id = is_page() ? (int) get_queried_object_id() : silkscreen_about_page_id();
    }
    if ($page_id) {
        $on_page = silkscreen_text('about_statement', $page_id, '');
        if ($on_page !== '') {
            return $on_page;
        }
    }
    return SILKSCREEN_STATEMENT;
}

function silkscreen_contact_page_id() {
    static $page_id = null;
    if ($page_id !== null) {
        return $page_id;
    }
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => array('publish', 'draft', 'private'),
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-contact.php',
        'no_found_rows' => true,
        'fields' => 'ids',
    ));
    $page_id = $pages ? (int) $pages[0] : 0;
    return $page_id;
}

function silkscreen_contact_intro($page_id = 0) {
    if (!$page_id) {
        $page_id = is_page() ? (int) get_queried_object_id() : silkscreen_contact_page_id();
    }
    if ($page_id) {
        $on_page = silkscreen_text('contact_intro', $page_id, '');
        if ($on_page !== '') {
            return $on_page;
        }
    }
    return SILKSCREEN_CONTACT_INTRO;
}

function silkscreen_contact_emails($page_id = 0) {
    if (!$page_id) {
        $page_id = is_page() ? (int) get_queried_object_id() : silkscreen_contact_page_id();
    }
    if ($page_id) {
        $on_page = silkscreen_contact_emails_from_post($page_id);
        if ($on_page) {
            return $on_page;
        }
    }
    return silkscreen_settings()['emails'];
}
