<?php
// Enqueue Styles
function silkscreen_enqueue_scripts()
{
    wp_enqueue_style('silkscreen-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'silkscreen_enqueue_scripts');

// Disable Gutenberg - Force Classic Editor
add_filter('use_block_editor_for_post', '__return_false');
add_filter('use_block_editor_for_page', '__return_false');

function silkscreen_remove_editor()
{
    remove_post_type_support('page', 'editor');
    remove_post_type_support('post', 'editor');
}
add_action('admin_init', 'silkscreen_remove_editor');

add_filter('show_admin_bar', '__return_false');

function silkscreen_enable_svg_uploads($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'silkscreen_enable_svg_uploads');

function allow_ico_upload($mimes)
{
    $mimes['ico'] = 'image/x-icon';
    return $mimes;
}
add_filter('upload_mimes', 'allow_ico_upload');

function silkscreen_legacy_media_base_url()
{
    if (file_exists(WP_CONTENT_DIR . '/themes/silkscreen-2026/assets/media/hero-1.png')) {
        return content_url('themes/silkscreen-2026/assets/media');
    }
    return get_template_directory_uri() . '/images';
}

function silkscreen_legacy_default_slides()
{
    $media = silkscreen_legacy_media_base_url();
    return array(
        array(
            'image' => $media . '/hero-1.png',
            'title' => 'Two Neighbours',
            'client' => 'Silkscreen Entertainment',
            'date' => '2026',
        ),
        array(
            'image' => $media . '/hero-2.png',
            'title' => 'Sorry, Baby',
            'client' => 'Silkscreen Entertainment',
            'date' => '2026',
        ),
        array(
            'image' => $media . '/hero-3.png',
            'title' => 'The Killings of a Flower Moon',
            'client' => 'Silkscreen Entertainment',
            'date' => '2026',
        ),
        array(
            'image' => $media . '/hero-4.png',
            'title' => 'A Different Man',
            'client' => 'Silkscreen Entertainment',
            'date' => '2026',
        ),
    );
}

function silkscreen_legacy_acf_scalar($post_id, $name, $default = '')
{
    if (function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if (is_string($value) && $value !== '') {
            return $value;
        }
        if (is_array($value) && !empty($value['url'])) {
            return $value;
        }
    }
    $meta = get_post_meta($post_id, $name, true);
    if (is_string($meta) && $meta !== '') {
        return $meta;
    }
    return $default;
}

/**
 * Read slideshow rows when the DB “Home page” ACF group was removed by Silkscreen 2026.
 */
function silkscreen_legacy_slides_from_post_meta($post_id)
{
    $slides = array();
    $all = get_post_meta($post_id);
    if (!$all) {
        return $slides;
    }

    for ($i = 1; $i <= 15; $i++) {
        $image_id = null;
        $sub_image = 'slide_' . $i . '_slide_image';

        if (!empty($all[$sub_image][0])) {
            $image_id = $all[$sub_image][0];
        } elseif (!empty($all['slide_' . $i][0])) {
            $raw = maybe_unserialize($all['slide_' . $i][0]);
            if (is_array($raw) && !empty($raw['slide_image'])) {
                $image_id = $raw['slide_image'];
            } elseif (is_numeric($raw)) {
                $image_id = (int) $raw;
            }
        }

        if (!$image_id) {
            continue;
        }

        if (is_array($image_id)) {
            $image_id = $image_id['ID'] ?? $image_id['id'] ?? 0;
        }
        $image_id = (int) $image_id;
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
        if (!$image_url) {
            continue;
        }

        $title = '';
        $client = '';
        $date = '';
        if (!empty($all['slide_' . $i . '_title'][0])) {
            $title = (string) $all['slide_' . $i . '_title'][0];
        }
        if (!empty($all['slide_' . $i . '_client'][0])) {
            $client = (string) $all['slide_' . $i . '_client'][0];
        }
        if (!empty($all['slide_' . $i . '_date'][0])) {
            $date = (string) $all['slide_' . $i . '_date'][0];
        }

        if (function_exists('get_field')) {
            $group = get_field('slide_' . $i, $post_id);
            if (is_array($group)) {
                $title = $title !== '' ? $title : (string) ($group['title'] ?? '');
                $client = $client !== '' ? $client : (string) ($group['client'] ?? '');
                $date = $date !== '' ? $date : (string) ($group['date'] ?? '');
            }
        }

        $slides[] = array(
            'image' => $image_url,
            'title' => $title,
            'client' => $client,
            'date' => $date,
        );
    }

    return $slides;
}

function silkscreen_legacy_slides_from_acf($post_id)
{
    $slides = array();
    if (!function_exists('get_field')) {
        return $slides;
    }
    for ($i = 1; $i <= 15; $i++) {
        $group = get_field('slide_' . $i, $post_id);
        $image_id = is_array($group) ? ($group['slide_image'] ?? null) : null;
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : null;
        if (!$image_url) {
            continue;
        }
        $slides[] = array(
            'image' => $image_url,
            'title' => (string) ($group['title'] ?? ''),
            'client' => (string) ($group['client'] ?? ''),
            'date' => (string) ($group['date'] ?? ''),
        );
    }
    return $slides;
}

function silkscreen_legacy_slide_count($post_id)
{
    if (!$post_id) {
        return 0;
    }
    $slides = silkscreen_legacy_slides_from_acf($post_id);
    if ($slides) {
        return count($slides);
    }
    $slides = silkscreen_legacy_slides_from_post_meta($post_id);
    return count($slides);
}

function silkscreen_legacy_get_slides($post_id)
{
    if ($post_id) {
        $slides = silkscreen_legacy_slides_from_acf($post_id);
        if ($slides) {
            return $slides;
        }
        $slides = silkscreen_legacy_slides_from_post_meta($post_id);
        if ($slides) {
            return $slides;
        }
    }
    return silkscreen_legacy_default_slides();
}

function silkscreen_legacy_content_page_id()
{
    $candidates = array();
    if ((int) get_queried_object_id() > 0) {
        $candidates[] = (int) get_queried_object_id();
    }
    $front = (int) get_option('page_on_front');
    if ($front > 0) {
        $candidates[] = $front;
    }
    foreach (array('home-page', 'home') as $slug) {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page) {
            $candidates[] = (int) $page->ID;
        }
    }

    foreach (array_unique($candidates) as $page_id) {
        if (silkscreen_legacy_slide_count($page_id) > 0) {
            return $page_id;
        }
    }

    foreach (array_unique($candidates) as $page_id) {
        return $page_id;
    }

    $legacy = get_page_by_path('home-page', OBJECT, 'page');
    return $legacy ? (int) $legacy->ID : 0;
}