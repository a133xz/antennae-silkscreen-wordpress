<?php

function silkscreen_credit_role_choices() {
    return array(
        'directed' => 'Directed by',
        'written' => 'Written by',
        'produced' => 'Produced by',
        'executive-producers' => 'Executive producers',
        'companies' => 'Companies',
        'starring' => 'Starring',
        'custom' => 'Custom label',
    );
}

function silkscreen_masonry_size_choices() {
    return array(
        'narrow' => 'Narrow (3 cols)',
        'medium' => 'Medium (4 cols)',
        'wide' => 'Wide (5 cols)',
        'half' => 'Half (6 cols)',
        'half-short' => 'Half short (6 cols)',
        'small' => 'Small (3 cols)',
    );
}

function silkscreen_project_fields() {
    return array(
        array(
            'key' => 'field_ss_project_director',
            'label' => 'Director',
            'name' => 'director',
            'type' => 'text',
        ),
        array(
            'key' => 'field_ss_project_synopsis',
            'label' => 'Synopsis',
            'name' => 'synopsis',
            'type' => 'textarea',
            'rows' => 4,
        ),
        array(
            'key' => 'field_ss_project_status',
            'label' => 'Status',
            'name' => 'status',
            'type' => 'select',
            'choices' => array('released' => 'Released', 'coming-soon' => 'Coming soon'),
            'default_value' => 'released',
        ),
        array(
            'key' => 'field_ss_project_detail_theme',
            'label' => 'Detail theme',
            'name' => 'detail_theme',
            'type' => 'select',
            'choices' => array('light' => 'Light', 'inverse' => 'Inverse', 'accent' => 'Accent'),
            'default_value' => 'light',
        ),
        array(
            'key' => 'field_ss_project_featured',
            'label' => 'Featured on home',
            'name' => 'featured',
            'type' => 'true_false',
            'ui' => 1,
        ),
        array(
            'key' => 'field_ss_project_listed',
            'label' => 'Listed on projects index',
            'name' => 'listed',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
        ),
        array(
            'key' => 'field_ss_project_on_posters',
            'label' => 'Show on posters page',
            'name' => 'on_posters',
            'type' => 'true_false',
            'ui' => 1,
        ),
        array(
            'key' => 'field_ss_project_card_image',
            'label' => 'Card / list still',
            'name' => 'card_image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ),
        array(
            'key' => 'field_ss_project_stills',
            'label' => 'Detail stills',
            'name' => 'stills',
            'type' => 'gallery',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ),
        array(
            'key' => 'field_ss_project_poster',
            'label' => 'Poster',
            'name' => 'poster',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ),
        array(
            'key' => 'field_ss_project_trailer',
            'label' => 'Trailer URL',
            'name' => 'trailer_url',
            'type' => 'url',
        ),
        array(
            'key' => 'field_ss_project_tickets',
            'label' => 'Tickets URL',
            'name' => 'tickets_url',
            'type' => 'url',
        ),
        array(
            'key' => 'field_ss_project_credits',
            'label' => 'Credits',
            'name' => 'credits',
            'type' => 'repeater',
            'layout' => 'row',
            'button_label' => 'Add credit row',
            'sub_fields' => array(
                array(
                    'key' => 'field_ss_credit_role',
                    'label' => 'Role',
                    'name' => 'role',
                    'type' => 'select',
                    'choices' => silkscreen_credit_role_choices(),
                ),
                array(
                    'key' => 'field_ss_credit_custom',
                    'label' => 'Custom label',
                    'name' => 'custom_label',
                    'type' => 'text',
                    'conditional_logic' => array(
                        array(array('field' => 'field_ss_credit_role', 'operator' => '==', 'value' => 'custom')),
                    ),
                ),
                array(
                    'key' => 'field_ss_credit_names',
                    'label' => 'Names (comma or line separated)',
                    'name' => 'names',
                    'type' => 'textarea',
                    'rows' => 2,
                ),
            ),
        ),
    );
}

function silkscreen_news_fields() {
    return array(
        array(
            'key' => 'field_ss_news_kicker',
            'label' => 'Kicker',
            'name' => 'kicker',
            'type' => 'text',
        ),
        array(
            'key' => 'field_ss_news_date',
            'label' => 'Date label',
            'name' => 'date_label',
            'type' => 'text',
            'instructions' => 'Display format as in design, e.g. 2.5.26',
        ),
        array(
            'key' => 'field_ss_news_layout',
            'label' => 'News layout',
            'name' => 'news_layout',
            'type' => 'select',
            'choices' => silkscreen_news_layout_choices(),
            'default_value' => 'caption_below',
            'instructions' => 'Three masonry layouts: caption below, overlay on image, or compact tile.',
        ),
        array(
            'key' => 'field_ss_news_size',
            'label' => 'Masonry size',
            'name' => 'masonry_size',
            'type' => 'select',
            'choices' => silkscreen_masonry_size_choices(),
            'default_value' => 'medium',
        ),
        array(
            'key' => 'field_ss_news_image',
            'label' => 'Image',
            'name' => 'news_image',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        ),
        array(
            'key' => 'field_ss_news_excerpt',
            'label' => 'Excerpt (hover)',
            'name' => 'excerpt',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_ss_news_project',
            'label' => 'Linked project',
            'name' => 'linked_project',
            'type' => 'post_object',
            'post_type' => array(SILKSCREEN_PROJECT_POST_TYPE),
            'return_format' => 'object',
            'allow_null' => 1,
        ),
        array(
            'key' => 'field_ss_news_featured',
            'label' => 'Featured on home',
            'name' => 'featured',
            'type' => 'true_false',
            'ui' => 1,
        ),
    );
}

function silkscreen_contact_primary_slot_fields() {
    $fields = array();
    $defaults = silkscreen_default_contact_emails();

    foreach (range(1, SILKSCREEN_CONTACT_PRIMARY_SLOTS) as $slot) {
        $row = isset($defaults[$slot - 1]) ? $defaults[$slot - 1] : array('label' => '', 'address' => '');
        $fields[] = array(
            'key' => 'field_ss_contact_email_' . $slot . '_label',
            'label' => sprintf('Email %d — type label', $slot),
            'name' => 'contact_email_' . $slot . '_label',
            'type' => 'text',
            'default_value' => $row['label'],
            'wrapper' => array('width' => '50'),
        );
        $fields[] = array(
            'key' => 'field_ss_contact_email_' . $slot . '_address',
            'label' => sprintf('Email %d — address', $slot),
            'name' => 'contact_email_' . $slot . '_address',
            'type' => 'email',
            'default_value' => $row['address'],
            'wrapper' => array('width' => '50'),
        );
    }

    return $fields;
}

function silkscreen_contact_fields() {
    return array_merge(
        array(
            array(
                'key' => 'field_ss_contact_page_intro',
                'label' => 'Contact intro',
                'name' => 'contact_intro',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => SILKSCREEN_CONTACT_INTRO,
            ),
        ),
        silkscreen_contact_primary_slot_fields()
    );
}

function silkscreen_about_fields() {
    return array(
        array(
            'key' => 'field_ss_about_statement',
            'label' => 'About statement',
            'name' => 'about_statement',
            'type' => 'textarea',
            'rows' => 5,
            'default_value' => SILKSCREEN_STATEMENT,
        ),
    );
}

function silkscreen_home_fields() {
    return array(
        array('key' => 'field_ss_tagline', 'label' => 'Tagline', 'name' => 'tagline', 'type' => 'text', 'default_value' => SILKSCREEN_TAGLINE),
        array('key' => 'field_ss_statement', 'label' => 'About statement', 'name' => 'statement', 'type' => 'textarea', 'rows' => 4, 'default_value' => SILKSCREEN_STATEMENT),
        array('key' => 'field_ss_copyright', 'label' => 'Copyright', 'name' => 'copyright', 'type' => 'text', 'default_value' => SILKSCREEN_COPYRIGHT),
        array('key' => 'field_ss_social_handle', 'label' => 'Social handle', 'name' => 'social_handle', 'type' => 'text', 'default_value' => SILKSCREEN_SOCIAL_HANDLE),
        array('key' => 'field_ss_social_url', 'label' => 'Social URL', 'name' => 'social_url', 'type' => 'url', 'default_value' => SILKSCREEN_SOCIAL_URL),
        array('key' => 'field_ss_contact_intro', 'label' => 'Contact intro', 'name' => 'contact_intro', 'type' => 'textarea', 'rows' => 3, 'default_value' => SILKSCREEN_CONTACT_INTRO),
    );
}

function silkscreen_shop_slot_fields($slot) {
    $p = 'shop_product_' . $slot . '_';
    return array(
        array('key' => 'field_ss_' . $p . 'title', 'label' => 'Title', 'name' => $p . 'title', 'type' => 'text'),
        array('key' => 'field_ss_' . $p . 'slug', 'label' => 'Slug', 'name' => $p . 'slug', 'type' => 'text'),
        array('key' => 'field_ss_' . $p . 'price', 'label' => 'Price (£)', 'name' => $p . 'price', 'type' => 'number'),
        array('key' => 'field_ss_' . $p . 'featured', 'label' => 'Featured on home', 'name' => $p . 'featured', 'type' => 'true_false', 'ui' => 1),
        array('key' => 'field_ss_' . $p . 'image', 'label' => 'Image', 'name' => $p . 'card_image', 'type' => 'image', 'return_format' => 'array'),
    );
}

function silkscreen_shop_fields_layout() {
    $fields = array(
        array(
            'key' => 'field_ss_shop_available',
            'label' => 'Shop available',
            'name' => 'shop_available',
            'type' => 'true_false',
            'ui' => 1,
            'instructions' => 'When off, /shop shows coming soon.',
        ),
    );
    for ($slot = 1; $slot <= SILKSCREEN_SHOP_PRODUCT_SLOTS; $slot++) {
        $fields = array_merge($fields, silkscreen_shop_slot_fields($slot));
    }
    return $fields;
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_silkscreen_2026_site',
        'title' => 'Site settings',
        'fields' => silkscreen_home_fields(),
        'location' => array(array(array('param' => 'page_type', 'operator' => '==', 'value' => 'front_page'))),
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'group_silkscreen_2026_project',
        'title' => 'Project details',
        'fields' => silkscreen_project_fields(),
        'location' => array(array(array('param' => 'post_type', 'operator' => '==', 'value' => SILKSCREEN_PROJECT_POST_TYPE))),
        'position' => 'normal',
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'group_silkscreen_2026_news',
        'title' => 'News details',
        'fields' => silkscreen_news_fields(),
        'location' => array(array(array('param' => 'post_type', 'operator' => '==', 'value' => SILKSCREEN_NEWS_POST_TYPE))),
        'position' => 'normal',
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'group_silkscreen_2026_shop',
        'title' => 'Shop catalog',
        'fields' => silkscreen_shop_fields_layout(),
        'location' => array(array(array('param' => 'page_template', 'operator' => '==', 'value' => SILKSCREEN_SHOP_PAGE_TEMPLATE))),
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'group_silkscreen_2026_about',
        'title' => 'About page',
        'fields' => silkscreen_about_fields(),
        'location' => array(array(array('param' => 'page_template', 'operator' => '==', 'value' => 'page-about.php'))),
        'position' => 'acf_after_title',
        'active' => true,
    ));

    acf_add_local_field_group(array(
        'key' => 'group_silkscreen_2026_contact',
        'title' => 'Contact page',
        'fields' => silkscreen_contact_fields(),
        'location' => array(array(array('param' => 'page_template', 'operator' => '==', 'value' => 'page-contact.php'))),
        'position' => 'acf_after_title',
        'active' => true,
    ));
});

add_filter('acf/load_value/name=contact_intro', function ($value, $post_id, $field) {
    if ($value !== null && $value !== '') {
        return $value;
    }
    if (get_post_meta($post_id, '_wp_page_template', true) !== 'page-contact.php') {
        return $value;
    }
    return SILKSCREEN_CONTACT_INTRO;
}, 10, 3);

add_filter('acf/load_value', function ($value, $post_id, $field) {
    if (!is_array($field) || empty($field['name'])) {
        return $value;
    }
    if (get_post_meta($post_id, '_wp_page_template', true) !== 'page-contact.php') {
        return $value;
    }
    if ($value !== null && $value !== '') {
        return $value;
    }
    if (!preg_match('/^contact_email_([1-3])_(label|address)$/', $field['name'], $matches)) {
        return $value;
    }
    $defaults = silkscreen_default_contact_emails();
    $slot = (int) $matches[1] - 1;
    if (!isset($defaults[$slot])) {
        return $value;
    }
    return $matches[2] === 'label' ? $defaults[$slot]['label'] : $defaults[$slot]['address'];
}, 10, 3);

add_filter('acf/load_value/name=about_statement', function ($value, $post_id, $field) {
    if ($value !== null && $value !== '') {
        return $value;
    }
    if (get_post_meta($post_id, '_wp_page_template', true) !== 'page-about.php') {
        return $value;
    }
    return SILKSCREEN_STATEMENT;
}, 10, 3);
