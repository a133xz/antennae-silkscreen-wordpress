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

/**
 * Project detail slider stills (ACF Free–compatible still_1…still_N fields).
 */
function silkscreen_project_stills($post_id) {
    if (!$post_id || !function_exists('get_field')) {
        return array();
    }

    $out = array();
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
