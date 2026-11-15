<?php

const SILKSCREEN_PROJECT_CREDITS_MAX = 24;

function silkscreen_project_credits_from_post_request() {
    if (empty($_POST['ss_credit_names']) || !is_array($_POST['ss_credit_names'])) {
        return array();
    }
    $roles = isset($_POST['ss_credit_role']) && is_array($_POST['ss_credit_role']) ? $_POST['ss_credit_role'] : array();
    $customs = isset($_POST['ss_credit_custom']) && is_array($_POST['ss_credit_custom']) ? $_POST['ss_credit_custom'] : array();
    $names_list = wp_unslash($_POST['ss_credit_names']);
    $rows = array();
    foreach ($names_list as $index => $names_raw) {
        $names = silkscreen_parse_credit_names($names_raw);
        if (!$names) {
            continue;
        }
        $role = isset($roles[$index]) ? sanitize_key((string) $roles[$index]) : 'custom';
        $custom = isset($customs[$index]) ? silkscreen_sanitize_unicode_text($customs[$index]) : '';
        $rows[] = array(
            'role' => $role !== '' ? $role : 'custom',
            'custom_label' => $custom,
            'names' => $names,
        );
        if (count($rows) >= SILKSCREEN_PROJECT_CREDITS_MAX) {
            break;
        }
    }
    return $rows;
}

add_action('add_meta_boxes', function () {
    add_meta_box(
        'silkscreen_project_credits',
        'Credits',
        'silkscreen_project_credits_meta_box',
        SILKSCREEN_PROJECT_POST_TYPE,
        'normal',
        'default'
    );
}, 30);

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== SILKSCREEN_PROJECT_POST_TYPE) {
        return;
    }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $script = $dir . '/js/admin-project-credits.js';
    if (!is_readable($script)) {
        return;
    }
    wp_enqueue_script(
        'silkscreen-project-credits',
        $uri . '/js/admin-project-credits.js',
        array(),
        filemtime($script),
        true
    );
    wp_localize_script('silkscreen-project-credits', 'silkscreenCredits', array(
        'roles' => silkscreen_credit_role_choices(),
        'max' => SILKSCREEN_PROJECT_CREDITS_MAX,
    ));
});

function silkscreen_project_credits_meta_box(WP_Post $post) {
    wp_nonce_field('silkscreen_save_project_credits', 'silkscreen_credits_nonce');
    $rows = silkscreen_project_credits($post->ID);
    $roles = silkscreen_credit_role_choices();
    ?>
    <p class="description" style="margin-top:0;">
        Add credit lines for the project detail page. Names can be comma-separated or one per line.
    </p>
    <div id="ss-project-credits" data-ss-credits-root>
        <div data-ss-credits-rows>
            <?php
            if ($rows) {
                foreach ($rows as $row) {
                    silkscreen_project_credits_row_markup($row, $roles);
                }
            }
            ?>
        </div>
        <p>
            <button type="button" class="button button-secondary" data-ss-credits-add>Add credit</button>
        </p>
    </div>
    <template id="ss-credit-row-tpl">
        <?php silkscreen_project_credits_row_markup(array('role' => 'directed', 'custom_label' => '', 'names' => array()), $roles); ?>
    </template>
    <style>
        .ss-credit-row {
            border: 1px solid #c3c4c7;
            background: #fff;
            padding: 12px 12px 4px;
            margin: 0 0 12px;
            max-width: 640px;
        }
        .ss-credit-row__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .ss-credit-row label { display: block; font-weight: 600; margin: 8px 0 4px; }
        .ss-credit-row select,
        .ss-credit-row input[type="text"],
        .ss-credit-row textarea { width: 100%; max-width: 100%; }
        .ss-credit-row textarea { min-height: 52px; }
        .ss-credit-row--custom-hidden .ss-credit-row__custom { display: none; }
    </style>
    <?php
}

function silkscreen_project_credits_row_markup(array $row, array $roles) {
    $role = isset($row['role']) ? (string) $row['role'] : 'directed';
    $custom = isset($row['custom_label']) ? (string) $row['custom_label'] : '';
    if (isset($row['names']) && is_array($row['names'])) {
        $names = implode("\n", $row['names']);
    } else {
        $names = isset($row['names']) ? (string) $row['names'] : '';
    }
    $is_custom = $role === 'custom';
    ?>
    <div class="ss-credit-row<?php echo $is_custom ? '' : ' ss-credit-row--custom-hidden'; ?>" data-ss-credit-row>
        <div class="ss-credit-row__head">
            <strong>Credit</strong>
            <button type="button" class="button-link-delete" data-ss-credits-remove aria-label="Remove credit">Remove</button>
        </div>
        <label>Role</label>
        <select name="ss_credit_role[]" data-ss-credit-role>
            <?php foreach ($roles as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>"<?php selected($role, $value); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="ss-credit-row__custom">
            <label>Custom label</label>
            <input type="text" name="ss_credit_custom[]" value="<?php echo esc_attr($custom); ?>" data-ss-credit-custom />
        </div>
        <label>Names</label>
        <textarea name="ss_credit_names[]" rows="2" data-ss-credit-names><?php echo esc_textarea($names); ?></textarea>
    </div>
    <?php
}

add_action('save_post_' . SILKSCREEN_PROJECT_POST_TYPE, function ($post_id) {
    if (!isset($_POST['silkscreen_credits_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['silkscreen_credits_nonce'])), 'silkscreen_save_project_credits')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $rows = silkscreen_project_credits_from_post_request();
    if ($rows) {
        update_post_meta($post_id, SILKSCREEN_PROJECT_CREDITS_META, wp_json_encode($rows, JSON_UNESCAPED_UNICODE));
    } else {
        delete_post_meta($post_id, SILKSCREEN_PROJECT_CREDITS_META);
    }
}, 10, 1);