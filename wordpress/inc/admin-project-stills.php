<?php

add_action('add_meta_boxes', function () {
    add_meta_box(
        'silkscreen_project_stills',
        'Project stills',
        'silkscreen_project_stills_meta_box',
        SILKSCREEN_PROJECT_POST_TYPE,
        'normal',
        'core'
    );
}, 20);

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== SILKSCREEN_PROJECT_POST_TYPE) {
        return;
    }
    wp_enqueue_media();
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $script = $dir . '/js/admin-project-stills.js';
    if (!is_readable($script)) {
        return;
    }
    wp_enqueue_script(
        'silkscreen-project-stills',
        $uri . '/js/admin-project-stills.js',
        array('jquery'),
        filemtime($script),
        true
    );
    wp_localize_script('silkscreen-project-stills', 'silkscreenStills', array(
        'max' => SILKSCREEN_PROJECT_STILL_SLOTS,
        'frameTitle' => 'Choose a still',
        'frameButton' => 'Use image',
    ));
});

function silkscreen_project_stills_meta_box(WP_Post $post) {
    wp_nonce_field('silkscreen_save_project_stills', 'silkscreen_stills_nonce');
    $ids = silkscreen_project_still_attachment_ids($post->ID);
    ?>
    <p class="description" style="margin-top:0;">
        Images for the project detail slider, in order. Up to <?php echo (int) SILKSCREEN_PROJECT_STILL_SLOTS; ?> stills.
    </p>
    <div id="ss-project-stills" data-ss-stills-root>
        <ul class="ss-still-rows" data-ss-stills-rows>
            <?php if ($ids) : ?>
                <?php foreach ($ids as $attachment_id) : ?>
                    <?php silkscreen_project_still_row_markup($attachment_id); ?>
                <?php endforeach; ?>
            <?php else : ?>
                <?php silkscreen_project_still_row_markup(0); ?>
            <?php endif; ?>
        </ul>
        <p>
            <button type="button" class="button button-secondary" data-ss-stills-add>Add still</button>
        </p>
    </div>
    <template id="ss-still-row-tpl">
        <?php silkscreen_project_still_row_markup(0); ?>
    </template>
    <style>
        .ss-still-rows { list-style: none; margin: 0; padding: 0; max-width: 640px; }
        .ss-still-row {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #c3c4c7;
            background: #fff;
            padding: 10px 12px;
            margin: 0 0 10px;
        }
        .ss-still-row__thumb {
            width: 80px;
            height: 56px;
            object-fit: cover;
            background: #f0f0f1;
            flex-shrink: 0;
        }
        .ss-still-row__thumb--empty { display: block; }
        .ss-still-row__meta { flex: 1; min-width: 0; font-size: 12px; color: #50575e; }
        .ss-still-row__actions { flex-shrink: 0; }
    </style>
    <?php
}

function silkscreen_project_still_row_markup($attachment_id) {
    $attachment_id = (int) $attachment_id;
    $thumb = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'thumbnail') : '';
    $title = $attachment_id ? get_the_title($attachment_id) : '';
    ?>
    <li class="ss-still-row" data-ss-still-row>
        <?php if ($thumb) : ?>
            <img class="ss-still-row__thumb" src="<?php echo esc_url($thumb); ?>" alt="" data-ss-still-preview />
        <?php else : ?>
            <span class="ss-still-row__thumb ss-still-row__thumb--empty" data-ss-still-preview aria-hidden="true"></span>
        <?php endif; ?>
        <div class="ss-still-row__meta" data-ss-still-label><?php echo esc_html($title ?: 'No image selected'); ?></div>
        <div class="ss-still-row__actions">
            <button type="button" class="button button-small" data-ss-still-pick>Choose</button>
            <button type="button" class="button-link-delete" data-ss-still-remove>Remove</button>
        </div>
        <input type="hidden" name="ss_project_still_ids[]" value="<?php echo (int) $attachment_id; ?>" data-ss-still-id />
    </li>
    <?php
}

add_action('save_post_' . SILKSCREEN_PROJECT_POST_TYPE, function ($post_id) {
    if (!isset($_POST['silkscreen_stills_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['silkscreen_stills_nonce'])), 'silkscreen_save_project_stills')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $ids = array();
    if (!empty($_POST['ss_project_still_ids']) && is_array($_POST['ss_project_still_ids'])) {
        foreach ($_POST['ss_project_still_ids'] as $raw_id) {
            $id = (int) $raw_id;
            if ($id > 0) {
                $ids[] = $id;
            }
            if (count($ids) >= SILKSCREEN_PROJECT_STILL_SLOTS) {
                break;
            }
        }
    }

    if ($ids) {
        update_post_meta($post_id, SILKSCREEN_PROJECT_STILLS_META, wp_json_encode($ids));
    } else {
        delete_post_meta($post_id, SILKSCREEN_PROJECT_STILLS_META);
    }
}, 10, 1);