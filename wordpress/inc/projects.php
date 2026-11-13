<?php

function silkscreen_project_from_post(WP_Post $post) {
    $id = $post->ID;
    $slug = $post->post_name;

    $image = silkscreen_image('card_image', $id);
    if (!$image && has_post_thumbnail($id)) {
        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
        if ($thumb) {
            $image = array('src' => $thumb[0], 'alt' => get_post_meta($id, '_wp_attachment_image_alt', true) ?: $post->post_title);
        }
    }

    $stills = silkscreen_gallery('stills', $id);
    if (!$stills && $image) {
        $stills = array($image);
    }

    $poster = silkscreen_image('poster', $id);

    $credits = array();
    if (function_exists('get_field')) {
        $rows = get_field('credits', $id);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $names_raw = isset($row['names']) ? (string) $row['names'] : '';
                $names = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', $names_raw))));
                $credits[] = array(
                    'role' => isset($row['role']) ? (string) $row['role'] : 'custom',
                    'custom_label' => isset($row['custom_label']) ? (string) $row['custom_label'] : '',
                    'names' => $names,
                );
            }
        }
    }

    $listed = function_exists('get_field') ? get_field('listed', $id) : true;
    if ($listed === null || $listed === '') {
        $listed = true;
    }

    return array(
        'id' => $id,
        'title' => $post->post_title,
        'slug' => $slug,
        'synopsis' => function_exists('get_field') ? (string) get_field('synopsis', $id) : '',
        'director' => function_exists('get_field') ? (string) get_field('director', $id) : '',
        'image' => $image,
        'stills' => $stills,
        'poster' => $poster,
        'trailer_url' => function_exists('get_field') ? (string) get_field('trailer_url', $id) : '',
        'tickets_url' => function_exists('get_field') ? (string) get_field('tickets_url', $id) : '',
        'credits' => $credits,
        'status' => function_exists('get_field') ? (string) get_field('status', $id) : 'released',
        'featured' => silkscreen_bool('featured', $id, false),
        'order' => (int) $post->menu_order,
        'detail_theme' => function_exists('get_field') ? (string) get_field('detail_theme', $id) : 'light',
        'listed' => (bool) $listed,
        'on_posters' => silkscreen_bool('on_posters', $id, false),
        'permalink' => get_permalink($id),
    );
}

function silkscreen_projects_query($args = array()) {
    $defaults = array(
        'post_type' => SILKSCREEN_PROJECT_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    $query = new WP_Query(array_merge($defaults, $args));
    $projects = array();
    foreach ($query->posts as $post) {
        $projects[] = silkscreen_project_from_post($post);
    }
    wp_reset_postdata();
    return $projects;
}

function silkscreen_get_project($slug) {
    $posts = get_posts(array(
        'post_type' => SILKSCREEN_PROJECT_POST_TYPE,
        'name' => $slug,
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'no_found_rows' => true,
    ));
    if (!$posts) {
        return null;
    }
    return silkscreen_project_from_post($posts[0]);
}

function silkscreen_featured_projects($limit = 2) {
    $all = silkscreen_projects_query();
    $featured = array_values(array_filter($all, function ($p) {
        return !empty($p['featured']) && !empty($p['listed']);
    }));
    if (count($featured) < $limit) {
        foreach ($all as $p) {
            if (count($featured) >= $limit) {
                break;
            }
            if (empty($p['listed']) || in_array($p, $featured, true)) {
                continue;
            }
            $featured[] = $p;
        }
    }
    return array_slice($featured, 0, $limit);
}

function silkscreen_listed_projects() {
    return array_values(array_filter(silkscreen_projects_query(), function ($p) {
        return !empty($p['listed']);
    }));
}

function silkscreen_poster_projects() {
    return array_values(array_filter(silkscreen_projects_query(), function ($p) {
        return !empty($p['on_posters']);
    }));
}
