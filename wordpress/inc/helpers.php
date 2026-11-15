<?php

function silkscreen_asset($file) {
    return get_template_directory_uri() . '/assets/' . ltrim($file, '/');
}

function silkscreen_front_id() {
    $id = (int) get_option('page_on_front');
    if ($id > 0) {
        return $id;
    }
    $queried = get_queried_object_id();
    return $queried ? (int) $queried : 0;
}

function silkscreen_text($name, $post_id, $fallback) {
    if ($post_id && function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }
        if (is_numeric($value)) {
            return (string) $value;
        }
    }
    return $fallback;
}

function silkscreen_url($name, $post_id, $fallback) {
    if ($post_id && function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if (is_array($value) && !empty($value['url'])) {
            return $value['url'];
        }
        if (is_numeric($value)) {
            $url = wp_get_attachment_url((int) $value);
            if ($url) {
                return $url;
            }
        }
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }
    }
    return $fallback;
}

function silkscreen_image($name, $post_id) {
    if ($post_id && function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if (is_array($value) && !empty($value['url'])) {
            return array(
                'src' => $value['url'],
                'alt' => isset($value['alt']) ? (string) $value['alt'] : '',
            );
        }
    }
    return null;
}

function silkscreen_acf_image_value($value) {
    if (is_array($value) && !empty($value['url'])) {
        return array(
            'src' => $value['url'],
            'alt' => isset($value['alt']) ? (string) $value['alt'] : '',
        );
    }
    if (is_numeric($value)) {
        $url = wp_get_attachment_url((int) $value);
        if ($url) {
            $alt = get_post_meta((int) $value, '_wp_attachment_image_alt', true);
            return array('src' => $url, 'alt' => is_string($alt) ? $alt : '');
        }
    }
    return null;
}

function silkscreen_picture_from_attachment($attachment_id) {
    $attachment_id = (int) $attachment_id;
    if ($attachment_id <= 0) {
        return null;
    }
    $url = wp_get_attachment_image_url($attachment_id, 'full');
    if (!$url) {
        return null;
    }
    $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    return array(
        'src' => $url,
        'alt' => is_string($alt) ? $alt : '',
    );
}

function silkscreen_project_still_attachment_ids($post_id) {
    if (!$post_id) {
        return array();
    }

    $json = get_post_meta($post_id, SILKSCREEN_PROJECT_STILLS_META, true);
    if (is_string($json) && $json !== '') {
        $ids = json_decode($json, true);
        if (is_array($ids)) {
            return array_values(array_filter(array_map('intval', $ids)));
        }
    }

    if (!function_exists('get_field')) {
        return array();
    }

    $ids = array();
    foreach (range(1, SILKSCREEN_PROJECT_STILL_SLOTS) as $slot) {
        $value = get_field('still_' . $slot, $post_id);
        if (is_array($value) && !empty($value['ID'])) {
            $ids[] = (int) $value['ID'];
        } elseif (is_numeric($value)) {
            $ids[] = (int) $value;
        }
    }
    return $ids;
}

/**
 * Project detail slider stills.
 */
function silkscreen_project_stills($post_id) {
    if (!$post_id) {
        return array();
    }

    $out = array();
    foreach (silkscreen_project_still_attachment_ids($post_id) as $attachment_id) {
        $picture = silkscreen_picture_from_attachment($attachment_id);
        if ($picture) {
            $out[] = $picture;
        }
    }
    if ($out) {
        return $out;
    }

    if (!function_exists('get_field')) {
        return array();
    }

    foreach (range(1, SILKSCREEN_PROJECT_STILL_SLOTS) as $slot) {
        $picture = silkscreen_image('still_' . $slot, $post_id);
        if ($picture) {
            $out[] = $picture;
        }
    }
    if ($out) {
        return $out;
    }

    $legacy = get_field('stills', $post_id);
    if (!is_array($legacy) || !$legacy) {
        return array();
    }

    if (isset($legacy['still_1'])) {
        foreach (range(1, SILKSCREEN_PROJECT_STILL_SLOTS) as $slot) {
            $picture = silkscreen_acf_image_value($legacy['still_' . $slot] ?? null);
            if ($picture) {
                $out[] = $picture;
            }
        }
        return $out;
    }

    foreach ($legacy as $row) {
        $picture = silkscreen_acf_image_value($row);
        if ($picture) {
            $out[] = $picture;
        }
    }
    return $out;
}

function silkscreen_sanitize_unicode_text($text) {
    $text = wp_unslash((string) $text);
    $text = wp_check_invalid_utf8($text, true);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
    return trim($text);
}

function silkscreen_parse_credit_names($raw) {
    $raw = wp_check_invalid_utf8(wp_unslash((string) $raw), true);
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $raw))));
}

function silkscreen_normalize_credit_rows($rows) {
    if (!is_array($rows)) {
        return array();
    }
    $credits = array();
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        if (isset($row['names']) && is_array($row['names'])) {
            $names = array_values(array_filter(array_map('trim', $row['names'])));
        } else {
            $names = silkscreen_parse_credit_names($row['names'] ?? '');
        }
        if (!$names) {
            continue;
        }
        $role = isset($row['role']) ? (string) $row['role'] : 'custom';
        if ($role === '') {
            $role = 'custom';
        }
        $credits[] = array(
            'role' => $role,
            'custom_label' => isset($row['custom_label']) ? trim((string) $row['custom_label']) : '',
            'names' => $names,
        );
    }
    return $credits;
}

function silkscreen_project_credits($post_id) {
    if (!$post_id) {
        return array();
    }

    $json = get_post_meta($post_id, SILKSCREEN_PROJECT_CREDITS_META, true);
    if (is_string($json) && $json !== '') {
        $rows = json_decode($json, true);
        if (is_array($rows)) {
            $normalized = silkscreen_normalize_credit_rows($rows);
            if ($normalized) {
                return $normalized;
            }
        }
    }

    if (function_exists('get_field')) {
        $legacy = array();
        for ($slot = 1; $slot <= 8; $slot++) {
            $names_raw = (string) get_field('credit_' . $slot . '_names', $post_id);
            $names = silkscreen_parse_credit_names($names_raw);
            if (!$names) {
                continue;
            }
            $role = (string) get_field('credit_' . $slot . '_role', $post_id);
            if ($role === '') {
                $role = 'custom';
            }
            $legacy[] = array(
                'role' => $role,
                'custom_label' => (string) get_field('credit_' . $slot . '_custom_label', $post_id),
                'names' => $names,
            );
        }
        if (!empty($legacy)) {
            return $legacy;
        }

        $rows = get_field('credits', $post_id);
        $from_repeater = silkscreen_normalize_credit_rows(is_array($rows) ? $rows : array());
        if ($from_repeater) {
            return $from_repeater;
        }
    }

    return array();
}

function silkscreen_gallery($name, $post_id) {
    if (!$post_id || !function_exists('get_field')) {
        return array();
    }
    $value = get_field($name, $post_id);
    if (!is_array($value)) {
        return array();
    }
    $out = array();
    foreach ($value as $row) {
        if (is_array($row) && !empty($row['url'])) {
            $out[] = array(
                'src' => $row['url'],
                'alt' => isset($row['alt']) ? (string) $row['alt'] : '',
            );
        } elseif (is_numeric($row)) {
            $url = wp_get_attachment_url((int) $row);
            if ($url) {
                $out[] = array('src' => $url, 'alt' => '');
            }
        }
    }
    return $out;
}

function silkscreen_bool($name, $post_id, $fallback = false) {
    if ($post_id && function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if ($value !== null && $value !== '') {
            return (bool) $value;
        }
    }
    return (bool) $fallback;
}

function silkscreen_is_shop_route() {
    return (bool) get_query_var('silkscreen_shop') || (bool) get_query_var('silkscreen_shop_product');
}

function silkscreen_contact_url() {
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-contact.php',
        'no_found_rows' => true,
    ));
    if ($pages) {
        return get_permalink($pages[0]);
    }
    $by_path = get_page_by_path('contact');
    if ($by_path) {
        return get_permalink($by_path);
    }
    return home_url('/contact/');
}

function silkscreen_project_url($slug) {
    return trailingslashit(home_url('/projects/' . $slug));
}

function silkscreen_news_masonry_class($size) {
    $map = array(
        'narrow' => 'col-3',
        'small' => 'col-3',
        'medium' => 'col-4',
        'wide' => 'col-5',
        'half' => 'col-6',
        'half-short' => 'col-6',
    );
    return isset($map[$size]) ? $map[$size] : 'col-4';
}

function silkscreen_news_frame_dimensions($size) {
    $frames = array(
        'narrow' => array(450, 536),
        'medium' => array(611, 728),
        'wide' => array(772, 920),
        'half' => array(928, 1106),
        'half-short' => array(928, 655),
        'small' => array(450, 323),
    );
    return isset($frames[$size]) ? $frames[$size] : array(611, 728);
}
