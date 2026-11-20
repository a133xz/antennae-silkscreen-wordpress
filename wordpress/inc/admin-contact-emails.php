<?php

function silkscreen_contact_email_rows_normalize($rows) {
    $clean = array();
    if (!is_array($rows)) {
        return $clean;
    }
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $label = isset($row['label']) ? silkscreen_sanitize_unicode_text($row['label']) : '';
        $address = isset($row['address']) ? sanitize_email($row['address']) : '';
        if ($label === '' && $address === '') {
            continue;
        }
        $clean[] = array(
            'label' => $label,
            'address' => $address,
        );
        if (count($clean) >= SILKSCREEN_CONTACT_EMAIL_MAX) {
            break;
        }
    }
    return $clean;
}

function silkscreen_contact_email_rows_from_meta($post_id) {
    $raw = get_post_meta($post_id, SILKSCREEN_CONTACT_EMAILS_META, true);
    if (!is_string($raw) || $raw === '') {
        return array();
    }
    $decoded = json_decode($raw, true);
    return silkscreen_contact_email_rows_normalize(is_array($decoded) ? $decoded : array());
}

function silkscreen_contact_legacy_email_rows($post_id) {
    $emails = array();
    for ($slot = 1; $slot <= SILKSCREEN_CONTACT_PRIMARY_SLOTS; $slot++) {
        $address = trim((string) get_post_meta($post_id, 'contact_email_' . $slot . '_address', true));
        $label = trim((string) get_post_meta($post_id, 'contact_email_' . $slot . '_label', true));
        if ($address === '' && $label === '') {
            continue;
        }
        $emails[] = array(
            'label' => $label,
            'address' => $address,
        );
    }
    return $emails;
}

function silkscreen_contact_email_rows_for_editor($post_id) {
    if (metadata_exists('post', $post_id, SILKSCREEN_CONTACT_EMAILS_META)) {
        return silkscreen_contact_email_rows_from_meta($post_id);
    }
    $legacy = silkscreen_contact_legacy_email_rows($post_id);
    if ($legacy) {
        return $legacy;
    }
    return silkscreen_default_contact_emails();
}

function silkscreen_is_contact_admin_screen($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return false;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'page') {
        return false;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if (!$post_id) {
        return false;
    }
    return get_page_template_slug($post_id) === 'page-contact.php';
}

add_action('add_meta_boxes_page', function ($post) {
    if (!$post instanceof WP_Post || get_page_template_slug($post->ID) !== 'page-contact.php') {
        return;
    }
    add_meta_box(
        'silkscreen_contact_emails',
        'Contact emails',
        'silkscreen_contact_emails_meta_box',
        'page',
        'normal',
        'core'
    );
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (!silkscreen_is_contact_admin_screen($hook)) {
        return;
    }
    silkscreen_admin_enqueue_row_reorder($hook);
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $script = $dir . '/js/admin-contact-emails.js';
    if (!is_readable($script)) {
        return;
    }
    wp_enqueue_script(
        'silkscreen-contact-emails',
        $uri . '/js/admin-contact-emails.js',
        array('jquery', 'silkscreen-admin-row-reorder'),
        filemtime($script),
        true
    );
    wp_localize_script('silkscreen-contact-emails', 'silkscreenContactEmails', array(
        'max' => SILKSCREEN_CONTACT_EMAIL_MAX,
    ));
});

function silkscreen_contact_emails_meta_box(WP_Post $post) {
    wp_nonce_field('silkscreen_save_contact_emails', 'silkscreen_contact_emails_nonce');
    $rows = silkscreen_contact_email_rows_for_editor($post->ID);
    ?>
    <p class="description" style="margin-top:0;">
        Each block is a label and an email. Use the arrows to reorder.
    </p>
    <div id="ss-contact-emails" data-ss-contact-root>
        <div data-ss-contact-rows>
            <?php foreach ($rows as $row) : ?>
                <?php silkscreen_contact_email_row_markup($row); ?>
            <?php endforeach; ?>
        </div>
        <p>
            <button type="button" class="button button-secondary" data-ss-contact-add>Add email</button>
        </p>
    </div>
    <template id="ss-contact-email-tpl">
        <?php silkscreen_contact_email_row_markup(array('label' => '', 'address' => '')); ?>
    </template>
    <?php silkscreen_admin_reorder_styles(); ?>
    <style>
        .ss-contact-email {
            border: 1px solid #dcdcde;
            border-radius: 6px;
            background: #fff;
            padding: 12px 12px 4px;
            margin: 0 0 12px;
            max-width: 760px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .ss-contact-email__head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f1;
        }
        .ss-contact-email__head strong { flex: 1; font-size: 13px; font-weight: 600; color: #1d2327; }
        .ss-contact-email__fields {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 12px;
        }
        .ss-contact-email label { display: block; font-weight: 600; margin: 0 0 4px; }
        .ss-contact-email input[type="text"],
        .ss-contact-email input[type="email"] { width: 100%; max-width: 100%; }
        @media (max-width: 600px) {
            .ss-contact-email__fields { grid-template-columns: 1fr; }
        }
    </style>
    <?php
}

function silkscreen_contact_email_row_markup(array $row) {
    $label = isset($row['label']) ? (string) $row['label'] : '';
    $address = isset($row['address']) ? (string) $row['address'] : '';
    ?>
    <div class="ss-contact-email" data-ss-contact-row>
        <div class="ss-contact-email__head">
            <strong>Email</strong>
            <div class="ss-admin-row-actions">
                <?php silkscreen_admin_row_order_buttons(); ?>
                <button type="button" class="button button-small" data-ss-contact-remove>Remove</button>
            </div>
        </div>
        <div class="ss-contact-email__fields">
            <p>
                <label>Label</label>
                <input type="text" name="ss_contact_label[]" value="<?php echo esc_attr($label); ?>" />
            </p>
            <p>
                <label>Email</label>
                <input type="email" name="ss_contact_address[]" value="<?php echo esc_attr($address); ?>" />
            </p>
        </div>
    </div>
    <?php
}

add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['silkscreen_contact_emails_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['silkscreen_contact_emails_nonce'])), 'silkscreen_save_contact_emails')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (!isset($_POST['post_ID']) || (int) $_POST['post_ID'] !== (int) $post_id) {
        return;
    }

    $labels = isset($_POST['ss_contact_label']) && is_array($_POST['ss_contact_label']) ? wp_unslash($_POST['ss_contact_label']) : array();
    $addresses = isset($_POST['ss_contact_address']) && is_array($_POST['ss_contact_address']) ? wp_unslash($_POST['ss_contact_address']) : array();
    $rows = array();
    foreach ($labels as $index => $label) {
        $rows[] = array(
            'label' => $label,
            'address' => isset($addresses[$index]) ? $addresses[$index] : '',
        );
    }
    $rows = silkscreen_contact_email_rows_normalize($rows);
    update_post_meta($post_id, SILKSCREEN_CONTACT_EMAILS_META, wp_json_encode($rows, JSON_UNESCAPED_UNICODE));
}, 10, 1);

add_action('do_meta_boxes', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'page') {
        return;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if (!$post_id || get_page_template_slug($post_id) !== 'page-contact.php') {
        return;
    }
    remove_meta_box('postimagediv', 'page', 'side');
}, 20);
