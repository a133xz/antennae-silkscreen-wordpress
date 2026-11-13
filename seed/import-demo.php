<?php
/**
 * Seed Local WordPress with Silkscreen demo content (from antennae-silkscreen records).
 *
 * Usage:
 *   php seed/import-demo.php
 *   php seed/import-demo.php --force
 *
 * Requires: active Silkscreen theme, Advanced Custom Fields.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI only.\n");
    exit(1);
}

$force = in_array('--force', $argv, true);
$root = dirname(__DIR__);
$wp_load = getenv('WP_LOAD_PATH') ?: '/Users/antonio/Local Sites/localpress/app/public/wp-load.php';

if (!is_file($wp_load)) {
    fwrite(STDERR, "wp-load.php not found at {$wp_load}. Set WP_LOAD_PATH.\n");
    exit(1);
}

require $wp_load;

if (!function_exists('get_field')) {
    fwrite(STDERR, "ACF is required (install Advanced Custom Fields).\n");
    exit(1);
}

if (!defined('SILKSCREEN_PROJECT_POST_TYPE')) {
    fwrite(STDERR, "Activate the Silkscreen 2026 theme first.\n");
    exit(1);
}

$data = require __DIR__ . '/records.php';

function silkscreen_seed_log($message) {
    echo $message . PHP_EOL;
}

function silkscreen_seed_demo_posts() {
  return get_posts(array(
      'post_type' => array('page', SILKSCREEN_PROJECT_POST_TYPE, SILKSCREEN_NEWS_POST_TYPE, 'attachment'),
      'post_status' => array('publish', 'draft', 'inherit', 'private'),
      'posts_per_page' => -1,
      'meta_key' => '_silkscreen_demo',
      'meta_value' => '1',
      'fields' => 'ids',
      'no_found_rows' => true,
  ));
}

function silkscreen_seed_purge() {
    foreach (silkscreen_seed_demo_posts() as $post_id) {
        wp_delete_post((int) $post_id, true);
    }
}

function silkscreen_seed_media_file($filename) {
    $path = get_template_directory() . '/assets/media/' . ltrim($filename, '/');
    return is_file($path) ? $path : null;
}

function silkscreen_seed_attach($filename, $alt = '') {
    static $cache = array();
    if (isset($cache[$filename])) {
        return $cache[$filename];
    }
    $path = silkscreen_seed_media_file($filename);
    if (!$path) {
        silkscreen_seed_log("  ! missing asset: {$filename}");
        $cache[$filename] = 0;
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $bits = wp_upload_bits(basename($path), null, file_get_contents($path));
    if (!empty($bits['error'])) {
        silkscreen_seed_log("  ! upload failed {$filename}: {$bits['error']}");
        $cache[$filename] = 0;
        return 0;
    }

    $filetype = wp_check_filetype(basename($path), null);
    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title' => sanitize_file_name(pathinfo($path, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
    );
    $attach_id = wp_insert_attachment($attachment, $bits['file']);
    if (is_wp_error($attach_id) || !$attach_id) {
        $cache[$filename] = 0;
        return 0;
    }

    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $bits['file']));
    if ($alt !== '') {
        update_post_meta($attach_id, '_wp_attachment_image_alt', $alt);
    }
    update_post_meta($attach_id, '_silkscreen_demo', '1');
    $cache[$filename] = (int) $attach_id;
    return $cache[$filename];
}

function silkscreen_seed_page($title, $slug, $template = '') {
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing && !get_post_meta($existing->ID, '_silkscreen_demo', true)) {
        $existing = null;
    }

    $page_id = wp_insert_post(array(
        'ID' => $existing ? $existing->ID : 0,
        'post_title' => $title,
        'post_name' => $slug,
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_content' => '',
    ), true);

    if (is_wp_error($page_id)) {
        throw new RuntimeException($page_id->get_error_message());
    }

    update_post_meta($page_id, '_silkscreen_demo', '1');
    if ($template !== '') {
        update_post_meta($page_id, '_wp_page_template', $template);
    }

    return (int) $page_id;
}

if (!$force && get_option('silkscreen_demo_seeded') === '1') {
    silkscreen_seed_log('Demo content already seeded. Pass --force to replace.');
    exit(0);
}

if ($force) {
    silkscreen_seed_log('Removing previous demo content…');
    silkscreen_seed_purge();
}

silkscreen_seed_log('Creating pages…');
$home_id = silkscreen_seed_page('Home', 'home');
$about_id = silkscreen_seed_page('About', 'about', 'page-about.php');
$contact_id = silkscreen_seed_page('Contact', 'contact', 'page-contact.php');
silkscreen_seed_page('Posters', 'posters', 'page-posters.php');
$shop_id = silkscreen_seed_page('Shop settings', 'shop-settings', 'page-shop-settings.php');

update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);

$s = $data['settings'];
update_field('tagline', $s['tagline'], $home_id);
update_field('statement', $s['statement'], $home_id);
update_field('about_statement', $s['statement'], $about_id);
update_field('contact_intro', $s['contact_intro'], $contact_id);
foreach (range(1, SILKSCREEN_CONTACT_PRIMARY_SLOTS) as $slot) {
    $row = $s['emails'][$slot - 1] ?? array('label' => '', 'address' => '');
    update_field('contact_email_' . $slot . '_label', $row['label'], $contact_id);
    update_field('contact_email_' . $slot . '_address', $row['address'], $contact_id);
}
update_field('contact_emails_extra', array(), $contact_id);
update_field('copyright', $s['copyright'], $home_id);
update_field('social_handle', $s['social_handle'], $home_id);
update_field('social_url', $s['social_url'], $home_id);
update_field('contact_intro', $s['contact_intro'], $home_id);
update_field('shop_enabled', (bool) $s['shop_enabled'], $home_id);
update_field('posters_enabled', (bool) $s['posters_enabled'], $home_id);
update_field('contact_emails', $s['emails'], $home_id);

$hero_ids = array();
foreach ($s['hero'] as $slide) {
    $id = silkscreen_seed_attach($slide['file'], $slide['alt']);
    if ($id) {
        $hero_ids[] = $id;
    }
}
if ($hero_ids) {
    update_field('hero_slides', $hero_ids, $home_id);
}

update_field('shop_available', true, $shop_id);
foreach ($data['products'] as $i => $product) {
    $slot = $i + 1;
    $prefix = 'shop_product_' . $slot . '_';
    update_field($prefix . 'title', $product['title'], $shop_id);
    update_field($prefix . 'slug', $product['slug'], $shop_id);
    update_field($prefix . 'price', $product['price'], $shop_id);
    update_field($prefix . 'featured', (bool) $product['featured'], $shop_id);
    $image_id = silkscreen_seed_attach($product['image'], $product['alt']);
    if ($image_id) {
        update_field($prefix . 'card_image', $image_id, $shop_id);
    }
}

silkscreen_seed_log('Creating projects…');
$project_ids = array();
foreach ($data['projects'] as $row) {
    $post_id = wp_insert_post(array(
        'post_title' => $row['title'],
        'post_name' => $row['slug'],
        'post_type' => SILKSCREEN_PROJECT_POST_TYPE,
        'post_status' => 'publish',
        'menu_order' => (int) $row['order'],
    ), true);
    if (is_wp_error($post_id)) {
        throw new RuntimeException($post_id->get_error_message());
    }
    $post_id = (int) $post_id;
    $project_ids[$row['slug']] = $post_id;
    update_post_meta($post_id, '_silkscreen_demo', '1');

    update_field('director', $row['director'] ?? '', $post_id);
    update_field('synopsis', $row['synopsis'] ?? '', $post_id);
    update_field('status', 'released', $post_id);
    update_field('detail_theme', 'light', $post_id);
    update_field('featured', !empty($row['featured']), $post_id);
    update_field('listed', array_key_exists('listed', $row) ? (bool) $row['listed'] : true, $post_id);
    update_field('on_posters', !empty($row['on_posters']), $post_id);

    if (!empty($row['image'])) {
        $card_id = silkscreen_seed_attach($row['image'], $row['image_alt'] ?? $row['title']);
        if ($card_id) {
            update_field('card_image', $card_id, $post_id);
            set_post_thumbnail($post_id, $card_id);
        }
    }

    $still_ids = array();
    foreach ($row['stills'] ?? array($row['image'] ?? '') as $still) {
        if ($still === '') {
            continue;
        }
        $sid = silkscreen_seed_attach($still, $row['image_alt'] ?? $row['title']);
        if ($sid) {
            $still_ids[] = $sid;
        }
    }
    if ($still_ids) {
        update_field('stills', $still_ids, $post_id);
    }

    if (!empty($row['poster'])) {
        $poster_id = silkscreen_seed_attach($row['poster'], $row['poster_alt'] ?? ($row['title'] . ' poster'));
        if ($poster_id) {
            update_field('poster', $poster_id, $post_id);
        }
    }

    if (!empty($row['credits'])) {
        $credit_rows = array();
        foreach ($row['credits'] as $credit) {
            $credit_rows[] = array(
                'role' => $credit['role'],
                'custom_label' => '',
                'names' => implode("\n", $credit['names']),
            );
        }
        update_field('credits', $credit_rows, $post_id);
    }
}

silkscreen_seed_log('Creating news…');
foreach ($data['news'] as $row) {
    $post_id = wp_insert_post(array(
        'post_title' => $row['title'],
        'post_name' => sanitize_title($row['title'] . '-' . $row['order']),
        'post_type' => SILKSCREEN_NEWS_POST_TYPE,
        'post_status' => 'publish',
        'menu_order' => (int) $row['order'],
        'post_excerpt' => $row['excerpt'] ?? '',
    ), true);
    if (is_wp_error($post_id)) {
        throw new RuntimeException($post_id->get_error_message());
    }
    $post_id = (int) $post_id;
    update_post_meta($post_id, '_silkscreen_demo', '1');

    update_field('kicker', $row['kicker'] ?? '', $post_id);
    update_field('date_label', $row['date_label'] ?? '', $post_id);
    update_field('news_layout', 'caption_below', $post_id);
    update_field('masonry_size', $row['size'] ?? 'medium', $post_id);
    update_field('featured', !empty($row['featured']), $post_id);
    if (!empty($row['excerpt'])) {
        update_field('excerpt', $row['excerpt'], $post_id);
    }

    if (!empty($row['image'])) {
        $image_id = silkscreen_seed_attach($row['image'], $row['image_alt'] ?? '');
        if ($image_id) {
            update_field('news_image', $image_id, $post_id);
        }
    }

    if (!empty($row['project_slug']) && !empty($project_ids[$row['project_slug']])) {
        update_field('linked_project', $project_ids[$row['project_slug']], $post_id);
    }
}

flush_rewrite_rules(false);
update_option('silkscreen_demo_seeded', '1');
update_option('silkscreen_2026_rewrite', '1');

silkscreen_seed_log('Done. Open the site front page (Settings → Reading should point to Home).');
