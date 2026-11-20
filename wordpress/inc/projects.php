<?php

/**
 * Projects page only includes films that have at least one still.
 */
function silkscreen_project_on_projects_index($on_posters, $synopsis, $featured, $stills_count) {
    unset($on_posters, $synopsis, $featured);
    return $stills_count > 0;
}

function silkscreen_project_from_post(WP_Post $post) {
    $id = $post->ID;
    $slug = $post->post_name;

    $image = silkscreen_image('card_image', $id);

    $own_stills = silkscreen_project_stills($id);
    $stills = $own_stills;
    if (!$stills && $image) {
        $stills = array($image);
    }

    $posters = silkscreen_project_posters($id);
    $poster = $posters[0] ?? null;

    $credits = silkscreen_project_credits($id);

    $on_posters = silkscreen_bool('on_posters', $id, false);
    $synopsis = function_exists('get_field') ? (string) get_field('synopsis', $id) : '';
    $featured = silkscreen_bool('featured', $id, false);
    $stills_count = count($own_stills);

    return array(
        'id' => $id,
        'title' => $post->post_title,
        'slug' => $slug,
        'synopsis' => $synopsis,
        'director' => function_exists('get_field') ? (string) get_field('director', $id) : '',
        'image' => $image,
        'stills' => $stills,
        'poster' => $poster,
        'posters' => $posters,
        'trailer_url' => trim(function_exists('get_field') ? (string) get_field('trailer_url', $id) : ''),
        'tickets_url' => trim(function_exists('get_field') ? (string) get_field('tickets_url', $id) : ''),
        'credits' => $credits,
        'featured' => $featured,
        'order' => (int) $post->menu_order,
        'listed' => silkscreen_project_on_projects_index($on_posters, $synopsis, $featured, $stills_count),
        'on_posters' => $on_posters,
        'permalink' => get_permalink($id),
    );
}

function silkscreen_projects_query($args = array()) {
    $defaults = array(
        'post_type' => SILKSCREEN_PROJECT_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
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
    $featured = array_values(array_filter(silkscreen_projects_query(), function ($p) {
        return !empty($p['featured']) && !empty($p['listed']);
    }));
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

/**
 * One card per poster. Each card links back to its project.
 * Saved Posters meta wins. Older projects still use the single poster when “Show on posters page” is on.
 */
function silkscreen_posters_page_items() {
    $items = array();
    foreach (silkscreen_projects_query() as $project) {
        $saved = !empty($project['id']) && metadata_exists('post', (int) $project['id'], SILKSCREEN_PROJECT_POSTERS_META);
        if ($saved) {
            $posters = $project['posters'] ?? array();
        } elseif (!empty($project['on_posters'])) {
            $fallback = !empty($project['poster']['src']) ? $project['poster'] : ($project['image'] ?? null);
            $posters = ($fallback && !empty($fallback['src'])) ? array($fallback) : array();
        } else {
            continue;
        }

        foreach ($posters as $poster) {
            if (empty($poster['src'])) {
                continue;
            }
            $items[] = array(
                'title' => $project['title'],
                'permalink' => $project['permalink'],
                'poster' => $poster,
            );
        }
    }
    return $items;
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

function silkscreen_project_has_saved_stills_meta($post_id) {
    if (!$post_id) {
        return false;
    }
    return metadata_exists('post', (int) $post_id, SILKSCREEN_PROJECT_STILLS_META);
}

function silkscreen_project_detail_stills($project) {
    $own = !empty($project['stills']) ? $project['stills'] : (!empty($project['image']) ? array($project['image']) : array());
    $post_id = !empty($project['id']) ? (int) $project['id'] : 0;
    if ($post_id && silkscreen_project_has_saved_stills_meta($post_id)) {
        return $own;
    }
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

/**
 * Project detail trailer / tickets — show only when CMS fields are non-empty.
 */
function silkscreen_project_actions_for_detail($project) {
    $trailer = trim((string) ($project['trailer_url'] ?? ''));
    $tickets = trim((string) ($project['tickets_url'] ?? ''));
    $has_trailer = $trailer !== '';
    $has_tickets = $tickets !== '';
    $has_links = $has_trailer || $has_tickets;
    $shows_synopsis = silkscreen_project_detail_synopsis($project) !== '';

    return array(
        'trailer_url' => $has_trailer ? $trailer : null,
        'tickets_url' => $has_tickets ? $tickets : null,
        'reserve_spacing' => $has_links || $shows_synopsis,
        'spacing_only' => $shows_synopsis && !$has_links,
    );
}

function silkscreen_project_action_markup($href, $label) {
    $href = trim((string) $href);
    if ($href === '') {
        return '';
    }
    $suffix = '<span aria-hidden="true">&gt;</span>';
    $label_esc = esc_html($label);
    $external = (bool) preg_match('#^https?://#', $href);
    $attrs = $external ? ' target="_blank" rel="noreferrer"' : '';

    return '<a class="ss-project-action" href="' . esc_url($href) . '"' . $attrs . '>' . $label_esc . $suffix . '</a>';
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
