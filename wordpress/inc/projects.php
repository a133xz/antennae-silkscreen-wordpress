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

function silkscreen_project_is_sparse($project) {
    $still_count = !empty($project['stills']) ? count($project['stills']) : 0;
    $no_copy = $project['synopsis'] === '' && empty($project['poster']) && empty($project['credits']);
    return $still_count < 2 || $no_copy;
}

function silkscreen_project_neighbors($project) {
    if (!empty($project['listed'])) {
        $list = silkscreen_listed_projects();
    } elseif (!empty($project['on_posters'])) {
        $list = silkscreen_poster_projects();
    } else {
        $list = silkscreen_listed_projects();
    }
    $index = -1;
    foreach ($list as $i => $item) {
        if ($item['slug'] === $project['slug']) {
            $index = $i;
            break;
        }
    }
    return array(
        'prev' => $index > 0 ? $list[$index - 1] : null,
        'next' => ($index >= 0 && $index < count($list) - 1) ? $list[$index + 1] : null,
    );
}

function silkscreen_project_primary_still($project) {
    if (!empty($project['stills'])) {
        return $project['stills'][0];
    }
    if (!empty($project['image'])) {
        return $project['image'];
    }
    return null;
}

function silkscreen_project_detail_stills($project) {
    $own = !empty($project['stills']) ? $project['stills'] : (!empty($project['image']) ? array($project['image']) : array());
    if (!silkscreen_project_is_sparse($project)) {
        return $own;
    }
    $neighbors = silkscreen_project_neighbors($project);
    $seen = array();
    foreach ($own as $still) {
        $seen[$still['src']] = true;
    }
    foreach (array('prev', 'next') as $key) {
        if (empty($neighbors[$key])) {
            continue;
        }
        $still = silkscreen_project_primary_still($neighbors[$key]);
        if (!$still || isset($seen[$still['src']])) {
            continue;
        }
        $own[] = $still;
        $seen[$still['src']] = true;
    }
    return $own;
}

function silkscreen_project_detail_credits($project) {
    if (!empty($project['credits'])) {
        return $project['credits'];
    }
    if (!empty($project['director'])) {
        return array(array(
            'role' => 'directed',
            'custom_label' => '',
            'names' => array($project['director']),
        ));
    }
    return array();
}

function silkscreen_project_detail_synopsis($project) {
    if ($project['synopsis'] !== '') {
        return $project['synopsis'];
    }
    if (silkscreen_project_is_sparse($project)) {
        return 'More on this title is coming soon.';
    }
    return '';
}

function silkscreen_credit_label($credit) {
    $labels = array(
        'directed' => 'Directed by',
        'written' => 'Written by',
        'produced' => 'Produced by',
        'executive-producers' => 'Executive Producers',
        'companies' => 'Production Companies',
        'starring' => 'Starring',
        'custom' => 'Credit',
    );
    if (($credit['role'] ?? '') === 'custom') {
        $custom = isset($credit['custom_label']) ? trim($credit['custom_label']) : '';
        return $custom !== '' ? $custom : $labels['custom'];
    }
    $role = $credit['role'] ?? 'custom';
    return isset($labels[$role]) ? $labels[$role] : $labels['custom'];
}
