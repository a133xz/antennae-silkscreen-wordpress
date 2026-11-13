<?php

function silkscreen_parse_contact_emails($rows) {
    $emails = array();
    if (!is_array($rows)) {
        return $emails;
    }
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $label = isset($row['label']) ? trim((string) $row['label']) : '';
        $address = isset($row['address']) ? trim((string) $row['address']) : '';
        if ($address !== '') {
            $emails[] = array('label' => $label, 'address' => $address);
        }
    }
    return $emails;
}

function silkscreen_contact_emails_from_post($post_id) {
    if (!$post_id || !function_exists('get_field')) {
        return array();
    }

    $emails = array();
    for ($slot = 1; $slot <= SILKSCREEN_CONTACT_PRIMARY_SLOTS; $slot++) {
        $address = trim(silkscreen_text('contact_email_' . $slot . '_address', $post_id, ''));
        if ($address === '') {
            continue;
        }
        $emails[] = array(
            'label' => silkscreen_text('contact_email_' . $slot . '_label', $post_id, ''),
            'address' => $address,
        );
    }

    $emails = array_merge($emails, silkscreen_parse_contact_emails(get_field('contact_emails_extra', $post_id)));

    if ($emails) {
        return $emails;
    }

    return silkscreen_parse_contact_emails(get_field('contact_emails', $post_id));
}

function silkscreen_settings() {
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $front_id = silkscreen_front_id();

    $emails = silkscreen_contact_emails_from_post($front_id);
    if (!$emails) {
        $emails = silkscreen_default_contact_emails();
    }

    $hero = array();
    $slides = $front_id ? silkscreen_gallery('hero_slides', $front_id) : array();
    if ($slides) {
        $hero = $slides;
    } else {
        foreach (array('hero-1.png', 'hero-2.png', 'hero-3.png', 'hero-4.png') as $i => $file) {
            $hero[] = array(
                'src' => silkscreen_asset('media/' . $file),
                'alt' => 'Silkscreen hero still ' . ($i + 1),
            );
        }
    }

    $settings = array(
        'tagline' => silkscreen_text('tagline', $front_id, SILKSCREEN_TAGLINE),
        'copyright' => silkscreen_text('copyright', $front_id, SILKSCREEN_COPYRIGHT),
        'social_handle' => silkscreen_text('social_handle', $front_id, SILKSCREEN_SOCIAL_HANDLE),
        'social_url' => silkscreen_url('social_url', $front_id, SILKSCREEN_SOCIAL_URL),
        'statement' => silkscreen_text('statement', $front_id, SILKSCREEN_STATEMENT),
        'contact_intro' => silkscreen_text('contact_intro', $front_id, SILKSCREEN_CONTACT_INTRO),
        'emails' => $emails,
        'shop_enabled' => silkscreen_bool('shop_enabled', $front_id, true),
        'posters_enabled' => silkscreen_bool('posters_enabled', $front_id, true),
        'hero' => $hero,
    );

    return $settings;
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
 * About headline: this page’s ACF → site settings on Home → approved default copy.
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
    return silkscreen_settings()['statement'];
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
    return silkscreen_settings()['contact_intro'];
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
