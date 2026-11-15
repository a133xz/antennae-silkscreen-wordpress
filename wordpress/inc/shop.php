<?php

const SILKSCREEN_SHOP_PAGE_TEMPLATE = 'page-shop-settings.php';
const SILKSCREEN_SHOP_PRODUCT_SLOTS = 6;

function silkscreen_shop_page_id() {
    static $page_id = null;
    if ($page_id !== null) {
        return $page_id;
    }
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => array('publish', 'draft', 'private'),
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => SILKSCREEN_SHOP_PAGE_TEMPLATE,
        'no_found_rows' => true,
        'fields' => 'ids',
    ));
    $page_id = $pages ? (int) $pages[0] : 0;
    return $page_id;
}

function silkscreen_shop_get_field($name, $default = null) {
    $page_id = silkscreen_shop_page_id();
    if (!$page_id || !function_exists('get_field')) {
        return $default;
    }
    $value = get_field($name, $page_id);
    if ($value === null || $value === '') {
        return $default;
    }
    return $value;
}

function silkscreen_shop_available() {
    return (bool) silkscreen_shop_get_field('shop_available', true);
}

function silkscreen_shop_url($slug = '') {
    $base = trailingslashit(home_url('/shop'));
    if ($slug === '') {
        return $base;
    }
    return $base . rawurlencode($slug) . '/';
}

function silkscreen_format_price($amount) {
    return '£' . number_format((float) $amount, fmod((float) $amount, 1.0) === 0.0 ? 0 : 2, '.', '');
}

function silkscreen_shop_acf_picture($image, $alt_fallback = '') {
    if (!is_array($image) || empty($image['url'])) {
        return null;
    }
    return array(
        'src' => $image['url'],
        'alt' => !empty($image['alt']) ? (string) $image['alt'] : $alt_fallback,
    );
}

function silkscreen_shop_product_from_slot($slot) {
    $page_id = silkscreen_shop_page_id();
    if (!$page_id || !function_exists('get_field')) {
        return null;
    }
    $title = trim((string) get_field('shop_product_' . $slot . '_title', $page_id));
    $slug = sanitize_title((string) get_field('shop_product_' . $slot . '_slug', $page_id));
    $card = silkscreen_shop_acf_picture(get_field('shop_product_' . $slot . '_card_image', $page_id), $title);
    if ($title === '' || $slug === '' || !$card) {
        return null;
    }

    return array(
        'id' => (int) $slot,
        'title' => $title,
        'slug' => $slug,
        'price' => (float) get_field('shop_product_' . $slot . '_price', $page_id),
        'image' => $card,
        'featured' => (bool) get_field('shop_product_' . $slot . '_featured', $page_id),
        'active' => true,
        'order' => (int) $slot - 1,
    );
}

function silkscreen_shop_catalog_default() {
    $products = array(
        array('title' => 'Tote bag', 'slug' => 'tote-bag', 'price' => 50, 'image' => 'product-tote-cut.png', 'alt' => 'Silkscreen tote bag', 'featured' => false, 'order' => 0),
        array('title' => 'T-shirt', 'slug' => 't-shirt', 'price' => 30, 'image' => 'product-tshirt.png', 'alt' => 'Black Silkscreen t-shirt', 'featured' => false, 'order' => 1),
        array('title' => 'Zine', 'slug' => 'zine', 'price' => 20, 'image' => 'product-zine.png', 'alt' => 'Silkscreen zine', 'featured' => false, 'order' => 2),
        array('title' => 'Lighter', 'slug' => 'lighter', 'price' => 50, 'image' => 'product-lighter-cut.png', 'alt' => 'Silkscreen lighter', 'featured' => true, 'order' => 3),
        array('title' => 'Postcards', 'slug' => 'postcards', 'price' => 50, 'image' => 'product-postcards-cut.png', 'alt' => 'Stack of Silkscreen postcards', 'featured' => true, 'order' => 4),
        array('title' => 'Skateboard', 'slug' => 'skateboard', 'price' => 80, 'image' => 'product-skateboard-cut.png', 'alt' => 'Two Silkscreen skateboards', 'featured' => true, 'order' => 5),
    );
    $out = array();
    foreach ($products as $i => $row) {
        $out[] = array(
            'id' => $i + 1,
            'title' => $row['title'],
            'slug' => $row['slug'],
            'price' => $row['price'],
            'image' => array('src' => silkscreen_asset('media/' . $row['image']), 'alt' => $row['alt']),
            'featured' => $row['featured'],
            'active' => true,
            'order' => $row['order'],
        );
    }
    return $out;
}

function silkscreen_shop_catalog_from_cms() {
    if (!silkscreen_shop_page_id()) {
        return array();
    }
    $products = array();
    for ($slot = 1; $slot <= SILKSCREEN_SHOP_PRODUCT_SLOTS; $slot++) {
        $product = silkscreen_shop_product_from_slot($slot);
        if ($product) {
            $products[] = $product;
        }
    }
    return $products;
}

function silkscreen_shop_catalog() {
    $from_cms = silkscreen_shop_catalog_from_cms();
    if ($from_cms !== array()) {
        return $from_cms;
    }
    return silkscreen_shop_catalog_default();
}

function silkscreen_shop_products() {
    $products = array_values(array_filter(silkscreen_shop_catalog(), function ($product) {
        return !empty($product['active']);
    }));
    usort($products, function ($a, $b) {
        return (int) $a['order'] <=> (int) $b['order'];
    });
    return $products;
}

function silkscreen_shop_product($slug) {
    foreach (silkscreen_shop_products() as $product) {
        if ($product['slug'] === $slug) {
            return $product;
        }
    }
    return null;
}

function silkscreen_featured_products($limit = 3) {
    $all = silkscreen_shop_products();
    $featured = array_values(array_filter($all, function ($p) {
        return !empty($p['featured']);
    }));
    if (count($featured) < $limit) {
        foreach ($all as $p) {
            if (count($featured) >= $limit) {
                break;
            }
            if (in_array($p, $featured, true)) {
                continue;
            }
            $featured[] = $p;
        }
    }
    return array_slice($featured, 0, $limit);
}
